<?php
/**
 * File for managing custom columns in the Certificates list table (CPT: fb_certificate) in the WordPress admin.
 * فایل مدیریت ستون‌های سفارشی در لیست مدرک‌ها (CPT: fb_certificate) در پیشخوان وردپرس.
 *
 * Version: 1.2.0 - Added i18n support and comments. Includes QR code column.
 * نسخه: 1.2.0 - افزودن پشتیبانی از ترجمه و کامنت‌ها. شامل ستون کد QR.
 */

// ** Security Check: Prevent direct access to the file. **
// ** بررسی امنیتی: جلوگیری از دسترسی مستقیم به فایل. **
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly. / خروج در صورت دسترسی مستقیم.
}

/**
 * Add a custom 'QR Code' column to the Certificate management list table.
 * اضافه کردن ستون سفارشی 'کد QR' به لیست مدیریت مدرک‌ها.
 *
 * @param array $columns Existing columns array. / آرایه ستون‌های موجود.
 * @return array Columns array with the new column added. / آرایه ستون‌ها با اضافه شدن ستون جدید.
 */
function fb_cert_add_qr_column($columns) {
    // Create a new array to precisely control column order.
    // ایجاد یک آرایه جدید برای کنترل دقیق ترتیب ستون‌ها.
    $new_columns = [];
    foreach ($columns as $key => $title) {
        $new_columns[$key] = $title;
        // Insert the QR Code column right after the 'title' column.
        // اضافه کردن ستون QR Code درست بعد از ستون 'title' (عنوان).
        if ($key === 'title') {
            // Add the new column with a translatable title.
            // افزودن ستون جدید با عنوان قابل ترجمه.
            $new_columns['fb_qr_code'] = __('QR Code', 'fanabyte-certificate'); /*کد QR*/
        }
    }

     // Fallback: If the 'title' column wasn't found for some reason, add QR code at the end.
     // پشتیبان: اگر به هر دلیلی ستون title یافت نشد، ستون QR را در انتها اضافه کن.
     if(!isset($new_columns['fb_qr_code'])){
         $new_columns['fb_qr_code'] = __('QR Code', 'fanabyte-certificate'); /*کد QR*/
     }

    // Return the modified columns array.
    // بازگرداندن آرایه ستون‌های اصلاح شده.
    return $new_columns;
}
// Hook the function to the filter for managing 'fb_certificate' post type columns.
// اتصال تابع به فیلتر مدیریت ستون‌های پست تایپ 'fb_certificate'.
add_filter('manage_fb_certificate_posts_columns', 'fb_cert_add_qr_column');

/**
 * Render and display the content for the custom 'QR Code' column.
 * رندر کردن و نمایش محتوای ستون سفارشی 'کد QR'.
 *
 * @param string $column_name The name of the current column being rendered. / نام ستون فعلی که باید محتوای آن نمایش داده شود.
 * @param int    $post_id     The ID of the current post (certificate) in the loop. / شناسه (ID) پست (مدرک) فعلی در حلقه.
 */
function fb_cert_render_qr_column($column_name, $post_id) {
    // Only run this logic for our custom 'fb_qr_code' column.
    // فقط برای ستون سفارشی 'fb_qr_code' اجرا شود.
    if ($column_name === 'fb_qr_code') {
        // Get the permalink (unique URL) of the certificate.
        // دریافت لینک یکتای مدرک.
        $permalink = get_permalink($post_id);

        // Check if the permalink exists and the QR generation helper function is available.
        // بررسی وجود لینک و تابع کمکی تولید کد QR.
        if ($permalink && function_exists('fb_cert_generate_qr_code_html')) {
             // Call the helper function to generate and display the QR code HTML.
             // فراخوانی تابع کمکی برای تولید و نمایش HTML کد QR.
             // Use a smaller size (e.g., 80px) for appropriate display in the admin list.
             // استفاده از اندازه کوچکتر (مثلا 80 پیکسل) برای نمایش مناسب در لیست ادمین.
             echo fb_cert_generate_qr_code_html($permalink, 80); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped within the helper function.

             /* --- Alternative/Additional Code: Display button and modal with JS (if preferred) --- */
             /* --- کد جایگزین یا اضافی: نمایش دکمه و مودال با JS (اگر ترجیح می‌دهید) ---
             // Display a button to open the modal.
             // نمایش دکمه برای باز کردن مودال.
             echo '<a href="#" class="button button-small fb-show-qr-button" data-url="' . esc_url($permalink) . '" title="'.esc_attr__('Show larger QR Code', 'fanabyte-certificate').'">' . esc_html__('Show', 'fanabyte-certificate') . '</a>';

             // HTML structure for the modal (hidden by default).
             // ساختار HTML مودال (مخفی به صورت پیش‌فرض).
             echo '<div id="fb-qr-modal-' . $post_id . '" class="fb-qr-modal" style="display:none;">';
             echo '  <p>' . esc_html__('Scan the QR Code:', 'fanabyte-certificate') . '</p>';
             // Generate a larger QR Code in the modal using the helper function.
             // تولید کد QR بزرگتر در مودال با تابع کمکی.
             echo '  <div class="fb-qr-code-image">' . fb_cert_generate_qr_code_html($permalink, 200) . '</div>'; // Larger size / اندازه بزرگتر
             echo '  <p><a href="'.esc_url($permalink).'" target="_blank" style="font-size:0.9em; word-break:break-all;">'.esc_url($permalink).'</a></p>';
             echo '  <button type="button" class="button fb-close-qr-modal">'.esc_html__('Close', 'fanabyte-certificate').'</button>';
             echo '</div>';
             // Note: The JavaScript for this button and modal should be in admin-script.js and enqueued.
             // نکته: کد JavaScript مربوط به این دکمه و مودال باید در فایل admin-script.js باشد و لود شود.
             */

        } else {
            // If the permalink doesn't exist or the helper function is missing.
            // اگر لینک وجود نداشت یا تابع کمکی تعریف نشده بود.
            echo '–'; // Display a dash / نمایش خط تیره
        }
    }
}
// Hook the function to the action for displaying custom column content.
// اتصال تابع به اکشن نمایش محتوای ستون سفارشی.
add_action('manage_fb_certificate_posts_custom_column', 'fb_cert_render_qr_column', 10, 2);


