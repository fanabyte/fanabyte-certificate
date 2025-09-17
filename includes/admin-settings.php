<?php
/**
 * File for managing the Fanabyte Certificate plugin settings page.
 *
 * Version: 1.3.0 - Added a new 'Language' tab to manage plugin language and LTR/RTL display.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add the main menu and submenus for the plugin to the WordPress admin dashboard.
 */
function fb_cert_add_admin_menu() {
    add_menu_page(
        __('FanaByte Certificates', 'fanabyte-certificate'),
        __('Certificate Inquiry', 'fanabyte-certificate'),
        'manage_options',
        'fanabyte-certificate-settings',
        'fb_cert_settings_page_callback',
        'dashicons-awards',
        30
    );

    add_submenu_page(
        'fanabyte-certificate-settings',
        __('Certificate Inquiry Settings', 'fanabyte-certificate'),
        __('Settings', 'fanabyte-certificate'),
        'manage_options',
        'fanabyte-certificate-settings',
        'fb_cert_settings_page_callback'
    );

    add_submenu_page(
        'fanabyte-certificate-settings',
        __('Plugin Usage Guide', 'fanabyte-certificate'),
        __('Usage Guide', 'fanabyte-certificate'),
        'manage_options',
        'fanabyte-certificate-guide',
        'fb_cert_guide_page_callback'
    );

    add_submenu_page(
        'fanabyte-certificate-settings',
        __('About the Plugin', 'fanabyte-certificate'),
        __('About Us', 'fanabyte-certificate'),
        'manage_options',
        'fanabyte-certificate-about',
        'fb_cert_about_page_callback'
    );

    // New: Add a submenu for Language Settings.
    add_submenu_page(
        'fanabyte-certificate-settings',
        __('Plugin Language', 'fanabyte-certificate'),
        __('Plugin Language', 'fanabyte-certificate'),
        'manage_options',
        'fanabyte-certificate-language',
        'fb_cert_language_settings_page_callback'
    );
}
add_action('admin_menu', 'fb_cert_add_admin_menu');

/**
 * Callback function to display the main settings page content with tabs.
 */
function fb_cert_settings_page_callback() {
    $language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
    $is_rtl = ($language_settings['language'] === 'fa');
    ?>
    <div class="wrap fb-cert-settings-wrap" <?php echo $is_rtl ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php settings_errors(); ?>

        <h2 class="nav-tab-wrapper">
            <?php
            $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'fields';
            ?>
            <a href="?page=fanabyte-certificate-settings&tab=fields" class="nav-tab <?php echo $active_tab == 'fields' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Field Management', 'fanabyte-certificate'); ?></a>
            <a href="?page=fanabyte-certificate-settings&tab=search_form" class="nav-tab <?php echo $active_tab == 'search_form' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Form/URL/Buttons Settings', 'fanabyte-certificate'); ?></a>
            <a href="?page=fanabyte-certificate-settings&tab=import_export" class="nav-tab <?php echo $active_tab == 'import_export' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Import/Export', 'fanabyte-certificate'); ?></a>
        </h2>

        <?php if ($active_tab !== 'import_export') : ?>
            <form action="options.php" method="post">
                <?php
                if ($active_tab == 'fields') {
                    settings_fields('fb_cert_fields_settings_group');
                    do_settings_sections('fanabyte-certificate-fields');
                } elseif ($active_tab == 'search_form') {
                    settings_fields('fb_cert_search_settings_group');
                    do_settings_sections('fanabyte-certificate-search');
                }
                submit_button(__('Save Settings', 'fanabyte-certificate'));
                ?>
            </form>
        <?php else: ?>
            <?php do_settings_sections('fanabyte-certificate-importexport'); ?>
            <div class="fb-cert-import-export-section">
                <h3><?php esc_html_e('Export Settings', 'fanabyte-certificate'); ?></h3>
                <p><?php esc_html_e('Click the button below to export the current plugin settings (fields and form/URL/button settings) as a JSON file.', 'fanabyte-certificate'); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="fb_cert_export_settings" />
                    <?php wp_nonce_field('fb_cert_export_nonce', 'fb_cert_export_nonce_field'); ?>
                    <?php submit_button(__('Download Export File (JSON)', 'fanabyte-certificate'), 'secondary', 'fb_cert_export_submit', false); ?>
                </form>
            </div>
            <hr>
            <div class="fb-cert-import-export-section">
                <h3><?php esc_html_e('Import Settings', 'fanabyte-certificate'); ?></h3>
                <p><?php esc_html_e('Select the JSON settings file you previously exported and upload it here. Note: This will overwrite your current settings.', 'fanabyte-certificate'); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="fb_cert_import_settings" />
                    <?php wp_nonce_field('fb_cert_import_nonce', 'fb_cert_import_nonce_field'); ?>
                    <p>
                        <label for="fb_cert_import_file"><?php esc_html_e('Select JSON file:', 'fanabyte-certificate'); ?></label><br>
                        <input type="file" id="fb_cert_import_file" name="fb_cert_import_file" accept=".json" required />
                    </p>
                    <?php submit_button(__('Upload and Import Settings', 'fanabyte-certificate'), 'primary', 'fb_cert_import_submit', false); ?>
                </form>
            </div>
        <?php endif; ?>
    </div><?php
}

/**
 * Callback function to display the Language Settings page content.
 */
function fb_cert_language_settings_page_callback() {
    $language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
    $is_rtl = ($language_settings['language'] === 'fa');
    ?>
    <div class="wrap fb-cert-settings-wrap" <?php echo $is_rtl ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        <h1><?php esc_html_e('Plugin Language', 'fanabyte-certificate'); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('fb_cert_language_settings_group');
            do_settings_sections('fanabyte-certificate-language');
            submit_button(__('Save Settings', 'fanabyte-certificate'));
            ?>
        </form>
    </div>
    <?php
}

/**
 * Callback for the About Us page content.
 */
