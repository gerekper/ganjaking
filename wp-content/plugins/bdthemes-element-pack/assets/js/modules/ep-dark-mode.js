(function ($, elementor) {

    'use strict';

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            widgetDarkMode;

        widgetDarkMode = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    left            : 'unset',
                    time            : '.5s',
                    mixColor        : '#fff',
                    backgroundColor : '#fff',
                    saveInCookies   : false,
                    label           : '🌓',
                    autoMatchOsTheme: false
                };
            },


            onElementChange: debounce(function (prop) {
                // if (prop.indexOf('time.size') !== -1) {
                this.run();
                // }
            }, 400),

            settings: function (key) {
                return this.getElementSettings(key);
            },

            setCookie: function (name, value, days) {
                var expires = "";
                if ( days ) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            },
            getCookie: function (name) {
                var nameEQ = name + "=";
                var ca     = document.cookie.split(';');
                for ( var i = 0; i < ca.length; i++ ) {
                    var c = ca[i];
                    while ( c.charAt(0) == ' ' ) c = c.substring(1, c.length);
                    if ( c.indexOf(nameEQ) == 0 ) return c.substring(nameEQ.length, c.length);
                }
                return null;
            },

            eraseCookie: function (name) {
                document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            },


            run: function () {
                var options = this.getDefaultSettings(),
                    element = this.findElement('.elementor-widget-container').get(0);

                var autoMatchOsTheme = (this.settings('autoMatchOsTheme') === 'yes'
                    && this.settings('autoMatchOsTheme') !== 'undefined');

                var saveInCookies = (this.settings('saveInCookies') === 'yes'
                    && this.settings('saveInCookies') !== 'undefined');

                options.left             = 'unset';
                options.time             = this.settings('time.size') / 1000 + 's';
                options.mixColor         = this.settings('mix_color');
                options.backgroundColor  = this.settings('default_background');
                options.saveInCookies    = saveInCookies;
                options.label            = '🌓';
                options.autoMatchOsTheme = autoMatchOsTheme;

                $('body').removeClass(function (index, css) {
                    return (css.match(/\bbdt-dark-mode-\S+/g) || []).join(' '); // removes anything that starts with "page-"
                });
                $('body').addClass('bdt-dark-mode-position-' + this.settings('toggle_position'));

                $(this.settings('ignore_element')).addClass('darkmode-ignore');

                if ( options.mixColor ) {

                    $('.darkmode-toggle, .darkmode-layer, .darkmode-background').remove();

                    var darkmode = new Darkmode(options);
                    darkmode.showWidget();

                    if ( this.settings('default_mode') === 'dark' ) {
                        darkmode.toggle();
                        $('body').addClass('darkmode--activated');
                        $('.darkmode-layer').addClass('darkmode-layer--simple darkmode-layer--expanded');
                        // console.log(darkmode.isActivated()) // will return true
                    } else {
                        $('body').removeClass('darkmode--activated');
                        $('.darkmode-layer').removeClass('darkmode-layer--simple darkmode-layer--expanded');
                        // console.log(darkmode.isActivated()) // will return true
                    }

                    var global_this = this,
                        editMode    = $('body').hasClass('elementor-editor-active');

                    if ( editMode === false && saveInCookies === true ) {
                        $('.darkmode-toggle').on('click', function () {
                            if ( darkmode.isActivated() === true ) {
                                global_this.eraseCookie('bdtDarkModeUserAction');
                                global_this.setCookie('bdtDarkModeUserAction', 'dark', 10);
                            } else if ( darkmode.isActivated() === false ) {
                                global_this.eraseCookie('bdtDarkModeUserAction');
                                global_this.setCookie('bdtDarkModeUserAction', 'light', 10);
                            } else {

                            }
                        });

                        var userCookie = this.getCookie('bdtDarkModeUserAction')

                        if ( userCookie !== null && userCookie !== 'undefined' ) {
                            if ( userCookie === 'dark' ) {
                                darkmode.toggle();
                                $('body').addClass('darkmode--activated');
                                $('.darkmode-layer').addClass('darkmode-layer--simple darkmode-layer--expanded');
                            } else {
                                $('body').removeClass('darkmode--activated');
                                $('.darkmode-layer').removeClass('darkmode-layer--simple darkmode-layer--expanded');
                            }

                        }
                    }

                }


            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-dark-mode.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(widgetDarkMode, { $element: $scope });

        });
    });


}(jQuery, window.elementorFrontend));

/**
 * End Dark Mode widget script
 */