jQuery(document).ready(function($) {

    // --- Initialize WordPress Color Picker ---
    // --- راه‌اندازی انتخابگر رنگ وردپرس ---
    // Finds elements with the class 'fb-cert-color-picker' and initializes the WP Color Picker on them.
    // المان‌هایی با کلاس 'fb-cert-color-picker' را پیدا کرده و انتخابگر رنگ وردپرس را روی آن‌ها راه‌اندازی می‌کند.
    if (typeof $.fn.wpColorPicker === 'function') {
        $('.fb-cert-color-picker').wpColorPicker();
    }

    // ---- Sortable Fields Logic (Settings Page) ----
    // ---- منطق فیلدهای قابل مرتب‌سازی (صفحه تنظیمات) ----
    // Check if the sortable container exists on the current page.
    // بررسی وجود کانتینر قابل مرتب‌سازی در صفحه فعلی.
    if ($('#fb-cert-fields-container.fb-cert-sortable').length > 0) {

        /**
         * Updates the hidden 'order' input field for each field row based on its current position.
         * فیلد ورودی مخفی 'order' را برای هر ردیف فیلد بر اساس موقعیت فعلی آن به‌روز می‌کند.
         */
        function updateFieldOrder() {
            $('#fb-cert-fields-container .fb-cert-field-row').each(function(index) {
                // Update the 'value' attribute of the hidden input with class 'fb-cert-field-order'.
                // به‌روزرسانی ویژگی 'value' ورودی مخفی با کلاس 'fb-cert-field-order'.
                $(this).find('.fb-cert-field-order').val(index);
            });
        }

        // Initialize jQuery UI Sortable on the container.
        // راه‌اندازی jQuery UI Sortable روی کانتینر.
        $('#fb-cert-fields-container').sortable({
            handle: '.fb-cert-drag-handle', // Specify the element to use as the drag handle / تعیین المانی که به عنوان دستگیره کشیدن استفاده شود
            axis: 'y', // Allow vertical sorting only / اجازه مرتب‌سازی فقط عمودی
            opacity: 0.7, // Opacity of the helper while sorting / شفافیت المان کمکی هنگام مرتب‌سازی
            placeholder: 'fb-cert-field-placeholder', // CSS class for the placeholder element / کلاس CSS برای المان جایگزین
            start: function(event, ui) {
                // Set the placeholder height to match the item being dragged.
                // تنظیم ارتفاع جایگزین برای تطابق با آیتم در حال کشیدن.
                ui.placeholder.height(ui.item.outerHeight());
                 ui.placeholder.css('margin-bottom', ui.item.css('margin-bottom')); // Match margin
            },
            update: function(event, ui) {
                // Update the order values after a field is dropped.
                // به‌روزرسانی مقادیر ترتیب پس از رها کردن یک فیلد.
                updateFieldOrder();
            }
        });

        // Initial update of order on page load.
        // به‌روزرسانی اولیه ترتیب هنگام بارگذاری صفحه.
        updateFieldOrder();

        // --- Add New Field Button Handler ---
        // --- مدیریت کننده دکمه افزودن فیلد جدید ---
        $('#fb-add-field-button').on('click', function() {
            var fieldType = $('#new_field_type').val(); // Get selected field type / دریافت نوع فیلد انتخاب شده
            var fieldTypeText = $('#new_field_type option:selected').text(); // Get selected field type text / دریافت متن نوع فیلد انتخاب شده
            var newKey = 'field_' + Date.now(); // Generate a simple unique key using timestamp / تولید کلید یونیک ساده با استفاده از timestamp

            // --- Translatable Strings (Should be passed via wp_localize_script) ---
            // --- رشته‌های قابل ترجمه (باید از طریق wp_localize_script ارسال شوند) ---
            // Example structure for localized data:
            // مثال ساختار برای داده‌های محلی‌سازی شده:
            // fbCertAdminData = {
            //     labels: {
            //         fieldType: 'نوع فیلد:', // Field Type:
            //         keyName: 'نام کلیدی (Key):', // Key Name:
            //         fieldLabel: 'عنوان فیلد:', // Field Label:
            //         isKey: 'فیلد کلیدی برای جستجو؟', // Key field for search?
            //         moveTitle: 'جابجا کنید', // Drag to reorder
            //         deleteButton: 'حذف', // Delete
            //         deleteConfirm: 'آیا از حذف این فیلد مطمئن هستید؟' // Are you sure you want to delete this field?
            //     }
            // };
            var localized = typeof fbCertAdminData !== 'undefined' ? fbCertAdminData.labels : {
                fieldType: 'Field Type:',
                keyName: 'Key Name:',
                fieldLabel: 'Field Label:',
                isKey: 'Key field for search?',
                moveTitle: 'Drag to reorder',
                deleteButton: 'Delete',
                deleteConfirm: 'Are you sure you want to delete this field?'
            }; // Fallback to English if localized data is not available / بازگشت به انگلیسی اگر داده محلی‌سازی شده در دسترس نباشد

            // HTML template for the new field row.
            // قالب HTML برای ردیف فیلد جدید.
            // Note: The structure should match the one generated by PHP in the callback `fb_cert_fields_list_callback`.
            // نکته: ساختار باید با ساختار تولید شده توسط PHP در callback `fb_cert_fields_list_callback` مطابقت داشته باشد.
             var newFieldHtml = `
                <div class="fb-cert-field-row" data-key="${newKey}">
                    <span class="dashicons dashicons-move fb-cert-drag-handle" title="${localized.moveTitle}"></span>
                    <input type="hidden" class="fb-cert-field-order" name="fanabyte_certificate_fields[${newKey}][order]" value="">
                    <div class="fb-cert-field-details">
                        <p>
                            <label>${localized.fieldType} <strong>${fieldTypeText}</strong></label>
                            &nbsp; | &nbsp;
                            <label>${localized.keyName} <code>${newKey}</code></label>
                            <input type="hidden" name="fanabyte_certificate_fields[${newKey}][type]" value="${fieldType}">
                        </p>
                        <p>
                            <label for="fb_field_label_${newKey}">${localized.fieldLabel}</label>
                            <input type="text" id="fb_field_label_${newKey}" name="fanabyte_certificate_fields[${newKey}][label]" value="" required>
                        </p>
                        ${fieldType === 'text' ? `
                        <p>
                            <label>
                                <input type="checkbox" name="fanabyte_certificate_fields[${newKey}][is_key]" value="1">
                                ${localized.isKey}
                            </label>
                        </p>
                        ` : `<input type="hidden" name="fanabyte_certificate_fields[${newKey}][is_key]" value="0">`}
                    </div>
                    <div class="fb-cert-field-actions">
                         <button type="button" class="button button-link-delete fb-remove-field" title="${localized.deleteButton}">
                              <span class="dashicons dashicons-trash"></span>
                              <span class="screen-reader-text">${localized.deleteButton}</span>
                         </button>
                    </div>
                </div>
            `;
            // Append the new field row to the container.
            // افزودن ردیف فیلد جدید به کانتینر.
            $('#fb-cert-fields-container').append(newFieldHtml);
            // Update the order values for all fields.
            // به‌روزرسانی مقادیر ترتیب برای همه فیلدها.
            updateFieldOrder();
        });

        // --- Remove Field Button Handler (using event delegation) ---
        // --- مدیریت کننده دکمه حذف فیلد (با استفاده از event delegation) ---
        // Use event delegation for dynamically added remove buttons.
        // استفاده از event delegation برای دکمه‌های حذف اضافه شده پویا.
        $('#fb-cert-fields-container').on('click', '.fb-remove-field', function() {
            // Get the confirmation message (should be localized).
            // دریافت پیام تأیید (باید محلی‌سازی شود).
            var confirmMsg = typeof fbCertAdminData !== 'undefined' ? fbCertAdminData.labels.deleteConfirm : 'Are you sure you want to delete this field?';
            if (confirm(confirmMsg)) {
                // Find the closest parent field row and remove it.
                // پیدا کردن نزدیک‌ترین ردیف فیلد والد و حذف آن.
                $(this).closest('.fb-cert-field-row').remove();
                // Update the order values after removal.
                // به‌روزرسانی مقادیر ترتیب پس از حذف.
                updateFieldOrder();
            }
        });
    } // End if sortable container exists

    // ---- QR Code Modal Handling (Admin List) ----
    // ---- مدیریت مودال کد QR (لیست ادمین) ----
    // This section depends on the HTML structure generated in `admin-columns.php`.
    // If the button/modal approach is used there, this JS is needed.
    // این بخش به ساختار HTML تولید شده در `admin-columns.php` بستگی دارد.
    // اگر روش دکمه/مودال در آنجا استفاده شود، این JS لازم است.
    /*
    if ($('.fb-show-qr-button').length > 0) {
         // Show modal on button click.
         // نمایش مودال با کلیک روی دکمه.
         $('body').on('click', '.fb-show-qr-button', function(e){
             e.preventDefault();
             e.stopPropagation(); // Prevent event bubbling / جلوگیری از انتشار رویداد
              $('.fb-qr-modal').hide(); // Hide any other open modals / پنهان کردن سایر مودال‌های باز
             var modal = $(this).siblings('.fb-qr-modal'); // Find the modal next to the button / پیدا کردن مودال کنار دکمه
             // Position and show the modal near the click event.
             // موقعیت‌دهی و نمایش مودال نزدیک رویداد کلیک.
             modal.css({ top: e.pageY + 10, left: e.pageX - 50 }).show();
         });

         // Close modal on close button click.
         // بستن مودال با کلیک روی دکمه بستن.
          $('body').on('click', '.fb-close-qr-modal', function(e){
              e.preventDefault();
              $(this).closest('.fb-qr-modal').hide();
          });

         // Close modal on click outside the modal area.
         // بستن مودال با کلیک بیرون از ناحیه مودال.
           $(document).on('click', function(event) {
               // Check if the click target is outside the modal and not the trigger button.
               // بررسی اینکه آیا هدف کلیک بیرون مودال است و دکمه فعال‌کننده نیست.
               if (!$(event.target).closest('.fb-qr-modal').length && !$(event.target).hasClass('fb-show-qr-button')) {
                   $('.fb-qr-modal').hide();
               }
           });
    }
    */


    // ---- Unified Media Uploader (Metaboxes) ----
    // ---- آپلودر رسانه یکپارچه (متاباکس‌ها) ----
    // This section handles both image and file uploads using data attributes.
    // این بخش هم آپلود تصویر و هم فایل را با استفاده از ویژگی‌های data مدیریت می‌کند.

    /**
     * Opens the WordPress Media Uploader frame.
     * فریم آپلودر رسانه وردپرس را باز می‌کند.
     *
     * @param {string} inputId The ID of the hidden input field to store the attachment ID. / شناسه فیلد ورودی مخفی برای ذخیره شناسه پیوست.
     * @param {string|array} libraryType The allowed media library type(s). / نوع(های) مجاز کتابخانه رسانه.
     */
    function openMediaUploader(inputId, libraryType = 'image') {
        // --- Translatable Strings (Should be passed via wp_localize_script) ---
        // --- رشته‌های قابل ترجمه (باید از طریق wp_localize_script ارسال شوند) ---
        var localized = typeof fbCertAdminData !== 'undefined' ? fbCertAdminData.uploader : {
            title: 'Select or Upload',
            button: 'Use this file',
            currentFile: 'Current file:'
        };

        var frame = wp.media({
            title: localized.title, // Frame title / عنوان فریم
            button: { text: localized.button }, // Button text / متن دکمه
            library: { type: libraryType }, // Allowed types / انواع مجاز
            multiple: false // Single selection only / فقط انتخاب تکی
        });

        // Event handler for when a file is selected.
        // مدیریت کننده رویداد برای زمانی که یک فایل انتخاب می‌شود.
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON(); // Get selected file data / دریافت داده فایل انتخاب شده

            // Get related DOM elements using the inputId.
            // دریافت المان‌های DOM مرتبط با استفاده از inputId.
            var $inputField = $('#' + inputId);
            var $previewImage = $('img[data-preview-for="' + inputId + '"]');
            var $removeButton = $('button.fb-remove-button[data-input-id="' + inputId + '"]');
            var $fileDisplay = $('.fb-current-file-display[data-input-id="' + inputId + '"]');

            // Update the hidden input field with the attachment ID.
            // به‌روزرسانی فیلد ورودی مخفی با شناسه پیوست.
            $inputField.val(attachment.id).trigger('change'); // Trigger change for potential dependencies / اجرای change برای وابستگی‌های احتمالی

            // Update image preview if it exists.
            // به‌روزرسانی پیش‌نمایش تصویر در صورت وجود.
            if ($previewImage.length) {
                // Use thumbnail size if available, otherwise full URL.
                // استفاده از اندازه تصویر کوچک در صورت وجود، در غیر این صورت URL کامل.
                var thumbnailUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $previewImage.attr('src', thumbnailUrl).show(); // Set src and display / تنظیم src و نمایش
            }

            // Update file display area if it exists (for non-image files).
            // به‌روزرسانی ناحیه نمایش فایل در صورت وجود (برای فایل‌های غیر تصویری).
            if ($fileDisplay.length) {
                // Construct the HTML to display the file link.
                // ساخت HTML برای نمایش لینک فایل.
                var fileLink = '<strong>' + localized.currentFile + '</strong> <a href="' + attachment.url + '" target="_blank">' + attachment.filename + '</a>';
                $fileDisplay.html(fileLink).show(); // Set content and display / تنظیم محتوا و نمایش
            }

            // Show the remove button.
            // نمایش دکمه حذف.
            $removeButton.show();
        });

        // Open the media frame.
        // باز کردن فریم رسانه.
        frame.open();
    }

    // --- Event delegation for Upload/Select buttons ---
    // --- Event delegation برای دکمه‌های آپلود/انتخاب ---
    // Uses namespacing '.fbCertUploader' to prevent potential conflicts.
    // استفاده از فضای نام '.fbCertUploader' برای جلوگیری از تداخل‌های احتمالی.
    $('body').off('click.fbCertUploader').on('click.fbCertUploader', '.fb-upload-button', function(e) {
        e.preventDefault();
        var inputId = $(this).data('input-id'); // Get the target input ID from 'data-input-id' / دریافت شناسه ورودی هدف از 'data-input-id'
        var libraryType = $(this).data('library-type') || 'image'; // Get allowed types from 'data-library-type', default to image / دریافت انواع مجاز از 'data-library-type'، پیش‌فرض image

        // Handle comma-separated types by converting to an array.
        // مدیریت انواع جدا شده با کاما با تبدیل به آرایه.
        if (typeof libraryType === 'string' && libraryType.includes(',')) {
            libraryType = libraryType.split(',').map(s => s.trim());
        }

        // Call the function to open the uploader.
        // فراخوانی تابع برای باز کردن آپلودر.
        openMediaUploader(inputId, libraryType);
    });

    // --- Event delegation for Remove buttons ---
    // --- Event delegation برای دکمه‌های حذف ---
    $('body').off('click.fbCertRemover').on('click.fbCertRemover', '.fb-remove-button', function(e) {
        e.preventDefault();
        var inputId = $(this).data('input-id'); // Target input ID / شناسه ورودی هدف

        // Get related DOM elements.
        // دریافت المان‌های DOM مرتبط.
        var $inputField = $('#' + inputId);
        var $previewImage = $('img[data-preview-for="' + inputId + '"]');
        var $fileDisplay = $('.fb-current-file-display[data-input-id="' + inputId + '"]');

        // Clear the hidden input value.
        // خالی کردن مقدار ورودی مخفی.
        $inputField.val('').trigger('change');

        // Hide the image preview.
        // پنهان کردن پیش‌نمایش تصویر.
        if($previewImage.length) {
            $previewImage.attr('src', '').hide();
        }
        // Clear and hide the file display area.
        // خالی و پنهان کردن ناحیه نمایش فایل.
        if($fileDisplay.length) {
            $fileDisplay.empty().hide();
        }
        // Hide the remove button itself.
        // پنهان کردن خود دکمه حذف.
        $(this).hide();
    });


    // ---- Shortcode Copy Button (Usage Guide Page) ----
    // ---- دکمه کپی شورت‌کد (صفحه راهنمای استفاده) ----
    // Check if the copy button exists on the page.
    // بررسی وجود دکمه کپی در صفحه.
    if ($('.copy-shortcode-button').length > 0) {
        // Add click event listener using delegation.
        // افزودن شنونده رویداد کلیک با استفاده از delegation.
        $('body').on('click', '.copy-shortcode-button', function() {
            var $button = $(this); // The clicked button / دکمه کلیک شده
            // Find the related shortcode element using the ID set in PHP.
            // پیدا کردن المان کد کوتاه مرتبط با استفاده از ID تنظیم شده در PHP.
            var $shortcodeElement = $('#shortcode-to-copy');
            // Find the feedback element next to the button.
            // پیدا کردن المان فیدبک کنار دکمه.
            var $feedbackElement = $button.siblings('.copy-feedback');
            // Get the text to copy from the data attribute set in PHP.
            // دریافت متن برای کپی از ویژگی data تنظیم شده در PHP.
            var textToCopy = $shortcodeElement.data('clipboard-text');

            // --- Translatable Strings (Should be passed via wp_localize_script) ---
            // --- رشته‌های قابل ترجمه (باید از طریق wp_localize_script ارسال شوند) ---
             var localizedCopy = typeof fbCertAdminData !== 'undefined' ? fbCertAdminData.copyFeedback : {
                copied: 'کپی شد!', // Copied!
                error: 'خطا در کپی کردن کد!' // Error copying code!
            };


            // Attempt to use the modern Clipboard API first.
            // ابتدا تلاش برای استفاده از Clipboard API مدرن.
            if (!navigator.clipboard) {
                // Fallback for older browsers using execCommand.
                // راه حل جایگزین برای مرورگرهای قدیمی‌تر با استفاده از execCommand.
                try {
                    var tempInput = document.createElement('input'); // Create temporary input / ایجاد ورودی موقت
                    tempInput.style.position = 'absolute';
                    tempInput.style.left = '-9999px'; // Position off-screen / موقعیت‌دهی خارج از صفحه
                    tempInput.value = textToCopy; // Set its value / تنظیم مقدار آن
                    document.body.appendChild(tempInput);
                    tempInput.select(); // Select the text / انتخاب متن
                    document.execCommand('copy'); // Execute copy command / اجرای دستور کپی
                    document.body.removeChild(tempInput); // Remove temporary input / حذف ورودی موقت

                    // Show success feedback.
                    // نمایش فیدبک موفقیت.
                    $feedbackElement.text(localizedCopy.copied).fadeIn();
                    // Hide feedback after 2 seconds.
                    // پنهان کردن فیدبک پس از ۲ ثانیه.
                    setTimeout(function() {
                        $feedbackElement.fadeOut();
                    }, 2000);

                } catch (err) {
                    // Handle errors in the fallback method.
                    // مدیریت خطاها در روش جایگزین.
                    console.error('Fallback: Oops, unable to copy', err);
                    alert(localizedCopy.error); // Alert user / هشدار به کاربر
                }
                return; // Stop execution / توقف اجرا
            }

            // Use modern Clipboard API.
            // استفاده از Clipboard API مدرن.
            navigator.clipboard.writeText(textToCopy).then(function() {
                /* Success */
                /* عملیات موفقیت آمیز */
                // Show success feedback.
                // نمایش فیدبک موفقیت.
                $feedbackElement.text(localizedCopy.copied).fadeIn();
                // Hide feedback after 2 seconds.
                // پنهان کردن فیدبک پس از ۲ ثانیه.
                setTimeout(function() {
                    $feedbackElement.fadeOut();
                }, 2000);
            }, function(err) {
                /* Failure */
                /* عملیات ناموفق */
                console.error('Async: Could not copy text: ', err);
                alert(localizedCopy.error + ': ' + err); // Alert user with error / هشدار به کاربر با خطا
            });
        });
    }


}); // End jQuery $(document).ready