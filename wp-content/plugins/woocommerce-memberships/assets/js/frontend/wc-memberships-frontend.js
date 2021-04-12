jQuery( function ( $ ) {

	var wc_memberships_frontend = window.wc_memberships_frontend !== null ? window.wc_memberships_frontend : {};


	let variationsProfileFields = {},
	    profileFieldsFileInputs = [];


	/**
	 * Initializes profile fields inputs.
	 *
	 * @since 1.21.4
	 */
	function initProfileFields() {

		// initialize any profile field enhanced dropdown form field
		if ( $.fn.select2 ) {
			$( '.wc-memberships-member-profile-field select' ).not( '.select2-hidden-accessible' ).select2().addClass( 'select2-initialized' );
		}

		// the profile fields form fields
		var $profileFields  = $( '.wc-memberships-member-profile-field' ),
		    $checkoutFields = $( '.form-row' );

		// when other fields than Memberships' are in focus, disable description of profile fields
		$checkoutFields.not( $profileFields ).find( '.woocommerce-input-wrapper :input' ).on( 'keydown click focus', function () {
			$profileFields.find( '.description' ).prop( 'aria-hidden', true ).slideUp( 250 );
		} );

		// handle profile field form fields description toggling
		$profileFields.each( function () {

			var $field       = $( this ),
			    $description = $field.find( '.description' );

			// detaches frontend/woocommerce.js listeners to prevent some weirdness
			$field.find( '.woocommerce-input-wrapper input[type="checkbox"], .woocommerce-input-wrapper input[type="radio"]' ).off();

			$description.hide();

			if ( $field.hasClass( 'wc-memberships-member-profile-field wc-memberships-member-profile-field-input-file' ) ) {

				$field.find( 'label, .wc-memberships-profile-field-input-file-dropzone' ).on( 'click', function () {
					toggleFieldDescription( $field, $checkoutFields );
				} );

			} else if ( $field.hasClass( 'wc-memberships-member-profile-field-input-multiselect' ) ) {

				$( this ).find( 'label, .select2-selection' ).on( 'click change', function () {
					toggleFieldDescription( $field, $checkoutFields );
				} )

			} else {

				if ( $field.hasClass( 'wc-memberships-member-profile-field-input-select' ) ) {

					$field.find( '.select2-selection' ).on( 'click change', function () {
						toggleFieldDescription( $field, $checkoutFields );
					} )
				}

				$field.find( 'input, textarea, select, label' ).not( '.select2-selection' ).on( 'keydown click', function () {

					// prevents a double event on text input, textarea when clicking on their labels
					if ( $( this ).is( 'label' ) ) {

						var id     = $( this ).attr( 'for' ),
						    $input = $( '#' + id );

						if ( $input.is( ':text' ) || $input.is( 'textarea' ) || $input.is( 'select' ) ) {
							return;
						}
					}

					toggleFieldDescription( $field, $checkoutFields );
				} );
			}
		} );


		// handle profile field form fields file uploads
		$( '.wc-memberships-member-profile-field-input-file' ).each( function () {

			var $inputField = $( this ),
			    $dropzone   = $inputField.find( '.wc-memberships-profile-field-input-file-dropzone' ),
			    $preview    = $inputField.find( '.wc-memberships-profile-field-input-file-preview' ),
			    $progress   = $inputField.find( '.wc-memberships-profile-field-input-file-upload-progress' ),
			    $feedback   = $inputField.find( '.wc-memberships-profile-field-input-file-feedback' ),
			    $input      = $inputField.find( 'input[type="hidden"]' ),
			    slug        = $input.data( 'slug' ),
			    options     = {
			    	url             : wc_memberships_frontend.ajax_url,
			    	browse_button   : $dropzone[ 0 ],
			    	drop_element    : $dropzone[ 0 ],
			    	multi_selection : wc_memberships_frontend.max_files > 1,
			    	multipart_params: {
			    		action       : 'wc_memberships_upload_profile_field_file',
			    		security     : wc_memberships_frontend.nonces.profile_field_upload_file,
			    		profile_field: slug
			    	},
			    	filters         : {
			    		max_file_size: wc_memberships_frontend.max_file_size,
			    		mime_types   : wc_memberships_frontend.mime_types
			    	}
			    };

			var uploader = new plupload.Uploader( options );

			// initializes the uploader for the field
			uploader.init();

			// append the new uploader instance for later clean up
			profileFieldsFileInputs.push( uploader );

			// handle upload errors
			uploader.bind( 'Error', function ( up, err ) {

				var error        = wc_memberships_frontend.i18n.upload_error,
				    errorCode    = error.replace( '%1$s', err.code ),
				    errorMessage = errorCode.replace( '%2$s', err.message );

				$feedback.html( errorMessage ).removeClass( 'hide' );

				return $progress.addClass( 'hide' );
			} );

			// display a preview and start uploading files immediately as they are added
			uploader.bind( 'FilesAdded', function ( up, files ) {

				$feedback.html( '' ).addClass( 'hide' );
				$dropzone.addClass( 'disabled' );
				$progress.removeClass( 'hide' );

				return uploader.start();
			} );

			// display upload progress
			uploader.bind( 'UploadProgress', function ( up, file ) {

				return $progress.find( '.bar' ).css( 'width', file.percent + '%' );
			} );

			// update preview once file has been uploaded
			uploader.bind( 'FileUploaded', function ( up, file, res ) {

				var data = JSON.parse( res.response );

				// check for upload errors
				if ( ! data || data.errors ) {

					$progress.addClass( 'hide' );
					$dropzone.removeClass( 'disabled' );

					if ( ! data || ! data.length ) {
						console.log( res.response );
						return;
					}

					$feedback.html( data.errors.upload_error ).removeClass( 'hide' );

					return;
				}

				// update the nonce that is generated once the session is opened by a file upload
				$( '#woocommerce-register-nonce' ).val( data.security );

				// handle successful upload
				$input.val( data.id ).trigger( 'change' );
				$preview.removeClass( 'hide' ).find( 'a.file' ).prop( 'href', data.url ).text( data.title );

				// hide dropzone and reset progress bar
				$dropzone.addClass( 'hide' ).removeClass( 'disabled' );
				$progress.addClass( 'hide' ).find( '.bar' ).css( 'width', 0 );

				// hide uploader from user
				return $inputField.find( '.moxie-shim' ).addClass( 'hide' );
			} );

			// handle uploaded file removal
			$inputField.find( 'a.remove-file' ).on( 'click', function ( e ) {
				e.preventDefault();

				// hide file removal HTML
				$preview.addClass( 'hide' ).find( 'a.file' ).prop( 'href', '' ).text( '' );
				$dropzone.removeClass( 'hide disabled' );

				var data = {
					action       : 'wc_memberships_remove_profile_field_file',
					security     : wc_memberships_frontend.nonces.profile_field_remove_file,
					file         : $input.val(),
					profile_field: slug
				}

				// drops the file from WordPress and WooCommerce session
				$.post( wc_memberships_frontend.ajax_url, data, function ( response ) {

					if ( ! response || ! response.success ) {
						console.log( response );
					}

				} ).always( function () {

					// delete file value from HTML and trigger change
					$input.val( '' ).trigger( 'change' );
				} )

				// show uploader back to user
				return $inputField.find( '.moxie-shim' ).removeClass( 'hide' );
			} );

		} );
	}


	/**
	 * Toggles the description of profile fields when they gain focus.
	 *
	 * @since 1.19.0
	 *
	 * @param {Object} $field profile field form field
	 * @param {Object} $checkoutFields checkout fields
	 */
	function toggleFieldDescription( $field, $checkoutFields ) {

		var $description = $field.find( 'span.description' );

		if ( ! $description.is( ':visible' ) ) {
			$description.prop( 'aria-hidden', false ).slideDown( 250 );
		}

		$checkoutFields.not( $field ).find( '.description' ).prop( 'aria-hidden', true ).slideUp( 250 );
	}


	/**
	 * Adds a wrapper to a variable product's profile fields form data.
	 *
	 * Used to handle profile fields specific to a variation.
	 *
	 * @since 1.21.4
	 *
	 * @param {Object} $form jQuery object
	 * @param {String} data form data
	 */
	function addProfileFieldsWrapper( $form, data ) {

		$form.find( '.woocommerce-variation-add-to-cart' ).prepend( data );

		initProfileFields();
	}


	/**
	 * Remove Profile Fields HTML previously added.
	 *
	 * Used to handle profile fields specific to a variation.
	 *
	 * @since 1.21.4
	 *
	 * @param {Object} $form
	 */
	function removeProfileFields( $form ) {

		let $fieldsWrapper = $form.find( '.wc-memberships-profile-fields-wrapper' );

		if ( ! $fieldsWrapper.length ) {
			return;
		}

		// clean up file inputs
		if ( profileFieldsFileInputs.length ) {

			// destroy any previous uploader instance
			profileFieldsFileInputs.forEach( function ( uploader ) {
				uploader.destroy();
				uploader = null;
			} );
		}

		// clean up select2 inputs
		let $selectFields = $fieldsWrapper.find( '.wc-memberships-member-profile-field select.select2-initialized' );
		if ( $selectFields.length ) {
			$selectFields.select2( 'destroy' );
		}

		// remove any remaining HTML
		$fieldsWrapper.remove();
	}


	// updates the product profile fields form according to the selected variation that may grant access to a plan where profile fields are linked to
	$( 'form.variations_form' ).on( 'show_variation wcm_show_variation', function ( e ) {

		// get the selected variation product ID
		let variation_id = $( 'input[name="variation_id"]' ).val(),
		    $form        = $( e.currentTarget );

		removeProfileFields( $form );

		if ( ! variation_id ) {
			return true;
		}

		if ( variation_id in variationsProfileFields ) {

			// prepend fields html to add-to-card wrapper
			addProfileFieldsWrapper( $form, variationsProfileFields[ variation_id ] );

		} else {

			// block form till AJAX request finished
			$form.block( { message: null } );

			$.post( wc_memberships_frontend.ajax_url, {
				action    : 'wc_memberships_get_variation_profile_fields',
				security  : wc_memberships_frontend.nonces.get_product_profile_fields,
				product_id: variation_id
			}, function ( response ) {

				if ( response.success && response.data.length ) {

					// prepend fields html to add-to-card wrapper
					addProfileFieldsWrapper( $form, response.data );

					// cache fields to avoid reloading via AJAX
					variationsProfileFields[ variation_id ] = response.data;
				}

			} ).always( function () {

				$form.unblock();
			} );
		}

	} ).on( 'woocommerce_variation_has_changed', function ( e ) {

		let variation_id = $( 'input[name="variation_id"]' ).val();

		if ( ! variation_id ) {
			removeProfileFields( $( e.currentTarget ) );
		}

	} );

	initProfileFields();

} );
