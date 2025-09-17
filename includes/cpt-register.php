<?php
/**
 * File for registering the Custom Post Type for Certificates (fb_certificate).
 * فایل ثبت Custom Post Type برای مدرک‌ها (fb_certificate).
 *
 * Version: 1.3.0 - Updated plugin version.
 * نسخه: 1.3.0 - به‌روزرسانی نسخه افزونه.
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
    $search_settings = get_option('fanabyte_certificate_search_settings');
    $custom_slug = isset($search_settings['cpt_slug']) && !empty($search_settings['cpt_slug'])
                   ? sanitize_title_with_dashes($search_settings['cpt_slug'])
                   : 'certificate';

    // --- Define labels for the CPT ---
    // --- تعریف لیبل‌ها (برچسب‌ها) برای CPT ---
    $labels = array(
        'name'                  => _x('Certificates', 'Post type general name', 'fanabyte-certificate'),
        'singular_name'         => _x('Certificate', 'Post type singular name', 'fanabyte-certificate'),
        'menu_name'             => _x('Certificates', 'Admin Menu text', 'fanabyte-certificate'),
        'name_admin_bar'        => _x('Certificate', 'Add New on Toolbar', 'fanabyte-certificate'),
        'add_new'               => __('Add New', 'fanabyte-certificate'),
        'add_new_item'          => __('Add New Certificate', 'fanabyte-certificate'),
        'new_item'              => __('New Certificate', 'fanabyte-certificate'),
        'edit_item'             => __('Edit Certificate', 'fanabyte-certificate'),
        'view_item'             => __('View Certificate', 'fanabyte-certificate'),
        'view_items'            => __('View Certificates', 'fanabyte-certificate'),
        'all_items'             => __('All Certificates', 'fanabyte-certificate'),
        'search_items'          => __('Search Certificates', 'fanabyte-certificate'),
        'parent_item_colon'     => __('Parent Certificate:', 'fanabyte-certificate'),
        'not_found'             => __('No certificates found.', 'fanabyte-certificate'),
        'not_found_in_trash'    => __('No certificates found in Trash.', 'fanabyte-certificate'),
        'featured_image'        => _x('Certificate Featured Image', 'Overrides the "Featured Image" phrase.', 'fanabyte-certificate'),
        'set_featured_image'    => _x('Set featured image', 'Overrides the "Set featured image" phrase.', 'fanabyte-certificate'),
        'remove_featured_image' => _x('Remove featured image', 'Overrides the "Remove featured image" phrase.', 'fanabyte-certificate'),
        'use_featured_image'    => _x('Use as featured image', 'Overrides the "Use as featured image" phrase.', 'fanabyte-certificate'),
        'archives'              => _x('Certificate Archives', 'The post type archive label.', 'fanabyte-certificate'),
        'insert_into_item'      => _x('Insert into certificate', 'Overrides the "Insert into post/page" phrase.', 'fanabyte-certificate'),
        'uploaded_to_this_item' => _x('Uploaded to this certificate', 'Overrides the "Uploaded to this post/page" phrase.', 'fanabyte-certificate'),
        'filter_items_list'     => _x('Filter certificates list', 'Screen reader text for the filter links.', 'fanabyte-certificate'),
        'items_list_navigation' => _x('Certificates list navigation', 'Screen reader text for the pagination heading.', 'fanabyte-certificate'),
        'items_list'            => _x('Certificates list', 'Screen reader text for the items list heading.', 'fanabyte-certificate'),
    );

    // --- Define main arguments for CPT registration ---
    // --- تعریف آرگومان‌های اصلی برای ثبت CPT ---
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'fanabyte-certificate-settings',
        'query_var'          => true,
        'rewrite'            => array(
            'slug'       => $custom_slug,
            'with_front' => true
        ),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array(
            'title',
            'editor',
            'thumbnail',
            'custom-fields'
        ),
        'show_in_rest'       => true,
    );

    // Finally, register the post type with the key 'fb_certificate' and defined arguments.
    // ثبت نهایی پست تایپ با نام 'fb_certificate' و آرگومان‌های تعریف شده.
    register_post_type('fb_certificate', $args);
}

// Hook the CPT registration function to WordPress's 'init' action.
// اتصال تابع ثبت CPT به هوک 'init' وردپرس.
add_action('init', 'fb_certificate_register_post_type');

?>