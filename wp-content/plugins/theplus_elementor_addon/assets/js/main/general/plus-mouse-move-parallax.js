/*MouseMove Paralalx*/
( function ( $ ) {
	'use strict';
	$(document).ready(function(){
		plus_mousemove_parallax();
	});
}( jQuery ));
function plus_mousemove_parallax(){
	"use strict";
		var $=jQuery,
			$parallaxContainer= $(".pt-plus-move-parallax"),
			$parallaxItems= $parallaxContainer.find(".parallax-move"),
			fixer  = 0.0008;
		if($parallaxContainer.length > 0){
			$(".pt-plus-move-parallax").on("mouseleave", function(event){
				var pageX =  event.pageX - ($(this).width() * 0.5);
				var pageY =  event.pageY - ($(this).height() * 0.5);
				$(this).find(".parallax-move").each(function(){
					var item 	= $(this);
					var speedX	= item.data("move_speed_x");  				
					var speedY	= item.data("move_speed_y");
					TweenLite.to(item,0.9,{
						x: (0)*fixer,
						y: (0)*fixer
					});
				});
			});
			
			$parallaxContainer.on('mousemove', function(e){
				$(this).find(".parallax-move").each(function(){
					var item 	= $(this);
					var speedX	= item.data("move_speed_x");
					var speedY	= item.data("move_speed_y");
					$(this).parallaxmovecontent(speedX,speedY, e);
				});
			});
			$.fn.parallaxmovecontent = function (resistancex, resistancey, mouse ) {
				var $el = $( this );
				TweenLite.to( $el, 0.5, {
					x : -(( mouse.clientX - (window.innerWidth/2) ) / resistancex),
					y : -(( mouse.clientY - (window.innerHeight/2) ) / resistancey)
				});
			};
		}
	}