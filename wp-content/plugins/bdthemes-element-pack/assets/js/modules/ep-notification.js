/**
 * Start notification widget script
 */

(function ($, elementor) {

    'use strict';

    // Notification
    var widgetNotification = function ($scope, $) {

        var $avdNotification = $scope.find('.bdt-notification-wrapper'),
            $settings = $avdNotification.data('settings');

        if (!$avdNotification.length) {
            return;
        }

        if (Boolean(elementorFrontend.isEditMode()) === false) {
            if ($($scope).is('.elementor-hidden-desktop, .elementor-hidden-tablet, .elementor-hidden-mobile')) {
                return;
            }

            if ($settings.externalSystem == 'yes' && $settings.externalSystemValid == false) {
                return;
            }

            if ($settings.linkWithConfetti === true){
                jQuery.ajax({
                    type: "post",
                    dataType: "json",
                    url: ElementPackConfig.ajaxurl,
                    data: {
                        action: "ep_connect_confetti",
                        data  : 'empty'
                    },
                    success: function () {
                        //  console.log('done');
                    }
                })
            }
           
        }


        var $settings = $avdNotification.data('settings'),
            id = '#' + $settings.id,
            timeOut = $settings.notifyTimeout,
            notifyType = $settings.notifyType,
            notifyFixPos = $settings.notifyFixPosition,
            editMode = Boolean(elementorFrontend.isEditMode());


        if (typeof $settings.notifyTimeout === "undefined") {
            timeOut = null;
        }

        bdtUIkit.util.on(document, 'beforehide', '[bdt-alert]', function (event) {
            if (notifyFixPos === 'top') {
                $('html').attr('style', 'margin-top: unset !important');
            }
        });

        var notification = {
            htmlMarginRemove: function () {
                $('html').css({
                    'margin-top': 'unset  !important'
                });
            },
            appendBody: function () {
                $('body > ' + id).slice(1).remove();
                $(id).prependTo($("body"));
            },
            showNotify: function () {
                $(id).removeClass('bdt-hidden');
            },
            notifyFixed: function () {
                this.htmlMarginRemove();
                setTimeout(function () {
                    if (notifyFixPos == 'top') {
                        var notifyHeight = $('.bdt-notify-wrapper').outerHeight();
                        if ($('.admin-bar').length) {
                            notifyHeight = notifyHeight + 32;
                            $(id).attr('style', 'margin-top: 32px !important');
                        }
                        $('html').attr('style', 'margin-top: ' + notifyHeight + 'px !important');
                        $('html').css({
                            'transition': 'margin-top .8s ease'
                        });
                        $(window).on('resize', function () {
                            notifyHeight = $('.bdt-notify-wrapper').outerHeight();
                            if ($('.admin-bar').length) {
                                notifyHeight = notifyHeight + 32;
                            }
                            $('html').attr('style', 'margin-top: ' + notifyHeight + 'px !important');
                        });
                    }
                }, 1000);
            },
            notifyRelative: function () {
                $('body > ' + id).remove();
            },
            notifyPopup: function () {
                bdtUIkit.notification({
                    message: $settings.msg,
                    status: $settings.notifyStatus,
                    pos: $settings.notifyPosition,
                    timeout: timeOut
                });
            },
            notifyFire: function () {
                if (notifyType === 'fixed') {
                    if (notifyFixPos !== 'relative') {
                        this.appendBody();
                        this.notifyFixed();
                    } else {
                        this.htmlMarginRemove();
                        this.notifyRelative();
                    }
                } else {
                    this.notifyPopup();
                }
            },
            setLocalize: function () {
                if (editMode) {
                    this.clearLocalize();
                    return;
                }
                var widgetID = $settings.id,
                    localVal = 0,
                    hours = $settings.displayTimesExpire;

                var expires = (hours * 60 * 60);
                var now = Date.now();
                var schedule = now + expires * 1000;

                if (localStorage.getItem(widgetID) === null) {
                    localStorage.setItem(widgetID, localVal);
                    localStorage.setItem(widgetID + '_expiresIn', schedule);
                }
                if (localStorage.getItem(widgetID) !== null) {
                    var count = parseInt(localStorage.getItem(widgetID));
                    count++;
                    localStorage.setItem(widgetID, count);
                    // this.clearLocalize();
                }
            },
            clearLocalize: function () {
                var localizeExpiry = parseInt(localStorage.getItem($settings.id + '_expiresIn'));
                var now = Date.now(); //millisecs since epoch time, lets deal only with integer
                var schedule = now;
                if (schedule >= localizeExpiry) {
                    localStorage.removeItem($settings.id + '_expiresIn');
                    localStorage.removeItem($settings.id);
                }
            },
            notificationInit: function () {
                var init = this;

                this.setLocalize();
                var displayTimes = $settings.displayTimes,
                    firedNotify = parseInt(localStorage.getItem($settings.id));

                if ((displayTimes !== false) && (firedNotify > displayTimes)) {
                    return;
                }

                this.showNotify();

                if ($settings.notifyEvent == 'onload' || $settings.notifyEvent == 'inDelay') {
                    $(document).ready(function () {
                        setTimeout(function () {
                            init.notifyFire();
                        }, $settings.notifyInDelay);
                    });
                }
                if ($settings.notifyEvent == 'click' || $settings.notifyEvent == 'mouseover') {
                    $($settings.notifySelector).on($settings.notifyEvent, function () {
                        init.notifyFire();
                    });
                }
            },
        };

        // kick off the notification widget
        notification.notificationInit();

        $('.bdt-notify-wrapper.bdt-position-fixed .bdt-alert-close').on('click', function (e) {
            $('html').attr('style', 'margin-top: unset !important');
            if ($('.admin-bar').length) {
                $('html').attr('style', 'margin-top: 32px !important');
            }
        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-notification.default', widgetNotification);
    });

}(jQuery, window.elementorFrontend));

/**
 * End notification widget script
 */