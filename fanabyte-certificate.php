<?php
/**
 * Plugin Name:       FanaByte - Certificate
 * Plugin URI:        https://fanabyte.com/themes-plugins/plugins/fanabyte-plugins/fanabyte-certificate/
 * Description:       Certificate inquiry plugin by FanaByte. Manage and inquire online certificates, warranties, statuses, etc.
 * Version:           1.2.0
 * Author:            FanaByte Academy
 * Author URI:        https://fanabyte.com
 * Text Domain:       fanabyte-certificate
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * License: 	      GPLv2 or later
 * Domain Path:       /languages
 */

// ** Security Check: Prevent direct access to the file. **
// ** بررسی امنیتی: جلوگیری از دسترسی مستقیم به فایل. **
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly. / خروج در صورت دسترسی مستقیم.
}

// ** Plugin Constants: Define basic plugin constants. **
// ** ثابت‌های افزونه: تعریف ثابت‌های پایه‌ای افزونه. **
define('FB_CERT_VERSION', '1.2.0'); // Plugin version / نسخه افزونه
define('FB_CERT_PATH', plugin_dir_path(__FILE__)); // Plugin directory path / مسیر پوشه افزونه
define('FB_CERT_URL', plugin_dir_url(__FILE__)); // Plugin directory URL / آدرس URL پوشه افزونه
define('FB_CERT_TEXT_DOMAIN', 'fanabyte-certificate'); // Text domain for translations / دامنه متن برای ترجمه‌ها

// ** Include Required Files: Load necessary plugin files. **
// ** بارگذاری فایل‌های مورد نیاز: فایل‌های ضروری افزونه را بارگذاری می‌کند. **
require_once FB_CERT_PATH . 'includes/cpt-register.php';       // Handles Custom Post Type registration / مدیریت ثبت پست تایپ سفارشی
require_once FB_CERT_PATH . 'includes/admin-settings.php';    // Handles admin settings page / مدیریت صفحه تنظیمات ادمین
require_once FB_CERT_PATH . 'includes/admin-metaboxes.php';   // Handles metaboxes for the CPT / مدیریت متاباکس‌ها برای پست تایپ سفارشی
require_once FB_CERT_PATH . 'includes/admin-columns.php';     // Handles custom admin columns (includes QR helper) / مدیریت ستون‌های سفارشی ادمین (شامل تابع کمکی QR)
require_once FB_CERT_PATH . 'public/shortcode-lookup.php';    // Handles the [fanabyte_certificate_lookup] shortcode / مدیریت شورت‌کد [fanabyte_certificate_lookup]
// require_once FB_CERT_PATH . 'includes/helpers.php'; // Uncomment if you separate helper functions / اگر توابع کمکی را جدا کردید از کامنت خارج کنید

/**
 * Load Text Domain for Internationalization (i18n).
 * بارگذاری دامنه متن برای بین‌المللی‌سازی (ترجمه).
 */
function fb_cert_load_textdomain() {
    load_plugin_textdomain(
        FB_CERT_TEXT_DOMAIN, // The plugin's text domain / دامنه متن افزونه
        false,                      // Deprecated parameter / پارامتر منسوخ شده
        dirname(plugin_basename(__FILE__)) . '/languages' // Path to the languages directory / مسیر پوشه زبان‌ها
    );
}
// Hook the function to the 'plugins_loaded' action.
// اتصال تابع به اکشن 'plugins_loaded'.
add_action('plugins_loaded', 'fb_cert_load_textdomain');

/**
 * Activation Hook: Runs when the plugin is activated.
 * هوک فعال‌سازی: هنگام فعال شدن افزونه اجرا می‌شود.
 *
 * Registers the custom post type and flushes rewrite rules
 * to ensure the CPT URLs work correctly immediately.
 * پست تایپ سفارشی را ثبت کرده و قوانین بازنویسی را فلاش می‌کند
 * تا اطمینان حاصل شود URL های CPT بلافاصله به درستی کار می‌کنند.
 */