/**
 * =====================================================================
 * Helper Function to Generate QR Code HTML
 * تابع کمکی برای تولید HTML کد QR
 * =====================================================================
 * ! Very Important Note: This function currently uses an online service
 * ! for initial display ONLY. You MUST replace its code with a proper
 * ! PHP QR Code library (like endroid/qr-code installed via Composer)
 * ! for the plugin to work correctly and independently.
 * ! نکته بسیار مهم: این تابع در حال حاضر از یک سرویس آنلاین فقط برای
 * ! نمایش اولیه استفاده می‌کند. شما باید **حتما** کد آن را با استفاده از
 * ! یک کتابخانه PHP QR Code معتبر (مانند endroid/qr-code که با Composer نصب می‌شود)
 * ! جایگزین کنید تا افزونه به درستی و بدون وابستگی به سرویس خارجی کار کند.
 *
 * @param string $url  The URL to be encoded into the QR code. / لینکی که باید به کد QR تبدیل شود.
 * @param int    $size The size of the QR code image in pixels (optional, default 150). / اندازه تصویر QR به پیکسل (اختیاری، پیش‌فرض 150).
 * @return string HTML string for the img tag displaying the QR Code, or an error/guidance message. / رشته HTML تگ img برای نمایش QR Code یا پیام خطا/راهنما.
 */
