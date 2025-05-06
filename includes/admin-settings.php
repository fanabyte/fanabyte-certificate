<?php
/**
 * File for managing the Fanabyte Certificate plugin settings page.
 * فایل مدیریت صفحه تنظیمات افزونه Fanabyte Certificate.
 *
 * Version: 1.2.0 - Added donation link to About page, i18n support, and comprehensive comments.
 * نسخه: 1.2.0 - افزودن لینک حمایت مالی به صفحه درباره ما، پشتیبانی از ترجمه و کامنت‌های جامع.
 */

// ** Security Check: Prevent direct access to the file. **
// ** بررسی امنیتی: جلوگیری از دسترسی مستقیم به فایل. **
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly. / خروج در صورت دسترسی مستقیم.
}

/**
 * Add the main menu and submenus for the plugin to the WordPress admin dashboard.
 * اضافه کردن منو و زیرمنوهای افزونه به پیشخوان وردپرس.
 */
function fb_cert_add_admin_menu() {
    // Add the top-level menu page.
    // اضافه کردن منوی سطح بالا.
    add_menu_page(
        __('FanaByte Certificates', 'fanabyte-certificate'), // Page title / عنوان صفحه
        __('Certificate Inquiry', 'fanabyte-certificate'), // Menu title / عنوان منو
        'manage_options', // Capability required / سطح دسترسی لازم
        'fanabyte-certificate-settings', // Menu slug / اسلاگ صفحه منو
        'fb_cert_settings_page_callback', // Callback function to display the page content / تابع callback برای نمایش محتوای صفحه اصلی
        'dashicons-awards', // Menu icon / آیکون منو
        30 // Menu position / موقعیت منو
    );

    // Add the Settings submenu (which points to the main page).
    // افزودن زیرمنوی تنظیمات (که همان صفحه اصلی است).
    add_submenu_page(
        'fanabyte-certificate-settings', // Parent slug / اسلاگ منوی والد
        __('Certificate Inquiry Settings', 'fanabyte-certificate'), // Page title / عنوان صفحه
        __('Settings', 'fanabyte-certificate'), // Submenu title / عنوان زیرمنو
        'manage_options', // Capability required / سطح دسترسی لازم
        'fanabyte-certificate-settings', // Menu slug (same as parent for main settings) / اسلاگ منو (همانند والد برای تنظیمات اصلی)
        'fb_cert_settings_page_callback' // Callback function / تابع callback
    );

    // Add the Usage Guide submenu.
    // افزودن زیرمنوی راهنمای استفاده.
    add_submenu_page(
        'fanabyte-certificate-settings', // Parent slug / اسلاگ منوی والد
        __('Plugin Usage Guide', 'fanabyte-certificate'), // Page title / عنوان صفحه
        __('Usage Guide', 'fanabyte-certificate'), // Submenu title / عنوان زیرمنو
        'manage_options', // Capability required / سطح دسترسی لازم
        'fanabyte-certificate-guide', // Unique slug for the guide page / اسلاگ منحصر به فرد صفحه راهنما
        'fb_cert_guide_page_callback' // Callback function to display the guide / تابع callback برای نمایش راهنما
    );

    // Add the About Us submenu.
    // افزودن زیرمنوی درباره ما.
    add_submenu_page(
        'fanabyte-certificate-settings', // Parent slug / اسلاگ منوی والد
        __('About the Plugin', 'fanabyte-certificate'), // Page title / عنوان صفحه
        __('About Us', 'fanabyte-certificate'), // Submenu title / عنوان زیرمنو
        'manage_options', // Capability required / سطح دسترسی لازم
        'fanabyte-certificate-about', // Menu slug / اسلاگ منو
        'fb_cert_about_page_callback' // Callback function / تابع callback
    );

    // Note: CPT (Custom Post Type) submenus are added automatically by register_post_type using 'show_in_menu'.
    // نکته: زیرمنوهای مربوط به CPT به صورت خودکار توسط register_post_type با استفاده از 'show_in_menu' اضافه می‌شوند.
}
// Hook the menu function to the 'admin_menu' action.
// اتصال تابع منو به اکشن 'admin_menu'.
add_action('admin_menu', 'fb_cert_add_admin_menu');

/**
 * Callback function to display the main settings page content with tabs.
 * تابع Callback برای نمایش محتوای صفحه تنظیمات اصلی به همراه تب‌ها.
 */
function fb_cert_settings_page_callback() {
    ?>
    <div class="wrap fb-cert-settings-wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php settings_errors(); // Display settings update errors/messages. / نمایش خطاها/پیام‌های ذخیره سازی. ?>

        <h2 class="nav-tab-wrapper">
            <?php
            // Determine the active tab, default to 'fields'.
            // تب فعال را مشخص می‌کند، پیش‌فرض 'fields' است.
            $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'fields';
            ?>
            <a href="?page=fanabyte-certificate-settings&tab=fields" class="nav-tab <?php echo $active_tab == 'fields' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Field Management', 'fanabyte-certificate'); /*مدیریت فیلدها*/ ?></a>
            <a href="?page=fanabyte-certificate-settings&tab=search_form" class="nav-tab <?php echo $active_tab == 'search_form' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Form/URL/Buttons Settings', 'fanabyte-certificate'); /*تنظیمات فرم/URL/دکمه‌ها*/ ?></a>
            <a href="?page=fanabyte-certificate-settings&tab=import_export" class="nav-tab <?php echo $active_tab == 'import_export' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Import/Export', 'fanabyte-certificate'); /*واردات/صادرات*/ ?></a>
        </h2>

        <?php // Display content based on the active tab. / نمایش محتوا بر اساس تب فعال. ?>
        <?php if ($active_tab !== 'import_export') : // If not the Import/Export tab, show the settings form. / اگر تب واردات/صادرات نیست، فرم تنظیمات را نشان بده. ?>
            <form action="options.php" method="post">
                <?php
                // Output nonce, action, and option_page fields for the correct settings group.
                // خروجی فیلدهای nonce، action و option_page برای گروه تنظیمات صحیح.
                if ($active_tab == 'fields') {
                    settings_fields('fb_cert_fields_settings_group'); // Group name for field settings / نام گروه برای تنظیمات فیلد
                    do_settings_sections('fanabyte-certificate-fields'); // Page slug for field settings sections / اسلاگ صفحه برای بخش‌های تنظیمات فیلد
                } elseif ($active_tab == 'search_form') {
                    settings_fields('fb_cert_search_settings_group'); // Group name for search/URL/button settings / نام گروه برای تنظیمات جستجو/URL/دکمه
                    do_settings_sections('fanabyte-certificate-search'); // Page slug for search/URL/button sections / اسلاگ صفحه برای بخش‌های تنظیمات جستجو/URL/دکمه
                }
                // Display the save button. / نمایش دکمه ذخیره.
                submit_button(__('Save Settings', 'fanabyte-certificate') /*ذخیره تنظیمات*/ );
                ?>
            </form>
        <?php else: // Content for the Import/Export tab. / محتوا برای تب واردات/صادرات. ?>
            <?php // Note: Import/Export actions are handled via admin-post.php, not options.php. / نکته: عملیات واردات/صادرات از طریق admin-post.php انجام می‌شود، نه options.php. ?>
            <?php do_settings_sections('fanabyte-certificate-importexport'); // We still call this to potentially show section descriptions. / این را همچنان فراخوانی می‌کنیم تا توضیحات بخش نمایش داده شوند. ?>

            <div class="fb-cert-import-export-section">
                <h3><?php esc_html_e('Export Settings', 'fanabyte-certificate'); /*خروجی گرفتن از تنظیمات*/ ?></h3>
                <p><?php esc_html_e('Click the button below to export the current plugin settings (fields and form/URL/button settings) as a JSON file.', 'fanabyte-certificate'); /*برای گرفتن خروجی از تنظیمات فعلی افزونه ... روی دکمه زیر کلیک کنید.*/ ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="fb_cert_export_settings" /> <?php // Action hook for admin-post.php / هوک اکشن برای admin-post.php ?>
                    <?php wp_nonce_field('fb_cert_export_nonce', 'fb_cert_export_nonce_field'); // Security nonce / نانس امنیتی ?>
                    <?php submit_button(__('Download Export File (JSON)', 'fanabyte-certificate'), 'secondary', 'fb_cert_export_submit', false); /*دریافت فایل خروجی (JSON)*/ ?>
                </form>
            </div>
            <hr>
            <div class="fb-cert-import-export-section">
                <h3><?php esc_html_e('Import Settings', 'fanabyte-certificate'); /*وارد کردن تنظیمات*/ ?></h3>
                <p><?php esc_html_e('Select the JSON settings file you previously exported and upload it here. Note: This will overwrite your current settings.', 'fanabyte-certificate'); /*فایل تنظیمات (با فرمت JSON) که قبلا خروجی گرفته‌اید را انتخاب و بارگذاری کنید. توجه: تنظیمات فعلی بازنویسی خواهند شد.*/ ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data"> <?php // Needs multipart for file upload / نیاز به multipart برای آپلود فایل ?>
                    <input type="hidden" name="action" value="fb_cert_import_settings" /> <?php // Action hook / هوک اکشن ?>
                    <?php wp_nonce_field('fb_cert_import_nonce', 'fb_cert_import_nonce_field'); // Security nonce / نانس امنیتی ?>
                    <p>
                        <label for="fb_cert_import_file"><?php esc_html_e('Select JSON file:', 'fanabyte-certificate'); /*انتخاب فایل JSON:*/ ?></label><br>
                        <input type="file" id="fb_cert_import_file" name="fb_cert_import_file" accept=".json" required />
                    </p>
                    <?php submit_button(__('Upload and Import Settings', 'fanabyte-certificate'), 'primary', 'fb_cert_import_submit', false); /*بارگذاری و درون‌ریزی تنظیمات*/ ?>
                </form>
            </div>
        <?php endif; // End tab content conditional / پایان شرط محتوای تب ?>
    </div><?php
}


/**
 * Callback function to display the About Us page content.
 * تابع Callback برای نمایش محتوای صفحه درباره ما (بازطراحی شده با محتوای کاربر و لینک حمایت).
 */