function fb_cert_activate() {
    // Register the custom post type defined in cpt-register.php
    // ثبت پست تایپ سفارشی تعریف شده در cpt-register.php
    fb_certificate_register_post_type();

    // Flush rewrite rules to make the new CPT available.
    // فلاش کردن قوانین بازنویسی تا CPT جدید در دسترس قرار گیرد.
    flush_rewrite_rules();
}
// Register the activation hook.
// ثبت هوک فعال‌سازی.
register_activation_hook(__FILE__, 'fb_cert_activate');

/**
 * Deactivation Hook: Runs when the plugin is deactivated.
 * هوک غیرفعال‌سازی: هنگام غیرفعال شدن افزونه اجرا می‌شود.
 *
 * Flushes rewrite rules to remove the plugin's CPT rules.
 * قوانین بازنویسی را فلاش می‌کند تا قوانین مربوط به CPT افزونه حذف شوند.
 */
function fb_cert_deactivate() {
    // Flush rewrite rules upon deactivation.
    // فلاش کردن قوانین بازنویسی هنگام غیرفعال‌سازی.
    flush_rewrite_rules();
}
// Register the deactivation hook.
// ثبت هوک غیرفعال‌سازی.
register_deactivation_hook(__FILE__, 'fb_cert_deactivate');

/**
 * Enqueue Admin Scripts and Styles.
 * بارگذاری اسکریپت‌ها و استایل‌های بخش مدیریت (ادمین).
 *
 * Loads necessary CSS and JS files for the plugin's admin pages,
 * including WordPress media uploader, color picker, and sortable scripts.
 * فایل‌های CSS و JS لازم برای صفحات ادمین افزونه را بارگذاری می‌کند،
 * شامل آپلودر رسانه وردپرس، انتخابگر رنگ و اسکریپت‌های مرتب‌سازی.
 *
 * @param string $hook The current admin page hook. / هوک صفحه ادمین فعلی.
 */
function fb_cert_admin_enqueue_scripts($hook) {
    // ** Optimization: Only load assets on relevant plugin pages. **
    // ** بهینه‌سازی: بارگذاری فایل‌ها فقط در صفحات مربوط به افزونه. **
    $screen = get_current_screen();
    $is_plugin_page = $screen && (
        $screen->id === 'toplevel_page_fanabyte-certificate-settings' || // Main settings page / صفحه تنظیمات اصلی
        strpos($screen->id, 'fanabyte-certificate') !== false ||       // Other submenus / سایر زیرمنوها
        $screen->post_type === 'fb_certificate'                       // CPT edit/add/list pages / صفحات ویرایش/افزودن/لیست CPT
    );

    // If not a relevant plugin page, do nothing.
    // اگر صفحه مربوط به افزونه نیست، کاری انجام نده.
    if (!$is_plugin_page) {
        return;
    }

    // ** Enqueue WordPress core scripts and styles needed. **
    // ** بارگذاری اسکریپت‌ها و استایل‌های هسته وردپرس مورد نیاز. **
    wp_enqueue_media(); // Needed for the media uploader / لازم برای آپلودر رسانه
    wp_enqueue_style('wp-color-picker'); // Needed for the color picker / لازم برای انتخابگر رنگ
    wp_enqueue_script('jquery-ui-sortable'); // Needed for sortable fields / لازم برای فیلدهای قابل مرتب‌سازی

    // ** Enqueue Custom Admin Scripts and Styles. **
    // ** بارگذاری اسکریپت‌ها و استایل‌های سفارشی ادمین. **
    wp_enqueue_script(
        'fb-cert-admin-script', // Handle / شناسه
        FB_CERT_URL . 'assets/js/admin-script.js', // Source URL / آدرس منبع
        ['jquery', 'jquery-ui-sortable', 'wp-color-picker'], // Dependencies / وابستگی‌ها
        FB_CERT_VERSION, // Version / نسخه
        true // Load in footer / بارگذاری در فوتر
    );
    wp_enqueue_style(
        'fb-cert-admin-style', // Handle / شناسه
        FB_CERT_URL . 'assets/css/admin-style.css', // Source URL / آدرس منبع
        ['wp-color-picker'], // Dependencies (ensure color picker styles load first) / وابستگی‌ها (اطمینان از بارگذاری استایل انتخابگر رنگ اول)
        FB_CERT_VERSION // Version / نسخه
    );

    // ** Localize script (optional): Pass PHP data to JavaScript. **
    // ** محلی‌سازی اسکریپت (اختیاری): ارسال داده از PHP به جاوااسکریپت. **
    // Example: wp_localize_script('fb-cert-admin-script', 'fbCertAdminData', ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('fb_cert_admin_nonce')]);
}
// Hook the function to the 'admin_enqueue_scripts' action.
// اتصال تابع به اکشن 'admin_enqueue_scripts'.
add_action('admin_enqueue_scripts', 'fb_cert_admin_enqueue_scripts');

