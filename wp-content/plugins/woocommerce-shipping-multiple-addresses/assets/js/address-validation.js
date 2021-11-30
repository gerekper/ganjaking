var liveAddress;
jQuery( document ).ready( function ( $ ) {

    "use strict";

    // setup + bind LiveAddress to billing/shipping fields
    liveAddress = $.LiveAddress( {
        key: wc_address_validation.smarty_streets_key,
        debug: wc_address_validation.debug,
        autoMap: false,
        addresses: [
            {
                id: "shipping",
                street: "#address_shipping_address_1",
                street2: "#address_shipping_address_1",
                city: "#address_shipping_city",
                state: "#address_shipping_state",
                zipcode: "#address_shipping_postcode",
                country: "#address_shipping_country"
            }
        ]
    } );


    // fires when address validation is complete
    liveAddress.on( 'Completed', function ( event, data, previousHandler ) {

        // only handle changes if address is in US
        if ( 'US' === $( '#add_address_form select.country_select' ).val() ) {

            // update state select
            $( '#add_address_form select.state_select' ).trigger( 'liszt:updated' );

            // update the order totals
            $( 'body' ).trigger( 'update_checkout' );

            // save latitude/longitude/address classification
            if ( data.response.raw.length > 0 ) {
                var info = {
                    'latitude' : data.response.raw[0].metadata.latitude,
                    'longitude' : data.response.raw[0].metadata.longitude,
                    'classification' : data.response.raw[0].metadata.rdi
                };

                $.each( info, function ( key, value ) {
                    $( '<input>' ).attr( { type : 'hidden', id : 'wc_address_validation_' + key, name : 'wc_address_validation_' + key } ).val( value ).appendTo( 'form.checkout' );
                });
            }
        }

        // make sure LiveAddress continues script actions
        previousHandler(event, data);
    } );


    // hide the verify shipping address button after fields are mapped
    liveAddress.on( 'MapInitialized', function( event, data, previousHandler ) {

        $( 'a.smarty-addr-shipping' ).parent( 'div' ).hide();

        // make sure LiveAddress continues script actions
        previousHandler(event, data);
    } );

    liveAddress.activate("shipping");

} );