function fb_cert_about_page_callback() {
    // Define base URL for images (assuming FB_CERT_URL constant exists).
    // تعریف URL پایه برای تصاویر (با فرض وجود ثابت FB_CERT_URL).
    $image_base_url = defined('FB_CERT_URL') ? trailingslashit(FB_CERT_URL . 'assets/images') : '';
    // Define donation link. / تعریف لینک حمایت مالی.
    $donation_link = 'https://www.coffeete.ir/fanabyte';

    // Define social media and website links.
    // تعریف لینک‌های شبکه اجتماعی و وبسایت.
    $social_links = [
        'website'   => 'https://fanabyte.com',
        'youtube'   => '#', // Replace with actual link / با لینک واقعی جایگزین شود
        'instagram' => 'https://instagram.com/fanabyte',
        'threads'   => 'https://threads.net/@fanabyte',
        'facebook'  => 'https://facebook.com/fanabyte',
        'github'    => 'https://github.com/fanabyte',
        'linkedin'  => 'https://www.linkedin.com/company/fanabyte',
        'x'         => 'https://twitter.com/fanabyte',
        'telegram'  => 'https://t.me/fanabyte',
        'pinterest' => 'https://www.pinterest.com/fanabyte',
        'email'     => 'mailto:info@fanabyte.com'
    ];
    ?>
    <div class="wrap fb-about-page-wrap" style="text-align: right; direction: rtl;">
        <h1><?php esc_html_e('About the Plugin and FanaByte Academy', 'fanabyte-certificate'); /*درباره افزونه و آکادمی فنابایت*/ ?></h1>

        <div class="fb-about-section">
            <p style="font-size: 1.1em;"><strong><?php esc_html_e('This plugin was designed and developed by FanaByte Academy.', 'fanabyte-certificate'); /*این افزونه توسط آکادمی فنابایت طراحی و توسعه داده شده است.*/ ?></strong></p>

            <?php if ($image_base_url) : // Only show logo if base URL is valid / فقط اگر URL پایه معتبر بود لوگو را نشان بده ?>
            <div style="margin: 25px 0; text-align: center;">
                <img src="<?php echo esc_url($image_base_url . 'fanabyte-logo.png'); ?>"
                     alt="<?php esc_attr_e('FanaByte Academy Logo', 'fanabyte-certificate'); /*لوگو آکادمی فنابایت*/ ?>"
                     style="max-width: 180px; height: auto;" class="fb-about-logo">
            </div>
            <?php endif; ?>
        </div>

        <hr>

        <div class="fb-about-section">
            <h2><?php esc_html_e('The FanaByte Story', 'fanabyte-certificate'); /*داستان فنابایت*/ ?></h2>
            <div style="line-height: 1.8;">
                <p><?php esc_html_e('We started in 1400 (Persian calendar), or more accurately, since 1390. In the beginning, we didn\'t have an official website. FanaByte\'s goal is to help everyone build a successful online business.', 'fanabyte-certificate'); /*از سال 1400 شروع به‌کار کردیم ...*/ ?></p>
                <p><?php esc_html_e('But how? At FanaByte, we teach you how to set up and use the world\'s best website builder, which powers over 40% of the world\'s websites.', 'fanabyte-certificate'); /*اما چطور؟ ...*/ ?></p>
                <p><?php esc_html_e('We have also published many free articles and video tutorials, as well as several training packages in various fields, which you can use to increase your knowledge and develop your business.', 'fanabyte-certificate'); /*هم‌چنین کلی مقاله و ویدئو ...*/ ?></p>
            </div>
        </div>

        <hr>

        <div class="fb-about-section">
            <h2><?php esc_html_e('Follow Us!', 'fanabyte-certificate'); /*ما را دنبال کنید!*/ ?></h2>
            <p><?php esc_html_e('Please follow us on social media:', 'fanabyte-certificate'); /*لطفا ما را در شبکه‌های اجتماعی دنبال کنید:*/ ?></p>

            <div class="fb-social-icons" style="margin-top: 20px; text-align: center; line-height: 1;">
                <?php if ($image_base_url) : // Display icons only if base URL is valid / نمایش آیکون‌ها فقط اگر URL پایه معتبر بود ?>
                    <?php
                    // Define filenames and titles for simplicity.
                    // تعریف نام فایل‌ها و title ها برای سادگی.
                    $icons = [ /* ... icon definitions as before ... */
                        'youtube'   => ['file' => 'YouTube.png',   'title' => __('FanaByte YouTube', 'fanabyte-certificate')],
                        'instagram' => ['file' => 'Instagram.png', 'title' => __('FanaByte Instagram', 'fanabyte-certificate')],
                        'threads'   => ['file' => 'Threads.png',   'title' => __('FanaByte Threads', 'fanabyte-certificate')],
                        'facebook'  => ['file' => 'Facebook.png',  'title' => __('FanaByte Facebook', 'fanabyte-certificate')],
                        'github'    => ['file' => 'GitHub.png',    'title' => __('FanaByte GitHub', 'fanabyte-certificate')],
                        'linkedin'  => ['file' => 'LinkedIn.png',  'title' => __('FanaByte LinkedIn', 'fanabyte-certificate')],
                        'x'         => ['file' => 'X.png',         'title' => __('FanaByte X (Twitter)', 'fanabyte-certificate')],
                        'telegram'  => ['file' => 'Telegram.png',  'title' => __('FanaByte Telegram', 'fanabyte-certificate')],
                        'pinterest' => ['file' => 'Pinterest.png', 'title' => __('FanaByte Pinterest', 'fanabyte-certificate')],
                        'email'     => ['file' => 'Email.png',     'title' => __('FanaByte Email', 'fanabyte-certificate')]
                    ];

                    foreach ($icons as $key => $icon_data) {
                        if (!empty($social_links[$key])) {
                            $target_blank = ($key !== 'email') ? 'target="_blank" rel="noopener noreferrer"' : '';
                            echo '<a href="' . esc_url($social_links[$key]) . '" ' . $target_blank . ' title="' . esc_attr($icon_data['title']) . '" style="margin: 0 7px; display: inline-block;">';
                            echo '<img src="' . esc_url($image_base_url . $icon_data['file']) . '" alt="' . esc_attr($icon_data['title']) . '" width="32" height="32" class="fb-social-icon">';
                            echo '</a>';
                        }
                    }
                    ?>

                    <?php // *** START: Donation Section *** / *** شروع: بخش حمایت مالی *** ?>
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed #eee;">
                        <p style="font-size: 1.1em; margin-bottom: 15px;">☕ <?php esc_html_e('If you enjoy this plugin and find it useful, please consider supporting its development by buying us a coffee. Your support is greatly appreciated!', 'fanabyte-certificate'); /*اگر از این افزونه لذت می‌برید و برای شما مفید است، لطفاً با خرید یک قهوه از توسعه آن حمایت کنید. حمایت شما برای ما بسیار ارزشمند است!*/ ?></p>
                        <a href="<?php echo esc_url($donation_link); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary" style="font-size: 1.1em;">
                            <?php esc_html_e('Support FanaByte on Coffeete', 'fanabyte-certificate'); /*حمایت از فنابایت در Coffeete*/ ?>
                        </a>
                    </div>
                    <?php // *** END: Donation Section *** / *** پایان: بخش حمایت مالی *** ?>

                    <p style="margin-top: 25px;">
                         <a href="<?php echo esc_url($social_links['website']); ?>" target="_blank" rel="noopener noreferrer" style="font-weight: bold; text-decoration: none;">
                            <?php esc_html_e('Visit FanaByte Academy Website', 'fanabyte-certificate'); /*مشاهده وب‌سایت آکادمی فنابایت*/ ?>
                         </a>
                    </p>
                <?php else : // Show error if image path is invalid / نمایش خطا اگر مسیر تصاویر نامعتبر بود ?>
                    <p style="color: red;"><?php printf(esc_html__('Error: Image path not found (%s).', 'fanabyte-certificate'), '<code>assets/images</code>'); /*خطا: مسیر تصاویر یافت نشد.*/ ?></p>
                <?php endif; ?>
            </div>
        </div>

    </div><?php
}


/**
 * Register plugin settings, sections, and fields using the WordPress Settings API.
 * ثبت تنظیمات، بخش‌ها و فیلدهای افزونه با استفاده از Settings API وردپرس.
 */
function fb_cert_register_settings() {

    // --- Fields Settings Group ---
    // --- گروه تنظیمات فیلدها ---
    register_setting(
        'fb_cert_fields_settings_group', // Option group name / نام گروه آپشن
        'fanabyte_certificate_fields', // Option name in wp_options table / نام آپشن در جدول wp_options
        'fb_cert_sanitize_fields_settings' // Sanitization callback function / تابع callback پاک‌سازی
    );
    // Add the main section for fields.
    // افزودن بخش اصلی برای فیلدها.
    add_settings_section(
        'fb_cert_fields_section', // Section ID / شناسه بخش
        __('Certificate Fields', 'fanabyte-certificate'), // Section title / عنوان بخش /*فیلدهای مدرک*/
        'fb_cert_fields_section_callback', // Callback for section description / Callback برای توضیحات بخش
        'fanabyte-certificate-fields' // Page slug where the section appears / اسلاگ صفحه‌ای که بخش در آن نمایش داده می‌شود
    );
    // Add the field for managing the list of defined fields.
    // افزودن فیلد برای مدیریت لیست فیلدهای تعریف شده.
    add_settings_field(
        'fb_cert_fields_list', // Field ID / شناسه فیلد
        __('Defined Fields', 'fanabyte-certificate'), // Field label / برچسب فیلد /*فیلدهای تعریف شده*/
        'fb_cert_fields_list_callback', // Callback to render the field UI / Callback برای رندر کردن UI فیلد
        'fanabyte-certificate-fields', // Page slug / اسلاگ صفحه
        'fb_cert_fields_section' // Section ID to attach the field to / شناسه بخشی که فیلد به آن متصل می‌شود
    );

    // --- Search Form / URL / Buttons Settings Group ---
    // --- گروه تنظیمات فرم جستجو / URL / دکمه‌ها ---
    register_setting(
        'fb_cert_search_settings_group', // Option group name / نام گروه آپشن
        'fanabyte_certificate_search_settings', // Option name / نام آپشن
        'fb_cert_sanitize_search_settings' // Updated sanitization callback / تابع پاک‌سازی آپدیت شده
    );

    // Section: Search Form Customization
    // بخش: شخصی‌سازی فرم استعلام
    add_settings_section(
        'fb_cert_search_form_section', // Section ID / شناسه بخش
        __('1. Inquiry Form Customization', 'fanabyte-certificate'), // Section title / عنوان بخش /*شخصی‌سازی فرم استعلام*/
        'fb_cert_search_form_section_callback', // Section description callback / Callback توضیحات بخش
        'fanabyte-certificate-search' // Page slug / اسلاگ صفحه
    );
    // Field: Intro text above the form
    // فیلد: متن بالای فرم استعلام
    add_settings_field(
        'fb_cert_search_intro_text',
        __('Text Above Inquiry Form', 'fanabyte-certificate'), /*متن بالای فرم استعلام*/
        'fb_cert_search_intro_text_callback',
        'fanabyte-certificate-search',
        'fb_cert_search_form_section'
    );
    // Field: Search button text
    // فیلد: متن دکمه جستجو
    add_settings_field(
        'fb_cert_search_button_text',
        __('Search Button Text', 'fanabyte-certificate'), /*متن دکمه جستجو*/
        'fb_cert_search_button_text_callback',
        'fanabyte-certificate-search',
        'fb_cert_search_form_section'
    );
    // Field: Search button color
    // فیلد: رنگ دکمه جستجو
    add_settings_field(
        'fb_cert_search_button_color',
        __('Search Button Color', 'fanabyte-certificate'), /*رنگ دکمه جستجو*/
        'fb_cert_search_button_color_callback',
        'fanabyte-certificate-search',
        'fb_cert_search_form_section'
    );
    // Field: Placeholders for search fields
    // فیلد: متن نگهدارنده فیلدها
    add_settings_field(
        'fb_cert_search_placeholders',
        __('Field Placeholders', 'fanabyte-certificate'), /*متن نگهدارنده فیلدها*/
        'fb_cert_search_placeholders_callback',
        'fanabyte-certificate-search',
        'fb_cert_search_form_section'
    );

    // Section: URL Settings
    // بخش: تنظیمات URL مدرک
    add_settings_section(
        'fb_cert_url_section', // Section ID / شناسه بخش
        __('2. Certificate URL Settings', 'fanabyte-certificate'), // Section title / عنوان بخش /*تنظیمات URL مدرک*/
        'fb_cert_url_section_callback', // Section description callback / Callback توضیحات بخش
        'fanabyte-certificate-search' // Page slug / اسلاگ صفحه
    );
    // Field: Base slug for CPT URLs
    // فیلد: اسلاگ پایه URL
    add_settings_field(
        'fb_cert_cpt_slug',
        __('Base URL Slug', 'fanabyte-certificate'), /*اسلاگ پایه URL*/
        'fb_cert_cpt_slug_callback',
        'fanabyte-certificate-search',
        'fb_cert_url_section'
    );

    // Section: Download Button Settings
    // بخش: تنظیمات دکمه دانلود
    add_settings_section(
        'fb_cert_download_button_section', // Section ID / شناسه بخش
        __('3. Download Button Customization', 'fanabyte-certificate'), // Section title / عنوان بخش /*شخصی‌سازی دکمه دانلود*/
        'fb_cert_download_button_section_callback', // Section description callback / Callback توضیحات بخش
        'fanabyte-certificate-search' // Page slug / اسلاگ صفحه
    );
    // Field: Download button text
    // فیلد: متن دکمه دانلود
    add_settings_field(
        'fb_cert_download_button_text',
        __('Download Button Text', 'fanabyte-certificate'), /*متن دکمه دانلود*/
        'fb_cert_download_button_text_callback',
        'fanabyte-certificate-search',
        'fb_cert_download_button_section'
    );
    // Field: Download button color
    // فیلد: رنگ دکمه دانلود
    add_settings_field(
        'fb_cert_download_button_color',
        __('Download Button Color', 'fanabyte-certificate'), /*رنگ دکمه دانلود*/
        'fb_cert_download_button_color_callback',
        'fanabyte-certificate-search',
        'fb_cert_download_button_section'
    );

    // Section: Labels and Titles Settings
    // بخش: تنظیمات برچسب‌ها و عناوین
    add_settings_section(
        'fb_cert_labels_section', // Section ID / شناسه بخش
        __('4. Labels and Titles Settings', 'fanabyte-certificate'), // Section title / عنوان بخش
        'fb_cert_labels_section_callback', // Section description callback / Callback توضیحات
        'fanabyte-certificate-search' // Page slug (show on the same page) / اسلاگ صفحه (نمایش در همان صفحه)
    );
    // Field: Heading for the details section on the certificate view page
    // فیلد: عنوان بخش جزئیات در صفحه نمایش مدرک
    add_settings_field(
        'fb_cert_label_details_heading',
        __('Details Section Heading', 'fanabyte-certificate'), /*عنوان بخش جزئیات*/
        'fb_cert_label_details_heading_callback', // Field callback / Callback فیلد
        'fanabyte-certificate-search',
        'fb_cert_labels_section' // Attach to the new section / تعلق به بخش جدید
    );
    // Field: Title for the main data metabox in the editor
    // فیلد: عنوان متاباکس اصلی اطلاعات در صفحه ویرایش
    add_settings_field(
        'fb_cert_metabox_title_main_data',
        __('Info Metabox Title', 'fanabyte-certificate'), /*عنوان متاباکس اطلاعات*/
        'fb_cert_metabox_title_main_data_callback',
        'fanabyte-certificate-search',
        'fb_cert_labels_section'
    );
    // Field: Label for the personal photo upload field in the editor
    // فیلد: برچسب فیلد آپلود عکس پرسنلی در صفحه ویرایش
    add_settings_field(
        'fb_cert_label_personal_photo',
        __('Personal Photo Field Label', 'fanabyte-certificate'), /*برچسب فیلد عکس پرسنلی*/
        'fb_cert_label_personal_photo_callback',
        'fanabyte-certificate-search',
        'fb_cert_labels_section'
    );
    // Field: Title for the main file metabox in the editor
    // فیلد: عنوان متاباکس آپلود فایل اصلی در صفحه ویرایش
    add_settings_field(
        'fb_cert_metabox_title_main_file',
        __('Main File Metabox Title', 'fanabyte-certificate'), /*عنوان متاباکس فایل اصلی*/
        'fb_cert_metabox_title_main_file_callback',
        'fanabyte-certificate-search',
        'fb_cert_labels_section'
    );

    // --- Import/Export Section ---
    // --- بخش واردات/صادرات ---
    add_settings_section( 'fb_cert_importexport_section', '', '__return_null', 'fanabyte-certificate-importexport');

}
// Hook the settings registration function to the 'admin_init' action.
// اتصال تابع ثبت تنظیمات به اکشن 'admin_init'.
add_action('admin_init', 'fb_cert_register_settings');


