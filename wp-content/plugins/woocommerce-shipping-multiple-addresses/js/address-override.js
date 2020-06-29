jQuery( document ).ready( function ( $ ) {

    "use strict";

    var address_col_el = "div.addresses .col-2";

    if ( $( address_col_el ).length ) {
        $( address_col_el ).remove();
    }

} );