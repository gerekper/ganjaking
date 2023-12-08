/**
 * Start countdown widget script
 */
 
(function ($, elementor) {
    'use strict';
    var widgetCountdown = function ($scope, $) {
        var $countdown = $scope.find('.bdt-countdown-wrapper');
        if (!$countdown.length) {
            return;
        }
        var $settings = $countdown.data('settings'),
            endTime = $settings.endTime,
            loopHours = $settings.loopHours,
            isLogged = $settings.isLogged;

           
 
        var countDownObj = {
            setCookie: function (name, value, hours) {
                var expires = "";
                if (hours) {
                    var date = new Date();
                    date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            },
            getCookie: function (name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            },
            randomIntFromInterval: function (min, max) { // min and max included 
                return Math.floor(Math.random() * (max - min + 1) + min)
            },
            getTimeSpan: function (date) {
                var total = date - Date.now();

                return {
                    total,
                    seconds: total / 1000 % 60,
                    minutes: total / 1000 / 60 % 60,
                    hours: total / 1000 / 60 / 60 % 24,
                    days: total / 1000 / 60 / 60 / 24
                };
            },
            showPost: function (endTime) {
                jQuery.ajax({
                    url: $settings.adminAjaxUrl,
                    type: 'post',
                    data: {
                        action: 'element_pack_countdown_end',
                        endTime: endTime,
                        couponTrickyId: $settings.couponTrickyId
                    },
                    success: function (data) {
                        if (data == 'ended') {
                            if ($settings.endActionType == 'message') {
                                jQuery($settings.msgId).css({
                                    'display': 'block'
                                });
                                jQuery($settings.id + '-timer').css({
                                    'display': 'none'
                                });
                            }
                            if ($settings.endActionType == 'url') {
                                setInterval(function () {
                                    jQuery(location).attr('href', $settings.redirectUrl);
                                }, $settings.redirectDelay);
                            }
                        } 
                    },
                    error: function () {
                        //error handling
                        console.log("Error");
                    }
                });
            },
            couponCode: function(){
                jQuery.ajax({
                    url: $settings.adminAjaxUrl,
                    type: 'post',
                    data: {
                        action: 'element_pack_countdown_end',
                        endTime: endTime,
                        couponTrickyId: $settings.couponTrickyId
                    },
                    success: function (data) {
                    },
                    error: function () {
                        //error handling
                        //console.log("Error");
                    }
                });
            },
            triggerFire : function(){
                jQuery.ajax({
                    url: $settings.adminAjaxUrl,
                    type: 'post',
                    data: {
                        action: 'element_pack_countdown_end',
                        endTime: endTime,
                        couponTrickyId: $settings.couponTrickyId
                    },
                    success: function (data) {
                         if (data == 'ended') {
                             setTimeout(function () {
                                if ($settings.triggerId){
                                    document.getElementById($settings.triggerId).click();
                                    
                                }
                                // document.getElementById($settings.triggerId).click();
                                //  jQuery('#' + $settings.triggerId).trigger('click');
                             }, 1500);
                         }
                    },
                    error: function () {
                        //console.log("Error");
                    }
                });
            },
            clearInterVal: function (myInterVal) {
                clearInterval(myInterVal);
            }

        };


        if (loopHours == false) {
            var countdown = bdtUIkit.countdown($($settings.id + '-timer'), {
                date: $settings.finalTime
            });

            var myInterVal = setInterval(function () {
                var seconds = countDownObj.getTimeSpan(countdown.date).seconds.toFixed(0);
                var finalSeconds = parseInt(seconds);
                if (finalSeconds < 0) {
                    if (!jQuery('body').hasClass('elementor-editor-active')) {
                        jQuery($settings.id + '-msg').css({
                            'display': 'none'
                        });
                        if ($settings.endActionType != 'none') {
                            countDownObj.showPost(endTime)
                        };
                    }
                    countDownObj.clearInterVal(myInterVal);
                }
            }, 1000);
            
            // for coupon code
            if ($settings.endActionType == 'coupon-code') {
                var myInterVal2 = setInterval(function () {
                    var seconds = countDownObj.getTimeSpan(countdown.date).seconds.toFixed(0);
                    var finalSeconds = parseInt(seconds);
                    if (finalSeconds < 0) {
                        if (!jQuery('body').hasClass('elementor-editor-active')) {
                            if ($settings.endActionType == 'coupon-code') {
                                countDownObj.couponCode(endTime)
                            };
                        }
                        countDownObj.clearInterVal(myInterVal2);
                    }
                }, 1000);
            }
            // custom trigger on the end

            if ($settings.triggerId !== false) {
                var myInterVal2 = setInterval(function () {
                    var seconds = countDownObj.getTimeSpan(countdown.date).seconds.toFixed(0);
                    var finalSeconds = parseInt(seconds);
                    if (finalSeconds < 0) {
                        if (!jQuery('body').hasClass('elementor-editor-active')) {
                                countDownObj.triggerFire();
                        }
                        countDownObj.clearInterVal(myInterVal2);
                    }
                }, 1000);
            }
 
        }


        if (loopHours !== false) {
            var now = new Date(),
                randMinute = countDownObj.randomIntFromInterval(6, 14),
                hours = loopHours * 60 * 60 * 1000 - (randMinute * 60 * 1000),
                timer = new Date(now.getTime() + hours),
                loopTime = timer.toISOString(),
                getCookieLoopTime = countDownObj.getCookie('bdtCountdownLoopTime');


            if ((getCookieLoopTime == null || getCookieLoopTime == 'undefined') && isLogged === false) {
                countDownObj.setCookie('bdtCountdownLoopTime', loopTime, loopHours);
            }

            var setLoopTimer;

            if (isLogged === false) {
                setLoopTimer = countDownObj.getCookie('bdtCountdownLoopTime');
            } else {
                setLoopTimer = loopTime;
            }

            $($settings.id + '-timer').attr('data-bdt-countdown', 'date: ' + setLoopTimer);
            var countdown = bdtUIkit.countdown($($settings.id + '-timer'), {
                date: setLoopTimer
            });

            var countdownDate = countdown.date;

            setInterval(function () {
                var seconds = countDownObj.getTimeSpan(countdownDate).seconds.toFixed(0);
                var finalSeconds = parseInt(seconds);
                // console.log(finalSeconds);
                if (finalSeconds > 0) {
                    if ((getCookieLoopTime == null || getCookieLoopTime == 'undefined') && isLogged === false) {
                        countDownObj.setCookie('bdtCountdownLoopTime', loopTime, loopHours);
                        bdtUIkit.countdown($($settings.id + '-timer'), {
                            date: setLoopTimer
                        });
                    }
                }

            }, 1000);


        }


    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-countdown.default', widgetCountdown);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-countdown.bdt-tiny-countdown', widgetCountdown);
    });
}(jQuery, window.elementorFrontend));

/**
 * End countdown widget script
 */