/**
 * ==================================
 * Settings Sections & Fields Callbacks
 * Callback های بخش‌ها و فیلدهای تنظیمات
 * ==================================
 */

// --- Fields Section Callbacks ---
// --- Callback های بخش فیلدها ---

/**
 * Callback for the Fields section description.
 * Callback برای توضیحات بخش فیلدها.
 */
function fb_cert_fields_section_callback() {
    // تعریف تگ‌های HTML مجاز فقط برای این مورد خاص
    $allowed_html_for_this_string = array(
        'span' => array(
            'class' => true, // اجازه دادن به ویژگی class برای تگ span
        ),
    );

    // استفاده از __() برای ترجمه و wp_kses() برای اجازه دادن به تگ span
    echo '<p>' . wp_kses(
        __( 'In this section, you can define the fields you want to enter for each certificate. You can specify the display order of the fields by dragging the move icon <span class="dashicons dashicons-move"></span>. You can select up to two text fields as "key fields" for searching.', 'fanabyte-certificate' ),
        $allowed_html_for_this_string
    ) . '</p>';
    /* در این بخش می‌توانید فیلدهایی که می‌خواهید برای هر مدرک وارد کنید را تعریف نمایید... */
}

/**
 * Callback to render the UI for managing the list of defined fields.
 * Callback برای رندر کردن UI برای مدیریت لیست فیلدهای تعریف شده.
 */
function fb_cert_fields_list_callback() {
    // Get the currently saved fields from options.
    // دریافت فیلدهای ذخیره شده فعلی از آپشن‌ها.
    $fields = get_option('fanabyte_certificate_fields', []);
    ?>
    <div id="fb-cert-fields-container" class="fb-cert-sortable">
        <?php if (!empty($fields) && is_array($fields)): // Add is_array check for safety / افزودن بررسی is_array برای اطمینان ?>
            <?php foreach ($fields as $key => $field): ?>
                <?php if (!is_array($field)) continue; // Skip if field data is not an array / رد شدن اگر داده فیلد آرایه نباشد ?>
                <div class="fb-cert-field-row" data-key="<?php echo esc_attr($key); ?>">
                    <span class="dashicons dashicons-move fb-cert-drag-handle" title="<?php esc_attr_e('Drag to reorder', 'fanabyte-certificate'); /*جابجا کنید*/ ?>"></span>
                    <input type="hidden" class="fb-cert-field-order" name="fanabyte_certificate_fields[<?php echo esc_attr($key); ?>][order]" value="<?php echo esc_attr($field['order'] ?? ''); // Order field used by JS for sorting / فیلد order که توسط JS برای مرتب‌سازی استفاده می‌شود ?>">
                    <div class="fb-cert-field-details">
                        <p>
                            <label><?php esc_html_e('Field Type:', 'fanabyte-certificate'); /*نوع فیلد:*/ ?> <strong><?php echo isset($field['type']) && $field['type'] === 'image' ? esc_html__('Image', 'fanabyte-certificate') : esc_html__('Text', 'fanabyte-certificate'); /*تصویر / متنی*/ ?></strong></label>
                            &nbsp; | &nbsp;
                            <label><?php esc_html_e('Key Name:', 'fanabyte-certificate'); /*نام کلیدی (Key):*/ ?> <code><?php echo esc_html($key); ?></code></label>
                            <input type="hidden" name="fanabyte_certificate_fields[<?php echo esc_attr($key); ?>][type]" value="<?php echo esc_attr($field['type'] ?? 'text'); // Store the type / ذخیره نوع ?>">
                        </p>
                        <p>
                            <label for="fb_field_label_<?php echo esc_attr($key); ?>"><?php esc_html_e('Field Label:', 'fanabyte-certificate'); /*عنوان فیلد:*/ ?></label>
                            <input type="text" id="fb_field_label_<?php echo esc_attr($key); ?>" name="fanabyte_certificate_fields[<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($field['label'] ?? ''); ?>" required>
                        </p>

                        <?php // --- Start Change: Display checkbox only for text type --- ?>
                        <?php // --- شروع تغییر: نمایش چک‌باکس فقط برای نوع متنی --- ?>
                        <?php if (isset($field['type']) && $field['type'] === 'text') : ?>
                            <p>
                                <label>
                                    <input type="checkbox" name="fanabyte_certificate_fields[<?php echo esc_attr($key); ?>][is_key]" value="1" <?php checked(isset($field['is_key']) && $field['is_key']); ?>>
                                    <?php esc_html_e('Key field for search?', 'fanabyte-certificate'); /*فیلد کلیدی برای جستجو؟*/ ?>
                                </label>
                            </p>
                        <?php else: // For image type, send a hidden is_key field with value 0 / برای نوع تصویر، یک فیلد مخفی is_key با مقدار 0 میفرستیم ?>
                            <input type="hidden" name="fanabyte_certificate_fields[<?php echo esc_attr($key); ?>][is_key]" value="0">
                        <?php endif; ?>
                        <?php // --- End Change --- ?>
                        <?php // --- پایان تغییر --- ?>

                    </div>
                    <div class="fb-cert-field-actions">
                        <button type="button" class="button button-link-delete fb-remove-field" title="<?php esc_attr_e('Delete this field', 'fanabyte-certificate'); /*حذف این فیلد*/ ?>">
                             <span class="dashicons dashicons-trash"></span>
                             <span class="screen-reader-text"><?php esc_html_e('Delete', 'fanabyte-certificate'); /*حذف*/ ?></span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: // Message if no fields are defined yet / پیامی اگر هنوز هیچ فیلدی تعریف نشده باشد ?>
            <p><?php esc_html_e('No fields have been defined yet. Add a new field using the section below.', 'fanabyte-certificate'); /*هنوز هیچ فیلدی تعریف نشده است. از بخش زیر یک فیلد جدید اضافه کنید.*/ ?></p>
        <?php endif; ?>
    </div><hr>
    <div class="fb-add-new-field-section">
        <h3><?php esc_html_e('Add New Field', 'fanabyte-certificate'); /*افزودن فیلد جدید*/ ?></h3>
        <label for="new_field_type"><?php esc_html_e('Field Type:', 'fanabyte-certificate'); /*نوع فیلد:*/ ?></label>
        <select id="new_field_type">
            <option value="text"><?php esc_html_e('Text', 'fanabyte-certificate'); /*متنی*/ ?></option>
            <option value="image"><?php esc_html_e('Image', 'fanabyte-certificate'); /*تصویر*/ ?></option>
        </select>
        <button type="button" id="fb-add-field-button" class="button"><?php esc_html_e('Add Field', 'fanabyte-certificate'); /*افزودن فیلد*/ ?></button>
        <p class="description"><?php esc_html_e('After adding, you can change the field order by dragging and dropping.', 'fanabyte-certificate'); /*پس از افزودن، می‌توانید ترتیب فیلدها را با کشیدن و رها کردن تغییر دهید.*/ ?></p>
    </div>
    <?php
}

/**
 * Sanitize callback for the fields settings.
 * تابع Callback پاک‌سازی برای تنظیمات فیلدها.
 */
