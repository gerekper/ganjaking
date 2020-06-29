(function( $ ) {
	'use strict';

	$(document).ready(function(){
		var a = jQuery('#rev_addon_rel_posts_settings_slideout');
		punchgs.TweenLite.set(a,{xPercent:"+100%", autoAlpha:0, display:"none"});

		jQuery('#rs-dash-addons-slide-out-trigger_revslider-rel-posts-addon').live('click', function() {
			
			//hide all wrappers
			jQuery('.rs-sbs-slideout-wrapper').each(function(){
				punchgs.TweenLite.to(jQuery(this),0.4,{xPercent:"+100%", autoAlpha:0, display:"none",overwrite:"auto",ease:punchgs.Power3.easeInOut});
			});
			
			//display slideout
			var a = jQuery('#rev_addon_rel_posts_settings_slideout'),					
			b= jQuery('.rs-dash-widget.revslider-rel-posts-addon');					
			punchgs.TweenLite.to(a,0.4,{xPercent:"0%", autoAlpha:1, display:"block",overwrite:"auto",ease:punchgs.Power3.easeOut});
			
			//enable Scrollbars
			jQuery('#rev_addon_rel_posts_settings_slideout .rs-sbs-slideout-inner').css("max-height",$( window ).height()-300);
			setTimeout(function() {
				jQuery('#rev_addon_rel_posts_settings_slideout .rs-sbs-slideout-inner').perfectScrollbar("update");
			},400);
		});

		//close current slideout
		jQuery('body').on('click','#rev_addon_rel_posts_settings_slideout .rs-sbs-close', function() {		
			var a = jQuery('#rev_addon_rel_posts_settings_slideout');				
			punchgs.TweenLite.to(a,0.4,{xPercent:"+100%", autoAlpha:0, display:"none",overwrite:"auto",ease:punchgs.Power3.easeInOut});
		});
	
		//call scrollbars
		$('#rev_addon_rel_posts_settings_slideout .rs-sbs-slideout-inner').perfectScrollbar({wheelPropagation:true, suppressScrollX:true});

		check_for_posttype($('select.rs-addon-rel-slider-switch:first'));
		
		// Setup a click handler to initiate the Ajax request and handle the response
		$('#rs-addon-rel_posts-save').live("click",function(evt) {
			showWaitAMinute({fadeIn:300,text:rev_slider_addon.please_wait_a_moment});
			$.ajax({
				url : rev_slider_addon_rel_posts.ajax_url,
				type : 'post',
				data : {
					action : 'save_rel_posts',
					nonce: 	$('#ajax_rev_slider_addon_rel_posts_nonce').text(), // The security nonce
					rel_post_form: $('#rs-addon-rel_post-form').serialize()
				},
				success : function( response ) {
					switch(response){
						case "0":
								UniteAdminRev.showInfo({type: 'warning', hideon: '', event: '', content: 'Ajax Error', hidedelay: 3});
								break;
						case "1":
								UniteAdminRev.showInfo({type: 'success', hideon: '', event: '', content: rev_slider_addon.settings_saved, hidedelay: 3});
								break;
						case "-1":
								UniteAdminRev.showInfo({type: 'warning', hideon: '', event: '', content: 'Nonce missing', hidedelay: 3});
								break;
					}
					showWaitAMinute({fadeOut:300,text:rev_slider_addon.please_wait_a_moment});
				},
				error : function ( response ){
					UniteAdminRev.showInfo({type: 'warning', hideon: '', event: '', content: 'Ajax Error', hidedelay: 3});
				}
			}); // End Ajax
			
		}); // End Click

		// Resize Window SlideOut modifications
		$(window).resize(function(){
			jQuery('#rev_addon_rel_posts_settings_slideout .rs-sbs-slideout-inner').css("max-height",$( window ).height()-300);
			jQuery('#rev_addon_rel_posts_settings_slideout .rs-sbs-slideout-inner').perfectScrollbar("update");
		});

		// Tabs for Content Source
		$('body').on('click','.rs-submenu-tabs li', function() {
			$('.rs-submenu-tabs li').removeClass("selected");
			$(this).addClass("selected");
			$('.subcat-wrapper').hide();
			$($(this).data('content')).show();
			// Show Hide Details per Type
			check_for_posttype( $($(this).data('content')).find('select.rs-addon-rel-slider-switch') );
		});

		// Show Hide Details per Type
		$('select.rs-addon-rel-slider-switch').change(function(){
			check_for_posttype(jQuery(this));
		});

		function check_for_posttype($this){
			if( $this.val()!="" ){
				 $('#rs-addon-rel-'+$this.data("type")+'-details').show();
				 //call scrollbars
				 $this.closest('.rs-sbs-slideout-inner').find('.ps-scrollbar-y-rail').css('visibility','visible');
				jQuery('#rev_addon_rel_posts_settings_slideout .rs-sbs-slideout-inner').css("max-height",$( window ).height()-300);
				jQuery('#rev_addon_rel_posts_settings_slideout .rs-sbs-slideout-inner').perfectScrollbar("update");
			}
			else {
				$('#rs-addon-rel-'+$this.data("type")+'-details').hide();
				$this.closest('.rs-sbs-slideout-inner').find('.ps-scrollbar-y-rail').css('visibility','hidden');
			}
		}

		

	}); // End document ready

})( jQuery );