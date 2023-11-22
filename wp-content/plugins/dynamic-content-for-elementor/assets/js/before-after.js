var isAdminBar = false,
    isEditMode = false;

(function ($) {

    var WidgetElements_TwentyTwentyHandler = function ($scope, $) {
        var elementSettings = dceGetElementSettings($scope);
        var scene = $scope.find('.twentytwenty-container');
        scene.imagesLoaded(() => {
			scene.twentytwenty({
                default_offset_pct: (Number(elementSettings.offset_pict.size) / 100) || 0.5, //0.5, // How much of the before image is visible when the page loads
                orientation: elementSettings.orientation || 'horizontal', // Orientation of the before and after images ('horizontal' or 'vertical')
                before_label: String(elementSettings.before_label) || 'Before', // Set a custom before label
                after_label: String(elementSettings.after_label) || 'After', // Set a custom after label
                no_overlay: Boolean( elementSettings.no_overlay ), // Do not show the overlay with before and after
                move_slider_on_hover: Boolean( elementSettings.move_slider_on_hover ), // Move slider on mouse hover?
                move_with_handle_only: Boolean( elementSettings.move_with_handle_only ), // Allow a user to swipe anywhere on the image to control slider movement.
                click_to_move: Boolean( elementSettings.click_to_move ) // Allow a user to click (or tap) anywhere on the image to move the slider to that location.
            });
		});
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-twentytwenty.default', WidgetElements_TwentyTwentyHandler);
    });
})(jQuery);
