(function ($) {
	"use strict";
/*Shape Divider*/
	var WidgetShapeDividerHandler = function($scope, $) {
		var tp_shape_divider = $scope.find('.tp-plus-shape-divider');
		var tp_shape_position = tp_shape_divider.data("position");
		var tp_section_type = tp_shape_divider.data("section-type");
		
		var parent_row= tp_shape_divider.closest('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con');
		var column_row= tp_shape_divider.closest('.elementor-column');
		var wid_sec=$scope.closest('section.elementor-element,.elementor-element.e-container,.elementor-element.e-con');
		
		if(wid_sec.length){
			var widget_remove_old=$(wid_sec).find(".tp-plus-shape-divider");
			if(widget_remove_old.length > 0){
				var update_id=tp_shape_divider.data("id");
				$(widget_remove_old).each(function(){
					var ids=$(this).data("id");
					if(ids==update_id){
						$("."+ids).remove();
					}
					if(ids!=undefined){
						var res = ids.replace("shape", "");
						var remove_widget=$(wid_sec).find(".elementor-element-"+res);
						if(remove_widget.length==0){
							$("."+ids).remove();
						}
					}
				});
			}
		}
		if(parent_row && tp_section_type!='column'){
			if(tp_shape_position=='top'){
				$( parent_row ).prepend(tp_shape_divider);
			}else{
				$( parent_row ).append(tp_shape_divider);
			}
			parent_row.css("position","relative");
			var bg_sec=$(wid_sec).find("> .tp-plus-shape-divider");
			if(bg_sec.data("section-hidden") !=undefined || bg_sec.data("section-hidden") !=''){
				if(!parent_row.hasClass("elementor-element-edit-mode")){
					parent_row.css("overflow",bg_sec.attr("data-section-hidden"));
				}
			}
		}else{
			if(tp_shape_position=='right' || tp_shape_position=='left'){
				$( window ).on( "load resize", function() {
					var sec_height=column_row.height();
					$(tp_shape_divider).css("width",sec_height+"px");
				});
				setTimeout(function(){
					var sec_height=column_row.height();
					var sec_offset=tp_shape_divider.offset();
					$(tp_shape_divider).css("width",sec_height+"px");
				}, 150);
			}
			$( column_row ).append(tp_shape_divider);
		}
		
		if(tp_shape_divider.hasClass('shape-wave')){
			tp_shape_divider.find('.wave-items').each(function() {				
				var _color = $(this).data('color') ? $(this).data('color') : '#8072fc'
				  , _height = $(this).data('height') ? $(this).data('height') : 80
				  , _bones = $(this).data('bones') ? $(this).data('bones') : 4
				  , _amplitude = $(this).data('amplitude') ? $(this).data('amplitude') : 40
				  , _speed = $(this).data('speed') ? $(this).data('speed') : 0.15
				  , _gradient_id = $(this).data('gradient-id') ? $(this).data('gradient-id') : '';
				if(_gradient_id!='' && _gradient_id!=undefined){
					_color = "url("+_gradient_id+")";
				}
				$(this).children('path').wavify({
					height: _height,
					bones: _bones,
					amplitude: _amplitude,
					color: _color,
					speed: _speed
				});
			});
		}
	};
	/*Shape Divider*/
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-shape-divider.default', WidgetShapeDividerHandler);
	});
})(jQuery);