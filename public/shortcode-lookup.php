<?php
/**
 * File for managing the [fanabyte_certificate_lookup] shortcode.
 * فایل مدیریت شورت‌کد [fanabyte_certificate_lookup].
 *
 * Version: 1.3.0 - Aligned with new language and RTL/LTR setting.
 * نسخه: 1.3.0 - هماهنگ شده با تنظیمات زبان و RTL/LTR جدید.
 */

// ** Security Check: Prevent direct access to the file. **
// ** بررسی امنیتی: جلوگیری از دسترسی مستقیم به فایل. **
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly. / خروج در صورت دسترسی مستقیم.
}

/**
 * Helper function to render the details of a single certificate.
 * تابع کمکی برای رندر کردن جزئیات یک مدرک تکی.
 *
 * @param int $cert_id The ID of the certificate post to display. / شناسه پست مدرکی که باید نمایش داده شود.
 * @return string The HTML output for the certificate details. / خروجی HTML برای جزئیات مدرک.
 */
if (!function_exists('fb_cert_render_certificate_details')) {
    function fb_cert_render_certificate_details($cert_id) {
        $defined_fields = get_option('fanabyte_certificate_fields', []);
        $search_settings = get_option('fanabyte_certificate_search_settings', []);
        $personal_photo_id = get_post_meta($cert_id, '_personal_photo_id', true);
        $footer_text = get_post_meta($cert_id, '_certificate_footer_text', true);
        $details_heading = $search_settings['label_details_heading'] ?? __('Certificate Details:', 'fanabyte-certificate');
        $file_id = get_post_meta($cert_id, '_certificate_file_id', true);
        
        ob_start();
        ?>
        <div class="fb-cert-header">
             <?php if ($personal_photo_id) : ?>
                <div class="fb-cert-personal-photo">
                    <?php echo wp_get_attachment_image(absint($personal_photo_id), 'thumbnail', false, ['class' => 'personal-photo']); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if($personal_photo_id): ?>
             <hr class="fb-cert-separator">
        <?php endif; ?>


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
            $file_id = get_post_meta($cert_id, '_certificate_file_id', true);
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
        <?php if (!empty($footer_text) || function_exists('fb_cert_generate_qr_code_html')) : ?>
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
        <?php endif; ?>

        <?php
        return ob_get_clean();
    }
}


/**
 * Register the [fanabyte_certificate_lookup] shortcode.
 * ثبت شورت‌کد [fanabyte_certificate_lookup].
 */
if (!function_exists('fb_cert_register_lookup_shortcode')) {
    function fb_cert_register_lookup_shortcode() {
        add_shortcode('fanabyte_certificate_lookup', 'fb_cert_lookup_shortcode_callback');
    }
    // Hook the registration to 'init'.
    // اتصال ثبت به 'init'.
    add_action('init', 'fb_cert_register_lookup_shortcode');
}

/**
 * Callback function for the [fanabyte_certificate_lookup] shortcode.
 * تابع Callback برای شورت‌کد [fanabyte_certificate_lookup].
 *
 * @param array $atts Shortcode attributes (not used currently). / ویژگی‌های شورت‌کد (در حال حاضر استفاده نمی‌شود).
 * @return string HTML output for the shortcode. / خروجی HTML برای شورت‌کد.
 */
