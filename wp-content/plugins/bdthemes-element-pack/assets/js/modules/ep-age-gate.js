/**
 * Start age-gate script
 */

(function ($, elementor) {

    'use strict';

    var widgetAgeGate = function ($scope, $) {

        var $modal = $scope.find('.bdt-age-gate');

        if (!$modal.length) {
            return;
        }

        $.each($modal, function (index, val) {

            var $this = $(this),
                $settings = $this.data('settings'),
                modalID = $settings.id,
                displayTimes = $settings.displayTimes,
                closeBtnDelayShow = $settings.closeBtnDelayShow,
                delayTime = $settings.delayTime,
                widgetId = $settings.widgetId,
                requiredAge = $settings.requiredAge,
                redirect_link = $settings.redirect_link;
            var editMode = Boolean(elementorFrontend.isEditMode());

            if (editMode) {
                redirect_link = false;
            }

            var modal = {
                setLocalize: function () {
                    if (editMode) {
                        this.clearLocalize();
                        return;
                    }
                    this.clearLocalize();
                    var widgetID = widgetId,
                        localVal = 0,
                        // hours = 4;
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
                    var localizeExpiry = parseInt(localStorage.getItem(widgetId + '_expiresIn'));
                    var now = Date.now(); //millisecs since epoch time, lets deal only with integer
                    var schedule = now;
                    if (schedule >= localizeExpiry) {
                        localStorage.removeItem(widgetId + '_expiresIn');
                        localStorage.removeItem(widgetId);
                    }
                },
                modalFire: function () {
                    var displayTimes = 1;
                    var firedNotify = parseInt(localStorage.getItem(widgetId)) || 0;

                    if ((displayTimes !== false) && (firedNotify >= displayTimes)) {
                        return;
                    }
                    bdtUIkit.modal($this, {
                        bgclose: false,
                        keyboard: false
                    }).show();
                },
                ageVerify: function () {
                    var init = this;
                    var firedNotify = parseInt(localStorage.getItem(widgetId)) || 0;
                    $('#' + widgetId).find('.bdt-button').on('click', function () {
                        var input_age = parseInt($('#' + widgetId).find('.bdt-age-input').val());
                        if (input_age >= requiredAge) {
                            init.setLocalize();
                            firedNotify += 1;
                            bdtUIkit.modal($this).hide();
                        } else {
                            if (redirect_link == false) {
                                $('.modal-msg-text').removeClass('bdt-hidden');
                                return;
                            } else {
                                $('.modal-msg-text').removeClass('bdt-hidden');
                            }
                            window.location.replace(redirect_link);
                        }
                    });

                    bdtUIkit.util.on($this, 'hidden', function () {

                        if(editMode){
                            return;
                        }

                        if (redirect_link == false && firedNotify <= 0) {

                            setTimeout( function(){
                                init.modalFire();
                            }, 1500);

                            return;
                        }

                        if (redirect_link !== false && firedNotify <= 0) {
                            window.location.replace(redirect_link);
                        }
                    });
                },
                closeBtnDelayShow: function () {
                    var $modal = $('#' + modalID);
                    $modal.find('#bdt-modal-close-button').hide(0);
                    $modal.on("shown", function () {
                            $('#bdt-modal-close-button').hide(0).fadeIn(delayTime);
                        })
                        .on("hide", function () {
                            $modal.find('#bdt-modal-close-button').hide(0);
                        });
                },

                default: function () {
                    this.modalFire();
                },
                init: function () {
                    var init = this;
                    init.default();
                    init.ageVerify();

                    if (closeBtnDelayShow) {
                        init.closeBtnDelayShow();
                    }
                }
            };

            // kick the modal
            modal.init();

        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-age-gate.default', widgetAgeGate);
    });

}(jQuery, window.elementorFrontend));

/**
 * End age-gate script
 */