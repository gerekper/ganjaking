/*--- on load animation ----*/
jQuery(document).ready(function() {
	"use strict";
	jQuery('.animate-general').each(function() {
		var c, p=jQuery(this);
		if(p.find('.animated-columns').length){
			var b = jQuery('.animated-columns',this);				
			var delay_time=p.data("animate-delay");
			
			c = p.find('.animated-columns');
			c.each(function() {
				jQuery(this).css("opacity", "0");
			});
			
			}else{			
			var b=jQuery(this);
			var delay_time=b.data("animate-delay");
			
			if(b.data("animate-item")){
				c = b.find(b.data("animate-item"));
				c.each(function() {
					jQuery(this).css("opacity", "0");
				});
				}else{
				b.css("opacity", "0");
			}
		}
	});
	jQuery('.pt-plus-reveal').each(function() {
	var b=jQuery(this);
	var uid=b.data('reveal-id');
		var color_1=b.data('effect-color-1');
		var color_2=b.data('effect-color-2');
		jQuery('head').append("<style type='text/css'>."+uid+".animated::before{background: "+color_2+";}."+uid+".animated::after{background: "+color_1+";}</style>");
		b.waypoint(function(direction) {
						if( direction === 'down'){
							if(b.hasClass("animated")){
								b.hasClass("animated");
							}else{
								b.addClass("animated");
							}
						}
		}, {triggerOnce: true,  offset: '90%' } );
	});
	var d = function() {
		jQuery('.animate-general').each(function() {
			var c, d, p=jQuery(this), e = "85%";
			var id=jQuery(this).data("id");
			if(p.data("animate-columns")=="stagger"){
				var b = jQuery('.animated-columns',this);
				var animation_stagger=p.data("animate-stagger");
				var delay_time=p.data("animate-delay");
				var d = p.data("animate-type");
				p.css("opacity","1");
				c = p.find('.animated-columns');
				p.waypoint(function(direction) {
						if( direction === 'down'){
							if(c.hasClass("animation-done")){
								c.hasClass("animation-done");
							}else{
								c.addClass("animation-done").velocity(d,{ delay: delay_time,display:'auto',stagger: animation_stagger});
							}
						}
				}, {triggerOnce: true, offset: '120%'} );
				if(c){
					jQuery('head').append("<style type='text/css'>."+id+" .animated-columns.animation-done{opacity:1;}</style>")
				}
			}else if(p.data("animate-columns")=="columns"){
				var b = jQuery('.animated-columns',this);
				var delay_time=p.data("animate-delay");
				var d = p.data("animate-type");
				p.css("opacity","1");
				c = p.find('.animated-columns');
				c.each(function() {
					var bc=jQuery(this);
					bc.waypoint(function(direction) {
						if( direction === 'down'){
							if(bc.hasClass("animation-done")){
								bc.hasClass("animation-done");
							}else{
								bc.addClass("animation-done").velocity(d,{ delay: delay_time,display:'auto'});
							}
						}
					}, {triggerOnce: true, offset: '120%'} );
				});
				if(c){
					jQuery('head').append("<style type='text/css'>."+id+" .animated-columns.animation-done{opacity:1;}</style>")
				}
			}else{
				var b = jQuery(this);
				var delay_time=b.data("animate-delay");
				d = b.data("animate-type"),
				b.waypoint(function(direction ) {
					if( direction === 'down'){
						if(b.hasClass("animation-done")){
							b.hasClass("animation-done");
						}else{
							b.addClass("animation-done").velocity(d, {delay: delay_time,display:'auto'});
						}
					}
				}, {triggerOnce: true,  offset: '90%' } );
			}
		})
	},
	e = function() {
		jQuery(".call-on-waypoint").each(function() {
			var c = jQuery(this);
			c.waypoint(function() {
				c.trigger("on-waypoin")
                }, {
				triggerOnce: !0,
				offset: "bottom-in-view"
			})
		})
	};
	jQuery(window).load(e),
	jQuery(document.body).on('post-load', function() {
		e()
	}),
	jQuery(window).load(d),
	jQuery(document.body).on('post-load', function() {
		d()
	});
	
});
/*--- on load animation ----*/
/*-video post ! fluidvids.js v2.4.1 | (c) 2014 @toddmotto | https://github.com/toddmotto/fluidvids */
!function(e,t){"function"==typeof define&&define.amd?define(t):"object"==typeof exports?module.exports=t:e.fluidvids=t()}(this,function(){"use strict";function e(e){return new RegExp("^(https?:)?//(?:"+d.players.join("|")+").*$","i").test(e)}function t(e,t){return parseInt(e,10)/parseInt(t,10)*100+"%"}function i(i){if((e(i.src)||e(i.data))&&!i.getAttribute("data-fluidvids")){var n=document.createElement("div");i.parentNode.insertBefore(n,i),i.className+=(i.className?" ":"")+"fluidvids-item",i.setAttribute("data-fluidvids","loaded"),n.className+="fluidvids",n.style.paddingTop=t(i.height,i.width),n.appendChild(i)}}function n(){var e=document.createElement("div");e.innerHTML="<p>x</p><style>"+o+"</style>",r.appendChild(e.childNodes[1])}var d={selector:["iframe","object"],players:["www.youtube.com","player.vimeo.com"]},o=[".fluidvids {","width: 100%; max-width: 100%; position: relative;","}",".fluidvids-item {","position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;","}"].join(""),r=document.head||document.getElementsByTagName("head")[0];return d.render=function(){for(var e=document.querySelectorAll(d.selector.join()),t=e.length;t--;)i(e[t])},d.init=function(e){for(var t in e)d[t]=e[t];d.render(),n()},d});
(function($){
	"use strict";
	var initFluidVids = function() {
		fluidvids.init({ selector: ['iframe:not(.pt-plus-bg-video)'],players: ['www.youtube.com', 'player.vimeo.com']})
	};
	$(window).on('load', initFluidVids);
	$('body').on('post-load', initFluidVids);
})(jQuery);
/*-video post ----*/

/*---- gmaps js-------------------*/
(function($) {
    'use strict';
	$(document).ready(function() {
		   $(".pt-plus-overlay-map-content").each(function() {
			   var uid= $(this).data('uid');
			   var desc_color = $(this).data( 'desc_color');
			 var toggle_btn_color=$(this).data('toggle-btn-color');
			var toggle_active_color=$(this).data('toggle-active-color');
			
		   $('head').append('<style >.'+uid+' .gmap-desc,.'+uid+' .gmap-desc p{color :'+desc_color+';}.checked-'+uid+':not(checked) + .check-label-'+uid+':after,.checked-'+uid+' + .check-label-'+uid+':before{border-color: '+toggle_btn_color+';}.checked-'+uid+':checked + .check-label-'+uid+':after{    border-color: '+toggle_active_color+';}</style>');
		  });
		var elements = document.querySelectorAll('.pt-plus-adv-map');
		Array.prototype.forEach.call(elements, function(el) {
			var $this = $(el),
			data_id = $this.data( 'id' ),
			data = $this.data( 'adv-maps' ),
			data_style = $this.data( 'map-style' ),
			map = null,
            bounds = null,
            infoWindow = null,
            position = null;
			var styles1='';
			
			
			if(data_style=='style-1'){
				styles1='[{"featureType":"all","elementType":"all","stylers":[{"hue":"#ff0000"},{"saturation":-100},{"lightness":-30}]},{"featureType":"all","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"color":"#353535"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#656565"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#505050"}]},{"featureType":"poi","elementType":"geometry.stroke","stylers":[{"color":"#808080"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#454545"}]}]';
				}else if(data_style=='style-2'){
				styles1='[{"featureType":"administrative","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","elementType":"all","stylers":[{"saturation":-100},{"lightness":"50"},{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"lightness":"30"}]},{"featureType":"road.local","elementType":"all","stylers":[{"lightness":"40"}]},{"featureType":"transit","elementType":"all","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]},{"featureType":"water","elementType":"labels","stylers":[{"lightness":-25},{"saturation":-100}]}]';
				}else if(data_style=='style-3'){
				styles1='[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]';
				}else if(data_style=='style-4'){
				styles1='[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]';
				}else if(data_style=='style-5'){
				styles1='[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]';
				}else if(data_style=='style-6'){
				styles1='[{"elementType":"geometry","stylers":[{"hue":"#ff4400"},{"saturation":-68},{"lightness":-4},{"gamma":0.72}]},{"featureType":"road","elementType":"labels.icon"},{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"hue":"#0077ff"},{"gamma":3.1}]},{"featureType":"water","stylers":[{"hue":"#00ccff"},{"gamma":0.44},{"saturation":-33}]},{"featureType":"poi.park","stylers":[{"hue":"#44ff00"},{"saturation":-23}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"hue":"#007fff"},{"gamma":0.77},{"saturation":65},{"lightness":99}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"gamma":0.11},{"weight":5.6},{"saturation":99},{"hue":"#0091ff"},{"lightness":-86}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"lightness":-48},{"hue":"#ff5e00"},{"gamma":1.2},{"saturation":-23}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"saturation":-64},{"hue":"#ff9100"},{"lightness":16},{"gamma":0.47},{"weight":2.7}]}]';
				}else if(data_style=='style-7'){
				styles1='[{"featureType":"water","stylers":[{"color":"#0e171d"}]},{"featureType":"landscape","stylers":[{"color":"#1e303d"}]},{"featureType":"road","stylers":[{"color":"#1e303d"}]},{"featureType":"poi.park","stylers":[{"color":"#1e303d"}]},{"featureType":"transit","stylers":[{"color":"#182731"},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"color":"#f0c514"},{"visibility":"off"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#1e303d"},{"visibility":"off"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#e77e24"},{"visibility":"off"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#94a5a6"}]},{"featureType":"administrative","elementType":"labels","stylers":[{"visibility":"simplified"},{"color":"#e84c3c"}]},{"featureType":"poi","stylers":[{"color":"#e84c3c"},{"visibility":"off"}]}]';
			}
			var _toBuild = [];
			var build = function() {
				data.options.mapTypeId = google.maps.MapTypeId[data.options.mapTypeId];
				data.options.styles = data.style;
				if(styles1!=''){
					data.options.styles =JSON.parse(styles1);
					
				}          
				
				bounds = new google.maps.LatLngBounds();
				map = new google.maps.Map(document.getElementById(data_id), data.options);
				infoWindow = new google.maps.InfoWindow();
				
				map.setOptions({
					scrollwheel : data.options.scrollwheel,
					panControl : data.options.panControl,
					draggable:  data.options.draggable,
					zoomControl:  data.options.zoomControl,
					mapTypeControl:  data.options.scaleControl,
					scaleControl:  data.options.mapTypeControl,
				});
				var marker, i;
				map.setTilt(45);
				
				
				
				google.maps.event.addListener(infoWindow , 'domready', function() {
					
					
					var iwOuter = $('.gm-style-iw');
					var iwBackground = iwOuter.prev();
					
					var parentdiv = iwOuter.parent('div');
					parentdiv.addClass('marker-icon');
					var iwCloseBtn = iwOuter.next();
					iwCloseBtn.hide();
					
					iwOuter.addClass('marker-title');
					
					
				});
				
				
				
				for (i = 0; i < data.places.length; i++) {
					position = new google.maps.LatLng(data.places[i].latitude, data.places[i].longitude);
					
					bounds.extend(position);
					
					marker = new google.maps.Marker({
						position: position,
						map: map,
						title: data.places[i].address,
						icon: data.places[i].pin_icon
					});
					
					google.maps.event.addListener(marker, 'click', (function(marker, i) {
						return function() { 
							if(data.places[i].address.length > 1) {
								infoWindow.setContent('<div class="gmap_info_content"><p>'+ data.places[i].address +'</p></div>');
							}
							
							infoWindow.open(map, marker);
						};
					})(marker, i));
					
					map.fitBounds(bounds);
				}
				
				
				var boundsListener = google.maps.event.addListener((map), 'idle', function(event) {
					this.setZoom(data.options.zoom);
					google.maps.event.removeListener(boundsListener);
				});
				
				
				var update = function() {
					google.maps.event.trigger(map, "resize");
					map.setCenter(position);
				};
				update();
			};
			var initAll = function() {
				for( var i = 0, l = _toBuild.length; i < l; i++ ) {
					_toBuild[i]();
				}
			};
			var initialize= function() {
				initAll();
			};
			
			_toBuild.push( build );
			google.maps.event.addDomListener(window, "load", initialize);
			
			
			
		});
		
		$(".overlay-list-item").click(function() {      
			var $checkbox = $(this).find('input[type=checkbox]');
			if ($checkbox.is(':checked')) {
				$checkbox.attr('checked', false);
				$(this).parent('.pt-plus-overlay-map-content').removeClass("selected");
				} else {
				$checkbox.attr('checked', true);
				$(this).parent('.pt-plus-overlay-map-content').addClass("selected");
			}
		});
		
	});
	
})(jQuery);
/*---- gmaps js-------------------*/

