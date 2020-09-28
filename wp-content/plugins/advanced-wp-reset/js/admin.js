jQuery(document).ready(function(){

	jQuery('#DBR_reset_button').on('click', function(e){

		// Prevent doaction button from its default behaviour
		e.preventDefault();

		var confiramation_msg = jQuery('#DBR_reset_comfirmation').val();

		if(confiramation_msg != "reset"){

			// If confirmation != reset, show msg box
			Swal.fire({
			  icon					: 'error',
			  confirmButtonColor	: '#0085ba',
			  showCloseButton		: true,
			  html					: DBR_ajax_obj.type_reset
			})

		}else{

			Swal.fire({
				title				: '<font size="4px" color="red">' + DBR_ajax_obj.are_you_sure + '</font>',
				text				: DBR_ajax_obj.warning_msg,
				imageUrl			: DBR_ajax_obj.images_path + 'alert_delete.svg',
				imageWidth			: 60,
				imageHeight			: 60,
				showCancelButton	: true,
				showCloseButton		: true,
				cancelButtonText	: DBR_ajax_obj.cancel,
				cancelButtonColor	: '#555',
				confirmButtonText	: DBR_ajax_obj.Continue,
				confirmButtonColor	: '#0085ba',
				focusCancel 		: true,

			}).then((result) => {

				// If the user clicked on "confirm", call reset function
				if(result.value){

					// Show processing icon
					Swal.fire({
					  imageUrl				: DBR_ajax_obj.images_path + 'loading20px.svg',
					  imageWidth			: 60,
					  imageHeight			: 60,					  
					  showCloseButton		: false,
					  showConfirmButton		: false,
					  allowOutsideClick		: false,
					  text					: DBR_ajax_obj.processing
					})

					jQuery.ajax({
						type 	: "post",
						url		: DBR_ajax_obj.ajaxurl,
						cache	: false,
						data: {
							'action'	: 'DBR_wp_reset',
							'security'	: DBR_ajax_obj.ajax_nonce
						},
						success: function(result) {
							Swal.fire(DBR_ajax_obj.done, '', 'success')
						},
						complete: function(){
							// wait for 1 sec then reload the page.
							setTimeout(function(){location.reload();}, 1000);
						}
					});
				}
			})
		}
	});
});
