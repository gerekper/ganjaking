/**
 * Start coupon reveal widget script
 */
(function ($, elementor) {
    'use strict';
    var widgetCoupon = function ($scope, $) {
        var $widgetContainer = $scope.find('.bdt-coupon-code'),
            editMode = Boolean(elementorFrontend.isEditMode()),
            $couponExecuted = false;
        if (!$widgetContainer.length) {
            return;
        }
        var $settings = $widgetContainer.data('settings'),
            triggerURL = $settings.triggerURL;

        if ($settings.triggerByAction != true) {
            var clipboard = new ClipboardJS($settings.couponMsgId, {
                target: function (trigger) {
                    // $trigger.nextElementSibling.addClass('bdt-coupon-showing');
                    return trigger.nextElementSibling;
                }
            });

            clipboard.on('success', function (event) {
                $(event.trigger).addClass('active');

                event.clearSelection();
                setTimeout(function () {
                    $(event.trigger).removeClass('active');
                    // $($settings.couponId).removeClass('bdt-coupon-showing');
                }, 3000);
            });
        }

        if (($settings.couponLayout == 'style-2') && ($settings.triggerByAction == true)) {
            var clipboard = new ClipboardJS($settings.couponId, {
                target: function (trigger) {
                    return trigger;
                }
            });

            clipboard.on('success', function (event) {
                $widgetContainer.find($settings.couponId).addClass('active');
                event.clearSelection();
                setTimeout(function () {
                    $widgetContainer.find($settings.couponId).removeClass('active');
                }, 2000);
            });

            //   attention
            $widgetContainer.on('click', function () {
                if (!$widgetContainer.hasClass('active') && ($settings.triggerAttention != false)) {
                    var $triggerSelector = $settings.triggerInputId;
                    $('[name="' + $triggerSelector.substring(1) + '"]').closest('form').addClass('ep-shake-animation-cc');
                    setTimeout(function () {
                        $('[name="' + $triggerSelector.substring(1) + '"]').closest('form').removeClass('ep-shake-animation-cc');
                    }, 5000);
                }

            });
        }

        var couponObj = {
            decodeCoupon: function (data) {
                jQuery.ajax({
                    url: $settings.adminAjaxURL,
                    type: 'post',
                    data: {
                        action: 'element_pack_coupon_code',
                        coupon_code: data
                    },
                    success: function (couponCode) {
                        $($settings.couponId).find('.bdt-coupon-code-text').html(couponCode);
                    },
                    error: function () {
                        $($settings.couponId).html('Something wrong, please contact support team.');
                    }
                });
            },
            displayCoupon: function ($widgetContainer) {
                $widgetContainer.addClass('active');

            },
            triggerURL: function (triggerURL) {
                var target = (true !== $settings.is_external) ? '_self' : '_blank';
                var redirectWindow = window.open(triggerURL, target);

                if (triggerURL) {
                    // Url contains a #
                    if (target == '_self' && triggerURL.indexOf('#') !== -1) {
                        var hash = triggerURL.split('#')[1];
                        if (hash) {
                            // console.log(hash);
                            $('html, body').animate({
                                scrollTop: $('#' + hash).offset().top - 100
                            }, 1500);
                            // return;
                        }
                    }
                    redirectWindow.location;
                }
                return false;
            },
            formSubmitted: function () {
                this.displayCoupon($widgetContainer);
                if (triggerURL !== false) {
                    this.triggerURL(triggerURL);
                }
                this.decodeCoupon($settings.couponCode);
                $couponExecuted = true;
            }
        };


        $widgetContainer.on('click', function () {
            if (!$widgetContainer.hasClass('active') && ($settings.triggerByAction !== true)) {
                couponObj.displayCoupon($widgetContainer);
                if (triggerURL !== false) {
                    setTimeout(function () {
                        couponObj.triggerURL(triggerURL);
                    }, 2000);
                }
            }
        });

        if (!editMode) {
            var triggerInput = $settings.triggerInputId;
            $(document).ajaxComplete(function (event, jqxhr, settings) {
                if (!$couponExecuted) {
                    if ((triggerInput !== false) && ($settings.triggerByAction === true)) {
                        var str = settings.data;
                        // console.log(str);
                        if (str.toLowerCase().indexOf(triggerInput.substring(1)) >= 0) {
                            couponObj.formSubmitted();
                        }
                    } else {
                        if ($settings.triggerByAction === true) {
                            couponObj.formSubmitted();
                        }
                    }
                }

            });

        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-coupon-code.default', widgetCoupon);
    });

}(jQuery, window.elementorFrontend));

/**
 * End coupon reveal widget script
 */