/*----------animated image hover tilt option------------------*/
( function ( $ ) {	
	'use strict';
	$(window).load(function () {
			$(".hover-tilt").hover3d({
                selector: ".blog-list-style-content,.portfolio-item-content,> .addbanner-block,> .addbanner_product_box,> .vc_single_image-wrapper,> .cascading-inner-loop,> .pt-plus-magic-box,> .call-to-action-img,> .blog-hover-inner-tilt,> .logo-image-wrap",
                shine: !1,
				perspective: 2e3,
                invert: !0,
				sensitivity: 35,
            });
	});
	
} ( jQuery ) );	
/*----------animated image hover tilt option------------------*/
/*------------- magic scroll js ---*/
( function ( $ ) {	
	'use strict';
	$(document).ready(function(){
		pt_plus_animateParalax();
	});
} ( jQuery ) );
function pt_plus_animateParalax() {
	if(jQuery('body').find('.magic-scroll').length>0){
  var controller = new ScrollMagic.Controller();
  jQuery('.magic-scroll').each(function(index, elem){
    var tween = 'tween-'+index;
    tween = new TimelineMax();
    var lengthBox = jQuery(elem).find('.parallax-scroll').length;
    for(var i=0; i < lengthBox; i++){
        var speed = 0.5;
		var scroll_type=jQuery(elem).find('.parallax-scroll').data("scroll_type");
		var scale_scroll=jQuery(elem).find('.parallax-scroll').data("scale_scroll");
		var scroll_x=jQuery(elem).find('.parallax-scroll').data("scroll_x");
		var scroll_y=jQuery(elem).find('.parallax-scroll').data("scroll_y");
        var j1 = 0.2*(i+1);
        var k1 = 0.5*i;
		if(scroll_type=='position'){
			if(i==0) {
			  tween.to(jQuery(elem).find('.parallax-scroll:eq('+i+')'), 1, {x:-(scroll_x*speed),y:-(scroll_y*speed), ease: Linear.easeNone})
			}else {
			  tween.to(jQuery(elem).find('.parallax-scroll:eq('+i+')'), 1, {y:-(scroll_y*speed), ease: Linear.easeNone}, '-=1')
			}
		}
    }
	var lengthBox = jQuery(elem).find('.scale-scroll').length;
	  for(var i=0; i < lengthBox; i++){
        var speed = 0.5;
		var scroll_type=jQuery(elem).find('.scale-scroll').data("scroll_type");
		var scale_scroll=jQuery(elem).find('.scale-scroll').data("scale_scroll");
		
		if(scroll_type=='scale'){
			  tween.to(jQuery(elem).find('.scale-scroll:eq('+i+')'), 1, {scale:scale_scroll,opacity:1, ease: Linear.easeNone})
		}
    }
	
	var lengthBox = jQuery(elem).find('.both-scroll').length;
	  for(var i=0; i < lengthBox; i++){
        var speed = 0.5;
		var scroll_type=jQuery(elem).find('.both-scroll').data("scroll_type");
		var scale_scroll=jQuery(elem).find('.both-scroll').data("scale_scroll");
		var scroll_x=jQuery(elem).find('.both-scroll').data("scroll_x");
		var scroll_y=jQuery(elem).find('.both-scroll').data("scroll_y");
		if(scroll_type=='both'){
			  tween.to(jQuery(elem).find('.both-scroll:eq('+i+')'), 1, {scale:scale_scroll,x:-(scroll_x*speed),y:-(scroll_y*speed),opacity:1, ease: Linear.easeNone})
		}
    }
    new ScrollMagic.Scene({triggerElement: elem, duration: jQuery(this).outerHeight(), triggerHook:.7})
    .setTween(tween)
    .addTo(controller);
  })
	}
}
/*------------- magic scroll js ---*/

/*---------creative simple image super parallax----------------------*/
function pt_plus_bg_image_scroll_parallax(){
	if(jQuery('body').find('.creative-simple-img-parallax').length>0){
	var controller = new ScrollMagic.Controller();
  jQuery('.creative-simple-img-parallax').each(function(index, elem){

  var parallax_image=jQuery('.simple-parallax-img',this);
	var tween = 'tween-'+index;
    tween = new TimelineMax();
	 new ScrollMagic.Scene({
                triggerElement: elem,
				duration: '200%'
	 }).setTween(tween.from(parallax_image, 1, {x:-100,ease: Linear.easeNone})).addTo(controller);;
  });
	}
}
( function ( $ ) {	
	'use strict';
	$(document).ready(function(){
           pt_plus_bg_image_scroll_parallax();
        });
} ( jQuery ) );
/*---------creative simple image super parallax----------------------*/
/*--------- animated svg js -----------*/
( function ( $ ) {	
	'use strict';
	$.fn.pt_plus_animated_svg = function() {
		return this.each(function() {
			var $self = $(this);
			var data_id=$self.data("id");
			var data_duration=$self.data("duration");
			var data_type=$self.data("type");
			var data_stroke=$self.data("stroke");
			var data_fill_color=$self.data("fill_color");
			new Vivus(data_id, {type: data_type, duration: data_duration,forceRender:true,start: 'inViewport',onReady: function (myVivus) {
					var c=myVivus.el.childNodes;
					var show_id=document.getElementById(data_id);
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
			} });
		});
	};
$(window).load(function() {
	setTimeout(function(){
		$('.pt_plus_row_bg_animated_svg').pt_plus_animated_svg();
		$('.pt_plus_animated_svg,.ts-hover-draw-svg').pt_plus_animated_svg();
		$('body').find('.pt_plus_row_bg_animated_svg').attr('style', 'stroke:black');
}, 100);
});
} ( jQuery ) );
/*--------- animated svg js -----------*/
/*- contact form-----------*/
( function ( $ ) { 
 'use strict';
	$(document).ready(function() {
		$('.pt_plus_cf7_styles').each(function(){
			$('body').addClass("pt_plus_cf7_form");
			var style=$(this).data("style");
			var radio_checkbox=$(this).data("style-radio-checkbox");
			var svg_path='';
			var line_svg='';
			if(style=='style-3'){
				svg_path='<svg class="graphic graphic--style-3" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none"><path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0" stroke-width="1"></path></svg>';
			}
			if(style=='style-11'){
			 line_svg='<svg class="graphic graphic--style-11" width="100%" height="100%" viewBox="0 0 404 77" preserveAspectRatio="none"><path d="m0,0l404,0l0,77l-404,0l0,-77z" stroke-width="1.5"></path></svg>';
			}
			var i=1;
			$(".wpcf7-form-control.wpcf7-text, .wpcf7-form-control.wpcf7-number, .wpcf7-form-control.wpcf7-date, .wpcf7-form-control.wpcf7-textarea, .wpcf7-form-control.wpcf7-select",this).each(function(){
				var placeholder_name = $(this).attr('placeholder');
				
				if($(this).hasClass("wpcf7-select")){
					placeholder_name=$("option:first-child",this).text();
				}
				$(this).parents(".wpcf7-form-control-wrap").append('<label class="input__label input__label--'+style+'" for="'+style+'-cf-input-'+i+'">'+line_svg+'<span class="input__label-content input__label-content--'+style+'" data-content="'+placeholder_name+'">'+placeholder_name+'</span></label>'+svg_path);
				$(this).attr('placeholder','');
				$(this).attr('id',style+'-cf-input-'+i);
				$(this).addClass('input__field input__field--'+style);
				$(this).parents(".wpcf7-form-control-wrap").addClass('input--'+style);
				i++;
			});
			$(".wpcf7-form-control.wpcf7-select",this).each(function(){
				$(this).parents(".wpcf7-form-control-wrap").addClass("input--filled");
			});
			$(".wpcf7-form-control.wpcf7-radio .wpcf7-list-item",this).each(function(){
				var text_val=$(this).find('.wpcf7-list-item-label').text();
				$(this).find('.wpcf7-list-item-label').remove();
				var label_Tags=$('input[type="radio"]',this);
					if ( label_Tags.parent().is( 'label' )) {
						label_Tags.unwrap();
						}
				var radio_name=$(this).find('input[type="radio"]').attr('name');
				$(this).append('<label class="input__radio_btn" for="'+radio_name+i+'">'+text_val+'<div class="toggle-button__icon"></div></label>');
				$(this).find('input[type="radio"]').attr('id',radio_name+i);
				
				$(this).find('input[type="radio"]').addClass("input-radio-check");
				$(this).parents(".wpcf7-form-control-wrap").addClass(radio_checkbox);
				i++;
			});
			$(".wpcf7-form-control.wpcf7-checkbox .wpcf7-list-item",this).each(function(){
				var text_val=$(this).find('.wpcf7-list-item-label').text();
				$(this).find('.wpcf7-list-item-label').remove();
				var label_Tags=$('input[type="checkbox"]',this);
					if ( label_Tags.parent().is( 'label' )) {
						label_Tags.unwrap();
					}
				$(this).append('<label class="input__checkbox_btn" for="'+radio_checkbox+i+'">'+text_val+'<div class="toggle-button__icon"></div></label>');
				$(this).find('input[type="checkbox"]').attr('id',radio_checkbox+i);
				
				$(this).find('input[type="checkbox"]').addClass("input-checkbox-check");
				$(this).parents(".wpcf7-form-control-wrap").addClass(radio_checkbox);
				i++;
			});
			$(".wpcf7-form-control-wrap input[type='file']",this).each(function(){
			var file_name=$(this).attr('name');
				$(this).attr('id',file_name+i);
				$(this).attr('data-multiple-caption',"{count} files selected");
				$(this).parents(".wpcf7-form-control-wrap").append('<label class="input__file_btn" for="'+file_name+i+'"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg><span>Choose a file…</span></label>');
				$(this).parents(".wpcf7-form-control-wrap").addClass("cf7-style-file");
				i++;
			});
		});	
	
		$("input.wpcf7-form-control,textarea.wpcf7-form-control").focus(function() {
		  $(this).parents(".wpcf7-form-control-wrap").addClass("input--filled");
		});
	  
		$('input.wpcf7-form-control,textarea.wpcf7-form-control').blur(function(){
			if( !$(this).val() ) {
				  $(this).parents(".wpcf7-form-control-wrap").removeClass('input--filled');
			}
		});
		$('.wpcf7-form-control-wrap select').on('change',function(){
				var select_val=$(this).find(':selected').val();
			if(select_val!=''){
				$(this).parents(".wpcf7-form-control-wrap").addClass("input--filled");
			}
		});
		$(".wpcf7-form-control.wpcf7-textarea.input__field--style-9").each(function(){
			var height_textarea=$(this).outerHeight();
			$("head").append('<style >.pt_plus_cf7_styles .wpcf7-textarea.input__field--style-9 + .input__label--style-9::before{height:'+height_textarea+'px;}</style>');
		});
		$(".pt_plus_cf7_styles .wpcf7-form-control-wrap.input--style-12").each(function(){
			var height_textarea=$(this).outerHeight();
			$("head").append('<style >.pt_plus_cf7_form .pt_plus_cf7_styles .wpcf7-textarea.input__field--style-12{height:'+height_textarea+'px;}</style>');
		});
		$(window).load(function(){
			$(".pt_plus_cf7_styles").find(".minimal-form-input").removeClass("minimal-form-input");
		});
	});	
	$(document).on('load resize',function(){
		$(".wpcf7-form-control.wpcf7-textarea.input__field--style-9").each(function(){
			var height_textarea=$(this).outerHeight();
			$("head").append('<style >.pt_plus_cf7_styles .wpcf7-textarea.input__field--style-9 + .input__label--style-9::before{height:'+height_textarea+'px;}</style>');
		});
		$(".pt_plus_cf7_styles .wpcf7-form-control-wrap.input--style-12").each(function(){
			var height_textarea=$(this).outerHeight();
			$("head").append('<style >.pt_plus_cf7_form .pt_plus_cf7_styles .wpcf7-textarea.input__field--style-12{height:'+height_textarea+'px;}</style>');
		});
	});
} ( jQuery ) );

