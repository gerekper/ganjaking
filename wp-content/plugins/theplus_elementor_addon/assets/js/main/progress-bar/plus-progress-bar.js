( function( $ ) {
	"use strict";
	var WidgetProgressBarHandler = function($scope, $) {
		var container = $scope.find('.pt-plus-peicharts');
		if(container.length > 0){
			container.each(function(){
				var b=$(this);
				var e= $(this).find(".progress_bar-skill-bar-filled");
				b.waypoint(function(direction) {
					if (direction === 'down') {
						if(!b.hasClass("done-progress")){
							e.css("width", e.data("width"));
							if(b.find(".progress_bar-media.large")){
								b.find(".progress_bar-media.large").css("width", e.data("width"));
							}
							b.addClass("done-progress");
						}
					}
				}, { offset: '90%' });
			});
		}
	};
	$(window).on('elementor/frontend/init', function () {		
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-progress-bar.default', WidgetProgressBarHandler);
	});
})(jQuery);