(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumImageCompareHandler = function ($scope, $) {

            var $imgCompareElem = $scope.find(".premium-images-compare-container"),
                settings = $imgCompareElem.data("settings");

            $imgCompareElem.imagesLoaded(function () {
                $imgCompareElem.twentytwenty({
                    orientation: settings.orientation,
                    default_offset_pct: settings.visibleRatio,
                    switch_before_label: settings.switchBefore,
                    before_label: settings.beforeLabel,
                    switch_after_label: settings.switchAfter,
                    after_label: settings.afterLabel,
                    move_slider_on_hover: settings.mouseMove,
                    click_to_move: settings.clickMove,
                    show_drag: settings.showDrag,
                    show_sep: settings.showSep,
                    no_overlay: settings.overlay,
                    horbeforePos: settings.beforePos,
                    horafterPos: settings.afterPos,
                    verbeforePos: settings.verbeforePos,
                    verafterPos: settings.verafterPos
                });
            });
        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-image-comparison.default', PremiumImageCompareHandler);
    });
})(jQuery);