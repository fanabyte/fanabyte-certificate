<?php
/**
 * File for managing metaboxes for the Certificate CPT (fb_certificate).
 * فایل مدیریت متاباکس‌ها برای CPT مدرک (fb_certificate).
 *
 * Version: 1.2.0 - Added i18n support, comments, configurable titles/labels, and improved JS uploader.
 * نسخه: 1.2.0 - افزودن پشتیبانی از ترجمه، کامنت‌ها، عناوین/برچسب‌های قابل تنظیم و بهبود آپلودر JS.
 */

// ** Security Check: Prevent direct access to the file. **
// ** بررسی امنیتی: جلوگیری از دسترسی مستقیم به فایل. **
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly. / خروج در صورت دسترسی مستقیم.
}

/**
 * Add custom metaboxes to the Certificate edit screen using configurable titles.
 * افزودن متاباکس‌های سفارشی به صفحه ویرایش مدرک با استفاده از عناوین قابل تنظیم.
 */
function fb_cert_add_metaboxes() {
    // Read settings to get configurable metabox titles.
    // خواندن تنظیمات برای دریافت عناوین متاباکس‌های قابل تنظیم.
    $settings = get_option('fanabyte_certificate_search_settings');

    // Use saved titles or default values if not set.
    // استفاده از عناوین ذخیره شده یا مقادیر پیش‌فرض اگر تنظیم نشده باشند.
    $main_data_title = $settings['metabox_title_main_data'] ?? __('Certificate Information & Details', 'fanabyte-certificate'); /*اطلاعات و جزئیات مدرک*/
    $main_file_title = $settings['metabox_title_main_file'] ?? __('Main Certificate File (PDF/Image)', 'fanabyte-certificate'); /*فایل اصلی مدرک (PDF/Image)*/

    // Register the main metabox for custom fields, personal photo, and custom text.
    // ثبت متاباکس اصلی برای فیلدهای سفارشی، عکس پرسنلی و متن دلخواه.
    add_meta_box(
        'fb_certificate_main_data_metabox',       // Metabox ID / شناسه متاباکس
        esc_html($main_data_title),               // Title (configurable) / عنوان (قابل تنظیم)
        'fb_cert_main_data_metabox_callback',     // Callback function to render content / تابع callback برای رندر محتوا
        'fb_certificate',                         // Custom Post Type name / نام CPT
        'normal',                                 // Context (normal, side, advanced) / موقعیت (نرمال، سایدبار، پیشرفته)
        'high'                                    // Priority (high, core, default, low) / اولویت (بالا، هسته، پیش‌فرض، پایین)
    );

    // Register the metabox for the main certificate file (in the sidebar).
    // ثبت متاباکس برای فایل اصلی مدرک (در سایدبار).
    add_meta_box(
        'fb_certificate_file_metabox',            // Metabox ID / شناسه متاباکس
        esc_html($main_file_title),               // Title (configurable) / عنوان (قابل تنظیم)
        'fb_cert_file_metabox_callback',          // Callback function / تابع callback
        'fb_certificate',                         // CPT name / نام CPT
        'side',                                   // Context (sidebar) / موقعیت (سایدبار)
        'default'                                 // Priority / اولویت
    );
}
// Hook the function to the 'add_meta_boxes' action.
// اتصال تابع به اکشن 'add_meta_boxes'.
add_action('add_meta_boxes', 'fb_cert_add_metaboxes');

/**
 * Callback function to display the content of the main data metabox.
 * تابع Callback برای نمایش محتوای متاباکس اصلی اطلاعات.
 *
 * Includes: Defined custom fields, personal photo uploader, custom footer text field,
 * and the JavaScript for the media uploader functionality.
 * شامل: فیلدهای سفارشی تعریف شده، آپلودر عکس پرسنلی، فیلد متن دلخواه فوتر،
 * و کد جاوااسکریپت برای عملکرد آپلودر رسانه.
 *
 * @param WP_Post $post The current post object. / آبجکت پست فعلی.
 */
