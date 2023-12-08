( function( $ ) {
	"use strict";
	var WidgetDynamicDeviceHandler = function ($scope, $) {
		if($scope.find('.plus-device-carousal').length>0){
			var carousel_elem = $scope.find('.plus-device-carousal').eq(0),
			$self=carousel_elem,
			$uid=$self.data("id"),
			infinite=$self.data("infinite"),
			autoplay=$self.data("autoplay"),
			autoplay_speed=$self.data("autoplay_speed"),
			speed=$self.data("speed"),
            dots=$self.data("dots"),
            arrows=$self.data("arrows"),
			rtl_crsl=false;
			if($("body").hasClass("rtl")){
				rtl_crsl=true;			
			}
			if(!$('.'+$uid).hasClass("done-carousel")){
				$('.'+$uid).slick({
					arrows:arrows,
					dots:dots,
					infinite: infinite,
					speed: speed,
					autoplay: autoplay,
					autoplaySpeed: autoplay_speed,
					centerMode: true,
					centerPadding: '0px',
					slidesToShow: 1,
					slidesToScroll: 1,
					draggable:true,
					variableWidth: true,
					rtl: rtl_crsl,
				});
				$('.'+$uid).addClass("done-carousel");
			}
		}

        if($scope.find('.plus-device-content').hasClass('tp-scroll-img-js')){
            $scope.find('.plus-device-content.tp-scroll-img-js').on("mouseenter",function() {
				if($(this).find(".elementor>.elementor-inner>.elementor-section-wrap").length){
                    $(this).find(".elementor>.elementor-inner>.elementor-section-wrap").addClass("active_on_scroll");
                }else{
                    $(this).addClass("active_on_scroll");
                }
            }).on("mouseleave", function() {
                if($(this).find(".elementor>.elementor-inner>.elementor-section-wrap").length){
                    $(this).find(".elementor>.elementor-inner>.elementor-section-wrap").removeClass("active_on_scroll");
                }else{
                    $(this).removeClass("active_on_scroll");
                }
			});
        }
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-dynamic-device.default', WidgetDynamicDeviceHandler);
	});
})(jQuery);