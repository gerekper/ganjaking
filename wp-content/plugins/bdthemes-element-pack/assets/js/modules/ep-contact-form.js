/**
 * Start contact form widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetSimpleContactForm = function ($scope, $) {

        var $contactForm = $scope.find('.bdt-contact-form .without-recaptcha');

        if (!$contactForm.length) {
            return;
        }

        $contactForm.submit(function (e) {
            sendContactForm($contactForm);
            return false;
        });

        return false;

    };

    function sendContactForm($contactForm) {
        var langStr = window.ElementPackConfig.contact_form;

        $.ajax({
            url: $contactForm.attr('action'),
            type: 'POST',
            data: $contactForm.serialize(),
            beforeSend: function () {
                bdtUIkit.notification({
                    message: '<div bdt-spinner></div> ' + langStr.sending_msg,
                    timeout: false,
                    status: 'primary'
                });
            },
            success: function (data) {
                var redirectURL = $(data).data('redirect'),
                    isExternal = $(data).data('external'),
                    resetStatus = $(data).data('resetstatus');

                bdtUIkit.notification.closeAll();
                var notification = bdtUIkit.notification({
                    message: data
                });

                if (redirectURL) {
                    if (redirectURL != 'no') {
                        bdtUIkit.util.on(document, 'close', function (evt) {
                            if (evt.detail[0] === notification) {
                                window.open(redirectURL, isExternal);
                            }
                        });
                    }
                }

                localStorage.setItem("bdtCouponCode", $contactForm.attr('id'));

                if (resetStatus) {
                    if (resetStatus !== 'no') {
                        $contactForm[0].reset();
                    }
                }

                // $contactForm[0].reset();
            }
        });
        return false;
    }

    // google invisible captcha
    function elementPackGIC() {

        var langStr = window.ElementPackConfig.contact_form;

        return new Promise(function (resolve, reject) {

            if (grecaptcha === undefined) {
                bdtUIkit.notification({
                    message: '<div bdt-spinner></div> ' + langStr.captcha_nd,
                    timeout: false,
                    status: 'warning'
                });
                reject();
            }

            var response = grecaptcha.getResponse();

            if (!response) {
                bdtUIkit.notification({
                    message: '<div bdt-spinner></div> ' + langStr.captcha_nr,
                    timeout: false,
                    status: 'warning'
                });
                reject();
            }

            var $contactForm = $('textarea.g-recaptcha-response').filter(function () {
                return $(this).val() === response;
            }).closest('form.bdt-contact-form-form');

            var contactFormAction = $contactForm.attr('action');

            if (contactFormAction && contactFormAction !== '') {
                sendContactForm($contactForm);
            } else {
                // console.log($contactForm);
            }

            grecaptcha.reset();

        }); //end promise

    }

    //Contact form recaptcha callback, if needed
    window.elementPackGICCB = elementPackGIC;

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-contact-form.default', widgetSimpleContactForm);
    });


}(jQuery, window.elementorFrontend));

/**
 * End contact form widget script
 */