function fb_cert_sanitize_fields_settings($input) {
    $new_input_unsorted = []; // Array to hold unsorted but processed fields / آرایه‌ای برای نگهداری فیلدهای پردازش شده اما مرتب نشده

    // Ensure $input is a valid array before processing.
    // اطمینان از اینکه $input یک آرایه معتبر است قبل از پردازش.
    if (empty($input) || !is_array($input)) {
        return $new_input_unsorted; // Return empty if input is invalid / بازگرداندن آرایه خالی اگر ورودی نامعتبر است
    }

    // Loop through each submitted field.
    // حلقه زدن روی هر فیلد ارسال شده.
    foreach ($input as $key => $field_data) {
        $clean_key = sanitize_key($key); // Sanitize the field key / پاک‌سازی کلید فیلد

        // Ensure $field_data is an array and the key is not empty.
        // اطمینان از اینکه $field_data یک آرایه است و کلید خالی نیست.
        if (empty($clean_key) || !is_array($field_data)) {
            continue; // Skip invalid entries / رد شدن از ورودی‌های نامعتبر
        }

        $new_field = []; // Array for the sanitized field data / آرایه برای داده‌های فیلد پاک‌سازی شده

        // Sanitize field type (default to 'text').
        // پاک‌سازی نوع فیلد (پیش‌فرض 'text').
        $new_field['type'] = isset($field_data['type']) && in_array($field_data['type'], ['text', 'image']) ? $field_data['type'] : 'text';
        // Sanitize field label.
        // پاک‌سازی برچسب فیلد.
        $new_field['label'] = isset($field_data['label']) ? sanitize_text_field($field_data['label']) : '';

        // Skip fields without labels.
        // رد شدن از فیلدهای بدون برچسب.
        if (empty($new_field['label'])) {
            continue;
        }

        // --- Change: 'is_key' is only checked and saved for 'text' type fields. ---
        // --- تغییر: 'is_key' فقط برای فیلدهای نوع 'text' بررسی و ذخیره می‌شود. ---
        $new_field['is_key'] = (isset($field_data['is_key']) && $field_data['is_key'] == '1' && $new_field['type'] === 'text');

        // Sanitize field order (used for sorting).
        // پاک‌سازی ترتیب فیلد (برای مرتب‌سازی استفاده می‌شود).
        $new_field['order'] = isset($field_data['order']) ? absint($field_data['order']) : 999;

        // Add the sanitized field to the unsorted array.
        // افزودن فیلد پاک‌سازی شده به آرایه مرتب نشده.
        $new_input_unsorted[$clean_key] = $new_field;
    }

    // Sort the fields based on the 'order' key.
    // مرتب‌سازی فیلدها بر اساس کلید 'order'.
    uasort($new_input_unsorted, function($a, $b) {
        // Use null coalescing operator for safety if 'order' key is missing.
        // استفاده از عملگر null coalescing برای اطمینان در صورت عدم وجود کلید 'order'.
        return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
    });

    // Final processing: Enforce key field limit (max 2) and remove the 'order' key.
    // پردازش نهایی: اعمال محدودیت فیلد کلیدی (حداکثر ۲) و حذف کلید 'order'.
    $final_input = []; // Array for the final output / آرایه برای خروجی نهایی
    $current_key_count = 0; // Counter for key fields / شمارنده برای فیلدهای کلیدی

    foreach($new_input_unsorted as $key => $field){
         // Ensure 'is_key' exists before checking.
         // اطمینان از وجود 'is_key' قبل از بررسی.
         if(isset($field['is_key']) && $field['is_key']){
             // If the key field limit is reached, set 'is_key' to false.
             // اگر به محدودیت فیلد کلیدی رسیده‌ایم، 'is_key' را false قرار بده.
             if($current_key_count >= 2) {
                 $field['is_key'] = false;
             } else {
                 $current_key_count++; // Increment the key field counter / افزایش شمارنده فیلد کلیدی
             }
         } else {
             // Ensure 'is_key' is explicitly false if not true (important!).
             // اطمینان از اینکه 'is_key' به صراحت false است اگر true نباشد (مهم!).
             $field['is_key'] = false;
         }
         unset($field['order']); // Remove the temporary order key before saving / حذف کلید order موقت قبل از ذخیره
         $final_input[$key] = $field; // Add the finalized field to the output array / افزودن فیلد نهایی شده به آرایه خروجی
    }

    // Add a settings error message if more than 2 text fields were originally checked as keys.
    // افزودن پیام خطای تنظیمات اگر در ابتدا بیش از ۲ فیلد متنی به عنوان کلیدی علامت زده شده بودند.
    $original_key_count = 0;
    if (is_array($input)) { // Check if $input is an array before iterating
        foreach ($input as $field_data) {
            // Check if it's an array, 'is_key' is set and '1', and type is 'text'.
            // بررسی اینکه آیا آرایه است، 'is_key' تنظیم شده و '1' است، و نوع آن 'text' است.
            if (is_array($field_data) && isset($field_data['is_key']) && $field_data['is_key'] == '1' && isset($field_data['type']) && $field_data['type'] === 'text') {
                $original_key_count++;
            }
        }
    }
    if($original_key_count > 2) {
        add_settings_error(
            'fanabyte_certificate_fields', // Settings slug / اسلاگ تنظیمات
            'key_field_limit', // Error code / کد خطا
            __('You cannot select more than 2 key fields. Only the first 2 in the order were saved as keys.', 'fanabyte-certificate'), // Error message / پیام خطا /*شما نمی‌توانید بیش از ۲ فیلد کلیدی انتخاب کنید. فقط ۲ تای اول در ترتیب به عنوان کلیدی ذخیره شدند.*/
            'warning' // Message type / نوع پیام
        );
    }

    // Return the final, sanitized, sorted, and processed array of fields.
    // بازگرداندن آرایه نهایی، پاک‌سازی شده، مرتب شده و پردازش شده فیلدها.
    return $final_input;
}


// --- Search Form / URL / Buttons Section Callbacks ---
// --- Callback های بخش فرم جستجو / URL / دکمه‌ها ---

/**
 * Callback for the Search Form Customization section description.
 * Callback برای توضیحات بخش شخصی‌سازی فرم استعلام.
 */
function fb_cert_search_form_section_callback() {
    echo '<p>' . esc_html__('Settings for the text and button of the inquiry form.', 'fanabyte-certificate') . '</p>';
    /* تنظیمات متن و دکمه فرم استعلام. */
}

/**
 * Callback to render the intro text field using the full WordPress editor.
 * Callback برای رندر کردن فیلد متن مقدمه با استفاده از ویرایشگر کامل وردپرس.
 */
function fb_cert_search_intro_text_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    // Get the saved content, default to empty string.
    // دریافت محتوای ذخیره شده، پیش‌فرض رشته خالی.
    $content = $settings['intro_text'] ?? '';
    // Use wp_editor to display the rich text editor.
    // استفاده از wp_editor برای نمایش ویرایشگر متن غنی.
    wp_editor($content, 'fb_cert_intro_text_editor', [ // Unique ID for the editor / شناسه منحصر به فرد برای ویرایشگر
        'textarea_name' => 'fanabyte_certificate_search_settings[intro_text]', // Name attribute for the textarea / ویژگی name برای textarea
        'media_buttons' => true, // Enable the "Add Media" button / فعال کردن دکمه افزودن رسانه
        'textarea_rows' => 10,   // Set the number of rows / تنظیم تعداد خطوط
        // 'teeny' and 'quicktags' are omitted to load the full editor / حذف teeny و quicktags برای بارگذاری ویرایشگر کامل
    ]);
    echo '<p class="description">' . esc_html__('This text will be displayed before the search form. You can use all the features of the WordPress editor.', 'fanabyte-certificate') . '</p>';
    /* این متن قبل از فرم جستجو نمایش داده می‌شود. می‌توانید از تمام امکانات ویرایشگر وردپرس استفاده کنید. */
}

/**
 * Callback to render the search button text field.
 * Callback برای رندر کردن فیلد متنی دکمه جستجو.
 */
function fb_cert_search_button_text_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    // Get saved value or default text.
    // دریافت مقدار ذخیره شده یا متن پیش‌فرض.
    $default_text = __('Search', 'fanabyte-certificate'); /*جستجو*/
    $value = $settings['button_text'] ?? $default_text;
    echo '<input type="text" name="fanabyte_certificate_search_settings[button_text]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default_text) . '" />';
}

/**
 * Callback to render the search button color picker field.
 * Callback برای رندر کردن فیلد انتخابگر رنگ دکمه جستجو.
 */
function fb_cert_search_button_color_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    // Get saved value or default color.
    // دریافت مقدار ذخیره شده یا رنگ پیش‌فرض.
    $default_color = '#2271b1'; // WordPress blue / آبی وردپرس
    $value = $settings['button_color'] ?? $default_color;
    // Input field with the color picker class.
    // فیلد ورودی با کلاس انتخابگر رنگ.
    echo '<input type="text" name="fanabyte_certificate_search_settings[button_color]" value="' . esc_attr($value) . '" class="fb-cert-color-picker" data-default-color="' . esc_attr($default_color) . '" />';
    echo '<p class="description">' . esc_html__('Background color for the search button.', 'fanabyte-certificate') . '</p>';
    /* رنگ پس‌زمینه دکمه جستجو. */
}

/**
 * Callback to render the placeholder text fields for key search fields.
 * Callback برای رندر کردن فیلدهای متنی نگهدارنده برای فیلدهای کلیدی جستجو.
 */
function fb_cert_search_placeholders_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $defined_fields = get_option('fanabyte_certificate_fields', []);

    // Filter defined fields to get only the key fields (text type).
    // فیلتر کردن فیلدهای تعریف شده برای دریافت فقط فیلدهای کلیدی (نوع متنی).
    $key_fields = [];
    if (!empty($defined_fields) && is_array($defined_fields)) {
        $key_fields = array_filter($defined_fields, function($field){
            return isset($field['is_key']) && $field['is_key'] && isset($field['type']) && $field['type'] === 'text';
        });
    }

    // If no key fields are defined, show a message.
    // اگر هیچ فیلد کلیدی تعریف نشده است، پیامی نشان بده.
    if(empty($key_fields)){
        echo '<p>' . esc_html__('First, select at least one field as a "key field" in the "Field Management" tab.', 'fanabyte-certificate') . '</p>';
        /* ابتدا حداقل یک فیلد را به عنوان "فیلد کلیدی" در تب "مدیریت فیلدها" انتخاب کنید. */
        return;
    }

    echo '<p>' . esc_html__('Placeholder text (hint) displayed inside each search field:', 'fanabyte-certificate') . '</p>';
    /* متن نگهدارنده (راهنما) که داخل هر فیلد جستجو نمایش داده می‌شود: */

    // Loop through key fields and display an input for each placeholder.
    // حلقه زدن روی فیلدهای کلیدی و نمایش یک ورودی برای هر نگهدارنده.
    foreach($key_fields as $key => $field){
        // Get saved placeholder or generate a default one.
        // دریافت نگهدارنده ذخیره شده یا تولید یک پیش‌فرض.
        $placeholder_value = $settings['placeholders'][$key] ?? '';
        // Translators: %s: Field label. Example: "Please enter Student Name"
        // مترجمان: %s: برچسب فیلد. مثال: "لطفا نام دانشجو را وارد کنید"
        $default_placeholder = sprintf(__('Please enter %s', 'fanabyte-certificate'), $field['label']);
        echo '<p>';
        echo '<label for="placeholder_' . esc_attr($key) . '">' . esc_html($field['label']) . ':</label><br/>';
        echo '<input type="text" id="placeholder_' . esc_attr($key) . '" name="fanabyte_certificate_search_settings[placeholders][' . esc_attr($key) . ']" value="' . esc_attr($placeholder_value) . '" class="regular-text" placeholder="'. esc_attr($default_placeholder) .'" />';
        echo '</p>';
    }
}


// --- URL Settings Section Callbacks ---
// --- Callback های بخش تنظیمات URL ---

/**
 * Callback for the URL Settings section description.
 * Callback برای توضیحات بخش تنظیمات URL.
 */
function fb_cert_url_section_callback() {
    echo '<p>' . esc_html__('In this section, you can set the fixed part of the certificate URLs (slug).', 'fanabyte-certificate') . '</p>';
    /* در این بخش می‌توانید بخش ثابت URL ‌مدرک‌ها (اسلاگ) را تنظیم کنید. */
    echo '<p><strong>' . esc_html__('Important:', 'fanabyte-certificate') . '</strong> ' .
         sprintf(
            // Translators: %s is the URL to the Permalinks settings page.
            // مترجمان: %s آدرس URL صفحه تنظیمات پیوندهای یکتا است.
            esc_html__('After changing and saving this value, you must visit the Settings > %s page in the WordPress admin and click the "Save Changes" button (even without changing anything there). This is necessary to apply the new URL structure.', 'fanabyte-certificate'),
            '<a href="' . esc_url(admin_url('options-permalink.php')) . '">' . esc_html__('Permalinks', 'fanabyte-certificate') . '</a>'
         ) . '</p>';
    /* مهم: پس از تغییر و ذخیره این مقدار، حتما به صفحه تنظیمات > <a href="...">پیوندهای یکتا</a> ... مراجعه کنید. */
}

/**
 * Callback to render the CPT slug input field.
 * Callback برای رندر کردن فیلد ورودی اسلاگ CPT.
 */
function fb_cert_cpt_slug_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    // Get saved slug or default value.
    // دریافت اسلاگ ذخیره شده یا مقدار پیش‌فرض.
    $default_slug = 'certificate';
    $value = $settings['cpt_slug'] ?? $default_slug;
    ?>
    <input type="text" name="fanabyte_certificate_search_settings[cpt_slug]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="<?php echo esc_attr($default_slug); ?>" />
    <p class="description">
        <?php esc_html_e('Use only lowercase English letters, numbers, and hyphens (-).', 'fanabyte-certificate'); /* فقط از حروف کوچک انگلیسی، اعداد و خط تیره (-) استفاده کنید. */ ?>
        <br>
        <?php
        // Translators: %s: Example URL structure.
        // مترجمان: %s: ساختار URL مثال.
        printf(
            esc_html__('Current certificate URL structure: %s', 'fanabyte-certificate'),
            '<code>' . esc_url(home_url('/' . $value . '/your-certificate-slug/')) . '</code>'
        );
        /* ساختار فعلی URL ‌مدرک‌ها: ... */
        ?>
    </p>
    <?php
}