/**
 * Enqueue Public (Frontend) Scripts and Styles.
 * بارگذاری اسکریپت‌ها و استایل‌های بخش عمومی (فرانت‌اند).
 *
 * Loads CSS and potentially JS for the frontend display, specifically
 * when the lookup shortcode or single certificate page is being viewed.
 * فایل‌های CSS و احتمالاً JS برای نمایش در بخش کاربری را بارگذاری می‌کند،
 * به خصوص زمانی که شورت‌کد استعلام یا صفحه تکی مدرک نمایش داده می‌شود.
 */
function fb_cert_public_enqueue_scripts() {
    // ** Optimization: Load assets only when needed. **
    // ** بهینه‌سازی: بارگذاری فایل‌ها فقط در صورت نیاز. **
    global $post;
    $load_assets = false;

    // Check if the post object exists and is a WP_Post instance.
    // بررسی وجود آبجکت پست و اینکه از نوع WP_Post باشد.
    if (is_a($post, 'WP_Post')) {
        // Check if the post content contains the shortcode OR if it's a single certificate view.
        // بررسی اینکه آیا محتوای پست شامل شورت‌کد است یا در حال مشاهده صفحه تکی مدرک هستیم.
        if (has_shortcode($post->post_content, 'fanabyte_certificate_lookup') || is_singular('fb_certificate')) {
            $load_assets = true;
        }
    }

    // Check specifically for shortcode presence even if $post isn't set yet (e.g., in some blocks/widgets).
    // بررسی وجود شورت‌کد حتی اگر $post هنوز تنظیم نشده باشد (مثلاً در برخی بلوک‌ها/ابزارک‌ها).
    // Note: This check might not be foolproof in all scenarios.
    // توجه: این بررسی ممکن است در همه سناریوها ۱۰۰٪ دقیق نباشد.
    global $wp_query;
    if (!$load_assets && isset($wp_query->posts) && is_array($wp_query->posts)) {
        foreach ($wp_query->posts as $global_post) {
            if (is_a($global_post, 'WP_Post') && has_shortcode($global_post->post_content, 'fanabyte_certificate_lookup')) {
                $load_assets = true;
                break;
            }
        }
    }


    // If assets should be loaded:
    // اگر فایل‌ها باید بارگذاری شوند:
    if ($load_assets) {
        // ** Enqueue Public Stylesheet. **
        // ** بارگذاری استایل‌شیت عمومی. **
        wp_enqueue_style(
            'fb-cert-public-style', // Handle / شناسه
            FB_CERT_URL . 'assets/css/public-style.css', // Source URL / آدرس منبع
            [], // Dependencies / وابستگی‌ها
            FB_CERT_VERSION // Version / نسخه
        );

        // ** Enqueue RTL Stylesheet if needed. **
        // ** بارگذاری استایل‌شیت RTL در صورت نیاز. **
        if (is_rtl()) {
            wp_enqueue_style(
                'fb-cert-public-rtl-style', // Handle / شناسه
                FB_CERT_URL . 'assets/css/rtl.css', // Source URL for RTL styles / آدرس منبع استایل‌های RTL
                ['fb-cert-public-style'], // Dependency: Load after the main public style / وابستگی: بعد از استایل اصلی عمومی بارگذاری شود
                FB_CERT_VERSION // Version / نسخه
            );
        }

        // ** Enqueue Public Script (Optional). **
        // ** بارگذاری اسکریپت عمومی (اختیاری). **
        // wp_enqueue_script('fb-cert-public-script', FB_CERT_URL . 'assets/js/public-script.js', ['jquery'], FB_CERT_VERSION, true);
        // wp_localize_script('fb-cert-public-script', 'fbCertAjax', ['ajax_url' => admin_url('admin-ajax.php')]); // Example for AJAX / مثال برای AJAX
    }
}
// Hook the function to the 'wp_enqueue_scripts' action.
// اتصال تابع به اکشن 'wp_enqueue_scripts'.
add_action('wp_enqueue_scripts', 'fb_cert_public_enqueue_scripts');


