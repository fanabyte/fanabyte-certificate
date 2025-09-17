<?php
/**
 * File for managing metaboxes for the Certificate CPT (fb_certificate).
 * فایل مدیریت متاباکس‌ها برای CPT مدرک (fb_certificate).
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
 * Add custom metaboxes to the Certificate edit screen using configurable titles.
 * افزودن متاباکس‌های سفارشی به صفحه ویرایش مدرک با استفاده از عناوین قابل تنظیم.
 */
function fb_cert_add_metaboxes() {
    // Read settings to get configurable metabox titles.
    // خواندن تنظیمات برای دریافت عناوین متاباکس‌های قابل تنظیم.
    $settings = get_option('fanabyte_certificate_search_settings');
    $language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
    $is_rtl = ($language_settings['language'] === 'fa');

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
 * @param WP_Post $post The current post object. / آبجکت پست فعلی.
 */
function fb_cert_main_data_metabox_callback($post) {
    wp_nonce_field('fb_cert_save_meta_data', 'fb_cert_meta_nonce');

    $settings = get_option('fanabyte_certificate_search_settings');
    $personal_photo_label = $settings['label_personal_photo'] ?? __('Personal Photo (Optional)', 'fanabyte-certificate');

    $defined_fields = get_option('fanabyte_certificate_fields', []);

    echo '<div ' . ( (get_option('fanabyte_certificate_language_settings', ['language' => 'fa'])['language'] === 'fa') ? 'dir="rtl"' : 'dir="ltr"' ) . '>';

    if (!empty($defined_fields) && is_array($defined_fields)) {
        echo '<h4>' . esc_html__('Custom Fields', 'fanabyte-certificate') . '</h4>';
        echo '<table class="form-table">';
        foreach ($defined_fields as $field_key => $field_config) {
             if (!is_array($field_config)) continue;
             $label = isset($field_config['label']) ? esc_html($field_config['label']) : esc_html__('Untitled', 'fanabyte-certificate');
             $type = isset($field_config['type']) ? $field_config['type'] : 'text';
             $meta_key = '_fb_cert_' . sanitize_key($field_key);
             $value = get_post_meta($post->ID, $meta_key, true);

             echo '<tr>';
             echo '<th scope="row"><label for="' . esc_attr($meta_key) . '">' . $label . ':</label></th>';
             echo '<td>';

             if ($type === 'text') {
                 echo '<input type="text" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value) . '" class="regular-text" />';
             } elseif ($type === 'image') {
                 $image_id = absint($value);
                 $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
                 echo '<div class="fb-cert-image-uploader">';
                 echo '<img src="' . esc_url($image_url) . '" style="max-width: 100px; height: auto; display: ' . (empty($image_url) ? 'none' : 'inline-block') . '; vertical-align: middle; margin-left: 10px;" data-preview-for="' . esc_attr($meta_key) . '" />';
                 echo '<input type="hidden" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($image_id) . '" />';
                 echo '<button type="button" class="button fb-upload-button" data-input-id="' . esc_attr($meta_key) . '" data-library-type="image">' . esc_html__('Select/Upload Image', 'fanabyte-certificate') . '</button>';
                 echo '<button type="button" class="button fb-remove-button" data-input-id="' . esc_attr($meta_key) . '" style="margin-right: 5px; display: ' . (empty($image_id) ? 'none' : 'inline-block') . ';">' . esc_html__('Remove', 'fanabyte-certificate') . '</button>';
                 echo '</div>';
             }
             echo '</td>';
             echo '</tr>';
        }
        echo '</table><hr>';
    } else {
        echo '<p>' . sprintf(
                esc_html__('To add information, first define the desired fields on the %s page.', 'fanabyte-certificate'),
                '<a href="' . esc_url(admin_url('admin.php?page=fanabyte-certificate-settings&tab=fields')) . '"><strong>' . esc_html__('Certificates -> Settings -> Field Management', 'fanabyte-certificate') . '</strong></a>'
             ) . '</p><hr>';
    }

    echo '<h4>' . esc_html($personal_photo_label) . '</h4>';
    echo '<table class="form-table"><tbody><tr>';
    echo '<th scope="row"><label for="_personal_photo_id">' . esc_html__('Select Image:', 'fanabyte-certificate') . '</label></th>';
    echo '<td>';
    $personal_photo_id = get_post_meta($post->ID, '_personal_photo_id', true);
    $personal_photo_url = $personal_photo_id ? wp_get_attachment_image_url(absint($personal_photo_id), 'thumbnail') : '';
    echo '<div class="fb-cert-image-uploader">';
    echo '<img src="' . esc_url($personal_photo_url) . '" style="max-width: 100px; height: auto; display: ' . (empty($personal_photo_url) ? 'none' : 'inline-block') . '; vertical-align: middle; margin-left: 10px;" data-preview-for="_personal_photo_id" />';
    echo '<input type="hidden" id="_personal_photo_id" name="_personal_photo_id" value="' . esc_attr($personal_photo_id) . '" />';
    echo '<button type="button" class="button fb-upload-button" data-input-id="_personal_photo_id" data-library-type="image">' . esc_html__('Select/Upload Image', 'fanabyte-certificate') . '</button>';
    echo '<button type="button" class="button fb-remove-button" data-input-id="_personal_photo_id" style="margin-right: 5px; display: ' . (empty($personal_photo_id) ? 'none' : 'inline-block') . ';">' . esc_html__('Remove', 'fanabyte-certificate') . '</button>';
    echo '<p class="description">' . esc_html__('Select a square image to display next to the title (allowed formats: jpg, jpeg, png).', 'fanabyte-certificate') . '</p>';
    echo '</div>';
    echo '</td>';
    echo '</tr></tbody></table><hr>';

    echo '<h4>' . esc_html__('Custom Footer Text (Optional)', 'fanabyte-certificate') . '</h4>';
    echo '<table class="form-table"><tbody><tr>';
    echo '<th scope="row"><label for="_certificate_footer_text">' . esc_html__('Enter text:', 'fanabyte-certificate') . '</label></th>';
    echo '<td>';
    $footer_text = get_post_meta($post->ID, '_certificate_footer_text', true);
    echo '<textarea id="_certificate_footer_text" name="_certificate_footer_text" rows="5" class="large-text">' . esc_textarea($footer_text) . '</textarea>';
    echo '<p class="description">' . esc_html__('This text will be displayed at the bottom of the page (in the footer section, right side).', 'fanabyte-certificate') . '</p>';
    echo '</td>';
    echo '</tr></tbody></table>';
    
    echo '</div>'; // Close the dir wrapper
    
    ?>
    <script>
    jQuery(document).ready(function($){
        var fbCertAdminData = window.fbCertAdminData || {
            uploader: {
                title: '<?php echo esc_js(__("Select or Upload", "fanabyte-certificate")); ?>',
                button: '<?php echo esc_js(__("Use this file", "fanabyte-certificate")); ?>',
                currentFile: '<?php echo esc_js(__("Current file:", "fanabyte-certificate")); ?>'
            }
        };

        function openMediaUploader(inputId, libraryType = 'image') {
            var frame = wp.media({
                title: fbCertAdminData.uploader.title,
                button: { text: fbCertAdminData.uploader.button },
                library: { type: libraryType },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                var $inputField = $('#' + inputId);
                var $previewImage = $('img[data-preview-for="' + inputId + '"]');
                var $removeButton = $('button.fb-remove-button[data-input-id="' + inputId + '"]');
                var $fileDisplay = $('.fb-current-file-display[data-input-id="' + inputId + '"]');

                $inputField.val(attachment.id).trigger('change');

                if ($previewImage.length) {
                    var thumbnailUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                    $previewImage.attr('src', thumbnailUrl).show();
                }
                if ($fileDisplay.length) {
                    var fileLink = '<strong>' + fbCertAdminData.uploader.currentFile + '</strong> <a href="' + attachment.url + '" target="_blank">' + attachment.filename + '</a>';
                    $fileDisplay.html(fileLink).show();
                }
                 $removeButton.show();
            });

            frame.open();
        }

        $('body').off('click.fbCertUploader').on('click.fbCertUploader', '.fb-upload-button', function(e) {
            e.preventDefault();
            var inputId = $(this).data('input-id');
            var libraryType = $(this).data('library-type') || 'image';
            if (typeof libraryType === 'string' && libraryType.includes(',')) {
                libraryType = libraryType.split(',').map(s => s.trim());
            }
            openMediaUploader(inputId, libraryType);
        });

        $('body').off('click.fbCertRemover').on('click.fbCertRemover', '.fb-remove-button', function(e) {
            e.preventDefault();
            var inputId = $(this).data('input-id');
            var $inputField = $('#' + inputId);
            var $previewImage = $('img[data-preview-for="' + inputId + '"]');
            var $fileDisplay = $('.fb-current-file-display[data-input-id="' + inputId + '"]');

            $inputField.val('').trigger('change');
            if($previewImage.length) {
                $previewImage.attr('src', '').hide();
            }
            if($fileDisplay.length) {
                $fileDisplay.empty().hide();
            }
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
 * @param WP_Post $post The current post object. / آبجکت پست فعلی.
 */
function fb_cert_file_metabox_callback($post) {
    $file_id = get_post_meta($post->ID, '_certificate_file_id', true);
    $file_url = $file_id ? wp_get_attachment_url($file_id) : '';
    $file_name = $file_id ? basename(get_attached_file($file_id)) : '';

    echo '<div class="fb-cert-file-uploader">';

    echo '<p class="fb-current-file-display" data-input-id="_certificate_file_id" style="margin-bottom: 8px; display: ' . ($file_url ? 'block' : 'none') . ';">';
    if ($file_url) {
        echo '<strong>' . esc_html__('Current file:', 'fanabyte-certificate') . '</strong> <a href="' . esc_url($file_url) . '" target="_blank">' . esc_html($file_name) . '</a>';
    }
    echo '</p>';

    echo '<input type="hidden" id="_certificate_file_id" name="_certificate_file_id" value="' . esc_attr($file_id) . '" />';

    echo '<button type="button" class="button fb-upload-button" data-input-id="_certificate_file_id" data-library-type="application/pdf,image/jpeg,image/png,image/jpg">' . esc_html__('Select/Upload File', 'fanabyte-certificate') . '</button>';

    echo '<button type="button" class="button fb-remove-button" data-input-id="_certificate_file_id" style="margin-right: 5px; display: ' . (empty($file_id) ? 'none' : 'inline-block') . ';">' . esc_html__('Remove', 'fanabyte-certificate') . '</button>';

    echo '<p class="description" style="margin-top: 8px;">' . esc_html__('The main PDF or image file of the certificate.', 'fanabyte-certificate') . '</p>';
    echo '</div>';
}

/**
 * Save the data from the custom metaboxes.
 * ذخیره داده‌های متاباکس‌های سفارشی.
 *
 * @param int $post_id The ID of the post being saved. / شناسه پستی که در حال ذخیره شدن است.
 */
function fb_cert_save_meta_data($post_id) {
    if (!isset($_POST['fb_cert_meta_nonce']) || !wp_verify_nonce($_POST['fb_cert_meta_nonce'], 'fb_cert_save_meta_data')) {
        return $post_id;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    if (!isset($_POST['post_type']) || 'fb_certificate' !== $_POST['post_type']) {
        return $post_id;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    $defined_fields = get_option('fanabyte_certificate_fields', []);
    if (!empty($defined_fields) && is_array($defined_fields)) {
        foreach ($defined_fields as $field_key => $field_config) {
            if (!is_array($field_config)) continue;
            $meta_key = '_fb_cert_' . sanitize_key($field_key);
            if (isset($_POST[$meta_key])) {
                $value = $_POST[$meta_key];
                $value_to_save = '';
                if (isset($field_config['type']) && $field_config['type'] === 'text') {
                     $value_to_save = sanitize_text_field(wp_unslash($value));
                 } elseif (isset($field_config['type']) && $field_config['type'] === 'image') {
                     $value_to_save = absint($value);
                 }
                 update_post_meta($post_id, $meta_key, $value_to_save);
            } else {
                 delete_post_meta($post_id, $meta_key);
            }
        }
    }

    $file_meta_key = '_certificate_file_id';
    if (isset($_POST[$file_meta_key])) {
         $file_id = absint($_POST[$file_meta_key]);
         if ($file_id > 0) {
            update_post_meta($post_id, $file_meta_key, $file_id);
         } else {
             delete_post_meta($post_id, $file_meta_key);
         }
     } else {
          delete_post_meta($post_id, $file_meta_key);
     }

     $personal_photo_key = '_personal_photo_id';
      if (isset($_POST[$personal_photo_key])) {
         $photo_id = absint($_POST[$personal_photo_key]);
         if ($photo_id > 0) {
            update_post_meta($post_id, $personal_photo_key, $photo_id);
         } else {
             delete_post_meta($post_id, $personal_photo_key);
         }
     } else {
          delete_post_meta($post_id, $personal_photo_key);
     }

      $footer_text_key = '_certificate_footer_text';
       if (isset($_POST[$footer_text_key])) {
           $cleaned_text = wp_kses_post(wp_unslash($_POST[$footer_text_key]));
           update_post_meta($post_id, $footer_text_key, $cleaned_text);
       }
}
add_action('save_post_fb_certificate', 'fb_cert_save_meta_data');
?>