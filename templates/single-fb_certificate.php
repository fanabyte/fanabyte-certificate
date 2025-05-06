<?php
/**
 * The template for displaying single Fanabyte Certificates. (Redesigned Layout)
 * قالب نمایش تک مدرک‌های فنابایت. (طرح بازطراحی شده)
 *
 * Version: 1.2.0 - Added i18n support, comments, uses custom label, removes QR label.
 * نسخه: 1.2.0 - افزودن پشتیبانی از ترجمه، کامنت‌ها، استفاده از برچسب سفارشی، حذف لیبل QR.
 *
 * This template can be overridden by copying it to yourtheme/single-fb_certificate.php.
 * این قالب می‌تواند با کپی کردن آن به مسیر yourtheme/single-fb_certificate.php در پوسته شما بازنویسی شود.
 *
 * @package FanabyteCertificate
 */

// Load the theme's header.
// بارگذاری هدر پوسته فعال.
get_header();
?>

<div id="primary" class="content-area fb-cert-single-page-container">
    <main id="main" class="site-main">

        <?php
        // Start the WordPress Loop.
        // شروع حلقه وردپرس.
        while (have_posts()) :
            the_post(); // Set up post data for the current post. / تنظیم داده‌های پست برای پست فعلی.
            $cert_id = get_the_ID(); // Get the current certificate's ID. / دریافت شناسه مدرک فعلی.

            // --- Data Retrieval ---
            // --- واکشی داده‌ها ---

            // Get defined fields and plugin settings from options.
            // دریافت فیلدهای تعریف شده و تنظیمات افزونه از آپشن‌ها.
            $defined_fields = get_option('fanabyte_certificate_fields', []);
            $search_settings = get_option('fanabyte_certificate_search_settings', []);

            // Get specific meta data for this certificate.
            // دریافت داده‌های متای خاص برای این مدرک.
            $personal_photo_id = get_post_meta($cert_id, '_personal_photo_id', true); // Personal photo attachment ID / شناسه پیوست عکس پرسنلی
            $footer_text = get_post_meta($cert_id, '_certificate_footer_text', true); // Custom footer text / متن دلخواه فوتر
            $file_id = get_post_meta($cert_id, '_certificate_file_id', true); // Main file attachment ID / شناسه پیوست فایل اصلی

            // Get the custom heading for the details section from settings, with a default fallback.
            // دریافت عنوان سفارشی برای بخش جزئیات از تنظیمات، با یک مقدار پیش‌فرض جایگزین.
            $details_heading = $search_settings['label_details_heading'] ?? __('Certificate Details:', 'fanabyte-certificate'); /*جزئیات مدرک:*/

            ?>
            <article id="post-<?php echo esc_attr($cert_id); ?>" <?php post_class('fb-cert-single-container'); // Add post classes for styling / افزودن کلاس‌های پست برای استایل‌دهی ?>>

                <div class="fb-cert-header">
                    <div class="fb-cert-title">
                        <?php the_title('<h1 class="entry-title">', '</h1>'); // Display the certificate title / نمایش عنوان مدرک ?>
                    </div>
                    <?php if ($personal_photo_id) : // Check if personal photo exists / بررسی وجود عکس پرسنلی ?>
                        <div class="fb-cert-personal-photo">
                            <?php
                            // Display the personal photo using wp_get_attachment_image (thumbnail size).
                            // نمایش عکس پرسنلی با استفاده از wp_get_attachment_image (اندازه تصویر کوچک).
                            echo wp_get_attachment_image(
                                absint($personal_photo_id), // Sanitize ID / پاک‌سازی شناسه
                                'thumbnail', // Image size / اندازه تصویر
                                false, // Icon fallback / نمایش آیکون در صورت عدم وجود تصویر
                                ['class' => 'personal-photo'] // CSS class / کلاس CSS
                            );
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                <hr class="fb-cert-separator"> <?php // Visual separator / جدا کننده بصری ?>

                <div class="entry-content fb-cert-body">
                    <?php // Use the configurable heading for the details section. / استفاده از عنوان قابل تنظیم برای بخش جزئیات. ?>
                    <h3><?php echo esc_html($details_heading); ?></h3>
                    <?php
                    // Display the list of defined custom fields.
                    // نمایش لیست فیلدهای سفارشی تعریف شده.
                    if (!empty($defined_fields) && is_array($defined_fields)) {
                        echo '<ul class="fb-cert-custom-fields-list">';
                        foreach ($defined_fields as $key => $field_config) {
                             // Validate field configuration.
                             // اعتبارسنجی پیکربندی فیلد.
                             if (!is_array($field_config)) continue;
                             $meta_key_db = '_fb_cert_' . sanitize_key($key); // Generate meta key / تولید کلید متا
                             $value = get_post_meta($cert_id, $meta_key_db, true); // Get the saved value / دریافت مقدار ذخیره شده

                             // Only display the field if it has a value.
                             // فقط فیلدی را نمایش بده که مقدار دارد.
                             if (!empty($value)) {
                                 echo '<li>';
                                 echo '<strong class="field-label">' . esc_html($field_config['label']) . ':</strong>';
                                 // Wrap the value in a span for better styling control.
                                 // قرار دادن مقدار در یک span برای کنترل بهتر استایل‌دهی.
                                 echo '<span class="field-value">';
                                 // Display based on field type.
                                 // نمایش بر اساس نوع فیلد.
                                 if (isset($field_config['type']) && $field_config['type'] === 'text') {
                                     echo nl2br(esc_html($value)); // Display text with line breaks / نمایش متن با شکست خط
                                 } elseif (isset($field_config['type']) && $field_config['type'] === 'image') {
                                     // Display image (use a larger size on single view).
                                     // نمایش تصویر (استفاده از اندازه بزرگتر در نمای تکی).
                                     echo wp_get_attachment_image(absint($value), 'medium_large'); // Or 'large', 'full' / یا 'large', 'full'
                                 }
                                 // Add display logic for other field types if needed.
                                 // منطق نمایش برای انواع دیگر فیلد در صورت نیاز اضافه شود.
                                 echo '</span>';
                                 echo '</li>';
                             }
                        }
                        echo '</ul>';
                    } else {
                         // Optional message if no fields are defined (usually not needed).
                         // پیام اختیاری اگر هیچ فیلدی تعریف نشده باشد (معمولا لازم نیست).
                         // echo '<p>' . esc_html__('No additional details defined.', 'fanabyte-certificate') . '</p>';
                    }

                    // Display the download button for the main certificate file.
                    // نمایش دکمه دانلود برای فایل اصلی مدرک.
                    if ($file_id) {
                        $file_url = wp_get_attachment_url($file_id); // Get file URL / دریافت URL فایل
                        if ($file_url) {
                            // Get custom text and color from settings, with defaults.
                            // دریافت متن و رنگ سفارشی از تنظیمات، با پیش‌فرض‌ها.
                            $download_text = $search_settings['download_button_text'] ?? __('Download Certificate File', 'fanabyte-certificate'); /*دانلود فایل مدرک*/
                            $download_color = $search_settings['download_button_color'] ?? '#2ecc71';
                            // Validate the color code.
                            // اعتبارسنجی کد رنگ.
                            $valid_color = preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $download_color) ? $download_color : '#2ecc71';
                            // Generate inline style for the button.
                            // تولید استایل inline برای دکمه.
                            $download_style = 'background-color: ' . esc_attr($valid_color) . '; border-color: ' . esc_attr($valid_color) . ';';

                            // Output the button container and the download link.
                            // خروجی کانتینر دکمه و لینک دانلود.
                            echo '<div class="fb-cert-download-button-area">';
                            echo '<a href="' . esc_url($file_url) . '" target="_blank" download class="button fb-cert-download-button" style="' . $download_style . '">' . esc_html($download_text) . '</a>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
                <hr class="fb-cert-separator"> <?php // Visual separator / جدا کننده بصری ?>

                <div class="fb-cert-footer">
                    <div class="fb-cert-footer-text">
                        <?php
                        // Display the custom footer text if it exists.
                        // نمایش متن دلخواه فوتر در صورت وجود.
                        // Use wpautop to convert newlines to paragraphs and wp_kses_post for security.
                        // استفاده از wpautop برای تبدیل خطوط جدید به پاراگراف و wp_kses_post برای امنیت.
                        if (!empty($footer_text)) {
                            echo wp_kses_post(wpautop($footer_text));
                        }
                        ?>
                    </div>
                    <div class="fb-cert-footer-qr">
                        <?php
                        // Display the QR Code (without a text label here).
                        // نمایش کد QR (بدون برچسب متنی در اینجا).
                        $certificate_link = get_permalink($cert_id); // Get the permalink / دریافت لینک یکتا
                        // Check if the helper function exists before calling it.
                        // بررسی وجود تابع کمکی قبل از فراخوانی آن.
                        if (function_exists('fb_cert_generate_qr_code_html')) {
                            // Output the QR code image.
                            // خروجی تصویر کد QR.
                            echo fb_cert_generate_qr_code_html($certificate_link, 120); // Smaller size for footer / اندازه کوچکتر برای فوتر phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                        ?>
                    </div>
                </div>

                <footer class="entry-footer fb-cert-wp-footer">
                    <?php
                        // Display the edit link for logged-in users with permission.
                        // نمایش لینک ویرایش برای کاربران وارد شده با دسترسی لازم.
                        edit_post_link(
                            sprintf(
                                /* translators: %s: Name of current post. Only visible to screen readers. */
                                /* مترجمان: %s: نام پست فعلی. فقط برای صفحه خوان‌ها قابل مشاهده است. */
                                esc_html__( 'Edit %s', 'fanabyte-certificate' ), /*ویرایش %s*/
                                '<span class="screen-reader-text">' . get_the_title() . '</span>' // Screen reader text / متن برای صفحه خوان
                            ),
                            '<span class="edit-link">', // Opening tag with class / تگ باز شونده با کلاس
                            '</span>' // Closing tag / تگ بسته شونده
                        );
                    ?>
                </footer></article><?php
        endwhile; // End of the loop. / پایان حلقه.
        ?>

    </main></div><?php
// Load the theme's footer.
// بارگذاری فوتر پوسته فعال.
get_footer();
?>