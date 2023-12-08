/*body movin*/(function(g) {
	"use strict";
    function plus_bodyMovinLoad(a, b, c) {
        var d = {
                container: document.getElementById(b.container_id),
                renderer: "undefined" == typeof b.renderer ? "svg" : b.renderer,
                loop: b.loop,
                prerender: !0,
                assetsPath: "undefined" == typeof b.assets_path ? null : b.assets_path,
                autoplay: !b.autoplay_viewport && b.autoplay_onload && "autoplay" === b.play_action,
                rendererSettings: {
                    progressiveLoad: !1
                },
                animationData: JSON.parse(c)
            },
            e = jQuery("#" + b.container_id),
            f = document.getElementById(b.container_id);
			b.instance = bodymovin.loadAnimation(d), g(window).on("wpbodymovin_anim_load resize scroll", function() {
            if (!0 === b.autoplay_viewport && "function" == typeof jQuery.fn.isOnScreen)
                if (e.isOnScreen(function(b) {
                        return -100 <= b.top && -100 <= b.bottom
                    })) {
                    if (e.hasClass("playing")) return !0;
                    b.instance.play(), e.addClass("playing").removeClass("paused")
                } else if (!0 === b.autostop_viewport) {
					if (e.hasClass("paused")) return !0;
						b.instance.pause(), e.addClass("paused").removeClass("playing")
				}
				if(b.play_action =='mousescroll' && b.play_action!=''){
 
					var sec_offset = e.offset().top;
					var sec_duration = b.bm_section_duration;
					var offset_top = b.bm_section_offset;
					var total_duration = sec_duration + sec_offset - offset_top;
					if( g(window).scrollTop() >= (sec_offset - offset_top ) && g(window).scrollTop() <= total_duration && b.bm_scrollbased == 'bm_custom'){
						var scrollPercent = 100 * (g(window).scrollTop() - (sec_offset - offset_top)) / (sec_duration);
						var scrollPercentRounded = Math.round(scrollPercent);
						/*console.log(g(window).scrollTop() +'--'+sec_offset + '--' + total_duration + '--' + (scrollPercentRounded/100));*/
					}else if(b.bm_scrollbased == 'bm_document'){
						var scrollPercent = 100 * g(window).scrollTop() / (g(document).height() - g(window).height());
						var scrollPercentRounded = Math.round(scrollPercent);
						/*console.log(stop_time +'--' +scrollPercentRounded+'---'+currframe);*/
					}
 
					var start_time = 0;
					var stop_time = b.instance.totalFrames;
					if(b.bm_start_time!='' && b.bm_start_time!=undefined){
						start_time = b.bm_start_time;
					}
					if(b.bm_end_time!='' && b.bm_end_time!=undefined){
						stop_time = b.bm_end_time;
					}
 
					var currframe = ((scrollPercentRounded)/100 ) * (stop_time - start_time);
					if(currframe >= stop_time){
						b.instance.goToAndStop(stop_time, true);
					}else if(isNaN(currframe)){
						if(jQuery(window).scrollTop() <= sec_offset ){											
							b.instance.goToAndStop((start_time), true);
						}else{
							b.instance.goToAndStop((stop_time), true);
						}						
					}else{
						b.instance.goToAndStop((currframe + start_time), true);
					}
 
				}
 
        }), g(window).trigger("wpbodymovin_anim_load");
		var start_time = 0;
		if(b.bm_start_time!='' && b.bm_start_time!=undefined){
			if("autoplay" === b.play_action || "column" === b.play_action || "section" === b.play_action || "viewport" === b.play_action || "" === b.play_action){
				var start_time = b.bm_start_time;
			}
		}
		var end_time = b.instance.totalFrames;
 
            if(b.playSpeed.size){
                b.instance.setSpeed(b.playSpeed.size);
            }            
 
 
		b.play_action && ("column" === b.play_action && (f = e.closest(".elementor-widget-wrap")[0]),
		"section" === b.play_action && (f = e.closest(".elementor-section,.e-container,.e-con")[0]),
		/*mouse in out start*/
		"mouseinout" === b.play_action && (
		(b.bm_start_time!='' && b.bm_start_time!=undefined) && (
					start_time = b.bm_start_time
				),
				(b.bm_end_time!='' && b.bm_end_time!=undefined) && (
					end_time = b.bm_end_time
				),
				b.instance.playSegments([0, start_time], !0),
			jQuery(f).mouseenter(function() {
				b.instance.playSegments([start_time, end_time], !0), e.addClass("playing").removeClass("paused")
			}),  jQuery(f).closest('.theplus-bodymovin-hd').mouseenter(function() {
				b.instance.playSegments([start_time, end_time], !0), e.addClass("playing").removeClass("paused")
			}),	jQuery(f).mouseleave(function() {
				var ba = b.instance.currentRawFrame;
				b.instance.setDirection(-1), b.instance.goToAndPlay(ba, !0), e.addClass("paused").removeClass("playing")
			}),jQuery(f).closest('.theplus-bodymovin-hd').mouseleave(function() {
				var ba = b.instance.currentRawFrame;
				b.instance.setDirection(-1), b.instance.goToAndPlay(ba, !0), e.addClass("paused").removeClass("playing")
			})
		),
		/*mouse in out end*/
		/*click start*/
		"click" === b.play_action && (
				(b.bm_start_time!='' && b.bm_start_time!=undefined) && (
					start_time = b.bm_start_time
				),
				(b.bm_end_time!='' && b.bm_end_time!=undefined) && (
					end_time = b.bm_end_time
				),b.instance.goToAndStop(start_time, true),e.addClass("playing").removeClass("paused"),
				f.closest('.theplus-bodymovin-hd') ? f.closest('.theplus-bodymovin-hd').addEventListener("click", function
		() {
			b.instance.playSegments([start_time, end_time], !0)
		}) : "click" === b.play_action &&
				(b.bm_start_time!='' && b.bm_start_time!=undefined) && (
					start_time = b.bm_start_time
				),
				(b.bm_end_time!='' && b.bm_end_time!=undefined) && (
					end_time = b.bm_end_time
				),b.instance.goToAndStop(start_time, true), f.addEventListener("click", function() {
				b.instance.playSegments([start_time, end_time], !0),e.addClass("playing").removeClass("paused")			
        })),
		/*click end*/
		/*hover start*/
		"hover" === b.play_action && ((b.bm_start_time!='' && b.bm_start_time!=undefined) && (
					start_time = b.bm_start_time
				),
				(b.bm_end_time!='' && b.bm_end_time!=undefined) && (
					end_time = b.bm_end_time
				),b.instance.goToAndStop(start_time, true),
				f.closest('.theplus-bodymovin-hd') ? f.closest('.theplus-bodymovin-hd').addEventListener("mouseenter", function() {
			b.instance.playSegments([start_time, end_time], !0), e.addClass("playing").removeClass("paused")			
		}) : "hover" === b.play_action && 
			(b.bm_start_time!='' && b.bm_start_time!=undefined) && (
					start_time = b.bm_start_time
				),
				(b.bm_end_time!='' && b.bm_end_time!=undefined) && (
					end_time = b.bm_end_time
				),b.instance.goToAndStop(start_time, true),f.addEventListener("mouseenter", function() {
			b.instance.playSegments([start_time, end_time], !0),e.addClass("playing").removeClass("paused")			
		})),
		/*hover end*/
                 /*view port*/
		"viewport" === b.play_action && ((b.bm_start_time!='' && b.bm_start_time!=undefined) && (
					start_time = b.bm_start_time
				),
				(b.bm_end_time!='' && b.bm_end_time!=undefined) && (
					end_time = b.bm_end_time
				),b.instance.goToAndStop(start_time, true),
			b.instance.playSegments([start_time, end_time], !0), e.addClass("playing").removeClass("paused")			
		),
		/*view port*/
 
		 "viewport" !== b.play_action && "click" !== b.play_action && "hover" !== b.play_action && "autoplay" !== b.play_action && "mousescroll" !== b.play_action && (f.addEventListener("mouseenter", function() {
            b.instance.goToAndPlay(0), e.addClass("playing").removeClass("paused")
        }) 
		)
		)
 
    }
 
    var WidgetBodyMovinHandler = function($scope, g) {
        var container = $scope.find('.pt-plus-bodymovin'),
		bm_containerID= container.attr('id'),
		bm_backendload = container.data('editor-load'),
		bm_popup_load = container.data('popup-load');
        if(elementorFrontend.isEditMode() && bm_backendload=='yes'){
			var movin= container.data("settings");
			if(movin.animation_data){
				plus_bodyMovinLoad(bm_containerID, movin, movin.animation_data);
			}
		}else if(!elementorFrontend.isEditMode() && (bm_popup_load == undefined || bm_popup_load=='no')){
			if(wpbodymovin!==undefined && wpbodymovin.animations[bm_containerID]!==undefined){
				plus_bodyMovinLoad(bm_containerID, wpbodymovin.animations[bm_containerID], wpbodymovin.animations[bm_containerID].animation_data);
			}
		}
 
    };
 
 
	jQuery( document ).on( 'elementor/popup/show',(event, id, instance) => {
 
		var container = jQuery('#elementor-popup-modal-'+id).find('.pt-plus-bodymovin');
			if(container.length){
				var bm_containerID= container.attr('id'),
				bm_backendload = container.data('editor-load');
				var movin= container.data("settings");
 
				if(movin.animation_data){
					plus_bodyMovinLoad(bm_containerID, movin, movin.animation_data);
				}
			}
	} );
 
    g(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-wp-bodymovin.default', WidgetBodyMovinHandler);
    });
})(jQuery);
 
/*IsOnScreen*/!function(a){"use strict";a.fn.isOnScreen=function(b){var c=this.outerHeight(),d=this.outerWidth();if(!d||!c)return!1;var e=a(window),f={top:e.scrollTop(),left:e.scrollLeft()};f.right=f.left+e.width(),f.bottom=f.top+e.height();var g=this.offset();g.right=g.left+d,g.bottom=g.top+c;var h={top:f.bottom-g.top,left:f.right-g.left,bottom:g.bottom-f.top,right:g.right-f.left};return"function"==typeof b?b.call(this,h):h.top>0&&h.left>0&&h.right>0&&h.bottom>0}}(jQuery);