'use strict';
;( function ( document, window, index )
{
	var inputs = document.querySelectorAll( '.wpcf7-form-control.wpcf7-file' );
	Array.prototype.forEach.call( inputs, function( input )
	{
		var label='';
		var labelVal='';
		var i=0;
		input.addEventListener( 'change', function( e )
		{
		label  = input.nextElementSibling;
		if(i==0){
			labelVal = label.innerHTML;
		}
			var fileName = '';
			if( this.files && this.files.length > 1 )
				fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
			else
				fileName = e.target.value.split( '\\' ).pop();

			if( fileName )
				label.querySelector( 'span' ).innerHTML = fileName;
			else
				label.innerHTML = labelVal;
			i++;
		});
		input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
		input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
	});
}( document, window, 0 ));
/*- contact form-----------*/
/*-theservice coutdown-----------*/
/*count down js*/

/*-theservice coutdown-----------*/

/*---header breadcurmbs--*/
( function ( $ ) {
	'use strict';
	$.fn.HeaderBreadcrumbsParallax= function() {
		var scrolledY = $(window).scrollTop();
		var $self = $(this);
		var height = $self.parent().height();
		$self.css({
			'top': ((scrolledY*0.4))+'px',
			'opacity': (1 - 1/(height/scrolledY))
		});
	};
	$(window).on('load scroll',function(e){
		$('.parallax-on').HeaderBreadcrumbsParallax();
		$('.pt-plus-header-archive-content .container').HeaderBreadcrumbsParallax();
	});	
} ( jQuery ) );

/*---header breadcurmbs--*/
/*------ heading animation--------*/
jQuery(document).ready(function($){
	"use strict";
	//set animation timing
	var animationDelay = 2500,
	//loading bar effect
	barAnimationDelay = 3800,
	barWaiting = barAnimationDelay - 3000, //3000 is the duration of the transition on the loading bar - set in the scss/css file
	//letters effect
	lettersDelay = 50,
	//type effect
	typeLettersDelay = 150,
	selectionDuration = 500,
	typeAnimationDelay = selectionDuration + 800,
	//clip effect 
	revealDuration = 600,
	revealAnimationDelay = 1500;
	
	pt_plus_initHeadline();
	
	
	function pt_plus_initHeadline() {
		//insert <i> element for each letter of a changing word
		singleLetters($('.pt-plus-cd-headline.letters').find('b'));
		//initialise headline animation
		animateHeadline($('.pt-plus-cd-headline'));
	}
	
	function singleLetters($words) {
		$words.each(function(){
		var i;
			var word = $(this),
			letters = word.text().split(''),
			selected = word.hasClass('is-visible');
			for (i in letters) {
				if(word.parents('.rotate-2').length > 0) letters[i] = '<em>' + letters[i] + '</em>';
				letters[i] = (selected) ? '<i class="in">' + letters[i] + '</i>': '<i>' + letters[i] + '</i>';
			}
		    var newLetters = letters.join('');
		    word.html(newLetters).css('opacity', 1);
		});
	}
	
	function animateHeadline($headlines) {
		var duration = animationDelay;
		$headlines.each(function(){
			var headline = $(this);
			
			if(headline.hasClass('loading-bar')) {
				duration = barAnimationDelay;
				setTimeout(function(){ headline.find('.cd-words-wrapper').addClass('is-loading') }, barWaiting);
				} else if (headline.hasClass('clip')){
				var spanWrapper = headline.find('.cd-words-wrapper'),
				newWidth = spanWrapper.width() + 10
				spanWrapper.css('width', newWidth);
				} else if (!headline.hasClass('type') ) {
				//assign to .cd-words-wrapper the width of its longest word
				var words = headline.find('.cd-words-wrapper b'),
				width = 0;
				words.each(function(){
					var wordWidth = $(this).width();
				    if (wordWidth > width) width = wordWidth;
				});
				headline.find('.cd-words-wrapper').css('width', width);
			};
			
			//trigger animation
			setTimeout(function(){ hideWord( headline.find('.is-visible').eq(0) ) }, duration);
		});
	}
	
	function hideWord($word) {
		var nextWord = takeNext($word);
		
		if($word.parents('.pt-plus-cd-headline').hasClass('type')) {
			var parentSpan = $word.parent('.cd-words-wrapper');
			parentSpan.addClass('selected').removeClass('waiting');	
			setTimeout(function(){ 
				parentSpan.removeClass('selected'); 
				$word.removeClass('is-visible').addClass('is-hidden').children('i').removeClass('in').addClass('out');
			}, selectionDuration);
			setTimeout(function(){ showWord(nextWord, typeLettersDelay) }, typeAnimationDelay);
			
			} else if($word.parents('.pt-plus-cd-headline').hasClass('letters')) {
			var bool = ($word.children('i').length >= nextWord.children('i').length) ? true : false;
			hideLetter($word.find('i').eq(0), $word, bool, lettersDelay);
			showLetter(nextWord.find('i').eq(0), nextWord, bool, lettersDelay);
			
			}  else if($word.parents('.pt-plus-cd-headline').hasClass('clip')) {
			$word.parents('.cd-words-wrapper').animate({ width : '2px' }, revealDuration, function(){
				switchWord($word, nextWord);
				showWord(nextWord);
			});
			
			} else if ($word.parents('.pt-plus-cd-headline').hasClass('loading-bar')){
			$word.parents('.cd-words-wrapper').removeClass('is-loading');
			switchWord($word, nextWord);
			setTimeout(function(){ hideWord(nextWord) }, barAnimationDelay);
			setTimeout(function(){ $word.parents('.cd-words-wrapper').addClass('is-loading') }, barWaiting);
			
			} else {
			switchWord($word, nextWord);
			setTimeout(function(){ hideWord(nextWord) }, animationDelay);
		}
	}
	
	function showWord($word, $duration) {
		if($word.parents('.pt-plus-cd-headline').hasClass('type')) {
			showLetter($word.find('i').eq(0), $word, false, $duration);
			$word.addClass('is-visible').removeClass('is-hidden');
			
			}  else if($word.parents('.pt-plus-cd-headline').hasClass('clip')) {
			$word.parents('.cd-words-wrapper').animate({ 'width' : $word.width() + 10 }, revealDuration, function(){ 
				setTimeout(function(){ hideWord($word) }, revealAnimationDelay); 
			});
		}
	}
	
	function hideLetter($letter, $word, $bool, $duration) {
		$letter.removeClass('in').addClass('out');
		
		if(!$letter.is(':last-child')) {
		 	setTimeout(function(){ hideLetter($letter.next(), $word, $bool, $duration); }, $duration);  
			} else if($bool) { 
		 	setTimeout(function(){ hideWord(takeNext($word)) }, animationDelay);
		}
		
		if($letter.is(':last-child') && $('html').hasClass('no-csstransitions')) {
			var nextWord = takeNext($word);
			switchWord($word, nextWord);
		} 
	}
	
	function showLetter($letter, $word, $bool, $duration) {
		$letter.addClass('in').removeClass('out');
		
		if(!$letter.is(':last-child')) { 
			setTimeout(function(){ showLetter($letter.next(), $word, $bool, $duration); }, $duration); 
			} else { 
			if($word.parents('.pt-plus-cd-headline').hasClass('type')) { setTimeout(function(){ $word.parents('.cd-words-wrapper').addClass('waiting'); }, 200);}
			if(!$bool) { setTimeout(function(){ hideWord($word) }, animationDelay) }
		}
	}
	
	function takeNext($word) {
		return (!$word.is(':last-child')) ? $word.next() : $word.parent().children().eq(0);
	}
	
	function takePrev($word) {
		return (!$word.is(':first-child')) ? $word.prev() : $word.parent().children().last();
	}
	
	function switchWord($oldWord, $newWord) {
		$oldWord.removeClass('is-visible').addClass('is-hidden');
		$newWord.removeClass('is-hidden').addClass('is-visible');
	}
});
/*----header animation element--------*/

/*--Content hover Effects --*/
( function ( $ ) {
	'use strict';
	$(window).load(function () {
		$('.content_hover_effect').each(function () {
			var $this=$(this);
			var hover_uniqid = $this.data('hover_uniqid');
			var hover_shadow = $this.data('hover_shadow');
			var content_hover_effects= $this.data('content_hover_effects');
			if(content_hover_effects=='float_shadow'){
				$('head').append('<style >.'+hover_uniqid+'.content_hover_float_shadow:before{background: -webkit-radial-gradient(center, ellipse, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);background: radial-gradient(ellipse at center, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);}</style>');
			}
			if(content_hover_effects=='shadow_radial'){
				$('head').append('<style >.'+hover_uniqid+'.content_hover_radial:after{background: -webkit-radial-gradient(50% -50%, ellipse, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);background: radial-gradient(ellipse at 50% -50%, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);}.'+hover_uniqid+'.content_hover_radial:before{background: -webkit-radial-gradient(50% 150%, ellipse, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);background: radial-gradient(ellipse at 50% 150%, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);}</style>');
			}
			if(content_hover_effects=='grow_shadow'){
				$('head').append('<style >.'+hover_uniqid+'.content_hover_grow_shadow:hover{-webkit-box-shadow: 0 10px 10px -10px '+hover_shadow+';-moz-box-shadow: 0 10px 10px -10px '+hover_shadow+';box-shadow: 0 10px 10px -10px '+hover_shadow+';}</style>');
			}
				
		});
	});	
} ( jQuery ) );
/*--Content hover Effects --*/
/*--Content hover Effects --*/
( function ( $ ) {
	'use strict';
	$(window).load(function () {
		$('.animted-content-inner').each(function () {
			var $this=$(this);
			var bg_uniqid = $this.data('bg_uniqid');
			var bg_animated_color = $this.data('bg_animated_color');
			$('head').append('<style >.'+bg_uniqid+'.pt-plus-bg-color-animated:after{background: '+bg_animated_color+';}</style>');
		});
	});	
} ( jQuery ) );
/*--Content hover Effects --*/

