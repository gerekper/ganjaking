/*-magic scroll js-*/
( function ( $ ) {	
	'use strict';
	$(document).ready(function(){
		pt_plus_animateParalax();
	});
} ( jQuery ) );
		
function pt_plus_animateParalax() {
	'use strict';
	var $=jQuery;
	if($('body').find('.magic-scroll').length>0){
		var controller = new ScrollMagic.Controller();
		$('.magic-scroll').each(function(index, elem){
			var tween = 'tween-'+index;
			tween = new TimelineMax();
			var lengthBox = $(elem).find('.parallax-scroll').length;
			var scroll_offset=$(elem).find('.parallax-scroll').data("scroll_offset");
			var scroll_duration=$(elem).find('.parallax-scroll').data("scroll_duration");
			for(var i=0; i < lengthBox; i++){
				var speed = 0.5;
				var scroll_type=$(elem).find('.parallax-scroll').data("scroll_type");
				var scroll_x_from=$(elem).find('.parallax-scroll').data("scroll_x_from");
				var scroll_x_to=$(elem).find('.parallax-scroll').data("scroll_x_to");				
				var scroll_y_from=$(elem).find('.parallax-scroll').data("scroll_y_from");
				var scroll_y_to=$(elem).find('.parallax-scroll').data("scroll_y_to");
				var scroll_opacity_from=$(elem).find('.parallax-scroll').data("scroll_opacity_from");
				var scroll_opacity_to=$(elem).find('.parallax-scroll').data("scroll_opacity_to");
				var scroll_rotate_from=$(elem).find('.parallax-scroll').data("scroll_rotate_from");
				var scroll_rotate_to=$(elem).find('.parallax-scroll').data("scroll_rotate_to");
				var scroll_scale_from=$(elem).find('.parallax-scroll').data("scroll_scale_from");
				var scroll_scale_to=$(elem).find('.parallax-scroll').data("scroll_scale_to");
				
				var j1 = 0.2*(i+1);
				var k1 = 0.5*i;
				if(scroll_type=='position'){
					if(i==0) {
						
						tween.fromTo($(elem).find('.parallax-scroll:eq('+i+')'), 1, {scale:scroll_scale_from,rotation:scroll_rotate_from,opacity:scroll_opacity_from,x:-(scroll_x_from*speed),y:-(scroll_y_from*speed), ease: Linear.easeNone},{scale:scroll_scale_to,rotation:scroll_rotate_to,opacity:scroll_opacity_to,x:-(scroll_x_to*speed),y:-(scroll_y_to*speed), ease: Linear.easeNone})
					}else {
						tween.to($(elem).find('.parallax-scroll:eq('+i+')'), 1, {scale:scroll_scale_to,y:-(scroll_y_to*speed), ease: Linear.easeNone}, '-=1')
					}
				}
			}			
			new ScrollMagic.Scene({triggerElement: elem, duration: scroll_duration, triggerHook:.5,offset: scroll_offset})
				.setTween(tween)
				.addTo(controller);
		})
	}
}