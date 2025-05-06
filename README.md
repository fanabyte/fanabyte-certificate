# FanaByte Certificate

**FanaByte Certificate Inquiry Plugin | A comprehensive solution for managing and querying various certificates, diplomas, warranty cards, and similar documents online with advanced features.**

Developed by [FanaByte Academy](https://fanabyte.com).

[![License: GPL v2 or later](https://img.shields.io/badge/License-GPL%20v2%20or%20later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![PHP Version Require](https://img.shields.io/badge/php-%3E%3D%207.4-blue.svg)](https://www.php.net/)
[![WordPress Requires At Least](https://img.shields.io/badge/WordPress-%3E%3D%205.0-blue.svg)](https://wordpress.org/download/)
[![WordPress Tested Up To](https://img.shields.io/badge/WordPress%20Tested%20Up%20To-6.5-blue.svg)](https://wordpress.org/download/releases/)

---

## Description (English)

FanaByte Certificate is a powerful and flexible system for WordPress websites that allows you to manage and provide online inquiry services for various verifiable documents such as educational certificates, diplomas, warranty cards, identification cards, order statuses, and any other type of document requiring online validation.

Users can quickly access the information of their desired certificate or document and verify its validity by using a simple search form on your website and entering key information (like National ID, serial number, certificate code, etc.).

## Key Features

* **Easy Certificate Management:** Easily add, edit, and manage certificates through a dedicated Custom Post Type (CPT).
* **Unlimited Custom Fields:** Define custom fields (text and image) for each certificate type.
* **Customizable Inquiry Form:**
    * Select key fields for searching (max 2 text fields).
    * Customize intro text, search button text/color, and placeholders.
* **Certificate Details Display:**
    * Dedicated page with unique URL.
    * Optional personal photo/logo display.
    * Shows all custom fields.
    * Optional main file upload (PDF/Image) with customizable download button.
    * Optional custom footer text.
* **Automatic QR Code:** Generates and displays QR codes linking to the certificate page.
* **Form Display Shortcode:** Embed the form using `[fanabyte_certificate_lookup]`.
* **URL Settings:** Set the base slug for certificate permalinks.
* **Settings Import/Export:** Backup or transfer settings via JSON.
* **Bilingual Support:** Fully translatable (Fa/En included) with RTL support.
* **Template Overridable:** Themes can override the single certificate template.
* **Familiar UI:** Admin interface aligned with WordPress standards.

---

## Installation

1.  **Via WordPress Admin:**
    * Go to `Plugins > Add New`.
    * Search for "FanaByte Certificate".
    * Install and activate.
2.  **Manual Upload:**
    * Download the latest release `.zip` file from the [GitHub Releases](link-to-your-releases-page) page.
    * Go to `Plugins > Add New > Upload Plugin`.
    * Choose the zip file and click "Install Now", then activate.
3.  **Initial Configuration:**
    * Navigate to the new "Certificate Inquiry" -> "Settings" menu.
    * Define your fields under "Field Management" and mark key fields.
    * **Important:** Go to `Settings > Permalinks` and click "Save Changes".
    * Start adding certificates via "Certificate Inquiry" -> "Add New".
    * Place the `[fanabyte_certificate_lookup]` shortcode on a page.

---

## Usage

1.  **Define Fields:** Set up the necessary fields for your certificates in the settings (`Certificate Inquiry > Settings > Field Management`).
2.  **Add Certificates:** Populate the certificates with data using the "Add New" menu (`Certificate Inquiry > Add New`). Upload personal photos or main files as needed.
3.  **Display Form:** Use the `[fanabyte_certificate_lookup]` shortcode on the page where you want the inquiry form to appear. Customize the form's appearance via the settings (`Certificate Inquiry > Settings > Form/URL/Buttons Settings`).

---

## Frequently Asked Questions (FAQ)

* **Can I use more than two fields for searching?**
    > Currently, no. The plugin supports up to two text fields as search keys for simplicity.

* **Can I change the appearance?**
    > Yes, button colors/text are configurable in settings. For more extensive visual changes, use custom CSS in your theme or override the `templates/single-fb_certificate.php` template file in your theme directory.

* **How does the QR code work?**
    > It links directly to the certificate's page. The QR generation itself currently uses a placeholder online API in `includes/admin-columns.php` (function `fb_cert_generate_qr_code_html`). **It is highly recommended to replace this with a server-side PHP library like `endroid/qr-code` for production use.**

* **Can I transfer settings?**
    > Yes, use the Import/Export feature in `Certificate Inquiry > Settings > Import/Export`.

---

## Changelog

### 1.2.0 (Your Current Version)
* Added full internationalization support (i18n) for Persian and English.
* Added comprehensive Persian and English comments to all code files.
* Improved code structure and added necessary documentation for release.

### 1.0.0
* Initial release.

*(Add more versions as you release them)*

---

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change. Please ensure your code adheres to the WordPress Coding Standards.

---

## License

[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Support

For bug reports, feature requests, or other issues, please use the [GitHub Issues](link-to-your-issues-page) section of this repository.

<br/>
<hr/>
<br/>

<div dir="rtl" align="right">

# افزونه استعلام مدرک فنابایت

**افزونه استعلام مدرک فنابایت | راهکاری جامع برای مدیریت و استعلام آنلاین انواع مدارک، گواهینامه‌ها، کارت‌های گارانتی و موارد مشابه با قابلیت‌های پیشرفته.**

توسعه داده شده توسط [آکادمی فنابایت](https://fanabyte.com).

---

## توضیحات (فارسی)

**افزونه استعلام مدرک فنابایت** یک سیستم قدرتمند و انعطاف‌پذیر برای وب‌سایت‌های وردپرسی است که به شما امکان می‌دهد انواع اسناد قابل استعلام مانند مدارک آموزشی، گواهینامه‌ها، کارت‌های گارانتی، کارت‌های شناسایی، وضعیت سفارشات و هر نوع سند دیگری که نیاز به تأیید اعتبار آنلاین دارد را مدیریت و ارائه کنید.

کاربران می‌توانند با استفاده از یک فرم جستجوی ساده در سایت شما و وارد کردن اطلاعات کلیدی (مانند کد ملی، شماره سریال، کد مدرک و...)، به سرعت به اطلاعات مدرک یا سند مورد نظر خود دسترسی پیدا کنند و از اعتبار آن مطمئن شوند.

## ویژگی‌های کلیدی

* **مدیریت آسان مدارک:** افزودن، ویرایش و مدیریت آسان مدارک از طریق یک پست تایپ سفارشی (CPT).
* **فیلدهای سفارشی نامحدود:** تعریف فیلدهای دلخواه (متنی و تصویری) برای هر نوع مدرک.
* **فرم استعلام قابل تنظیم:**
    * انتخاب فیلدهای کلیدی برای جستجو (حداکثر ۲ فیلد متنی).
    * شخصی‌سازی متن بالای فرم، متن دکمه جستجو و رنگ آن و Placeholder ها.
* **نمایش جزئیات مدرک:**
    * صفحه اختصاصی با URL منحصر به فرد.
    * قابلیت نمایش عکس پرسنلی یا لوگو.
    * نمایش تمام فیلدهای سفارشی.
    * امکان آپلود و نمایش فایل اصلی (PDF/تصویر) با دکمه دانلود قابل تنظیم.
    * قابلیت افزودن متن دلخواه در پایین صفحه.
* **کد QR خودکار:** تولید و نمایش کد QR برای هر مدرک.
* **شورت‌کد نمایش فرم:** قرار دادن فرم با شورت‌کد `[fanabyte_certificate_lookup]`.
* **تنظیمات URL:** قابلیت تنظیم اسلاگ پایه URL.
* **واردات و صادرات تنظیمات:** پشتیبان‌گیری یا انتقال تنظیمات با JSON.
* **پشتیبانی از دو زبان:** کاملاً قابل ترجمه (فارسی/انگلیسی) با پشتیبانی RTL.
* **قالب قابل بازنویسی:** پوسته می‌تواند قالب نمایش تکی را بازنویسی کند.
* **رابط کاربری آشنا:** هماهنگ با پیشخوان وردپرس.

---

## نصب

۱.  **از طریق پیشخوان وردپرس:**
    * به `افزونه‌ها > افزودن` بروید.
    * "FanaByte Certificate" را جستجو کنید.
    * نصب و فعال کنید.
۲.  **بارگذاری دستی:**
    * آخرین نسخه فایل `.zip` را از صفحه [GitHub Releases](link-to-your-releases-page) دانلود کنید.
    * به `افزونه‌ها > افزودن > بارگذاری افزونه` بروید.
    * فایل zip را انتخاب و نصب کنید، سپس فعال نمایید.
۳.  **پیکربندی اولیه:**
    * به منوی جدید `استعلام مدرک > تنظیمات` بروید.
    * فیلدهای خود را در تب `مدیریت فیلدها` تعریف کرده و فیلدهای کلیدی را مشخص کنید.
    * **مهم:** به `تنظیمات > پیوندهای یکتا` رفته و روی `ذخیره تغییرات` کلیک کنید.
    * مدارک را از منوی `استعلام مدرک > افزودن جدید` اضافه کنید.
    * شورت‌کد `[fanabyte_certificate_lookup]` را در یک برگه قرار دهید.

---

## نحوه استفاده

۱.  **تعریف فیلدها:** فیلدهای لازم را در تنظیمات افزونه تعریف کنید (`استعلام مدرک > تنظیمات > مدیریت فیلدها`).
۲.  **افزودن مدرک:** مدارک را با داده‌های مربوطه از طریق منوی `افزودن جدید` وارد کنید.
۳.  **نمایش فرم:** از شورت‌کد `[fanabyte_certificate_lookup]` در برگه مورد نظر برای نمایش فرم استفاده کنید. ظاهر فرم را از تنظیمات تغییر دهید.

---

## پرسش‌های متداول (FAQ)

* **آیا می‌توانم بیش از دو فیلد برای جستجو استفاده کنم؟**
    > در حال حاضر خیر. افزونه حداکثر از دو فیلد متنی به عنوان کلید جستجو پشتیبانی می‌کند.

* **آیا می‌توانم ظاهر را تغییر دهم؟**
    > بله، رنگ/متن دکمه‌ها قابل تنظیم است. برای تغییرات بیشتر از CSS سفارشی استفاده کنید یا فایل قالب `templates/single-fb_certificate.php` را در پوسته خود بازنویسی کنید.

* **کد QR چگونه کار می‌کند؟**
    > به صفحه مدرک لینک می‌دهد. تولید کد QR در حال حاضر از یک API آنلاین موقت در `includes/admin-columns.php` (تابع `fb_cert_generate_qr_code_html`) استفاده می‌کند. **اکیداً توصیه می‌شود این بخش با یک کتابخانه PHP سمت سرور مانند `endroid/qr-code` جایگزین شود.**

* **آیا می‌توانم تنظیمات را منتقل کنم؟**
    > بله، از بخش واردات/صادرات در تنظیمات استفاده کنید.

---

## لیست تغییرات (Changelog)

### نسخه ۱.۲.۰ (نسخه فعلی شما)
* افزودن پشتیبانی کامل از دو زبان فارسی و انگلیسی (i18n).
* افزودن کامنت‌گذاری جامع فارسی و انگلیسی به تمام فایل‌های کد.
* بهبود ساختار کد و افزودن توضیحات لازم برای انتشار.

### نسخه ۱.۰.۰
* انتشار اولیه.

*(نسخه‌های بعدی را اینجا اضافه کنید)*

---

## مشارکت

Pull request ها پذیرفته می‌شوند. برای تغییرات بزرگ، لطفاً ابتدا یک Issue باز کنید تا در مورد آنچه می‌خواهید تغییر دهید بحث کنیم. لطفاً اطمینان حاصل کنید که کد شما با استانداردهای کدنویسی وردپرس مطابقت دارد.

---

## لایسنس

[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

---

## پشتیبانی

برای گزارش باگ، درخواست ویژگی جدید یا سایر مسائل، لطفاً از بخش [GitHub Issues](link-to-your-issues-page) این مخزن استفاده کنید.

</div>