/*-------------Info Box*/
( function ( $ ) {	
	'use strict';
	$(window).load(function () {
		$('.remove-padding').each(function () { 
			var $this=$(this);
			var parent_row= $(this).parents('.vc_column_container .vc_column-inner');
			parent_row.css("padding","0");
			
		});
	});
} ( jQuery ) );			
/*--end Info Box------*/
/*-cursor icon-----*/
( function ( $ ) {
	'use strict';
	$(document).ready(function () {
		$('.pt-plus-cursor-icon').each(function () {
			var $this=$(this);
			var cursor_uid = $this.data('cursor_uid');
			var cursor_icon_url = $this.data('cursor_icon_url');
			$('head').append('<style >.'+cursor_uid+'.pt-plus-cursor-icon *{cursor: url("'+cursor_icon_url+'"), auto !important;}</style>');
		});
	});	
} ( jQuery ) );
/*-cursor icon-----*/
/*-theplus row separator----*/
(function($) {
	'use strict';
	$(window).load(function() {
		$(".pt-plus-row-separator-style-top-bottom").each(function() {			
			var parent_row= $(this).parents('.vc_row');
			parent_row.addClass("pt-plus-row-separator");
			if(parent_row){
				$( parent_row ).prepend($(this));		
			}
		});
	});
})(jQuery);
/*-theplus row separator----*/
/*-social icon element----*/
( function ( $ ) {	
	'use strict';
	$(document).ready(function() {
		$('.ts-chaffle').chaffle({
			speed: 20,
			time: 140
		});
	});		
} ( jQuery ) );
/*-social icon element----*/
/*- toggle-------------*/
( function ( $ ) { 
	'use strict';
	$(document).ready(function() {
	$('.pt-plus-toggle').each(function () {
		var $this=$(this);
		var uid= $(this).data('uid');
		$('.'+uid+' .pt_plus_button').click(function () {
			$('#'+uid).slideToggle('3000',"swing", function () {
				$('.list-isotope').find('.post-inner-loop').isotope('layout').isotope( 'reloadItems' );
				if ($('.list-isotope-metro').size() > 0) {
					setTimeout(function() {
						theplus_setup_packery_portfolio('all');	
					}, 500);
				}
				$('.list-carousel-slick').resize();
				$(".pt-plus-timeline-list").isotope('layout').isotope( 'reloadItems' );
			});
		});
	});
	});
} ( jQuery ) );
/*- toggle-------------*/
/*-unique box -*/
(function($) {
	'use strict';
	$(document).ready(function() {
		$(".pt-plus-unique-box").each(function() {
			var uid= $(this).data('uid');
			var parent_row= $('.'+uid).parents('.vc_row');
			if(parent_row){
				parent_row.addClass("pt-plus-unique-flex");
			}
		});
	});
})(jQuery);
/*-unique box -*/
/*-the plus video--*/

! function(e) {
    "use strict";

    function t(t) {
        var a = t.find("video"),
            n = t.find(".ts-video-lazyload");
        if (t.is("[data-grow]") && t.css("max-width", "none"), t.find(".ts-video-title, .ts-video-description, .ts-video-play-btn, .ts-video-thumbnail").addClass("ts-video-hidden"), n.length) {
            var i = n.data();
            e("<iframe></iframe>").attr(i).insertAfter(n)
        }
        a.length && a.get(0).play()
    }

    function a() {
        e(".ts-video-wrapper[data-inview-lazyload]").one("inview", function(a, n) {
            n && t(e(this))
        })
    }
    e(document).on("click", '[data-mode="lazyload"] .ts-video-play-btn', function(a) {
        a.preventDefault(), t(e(this).closest(".ts-video-wrapper"))
    }), a(), e(document).ajaxComplete(function() {
        a()
    }), e(document).on("lity:open", function() {
        /*e("*").not(".lity, .lity-wrap, .lity-close").filter(function() {
            return "fixed" === e(this).css("position")
        }).addClass("ts-video-hidden").attr("data-hidden-fixed", "true")*/
    }), e(document).on("lity:ready", function(t, a) {
        var n = a.element(),
            i = n.find("video"),
            r = n.find(".ts-video-lazyload");
        if (e(".lity-wrap").attr("id", "ts-video"), r.length) e("<iframe></iframe>").attr(r.data()).insertAfter(r);
        i.length && i.get(0).play()
    }), e(document).on("lity:close", function(t, a) {
        a.element().find("video").length && a.element().find("video").get(0).pause(), e(".ts-video-lity-container .pt-plus-video-frame").remove(), e("[data-hidden-fixed]").removeClass("ts-video-hidden")
    }), e(document).ready(function() {
        e(".ts-video-lightbox-link").off()
    })
}(jQuery);


