jQuery( function ( $ ) {
    var is_blocked = function ( $node ) {
        return $node.is( '.processing' ) ;
    } ;

    var block = function ( $node ) {
        $node.addClass( 'processing' ).block( {
            message : null ,
            overlayCSS : {
                background : '#fff' ,
                opacity : 0.6
            }
        } ) ;
    } ;
    var unblock = function ( $node ) {
        $node.removeClass( 'processing' ).unblock() ;
    } ;
    var point_price = {
        init : function () {
            $( document.body ).on( 'change' , 'input, select' , '.wc-bookings-booking-form' , this.remove_coupon ) ;
        } ,
        remove_coupon : function ( e ) {
            e.preventDefault() ;
            var form = $( this ).closest( 'form' ) ;
            block( form ) ;
            var data = {
                action : 'rs_point_price_compatability' ,
                form : form.serialize() ,
            } ;
            $.ajax( {
                url : pointpricecompatibility_variable_js.wp_ajax_url ,
                data : data ,
                dataType : 'html' ,
                type : 'post' ,
                success : function ( code ) {
                    result = $.parseJSON( code ) ;
                    console.log( result.sumorewardpoints ) ;
                    if ( result.sumorewardpoints == '0' ) {
                        $form.find( '.wc-bookings-booking-cost1' ).hide() ;
                    }
                    if ( result.result == 'SUCCESS' ) {

                        $form.find( '.wc-bookings-booking-cost' ).hide() ;
                        $form.find( '.wc-bookings-booking-cost1' ).show() ;
                        $form.find( '.wc-bookings-booking-cost1' ).html( result.html ) ;
                        $form.find( '.point_price_label' ).html( result.html ) ;
                        $form.find( '.wc-bookings-booking-cost1' ).unblock() ;
                        $form.find( '.single_add_to_cart_button' ).removeClass( 'disabled' ) ;
                    }
                    if ( result.result == 'ERROR' ) {
                        $form.find( '.wc-bookings-booking-cost1' ).html( result.html ) ;
                        $form.find( '.wc-bookings-booking-cost1' ).unblock() ;
                        $form.find( '.single_add_to_cart_button' ).addClass( 'disabled' ) ;
                    }
                    console.log( code ) ;

                } ,
                complete : function () {
                    unblock( form ) ;

                }

            } ) ;

        }
    } ;

    point_price.init() ;
} ) ;