// --- Download Button Section Callbacks ---
// --- Callback های بخش تنظیمات دکمه دانلود ---

/**
 * Callback for the Download Button Customization section description.
 * Callback برای توضیحات بخش شخصی‌سازی دکمه دانلود.
 */
function fb_cert_download_button_section_callback() {
    echo '<p>' . esc_html__('Settings for the text and color of the "Download Certificate File" button displayed on the details page.', 'fanabyte-certificate') . '</p>';
    /* تنظیمات متن و رنگ دکمه "دانلود فایل مدرک" که در صفحه جزئیات نمایش داده می‌شود. */
}

/**
 * Callback to render the download button text field.
 * Callback برای رندر کردن فیلد متنی دکمه دانلود.
 */
function fb_cert_download_button_text_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    // Get saved value or default text.
    // دریافت مقدار ذخیره شده یا متن پیش‌فرض.
    $default_text = __('Download Certificate File', 'fanabyte-certificate'); /*دانلود فایل مدرک*/
    $value = $settings['download_button_text'] ?? $default_text;
    echo '<input type="text" name="fanabyte_certificate_search_settings[download_button_text]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default_text) . '" />';
}

/**
 * Callback to render the download button color picker field.
 * Callback برای رندر کردن فیلد انتخابگر رنگ دکمه دانلود.
 */
function fb_cert_download_button_color_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    // Get saved value or default color.
    // دریافت مقدار ذخیره شده یا رنگ پیش‌فرض.
    $default_color = '#2ecc71'; // Green color / رنگ سبز
    $value = $settings['download_button_color'] ?? $default_color;
    echo '<input type="text" name="fanabyte_certificate_search_settings[download_button_color]" value="' . esc_attr($value) . '" class="fb-cert-color-picker" data-default-color="' . esc_attr($default_color) . '" />';
    echo '<p class="description">' . esc_html__('Background color for the download button.', 'fanabyte-certificate') . '</p>';
    /* رنگ پس‌زمینه دکمه دانلود. */
}


// --- Labels and Titles Section Callbacks ---
// --- Callback های بخش برچسب‌ها و عناوین ---

/**
 * Callback for the Labels and Titles Settings section description.
 * Callback برای توضیحات بخش تنظیمات برچسب‌ها و عناوین.
 */
function fb_cert_labels_section_callback() {
    echo '<p>' . esc_html__('Change the default text for some parts of the plugin here.', 'fanabyte-certificate') . '</p>';
    /* متن‌های پیش‌فرض برخی از بخش‌های افزونه را در اینجا تغییر دهید. */
}

/**
 * Callback to render the field for the "Details" heading on the front-end.
 * Callback برای رندر کردن فیلد عنوان "جزئیات" در فرانت‌اند.
 */
function fb_cert_label_details_heading_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default = __('Certificate Details:', 'fanabyte-certificate'); /*جزئیات مدرک:*/
    $value = $settings['label_details_heading'] ?? $default;
    echo '<input type="text" name="fanabyte_certificate_search_settings[label_details_heading]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default) . '" />';
    echo '<p class="description">' . esc_html__('The heading displayed above the custom fields list on the certificate view page.', 'fanabyte-certificate') . '</p>';
    /* عنوانی که بالای لیست فیلدهای سفارشی در صفحه نمایش مدرک نشان داده می‌شود. */
}

/**
 * Callback to render the field for the main data metabox title.
 * Callback برای رندر کردن فیلد عنوان متاباکس اصلی اطلاعات.
 */
function fb_cert_metabox_title_main_data_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default = __('Certificate Information & Details', 'fanabyte-certificate'); /*اطلاعات و جزئیات مدرک*/
    $value = $settings['metabox_title_main_data'] ?? $default;
    echo '<input type="text" name="fanabyte_certificate_search_settings[metabox_title_main_data]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default) . '" />';
    echo '<p class="description">' . esc_html__('Title for the main metabox on the certificate edit/add screen.', 'fanabyte-certificate') . '</p>';
    /* عنوان متاباکس اصلی در صفحه ویرایش/افزودن مدرک. */
}

/**
 * Callback to render the field for the personal photo field label.
 * Callback برای رندر کردن فیلد برچسب فیلد عکس پرسنلی.
 */
function fb_cert_label_personal_photo_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default = __('Personal Photo (Optional)', 'fanabyte-certificate'); /*تصویر پرسنلی (اختیاری)*/
    $value = $settings['label_personal_photo'] ?? $default;
    echo '<input type="text" name="fanabyte_certificate_search_settings[label_personal_photo]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default) . '" />';
     echo '<p class="description">' . esc_html__('Label for the personal photo upload field on the certificate edit/add screen.', 'fanabyte-certificate') . '</p>';
     /* برچسب فیلد آپلود عکس پرسنلی در صفحه ویرایش/افزودن مدرک. */
}

/**
 * Callback to render the field for the main file metabox title.
 * Callback برای رندر کردن فیلد عنوان متاباکس فایل اصلی.
 */
function fb_cert_metabox_title_main_file_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default = __('Main Certificate File (PDF/Image)', 'fanabyte-certificate'); /*فایل اصلی مدرک (PDF/Image)*/
    $value = $settings['metabox_title_main_file'] ?? $default;
    echo '<input type="text" name="fanabyte_certificate_search_settings[metabox_title_main_file]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default) . '" />';
    echo '<p class="description">' . esc_html__('Title for the main file upload metabox on the certificate edit/add screen.', 'fanabyte-certificate') . '</p>';
    /* عنوان متاباکس آپلود فایل اصلی در صفحه ویرایش/افزودن مدرک. */
}


/**
 * Sanitize callback for the search/URL/buttons settings group.
 * تابع Callback پاک‌سازی برای گروه تنظیمات جستجو/URL/دکمه‌ها.
 */
function fb_cert_sanitize_search_settings($input) {
     $new_input = []; // Array for sanitized output / آرایه برای خروجی پاک‌سازی شده
     // Define default values for all settings in this group.
     // تعریف مقادیر پیش‌فرض برای تمام تنظیمات این گروه.
     $default_settings = [
        'intro_text'             => '',
        'button_text'            => __('Search', 'fanabyte-certificate'),
        'button_color'           => '#2271b1',
        'placeholders'           => [],
        'cpt_slug'               => 'certificate',
        'download_button_text'   => __('Download Certificate File', 'fanabyte-certificate'),
        'download_button_color'  => '#2ecc71',
        // New defaults for labels / پیش‌فرض‌های جدید برای برچسب‌ها
        'label_details_heading'  => __('Certificate Details:', 'fanabyte-certificate'),
        'metabox_title_main_data'=> __('Certificate Information & Details', 'fanabyte-certificate'),
        'label_personal_photo'   => __('Personal Photo (Optional)', 'fanabyte-certificate'),
        'metabox_title_main_file'=> __('Main Certificate File (PDF/Image)', 'fanabyte-certificate')
     ];

     // --- Sanitize existing fields ---
     // --- پاک‌سازی فیلدهای قبلی ---
     $new_input['intro_text'] = isset($input['intro_text']) ? wp_kses_post($input['intro_text']) : $default_settings['intro_text']; // Allow safe HTML / اجازه دادن HTML امن
     $new_input['button_text'] = isset($input['button_text']) ? sanitize_text_field($input['button_text']) : $default_settings['button_text'];
     // Sanitize color fields (must be a valid hex code).
     // پاک‌سازی فیلدهای رنگ (باید کد هگز معتبر باشد).
     if (isset($input['button_color']) && preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $input['button_color'])) {
         $new_input['button_color'] = $input['button_color'];
     } else {
         $new_input['button_color'] = $default_settings['button_color'];
     }
     // Sanitize placeholders array.
     // پاک‌سازی آرایه نگهدارنده‌ها.
     if (isset($input['placeholders']) && is_array($input['placeholders'])) {
         $new_input['placeholders'] = [];
         foreach ($input['placeholders'] as $key => $placeholder) {
             $clean_key = sanitize_key($key);
             if ($clean_key) {
                 $new_input['placeholders'][$clean_key] = sanitize_text_field($placeholder);
             }
         }
     } else {
         $new_input['placeholders'] = $default_settings['placeholders'];
     }
     // Sanitize CPT slug (must be valid).
     // پاک‌سازی اسلاگ CPT (باید معتبر باشد).
     if (isset($input['cpt_slug']) && !empty(trim($input['cpt_slug']))) {
         $sanitized_slug = sanitize_title_with_dashes(trim($input['cpt_slug']));
         // Use sanitized slug if not empty, otherwise default.
         // استفاده از اسلاگ پاک‌سازی شده اگر خالی نباشد، در غیر این صورت پیش‌فرض.
         $new_input['cpt_slug'] = !empty($sanitized_slug) ? $sanitized_slug : $default_settings['cpt_slug'];
     } else {
         $new_input['cpt_slug'] = $default_settings['cpt_slug'];
     }
     // Sanitize download button text (ensure not empty, fallback to default).
     // پاک‌سازی متن دکمه دانلود (اطمینان از خالی نبودن، بازگشت به پیش‌فرض).
     $new_input['download_button_text'] = isset($input['download_button_text']) && !empty(trim($input['download_button_text']))
                                          ? sanitize_text_field($input['download_button_text'])
                                          : $default_settings['download_button_text'];
     // Sanitize download button color.
     // پاک‌سازی رنگ دکمه دانلود.
     if (isset($input['download_button_color']) && preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $input['download_button_color'])) {
         $new_input['download_button_color'] = $input['download_button_color'];
     } else {
         $new_input['download_button_color'] = $default_settings['download_button_color'];
     }

     // --- Sanitize new label fields ---
     // --- پاک‌سازی فیلدهای برچسب جدید ---
     $new_input['label_details_heading'] = isset($input['label_details_heading']) && !empty(trim($input['label_details_heading']))
                                           ? sanitize_text_field($input['label_details_heading'])
                                           : $default_settings['label_details_heading'];
     $new_input['metabox_title_main_data'] = isset($input['metabox_title_main_data']) && !empty(trim($input['metabox_title_main_data']))
                                           ? sanitize_text_field($input['metabox_title_main_data'])
                                           : $default_settings['metabox_title_main_data'];
     $new_input['label_personal_photo'] = isset($input['label_personal_photo']) && !empty(trim($input['label_personal_photo']))
                                           ? sanitize_text_field($input['label_personal_photo'])
                                           : $default_settings['label_personal_photo'];
     $new_input['metabox_title_main_file'] = isset($input['metabox_title_main_file']) && !empty(trim($input['metabox_title_main_file']))
                                           ? sanitize_text_field($input['metabox_title_main_file'])
                                           : $default_settings['metabox_title_main_file'];


     // Return the fully sanitized array.
     // بازگرداندن آرایه کاملاً پاک‌سازی شده.
     return $new_input;
}


/**
 * ==================================
 * Import/Export Handling Functions
 * توابع مدیریت واردات/صادرات
 * ==================================
 */

/**
 * Handle the settings export request.
 * مدیریت درخواست خروجی گرفتن از تنظیمات.
 */
