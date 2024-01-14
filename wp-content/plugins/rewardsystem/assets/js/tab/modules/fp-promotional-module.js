/*
 * Promotional Reward Points - Module
 */
jQuery( function ( $ ) {
    'use strict' ;
    var PromotionalModule = {
        init : function () {
            this.trigger_on_page_load() ;
            
            $( document ).on( 'click' , ".srp-rules-content h3" , this.toggle_section ) ;
            //Add Rules for Promotional.
            $( document ).on( 'click' , ".srp-add-rule" , this.add_rule ) ;
            //Delete Rules for Promotional.
            $( document ).on( 'click' , ".srp-delete-rule" , this.delete_rule ) ;
        } ,
        trigger_on_page_load : function () {
            $( '.srp-rule-fields' ).hide() ;
        } ,
        toggle_section : function () {
            $( this ).nextUntil( 'h3' ).toggle() ;
        } ,
        add_rule : function ( e ) {
            e.preventDefault() ;
            var $this = $( e.currentTarget ) ;
            var block_div = $( $this ).closest( '.srp-rules-wrapper' ).find( '.srp-rules-content' ) ;
            PromotionalModule.block( block_div ) ;
            var count = parseInt( $( 'input#srp_promotional_key:last' ).val( ) ) ;
            count = count + 1 || 1 ;
            var data = {
                action : 'srp_add_rule' ,
                count : count ,
                srp_security : fp_promotional_params.rule_nonce
            } ;

            $.post( ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    $( $this ).closest( '.srp-rules-wrapper' ).find( '.srp-rules-content' ).append( response.data.field ) ;
                    $( '.srp-deal-type' ).each( function () {
                        PromotionalModule.toggle_deal_type( $( this ) ) ;
                    } ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
                $( document.body ).trigger( 'srp-enhanced-init' ) ;
                PromotionalModule.unblock( block_div ) ;
            } ) ;
        } ,
        delete_rule : function ( e ) {
            e.preventDefault() ;
            if ( confirm( fp_promotional_params.delete_rule ) ) {
                var $this = $( e.currentTarget ) ;
                $( $this ).closest( '.srp-rules-content-wrapper' ).remove() ;
                var data = {
                    action : 'srp_delete_rule' ,
                    rule_id : $( this ).attr( 'data-ruleid' ) ,
                    srp_security : fp_promotional_params.rule_nonce
                } ;

                $.post( ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        $( $this ).closest( '.srp-rules-content-wrapper' ).remove() ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
        } ,
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
            $( id ).unblock() ;
        } ,
    } ;
    PromotionalModule.init() ;
} ) ;