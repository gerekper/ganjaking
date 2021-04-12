
( function( GPNFAdmin, $ ) {

	GPNFAdmin = function() {

		var self = this;

		self.init = function() {

			self.$formSelect         = $( '#gpnf-form' );
			self.$fieldSelect        = $( '#gpnf-fields' );
			self.$formSettings       = $( '#gpnf-form-settings' );
			self.$entryLabelSingular = $( '#gpnf-entry-label-singular' );
			self.$entryLabelPlural   = $( '#gpnf-entry-label-plural' );
			self.$entryLimitMin      = $( '#gpnf-entry-limit-min' );
			self.$entryLimitMax      = $( '#gpnf-entry-limit-max' );
			self.$feedProcessing     = $( '#gpnf-feed-processing' );
			self.$modalHeaderColor   = $( '#gpnf-modal-header-color' );

			$( document ).bind( 'gform_load_field_settings', function( event, field, form ) {

				if( field.type != 'form' ) {
					return;
				}

				var nestedFormId = field['gpnfForm'];

				self.$entryLabelSingular.val( field['gpnfEntryLabelSingular'] );
				self.$entryLabelPlural.val( field['gpnfEntryLabelPlural'] );
				self.$entryLimitMin.val( field['gpnfEntryLimitMin'] );
				self.$entryLimitMax.val( field['gpnfEntryLimitMax'] );
				self.$feedProcessing.val( self.getFeedProcessingSetting( field ) );

				self.$modalHeaderColor.val( field['gpnfModalHeaderColor'] );
				$( '#chip_gpnf-modal-header-color' ).css( 'background-color', field['gpnfModalHeaderColor'] );

				// set the 'form' field even if there is no value (resets 'form' for fields with no form selected)
				self.$formSelect.val( nestedFormId );

				// hide form settings regardless of whether we have a 'form' or not, the getFormFields will reload it once fields have been retrieved
				self.$formSettings.hide();

				if( nestedFormId ) {
					// if we have 'form', do an ajax call to get the fields and load them
					self.getFormFields( nestedFormId, field['gpnfFields'] );
				}

			} );

			// Initialize color picker.
			$( "#chooser_gpnf-modal-header-color, #chip_gpnf-modal-header-color" ).click( function( event ) {
				iColorShow( event.pageX, event.pageY, 'gpnf-modal-header-color', 'gpnfSetModalHeaderColor' );
			} );

			$().add( self.$entryLabelSingular ).add( self.$entryLabelPlural ).on( 'change', function() {
				RefreshSelectedFieldPreview();
			} );

		};

		self.toggleNestedFormFields = function() {
			if( self.$formSelect.val() ) {
				self.getFormFields( this.$formSelect.val() );
			} else {
				self.$formSettings.slideUp();
			}
		};

		self.setFieldsSelect = function( fields, selectedFields ) {

			var options = [];

			for( var i = 0; i < fields.length; i++ ) {
				if( $.inArray( fields[ i ].type, [ 'page', 'html', 'section', 'captcha' ] ) === -1 ) {
					options.push( '<option value="' + fields[ i ].id + '">' + GetLabel( fields[ i ] ) + '</option>' );
				}
			}

			self.$fieldSelect.html( options.join( '' ) );
			self.$fieldSelect.val( selectedFields ).select2( {
				placeholder: GPNFAdminData.strings.displayFieldsPlaceholder
			} ).on( 'select2:select select2:unselect', function() {
				RefreshSelectedFieldPreview();
			} ).on('select2:unselecting', function() {
				// Prevent Select2 from opening menu when an option is unselected.
				var opts = $(this).data('select2').options;
				opts.set('disabled', true);
				setTimeout(function() {
					opts.set('disabled', false);
				}, 1);
			} );

			// Add our custom class to identify (and style) our Select2's. You'd think there would be an easier way...
			$.each( self.$fieldSelect.data( 'select2' ), function ( index, $elem ) {
				if( $elem instanceof jQuery ) {
					$elem.addClass( 'gpnf-select2' );
				}
			} );

			self.$formSettings.slideDown();

		};

		self.getFormFields = function( formId, selectedFields ) {

			var $spinner = gfAjaxSpinner( self.$formSelect, null, 'position:relative;top:6px;left:6px;' );

			$.post( ajaxurl, {
				action: 'gpnf_get_form_fields',
				nonce: GPNFAdminData.nonces.getFormFields,
				form_id: formId
			}, function( response ) {
				$spinner.destroy();
				if( typeof response === 'object' ) {
					var fields = response;
					self.setFieldsSelect( fields, selectedFields );
				} else {
					alert( GPNFAdminData.strings.getFormFieldsError );
				}
			} );
		};

		self.getFeedProcessingSetting = function( field ) {
			return field.gpnfFeedProcessing ? field.gpnfFeedProcessing : 'parent';
		};

		self.init();

	};

	window.gpnfSetModalHeaderColor = function( color ) {
		SetFieldProperty( 'gpnfModalHeaderColor', color );
	};

	$( document ).ready( function() {
		if( ! window.gpGlobals ) {
			window.gpGlobals = {};
		}
		window.gpGlobals.GPNFAdmin = new GPNFAdmin();
	} );

} )( window.GPNFAdmin = window.GPNFAdmin || {}, jQuery );