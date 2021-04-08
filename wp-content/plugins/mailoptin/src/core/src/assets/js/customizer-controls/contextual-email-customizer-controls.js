(function (api, $) {
    'use strict';

    $(window).on('load', function () {

        setTimeout(function () {

            api('mo_email_campaigns[' + mailoptin_email_campaign_id + '][connection_service]', function (setting) {
                var is_display_footer_description, linkSettingValueToControlActiveState;

                /**
                 * Determine whether the hide name dependent fields should be displayed.
                 *
                 * @returns {boolean} Is displayed?
                 */
                is_display_footer_description = function () {
                    return setting.get() !== 'ActiveCampaignConnect';
                };

                /**
                 * Update a control's active state according to the header_textcontrol setting's value.
                 *
                 * @param {wp.customize.Control} control Site Title or Tagline Control.
                 */
                linkSettingValueToControlActiveState = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_display_footer_description());
                    };

                    // FYI: With the following we can eliminate all of our PHP active_callback code.
                    control.active.validate = is_display_footer_description;

                    // Set initial active state.
                    setActiveState();

                    /*
                     * Update activate state whenever the setting is changed.
                     * Even when the setting does have a refresh transport where the
                     * server-side active callback will manage the active state upon
                     * refresh, having this JS management of the active state will
                     * ensure that controls will have their visibility toggled
                     * immediately instead of waiting for the preview to load.
                     * This is especially important if the setting has a postMessage
                     * transport where changing the setting wouldn't normally cause
                     * the preview to refresh and thus the server-side active_callbacks
                     * would not get invoked.
                     */
                    setting.bind(setActiveState);
                };

                // Call linkSettingValueToControlActiveState on the site title and tagline controls when they exist.
                api.control('mo_email_campaigns[' + mailoptin_email_campaign_id + '][footer_description]', linkSettingValueToControlActiveState);
            });

            api('mo_email_campaigns[' + mailoptin_email_campaign_id + '][content_remove_feature_image]', function (setting) {
                var is_display_fallback_image, linkSettingValueToControlActiveState;

                is_display_fallback_image = function () {
                    return !setting.get();
                };

                linkSettingValueToControlActiveState = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_display_fallback_image());
                    };

                    control.active.validate = is_display_fallback_image;

                    setActiveState();

                    setting.bind(setActiveState);
                };

                api.control('mo_email_campaigns[' + mailoptin_email_campaign_id + '][default_image_url]', linkSettingValueToControlActiveState);
            });

            api('mo_email_campaigns[' + mailoptin_email_campaign_id + '][content_remove_ellipsis_button]', function (setting) {
                var is_display, linkSettingValueToControlActiveState;

                is_display = function () {
                    return !setting.get();
                };

                linkSettingValueToControlActiveState = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_display());
                    };

                    control.active.validate = is_display;

                    setActiveState();

                    setting.bind(setActiveState);
                };

                api.control('mo_email_campaigns[' + mailoptin_email_campaign_id + '][content_ellipsis_button_label]', linkSettingValueToControlActiveState);
            });

            api('mo_email_campaigns[' + mailoptin_email_campaign_id + '][send_immediately]', function (setting) {
                var is_display, linkSettingValueToControlActiveState;

                is_display = function () {
                    return !setting.get();
                };

                linkSettingValueToControlActiveState = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_display());
                    };

                    control.active.validate = is_display;

                    setActiveState();

                    setting.bind(setActiveState);
                };

                api.control('mo_email_campaigns[' + mailoptin_email_campaign_id + '][email_campaign_schedule]', linkSettingValueToControlActiveState);
            });

            // handles activation and deactivation of optin
            $('#mo-automation-activate-switch').on('change', function () {
                $.post(ajaxurl, {
                    action: 'mailoptin_automation_toggle_active',
                    id: mailoptin_email_campaign_id,
                    status: this.checked,
                    security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
                }, function ($response) {
                    // do nothing
                });
            });

            // CPT settings control
            var cpt_settings_contextual_display = function (e, flag, val) {
                e = e || false;
                flag = flag || false;
                if (typeof val === 'undefined' && typeof this !== 'undefined') {
                    val = this.value;
                }

                if (typeof val === 'undefined') return;

                if (val === 'post') {
                    $('li[id*="custom_post_type_settings"]').hide();
                    $('li[id*="post_categories"], li[id*="post_tags"]').show();
                } else {
                    $('li[id*="post_categories"], li[id*="post_tags"]').hide();
                    if (flag === false) {
                        $('li[id*="custom_post_type_settings"]').hide();
                        add_spinner(this);

                        $.post(ajaxurl, {
                                action: 'mailoptin_customizer_email_automation_get_taxonomy',
                                custom_post_type: val,
                                security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
                            },
                            function (response) {
                                if (_.isObject(response) && 'success' in response && 'data' in response) {
                                    var cache = $('li[id*="custom_post_type_settings"]');
                                    cache.find('.mo-ea-cpt-setting-container').html(response.data);
                                    cache.show();
                                    $('.mo-ea-cpt-setting-container .mailoptin-chosen').chosen({
                                        inherit_select_classes: true,
                                        width: "100%"
                                    });

                                    $('.mo-ea-cpt-setting-container .mailoptin-chosen .search-field input').attr('placeholder', mailoptin_globals.chosen_search_placeholder);
                                }

                                remove_spinner();
                            });
                    }
                }
            };

            // on load
            var custom_post_type_obj = $("select[data-customize-setting-link*='[custom_post_type]']");
            cpt_settings_contextual_display('', true, custom_post_type_obj.val());
            custom_post_type_obj.on('change', cpt_settings_contextual_display);


            function add_spinner(placement) {
                var spinner_html = $('<img class="mo-spinner fetch-email-list" src="' + mailoptin_globals.admin_url + 'images/spinner.gif">');
                $(placement).after(spinner_html);
            }


            function remove_spinner() {
                $('.mo-spinner.fetch-email-list').remove();
            }

        }, 1000);
    });

})(wp.customize, jQuery);