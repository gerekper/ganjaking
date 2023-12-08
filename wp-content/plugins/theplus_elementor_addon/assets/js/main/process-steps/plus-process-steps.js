(function($) {
	"use strict";
    var WidgetProcessStepsHandler = function($scope, $) {
        var container = $scope.find('.tp-process-steps-widget'),
            loop_item = container.find(".tp-process-steps-wrapper"),
            data_conn = container.data("connection"),
            data_eventtype = container.data("eventtype");

        if (container.hasClass('style_2')) {
            var w = $(window).innerWidth();
            if (w >= 768) {
				var total_item = loop_item.length;
				var divWidth = container.width();
				var margin = total_item * 20;

				var new_divWidth = divWidth - margin;
				var per_box_width = new_divWidth / total_item;
				loop_item.css('width', per_box_width);
					
                $(window).on('resize', function() {                    
                    var total_item = loop_item.length;
                    var divWidth = container.width();
                    var margin = total_item * 20;

                    new_divWidth = divWidth - margin;
                    per_box_width = new_divWidth / total_item;
                    loop_item.css('width', per_box_width);

                });
            }
        }
		if(data_conn!='' && data_conn!=undefined){
			if(data_conn!='' && data_eventtype=='con_pro_hover'){
				loop_item.on("mouseenter", function() {
					$(this ).closest('.tp-process-steps-widget').find(".tp-process-steps-wrapper").removeClass("active");
					$(this).addClass("active");
					var Connection=$(this).closest(".tp-process-steps-widget").data('connection');
					if(Connection!='' && Connection!=undefined){
						var index = $(this).index();
						plus_process_step_connection(parseInt(index),Connection);
					}
				});
			}else if(data_conn!='' && data_eventtype=='con_pro_click'){
				loop_item.on('click',function(){
					$(this ).closest('.tp-process-steps-widget').find(".tp-process-steps-wrapper").removeClass("active");
					$(this).addClass("active");
					var Connection=$(this).closest(".tp-process-steps-widget").data('connection');
					if(Connection!='' && Connection!=undefined){
						var index = $(this).index();
						plus_process_step_connection(parseInt(index),Connection);
					}					
				});
			}
		}
    };
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-process-steps.default', WidgetProcessStepsHandler);
    });
})(jQuery);
function plus_process_step_connection(index,connection){
	"use strict";
	var $=jQuery;
	if(connection!='' && $("."+connection).length  > 0){
		var current=$('.'+connection+' > .post-inner-loop').slick('slickCurrentSlide');
		if(current!=(index)){
			$('.'+connection+' > .post-inner-loop').slick('slickGoTo', index);
		}
	}
}