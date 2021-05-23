
( function( $ ) {

	window.GPLCAdmin = {

		addRuleFieldAfter: function( ruleField, targetLabel, ruleFields ) {

			var targetIndex;

			$.each( ruleFields, function( i, _ruleField ) {
				if ( targetLabel == _ruleField.label ) {
					targetIndex = i;
					return false;
				}
			} );

			ruleFields.splice( targetIndex + 1, 0, ruleField );

			return ruleFields;
		},

		addLimitChoiceInputs: function () {

			jQuery( 'ul#field_choices li' ).each(function (i) {

				var limitValue = typeof field.choices[i]['limit'] != 'undefined' ? field.choices[i]['limit'] : '';

				// skip this row if already has a limit input
				if (jQuery( this ).find( 'input.field-choice-limit' ).length > 0) {
					return;
				}

				// add limit input
				jQuery( this ).find( 'input.field-choice-input:last' ).after( '<input type="text" class="field-choice-input field-choice-limit gform-input" value="' + limitValue + '" onkeyup="GPLCAdmin.setChoiceLimit(' + i + ', this.value)" />' );

				// replace onclick options
				jQuery( this ).find( 'img.add_field_choice' ).attr( 'onclick', jQuery( this ).find( 'img.add_field_choice' ).attr( 'onclick' ) + ' GPLCAdmin.addLimitChoiceInputs();' );
				jQuery( this ).find( 'img.delete_field_choice' ).attr( 'onclick', jQuery( this ).find( 'img.delete_field_choice' ).attr( 'onclick' ) + ' GPLCAdmin.addLimitChoiceInputs();' );

			});

		},

		setChoiceLimit: function (index, value) {
			field.choices[index]['limit'] = value;
		},

		addEnableLimitsCheckbox: function () {

			// add checkbox if it has not been added
			if (jQuery( '#field_choice_limits_enabled' ).length < 1) {
				jQuery( 'li.gw-limit-choice.field_setting' ).children( 'div:first-child' ).after( GPLCAdminData.enableCheckbox );
			}

			// check or uncheck
			jQuery( '#field_choice_limits_enabled' ).prop( 'checked', field['gwlimitchoices_enableLimits'] == true );

			GPLCAdmin.toggleEnableLimits();

		},

		removeEnableLimitsCheckbox: function () {
			jQuery( '#field_choice_limits_enabled' ).parent( 'div' ).remove();
		},

		toggleEnableLimits: function () {
			var isChecked = jQuery( '#field_choice_limits_enabled' ).prop( 'checked' );
			if (isChecked) {
				jQuery( 'li.gw-limit-choice.field_setting' ).addClass( 'limits-enabled' );
			} else {
				jQuery( 'li.gw-limit-choice.field_setting' ).removeClass( 'limits-enabled' );
			}
		}

	}

	gform.addFilter( 'gform_conditional_logic_fields', function( ruleFields, form, selectedFieldId ) {

		jQuery.each( form.fields, function( i, field ) {

			var isCondLogicSupportedFieldType = $.inArray( GetInputType( field ), [ 'checkbox', 'multiselect' ] ) == -1;

			if ( field['gwlimitchoices_enableLimits'] && isCondLogicSupportedFieldType ) {
				GPLCAdmin.addRuleFieldAfter( {
					label: '(Remaining) ' + field.label,
					value: 'gplc_count_remaining_' + field.id
				}, field.label, ruleFields );
			}

		} );

		return ruleFields;
	} );

	/**
	 * Handle field settings load
	 */
	$( document ).bind('gform_load_field_settings', function(event, field) {

		if ($.inArray( field.type, GPLCAdminData.allowedFieldTypes ) == -1 && $.inArray( field.inputType, GPLCAdminData.allowedFieldTypes ) == -1) {

			$( 'li.choices_setting' ).removeClass( 'gw-limit-choice' );
			GPLCAdmin.removeEnableLimitsCheckbox();
			return;

		} else {

			// add limit class to choice setting
			$( 'li.choices_setting' ).addClass( 'gw-limit-choice' );

			// add limit header if does not exists
			if ( ! $( '.gfield_choice_header_limit' ).length) {
				$( '.gfield_choice_header_price' ).after( '<label class="gfield_choice_header_limit">Limit</label>' );
			}

			// init enable limits checkbox
			GPLCAdmin.addEnableLimitsCheckbox( field );

			// init choice inputs
			GPLCAdmin.addLimitChoiceInputs();

			// only bind once for sorting action
			if ( ! GPLCAdmin.limitChoicesSetup) {

				$( '#field_choices' ).bind('sortupdate', function () {
					// was firing before GF's update function
					setTimeout( 'GPLCAdmin.addLimitChoiceInputs()', 1 );
				});

				$( document ).on('gform_load_field_choices', function () {

					GPLCAdmin.addLimitChoiceInputs();

				});

			}

			GPLCAdmin.limitChoicesSetup = true;

		}

		GPLCAdmin.limitChoicesSetup = false;

	} );

} )( jQuery );
