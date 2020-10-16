/*
 * Product Purchase - Module
 */
jQuery( function ( $ ) {
    var ProductPurchaseScripts = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_global_level_settings() ;
            this.show_or_hide_for_hold_product_purchase_points() ;
            this.show_or_hide_for_total_points_that_can_earn_in_cart() ;
            this.show_or_hide_for_total_points_that_can_earn_in_checkout() ;
            this.show_or_hide_for_cart_custom_message() ;
            this.show_or_hide_for_checkout_custom_message() ;
            this.show_or_hide_for_thankyou_custom_message() ;
            this.show_or_hide_for_enable_earn_point_msg_in_edit_order_page() ;
            this.show_or_hide_for_product_category_selection() ;
            this.show_or_hide_for_first_purchase_points() ;
            $( document ).on( 'change' , '.rs_enable_product_category_level_for_product_purchase' , this.global_level_settings ) ;
            $( document ).on( 'change' , '#rs_product_purchase_global_level_applicable_for' , this.global_level_settings_applicable_for ) ;
            $( document ).on( 'change' , '#rs_global_enable_disable_sumo_reward' , this.enable_global_level_settings ) ;

            $( document ).on( 'change' , '#rs_award_points_for_cart_or_product_total' , this.award_point_based_on ) ;
            $( document ).on( 'change' , '#rs_enable_cart_total_reward_points' , this.enable_cart_total_based_points ) ;
            $( document ).on( 'change' , '#rs_reward_type_for_cart_total' , this.reward_type_for_cart_total ) ;
            $( document ).on( 'change' , '#rs_enable_first_purchase_reward_points' , this.enable_first_purchase_points ) ;
            $( document ).on( 'change' , '.rs_which_product_selection' , this.product_category_selection ) ;

            $( document ).on( 'change' , '#rs_restrict_days_for_product_purchase' , this.hold_product_purchase_points ) ;
            $( document ).on( 'change' , '#rs_show_hide_minimum_cart_total_earn_error_message' , this.minimum_cart_total_error ) ;
            $( document ).on( 'change' , '#rs_show_hide_maximum_cart_total_earn_error_message' , this.maximum_cart_total_error ) ;
            $( document ).on( 'change' , '#rs_show_hide_total_points_cart_field' , this.total_points_that_can_earn_in_cart ) ;
            $( document ).on( 'change' , '#rs_show_hide_total_points_checkout_field' , this.total_points_that_can_earn_in_checkout ) ;

            $( document ).on( 'change' , '#rs_show_hide_custom_msg_for_points_cart' , this.cart_custom_message ) ;
            $( document ).on( 'change' , '#rs_show_hide_custom_msg_for_points_checkout' , this.checkout_custom_message ) ;
            $( document ).on( 'change' , '#rs_show_hide_custom_msg_for_points_thankyou' , this.thankyou_custom_message ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_earned_points' , this.enable_earn_point_msg_in_edit_order_page ) ;

            $( document ).on( 'click' , '.rs_sumo_reward_button' , this.bulk_update_points_for_product_purchase ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_product_purchase_module_param.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '.rs_include_particular_categories_for_product_purchase' ).chosen() ;
                $( '.rs_exclude_particular_categories_for_product_purchase' ).chosen() ;
                $( '.rs_select_particular_categories' ).chosen() ;
                $( '.rs_select_payment_gateway_for_restrict_reward' ).chosen() ;
            } else {
                $( '.rs_include_particular_categories_for_product_purchase' ).select2() ;
                $( '.rs_exclude_particular_categories_for_product_purchase' ).select2() ;
                $( '.rs_select_particular_categories' ).select2() ;
                $( '.rs_select_payment_gateway_for_restrict_reward' ).select2() ;
            }
        } ,
        global_level_settings : function () {
            ProductPurchaseScripts.show_or_hide_for_global_level_settings() ;
        } ,
        show_or_hide_for_global_level_settings : function () {
            if ( $( 'input[name=rs_enable_product_category_level_for_product_purchase]:checked' ).val() == 'no' ) {
                $( '#rs_product_purchase_global_level_applicable_for' ).closest( 'tr' ).show() ;
                $( '#rs_award_points_for_cart_or_product_total' ).closest( 'tr' ).show() ;
                $( '#rs_global_enable_disable_sumo_reward' ).closest( 'tr' ).show() ;
                $( '.rs_hide_bulk_update_for_product_purchase_start' ).hide() ;
                ProductPurchaseScripts.show_or_hide_for_award_point_based_on() ;
            } else {
                $( '#rs_product_purchase_global_level_applicable_for' ).closest( 'tr' ).hide() ;
                $( '#rs_include_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_include_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '.rs_hide_bulk_update_for_product_purchase_start' ).show() ;
                $( '#rs_award_points_for_cart_or_product_total' ).closest( 'tr' ).hide() ;
                $( '#rs_enable_cart_total_reward_points' ).closest( 'tr' ).hide() ;
                $( '#rs_reward_type_for_cart_total' ).closest( 'tr' ).hide() ;
                $( '#rs_reward_points_for_cart_total_in_fixed' ).closest( 'tr' ).hide() ;
                $( '#rs_reward_points_for_cart_total_in_percent' ).closest( 'tr' ).hide() ;
                $( '#rs_global_enable_disable_sumo_reward' ).closest( 'tr' ).show() ;
                ProductPurchaseScripts.show_or_hide_for_enable_global_level_settings() ;
                $( '#rs_calculate_point_based_on_reg_or_sale' ).closest( 'tr' ).show() ;
                $( '#rs_point_not_award_when_sale_price' ).closest( 'tr' ).show() ;
                $( '#rs_restrict_reward' ).closest( 'tr' ).show() ;
                $( '#rs_minimum_cart_total_for_earning' ).closest( 'tr' ).show() ;
                $( '#rs_show_hide_minimum_cart_total_earn_error_message' ).closest( 'tr' ).show() ;
                ProductPurchaseScripts.show_or_hide_for_minimum_cart_total_error() ;
                $( '#rs_show_hide_maximum_cart_total_earn_error_message' ).closest( 'tr' ).show() ;
                $( '#rs_maximum_cart_total_for_earning' ).closest( 'tr' ).show() ;
                ProductPurchaseScripts.show_or_hide_for_maximum_cart_total_error() ;
            }
        } ,
        enable_first_purchase_points : function () {
            ProductPurchaseScripts.show_or_hide_for_first_purchase_points() ;
        } ,
        show_or_hide_for_first_purchase_points : function () {
            if ( $( '#rs_enable_first_purchase_reward_points' ).is( ':checked' ) ) {
                $( '#rs_reward_points_for_first_purchase_in_fixed' ).closest( 'tr' ).show() ;

            } else {
                $( '#rs_reward_points_for_first_purchase_in_fixed' ).closest( 'tr' ).hide() ;
            }
        } ,
        award_point_based_on : function () {
            ProductPurchaseScripts.show_or_hide_for_award_point_based_on() ;
        } ,
        show_or_hide_for_award_point_based_on : function () {
            if ( $( '#rs_award_points_for_cart_or_product_total' ).val() == '1' ) {
                $( '#rs_enable_disable_reward_point_based_coupon_amount' ).closest( 'tr' ).show() ;
                $( '#rs_exclude_shipping_cost_based_on_cart_total' ).closest( 'tr' ).hide() ;
                $( '#rs_enable_cart_total_reward_points' ).closest( 'tr' ).hide() ;
                $( '#rs_reward_type_for_cart_total' ).closest( 'tr' ).hide() ;
                $( '#rs_product_purchase_global_level_applicable_for' ).closest( 'tr' ).show() ;
                $( '#rs_reward_points_for_cart_total_in_fixed' ).closest( 'tr' ).hide() ;
                $( '#rs_reward_points_for_cart_total_in_percent' ).closest( 'tr' ).hide() ;
                $( '#rs_global_enable_disable_sumo_reward' ).closest( 'tr' ).show() ;
                ProductPurchaseScripts.show_or_hide_for_enable_global_level_settings() ;
                ProductPurchaseScripts.show_or_hide_for_global_level_settings_applicable_for() ;
                $( '#rs_calculate_point_based_on_reg_or_sale' ).closest( 'tr' ).show() ;
                $( '#rs_point_not_award_when_sale_price' ).closest( 'tr' ).show() ;
                $( '#rs_restrict_reward' ).closest( 'tr' ).show() ;
                $( '#rs_minimum_cart_total_for_earning' ).closest( 'tr' ).show() ;
                $( '#rs_show_hide_minimum_cart_total_earn_error_message' ).closest( 'tr' ).show() ;
                ProductPurchaseScripts.show_or_hide_for_minimum_cart_total_error() ;
                $( '#rs_show_hide_maximum_cart_total_earn_error_message' ).closest( 'tr' ).show() ;
                $( '#rs_maximum_cart_total_for_earning' ).closest( 'tr' ).show() ;
                ProductPurchaseScripts.show_or_hide_for_maximum_cart_total_error() ;
                $( '#rs_display_earn_point_tax_based' ).closest( 'tr' ).show() ;
            } else {
                $( '#rs_enable_disable_reward_point_based_coupon_amount' ).closest( 'tr' ).hide() ;
                $( '#rs_enable_cart_total_reward_points' ).closest( 'tr' ).show() ;
                $( '#rs_reward_type_for_cart_total' ).closest( 'tr' ).show() ;
                $( '#rs_exclude_shipping_cost_based_on_cart_total' ).closest( 'tr' ).show() ; 
                ProductPurchaseScripts.show_or_hide_for_enable_cart_total_based_points() ;
                $( '#rs_product_purchase_global_level_applicable_for' ).closest( 'tr' ).hide() ;
                $( '#rs_include_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_include_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_global_enable_disable_sumo_reward' ).closest( 'tr' ).hide() ;
                $( '#rs_global_reward_type' ).closest( 'tr' ).hide() ;
                $( '#rs_global_reward_points' ).closest( 'tr' ).hide() ;
                $( '#rs_global_reward_percent' ).closest( 'tr' ).hide() ;
                $( '#rs_calculate_point_based_on_reg_or_sale' ).closest( 'tr' ).hide() ;
                $( '#rs_point_not_award_when_sale_price' ).closest( 'tr' ).hide() ;
                $( '#rs_restrict_reward' ).closest( 'tr' ).hide() ;
                $( '#rs_minimum_cart_total_for_earning' ).closest( 'tr' ).hide() ;
                $( '#rs_show_hide_minimum_cart_total_earn_error_message' ).closest( 'tr' ).hide() ;
                $( '#rs_min_cart_total_for_earning_error_message' ).closest( 'tr' ).hide() ;
                $( '#rs_maximum_cart_total_for_earning' ).closest( 'tr' ).hide() ;
                $( '#rs_show_hide_maximum_cart_total_earn_error_message' ).closest( 'tr' ).hide() ;
                $( '#rs_max_cart_total_for_earning_error_message' ).closest( 'tr' ).hide() ;
                $( '#rs_display_earn_point_tax_based' ).closest( 'tr' ).hide() ;
            }
        } ,
        enable_cart_total_based_points : function () {
            ProductPurchaseScripts.show_or_hide_for_enable_cart_total_based_points() ;
        } ,
        show_or_hide_for_enable_cart_total_based_points : function () {
            if ( $( '#rs_enable_cart_total_reward_points' ).val() == '1' ) {
                $( '#rs_reward_type_for_cart_total' ).closest( 'tr' ).show() ;
                ProductPurchaseScripts.show_or_hide_for_reward_type_for_cart_total() ;
                $( '#rs_product_purchase_global_level_applicable_for' ).closest( 'tr' ).hide() ;
                $( '#rs_include_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_include_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
            } else {
                $( '#rs_reward_type_for_cart_total' ).closest( 'tr' ).hide() ;
                $( '#rs_reward_points_for_cart_total_in_fixed' ).closest( 'tr' ).hide() ;
                $( '#rs_reward_points_for_cart_total_in_percent' ).closest( 'tr' ).hide() ;
                $( '#rs_product_purchase_global_level_applicable_for' ).closest( 'tr' ).hide() ;
                $( '#rs_include_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_include_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
            }
        } ,
        reward_type_for_cart_total : function () {
            ProductPurchaseScripts.show_or_hide_for_reward_type_for_cart_total() ;
        } ,
        show_or_hide_for_reward_type_for_cart_total : function () {
            if ( $( '#rs_reward_type_for_cart_total' ).val() == '1' ) {
                $( '#rs_reward_points_for_cart_total_in_fixed' ).closest( 'tr' ).show() ;
                $( '#rs_reward_points_for_cart_total_in_percent' ).closest( 'tr' ).hide() ;
            } else {
                $( '#rs_reward_points_for_cart_total_in_fixed' ).closest( 'tr' ).hide() ;
                $( '#rs_reward_points_for_cart_total_in_percent' ).closest( 'tr' ).show() ;
            }
        } ,
        global_level_settings_applicable_for : function () {
            ProductPurchaseScripts.show_or_hide_for_global_level_settings_applicable_for() ;
        } ,
        show_or_hide_for_global_level_settings_applicable_for : function () {
            if ( $( '#rs_product_purchase_global_level_applicable_for' ).val() == '1' ) {
                $( '#rs_include_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_include_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
            } else if ( $( '#rs_product_purchase_global_level_applicable_for' ).val() == '2' ) {
                $( '#rs_include_products_for_product_purchase' ).closest( 'tr' ).show() ;
                $( '#rs_exclude_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_include_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
            } else if ( $( '#rs_product_purchase_global_level_applicable_for' ).val() == '3' ) {
                $( '#rs_include_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_product_purchase' ).closest( 'tr' ).show() ;
                $( '#rs_include_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
            } else if ( $( '#rs_product_purchase_global_level_applicable_for' ).val() == '4' ) {
                $( '#rs_include_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_include_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
            } else if ( $( '#rs_product_purchase_global_level_applicable_for' ).val() == '5' ) {
                $( '#rs_include_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_include_particular_categories_for_product_purchase' ).closest( 'tr' ).show() ;
                $( '#rs_exclude_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
            } else {
                $( '#rs_include_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_products_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_include_particular_categories_for_product_purchase' ).closest( 'tr' ).hide() ;
                $( '#rs_exclude_particular_categories_for_product_purchase' ).closest( 'tr' ).show() ;
            }
        } ,
        enable_global_level_settings : function () {
            ProductPurchaseScripts.show_or_hide_for_enable_global_level_settings() ;
        } ,
        show_or_hide_for_enable_global_level_settings : function () {
            if ( $( '#rs_global_enable_disable_sumo_reward' ).val() == '2' ) {
                $( '.show_if_enable_in_general' ).parent().parent().hide() ;
            } else {
                $( '#rs_global_reward_type' ).parent().parent().show() ;
                if ( $( '#rs_global_reward_type' ).val() == '1' ) {
                    $( '#rs_global_reward_points' ).parent().parent().show() ;
                    $( '#rs_global_reward_percent' ).parent().parent().hide() ;
                } else {
                    $( '#rs_global_reward_points' ).parent().parent().hide() ;
                    $( '#rs_global_reward_percent' ).parent().parent().show() ;
                }

                $( '#rs_global_reward_type' ).change( function () {
                    if ( $( '#rs_global_reward_type' ).val() == '1' ) {
                        $( '#rs_global_reward_points' ).parent().parent().show() ;
                        $( '#rs_global_reward_percent' ).parent().parent().hide() ;
                    } else {
                        $( '#rs_global_reward_points' ).parent().parent().hide() ;
                        $( '#rs_global_reward_percent' ).parent().parent().show() ;
                    }
                } ) ;
            }
        } ,
        hold_product_purchase_points : function () {
            ProductPurchaseScripts.show_or_hide_for_hold_product_purchase_points() ;
        } ,
        show_or_hide_for_hold_product_purchase_points : function () {
            if ( $( '#rs_restrict_days_for_product_purchase' ).is( ':checked' ) ) {
                $( '#rs_restrict_product_purchase_cron_type' ).closest( 'tr' ).show() ;
                $( '#rs_restrict_product_purchase_time' ).closest( 'tr' ).show() ;
            } else {
                $( '#rs_restrict_product_purchase_cron_type' ).closest( 'tr' ).hide() ;
                $( '#rs_restrict_product_purchase_time' ).closest( 'tr' ).hide() ;
            }
        } ,
        minimum_cart_total_error : function () {
            ProductPurchaseScripts.show_or_hide_for_minimum_cart_total_error() ;
        } ,
        show_or_hide_for_minimum_cart_total_error : function () {
            if ( $( '#rs_show_hide_minimum_cart_total_earn_error_message' ).val() == '1' ) {
                $( '#rs_min_cart_total_for_earning_error_message' ).closest( 'tr' ).show() ;
            } else {
                $( '#rs_min_cart_total_for_earning_error_message' ).closest( 'tr' ).hide() ;
            }
        } ,
        maximum_cart_total_error : function () {
            ProductPurchaseScripts.show_or_hide_for_maximum_cart_total_error() ;
        } ,
        show_or_hide_for_maximum_cart_total_error : function () {
            if ( $( '#rs_show_hide_maximum_cart_total_earn_error_message' ).val() == '1' ) {
                $( '#rs_max_cart_total_for_earning_error_message' ).closest( 'tr' ).show() ;
            } else {
                $( '#rs_max_cart_total_for_earning_error_message' ).closest( 'tr' ).hide() ;
            }
        } ,
        total_points_that_can_earn_in_cart : function () {
            ProductPurchaseScripts.show_or_hide_for_total_points_that_can_earn_in_cart() ;
        } ,
        show_or_hide_for_total_points_that_can_earn_in_cart : function () {
            if ( $( '#rs_show_hide_total_points_cart_field' ).val() == '1' ) {
                $( '#rs_total_earned_point_caption' ).closest( 'tr' ).show() ;
                $( '#rs_show_hide_equivalent_price_for_points_cart' ).closest( 'tr' ).show() ;
                $( '#rs_select_type_for_cart' ).closest( 'tr' ).show() ;
                $( '#rs_show_hide_custom_msg_for_points_cart' ).closest( 'tr' ).show() ;
                if ( $( '#rs_show_hide_custom_msg_for_points_cart' ).val() == '1' ) {
                    $( '#rs_custom_message_for_points_cart' ).closest( 'tr' ).show() ;
                } else {
                    $( '#rs_custom_message_for_points_cart' ).closest( 'tr' ).hide() ;
                }
            } else {
                $( '#rs_total_earned_point_caption' ).closest( 'tr' ).hide() ;
                $( '#rs_show_hide_equivalent_price_for_points_cart' ).closest( 'tr' ).hide() ;
                $( '#rs_select_type_for_cart' ).closest( 'tr' ).hide() ;
                $( '#rs_show_hide_custom_msg_for_points_cart' ).closest( 'tr' ).hide() ;
                $( '#rs_custom_message_for_points_cart' ).closest( 'tr' ).hide() ;
            }
        } ,
        total_points_that_can_earn_in_checkout : function () {
            ProductPurchaseScripts.show_or_hide_for_total_points_that_can_earn_in_checkout() ;
        } ,
        show_or_hide_for_total_points_that_can_earn_in_checkout : function () {
            if ( $( '#rs_show_hide_total_points_checkout_field' ).val() == '1' ) {
                $( '#rs_total_earned_point_caption_checkout' ).closest( 'tr' ).show() ;
                $( '#rs_show_hide_equivalent_price_for_points' ).closest( 'tr' ).show() ;
                $( '#rs_select_type_for_checkout' ).closest( 'tr' ).show() ;

                $( '#rs_show_hide_custom_msg_for_points_checkout' ).closest( 'tr' ).show() ;
                if ( $( '#rs_show_hide_custom_msg_for_points_checkout' ).val() == '1' ) {
                    $( '#rs_custom_message_for_points_checkout' ).closest( 'tr' ).show() ;
                } else {
                    $( '#rs_custom_message_for_points_checkout' ).closest( 'tr' ).hide() ;
                }
            } else {
                $( '#rs_total_earned_point_caption_checkout' ).closest( 'tr' ).hide() ;
                $( '#rs_show_hide_equivalent_price_for_points' ).closest( 'tr' ).hide() ;
                $( '#rs_select_type_for_checkout' ).closest( 'tr' ).hide() ;
                $( '#rs_show_hide_custom_msg_for_points_checkout' ).closest( 'tr' ).hide() ;
                $( '#rs_custom_message_for_points_checkout' ).closest( 'tr' ).hide() ;
            }
        } ,
        cart_custom_message : function () {
            ProductPurchaseScripts.show_or_hide_for_cart_custom_message() ;
        } ,
        show_or_hide_for_cart_custom_message : function () {
            if ( $( '#rs_show_hide_total_points_cart_field' ).val() == '1' ) {
                if ( $( '#rs_show_hide_custom_msg_for_points_cart' ).val() == '1' ) {
                    $( '#rs_custom_message_for_points_cart' ).closest( 'tr' ).show() ;
                } else {
                    $( '#rs_custom_message_for_points_cart' ).closest( 'tr' ).hide() ;
                }
            }
        } ,
        checkout_custom_message : function () {
            ProductPurchaseScripts.show_or_hide_for_checkout_custom_message() ;
        } ,
        show_or_hide_for_checkout_custom_message : function () {
            if ( $( '#rs_show_hide_total_points_checkout_field' ).val() == '1' ) {
                if ( $( '#rs_show_hide_custom_msg_for_points_checkout' ).val() == '1' ) {
                    $( '#rs_custom_message_for_points_checkout' ).closest( 'tr' ).show() ;
                } else {
                    $( '#rs_custom_message_for_points_checkout' ).closest( 'tr' ).hide() ;
                }
            }
        } ,
        thankyou_custom_message : function () {
            ProductPurchaseScripts.show_or_hide_for_thankyou_custom_message() ;
        } ,
        show_or_hide_for_thankyou_custom_message : function () {
            if ( $( '#rs_show_hide_custom_msg_for_points_thankyou' ).val() == '1' ) {
                $( '#rs_custom_message_for_points_thankyou' ).closest( 'tr' ).show() ;
            } else {
                $( '#rs_custom_message_for_points_thankyou' ).closest( 'tr' ).hide() ;
            }
        } ,
        enable_earn_point_msg_in_edit_order_page : function () {
            ProductPurchaseScripts.show_or_hide_for_enable_earn_point_msg_in_edit_order_page() ;
        } ,
        show_or_hide_for_enable_earn_point_msg_in_edit_order_page : function () {
            if ( $( '#rs_enable_msg_for_earned_points' ).is( ":checked" ) ) {
                $( '#rs_msg_for_earned_points' ).parent().parent().show() ;
            } else {
                $( '#rs_msg_for_earned_points' ).parent().parent().hide() ;
            }
        } ,
        product_category_selection : function () {
            ProductPurchaseScripts.show_or_hide_for_product_category_selection() ;
        } ,
        show_or_hide_for_product_category_selection : function () {
            if ( ( $( '.rs_which_product_selection' ).val() === '1' ) ) {
                $( '#rs_select_particular_products' ).parent().parent().hide() ;
                $( '#rs_select_particular_categories' ).parent().parent().hide() ;
            } else if ( $( '.rs_which_product_selection' ).val() === '2' ) {
                $( '#rs_select_particular_products' ).parent().parent().show() ;
                $( '#rs_select_particular_categories' ).parent().parent().hide() ;
            } else if ( $( '.rs_which_product_selection' ).val() === '3' ) {
                $( '#rs_select_particular_products' ).parent().parent().hide() ;
                $( '#rs_select_particular_categories' ).parent().parent().hide() ;
            } else {
                $( '#rs_select_particular_categories' ).parent().parent().show() ;
                $( '#rs_select_particular_products' ).parent().parent().hide() ;
            }
        } ,
        bulk_update_points_for_product_purchase : function () {
            var rsconfirm = confirm( "It is strongly recommended that you do not reload or refresh page. Are you sure you wish to update now?" ) ;
            if ( rsconfirm === true ) {
                ProductPurchaseScripts.block( '.rs_hide_bulk_update_for_product_purchase_start' ) ;
                var dataparam = {
                    action : 'bulk_update_points_for_product' ,
                    sumo_security : fp_product_purchase_module_param.product_purchase_bulk_update ,
                    productselection : $( '#rs_which_product_selection' ).val() ,
                    enablereward : $( '#rs_local_enable_disable_reward' ).val() ,
                    selectedproducts : $( '#rs_select_particular_products' ).val() ,
                    selectedcategories : $( '#rs_select_particular_categories' ).val() ,
                    rewardtype : $( '#rs_local_reward_type' ).val() ,
                    rewardpoints : $( '#rs_local_reward_points' ).val() ,
                    rewardpercent : $( '#rs_local_reward_percent' ).val() ,
                    enablereferralreward : $( '#rs_local_enable_disable_referral_reward' ).val() ,
                    referralrewardtype : $( '#rs_local_referral_reward_type' ).val() ,
                    referralrewardpoint : $( '#rs_local_referral_reward_point' ).val() ,
                    referralrewardpercent : $( '#rs_local_referral_reward_percent' ).val() ,
                    referralrewardtypegettingrefer : $( '#rs_local_referral_reward_type_get_refer' ).val() ,
                    referralpointforgettingrefer : $( '#rs_local_referral_reward_point_for_getting_referred' ).val() ,
                    referralrewardpercentgettingrefer : $( '#rs_local_referral_reward_percent_for_getting_referred' ).val() ,
                } ;
                $.post( fp_product_purchase_module_param.ajaxurl , dataparam , function ( response ) {
                    if ( true === response.success ) {
                        window.location.href = fp_product_purchase_module_param.redirecturl ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                    ProductPurchaseScripts.unblock( '.rs_hide_bulk_update_for_product_purchase_start' ) ;
                } ) ;
            }
            return false ;
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
    ProductPurchaseScripts.init() ;
} ) ;