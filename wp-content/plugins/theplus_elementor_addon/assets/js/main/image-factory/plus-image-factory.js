function plus_bgimage_scrollparallax(){
	"use strict";
	var $=jQuery;
	if($('body').find('.creative-simple-img-parallax').length>0){
		var controller = new ScrollMagic.Controller();
		$('.creative-simple-img-parallax').each(function(index, elem){
			var data_parallax =$(this).data("scroll-parallax");
			data_parallax = -(data_parallax);
			var parallax_image=$('.simple-parallax-img',this);
			var tween = 'tween-'+index;
			tween = new TimelineMax();
			new ScrollMagic.Scene({
                triggerElement: elem,
				duration: '150%'
			}).setTween(tween.from(parallax_image, 1, {x:data_parallax,ease: Linear.easeNone})).addTo(controller);;
		});
	}
}
( function ( $ ) {
	'use strict';
	$(document).ready(function(){
		plus_bgimage_scrollparallax();
		if($('.pt_plus_animated_image.bg-img-animated').length > 0){
			$('.pt_plus_animated_image.bg-img-animated').each(function() {
				var b=$(this);
				b.waypoint(function(direction) {
					if( direction === 'down'){
						if(b.hasClass("creative-animated")){
							b.hasClass("creative-animated");
							}else{
							b.addClass("creative-animated");
						}
					}
				}, {triggerOnce: true,  offset: '90%' } );
			});
		}
	});
} ( jQuery ) );