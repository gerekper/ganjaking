jQuery(
	function ($) {

		var billingCountry       = $( '#billing_country' );
		billingCompany           = $( '#billing_company' ),
		billingType              = $( "input[name='billing_receiver_type']" ),
		billingReceiverID        = $( '#billing_receiver_id' ),
		billingReceiverPec       = $( '#billing_receiver_pec' ),
		billingReceiverVatNumber = $( '#billing_vat_number' ),
		billingReceiverVatSSN    = $( '#billing_vat_ssn' ),
		billingInvoiceType       = $( '#billing_invoice_type' );

		validateFields();

		billingType.on(
			'change',
			function () {
				validateFields();
			}
		);

		// billingCompany.on('input', function () {
		// validateFields();
		// });
		billingReceiverID.on(
			'input',
			function () {
				validateFields();
			}
		);
		billingReceiverPec.on(
			'input',
			function () {
				validateFields();
			}
		);
		billingReceiverVatNumber.on(
			'input',
			function () {
				validateFields();
			}
		);

		billingInvoiceType.on(
			'change',
			function(){
				validateFields();
			}
		);

		if ( ywpi_checkout.is_ssn_mandatory != 'yes' ) {
			billingReceiverVatSSN.on(
				'input',
				function () {
					validateFields();
				}
			);
		}

		$( '#billing_vat_ssn' ).on(
			'focusout',
			function (e) {
				validate_ssn_field( 'ssn',jQuery( this ).val() );
			}
		);

		billingCountry.on(
			'change',
			function () {
				validateFields();
			}
		);
	}
);


var validateFields = function(){

	var billingCountry           = jQuery( '#billing_country' ),
		billingCompany           = jQuery( '#billing_company' ),
		billingType              = jQuery( '#billing_receiver_type' ),
		billingReceiverID        = jQuery( '#billing_receiver_id' ),
		billingReceiverPec       = jQuery( '#billing_receiver_pec' ),
		billingReceiverVatNumber = jQuery( '#billing_vat_number' ),
		billingReceiverVatSSN    = jQuery( '#billing_vat_ssn' ),
		billingTypeValue         = jQuery( "input[name='billing_receiver_type']:checked" ).val(),
		billingInvoiceType       = jQuery( '#billing_invoice_type' );

	if ( billingTypeValue == 'company' ) {
		jQuery( '#billing_invoice_type_field' ).hide();
	} else {
		jQuery( '#billing_invoice_type_field' ).show();
	}

	if ( billingTypeValue !== 'undefined' && jQuery.inArray( billingTypeValue, ["company","freelance"] ) !== -1  ) {

		billingCompany.closest( '.form-row' ).show();

		if ( billingTypeValue == 'company' ) {
			jQuery( '#billing_invoice_type_field' ).hide();
			jQuery( '#billing_invoice_type_field option[value="receipt"]' ).removeAttr( 'selected' );
			jQuery( '#billing_invoice_type_field option[value="invoice"]' ).attr( "selected", "selected" );
			setFieldAsRequired( billingCompany );
		} else {
			jQuery( '#billing_invoice_type_field' ).show();
			setFieldAsNotRequired( billingCompany );
		}

		if ( billingCountry.val() == 'IT' ) {

			billingReceiverID.closest( '.form-row' ).show();
			billingReceiverPec.closest( '.form-row' ).show();

			if ( billingReceiverID.val() != '' && billingReceiverPec.val() == '' ) {
				setFieldAsNotRequired( billingReceiverPec );
				billingReceiverID.closest( '.form-row' ).find( '.optional' ).remove();
			} else if ( billingReceiverID.val() == '' && billingReceiverPec.val() != '' ) {
				setFieldAsNotRequired( billingReceiverID );
				billingReceiverPec.closest( '.form-row' ).find( '.optional' ).remove();

			} else if ( billingReceiverID.val() == '' && billingReceiverPec.val() == '' ) {
				setFieldAsRequired( billingReceiverID );
				setFieldAsRequired( billingReceiverPec );
			}

			if ( ywpi_checkout.is_vat_mandatory != 'yes' ) {
				setFieldAsRequired( billingReceiverVatNumber );
			}

			if ( ywpi_checkout.is_ssn_mandatory != 'yes' ) {
				setFieldAsNotRequired( billingReceiverVatSSN );
			}

		} else {
			if ( ywpi_checkout.is_vat_mandatory != 'yes' ) {
				setFieldAsNotRequired( billingReceiverVatNumber );
			}
			if ( ywpi_checkout.is_ssn_mandatory != 'yes' ) {
				setFieldAsNotRequired( billingReceiverVatSSN );
			}

			setFieldAsNotRequired( billingReceiverID, 'no' );
			setFieldAsNotRequired( billingReceiverPec, 'no' );
		}

	} else {

		setFieldAsNotRequired( billingReceiverID, 'no' );
		setFieldAsNotRequired( billingReceiverPec, 'no' );

		if ( ywpi_checkout.is_vat_mandatory != 'yes' ) {
			setFieldAsNotRequired( billingReceiverVatNumber, 'no' );
		}

		if ( ywpi_checkout.is_ssn_mandatory != 'yes' ) {
			var billingCountryVal = billingCountry.val();

			if ( ( billingCountryVal === 'IT' && typeof billingInvoiceType.val() === 'undefined' ) || ( billingCountryVal === 'IT' && typeof billingInvoiceType.val() !== 'undefined' && billingInvoiceType.val() === 'invoice' ) || billingCountryVal === 'IT' && ywpi_checkout.receipt_ssn_mandatory ) {
				setFieldAsRequired( billingReceiverVatSSN );
			} else {
				setFieldAsNotRequired( billingReceiverVatSSN );
			}
		}

		billingCompany.closest( '.form-row' ).hide();

	}

};