/*-the plus video--*/
/*caroseal slider------------------------*/
( function ( $ ) {	
	'use strict';
	$(document).ready(function() {
		$('.list-carousel-slick').each(function(){
			var $self=$(this);
			var $uid=$(this).data("id");
			var testimonial_style=$(this).data("testimonial-style");
			var show_arrows=$(this).data("show_arrows");
			var show_dots=$(this).data("show_dots");
			var show_draggable=$(this).data("show_draggable");
			var slide_loop=$(this).data("slide_loop");
			var slide_autoplay=$(this).data("slide_autoplay");
			var slide_mouse_scroll=$(this).data("slide_mouse_scroll");
			
			var autoplay_speed=$(this).data("autoplay_speed");
			var steps_slide=$(this).data("steps_slide");
			var carousel_column=$(this).data("carousel_column");
			var carousel_tablet_column=$(this).data("carousel_tablet_column");
			var carousel_mobile_column=$(this).data("carousel_mobile_column");
			var dots_style=$(this).data("dots_style");
			var arrows_style=$(this).data("arrows_style");
			var arrows_position=$(this).data("arrows_position");
			
			if(steps_slide=='1'){
				steps_slide=='1';
				}else{
				steps_slide=carousel_column;
			}
			var tablet_slide,mobile_slide;
			if(steps_slide=='1'){
				tablet_slide=='1';
				}else{
				tablet_slide=carousel_tablet_column;
			}
			
			if(steps_slide=='1'){
				mobile_slide=='1';
				}else{
				mobile_slide=carousel_mobile_column;
			}
			
			if(arrows_style=='style-1'){
				var prev_arrow='<button type="button" class="slick-nav slick-prev '+arrows_style+'"></button>';
				var next_arrow='<button type="button" class="slick-nav slick-next '+arrows_style+'"></button>';
			}
			
			if(arrows_style=='style-3'){
				var prev_arrow='<button type="button" class="slick-nav slick-prev '+arrows_style+'"><span class="icon-wrap"></span></button>';
				var next_arrow='<button type="button" class="slick-nav slick-next '+arrows_style+'"><span class="icon-wrap"></span></button>';
			}
			if(arrows_style=='style-4' || arrows_style=='style-5' ){
				var prev_arrow='<button type="button" class="slick-nav slick-prev '+arrows_style+' '+arrows_position+'"></button>';
				var next_arrow='<button type="button" class="slick-nav slick-next '+arrows_style+' '+arrows_position+'"></button>';
			}
			if(arrows_style=='style-6'){
				var prev_arrow='<button type="button" class="slick-nav slick-prev '+arrows_style+'"><span class="icon-wrap"></span></button>';
				var next_arrow='<button type="button" class="slick-nav slick-next '+arrows_style+'"><span class="icon-wrap"></span></button>';
			}
			if(arrows_style=='style-7'){
				var prev_arrow='<button type="button" class="slick-nav slick-prev '+arrows_style+'"><span class="icon-wrap"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></span></button>';
				var next_arrow='<button type="button" class="slick-nav slick-next '+arrows_style+'"><span class="icon-wrap"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></span></button>';
			}
			
			$('.'+$uid+' .post-inner-loop').slick({
				dots: show_dots,
				arrows: show_arrows,
				infinite: slide_loop,
				speed: 1000,
				centerMode: false,
				autoplay: slide_autoplay,
				autoplaySpeed: autoplay_speed,
				pauseOnHover: true,
				prevArrow: prev_arrow,
				nextArrow: next_arrow,
				slidesToShow: carousel_column,
				lazyLoad: 'ondemand',
				slidesToScroll: steps_slide,
				draggable:show_draggable,
				dotsClass:dots_style,
				responsive: [
					{
						breakpoint: 800,
						settings: {
							slidesToShow: carousel_tablet_column,
							slidesToScroll: tablet_slide
						}
					},
					{
						breakpoint: 500,
						settings: {
							slidesToShow: carousel_mobile_column,
							slidesToScroll: mobile_slide,
							dots:false,
						}
					}
				]
			});
			if(slide_mouse_scroll==true && slide_mouse_scroll!=undefined){
				
                $('.'+$uid+' .post-inner-loop').mousewheel(function(e) {
                    e.preventDefault();
                    if (e.deltaY < 0) {
                        $('.'+$uid+' .post-inner-loop').slick("slickNext");
                    } else {
                        $('.'+$uid+' .post-inner-loop').slick("slickPrev");
                    }
                });
			}	
			$('.'+$uid+' .pt-plus-testi-nav-loop').slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: false,
				fade: true,
				asNavFor: '.'+$uid+' .testi-inner-loop'
			});
			
			if(testimonial_style=='style_5'){
				var center_mode=true;
			}else{
				var center_mode=false;
			}
			$('.'+$uid+' .testi-inner-loop').slick({
				dots: show_dots,
				arrows: show_arrows,
				infinite: slide_loop,
				speed: 1000,
				centerMode: center_mode,
				autoplay: slide_autoplay,
				autoplaySpeed: autoplay_speed,
				pauseOnHover: true,
				prevArrow: prev_arrow,
				nextArrow: next_arrow,
				slidesToShow: carousel_column,
				lazyLoad: 'ondemand',
				slidesToScroll: steps_slide,
				draggable:show_draggable,
				dotsClass:dots_style,
				asNavFor: '.'+$uid+' .pt-plus-testi-nav-loop',
				focusOnSelect: true,
				responsive: [
					{
						breakpoint: 800,
						settings: {
							slidesToShow: carousel_tablet_column,
							slidesToScroll: tablet_slide
						}
					},
					{
						breakpoint: 500,
						settings: {
							slidesToShow: carousel_mobile_column,
							slidesToScroll: mobile_slide,
							dots:false,
						}
					}
				]
			});
			
			$('.'+$uid+' .testi-inner-loop').on('click', '.slick-slide', function (e) {
				var $currTarget = $(e.currentTarget), 
					index = $currTarget.data('slick-index'),
					slickObj = $('.'+$uid+' .pt-plus-testi-nav-loop').slick('getSlick');
			slickObj.slickGoTo(index);   
			});
			
			var dots_border_color=$(this).data("dots_border_color");
			var dots_bg_color=$(this).data("dots_bg_color");
			var dots_active_border_color=$(this).data("dots_active_border_color");
			var dots_active_bg_color=$(this).data("dots_active_bg_color");
			
			
			if(dots_style=='slick-dots style-1'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-1 li button{border-color:'+dots_border_color+';background:'+dots_bg_color+';}.'+$uid+' .slick-dots.style-1 li.slick-active button:after{background:'+dots_active_bg_color+';}.'+$uid+' .slick-dots.style-1 li.slick-active button{border-color:'+dots_border_color+';}</style>');
			}
			if(dots_style=='slick-dots style-2'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-2 li button{background:'+dots_bg_color+';}.'+$uid+' .slick-dots.style-2 li.slick-active svg circle{stroke:'+dots_border_color+';}</style>');
			}
			if(dots_style=='slick-dots style-3'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-3 li button{-webkit-box-shadow: inset 0 0 0 8px '+dots_border_color+';-moz-box-shadow: inset 0 0 0 8px '+dots_border_color+';box-shadow: inset 0 0 0 8px '+dots_border_color+';}.'+$uid+' .slick-dots.style-3 li.slick-active button{-webkit-box-shadow: inset 0 0 0 1px '+dots_border_color+';-moz-box-shadow: inset 0 0 0 1px '+dots_border_color+';box-shadow: inset 0 0 0 1px '+dots_border_color+';}</style>');
			}
			if(dots_style=='slick-dots style-4'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-4 li button{border-color:'+dots_border_color+';background:'+dots_bg_color+';}.'+$uid+' .slick-dots.style-4 li::after{background:'+dots_active_bg_color+';border-color:'+dots_active_border_color+';}</style>');
			}
			if(dots_style=='slick-dots style-5'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-5 li button::before,.slick-dots.style-5 li button::after{background:'+dots_bg_color+';}.'+$uid+' ul.slick-dots.style-5 li button::after{background:'+dots_active_bg_color+';}</style>');
			}
			if(dots_style=='slick-dots style-6'){
				$("head").append('<style >.'+$uid+' ul.slick-dots.style-6 li button{-webkit-box-shadow: inset 0 0 0 1px '+dots_border_color+';-moz-box-shadow: inset 0 0 0 1px '+dots_border_color+';box-shadow: inset 0 0 0 1px '+dots_border_color+';background:'+dots_bg_color+';}.'+$uid+' .slick-dots.style-6 li.slick-active button{-webkit-box-shadow: inset 0 0 0 8px '+dots_border_color+';-moz-box-shadow: inset 0 0 0 8px '+dots_border_color+';box-shadow: inset 0 0 0 8px '+dots_border_color+';}</style>');
			}
			if(dots_style=='slick-dots style-7'){
				$("head").append('<style >.'+$uid+' ul.slick-dots.style-7 li button{-webkit-box-shadow: inset 0 0 0 0px '+dots_border_color+';-moz-box-shadow: inset 0 0 0 0px '+dots_border_color+';box-shadow: inset 0 0 0 0px '+dots_border_color+';}.'+$uid+'  .slick-dots.style-7 li button:before{background:'+dots_bg_color+';}.'+$uid+' .slick-dots.style-7 li.slick-active button{-webkit-box-shadow: inset 0 0 0 1px '+dots_active_border_color+';-moz-box-shadow: inset 0 0 0 1px '+dots_active_border_color+';box-shadow: inset 0 0 0 1px '+dots_active_border_color+';}.'+$uid+'  .slick-dots.style-7 li.slick-active button:before{background:'+dots_active_bg_color+';}</style>');
			};
			if(dots_style=='slick-dots style-8'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-8 button{ background-color: '+dots_bg_color+';}.'+$uid+'  .slick-dots.style-8 .slick-active button{background:'+dots_active_bg_color+';}</style>');
			};
			if(dots_style=='slick-dots style-9'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-9 button{ background-color: '+dots_bg_color+';}.'+$uid+'  .slick-dots.style-9 .slick-active button{background:'+dots_active_bg_color+';}</style>');
			};
			if(dots_style=='slick-dots style-10'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-10 li button{ border-color: '+dots_border_color+';}.'+$uid+'  .slick-dots.style-10 .slick-active button:after{color:'+dots_active_border_color+';}</style>');
			};
			if(dots_style=='slick-dots style-11'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-11 button{ background-color: '+dots_bg_color+';}.'+$uid+'  .slick-dots.style-11 .slick-active button{background:'+dots_active_bg_color+';}</style>');
			};
			if(dots_style=='slick-dots style-12'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-12 button{ background: '+dots_bg_color+';}.'+$uid+'  .slick-dots.style-12 .slick-active button{background:'+dots_active_bg_color+';}</style>');
			};
			if(dots_style=='slick-dots style-13'){
				$("head").append('<style >.'+$uid+' .slick-dots.style-13 li{ background-color: '+dots_bg_color+';}.'+$uid+'  .slick-dots.style-13 .slick-active {background-color:'+dots_active_bg_color+';}</style>');
			};
			
			var arrow_bg_color=$(this).data("arrow_bg_color");
			var arrow_icon_color=$(this).data("arrow_icon_color");
			var arrow_hover_bg_color=$(this).data("arrow_hover_bg_color");
			var arrow_hover_icon_color=$(this).data("arrow_hover_icon_color");
			var arrow_text_color=$(this).data("arrow_text_color");
			
			if(arrows_style=='style-1'){
				$("head").append('<style >.'+$uid+' .slick-nav.slick-prev.style-1,.'+$uid+'  .slick-nav.slick-next.style-1{background: '+arrow_bg_color+';}.'+$uid+' .slick-prev.style-1:before,.'+$uid+'  .slick-next.style-1:before{color: '+arrow_icon_color+';}.'+$uid+' .slick-prev.style-1::after,.'+$uid+'  .slick-next.style-1::after{background: '+arrow_hover_bg_color+';}.'+$uid+' .slick-prev.style-1:hover:before,.'+$uid+' .slick-next.style-1:hover:before{color: '+arrow_hover_icon_color+';}</style>');
			}
			
			
			if(arrows_style=='style-3'){
				$("head").append('<style >.'+$uid+' .slick-prev.style-3:before,.'+$uid+' .slick-next.style-3:before{background: transparent;}.'+$uid+' .slick-prev.style-3:hover::before,.'+$uid+' .slick-next.style-3:hover::before{background: '+arrow_hover_bg_color+';}.'+$uid+' .slick-prev.style-3 .icon-wrap:before, .'+$uid+' .slick-prev.style-3 .icon-wrap:after,.'+$uid+' .slick-next.style-3 .icon-wrap:before,.'+$uid+' .slick-next.style-3 .icon-wrap:after{background: '+arrow_icon_color+';}.'+$uid+' .slick-prev.style-3:hover .icon-wrap::before,.'+$uid+' .slick-prev.style-3:hover .icon-wrap::after,.'+$uid+' .slick-next.style-3:hover .icon-wrap::before, .'+$uid+' .slick-next.style-3:hover .icon-wrap::after{background: '+arrow_hover_icon_color+';}</style>');
			}
			
			if(arrows_style=='style-4'){
				$("head").append('<style >.'+$uid+' .slick-prev.style-4:before,.'+$uid+' .slick-nav.style-4:before{background: '+arrow_bg_color+';color: '+arrow_icon_color+';}.'+$uid+' .slick-prev.style-4:hover:before,.'+$uid+' .slick-nav.style-4:hover:before{background: '+arrow_hover_bg_color+';color: '+arrow_hover_icon_color+';}</style>');
			}
			if(arrows_style=='style-5'){
				$("head").append('<style >.'+$uid+' .slick-prev.style-5:before,.'+$uid+' .slick-nav.style-5:before{border-color: '+arrow_bg_color+';color: '+arrow_icon_color+';}.'+$uid+' .slick-prev.style-5:hover:before,.'+$uid+' .slick-nav.style-5:hover:before{border-color: '+arrow_hover_bg_color+';color: '+arrow_hover_icon_color+';}</style>');
			}
			if(arrows_style=='style-6'){
				$("head").append('<style >.'+$uid+' .slick-prev.style-6 .icon-wrap:before, .'+$uid+' .slick-prev.style-6 .icon-wrap:after,.'+$uid+' .slick-next.style-6 .icon-wrap:before,.'+$uid+' .slick-next.style-6 .icon-wrap:after{background: '+arrow_icon_color+';}.'+$uid+' .slick-prev.style-6:hover .icon-wrap::before,.'+$uid+' .slick-prev.style-6:hover .icon-wrap::after,.'+$uid+' .slick-next.style-6:hover .icon-wrap::before, .'+$uid+' .slick-next.style-6:hover .icon-wrap::after{background: '+arrow_hover_icon_color+';}</style>');
			}
			if(arrows_style=='style-7'){
				$("head").append('<style >.'+$uid+' .slick-prev.style-7:before,.'+$uid+' .slick-next.style-7:before{background: '+arrow_bg_color+';}.'+$uid+' .slick-nav.style-7:hover .icon-wrap{color: '+arrow_hover_icon_color+';}.'+$uid+' .slick-nav.style-7 .icon-wrap{color: '+arrow_icon_color+';}</style>');
			}
			if(arrows_style=='style-8'){
				$("head").append('<style >.'+$uid+' .slick-nav.style-8:hover .fa{color: '+arrow_hover_icon_color+';}.'+$uid+' .slick-nav.style-8 .fa{color: '+arrow_icon_color+';}.'+$uid+' .slick-nav.style-8:after{border-color: '+arrow_icon_color+' !important;}</style>');
			}
		});
	
		setTimeout(function(){
			$(".slick-dots.style-2 li").each(function(){
				$(this).append('<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 16 16" preserveAspectRatio="none"><circle cx="8" cy="8" r="6.215"></circle></svg>');
			});
		}, 1000);
	});
})(jQuery);
/*caroseal slider------------------------*/