function fb_cert_about_page_callback() {
    $image_base_url = defined('FB_CERT_URL') ? trailingslashit(FB_CERT_URL . 'assets/images') : '';
    $donation_link = 'https://www.coffeete.ir/fanabyte';

    $social_links = [
        'website'   => 'https://fanabyte.com',
        'youtube'   => '#',
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
    $language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
    $is_rtl = ($language_settings['language'] === 'fa');
    ?>
    <div class="wrap fb-about-page-wrap" style="text-align: right; direction: rtl;">
        <h1><?php esc_html_e('About the Plugin and FanaByte Academy', 'fanabyte-certificate'); ?></h1>
        <div class="fb-about-section">
            <p style="font-size: 1.1em;"><strong><?php esc_html_e('This plugin was designed and developed by FanaByte Academy.', 'fanabyte-certificate'); ?></strong></p>
            <?php if ($image_base_url) : ?>
            <div style="margin: 25px 0; text-align: center;">
                <img src="<?php echo esc_url($image_base_url . 'fanabyte-logo.png'); ?>"
                     alt="<?php esc_attr_e('FanaByte Academy Logo', 'fanabyte-certificate'); ?>"
                     style="max-width: 180px; height: auto;" class="fb-about-logo">
            </div>
            <?php endif; ?>
        </div>
        <hr>
        <div class="fb-about-section">
            <h2><?php esc_html_e('The FanaByte Story', 'fanabyte-certificate'); ?></h2>
            <div style="line-height: 1.8;">
                <p><?php esc_html_e('We started in 1400 (Persian calendar), or more accurately, since 1390. In the beginning, we didn\'t have an official website. FanaByte\'s goal is to help everyone build a successful online business.', 'fanabyte-certificate'); ?></p>
                <p><?php esc_html_e('But how? At FanaByte, we teach you how to set up and use the world\'s best website builder, which powers over 40% of the world\'s websites.', 'fanabyte-certificate'); ?></p>
                <p><?php esc_html_e('We have also published many free articles and video tutorials, as well as several training packages in various fields, which you can use to increase your knowledge and develop your business.', 'fanabyte-certificate'); ?></p>
            </div>
        </div>
        <hr>
        <div class="fb-about-section">
            <h2><?php esc_html_e('Follow Us!', 'fanabyte-certificate'); ?></h2>
            <p><?php esc_html_e('Please follow us on social media:', 'fanabyte-certificate'); ?></p>
            <div class="fb-social-icons" style="margin-top: 20px; text-align: center; line-height: 1;">
                <?php if ($image_base_url) : ?>
                    <?php
                    $icons = [
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
                    <?php // *** START: Donation Section *** ?>
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed #eee;">
                        <p style="font-size: 1.1em; margin-bottom: 15px;">☕ <?php esc_html_e('If you enjoy this plugin and find it useful, please consider supporting its development by buying us a coffee. Your support is greatly appreciated!', 'fanabyte-certificate'); ?></p>
                        <a href="<?php echo esc_url($donation_link); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary" style="font-size: 1.1em;">
                            <?php esc_html_e('Support FanaByte on Coffeete', 'fanabyte-certificate'); ?>
                        </a>
                    </div>
                    <?php // *** END: Donation Section *** ?>
                    <p style="margin-top: 25px;">
                         <a href="<?php echo esc_url($social_links['website']); ?>" target="_blank" rel="noopener noreferrer" style="font-weight: bold; text-decoration: none;">
                            <?php esc_html_e('Visit FanaByte Academy Website', 'fanabyte-certificate'); ?>
                         </a>
                    </p>
                <?php else : ?>
                    <p style="color: red;"><?php printf(esc_html__('Error: Image path not found (%s).', 'fanabyte-certificate'), '<code>assets/images</code>'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div><?php
}

/**
 * Register plugin settings, sections, and fields using the WordPress Settings API.
 */
function fb_cert_register_settings() {

    // --- Fields Settings Group ---
    register_setting(
        'fb_cert_fields_settings_group',
        'fanabyte_certificate_fields',
        'fb_cert_sanitize_fields_settings'
    );
    add_settings_section(
        'fb_cert_fields_section',
        __('Certificate Fields', 'fanabyte-certificate'),
        'fb_cert_fields_section_callback',
        'fanabyte-certificate-fields'
    );
    add_settings_field(
        'fb_cert_fields_list',
        __('Defined Fields', 'fanabyte-certificate'),
        'fb_cert_fields_list_callback',
        'fanabyte-certificate-fields',
        'fb_cert_fields_section'
    );

    // --- Search Form / URL / Buttons Settings Group ---
    register_setting(
        'fb_cert_search_settings_group',
        'fanabyte_certificate_search_settings',
        'fb_cert_sanitize_search_settings'
    );
    add_settings_section(
        'fb_cert_search_form_section',
        __('1. Inquiry Form Customization', 'fanabyte-certificate'),
        'fb_cert_search_form_section_callback',
        'fanabyte-certificate-search'
    );
    add_settings_field(
        'fb_cert_search_intro_text',
        __('Text Above Inquiry Form', 'fanabyte-certificate'),
        'fb_cert_search_intro_text_callback',
        'fanabyte-certificate-search',
        'fb_cert_search_form_section'
    );
    add_settings_field(
        'fb_cert_search_button_text',
        __('Search Button Text', 'fanabyte-certificate'),
        'fb_cert_search_button_text_callback',
        'fanabyte-certificate-search',
        'fb_cert_search_form_section'
    );
    add_settings_field(
        'fb_cert_search_button_color',
        __('Search Button Color', 'fanabyte-certificate'),
        'fb_cert_search_button_color_callback',
        'fanabyte-certificate-search',
        'fb_cert_search_form_section'
    );
    add_settings_field(
        'fb_cert_search_placeholders',
        __('Field Placeholders', 'fanabyte-certificate'),
        'fb_cert_search_placeholders_callback',
        'fanabyte-certificate-search',
        'fb_cert_search_form_section'
    );
    add_settings_section(
        'fb_cert_url_section',
        __('2. Certificate URL Settings', 'fanabyte-certificate'),
        'fb_cert_url_section_callback',
        'fanabyte-certificate-search'
    );
    add_settings_field(
        'fb_cert_cpt_slug',
        __('Base URL Slug', 'fanabyte-certificate'),
        'fb_cert_cpt_slug_callback',
        'fanabyte-certificate-search',
        'fb_cert_url_section'
    );
    add_settings_section(
        'fb_cert_download_button_section',
        __('3. Download Button Customization', 'fanabyte-certificate'),
        'fb_cert_download_button_section_callback',
        'fanabyte-certificate-search'
    );
    add_settings_field(
        'fb_cert_download_button_text',
        __('Download Button Text', 'fanabyte-certificate'),
        'fb_cert_download_button_text_callback',
        'fanabyte-certificate-search',
        'fb_cert_download_button_section'
    );
    add_settings_field(
        'fb_cert_download_button_color',
        __('Download Button Color', 'fanabyte-certificate'),
        'fb_cert_download_button_color_callback',
        'fanabyte-certificate-search',
        'fb_cert_download_button_section'
    );
    add_settings_section(
        'fb_cert_labels_section',
        __('4. Labels and Titles Settings', 'fanabyte-certificate'),
        'fb_cert_labels_section_callback',
        'fanabyte-certificate-search'
    );
    add_settings_field(
        'fb_cert_label_details_heading',
        __('Details Section Heading', 'fanabyte-certificate'),
        'fb_cert_label_details_heading_callback',
        'fanabyte-certificate-search',
        'fb_cert_labels_section'
    );
    add_settings_field(
        'fb_cert_metabox_title_main_data',
        __('Info Metabox Title', 'fanabyte-certificate'),
        'fb_cert_metabox_title_main_data_callback',
        'fanabyte-certificate-search',
        'fb_cert_labels_section'
    );
    add_settings_field(
        'fb_cert_label_personal_photo',
        __('Personal Photo Field Label', 'fanabyte-certificate'),
        'fb_cert_label_personal_photo_callback',
        'fanabyte-certificate-search',
        'fb_cert_labels_section'
    );
    add_settings_field(
        'fb_cert_metabox_title_main_file',
        __('Main File Metabox Title', 'fanabyte-certificate'),
        'fb_cert_metabox_title_main_file_callback',
        'fanabyte-certificate-search',
        'fb_cert_labels_section'
    );

    // --- Import/Export Section ---
    add_settings_section( 'fb_cert_importexport_section', '', '__return_null', 'fanabyte-certificate-importexport');

    // New: Language Settings Group
    register_setting(
        'fb_cert_language_settings_group',
        'fanabyte_certificate_language_settings',
        'fb_cert_sanitize_language_settings'
    );
    add_settings_section(
        'fb_cert_language_section',
        __('Plugin Language', 'fanabyte-certificate'),
        'fb_cert_language_section_callback',
        'fanabyte-certificate-language'
    );
    add_settings_field(
        'fb_cert_language_selection',
        __('Select Language', 'fanabyte-certificate'),
        'fb_cert_language_selection_callback',
        'fanabyte-certificate-language',
        'fb_cert_language_section'
    );
}
add_action('admin_init', 'fb_cert_register_settings');

function fb_cert_fields_section_callback() {
    $allowed_html_for_this_string = array(
        'span' => array(
            'class' => true,
        ),
    );
    echo '<p>' . wp_kses(
        __( 'In this section, you can define the fields you want to enter for each certificate. You can specify the display order of the fields by dragging the move icon <span class="dashicons dashicons-move"></span>. You can select up to two text fields as "key fields" for searching.', 'fanabyte-certificate' ),
        $allowed_html_for_this_string
    ) . '</p>';
}

function fb_cert_fields_list_callback() {
    $fields = get_option('fanabyte_certificate_fields', []);
    ?>
    <div id="fb-cert-fields-container" class="fb-cert-sortable">
        <?php if (!empty($fields) && is_array($fields)): ?>
            <?php foreach ($fields as $key => $field): ?>
                <?php if (!is_array($field)) continue; ?>
                <div class="fb-cert-field-row" data-key="<?php echo esc_attr($key); ?>">
                    <span class="dashicons dashicons-move fb-cert-drag-handle" title="<?php esc_attr_e('Drag to reorder', 'fanabyte-certificate'); ?>"></span>
                    <input type="hidden" class="fb-cert-field-order" name="fanabyte_certificate_fields[<?php echo esc_attr($key); ?>][order]" value="<?php echo esc_attr($field['order'] ?? ''); ?>">
                    <div class="fb-cert-field-details">
                        <p>
                            <label><?php esc_html_e('Field Type:', 'fanabyte-certificate'); ?> <strong><?php echo isset($field['type']) && $field['type'] === 'image' ? esc_html__('Image', 'fanabyte-certificate') : esc_html__('Text', 'fanabyte-certificate'); ?></strong></label>
                            &nbsp; | &nbsp;
                            <label><?php esc_html_e('Key Name:', 'fanabyte-certificate'); ?> <code><?php echo esc_html($key); ?></code></label>
                            <input type="hidden" name="fanabyte_certificate_fields[<?php echo esc_attr($key); ?>][type]" value="<?php echo esc_attr($field['type'] ?? 'text'); ?>">
                        </p>
                        <p>
                            <label for="fb_field_label_<?php echo esc_attr($key); ?>"><?php esc_html_e('Field Label:', 'fanabyte-certificate'); ?></label>
                            <input type="text" id="fb_field_label_<?php echo esc_attr($key); ?>" name="fanabyte_certificate_fields[<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($field['label'] ?? ''); ?>" required>
                        </p>
                        <?php if (isset($field['type']) && $field['type'] === 'text') : ?>
                            <p>
                                <label>
                                    <input type="checkbox" name="fanabyte_certificate_fields[<?php echo esc_attr($key); ?>][is_key]" value="1" <?php checked(isset($field['is_key']) && $field['is_key']); ?>>
                                    <?php esc_html_e('Key field for search?', 'fanabyte-certificate'); ?>
                                </label>
                            </p>
                        <?php else: ?>
                            <input type="hidden" name="fanabyte_certificate_fields[<?php echo esc_attr($key); ?>][is_key]" value="0">
                        <?php endif; ?>
                    </div>
                    <div class="fb-cert-field-actions">
                        <button type="button" class="button button-link-delete fb-remove-field" title="<?php esc_attr_e('Delete this field', 'fanabyte-certificate'); ?>">
                             <span class="dashicons dashicons-trash"></span>
                             <span class="screen-reader-text"><?php esc_html_e('Delete', 'fanabyte-certificate'); ?></span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?php esc_html_e('No fields have been defined yet. Add a new field using the section below.', 'fanabyte-certificate'); ?></p>
        <?php endif; ?>
    </div><hr>
    <div class="fb-add-new-field-section">
        <h3><?php esc_html_e('Add New Field', 'fanabyte-certificate'); ?></h3>
        <label for="new_field_type"><?php esc_html_e('Field Type:', 'fanabyte-certificate'); ?></label>
        <select id="new_field_type">
            <option value="text"><?php esc_html_e('Text', 'fanabyte-certificate'); ?></option>
            <option value="image"><?php esc_html_e('Image', 'fanabyte-certificate'); ?></option>
        </select>
        <button type="button" id="fb-add-field-button" class="button"><?php esc_html_e('Add Field', 'fanabyte-certificate'); ?></button>
        <p class="description"><?php esc_html_e('After adding, you can change the field order by dragging and dropping.', 'fanabyte-certificate'); ?></p>
    </div>
    <?php
}

function fb_cert_sanitize_fields_settings($input) {
    $new_input_unsorted = [];
    if (empty($input) || !is_array($input)) {
        return $new_input_unsorted;
    }
    foreach ($input as $key => $field_data) {
        $clean_key = sanitize_key($key);
        if (empty($clean_key) || !is_array($field_data)) {
            continue;
        }
        $new_field = [];
        $new_field['type'] = isset($field_data['type']) && in_array($field_data['type'], ['text', 'image']) ? $field_data['type'] : 'text';
        $new_field['label'] = isset($field_data['label']) ? sanitize_text_field($field_data['label']) : '';
        if (empty($new_field['label'])) {
            continue;
        }
        $new_field['is_key'] = (isset($field_data['is_key']) && $field_data['is_key'] == '1' && $new_field['type'] === 'text');
        $new_field['order'] = isset($field_data['order']) ? absint($field_data['order']) : 999;
        $new_input_unsorted[$clean_key] = $new_field;
    }
    uasort($new_input_unsorted, function($a, $b) {
        return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
    });
    $final_input = [];
    $current_key_count = 0;
    foreach($new_input_unsorted as $key => $field){
         if(isset($field['is_key']) && $field['is_key']){
             if($current_key_count >= 2) {
                 $field['is_key'] = false;
             } else {
                 $current_key_count++;
             }
         } else {
             $field['is_key'] = false;
         }
         unset($field['order']);
         $final_input[$key] = $field;
    }
    $original_key_count = 0;
    if (is_array($input)) {
        foreach ($input as $field_data) {
            if (is_array($field_data) && isset($field_data['is_key']) && $field_data['is_key'] == '1' && isset($field_data['type']) && $field_data['type'] === 'text') {
                $original_key_count++;
            }
        }
    }
    if($original_key_count > 2) {
        add_settings_error(
            'fanabyte_certificate_fields',
            'key_field_limit',
            __('You cannot select more than 2 key fields. Only the first 2 in the order were saved as keys.', 'fanabyte-certificate'),
            'warning'
        );
    }
    return $final_input;
}

function fb_cert_search_form_section_callback() {
    echo '<p>' . esc_html__('Settings for the text and button of the inquiry form.', 'fanabyte-certificate') . '</p>';
}

function fb_cert_search_intro_text_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $content = $settings['intro_text'] ?? '';
    wp_editor($content, 'fb_cert_intro_text_editor', [
        'textarea_name' => 'fanabyte_certificate_search_settings[intro_text]',
        'media_buttons' => true,
        'textarea_rows' => 10,
    ]);
    echo '<p class="description">' . esc_html__('This text will be displayed before the search form. You can use all the features of the WordPress editor.', 'fanabyte-certificate') . '</p>';
}

function fb_cert_search_button_text_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default_text = __('Search', 'fanabyte-certificate');
    $value = $settings['button_text'] ?? $default_text;
    echo '<input type="text" name="fanabyte_certificate_search_settings[button_text]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default_text) . '" />';
}

function fb_cert_search_button_color_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default_color = '#2271b1';
    $value = $settings['button_color'] ?? $default_color;
    echo '<input type="text" name="fanabyte_certificate_search_settings[button_color]" value="' . esc_attr($value) . '" class="fb-cert-color-picker" data-default-color="' . esc_attr($default_color) . '" />';
    echo '<p class="description">' . esc_html__('Background color for the search button.', 'fanabyte-certificate') . '</p>';
}

