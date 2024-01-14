/*
 * Redeeming Points - Module
 */
jQuery( function ( $ ) {
    'use strict' ;
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
            this.show_or_hide_for_redeem_min_point_required_error_msg() ;
            this.show_or_hide_for_redeem_min_point_required_error_msg_after_first_time() ;
            this.show_or_hide_for_min_cart_total_error_msg() ;
            this.show_or_hide_for_max_cart_total_error_msg() ;
            this.show_or_hide_for_enable_msg_for_redeem_points() ;
            this.show_or_hide_for_points_empty_error_msg() ;
            this.show_or_hide_for_auto_redeem_not_applicable_err_msg() ;
            this.show_or_hide_for_min_available_points_based_user_role() ;
            this.show_or_hide_for_available_points_based_redeem() ;
            $( document ).on( 'change' , '#rs_enable_user_role_based_reward_points_for_redeem' , this.enable_userrole_based_redeem ) ;
            $( document ).on( 'change' , '#rs_enable_redeem_level_based_reward_points' , this.enable_redeem_level_based_reward ) ;
            $( document ).on( 'change' , '#rs_enable_user_purchase_history_based_reward_points_redeem' , this.enable_purchase_history_based_reward ) ;
            $( document ).on( 'change' , '#rs_enable_disable_auto_redeem_points' , this.enable_auto_redeeming ) ;
            $( document ).on( 'change' , '#rs_redeem_field_type_option' , this.redeeming_field_type ) ;
            $( document ).on( 'change' , '#rs_redeem_field_type_option_checkout' , this.redeeming_field_type_in_checkout ) ;
            $( document ).on( 'change' , '#rs_default_redeeming_type_enabled' , this.toggle_default_redeeming_type_checkbox ) ;
            $( document ).on( 'change' , '#rs_default_redeeming_type' , this.toggle_default_redeeming_type ) ;
            $( document ).on( 'change' , '#rs_default_redeeming_type_enabled_checkout' , this.toggle_default_redeeming_type_checkout_checkbox ) ;
            $( document ).on( 'change' , '#rs_default_redeeming_type_checkout' , this.toggle_default_redeeming_type_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_redeem_field_checkout' , this.redeem_field_checkout ) ;
            $( document ).on( 'change' , '#rs_show_hide_redeem_caption' , this.redeem_caption ) ;
            $( document ).on( 'change' , '#rs_show_hide_redeem_placeholder' , this.redeem_placeholder ) ;
            $( document ).on( 'change' , '#rs_show_hide_redeem_field' , this.coupon_field ) ;
            $( document ).on( 'change' , '#rs_max_redeem_discount' , this.max_redeem_discount_type ) ;
            $( document ).on( 'change' , '#rs_show_hide_first_redeem_error_message' , this.redeem_min_point_required_error_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_after_first_redeem_error_message' , this.redeem_min_point_required_error_msg_after_first_time ) ;
            $( document ).on( 'change' , '#rs_show_hide_minimum_cart_total_error_message' , this.min_cart_total_error_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_maximum_cart_total_error_message' , this.max_cart_total_error_msg ) ;
            $( document ).on( 'change' , '#rs_enable_msg_for_redeem_points' , this.enable_msg_for_redeem_points ) ;
            $( document ).on( 'change' , '#rs_show_hide_points_empty_error_message' , this.points_empty_error_msg ) ;
            $( document ).on( 'change' , '#rs_show_hide_auto_redeem_not_applicable' , this.auto_redeem_not_applicable_err_msg ) ;
            $( document ).on( 'change' , '#rs_minimum_available_points_based_on' , this.min_available_points_based_user_role ) ;
            $( document ).on( 'change' , '#rs_minimum_available_points_restriction_is_enabled' , this.redeeming_restriction_based_available_points ) ;

            // Redeeming Percentage Rule.
            $( document ).on( 'click' , '.rs-add-redeeming-percentage-rule' , this.add_rule_for_redeeming_percentage ) ;
            $( document ).on( 'click' , '.rs-remove-redeeming-percentage-rule' , this.remove_rule_for_redeeming_percentage ) ;

            // Redeeming Purchase History Rule.
            $( document ).on( 'click' , '.rs-add-redeeming-user-purchase-history-rule' , this.add_rule_for_redeeming_purchase_history ) ;
            $( document ).on( 'click' , '.rs-remove-redeeming-user-purchase-history-rule' , this.remove_rule_for_redeeming_purchase_history ) ;
            
            $( document ).on('change','#rs_maximum_redeeming_per_day_restriction_enabled',this.max_redeeming_per_day_restriction_checkbox);
            $('#rs_maximum_redeeming_per_day_restriction_enabled').change();
            $( document ).on( 'click' , '.rs_sumo_reward_button' , this.bulk_update_points_for_redeeming_points ) ;
            $( document ).on( 'change' , '#rs_select_redeeming_based_on' , this.redeeming_based_on ) ;
            $( document ).on( 'change' , '#rs_enable_bulk_update_for_product_level_redeeming' , this.enable_bulk_update_maximum_redeem ) ;
            $( document ).on( 'change' , '#rs_product_level_redeem_product_selection_type' , this.bulk_update_product_selection_type ) ;
            $( document ).on( 'change' , '#rs_enable_maximum_redeeming_points' , this.enable_maximum_redeem ) ;
            $(document).on('change', '#rs_enable_redeem_for_selected_products', this.toggle_redeeming_include_products);
            $(document).on('change', '#rs_exclude_products_for_redeeming', this.toggle_redeeming_exclude_products);
            $(document).on('change', '#rs_enable_redeem_for_selected_category', this.toggle_redeeming_include_category);
            $(document).on('change', '#rs_exclude_category_for_redeeming', this.toggle_redeeming_exclude_category);
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
                $( '.rs_select_payment_gateway_for_restrict_redeem_points' ).chosen() ;
                $( '.srp-rp-product-include-categories' ).chosen() ;
                $( '.srp-rp-product-exclude-categories' ).chosen() ;                
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
                $( '.rs_select_payment_gateway_for_restrict_redeem_points' ).select2() ;
                $( '.srp-rp-product-include-categories' ).select2() ;  
                $( '.srp-rp-product-exclude-categories' ).select2() ;                
            }
            
            $("#rs_order_status_control_redeem").next('.select2').css({"pointer-events": "none","opacity":"0.5"});

            RedeemingPointsModule.toggle_redeeming_based_on($('#rs_select_redeeming_based_on'));
            
        } ,

        redeeming_based_on : function(e){
            e.preventDefault();
            RedeemingPointsModule.toggle_redeeming_based_on(this);
        },
        
        toggle_redeeming_based_on : function (e) {
            if ( '1' === $(e).val() ){
                $('#rs_redeeming_message_for_product_level').closest('tr').show();
                $('#rs_error_msg_for_disabled_redeeming_products').closest('tr').show();
                $('#rs_enable_bulk_update_for_product_level_redeeming').closest('tr').show();
                RedeemingPointsModule.toggle_enable_bulk_update_maximum_redeem($('#rs_enable_bulk_update_for_product_level_redeeming'));
                $( '.srp-rp-cart-level-redeem' ).closest('tr').hide();
                
                if ( '2' === $('#rs_redeem_field_type_option').val()){
                    $( '#rs_percentage_cart_total_redeem' ).closest('tr').hide();
                }
                
                if ( '2' === $('#rs_redeem_field_type_option_checkout').val()){
                    $( '#rs_percentage_cart_total_redeem_checkout' ).closest('tr').hide();
                } 
                $( '#rs_select_products_to_enable_redeeming' ).closest('tr').hide();
                $( '#rs_exclude_products_to_enable_redeeming' ).closest('tr').hide();
                           
            } else {
                $('#rs_redeeming_message_for_product_level').closest('tr').hide();
                $('#rs_error_msg_for_disabled_redeeming_products').closest('tr').hide();
                $('#rs_enable_bulk_update_for_product_level_redeeming').closest('tr').hide();
                $('.srp-rp-bulk-update-opt').closest('tr').hide();
                $('.srp-rp-cart-level-redeem').closest('tr').show();

                if ( '2' === $('#rs_redeem_field_type_option').val()){
                    $( '#rs_percentage_cart_total_redeem' ).closest('tr').show();
                }
            
                if ( '2' === $('#rs_redeem_field_type_option_checkout').val()){
                    $( '#rs_percentage_cart_total_redeem_checkout' ).closest('tr').show();
                }

                RedeemingPointsModule.default_redeeming_type_checkbox($('#rs_default_redeeming_type_enabled'));
                RedeemingPointsModule.default_redeeming_type_checkout_checkbox($('#rs_default_redeeming_type_enabled_checkout')) ;
                RedeemingPointsModule.show_or_hide_for_max_redeem_discount_type() ;
                RedeemingPointsModule.show_or_hide_for_redeem_min_point_required_error_msg() ;
                RedeemingPointsModule.toggle_max_redeeming_per_day_restriction_checkbox($( '#rs_maximum_redeeming_per_day_restriction_enabled' ));
                RedeemingPointsModule.show_or_hide_for_min_cart_total_error_msg() ;
                RedeemingPointsModule.show_or_hide_for_max_cart_total_error_msg() ;
                RedeemingPointsModule.handle_redeeming_include_products('#rs_enable_redeem_for_selected_products') ;
                RedeemingPointsModule.handle_redeeming_exclude_products('#rs_exclude_products_for_redeeming') ;
                RedeemingPointsModule.handle_redeeming_include_category('#rs_enable_redeem_for_selected_category') ;
                RedeemingPointsModule.handle_redeeming_exclude_category('#rs_exclude_category_for_redeeming') ;
            }
        } ,

        enable_bulk_update_maximum_redeem : function(e){
            e.preventDefault();
            RedeemingPointsModule.toggle_enable_bulk_update_maximum_redeem(this);
        },
        
        toggle_enable_bulk_update_maximum_redeem : function (e) {
            if ( false === $(e).is(':checked') ){
                $(e).closest('div').find('.srp-rp-bulk-update-opt').closest('tr').hide();
            } else {
                $(e).closest('div').find('.srp-rp-bulk-update-opt').closest('tr').show();
                RedeemingPointsModule.toggle_bulk_update_product_selection_type($('#rs_product_level_redeem_product_selection_type'));
                RedeemingPointsModule.toggle_enable_maximum_redeem($('#rs_enable_maximum_redeeming_points'));
            } 
        } ,

        enable_maximum_redeem : function(e){
            e.preventDefault();
            RedeemingPointsModule.toggle_enable_maximum_redeem(this);
        },
        
        toggle_enable_maximum_redeem : function (e) {
           
            if ( '1' === $(e).val() ){
                $(e).closest('div').find('#rs_maximum_redeeming_points').closest('tr').show();
            } else {
                $(e).closest('div').find('#rs_maximum_redeeming_points').closest('tr').hide();
            } 
        } ,

        bulk_update_product_selection_type : function(e){
            e.preventDefault();
            RedeemingPointsModule.toggle_bulk_update_product_selection_type(this);
        },
        
        toggle_bulk_update_product_selection_type : function (e) {
            $(e).closest('div').find('.srp-rp-product-selection').closest('tr').hide();
            
            if ( '2' === $(e).val() ){
                $(e).closest('div').find('.srp-rp-product-include-products').closest('tr').show();
            } else if ( '3' === $(e).val() ){
                $(e).closest('div').find('.srp-rp-product-exclude-products').closest('tr').show();
            } else if ( '4' === $(e).val() ){
                $(e).closest('div').find('.srp-rp-product-include-categories').closest('tr').show();
            } else if ( '5' === $(e).val() ){
                $(e).closest('div').find('.srp-rp-product-exclude-categories').closest('tr').show();
            }
        } ,

        bulk_update_points_for_redeeming_points : function (e) {
            e.preventDefault();
            RedeemingPointsModule.block( '.rs_hide_bulk_update_for_redeeming_points_start' ) ;
            let data = {
                action : 'redeeming_points_bulk_update_action' ,
                sumo_security : fp_redeem_module_params.redeeming_points_bulk_update ,
                product_level_bulk_update_data : $(this).closest('div').find('checkbox,select,input' ).serialize(),
            } ;
            $.post( fp_redeem_module_params.ajaxurl , data , function ( res ) {
                if ( true === res.success ) {
                    window.location.href = res.data.redirect_url ;
                } else {
                    window.alert( res.data.error ) ;
                }
                RedeemingPointsModule.unblock( '.rs_hide_bulk_update_for_redeeming_points_start' ) ;
            } ) ;
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
            if ( $( '#rs_enable_disable_auto_redeem_points' ).is( ':checked' ) ) {
                if ( '2' === $( '#rs_select_redeeming_based_on' ).val() ){
                    $( '#rs_percentage_cart_total_auto_redeem' ).closest('tr').show() ;
                } else {
                    $( '#rs_percentage_cart_total_auto_redeem' ).closest('tr').hide() ;
                }
            } else {
                $( '#rs_percentage_cart_total_auto_redeem' ).closest('tr').hide() ;
            }
        } ,
        redeeming_field_type : function () {
            RedeemingPointsModule.show_or_hide_for_redeeming_field_type() ;
        } ,
        show_or_hide_for_redeeming_field_type : function () {
            if ( jQuery( '#rs_redeem_field_type_option' ).val() == '1' ) {
                jQuery( '#rs_percentage_cart_total_redeem' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_button_option_message' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_default_redeeming_type_enabled' ).closest( 'tr' ).show() ;
                RedeemingPointsModule.default_redeeming_type_checkbox($('#rs_default_redeeming_type_enabled')) ;
                RedeemingPointsModule.toggle_redeeming_based_on($('#rs_select_redeeming_based_on'));
            } else{
                jQuery( '#rs_percentage_cart_total_redeem' ).parent().parent().show() ;
                jQuery( '#rs_redeeming_button_option_message' ).closest( 'tr' ).show() ;
                jQuery( '#rs_default_redeeming_type_enabled' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_default_redeeming_type' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_redeeming_predefined_option_values' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_redeeming_start_sequence_number' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_redeeming_predefined_points_selection_label' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_choose_option_label' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_choose_option_label' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_msg' ).parent().parent().hide() ;
                RedeemingPointsModule.toggle_redeeming_based_on($('#rs_select_redeeming_based_on'));
            }
        } ,
        redeeming_field_type_in_checkout : function () {
            RedeemingPointsModule.show_or_hide_for_redeeming_field_type_in_checkout() ;
        } ,
        show_or_hide_for_redeeming_field_type_in_checkout : function () {
            if ( jQuery( '#rs_redeem_field_type_option_checkout' ).val() == '1' ) {
                jQuery( '#rs_percentage_cart_total_redeem_checkout' ).parent().parent().hide() ;
                jQuery('#rs_default_redeeming_type_enabled_checkout').closest('tr').show() ;
                RedeemingPointsModule.default_redeeming_type_checkout_checkbox($('#rs_default_redeeming_type_enabled_checkout')) ;
                RedeemingPointsModule.toggle_redeeming_based_on($('#rs_select_redeeming_based_on'));
            } else {
                jQuery( '#rs_percentage_cart_total_redeem_checkout' ).parent().parent().show() ;
                jQuery('#rs_default_redeeming_type_enabled_checkout').closest('tr').hide() ;
                jQuery( '#rs_default_redeeming_type_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_number_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_option_values_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_points_selection_label_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_choose_option_label_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_msg_checkout' ).parent().parent().hide() ;
                RedeemingPointsModule.toggle_redeeming_based_on($('#rs_select_redeeming_based_on'));
            }
        } ,
        toggle_default_redeeming_type_checkbox:function(event){
            event.preventDefault();
            var $this = $(event.currentTarget);
            RedeemingPointsModule.default_redeeming_type_checkbox($this) ;
        },
        default_redeeming_type_checkbox:function($this){
            if ( $this.is(':checked') ) {
                jQuery( '#rs_default_redeeming_type' ).parent().parent().show() ;
                RedeemingPointsModule.default_redeeming_type($('#rs_default_redeeming_type')) ;
            } else {
                jQuery( '#rs_default_redeeming_type' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_number' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_option_values' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_points_selection_label' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_choose_option_label' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_choose_option_label' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_msg' ).parent().parent().hide() ;
            }
        },
        toggle_default_redeeming_type:function(event){
            event.preventDefault();
            var $this = $(event.currentTarget);
            RedeemingPointsModule.default_redeeming_type($this) ;
        },
        default_redeeming_type:function($this){
            if ( '1' == $this.val()) {
                jQuery( '#rs_redeeming_predefined_option_values' ).parent().parent().show() ;
                jQuery( '#rs_redeeming_start_sequence_number' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_points_selection_label' ).parent().parent().show() ;
                jQuery( '#rs_redeeming_predefined_choose_option_label' ).parent().parent().show() ;
                jQuery( '#rs_redeeming_start_sequence_msg' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_redeeming_predefined_option_values' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_number' ).parent().parent().show() ;
                jQuery( '#rs_redeeming_predefined_points_selection_label' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_choose_option_label' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_msg' ).parent().parent().show() ;
            }
        },
        toggle_default_redeeming_type_checkout_checkbox:function(event){
            event.preventDefault();
            var $this = $(event.currentTarget);
            RedeemingPointsModule.default_redeeming_type_checkout_checkbox($this) ;
        },
        default_redeeming_type_checkout_checkbox:function($this){
            if ( $($this).is(':checked') ) {
                jQuery( '#rs_default_redeeming_type_checkout' ).parent().parent().show() ;
                RedeemingPointsModule.default_redeeming_type_checkout($('#rs_default_redeeming_type_checkout')) ;
            } else {
                jQuery( '#rs_default_redeeming_type_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_number_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_option_values_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_points_selection_label_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_choose_option_label_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_msg_checkout' ).parent().parent().hide() ;
            }
        },
        toggle_default_redeeming_type_checkout:function(event){
            event.preventDefault();
            var $this = $(event.currentTarget);
            RedeemingPointsModule.default_redeeming_type_checkout($this) ;
        },
        default_redeeming_type_checkout:function($this){
            if ( '1' == $this.val()) {
                jQuery( '#rs_redeeming_predefined_option_values_checkout' ).parent().parent().show() ;
                jQuery( '#rs_redeeming_start_sequence_number_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_points_selection_label_checkout' ).parent().parent().show() ;
                jQuery( '#rs_redeeming_predefined_choose_option_label_checkout' ).parent().parent().show() ;
                jQuery( '#rs_redeeming_start_sequence_msg_checkout' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_redeeming_predefined_option_values_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_number_checkout' ).parent().parent().show() ;
                jQuery( '#rs_redeeming_predefined_points_selection_label_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_choose_option_label_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_msg_checkout' ).parent().parent().show() ;
            }
        },
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
                jQuery('#rs_default_redeeming_type_enabled_checkout').closest('tr').hide() ;
                jQuery( '#rs_default_redeeming_type_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_start_sequence_number_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_redeeming_predefined_option_values_checkout' ).parent().parent().hide() ;
				jQuery( '#rs_redeeming_predefined_points_selection_label_checkout' ).parent().parent().hide() ;
    			jQuery( '#rs_redeeming_predefined_choose_option_label_checkout' ).parent().parent().hide() ;
    			jQuery( '#rs_redeeming_start_sequence_msg_checkout' ).parent().parent().hide() ;
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
        toggle_redeeming_exclude_category(e){
            e.preventDefault();
            RedeemingPointsModule.handle_redeeming_exclude_category(this);
        },

        handle_redeeming_exclude_category(e){
            if (true === $(e).is(':checked')) {
                $('#rs_exclude_category_to_enable_redeeming').closest('tr').show();
            } else {
                $('#rs_exclude_category_to_enable_redeeming').closest('tr').hide();
            }
        },
        toggle_redeeming_include_category(e){
            e.preventDefault();
            RedeemingPointsModule.handle_redeeming_include_category(this);
        },

        handle_redeeming_include_category(e){
            if (true === $(e).is(':checked')) {
                $('#rs_select_category_to_enable_redeeming').closest('tr').show();
            } else {
                $('#rs_select_category_to_enable_redeeming').closest('tr').hide();
            }
        },
        toggle_redeeming_exclude_products(e){
            e.preventDefault();
            RedeemingPointsModule.handle_redeeming_exclude_products(this);
        },

        handle_redeeming_exclude_products(e){
            if ( true === $(e).is(':checked') ) {
                $( '#rs_exclude_products_to_enable_redeeming' ).closest('tr').show();
            } else {
                $( '#rs_exclude_products_to_enable_redeeming' ).closest('tr').hide();
            }
        },
        toggle_redeeming_include_products(e){
            e.preventDefault();
            RedeemingPointsModule.handle_redeeming_include_products(this);
        },

        handle_redeeming_include_products(e){
            if ( true === $(e).is( ':checked' ) ) {
                $( '#rs_select_products_to_enable_redeeming' ).closest('tr').show() ;
            } else {
                $( '#rs_select_products_to_enable_redeeming' ).closest('tr').hide() ;
            }
        },
       
        redeeming_restriction_based_available_points : function() {
            RedeemingPointsModule.show_or_hide_for_available_points_based_redeem() ;
        } ,
        show_or_hide_for_available_points_based_redeem : function() {
            if( $( '#rs_minimum_available_points_restriction_is_enabled' ).is( ':checked' ) == true ) {
                $( '.rs_hide_minimum_available_point_restriction_fields' ).closest( 'tr' ).show() ;
                RedeemingPointsModule.show_or_hide_for_min_available_points_based_user_role() ;
            } else {
                $( '.rs_hide_minimum_available_point_restriction_fields' ).closest( 'tr' ).hide() ;
            }
        } ,
        min_available_points_based_user_role : function() {
            RedeemingPointsModule.show_or_hide_for_min_available_points_based_user_role() ;
        } ,
        show_or_hide_for_min_available_points_based_user_role : function() {
            if( $( '#rs_minimum_available_points_based_on' ).val() == '2' ) {
                $( '.rs_minimum_available_points_to_redeem_value' ).closest( 'tr' ).show() ;
                $( '#rs_available_points_based_redeem' ).closest( 'tr' ).hide() ;
            } else {
                $( '.rs_minimum_available_points_to_redeem_value' ).closest( 'tr' ).hide() ;
                $( '#rs_available_points_based_redeem' ).closest( 'tr' ).show() ;
            }
        } ,
        max_redeem_discount_type : function() {
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
        enable_msg_for_redeem_points : function() {
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
        add_rule_for_redeeming_percentage : function( event ) {
            event.preventDefault( ) ;
            var $this = $( event.currentTarget ) ;
            var random_value = Math.round( new Date( ).getTime( ) + ( Math.random( ) * 100 ) ) ;
            var data = {
                action : 'add_redeeming_percentage_rule' ,
                random_value : random_value ,
                sumo_security : fp_redeem_module_params.redeeming_percentage_nonce
            } ;
            $.post( fp_redeem_module_params.ajaxurl , data , function( response ) {
                if( true == response.success && response.data.html ) {
                    $( $this ).closest( '.rs-redeeming-percentage-rule' ).find( 'tbody' ).append( response.data.html ) ;
                    $( 'body' ).trigger( 'wc-enhanced-select-init' ) ;
                    $( '#rs_enable_user_role_based_reward_points_for_redeem' ).addClass( 'rs_enable_user_role_based_reward_points_for_redeem' ) ;
                    $( '#rs_enable_earned_level_based_reward_points_for_redeem' ).addClass( 'rs_enable_earned_level_based_reward_points_for_redeem' ) ;
                } else {
                    alert( response.data.error ) ;
                }
            } ) ;
        } ,
        remove_rule_for_redeeming_percentage : function( event ) {
            event.preventDefault() ;
            $( event.currentTarget ).closest( "tr" ).remove() ;
        } ,
        add_rule_for_redeeming_purchase_history : function( event ) {
            event.preventDefault( ) ;
            var $this = $( event.currentTarget ) ;
            var random_value = Math.round( new Date( ).getTime( ) + ( Math.random( ) * 100 ) ) ;
            var data = {
                action : 'add_redeeming_user_purchase_history_rule' ,
                random_value : random_value ,
                sumo_security : fp_redeem_module_params.redeeming_user_purchase_history_nonce
            } ;
            $.post( fp_redeem_module_params.ajaxurl , data , function( response ) {
                if( true == response.success && response.data.html ) {
                    $( $this ).closest( '.rs-redeeming-user-purchase-history' ).find( 'tbody' ).append( response.data.html ) ;
                    $( '#rs_enable_user_role_based_reward_points_for_redeem' ).addClass( 'rs_enable_user_role_based_reward_points_for_redeem' ) ;
                    $( '#rs_enable_earned_level_based_reward_points_for_redeem' ).addClass( 'rs_enable_earned_level_based_reward_points_for_redeem' ) ;
                } else {
                    alert( response.data.error ) ;
                }
            } ) ;
        } ,
        remove_rule_for_redeeming_purchase_history : function( event ) {
            event.preventDefault() ;
            $( event.currentTarget ).closest( "tr" ).remove() ;
        } ,
        max_redeeming_per_day_restriction_checkbox:function(e){
            e.preventDefault();
            RedeemingPointsModule.show_or_hide_for_auto_redeem_not_applicable_err_msg(this) ;
        },
        toggle_max_redeeming_per_day_restriction_checkbox: function(e){           
            if($(e).is(':checked')){
                $('#rs_maximum_redeeming_per_day_restriction').closest('tr').show();
                $('#rs_maximum_redeeming_per_day_error').closest('tr').show();
            }else{
                $('#rs_maximum_redeeming_per_day_restriction').closest('tr').hide();
                $('#rs_maximum_redeeming_per_day_error').closest('tr').hide();
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
            $( id ).unblock() ;
        } ,
    } ;
    RedeemingPointsModule.init() ;
} ) ;