function fb_cert_main_data_metabox_callback($post) {
    // Add a nonce field for security verification upon saving.
    // افزودن فیلد Nonce برای تأیید امنیتی هنگام ذخیره.
    wp_nonce_field('fb_cert_save_meta_data', 'fb_cert_meta_nonce');

    // Read settings to get the configurable label for the personal photo.
    // خواندن تنظیمات برای دریافت برچسب قابل تنظیم عکس پرسنلی.
    $settings = get_option('fanabyte_certificate_search_settings');
    $personal_photo_label = $settings['label_personal_photo'] ?? __('Personal Photo (Optional)', 'fanabyte-certificate'); /*تصویر پرسنلی (اختیاری)*/

    // --- Display Defined Custom Fields ---
    // --- نمایش فیلدهای سفارشی تعریف شده ---
    $defined_fields = get_option('fanabyte_certificate_fields', []); // Get fields from options / دریافت فیلدها از آپشن‌ها

    // Check if fields are defined and are in array format.
    // بررسی اینکه آیا فیلدها تعریف شده‌اند و در قالب آرایه هستند.
    if (!empty($defined_fields) && is_array($defined_fields)) {
        echo '<h4>' . esc_html__('Custom Fields', 'fanabyte-certificate') . '</h4>'; /*فیلدهای سفارشی*/
        echo '<table class="form-table">'; // Use WordPress standard table class / استفاده از کلاس جدول استاندارد وردپرس
        foreach ($defined_fields as $field_key => $field_config) {
             // Ensure field config data is valid.
             // اطمینان از معتبر بودن داده‌های پیکربندی فیلد.
             if (!is_array($field_config)) continue;

             // Get field label, type, meta key, and saved value.
             // دریافت برچسب، نوع، کلید متا و مقدار ذخیره شده فیلد.
             $label = isset($field_config['label']) ? esc_html($field_config['label']) : esc_html__('Untitled', 'fanabyte-certificate'); /*بدون عنوان*/
             $type = isset($field_config['type']) ? $field_config['type'] : 'text'; // Default to text / پیش‌فرض متن
             $meta_key = '_fb_cert_' . sanitize_key($field_key); // Ensure a valid meta key / اطمینان از کلید متای معتبر
             $value = get_post_meta($post->ID, $meta_key, true); // Get saved meta value / دریافت مقدار متای ذخیره شده

             echo '<tr>';
             echo '<th scope="row"><label for="' . esc_attr($meta_key) . '">' . $label . ':</label></th>';
             echo '<td>';

             // Render the input field based on its type.
             // رندر کردن فیلد ورودی بر اساس نوع آن.
             if ($type === 'text') {
                 // Standard text input.
                 // ورودی متن استاندارد.
                 echo '<input type="text" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value) . '" class="regular-text" />';
             } elseif ($type === 'image') {
                 // Image uploader field.
                 // فیلد آپلودر تصویر.
                 $image_id = absint($value); // Saved value is the attachment ID / مقدار ذخیره شده شناسه پیوست است
                 $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : ''; // Get thumbnail URL / دریافت URL تصویر کوچک
                 echo '<div class="fb-cert-image-uploader">'; // Wrapper div / دیو دربرگیرنده
                 // Image preview.
                 // پیش‌نمایش تصویر.
                 echo '<img src="' . esc_url($image_url) . '" style="max-width: 100px; height: auto; display: ' . (empty($image_url) ? 'none' : 'inline-block') . '; vertical-align: middle; margin-left: 10px;" data-preview-for="' . esc_attr($meta_key) . '" />';
                 // Hidden input to store the attachment ID.
                 // ورودی مخفی برای ذخیره شناسه پیوست.
                 echo '<input type="hidden" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($image_id) . '" />';
                 // Upload/Select button.
                 // دکمه آپلود/انتخاب.
                 echo '<button type="button" class="button fb-upload-button" data-input-id="' . esc_attr($meta_key) . '" data-library-type="image">' . esc_html__('Select/Upload Image', 'fanabyte-certificate') . '</button>'; /*انتخاب/آپلود تصویر*/
                 // Remove button.
                 // دکمه حذف.
                 echo '<button type="button" class="button fb-remove-button" data-input-id="' . esc_attr($meta_key) . '" style="margin-right: 5px; display: ' . (empty($image_id) ? 'none' : 'inline-block') . ';">' . esc_html__('Remove', 'fanabyte-certificate') . '</button>'; /*حذف*/
                 echo '</div>';
             }
             // Add rendering for other field types here if needed (e.g., textarea, select).
             // رندرینگ برای انواع دیگر فیلد در اینجا اضافه شود در صورت نیاز (مانند textarea، select).

             echo '</td>';
             echo '</tr>';
        }
        echo '</table><hr>'; // Separator after custom fields / جدا کننده بعد از فیلدهای سفارشی
    } else {
        // Message if no custom fields are defined.
        // پیامی اگر هیچ فیلد سفارشی تعریف نشده باشد.
        echo '<p>' . sprintf(
                // Translators: %s is the link to the settings page -> Fields tab.
                // مترجمان: %s لینک به صفحه تنظیمات -> تب مدیریت فیلدها است.
                esc_html__('To add information, first define the desired fields on the %s page.', 'fanabyte-certificate'),
                '<a href="' . esc_url(admin_url('admin.php?page=fanabyte-certificate-settings&tab=fields')) . '"><strong>' . esc_html__('Certificates -> Settings -> Field Management', 'fanabyte-certificate') . '</strong></a>'
             ) . '</p><hr>';
        /* برای افزودن اطلاعات، ابتدا فیلدهای مورد نظر را در صفحه ... تعریف کنید. */
    }

    // --- Display Personal Photo Uploader ---
    // --- نمایش آپلودر عکس پرسنلی ---
    echo '<h4>' . esc_html($personal_photo_label) . '</h4>'; // Use configurable label / استفاده از برچسب قابل تنظیم
    echo '<table class="form-table"><tbody><tr>';
    echo '<th scope="row"><label for="_personal_photo_id">' . esc_html__('Select Image:', 'fanabyte-certificate') . '</label></th>'; /*انتخاب تصویر:*/
    echo '<td>';
    // Get saved photo ID and URL.
    // دریافت شناسه و URL عکس ذخیره شده.
    $personal_photo_id = get_post_meta($post->ID, '_personal_photo_id', true);
    $personal_photo_url = $personal_photo_id ? wp_get_attachment_image_url(absint($personal_photo_id), 'thumbnail') : '';
    // Use the same uploader structure as custom image fields.
    // استفاده از همان ساختار آپلودر فیلدهای تصویر سفارشی.
    echo '<div class="fb-cert-image-uploader">';
    echo '<img src="' . esc_url($personal_photo_url) . '" style="max-width: 100px; height: auto; display: ' . (empty($personal_photo_url) ? 'none' : 'inline-block') . '; vertical-align: middle; margin-left: 10px;" data-preview-for="_personal_photo_id" />';
    echo '<input type="hidden" id="_personal_photo_id" name="_personal_photo_id" value="' . esc_attr($personal_photo_id) . '" />';
    echo '<button type="button" class="button fb-upload-button" data-input-id="_personal_photo_id" data-library-type="image">' . esc_html__('Select/Upload Image', 'fanabyte-certificate') . '</button>'; /*انتخاب/آپلود تصویر*/
    echo '<button type="button" class="button fb-remove-button" data-input-id="_personal_photo_id" style="margin-right: 5px; display: ' . (empty($personal_photo_id) ? 'none' : 'inline-block') . ';">' . esc_html__('Remove', 'fanabyte-certificate') . '</button>'; /*حذف*/
    // Updated description text.
    // متن توضیحات اصلاح شده.
    echo '<p class="description">' . esc_html__('Select a square image to display next to the title (allowed formats: jpg, jpeg, png).', 'fanabyte-certificate') . '</p>'; /*یک تصویر مربعی برای نمایش در کنار عنوان انتخاب کنید (فرمت‌های مجاز: jpg, jpeg, png).*/
    echo '</div>';
    echo '</td>';
    echo '</tr></tbody></table><hr>'; // Separator / جدا کننده

    // --- Display Custom Footer Text Field ---
    // --- نمایش فیلد متن دلخواه پایین صفحه ---
     echo '<h4>' . esc_html__('Custom Footer Text (Optional)', 'fanabyte-certificate') . '</h4>'; /*متن دلخواه پایین صفحه (اختیاری)*/
     echo '<table class="form-table"><tbody><tr>';
     echo '<th scope="row"><label for="_certificate_footer_text">' . esc_html__('Enter text:', 'fanabyte-certificate') . '</label></th>'; /*متن مورد نظر:*/
     echo '<td>';
     // Get saved footer text.
     // دریافت متن فوتر ذخیره شده.
     $footer_text = get_post_meta($post->ID, '_certificate_footer_text', true);
     // Use textarea for multi-line input.
     // استفاده از textarea برای ورودی چند خطی.
     echo '<textarea id="_certificate_footer_text" name="_certificate_footer_text" rows="5" class="large-text">' . esc_textarea($footer_text) . '</textarea>';
     echo '<p class="description">' . esc_html__('This text will be displayed at the bottom of the page (in the footer section, right side).', 'fanabyte-certificate') . '</p>'; /*این متن در پایین صفحه (در بخش فوتر، سمت راست) نمایش داده خواهد شد.*/
     echo '</td>';
     echo '</tr></tbody></table>';


    // --- Media Uploader JavaScript ---
    // --- جاوااسکریپت آپلودر رسانه ---
    // This script handles the "Upload/Select" and "Remove" buttons for both image and file fields.
    // این اسکریپت دکمه‌های "آپلود/انتخاب" و "حذف" را برای فیلدهای تصویر و فایل مدیریت می‌کند.
    ?>
    <script>
    jQuery(document).ready(function($){
        // Function to open the WordPress media uploader.
        // تابع برای باز کردن آپلودر رسانه وردپرس.
        function openMediaUploader(inputId, libraryType = 'image') {
            // Create a new media frame or reuse existing one if needed (more advanced).
            // یک فریم رسانه جدید ایجاد می‌کند یا در صورت نیاز از موجود استفاده می‌کند (پیشرفته‌تر).
            var frame = wp.media({
                title: '<?php echo esc_js(__("Select or Upload", "fanabyte-certificate")); /*انتخاب یا آپلود*/ ?>',
                button: { text: '<?php echo esc_js(__("Use this file", "fanabyte-certificate")); /*استفاده از این فایل*/ ?>' },
                library: { type: libraryType }, // Set allowed file types / تنظیم انواع فایل مجاز
                multiple: false // Only allow single selection / فقط اجازه انتخاب تکی
            });

            // When a file is selected from the media library.
            // وقتی یک فایل از کتابخانه رسانه انتخاب می‌شود.
            frame.on('select', function() {
                // Get the selected attachment details.
                // دریافت جزئیات پیوست انتخاب شده.
                var attachment = frame.state().get('selection').first().toJSON();

                // Get the related input field, preview image, remove button, and file display elements.
                // دریافت فیلد ورودی مرتبط، تصویر پیش‌نمایش، دکمه حذف و المان نمایش فایل.
                var $inputField = $('#' + inputId);
                var $previewImage = $('img[data-preview-for="' + inputId + '"]');
                var $removeButton = $('button.fb-remove-button[data-input-id="' + inputId + '"]');
                var $fileDisplay = $('.fb-current-file-display[data-input-id="' + inputId + '"]');

                // Set the hidden input field value to the attachment ID and trigger change event.
                // مقدار فیلد ورودی مخفی را به شناسه پیوست تنظیم کرده و رویداد change را اجرا می‌کند.
                $inputField.val(attachment.id).trigger('change');

                // Update image preview if it exists.
                // به‌روزرسانی پیش‌نمایش تصویر در صورت وجود.
                if ($previewImage.length) {
                    var thumbnailUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                    $previewImage.attr('src', thumbnailUrl).show();
                }
                // Update file name display if it exists (for the main file metabox).
                // به‌روزرسانی نمایش نام فایل در صورت وجود (برای متاباکس فایل اصلی).
                if ($fileDisplay.length) {
                    var fileLink = '<strong><?php echo esc_js(__("Current file:", "fanabyte-certificate")); /*فایل فعلی:*/ ?></strong> <a href="' + attachment.url + '" target="_blank">' + attachment.filename + '</a>';
                    $fileDisplay.html(fileLink).show();
                }
                // Show the remove button.
                // نمایش دکمه حذف.
                 $removeButton.show();
            });

            // Open the media uploader frame.
            // باز کردن فریم آپلودر رسانه.
            frame.open();
        }

        // --- Event Handlers ---
        // --- مدیریت کننده‌های رویداد ---

        // Click handler for Upload/Select buttons (delegated to body for dynamically added fields).
        // هندلر کلیک برای دکمه‌های آپلود/انتخاب (به body واگذار شده برای فیلدهای اضافه شده پویا).
        // Use namespacing (.fbCertUploader) to avoid conflicts.
        // استفاده از فضای نام (.fbCertUploader) برای جلوگیری از تداخل.
        $('body').off('click.fbCertUploader').on('click.fbCertUploader', '.fb-upload-button', function(e) {
            e.preventDefault();
            var inputId = $(this).data('input-id'); // Get target input ID from data attribute / دریافت شناسه ورودی هدف از ویژگی data
            // Determine allowed library type from data attribute, default to 'image'.
            // تعیین نوع کتابخانه مجاز از ویژگی data، پیش‌فرض 'image'.
            var libraryType = $(this).data('library-type') || 'image';
            // If multiple types are specified (comma-separated), convert to array.
            // اگر چند نوع مشخص شده باشد (جدا شده با کاما)، به آرایه تبدیل کن.
            if (typeof libraryType === 'string' && libraryType.includes(',')) {
                libraryType = libraryType.split(',').map(s => s.trim());
            }
            // Open the media uploader.
            // باز کردن آپلودر رسانه.
            openMediaUploader(inputId, libraryType);
        });

        // Click handler for Remove buttons.
        // هندلر کلیک برای دکمه‌های حذف.
        $('body').off('click.fbCertRemover').on('click.fbCertRemover', '.fb-remove-button', function(e) {
            e.preventDefault();
            var inputId = $(this).data('input-id'); // Get target input ID / دریافت شناسه ورودی هدف
            var $inputField = $('#' + inputId);
            var $previewImage = $('img[data-preview-for="' + inputId + '"]');
            var $fileDisplay = $('.fb-current-file-display[data-input-id="' + inputId + '"]');

            // Clear the input field value and trigger change.
            // خالی کردن مقدار فیلد ورودی و اجرای trigger.
            $inputField.val('').trigger('change');
            // Hide image preview if it exists.
            // پنهان کردن پیش‌نمایش عکس در صورت وجود.
            if($previewImage.length) {
                $previewImage.attr('src', '').hide();
            }
            // Hide file name display if it exists.
            // پنهان کردن نمایش نام فایل در صورت وجود.
            if($fileDisplay.length) {
                $fileDisplay.empty().hide();
            }
            // Hide the remove button itself.
            // پنهان کردن خود دکمه حذف.
            $(this).hide();
        });
    });
    </script>
    <?php
}