function fb_cert_handle_export_settings() {
    // Security check: Verify nonce.
    // بررسی امنیتی: تأیید نانس.
    if (!isset($_POST['fb_cert_export_nonce_field']) || !wp_verify_nonce($_POST['fb_cert_export_nonce_field'], 'fb_cert_export_nonce')) {
        wp_die(
            esc_html__('Security check failed!', 'fanabyte-certificate'), /*خطای امنیتی!*/
            esc_html__('Security Error', 'fanabyte-certificate'), /*خطای امنیتی*/
            ['response' => 403]
        );
    }
    // Permission check: Ensure user can manage options.
    // بررسی دسترسی: اطمینان از اینکه کاربر می‌تواند گزینه‌ها را مدیریت کند.
    if (!current_user_can('manage_options')) {
        wp_die(
            esc_html__('You do not have permission to perform this action.', 'fanabyte-certificate'), /*شما اجازه انجام این کار را ندارید.*/
            esc_html__('Permission Denied', 'fanabyte-certificate'), /*عدم دسترسی*/
            ['response' => 403]
        );
    }

    // Gather settings to export.
    // جمع‌آوری تنظیمات برای خروجی گرفتن.
    $settings_to_export = [
        'version' => FB_CERT_VERSION, // Include plugin version / شامل کردن نسخه افزونه
        'fields'  => get_option('fanabyte_certificate_fields', []), // Get field settings / دریافت تنظیمات فیلد
        'search'  => get_option('fanabyte_certificate_search_settings', []) // Get search/URL/button settings / دریافت تنظیمات جستجو/URL/دکمه
    ];

    // Prepare JSON data and filename.
    // آماده‌سازی داده JSON و نام فایل.
    $filename = 'fanabyte-certificate-settings-' . date('Y-m-d') . '.json';
    // Encode to JSON with pretty print and unicode support.
    // انکود به JSON با چاپ زیبا و پشتیبانی از یونیکد.
    $json_data = json_encode($settings_to_export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Set headers for file download.
    // تنظیم هدرها برای دانلود فایل.
    nocache_headers(); // Prevent caching / جلوگیری از کش شدن
    header('Content-Description: File Transfer');
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($json_data));

    // Output the JSON data and exit.
    // خروجی داده JSON و خروج.
    echo $json_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    exit;
}
// Hook the export handler to the 'admin_post_fb_cert_export_settings' action.
// اتصال هندلر خروجی به اکشن 'admin_post_fb_cert_export_settings'.
add_action('admin_post_fb_cert_export_settings', 'fb_cert_handle_export_settings');


/**
 * Handle the settings import request.
 * مدیریت درخواست وارد کردن تنظیمات.
 */
function fb_cert_handle_import_settings() {
    // Security check: Verify nonce.
    // بررسی امنیتی: تأیید نانس.
    if (!isset($_POST['fb_cert_import_nonce_field']) || !wp_verify_nonce($_POST['fb_cert_import_nonce_field'], 'fb_cert_import_nonce')) {
        wp_die(__('Security check failed!', 'fanabyte-certificate'), __('Security Error', 'fanabyte-certificate'), ['response' => 403]);
    }
    // Permission check.
    // بررسی دسترسی.
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to perform this action.', 'fanabyte-certificate'), __('Permission Denied', 'fanabyte-certificate'), ['response' => 403]);
    }

    // Define the redirect URL (back to the import/export tab).
    // تعریف URL بازگشت (به تب واردات/صادرات).
    $redirect_url = add_query_arg(
        ['page' => 'fanabyte-certificate-settings', 'tab' => 'import_export'],
        admin_url('admin.php') // Changed from admin.php?page=... to avoid potential issues / تغییر از admin.php?page=... برای جلوگیری از مشکلات احتمالی
    );

    // --- File Upload Validation ---
    // --- اعتبارسنجی آپلود فایل ---

    // Check if a file was uploaded and if it's a valid upload.
    // بررسی اینکه آیا فایلی آپلود شده و آپلود معتبر است.
    if (!isset($_FILES['fb_cert_import_file']) || !is_uploaded_file($_FILES['fb_cert_import_file']['tmp_name'])) {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_upload', 'code' => 'no_file'], $redirect_url));
        exit;
    }
    // Check for upload errors.
    // بررسی خطاهای آپلود.
    if ($_FILES['fb_cert_import_file']['error'] !== UPLOAD_ERR_OK) {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_upload', 'code' => $_FILES['fb_cert_import_file']['error']], $redirect_url));
        exit;
    }
    // Check file extension (must be .json).
    // بررسی پسوند فایل (باید .json باشد).
    $file_name = $_FILES['fb_cert_import_file']['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if (empty($file_ext) || strtolower($file_ext) !== 'json') {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_type'], $redirect_url));
        exit;
    }

    // --- File Content Validation ---
    // --- اعتبارسنجی محتوای فایل ---

    // Read file content.
    // خواندن محتوای فایل.
    $file_path = $_FILES['fb_cert_import_file']['tmp_name'];
    // Use WP_Filesystem for better reliability if possible, fallback to file_get_contents.
    // در صورت امکان از WP_Filesystem برای اطمینان بیشتر استفاده کنید، در غیر این صورت از file_get_contents استفاده کنید.
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once (ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }
    $file_content = $wp_filesystem->get_contents($file_path);

    //$file_content = file_get_contents($file_path); // Simpler alternative / جایگزین ساده‌تر
    if ($file_content === false) {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_read'], $redirect_url));
        exit;
    }
    // Decode JSON content.
    // دیکود کردن محتوای JSON.
    $imported_settings = json_decode($file_content, true); // true for associative array / true برای آرایه انجمنی
    // Check for JSON decoding errors.
    // بررسی خطاهای دیکود JSON.
    if ($imported_settings === null && json_last_error() !== JSON_ERROR_NONE) {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_json', 'code' => json_last_error()], $redirect_url));
        exit;
    }
    // Check if the decoded data is an array.
    // بررسی اینکه آیا داده دیکود شده یک آرایه است.
    if (!is_array($imported_settings)) {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_format'], $redirect_url));
        exit;
    }

    // --- Sanitize and Update Settings ---
    // --- پاک‌سازی و به‌روزرسانی تنظیمات ---

    // Extract field and search settings from the imported data.
    // استخراج تنظیمات فیلد و جستجو از داده‌های وارد شده.
    $fields_to_import = isset($imported_settings['fields']) && is_array($imported_settings['fields']) ? $imported_settings['fields'] : [];
    $search_to_import = isset($imported_settings['search']) && is_array($imported_settings['search']) ? $imported_settings['search'] : [];

    // Sanitize the imported settings using the existing sanitize callbacks.
    // پاک‌سازی تنظیمات وارد شده با استفاده از callback های پاک‌سازی موجود.
    $sanitized_fields = fb_cert_sanitize_fields_settings($fields_to_import);
    $sanitized_search = fb_cert_sanitize_search_settings($search_to_import);

    // Update the options in the database.
    // به‌روزرسانی آپشن‌ها در پایگاه داده.
    update_option('fanabyte_certificate_fields', $sanitized_fields);
    update_option('fanabyte_certificate_search_settings', $sanitized_search);

    // Schedule a rewrite rule flush in case the CPT slug was changed.
    // زمان‌بندی فلاش قوانین بازنویسی در صورتی که اسلاگ CPT تغییر کرده باشد.
    fb_cert_schedule_rewrite_flush();

    // Redirect back with a success message.
    // بازگشت با پیام موفقیت.
    wp_safe_redirect(add_query_arg(['message' => 'import_success'], $redirect_url));
    exit;
}
// Hook the import handler to the 'admin_post_fb_cert_import_settings' action.
// اتصال هندلر واردات به اکشن 'admin_post_fb_cert_import_settings'.
add_action('admin_post_fb_cert_import_settings', 'fb_cert_handle_import_settings');


/**
 * Display admin notices for import/export results.
 * نمایش اعلان‌های ادمین برای نتایج واردات/صادرات.
 */
function fb_cert_show_admin_notices() {
    // Get the current screen information.
    // دریافت اطلاعات صفحه فعلی.
    $screen = get_current_screen();
    // Only proceed if we are on the plugin's settings page and a 'message' parameter exists.
    // فقط در صورتی ادامه بده که در صفحه تنظیمات افزونه هستیم و پارامتر 'message' وجود دارد.
    if (!$screen || strpos($screen->id, 'fanabyte-certificate-settings') === false || !isset($_GET['message'])) {
        return;
    }

    // Sanitize the message code and error code from query parameters.
    // پاک‌سازی کد پیام و کد خطا از پارامترهای کوئری.
    $message_code = sanitize_key($_GET['message']);
    $error_code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';

    $message = ''; // Initialize message string / مقداردهی اولیه رشته پیام
    $type = 'info'; // Default notice type / نوع اعلان پیش‌فرض

    // Determine the message and type based on the message code.
    // تعیین پیام و نوع بر اساس کد پیام.
    switch ($message_code) {
        case 'import_success':
            $message = esc_html__('Settings imported and saved successfully.', 'fanabyte-certificate') . ' ' .
                       esc_html__('If the URL slug was changed, please visit the Settings > Permalinks page and click Save Changes.', 'fanabyte-certificate');
            /* تنظیمات با موفقیت وارد و ذخیره شدند. اگر اسلاگ URL تغییر کرده است، لطفا به صفحه تنظیمات > پیوندهای یکتا مراجعه و دکمه ذخیره را بزنید. */
            $type = 'success';
            break;

        // --- Upload Error Messages ---
        // --- پیام‌های خطای آپلود ---
        case 'import_error_upload':
            // Map upload error codes to human-readable messages.
            // نگاشت کدهای خطای آپلود به پیام‌های قابل خواندن برای انسان.
            $upload_errors = [ /* ... error messages as before ... */
                UPLOAD_ERR_INI_SIZE   => __('File size exceeds the limit allowed by php.ini.', 'fanabyte-certificate'),
                UPLOAD_ERR_FORM_SIZE  => __('File size exceeds the limit allowed by the form.', 'fanabyte-certificate'),
                UPLOAD_ERR_PARTIAL    => __('The file was only partially uploaded.', 'fanabyte-certificate'),
                UPLOAD_ERR_NO_FILE    => __('No file was selected for upload.', 'fanabyte-certificate'),
                UPLOAD_ERR_NO_TMP_DIR => __('Missing temporary folder.', 'fanabyte-certificate'),
                UPLOAD_ERR_CANT_WRITE => __('Failed to write file to disk.', 'fanabyte-certificate'),
                UPLOAD_ERR_EXTENSION  => __('File upload stopped by a PHP extension.', 'fanabyte-certificate'),
                'no_file'             => __('No file found.', 'fanabyte-certificate'),
                'unknown'             => __('Unknown upload error.', 'fanabyte-certificate'),
            ];
            // Get the specific error detail or use the 'unknown' message.
            // دریافت جزئیات خطای خاص یا استفاده از پیام 'unknown'.
            $error_detail = $upload_errors[$error_code] ?? $upload_errors['unknown'];
            $message = esc_html__('Error uploading file:', 'fanabyte-certificate') . ' ' . esc_html($error_detail); /*خطا در آپلود فایل:*/
            $type = 'error';
            break;

        // --- File Type Error ---
        // --- خطای نوع فایل ---
        case 'import_error_type':
            $message = esc_html__('Invalid file format. Please upload a .json file only.', 'fanabyte-certificate'); /*فرمت فایل نامعتبر است. لطفا فقط فایل .json آپلود کنید.*/
            $type = 'error';
            break;

        // --- File Read Error ---
        // --- خطای خواندن فایل ---
        case 'import_error_read':
            $message = esc_html__('Error reading file content.', 'fanabyte-certificate'); /*خطا در خواندن محتوای فایل.*/
            $type = 'error';
            break;

        // --- JSON Decode Error ---
        // --- خطای دیکود JSON ---
        case 'import_error_json':
            // Map JSON error codes to messages.
            // نگاشت کدهای خطای JSON به پیام‌ها.
            $json_errors = [ /* ... json error messages as before ... */
                JSON_ERROR_NONE             => '',
                JSON_ERROR_DEPTH            => __('Maximum stack depth exceeded.', 'fanabyte-certificate'),
                JSON_ERROR_STATE_MISMATCH   => __('Invalid or malformed JSON.', 'fanabyte-certificate'),
                JSON_ERROR_CTRL_CHAR        => __('Control character error, possibly incorrectly encoded.', 'fanabyte-certificate'),
                JSON_ERROR_SYNTAX           => __('Syntax error.', 'fanabyte-certificate'),
                JSON_ERROR_UTF8             => __('Malformed UTF-8 characters, possibly incorrectly encoded.', 'fanabyte-certificate'),
                JSON_ERROR_RECURSION        => __('Recursive references detected.', 'fanabyte-certificate'),
                JSON_ERROR_INF_OR_NAN       => __('NAN or INF value encountered.', 'fanabyte-certificate'),
                JSON_ERROR_UNSUPPORTED_TYPE => __('A value of an unsupported type was found.', 'fanabyte-certificate'),
                JSON_ERROR_INVALID_PROPERTY_NAME => __('An invalid property name was found.', 'fanabyte-certificate'),
                JSON_ERROR_UTF16            => __('Malformed UTF-16 characters, possibly incorrectly encoded.', 'fanabyte-certificate'),
            ];
            $error_detail = $json_errors[$error_code] ?? __('Unknown JSON error.', 'fanabyte-certificate'); /*خطای نامشخص JSON.*/
            $message = esc_html__('Invalid JSON file:', 'fanabyte-certificate') . ' ' . esc_html($error_detail); /*فایل JSON نامعتبر است:*/
            $type = 'error';
            break;

        // --- JSON Format Error ---
        // --- خطای فرمت JSON ---
        case 'import_error_format':
            $message = esc_html__('Invalid JSON data structure.', 'fanabyte-certificate'); /*ساختار داده JSON نامعتبر است.*/
            $type = 'error';
            break;
    }

    // If a message was generated, display the admin notice.
    // اگر پیامی تولید شد، اعلان ادمین را نمایش بده.
    if ($message) {
        echo '<div id="setting-error-import-export" class="notice notice-' . esc_attr($type) . ' is-dismissible"><p>' . $message . '</p></div>'; // Output the notice HTML / خروجی HTML اعلان

        // --- JavaScript to remove query parameters from URL after displaying the notice ---
        // --- جاوااسکریپت برای حذف پارامترهای کوئری از URL پس از نمایش اعلان ---
        ?>
        <script type="text/javascript">
            window.addEventListener('load', function() {
                if (window.history && window.history.replaceState) {
                    var currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.delete('message');
                    currentUrl.searchParams.delete('code');
                    window.history.replaceState({ path: currentUrl.href }, '', currentUrl.href);
                }
            });
        </script>
        <?php
    }
}
// Hook the notice display function to the 'admin_notices' action.
// اتصال تابع نمایش اعلان به اکشن 'admin_notices'.
add_action('admin_notices', 'fb_cert_show_admin_notices');


