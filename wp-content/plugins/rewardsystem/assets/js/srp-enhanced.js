// global srp_enhanced_params.
jQuery( function( $ ) {
    'use strict' ;

    try {
        $( document.body ).on( 'srp-enhanced-init' , function( ) {
            if( $( 'select.srp_select2' ).length ) {
                //Select2 with customization
                $( 'select.srp_select2' ).each( function( ) {
                    var select2_args = {
                        allowClear : $( this ).data( 'allow_clear' ) ? true : false ,
                        placeholder : $( this ).data( 'placeholder' ) ,
                        minimumResultsForSearch : 10 ,
                    } ;
                    
                    if( parseFloat( srp_enhanced_params.srp_wc_version ) > parseFloat( '2.2.0' ) ) {
                        $( this ).select2( select2_args ) ;
                    } else {
                        $( this ).chosen( select2_args ) ;
                    }
                } ) ;
            }

        } ) ;
        $( document.body ).trigger( 'srp-enhanced-init' ) ;
    } catch( err ) {
        window.console.log( err ) ;
    }

} ) ;