if (!function_exists('fb_cert_lookup_shortcode_callback')) {
    function fb_cert_lookup_shortcode_callback($atts) {
        $search_settings = get_option('fanabyte_certificate_search_settings', []);
        $defined_fields = get_option('fanabyte_certificate_fields', []);
        $key_fields = [];
        if (!empty($defined_fields) && is_array($defined_fields)) {
            foreach ($defined_fields as $key => $field) {
                if (isset($field['is_key']) && $field['is_key'] && isset($field['type']) && $field['type'] === 'text') {
                    $key_fields[$key] = $field;
                }
            }
        }
        
        $language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
        $is_rtl = ($language_settings['language'] === 'fa');

        ob_start();

        $view_cert_id = null;
        if (isset($_GET['view_cert_id']) && is_numeric($_GET['view_cert_id'])) {
            $view_cert_id = absint($_GET['view_cert_id']);
            $post_to_view = get_post($view_cert_id);

            if (!$post_to_view || $post_to_view->post_type !== 'fb_certificate' || $post_to_view->post_status !== 'publish') {
                $view_cert_id = null;
                echo '<p class="fb-cert-error">' . esc_html__('The requested certificate was not found or is invalid.', 'fanabyte-certificate') . '</p>';
            }
        }

        if (!$view_cert_id) {
            if(!empty($key_fields)) {
                 if (!empty($search_settings['intro_text'])) {
                     echo '<div class="fb-cert-intro-text">' . wp_kses_post($search_settings['intro_text']) . '</div>';
                 }
                $button_text = $search_settings['button_text'] ?? __('Search', 'fanabyte-certificate');
                $button_color = $search_settings['button_color'] ?? '#2271b1';
                $form_action_url = esc_url(remove_query_arg('view_cert_id', add_query_arg(null, null))) . '#fb-cert-results';
                echo '<div class="fb-cert-lookup-form-container" ' . ($is_rtl ? 'dir="rtl"' : 'dir="ltr"') . ' id="fb-cert-lookup-form">';
                echo '<form method="post" action="' . $form_action_url . '">';
                wp_nonce_field('fb_cert_lookup_action', 'fb_cert_lookup_nonce');
                foreach ($key_fields as $key => $field) {
                    $placeholder = $search_settings['placeholders'][$key] ?? sprintf(__('Please enter %s', 'fanabyte-certificate'), $field['label']);
                    $field_id = 'fb_cert_field_' . esc_attr($key);
                    $field_name = 'fb_cert_key[' . esc_attr($key) . ']';
                    $last_value = (isset($_POST['fb_cert_key'][$key])) ? esc_attr(wp_unslash($_POST['fb_cert_key'][$key])) : '';
                    echo '<div class="fb-cert-form-field">';
                    echo '<label for="' . $field_id . '">' . esc_html($field['label']) . ':</label>';
                    echo '<input type="text" id="' . $field_id . '" name="' . $field_name . '" placeholder="' . esc_attr($placeholder) . '" value="' . $last_value . '" required="required">';
                    echo '</div>';
                }
                echo '<div class="fb-cert-form-submit">';
                $button_style = 'background-color: ' . esc_attr($button_color) . '; border-color: ' . esc_attr($button_color) . ';';
                echo '<button type="submit" style="' . $button_style . '">' . esc_html($button_text) . '</button>';
                echo '</div>';
                echo '</form>';
                echo '</div>';
            } else {
                echo '<div class="fb-cert-error">' . esc_html__('The inquiry form is unavailable because no key fields have been defined in the plugin settings.', 'fanabyte-certificate') . '</div>';
            }
        }

        echo '<div id="fb-cert-results" style="margin-top: 30px;"></div>';
        echo '<div class="fb-cert-results-container" ' . ($is_rtl ? 'dir="rtl"' : 'dir="ltr"') . '>';

        if ($view_cert_id) {
            $post_to_view = get_post($view_cert_id);
            echo '<h2>' . sprintf(esc_html__('Certificate Details: %s', 'fanabyte-certificate'), esc_html($post_to_view->post_title)) . '</h2>';
            echo fb_cert_render_certificate_details($view_cert_id);
            $back_search_url = esc_url(remove_query_arg('view_cert_id', add_query_arg(null, null))) . '#fb-cert-lookup-form';
            echo '<p class="fb-back-to-search"><a href="' . $back_search_url . '">&laquo; ' . esc_html__('New Search', 'fanabyte-certificate') . '</a></p>';
        }
        elseif ('POST' === $_SERVER['REQUEST_METHOD']
                && isset($_POST['fb_cert_lookup_nonce'])
                && wp_verify_nonce($_POST['fb_cert_lookup_nonce'], 'fb_cert_lookup_action')
                && isset($_POST['fb_cert_key'])
                && is_array($_POST['fb_cert_key'])
               )
        {
            $search_values = $_POST['fb_cert_key'];
            $meta_query_args = ['relation' => 'AND'];
            $is_search_valid = true;

            if (empty($key_fields)) {
                $is_search_valid = false;
            }

            if($is_search_valid) {
                 foreach ($key_fields as $key => $field) {
                     $meta_key_db = '_fb_cert_' . sanitize_key($key);
                     $value = isset($search_values[$key]) ? sanitize_text_field(wp_unslash($search_values[$key])) : '';
                     if (empty($value)) {
                         $is_search_valid = false;
                         break;
                     }
                     $meta_query_args[] = [
                         'key' => $meta_key_db,
                         'value' => $value,
                         'compare' => '='
                     ];
                 }
            }
            if ($is_search_valid && count($meta_query_args) > 1) {
                $query_args = array(
                    'post_type' => 'fb_certificate',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'meta_query' => $meta_query_args,
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'update_post_meta_cache' => true,
                    'update_post_term_cache' => false,
                );
                $certificate_query = new WP_Query($query_args);

                if ($certificate_query->have_posts()) {
                    if ($certificate_query->post_count > 1) {
                        echo '<h2>' . sprintf(
                                 _n(
                                     '%d certificate found. Please select one to view:',
                                     '%d certificates found. Please select one to view:',
                                     $certificate_query->post_count,
                                     'fanabyte-certificate'
                                 ),
                                 $certificate_query->post_count
                             ) . '</h2>';
                        echo '<ul class="fb-cert-results-list">';
                        while ($certificate_query->have_posts()) {
                            $certificate_query->the_post();
                            $cert_id = get_the_ID();
                            $cert_title = get_the_title();
                            $current_page_url = remove_query_arg('view_cert_id', add_query_arg(null, null));
                            $view_url = esc_url(add_query_arg(['view_cert_id' => $cert_id], $current_page_url)) . '#fb-cert-results';
                            echo '<li><a href="' . $view_url . '">' . esc_html($cert_title) . '</a></li>';
                        }
                        echo '</ul>';
                    }
                    elseif ($certificate_query->post_count === 1) {
                        $certificate_query->the_post();
                        $cert_id = get_the_ID();
                        echo '<h2>' . sprintf(esc_html__('Certificate Details: %s', 'fanabyte-certificate'), get_the_title()) . '</h2>';
                        echo fb_cert_render_certificate_details($cert_id);
                    }
                    wp_reset_postdata();
                } else {
                    echo '<p class="fb-cert-not-found">' . esc_html__('Sorry, no certificate matching the provided details was found. Please check your input and try again.', 'fanabyte-certificate') . '</p>';
                }
            } elseif(isset($_POST['fb_cert_key'])) {
                 echo '<p class="fb-cert-error">' . esc_html__('Error: Please enter all search fields correctly.', 'fanabyte-certificate') . '</p>';
            }
        }

        echo '</div>';
        return ob_get_clean();
    }
}
?>