/**
 * ==================================
 * Rewrite Rule Flushing Handling
 * مدیریت فلاش کردن قوانین بازنویسی
 * ==================================
 */

/**
 * Check if the CPT slug changed during option update and schedule a flush if needed.
 * بررسی اینکه آیا اسلاگ CPT در حین به‌روزرسانی آپشن تغییر کرده و در صورت نیاز فلاش را زمان‌بندی کند.
 */
function fb_cert_check_slug_change_and_flush($old_value, $new_value, $option_name) {
    // Get the old and new slugs, providing defaults if they don't exist.
    // دریافت اسلاگ‌های قدیمی و جدید، با ارائه پیش‌فرض در صورت عدم وجود.
    $old_slug = isset($old_value['cpt_slug']) ? $old_value['cpt_slug'] : 'certificate';
    $new_slug = isset($new_value['cpt_slug']) ? $new_value['cpt_slug'] : 'certificate';

    // If the slug has changed, schedule a rewrite rule flush.
    // اگر اسلاگ تغییر کرده است، فلاش قوانین بازنویسی را زمان‌بندی کن.
    if ($old_slug !== $new_slug) {
        fb_cert_schedule_rewrite_flush();
    }
}
// Hook this check to the update action for the specific settings option.
// اتصال این بررسی به اکشن به‌روزرسانی برای آپشن تنظیمات خاص.
add_action('update_option_fanabyte_certificate_search_settings', 'fb_cert_check_slug_change_and_flush', 10, 3);

/**
 * Set a flag in wp_options to indicate that rewrite rules need flushing.
 * تنظیم یک پرچم در wp_options برای نشان دادن اینکه قوانین بازنویسی نیاز به فلاش دارند.
 */
function fb_cert_schedule_rewrite_flush() {
    // Set the flag option with autoload set to false.
    // تنظیم آپشن پرچم با autoload تنظیم شده روی false.
    update_option('fb_cert_flush_rewrite_rules_flag', '1', false);
}

/**
 * Check for the flush flag on WordPress shutdown and flush rewrite rules if set.
 * بررسی پرچم فلاش در هنگام خاموش شدن وردپرس و فلاش کردن قوانین بازنویسی در صورت تنظیم بودن.
 */
function fb_cert_flush_scheduled_rewrite_rules() {
    // Check if the flag is set to '1'.
    // بررسی اینکه آیا پرچم روی '1' تنظیم شده است.
    if (get_option('fb_cert_flush_rewrite_rules_flag') === '1') {
        // Delete the flag immediately to prevent repeated flushes.
        // حذف فوری پرچم برای جلوگیری از فلاش‌های مکرر.
        delete_option('fb_cert_flush_rewrite_rules_flag');
        // Flush the rewrite rules.
        // فلاش کردن قوانین بازنویسی.
        flush_rewrite_rules();
    }
}
// Hook this function to the 'shutdown' action.
// اتصال این تابع به اکشن 'shutdown'.
add_action('shutdown', 'fb_cert_flush_scheduled_rewrite_rules');


/**
 * Callback function to display the Usage Guide page content.
 * تابع Callback برای نمایش محتوای صفحه راهنمای استفاده.
 */
