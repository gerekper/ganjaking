/*--- Pro Version --- */
ea.hooks.addAction("init", "ea", () => {
    const EALoginRegisterPro = function ($scope, $) {
        const $wrap = $scope.find('.eael-login-registration-wrapper');// cache wrapper
        const ajaxEnabled = $wrap.data('is-ajax');
        const widgetId = $wrap.data('widget-id');
        const redirectTo = $wrap.data('redirect-to');
        const $loginForm = $wrap.find('#eael-login-form');
        const $lostpasswordForm = $wrap.find('#eael-lostpassword-form');
        const $resetpasswordForm = $wrap.find('#eael-resetpassword-form');
        const recaptchaAvailablePro = (typeof grecaptcha !== 'undefined' && grecaptcha !== null);
        const loginRecaptchaVersionPro = $wrap.data('login-recaptcha-version');
        const registerRecaptchaVersionPro = $wrap.data('register-recaptcha-version');
        const recaptchaSiteKeyV3Pro = $wrap.data('recaptcha-sitekey-v3');

        let isRecaptchaVersion3Pro = false;
        isRecaptchaVersion3Pro = loginRecaptchaVersionPro === 'v3' || registerRecaptchaVersionPro === 'v3' ;

        window.isLoggedInByFB = false;
        window.isUsingGoogleLogin = false;
        // Google
        const gLoginNodeId = 'eael-google-login-btn-' + widgetId;
        const $gBtn = $loginForm.find('#' + gLoginNodeId);
        // Facebook
        const fLoginNodeId = 'eael-fb-login-btn-' + widgetId;
        const $fBtn = $loginForm.find('#' + fLoginNodeId);

        const $registerFormWrapper = $wrap.find('#eael-register-form-wrapper');
        const $registerForm = $wrap.find('#eael-register-form');

         // Register: Google
         const gRegisterNodeId = 'eael-google-register-btn-' + widgetId;
         const $gBtnRegister = $registerForm.find('#' + gRegisterNodeId);
         // Register: Facebook
         const fRegisterNodeId = 'eael-fb-register-btn-' + widgetId;
         const $fBtnRegister = $registerForm.find('#' + fRegisterNodeId);

        const ajaxAction = {
            name: "action",
            value: 'eael-login-register-form'
        };
        const valid_login_vendors = ['facebook', 'google', 'login'];
        const $passField = $registerForm.find('#form-field-password');
        const psOps = $registerForm.find('.pass-meta-info').data('strength-options');
        const $passNotice = $registerForm.find('.eael-pass-notice');
        const $passMeter = $registerForm.find('.eael-pass-meter');
        const $passHint = $registerForm.find('.eael-pass-hint');
        const $useWeakPass = $registerFormWrapper.attr('data-use-weak-password');
        const $passwordMinLength = $registerFormWrapper.data('password-min-length');
        const $passwordOneUppercase = $registerFormWrapper.attr('data-password-one-uppercase');
        const $passwordOneLowercase = $registerFormWrapper.data('password-one-lowercase');
        const $passwordOneNumber = $registerFormWrapper.data('password-one-number');
        const $passwordOneSpecial = $registerFormWrapper.data('password-one-special');

        const showPassMeta = ($passField.length > 0 && ($passNotice.length > 0 || $passMeter.length > 0 || $passHint.length > 0));
        ea.getToken();
        const sendData = function sendData(form_data, formType) {
            // set the correct form type we are submitting: login or register?
            form_data.push({
                "name": `eael-${formType}-submit`,
                "value": true
            });

            // set dynamic nonce for ajax request

            form_data = form_data.map(function(item){
                if( item.name === 'eael-login-nonce' || item.name === 'eael-register-nonce' || item.name === 'eael-lostpassword-nonce' || item.name === 'eael-resetpassword-nonce' ){
                    item.value = localize.nonce;
                };
                return item;
            });

            form_data.push(ajaxAction);

            if (recaptchaAvailablePro && isRecaptchaVersion3Pro) {
                grecaptcha.ready(function() {
                    grecaptcha.execute(recaptchaSiteKeyV3Pro, { 
                        action: 'eael_login_register_form' 
                    }).then(function (token) {
                        if ($('form input[name="g-recaptcha-response"]', $scope).length === 0) {
                            $('form', $scope).append('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                        } else {
                            $('form input[name="g-recaptcha-response"]', $scope).val(token);
                        }
                        const recaptchaV3Token = {
                            name: "g-recaptcha-response",
                            value: token
                        };

                        form_data.push(recaptchaV3Token);

                        eaelAjaxCall(form_data, formType);
                    });
                });
            } else {
                eaelAjaxCall(form_data, formType);
            }
        }

        if ('yes' === ajaxEnabled) {
            //Handle Register form submission via ajax
            $loginForm.unbind().on('submit', function (e) {
                $loginForm.find("#eael-login-submit").prop("disabled",true);
                const form_data = $(this).serializeArray()
                form_data.filter((currentValue, index) => {
                    if (form_data[index].name == 'eael-login-nonce') {
                        form_data[index].value = localize.eael_login_nonce;
                        return;
                    }
                });
                sendData(form_data, 'login');
                return false;
            });

            //Handle Register form submission via ajax
            $registerForm.unbind().on('submit', function (e) {
                $registerForm.find("#eael-register-submit").prop("disabled",true);
                const form_data = $(this).serializeArray()
                form_data.filter((currentValue, index) => {
                    if (form_data[index].name == 'eael-register-nonce') {
                        form_data[index].value = localize.eael_register_nonce;
                    }
                });
                sendData(form_data, 'register');
                return false;
            });

            //Handle Lost Password form submission via ajax
            $lostpasswordForm.on('submit', function (e) {
                $lostpasswordForm.find("#eael-lostpassword-submit").prop("disabled",true);
                const form_data = $(this).serializeArray()
                form_data.filter((currentValue, index) => {
                    if (form_data[index].name == 'eael-lostpassword-nonce') {
                        form_data[index].value = localize.eael_lostpassword_nonce;
                    }
                });
                sendData(form_data, 'lostpassword');
                return false;
            });

            //Handle Reset Password form submission via ajax
            $resetpasswordForm.on('submit', function (e) {
                $resetpasswordForm.find("#eael-resetpassword-submit").prop("disabled",true);
                const form_data = $(this).serializeArray()
                form_data.filter((currentValue, index) => {
                    if (form_data[index].name == 'eael-resetpassword-nonce') {
                        form_data[index].value = localize.eael_resetpassword_nonce;
                    }
                });
                sendData(form_data, 'resetpassword');
                return false;
            });
            
        }

        function eaelAjaxCall(form_data, formType){
            $.ajax({
                url: localize.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: form_data,
                beforeSend: function () {
                    $wrap.find('.eael-lr-form-loader').show();
                },
                success: function (data) {
                    const success = (data && data.success);
                    const isLoginForm = valid_login_vendors.includes(formType);
                    const isLostpasswordForm = formType === 'lostpassword';
                    const isResetpasswordForm = formType === 'resetpassword';

                    let message;
                    if (success) {
                        message = `<div class="eael-form-msg valid">${data.data.message}</div>`;
                        $loginForm.trigger('reset');
                        $registerForm.trigger('reset');
                        $lostpasswordForm.trigger('reset');
                    } else {
                        if( recaptchaAvailablePro && !isRecaptchaVersion3Pro ){
                            try{
                                grecaptcha.reset(0);
                                grecaptcha.reset(1);
                            }catch( error ){
                                // do nothing
                            }
                        }
                        message = `<div class="eael-form-msg invalid">${data.data}</div>`;
                    }

                    if (isLoginForm) {
                        if(!success){
                            $loginForm.find("#eael-login-submit").prop("disabled",false);
                        }
                        $loginForm.find('.eael-form-validation-container').html(message);
                    } else if ( isLostpasswordForm ) {
                        if(!success){
                            $lostpasswordForm.find("#eael-lostpassword-submit").prop("disabled",false);
                        }
                        $lostpasswordForm.find('.eael-form-validation-container').html(message);
                    } else if ( isResetpasswordForm ) {
                        if(!success){
                            $resetpasswordForm.find("#eael-resetpassword-submit").prop("disabled",false);
                        } else {
                            $resetpasswordForm.find(".eael-lr-form-group").css("display", 'none');
                            $resetpasswordForm.find("#eael-resetpassword-submit").css("display", 'none');
                        }
                        $resetpasswordForm.find('.eael-form-validation-container').html(message);
                    } else {
                        $registerForm.find("#eael-register-submit").prop("disabled",false);
                        $registerForm.find('.eael-form-validation-container').html(message);
                    }

                    //handle redirect
                    if (success) {
                        if (data.data.redirect_to) {
                            setTimeout(() => window.location = data.data.redirect_to, 500);
                        } else if (isLoginForm) {
                            // refresh the page on login success
                            setTimeout(() => location.reload(), 1000);
                        }
                    }


                },
                error: function (xhr, err) {
                    let errorHtml = `
                    <p class="eael-form-msg invalid">
                    Error occurred: ${err.toString()} 
                    </p>
                    `;
                    if ('login' === formType) {
                        $loginForm.find("#eael-login-submit").prop("disabled",false);
                        $loginForm.find('.eael-form-validation-container').html(errorHtml);
                    } else if ('lostpassword' === formType) {
                        $lostpasswordForm.find("#eael-lostpassword-submit").prop("disabled",false);
                        $lostpasswordForm.find('.eael-form-validation-container').html(errorHtml);
                    } else if ('resetpassword' === formType) {
                        $resetpasswordForm.find("#eael-resetpassword-submit").prop("disabled",false);
                        $resetpasswordForm.find('.eael-form-validation-container').html(errorHtml);
                    } else {
                        $registerForm.find("#eael-register-submit").prop("disabled",false);
                        $registerForm.find('.eael-form-validation-container').html(errorHtml);
                    }
                },
                complete: function () {
                    $wrap.find('.eael-lr-form-loader').hide();
                }
            });
        }

        const gLoginRegisterClickHandler = function (googleUser) {

            let id_token = googleUser.credential;
            let googleData = [
                {
                    name: 'widget_id',
                    value: widgetId,
                },
                {
                    name: 'redirect_to',
                    value: redirectTo,
                },
                {
                    name: 'id_token',
                    value: id_token,
                }, {
                    name: 'nonce',
                    value: $loginForm.find('#eael-login-nonce').val(),
                },
            ];

            sendData(googleData, 'google');

        }

        if (($gBtn.length) || ($gBtnRegister.length)) {
            let gClientId = $gBtn.data('g-client-id'),
                default_form_type = $('.eael-login-form-wrapper').is(':visible') ? 'login' : 'register',
                type = $gBtn.data('type'),
                theme = $gBtn.data('theme'),
                size = $gBtn.data('size'),
                text = $gBtn.data('text'),
                shape = $gBtn.data('shape'),
                logo_alignment = $gBtn.data('logo_alignment'),
                width = $gBtn.data('width'),
                locale = $gBtn.data('locale'),
                login_btn_trigger = false,
                reg_btn_trigger = false;

            const gisButton = function ($node, $type = 'login') {
                google.accounts.id.renderButton(
                    document.getElementById($node),
                    {
                        type: type,
                        theme: theme,
                        size: size,
                        text: $type === 'register' ? 'signup_with' : text,
                        shape: shape,
                        logo_alignment: logo_alignment,
                        width: width,
                        locale: locale
                    }
                );
            }

            // Login with Google
            if (typeof google !== 'undefined' && google !== null) {
                google.accounts.id.initialize({
                    client_id: gClientId,
                    callback: gLoginRegisterClickHandler
                });

                if ($gBtnRegister.length && default_form_type === 'register') {
                    gisButton(gRegisterNodeId, 'register');
                    $('#eael-lr-login-toggle').on('click', function () {
                        if (!login_btn_trigger) {
                            gisButton(gLoginNodeId);
                            login_btn_trigger = true;
                        }
                    });
                } else if ($gBtnRegister.length && default_form_type === 'login') {
                    gisButton(gLoginNodeId);
                    $('#eael-lr-reg-toggle').on('click', function () {
                        if (!reg_btn_trigger) {
                            gisButton(gRegisterNodeId, 'register');
                            reg_btn_trigger = true;
                        }
                    });
                } else {
                    gisButton(gLoginNodeId);
                }
            } else {
                console.log('google not defined or loaded');
            }
        }

        const gLoginRegisterClickHandlerError = function (error) {
            let msg = `<p class="eael-form-msg invalid"> Something went wrong! ${error.error}</p>`
            $scope.find('.eael-form-validation-container').html(msg);
        };

        if ( ($fBtn.length && !isEditMode) || ($fBtnRegister.length && !isEditMode) ) {
            let appId = $fBtn.data('fb-appid');
            window.fbAsyncInit = function () {
                FB.init({
                    appId: appId,
                    cookie: true,
                    xfbml: true,
                    version: 'v8.0'
                });

                FB.AppEvents.logPageView();

            };

            (function (d, s, id) {
                var js,
                    fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s);
                js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));


            $fBtn.on('click', function () {

                if (!isLoggedInByFB) {
                    FB.login(function (response) {
                        // handle the response
                        if (response.status === 'connected') {
                            // Logged into our webpage and Facebook.
                            logUserInOurAppUsingFB();
                        } else {
                            console.log('The person is not logged into our webpage or facebook is unable to tell.')
                        }
                    }, {scope: 'public_profile,email'});
                }

            });

            $fBtnRegister.on('click', function () {

                if (!isLoggedInByFB) {
                    FB.login(function (response) {
                        // handle the response
                        if (response.status === 'connected') {
                            // Logged into our webpage and Facebook.
                            logUserInOurAppUsingFB();
                        } else {
                            console.log('The person is not logged into our webpage or facebook is unable to tell.')
                        }
                    }, {scope: 'public_profile,email'});
                }

            });

            // Fetch the user profile data from facebook.
            function logUserInOurAppUsingFB() {
                FB.api('/me', {fields: 'id, name, email'},
                    function (response) {
                        window.isLoggedInByFB = true;
                        let fbData = [
                            {
                                name: 'widget_id',
                                value: widgetId,
                            },
                            {
                                name: 'redirect_to',
                                value: redirectTo,
                            },
                            {
                                name: 'email',
                                value: response.email,
                            },
                            {
                                name: 'full_name',
                                value: response.name,
                            },
                            {
                                name: 'user_id',
                                value: response.id,
                            },
                            {
                                name: 'access_token',
                                value: FB.getAuthResponse()['accessToken'],
                            },
                            {
                                name: 'nonce',
                                value: $loginForm.find('#eael-login-nonce').val(),
                            },
                        ];

                        sendData(fbData, 'facebook');

                    });

            }
        }

        // Password Strength Related meta information
        if (showPassMeta) {
            function showStrengthMeter(strength, password) {
                if ('yes' !== psOps.show_ps_meter) {
                    return;
                }
                if (!password) {
                    $passMeter.hide(300);
                    return;
                }
                $passMeter.show(400);
                const meterValue = 0 === strength ? 1 : strength;
                $passMeter.val(meterValue);
            }

            function showStrengthText(strength, password) {
                if ('yes' !== psOps.show_pass_strength) {
                    return;
                }
                if (!password) {
                    $passNotice.hide(300);
                    return;
                }
                $passNotice.show(400);
                let pText = '';
                const useCustomText = ('custom' === psOps.ps_text_type);
                const cssClasses = 'short bad mismatch good strong';

                switch (strength) {
                    case -1:
                        // do nothing
                        break;
                    case 2:
                        pText = useCustomText ? psOps.ps_text_bad : pwsL10n.bad;
                        $passNotice.html(pText).removeClass(cssClasses).addClass('bad');

                        break;
                    case 3:
                        pText = useCustomText ? psOps.ps_text_good : pwsL10n.good;
                        $passNotice.html(pText).removeClass(cssClasses).addClass('good');
                        break;
                    case 4:
                        pText = useCustomText ? psOps.ps_text_strong : pwsL10n.strong;
                        $passNotice.html(pText).removeClass(cssClasses).addClass('strong');

                        break;
                    case 5:
                        $passNotice.html(pwsL10n.mismatch).removeClass(cssClasses).addClass('mismatch');
                        break;
                    default:
                        pText = useCustomText ? psOps.ps_text_short : pwsL10n.short;
                        $passNotice.html(pText).removeClass(cssClasses).addClass('short');
                }
            }

            function togglePassHint(strength) {
                if (strength >= 3) {
                    $passHint.hide(300); // hide hint when pass word is good.
                } else {
                    $passHint.show(400);
                }
            }

            function checkPassStrength() {
                let strength;
                let password = $passField.val();
                if (password) {
                    strength = wp.passwordStrength.meter(password, wp.passwordStrength.userInputDisallowedList(), password);// @todo; add confirm pass check later
                }
                // recalculate if use weak password is disabled
                if(typeof $useWeakPass !== 'undefined' && $useWeakPass === '0'){
                    strength = 2; // by default password is weak

                    let passwordMinLengthPassed = $passwordMinLength ? password.length >= $passwordMinLength : true;
                    let passwordOneUppercasePassed = $passwordOneUppercase ? password.match(/[A-Z]/) : true;
                    let passwordOneLowercasePassed = $passwordOneLowercase ? password.match(/[a-z]/) : true;
                    let passwordOneNumberPassed = $passwordOneNumber ? password.match(/\d/) : true;
                    let passwordOneSpecialPassed = $passwordOneSpecial ? password.match(/[!@#$%^&*-]/) : true;

                    if(passwordMinLengthPassed && passwordOneUppercasePassed && passwordOneLowercasePassed && passwordOneNumberPassed && passwordOneSpecialPassed){
                        strength = 4; //strong
                    } else {
                        if(passwordMinLengthPassed){
                            strength = 3; //good
                        }
                    }
                }
                
                showStrengthMeter(strength, password)
                showStrengthText(strength, password);
                togglePassHint(strength);
            }

            $passField.on('keyup', function (e) {
                checkPassStrength();
            });
        }

    };
    elementorFrontend.hooks.addAction("frontend/element_ready/eael-login-register.default", EALoginRegisterPro);
});