/**
 * Callback function to display the content of the main certificate file metabox (sidebar).
 * تابع Callback برای نمایش محتوای متاباکس فایل اصلی مدرک (سایدبار).
 *
 * Updated to align with the new JS uploader logic using data attributes and classes.
 * به‌روز شده تا با منطق جدید آپلودر JS با استفاده از ویژگی‌های data و کلاس‌ها هماهنگ باشد.
 *
 * @param WP_Post $post The current post object. / آبجکت پست فعلی.
 */
function fb_cert_file_metabox_callback($post) {
    // Get saved file ID and details.
    // دریافت شناسه فایل ذخیره شده و جزئیات.
    $file_id = get_post_meta($post->ID, '_certificate_file_id', true);
    $file_url = $file_id ? wp_get_attachment_url($file_id) : '';
    $file_name = $file_id ? basename(get_attached_file($file_id)) : ''; // Get filename / دریافت نام فایل

    // Wrapper div.
    // دیو دربرگیرنده.
    echo '<div class="fb-cert-file-uploader">';

    // Paragraph to display the current file name (with class and data attribute for JS).
    // پاراگراف برای نمایش نام فایل فعلی (با کلاس و ویژگی data برای JS).
    echo '<p class="fb-current-file-display" data-input-id="_certificate_file_id" style="margin-bottom: 8px; display: ' . ($file_url ? 'block' : 'none') . ';">';
    if ($file_url) {
        echo '<strong>' . esc_html__('Current file:', 'fanabyte-certificate') . '</strong> <a href="' . esc_url($file_url) . '" target="_blank">' . esc_html($file_name) . '</a>'; /*فایل فعلی:*/
    }
    echo '</p>';

    // Hidden input to store the attachment ID.
    // ورودی مخفی برای ذخیره شناسه پیوست.
    echo '<input type="hidden" id="_certificate_file_id" name="_certificate_file_id" value="' . esc_attr($file_id) . '" />';

    // Upload/Select button with data attributes for allowed types.
    // دکمه آپلود/انتخاب با ویژگی‌های data برای انواع مجاز.
    echo '<button type="button" class="button fb-upload-button" data-input-id="_certificate_file_id" data-library-type="application/pdf,image/jpeg,image/png,image/jpg">' . esc_html__('Select/Upload File', 'fanabyte-certificate') . '</button>'; /*انتخاب/آپلود فایل*/

    // Remove button with data attribute.
    // دکمه حذف با ویژگی data.
    echo '<button type="button" class="button fb-remove-button" data-input-id="_certificate_file_id" style="margin-right: 5px; display: ' . (empty($file_id) ? 'none' : 'inline-block') . ';">' . esc_html__('Remove', 'fanabyte-certificate') . '</button>'; /*حذف*/

    // Description text.
    // متن توضیحات.
    echo '<p class="description" style="margin-top: 8px;">' . esc_html__('The main PDF or image file of the certificate.', 'fanabyte-certificate') . '</p>'; /*فایل PDF یا تصویر اصلی مدرک.*/
    echo '</div>';

    // Note: The JavaScript for this uploader is handled within the main data metabox callback.
    // نکته: جاوااسکریپت این آپلودر در callback متاباکس اصلی اطلاعات مدیریت می‌شود.
}


