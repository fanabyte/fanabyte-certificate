<?php
/**
 * The template for displaying single Fanabyte Certificates. (Redesigned Layout)
 * قالب نمایش تک مدرک‌های فنابایت. (طرح بازطراحی شده)
 *
 * Version: 1.3.0 - Aligned with new language and RTL/LTR setting.
 * نسخه: 1.3.0 - هماهنگ شده با تنظیمات زبان و RTL/LTR جدید.
 *
 * This template can be overridden by copying it to yourtheme/single-fb_certificate.php.
 * این قالب می‌تواند با کپی کردن آن به مسیر yourtheme/single-fb_certificate.php در پوسته شما بازنویسی شود.
 *
 * @package FanabyteCertificate
 */

get_header();

$language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
$is_rtl = ($language_settings['language'] === 'fa');

?>

<div id="primary" class="content-area fb-cert-single-page-container">
    <main id="main" class="site-main" <?php echo $is_rtl ? 'dir="rtl"' : 'dir="ltr"'; ?>>

        <?php
        while (have_posts()) :
            the_post();
            $cert_id = get_the_ID();

            $defined_fields = get_option('fanabyte_certificate_fields', []);
            $search_settings = get_option('fanabyte_certificate_search_settings', []);
            $personal_photo_id = get_post_meta($cert_id, '_personal_photo_id', true);
            $footer_text = get_post_meta($cert_id, '_certificate_footer_text', true);
            $file_id = get_post_meta($cert_id, '_certificate_file_id', true);
            $details_heading = $search_settings['label_details_heading'] ?? __('Certificate Details:', 'fanabyte-certificate');
            ?>
            <article id="post-<?php echo esc_attr($cert_id); ?>" <?php post_class('fb-cert-single-container'); ?>>

                <div class="fb-cert-header">
                    <div class="fb-cert-title">
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    </div>
                    <?php if ($personal_photo_id) : ?>
                        <div class="fb-cert-personal-photo">
                            <?php
                            echo wp_get_attachment_image(
                                absint($personal_photo_id),
                                'thumbnail',
                                false,
                                ['class' => 'personal-photo']
                            );
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                <hr class="fb-cert-separator">

                <div class="entry-content fb-cert-body">
                    <h3><?php echo esc_html($details_heading); ?></h3>
                    <?php
                    if (!empty($defined_fields) && is_array($defined_fields)) {
                        echo '<ul class="fb-cert-custom-fields-list">';
                        foreach ($defined_fields as $key => $field_config) {
                             if (!is_array($field_config)) continue;
                             $meta_key_db = '_fb_cert_' . sanitize_key($key);
                             $value = get_post_meta($cert_id, $meta_key_db, true);
                             if (!empty($value)) {
                                 echo '<li>';
                                 echo '<strong class="field-label">' . esc_html($field_config['label']) . ':</strong>';
                                 echo '<span class="field-value">';
                                 if (isset($field_config['type']) && $field_config['type'] === 'text') {
                                     echo nl2br(esc_html($value));
                                 } elseif (isset($field_config['type']) && $field_config['type'] === 'image') {
                                     echo wp_get_attachment_image(absint($value), 'medium_large');
                                 }
                                 echo '</span>';
                                 echo '</li>';
                             }
                        }
                        echo '</ul>';
                    }
                    if ($file_id) {
                        $file_url = wp_get_attachment_url($file_id);
                        if ($file_url) {
                            $download_text = $search_settings['download_button_text'] ?? __('Download Certificate File', 'fanabyte-certificate');
                            $download_color = $search_settings['download_button_color'] ?? '#2ecc71';
                            $valid_color = preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $download_color) ? $download_color : '#2ecc71';
                            $download_style = 'background-color: ' . esc_attr($valid_color) . '; border-color: ' . esc_attr($valid_color) . ';';

                            echo '<div class="fb-cert-download-button-area">';
                            echo '<a href="' . esc_url($file_url) . '" target="_blank" download class="button fb-cert-download-button" style="' . $download_style . '">' . esc_html($download_text) . '</a>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
                <hr class="fb-cert-separator">

                <div class="fb-cert-footer">
                    <div class="fb-cert-footer-text">
                        <?php
                        if (!empty($footer_text)) {
                            echo wp_kses_post(wpautop($footer_text));
                        }
                        ?>
                    </div>
                    <div class="fb-cert-footer-qr">
                        <?php
                        $certificate_link = get_permalink($cert_id);
                        if (function_exists('fb_cert_generate_qr_code_html')) {
                            echo fb_cert_generate_qr_code_html($certificate_link, 120);
                        }
                        ?>
                    </div>
                </div>

                <footer class="entry-footer fb-cert-wp-footer">
                    <?php
                        edit_post_link(
                            sprintf(
                                esc_html__( 'Edit %s', 'fanabyte-certificate' ),
                                '<span class="screen-reader-text">' . get_the_title() . '</span>'
                            ),
                            '<span class="edit-link">',
                            '</span>'
                        );
                    ?>
                </footer></article><?php
        endwhile;
        ?>

    </main></div><?php
get_footer();
?>