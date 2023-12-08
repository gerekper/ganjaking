(function($) {
	"use strict";
	var WidgetHotspotHandler = function ($scope, $) {
		var $target = $('.theplus-hotspot', $scope);
		if($target.length){
			var $overlay_color =$target.find(".theplus-hotspot-inner.overlay-bg-color");
			var $pin_hover =$target.find(".pin-hotspot-loop");
			if($overlay_color.length > 0){
				$pin_hover.mouseover(function() {						
					$(this).closest(".theplus-hotspot-inner.overlay-bg-color").addClass("on-hover");
				}).mouseout(function() {
					$(this).closest(".theplus-hotspot-inner.overlay-bg-color").removeClass("on-hover");
				});
			}
		}		
	};
	$(window).on('elementor/frontend/init', function () {		
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-hotspot.default', WidgetHotspotHandler);		
	});
})(jQuery);