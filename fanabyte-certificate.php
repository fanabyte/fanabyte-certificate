<?php
/**
 * Plugin Name:       FanaByte - Certificate
 * Plugin URI:        https://fanabyte.com/themes-plugins/plugins/fanabyte-plugins/fanabyte-certificate/
 * Description:       Certificate inquiry plugin by FanaByte. Manage and inquire online certificates, warranties, statuses, etc.
 * Version:           1.3.0
 * Author:            FanaByte Academy
 * Author URI:        https://fanabyte.com
 * Text Domain:       fanabyte-certificate
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * License: 	      GPLv2 or later
 * Domain Path:       /languages
 */

// ** Security Check: Prevent direct access to the file. **
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// ** Plugin Constants: Define basic plugin constants. **
define('FB_CERT_VERSION', '1.3.0'); // Plugin version
define('FB_CERT_PATH', plugin_dir_path(__FILE__)); // Plugin directory path
define('FB_CERT_URL', plugin_dir_url(__FILE__)); // Plugin directory URL
define('FB_CERT_TEXT_DOMAIN', 'fanabyte-certificate'); // Text domain for translations

// ** Include Required Files: Load necessary plugin files. **
require_once FB_CERT_PATH . 'includes/cpt-register.php';       // Handles Custom Post Type registration
require_once FB_CERT_PATH . 'includes/admin-settings.php';    // Handles admin settings page
require_once FB_CERT_PATH . 'includes/admin-metaboxes.php';   // Handles metaboxes for the CPT
require_once FB_CERT_PATH . 'includes/admin-columns.php';     // Handles custom admin columns (includes QR helper)
require_once FB_CERT_PATH . 'public/shortcode-lookup.php';    // Handles the [fanabyte_certificate_lookup] shortcode

/**
 * Load Text Domain for Internationalization (i18n).
 */
function fb_cert_load_textdomain() {
    $language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
    $language = isset($language_settings['language']) ? $language_settings['language'] : 'fa';

    if ($language === 'fa') {
        load_plugin_textdomain(
            FB_CERT_TEXT_DOMAIN,
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    // If language is 'en', no translation file is loaded, and the default strings are used.
}
add_action('plugins_loaded', 'fb_cert_load_textdomain');

/**
 * Activation Hook: Runs when the plugin is activated.
 */
function fb_cert_activate() {
    fb_certificate_register_post_type();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'fb_cert_activate');

/**
 * Deactivation Hook: Runs when the plugin is deactivated.
 */
function fb_cert_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'fb_cert_deactivate');

/**
 * Enqueue Admin Scripts and Styles.
 *
 * @param string $hook The current admin page hook.
 */
function fb_cert_admin_enqueue_scripts($hook) {
    $screen = get_current_screen();
    $is_plugin_page = $screen && (
        $screen->id === 'toplevel_page_fanabyte-certificate-settings' ||
        strpos($screen->id, 'fanabyte-certificate') !== false ||
        $screen->post_type === 'fb_certificate'
    );

    if (!$is_plugin_page) {
        return;
    }

    $language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
    $is_rtl = ($language_settings['language'] === 'fa');

    if ($is_rtl) {
        wp_enqueue_style(
            'fb-cert-admin-rtl-style',
            FB_CERT_URL . 'assets/css/admin-rtl.css',
            [],
            FB_CERT_VERSION
        );
    }

    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('jquery-ui-sortable');

    wp_enqueue_script(
        'fb-cert-admin-script',
        FB_CERT_URL . 'assets/js/admin-script.js',
        ['jquery', 'jquery-ui-sortable', 'wp-color-picker'],
        FB_CERT_VERSION,
        true
    );
    wp_enqueue_style(
        'fb-cert-admin-style',
        FB_CERT_URL . 'assets/css/admin-style.css',
        ['wp-color-picker'],
        FB_CERT_VERSION
    );

    wp_localize_script('fb-cert-admin-script', 'fbCertAdminData', [
        'is_rtl' => $is_rtl,
        'labels' => [
            'fieldType' => __('Field Type:', 'fanabyte-certificate'),
            'keyName' => __('Key Name:', 'fanabyte-certificate'),
            'fieldLabel' => __('Field Label:', 'fanabyte-certificate'),
            'isKey' => __('Key field for search?', 'fanabyte-certificate'),
            'moveTitle' => __('Drag to reorder', 'fanabyte-certificate'),
            'deleteButton' => __('Delete', 'fanabyte-certificate'),
            'deleteConfirm' => __('Are you sure you want to delete this field?', 'fanabyte-certificate')
        ],
        'uploader' => [
            'title' => __('Select or Upload', 'fanabyte-certificate'),
            'button' => __('Use this file', 'fanabyte-certificate'),
            'currentFile' => __('Current file:', 'fanabyte-certificate')
        ],
        'copyFeedback' => [
            'copied' => __('Copied!', 'fanabyte-certificate'),
            'error' => __('Error copying code!', 'fanabyte-certificate')
        ]
    ]);
}
add_action('admin_enqueue_scripts', 'fb_cert_admin_enqueue_scripts');

/**
 * Enqueue Public (Frontend) Scripts and Styles.
 */
function fb_cert_public_enqueue_scripts() {
    global $post;
    $load_assets = false;

    if (is_a($post, 'WP_Post')) {
        if (has_shortcode($post->post_content, 'fanabyte_certificate_lookup') || is_singular('fb_certificate')) {
            $load_assets = true;
        }
    }

    global $wp_query;
    if (!$load_assets && isset($wp_query->posts) && is_array($wp_query->posts)) {
        foreach ($wp_query->posts as $global_post) {
            if (is_a($global_post, 'WP_Post') && has_shortcode($global_post->post_content, 'fanabyte_certificate_lookup')) {
                $load_assets = true;
                break;
            }
        }
    }

    if ($load_assets) {
        $language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
        $is_rtl = ($language_settings['language'] === 'fa');

        wp_enqueue_style(
            'fb-cert-public-style',
            FB_CERT_URL . 'assets/css/public-style.css',
            [],
            FB_CERT_VERSION
        );

        if ($is_rtl) {
            wp_enqueue_style(
                'fb-cert-public-rtl-style',
                FB_CERT_URL . 'assets/css/rtl.css',
                ['fb-cert-public-style'],
                FB_CERT_VERSION
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'fb_cert_public_enqueue_scripts');

/**
 * Include Custom Template for Single Certificate View.
 */
function fb_cert_template_include($template) {
    if (is_singular('fb_certificate')) {
        $plugin_template = FB_CERT_PATH . 'templates/single-fb_certificate.php';
        if (file_exists($plugin_template)) {
            $theme_template = locate_template(['single-fb_certificate.php']);
            if ($theme_template) {
                return $theme_template;
            }
            return $plugin_template;
        }
    }
    return $template;
}
add_filter('template_include', 'fb_cert_template_include');
?>