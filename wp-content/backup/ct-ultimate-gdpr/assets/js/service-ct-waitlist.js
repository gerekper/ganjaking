/** @var ct_ultimate_gdpr_eform object - localized */

jQuery(document).on('ready', function ($) {

    if( ! jQuery('body').find('.ct-waitlist-add-button').is(":hidden") ){
        jQuery('.ct-waitlist-button-container').append( ct_ultimate_gdpr_ct_waitlist.checkbox );
    }

    if( jQuery('body').find('.product-type-variable').length !== 0 ){

        var productVariations;
        productVariations = jQuery('.variations_form').data('product_variations');

        jQuery( document ).on('change', 'input[name="variation_id"]', function(){
            var counter, inStock, selectedVariation;
            selectedVariation = parseInt( jQuery( this ).val() ) > 0 ? parseInt( jQuery( this ).val() ) : false;

            if ( selectedVariation ) {
                for ( counter = 0; counter < productVariations.length; counter++ ) {
                    if ( parseInt( productVariations[ counter ].variation_id ) === selectedVariation ) {
                        inStock = productVariations[ counter ].is_in_stock;
                        if (inStock !== true) {
                            setTimeout( function(){
                                jQuery('.ct-waitlist-add-button').after( ct_ultimate_gdpr_ct_waitlist.checkbox );
                                jQuery('.ct-ultimate-gdpr-ct-waitlist input[type=checkbox]').css( {
                                    'width'         : 'auto',
                                    'margin-right' : '15px'
                                } );

                                if ( ! jQuery('#ct-ultimate-gdpr-consent-field-ct-waitlist').is(':checked') ) {
                                    jQuery('.ct-waitlist-add-button').attr('disabled', 'disabled');
                                }
                            },500 )
                        }
                    }
                }
            }
        });

        if ( ! jQuery('#ct-ultimate-gdpr-consent-field-ct-waitlist').is(':checked') ) {
            jQuery('.ct-waitlist-add-button').attr('disabled', 'disabled');
        }

        jQuery('body').on('click', '#ct-ultimate-gdpr-consent-field-ct-waitlist', function (e) {
            if (jQuery(this).is(":checked")) {
                jQuery('.ct-waitlist-add-button').removeAttr('disabled');
            } else {
                jQuery('.ct-waitlist-add-button').attr('disabled', 'disabled');
            }
        });

        jQuery(document).ajaxStart(function () {

            if (jQuery('#ct-ultimate-gdpr-consent-field-ct-waitlist').is(':checked')) {
                jQuery('.ct-ultimate-gdpr-ct-waitlist').hide();
            }

        });
    }

    if(
        jQuery( 'body' ).find( '.ct-ultimate-gdpr-ct-waitlist' ).length != 0 ||
        jQuery( 'body' ).find( '.ct-waitlist-add-button' ).length != 0

    ) {

        if ( ! jQuery('#ct-ultimate-gdpr-consent-field-ct-waitlist').is(':checked') ) {
            jQuery('.ct-waitlist-add-button').attr('disabled', 'disabled');
        }

        jQuery('body').on('click', '#ct-ultimate-gdpr-consent-field-ct-waitlist', function (e) {
            if (jQuery(this).is(":checked")) {
                jQuery('.ct-waitlist-add-button').removeAttr('disabled');
            } else {
                jQuery('.ct-waitlist-add-button').attr('disabled', 'disabled');
            }
        });

        jQuery(document).ajaxStart(function () {

            if (jQuery('#ct-ultimate-gdpr-consent-field-ct-waitlist').is(':checked')) {
                jQuery('.ct-ultimate-gdpr-ct-waitlist').hide();
            }

        });
    }

});