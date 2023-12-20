jQuery(document).ready(function ($) {
    $(".eael-admin-settings-popup").on("click", function (e) {
        e.preventDefault();
        var settings = $(this).data("settings");
        var key = $(this).data("key");
        var title = $(this).data("title");

        swal.fire({
            title: title,
            html:
                '<input type="text" id="' +
                settings +
                '" class="swal2-input" name="' +
                settings +
                '" placeholder="' +
                title +
                '" value="' +
                eaelAdmin[key] +
                '" />',
            closeOnClickOutside: false,
            closeOnEsc: false,
            showCloseButton: true
        }).then(function (result) {
            if (!result.dismiss) {
                $("#" + settings + "-hidden").val($("#" + settings).val());
                $(".js-eael-settings-save")
                    .addClass("save-now")
                    .removeAttr("disabled")
                    .css("cursor", "pointer")
                    .trigger("click");
            }
        });
    });

    $(document).on('click', '.eael-license-form-block button[type="submit"][name="license_activate"]', function (e) {
        e.preventDefault();
        $('.eael-license-error-msg').hide().text('');
        $('.eael-verification-msg').hide();

        let button = $(this);
        button.text('Activating...');

        $.ajax({
            url: wpdeveloperLicenseManagerConfig.api_url,
            type: 'POST',
            data: {
                action: `${wpdeveloperLicenseManagerConfig.action}/license/activate`,
                _nonce: wpdeveloperLicenseManagerConfig.nonce,
                license_key: $(`#${wpdeveloperLicenseManagerConfig.action}-license-key`).val()
            },
            success: function (response) {
                if (response.success) {
                    $(`#${wpdeveloperLicenseManagerConfig.action}-license-key`).attr('disabled', 'disabled');

                    if (response.data.license !== 'required_otp') {
                        $('.eael-activate__license__block').hide().siblings('.--deactivation-form').show();
                        $('.--deactivation-form input').val(response.data.license_key);
                        $(`#${wpdeveloperLicenseManagerConfig.action}-license-key`).val('').removeAttr('disabled')
                            .siblings('button').removeAttr('disabled').text('Activate');
                        return;
                    }

                    button.text('Verification Required').attr('disabled', 'disabled').addClass('--verification-required');
                    $('.eael-customer-email').text(response.data.customer_email);
                    $('.eael-verification-msg').show();
                } else {
                    $('.eael-license-error-msg').text(response.data.message).show();
                    button.text('Activate');
                }
            },
            error: function (response) {
                console.log(response);
                button.text('Activate');
            }
        });
    }).on('click', '.eael-verification-msg button[type="submit"]', function (e) {
        e.preventDefault();
        $('.eael-license-error-msg').hide().text('');

        let button = $(this);
        button.text('Verifying...');

        $.ajax({
            url: wpdeveloperLicenseManagerConfig.api_url,
            type: 'POST',
            data: {
                action: `${wpdeveloperLicenseManagerConfig.action}/license/submit-otp`,
                _nonce: wpdeveloperLicenseManagerConfig.nonce,
                license: $(`#${wpdeveloperLicenseManagerConfig.action}-license-key`).val(),
                otp: $(`#${wpdeveloperLicenseManagerConfig.action}-license-otp`).val()
            },
            success: function (response) {
                if (response.success) {
                    $('.eael-activate__license__block').hide().siblings('.--deactivation-form').show();
                    $(`#${wpdeveloperLicenseManagerConfig.action}-license-otp`).val('');
                    $(`#${wpdeveloperLicenseManagerConfig.action}-license-key`).val('').removeAttr('disabled')
                        .siblings('button').removeAttr('disabled').text('Activate').removeClass('--verification-required');
                    $('.eael-verification-msg').hide();
                    $('.--deactivation-form input').val(response.data.license_key);
                } else {
                    let $error_msg = $('.eael-license-error-msg');
                    if (response.data.code === 'invalid_otp') {
                        $error_msg.text('Whoops! Your License Verification Code is Invalid. Please try again.').show();
                    } else if (response.data.code === 'expired_otp') {
                        $error_msg.text('Whoops! Your License Verification Code has expired. Please try again.').show();
                    }
                }

                button.text('Verify');
            },
            error: function (response) {
                console.log(response);
                button.text('Verify');
            }
        });
    }).on('click', '.eael-license-form-block button[type="submit"][name="license_deactivate"]', function (e) {
        e.preventDefault();

        let button = $(this);
        button.text('Deactivating...');

        $.ajax({
            url: wpdeveloperLicenseManagerConfig.api_url,
            type: 'POST',
            data: {
                action: `${wpdeveloperLicenseManagerConfig.action}/license/deactivate`,
                _nonce: wpdeveloperLicenseManagerConfig.nonce
            },
            success: function (response) {
                if (response.success) {
                    $('.eael-activate__license__block').hide().siblings('.--activation-form').show();
                } else {
                    $('.eael-license-error-msg').text(response.data.message).show();
                }

                button.text('Deactivate');
            },
            error: function (response) {
                console.log(response);
                button.text('Deactivate');
            }
        });
    }).on('click', '.eael-otp-resend', function (e) {
        e.preventDefault();

        $.ajax({
            url: wpdeveloperLicenseManagerConfig.api_url,
            type: 'POST',
            data: {
                action: `${wpdeveloperLicenseManagerConfig.action}/license/resend-otp`,
                _nonce: wpdeveloperLicenseManagerConfig.nonce,
                license: $(`#${wpdeveloperLicenseManagerConfig.action}-license-key`).val()
            },
            success: function (response) {
                if (response.success) {
                    $('.eael-license-error-msg').text('License Verification Code has been sent to your email address. Please check your email to find the code.').addClass('notice-message').show();
                    setTimeout(function () {
                        $('.eael-license-error-msg').removeClass('notice-message').text('').hide();
                    }, 3000);
                } else {
                    $('.eael-license-error-msg').text(response.data.message).show();
                }
            },
            error: function (response) {
                console.log(response);
            }
        })
    });
});
