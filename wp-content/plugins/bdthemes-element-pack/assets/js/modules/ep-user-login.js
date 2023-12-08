/**
 * Start user login widget script
 */

( function ($, elementor) {

        'use strict';

        window.is_fb_loggedin = false;
        window.is_google_loggedin = false;

        var widgetUserLoginForm = {
            loginFormSubmission: function (login_form) {
                var redirect_url = login_form.find('.redirect_after_login').val();

                $.ajax({
                    type     : 'POST',
                    dataType : 'json',
                    url      : element_pack_ajax_login_config.ajaxurl,
                    data     : login_form.serialize(),
                    beforeSend: function (xhr) {
                        bdtUIkit.notification({
                            message: '<div bdt-spinner></div> ' + element_pack_ajax_login_config.loadingmessage,
                            timeout: false
                        });
                    },
                    success: function (data) {
                        var recaptcha_field = login_form.find('.element-pack-google-recaptcha');
                        if (recaptcha_field.length > 0) {
                            var recaptcha_id = recaptcha_field.attr('data-widgetid');
                            grecaptcha.reset(recaptcha_id);
                            grecaptcha.execute(recaptcha_id);
                        }

                        if (data.loggedin == true) {
                            bdtUIkit.notification.closeAll();
                            bdtUIkit.notification({
                                message : '<span bdt-icon=\'icon: check\'></span> ' + data.message,
                                status  : 'primary'
                            });
                            document.location.href = redirect_url;
                        } else {
                            bdtUIkit.notification.closeAll();
                            bdtUIkit.notification({
                                message : '<div class="bdt-flex"><span bdt-icon=\'icon: warning\'></span><span>' + data.message + '</span></div>',
                                status  : 'warning'
                            });
                        }
                    },
                    error: function (data) {
                        bdtUIkit.notification.closeAll();
                        bdtUIkit.notification({
                            message : '<span bdt-icon=\'icon: warning\'></span>' + element_pack_ajax_login_config.unknownerror,
                            status  : 'warning'
                        });
                    }
                });
            },
            get_facebook_user_data: function (widget_wrapper) {
                var redirect_url = widget_wrapper.find('.redirect_after_login').val();

                FB.api('/me', {fields: 'id, name, first_name, last_name, email, link, gender, locale, picture'},
                    function (response) {

                        var userID = FB.getAuthResponse()['userID'];
                        var access_token = FB.getAuthResponse()['accessToken'];

                        window.is_fb_loggedin = true;

                        var fb_data = {
                            'id'         : response.id,
                            'name'       : response.name,
                            'first_name' : response.first_name,
                            'last_name'  : response.last_name,
                            'email'      : response.email,
                            'link'       : response.link,
                        };

                        $.ajax({
                            url: window.ElementPackConfig.ajaxurl,
                            method: 'post',
                            data: {
                                action          : 'element_pack_social_facebook_login',
                                data            : fb_data,
                                method          : 'post',
                                dataType        : 'json',
                                userID          : userID,
                                security_string : access_token,
                                'lang': element_pack_ajax_login_config.language
                            },
                            dataType: 'json',
                            beforeSend: function (xhr) {
                                bdtUIkit.notification({
                                    message: '<div bdt-spinner></div> ' + element_pack_ajax_login_config.loadingmessage,
                                    timeout: false
                                });
                            },
                            success: function (data) {
                                if (data.success === true) {
                                    if (undefined === redirect_url) {
                                        location.reload();
                                    } else {
                                        window.location = redirect_url;
                                    }
                                } else {
                                    location.reload();
                                }
                            },
                            complete: function (xhr, status) {

                                bdtUIkit.notification.closeAll();
                            }

                        });

                    });
            },

            load_recaptcha: function () {
                var reCaptchaFields = $('.element-pack-google-recaptcha'), widgetID;

                if (reCaptchaFields.length > 0) {
                    reCaptchaFields.each(function () {
                        var self = $(this),
                            attrWidget = self.attr('data-widgetid');
                        // alert(self.data('sitekey'))
                        // Avoid re-rendering as it's throwing API error
                        if (( typeof attrWidget !== typeof undefined && attrWidget !== false )) {
                            return;
                        } else {
                            widgetID = grecaptcha.render($(this).attr('id'), {
                                sitekey: self.data('sitekey'),
                                callback: function (response) {
                                    if (response !== '') {
                                        self.append(jQuery('<input>', {
                                            type  : 'hidden',
                                            value : response,
                                            class : 'g-recaptcha-response'
                                        }));
                                    }
                                }
                            });
                            self.attr('data-widgetid', widgetID);
                        }
                    });
                }
            }

        };

        window.onLoadElementPackLoginCaptcha = widgetUserLoginForm.load_recaptcha;

        var widgetUserLoginFormHandler = function ($scope, $) {
            var widget_wrapper  = $scope.find('.bdt-user-login');
            var login_form      = $scope.find('form.bdt-user-login-form');
            var recaptcha_field = $scope.find('.element-pack-google-recaptcha');
            var fb_button       = widget_wrapper.find('.fb_btn_link');
            var google_button   = widget_wrapper.find('#google_btn_link');
            var redirect_url    = widget_wrapper.find('.redirect_after_login').val();

            if (login_form.length > 0) {
                login_form.on('submit', function (e) {
                    e.preventDefault();
                    widgetUserLoginForm.loginFormSubmission(login_form);
                });
            }

            if (elementorFrontend.isEditMode() && undefined === recaptcha_field.attr('data-widgetid')) {
                onLoadElementPackLoginCaptcha();
            }

            if (recaptcha_field.length > 0) {
                grecaptcha.ready(function () {
                    var recaptcha_id = recaptcha_field.attr('data-widgetid');
                    grecaptcha.execute(recaptcha_id);
                });
            }

            if (fb_button.length > 0) {
                /**
                 * Login with Facebook.
                 *
                 */
                // Fetch the user profile data from facebook.

                fb_button.on('click', function () {
                    if (!is_fb_loggedin) {
                        FB.login(function (response) {
                            if (response.authResponse) {
                                // Get and display the user profile data.
                                widgetUserLoginForm.get_facebook_user_data(widget_wrapper);
                            } else {
                                // $scope.find( '.status' ).addClass( 'error' ).text( 'User cancelled login or did not fully authorize.' );
                            }
                        }, {scope: 'email'});
                    }

                });
            }


            /** google */
            if (google_button.length > 0) {

                var client_id = google_button.data('clientid');

                /**
                 * Login with Google.
                 */
                gapi.load('auth2', function () {
                    // Retrieve the singleton for the GoogleAuth library and set up the client.
                    var auth2 = gapi.auth2.init({
                        client_id: client_id,
                        cookiepolicy: 'single_host_origin',
                    });

                    auth2.attachClickHandler('google_btn_link', {},
                        function (googleUser) {

                            var profile = googleUser.getBasicProfile();
                            var name    = profile.getName();
                            var email   = profile.getEmail();

                            if (window.is_google_loggedin) {

                                var id_token = googleUser.getAuthResponse().id_token;

                                $.ajax({
                                    url: window.ElementPackConfig.ajaxurl,
                                    method: 'post',
                                    data: {
                                        action: 'element_pack_social_google_login',
                                        id_token: id_token
                                    },
                                    dataType: 'json',
                                    beforeSend: function (xhr) {
                                        bdtUIkit.notification({
                                            message: '<div bdt-spinner></div> ' + element_pack_ajax_login_config.loadingmessage,
                                            timeout: false
                                        });
                                    },
                                    success: function (data) {
                                        if (data.success === true) {
                                            if (undefined === redirect_url) {
                                                location.reload();
                                            } else {
                                                window.location = redirect_url;
                                            }
                                        }
                                    },
                                    complete: function (xhr, status) {
                                        bdtUIkit.notification.closeAll();
                                    }

                                });
                            }

                        }, function (error) {
                            // error here
                        }
                    );

                });

                google_button.on('click', function () {
                    window.is_google_loggedin = true;
                });
            }
        };


        jQuery(window).on('elementor/frontend/init', function () {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-login.default', widgetUserLoginFormHandler);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-login.bdt-dropdown', widgetUserLoginFormHandler);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-login.bdt-modal', widgetUserLoginFormHandler);
        });

    }(jQuery, window.elementorFrontend));

/**
 * End user login widget script
 */

