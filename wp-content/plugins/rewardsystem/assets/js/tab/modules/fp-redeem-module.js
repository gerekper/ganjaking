/*
 * Redeeming Points - Module
 */
jQuery( function ( $ ) {
    var RedeemingPointsModule = {
        init : function () {
            this.trigger_on_page_load() ;
            this.validation_for_min_and_max_value() ;
            this.show_or_hide_for_enable_userrole_based_redeem() ;
            this.show_or_hide_for_enable_redeem_level_based_reward() ;
            this.show_or_hide_for_enable_purchase_history_based_reward() ;
            this.show_or_hide_for_enable_auto_redeeming() ;
            this.show_or_hide_for_redeeming_field_type() ;
            this.show_or_hide_for_redeeming_field_type_in_checkout() ;
            this.show_hide_redeem_field_checkout() ;
            this.show_or_hide_for_redeem_caption() ;
            this.show_or_hide_for_redeem_placeholder() ;
            this.show_or_hide_for_coupon_field() ;
            this.show_or_hide_for_prevent_coupon_usage() ;
            this.show_or_hide_for_max_redeem_discount_type() ;
            this.show_or_hide_for_redeem_min_point_required_error_msg() ;
            this.show_or_hide_for_redeem_min_point_required_error_msg_after_first_time() ;
            this.show_or_hide_for_min_cart_total_error_msg() ;
            this.show_or_hide_for_max_cart_total_error_msg() ;
            this.show_or_hide_for_enable_msg_for_redeem_points() ;
            this.show_or_hide_for_points_empty_error_msg() ;
            this.show_or_hide_for_auto_redeem_not_applicable_err_msg() ;
            $( document ).on( 'change' , '#rs_enable_user_role_based_reward_points_for_redeem' , this.enable_userrole_based_redeem ) ;
            $( document ).on( 'change' , '#rs_enable_redeem_level_based_reward_points' , this.enable_redeem_level_based_reward ) ;
            $( document ).on( 'change' , '#rs_enable_user_purchase_history_based_reward_points_redeem' , this.enable_purchase_history_based_reward ) ;
            $( document ).on( 'change' , '#rs_enable_disable_auto_redeem_points' , this.enable_auto_redeeming ) ;
            $( document ).on( 'change' , '#rs_redeem_field_type_option' , this.redeeming_field_type ) ;
            $( document ).on( 'change' , '#rs_redeem_field_type_option_checkout' , this.redeeming_field_type_in_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_redeem_field_checkout' , this.redeem_field_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_redeem_caption' , this.redeem_caption ) ;
            $( document ).on( 'change' , '#rs_show_hide_redeem_placeholder' , this.redeem_placeholder ) ;
            $( document ).on( 'change' , '#rs_show_hide_redeem_field' , this.coupon_field ) ;
            $( document ).on( 'change' , '#rs_coupon_applied_individual' , this.prevent_coupon_usage ) ;
            $( document ).on( 'change' , '#rs_max_redeem_discount' , this.max_redeem_discount_type ) ;
            $( document ).on( 'change' , '#rs_show_hide_first_redeem_error_message' , this.redeem_min_point_required_error_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_after_first_redeem_error_message' , this.redeem_min_point_required_error_msg_after_first_time ) ;
            $( document ).on( 'change' , '#rs_show_hide_minimum_cart_total_error_message' , this.min_cart_total_error_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_maximum_cart_total_error_message' , this.max_cart_total_error_msg ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_redeem_points' , this.enable_msg_for_redeem_points ) ;
            $( document ).on( 'change' , '#rs_show_hide_points_empty_error_message' , this.points_empty_error_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_auto_redeem_not_applicable' , this.auto_redeem_not_applicable_err_msg ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_redeem_module_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_select_product_for_purchase_using_points' ).chosen() ;
                $( '#rs_ajax_chosen_select_products_redeem' ).chosen() ;
                $( '#rs_select_category_to_enable_redeeming' ).chosen() ;
                $( '#rs_exclude_category_to_enable_redeeming' ).chosen() ;
                $( '#rs_select_category_for_purchase_using_points' ).chosen() ;
                $( '#rs_order_status_control_redeem' ).chosen() ;
                $( '#rs_select_product_for_hide_gateway' ).chosen() ;
                $( '#rs_select_product_for_hide_gateway' ).chosen() ;
                $( '#rs_select_product_for_hide_gateway' ).chosen() ;
            } else {
                $( '#rs_select_product_for_purchase_using_points' ).select2() ;
                $( '#rs_ajax_chosen_select_products_redeem' ).select2() ;
                $( '#rs_select_category_to_enable_redeeming' ).select2() ;
                $( '#rs_exclude_category_to_enable_redeeming' ).select2() ;
                $( '#rs_select_category_for_purchase_using_points' ).select2() ;
                $( '#rs_order_status_control_redeem' ).select2() ;
                $( '#rs_select_product_for_hide_gateway' ).select2() ;
                $( '#rs_select_product_for_hide_gateway' ).select2() ;
                $( '#rs_select_product_for_hide_gateway' ).select2() ;
            }
        } ,
        validation_for_min_and_max_value : function () {
            jQuery( document ).ready( function ( ) {
                jQuery( '#rs_maximum_cart_total_points' ).keyup( function ( ) {
                    var maximum_cart_total_redeem = jQuery( '#rs_maximum_cart_total_points' ).val( ) ;
                    jQuery( '#rs_maximum_cart_total_points' ).val( maximum_cart_total_redeem ) ;
                } ) ;
                jQuery( '#rs_minimum_cart_total_points' ).keyup( function ( ) {
                    var mimimum_cart_total_redeem = jQuery( '#rs_minimum_cart_total_points' ).val( ) ;
                    jQuery( '#rs_minimum_cart_total_points' ).val( mimimum_cart_total_redeem ) ;
                } ) ;
                jQuery( '#rs_maximum_cart_total_for_earning' ).keyup( function ( ) {
                    var maximum_cart_total_earn = jQuery( '#rs_maximum_cart_total_for_earning' ).val( ) ;
                    jQuery( '#rs_maximum_cart_total_for_earning' ).val( maximum_cart_total_earn ) ;
                } ) ;
                jQuery( '#rs_minimum_cart_total_for_earning' ).keyup( function ( ) {
                    var mimimum_cart_total_earn = jQuery( '#rs_minimum_cart_total_for_earning' ).val( ) ;
                    jQuery( '#rs_minimum_cart_total_for_earning' ).val( mimimum_cart_total_earn ) ;
                } ) ;
                jQuery( '.button-primary' ).click( function ( e ) {
                    if ( jQuery( '#rs_maximum_cart_total_points' ).val( ) != '' && jQuery( '#rs_minimum_cart_total_points' ).val( ) != '' ) {
                        var maximum_cart_total_redeem = Number( jQuery( '#rs_maximum_cart_total_points' ).val( ) ) ;
                        var mimimum_cart_total_redeem = Number( jQuery( '#rs_minimum_cart_total_points' ).val( ) ) ;
                        if ( maximum_cart_total_redeem < mimimum_cart_total_redeem ) {
                            e.preventDefault( ) ;
                            jQuery( '#rs_maximum_cart_total_points' ).focus( ) ;
                            jQuery( "#rs_maximum_cart_total_points" ).after( "<div class='validation' style='color:red;margin-bottom: 20px;'>Please enter cart total greater than mimimum cart total for redeem points</div>" ) ;
                        }
                    }
                    if ( jQuery( '#rs_maximum_cart_total_for_earning' ).val( ) != '' && jQuery( '#rs_minimum_cart_total_for_earning' ).val( ) != '' ) {
                        var maximum_cart_total_redeem = Number( jQuery( '#rs_maximum_cart_total_for_earning' ).val( ) ) ;
                        var mimimum_cart_total_redeem = Number( jQuery( '#rs_minimum_cart_total_for_earning' ).val( ) ) ;
                        if ( maximum_cart_total_redeem < mimimum_cart_total_redeem ) {
                            e.preventDefault( ) ;
                            jQuery( '#rs_maximum_cart_total_for_earning' ).focus( ) ;
                            jQuery( "#rs_maximum_cart_total_for_earning" ).after( "<div class='validation1' style='color:red;margin-bottom: 20px;'>Please enter cart total greater than mimimum cart total for earn points</div>" ) ;
                        }
                    }
                    jQuery( '#rs_maximum_cart_total_points' ).keyup( function ( ) {
                        jQuery( ".validation" ).hide( ) ;
                    } ) ;
                    jQuery( '#rs_maximum_cart_total_for_earning' ).keyup( function ( ) {
                        jQuery( ".validation1" ).hide( ) ;
                    } ) ;
                } ) ;
            } ) ;
        } ,
        enable_userrole_based_redeem : function () {
            RedeemingPointsModule.show_or_hide_for_enable_userrole_based_redeem() ;
        } ,
        show_or_hide_for_enable_userrole_based_redeem : function () {
            if ( jQuery( '#rs_enable_user_role_based_reward_points_for_redeem' ).is( ':checked' ) ) {
                jQuery( '.rewardpoints_userrole_for_redeem' ).parent().parent().show() ;
            } else {
                jQuery( '.rewardpoints_userrole_for_redeem' ).parent().parent().hide() ;
            }
        } ,
        enable_redeem_level_based_reward : function () {
            RedeemingPointsModule.show_or_hide_for_enable_redeem_level_based_reward() ;
        } ,
        show_or_hide_for_enable_redeem_level_based_reward : function () {
            if ( jQuery( '#rs_enable_redeem_level_based_reward_points' ).is( ':checked' ) ) {
                jQuery( '.rsdynamicrulecreation_for_redeem' ).parent().show() ;
                jQuery( '#rs_select_redeem_points_based_on' ).parent().parent().show() ;
            } else {
                jQuery( '.rsdynamicrulecreation_for_redeem' ).parent().hide() ;
                jQuery( '#rs_select_redeem_points_based_on' ).parent().parent().hide() ;
            }
        } ,
        enable_purchase_history_based_reward : function () {
            RedeemingPointsModule.show_or_hide_for_enable_purchase_history_based_reward() ;
        } ,
        show_or_hide_for_enable_purchase_history_based_reward : function () {
            if ( jQuery( '#rs_enable_user_purchase_history_based_reward_points_redeem' ).is( ':checked' ) ) {
                jQuery( '.rsdynamicrulecreationsforuserpurchasehistory_redeeming' ).show() ;
            } else {
                jQuery( '.rsdynamicrulecreationsforuserpurchasehistory_redeeming' ).hide() ;
            }
        } ,
        enable_auto_redeeming : function () {
            RedeemingPointsModule.show_or_hide_for_enable_auto_redeeming() ;
        } ,
        show_or_hide_for_enable_auto_redeeming : function () {
            if ( jQuery( '#rs_enable_disable_auto_redeem_points' ).is( ':checked' ) ) {
                jQuery( '#rs_percentage_cart_total_auto_redeem' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_percentage_cart_total_auto_redeem' ).parent().parent().hide() ;
            }
        } ,
        redeeming_field_type : function () {
            RedeemingPointsModule.show_or_hide_for_redeeming_field_type() ;
        } ,
        show_or_hide_for_redeeming_field_type : function () {
            if ( jQuery( '#rs_redeem_field_type_option' ).val() == '1' ) {
                jQuery( '#rs_percentage_cart_total_redeem' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_button_option_message' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '#rs_percentage_cart_total_redeem' ).parent().parent().show() ;
                jQuery( '#rs_redeeming_button_option_message' ).closest( 'tr' ).show() ;
            }
        } ,
        redeeming_field_type_in_checkout : function () {
            RedeemingPointsModule.show_or_hide_for_redeeming_field_type_in_checkout() ;
        } ,
        show_or_hide_for_redeeming_field_type_in_checkout : function () {
            if ( jQuery( '#rs_redeem_field_type_option_checkout' ).val() == '1' ) {
                jQuery( '#rs_percentage_cart_total_redeem_checkout' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_percentage_cart_total_redeem_checkout' ).parent().parent().show() ;
            }
        } ,
        redeem_field_checkout : function () {
            RedeemingPointsModule.show_hide_redeem_field_checkout() ;
        } ,
        show_hide_redeem_field_checkout : function () {
            if ( jQuery( '#rs_show_hide_redeem_field_checkout' ).val() == '1' ) {
                jQuery( '#rs_redeem_field_type_option_checkout' ).parent().parent().show() ;
                RedeemingPointsModule.show_or_hide_for_redeeming_field_type_in_checkout() ;
            } else {
                jQuery( '#rs_redeem_field_type_option_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_percentage_cart_total_redeem_checkout' ).parent().parent().hide() ;
            }
        } ,
        redeem_caption : function () {
            RedeemingPointsModule.show_or_hide_for_redeem_caption() ;
        } ,
        show_or_hide_for_redeem_caption : function () {
            if ( jQuery( '#rs_show_hide_redeem_caption' ).val() == '1' ) {
                jQuery( '#rs_redeem_field_caption' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_redeem_field_caption' ).parent().parent().hide() ;
            }
        } ,
        redeem_placeholder : function () {
            RedeemingPointsModule.show_or_hide_for_redeem_placeholder() ;
        } ,
        show_or_hide_for_redeem_placeholder : function () {
            if ( jQuery( '#rs_show_hide_redeem_placeholder' ).val() == '1' ) {
                jQuery( '#rs_redeem_field_placeholder' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_redeem_field_placeholder' ).parent().parent().hide() ;
            }
        } ,
        coupon_field : function () {
            RedeemingPointsModule.show_or_hide_for_coupon_field() ;
        } ,
        show_or_hide_for_coupon_field : function () {
            var currentvalue = jQuery( '#rs_show_hide_redeem_field' ).val() ;
            if ( currentvalue === '1' || currentvalue == '2' ) {
                jQuery( '#rs_hide_redeeming_field' ).closest( 'tr' ).show() ;
                jQuery( '#rs_enable_redeem_for_selected_products' ).parent().parent().parent().parent().show() ;
                jQuery( '#rs_exclude_products_for_redeeming' ).parent().parent().parent().parent().show() ;
                jQuery( '#rs_enable_redeem_for_selected_category' ).parent().parent().parent().parent().show() ;
                jQuery( '#rs_exclude_category_for_redeeming' ).parent().parent().parent().parent().show() ;
                jQuery( '#rs_show_redeeming_field' ).parent().parent().show() ;
                var enable_selected_product_checkbox = jQuery( '#rs_enable_redeem_for_selected_products' ).is( ':checked' ) ? 'yes' : 'no' ;
                var enable_exclude_product_checkbox = jQuery( '#rs_exclude_products_for_redeeming' ).is( ':checked' ) ? 'yes' : 'no' ;
                var enable_selected_category_checkbox = jQuery( '#rs_enable_redeem_for_selected_category' ).is( ':checked' ) ? 'yes' : 'no' ;
                var enable_exclude_category_checkbox = jQuery( '#rs_exclude_category_for_redeeming' ).is( ':checked' ) ? 'yes' : 'no' ;
                if ( enable_selected_product_checkbox === 'yes' ) {
                    jQuery( '#rs_select_products_to_enable_redeeming' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_select_products_to_enable_redeeming' ).parent().parent().hide() ;
                }
                if ( enable_exclude_product_checkbox === 'yes' ) {
                    jQuery( '#rs_exclude_products_to_enable_redeeming' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_exclude_products_to_enable_redeeming' ).parent().parent().hide() ;
                }
                if ( enable_selected_category_checkbox === 'yes' ) {
                    jQuery( '#rs_select_category_to_enable_redeeming' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_select_category_to_enable_redeeming' ).parent().parent().hide() ;
                }
                if ( enable_exclude_category_checkbox === 'yes' ) {
                    jQuery( '#rs_exclude_category_to_enable_redeeming' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_exclude_category_to_enable_redeeming' ).parent().parent().hide() ;
                }

                jQuery( '#rs_restrict_sale_price_for_redeeming' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_restrict_sale_price_for_redeeming' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_redeeming_message_restrict_for_sale_price_product' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_redeeming_message_restrict_for_sale_price_product' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_restrict_sale_price_for_redeeming' ).change( function () {
                    if ( jQuery( '#rs_restrict_sale_price_for_redeeming' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_redeeming_message_restrict_for_sale_price_product' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_redeeming_message_restrict_for_sale_price_product' ).closest( 'tr' ).hide() ;
                    }
                } ) ;

                //When enabling the product and category
                jQuery( '#rs_enable_redeem_for_selected_products' ).click( function () {
                    var enable_redeem_for_selected_product = jQuery( '#rs_enable_redeem_for_selected_products' ).is( ':checked' ) ? 'yes' : 'no' ;
                    if ( enable_redeem_for_selected_product == 'yes' ) {
                        jQuery( '#rs_select_products_to_enable_redeeming' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_select_products_to_enable_redeeming' ).parent().parent().hide() ;
                    }
                } ) ;
                jQuery( '#rs_exclude_products_for_redeeming' ).click( function () {
                    var enable_exclude_product_checkbox = jQuery( '#rs_exclude_products_for_redeeming' ).is( ':checked' ) ? 'yes' : 'no' ;
                    if ( enable_exclude_product_checkbox == 'yes' ) {
                        jQuery( '#rs_exclude_products_to_enable_redeeming' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_exclude_products_to_enable_redeeming' ).parent().parent().hide() ;
                    }
                } ) ;
                jQuery( '#rs_enable_redeem_for_selected_category' ).click( function () {
                    var enable_selected_category_checkbox = jQuery( '#rs_enable_redeem_for_selected_category' ).is( ':checked' ) ? 'yes' : 'no' ;
                    if ( enable_selected_category_checkbox == 'yes' ) {
                        jQuery( '#rs_select_category_to_enable_redeeming' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_select_category_to_enable_redeeming' ).parent().parent().hide() ;
                    }
                } ) ;
                jQuery( '#rs_exclude_category_for_redeeming' ).click( function () {
                    var enable_exclude_category_checkbox = jQuery( '#rs_exclude_category_for_redeeming' ).is( ':checked' ) ? 'yes' : 'no' ;
                    if ( enable_exclude_category_checkbox == 'yes' ) {
                        jQuery( '#rs_exclude_category_to_enable_redeeming' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_exclude_category_to_enable_redeeming' ).parent().parent().hide() ;
                    }
                } ) ;
                if ( currentvalue === '2' ) {
                    jQuery( '#rs_show_redeeming_field' ).parent().parent().show() ;
                }
            } else {
                jQuery( '#rs_enable_redeem_for_selected_products' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_exclude_products_for_redeeming' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_enable_redeem_for_selected_category' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_exclude_category_for_redeeming' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_select_products_to_enable_redeeming' ).parent().parent().hide() ;
                jQuery( '#rs_exclude_products_to_enable_redeeming' ).parent().parent().hide() ;
                jQuery( '#rs_select_category_to_enable_redeeming' ).parent().parent().hide() ;
                jQuery( '#rs_exclude_category_to_enable_redeeming' ).parent().parent().hide() ;
                jQuery( '#rs_show_redeeming_field' ).parent().parent().hide() ;
                jQuery( '#rs_hide_redeeming_field' ).closest( 'tr' ).hide() ;
                if ( currentvalue === '5' ) {
                    jQuery( '#rs_show_redeeming_field' ).parent().parent().show() ;
                }
                jQuery( '#rs_restrict_sale_price_for_redeeming' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_redeeming_message_restrict_for_sale_price_product' ).closest( 'tr' ).hide() ;
            }
        } ,
        prevent_coupon_usage : function () {
            RedeemingPointsModule.show_or_hide_for_prevent_coupon_usage() ;
        } ,
        show_or_hide_for_prevent_coupon_usage : function () {
            if ( jQuery( '#rs_coupon_applied_individual' ).is( ':checked' ) == true ) {
                jQuery( '#rs_coupon_applied_individual_error_msg' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_coupon_applied_individual_error_msg' ).closest( 'tr' ).hide() ;
            }
        } ,
        max_redeem_discount_type : function () {
            RedeemingPointsModule.show_or_hide_for_max_redeem_discount_type() ;
        } ,
        show_or_hide_for_max_redeem_discount_type : function () {
            if ( jQuery( '#rs_max_redeem_discount' ).val() == '1' ) {
                jQuery( '#rs_fixed_max_redeem_discount' ).parent().parent().show() ;
                jQuery( '#rs_percent_max_redeem_discount' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_fixed_max_redeem_discount' ).parent().parent().hide() ;
                jQuery( '#rs_percent_max_redeem_discount' ).parent().parent().show() ;
            }
        } ,
        redeem_min_point_required_error_msg : function () {
            RedeemingPointsModule.show_or_hide_for_redeem_min_point_required_error_msg() ;
        } ,
        show_or_hide_for_redeem_min_point_required_error_msg : function () {
            if ( jQuery( '#rs_show_hide_first_redeem_error_message' ).val() == '1' ) {
                jQuery( '#rs_min_points_first_redeem_error_message' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_min_points_first_redeem_error_message' ).parent().parent().hide() ;
            }
        } ,
        redeem_min_point_required_error_msg_after_first_time : function () {
            RedeemingPointsModule.show_or_hide_for_redeem_min_point_required_error_msg_after_first_time() ;
        } ,
        show_or_hide_for_redeem_min_point_required_error_msg_after_first_time : function () {
            if ( jQuery( '#rs_show_hide_after_first_redeem_error_message' ).val() == '1' ) {
                jQuery( '#rs_min_points_after_first_error' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_min_points_after_first_error' ).parent().parent().hide() ;
            }
        } ,
        min_cart_total_error_msg : function () {
            RedeemingPointsModule.show_or_hide_for_min_cart_total_error_msg() ;
        } ,
        show_or_hide_for_min_cart_total_error_msg : function () {
            if ( jQuery( '#rs_show_hide_minimum_cart_total_error_message' ).val() == '1' ) {
                jQuery( '#rs_min_cart_total_redeem_error' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_min_cart_total_redeem_error' ).parent().parent().hide() ;
            }
        } ,
        max_cart_total_error_msg : function () {
            RedeemingPointsModule.show_or_hide_for_max_cart_total_error_msg() ;
        } ,
        show_or_hide_for_max_cart_total_error_msg : function () {
            if ( jQuery( '#rs_show_hide_maximum_cart_total_error_message' ).val() == '1' ) {
                jQuery( '#rs_max_cart_total_redeem_error' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_max_cart_total_redeem_error' ).closest( 'tr' ).hide() ;
            }
        } ,
        enable_msg_for_redeem_points : function () {
            RedeemingPointsModule.show_or_hide_for_enable_msg_for_redeem_points() ;
        } ,
        show_or_hide_for_enable_msg_for_redeem_points : function () {
            if ( jQuery( '#rs_enable_msg_for_redeem_points' ).is( ":checked" ) ) {
                jQuery( '#rs_msg_for_redeem_points' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_msg_for_redeem_points' ).parent().parent().hide() ;
            }
        } ,
        points_empty_error_msg : function () {
            RedeemingPointsModule.show_or_hide_for_points_empty_error_msg() ;
        } ,
        show_or_hide_for_points_empty_error_msg : function () {
            if ( jQuery( '#rs_show_hide_points_empty_error_message' ).val() == '1' ) {
                jQuery( '#rs_current_points_empty_error_message' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_current_points_empty_error_message' ).parent().parent().hide() ;
            }
        } ,
        auto_redeem_not_applicable_err_msg : function () {
            RedeemingPointsModule.show_or_hide_for_auto_redeem_not_applicable_err_msg() ;
        } ,
        show_or_hide_for_auto_redeem_not_applicable_err_msg : function () {
            if ( jQuery( '#rs_show_hide_auto_redeem_not_applicable' ).val() == '1' ) {
                jQuery( '#rs_auto_redeem_not_applicable_error_message' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_auto_redeem_not_applicable_error_message' ).parent().parent().hide() ;
            }
        } ,
    } ;
    RedeemingPointsModule.init() ;
} ) ;