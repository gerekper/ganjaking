
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
			self.$editChildForm      = $( '#gpnf-edit-child-form' );

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
				self.setEditChildFormLink( nestedFormId );

				var selectedFields = field['gpnfFields'] ? field['gpnfFields'] : [];
				self.toggleNestedFormFields( selectedFields );

			} );

			// Initialize color picker.
			$( "#chooser_gpnf-modal-header-color, #chip_gpnf-modal-header-color" ).click( function( event ) {
				iColorShow( event.pageX - 245, event.pageY - 57, 'gpnf-modal-header-color', 'gpnfSetModalHeaderColor' );
			} );

			$().add( self.$entryLabelSingular ).add( self.$entryLabelPlural ).on( 'change', function() {
				RefreshSelectedFieldPreview();
			} );

		};

		self.toggleNestedFormFields = function( selectedFields ) {
			self.$fieldSelect
				.attr( 'disabled', true );
			if ( self.$formSelect.val() ) {
				self.$formSettings.show();
				self.getFormFields( self.$formSelect.val(), selectedFields ? selectedFields : [] );
			} else {
				self.$formSettings.hide();
			}
		};

		self.setEditChildFormLink = function( childFormId ) {
			self.$editChildForm.attr( 'href', '?page=gf_edit_forms&id=' + childFormId );
		}

		self.sortAsmSelectDropdown = function( fields ) {
			var $select = self.$fieldSelect.siblings('.asmSelect');
			var fieldIds = fields.map(function(field) {
				return field.id.toString();
			});

			$select.find('option:not([value=""])').sort(function(a, b) {
				a = fieldIds.indexOf(a.value.toString());
				b = fieldIds.indexOf(b.value.toString());

				return a - b;
			}).appendTo($select);
		}

		self.setFieldsSelect = function( fields, selectedFields ) {

			var options = [];

			if ( ! fields.length && selectedFields.length ) {
				for ( var i = 0; i < selectedFields.length; i++ ) {
					options.push( '<option value="' + selectedFields[ i ] + '" selected="selected">' + selectedFields[i] + '</option>' );
				}
			} else {
				// Sort the fields/options to match the value. Without doing this, the order of the sortable will be
				// incorrect.
				//
				// This also means we need to re-sort the "Select your fields" dropdown to match the field order from
				// the child form.
				var sortedFields = $.extend([], fields).sort(function(a, b) {
					a = selectedFields.indexOf(a.id.toString());
					b = selectedFields.indexOf(b.id.toString());

					return a - b;
				});

				for ( var i = 0; i < sortedFields.length; i++ ) {
					var field = sortedFields[i];
					// Interestingly, setting the .val() on the select does not work so we need to fallback to setting
					// the selected attribute on the options.
					var selected = $.inArray( String( field.id ), selectedFields ) != -1 ? 'selected="selected"' : '';

					if ( $.inArray( field.type, [ 'page', 'html', 'section', 'captcha' ] ) === -1 ) {
						options.push( '<option value="' + field.id + '"' + selected + '>' +  GetLabel( field ) + '</option>' );
					}
				}
			}

			self.$fieldSelect
			self.$fieldSelect
				.html( options.join( '' ) )
				.val( selectedFields )
				.change();

			if ( self.$fieldSelect.data( 'asmApplied' ) ) {
				self.sortAsmSelectDropdown( fields );
				return;
			}

			var updateFields = function(val) {
				SetFieldProperty( 'gpnfFields', val );
				RefreshSelectedFieldPreview();
			};

			self.$fieldSelect.asmSelect({
				addItemTarget: 'bottom',
				highlight: true,
				sortable: true
			}).data( 'asmApplied', true );

			var $sortable = self.$fieldSelect.siblings('.asmListSortable');

			// Change axis to undefined to allow dragging on x and y axes
			$sortable.sortable('option', 'axis', '');

			// Do not allow scrolling as there is no way to only disable X-axis scrolling
			$sortable.sortable('option', 'scroll', false);

			// Force getting offset parent position and caching it. Without this, if the field settings are scrolled
			// and dragging starts, the ghost will be positioned incorrectly until another item is intersected.
			$sortable.on('sortstart', function() {
				var sortable = $(this).sortable('instance');
				sortable.offset.parent = sortable._getParentOffset();
			});

			// Bind directly to sortupdate on the sortable as ASM select doesn't seem to be passing the value on
			// correctly.
			$sortable.on('sortupdate', function () {
				// Use setTimeout to give ASM select time to update the field value (but ironically not trigger a change)
				setTimeout(function() {
					updateFields(self.$fieldSelect.val());
				}, 5);
			})

			self.$fieldSelect.change(function () {
				updateFields($(this).val());
			});

			self.sortAsmSelectDropdown( fields );
		};

		self.getFormFields = function( formId, selectedFields ) {
			$.post( ajaxurl, {
				action: 'gpnf_get_form_fields',
				nonce: GPNFAdminData.nonces.getFormFields,
				form_id: formId
			}, function( fields ) {
				self.$formSettings.find( 'select' ).attr( 'disabled', false );
				if( typeof fields === 'object' ) {
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
