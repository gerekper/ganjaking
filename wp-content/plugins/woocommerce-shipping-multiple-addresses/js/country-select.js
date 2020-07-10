jQuery( document ).ready( function( $ ) {

    // wcms_country_select_params is required to continue, ensure the object exists.
    if ( typeof wcms_country_select_params === 'undefined' ) {
		return false;
	}

    // Use selectWoo if it exists.
    if ( $().selectWoo ) {
        var wc_country_select_select2 = function() {
            $( 'select.country_select:visible, select.state_select:visible' ).each( function() {
                var select2_args = {
                    minimumResultsForSearch: 10,
                    placeholder: $( this ).attr( 'placeholder' ),
                    placeholderOption: 'first',
                    width: 'element',
				};

				$( this ).selectWoo( select2_args );
			} );
        };

        wc_country_select_select2();

        $( 'body' ).bind( 'country_to_state_changed', function() {
            wc_country_select_select2();
        } );
    }

    // State/Country select boxes.
    var states_json = wcms_country_select_params.countries.replace( /&quot;/g, '"' );
    var states = $.parseJSON( states_json );

	$( document.body ).on( 'change refresh', 'select.country_to_state, input.country_to_state', function() {

		var $wrapper = $( this ).closest( '.shipping_address' );

		if ( ! $wrapper.length ) {
			$wrapper = $( this ).closest( '.form-row' ).parent();
		}

		var country       = $( this ).val(),
        	$statebox     = $wrapper.find( '#billing_state, #shipping_state, #calc_shipping_state' ),
        	$parent       = $statebox.parent(),
			input_name    = $statebox.attr('name'),
			input_id      = $statebox.attr('id'),
			input_classes = $statebox.attr('data-input-classes'),
        	value         = $statebox.val(),
			placeholder   = $statebox.attr( 'placeholder' ) || $statebox.attr( 'data-placeholder' ) || '',
			$newstate;

		if ( states[ country ] ) {
			if ( states[ country ].length == 0 ) {

				$newstate = $( '<input type="hidden" />' )
					.prop( 'id', input_id )
					.prop( 'name', input_name )
					.prop( 'placeholder', placeholder )
					.attr( 'data-input-classes', input_classes )
					.addClass( 'hidden ' + input_classes );
				$parent.hide().find( '.select2-container' ).remove();
				$statebox.replaceWith( $newstate );
				$( document.body ).trigger( 'country_to_state_changed', [ country, $wrapper ] );

            } else {

				var state      = states[ country ],
				$defaultOption = $( '<option value=""></option>' ).text( wcms_country_select_params.i18n_select_state_text );

				$parent.show();

				if ( $statebox.is( 'input' ) ) {
					$newstate = $( '<select></select>' )
						.prop( 'id', input_id )
						.prop( 'name', input_name )
						.data( 'placeholder', placeholder )
						.attr( 'data-input-classes', input_classes )
						.addClass( 'state_select ' + input_classes );
					$statebox.replaceWith( $newstate );
					$statebox = $wrapper.find( '#billing_state, #shipping_state, #calc_shipping_state' );
				}

				$statebox.empty().append( $defaultOption );

				$.each( state, function( index ) {
					var $option = $( '<option></option>' )
						.prop( 'value', index )
						.text( state[ index ] );
					$statebox.append( $option );
				} );

				$statebox.val( value ).change();

				$( document.body ).trigger( 'country_to_state_changed', [ country, $wrapper ] );

            }
        } else {
			if ( $statebox.is( 'select, input[type="hidden"]' ) ) {
				$newstate = $( '<input type="text" />' )
					.prop( 'id', input_id )
					.prop( 'name', input_name )
					.prop('placeholder', placeholder)
					.attr('data-input-classes', input_classes )
					.addClass( 'input-text  ' + input_classes );
				$parent.show().find( '.select2-container' ).remove();
				$statebox.replaceWith( $newstate );
				$( document.body ).trigger( 'country_to_state_changed', [ country, $wrapper ] );
			}
        }

        $( document.body ).trigger( 'country_to_state_changing', [ country, $wrapper ] );

    } );

    $( 'select.country_select' ).change();

} );
