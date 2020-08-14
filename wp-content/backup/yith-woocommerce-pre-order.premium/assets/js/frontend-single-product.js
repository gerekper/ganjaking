(function ($) {
    $(document).ready(function ($) {
        var button = $( '.single_variation_wrap .single_add_to_cart_button');
        var add_to_cart_text = button.html();
        $( 'form.variations_form' )
            .on( 'show_variation', function( event, variation, purchasable ) {
                var button = $( this ).find( '.single_add_to_cart_button'); // It's mandatory to override this variable in order to take specifically the current button (when there is more than 1 Variable product in the current page).

                if ( 'yes' === variation.is_pre_order ) {
                    // Show button label
                    button.html( variation.pre_order_label );

                    // Show date label
                    var unix_time = parseInt( $( this ).find( 'div.pre_order_single' ).data( 'time' ) );
                    var date = new Date(0);
                    date.setUTCSeconds( unix_time );
                    var time = date.toLocaleTimeString();
                    time = time.slice(0, -3);
                    $( this ).find( '.availability_date' ).text( date.toLocaleDateString() );
                    $( this ).find( '.availability_time' ).text( time );

                } else {
                    button.html( add_to_cart_text );
                }
            } )
            .on( 'hide_variation', function( event ) {
                var button = $( this ).find( '.single_add_to_cart_button');
                event.preventDefault();
                button.html( add_to_cart_text );
            } );

        // Show date label for simple products

        $( 'div.pre_order_single' ).each( function () {
            var unix_time = parseInt( $( this ).data( 'time' ) );
            var date = new Date(0);
            date.setUTCSeconds( unix_time );
            var time = date.toLocaleTimeString();
            time = time.slice(0, -3);
            $( this ).find( '.availability_date' ).text( date.toLocaleDateString() );
            $( this ).find( '.availability_time' ).text( time );
        });

        // Show date label for simple products shown at 'Related products' (they has the .preorder-loop css class )

        $( 'div.pre_order_loop' ).each( function () {
            var unix_time = parseInt( $( this ).data( 'time' ) );
            var date = new Date(0);
            date.setUTCSeconds( unix_time );
            var time = date.toLocaleTimeString();
            time = time.slice(0, -3);
            $( this ).find( '.availability_date' ).text( date.toLocaleDateString() );
            $( this ).find( '.availability_time' ).text( time );
        });

    } );
})
(jQuery);