function fb_cert_search_placeholders_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $defined_fields = get_option('fanabyte_certificate_fields', []);
    $key_fields = [];
    if (!empty($defined_fields) && is_array($defined_fields)) {
        $key_fields = array_filter($defined_fields, function($field){
            return isset($field['is_key']) && $field['is_key'] && isset($field['type']) && $field['type'] === 'text';
        });
    }
    if(empty($key_fields)){
        echo '<p>' . esc_html__('First, select at least one field as a "key field" in the "Field Management" tab.', 'fanabyte-certificate') . '</p>';
        return;
    }
    echo '<p>' . esc_html__('Placeholder text (hint) displayed inside each search field:', 'fanabyte-certificate') . '</p>';
    foreach($key_fields as $key => $field){
        $placeholder_value = $settings['placeholders'][$key] ?? '';
        $default_placeholder = sprintf(__('Please enter %s', 'fanabyte-certificate'), $field['label']);
        echo '<p>';
        echo '<label for="placeholder_' . esc_attr($key) . '">' . esc_html($field['label']) . ':</label><br/>';
        echo '<input type="text" id="placeholder_' . esc_attr($key) . '" name="fanabyte_certificate_search_settings[placeholders][' . esc_attr($key) . ']" value="' . esc_attr($placeholder_value) . '" class="regular-text" placeholder="'. esc_attr($default_placeholder) .'" />';
        echo '</p>';
    }
}

