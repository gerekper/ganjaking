/**
 * Start webhook form widget script
 */

(function ($, elementor) {
    "use strict";
    var widgetWebhookForm = function ($scope, $) {
        var $formWrapper = $scope.find(".bdt-ep-webhook-form"),
            $settings = $formWrapper.data("settings");

        if (!$formWrapper.length) {
            return;
        }

        $($settings.id)
            .find(".bdt-ep-webhook-form-form")
            .submit(function (e) {
                e.preventDefault();
                var formData = $(this).serialize();
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
                                message:
                                    '<div bdt-icon="icon: check"></div> ' + response.message,
                            });
                        } else {
                            bdtUIkit.notification({
                                message:
                                    '<div bdt-icon="icon: close"></div> ' + response.message,
                            });
                        }
                    },
                });
            });
    };

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