var setFieldAsRequired = function( field ){

	var requiredHtml = '<abbr class="required" title="required">*</abbr>';
	if ( field.closest( '.form-row' ).find( '.optional' ).length != 0) {
		field.closest( '.form-row' ).find( '.optional' ).remove();
		field.closest( '.form-row' ).find( 'label' ).append( requiredHtml );
	}
	field.closest( '.form-row' ).show();
	if ( field.val() == '' ) {

		field.closest( '.form-row' ).addClass( 'validate-required woocommerce-invalid woocommerce-invalid-required-field' );
	}

};

var setFieldAsNotRequired = function( field, $show = 'yes' ){

	var optionalHtml = '<span class="optional">' + ywpi_checkout.optional_label + '</span>';

	if ( field.closest( '.form-row' ).find( '.optional' ).length == 0) {
		field.closest( '.form-row' ).find( 'abbr' ).remove();
		field.closest( '.form-row' ).find( 'label' ).append( optionalHtml );
	}

	field.closest( '.form-row' ).removeClass( 'validate-required woocommerce-invalid woocommerce-invalid-required-field' );
	if ( $show == 'no' ) {
		field.closest( '.form-row' ).removeClass( 'validate-required woocommerce-invalid woocommerce-invalid-required-field' ).hide();
	}
};


function CheckCodiceFiscale(codice_fiscale)
{
	var caratteri_validi_cf, i, s, car1, car2, set_pari, set_dispari;
	if ( codice_fiscale == '' ) {
		return '';
	}
	codice_fiscale = codice_fiscale.toUpperCase();
	if ( codice_fiscale.length != 16 ) {
		return ywpi_checkout.codice_fiscale_length_error;
	}
	caratteri_validi_cf = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	for ( i = 0; i < 16; i++ ) {
		if ( caratteri_validi_cf.indexOf( codice_fiscale.charAt( i ) ) == -1 ) {
			return ywpi_checkout.codice_fiscale_char1_error + "`" +
				codice_fiscale.charAt( i ) +
				"'.\n" + ywpi_checkout.codice_fiscale_char2_error + " \n";
		}
	}
	car1        = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	car2        = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
	set_pari    = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	set_dispari = "BAKPLCQDREVOSFTGUHMINJWZYX";
	s           = 0;
	for ( i = 1; i <= 13; i += 2 ) {
		s += set_pari.indexOf( car2.charAt( car1.indexOf( codice_fiscale.charAt( i ) ) ) );
	}
	for ( i = 0; i <= 14; i += 2 ) {
		s += set_dispari.indexOf( car2.charAt( car1.indexOf( codice_fiscale.charAt( i ) ) ) );
	}
	if ( s % 26 != codice_fiscale.charCodeAt( 15 ) - 'A'.charCodeAt( 0 ) ) {
		return ywpi_checkout.codice_fiscale_errato;
	}
	return "";
}


function validate_ssn_field( field,value )
{
	var receiver_type = jQuery( 'input[name=billing_receiver_type]:checked' ).val();
	if ( receiver_type === 'private' ) {
		var err = CheckCodiceFiscale( value );
		if ( err != '' ) {
			alert( ywpi_checkout.validate_ssn_msg_error );
		}
	}

}