function fb_cert_url_section_callback() {
    echo '<p>' . esc_html__('In this section, you can set the fixed part of the certificate URLs (slug).', 'fanabyte-certificate') . '</p>';
    echo '<p><strong>' . esc_html__('Important:', 'fanabyte-certificate') . '</strong> ' .
         sprintf(
            esc_html__('After changing and saving this value, you must visit the Settings > %s page in the WordPress admin and click the "Save Changes" button (even without changing anything there). This is necessary to apply the new URL structure.', 'fanabyte-certificate'),
            '<a href="' . esc_url(admin_url('options-permalink.php')) . '">' . esc_html__('Permalinks', 'fanabyte-certificate') . '</a>'
         ) . '</p>';
}

function fb_cert_cpt_slug_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default_slug = 'certificate';
    $value = $settings['cpt_slug'] ?? $default_slug;
    ?>
    <input type="text" name="fanabyte_certificate_search_settings[cpt_slug]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="<?php echo esc_attr($default_slug); ?>" />
    <p class="description">
        <?php esc_html_e('Use only lowercase English letters, numbers, and hyphens (-).', 'fanabyte-certificate'); ?>
        <br>
        <?php
        printf(
            esc_html__('Current certificate URL structure: %s', 'fanabyte-certificate'),
            '<code>' . esc_url(home_url('/' . $value . '/your-certificate-slug/')) . '</code>'
        );
        ?>
    </p>
    <?php
}

function fb_cert_download_button_section_callback() {
    echo '<p>' . esc_html__('Settings for the text and color of the "Download Certificate File" button displayed on the details page.', 'fanabyte-certificate') . '</p>';
}

function fb_cert_download_button_text_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default_text = __('Download Certificate File', 'fanabyte-certificate');
    $value = $settings['download_button_text'] ?? $default_text;
    echo '<input type="text" name="fanabyte_certificate_search_settings[download_button_text]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default_text) . '" />';
}

function fb_cert_download_button_color_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default_color = '#2ecc71';
    $value = $settings['download_button_color'] ?? $default_color;
    echo '<input type="text" name="fanabyte_certificate_search_settings[download_button_color]" value="' . esc_attr($value) . '" class="fb-cert-color-picker" data-default-color="' . esc_attr($default_color) . '" />';
    echo '<p class="description">' . esc_html__('Background color for the download button.', 'fanabyte-certificate') . '</p>';
}

