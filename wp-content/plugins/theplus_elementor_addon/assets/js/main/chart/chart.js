/*chart js*/
( function( $ ) {
	"use strict";
	var WidgetChartHandler = function ($scope, $) {
		var container = $scope.find('.tp-chart-wrapper'),
			canvas = container.find( '> canvas' ),
			data_settings  = container.data('settings');

		if(container.length){
			elementorFrontend.waypoint(canvas,function(){
				var $this = $(this),
				ctx = $this[0].getContext('2d'),
				myChart = new Chart(ctx,data_settings);
			}, {
				offset: 'bottom-in-view'
			} );
		}		
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-chart.default', WidgetChartHandler);
	});
})(jQuery);