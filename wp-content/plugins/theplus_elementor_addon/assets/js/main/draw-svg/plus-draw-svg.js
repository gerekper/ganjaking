/*Draw Svg*/( function ( $ ) {	
	'use strict';
	$.fn.pt_plus_animated_svg = function() {
		return this.each(function() {
			var $self = $(this);
			var data_id=$self.data("id");
			var data_duration=$self.data("duration");
			var data_type=$self.data("type");
			var data_stroke=$self.data("stroke");
			var svg_fill_enable=$self.data("svg_fill_enable");
			var svg_loop_enable=$self.data("svg_loop_enable");
			var data_fill_color=$self.data("fill_color");
			var fill_hover_color = $self.data("fillhover");
			var stroke_hover_color=$self.data("strokehover");
			/*if($self.find(".info_box_svg").length > 0){
				$self.find(".info_box_svg > svg").attr("id",data_id);
				new Vivus(data_id, {type: data_type, duration: data_duration,forceRender:false,start: 'inViewport'});
			}else{ */
				var drawSvg = new Vivus(data_id, {type: data_type, duration: data_duration,forceRender:false,start: 'inViewport',onReady: function (myVivus) {
					var c=myVivus.el.childNodes;
					var show_id=document.getElementById(data_id);
					if(svg_fill_enable!='' && svg_fill_enable=='yes'){
						myVivus.el.style.fillOpacity='0';
						myVivus.el.style.transition='fill-opacity 0s';
					}
					show_id.style.opacity = "1";
					if(data_stroke!=''){
						for (var i = 0; i < c.length; i++) {
							$(c[i]).attr("fill", data_fill_color);
							$(c[i]).attr("stroke",data_stroke);
							var child=c[i];
							var pchildern=child.children;
							if(pchildern != undefined){
								for(var j=0; j < pchildern.length; j++){
									$(pchildern[j]).attr("fill", data_fill_color);
									$(pchildern[j]).attr("stroke",data_stroke);
								}
							}
						}
					}
					if(stroke_hover_color!='' || fill_hover_color!=''){
						var fillc='',strokec='';
						if(stroke_hover_color!=''){
							strokec = 'stroke:'+stroke_hover_color+' !important;'
						}
						if(fill_hover_color!=''){
							fillc = 'fill:'+fill_hover_color+' !important;'
						}
						myVivus.el.insertAdjacentHTML('afterbegin', '<style>svg *{-webkit-transition: all .3s;moz-transition: all .3s;-o-transition: all .3s;-ms-transition: all .3s;transition:all .3s;}svg.hoversvg *{'+strokec+fillc+' }</style>');
						if($self.closest('.info-box-inner').length > 0){
							$self.closest('.info-box-inner').on("mouseover", function(){
								myVivus.el.classList.add('hoversvg')
							});
							$self.closest('.info-box-inner').on("mouseout", function(){
								myVivus.el.classList.remove('hoversvg');
							});
						}
					}
					
					
					
					
				}
				}, function (myVivus) {
                    if(myVivus.getStatus() === 'end' && svg_loop_enable!='' && svg_loop_enable=='yes'){
                        myVivus.reset().play();
                    }
					if(myVivus.getStatus() === 'end' && svg_fill_enable!='' && svg_fill_enable=='yes'){
						myVivus.el.style.fillOpacity='1';
						myVivus.el.style.transition='fill-opacity 1s';
					}
				} );
				/*window.addEventListener("scroll", function(e) {
					var scrollPercentage = (document.documentElement.scrollTop + $("#"+data_id).scrollTop()) / (document.documentElement.scrollHeight - document.documentElement.clientHeight);
					drawSvg.setFrameProgress(scrollPercentage);
				});*/
			//}
		});
	};
	
	$(window).on("load",function() {
		setTimeout(function(){
			$('.pt_plus_row_bg_animated_svg').pt_plus_animated_svg();
			$('.pt_plus_animated_svg,.ts-hover-draw-svg').pt_plus_animated_svg();
			$('body').find('.pt_plus_row_bg_animated_svg').attr('style', 'stroke:black');
		}, 100);
	});
	$(document).ready(function() {
		$('.plus-hover-draw-svg .svg_inner_block').on("mouseenter",function() {
			var $self;
			$self = $(this).parent();
			var data_id=$self.data("id");
			var data_duration=$self.data("duration");
			var data_type=$self.data("type");
			new Vivus(data_id, {type: data_type, duration: data_duration,start: 'inViewport'}).play();
		}).on("mouseleave", function() {                      
		});
	});
} ( jQuery ) );