/**
 * Include Custom Template for Single Certificate View.
 * بارگذاری قالب سفارشی برای نمایش تکی مدرک.
 *
 * Checks if the current view is for a single 'fb_certificate' post type
 * and attempts to load a custom template from the plugin's 'templates' directory.
 * Allows themes to override this template by placing a file named
 * 'single-fb_certificate.php' in the theme's directory.
 * بررسی می‌کند که آیا نمای فعلی برای یک پست تکی از نوع 'fb_certificate' است
 * و تلاش می‌کند قالب سفارشی را از پوشه 'templates' افزونه بارگذاری کند.
 * به پوسته‌ها اجازه می‌دهد با قرار دادن فایلی به نام 'single-fb_certificate.php'
 * در پوشه پوسته، این قالب را بازنویسی کنند.
 *
 * @param string $template The template file path suggested by WordPress. / مسیر فایل قالب پیشنهادی توسط وردپرس.
 * @return string The template file path to be used. / مسیر فایل قالبی که باید استفاده شود.
 */
function fb_cert_template_include($template) {
    // Check if we are viewing a single post of the 'fb_certificate' CPT.
    // بررسی اینکه آیا در حال مشاهده یک پست تکی از نوع 'fb_certificate' هستیم.
    if (is_singular('fb_certificate')) {
        // Define the path to the custom template within the plugin.
        // تعریف مسیر قالب سفارشی داخل افزونه.
        $plugin_template = FB_CERT_PATH . 'templates/single-fb_certificate.php';

        // Check if the custom template file exists in the plugin.
        // بررسی وجود فایل قالب سفارشی در افزونه.
        if (file_exists($plugin_template)) {
            // ** Theme Override Check: Look for the template in the theme/child theme first. **
            // ** بررسی بازنویسی پوسته: ابتدا به دنبال قالب در پوسته/پوسته فرزند بگرد. **
            $theme_template = locate_template(['single-fb_certificate.php']);

            // If the template exists in the theme, use it.
            // اگر قالب در پوسته وجود داشت، از آن استفاده کن.
            if ($theme_template) {
                return $theme_template;
            }

            // Otherwise, use the template from the plugin.
            // در غیر این صورت، از قالب افزونه استفاده کن.
            return $plugin_template;
        }
    }

    // If it's not a single certificate view or the template doesn't exist,
    // return the default template suggested by WordPress.
    // اگر نمای تکی مدرک نیست یا قالب وجود ندارد،
    // قالب پیش‌فرض پیشنهادی وردپرس را برگردان.
    return $template;
}
// Hook the function to the 'template_include' filter.
// اتصال تابع به فیلتر 'template_include'.
add_filter('template_include', 'fb_cert_template_include');

?>