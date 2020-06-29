var $ = jQuery;

$(document).ready(function () {

    var billingCountry = $('#billing_country');
    var billingCompany = $('#billing_company');
    var billingType = $("input[name='billing_receiver_type']");
    var billingReceiverID = $('#billing_receiver_id');
    var billingReceiverPec = $('#billing_receiver_pec');
    var billingReceiverVatNumber = $('#billing_vat_number');
    var billingReceiverVatSSN = $('#billing_vat_ssn');

    validateFields();

    billingType.on('change', function () {
        validateFields();
    });

    // billingCompany.on('input', function () {
    //     validateFields();
    // });
    billingReceiverID.on('input', function () {
        validateFields();
    });
    billingReceiverPec.on('input', function () {
        validateFields();
    });
    billingReceiverVatNumber.on('input', function () {
        validateFields();
    });

    if( ywpi_checkout.is_ssn_mandatory != 'yes' ){
        billingReceiverVatSSN.on('input', function () {
            validateFields();
        });
    }

    $('#billing_vat_ssn').on('focusout', function (e) {
        validate_ssn_field('ssn',jQuery(this).val());
    });



    billingCountry.on('change', function () {
        validateFields();
    });
});


var validateFields = function(){

    var billingCountry = $('#billing_country');
    var billingCompany = $('#billing_company');
    var billingType = $('#billing_receiver_type');
    var billingReceiverID = $('#billing_receiver_id');
    var billingReceiverPec = $('#billing_receiver_pec');
    var billingReceiverVatNumber = $('#billing_vat_number');
    var billingReceiverVatSSN = $('#billing_vat_ssn');
    var billingTypeValue = $("input[name='billing_receiver_type']:checked").val();



    if( billingTypeValue == 'company' ){
      $('#billing_invoice_type_field').hide();
    }else{
      $('#billing_invoice_type_field').show();
    }

    if ( billingTypeValue !== 'undefined' && jQuery.inArray( billingTypeValue, ["company","freelance"]) !== -1  ) {

        billingCompany.closest('.form-row').show();

        if( billingTypeValue == 'company' ){
            $('#billing_invoice_type_field').hide();
            $('#billing_invoice_type_field option[value="receipt"]').removeAttr('selected');
            $('#billing_invoice_type_field option[value="invoice"]').attr("selected", "selected");
            setFieldAsRequired( billingCompany );
        }else{
          $('#billing_invoice_type_field').show();
          setFieldAsNotRequired( billingCompany );
        }

        if( billingCountry.val() == 'IT' ){

            billingReceiverID.closest('.form-row').show();
            billingReceiverPec.closest('.form-row').show();

            if( billingReceiverID.val() != '' && billingReceiverPec.val() == '' ){
                setFieldAsNotRequired( billingReceiverPec );
                billingReceiverID.closest('.form-row').find('.optional').remove();
            }else if( billingReceiverID.val() == '' && billingReceiverPec.val() != '' ){
                setFieldAsNotRequired( billingReceiverID );
                billingReceiverPec.closest('.form-row').find('.optional').remove();

            }else if( billingReceiverID.val() == '' && billingReceiverPec.val() == '' ){
                setFieldAsRequired( billingReceiverID );
                setFieldAsRequired( billingReceiverPec );
            }

            if( ywpi_checkout.is_vat_mandatory != 'yes' ){
                setFieldAsRequired( billingReceiverVatNumber );
            }

            if( ywpi_checkout.is_ssn_mandatory != 'yes' ){
                setFieldAsNotRequired( billingReceiverVatSSN );
            }


        }else{

            if( ywpi_checkout.is_vat_mandatory != 'yes' ){
                setFieldAsNotRequired( billingReceiverVatNumber );
            }

            setFieldAsNotRequired( billingReceiverID, 'no' );
            setFieldAsNotRequired( billingReceiverPec, 'no' );
        }


    }else{

        setFieldAsNotRequired( billingReceiverID, 'no' );
        setFieldAsNotRequired( billingReceiverPec, 'no' );

        if( ywpi_checkout.is_vat_mandatory != 'yes' ){
            setFieldAsNotRequired( billingReceiverVatNumber, 'no' );
        }


        if( ywpi_checkout.is_ssn_mandatory != 'yes' ){

            if( billingCountry.val() == 'IT' ){
                setFieldAsRequired( billingReceiverVatSSN );
            }else{
                setFieldAsNotRequired( billingReceiverVatSSN );
            }
        }

        billingCompany.closest('.form-row').hide();


    }


};



var setFieldAsRequired = function( field ){

    var requiredHtml = '<abbr class="required" title="required">*</abbr>';
    if( field.closest('.form-row').find('.optional').length != 0){
        field.closest('.form-row').find('.optional').remove();
        field.closest('.form-row').find('label').append(requiredHtml);
    }
    field.closest('.form-row').show();
    if( field.val() == '' ){

        field.closest('.form-row').addClass('validate-required woocommerce-invalid woocommerce-invalid-required-field');
    }

};

var setFieldAsNotRequired = function( field, $show = 'yes' ){

    var optionalHtml = '<span class="optional">(optional)</span>';

    if( field.closest('.form-row').find('.optional').length == 0){
        field.closest('.form-row').find('abbr').remove();
        field.closest('.form-row').find('label').append(optionalHtml);
    }

    field.closest('.form-row').removeClass('validate-required woocommerce-invalid woocommerce-invalid-required-field');
    if( $show == 'no' ){
        field.closest('.form-row').removeClass('validate-required woocommerce-invalid woocommerce-invalid-required-field').hide();
    }
};


function CheckCodiceFiscale(codice_fiscale)
{
    var caratteri_validi_cf, i, s, car1, car2, set_pari, set_dispari;
    if( codice_fiscale == '' )  return '';
    codice_fiscale = codice_fiscale.toUpperCase();
    if( codice_fiscale.length != 16 )
        return "La lunghezza del codice fiscale non è\n"
            +"corretta: il codice fiscale dovrebbe essere lungo\n"
            +"esattamente 16 caratteri.\n";
    caratteri_validi_cf = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for( i = 0; i < 16; i++ ){
        if( caratteri_validi_cf.indexOf( codice_fiscale.charAt(i) ) == -1 )
            return "Il codice fiscale contiene caratteri non validi `" +
                codice_fiscale.charAt(i) +
                "'.\nI caratteri peril codice fiscale sono lettere e cifre.\n";
    }
    car1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    car2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
    set_pari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    set_dispari = "BAKPLCQDREVOSFTGUHMINJWZYX";
    s = 0;
    for( i = 1; i <= 13; i += 2 )
        s += set_pari.indexOf( car2.charAt( car1.indexOf( codice_fiscale.charAt(i) )));
    for( i = 0; i <= 14; i += 2 )
        s += set_dispari.indexOf( car2.charAt( car1.indexOf( codice_fiscale.charAt(i) )));
    if( s%26 != codice_fiscale.charCodeAt(15)-'A'.charCodeAt(0) )
        return "Il codice fiscale è errato";
    return "";
}


function validate_ssn_field( field,value )
{

    var err = CheckCodiceFiscale(value);
    if( err != '' ){
        alert("Il codice fiscale inserito non è corretto");
    }
}
