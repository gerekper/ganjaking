( function( $ ) {
	"use strict";
	var WidgetHoverTiltHandler = function ($scope, $) {
		var wid_sec=$scope.parents('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con');
		if(wid_sec.find(".hover-tilt").length){			
			$(".hover-tilt").hover3d({
				selector: ".blog-list-style-content,> .addbanner-block,> .addbanner_product_box,> .vc_single_image-wrapper,> .cascading-inner-loop,.info-box-bg-box",
				shine: !1,
				invert: !0,
				sensitivity: 20,
			});
		}
	};
$(window).on('elementor/frontend/init', function () {
	elementorFrontend.hooks.addAction('frontend/element_ready/global', WidgetHoverTiltHandler);
	});
})(jQuery);