/*global wc_address_i18n_params */
jQuery( function( $ ) {

    // wc_address_i18n_params is required to continue, ensure the object exists
    if ( typeof wc_address_i18n_params === 'undefined' ) {
        return false;
    }

    var locale_json = wc_address_i18n_params.locale.replace( /&quot;/g, '"' ), locale = $.parseJSON( locale_json );

    function field_is_required( field, is_required ) {
        if ( is_required ) {
            field.find( 'label .optional' ).remove();
            field.addClass( 'validate-required' );

            if ( field.find( 'label .required' ).length === 0 ) {
                field.find( 'label' ).append(
                    '&nbsp;<abbr class="required" title="' +
                    wc_address_i18n_params.i18n_required_text +
                    '">*</abbr>'
                );
            }
        } else {
            field.find( 'label .required' ).remove();
            field.removeClass( 'validate-required woocommerce-invalid woocommerce-invalid-required-field' );

            if ( field.find( 'label .optional' ).length === 0 ) {
                field.find( 'label' ).append( '&nbsp;<span class="optional">(' + wc_address_i18n_params.i18n_optional_text + ')</span>' );
            }
        }
    }

    // Handle locale
    $( document.body )
        .bind( 'country_to_state_changing', function( event, country, wrapper ) {
            var thisform = wrapper, thislocale, section;

            // get section
            section = thisform.attr('class');
            section = ( section && section.indexOf('billing') !== -1 ) ? 'billing' : 'shipping';

            if ( typeof locale[ country ] !== 'undefined' ) {
                thislocale = locale[ country ];
            } else {
                thislocale = locale['default'][ section ];
            }

            if( ! thislocale ) {
                return false;
            }

            var $postcodefield = thisform.find( '#billing_postcode_field, #shipping_postcode_field' ),
                $cityfield     = thisform.find( '#billing_city_field, #shipping_city_field' ),
                $statefield    = thisform.find( '#billing_state_field, #shipping_state_field' );

            if ( ! $postcodefield.attr( 'data-o_class' ) ) {
                $postcodefield.attr( 'data-o_class', $postcodefield.attr( 'class' ) );
                $cityfield.attr( 'data-o_class', $cityfield.attr( 'class' ) );
                $statefield.attr( 'data-o_class', $statefield.attr( 'class' ) );
            }

            var locale_fields = $.parseJSON( wc_address_i18n_params.locale_fields );

            $.each( locale_fields, function( key, value ) {

                var field       = thisform.find( value ),
                    fieldLocale = $.extend( true, {}, locale['default'][ section ][ key ], thislocale[ key ] );

                // Labels.
                if ( typeof fieldLocale.label !== 'undefined' ) {
                    field.find( 'label' ).html( fieldLocale.label );
                }

                // Placeholders.
                if ( typeof fieldLocale.placeholder !== 'undefined' ) {
                    field.find( ':input' ).attr( 'placeholder', fieldLocale.placeholder );
                    field.find( ':input' ).attr( 'data-placeholder', fieldLocale.placeholder );
                    field.find( '.select2-selection__placeholder' ).text( fieldLocale.placeholder );
                }

                // Use the i18n label as a placeholder if there is no label element and no i18n placeholder.
                if (
                    typeof fieldLocale.placeholder === 'undefined' &&
                    typeof fieldLocale.label !== 'undefined' &&
                    ! field.find( 'label' ).length
                ) {
                    field.find( ':input' ).attr( 'placeholder', fieldLocale.label );
                    field.find( ':input' ).attr( 'data-placeholder', fieldLocale.label );
                    field.find( '.select2-selection__placeholder' ).text( fieldLocale.label );
                }

                // Required.
                if ( typeof fieldLocale.required !== 'undefined' ) {
                    field_is_required( field, fieldLocale.required );
                } else {
                    field_is_required( field, false );
                }

                // Hidden fields.
                if ( 'state' !== key ) {
                    if ( typeof fieldLocale.hidden !== 'undefined' && true === fieldLocale.hidden ) {
                        field.hide().find( ':input' ).val( '' );
                    } else {
                        field.show();
                    }
                }

                $( document.body ).trigger( 'yith_wccp_i18n_locale_done' );
            });
        })
        .trigger( 'wc_address_i18n_ready' );
});