/*-time line js theservice-------------*/
( function ( $ ) {	
    "use strict";
	$(document).ready(function() {
    function b(a, b, c) {
        return c >= a && b >= c
	}
    function c(a, c, d) {
        var e = 0;
        return c.forEach(function(c) {
            return b(c.top, c.bottom, a.top) ? e = c.bottom - a.top + 5 : b(c.top, c.bottom, a.bottom) ? e = c.top - a.bottom + 5 : void 0
		}),
        e
	}
    function d(a, b, d) {
        var f = {
            top: b.offset().top,
            bottom: b.offset().top + b.outerHeight()
		}
		, g = {
            top: a.offset().top,
            bottom: a.offset().top + a.height()
		}
		, h = a.attr("data-side")
		, i = 0;
        return "left" == h ? (i = c(f, j.right, g),
        f = e(b, d, f, i),
        j.left.push(f),
        !0) : (i = c(f, j.left, g),
        f = e(b, d, f, i),
        j.right.push(f),
        !0)
	}
    function e(a, b, c, d) {
        return 0 == d ? c : (c.top += d,
        c.bottom += d,
        a.css({
            "margin-top": parseInt(a.css("margin-top")) + parseInt(d)
		}),
        b.css({
            "margin-top": parseInt(b.css("margin-top")) + parseInt(d)
		}),
        c)
	}
    function f(b) {
        var c = b.find(".mpc-timeline-item__wrap");
        c.each(function() {
            var c = $(this)
			, e = c.find(".mpc-timeline-item")
			, f = c.find(".mpc-tl-icon")
			, g = e.find(".mpc-tl-before")
			, h = 0;
            b.is(".mpc-layout--left") ? c.attr("data-side", "right") : b.is(".mpc-layout--right") ? c.attr("data-side", "left") : "0px" == c.css("left") ? c.attr("data-side", "left") : c.attr("data-side", "right"),
            g.removeAttr("style"),
            "left" == c.attr("data-side") && !b.is(".mpc-layout--right") && _mpc_vars.breakpoints.custom("(min-width: 767px)") ? g.css({
                "margin-left": parseInt(e.css("border-right-width"))
				}) : g.css({
                "margin-right": parseInt(e.css("border-left-width"))
			}),
            b.is(".mpc-pointer--middle") && g.css({
                "margin-top": parseInt(g.css("margin-top")) - parseInt(.5 * g.outerHeight())
			}),
            h = g.offset().top - c.offset().top - .5 * f.height(),            
            f.css({
                "margin-top": parseInt(h)
			}),
            b.is(".mpc-layout--both") && d(c, f, g)
		})
	}
    function g(a) {
        var b = a.find(".timeline--icon")
		, c = a.find(".timeline-track");
        b.css({
            "margin-left": -parseInt(.5 * (b.outerWidth() + c.outerWidth()))
		})
	}
    function h(b) {
        $.fn.isotope ? i(b) : setTimeout(function() {
            h(b)
		}, 50)
	}
    function i(b) {
		
        g(b);
        var d = {
            itemSelector: ".timeline-item-wrap",
            layoutMode: "masonry"
		};
        
		b.isotope(d),           
		$(document).ready(function() {
			setTimeout(function() {
				b.data("isotope") && b.isotope("layout")
			}, 50)
		})
        
	}
    var j, k = $(".pt-plus-timeline-list");
    k.each(function() {
        var b = $(this);       
		h(b);
	});
	});
} ( jQuery ) );
/*-time line js theservice-------------*/
/*-----pin to point js-------------*/
(function($) {
    'use strict';
	$(document).ready(function(){		
		$(".pt-plus-pin-point-single a").each(function() {
			var uid= $(this).data('uid');
			var pin_style= $(this).data('pin_style');
			var pin_bg_color = $(this).data('pin_bg_color');			
			var pin_hover_bg_color = $(this).data('pin_hover_bg_color');
			var shadow_color=pt_plus_hexToRgbA(pin_bg_color,'0.8');
			var shadow_hover_color=pt_plus_hexToRgbA(pin_hover_bg_color,'0.8');
			
		if(pin_style=='style-1' || pin_style=='style-2'){
			$('head').append('<style >@-webkit-keyframes cd-pulse-'+uid+'{0%{-webkit-transform:scale(1);-webkit-box-shadow:inset 0 0 1px 1px '+shadow_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_color+';box-shadow:inset 0 0 1px 1px '+shadow_color+';}50%{-webkit-box-shadow:inset 0 0 1px 1px '+shadow_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_color+';box-shadow:inset 0 0 1px 1px '+shadow_color+';}100%{-webkit-transform:scale(1.6);-webkit-box-shadow:inset 0 0 1px 1px inset 0 0 1px 1px rgba(217, 83, 83, 0);-moz-box-shadow:inset 0 0 1px 1px inset 0 0 1px 1px rgba(217, 83, 83, 0);box-shadow:inset 0 0 1px 1px inset 0 0 1px 1px rgba(217, 83, 83, 0);}}@-moz-keyframes cd-pulse-'+uid+'{0%{-moz-transform:scale(1);-webkit-box-shadow:inset 0 0 1px 1px '+shadow_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_color+';box-shadow:inset 0 0 1px 1px '+shadow_color+';}50%{-webkit-box-shadow:inset 0 0 1px 1px '+shadow_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_color+';box-shadow:inset 0 0 1px 1px '+shadow_color+';}100%{-moz-transform:scale(1.6);-webkit-box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);-moz-box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);}}@keyframes cd-pulse-'+uid+'{0%{-webkit-transform:scale(1);-moz-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transform:scale(1);-webkit-box-shadow:inset 0 0 1px 1px '+shadow_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_color+';box-shadow:inset 0 0 1px 1px '+shadow_color+';}50%{-webkit-box-shadow:inset 0 0 1px 1px '+shadow_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_color+';box-shadow:inset 0 0 1px 1px '+shadow_color+';}100%{-webkit-transform:scale(1.6);-moz-transform:scale(1.6);-ms-transform:scale(1.6);-o-transform:scale(1.6);transform:scale(1.6);-webkit-box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);-moz-box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);}}@-webkit-keyframes cd-pulse-hover-'+uid+'{0%{-webkit-transform:scale(1);-webkit-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';}50%{-webkit-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';}100%{-webkit-transform:scale(1.6);-webkit-box-shadow:inset 0 0 1px 1px rgba(217, 83, 83, 0);-moz-box-shadow:inset 0 0 1px 1px rgba(217, 83, 83, 0);box-shadow:inset 0 0 1px 1px rgba(217, 83, 83, 0);}}@-moz-keyframes cd-pulse-hover-'+uid+'{0%{-moz-transform:scale(1);-webkit-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';}50%{-webkit-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';}100%{-moz-transform:scale(1.6);-webkit-box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);-moz-box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);}}@keyframes cd-pulse-hover-'+uid+'{0%{-webkit-transform:scale(1);-moz-transform:scale(1);-ms-transform:scale(1);-o-transform:scale(1);transform:scale(1);-webkit-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';}50%{-webkit-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';-moz-box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';box-shadow:inset 0 0 1px 1px '+shadow_hover_color+';}100%{-webkit-transform:scale(1.6);-moz-transform:scale(1.6);-ms-transform:scale(1.6);-o-transform:scale(1.6);transform:scale(1.6);-webkit-box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);-moz-box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);box-shadow:inset 0 0 1px 1px rgba(217,83,83,0);}} .'+uid+'.'+pin_style+'.pt-plus-pin-point-single::after{-webkit-animation: cd-pulse-'+uid+' 2s infinite;-moz-animation: cd-pulse-'+uid+' 2s infinite;animation: cd-pulse-'+uid+' 2s infinite;}.'+uid+'.'+pin_style+'.pt-plus-pin-point-single:hover:after{-webkit-animation: cd-pulse-hover-'+uid+' 2s infinite;-moz-animation: cd-pulse-hover-'+uid+' 2s infinite;animation: cd-pulse-hover-'+uid+' 2s infinite;}</style>');
		}
		});
		
	});
})(jQuery);
/*----ThePlus portfolio js -------*/
(function($) {
    'use strict';
	$(window).load(function() {
		$(document).ready(function() {
			/* theplus portfolio */
			$('.pt_theplus-list-portfolio-post .grid-item').each(function() {
				var data_opacity= $(this).data('opacity');
				var data_color=$(this).data('color');
				var rgba_color=pt_plus_hexToRgbA(data_color,data_opacity);
				$(this).find(".portfolio-item-hover").css('background',rgba_color);  
			});
			$('.pt_theplus-list-portfolio-post .grid-item.style-2').each( function() { $(this).hoverdir(); } );
		/* theplus portfolio */
			$('.list-carousel-slick,.post-format-gallery,.slick-initialized').resize();
		});
	});
})(jQuery);
/*----ThePlus portfolio js -------*/
function pt_plus_hexToRgbA(hex,data_opacity){
    var c;
    if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
        c= hex.substring(1).split('');
        if(c.length== 3){
            c= [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c= '0x'+c.join('');
        return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+data_opacity+')';
	}
}
/*-----pin to point js-------------*/
/*-Grid Masonry Metro list js-----*/
(function($) {
    'use strict';
	$(document).ready(function() {
		$('.pt-plus-filter-post-category').each(function() {
			var e, c = $(this);
			var uid=c.data('id');
			var filter_btn_style=$('.'+uid).data('filter_btn_style');
			var filter_hover_style=$('.'+uid).data('filter_hover_style');
			var filter_text_font_size=$('.'+uid).data('filter_text_font_size');
			var filter_text_line_height=$('.'+uid).data('filter_text_line_height');
			var filter_text_letter_space=$('.'+uid).data('filter_text_letter_space');
			var filter_text_color=$('.'+uid).data('filter_text_color');
			var filter_text_hover_color=$('.'+uid).data('filter_text_hover_color');
			var filter_color_1=$('.'+uid).data('filter_color_1');
			var filter_color_2=$('.'+uid).data('filter_color_2');
			
		$('head').append('<style >.'+uid+' .category-filters li a{color: '+filter_text_color+';font-size:'+filter_text_font_size+';line-height:'+filter_text_line_height+';letter-spacing:'+filter_text_letter_space+';}.'+uid+' .category-filters li a:hover,.'+uid+' .category-filters li a:focus,.'+uid+' .category-filters li a.active{color: '+filter_text_hover_color+';}</style>');
			if(filter_btn_style=='style-1'){
				$('head').append('<style >.'+uid+' .category-filters.style-1 li a.all span.all_post_count{background: '+filter_color_2+';color:'+filter_color_1+';}</style>');
			}
			if(filter_btn_style=='style-3'){
			$('head').append('<style >.'+uid+' .category-filters.style-3 a span.all_post_count{background: '+filter_color_2+';color:'+filter_text_color+';}.'+uid+' .category-filters.style-3 a span.all_post_count:before{border-top-color:'+filter_color_2+';}</style>');
			}
			if(filter_hover_style=='hover-style-1'){
				$('head').append('<style >.'+uid+' .category-filters.hover-style-1 li a::after{background: '+filter_color_2+';}</style>');
			}
			if(filter_hover_style=='hover-style-2'){
				$('head').append('<style >.'+uid+' .category-filters.hover-style-2 li a span:not(.all_post_count),.'+uid+' .category-filters.hover-style-2 li a span:not(.all_post_count)::before{background: '+filter_color_1+';}.pt-plus-filter-post-category ul.category-filters.hover-style-2 li a:hover span:not(.all_post_count):before, .pt-plus-filter-post-category ul.category-filters.hover-style-2 li a:focus span:not(.all_post_count):before, .pt-plus-filter-post-category ul.category-filters.hover-style-2 li a.active span:not(.all_post_count):before{background: '+filter_color_2+';}</style>');
			}
			if(filter_hover_style=='hover-style-4'){
				$('head').append('<style >.'+uid+' .category-filters.hover-style-4 li a:before{border-top-color: '+filter_color_1+';}.'+uid+' .category-filters.hover-style-4 li a:after{background: '+filter_color_1+';}</style>');
			}
		});
	});
	var b = window.theplus || {};
	b.window = $(window),
    b.document = $(document),
    b.windowHeight = b.window.height(),
    b.windowWidth = b.window.width();	
	b.list_isotope_Posts = function() {
		var c = function(c) {
            $('.list-isotope').each(function() {
				
                var e, c = $(this), d = c.data("layout-type"),f = {
                    itemSelector: ".grid-item",
                    resizable: !0,
                    sortBy: "original-order"
				};
				var uid=c.data("id");
				var inner_c=$('.'+uid).find(".post-inner-loop");
                $('.'+uid).addClass("pt-plus-isotope layout-" + d),
                e = "masonry" === d  ? "packery" : "fitRows",
                f.layoutMode = e,
                function() {
                    //b.initMetroIsotope(),
                    inner_c.isotope(f)
				}(),
				$('.'+uid+' .post-filter-data').find(".filter-category-list").click(function(event) {
                    event.preventDefault();
                    var d = $(this).attr("data-filter");
                    $(this).parent().parent().find(".active").removeClass("active"),
                    $(this).addClass("active"),
                    inner_c.isotope({
                        filter: d
					}),
                    $("body").trigger("isotope-sorted");
				});
			})
		};
		b.window.on("load resize", function() {
            c('[data-enable-isotope="1"]')
		}),
        $("body").on("post-load resort-isotope", function() {
            setTimeout(function() {
                c('[data-enable-isotope="1"]')
			}, 800)
		}),
        $("body").on("tabs-reinited", function() {
            setTimeout(function() {
                c('[data-enable-isotope="1"]')
			}, 800)
		}),
        $.browser.firefox = /firefox/.test(navigator.userAgent.toLowerCase()),
        $.browser.firefox && setTimeout(function() {
            c('[data-enable-isotope="1"]')
		}, 2500);
	},
	b.init = function() {
		b.list_isotope_Posts();
	}
    ,
    b.init();
	$(document).ready(function() {
	$('.list-isotope-metro').each(function() {
		var c = $(this);
		var uid=c.data("id");
		var inner_c=$('.'+uid).find(".post-inner-loop");
		$('.'+uid+' .post-filter-data').find(".filter-category-list").click(function(event) {
			event.preventDefault();
			var d = $(this).attr("data-filter");
			$(this).parent().parent().find(".active").removeClass("active"),
			$(this).addClass("active"),
			inner_c.isotope({
				filter: d,
				visibleStyle: { opacity: 1 }
			}),
			$("body").trigger("isotope-sorted");
			
		});
	});
	});
})(jQuery);
jQuery(document).ready(function(){
	"use strict";
	var $port_metro_container = jQuery('.list-isotope-metro .post-inner-loop');
	if (jQuery('.list-isotope-metro').size() > 0) {
		theplus_setup_packery_portfolio('all');
	}
});

jQuery(window).load(function () {
	"use strict";
	if (jQuery('.list-isotope-metro').size() > 0) {
		theplus_setup_packery_portfolio('all');	
		jQuery('.list-isotope-metro .post-inner-loop').isotope('layout');
	}		
});
jQuery(window).resize(function () {
	"use strict";
	if (jQuery('.list-isotope-metro').size() > 0) {
		theplus_setup_packery_portfolio('all');	
		jQuery('.list-isotope-metro .post-inner-loop').isotope('layout');
	}	
});
function theplus_setup_packery_portfolio(packery_id) {
	jQuery('.list-isotope-metro').each(function(){
		//var setPad = Math.floor(parseInt(jQuery(this).attr('data-pad'))/2);
		var setPad = 0;
		var myWindow=jQuery(window);
		//jQuery(this).find('.post-inner-loop').css('margin', setPad+'px');
		
		if (jQuery(this).attr('data-columns') == '4') {			
			var	norm_size = Math.floor((jQuery(this).width() - setPad*2)/3),
			double_size = norm_size*2;
			jQuery(this).find('.grid-item').each(function(){
				var set_w = norm_size,
				set_h = norm_size;					
				if (jQuery(this).hasClass('metro-item1') ) {
					set_w = double_size,
					set_h = double_size;
				}
				if (jQuery(this).hasClass('metro-item4') || jQuery(this).hasClass('metro-item6')) {
					set_w = double_size,
					set_h = norm_size;
				}
				if (jQuery(this).hasClass('metro-item3') || jQuery(this).hasClass('metro-item7')) {
					set_w = norm_size,
					set_h = double_size;
				}
				if (myWindow.width() < 760) {
					set_w = myWindow.width() - setPad*2;
					set_h = myWindow.width() - setPad*2;
				}
				jQuery(this).find('.packery_item_inner').css({
					'margin-left' : setPad+'px',
					'margin-top' : setPad+'px',
					'margin-right' : setPad+'px',
					'margin-bottom' : setPad+'px',
					'width' : (set_w-setPad*2)+'px',
					'height' : (set_h-setPad*2)+'px'
				});
				jQuery(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});							
			});
		}
		
		if (jQuery(this).attr('data-columns') == '3') {
			var	norm_size = Math.floor((jQuery(this).width() - setPad*2)/4),
			double_size = norm_size*2;				
			jQuery(this).find('.grid-item').each(function(){
				var set_w = norm_size,
				set_h = norm_size;					
				if (jQuery(this).hasClass('metro-item1') || jQuery(this).hasClass('metro-item7')) {
					set_w = double_size,
					set_h = double_size;
				}
				if (jQuery(this).hasClass('metro-item4') || jQuery(this).hasClass('metro-item8')  || jQuery(this).hasClass('metro-item11')) {
					set_w = double_size,
					set_h = norm_size;
				}
				if (myWindow.width() < 760) {
					set_w = myWindow.width() - setPad*2;
					set_h = myWindow.width() - setPad*2;
				}				
				jQuery(this).find('.packery_item_inner').css({
					'margin-left' : setPad+'px',
					'margin-top' : setPad+'px',
					'margin-right' : setPad+'px',
					'margin-bottom' : setPad+'px',
					'width' : (set_w-setPad*2)+'px',
					'height' : (set_h-setPad*2)+'px'
				});
				jQuery(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});
				
			});
		}
		if (jQuery(this).attr('data-columns') == '2') {
			var	norm_size = Math.floor((jQuery(this).width() - setPad*2)/5),
			double_size = norm_size*2;				
			jQuery(this).find('.grid-item').each(function(){
				var set_w = norm_size,
				set_h = norm_size;					
				if (jQuery(this).hasClass('metro-item2') || jQuery(this).hasClass('metro-item7') || jQuery(this).hasClass('metro-item10')) {
					set_w = double_size,
					set_h = double_size;
				}
				if (jQuery(this).hasClass('metro-item1') || jQuery(this).hasClass('metro-item11')) {
					set_w = double_size,
					set_h = norm_size;
				}
				if (jQuery(this).hasClass('metro-item4') || jQuery(this).hasClass('metro-item6')) {
					set_w = norm_size,
					set_h = double_size;
				}
				if (myWindow.width() < 760) {
					set_w = myWindow.width() - setPad*2;
					set_h = myWindow.width() - setPad*2;
				}				
				jQuery(this).find('.packery_item_inner').css({
					'margin-left' : setPad+'px',
					'margin-top' : setPad+'px',
					'margin-right' : setPad+'px',
					'margin-bottom' : setPad+'px',
					'width' : (set_w-setPad*2)+'px',
					'height' : (set_h-setPad*2)+'px'
				});
				jQuery(this).css({
					'width' : set_w+'px',
					'height' : set_h+'px'
				});
				
			});
		}
		if(jQuery(this).hasClass('list-isotope-metro')){
			if (myWindow.innerWidth() > 767) {
				jQuery(this).find('.post-inner-loop').isotope({
					layoutMode: 'masonry',
					masonry: {
						columnWidth: norm_size
					}
				});
			}else{
				jQuery(this).find('.post-inner-loop').isotope({
					layoutMode: 'masonry',
					masonry: {
						columnWidth: '.grid-item'
					}
				});
			}
		}else{
			jQuery(this).find('.post-inner-loop').isotope({
				layoutMode: 'masonry',
				masonry: {
					columnWidth: norm_size
				}
			});
		}
		jQuery(this).find('.post-inner-loop').isotope('layout').isotope( 'reloadItems' );
	});
}
/*-Grid Masonry Metro list js-----*/
/*---mouse move parallax ---*/
( function ( $ ) {	
	'use strict';
	$(document).ready(function(){
				var $parallaxContainer 	  = $(".pt-plus-move-parallax");
				var $parallaxItems		    = $parallaxContainer.find(".parallax-move");
				var fixer  = 0.0008;
				
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
					   $(this).parallax(speedX,speedY, e);
					});
				 });
			});
} ( jQuery ) );

