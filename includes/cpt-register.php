<?php
/**
 * File for registering the Custom Post Type for Certificates (fb_certificate).
 * فایل ثبت Custom Post Type برای مدرک‌ها (fb_certificate).
 *
 * Version: 1.2.0 - Uses custom slug from settings and adds i18n support.
 * نسخه: 1.2.0 - استفاده از اسلاگ سفارشی از تنظیمات و افزودن پشتیبانی از ترجمه.
 */

// ** Security Check: Prevent direct access to the file. **
// ** بررسی امنیتی: جلوگیری از دسترسی مستقیم به فایل. **
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly. / خروج در صورت دسترسی مستقیم.
}

/**
 * Register the 'fb_certificate' Custom Post Type with a configurable slug.
 * ثبت Custom Post Type 'fb_certificate' با اسلاگ قابل تنظیم.
 *
 * This function is hooked into WordPress's 'init' action.
 * این تابع به هوک 'init' وردپرس متصل می شود.
 */
function fb_certificate_register_post_type() {

    // --- Read custom slug from saved settings ---
    // --- خواندن اسلاگ سفارشی از تنظیمات ذخیره شده ---

    // Get the search/URL settings option.
    // آپشن مربوط به تنظیمات جستجو/URL را دریافت می‌کنیم.
    $search_settings = get_option('fanabyte_certificate_search_settings');

    // Extract the 'cpt_slug' value from the settings array.
    // مقدار فیلد 'cpt_slug' را از آرایه تنظیمات استخراج می‌کنیم.
    // Use the default value 'certificate' if settings don't exist or the field is empty.
    // اگر تنظیمات وجود نداشت یا فیلد cpt_slug خالی بود، از مقدار پیش‌فرض 'certificate' استفاده می‌کنیم.
    $custom_slug = isset($search_settings['cpt_slug']) && !empty($search_settings['cpt_slug'])
                   ? sanitize_title_with_dashes($search_settings['cpt_slug']) // Ensure it's sanitized again (optional precaution). / اطمینان از پاکسازی مجدد (اختیاری).
                   : 'certificate'; // Default value / مقدار پیش‌فرض.

    // --- Define labels for the CPT ---
    // --- تعریف لیبل‌ها (برچسب‌ها) برای CPT ---
    // These labels are displayed in various parts of the WordPress admin area.
    // این لیبل‌ها در بخش‌های مختلف پیشخوان وردپرس نمایش داده می‌شوند.
    $labels = array(
        'name'                  => _x('Certificates', 'Post type general name', 'fanabyte-certificate'), // جمع عمومی
                                    // _x('استعلام مدرک', 'Post type general name', 'fanabyte-certificate'),
        'singular_name'         => _x('Certificate', 'Post type singular name', 'fanabyte-certificate'), // مفرد
                                    // _x('مدرک', 'Post type singular name', 'fanabyte-certificate'),
        'menu_name'             => _x('Certificates', 'Admin Menu text', 'fanabyte-certificate'), // متن منوی ادمین
                                    // _x('استعلام مدرک', 'Admin Menu text', 'fanabyte-certificate'),
        'name_admin_bar'        => _x('Certificate', 'Add New on Toolbar', 'fanabyte-certificate'), // نوار ابزار ادمین -> افزودن جدید
                                    // _x('مدرک', 'Add New on Toolbar', 'fanabyte-certificate'),
        'add_new'               => __('Add New', 'fanabyte-certificate'), // دکمه افزودن جدید
                                    // __('افزودن جدید', 'fanabyte-certificate'),
        'add_new_item'          => __('Add New Certificate', 'fanabyte-certificate'), // عنوان صفحه افزودن جدید
                                    // __('افزودن مدرک جدید', 'fanabyte-certificate'),
        'new_item'              => __('New Certificate', 'fanabyte-certificate'), // متن مورد استفاده در جاوااسکریپت یا موارد دیگر
                                    // __('مدرک جدید', 'fanabyte-certificate'),
        'edit_item'             => __('Edit Certificate', 'fanabyte-certificate'), // عنوان صفحه ویرایش
                                    // __('ویرایش مدرک', 'fanabyte-certificate'),
        'view_item'             => __('View Certificate', 'fanabyte-certificate'), // متن دکمه مشاهده در صفحه ویرایش
                                    // __('مشاهده مدرک', 'fanabyte-certificate'),
        'view_items'            => __('View Certificates', 'fanabyte-certificate'), // متن دکمه مشاهده در نوار ابزار (معمولا برای آرشیو)
                                    // __('مشاهده مدرک‌ها', 'fanabyte-certificate'),
        'all_items'             => __('All Certificates', 'fanabyte-certificate'), // زیرمنوی "همه ..."
                                    // __('همه مدرک‌ها', 'fanabyte-certificate'),
        'search_items'          => __('Search Certificates', 'fanabyte-certificate'), // متن دکمه جستجو در لیست ادمین
                                    // __('جستجوی مدرک‌ها', 'fanabyte-certificate'),
        'parent_item_colon'     => __('Parent Certificate:', 'fanabyte-certificate'), // برای پست تایپ‌های سلسله مراتبی
                                    // __('والد مدرک:', 'fanabyte-certificate'),
        'not_found'             => __('No certificates found.', 'fanabyte-certificate'), // پیام وقتی هیچ موردی یافت نشود
                                    // __('هیچ مدرکی یافت نشد.', 'fanabyte-certificate'),
        'not_found_in_trash'    => __('No certificates found in Trash.', 'fanabyte-certificate'), // پیام وقتی در زباله‌دان چیزی یافت نشود
                                    // __('هیچ مدرکی در زباله‌دان یافت نشد.', 'fanabyte-certificate'),
        'featured_image'        => _x('Certificate Featured Image', 'Overrides the "Featured Image" phrase.', 'fanabyte-certificate'), // عنوان متاباکس تصویر شاخص
                                    // _x('تصویر شاخص مدرک', 'Overrides the "Featured Image" phrase.', 'fanabyte-certificate'),
        'set_featured_image'    => _x('Set featured image', 'Overrides the "Set featured image" phrase.', 'fanabyte-certificate'), // متن لینک "تنظیم تصویر شاخص"
                                    // _x('تنظیم تصویر شاخص', 'Overrides the "Set featured image" phrase.', 'fanabyte-certificate'),
        'remove_featured_image' => _x('Remove featured image', 'Overrides the "Remove featured image" phrase.', 'fanabyte-certificate'), // متن لینک "حذف تصویر شاخص"
                                    // _x('حذف تصویر شاخص', 'Overrides the "Remove featured image" phrase.', 'fanabyte-certificate'),
        'use_featured_image'    => _x('Use as featured image', 'Overrides the "Use as featured image" phrase.', 'fanabyte-certificate'), // متن دکمه در مدیا آپلودر
                                    // _x('استفاده به عنوان تصویر شاخص', 'Overrides the "Use as featured image" phrase.', 'fanabyte-certificate'),
        'archives'              => _x('Certificate Archives', 'The post type archive label.', 'fanabyte-certificate'), // برچسب صفحه آرشیو
                                    // _x('بایگانی مدرک‌ها', 'The post type archive label.', 'fanabyte-certificate'),
        'insert_into_item'      => _x('Insert into certificate', 'Overrides the "Insert into post/page" phrase.', 'fanabyte-certificate'), // متن دکمه در مدیا آپلودر
                                    // _x('افزودن به مدرک', 'Overrides the "Insert into post/page" phrase.', 'fanabyte-certificate'),
        'uploaded_to_this_item' => _x('Uploaded to this certificate', 'Overrides the "Uploaded to this post/page" phrase.', 'fanabyte-certificate'), // متن در مدیا لایبرری
                                    // _x('آپلود شده در این مدرک', 'Overrides the "Uploaded to this post/page" phrase.', 'fanabyte-certificate'),
        'filter_items_list'     => _x('Filter certificates list', 'Screen reader text for the filter links.', 'fanabyte-certificate'), // متن برای صفحه خوان (فیلتر)
                                    // _x('فیلتر لیست مدرک‌ها', 'Screen reader text for the filter links.', 'fanabyte-certificate'),
        'items_list_navigation' => _x('Certificates list navigation', 'Screen reader text for the pagination heading.', 'fanabyte-certificate'), // متن برای صفحه خوان (پیمایش)
                                    // _x('پیمایش لیست مدرک‌ها', 'Screen reader text for the pagination heading.', 'fanabyte-certificate'),
        'items_list'            => _x('Certificates list', 'Screen reader text for the items list heading.', 'fanabyte-certificate'), // متن برای صفحه خوان (لیست)
                                    // _x('لیست مدرک‌ها', 'Screen reader text for the items list heading.', 'fanabyte-certificate'),
    );

    // --- Define main arguments for CPT registration ---
    // --- تعریف آرگومان‌های اصلی برای ثبت CPT ---
    $args = array(
        'labels'             => $labels,                      // Labels defined above / لیبل‌های تعریف شده در بالا
        'public'             => true,                        // Should be publicly accessible (important for permalinks & QR) / آیا در بخش کاربری قابل دسترسی باشد (برای لینک مستقیم و QR مهم است)
        'publicly_queryable' => true,                        // Can be queried publicly / آیا بتوان از طریق کوئری‌های وردپرس در بخش کاربری به آن دسترسی داشت
        'show_ui'            => true,                        // Show in the WordPress admin UI / آیا در پیشخوان وردپرس نمایش داده شود
        'show_in_menu'       => 'fanabyte-certificate-settings', // Show under the main plugin menu (parent menu slug) / نمایش زیر منوی اصلی افزونه (اسلاگ منوی والد)
        'query_var'          => true,                        // Enable query variable using the post type key (fb_certificate) / فعال کردن query variable با نام پست تایپ (fb_certificate)
        // *** Important: Use the custom slug in rewrite rules ***
        // *** مهم: استفاده از اسلاگ سفارشی در قوانین بازنویسی URL ***
        'rewrite'            => array(
            'slug'       => $custom_slug,                // Slug read from settings or the default value / اسلاگ خوانده شده از تنظیمات یا مقدار پیش‌فرض
            'with_front' => true                       // Should the permalink structure prefix (e.g., /blog/) be prepended? (Usually true) / آیا پیشوند ساختار پیوند یکتا (مثلا /blog/) قبل از آن بیاید؟ (معمولا true)
        ),
        'capability_type'    => 'post',                      // Capability type (like standard posts) / نوع دسترسی‌ها مشابه "نوشته‌ها" باشد
        'has_archive'        => false,                       // Should it have an archive page? (Usually false for certificates) / آیا صفحه آرشیو برای نمایش لیست همه مدرک‌ها داشته باشد؟ (معمولا false)
        'hierarchical'       => false,                       // Is it hierarchical (like pages)? (Usually false) / آیا سلسله مراتبی باشد (مانند برگه‌ها)؟ (معمولا false)
        'menu_position'      => null,                        // Position in the main menu (not needed as it's a submenu) / موقعیت در منوی اصلی (اگر show_in_menu برابر true بود) - اینجا لازم نیست چون زیرمنو است
        'supports'           => array(                       // Features this post type supports / قابلیت‌هایی که این پست تایپ پشتیبانی می‌کند
            'title',                                          // Certificate title / عنوان مدرک
            'editor',                                         // Main content editor (for potential descriptions) / ویرایشگر محتوای اصلی (برای توضیحات احتمالی)
            'thumbnail',                                      // Featured image (optional, perhaps for list view) / تصویر شاخص (اختیاری، شاید برای نمایش لیست)
            'custom-fields'                                   // General support for custom fields (though we use metaboxes) / پشتیبانی کلی از فیلدهای سفارشی (هرچند از متاباکس استفاده می‌کنیم)
            // 'excerpt' // Excerpt (optional) / خلاصه (اختیاری)
            // 'author' // Author (optional) / نویسنده (اختیاری)
        ),
        'show_in_rest'       => true,                        // Enable in Block Editor (Gutenberg) and WordPress REST API / فعال بودن در ویرایشگر بلوک (گوتنبرگ) و REST API وردپرس
        // 'taxonomies'      => array('category', 'post_tag'), // Uncomment if you need categories or tags / اگر نیاز به دسته‌بندی یا برچسب دارید از کامنت خارج کنید
    );

    // Finally, register the post type with the key 'fb_certificate' and defined arguments.
    // ثبت نهایی پست تایپ با نام 'fb_certificate' و آرگومان‌های تعریف شده.
    register_post_type('fb_certificate', $args);
}

// Hook the CPT registration function to WordPress's 'init' action.
// اتصال تابع ثبت CPT به هوک 'init' وردپرس.
// This hook runs after WordPress is fully loaded but before headers are sent.
// این هوک پس از بارگذاری کامل وردپرس و قبل از ارسال هدرها اجرا می‌شود.
add_action('init', 'fb_certificate_register_post_type');

?>