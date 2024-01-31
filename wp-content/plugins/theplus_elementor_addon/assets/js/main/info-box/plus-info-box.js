(function($) {
	"use strict";
    var WidgetInfoBoxHandler = function($scope, $) {
        var container = $scope.find('.pt_plus_info_box.list-carousel-slick'),
            uid = container.data('id'),
            loop_item = container.find(".info-box-inner").closest('.slick-slide'),
            data_conn = container.data("connection"),
            data_eventtype = container.data("eventtype");			
		if(data_conn!='' && data_conn!=undefined){
			if(data_conn!='' && data_eventtype=='con_pro_hover'){
				loop_item.on("mouseenter", function() {
					$(this ).closest('.pt_plus_info_box').find(".info-box-inner").removeClass("tp-info-active");
					$(this).addClass("tp-info-active");
					var Connection=$(this).closest(".pt_plus_info_box").data('connection');
					if(Connection!='' && Connection!=undefined){
						var index = $(this).data("slick-index");
						plus_infobox_connection(parseInt(index),Connection);
					}
				});
			}else if(data_conn!='' && data_eventtype=='con_pro_click'){
				loop_item.click(function(){
					$(this ).closest('.pt_plus_info_box').find(".info-box-inner").removeClass("tp-info-active");
					$(this).addClass("tp-info-active");
					var Connection=$(this).closest(".pt_plus_info_box").data('connection');
					if(Connection!='' && Connection!=undefined){
						var index = $(this).data("slick-index");
						plus_infobox_connection(parseInt(index),Connection);
					}
				});
			}
			$('.'+uid+' > .post-inner-loop').on('beforeChange', function(e, slick, currentSlide, nextSlide) {
				if(currentSlide!=nextSlide){
					if(!$("."+uid).find('.info-box-inner[data-slick-index="'+parseInt(nextSlide)+'"]').hasClass("tp-info-active")){
						$("."+uid).find('.info-box-inner').removeClass("tp-info-active");
						$("."+uid).find('.info-box-inner[data-slick-index="'+parseInt(nextSlide)+'"]').addClass("tp-info-active");
						var conn = $("."+uid).data('connection');
						if(conn!='' && conn!=undefined){
							plus_infobox_connection(parseInt(nextSlide),conn);
						}
					}
				}
			});
		}
    };
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-info-box.default', WidgetInfoBoxHandler);
    });
})(jQuery);
function plus_infobox_connection(index,connection){
	"use strict";
	var $=jQuery;
	if(connection!='' && $("."+connection).length==1){
		var current=$('.'+connection+' > .post-inner-loop').slick('slickCurrentSlide');
		if(current!=(index)){
			$('.'+connection+' > .post-inner-loop').slick('slickGoTo', index);
		}
	}
}