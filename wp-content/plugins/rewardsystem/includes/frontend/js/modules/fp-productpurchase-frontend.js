/*
 * Product Purcahse - Module
 */
jQuery( function ( $ ) {
    var RSProductPurchaseFrontend = {
        init : function () {
            this.trigger_on_page_load() ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_productpurchase_frontend_params.availablepointsmsgp == 2 ) {
                if ( fp_productpurchase_frontend_params.page == 'cart' ) {
                    $( '.sumo_reward_points_manual_redeem_message' ).insertAfter( '.sumo_reward_points_current_points_message' ) ;
                    $( '.sumo_reward_points_auto_redeem_message' ).insertAfter( '.sumo_reward_points_current_points_message' ) ;
                    $( '.rs_button_redeem_cart' ).insertAfter( '.sumo_reward_points_current_points_message' ) ;
                }

                if ( fp_productpurchase_frontend_params.page == 'checkout' ) {
                    $( '.sumo_redeemed_points' ).insertAfter( '.sumo_available_points' ) ;
                    $( '.rs_button_redeem_checkout' ).insertAfter( '.sumo_available_points' ) ;
                }
            }
        } ,
    } ;
    RSProductPurchaseFrontend.init() ;
} ) ;