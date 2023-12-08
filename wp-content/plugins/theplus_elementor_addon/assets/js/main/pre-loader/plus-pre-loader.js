/*preloader*/(function ($) {
	'use strict';	
	var WidgetPreLoader = function($scope, $) {
		var container = $scope.find('.tp-loader-wrapper'),
		data = container.data('plec'),
		post_load_opt = container.data('post_load_opt'),
		post_load_exclude_class = data['post_load_exclude_class'];
		
		if($( "#tp-img-loader" ).length){
			var heightimg = $("#tp-img-loader .tp-preloader-logo-img").height(),
			widthimg = $("#tp-img-loader .tp-preloader-logo-img").width();
			$(".tp-img-loader-wrap .tp-img-loader-wrap-in").css("width",widthimg).css("height",heightimg);			
		}		
		
		$(document).ready(function(){
			
			if(post_load_opt==='disablepostload'){
				$("body").removeClass("theplus-preloader");
			}			
			setTimeout(function(){
				
			}, 20);
			if($('body').hasClass('theplus-preloader')){
				if(post_load_exclude_class != undefined && post_load_exclude_class !=''){
					$(document).on("click", post_load_exclude_class, function(e) {
						if ((e.shiftKey || e.ctrlKey || e.metaKey || '_blank' == $.trim($(this).attr('target')))) {
							return;
						}					
						$('body').removeClass('tp-loaded').addClass('tp-out-loaded');
						
						if($('body.tp-out-loaded').find(".tp-loader-wrapper").hasClass("tp-preload-transion4")){
							$("body").find(".tp-loader-wrapper.tp-preload-transion4 .tp-preload-reveal-layer-box").css("transform","");
							var transform, direction='';
							if( $( ".tp-out-loaded .tp-4-postload-topleft" ).length || $( ".tp-out-loaded .tp-4-postload-topright" ).length  || $( ".tp-out-loaded .tp-4-postload-bottomleft" ).length  || $( ".tp-out-loaded .tp-4-postload-bottomright" ).length  ) {	
								var winsize = {width: window.innerWidth, height: window.innerHeight};
								var crosswh = Math.sqrt(Math.pow(winsize.width, 2) + Math.pow(winsize.height, 2));
								
								if( $( ".tp-out-loaded .tp-4-postload-topleft" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,135deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if( $( ".tp-out-loaded .tp-4-postload-topright" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,-135deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if( $( ".tp-out-loaded .tp-4-postload-bottomleft" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,45deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if(  $( ".tp-out-loaded .tp-4-postload-bottomright" ).length  ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,-45deg) translate3d(0,' + crosswh + 'px,0)';
								}
							}else if( $( ".tp-out-loaded .tp-4-postload-left" ).length || $( ".tp-4-postload-right" ).length ) {
								direction='right';
								if($( ".tp-out-loaded .tp-4-postload-left" ).length){
									direction='left';
								}
								transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,' + (direction === 'left' ? 90 : -90) + 'deg) translate3d(0,100%,0)';
							}else if( $( ".tp-out-loaded .tp-4-postload-top" ).length || $( ".tp-out-loaded .tp-4-postload-bottom" ).length ) {
								direction='bottom';
								if($( ".tp-out-loaded .tp-4-postload-top" ).length){
									direction='top';
								}
								transform = direction === 'top' ? 'rotate3d(0,0,1,180deg)' : 'none';
							}
							if( $( ".tp-out-loaded .tp-4-postload-topleft" ).length || $( ".tp-out-loaded .tp-4-postload-topright" ).length  || $( ".tp-out-loaded .tp-4-postload-bottomleft" ).length  || $( ".tp-out-loaded .tp-4-postload-bottomright" ).length  || $( ".tp-out-loaded .tp-4-postload-left" ).length || $( ".tp-out-loaded .tp-4-postload-right" ).length || $( ".tp-out-loaded .tp-4-postload-top" ).length || $( ".tp-out-loaded .tp-4-postload-bottom" ).length ) {
								$( ".tp-out-loaded .tp-loader-wrapper .tp-preload-reveal-layer-box" ).css("transform",transform).css("-webkit-transform",transform);
							}
						}
						
						/*setTimeout(function(){
							$('body').removeClass('tp-out-loaded').addClass('tp-loaded');
						}, 4000);*/
					});
				}else{
					$(document).on("click", 'a:not(.ajax_add_to_cart,.button-toggle-link,.noajax,.post-load-more,.slick-slide, .woocommerce a, .btn, .button,[data-slick-index],[data-month], .popup-gallery, .popup-video, [href$=".png"], [href$=".jpg"], [href$=".jpeg"], [href$=".svg"], [href$=".mp4"], [href$=".webm"], [href$=".ogg"], [href$=".mp3"], [href^="#"],[href*="#"], [href^="mailto:"],[data-lity=""], [href=""], [href*="wp-login"], [href*="wp-admin"], .dot-nav-noajax, .pix-dropdown-arrow,[data-toggle="dropdown"],[role="tab"]),button:not(.elementor-button,.lity-close,[type="button"],.single_add_to_cart_button,.pswp__button.pswp__button--close,.pswp__button--fs,.pswp__button--zoom,.pswp__button--arrow--left,.pswp__button--arrow--right)', function(e) {
						if ((e.shiftKey || e.ctrlKey || e.metaKey || '_blank' == $.trim($(this).attr('target')))) {
							return;
						}					
						$('body').removeClass('tp-loaded').addClass('tp-out-loaded');
						if($('body.tp-out-loaded').find(".tp-loader-wrapper").hasClass("tp-preload-transion4")){
							$("body").find(".tp-loader-wrapper.tp-preload-transion4 .tp-preload-reveal-layer-box").css("transform","");
							var transform, direction='';
							if( $( ".tp-out-loaded .tp-4-postload-topleft" ).length || $( ".tp-out-loaded .tp-4-postload-topright" ).length  || $( ".tp-out-loaded .tp-4-postload-bottomleft" ).length  || $( ".tp-out-loaded .tp-4-postload-bottomright" ).length  ) {	
								var winsize = {width: window.innerWidth, height: window.innerHeight};
								var crosswh = Math.sqrt(Math.pow(winsize.width, 2) + Math.pow(winsize.height, 2));
								
								if( $( ".tp-out-loaded .tp-4-postload-topleft" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,135deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if( $( ".tp-out-loaded .tp-4-postload-topright" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,-135deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if( $( ".tp-out-loaded .tp-4-postload-bottomleft" ).length ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,45deg) translate3d(0,' + crosswh + 'px,0)';
								}
								else if(  $( ".tp-out-loaded .tp-4-postload-bottomright" ).length  ) {
									transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,-45deg) translate3d(0,' + crosswh + 'px,0)';
								}
							}else if( $( ".tp-out-loaded .tp-4-postload-left" ).length || $( ".tp-4-postload-right" ).length ) {
								direction='right';
								if($( ".tp-out-loaded .tp-4-postload-left" ).length){
									direction='left';
								}
								transform = 'translate3d(-50%,-50%,0) rotate3d(0,0,1,' + (direction === 'left' ? 90 : -90) + 'deg) translate3d(0,100%,0)';
							}else if( $( ".tp-out-loaded .tp-4-postload-top" ).length || $( ".tp-out-loaded .tp-4-postload-bottom" ).length ) {
								direction='bottom';
								if($( ".tp-out-loaded .tp-4-postload-top" ).length){
									direction='top';
								}
								transform = direction === 'top' ? 'rotate3d(0,0,1,180deg)' : 'none';
							}
							if( $( ".tp-out-loaded .tp-4-postload-topleft" ).length || $( ".tp-out-loaded .tp-4-postload-topright" ).length  || $( ".tp-out-loaded .tp-4-postload-bottomleft" ).length  || $( ".tp-out-loaded .tp-4-postload-bottomright" ).length  || $( ".tp-out-loaded .tp-4-postload-left" ).length || $( ".tp-out-loaded .tp-4-postload-right" ).length || $( ".tp-out-loaded .tp-4-postload-top" ).length || $( ".tp-out-loaded .tp-4-postload-bottom" ).length ) {
								$( ".tp-out-loaded .tp-loader-wrapper .tp-preload-reveal-layer-box" ).css("transform",transform).css("-webkit-transform",transform);
							}
						}
						/*setTimeout(function(){							
							$('body').removeClass('tp-out-loaded').addClass('tp-loaded');
						}, 4000);*/
					});
				}
			}
		});		
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-pre-loader.default', WidgetPreLoader);
	});

})(jQuery);


jQuery(window).on('load', function(){
	var width = 100,
    performancedata = window.performance.timing,
    estimatedloadtime = -(performancedata.loadEventEnd - performancedata.navigationStart),
    time = parseInt((estimatedloadtime/1000)%60)*100;
	
	var containerload = jQuery('.tp-loader-wrapper');
	if(typeof elementorFrontend !== 'undefined' && !elementorFrontend.isEditMode() && containerload.length){
		var data = containerload.data('plec'),		
		loadtime = data['loadtime'],
		loadmaxtime = data['loadmaxtime'],
		loadmintime = data['loadmintime'],		
		csttimemax1000 = loadmaxtime*1000,
		csttimemin1000 = loadmintime*1000;
						
		if(csttimemax1000 != undefined && csttimemax1000 < time && loadtime!=undefined && loadtime=='loadtimemax'){
			time = csttimemax1000;
		}
		
		if(csttimemin1000 != undefined && csttimemin1000 > time && loadtime!=undefined && loadtime=='loadtimemin'){
			time = csttimemin1000;
		}
	}	
	//console.log(time);
	if(width > 1){
		jQuery(".tp-percentage").addClass("tp-percentage-load");
	}
	var tp_preloader = 'tp-preloader-wrap',
		tp_loadbar = 'tp-loadbar',
		tp_percentagelayout = 'percentagelayout',
		tp_plcper = 'plcper',
		tp_logo_width = 'tp-loader-wrapper .tp-img-loader-wrap',
		tp_text_loader = 'tp-loader-wrapper .tp-text-loader .tp-text-loader-inner',
		tp_pre_5 ='tp-pre-5-in';
		
	if( jQuery("."+tp_loadbar).length || jQuery("."+tp_percentagelayout).length || jQuery("."+tp_preloader+"4-in").length || jQuery("."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"3").length || jQuery("."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"4").length ||  jQuery("."+tp_logo_width).length ||  jQuery("."+tp_text_loader).length){
		jQuery("."+tp_loadbar+",."+tp_percentagelayout+",."+tp_preloader+"4-in,."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"3, ."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"4,."+tp_logo_width+",."+tp_text_loader).animate({
		  width: width + "%"
		}, time);
	}
	
	if( jQuery("."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"1").length || jQuery("."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"2").length){
		jQuery("."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"1, ."+tp_preloader+"5."+tp_plcper+"5 ."+tp_pre_5+"2").animate({
			height : width + "%"
		}, time);
	}

var percwrap = jQuery("#tp-precent,#tp-precent3,#tp-precent4"),
		start = 0,
		end = 100,
		durataion = time;
		if(percwrap){
			animationoutput(percwrap, start, end, durataion);
		}		
		
function animationoutput(id, start, end, duration) {
  
	var range = end - start,
      current = start,
      increment = end > start? 1 : -1,
      stepfortime = Math.abs(Math.floor(duration / range)),
      obj = jQuery(id);
    
	var timer = setInterval(function() {
		current += increment;
		jQuery(obj).text(current + "%");
		setProgress(current);
		if (current == end) {
			clearInterval(timer);
		}
	}, stepfortime);
}


var circle = document.querySelector('.progress-ring1');
if(circle){
	var radius = circle.r.baseVal.value;
	var circumference = radius * 2 * Math.PI;

	circle.style.strokeDasharray = `${circumference} ${circumference}`;
	circle.style.strokeDashoffset = `${circumference}`;
}
function setProgress(percent) {
	if(circle){
		const offset = circumference - percent / 100 * circumference;
		circle.style.strokeDashoffset = offset;
	}
}


setTimeout(function(){
  jQuery('body').addClass('tp-loaded');
	if(jQuery('body').find(".tp-loader-wrapper").hasClass("tp-preload-transion4")){
		setTimeout(function(){
			jQuery("body").find(".tp-loader-wrapper.tp-preload-transion4").addClass("tpprein");
			jQuery("body").find(".tp-loader-wrapper.tp-preload-transion4").addClass("tppreinout");
			setTimeout(function(){
				jQuery("body").find(".tp-loader-wrapper.tp-preload-transion4").removeClass("tpprein");
				jQuery("body").find(".tp-loader-wrapper.tp-preload-transion4").addClass("tppreout");
			}, 1500);						
		}, 20);
	}
  jQuery('.tp-preloader-wrap,.percentagelayout,.tp-preloader-wrap4.plcper4,.tp-preloader-wrap6').fadeOut(300);
}, time+1000);

});