function fb_cert_labels_section_callback() {
    echo '<p>' . esc_html__('Change the default text for some parts of the plugin here.', 'fanabyte-certificate') . '</p>';
}

function fb_cert_label_details_heading_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default = __('Certificate Details:', 'fanabyte-certificate');
    $value = $settings['label_details_heading'] ?? $default;
    echo '<input type="text" name="fanabyte_certificate_search_settings[label_details_heading]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default) . '" />';
    echo '<p class="description">' . esc_html__('The heading displayed above the custom fields list on the certificate view page.', 'fanabyte-certificate') . '</p>';
}

function fb_cert_metabox_title_main_data_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default = __('Certificate Information & Details', 'fanabyte-certificate');
    $value = $settings['metabox_title_main_data'] ?? $default;
    echo '<input type="text" name="fanabyte_certificate_search_settings[metabox_title_main_data]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default) . '" />';
    echo '<p class="description">' . esc_html__('Title for the main metabox on the certificate edit/add screen.', 'fanabyte-certificate') . '</p>';
}

function fb_cert_label_personal_photo_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default = __('Personal Photo (Optional)', 'fanabyte-certificate');
    $value = $settings['label_personal_photo'] ?? $default;
    echo '<input type="text" name="fanabyte_certificate_search_settings[label_personal_photo]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default) . '" />';
     echo '<p class="description">' . esc_html__('Label for the personal photo upload field on the certificate edit/add screen.', 'fanabyte-certificate') . '</p>';
}

function fb_cert_metabox_title_main_file_callback() {
    $settings = get_option('fanabyte_certificate_search_settings');
    $default = __('Main Certificate File (PDF/Image)', 'fanabyte-certificate');
    $value = $settings['metabox_title_main_file'] ?? $default;
    echo '<input type="text" name="fanabyte_certificate_search_settings[metabox_title_main_file]" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . esc_attr($default) . '" />';
    echo '<p class="description">' . esc_html__('Title for the main file upload metabox on the certificate edit/add screen.', 'fanabyte-certificate') . '</p>';
}

function fb_cert_sanitize_search_settings($input) {
     $new_input = [];
     $default_settings = [
        'intro_text'             => '',
        'button_text'            => __('Search', 'fanabyte-certificate'),
        'button_color'           => '#2271b1',
        'placeholders'           => [],
        'cpt_slug'               => 'certificate',
        'download_button_text'   => __('Download Certificate File', 'fanabyte-certificate'),
        'download_button_color'  => '#2ecc71',
        'label_details_heading'  => __('Certificate Details:', 'fanabyte-certificate'),
        'metabox_title_main_data'=> __('Certificate Information & Details', 'fanabyte-certificate'),
        'label_personal_photo'   => __('Personal Photo (Optional)', 'fanabyte-certificate'),
        'metabox_title_main_file'=> __('Main Certificate File (PDF/Image)', 'fanabyte-certificate')
     ];
     $new_input['intro_text'] = isset($input['intro_text']) ? wp_kses_post($input['intro_text']) : $default_settings['intro_text'];
     $new_input['button_text'] = isset($input['button_text']) ? sanitize_text_field($input['button_text']) : $default_settings['button_text'];
     if (isset($input['button_color']) && preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $input['button_color'])) {
         $new_input['button_color'] = $input['button_color'];
     } else {
         $new_input['button_color'] = $default_settings['button_color'];
     }
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
     if (isset($input['cpt_slug']) && !empty(trim($input['cpt_slug']))) {
         $sanitized_slug = sanitize_title_with_dashes(trim($input['cpt_slug']));
         $new_input['cpt_slug'] = !empty($sanitized_slug) ? $sanitized_slug : $default_settings['cpt_slug'];
     } else {
         $new_input['cpt_slug'] = $default_settings['cpt_slug'];
     }
     $new_input['download_button_text'] = isset($input['download_button_text']) && !empty(trim($input['download_button_text']))
                                          ? sanitize_text_field($input['download_button_text'])
                                          : $default_settings['download_button_text'];
     if (isset($input['download_button_color']) && preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $input['download_button_color'])) {
         $new_input['download_button_color'] = $input['download_button_color'];
     } else {
         $new_input['download_button_color'] = $default_settings['download_button_color'];
     }
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
     return $new_input;
}

function fb_cert_handle_export_settings() {
    if (!isset($_POST['fb_cert_export_nonce_field']) || !wp_verify_nonce($_POST['fb_cert_export_nonce_field'], 'fb_cert_export_nonce')) {
        wp_die(
            esc_html__('Security check failed!', 'fanabyte-certificate'),
            esc_html__('Security Error', 'fanabyte-certificate'),
            ['response' => 403]
        );
    }
    if (!current_user_can('manage_options')) {
        wp_die(
            esc_html__('You do not have permission to perform this action.', 'fanabyte-certificate'),
            esc_html__('Permission Denied', 'fanabyte-certificate'),
            ['response' => 403]
        );
    }

    $settings_to_export = [
        'version' => FB_CERT_VERSION,
        'fields'  => get_option('fanabyte_certificate_fields', []),
        'search'  => get_option('fanabyte_certificate_search_settings', []),
        'language' => get_option('fanabyte_certificate_language_settings', [])
    ];
    $filename = 'fanabyte-certificate-settings-' . date('Y-m-d') . '.json';
    $json_data = json_encode($settings_to_export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    nocache_headers();
    header('Content-Description: File Transfer');
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($json_data));

    echo $json_data;
    exit;
}
add_action('admin_post_fb_cert_export_settings', 'fb_cert_handle_export_settings');

