/**
 * @var {object} mailoptin_globals
 */
define(['jquery', 'js.cookie', 'mailoptin_globals', 'pikaday', 'moModal', 'moExitIntent', 'moScrollTrigger', 'mc-groups-validation', 'recaptcha'],
    function ($, Cookies, mailoptin_globals, Pikaday) {
        "use strict";

        $.MailOptin = {

            // flag to detect if we've already loaded showads.js file to detect adblock
            is_adblock_script_loaded: false,
            /**
             * Is the current screen customizer preview?
             * @return {boolean}
             */
            is_customize_preview: (mailoptin_globals.is_customize_preview === 'true'),

            /**
             * Track optin conversion and impression.
             *
             * @param {string} optin_uuid
             */
            track_impression: function (optin_uuid) {
                // bail if this is customizer preview
                if ($.MailOptin.is_customize_preview === true || mailoptin_globals.disable_impression_tracking === 'true') return;

                var stat_data = {
                    optin_uuid: optin_uuid,
                    conversion_page: window.location.href,
                    referrer: document.referrer || ""
                };

                $.post(mailoptin_globals.mailoptin_ajaxurl.toString().replace('%%endpoint%%', 'track_optin_impression'), {stat_data: stat_data});
            }
        };

        var mailoptin_optin = {

            mailoptin_jq_plugin: function () {
                var self = this;
                $.fn.mailoptin = function (skip_display_checks) {
                    skip_display_checks = typeof skip_display_checks !== 'undefined' ? skip_display_checks : false;

                    var modal_options,
                        $optin_uuid,
                        $optin_type,
                        $optin_css_id,
                        optin_js_config,
                        test_mode;

                    $optin_uuid = this.attr('id');
                    $optin_type = this.attr('data-optin-type');
                    $optin_css_id = $optin_uuid + '_' + $optin_type;
                    optin_js_config = self.optin_js_config($optin_css_id);

                    if (typeof optin_js_config === 'undefined') return;

                    test_mode = ($.MailOptin.is_customize_preview === true) ? true : optin_js_config.test_mode;

                    // add the close-optin event handler. modal/lightbox has its own so skip.
                    if (this.hasClass('mo-optin-form-lightbox') === false) {
                        $(document).on('click.moOptin', 'a[rel~="moOptin:close"], .mo-close-optin', {
                                'optin_uuid': $optin_uuid,
                                'optin_type': $optin_type,
                                'optin_js_config': optin_js_config,
                                'self': self
                            }, self.close_optin
                        );
                    }

                    // remove the close optin event if we're in customizer.
                    if ($.MailOptin.is_customize_preview === true) {
                        $(document).off('submit.moOptinSubmit', 'form.mo-optin-form');
                        $(document).off('click.moOptinSubmit', '.mo-optin-form-submit-button');
                        $(document).off('click.moOptin', 'a[rel~="moOptin:close"]');
                        $(document).off('click.moOptin', '.mo-close-optin');
                    }

                    /** lightbox / modal */
                    if (this.hasClass('mo-optin-form-lightbox')) {
                        modal_options = {
                            optin_uuid: $optin_uuid,
                            bodyClose: optin_js_config.body_close,
                            keyClose: optin_js_config.body_close,
                            test_mode: test_mode,
                            iconClose: optin_js_config.icon_close,
                            onOpen: function () {
                                self.animate_optin_display.call(this, optin_js_config.effects);
                            },
                            onClose: function () {
                                self.set_cookie('exit', $optin_uuid, optin_js_config);
                            }
                        };

                        if ($.MailOptin.is_customize_preview === true) {
                            modal_options.keyClose = false;
                            modal_options.bodyClose = false;
                            modal_options.test_mode = true;
                        }

                        // merge modal specific object with that of optin js config
                        optin_js_config = $.extend({}, modal_options, optin_js_config);

                        self.process_optin_form_display.call(this, optin_js_config, 'lightbox', skip_display_checks);
                    }

                    /** Notification bar */
                    if (this.hasClass('mo-optin-form-bar')) {
                        // only one instance of top bar can show at a time.
                        if (self.is_flag_optin_type_active(optin_js_config, 'bar')) return;

                        self.process_optin_form_display.call(this, optin_js_config, 'bar', skip_display_checks);
                    }

                    /** Slide INs */
                    if (this.hasClass('mo-optin-form-slidein')) {
                        // only one instance of slidein type can shown at a time.
                        if (self.is_flag_optin_type_active(optin_js_config, 'slidein')) return;
                        self.process_optin_form_display.call(this, optin_js_config, 'slidein', skip_display_checks);
                    }

                    /** Sidebar */
                    if (this.hasClass('mo-optin-form-sidebar')) {
                        self.process_optin_form_display.call(this, optin_js_config, 'sidebar', skip_display_checks);
                    }

                    /** Inpost */
                    if (this.hasClass('mo-optin-form-inpost')) {
                        self.process_optin_form_display.call(this, optin_js_config, 'inpost', skip_display_checks);
                    }

                    // custom html conversion tracker
                    $(document).on('click', '.mo-trigger-conversion', function () {
                        // set cookie for this option conversion when button is clicked.
                        self.set_cookie('success', $optin_uuid, optin_js_config);
                        self.ga_event_tracking('conversion', optin_js_config);
                    });

                    // handle CTA button click if activated
                    if (self.is_defined_not_empty(optin_js_config.cta_display) && optin_js_config.cta_display === true && self.is_defined_not_empty(optin_js_config.cta_action)) {
                        // if cta action is to navigate
                        $(document).on('click', '#' + $optin_css_id + '_cta_button', function (e) {
                            e.preventDefault();
                            var optin_container = $(this).parents('.moOptinForm');

                            if (optin_js_config.cta_action === 'navigate_to_url' && self.is_defined_not_empty(optin_js_config.cta_navigate_url)) {
                                // bail if we are in customizer preview.
                                if ($.MailOptin.is_customize_preview === true) return;
                                // set cookie for this option conversion when button is clicked.
                                self.set_cookie('success', $optin_uuid, optin_js_config);
                                self.ga_event_tracking('conversion', optin_js_config);

                                window.location.assign(optin_js_config.cta_navigate_url);
                            } else if (optin_js_config.cta_action === 'reveal_optin_form') {
                                var cache = $('#' + $optin_css_id);
                                cache.find('.mo-optin-form-cta-button, .mo-optin-form-cta-wrapper').hide();
                                cache.find('.mo-optin-fields-wrapper').show();
                                cache.find('.mo-optin-form-submit-button').show();
                                $('#' + $optin_uuid).removeClass('mo-cta-button-flag');
                            } else if ($.inArray(optin_js_config.cta_action, ['close_optin', 'close_optin_reload_page']) !== -1) {
                                $.MoModalBox.close();
                                mailoptin_optin._close_optin(optin_container);

                                if (optin_js_config.cta_action === 'close_optin_reload_page') {
                                    window.location.reload();
                                }
                            } else {
                                console.warn('something went wrong.');
                            }
                            return false;
                        });
                    }

                    if (!$.MailOptin.is_customize_preview && self.is_adblock_rule_active(optin_js_config) === true && $.MailOptin.is_adblock_script_loaded === false) {
                        self.load_adblock_detect_script();
                        $.MailOptin.is_adblock_script_loaded = true;
                    }
                };

                $.fn.extend({
                    animateOptin: function (animationName) {
                        var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
                        this.addClass('MOanimated ' + animationName).one(animationEnd, function () {
                            $(this).removeClass('MOanimated ' + animationName);
                        });
                    }
                });
            },

            /**
             * Is after x seconds rule active?
             *
             * @param {object} optin_config
             * @returns {boolean}
             */
            is_after_x_seconds_active: function (optin_config) {
                return optin_config.x_seconds_status === true && optin_config.x_seconds_value !== undefined;
            },

            /**
             * Is after x seconds rule active?
             *
             * @param {object} optin_config
             * @returns {boolean}
             */
            is_after_x_scroll_active: function (optin_config) {
                return optin_config.x_scroll_status === true && optin_config.x_scroll_value !== undefined;
            },

            /**
             * Is after x page views rule active?
             *
             * @param {object} optin_config
             * @returns {boolean}
             */
            is_after_x_page_views_active: function (optin_config) {
                return optin_config.x_page_views_status === true &&
                    optin_config.x_page_views_condition !== undefined &&
                    optin_config.x_page_views_value !== undefined;
            },

            /**
             * Is exit intent rule active?
             *
             * @param {object} optin_config
             * @returns {boolean}
             */
            is_exit_intent_active: function (optin_config) {
                return optin_config.exit_intent_status === true;
            },

            load_adblock_detect_script: function () {
                var ad = document.createElement('script');
                ad.src = mailoptin_globals.public_js + '/showads.js';
                ad.async = true;

                // Attempt to append it to the <head>, otherwise append to the document.
                (document.getElementsByTagName('head')[0] || document.documentElement).appendChild(ad);
            },

            /**
             * Is New vs Returning rule active?
             *
             * @param {object} optin_config
             * @returns {boolean}
             */
            is_newvsreturn_rule_active: function (optin_config) {
                // no need checking if optin_config.newvsreturn_status_settings is not empty because
                // newvsreturn_status and newvsreturn_settings config are only exposed if the former is true
                // and latter not empty.
                return optin_config.newvsreturn_status === true;
            },

            /**
             * Is referrer detection active?
             *
             * @param {object} optin_config
             * @returns {boolean}
             */
            is_referrer_detection_rule_active: function (optin_config) {
                return optin_config.referrer_detection_status === true;
            },

            /**
             * Is Adblock rule active?
             *
             * @param {object} optin_config
             * @returns {boolean}
             */
            is_adblock_rule_active: function (optin_config) {
                // no need checking if optin_config.adblock_settings is not empty because
                // adblock_status and adblock_settings config are only exposed if the former is true
                // and latter not empty.
                return optin_config.adblock_status === true;
            },

            /**
             * Determine if optin should display or not.
             *
             * @param {object} optin_config
             *
             * @returns {boolean}
             */
            is_optin_visible: function (optin_config) {
                var $optin_uuid = optin_config.optin_uuid;
                // if global success cookie found, do not display any optin.
                if (optin_config.global_success_cookie > 0 && Cookies.get('mo_global_success_cookie')) return false;
                // if global interaction/exit cookie found, do not display any optin.
                if (optin_config.global_cookie > 0 && Cookies.get('mo_global_cookie')) return false;
                // if success cookie found for this optin, do not display it.
                if (Cookies.get('mo_success_' + $optin_uuid)) return false;
                // if exit cookie found for this optin, do not dispay it.
                if (Cookies.get('mo_' + $optin_uuid)) return false;

                return true;
            },

            /**
             * Determine if optin should display or not.
             *
             * @param {object} optin_config
             *
             * @returns {boolean}
             */
            split_test_cookie_test: function (optin_config) {

                if (optin_config.is_split_test === true) {

                    var flag = true,
                        self = mailoptin_optin,
                        optin_campaign_id = optin_config.optin_campaign_id;

                    if (optin_campaign_id in optin_config.split_test_variants) {

                        $.each(optin_config.split_test_variants, function (index, variant_config) {

                            if (self.is_optin_visible(variant_config) === false) {
                                flag = false;
                                return false; // break the loop
                            }
                        });

                        return flag;
                    }
                }

                return true;
            },

            /**
             * Handle display/showing of optin form.
             *
             * @param {object} optin_js_config for lightbox, this is modal_options.  others is optin_js_config
             * @param {string} optin_type type of optin
             * @param {boolean} skip_display_checks skip any display/cookie check
             */
            process_optin_form_display: function (optin_js_config, optin_type, skip_display_checks) {
                var self = mailoptin_optin;
                // we did this becos 'this' inside $(window).load will be wrong.
                var _this = this;

                if (self.is_adblock_rule_active(optin_js_config) === true) {
                    // we're gonna wait until page is loaded so we can detect if adblock is enabled or not
                    $(window).on('load', function () {
                        self.rule_base_show_optin_form.call(_this, optin_js_config, optin_type, skip_display_checks);
                    });
                } else {
                    self.rule_base_show_optin_form.call(_this, optin_js_config, optin_type, skip_display_checks);
                }
            },

            /**
             * Run through display ruleset and determine which to display
             *
             * @param {object} optin_config for lightbox, this is modal_options.  others is optin_js_config
             * @param {string} optin_type type of optin
             * @param {boolean} skip_display_checks skip any display/cookie check
             */
            rule_base_show_optin_form: function (optin_config, optin_type, skip_display_checks) {

                var self = mailoptin_optin;
                // we did this becos 'this' inside setTimeout() will be wrong.
                var _this = this;

                // if customizer, display immediately.
                if ($.MailOptin.is_customize_preview === true || optin_config.test_mode === true || skip_display_checks === true) {
                    return self.display_optin_form.call(_this, optin_config, optin_type, skip_display_checks);
                }

                // return if click launch status is activated for optin but the trigger isn't it.
                if (optin_config.click_launch_status === true && skip_display_checks === false) return;

                if (self.is_optin_visible(optin_config) === false) return;

                if (self.split_test_cookie_test(optin_config) === false) return;

                if (self.is_after_x_page_views_active(optin_config)) {
                    var x_page_views_condition = optin_config.x_page_views_condition;
                    var x_page_views_value = optin_config.x_page_views_value;

                    switch (x_page_views_condition) {
                        // for each condition, do the inverse return false if comparison is true.
                        case 'equals':
                            if (self.get_page_views() !== x_page_views_value) return;
                            break;
                        case 'more_than':
                            if (self.get_page_views() <= x_page_views_value) return;
                            break;
                        case 'less_than':
                            if (self.get_page_views() >= x_page_views_value) return;
                            break;
                        case 'at_least':
                            if (self.get_page_views() < x_page_views_value) return;
                            break;
                        case 'not_more_than':
                            if (self.get_page_views() > x_page_views_value) return;
                            break;
                    }
                }

                if (self.is_referrer_detection_rule_active(optin_config) === true) {
                    var remove_trailing_slash = function (url) {
                        return url.replace(/\/+$/, "");
                    };

                    var actual_referrer_url = document.referrer.toLowerCase() || false;

                    if (!actual_referrer_url) return;

                    var display_type = optin_config.referrer_detection_settings;
                    var referrers = optin_config.referrer_detection_values;

                    if (display_type === 'show_to') {
                        var is_display = false;
                        $.each(referrers, function (index, referrer) {
                            referrer = remove_trailing_slash(referrer).toLowerCase();
                            // if list of referrer entered by admin in MailOptin matches actual referral.
                            if (actual_referrer_url.indexOf(referrer) !== -1) {
                                is_display = true;
                                // return false to stop loop.
                                return false;
                            }
                        });
                    }

                    if (display_type === 'hide_from') {
                        var is_display = true;
                        $.each(referrers, function (index, referrer) {
                            referrer = remove_trailing_slash(referrer);
                            // if list of referrer entered by admin in MailOptin matches actual referral.
                            if (actual_referrer_url.indexOf(referrer) !== -1) {
                                is_display = false;
                                // return false to stop loop.
                                return false;
                            }
                        });
                    }

                    if (!is_display) return;
                }

                if (self.is_newvsreturn_rule_active(optin_config) === true) {
                    if (optin_config.newvsreturn_settings === "is_new" && self.visitor_is_returning()) return;
                    if (optin_config.newvsreturn_settings === "is_returning" && self.visitor_is_new()) return;
                }

                if (self.is_adblock_rule_active(optin_config) === true) {
                    if (optin_config.adblock_settings === "adblock_enabled" && self.isAdblockDisabled()) return;
                    if (optin_config.adblock_settings === "adblock_disabled" && self.isAdblockEnabled()) return;
                }

                // device detection rule
                if (typeof window.MobileDetect !== "undefined") {
                    var mdInstance = new MobileDetect(window.navigator.userAgent);

                    if (optin_config.device_targeting_hide_mobile === true) {
                        if (mdInstance.phone()) return;
                    }

                    if (optin_config.device_targeting_hide_tablet === true) {
                        if (mdInstance.tablet()) return;
                    }

                    if (optin_config.device_targeting_hide_desktop === true) {
                        if (!mdInstance.mobile()) return;
                    }
                }

                var wait_seconds = optin_config.x_seconds_value * 1000;
                var optin_scroll_percent = optin_config.x_scroll_value;

                // If all three rules are active, run the below shebang
                if (self.is_after_x_seconds_active(optin_config) === true &&
                    self.is_after_x_scroll_active(optin_config) === true &&
                    self.is_exit_intent_active(optin_config) === true) {
                    setTimeout(function () {
                        $.moScrollTrigger('enable');
                        $(document).on('moScrollTrigger', function (e, pctScrolled) {
                            if (pctScrolled >= optin_scroll_percent) {
                                $.moExitIntent('enable');
                                $(document).on("moExitIntent", function () {
                                    return self.display_optin_form.call(_this, optin_config, optin_type);
                                });
                            }
                        });

                    }, wait_seconds);

                    return;
                }

                // If only "is_after_x_scroll_active" and "is_exit_intent_active" rules are active, run the below shebang
                if (self.is_after_x_scroll_active(optin_config) === true &&
                    self.is_exit_intent_active(optin_config) === true) {

                    $.moScrollTrigger('enable');
                    $(document).on('moScrollTrigger', function (e, pctScrolled) {
                        if (pctScrolled >= optin_scroll_percent) {
                            $.moExitIntent('enable');
                            $(document).on("moExitIntent", function () {
                                return self.display_optin_form.call(_this, optin_config, optin_type);
                            });
                        }
                    });

                    return;
                }

                // If only "after_x_seconds" and "after_x_scroll" rules are active, run the below shebang
                if (self.is_after_x_seconds_active(optin_config) === true &&
                    self.is_after_x_scroll_active(optin_config) === true) {

                    setTimeout(function () {
                        $.moScrollTrigger('enable');
                        $(document).on('moScrollTrigger', function (e, pctScrolled) {
                            if (_this.hasClass('si-open') === false) {
                                if (pctScrolled >= optin_scroll_percent) {
                                    _this.addClass('si-open');
                                    return self.display_optin_form.call(_this, optin_config, optin_type);
                                }
                            }
                        });

                    }, wait_seconds);

                    return;
                }

                // If only "after_x_seconds" and "exit intent" rules are active, run the below shebang
                if (self.is_after_x_seconds_active(optin_config) === true &&
                    self.is_exit_intent_active(optin_config) === true) {
                    setTimeout(function () {
                        $.moExitIntent('enable');
                        $(document).on("moExitIntent", function () {
                            return self.display_optin_form.call(_this, optin_config, optin_type);
                        });

                    }, wait_seconds);

                    return;
                }

                // If only "after_x_seconds" rules is active, run the below shebang
                if (self.is_after_x_seconds_active(optin_config) === true) {
                    setTimeout(function () {
                        return self.display_optin_form.call(_this, optin_config, optin_type);
                    }, wait_seconds);

                    return;
                }

                // If only "after x scroll" rules is active, run the below shebang
                if (self.is_after_x_scroll_active(optin_config)) {
                    $.moScrollTrigger('enable');
                    $(document).on('moScrollTrigger', function (e, pctScrolled) {
                        if (_this.hasClass('si-open') === false) {
                            if (pctScrolled >= optin_scroll_percent) {
                                _this.addClass('si-open');
                                return self.display_optin_form.call(_this, optin_config, optin_type);
                            }
                        }
                    });

                    return;
                }

                // If only "exit intent" rules is active, run the below shebang
                if (self.is_exit_intent_active(optin_config)) {
                    $.moExitIntent('enable');
                    $(document).on("moExitIntent", function () {
                        return self.display_optin_form.call(_this, optin_config, optin_type);
                    });

                    return;
                }

                return self.display_optin_form.call(_this, optin_config, optin_type);
            },

            /**
             * Optin-type agnostic helper function to display optin form.
             *
             * @param {object} optin_config
             * @param {string} optin_type
             * @param {boolean} skip_display_checks
             */
            display_optin_form: function (optin_config, optin_type, skip_display_checks) {

                // bail if required parameter is undefined
                if (typeof optin_type === 'undefined' || typeof optin_type === 'undefined') return;

                var self = mailoptin_optin;

                // do cookie checking if we are not in customizer mode and not test mode is active.
                if ($.MailOptin.is_customize_preview === false && optin_config.test_mode === false && skip_display_checks !== true) {
                    if (self.is_optin_visible(optin_config) === false) return;

                    if (self.split_test_cookie_test(optin_config) === false) return;
                }

                if (optin_type !== undefined && optin_type === 'lightbox') {
                    // trigger optin show event.
                    $(document.body).on($.MoModalBox.OPEN, function (e, elm, optin_config) {
                        $(this).trigger('moOptin:show', [optin_config.optin_uuid, optin_config]);
                    });

                    this.MoModalBox(optin_config);
                    // stop further execution
                    return;
                }

                self.animate_optin_display.call(this, optin_config.effects);
                self.flag_optin_type_displayed(optin_config, optin_type);

                if (optin_type === 'bar' && optin_config.bar_position === 'top') {

                    var originalMargin = parseFloat($(document.body).css('margin-top')),
                        optin_uuid = optin_config.optin_uuid;

                    $(window).on('resize.MoBarTop', function () {
                        var cache = $('#' + optin_uuid);
                        var mHeight = cache.outerHeight();

                        if ($(window).width() <= 600) {
                            mHeight -= $("#wpadminbar").outerHeight();
                        }

                        mHeight = $.MailOptin.activeBarHeight = originalMargin + mHeight;

                        $(document.body).css('margin-top', originalMargin + mHeight + 'px');
                    });

                    // init
                    $(window).resize();
                }

                this.show();
                $(this).trigger('moOptin:show', [optin_config.optin_uuid, optin_config]);
            },

            /**
             * Set flag when an optin-type is displayed to prevent multiple optin-type instance showing.
             *
             * @param {object} optin_config
             * @param {string} optin_type
             */
            flag_optin_type_displayed: function (optin_config, optin_type) {
                if (optin_type === 'bar') {
                    var bar_position = optin_config.bar_position;
                    $.MailOptin['isActiveMOBar_' + bar_position] = true;
                }

                if (optin_type === 'slidein') {
                    var slidein_position = optin_config.slidein_position;
                    $.MailOptin['isActiveMOSlidein_' + slidein_position] = true;
                }
            },

            /**
             * Set flag when an optin-type is closed.
             *
             * @param {object} optin_config
             * @param {string} optin_type
             */
            flag_optin_type_close: function (optin_config, optin_type) {
                if (optin_type === 'bar') {
                    var bar_position = optin_config.bar_position;
                    $.MailOptin['isActiveMOBar_' + bar_position] = false;
                }

                if (optin_type === 'slidein') {
                    var slidein_position = optin_config.slidein_position;
                    $.MailOptin['isActiveMOSlidein_' + slidein_position] = false;
                }
            },

            is_flag_optin_type_active: function (optin_config, optin_type) {
                if (optin_type === 'bar') {
                    var bar_position = optin_config.bar_position;
                    return $.MailOptin['isActiveMOBar_' + bar_position] === true;
                }

                if (optin_type === 'slidein') {
                    var slidein_position = optin_config.slidein_position;
                    return $.MailOptin['isActiveMOSlidein_' + slidein_position] === true;
                }
            },

            /**
             * Closes any displayed optin. well doesn't for modals as they have theirs.
             */
            close_optin: function (e) {
                e.preventDefault();

                var optin_container = $(this).parents('.moOptinForm');
                var optin_uuid = optin_container.attr('id');
                var optin_type = optin_container.attr('data-optin-type');
                var optin_config = mailoptin_optin.optin_js_config(optin_uuid);

                mailoptin_optin._close_optin(optin_container);

                // cleanup for on-scroll optin to prevent from triggering all the time
                optin_container.removeClass('si-open');

                mailoptin_optin.set_cookie('exit', optin_uuid, optin_config);
                mailoptin_optin.flag_optin_type_close(optin_config, optin_type);
            },

            /**
             * Actual func to close non-modal optin forms.
             * @param optin_container
             * @private
             */
            _close_optin: function (optin_container) {
                optin_container.fadeOut(400, function () {
                    $(this).trigger('moOptin:close', [this]);

                    var optin_uuid = optin_container.attr('id');
                    var optin_config = mailoptin_optin.optin_js_config(optin_uuid);

                    if (optin_config.optin_type === 'bar' && optin_config.bar_position === 'top') {
                        var mt = parseFloat($(document.body).css('margin-top'));
                        $(document.body).css('margin-top', mt - $.MailOptin.activeBarHeight + 'px');
                        delete $.MailOptin.activeBarHeight;
                        $(window).off('resize.MoBarTop');
                    }
                });
            },

            /**
             * Track number of page views.
             */
            track_page_views: function () {
                var prev_count = Cookies.get('mo_page_views_counter');
                var count = (prev_count === undefined) ? 0 : prev_count;

                // cookie expiration is missing thus making it a session cookie.
                Cookies.set('mo_page_views_counter', ++count);
            },

            set_visitor_cookies: function () {
                // Set two cookies: persistent visitor and session visitor.
                // If persistent visitor already exists, don't set anything.
                // This is how we determine new vs. returning visitors.
                // basically the session cookie is to keep identifying the visitor until session expires
                // then next visit, they become a returning visitor.
                if (!Cookies.get('mo_has_visited')) {
                    Cookies.set('mo_is_new', 'true');
                    Cookies.set('mo_has_visited', 'true', {expires: 3999});
                }
            },

            isAdblockEnabled: function () {
                return typeof mailoptin_no_adblock_detected === 'undefined';
            },

            isAdblockDisabled: function () {
                return typeof mailoptin_no_adblock_detected !== 'undefined';
            },

            visitor_is_new: function () {
                return Cookies.get('mo_has_visited') === 'true' && Cookies.get('mo_is_new') === 'true';
            },

            visitor_is_returning: function () {
                return Cookies.get('mo_has_visited') === 'true' && !Cookies.get('mo_is_new');
            },

            /**
             * Get number of page views.
             */
            get_page_views: function () {
                return Number(Cookies.get('mo_page_views_counter'));
            },

            /**
             * Animate optin form display
             */
            animate_optin_display: function (effects) {
                if ((effects !== '') || (typeof effects !== 'undefined')) {
                    this.find('.mo-optin-form-wrapper').animateOptin(effects);
                }
            },

            /**
             * Return the configuration in Javascript of an optin.
             * @param {string} optin_css_id could be optin campaign ID or css ID (optin id with optin type joined by "_")
             * @returns {object}
             */
            optin_js_config: function (optin_css_id) {
                return window[optin_css_id];
            },

            /** @todo move this into its own class/object such that all methods/functions utilized when conversion happens have access to properties and data without passing the data/properites via arguments
             * @see https://www.phpied.com/3-ways-to-define-a-javascript-class/
             */
            optin_conversion: function () {

                var optin_data, optin_container, $optin_uuid, $optin_type, $optin_css_id, optin_js_config, self;

                self = this;

                // if we are in customizer preview, bail.
                if ($.MailOptin.is_customize_preview === true) return;

                var process_form = function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    optin_container = $(this).parents('.moOptinForm');
                    $optin_uuid = optin_container.attr('id');
                    $optin_type = optin_container.attr('data-optin-type');
                    $optin_css_id = $optin_uuid + '_' + $optin_type;
                    optin_js_config = self.optin_js_config($optin_css_id);

                    self.hide_optin_error($optin_uuid, optin_container);

                    // data variable is only populated if validation passes.
                    if (self.validate_optin_form_fields($optin_css_id, optin_js_config, optin_container)) {

                        // loop over form fields and create and object with key to the field name and value the field value.
                        var all_form_fields_and_values = $('form#' + $optin_css_id + '_form').serializeArray().reduce(function (obj, item) {
                            if (item.name.indexOf('[]') !== -1) {
                                var item_name = item.name.replace('[]', '');
                                if (typeof obj[item_name] === 'undefined') {
                                    obj[item_name] = [];
                                    obj[item_name].push(item.value);
                                } else {
                                    obj[item_name].push(item.value);
                                }
                            } else {
                                obj[item.name] = item.value;
                            }
                            return obj;
                        }, {});

                        optin_data = $.extend({}, all_form_fields_and_values, {
                            optin_uuid: $optin_uuid,
                            optin_campaign_id: optin_js_config.optin_campaign_id,
                            email: $('input#' + $optin_css_id + '_email_field', optin_container).val(),
                            name: $('input#' + $optin_css_id + '_name_field', optin_container).val(),
                            _mo_timestamp: $('input#' + $optin_css_id + '_honeypot_timestamp').val(),
                            user_agent: navigator.userAgent,
                            conversion_page: window.location.href,
                            referrer: document.referrer || ""
                        });

                        self.addProcessingOverlay.call(optin_container);

                        self.subscribe_to_email_list(optin_data, optin_container, optin_js_config, $optin_type);
                    }
                };

                // this is important so form can be processed when say enter button is pressed to submit form.
                $(document).on('submit.moOptinSubmit', 'form.mo-optin-form', process_form);
                // added this option because there was an issue where form submit event didn't work.
                $(document).on('click.moOptinSubmit', '.mo-optin-form-submit-button', process_form);
            },

            /**
             * Add overlay over optin lighbox/modal that shows spinner and success message.
             *
             */
            addProcessingOverlay: function () {
                this.find('.mo-optin-spinner').show();
            },

            /**
             * Remove overlay over optin lighbox/modal that shows spinner and success message.
             *
             */
            removeProcessingOverlay: function () {
                this.find('.mo-optin-spinner').hide();
            },

            /**
             * Add close icon to processing overlay modal after successful optin.
             */
            addSuccessCloseIcon: function () {
                this.find('.mo-optin-spinner').after('<a href="#" class="mo-optin-success-close" rel="moOptin:close">×</a>');
            },

            /**
             * Remove close icon to processing overlay modal after successful optin.
             */
            removeSuccessCloseIcon: function () {
                this.find('.mo-optin-success-close').remove();
            },

            /**
             * overlay over optin lighbox/modal that shows spinner and success message.
             *
             */
            displaySuccessContent: function () {
                // display the success container div.
                this.find('.mo-optin-success-msg').show();
            },

            /**
             * Remove spinner.
             */
            removeSpinner: function () {
                // remove spinner gif icon
                this.find('.mo-optin-spinner').css('background-image', 'none');
            },

            /**
             * Display error message from optin.
             *
             * @param {string} error_message
             */
            displayErrorMessage: function (error_message) {
                this.find('.mo-optin-error').html(error_message).show();
            },

            /**
             * Set conversion / close cookie for campaign.
             *
             * @param {string} type type of cookie to set. Can be exit or success cookie.
             * @param {string} optin_uuid
             * @param {object} optin_js_config
             */
            set_cookie: function (type, optin_uuid, optin_js_config) {
                // default test mode to false.
                var test_mode = optin_js_config.test_mode || false;

                var cookie = optin_js_config.cookie;
                if (type == 'success') {
                    var cookie = optin_js_config.success_cookie;
                }

                // if type is exit cookie, return empty. if it's success, it get appended to 'mo_' when setting cookie key.
                type = type === 'exit' ? '' : type + '_';

                if (!test_mode) {

                    Cookies.set('mo_' + type + optin_uuid, true, {expires: cookie});
                    // set either global exit or success cookie depending on the context in which set_cookie() is called.
                    // cookie expiration could be optin_js_config.global_cookie or optin_js_config.global_success_cookie
                    // no need to check if cookie value is not zero(0) before setting cookie because a cookie set to expires in 0 days
                    // returns undefined when tried to be gotten.
                    Cookies.set('mo_global_' + type + 'cookie', true, {expires: optin_js_config['global_' + type + 'cookie']});
                }
            },

            /**
             * POST collected optin data to appropriate connected email list.
             *
             * @param {mixed} optin_data
             * @param {object} optin_container jQuery object of the parent div container
             * @param {object} optin_js_config optin JS configuarations
             * @param {object} $optin_type optin optin type of the optin form being looped.
             */
            subscribe_to_email_list: function (optin_data, optin_container, optin_js_config, $optin_type) {
                var self = this;

                $.post(mailoptin_globals.mailoptin_ajaxurl.toString().replace('%%endpoint%%', 'subscribe_to_email_list'),
                    {
                        optin_data: optin_data
                    },
                    function (response) {
                        if (!$.isEmptyObject(response) && 'success' in response) {
                            if (response.success === true) {

                                $(document.body).trigger('moOptinConversion', [optin_container, optin_js_config, optin_data]);

                                // set cookie for this option conversion
                                self.set_cookie('success', optin_data.optin_uuid, optin_js_config);

                                // do not include success icon if icon_close (close icon automatically attached to lightbox) is set to true.
                                // icon_close config is always false for none lightbox optin forms. see ./Core/src/OptinForms/AbstractOptinForm.php LN497

                                // Because JavaScript treats 0 as loosely equal to false (i.e. 0 == false, but 0 !== false),
                                // to check for the presence of value within array, you need to check if it's not equal to (or greater than) -1.

                                /**@todo revisit this when new optin type is added */
                                if ($.inArray($optin_type, ['lightbox', 'bar', 'slidein']) !== -1 && optin_js_config.icon_close !== true) {
                                    self.addSuccessCloseIcon.call(optin_container);
                                }

                                self.displaySuccessContent.call(optin_container);

                                self.removeSpinner.call(optin_container);
                            } else {
                                self.removeProcessingOverlay.call(optin_container);
                                self.removeSuccessCloseIcon.call(optin_container);
                                self.displayErrorMessage.call(optin_container, response.message);
                            }
                        } else {
                            self.displayErrorMessage.call(optin_container, optin_js_config.unexpected_error);
                            self.removeProcessingOverlay.call(optin_container);
                            self.removeSuccessCloseIcon.call(optin_container);
                        }
                    },
                    'json'
                );
            },

            /**
             * Validate name and email fields.
             *
             * @param {string} $optin_css_id optin CSS ID
             * @param {object} optin_js_config optin js config
             * @param {object} optin_container
             * @returns {boolean}
             */
            validate_optin_form_fields: function ($optin_css_id, optin_js_config, optin_container) {

                var namefield_error = optin_js_config.name_missing_error,
                    emailfield_error = optin_js_config.email_missing_error,
                    honeypot_error = optin_js_config.honeypot_error,
                    note_acceptance_error = optin_js_config.note_acceptance_error,
                    custom_field_required_error = optin_js_config.custom_field_required_error,

                    self = this,
                    name_field = $('#' + $optin_css_id + '_name_field:visible', optin_container),
                    email_field = $('#' + $optin_css_id + '_email_field:visible', optin_container),
                    acceptance_checkbox = $('#' + $optin_css_id + ' .mo-acceptance-checkbox', optin_container),

                    honeypot_email_field = $('#' + $optin_css_id + '_honeypot_email_field', optin_container).val(),
                    honeypot_website_field = $('#' + $optin_css_id + '_honeypot_website_field', optin_container).val(),
                    response = true;

                // Throw error if either of the honeypot fields are filled.
                if (honeypot_email_field.length > 0 || honeypot_website_field.length > 0) {
                    self.display_optin_error.call(undefined, $optin_css_id, honeypot_error, optin_container);
                    response = false;
                }

                $('#' + $optin_css_id + ' .mo-optin-form-custom-field', optin_container).each(function () {
                    var cache = $(this),
                        field_id = $(this).data('field-id'),
                        required_field_bucket = optin_js_config.required_custom_fields,
                        cache_value = cache.val();

                    if (cache.find('input[type=radio]').length > 0) {
                        cache_value = cache.find('input[type=radio]:checked').length === 0 ? '' : cache.find('input[type=radio]:checked').val();
                    }

                    if (cache.find('input[type=checkbox]').length > 0) {
                        cache_value = cache.find('input[type=checkbox]:checked').length === 0 ? '' : cache.find('input[type=checkbox]:checked').val();
                    }

                    if ($.inArray(field_id, required_field_bucket) !== -1 && cache_value === "") {
                        self.display_optin_error.call(cache, $optin_css_id, custom_field_required_error, optin_container);
                        response = false;
                    }
                });

                // if this is an email field, validate that the email address.
                if (email_field.length > 0) {
                    if (self.isValidEmail(email_field.val()) === false) {
                        self.display_optin_error.call(email_field, $optin_css_id, emailfield_error, optin_container);
                        response = false;
                    }
                }

                // if this is a name field, check if the field isn't empty.
                if (optin_js_config.name_field_required === true && name_field.length > 0) {
                    if (name_field.val() === "") {
                        self.display_optin_error.call(name_field, $optin_css_id, namefield_error, optin_container);
                        response = false;
                    }
                }

                // we are doing a return here to ensure core validation has passed before hooked validations.
                if (response === false) return response;

                if (acceptance_checkbox.length > 0) {
                    if (acceptance_checkbox[0].checked === false) {
                        self.display_optin_error.call(undefined, $optin_css_id, note_acceptance_error, optin_container);
                        return false;
                    }
                }

                var added_validation = $(document.body).triggerHandler('mo_validate_optin_form_fields', [self, $optin_css_id, optin_js_config]);

                if (added_validation === false) response = false;

                return response;
            },

            /**
             * Output an optin error with the field highlighted red.
             *
             * @param {string} $optin_css_id optin CSS ID
             * @param {object} optin_container
             * @param {string} error
             */
            display_optin_error: function ($optin_css_id, error, optin_container) {
                if (this !== undefined) {
                    this.css("-webkit-box-shadow", "inset 0px 0px 0px 2px #f45a4a");
                    this.css("-moz-box-shadow", "inset 0px 0px 0px 2px #f45a4a");
                    this.css("box-shadow", "inset 0px 0px 0px 2px #f45a4a");
                }

                var mo_optin_error_text = $('div#' + $optin_css_id + ' .mo-optin-error', optin_container);
                if (typeof error !== 'undefined' && typeof mo_optin_error_text !== 'undefined' && mo_optin_error_text.length > 0) {
                    if (typeof error == "string") {
                        mo_optin_error_text.text(error).show();
                    }
                }
            },

            /**
             * Hide optin error including removing the red border.
             *
             * @param {string} $optin_css_id optin CSS ID
             * @param {object} optin_container
             */
            hide_optin_error: function ($optin_css_id, optin_container) {
                var input_fields = $('.mo-optin-field', optin_container);
                $('.mo-optin-error', optin_container).hide();
                input_fields.css('-webkit-box-shadow', '');
                input_fields.css('-moz-box-shadow', '');
                input_fields.css('box-shadow', '');
            },

            /**
             * Check if email address is valid.
             *
             * @param {string} email
             * @returns {boolean}
             */
            isValidEmail: function (email) {
                return (new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i)).test(email);
            },

            ga_event_tracking: function (action, optin_js_config) {

                action = action || 'impression';

                if (mailoptin_optin.is_defined_not_empty(optin_js_config.ga_active) === false) return;

                if (typeof ga !== "function") return;

                ga(function () {

                    var trackingId = ga.getAll()[0].get('trackingId');

                    if (mailoptin_optin.is_defined_not_empty(trackingId) === false) return;

                    ga('create', trackingId, 'auto', 'moTracker');

                    ga('moTracker.send', 'event', optin_js_config.optin_campaign_name, action, optin_js_config.optin_uuid, {
                        nonInteraction: true
                    });
                });
            },

            /**
             * Handle after conversion/success actions
             * @param e
             * @param optin_container
             * @param optin_js_config
             * @param optin_data
             */
            success_action_after_conversion: function (e, optin_container, optin_js_config, optin_data) {
                var success_action = optin_js_config.success_action;
                var redirect_url_val = optin_js_config.redirect_url_value;
                var success_js_script = optin_js_config.success_js_script;
                var is_success_js_script = typeof success_js_script !== 'undefined' && success_js_script !== '';
                var lead_data = {};

                lead_data.mo_name = lead_data.mo_email = '';

                if (mailoptin_optin.is_defined_not_empty(optin_data.name)) {
                    lead_data.mo_name = optin_data.name;
                }

                if (mailoptin_optin.is_defined_not_empty(optin_data.email)) {
                    lead_data.mo_email = optin_data.email;
                }

                // track GA
                mailoptin_optin.ga_event_tracking('conversion', optin_js_config);

                // if we have a JS success script, trigger it.
                if (is_success_js_script === true) {
                    if (success_js_script.indexOf('<script') === -1) {
                        success_js_script = '<script type="text/javascript">' + success_js_script + '</script>';
                    }

                    success_js_script = success_js_script.replace(/\[EMAIL\]/gi, lead_data.mo_email).replace(/\[NAME\]/gi, lead_data.mo_name);

                    $(optin_container).append(success_js_script);
                }

                if (typeof success_action !== 'undefined' && $.inArray(success_action, ['close_optin', 'redirect_url', 'close_optin_reload_page']) !== -1) {

                    setTimeout(function () {
                        $.MoModalBox.close();
                        mailoptin_optin._close_optin(optin_container);

                        if (success_action === 'close_optin_reload_page') {
                            return window.location.reload();
                        }

                        if (success_action === 'redirect_url' && typeof redirect_url_val !== 'undefined' && redirect_url_val !== '') {
                            if (typeof optin_js_config.pass_lead_data !== 'undefined' && true === optin_js_config.pass_lead_data) {
                                redirect_url_val = mailoptin_optin.add_query_args(redirect_url_val, lead_data);
                            }

                            window.location.assign(redirect_url_val);
                        }

                    }, 1000);
                }
            },

            /**
             * All event subscription / listener should go here.
             */
            eventSubscription: function () {
                // track impression for optin form other than modals
                $(document.body).on('moOptin:show', function (e, optin_uuid, optin_js_config) {
                    $.MailOptin.track_impression(optin_uuid);
                    // track GA
                    mailoptin_optin.ga_event_tracking('impression', optin_js_config);
                });

                // success actions
                $(document.body).on('moOptinConversion', this.success_action_after_conversion);
            },

            add_query_args: function (uri, params) {
                var separator = uri.indexOf('?') !== -1 ? '&' : '?';
                for (var key in params) {
                    if (params.hasOwnProperty(key)) {
                        uri += separator + key + '=' + params[key];
                        separator = '&';
                    }
                }
                return uri;
            },

            /**
             * Initialize optin event handlers.
             */
            initOptinForms: function () {

                $(".moOptinForm").each(function (index, element) {
                    $(element).mailoptin();
                });

                // click launch trigger
                $('.mailoptin-click-trigger').click(function (event) {
                    event.preventDefault();

                    var optin_uuid = $(this).data('optin-uuid') || $(this).attr('id');

                    if (typeof optin_uuid !== 'undefined') {

                        var selector = [
                            "#" + optin_uuid + ".mo-optin-form-lightbox",
                            "#" + optin_uuid + ".mo-optin-form-bar",
                            "#" + optin_uuid + ".mo-optin-form-slidein"
                        ];

                        $(selector.join(',')).mailoptin(true);
                    }
                });
            },

            is_scheduled_for_display: function () {

                if ($.MailOptin.is_customize_preview === true) return;

                $('.moOptinForm').each(function () {

                    var optin_uuid = $(this).attr('id');
                    var optin_js_config = mailoptin_optin.optin_js_config(optin_uuid);

                    var schedule_status = optin_js_config.schedule_status;
                    var schedule_start = optin_js_config.schedule_start;
                    var schedule_end = optin_js_config.schedule_end;
                    var schedule_timezone = optin_js_config.schedule_timezone;

                    // if we have a JS success script, trigger it.
                    if (mailoptin_optin.is_defined_not_empty(schedule_status) &&
                        mailoptin_optin.is_defined_not_empty(schedule_start) &&
                        mailoptin_optin.is_defined_not_empty(schedule_end) &&
                        mailoptin_optin.is_defined_not_empty(schedule_timezone)
                    ) {
                        var d = new Date(), timezone_offset, now, start, end, result;

                        if (schedule_timezone === 'visitors_local_time') {
                            // d.getTimezoneOffset is in minutes. so 60 * 1000 converts it to milliseconds
                            timezone_offset = d.getTimezoneOffset() * 60 * 1000;
                        } else {
                            // convert timezone offset in seconds to milliseconds
                            timezone_offset = schedule_timezone * 1000;
                        }

                        // getTime return time in UTC/GMT
                        now = d.getTime();
                        // we substracting time offset to convert the time to UTC/GMT
                        start = Date.parse(schedule_start + ' GMT') - timezone_offset;
                        end = Date.parse(schedule_end + ' GMT') - timezone_offset;

                        // return true of optin should display or false otherwise
                        result = (now >= start && now <= end);

                        if (result === false) {
                            $(this).remove();
                        } else {
                            $(this).mailoptin();
                        }
                    }
                });
            },

            /**
             * Check if value is defined and not empty.
             *
             * @param {mixed} val
             *
             * @returns {boolean}
             */
            is_defined_not_empty: function (val) {
                return (typeof val !== 'undefined' && val !== '');
            },

            init_date_picker: function () {
                $('.mo-optin-form-custom-field.date-field').each(function () {
                    var currentYr = (new Date()).getFullYear();
                    var range = 150;
                    var minYear = currentYr - range;
                    new Pikaday({
                        field: this,
                        minDate: new Date(minYear, 0),
                        maxDate: new Date(currentYr + range, 0),
                        yearRange: range + range,
                        toString: function (date, format) {
                            var day = ('0' + date.getDate()).slice(-2);
                            var month = ('0' + (date.getMonth() + 1)).slice(-2);
                            var year = date.getFullYear();
                            return year + '-' + month + '-' + day;
                        }
                    });
                });
            },

            /**
             * Initialize class
             */
            init: function () {
                var _this = this;
                // don't wait for dom to be loaded first. start tracking asap.
                _this.track_page_views();
                _this.set_visitor_cookies();
                $(function () {
                    _this.eventSubscription();
                    _this.mailoptin_jq_plugin();
                    _this.is_scheduled_for_display();
                    _this.initOptinForms();
                    _this.optin_conversion();
                    _this.init_date_picker();

                    $(document.body).trigger('mo-mailoptinjs-loaded')
                });
            }
        };

        mailoptin_optin.init();
    });