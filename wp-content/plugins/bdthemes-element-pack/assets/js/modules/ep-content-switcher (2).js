/**
 * Start Content Switcher widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetContentSwitcher = function ($scope, $) {

        var $contentSwitcher = $scope.find('.bdt-content-switcher'),
            $settings = $contentSwitcher.data('settings');

        if (!$contentSwitcher.length) {
            return;
        }

        if ('button' !== $settings.switcherStyle) {

            // Conten Switcher Checkbox
            var $checkbox = $contentSwitcher.find('input[type="checkbox"]');
            var primarySwitcher = $contentSwitcher.find('.bdt-primary-switcher');
            var secondarySwitcher = $contentSwitcher.find('.bdt-secondary-switcher');
            var primaryIcon = $contentSwitcher.find('.bdt-primary-icon');
            var secondaryIcon = $contentSwitcher.find('.bdt-secondary-icon');
            var primaryText = $contentSwitcher.find('.bdt-primary-text');
            var secondaryText = $contentSwitcher.find('.bdt-secondary-text');
            var primaryContent = $contentSwitcher.find('.bdt-switcher-content.bdt-primary');
            var secondaryContent = $contentSwitcher.find('.bdt-switcher-content.bdt-secondary');

            $checkbox.on('change', function () {
                if (this.checked) {
                    primarySwitcher.removeClass('bdt-active');
                    secondarySwitcher.addClass('bdt-active');
                    primaryIcon.removeClass('bdt-active');
                    secondaryIcon.addClass('bdt-active');
                    primaryText.removeClass('bdt-active');
                    secondaryText.addClass('bdt-active');
                    primaryContent.removeClass('bdt-active');
                    secondaryContent.addClass('bdt-active');
                } else {
                    primarySwitcher.addClass('bdt-active');
                    secondarySwitcher.removeClass('bdt-active');
                    primaryIcon.addClass('bdt-active');
                    secondaryIcon.removeClass('bdt-active');
                    primaryText.addClass('bdt-active');
                    secondaryText.removeClass('bdt-active');
                    primaryContent.addClass('bdt-active');
                    secondaryContent.removeClass('bdt-active');
                }
            });

        }

        

        if ('button' == $settings.switcherStyle) {

            var $tabs = $contentSwitcher.find('.bdt-switcher-content-wrapper');
            var $tab = $contentSwitcher.find('.bdt-content-switcher-tab');

            $tab.on('click', function () {
                var $this = $(this);
                var id = $this.attr('id');
                var $content = $contentSwitcher.find('.bdt-switcher-content[data-content-id="' + id + '"]');

                $tab.removeClass('bdt-active');
                $this.addClass('bdt-active');

                $tabs.find('.bdt-switcher-content').removeClass('bdt-active');
                $content.addClass('bdt-active');
            });
            
        }
    }


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-content-switcher.default', widgetContentSwitcher);
    });


}(jQuery, window.elementorFrontend));

/**
 * End Content Switcher widget script
 */