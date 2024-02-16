/**
 * Start webhook form widget script
 */

(function ($, elementor) {
    "use strict";
    var widgetWebhookForm = function ($scope, $) {
        var $formWrapper = $scope.find(".bdt-ep-webhook-form.without-recaptcha"),
            $form = $formWrapper.find(".bdt-ep-webhook-form-form"),
            $settings = $formWrapper.data("settings");

        if (!$formWrapper.length) {
            return;
        }

        $($settings.id).find(".bdt-ep-webhook-form-form").submit(function (e) {
            e.preventDefault();
            send_form_data($form);
        });
    };

    function send_form_data(form) {
        var formData = $(form).serialize();
        formData = formData + "&action=submit_webhook_form";
        formData = formData + "&nonce=" + ElementPackConfig.nonce;

        $.ajax({
            url: ElementPackConfig.ajaxurl,
            type: "post",
            data: formData,
            beforeSend: function () {
                bdtUIkit.notification({
                    message: "<div bdt-spinner></div> Sending...",
                    timeout: false,
                    status: "primary",
                });
            },
            success: function (res) {
                let response = JSON.parse(res);
                bdtUIkit.notification.closeAll();

                if (true == response.success) {
                    bdtUIkit.notification({
                        message: '<div bdt-icon="icon: check"></div> ' + response.message,
                    });
                } else {
                    bdtUIkit.notification({
                        message: '<div bdt-icon="icon: close"></div> ' + response.message,
                    });
                }
            },
        });
    }

    // google invisible captcha
    function elementPackWebFormGIC() {

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

            var $webhookForm = $('textarea.g-recaptcha-response').filter(function () {
                return $(this).val() === response;
            }).closest('form.bdt-ep-webhook-form-form');

            var contactFormAction = $webhookForm.attr('action');

            if (contactFormAction && contactFormAction !== '') {
                send_form_data($webhookForm);
            } else {
                console.log($webhookForm);
            }

            grecaptcha.reset();

        }); //end promise

    }

    //Contact form recaptcha callback, if needed
    window.elementPackGICCB = elementPackWebFormGIC;

    jQuery(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/bdt-webhook-form.default",
            widgetWebhookForm
        );
    });
})(jQuery, window.elementorFrontend);

/**
 * End webhook form widget script
 */