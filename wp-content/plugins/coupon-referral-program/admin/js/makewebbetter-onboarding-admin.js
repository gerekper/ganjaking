jQuery(document).ready( function($) {

	/*if device is mobile*/
	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
	    jQuery('body').addClass( 'mobile-device' );
	}

	var deactivate_url = '';

	// Add Select2.
	jQuery( '.on-boarding-select2' ).select2({
		placeholder : 'Select All Suitable Options...',
	});

	// Add Deactivation id to all deactivation links.
	embed_id_to_deactivation_urls();

	// On click of deactivate.
	if( 'plugins.php' == mwb.current_screen ) {

		add_deactivate_slugs_callback( mwb.current_supported_slug );

		jQuery( document ).on( 'change','.on-boarding-radio-field' ,function(e){

			e.preventDefault();
			if ( 'other' == jQuery( this ).attr( 'id' ) ) {
				jQuery( '#deactivation-reason-text' ).removeClass( 'keep_hidden' );
			} else {
				jQuery( '#deactivation-reason-text' ).addClass( 'keep_hidden' );
			}
		});
	}

	else {

		// Show Popup after 1 second of entering into the MWB pagescreen.
		if ( jQuery( '#show-counter' ).length > 0 && jQuery( '#show-counter' ).val() == 'not-sent' ) {
			setTimeout( mwb_show_onboard_popup(), 1000 );
		}
	}

	/* Close Button Click */
	jQuery( document ).on( 'click','.mwb-on-boarding-close-btn a',function(e){
		e.preventDefault();
		mwb_hide_onboard_popup();
	});

	/* Skip and deactivate. */
	jQuery( document ).on( 'click','.mwb-deactivation-no_thanks',function(e){

		window.location.replace( deactivate_url );
		mwb_hide_onboard_popup();
	});

	/* Skip For a day. */
	jQuery( document ).on( 'click','.mwb-on-boarding-no_thanks',function(e){

		jQuery.ajax({
            type: 'post',
            dataType: 'json',
            url: mwb.ajaxurl,
            data: {
                nonce : mwb.auth_nonce, 
                action: 'skip_onboarding_popup' ,
            },
            success: function( msg ){
				mwb_hide_onboard_popup();
            }
        });

	});

	/* Submitting Form */
	jQuery( document ).on( 'submit','form.mwb-on-boarding-form',function(e){

		jQuery( document ).find('#mwb_crp_loader').show();
		e.preventDefault();
		var form_data = JSON.stringify( jQuery( 'form.mwb-on-boarding-form' ).serializeArray() ); 

		jQuery.ajax({
            type: 'post',
            dataType: 'json',
            url: mwb.ajaxurl,
            data: {
                nonce : mwb.auth_nonce, 
                action: 'send_onboarding_data' ,
                form_data: form_data,  
            },
            success: function( msg ){
            	jQuery( document ).find('#mwb_crp_loader').hide();
        		if( 'plugins.php' == mwb.current_screen ) {
					window.location.replace( deactivate_url );
				}
                mwb_hide_onboard_popup();
            }
        });
	});

	/* Open Popup */
	function mwb_show_onboard_popup() {
		jQuery( '.mwb-onboarding-section' ).show();
		jQuery( '.mwb-on-boarding-wrapper-background' ).addClass( 'onboard-popup-show' );

	    if( ! jQuery( 'body' ).hasClass( 'mobile-device' ) ) {
	    	jQuery( 'body' ).addClass( 'mwb-on-boarding-wrapper-control' );
	    }
	}

	/* Close Popup */
	function mwb_hide_onboard_popup() {
		jQuery( '.mwb-on-boarding-wrapper-background' ).removeClass( 'onboard-popup-show' );
		jQuery( '.mwb-onboarding-section' ).hide();
		if( ! jQuery( 'body' ).hasClass( 'mobile-device' ) ) {
	    	jQuery( 'body' ).removeClass( 'mwb-on-boarding-wrapper-control' );
	    }
	}

	/* Apply deactivate in all the MWB plugins. */
	function add_deactivate_slugs_callback( all_slugs ) {
		
		for ( var i = all_slugs.length - 1; i >= 0; i-- ) {

			jQuery( document ).on( 'click', '#deactivate-' + all_slugs[i] ,function(e){

				e.preventDefault();
				deactivate_url = jQuery( this ).attr( 'href' );
				plugin_name = jQuery( this ).attr( 'aria-label' );
				jQuery( '#plugin-name' ).val( plugin_name.replace( 'Deactivate ', '' ) );
				plugin_name = plugin_name.replace( 'Deactivate ', '' );
				jQuery( '#plugin-name' ).val( plugin_name );
				jQuery( '.mwb-on-boarding-heading' ).text( plugin_name + ' Feedback' );
				var placeholder = jQuery( '#deactivation-reason-text' ).attr( 'placeholder' );
				jQuery( '#deactivation-reason-text' ).attr( 'placeholder', placeholder.replace( '{plugin-name}', plugin_name ) );
				mwb_show_onboard_popup();
			});
		}
	}

	/* Add deactivate id in all the plugins links. */
	function embed_id_to_deactivation_urls() {
		jQuery( 'a' ).each(function(){
		    if ( 'Deactivate' == jQuery(this).text() && 0 < jQuery(this).attr( 'href' ).search( 'action=deactivate' ) ) {
		    	if( 'undefined' == typeof jQuery(this).attr( 'id' ) ) {
			    	var slug = jQuery(this).closest( 'tr' ).attr( 'data-slug' );
			    	jQuery(this).attr( 'id', 'deactivate-' + slug );
		    	}
		    }
		});	
	}
	
// End of scripts.
});