(function ( $ ) { 
'use strict';
$.fn.parallax = function (resistancex, resistancey, mouse ) {
	var $el = $( this );
	TweenLite.to( $el, 0.5, {
		x : -(( mouse.clientX - (window.innerWidth/2) ) / resistancex),
		y : -(( mouse.clientY - (window.innerHeight/2) ) / resistancey)
	});
};
} ( jQuery ) );
/*---mouse move parallax ---*/
/*----load more post ajax----------------*/
;( function($) {
	'use strict';
	$(document).ready(function(){
		$(".post-load-more").each(function() {
			$(this).on("click",function(e){
				
				e.preventDefault();
				var current_click= $(this);
				var post_load=$(this).attr('data-load');
				var post_type=$(this).attr('data-post_type');
				var texonomy_category=$(this).attr('data-texonomy_category');
				
				var page = $(this).attr('data-page');
				var total_page=$(this).attr('data-total_page');
				var load_class= $(this).attr('data-load-class');
				var layout=$(this).attr('data-layout');
				var desktop_column=$(this).attr('data-desktop-column');
				var tablet_column=$(this).attr('data-tablet-column');
				var mobile_column=$(this).attr('data-mobile-column');
				var style=$(this).attr('data-style');
				var category=$(this).attr('data-category');
				var order_by=$(this).attr('data-order_by');
				var post_sort=$(this).attr('data-post_sort');
				var filter_category=$(this).attr('data-filter_category');
				var display_post=$(this).attr('data-display_post');
				var post_load_more=$(this).attr('data-post_load_more');
				var animated_columns=$(this).attr('data-animated_columns');
				var current_text= $(this).text();
				if ( current_click.data('requestRunning') ) {
					return;
				}
				
				current_click.data('requestRunning', true);
				if(page<=total_page){
					var offset=(page-1)*post_load_more;
					offset=parseInt(offset)+parseInt(display_post);
					$.ajax({
						type:'POST',
						data:'style='+style+'&action=pt_plus_more_post&post_load='+post_load+'&post_type='+post_type+'&texonomy_category='+texonomy_category+'&layout='+layout+'&desktop_column='+desktop_column+'&tablet_column='+tablet_column+'&mobile_column='+mobile_column+'&offset='+offset+'&category='+category+'&display_post='+display_post+'&order_by='+order_by+'&filter_category='+filter_category+'&post_sort='+post_sort+'&animated_columns='+animated_columns+'&post_load_more='+post_load_more,
						url:theplus_ajax_url,
						beforeSend: function() {
							$(current_click).text('Loading..');
							},success: function(data) {         
							if(data==''){
								$(current_click).addClass("hide");
								}else{
								$("."+load_class).append( data );
								if(layout=='grid' || layout=='masonry'){
									var $newItems = $('');
									$("."+load_class).isotope( 'insert', $newItems );
									$("."+load_class).isotope( 'layout' ).isotope( 'reloadItems' ); 
									
								}else{
									setTimeout(function(){	
										$('.pt_theplus-list-portfolio-post .grid-item.style-2').each( function() { $(this).hoverdir(); } );
									}, 100);	
								}
								if (jQuery('.list-isotope-metro').size() > 0) {
									theplus_setup_packery_portfolio('all');	
								}
								if($("."+load_class).parents(".animate-general").length){
								var c,d;
								if($("."+load_class).find(".animated-columns").length){
										var p = $("."+load_class).parents(".animate-general");
										var delay_time=p.data("animate-delay");
										var animation_stagger=p.data("animate-stagger");
										var d = p.data("animate-type");
										p.css("opacity","1");
										c = p.find('.animated-columns');
										c.each(function() {
											var bc=$(this);
											bc.waypoint(function(direction) {
												if( direction === 'down'){
													if(bc.hasClass("animation-done")){
														bc.hasClass("animation-done");
													}else{
														bc.addClass("animation-done").velocity(d,{ delay: delay_time,display:'auto'});
													}
												}
											}, {triggerOnce: true,  offset: '85%' } );
										});
									}else{
										var b = $("."+load_class).parents(".animate-general");
										var delay_time=b.data("animate-delay");
										d = b.data("animate-type"),
										b.waypoint(function(direction ) {
											if( direction === 'down'){
												if(b.hasClass("animation-done")){
													b.hasClass("animation-done");
												}else{
													b.addClass("animation-done").velocity(d, {delay: delay_time,display:'auto'});
												}
											}
										}, {triggerOnce: true,  offset: '85%' } );
								}
								}
								$(".hover-tilt").hover3d({
									selector: ".blog-list-style-content,.portfolio-item-content",
									shine: !1,
									perspective: 1000,
									invert: !0,
									sensitivity: 35,
								});
								$('.pt_theplus-list-portfolio-post .grid-item').each(function() {
									var data_opacity= $(this).data('opacity');
									var data_color=$(this).data('color');
									var rgba_color=pt_plus_hexToRgbA(data_color,data_opacity);
									$(this).find(".portfolio-item-hover").css('background',rgba_color);  
								});
							}
							page++;
							if(page==total_page){
								$(current_click).addClass("hide");
								$(current_click).attr('data-page', page);
								}else{
								$(current_click).text(current_text);
								
								$(current_click).attr('data-page', page);	
							}
							},complete: function() {
							if(layout=='grid' || layout=='masonry'){
								setTimeout(function(){	
									$("."+load_class).isotope( 'layout' ).isotope( 'reloadItems' );
									$('.pt_theplus-list-portfolio-post .grid-item.style-2').each( function() { $(this).hoverdir(); } );
								}, 500);
							}
							if (jQuery('.list-isotope-metro').size() > 0) {
								setTimeout(function(){	
									theplus_setup_packery_portfolio('all');	
								}, 500);
							}
							
							current_click.data('requestRunning', false);
						}
						}).then(function(){
						
						if(layout=='grid' || layout=='masonry'){
							var container = $("."+load_class);
							container.isotope({
								itemSelector: '.grid-item',
							});						
						}
						if (jQuery('.list-isotope-metro').size() > 0) {
							theplus_setup_packery_portfolio('all');	
						}
						
					});
					}else{
					$(current_click).addClass("hide");
				}
			});
		});
	});
})(jQuery );
/*----load more post ajax----------------*/
/*----lazy load ajax----------------*/
;( function($) {
	'use strict';
	$(window).load(function(){
		
		if($('body').find('.pt_theplus_lazy_load').length>=1){
			
			var windowWidth, windowHeight, documentHeight, scrollTop, containerHeight, containerOffset, $window = $(window);
			
			var recalcValues = function() {
				windowWidth = $window.width();
				windowHeight = $window.height();
				documentHeight = $('body').height();
				containerHeight = $('.list-isotope').height();
				containerOffset = $('.list-isotope').offset().top;
			};
			
			recalcValues();
			$window.resize(recalcValues);
			
			$window.bind('scroll', function(e) {
				
				e.preventDefault();
				recalcValues();
				scrollTop = $window.scrollTop();
				if(scrollTop < documentHeight && scrollTop > (containerHeight + containerOffset - windowHeight)){
					
					$(".post-lazy-load").each(function() {
						var current_click= $(this);
						var post_load=$(this).attr('data-load');
						var post_type=$(this).attr('data-post_type');
						var texonomy_category=$(this).attr('data-texonomy_category');
						
						var page = $(this).attr('data-page');
						var total_page=$(this).attr('data-total_page');
						var load_class= $(this).attr('data-load-class');
						var layout=$(this).attr('data-layout');
						var desktop_column=$(this).attr('data-desktop-column');
						var tablet_column=$(this).attr('data-tablet-column');
						var mobile_column=$(this).attr('data-mobile-column');
						var style=$(this).attr('data-style');
						var category=$(this).attr('data-category');
						var order_by=$(this).attr('data-order_by');
						var post_sort=$(this).attr('data-post_sort');
						var filter_category=$(this).attr('data-filter_category');
						var display_post=$(this).attr('data-display_post');
						var post_load_more=$(this).attr('data-post_load_more');
						var animated_columns=$(this).attr('data-animated_columns');
						var current_text= $(this).text();
						if ( current_click.data('requestRunning') ) {
							return;
						}
						
						if(page<=total_page){
							current_click.data('requestRunning', true);
							var offset=(page-1)*post_load_more;
							offset=parseInt(offset)+parseInt(display_post);
							
							$.ajax({
								type:'POST',
								data:'style='+style+'&action=pt_plus_more_post&post_load='+post_load+'&post_type='+post_type+'&texonomy_category='+texonomy_category+'&layout='+layout+'&desktop_column='+desktop_column+'&tablet_column='+tablet_column+'&mobile_column='+mobile_column+'&offset='+offset+'&category='+category+'&display_post='+display_post+'&order_by='+order_by+'&filter_category='+filter_category+'&post_sort='+post_sort+'&animated_columns='+animated_columns+'&post_load_more='+post_load_more,
								url:theplus_ajax_url,
								beforeSend: function() {
									$(current_click).text('Loading..');
									},success: function(data) {         
									if(data==''){
										$(current_click).addClass("hide");
										}else{
										$("."+load_class).append( data );
										
										if(layout=='grid' || layout=='masonry'){
											var $newItems = $('');
											$("."+load_class).isotope( 'insert', $newItems );
											$("."+load_class).isotope( 'layout' ).isotope( 'reloadItems' ); 
											
											}else{
											setTimeout(function(){	
												$('.pt_theplus-list-portfolio-post .grid-item.style-3').each( function() { $(this).hoverdir(); } );
											}, 100);
										}
										if (jQuery('.list-isotope-metro').size() > 0) {
											theplus_setup_packery_portfolio('all');	
										}
										
										if($("."+load_class).parents(".animate-general").length){
											var c,d;
											if($("."+load_class).find(".animated-columns").length){
													var p = $("."+load_class).parents(".animate-general");
													var delay_time=p.data("animate-delay");
													var animation_stagger=p.data("animate-stagger");
													var d = p.data("animate-type");
													p.css("opacity","1");
													c = p.find('.animated-columns');
													c.each(function() {
														var bc=$(this);
														bc.waypoint(function(direction) {
															if( direction === 'down'){
																if(bc.hasClass("animation-done")){
																	bc.hasClass("animation-done");
																}else{
																	bc.addClass("animation-done").velocity(d,{ delay: delay_time,display:'auto'});
																}
															}
														}, {triggerOnce: true,  offset: '85%' } );
													});
												}else{
													var b = $("."+load_class).parents(".animate-general");
													var delay_time=b.data("animate-delay");
													d = b.data("animate-type"),
													b.waypoint(function(direction ) {
														if( direction === 'down'){
															if(b.hasClass("animation-done")){
																b.hasClass("animation-done");
															}else{
																b.addClass("animation-done").velocity(d, {delay: delay_time,display:'auto'});
															}
														}
													}, {triggerOnce: true,  offset: '85%' } );
											}
											
									}
										
										$(".hover-tilt").hover3d({
											selector: ".blog-list-style-content,.portfolio-item-content",
											shine: !1,
											perspective: 2e3,
											invert: !0,
											sensitivity: 35,
										});
										$('.pt_theplus-list-portfolio-post .grid-item').each(function() {
											var data_opacity= $(this).data('opacity');
											var data_color=$(this).data('color');
											var rgba_color=pt_plus_hexToRgbA(data_color,data_opacity);
											$(this).find(".portfolio-item-hover").css('background',rgba_color);  
										});
										page++;
										if(page==total_page){
											$(current_click).addClass("hide");
											$(current_click).attr('data-page', page);	
											}else{
											$(current_click).text(current_text);
											
											$(current_click).attr('data-page', page);	
										}
									}
									$(current_click).text(current_text);
									page++;
									$(current_click).attr('data-page', page);	
									
									},complete: function() {
									if(layout=='grid' || layout=='masonry'){
										setTimeout(function(){
											$("."+load_class).isotope( 'layout' ).isotope( 'reloadItems' );
											$('.pt_theplus-list-portfolio-post .grid-item.style-2').each( function() { $(this).hoverdir(); } );
										}, 500);
									}
									if (jQuery('.list-isotope-metro').size() > 0) {
										setTimeout(function(){	
											theplus_setup_packery_portfolio('all');	
										}, 500);
									}
									
									current_click.data('requestRunning', false);
								}
								}).then(function(){
								if(layout=='grid' || layout=='masonry'){
									var container = $("."+load_class);
									container.isotope({
										itemSelector: '.grid-item',
									});								
								}
								if (jQuery('.list-isotope-metro').size() > 0) {
									theplus_setup_packery_portfolio('all');	
								}
								
							});
							
							}else{
							$(current_click).addClass("hide");
						}
					});
				}
			});
		}
	});
})(jQuery );
/*---*/
/*----cascading image loop slide -----*/
;( function($) {
	'use strict';
	$(window).load(function(){
		$(".slide_show_image").length && $(".slide_show_image").each(function() {
		var t = $(this),uid1=t.data("uid");
		var uid=$('.'+uid1);
		$('.'+uid1+'.slide_show_image .cascading-image:last').addClass('active');
		var  i = t.find(".cascading-image"),opt=t.data("play");
			$('.'+uid1+" .cascading-image").each(function() {
				var o = $(this);
			if(opt=='onclick'){
				o.click(function() {
					if (!i.last().is(o))
						return o.addClass("out").animate({
							opacity: 0.7,
						}, 200, function() {
							o.detach(),
							o.insertAfter(i.last()).animate({
								opacity: 1,
							}, 500, function() {
								o.removeClass("out")
							}),
							i = t.find(".cascading-image"),
							i.removeClass('active'),
							o.addClass("active");
						}),
						!1
					});
			}else{
				var time=$('.'+uid1).data('interval_time');
				setInterval(function () {
					var current = $('.'+uid1+'.slide_show_image .cascading-image.active').removeClass('active'),
					o = current.next().length ? current.next() : current.siblings().filter(':first');
					o.addClass('active');
					if (!i.last().is(o))
						return o.addClass("out").animate({
							opacity: 0.7,
						}, 200, function() {
							o.detach(),
							o.insertAfter(i.last()).animate({
								opacity: 1,
							}, 500, function() {
								o.removeClass("out")
							}),
							i = t.find(".cascading-image"),
							i.removeClass('active'),
							o.addClass("active");
						}),
						!1
				}, time);
				o.click(function() {
					if (!i.last().is(o))
						return o.addClass("out").animate({
							opacity: 0.7,
						}, 200, function() {
							o.detach(),
							o.insertAfter(i.last()).animate({
								opacity: 1,
							}, 500, function() {
								o.removeClass("out")
							}),
							i = t.find(".cascading-image"),
							i.removeClass('active'),
							o.addClass("active");
						}),
						!1
					});
			}
			})
		});
	});
})(jQuery );
;( function($) {
	'use strict';
	$(window).load(function(){
		$('.plus-smart-gallery .gallery-attach-list').each(createFader);
	});
})(jQuery );
function createFader(i, elem) {
  var track = jQuery(elem);
  var interval_time=jQuery(elem).data("interval-time");
   var lengthSlide = track.find('.gallery-list-item').length;
  var firstSlide = track.find('.gallery-list-item').eq(0);
  var loop = 0;
	if(lengthSlide>1){
  firstSlide.nextAll().hide();
  setInterval(function() {
    loop++;
	var options = {};
    firstSlide = firstSlide.fadeOut(700, function() {
        jQuery(this).appendTo(track);
      })
      .next()
      .fadeIn(700);
  }, interval_time);
	}
}
/*----cascading image loop slide -----*/
/*---- row background parallax scroll------*/
( function ( $ ) {	
	'use strict';
	$(document).ready(function(){
		pt_plus_bgParalax();
	});
} ( jQuery ) );
function pt_plus_bgParalax() {
	if(jQuery('body').find('.row-parallax-bg-img').length>0){
  var controller = new ScrollMagic.Controller();
  jQuery('.row-parallax-bg-img').each(function(index, elem){
    var tween = 'tween1-'+index;
    tween = new TimelineMax();
    var lengthBox = jQuery(elem).find('.parallax-bg-img').length;
   
var $bcg =  jQuery(elem).find('.parallax-bg-img');
 
    var slideParallaxScene = new ScrollMagic.Scene({
        triggerElement: elem,
        triggerHook: 1,
        duration: "200%"
    })
    .setTween(TweenMax.fromTo($bcg, 1, {delay:0.5,backgroundPositionY: '20%', ease:Power0.easeNone},{delay:0.5,backgroundPositionY: '80%', ease:Power0.easeNone}))
    .addTo(controller);
  })
	}
}
/*---- row background parallax scroll------*/