/**
 * Start advanced heading widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetAdavancedHeading = function ($scope, $) {
        var $advHeading = $scope.find('.bdt-ep-advanced-heading'),
            $advMainHeadeingInner = $advHeading.find('.bdt-ep-advanced-heading-main-title-inner');

        if (!$advHeading.length) {
            return;
        }
        var $settings = $advHeading.data('settings');
        if (typeof $settings.titleMultiColor !== "undefined") {
            if ($settings.titleMultiColor != 'yes') {
                return;
            }
            var word = $($advMainHeadeingInner).text();
            var words = word.split(" ");

            // console.log(words);
            $($advMainHeadeingInner).html('');
            var i;
            for (i = 0; i < words.length; ++i) {
                // $('#result').append('<span>'+words[i] +' </span>');
                $($advMainHeadeingInner).append('<span>' + words[i] + '&nbsp;</span>');
            }

            $($advMainHeadeingInner).find('span').each(function () {
                var randomColor = Math.floor(Math.random() * 16777215).toString(16);
                $(this).css({
                    'color': '#' + randomColor
                });
            });
        }

    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-heading.default', widgetAdavancedHeading);
    });
}(jQuery, window.elementorFrontend));

/**
 * End advanced heading widget script
 */