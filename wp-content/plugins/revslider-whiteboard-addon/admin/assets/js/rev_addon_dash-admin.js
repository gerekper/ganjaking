(function( $ ) {
		
		$(function() {

			/*! Main Functionality for Settings SlideOut */
			jQuery('document').ready(function() {
				var a = jQuery('#rev_addon_gal_settings_slideout');
				punchgs.TweenLite.set(a,{xPercent:"+100%", autoAlpha:0, display:"none"});

				jQuery('body').on('click', '#rs-dash-addons-slide-out-trigger_revslider-whiteboard-addon', function() {
					//hide all wrappers
					jQuery('.rs-sbs-slideout-wrapper').each(function(){
						punchgs.TweenLite.to(jQuery(this),0.4,{xPercent:"+100%", autoAlpha:0, display:"none",overwrite:"auto",ease:punchgs.Power3.easeInOut});
					});

					//display slideout
					var a = jQuery('#rev_addon_whiteboard_settings_slideout'),
						b = jQuery('.rs-dash-addons');						
					punchgs.TweenLite.to(a,0.4,{xPercent:"0%", autoAlpha:1, display:"block",overwrite:"auto",ease:punchgs.Power3.easeOut});

					//enable Scrollbars
					jQuery('#rev_addon_whiteboard_settings_slideout .rs-sbs-slideout-inner').css("max-height",$( window ).height()-300);
					setTimeout(function() {
						jQuery('#rev_addon_whiteboard_settings_slideout .rs-sbs-slideout-inner').perfectScrollbar("update");
					},400);
				});
				jQuery('body').on('click','#rev_addon_whiteboard_settings_slideout .rs-sbs-close', function() {
					var a = jQuery('#rev_addon_whiteboard_settings_slideout');				
					punchgs.TweenLite.to(a,0.4,{xPercent:"+100%", autoAlpha:0, display:"none",overwrite:"auto",ease:punchgs.Power3.easeInOut});
				});

				//call scrollbars
				jQuery('#rev_addon_whiteboard_settings_slideout .rs-sbs-slideout-inner').perfectScrollbar({wheelPropagation:true, suppressScrollX:true});

				$(window).resize(function(){
					jQuery('#rev_addon_whiteboard_settings_slideout .rs-sbs-slideout-inner').css("max-height",$( window ).height()-300);
					jQuery('#rev_addon_whiteboard_settings_slideout .rs-sbs-slideout-inner').perfectScrollbar("update");
				});
				
			}); //end document ready

		});

})( jQuery );
