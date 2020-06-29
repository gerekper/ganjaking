(function ($, document, window, mailoptin_optin_campaign_id, moToastrLabels) {

    var nt = {};

    // this could also be window.moHeartbeatCounter
    nt.moHeartbeatCounter = 0;

    nt.optinNotActiveFlag = nt.integrationNotSetFlag = false;

    /**
     * Check if optin campaign has been activated.
     *
     * @returns {boolean}
     */
    nt.isOptinActive = function () {
        return document.getElementById('mo-optin-activate-switch').checked === true;
    };

    /**
     * Check if integration has been set.
     *
     * @returns {boolean}
     */
    nt.isIntegrationSet = function () {
        return document.querySelector("select[name='connection_service']").value !== "";
    };

    nt.dismiss_ajax = function (notification) {
        $.post(ajaxurl, {
            action: 'mailoptin_dismiss_toastr_notifications',
            notification: notification,
            optin_id: mailoptin_optin_campaign_id
        });
    };

    nt.controlFocus = function (control) {
        parent.wp.customize.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][' + control + ']').focus();
    };

    nt.integrationNotSetNotification = function (window) {
        if (moStateRepository.data['integrationNotSet'] === mailoptin_optin_campaign_id) return;

        // after 45seconds, alert users to set an integration
        if (nt.moHeartbeatCounter > 45 && !nt.isIntegrationSet() && !nt.integrationNotSetFlag) {
            nt.integrationNotSetFlag = true;

            var options = {
                onclick: function () {
                    nt.controlFocus('integrations');
                    nt.dismiss_ajax('integrationNotSet');
                },

                onCloseClick: function () {
                    nt.dismiss_ajax('integrationNotSet');
                }
            };

            window.moToastr('warning', moToastrLabels.integrationNotSet.title, moToastrLabels.integrationNotSet.message, options);
        }
    };

    nt.optinNotActiveNotification = function (window) {
        if (moStateRepository.data['optinNotActive'] === mailoptin_optin_campaign_id) return;

        // after 40seconds, alert users to set an integration
        if (nt.moHeartbeatCounter >= 120 && !nt.isOptinActive() && !nt.optinNotActiveFlag) {
            nt.optinNotActiveFlag = true;

            var options = {
                onclick: function () {
                    $('#mo-optin-activate-switch').click();
                    // delay to allow ajax for optin activate to have completed.
                    _.delay(function () {
                        nt.dismiss_ajax('optinNotActive');
                    }, 2000);
                },

                onCloseClick: function () {
                    nt.dismiss_ajax('optinNotActive');
                }
            };

            window.moToastr('warning', moToastrLabels.optinNotActive.title, moToastrLabels.optinNotActive.message, options);
        }
    };

    window.onload = function () {
        var preview_iframe_name = $('.wp-full-overlay-main').find('iframe').prop('name');

        if (typeof preview_iframe_name === 'undefined') return;

        // source https://stackoverflow.com/a/16019605/2648410
        var preview_iframe_window = document.getElementsByName(preview_iframe_name)[0].contentWindow.window;

        if (typeof preview_iframe_window === 'undefined') return;

        // this script is loaded inside preview iframe where heartbeat isn't available hence parent.document
        $(document).on('heartbeat-send', function () {
            nt.moHeartbeatCounter += wp.heartbeat.interval();
            nt.integrationNotSetNotification(preview_iframe_window);
            nt.optinNotActiveNotification(preview_iframe_window);
        });

    }

})(jQuery, document, window, mailoptin_optin_campaign_id, moToastrLabels);