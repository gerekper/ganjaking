(function ($) {
    var WidgetElements_DynamicCountdownHandler = function ($scope, $) {
        var elementSettings = dceGetElementSettings($scope);
        var id_scope = $scope.attr('data-id');
		var target = '.elementor-element-' + id_scope + ' .elementor-countdown-wrapper';

        if ( elementSettings.dynamic_due_date ) {
			var dynamic_due_date = elementSettings.dynamic_due_date;
            $( target ).attr( "data-date", dayjs(dynamic_due_date).unix() ); // Convert datetime to timestamp
        }
    };

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/countdown.default', WidgetElements_DynamicCountdownHandler);
    });
})(jQuery);
