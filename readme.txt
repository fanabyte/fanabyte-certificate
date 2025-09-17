=== FanaByte - Certificate ===
Contributors: fanabyte, fanabyte_academy
Tags: certificate, inquiry, lookup, verification, diploma, warranty, qr code, custom fields, استعلام, مدرک, گواهینامه, گارانتی, کد کیو آر, فیلد سفارشی
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://fanabyte.com/donate/

FanaByte Certificate Inquiry Plugin |
A comprehensive solution for managing and querying various certificates, diplomas, warranty cards, and similar documents online with advanced features.
== Description ==

**FanaByte Certificate** is a powerful and flexible system for WordPress websites that allows you to manage and provide online inquiry services for various verifiable documents such as educational certificates, diplomas, warranty cards, identification cards, order statuses, and any other type of document requiring online validation.
Users can quickly access the information of their desired certificate or document and verify its validity by using a simple search form on your website and entering key information (like National ID, serial number, certificate code, etc.).
This plugin has been developed by **FanaByte Academy** to provide an easy yet professional solution for educational institutions, companies, organizations, and stores that need to offer an online inquiry system.
**Key Features:**

* **Easy Certificate Management:** Easily add, edit, and manage certificates through a dedicated Custom Post Type (CPT) in the WordPress admin area.
* **Unlimited Custom Fields:** Define custom fields (text and image) for each certificate type according to your needs (e.g., Student Name, Course Name, Issue Date, Warranty Code, Product Image, etc.).
* **Customizable Inquiry Form:**
    * Select key fields for searching (maximum 2 text fields).
* Customize the intro text above the form, search button text, and its color.
* Set placeholder text for search fields to guide users.
* **Certificate Details Display:**
    * Dedicated page for displaying the full details of each certificate with a unique URL.
* Option to display a personal photo or relevant logo next to the certificate title.
* Displays all defined custom fields.
    * Ability to upload and display the main certificate file (PDF or Image) with a customizable download button (text and color).
* Option to add custom text at the bottom of the certificate page.
* **Automatic QR Code:** Automatically generates and displays a QR code for each certificate linking to its direct page (visible in the admin list and on the single certificate page).
* **Form Display Shortcode:** Easily embed the inquiry form on any page or post using the `[fanabyte_certificate_lookup]` shortcode.
* **URL Settings:** Ability to set the base slug for certificate permalinks.
* **Settings Import/Export:** Backup or transfer field and form settings via JSON file.
* **Bilingual Support:** Fully translatable (Persian and English included by default) with Right-to-Left (RTL) display support.
* **Template Overridable:** Allows themes to override the single certificate display template.
* **Familiar UI:** Settings interface designed to align with the WordPress admin dashboard for a better user experience.
**How to Use:**

1.  **Define Fields:** Go to the "Certificate Inquiry" -> "Settings" menu and, in the "Field Management" tab, define the fields you need (e.g., Name, National ID, Serial Number).
Mark at least one and up to two text fields as "Key field for search?".
2.  **Configure Form (Optional):** In the "Form/URL/Buttons Settings" tab, customize the search form appearance, URL slug, and download button.
3.  **Add Certificates:** From the "Certificate Inquiry" -> "Add New" menu, enter your certificates along with the relevant custom field data, main file, personal photo, etc., and publish them.
4.  **Display Form on Site:** Create a new page (e.g., "Certificate Lookup") and insert the `[fanabyte_certificate_lookup]` shortcode into it.
Now users can visit this page and look up their certificate by entering the key information.
== Installation ==

1.  **Method 1: Via WordPress Admin**
    * Go to Plugins > Add New.
* Search for "FanaByte Certificate".
    * Install and then activate the plugin.
2.  **Method 2: Manual Upload**
    * Download the plugin zip file.
* Go to Plugins > Add New and click the "Upload Plugin" button.
* Choose the downloaded zip file and click "Install Now".
    * After installation, activate the plugin.
3.  **Initial Configuration:**
    * Navigate to the new "Certificate Inquiry" -> "Settings" menu in the admin dashboard.
* In the "Field Management" tab, define your required fields and select at least one as "Key field for search?".
* (Important) Go to Settings > Permalinks and click "Save Changes" once to ensure the new URL structure works.
* Start adding certificates via the "Certificate Inquiry" -> "Add New" menu.
    * Place the `[fanabyte_certificate_lookup]` shortcode on a page.
== Frequently Asked Questions ==

= Can I use more than two fields for searching? =
Currently, no.
For simplicity and to prevent overly complex queries, the plugin allows you to select a maximum of two text fields as search keys.
= Can I change the appearance of the search form and certificate page?
=
Yes, you can change button colors and text from the settings.
For more significant visual changes, you can use custom CSS in your theme or override the single certificate template file (`single-fb_certificate.php`) in your theme.
= How does the QR code work? =
The plugin automatically generates a QR code for each published certificate containing the direct link to that certificate's page.
Users can scan this code to quickly access the details page.
(Technical Note: May require a PHP QR Code library installed on the server or rely on third-party services depending on the implementation in `admin-columns.php`).
= Can I transfer the settings to another site? =
Yes, from the Settings -> Import/Export section, you can export all field and form settings as a JSON file and import it on another site.
== Screenshots ==

1.  Custom Field Management page
2.  Form and URL Settings page
3.  Add/Edit Certificate screen with custom metaboxes
4.  Frontend inquiry form display
5.  Frontend certificate details page showing personal photo, fields, download button, and QR code
6.  Admin certificate list with QR code column

== Changelog ==

= 1.3.0 =
* Added a new 'Plugin Language' setting to switch between Persian (RTL) and English (LTR).
* Refactored CSS to support dynamic RTL/LTR switching.
* Updated plugin version to 1.3.0.

= 1.2.0 =
* Added full internationalization support for Persian and English (i18n).
* Added comprehensive Persian and English comments to all code files.
* Improved code structure and added necessary documentation for release.
* (Note: Previous versions might only contain internal changes)

= 1.0.0 =
* Initial release of the plugin.
== Upgrade Notice ==

= 1.3.0 =
This version adds a new language setting and improves RTL/LTR support. Updating is recommended.