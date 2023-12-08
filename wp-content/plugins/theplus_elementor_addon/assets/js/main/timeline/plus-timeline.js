(function ($) {
	"use strict";
	var WidgetTimeLineContentHandler = function(e, a) {
		var container = e.find('.pt-plus-timeline-list');
		if(container.hasClass("end-pin-none")){
			var start_icon=container.find('.timeline-item-wrap').first().find('.timeline-inner-block .point-icon');
			var end_icon=container.find('.timeline-item-wrap').last().find('.timeline-inner-block .point-icon');
			if(start_icon.length){
				start_icon=start_icon.offset().top;
			}else{
				start_icon=0;
			}
			if(end_icon.length){
				end_icon=end_icon.offset().top;
			}else{
				end_icon=0;
			}
			var total_height=end_icon-start_icon;
			var offset_top=0;
			if(!container.hasClass("start-pin-none")){
				offset_top=50;
			}
			if(container.hasClass("start-pin-none")){
				offset_top=-10;
			}
			container.find(".post-inner-loop .timeline-track").css("height",total_height + offset_top );
			
			container.find(".post-inner-loop").on( 'arrangeComplete', function() {
				var start_icon=container.find('.timeline-item-wrap').first().find('.timeline-inner-block .point-icon');
				var end_icon=container.find('.timeline-item-wrap').last().find('.timeline-inner-block .point-icon');
				if(start_icon.length){
					start_icon=start_icon.offset().top;
				}else{
					start_icon=0;
				}
				if(end_icon.length){
					end_icon=end_icon.offset().top;
				}else{
					end_icon=0;
				}
				var total_height=end_icon-start_icon;
				var offset_top=0;
				if(!container.hasClass("start-pin-none")){
					offset_top=50;
				}
				if(container.hasClass("start-pin-none")){
					offset_top=-10;
				}
				container.find(".post-inner-loop .timeline-track").css("height",total_height + offset_top );					
			});
		}
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-timeline.default', WidgetTimeLineContentHandler);
	});
})(jQuery);