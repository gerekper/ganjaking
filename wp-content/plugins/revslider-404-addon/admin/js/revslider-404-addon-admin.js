(function( $ ) {
	//'use strict';

	/*! Main Functionality for Settings SlideOut */
			jQuery('document').ready(function() {
				var a = jQuery('#revslider_404_addon_settings_slideout');
				punchgs.TweenLite.set(a,{xPercent:"+100%", autoAlpha:0, display:"none"});

				jQuery('body').on('click', '#rs-dash-addons-slide-out-trigger_revslider-404-addon', function() {
					//hide all wrappers
					jQuery('.rs-sbs-slideout-wrapper').each(function(){
						punchgs.TweenLite.to(jQuery(this),0.4,{xPercent:"+100%", autoAlpha:0, display:"none",overwrite:"auto",ease:punchgs.Power3.easeInOut});
					});

					//display slideout
					var a = jQuery('#revslider_404_addon_settings_slideout'),
						b = jQuery('.rs-dash-addons');						
					punchgs.TweenLite.to(a,0.4,{xPercent:"0%", autoAlpha:1, display:"block",overwrite:"auto",ease:punchgs.Power3.easeOut});
				});
				jQuery('body').on('click','#revslider_404_addon_settings_slideout .rs-sbs-close', function() {
					var a = jQuery('#revslider_404_addon_settings_slideout');				
					punchgs.TweenLite.to(a,0.4,{xPercent:"+100%", autoAlpha:0, display:"none",overwrite:"auto",ease:punchgs.Power3.easeInOut});
				});

				//enable Scrollbars
				jQuery('#revslider_404_addon_settings_slideout .rs-sbs-slideout-inner').css("max-height",$( window ).height()-300);
					setTimeout(function() {
						jQuery('#revslider_404_addon_settings_slideout .rs-sbs-slideout-inner').perfectScrollbar("update");
					},400);
				$(window).resize(function(){
					jQuery('#revslider_404_addon_settings_slideout .rs-sbs-slideout-inner').css("max-height",$( window ).height()-300);
					jQuery('#revslider_404_addon_settings_slideout .rs-sbs-slideout-inner').perfectScrollbar("update");
				});

				//call scrollbars
				jQuery('#revslider_404_addon_settings_slideout .rs-sbs-slideout-inner').perfectScrollbar({wheelPropagation:true, suppressScrollX:true});

				$('input[name=revslider-404-addon-type]').click(function(){
					$(".revslider_404_type_details").hide();
					$('#revslider_404_type_'+$('input[name=revslider-404-addon-type]:checked').val()).show();
				});
				//$('#revslider_404_type_'+$('input[name=revslider-404-addon-type]:checked').val()).show();

				$check_val = $('input[name=revslider-404-addon-type]:checked').length ? $('input[name=revslider-404-addon-type]:checked').val() : 'slider';
				$(".revslider_404_type_details").hide();
				$('#revslider_404_type_'+$check_val).show();

				// Setup a click handler to initiate the Ajax request and handle the response
				$('#revslider-404-addon-save').live("click",function(evt) {
					console.log( $('#revslider-404-addon-form').serialize() );
					showWaitAMinute({fadeIn:300,text:rev_slider_addon.please_wait_a_moment});
					$.ajax({
						url : revslider_404_addon.ajax_url,
						type : 'post',
						data : {
							action : 'save_404',
							nonce: 	$('#ajax_revslider_404_addon_nonce').text(), // The security nonce
							revslider_404_form: $('#revslider-404-addon-form').serialize()
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
			});
})( jQuery );