function fb_cert_handle_import_settings() {
    if (!isset($_POST['fb_cert_import_nonce_field']) || !wp_verify_nonce($_POST['fb_cert_import_nonce_field'], 'fb_cert_import_nonce')) {
        wp_die(__('Security check failed!', 'fanabyte-certificate'), __('Security Error', 'fanabyte-certificate'), ['response' => 403]);
    }
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have permission to perform this action.', 'fanabyte-certificate'), __('Permission Denied', 'fanabyte-certificate'), ['response' => 403]);
    }

    $redirect_url = add_query_arg(
        ['page' => 'fanabyte-certificate-settings', 'tab' => 'import_export'],
        admin_url('admin.php')
    );

    if (!isset($_FILES['fb_cert_import_file']) || !is_uploaded_file($_FILES['fb_cert_import_file']['tmp_name'])) {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_upload', 'code' => 'no_file'], $redirect_url));
        exit;
    }
    if ($_FILES['fb_cert_import_file']['error'] !== UPLOAD_ERR_OK) {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_upload', 'code' => $_FILES['fb_cert_import_file']['error']], $redirect_url));
        exit;
    }
    $file_name = $_FILES['fb_cert_import_file']['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if (empty($file_ext) || strtolower($file_ext) !== 'json') {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_type'], $redirect_url));
        exit;
    }
    $file_path = $_FILES['fb_cert_import_file']['tmp_name'];
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once (ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }
    $file_content = $wp_filesystem->get_contents($file_path);
    if ($file_content === false) {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_read'], $redirect_url));
        exit;
    }
    $imported_settings = json_decode($file_content, true);
    if ($imported_settings === null && json_last_error() !== JSON_ERROR_NONE) {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_json', 'code' => json_last_error()], $redirect_url));
        exit;
    }
    if (!is_array($imported_settings)) {
        wp_safe_redirect(add_query_arg(['message' => 'import_error_format'], $redirect_url));
        exit;
    }

    $fields_to_import = isset($imported_settings['fields']) && is_array($imported_settings['fields']) ? $imported_settings['fields'] : [];
    $search_to_import = isset($imported_settings['search']) && is_array($imported_settings['search']) ? $imported_settings['search'] : [];
    $language_to_import = isset($imported_settings['language']) && is_array($imported_settings['language']) ? $imported_settings['language'] : [];

    $sanitized_fields = fb_cert_sanitize_fields_settings($fields_to_import);
    $sanitized_search = fb_cert_sanitize_search_settings($search_to_import);
    $sanitized_language = fb_cert_sanitize_language_settings($language_to_import);

    update_option('fanabyte_certificate_fields', $sanitized_fields);
    update_option('fanabyte_certificate_search_settings', $sanitized_search);
    update_option('fanabyte_certificate_language_settings', $sanitized_language);

    fb_cert_schedule_rewrite_flush();

    wp_safe_redirect(add_query_arg(['message' => 'import_success'], $redirect_url));
    exit;
}
add_action('admin_post_fb_cert_import_settings', 'fb_cert_handle_import_settings');

