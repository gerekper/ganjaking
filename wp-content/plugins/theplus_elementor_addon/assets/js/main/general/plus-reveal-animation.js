( function ( $ ) {
	'use strict';
	$(document).ready(function(){
		plus_reveal_animation();
	});
}( jQuery ));
function plus_reveal_animation(){
	'use strict';
	var $=jQuery;
	$('.pt-plus-reveal').each(function() {
		var b=$(this);
		var uid=b.data('reveal-id');
		var color_1=b.data('effect-color-1');
		var color_2=b.data('effect-color-2');
		$('head').append("<style type='text/css'>."+uid+".animated::before{background: "+color_2+";}."+uid+".animated::after{background: "+color_1+";}</style>");
		b.waypoint(function(direction) {
			if( direction === 'down'){
				if(b.hasClass("animated")){
					b.hasClass("animated");
					}else{
					b.addClass("animated");
				}
			}
		}, {triggerOnce: true,  offset: '85%' } );
	});
}