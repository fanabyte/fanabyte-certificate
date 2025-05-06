<?php
/**
 * File for managing the [fanabyte_certificate_lookup] shortcode.
 * فایل مدیریت شورت‌کد [fanabyte_certificate_lookup].
 *
 * Version: 1.2.0 - Added i18n support, comments, download button settings, and aligns with new display layout.
 * نسخه: 1.2.0 - افزودن پشتیبانی از ترجمه، کامنت‌ها، تنظیمات دکمه دانلود و تطبیق با طرح جدید نمایش.
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
 * This function generates the HTML output for displaying the certificate's title,
 * personal photo, custom fields, download button, footer text, and QR code.
 * It reads necessary settings and post meta.
 * این تابع خروجی HTML برای نمایش عنوان مدرک، عکس پرسنلی، فیلدهای سفارشی،
 * دکمه دانلود، متن فوتر و کد QR را تولید می‌کند. تنظیمات لازم و متای پست را می‌خواند.
 *
 * @param int $cert_id The ID of the certificate post to display. / شناسه پست مدرکی که باید نمایش داده شود.
 * @return string The HTML output for the certificate details. / خروجی HTML برای جزئیات مدرک.
 */
if (!function_exists('fb_cert_render_certificate_details')) {
    function fb_cert_render_certificate_details($cert_id) {
        // Get defined fields and search settings from options.
        // دریافت فیلدهای تعریف شده و تنظیمات جستجو از آپشن‌ها.
        $defined_fields = get_option('fanabyte_certificate_fields', []);
        $search_settings = get_option('fanabyte_certificate_search_settings', []);

        // Get additional meta data: personal photo ID and footer text.
        // دریافت داده‌های متای اضافی: شناسه عکس پرسنلی و متن فوتر.
        $personal_photo_id = get_post_meta($cert_id, '_personal_photo_id', true);
        $footer_text = get_post_meta($cert_id, '_certificate_footer_text', true);
        // Note: The main title is typically displayed by the calling function (shortcode or template).
        // نکته: عنوان اصلی معمولاً توسط تابع فراخوانی کننده (شورت‌کد یا قالب) نمایش داده می‌شود.

        // Read the custom label for the details section heading from settings.
        // خواندن برچسب سفارشی برای عنوان بخش جزئیات از تنظیمات.
        $details_heading = $search_settings['label_details_heading'] ?? __('Certificate Details:', 'fanabyte-certificate'); /*جزئیات مدرک:*/

        ob_start(); // Start output buffering to collect HTML. / شروع بافر خروجی برای جمع‌آوری HTML.
        ?>
        <?php // Use a structure similar to single-fb_certificate.php for style consistency. / استفاده از ساختار مشابه single-fb_certificate.php برای هماهنگی استایل. ?>

        <div class="fb-cert-header">
             <?php // Title is not displayed in this helper, handled in the main shortcode function if needed. / عنوان در این تابع کمکی نمایش داده نمی‌شود، در تابع اصلی شورت‌کد در صورت نیاز مدیریت می‌شود. ?>
             <?php if ($personal_photo_id) : // If personal photo exists. / اگر عکس پرسنلی وجود داشت. ?>
                <div class="fb-cert-personal-photo">
                    <?php // Display the personal photo (thumbnail size). / نمایش عکس پرسنلی (اندازه تصویر کوچک). ?>
                    <?php echo wp_get_attachment_image(absint($personal_photo_id), 'thumbnail', false, ['class' => 'personal-photo']); ?>
                </div>
            <?php endif; ?>
            <?php // The title could also be placed in this header if needed everywhere. / عنوان را هم می‌توان در این هدر قرار داد اگر در همه جا لازم باشد. ?>
        </div>
        <?php if($personal_photo_id): // Show separator only if photo exists. / فقط اگر عکس بود جداکننده نمایش بده. ?>
             <hr class="fb-cert-separator">
        <?php endif; ?>


        <div class="entry-content fb-cert-body">
            <?php // Use the custom heading label. / استفاده از برچسب عنوان سفارشی. ?>
            <h3><?php echo esc_html($details_heading); ?></h3>
            <?php
            // Display custom fields (ordered based on settings).
            // نمایش فیلدهای سفارشی (مرتب شده بر اساس تنظیمات).
            if (!empty($defined_fields) && is_array($defined_fields)) {
                echo '<ul class="fb-cert-custom-fields-list">';
                foreach ($defined_fields as $key => $field_config) {
                    if (!is_array($field_config)) continue; // Ensure config is an array / اطمینان از آرایه بودن پیکربندی
                    $meta_key_db = '_fb_cert_' . sanitize_key($key); // Construct meta key / ساخت کلید متا
                    $value = get_post_meta($cert_id, $meta_key_db, true); // Get saved value / دریافت مقدار ذخیره شده

                    // Only display fields that have a value.
                    // فقط فیلدهایی که مقدار دارند نمایش داده شوند.
                    if (!empty($value)) {
                        echo '<li>';
                        echo '<strong class="field-label">' . esc_html($field_config['label']) . ':</strong>';
                        echo '<span class="field-value">'; // Wrap value in a span / قرار دادن مقدار در یک span
                        // Display value based on field type.
                        // نمایش مقدار بر اساس نوع فیلد.
                        if (isset($field_config['type']) && $field_config['type'] === 'text') {
                            echo nl2br(esc_html($value)); // Use nl2br for line breaks / استفاده از nl2br برای شکست خط
                        } elseif (isset($field_config['type']) && $field_config['type'] === 'image') {
                            // Display image (medium_large size).
                            // نمایش تصویر (اندازه medium_large).
                            echo wp_get_attachment_image(absint($value), 'medium_large');
                        }
                        // Add display logic for other types if needed.
                        // منطق نمایش برای انواع دیگر در صورت نیاز اضافه شود.
                        echo '</span>';
                        echo '</li>';
                    }
                }
                echo '</ul>';
            } else {
                 // Message if no fields are defined (usually not needed here).
                 // پیامی اگر هیچ فیلدی تعریف نشده باشد (معمولا اینجا لازم نیست).
                 // echo '<p>' . esc_html__('No further details defined.', 'fanabyte-certificate') . '</p>'; /*اطلاعات بیشتری تعریف نشده است.*/
            }

            // Download button for the main certificate file (with custom text and color).
            // دکمه دانلود برای فایل اصلی مدرک (با متن و رنگ سفارشی).
            $file_id = get_post_meta($cert_id, '_certificate_file_id', true);
            if ($file_id) {
                $file_url = wp_get_attachment_url($file_id);
                if ($file_url) {
                    // Get custom text and color from settings, with defaults.
                    // دریافت متن و رنگ سفارشی از تنظیمات، با پیش‌فرض‌ها.
                    $download_text = $search_settings['download_button_text'] ?? __('Download Certificate File', 'fanabyte-certificate'); /*دانلود فایل مدرک*/
                    $download_color = $search_settings['download_button_color'] ?? '#2ecc71';
                    // Validate color code.
                    // اعتبارسنجی کد رنگ.
                    $valid_color = preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $download_color) ? $download_color : '#2ecc71';
                    // Generate inline style.
                    // تولید استایل inline.
                    $download_style = 'background-color: ' . esc_attr($valid_color) . '; border-color: ' . esc_attr($valid_color) . ';';
                    // Output the button container and link.
                    // خروجی کانتینر دکمه و لینک.
                    echo '<div class="fb-cert-download-button-area">';
                    echo '<a href="' . esc_url($file_url) . '" target="_blank" download class="button fb-cert-download-button" style="' . $download_style . '">' . esc_html($download_text) . '</a>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        <?php // Display footer only if footer text or QR code exists. / نمایش فوتر فقط اگر متن فوتر یا کد QR وجود داشته باشد. ?>
        <?php if (!empty($footer_text) || function_exists('fb_cert_generate_qr_code_html')) : ?>
            <hr class="fb-cert-separator">
            <div class="fb-cert-footer">
                <div class="fb-cert-footer-text">
                    <?php
                    // Display custom footer text if it exists (allowing safe HTML).
                    // نمایش متن دلخواه فوتر در صورت وجود (اجازه دادن HTML امن).
                    if (!empty($footer_text)) {
                        echo wp_kses_post(wpautop($footer_text)); // Use wpautop for paragraphs / استفاده از wpautop برای پاراگراف‌ها
                    }
                    ?>
                </div>
                <div class="fb-cert-footer-qr">
                    <?php
                    // Display QR Code (without label).
                    // نمایش کد QR (بدون برچسب).
                    $certificate_link = get_permalink($cert_id);
                    if (function_exists('fb_cert_generate_qr_code_html')) { // Check if helper exists / بررسی وجود تابع کمکی
                        echo fb_cert_generate_qr_code_html($certificate_link, 120); // Use helper function / استفاده از تابع کمکی phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
        return ob_get_clean(); // Return the collected HTML output. / بازگرداندن خروجی HTML جمع‌آوری شده.
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
 * Handles displaying the search form, processing submissions, querying certificates,
 * and displaying either a list of results or the details of a single certificate.
 * Also handles direct viewing of a certificate via the 'view_cert_id' query parameter.
 * مدیریت نمایش فرم جستجو، پردازش ارسال‌ها، کوئری گرفتن از مدرک‌ها،
 * و نمایش لیست نتایج یا جزئیات یک مدرک تکی را انجام می‌دهد.
 * همچنین نمایش مستقیم یک مدرک از طریق پارامتر کوئری 'view_cert_id' را مدیریت می‌کند.
 *
 * @param array $atts Shortcode attributes (not used currently). / ویژگی‌های شورت‌کد (در حال حاضر استفاده نمی‌شود).
 * @return string HTML output for the shortcode. / خروجی HTML برای شورت‌کد.
 */
if (!function_exists('fb_cert_lookup_shortcode_callback')) {
    function fb_cert_lookup_shortcode_callback($atts) {
        // Get settings and defined key fields.
        // دریافت تنظیمات و فیلدهای کلیدی تعریف شده.
        $search_settings = get_option('fanabyte_certificate_search_settings', []);
        $defined_fields = get_option('fanabyte_certificate_fields', []);
        $key_fields = []; // Array to hold key fields / آرایه برای نگهداری فیلدهای کلیدی
        if (!empty($defined_fields) && is_array($defined_fields)) {
            foreach ($defined_fields as $key => $field) {
                // A key field must be marked as 'is_key' and be of type 'text'.
                // یک فیلد کلیدی باید به عنوان 'is_key' علامت خورده باشد و از نوع 'text' باشد.
                if (isset($field['is_key']) && $field['is_key'] && isset($field['type']) && $field['type'] === 'text') {
                    $key_fields[$key] = $field;
                }
            }
        }

        ob_start(); // Start output buffering. / شروع بافر خروجی.

        // --- Handle Direct Certificate View via URL parameter ---
        // --- مدیریت نمایش مستقیم مدرک از طریق پارامتر URL ---
        $view_cert_id = null; // Initialize / مقداردهی اولیه
        if (isset($_GET['view_cert_id']) && is_numeric($_GET['view_cert_id'])) {
            $view_cert_id = absint($_GET['view_cert_id']); // Sanitize the ID / پاک‌سازی شناسه
            $post_to_view = get_post($view_cert_id); // Get the post object / دریافت آبجکت پست

            // Validate the post: must exist, be the correct post type, and be published.
            // اعتبارسنجی پست: باید وجود داشته باشد، از نوع پست صحیح باشد و منتشر شده باشد.
            if (!$post_to_view || $post_to_view->post_type !== 'fb_certificate' || $post_to_view->post_status !== 'publish') {
                $view_cert_id = null; // Invalidate if checks fail / نامعتبر کردن اگر بررسی‌ها ناموفق باشند
                echo '<p class="fb-cert-error">' . esc_html__('The requested certificate was not found or is invalid.', 'fanabyte-certificate') . '</p>'; /*مدرک درخواستی یافت نشد یا معتبر نیست.*/
            }
        }

        // --- Display Search Form ---
        // --- نمایش فرم جستجو ---
        // The form is displayed only if we are NOT directly viewing a certificate details page.
        // فرم فقط زمانی نمایش داده می‌شود که مستقیماً در حال مشاهده جزئیات یک مدرک نباشیم.
        if (!$view_cert_id) {
            // Check if key fields are defined.
            // بررسی اینکه آیا فیلدهای کلیدی تعریف شده‌اند.
            if(!empty($key_fields)) {
                 // Display intro text if set.
                 // نمایش متن مقدمه در صورت تنظیم بودن.
                 if (!empty($search_settings['intro_text'])) {
                     echo '<div class="fb-cert-intro-text">' . wp_kses_post($search_settings['intro_text']) . '</div>';
                 }
                 // Display the search form.
                 // نمایش فرم جستجو.
                $button_text = $search_settings['button_text'] ?? __('Search', 'fanabyte-certificate'); /*جستجو*/
                $button_color = $search_settings['button_color'] ?? '#2271b1';
                // Form action URL points to the current page, removing view_cert_id if present, and adding an anchor.
                // آدرس action فرم به صفحه فعلی اشاره می‌کند، view_cert_id را در صورت وجود حذف می‌کند و یک انکر اضافه می‌کند.
                $form_action_url = esc_url(remove_query_arg('view_cert_id', add_query_arg(null, null))) . '#fb-cert-results';
                echo '<div class="fb-cert-lookup-form-container" id="fb-cert-lookup-form">'; // Add ID for back link anchor / افزودن ID برای انکر لینک بازگشت
                echo '<form method="post" action="' . $form_action_url . '">';
                // Add nonce field for security.
                // افزودن فیلد نانس برای امنیت.
                wp_nonce_field('fb_cert_lookup_action', 'fb_cert_lookup_nonce');
                // Loop through key fields to create input fields.
                // حلقه زدن روی فیلدهای کلیدی برای ایجاد فیلدهای ورودی.
                foreach ($key_fields as $key => $field) {
                    // Get placeholder text from settings or generate default.
                    // دریافت متن نگهدارنده از تنظیمات یا تولید پیش‌فرض.
                    // Translators: %s: Field label. Example: "Please enter Student Name"
                    // مترجمان: %s: برچسب فیلد. مثال: "لطفا نام دانشجو را وارد کنید"
                    $placeholder = $search_settings['placeholders'][$key] ?? sprintf(__('Please enter %s', 'fanabyte-certificate'), $field['label']);
                    $field_id = 'fb_cert_field_' . esc_attr($key); // Input field ID / شناسه فیلد ورودی
                    $field_name = 'fb_cert_key[' . esc_attr($key) . ']'; // Input field name / نام فیلد ورودی
                    // Keep the previously entered value if form was submitted.
                    // نگه داشتن مقدار قبلی وارد شده اگر فرم ارسال شده بود.
                    $last_value = (isset($_POST['fb_cert_key'][$key])) ? esc_attr(wp_unslash($_POST['fb_cert_key'][$key])) : '';
                    // Output the label and input field.
                    // خروجی برچسب و فیلد ورودی.
                    echo '<div class="fb-cert-form-field">';
                    echo '<label for="' . $field_id . '">' . esc_html($field['label']) . ':</label>';
                    echo '<input type="text" id="' . $field_id . '" name="' . $field_name . '" placeholder="' . esc_attr($placeholder) . '" value="' . $last_value . '" required="required">';
                    echo '</div>';
                }
                // Output the submit button with custom color style.
                // خروجی دکمه ارسال با استایل رنگ سفارشی.
                echo '<div class="fb-cert-form-submit">';
                $button_style = 'background-color: ' . esc_attr($button_color) . '; border-color: ' . esc_attr($button_color) . ';';
                echo '<button type="submit" style="' . $button_style . '">' . esc_html($button_text) . '</button>';
                echo '</div>';
                echo '</form>';
                echo '</div>'; // End form container
            } else {
                // Message if no key fields are defined in settings.
                // پیامی اگر هیچ فیلد کلیدی در تنظیمات تعریف نشده باشد.
                echo '<div class="fb-cert-error">' . esc_html__('The inquiry form is unavailable because no key fields have been defined in the plugin settings.', 'fanabyte-certificate') . '</div>'; /*فرم استعلام در دسترس نیست زیرا هیچ فیلد کلیدی در تنظیمات افزونه تعریف نشده است.*/
            }
        }

        // --- Results Section (Process form or display direct view) ---
        // --- بخش نتایج (پردازش فرم یا نمایش مستقیم) ---
        echo '<div id="fb-cert-results" style="margin-top: 30px;"></div>'; // Anchor for results / انکر برای نتایج
        echo '<div class="fb-cert-results-container">';

        // --- A. Direct View Mode ---
        // --- حالت نمایش مستقیم ---
        if ($view_cert_id) {
            $post_to_view = get_post($view_cert_id); // Post already validated above / پست قبلاً در بالا اعتبارسنجی شده است
            // Display the title for the single certificate view.
            // نمایش عنوان برای نمای مدرک تکی.
            // Translators: %s: Certificate Title.
            // مترجمان: %s: عنوان مدرک.
            echo '<h2>' . sprintf(esc_html__('Certificate Details: %s', 'fanabyte-certificate'), esc_html($post_to_view->post_title)) . '</h2>'; /*جزئیات مدرک: ...*/
            // Call the helper function to render the details.
            // فراخوانی تابع کمکی برای رندر کردن جزئیات.
            echo fb_cert_render_certificate_details($view_cert_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaping handled within helper.
            // Provide a link back to the search form page (pointing to the form anchor).
            // ارائه لینک بازگشت به صفحه فرم جستجو (اشاره به انکر فرم).
            $back_search_url = esc_url(remove_query_arg('view_cert_id', add_query_arg(null, null))) . '#fb-cert-lookup-form';
            echo '<p class="fb-back-to-search"><a href="' . $back_search_url . '">&laquo; ' . esc_html__('New Search', 'fanabyte-certificate') . '</a></p>'; /*جستجوی جدید*/
        }
        // --- B. Form Submission Processing ---
        // --- حالت پردازش فرم ارسالی ---
        elseif ('POST' === $_SERVER['REQUEST_METHOD']                             // Check if it's a POST request / بررسی اینکه آیا درخواست POST است
                && isset($_POST['fb_cert_lookup_nonce'])                          // Check if nonce is set / بررسی تنظیم بودن نانس
                && wp_verify_nonce($_POST['fb_cert_lookup_nonce'], 'fb_cert_lookup_action') // Verify nonce / تأیید نانس
                && isset($_POST['fb_cert_key'])                                   // Check if key data is set / بررسی تنظیم بودن داده کلیدی
                && is_array($_POST['fb_cert_key'])                                // Check if key data is an array / بررسی آرایه بودن داده کلیدی
               )
        {
            $search_values = $_POST['fb_cert_key']; // Submitted search values / مقادیر جستجوی ارسال شده
            $meta_query_args = ['relation' => 'AND']; // Meta query: all conditions must match / کوئری متا: تمام شرایط باید مطابقت داشته باشند
            $is_search_valid = true; // Flag to track validity / پرچم برای پیگیری اعتبار

            // Check if key fields are defined.
            // بررسی تعریف شدن فیلدهای کلیدی.
            if (empty($key_fields)) {
                $is_search_valid = false; // Cannot search without key fields / بدون فیلد کلیدی نمی‌توان جستجو کرد
            }

            // Build the meta query array if the search is potentially valid.
            // ساخت آرایه کوئری متا اگر جستجو به طور بالقوه معتبر است.
            if($is_search_valid) {
                 foreach ($key_fields as $key => $field) {
                     $meta_key_db = '_fb_cert_' . sanitize_key($key); // Database meta key / کلید متای پایگاه داده
                     // Sanitize the submitted value for this key.
                     // پاک‌سازی مقدار ارسال شده برای این کلید.
                     $value = isset($search_values[$key]) ? sanitize_text_field(wp_unslash($search_values[$key])) : '';
                     // If any key field value is empty, the search is invalid.
                     // اگر مقدار هر فیلد کلیدی خالی باشد، جستجو نامعتبر است.
                     if (empty($value)) {
                         $is_search_valid = false;
                         break; // Stop processing keys / توقف پردازش کلیدها
                     }
                     // Add the condition to the meta query.
                     // افزودن شرط به کوئری متا.
                     $meta_query_args[] = [
                         'key' => $meta_key_db,
                         'value' => $value,
                         'compare' => '=' // Exact match comparison / مقایسه تطابق دقیق
                     ];
                 }
            }

            // Execute the WP_Query if the search is valid and has conditions.
            // اجرای WP_Query اگر جستجو معتبر است و شرط دارد.
            if ($is_search_valid && count($meta_query_args) > 1) { // Need more than just 'relation' => 'AND' / نیاز به بیش از فقط 'relation' => 'AND'
                $query_args = array(
                    'post_type' => 'fb_certificate', // Query our CPT / کوئری گرفتن از CPT ما
                    'post_status' => 'publish',     // Only published certificates / فقط مدرک‌های منتشر شده
                    'posts_per_page' => -1,         // Get all matching results / دریافت تمام نتایج منطبق
                    'meta_query' => $meta_query_args, // The constructed meta query / کوئری متای ساخته شده
                    'orderby' => 'title',           // Order results by title / مرتب‌سازی نتایج بر اساس عنوان
                    'order' => 'ASC',               // Ascending order / ترتیب صعودی
                    'update_post_meta_cache' => true, // Optimize meta data fetching / بهینه‌سازی واکشی داده‌های متا
                    'update_post_term_cache' => false, // Not needed for terms / برای ترم‌ها لازم نیست
                );
                $certificate_query = new WP_Query($query_args); // Execute the query / اجرای کوئری

                // --- Display Results ---
                // --- نمایش نتایج ---
                if ($certificate_query->have_posts()) {
                    // Case 1: More than one certificate found - show a list.
                    // حالت ۱: بیش از یک مدرک یافت شد - نمایش لیست.
                    if ($certificate_query->post_count > 1) {
                        // Display heading with the number of results found.
                        // نمایش عنوان با تعداد نتایج یافت شده.
                        // Translators: %d: Number of certificates found.
                        // مترجمان: %d: تعداد مدرک‌های یافت شده.
                        echo '<h2>' . sprintf(
                                 _n(
                                     '%d certificate found. Please select one to view:', // Singular / مفرد
                                     '%d certificates found. Please select one to view:', // Plural / جمع
                                     $certificate_query->post_count, // Number / تعداد
                                     'fanabyte-certificate' // Text domain / دامنه متن
                                 ),
                                 $certificate_query->post_count
                             ) . '</h2>';
                        /* %d مدرک یافت شد. لطفاً یکی را برای مشاهده انتخاب کنید: */
                        echo '<ul class="fb-cert-results-list">';
                        // Loop through results and display links.
                        // حلقه زدن روی نتایج و نمایش لینک‌ها.
                        while ($certificate_query->have_posts()) {
                            $certificate_query->the_post();
                            $cert_id = get_the_ID();
                            $cert_title = get_the_title();
                            // Construct the URL to view this specific certificate.
                            // ساخت URL برای مشاهده این مدرک خاص.
                            $current_page_url = remove_query_arg('view_cert_id', add_query_arg(null, null)); // Base URL / آدرس پایه
                            $view_url = esc_url(add_query_arg(['view_cert_id' => $cert_id], $current_page_url)) . '#fb-cert-results'; // Add ID and anchor / افزودن شناسه و انکر
                            echo '<li><a href="' . $view_url . '">' . esc_html($cert_title) . '</a></li>';
                        }
                        echo '</ul>';
                    }
                    // Case 2: Exactly one certificate found - display its details directly.
                    // حالت ۲: دقیقاً یک مدرک یافت شد - نمایش مستقیم جزئیات آن.
                    elseif ($certificate_query->post_count === 1) {
                        $certificate_query->the_post();
                        $cert_id = get_the_ID();
                        // Display title.
                        // نمایش عنوان.
                        // Translators: %s: Certificate Title.
                        // مترجمان: %s: عنوان مدرک.
                        echo '<h2>' . sprintf(esc_html__('Certificate Details: %s', 'fanabyte-certificate'), get_the_title()) . '</h2>'; /*جزئیات مدرک: ...*/
                        // Call helper function to render details.
                        // فراخوانی تابع کمکی برای رندر کردن جزئیات.
                        echo fb_cert_render_certificate_details($cert_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaping handled within helper.
                    }
                    wp_reset_postdata(); // Restore original post data / بازیابی داده پست اصلی
                } else {
                    // Case 3: No certificates found matching the criteria.
                    // حالت ۳: هیچ مدرکی با معیارهای وارد شده یافت نشد.
                    echo '<p class="fb-cert-not-found">' . esc_html__('Sorry, no certificate matching the provided details was found. Please check your input and try again.', 'fanabyte-certificate') . '</p>'; /*متاسفانه، مدرکی با مشخصات وارد شده یافت نشد. لطفا ورودی خود را بررسی کرده و دوباره تلاش کنید.*/
                }
            } elseif(isset($_POST['fb_cert_key'])) { // If search was attempted but deemed invalid (e.g., empty field). / اگر جستجو انجام شد اما نامعتبر تشخیص داده شد (مثلاً فیلد خالی).
                 echo '<p class="fb-cert-error">' . esc_html__('Error: Please enter all search fields correctly.', 'fanabyte-certificate') . '</p>'; /*خطا: لطفا تمام فیلدهای جستجو را به درستی وارد کنید.*/
            }
            // Else: Initial page load without form submission or direct view request - do nothing here. / در غیر این صورت: بارگذاری اولیه صفحه بدون ارسال فرم یا درخواست نمایش مستقیم - کاری اینجا انجام نده.

        } // End of form processing/direct view logic / پایان منطق پردازش فرم/نمایش مستقیم

        echo '</div>'; // End results container
        return ob_get_clean(); // Return buffered output / بازگرداندن خروجی بافر شده
    }
} // End if function_exists fb_cert_lookup_shortcode_callback

?>