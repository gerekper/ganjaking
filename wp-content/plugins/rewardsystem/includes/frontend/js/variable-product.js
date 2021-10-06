
jQuery( function ( $ ) {
   'use strict' ;
    var FP_Variable_product = {
        init : function ( ) {
            this.trigger_on_page_load() ;
        } ,
        trigger_on_page_load : function ( ) {
            this.product_purchase_points_variable_product_load();
            this.buying_points_variable_product_load();
        } ,
        product_purchase_points_variable_product_load:function(){
            if ( '1' == variable_product_params.show_or_hide_purchase_message ) { 
		$( '#value_variable_product1' ).addClass( 'woocommerce-info' ) ;
		$( '#value_variable_product1' ).addClass( 'rs_message_for_single_product' ) ;
		$( '#value_variable_product1' ).show() ;
		$( '.gift_icon' ).hide() ;
		$( '#value_variable_product1' ).html(variable_product_params.purchase_message ) ;
	    } 
            if ( '1' == variable_product_params.show_or_hide_earn_message ) { 
		$( '.variableshopmessage' ).show() ;
		$( '.variableshopmessage' ).html(variable_product_params.earn_message) ;
	    } 
        },
        buying_points_variable_product_load:function(){
            if ( '1' == variable_product_params.show_or_hide_buying_purchase_message ) { 
		$( '#buy_Point_value_variable_product' ).addClass( 'woocommerce-info rs_message_for_single_product' ) ;
		$( '#buy_Point_value_variable_product' ).show() ;
		$( '.gift_icon' ).hide() ;
		$( '#buy_Point_value_variable_product' ).html(variable_product_params.buying_points_purchase_message) ;
	    }
		
            if ( '1' == variable_product_params.show_or_hide_buying_earn_message ) {
		if ( 'yes' == variable_product_params.product_purchase_activated ) {
		    $( '.variableshopmessage' ).show() ;
		    $( '.variableshopmessage' ).append( variable_product_params.buying_points_earn_message ) ;
                } else {
		    $( '.variableshopmessage' ).show() ;
		    $( '.variableshopmessage' ).html(variable_product_params.buying_points_earn_message) ;
                }
            }
        },
        block : function ( id ) {
            $( id ).block( {
                message : null ,
                overlayCSS : {
                    background : '#fff' ,
                    opacity : 0.6
                }
            } ) ;
        } ,
        unblock : function ( id ) {
            $( id ).unblock( ) ;
        } ,
    } ;
    FP_Variable_product.init( ) ;
} ) ;