function fb_cert_generate_qr_code_html($url, $size = 150) {
    // Check if the input URL is empty.
    // بررسی خالی نبودن URL ورودی.
    if (empty($url)) {
        return ''; // Return nothing if URL is empty / اگر URL خالی است، چیزی برنگردان
    }

    // Ensure the size is a positive integer.
    // اطمینان از اینکه اندازه یک عدد صحیح مثبت است.
    $size = absint($size);
    if ($size <= 0) {
        $size = 150; // Revert to default size if the value was invalid / بازگشت به اندازه پیش‌فرض اگر مقدار نامعتبر بود
    }

    // ****** Start of section requiring implementation with a real QR library ******
    // ****** شروع بخش نیازمند پیاده‌سازی با کتابخانه واقعی QR ******

    // -------------[ Option 1: Using Online QR Server API ]-------------
    // ! Warning: Suitable for initial testing and development ONLY. Never use in a final product!
    // ! This method requires internet access and the service might be unavailable or have limitations.
    // ! هشدار: فقط برای تست و توسعه اولیه مناسب است. هرگز در محصول نهایی استفاده نشود!
    // ! این روش به اینترنت نیاز دارد و ممکن است سرویس در دسترس نباشد یا محدودیت داشته باشد.
    $qr_api_url = add_query_arg(
        [
            'size'   => $size . 'x' . $size, // Format: WIDTHxHEIGHT / فرمت: عرضxارتفاع
            'data'   => urlencode($url),    // IMPORTANT: URL-encode the data / مهم: URL را انکود کنید
            'ecc'    => 'L',                // Error Correction Level: Low (suitable for URLs) / سطح تصحیح خطا: پایین (مناسب برای URL)
            'margin' => 1                   // Margin around QR code (pixels) / حاشیه دور کد QR (پیکسل)
        ],
        'https://api.qrserver.com/v1/create-qr-code/' // API endpoint / نقطه پایانی API
    );
    // Return the image tag with appropriate attributes.
    // بازگرداندن تگ img با ویژگی‌های مناسب.
    return '<img src="' . esc_url($qr_api_url) . '" alt="' . esc_attr__('QR Code', 'fanabyte-certificate') . /*کد QR*/ '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" style="display: block; border: 1px solid #eee; padding: 3px; background: #fff;">';
    // -------------[ End Option 1 ]-------------


    /*
    // -------------[ Option 2: Example with endroid/qr-code (Recommended Method) ]-------------
    // Requires:
    // 1. Installing the library: `composer require endroid/qr-code` in the plugin directory terminal.
    // 2. Loading the vendor autoload in the main plugin file: `require_once FB_CERT_PATH . 'vendor/autoload.php';`
    // نیازمند:
    // 1. نصب کتابخانه: در ترمینال در پوشه افزونه `composer require endroid/qr-code`
    // 2. لود کردن autoload وندور در فایل اصلی افزونه: `require_once FB_CERT_PATH . 'vendor/autoload.php';`

    // (This code is commented out - uncomment and configure to use)
    // (این کد کامنت شده است - برای استفاده باید آن را از کامنت خارج کرده و تنظیم کنید)

    // --- Make sure to include the necessary use statements at the top of the file ---
    // --- اطمینان حاصل کنید که دستورات use لازم در بالای فایل قرار دارند ---
    // use Endroid\QrCode\Builder\Builder;
    // use Endroid\QrCode\Encoding\Encoding;
    // use Endroid\QrCode\ErrorCorrectionLevel;
    // use Endroid\QrCode\RoundBlockSizeMode;
    // use Endroid\QrCode\Writer\PngWriter;
    // use Endroid\QrCode\Writer\Result\ResultInterface; // Import if needed for type hinting / در صورت نیاز برای type hinting وارد کنید

    try {
         // Build the QR code using the endroid/qr-code library.
         // ساخت کد QR با استفاده از کتابخانه endroid/qr-code.
         $result = Builder::create()
             ->writer(new PngWriter()) // Use PNG writer / استفاده از نویسنده PNG
             ->writerOptions([])      // Writer options (optional) / گزینه‌های نویسنده (اختیاری)
             ->data($url)             // Data (the URL) / داده (URL)
             ->encoding(new Encoding('UTF-8')) // Encoding / انکودینگ
             ->errorCorrectionLevel(ErrorCorrectionLevel::Low) // Error correction level / سطح تصحیح خطا
             ->size($size)            // Image size / اندازه تصویر
             ->margin(10)             // Margin around the QR code / حاشیه دور QR
             ->roundBlockSizeMode(RoundBlockSizeMode::Margin) // Round block corners / گرد کردن گوشه بلوک‌ها
             // ->logoPath(FB_CERT_PATH . 'assets/images/logo.png') // Optional: Path to logo / اختیاری: مسیر لوگو
             // ->logoResizeToWidth(50)                             // Optional: Resize logo / اختیاری: تغییر اندازه لوگو
             ->validateResult(false)  // Disable result validation (usually not needed) / عدم اعتبارسنجی نتیجه (معمولا لازم نیست)
             ->build();

         // Return the img tag with base64 encoded data URI (avoids saving temporary files).
         // بازگرداندن تگ img با داده base64 انکود شده (برای عدم نیاز به ذخیره فایل موقت).
         return '<img src="' . $result->getDataUri() . '" alt="' . esc_attr__('QR Code', 'fanabyte-certificate') . '" width="' . esc_attr($size) . '" height="' . esc_attr($size) . '" style="display: block;">';

    } catch (Exception $e) {
         // Log the error for admin review.
         // لاگ کردن خطا برای بررسی‌های بعدی توسط ادمین.
         error_log('Fanabyte Certificate - QR Code Generation Error using endroid/qr-code: ' . $e->getMessage());
         // Optionally display a user-friendly error message (use with caution).
         // نمایش پیام خطای کاربرپسند به صورت اختیاری (با احتیاط استفاده شود).
        return '<p class="fb-cert-error" style="font-size:0.8em; color:red;">'.esc_html__('Error generating QR Code.', 'fanabyte-certificate').'</p>'; // خطا در تولید کد QR.
    }
    */
    // -------------[ End Option 2 ]-------------


    // -------------[ Option 3: If using a different library ]-------------
    // Place the code for your chosen library here to return an <img> tag or base64 data.
    // کد مربوط به کتابخانه انتخابی خود را در اینجا قرار دهید تا یک تگ <img> یا داده base64 برگرداند.
    // return generate_qr_with_other_library($url, $size);
    // -------------[ End Option 3 ]-------------


    /*
    // Fallback message if no QR library is implemented yet.
    // پیام پشتیبان اگر هنوز هیچ کتابخانه QR پیاده‌سازی نشده باشد.
    return '<p style="font-size:0.8em; color:#777; border:1px dashed #ccc; padding:5px;">'.esc_html__('QR Code generation library is not configured yet. Please implement the necessary code in the fb_cert_generate_qr_code_html() function in admin-columns.php.', 'fanabyte-certificate').'</p>';
    // کتابخانه تولید QR Code هنوز پیکربندی نشده است. لطفا کد لازم را در تابع fb_cert_generate_qr_code_html() در فایل admin-columns.php قرار دهید.
    */

    // ****** End of section requiring implementation ******
    // ****** پایان بخش نیازمند پیاده‌سازی ******
}

?>