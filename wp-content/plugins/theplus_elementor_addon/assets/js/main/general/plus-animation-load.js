/*--- on load animation ----*/
( function ( $ ) {
	'use strict';
	$(document).ready(function() {
		"use strict";
		$('.animate-general').each(function() {
			var c, p=$(this);
			if(!p.hasClass("animation-done")){
				if(p.find('.animated-columns').length){
					var b = $('.animated-columns',this);				
					var delay_time=p.data("animate-delay");
					
					c = p.find('.animated-columns');
					c.each(function() {
						$(this).css("opacity", "0");
					});
					
					}else{			
					var b=$(this);
					var delay_time=b.data("animate-delay");
					
					if(b.data("animate-item")){
						c = b.find(b.data("animate-item"));
						c.each(function() {
							$(this).css("opacity", "0");
						});
						}else{
						b.css("opacity", "0");
					}
				}
			}
		});
		
		var d = function() {
			$('.animate-general').each(function() {
				var c, d, p=$(this), e = "85%";
				var id=$(this).data("id");
				if(p.data("animate-columns")=="stagger"){
					var b = $('.animated-columns',this);
					var animation_stagger=p.data("animate-stagger");
					var delay_time=p.data("animate-delay");
					var out_delay_time=p.data("animate-out-delay");
					var duration_time=p.data("animate-duration");
					var out_duration_time=p.data("animate-out-duration");
					var d = p.data("animate-type");
					var o = p.data("animate-out-type");												
					var animate_offset = p.data("animate-offset");
					
					p.css("opacity","1");
					c = p.find('.animated-columns');
					p.waypoint(function(direction) {
						if( direction === 'down'){
							if(!c.hasClass("animation-done")){
								c.addClass("animation-done").removeClass("animation-out-done").velocity(d,{ delay: delay_time,duration: duration_time,display:'auto',stagger: animation_stagger});
							}
						}else if (direction === 'up' && o!='' && o!=undefined && !c.hasClass("animation-out-done")) {
							c.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto',stagger: animation_stagger});
						}
					}, { offset: animate_offset } );
					if(c){
						$('head').append("<style type='text/css'>."+id+" .animated-columns.animation-done{opacity:1;}</style>")
					}
					
					}else if(p.data("animate-columns")=="columns"){
					
					var b = $('.animated-columns',this);
					var delay_time=p.data("animate-delay");
					var out_delay_time=p.data("animate-out-delay");
					var d = p.data("animate-type");
					var o = p.data("animate-out-type");	
					var animate_offset = p.data("animate-offset");
					var duration_time=p.data("animate-duration");
					var out_duration_time=p.data("animate-out-duration");
					p.css("opacity","1");
					c = p.find('.animated-columns');
					c.each(function() {
						var bc=$(this);
						bc.waypoint(function(direction) {
							if( direction === 'down'){
								if(!bc.hasClass("animation-done")){
									bc.addClass("animation-done").removeClass("animation-out-done").velocity(d,{ delay: delay_time,duration: duration_time,drag:true,display:'auto'});
								}
							}else if (direction === 'up' && o!='' && o!=undefined && !bc.hasClass("animation-out-done")) {
								bc.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto'});
							}
						}, { offset: animate_offset } );
					});
					if(c){
						$('head').append("<style type='text/css'>."+id+" .animated-columns.animation-done{opacity:1;}</style>")
					}
					}else{
					var b = $(this);
					var delay_time=b.data("animate-delay");
					var out_delay_time=b.data("animate-out-delay");
					var duration_time=b.data("animate-duration");
					var out_duration_time=p.data("animate-out-duration");
					d = b.data("animate-type"),
					o = b.data("animate-out-type"),
					animate_offset = b.data("animate-offset"),
					b.waypoint(function(direction ) {
						if( direction === 'down'){
							if(!b.hasClass("animation-done")){
								b.addClass("animation-done").removeClass("animation-out-done").velocity(d, {delay: delay_time,duration: duration_time,display:'auto'});
							}
						}else if (direction === 'up' && o!='' && o!=undefined && !b.hasClass("animation-out-done")) {
							if(!b.hasClass("animation-out-done")){
								b.addClass("animation-out-done").removeClass("animation-done").velocity(o,{ delay: out_delay_time,duration: out_duration_time,display:'auto' });
							}
						}
					}, { offset: animate_offset } );
				}
			})
		},
		e = function() {
			$(".call-on-waypoint").each(function() {
				var c = $(this);
				c.waypoint(function() {
					c.trigger("on-waypoin")
					}, {
					triggerOnce: !0,
					offset: "bottom-in-view"
				})
			})
		};

        function tpLoad(){
			$(document).ready(e),$(window).on("load",e),
			$(document.body).on('post-load', function() {
				e()
			}),
			$(document).ready(d),$(window).on("load",d),
			$(document.body).on('post-load', function() {
				d()
			});
			$(document).ready(function(){
				e(); d();
			});
        }

		var AnimateTP = document.querySelectorAll('.animate-general')
		AnimateTP.forEach(function(self){
			if(!self.closest('.tp-horizontal-scroll-wrapper')){
				tpLoad()
			}
		})
		
	});
} ( jQuery ) );
/*--- on load animation ----*/