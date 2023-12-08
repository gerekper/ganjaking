(function($) {
	"use strict";
	var WidgetPricingTableHandler = function ($scope, $) {		
		var $target = $('.plus-pricing-table', $scope);
		if($target.length){
			var $read_more =$target.find(".read-more-options");
			var default_load =$target.find(".read-more-options").data("default-load");
			var $ul_listing =$target.find(".pricing-content-wrap.listing-content.style-1 .plus-icon-list-items");
			$ul_listing.each(function(){				   
			   $(this).find("li:gt("+default_load+")").hide();
			});
			$read_more.on("click", function(e){
				e.preventDefault();
				var a=$(this),$less_text=a.data("less-text");
				var $more_text=a.data("more-text");
				if(a.hasClass("more")){
				   a.parent(".pricing-content-wrap.listing-content").find(".plus-icon-list-items li").show();
				   a.text($less_text).addClass("less").removeClass("more");
				}else if(a.hasClass("less")){
				   a.parent(".pricing-content-wrap.listing-content").find(".plus-icon-list-items li:gt("+default_load+")").hide();
				   a.text($more_text).addClass("more").removeClass("less");
				}
			});
		}
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-pricing-table.default', WidgetPricingTableHandler);
	});
})(jQuery);