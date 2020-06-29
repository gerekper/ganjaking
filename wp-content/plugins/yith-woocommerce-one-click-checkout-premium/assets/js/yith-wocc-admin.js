/**
 * ADMIN SCRIPTS
 **/
jQuery( document ).ready( function($){
   "use strict";

    var option = $( '.yith-wocc.wc-enhanced-select-nostd').parents('tr');

    $( 'input[name="yith-wocc-redirection-url"]').on('change', function(){

        if( $(this).is(':checked') && $(this).val() == 'custom' ) {
            option.show();
        }
        else {
            option.hide();
        }

    }).trigger('change' );

});