function fb_cert_show_admin_notices() {
    $screen = get_current_screen();
    if (!$screen || strpos($screen->id, 'fanabyte-certificate-settings') === false || !isset($_GET['message'])) {
        return;
    }

    $message_code = sanitize_key($_GET['message']);
    $error_code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';

    $message = '';
    $type = 'info';

    switch ($message_code) {
        case 'import_success':
            $message = esc_html__('Settings imported and saved successfully.', 'fanabyte-certificate') . ' ' .
                       esc_html__('If the URL slug was changed, please visit the Settings > Permalinks page and click Save Changes.', 'fanabyte-certificate');
            $type = 'success';
            break;

        case 'import_error_upload':
            $upload_errors = [
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
            $error_detail = $upload_errors[$error_code] ?? $upload_errors['unknown'];
            $message = esc_html__('Error uploading file:', 'fanabyte-certificate') . ' ' . esc_html($error_detail);
            $type = 'error';
            break;

        case 'import_error_type':
            $message = esc_html__('Invalid file format. Please upload a .json file only.', 'fanabyte-certificate');
            $type = 'error';
            break;

        case 'import_error_read':
            $message = esc_html__('Error reading file content.', 'fanabyte-certificate');
            $type = 'error';
            break;

        case 'import_error_json':
            $json_errors = [
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
            $error_detail = $json_errors[$error_code] ?? __('Unknown JSON error.', 'fanabyte-certificate');
            $message = esc_html__('Invalid JSON file:', 'fanabyte-certificate') . ' ' . esc_html($error_detail);
            $type = 'error';
            break;

        case 'import_error_format':
            $message = esc_html__('Invalid JSON data structure.', 'fanabyte-certificate');
            $type = 'error';
            break;
    }

    if ($message) {
        echo '<div id="setting-error-import-export" class="notice notice-' . esc_attr($type) . ' is-dismissible"><p>' . $message . '</p></div>';

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
add_action('admin_notices', 'fb_cert_show_admin_notices');

function fb_cert_check_slug_change_and_flush($old_value, $new_value, $option_name) {
    $old_slug = isset($old_value['cpt_slug']) ? $old_value['cpt_slug'] : 'certificate';
    $new_slug = isset($new_value['cpt_slug']) ? $new_value['cpt_slug'] : 'certificate';
    if ($old_slug !== $new_slug) {
        fb_cert_schedule_rewrite_flush();
    }
}
add_action('update_option_fanabyte_certificate_search_settings', 'fb_cert_check_slug_change_and_flush', 10, 3);

function fb_cert_schedule_rewrite_flush() {
    update_option('fb_cert_flush_rewrite_rules_flag', '1', false);
}

function fb_cert_flush_scheduled_rewrite_rules() {
    if (get_option('fb_cert_flush_rewrite_rules_flag') === '1') {
        delete_option('fb_cert_flush_rewrite_rules_flag');
        flush_rewrite_rules();
    }
}
add_action('shutdown', 'fb_cert_flush_scheduled_rewrite_rules');

function fb_cert_guide_page_callback() {
    $language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
    $is_rtl = ($language_settings['language'] === 'fa');
    ?>
    <div class="wrap fb-cert-guide-wrap" <?php echo $is_rtl ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        <h1><?php esc_html_e('FanaByte Certificate Inquiry Plugin Usage Guide', 'fanabyte-certificate'); ?></h1>
        <p><?php esc_html_e('Welcome to the FanaByte Certificate plugin guide! This guide explains the steps to use the various features of the plugin.', 'fanabyte-certificate'); ?></p>
        <div class="notice notice-info inline" style="margin-top: 15px;">
              <p><strong><?php esc_html_e('Initial Important Note:', 'fanabyte-certificate'); ?></strong> <?php
                  printf(
                      esc_html__('After activating the plugin or anytime you change the "Base URL Slug" in the settings, it is recommended to visit the %1$s page once and click the "Save Changes" button to ensure the certificate links work correctly.', 'fanabyte-certificate'),
                      '<strong><a href="' . esc_url(admin_url('options-permalink.php')) . '">' . esc_html__('Settings > Permalinks', 'fanabyte-certificate') . '</a></strong>'
                  );
              ?></p>
        </div>
        <hr style="margin: 25px 0;">
        <h2 style="color: #2271b1;"><?php esc_html_e('1. Initial Setup: Field Management', 'fanabyte-certificate'); ?></h2>
        <p><?php esc_html_e('The first step after installation and activation is to define the fields you want to have for each certificate.', 'fanabyte-certificate'); ?></p>
        <ol>
            <li><?php printf(esc_html__('From the admin menu, go to %s.', 'fanabyte-certificate'), '<strong>' . esc_html__('Certificates -> Settings', 'fanabyte-certificate') . '</strong>'); ?></li>
            <li><?php esc_html_e('Make sure you are on the "Field Management" tab.', 'fanabyte-certificate'); ?></li>
            <li><?php esc_html_e('In the "Add New Field" section: Select the field type (Text or Image) and click the "Add Field" button.', 'fanabyte-certificate'); ?></li>
            <li><?php esc_html_e('The new field will be added to the list above. Enter an appropriate "Field Label" (e.g., Student Name, National ID, Course Name).', 'fanabyte-certificate'); ?></li>
            <li><?php esc_html_e('The "Key Name" is generated automatically and is used to identify the field in the system.', 'fanabyte-certificate'); ?></li>
            <li><?php esc_html_e('Key field for search?: Check this option if you want users to be able to query using this field (maximum 2 text fields).', 'fanabyte-certificate'); ?></li>
            <li><?php printf(esc_html__('To change the display order of the fields, drag and drop the move icon (%s) next to each field.', 'fanabyte-certificate'), '<span class="dashicons dashicons-move"></span>'); ?></li>
            <li><?php printf(esc_html__('To delete a field, click the %s button.', 'fanabyte-certificate'), '<strong>' . esc_html__('Delete', 'fanabyte-certificate') . '</strong>'); ?></li>
            <li><?php printf(esc_html__('After finishing, click the %s button.', 'fanabyte-certificate'), '<strong>' . esc_html__('Save Settings', 'fanabyte-certificate') . '</strong>'); ?></li>
        </ol>
        <hr style="margin: 25px 0;">
        <h2 style="color: #2271b1;"><?php esc_html_e('2. Form, URL, and Button Settings', 'fanabyte-certificate'); ?></h2>
        <p><?php esc_html_e('In this section, you can customize the appearance and functionality of the search form, links, and related buttons.', 'fanabyte-certificate'); ?></p>
        <ol>
            <li><?php esc_html_e('Go to the "Form/URL/Buttons Settings" tab.', 'fanabyte-certificate'); ?></li>
            <li><strong><?php esc_html_e('Inquiry Form Customization:', 'fanabyte-certificate'); ?></strong>
                <ul>
                    <li><strong><?php esc_html_e('Text Above Form:', 'fanabyte-certificate'); ?></strong> <?php esc_html_e('Using the full WordPress editor, add help text, images, or any other content before the search form.', 'fanabyte-certificate'); ?></li>
                    <li><strong><?php esc_html_e('Search Button Text & Color:', 'fanabyte-certificate'); ?></strong> <?php esc_html_e('Set the text and background color of the main form button.', 'fanabyte-certificate'); ?></li>
                    <li><strong><?php esc_html_e('Placeholders:', 'fanabyte-certificate'); ?></strong> <?php esc_html_e('Specify the hint text inside the search fields (key fields).', 'fanabyte-certificate'); ?></li>
                </ul>
            </li>
             <li><strong><?php esc_html_e('Certificate URL Settings:', 'fanabyte-certificate'); ?></strong>
                <ul>
                     <li><strong><?php esc_html_e('Base URL Slug:', 'fanabyte-certificate'); ?></strong> <?php printf(esc_html__('Determine the fixed part of the URL (e.g., `certificate`). Only lowercase letters, numbers, and hyphens are allowed. After changing, be sure to go to %s and save.', 'fanabyte-certificate'), '<strong>' . esc_html__('Settings > Permalinks', 'fanabyte-certificate') . '</strong>'); ?></li>
                </ul>
            </li>
             <li><strong><?php esc_html_e('Download Button Customization:', 'fanabyte-certificate'); ?></strong>
                <ul>
                    <li><strong><?php esc_html_e('Download Button Text & Color:', 'fanabyte-certificate'); ?></strong> <?php esc_html_e('Set the text and background color of the "Download Certificate File" button that appears on the details page.', 'fanabyte-certificate'); ?></li>
                </ul>
            </li>
             <li><strong><?php esc_html_e('Labels and Titles Settings:', 'fanabyte-certificate'); ?></strong>
                <ul>
                    <li><?php esc_html_e('Customize various default labels like the "Details Section Heading" or metabox titles used in the admin area and frontend.', 'fanabyte-certificate'); ?></li>
                </ul>
            </li>
            <li><?php printf(esc_html__('Finally, click the %s button.', 'fanabyte-certificate'), '<strong>' . esc_html__('Save Settings', 'fanabyte-certificate') . '</strong>'); ?></li>
        </ol>
         <hr style="margin: 25px 0;">
         <h2 style="color: #2271b1;"><?php esc_html_e('3. Adding and Managing Certificates', 'fanabyte-certificate'); ?></h2>
         <p><?php esc_html_e('Follow these steps to register user certificates:', 'fanabyte-certificate'); ?></p>
         <ol>
            <li><?php printf(esc_html__('From the admin menu, go to %s.', 'fanabyte-certificate'), '<strong>' . esc_html__('Certificates -> Add New', 'fanabyte-certificate') . '</strong>'); ?></li>
            <li><?php esc_html_e('Enter the certificate title (e.g., Course X Certificate - Student Name).', 'fanabyte-certificate'); ?></li>
            <li><?php printf(esc_html__('In the "%s" section, enter the values for the custom fields you defined for this specific certificate.', 'fanabyte-certificate'), esc_html__('Certificate Information & Details', 'fanabyte-certificate')); ?></li>
            <li><?php printf(esc_html__('In the sidebar, in the "%s" metabox, upload the final certificate PDF or image file (optional).', 'fanabyte-certificate'), esc_html__('Main Certificate File (PDF/Image)', 'fanabyte-certificate')); ?></li>
            <li><?php esc_html_e('You can use the main editor for additional descriptions or to display specific content on the certificate page (optional).', 'fanabyte-certificate'); ?></li>
            <li><?php printf(esc_html__('If needed, edit the %s (Slug) in the corresponding metabox.', 'fanabyte-certificate'), '<strong>' . esc_html__('Permalink', 'fanabyte-certificate') . '</strong>'); ?></li>
            <li><?php printf(esc_html__('Click the %s button.', 'fanabyte-certificate'), '<strong>' . esc_html__('Publish', 'fanabyte-certificate') . '</strong>'); ?></li>
            <li><?php printf(esc_html__('To manage all certificates, go to the %s menu.', 'fanabyte-certificate'), '<strong>' . esc_html__('Certificates -> All Certificates', 'fanabyte-certificate') . '</strong>'); ?></li>
         </ol>
         <hr style="margin: 25px 0;">
         <h2 style="color: #2271b1;"><?php esc_html_e('4. Displaying the Inquiry Form on Your Site', 'fanabyte-certificate'); ?></h2>
         <p><?php esc_html_e('To enable the inquiry feature for users:', 'fanabyte-certificate'); ?></p>
         <ol>
            <li><?php esc_html_e('Go to edit an existing page or post, or create a new page (e.g., titled "Certificate Inquiry").', 'fanabyte-certificate'); ?></li>
            <li><?php esc_html_e('In the content editor, place the following shortcode where you want the form to appear:', 'fanabyte-certificate'); ?>
                <p>
                    <code id="shortcode-to-copy" class="fb-shortcode-to-copy" data-clipboard-text="[fanabyte_certificate_lookup]">[fanabyte_certificate_lookup]</code>
                    <span class="copy-shortcode-button" style="margin-left: 10px; cursor: pointer; color: #2271b1; text-decoration: underline;" title="<?php esc_attr_e('Click to copy', 'fanabyte-certificate'); ?>">
                        <?php esc_html_e('Copy Code', 'fanabyte-certificate'); ?>
                    </span>
                    <span class="copy-feedback" style="margin-left: 10px; color: green; font-weight: bold; display: none;">
                        <?php esc_html_e('Copied!', 'fanabyte-certificate'); ?>
                    </span>
                </p>
            </li>
            <li><?php esc_html_e('Publish or update the page/post.', 'fanabyte-certificate'); ?></li>
         </ol>
         <p><?php esc_html_e('Now, users visiting this page can search for their certificate by entering the key field information.', 'fanabyte-certificate'); ?></p>
         <hr style="margin: 25px 0;">
          <h2 style="color: #2271b1;"><?php esc_html_e('5. Direct Certificate View and QR Code', 'fanabyte-certificate'); ?></h2>
          <p><?php esc_html_e('Each published certificate has a direct link and a QR code:', 'fanabyte-certificate'); ?></p>
          <ul>
               <li><strong><?php esc_html_e('Direct Link:', 'fanabyte-certificate'); ?></strong> <?php esc_html_e('The unique URL for each certificate, which you can get from the edit screen or the certificate list. Its structure is based on the "Base URL Slug" set in step 2 and the certificate\'s own slug.', 'fanabyte-certificate'); ?></li>
               <li><strong><?php esc_html_e('QR Code:', 'fanabyte-certificate'); ?></strong> <?php esc_html_e('An image code containing the direct link to the certificate. This code is displayed in the corresponding column in the "All Certificates" list and also on the certificate details page on the site. Suitable for printing or quick sharing.', 'fanabyte-certificate'); ?></li>
          </ul>
          <hr style="margin: 25px 0;">
          <h2 style="color: #2271b1;"><?php esc_html_e('6. Importing and Exporting Settings', 'fanabyte-certificate'); ?></h2>
          <p><?php esc_html_e('To back up or transfer plugin settings:', 'fanabyte-certificate'); ?></p>
          <ol>
               <li><?php printf(esc_html__('Go to %1$s and select the %2$s tab.', 'fanabyte-certificate'), '<strong>' . esc_html__('Certificates -> Settings', 'fanabyte-certificate') . '</strong>', '<strong>' . esc_html__('Import/Export', 'fanabyte-certificate') . '</strong>'); ?></li>
               <li><strong><?php esc_html_e('Exporting:', 'fanabyte-certificate'); ?></strong> <?php printf(esc_html__('Click the "%s" button to download a file containing all settings.', 'fanabyte-certificate'), esc_html__('Download Export File (JSON)', 'fanabyte-certificate')); ?></li>
               <li><strong><?php esc_html_e('Importing:', 'fanabyte-certificate'); ?></strong> <?php printf(esc_html__('Select the JSON settings file and click the "%s" button. Current settings will be overwritten.', 'fanabyte-certificate'), esc_html__('Upload and Import Settings', 'fanabyte-certificate')); ?></li>
               <li><?php printf(esc_html__('Note: After importing, you might need to visit the %s page and click Save Changes once.', 'fanabyte-certificate'), '<strong>' . esc_html__('Settings > Permalinks', 'fanabyte-certificate') . '</strong>'); ?></li>
          </ol>
           <hr style="margin: 25px 0;">
          <h2 style="color: #2271b1;"><?php esc_html_e('7. Troubleshooting', 'fanabyte-certificate'); ?></h2>
          <ul>
               <li><strong><?php esc_html_e('404 Error (Page Not Found):', 'fanabyte-certificate'); ?></strong> <?php printf(esc_html__('Usually resolved by visiting %s and clicking "Save Changes".', 'fanabyte-certificate'), '<strong>' . esc_html__('Settings > Permalinks', 'fanabyte-certificate') . '</strong>'); ?></li>
               <li><strong><?php esc_html_e('Incorrect Styles Display:', 'fanabyte-certificate'); ?></strong> <?php esc_html_e('Clear your browser and site cache.', 'fanabyte-certificate'); ?></li>
               <li><strong><?php esc_html_e('Other Issues:', 'fanabyte-certificate'); ?></strong> <?php printf(esc_html__('For further assistance or to report an issue, please refer to the "%s" page in the plugin menu.', 'fanabyte-certificate'), esc_html__('About Us', 'fanabyte-certificate')); ?></li>
          </ul>
    </div><?php
}

/**
 * New: Callback for language settings section.
 */
function fb_cert_language_section_callback() {
    echo '<p>' . esc_html__('Select the language and direction for the plugin interface.', 'fanabyte-certificate') . '</p>';
}

/**
 * New: Callback for language selection field.
 */
function fb_cert_language_selection_callback() {
    $language_settings = get_option('fanabyte_certificate_language_settings', ['language' => 'fa']);
    $selected_language = isset($language_settings['language']) ? $language_settings['language'] : 'fa';
    ?>
    <fieldset>
        <label>
            <input type="radio" name="fanabyte_certificate_language_settings[language]" value="fa" <?php checked('fa', $selected_language); ?> />
            <?php esc_html_e('Persian (RTL)', 'fanabyte-certificate'); ?>
        </label><br>
        <label>
            <input type="radio" name="fanabyte_certificate_language_settings[language]" value="en" <?php checked('en', $selected_language); ?> />
            <?php esc_html_e('English (LTR)', 'fanabyte-certificate'); ?>
        </label>
    </fieldset>
    <?php
}

/**
 * New: Sanitize callback for language settings.
 */
function fb_cert_sanitize_language_settings($input) {
    $new_input = [];
    if (isset($input['language']) && in_array($input['language'], ['fa', 'en'])) {
        $new_input['language'] = sanitize_key($input['language']);
    } else {
        $new_input['language'] = 'fa';
    }
    return $new_input;
}
?>