function fb_cert_guide_page_callback() {
    ?>
    <div class="wrap fb-cert-guide-wrap">
        <h1><?php esc_html_e('FanaByte Certificate Inquiry Plugin Usage Guide', 'fanabyte-certificate'); /*راهنمای استفاده از افزونه استعلام مدرک فنابایت*/ ?></h1>
        <p><?php esc_html_e('Welcome to the FanaByte Certificate plugin guide! This guide explains the steps to use the various features of the plugin.', 'fanabyte-certificate'); /*به راهنمای افزونه FanaByte Certificate خوش آمدید! ...*/ ?></p>

        <div class="notice notice-info inline" style="margin-top: 15px;">
              <p><strong><?php esc_html_e('Initial Important Note:', 'fanabyte-certificate'); /*نکته مهم اولیه:*/ ?></strong> <?php
                  printf(
                      esc_html__('After activating the plugin or anytime you change the "Base URL Slug" in the settings, it is recommended to visit the %1$s page once and click the "Save Changes" button to ensure the certificate links work correctly.', 'fanabyte-certificate'),
                      '<strong><a href="' . esc_url(admin_url('options-permalink.php')) . '">' . esc_html__('Settings > Permalinks', 'fanabyte-certificate') . '</a></strong>'
                  );
                  /* پس از فعال‌سازی افزونه ... مراجعه و روی دکمه "ذخیره تغییرات" کلیک کنید... */
              ?></p>
        </div>

        <hr style="margin: 25px 0;">

        <h2 style="color: #2271b1;"><?php esc_html_e('1. Initial Setup: Field Management', 'fanabyte-certificate'); /*۱. تنظیمات اولیه: مدیریت فیلدها*/ ?></h2>
        <p><?php esc_html_e('The first step after installation and activation is to define the fields you want to have for each certificate.', 'fanabyte-certificate'); /*اولین قدم پس از نصب و فعال‌سازی، تعریف فیلدهایی است که می‌خواهید برای هر مدرک داشته باشید.*/ ?></p>
        <ol>
            <li><?php printf(esc_html__('From the admin menu, go to %s.', 'fanabyte-certificate'), '<strong>' . esc_html__('Certificates -> Settings', 'fanabyte-certificate') . '</strong>'); /*از منوی پیشخوان به ... بروید.*/ ?></li>
            <li><?php esc_html_e('Make sure you are on the "Field Management" tab.', 'fanabyte-certificate'); /*مطمئن شوید در تب "مدیریت فیلدها" هستید.*/ ?></li>
            <li><?php esc_html_e('In the "Add New Field" section: Select the field type (Text or Image) and click the "Add Field" button.', 'fanabyte-certificate'); /*در بخش "افزودن فیلد جدید": ... کلیک کنید.*/ ?></li>
            <li><?php esc_html_e('The new field will be added to the list above. Enter an appropriate "Field Label" (e.g., Student Name, National ID, Course Name).', 'fanabyte-certificate'); /*فیلد جدید به لیست بالا اضافه می‌شود. ... وارد کنید.*/ ?></li>
            <li><?php esc_html_e('The "Key Name" is generated automatically and is used to identify the field in the system.', 'fanabyte-certificate'); /*"نام کلیدی (Key)" به صورت خودکار ایجاد می‌شود ...*/ ?></li>
            <li><?php esc_html_e('Key field for search?: Check this option if you want users to be able to query using this field (maximum 2 text fields).', 'fanabyte-certificate'); /*فیلد کلیدی برای جستجو؟: ... تیک بزنید (حداکثر ۲ فیلد متنی).*/ ?></li>
            <li><?php printf(esc_html__('To change the display order of the fields, drag and drop the move icon (%s) next to each field.', 'fanabyte-certificate'), '<span class="dashicons dashicons-move"></span>'); /*برای تغییر ترتیب نمایش فیلدها، آیکون ... کنار هر فیلد را کشیده و رها کنید.*/ ?></li>
            <li><?php printf(esc_html__('To delete a field, click the %s button.', 'fanabyte-certificate'), '<strong>' . esc_html__('Delete', 'fanabyte-certificate') . '</strong>'); /*برای حذف فیلد، روی دکمه "حذف" کلیک کنید.*/ ?></li>
            <li><?php printf(esc_html__('After finishing, click the %s button.', 'fanabyte-certificate'), '<strong>' . esc_html__('Save Settings', 'fanabyte-certificate') . '</strong>'); /*پس از اتمام، روی دکمه "ذخیره تنظیمات" کلیک کنید.*/ ?></li>
        </ol>

        <hr style="margin: 25px 0;">

        <h2 style="color: #2271b1;"><?php esc_html_e('2. Form, URL, and Button Settings', 'fanabyte-certificate'); /*۲. تنظیمات فرم، URL و دکمه‌ها*/ ?></h2>
        <p><?php esc_html_e('In this section, you can customize the appearance and functionality of the search form, links, and related buttons.', 'fanabyte-certificate'); /*در این بخش می‌توانید ظاهر و عملکرد فرم جستجو، لینک‌ها و دکمه‌های مرتبط را سفارشی کنید.*/ ?></p>
        <ol>
            <li><?php esc_html_e('Go to the "Form/URL/Buttons Settings" tab.', 'fanabyte-certificate'); /*به تب "تنظیمات فرم/URL/دکمه‌ها" بروید.*/ ?></li>
            <li><strong><?php esc_html_e('Inquiry Form Customization:', 'fanabyte-certificate'); /*شخصی‌سازی فرم استعلام:*/ ?></strong>
                <ul>
                    <li><strong><?php esc_html_e('Text Above Form:', 'fanabyte-certificate'); /*متن بالای فرم:*/ ?></strong> <?php esc_html_e('Using the full WordPress editor, add help text, images, or any other content before the search form.', 'fanabyte-certificate'); /*با ویرایشگر کامل وردپرس، متن راهنما، تصویر ... قرار دهید.*/ ?></li>
                    <li><strong><?php esc_html_e('Search Button Text & Color:', 'fanabyte-certificate'); /*متن و رنگ دکمه جستجو:*/ ?></strong> <?php esc_html_e('Set the text and background color of the main form button.', 'fanabyte-certificate'); /*متن و رنگ پس‌زمینه دکمه اصلی فرم را تنظیم کنید.*/ ?></li>
                    <li><strong><?php esc_html_e('Placeholders:', 'fanabyte-certificate'); /*متن نگهدارنده:*/ ?></strong> <?php esc_html_e('Specify the hint text inside the search fields (key fields).', 'fanabyte-certificate'); /*متن راهنمای داخل فیلدهای جستجو (فیلدهای کلیدی) را مشخص کنید.*/ ?></li>
                </ul>
            </li>
             <li><strong><?php esc_html_e('Certificate URL Settings:', 'fanabyte-certificate'); /*تنظیمات URL مدرک:*/ ?></strong>
                <ul>
                     <li><strong><?php esc_html_e('Base URL Slug:', 'fanabyte-certificate'); /*اسلاگ پایه URL:*/ ?></strong> <?php printf(esc_html__('Determine the fixed part of the URL (e.g., `certificate`). Only lowercase letters, numbers, and hyphens are allowed. After changing, be sure to go to %s and save.', 'fanabyte-certificate'), '<strong>' . esc_html__('Settings > Permalinks', 'fanabyte-certificate') . '</strong>'); /*بخش ثابت URL را تعیین کنید ... رفته و ذخیره کنید.*/ ?></li>
                </ul>
            </li>
             <li><strong><?php esc_html_e('Download Button Customization:', 'fanabyte-certificate'); /*شخصی‌سازی دکمه دانلود:*/ ?></strong>
                <ul>
                    <li><strong><?php esc_html_e('Download Button Text & Color:', 'fanabyte-certificate'); /*متن و رنگ دکمه دانلود:*/ ?></strong> <?php esc_html_e('Set the text and background color of the "Download Certificate File" button that appears on the details page.', 'fanabyte-certificate'); /*متن و رنگ پس‌زمینه دکمه "دانلود فایل مدرک" ... را تنظیم کنید.*/ ?></li>
                </ul>
            </li>
             <li><strong><?php esc_html_e('Labels and Titles Settings:', 'fanabyte-certificate'); /*تنظیمات برچسب‌ها و عناوین:*/ ?></strong>
                <ul>
                    <li><?php esc_html_e('Customize various default labels like the "Details Section Heading" or metabox titles used in the admin area and frontend.', 'fanabyte-certificate'); /*برچسب‌های پیش‌فرض مختلف مانند "عنوان بخش جزئیات" ... را سفارشی کنید.*/ ?></li>
                </ul>
            </li>
            <li><?php printf(esc_html__('Finally, click the %s button.', 'fanabyte-certificate'), '<strong>' . esc_html__('Save Settings', 'fanabyte-certificate') . '</strong>'); /*در انتها روی دکمه "ذخیره تنظیمات" کلیک کنید.*/ ?></li>
        </ol>

         <hr style="margin: 25px 0;">

         <h2 style="color: #2271b1;"><?php esc_html_e('3. Adding and Managing Certificates', 'fanabyte-certificate'); /*۳. افزودن و مدیریت ‌مدرک‌ها*/ ?></h2>
         <p><?php esc_html_e('Follow these steps to register user certificates:', 'fanabyte-certificate'); /*برای ثبت ‌مدرک‌های کاربران مراحل زیر را دنبال کنید:*/ ?></p>
         <ol>
            <li><?php printf(esc_html__('From the admin menu, go to %s.', 'fanabyte-certificate'), '<strong>' . esc_html__('Certificates -> Add New', 'fanabyte-certificate') . '</strong>'); /*از منوی پیشخوان به ... بروید.*/ ?></li>
            <li><?php esc_html_e('Enter the certificate title (e.g., Course X Certificate - Student Name).', 'fanabyte-certificate'); /*عنوان مدرک را وارد کنید ...*/ ?></li>
            <li><?php printf(esc_html__('In the "%s" section, enter the values for the custom fields you defined for this specific certificate.', 'fanabyte-certificate'), esc_html__('Certificate Information & Details', 'fanabyte-certificate')); /*در بخش "%s"، مقادیر مربوط به ... وارد کنید.*/ ?></li>
            <li><?php printf(esc_html__('In the sidebar, in the "%s" metabox, upload the final certificate PDF or image file (optional).', 'fanabyte-certificate'), esc_html__('Main Certificate File (PDF/Image)', 'fanabyte-certificate')); /*در سایدبار، متاباکس "%s"، فایل PDF یا تصویر ... آپلود کنید (اختیاری).*/ ?></li>
            <li><?php esc_html_e('You can use the main editor for additional descriptions or to display specific content on the certificate page (optional).', 'fanabyte-certificate'); /*می‌توانید از ویرایشگر اصلی برای توضیحات بیشتر ... استفاده کنید (اختیاری).*/ ?></li>
            <li><?php printf(esc_html__('If needed, edit the %s (Slug) in the corresponding metabox.', 'fanabyte-certificate'), '<strong>' . esc_html__('Permalink', 'fanabyte-certificate') . '</strong>'); /*در صورت نیاز، پیوند یکتا (Slug) را در متاباکس مربوطه ویرایش کنید.*/ ?></li>
            <li><?php printf(esc_html__('Click the %s button.', 'fanabyte-certificate'), '<strong>' . esc_html__('Publish', 'fanabyte-certificate') . '</strong>'); /*روی دکمه "انتشار" کلیک کنید.*/ ?></li>
            <li><?php printf(esc_html__('To manage all certificates, go to the %s menu.', 'fanabyte-certificate'), '<strong>' . esc_html__('Certificates -> All Certificates', 'fanabyte-certificate') . '</strong>'); /*برای مدیریت همه ‌مدرک‌ها، به منوی ... مراجعه کنید.*/ ?></li>
         </ol>

         <hr style="margin: 25px 0;">

         <h2 style="color: #2271b1;"><?php esc_html_e('4. Displaying the Inquiry Form on Your Site', 'fanabyte-certificate'); /*۴. نمایش فرم استعلام در سایت*/ ?></h2>
         <p><?php esc_html_e('To enable the inquiry feature for users:', 'fanabyte-certificate'); /*برای فعال کردن قابلیت استعلام برای کاربران:*/ ?></p>
         <ol>
            <li><?php esc_html_e('Go to edit an existing page or post, or create a new page (e.g., titled "Certificate Inquiry").', 'fanabyte-certificate'); /*به بخش ویرایش یک برگه یا نوشته موجود بروید ... ایجاد کنید.*/ ?></li>
            <li><?php esc_html_e('In the content editor, place the following shortcode where you want the form to appear:', 'fanabyte-certificate'); /*در ویرایشگر محتوا، شورت‌کد زیر را در محل دلخواه قرار دهید:*/ ?>
                <p>
                    <?php // Shortcode display with copy button / نمایش کد کوتاه با دکمه کپی ?>
                    <code id="shortcode-to-copy" class="fb-shortcode-to-copy" data-clipboard-text="[fanabyte_certificate_lookup]">[fanabyte_certificate_lookup]</code>
                    <span class="copy-shortcode-button" style="margin-left: 10px; cursor: pointer; color: #2271b1; text-decoration: underline;" title="<?php esc_attr_e('Click to copy', 'fanabyte-certificate'); /*برای کپی کلیک کنید*/ ?>">
                        <?php esc_html_e('Copy Code', 'fanabyte-certificate'); /*کپی کد*/ ?>
                    </span>
                    <span class="copy-feedback" style="margin-left: 10px; color: green; font-weight: bold; display: none;">
                        <?php esc_html_e('Copied!', 'fanabyte-certificate'); /*کپی شد!*/ ?>
                    </span>
                </p>
            </li>
            <li><?php esc_html_e('Publish or update the page/post.', 'fanabyte-certificate'); /*برگه/نوشته را منتشر یا به‌روزرسانی کنید.*/ ?></li>
         </ol>
         <p><?php esc_html_e('Now, users visiting this page can search for their certificate by entering the key field information.', 'fanabyte-certificate'); /*اکنون کاربران با مراجعه به این برگه می‌توانند با وارد کردن اطلاعات فیلدهای کلیدی، مدرک خود را جستجو کنند.*/ ?></p>

         <hr style="margin: 25px 0;">

          <h2 style="color: #2271b1;"><?php esc_html_e('5. Direct Certificate View and QR Code', 'fanabyte-certificate'); /*۵. مشاهده مستقیم مدرک و کد QR*/ ?></h2>
          <p><?php esc_html_e('Each published certificate has a direct link and a QR code:', 'fanabyte-certificate'); /*هر مدرک منتشر شده، یک لینک مستقیم و یک کد QR دارد:*/ ?></p>
          <ul>
               <li><strong><?php esc_html_e('Direct Link:', 'fanabyte-certificate'); /*لینک مستقیم:*/ ?></strong> <?php esc_html_e('The unique URL for each certificate, which you can get from the edit screen or the certificate list. Its structure is based on the "Base URL Slug" set in step 2 and the certificate\'s own slug.', 'fanabyte-certificate'); /*آدرس URL منحصر به فرد هر مدرک ... و اسلاگ خود مدرک است.*/ ?></li>
               <li><strong><?php esc_html_e('QR Code:', 'fanabyte-certificate'); /*کد QR:*/ ?></strong> <?php esc_html_e('An image code containing the direct link to the certificate. This code is displayed in the corresponding column in the "All Certificates" list and also on the certificate details page on the site. Suitable for printing or quick sharing.', 'fanabyte-certificate'); /*یک کد تصویری که حاوی لینک مستقیم مدرک است. ... مناسب برای چاپ یا اشتراک‌گذاری سریع.*/ ?></li>
          </ul>

          <hr style="margin: 25px 0;">

          <h2 style="color: #2271b1;"><?php esc_html_e('6. Importing and Exporting Settings', 'fanabyte-certificate'); /*۶. واردات و صادرات تنظیمات*/ ?></h2>
          <p><?php esc_html_e('To back up or transfer plugin settings:', 'fanabyte-certificate'); /*برای پشتیبان‌گیری یا انتقال تنظیمات افزونه:*/ ?></p>
          <ol>
               <li><?php printf(esc_html__('Go to %1$s and select the %2$s tab.', 'fanabyte-certificate'), '<strong>' . esc_html__('Certificates -> Settings', 'fanabyte-certificate') . '</strong>', '<strong>' . esc_html__('Import/Export', 'fanabyte-certificate') . '</strong>'); /*به ... رفته و تب ... را انتخاب کنید.*/ ?></li>
               <li><strong><?php esc_html_e('Exporting:', 'fanabyte-certificate'); /*خروجی گرفتن:*/ ?></strong> <?php printf(esc_html__('Click the "%s" button to download a file containing all settings.', 'fanabyte-certificate'), esc_html__('Download Export File (JSON)', 'fanabyte-certificate')); /*روی دکمه "..." کلیک کنید تا یک فایل ... دانلود شود.*/ ?></li>
               <li><strong><?php esc_html_e('Importing:', 'fanabyte-certificate'); /*وارد کردن:*/ ?></strong> <?php printf(esc_html__('Select the JSON settings file and click the "%s" button. Current settings will be overwritten.', 'fanabyte-certificate'), esc_html__('Upload and Import Settings', 'fanabyte-certificate')); /*فایل JSON تنظیمات را انتخاب و روی دکمه "..." کلیک کنید. ... بازنویسی خواهند شد.*/ ?></li>
               <li><?php printf(esc_html__('Note: After importing, you might need to visit the %s page and click Save Changes once.', 'fanabyte-certificate'), '<strong>' . esc_html__('Settings > Permalinks', 'fanabyte-certificate') . '</strong>'); /*توجه: پس از وارد کردن، ممکن است لازم باشد به صفحه ... مراجعه و یک بار دکمه ذخیره را بزنید.*/ ?></li>
          </ol>

           <hr style="margin: 25px 0;">

          <h2 style="color: #2271b1;"><?php esc_html_e('7. Troubleshooting', 'fanabyte-certificate'); /*۷. عیب‌یابی*/ ?></h2>
          <ul>
               <li><strong><?php esc_html_e('404 Error (Page Not Found):', 'fanabyte-certificate'); /*خطای 404 (صفحه یافت نشد):*/ ?></strong> <?php printf(esc_html__('Usually resolved by visiting %s and clicking "Save Changes".', 'fanabyte-certificate'), '<strong>' . esc_html__('Settings > Permalinks', 'fanabyte-certificate') . '</strong>'); /*معمولاً با مراجعه به ... و کلیک روی "ذخیره تغییرات" حل می‌شود.*/ ?></li>
               <li><strong><?php esc_html_e('Incorrect Styles Display:', 'fanabyte-certificate'); /*عدم نمایش صحیح استایل‌ها:*/ ?></strong> <?php esc_html_e('Clear your browser and site cache.', 'fanabyte-certificate'); /*کش مرورگر و سایت خود را پاک کنید.*/ ?></li>
               <li><strong><?php esc_html_e('Other Issues:', 'fanabyte-certificate'); /*مشکلات دیگر:*/ ?></strong> <?php printf(esc_html__('For further assistance or to report an issue, please refer to the "%s" page in the plugin menu.', 'fanabyte-certificate'), esc_html__('About Us', 'fanabyte-certificate')); /*برای راهنمایی بیشتر یا گزارش مشکل، به صفحه "درباره ما" در منوی افزونه مراجعه کنید.*/ ?></li>
          </ul>

    </div><?php
} // End fb_cert_guide_page_callback

?>