/**
 * Save the data from the custom metaboxes.
 * ذخیره داده‌های متاباکس‌های سفارشی.
 *
 * Handles saving data for defined custom fields, the main certificate file,
 * the personal photo, and the custom footer text. Includes security checks
 * (nonce, autosave, permissions) and data sanitization.
 * ذخیره داده‌ها برای فیلدهای سفارشی تعریف شده، فایل اصلی مدرک،
 * عکس پرسنلی و متن دلخواه فوتر را مدیریت می‌کند. شامل بررسی‌های امنیتی
 * (نانس، ذخیره خودکار، دسترسی‌ها) و پاک‌سازی داده‌ها است.
 *
 * @param int $post_id The ID of the post being saved. / شناسه پستی که در حال ذخیره شدن است.
 */
function fb_cert_save_meta_data($post_id) {
    // --- Initial Security Checks ---
    // --- بررسی‌های امنیتی اولیه ---

    // 1. Verify the nonce.
    // 1. تأیید نانس.
    if (!isset($_POST['fb_cert_meta_nonce']) || !wp_verify_nonce($_POST['fb_cert_meta_nonce'], 'fb_cert_save_meta_data')) {
        return $post_id; // Nonce verification failed / تأیید نانس ناموفق بود
    }
    // 2. Check if it's an autosave.
    // 2. بررسی اینکه آیا ذخیره خودکار است.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id; // Don't save during autosave / در حین ذخیره خودکار ذخیره نکن
    }
    // 3. Check the post type.
    // 3. بررسی نوع پست.
    if (!isset($_POST['post_type']) || 'fb_certificate' !== $_POST['post_type']) {
        return $post_id; // Not our CPT / CPT ما نیست
    }
    // 4. Check user permissions.
    // 4. بررسی دسترسی‌های کاربر.
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id; // User doesn't have permission / کاربر دسترسی ندارد
    }

    // --- Save Custom Fields Data ---
    // --- ذخیره داده‌های فیلدهای سفارشی ---
    $defined_fields = get_option('fanabyte_certificate_fields', []); // Get defined fields / دریافت فیلدهای تعریف شده
    if (!empty($defined_fields) && is_array($defined_fields)) {
        foreach ($defined_fields as $field_key => $field_config) {
            if (!is_array($field_config)) continue; // Skip invalid config / رد شدن از پیکربندی نامعتبر

            $meta_key = '_fb_cert_' . sanitize_key($field_key); // Construct the meta key / ساخت کلید متا

            // Check if the field was submitted in the POST data.
            // بررسی اینکه آیا فیلد در داده‌های POST ارسال شده است.
            if (isset($_POST[$meta_key])) {
                $value = $_POST[$meta_key]; // Raw value / مقدار خام
                $value_to_save = ''; // Initialize value to save / مقداردهی اولیه مقدار برای ذخیره

                // Sanitize based on field type.
                // پاک‌سازی بر اساس نوع فیلد.
                if (isset($field_config['type']) && $field_config['type'] === 'text') {
                     // Sanitize text field, use wp_unslash for security.
                     // پاک‌سازی فیلد متنی، استفاده از wp_unslash برای امنیت.
                     $value_to_save = sanitize_text_field(wp_unslash($value));
                 } elseif (isset($field_config['type']) && $field_config['type'] === 'image') {
                     // Sanitize image field (store attachment ID as integer).
                     // پاک‌سازی فیلد تصویر (ذخیره شناسه پیوست به عنوان عدد صحیح).
                     $value_to_save = absint($value);
                 }
                 // Add sanitization for other field types if needed.
                 // افزودن پاک‌سازی برای انواع دیگر فیلد در صورت نیاز.

                 // Update the post meta.
                 // به‌روزرسانی متای پست.
                 update_post_meta($post_id, $meta_key, $value_to_save);
            } else {
                 // If a field (like a checkbox) is not present in POST, it means it's unchecked/empty.
                 // Delete the meta key to reflect this.
                 // اگر فیلدی (مانند چک‌باکس) در POST وجود نداشته باشد، به معنی عدم انتخاب/خالی بودن آن است.
                 // کلید متا را حذف کن تا این وضعیت منعکس شود.
                 delete_post_meta($post_id, $meta_key);
            }
        }
    }

     // --- Save Main Certificate File ID ---
     // --- ذخیره شناسه فایل اصلی مدرک ---
     $file_meta_key = '_certificate_file_id';
     if (isset($_POST[$file_meta_key])) {
         $file_id = absint($_POST[$file_meta_key]); // Sanitize as integer / پاک‌سازی به عنوان عدد صحیح
         // Only save if the ID is a positive integer.
         // فقط در صورتی ذخیره کن که شناسه یک عدد صحیح مثبت باشد.
         if ($file_id > 0) {
            update_post_meta($post_id, $file_meta_key, $file_id);
         } else {
             // If value is 0 or empty string, delete the meta (file removed).
             // اگر مقدار 0 یا رشته خالی است، متا را حذف کن (فایل حذف شده است).
             delete_post_meta($post_id, $file_meta_key);
         }
     } else {
          // If field not submitted at all, consider deleting (optional, depends on desired behavior).
          // اگر فیلد اصلاً ارسال نشده است، حذف را در نظر بگیر (اختیاری، به رفتار مورد نظر بستگی دارد).
          // Current behavior: Delete if not present.
          // رفتار فعلی: حذف در صورت عدم وجود.
          delete_post_meta($post_id, $file_meta_key);
     }

     // --- Save Personal Photo ID ---
     // --- ذخیره شناسه عکس پرسنلی ---
     $personal_photo_key = '_personal_photo_id';
      if (isset($_POST[$personal_photo_key])) {
         $photo_id = absint($_POST[$personal_photo_key]); // Sanitize as integer / پاک‌سازی به عنوان عدد صحیح
         if ($photo_id > 0) {
            update_post_meta($post_id, $personal_photo_key, $photo_id);
         } else {
             // Delete meta if ID is 0 or empty (photo removed).
             // حذف متا اگر شناسه 0 یا خالی است (عکس حذف شده است).
             delete_post_meta($post_id, $personal_photo_key);
         }
     } else {
          // Delete if field not present in POST.
          // حذف اگر فیلد در POST وجود ندارد.
          delete_post_meta($post_id, $personal_photo_key);
     }

      // --- Save Custom Footer Text ---
      // --- ذخیره متن دلخواه پایین صفحه ---
      $footer_text_key = '_certificate_footer_text';
       if (isset($_POST[$footer_text_key])) {
           // Sanitize textarea content, allowing basic safe HTML (similar to post content).
           // پاک‌سازی محتوای textarea، اجازه دادن HTML امن پایه (مشابه محتوای پست).
           // wp_unslash is important here before sanitization.
           // wp_unslash در اینجا قبل از پاک‌سازی مهم است.
           $cleaned_text = wp_kses_post(wp_unslash($_POST[$footer_text_key]));
           update_post_meta($post_id, $footer_text_key, $cleaned_text);
       }
       // Note: If the textarea is submitted empty, $_POST[$footer_text_key] will be set to '',
       // and wp_kses_post('') returns '', so an empty value is saved correctly.
       // No explicit delete needed unless the field is completely absent from POST (which shouldn't happen with a textarea).
       // نکته: اگر textarea خالی ارسال شود، $_POST[$footer_text_key] برابر '' خواهد بود،
       // و wp_kses_post('') مقدار '' را برمی‌گرداند، بنابراین مقدار خالی به درستی ذخیره می‌شود.
       // نیاز به حذف صریح نیست مگر اینکه فیلد کاملاً در POST غایب باشد (که برای textarea نباید اتفاق بیفتد).


    // Note: No need to return $post_id unless modifying the 'save_post' action itself.
    // نکته: نیازی به بازگرداندن $post_id نیست مگر اینکه خود اکشن 'save_post' را تغییر دهید.
}
// Hook the save function specifically to the CPT's save action for better performance.
// اتصال تابع ذخیره به طور خاص به اکشن ذخیره CPT برای عملکرد بهتر.
add_action('save_post_fb_certificate', 'fb_cert_save_meta_data');

?>