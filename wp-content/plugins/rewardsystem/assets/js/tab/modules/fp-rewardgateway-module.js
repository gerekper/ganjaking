/*
 * Reward GateWay - Module
 */
jQuery( function ( $ ) {
    var RewardGatewayScripts = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_visibility() ;
            $( document ).on( 'change' , '#rs_show_hide_reward_points_gateway' , this.show_or_hide_for_visibility ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_reward_gateway_module_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_select_category_for_purchase_using_points' ).chosen() ;
                $( '#rs_select_category_to_hide_gateway' ).chosen() ;
                $( '#rs_order_status_control_revise_redeem' ).chosen() ;
            } else {
                $( '#rs_select_category_for_purchase_using_points' ).select2() ;
                $( '#rs_select_category_to_hide_gateway' ).select2() ;
                $( '#rs_order_status_control_revise_redeem' ).select2() ;
            }
        } ,
        show_or_hide_for_visibility : function () {
            if ( jQuery( '#rs_show_hide_reward_points_gateway' ).val() == '1' ) {
                jQuery( '#rs_enable_selected_product_for_purchase_using_points' ).closest( 'tr' ).show() ;
                jQuery( '#rs_enable_selected_category_for_purchase_using_points' ).closest( 'tr' ).show() ;
                jQuery( '#rs_enable_selected_product_for_hide_gateway' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_enable_selected_category_to_hide_gateway' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_product_for_hide_gateway' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_category_to_hide_gateway' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_enable_gateway_visible_to_all_product' ).closest( 'tr' ).show() ;
                jQuery( '#rs_errmsg_when_other_products_added_to_cart_page' ).closest( 'tr' ).show() ;

                /*Show or Hide for Selected Product in Visible Option - Start*/
                if ( jQuery( '#rs_enable_selected_product_for_purchase_using_points' ).is( ':checked' ) ) {
                    jQuery( '#rs_select_product_for_purchase_using_points' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_select_product_for_purchase_using_points' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_enable_selected_product_for_purchase_using_points' ).change( function () {
                    if ( jQuery( '#rs_enable_selected_product_for_purchase_using_points' ).is( ':checked' ) ) {
                        jQuery( '#rs_select_product_for_purchase_using_points' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_select_product_for_purchase_using_points' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
                /*Show or Hide for Selected Product in Visible Option - End*/

                /*Show or Hide for Selected Category in Visible Option - Start*/
                if ( jQuery( '#rs_enable_selected_category_for_purchase_using_points' ).is( ':checked' ) ) {
                    jQuery( '#rs_select_category_for_purchase_using_points' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_select_category_for_purchase_using_points' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_enable_selected_category_for_purchase_using_points' ).change( function () {
                    if ( jQuery( '#rs_enable_selected_category_for_purchase_using_points' ).is( ':checked' ) ) {
                        jQuery( '#rs_select_category_for_purchase_using_points' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_select_category_for_purchase_using_points' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
                /*Show or Hide for Selected Category in Visible Option - End*/
            } else {
                jQuery( '#rs_enable_selected_product_for_purchase_using_points' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_enable_selected_category_for_purchase_using_points' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_product_for_purchase_using_points' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_category_for_purchase_using_points' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_enable_selected_product_for_hide_gateway' ).closest( 'tr' ).show() ;
                jQuery( '#rs_enable_selected_category_to_hide_gateway' ).closest( 'tr' ).show() ;
                jQuery( '#rs_enable_gateway_visible_to_all_product' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_errmsg_when_other_products_added_to_cart_page' ).closest( 'tr' ).show() ;

                /*Show or Hide for Selected Product in Hide Option - Start*/
                if ( jQuery( '#rs_enable_selected_product_for_hide_gateway' ).is( ':checked' ) ) {
                    jQuery( '#rs_select_product_for_hide_gateway' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_select_product_for_hide_gateway' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_enable_selected_product_for_hide_gateway' ).change( function () {
                    if ( jQuery( '#rs_enable_selected_product_for_hide_gateway' ).is( ':checked' ) ) {
                        jQuery( '#rs_select_product_for_hide_gateway' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_select_product_for_hide_gateway' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
                /*Show or Hide for Selected Product in Hide Option - End*/

                /*Show or Hide for Selected Category in Hide Option - Start*/
                if ( jQuery( '#rs_enable_selected_category_to_hide_gateway' ).is( ':checked' ) ) {
                    jQuery( '#rs_select_category_to_hide_gateway' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_select_category_to_hide_gateway' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_enable_selected_category_to_hide_gateway' ).change( function () {
                    if ( jQuery( '#rs_enable_selected_category_to_hide_gateway' ).is( ':checked' ) ) {
                        jQuery( '#rs_select_category_to_hide_gateway' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_select_category_to_hide_gateway' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
                /*Show or Hide for Selected Category in Hide Option - End*/
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
    RewardGatewayScripts.init() ;
} ) ;