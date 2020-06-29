(function (api, $) {
    'use strict';

    $(window).on('load', function () {

        function cta_button_action_toggle(is_show_cta_fields) {

            api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_action]', function (setting) {
                var is_displayed, linkSettingValueToControlActiveState;

                is_displayed = function () {
                    return is_show_cta_fields === true && setting.get() === 'navigate_to_url';
                };

                linkSettingValueToControlActiveState = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_displayed());
                    };

                    control.active.validate = is_displayed;

                    // Set initial active state.
                    setActiveState();

                    setting.bind(setActiveState);
                };

                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_navigation_url]', linkSettingValueToControlActiveState);
            });
        }

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][display_only_button]', function (setting) {
            var is_display_optin_fields, is_show_cta_fields, callToActionFieldsToggle, optinFieldsDisplayToggle;

            is_display_optin_fields = function () {
                return !setting.get();
            };

            is_show_cta_fields = function () {
                return setting.get();
            };

            optinFieldsDisplayToggle = function (control) {
                var setActiveState = function () {
                    control.active.set(is_display_optin_fields());
                };

                control.active.validate = is_display_optin_fields;

                // Set initial active state.
                setActiveState();

                setting.bind(setActiveState);
            };

            callToActionFieldsToggle = function (control) {
                var setActiveState = function () {
                    control.active.set(is_show_cta_fields());
                    cta_button_action_toggle(is_show_cta_fields());
                };

                control.active.validate = is_show_cta_fields;

                // Set initial active state.
                setActiveState();

                setting.bind(setActiveState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][submit_button_header]', optinFieldsDisplayToggle);

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][fields]', optinFieldsDisplayToggle);

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][submit_button]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][submit_button_color]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][submit_button_background]', optinFieldsDisplayToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][submit_button_font]', optinFieldsDisplayToggle);

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_header]', callToActionFieldsToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_action]', callToActionFieldsToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button]', callToActionFieldsToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_color]', callToActionFieldsToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_background]', callToActionFieldsToggle);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][cta_button_font]', callToActionFieldsToggle);
        });

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][bar_position]', function (setting) {
            var is_displayed, linkSettingValueToControlActiveState;

            is_displayed = function () {
                return setting.get() == 'top';
            };

            linkSettingValueToControlActiveState = function (control) {
                var setActiveState = function () {
                    control.active.set(is_displayed());
                };

                control.active.validate = is_displayed;

                // Set initial active state.
                setActiveState();

                setting.bind(setActiveState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][bar_sticky]', linkSettingValueToControlActiveState);
        });

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][click_launch_status]', function (setting) {
            var is_displayed, linkSettingValueToControlActiveState;

            is_displayed = function () {
                return setting.get();
            };

            linkSettingValueToControlActiveState = function (control) {
                var setActiveState = function () {
                    control.active.set(is_displayed());
                    // hide all display rules sections save for click launch when click launch is activated.
                    api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][load_optin_globally]').active(!is_displayed());

                    if (typeof api.section('mo_wp_exit_intent_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_exit_intent_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_x_seconds_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_x_seconds_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_x_scroll_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_x_scroll_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_x_page_views_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_x_page_views_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_page_filter_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_page_filter_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_shortcode_template_tag_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_shortcode_template_tag_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_adblock_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_adblock_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_newvsreturn_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_newvsreturn_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_referrer_detection_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_referrer_detection_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_schedule_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_schedule_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_device_targeting_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_device_targeting_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_user_filter_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_user_filter_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_query_filter_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_query_filter_display_rule_section').active(!is_displayed());
                    }

                    if (typeof api.section('mo_wp_polylang_display_rule_section') !== 'undefined') {
                        api.section('mo_wp_polylang_display_rule_section').active(!is_displayed());
                    }
                };

                control.active.validate = is_displayed;

                // Set initial active state.
                setActiveState();

                setting.bind(setActiveState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][click_launch_basic_shortcode]', linkSettingValueToControlActiveState);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][click_launch_advance_shortcode]', linkSettingValueToControlActiveState);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][click_launch_html_code]', linkSettingValueToControlActiveState);
        });

        // contextual display of redirect_url in success panel/section.
        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][success_action]', function (setting) {
                var is_redirect_url_value_displayed, is_success_message_displayed,
                    linkSettingValueToControlActiveState1, linkSettingValueToControlActiveState2;

                is_success_message_displayed = function () {
                    return setting.get() === 'success_message';
                };

                is_redirect_url_value_displayed = function () {
                    return setting.get() === 'redirect_url';
                };

                linkSettingValueToControlActiveState1 = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_redirect_url_value_displayed());
                    };

                    control.active.validate = is_redirect_url_value_displayed;
                    // Set initial active state.
                    setActiveState();

                    setting.bind(setActiveState);
                };

                linkSettingValueToControlActiveState2 = function (control) {
                    var setActiveState = function () {
                        control.active.set(is_success_message_displayed());
                    };

                    control.active.validate = is_success_message_displayed;
                    // Set initial active state.
                    setActiveState();

                    setting.bind(setActiveState);
                };

                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][redirect_url_value]', linkSettingValueToControlActiveState1);
                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][pass_lead_data_redirect_url]', linkSettingValueToControlActiveState1);
                api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][success_message]', linkSettingValueToControlActiveState2);
            }
        );

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][note_acceptance_checkbox]', function (setting) {
            var is_displayed, linkSettingValueToControlActiveState, controlCloseOptinOnClick;

            is_displayed = function () {
                return setting.get() === true;
            };

            linkSettingValueToControlActiveState = function (control) {
                var setActiveState = function () {
                    control.active.set(is_displayed());
                };

                control.active.validate = is_displayed;

                setActiveState();

                setting.bind(setActiveState);
            };

            controlCloseOptinOnClick = function (control) {
                var setValueState = function () {
                    if (is_displayed() === true) {
                        control.setting.set(false);
                    }
                };

                setValueState();

                setting.bind(setValueState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][note_acceptance_error]', linkSettingValueToControlActiveState);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][note_close_optin_onclick]', controlCloseOptinOnClick);
        });

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][load_optin_index]', function (setting) {
            var controlGlobalLoadOptin;

            controlGlobalLoadOptin = function (control) {
                var setValueState = function () {
                    if (setting.get() === true) {
                        control.setting.set(false);
                    }
                };

                setValueState();

                setting.bind(setValueState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][load_optin_globally]', controlGlobalLoadOptin);
        });

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][hide_mini_headline]', function (setting) {
            var is_displayed, linkSettingValueToControlActiveState;

            is_displayed = function () {
                return !setting.get();
            };

            linkSettingValueToControlActiveState = function (control) {
                var setActiveState = function () {
                    control.active.set(is_displayed());
                };

                control.active.validate = is_displayed;

                // Set initial active state.
                setActiveState();

                setting.bind(setActiveState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][mini_headline_font_color]', linkSettingValueToControlActiveState);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][mini_headline]', linkSettingValueToControlActiveState);
        });

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][woocommerce_show_all_endpoints]', function (setting) {
            var is_displayed, linkSettingValueToControlActiveState;

            is_displayed = function () {
                return !setting.get();
            };

            linkSettingValueToControlActiveState = function (control) {
                var setActiveState = function () {
                    control.active.set(is_displayed());
                };

                control.active.validate = is_displayed;

                setActiveState();

                setting.bind(setActiveState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][woocommerce_show_order_pay_endpoint]', linkSettingValueToControlActiveState);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][woocommerce_show_order_received_endpoint]', linkSettingValueToControlActiveState);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][woocommerce_show_view_order_endpoint]', linkSettingValueToControlActiveState);
        });

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][woocommerce_show_woo_products]', function (setting) {
            var is_displayed, linkSettingValueToControlActiveState;

            is_displayed = function () {
                return !setting.get();
            };

            linkSettingValueToControlActiveState = function (control) {
                var setActiveState = function () {
                    control.active.set(is_displayed());
                };

                control.active.validate = is_displayed;

                setActiveState();

                setting.bind(setActiveState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][woocommerce_show_specific_woo_products]', linkSettingValueToControlActiveState);
            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][woocommerce_show_specific_categories]', linkSettingValueToControlActiveState);
        });

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][hide_form_image]', function (setting) {
            var is_displayed, linkSettingValueToControlActiveState;

            is_displayed = function () {
                return !setting.get();
            };

            linkSettingValueToControlActiveState = function (control) {
                var setActiveState = function () {
                    control.active.set(is_displayed());
                };

                control.active.validate = is_displayed;

                // Set initial active state.
                setActiveState();

                setting.bind(setActiveState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][form_image]', linkSettingValueToControlActiveState);
        });

        api('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][note_close_optin_onclick]', function (setting) {
            var is_displayed, controlAcceptanceCheckbox;

            is_displayed = function () {
                return setting.get() === true;
            };

            controlAcceptanceCheckbox = function (control) {
                var setValueState = function () {
                    if (is_displayed() === true) {
                        control.setting.set(false);
                    }
                };

                setValueState();

                setting.bind(setValueState);
            };

            api.control('mo_optin_campaign[' + mailoptin_optin_campaign_id + '][note_acceptance_checkbox]', controlAcceptanceCheckbox);
        });

        $('input[data-customize-setting-link*=use_custom_html]').change(function () {
            $('li[id*=custom_html_content]').toggle(this.checked);
            $('#sub-accordion-section-mo_fields_section li.customize-control')
                .not('li[id*=use_custom_html], li[id*=custom_html_content]')
                .toggle(!this.checked);

            api.section('mo_success_section').active(!this.checked);

        }).change();

        $('select[data-customize-setting-link*=who_see_optin]').change(function () {
            var value = $(this).val();
            $('li[id*=show_to_roles]').toggle(value === 'show_to_roles');
            $('li[id*=prefill_logged_user_data]').toggle(value !== 'show_non_logged_in');
        }).change();

        // handles click to select on input readonly fields
        $('.mo-click-select').click(function () {
            this.select();
        });

        // handles activation and deactivation of optin
        $('#mo-optin-activate-switch').on('change', function () {
            $.post(ajaxurl, {
                action: 'mailoptin_optin_toggle_active',
                id: mailoptin_optin_campaign_id,
                status: this.checked,
                security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
            }, function ($response) {
                // do nothing
            });
        });

        // --------------------- Switch themes ----------------------------- //

        //Close button div html
        var switchThemesClose = '<div class="close-button-div"><button class="button button-secondary">' + moContextualControlsLabels.close + '</button></div>';

        //Loader
        var loader = '<span class="spinner" style="visibility: visible"></span>';

        //Create the main popup and add a loading spinner to it
        var switchThemesPopup =
            $('body')
                .append('<div class="mo-change-theme-popup"></div>')
                .find('.mo-change-theme-popup')
                .append(loader);

        //Helper function to attach a close button to a popup
        var appendPopupClose = function () {
            $(switchThemesPopup)
                .append('<span class="mo-popup-close"><span class="dashicons dashicons-no" style="font-size: 2.5em;cursor: pointer"></span></span>')
                .find('.mo-popup-close')
                .attr('title', moContextualControlsLabels.close)
                .on('click', function () {
                    $(switchThemesPopup)
                        .removeClass('mo-change-theme-popup-show mo-change-theme-display-block')
                        .html(loader)
                });
        };

        appendPopupClose();

        //Create the switch themes button...
        var switchThemesButton =
            $('#customize-info .customize-help-toggle')
                .after('<button></button>')
                .next()
                .text(moContextualControlsLabels.changeTheme)
                .attr('aria-label', moContextualControlsLabels.changeTheme)
                .attr('type', 'button')
                .addClass('button change-theme mo-change-theme-button');

        //... which when clicked loads optin themes
        $(switchThemesButton).on('click', function (e) {
            e.preventDefault();

            //Display the lightbox
            $(switchThemesPopup).addClass('mo-change-theme-popup-show');

            //Prepare our ajax request data
            var data = {
                action: 'mailoptin_customizer_get_templates',
                id: mailoptin_optin_campaign_id,
                _ajax_nonce: moContextualControlsLabels.themeNonce
            };

            //Then try loading themes
            $.post(ajaxurl, data)

            //If we succeeded, display them
                .done(function (data) {

                    //Replace the loader with our themes
                    $(switchThemesPopup)
                        .html(data)
                        .addClass('mo-change-theme-display-block')

                        //When a theme is selected, set it as the theme then reloaded
                        .find('.mailoptin-optin-theme a')
                        .on('click', function (e) {
                            if ($(this).attr('href') !== '#') return;

                            e.preventDefault();

                            //Save changes
                            $('#save').click();

                            //Display the loader
                            $(switchThemesPopup)
                                .html(loader)
                                .removeClass('mo-change-theme-display-block');

                            appendPopupClose();

                            var theme = $(this).parents('.mailoptin-optin-theme').data('optin-theme');
                            var data = {
                                action: 'mailoptin_customizer_set_template',
                                id: mailoptin_optin_campaign_id,
                                theme: theme,
                                _ajax_nonce: moContextualControlsLabels.themeNonce
                            };

                            //Save the data
                            $.post(ajaxurl, data)

                            //Reload the page if we succeeded
                                .done(function () {
                                    location.reload(true);
                                })

                                //If not, show an error message
                                .fail(function () {
                                    $(switchThemesPopup)
                                        .html('<div class="mailoptin-optin-themes-ajax-error">' + moContextualControlsLabels.ajaxError + switchThemesClose + '</div>')
                                        .find('button')
                                        .on('click', function () {
                                            $(switchThemesPopup)
                                                .removeClass('mo-change-theme-popup-show mo-change-theme-display-block')
                                                .html(loader)
                                        })
                                })

                        });

                    appendPopupClose();
                })

                //If not, show an error message
                .fail(function () {
                    $(switchThemesPopup)
                        .html('<div class="mailoptin-optin-themes-ajax-error">' + moContextualControlsLabels.ajaxError + switchThemesClose + '</div>')
                        .find('button')
                        .on('click', function () {
                            $(switchThemesPopup)
                                .removeClass('mo-change-theme-popup-show mo-change-theme-display-block')
                                .html(loader)
                        })
                })

        })
    });

})(wp.customize, jQuery);