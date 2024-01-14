/*
 * Variable Product Validation
 */

jQuery( function ( $ ) {
    'use strict' ;
    
    var RSVariableProductValidation = {
        init : function () {
            // this.toggle_variable_settings() ;
            this.toggle_enable_redeeming_points($('._rewardsystem_redeeming_points_enable')) ;
            this.enable_disable_reward_points( $('.srp-enable-reward-points') );
            
            // $( document ).on( 'click' , '#publish' , this.validate_point_price_for_variation ) ;
            // $( document ).on( 'change' , "select[name='_enable_reward_points_price['" + fp_variation_params.loop + "']']" , this.show_or_hide_for_point_price ) ;
            $( document ).on( 'change' , '.srp-variation-reward-type' , this.toggle_reward_type ) ;
            $( document ).on( 'change' , '.srp-enable-reward-points', this.enable_reward_points);
            $( document ).on( 'change' , '._rewardsystem_redeeming_points_enable' , this.enable_redeeming_points ) ;
            $( document ).on( 'change' , '.srp-referral-points-enable' , this.enable_referral_points ) ;
            $( document ).on( 'change' , '.srp-referral-type' , this.referral_type ) ;
            $( document ).on( 'change' , '.srp-getting-referred-type' , this.getting_referred_type ) ;
            $( document ).on( 'change' , '.srp-point-price-enable' , this.enable_point_price ) ;
            $( document ).on( 'change' , '.srp-point-pricing-display-type' , this.point_pricing_display_type ) ;
            $( document ).on( 'change' , '.srp-point-price-type' , this.point_price_type ) ;
        } ,

        point_price_type(e) {
            e.preventDefault();
            RSVariableProductValidation.toggle_point_price_type(this) ;
        } ,

        toggle_point_price_type(e) {
            if ( '1' === $(e).val() ){
                $(e).closest('div').find('.srp-point-price-fixed').closest('p').show();
                $(e).closest('div').find('.srp-point-price-based-on-conversion').closest('p').hide();
            } else {
                $(e).closest('div').find('.srp-point-price-fixed').closest('p').hide();
                $(e).closest('div').find('.srp-point-price-based-on-conversion').closest('p').show();
            }
        },

        point_pricing_display_type(e) {
            e.preventDefault();
            RSVariableProductValidation.toggle_point_pricing_display_type(this) ;
        } ,

        toggle_point_pricing_display_type(e) {
            if ( '1' === $(e).val() ){
                $(e).closest('div').find('.srp-point-price-type').closest('p').show();
                RSVariableProductValidation.toggle_point_price_type($(e).closest('div').find('.srp-point-price-type'));
            } else {
                $(e).closest('div').find('.srp-point-price-type').closest('p').hide();
                $(e).closest('div').find('.srp-point-price-fixed').closest('p').show();
                $(e).closest('div').find('.srp-point-price-based-on-conversion').closest('p').hide();
            }
        },

        enable_point_price(e) {
            e.preventDefault();
            RSVariableProductValidation.toggle_enable_point_price(this) ;
        } ,

        toggle_enable_point_price(e) {
            if ( '1' === $(e).val() ){
                $(e).closest('div').find('.srp-show-if-point-price-enable').closest('p').show();
                RSVariableProductValidation.toggle_point_pricing_display_type($(e).closest('div').find('.srp-point-pricing-display-type'));
            } else {
                $(e).closest('div').find('.srp-show-if-point-price-enable').closest('p').hide();
            }
        },

        getting_referred_type(e) {
            e.preventDefault();
            RSVariableProductValidation.toggle_getting_referred_type(this) ;
        } ,

        toggle_getting_referred_type(e) {
            if ( '1' === $(e).val() ){
                $(e).closest('div').find('.srp-getrefer-fixed').closest('p').show();
                $(e).closest('div').find('.srp-getrefer-percent').closest('p').hide();
            } else {
                $(e).closest('div').find('.srp-getrefer-fixed').closest('p').hide();
                $(e).closest('div').find('.srp-getrefer-percent').closest('p').show();
            }
        },

        referral_type(e) {
            e.preventDefault();
            RSVariableProductValidation.toggle_referral_type(this) ;
        } ,

        toggle_referral_type(e) {
            if ( '1' === $(e).val() ){
                $(e).closest('div').find('.srp-referral-fixed').closest('p').show();
                $(e).closest('div').find('.srp-referral-percent').closest('p').hide();
            } else {
                $(e).closest('div').find('.srp-referral-fixed').closest('p').hide();
                $(e).closest('div').find('.srp-referral-percent').closest('p').show();
            }
        },

        enable_referral_points(e) {
            e.preventDefault();
            RSVariableProductValidation.toggle_enable_referral_points(this) ;
        } ,

        toggle_enable_referral_points(e) {
            if ( '1' === $(e).val() ){
                $(e).closest('div').find('.srp-show-if-referral-points-enable').closest('p').show();
                RSVariableProductValidation.toggle_referral_type($(e).closest('div').find('.srp-referral-type'));
                RSVariableProductValidation.toggle_getting_referred_type($(e).closest('div').find('.srp-getting-referred-type'));
            } else {
                $(e).closest('div').find('.srp-show-if-referral-points-enable').closest('p').hide();
            }
        },

        enable_redeeming_points(e) {
            e.preventDefault();
            RSVariableProductValidation.toggle_enable_redeeming_points(this) ;
        } ,

        toggle_enable_redeeming_points(e) {
            if ( '1' === $(e).val() ){
                $(e).closest('div').find('.srp-show-if-redeem-points-enable').closest('p').show();
            } else {
                $(e).closest('div').find('.srp-show-if-redeem-points-enable').closest('p').hide();
            }
        },

        enable_reward_points(e) {
            e.preventDefault();
            RSVariableProductValidation.enable_disable_reward_points(this);
        }, 

        enable_disable_reward_points( enable_reward ) {
            if ( '1' === $(enable_reward).val() ){
                $(enable_reward).closest('div').find('.srp-show-if-reward-points-enable').closest('p').show();
                RSVariableProductValidation.show_or_hide_for_reward_type( $(enable_reward).closest('div').find('.srp-variation-reward-type') );
            } else {
                $(enable_reward).closest('div').find('.srp-show-if-reward-points-enable').closest('p').hide();
            }
        } ,

        toggle_reward_type(e){
            e.preventDefault();
            RSVariableProductValidation.show_or_hide_for_reward_type(this) ;
        },

        show_or_hide_for_reward_type(reward_type){
            if ( '1' === $(reward_type).val()){
                $(reward_type).closest('div').find('.srp-variation-fixed-points').closest('p').show();
                $(reward_type).closest('div').find('.srp-variation-percentage-points').closest('p').hide();
            } else {
                $(reward_type).closest('div').find('.srp-variation-fixed-points').closest('p').hide();
                $(reward_type).closest('div').find('.srp-variation-percentage-points').closest('p').show();
            }
        },

        toggle_variable_settings : function () {
            $( '.fp_variation_points_price' ).attr( 'readonly' , 'true' ) ;
            RSVariableProductValidation.show_or_hide_for_point_price() ;
        } ,
        show_or_hide_for_point_price_type : function () {
            if ( jQuery( "select[name='_enable_reward_points_pricing_type['" + fp_variation_params.loop + "']']" ).val() == '2' ) {
                jQuery( "[name='price_points['" + fp_variation_params.loop + "']']" ).parent().show() ;
                jQuery( "select[name='_enable_reward_points_price_type['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                jQuery( "[name='_price_points_based_on_conversion['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                if ( jQuery( '#rs_point_price_visibility' ).val() == '1' ) {
                    jQuery( "[name='variable_regular_price['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                    jQuery( "[name='variable_sale_price['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                } else {
                    jQuery( "[name='variable_regular_price['" + fp_variation_params.loop + "']']" ).parent().show() ;
                    jQuery( "[name='variable_sale_price['" + fp_variation_params.loop + "']']" ).parent().show() ;
                }
            } else {
                jQuery( "select[name='_enable_reward_points_price_type['" + fp_variation_params.loop + "']']" ).parent().show() ;
                jQuery( "[name='variable_regular_price['" + fp_variation_params.loop + "']']" ).parent().show() ;
                jQuery( "[name='variable_sale_price['" + fp_variation_params.loop + "']']" ).parent().show() ;
                if ( jQuery( "select[name='_enable_reward_points_price_type['" + fp_variation_params.loop + "']']" ).val() == '2' ) {
                    jQuery( "[name='_price_points_based_on_conversion['" + fp_variation_params.loop + "']']" ).parent().show() ;
                    jQuery( "[name='price_points['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                } else {
                    jQuery( "[name='price_points['" + fp_variation_params.loop + "']']" ).parent().show() ;
                    jQuery( "[name='_price_points_based_on_conversion['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                }
                jQuery( "select[name='_enable_reward_points_price_type['" + fp_variation_params.loop + "']']" ).change( function () {
                    if ( jQuery( "select[name='_enable_reward_points_price_type['" + fp_variation_params.loop + "']']" ).val() == '2' ) {
                        jQuery( "[name='_price_points_based_on_conversion['" + fp_variation_params.loop + "']']" ).parent().show() ;
                        jQuery( "[name='price_points['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                    } else {
                        jQuery( "[name='price_points['" + fp_variation_params.loop + "']']" ).parent().show() ;
                        jQuery( "[name='_price_points_based_on_conversion['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                    }
                } ) ;
            }
        } ,
        show_or_hide_for_point_price : function () {
            if ( jQuery( "select[name='_enable_reward_points_price['" + fp_variation_params.loop + "']']" ).val() == '2' ) {
                jQuery( "select[name='_enable_reward_points_pricing_type['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                jQuery( "select[name='_enable_reward_points_price_type['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                jQuery( "[name='_price_points_based_on_conversion['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                jQuery( "[name='price_points['" + fp_variation_params.loop + "']']" ).parent().hide() ;
                jQuery( "[name='variable_regular_price['" + fp_variation_params.loop + "']']" ).parent().show() ;
                jQuery( "[name='variable_sale_price['" + fp_variation_params.loop + "']']" ).parent().show() ;
            } else {
                jQuery( "select[name='_enable_reward_points_pricing_type['" + fp_variation_params.loop + "']']" ).parent().show() ;
                RSVariableProductValidation.show_or_hide_for_point_price_type() ;
                jQuery( "select[name='_enable_reward_points_pricing_type['" + fp_variation_params.loop + "']']" ).change( function () {
                    RSVariableProductValidation.show_or_hide_for_point_price_type() ;
                } ) ;
            }
        } ,

        validate_point_price_for_variation : function ( e ) {
            if ( $( "select[name='_enable_reward_points_pricing_type['" + fp_variation_params.loop + "']']" ).val( ) == '2' ) {
                if ( $( "[name='price_points['" + fp_variation_params.loop + "']']" ).val() == '' ) {
                    $( "[name='price_points['" + fp_variation_params.loop + "']']" ).css( {
                        "border" : "1px solid red" ,
                        "background" : "#FFCECE"
                    } ) ;
                    $( "[name='price_points['" + fp_variation_params.loop + "']']" ).show() ;
                    $( "[name='price_points['" + fp_variation_params.loop + "']']" ).focus() ;
                    e.preventDefault() ;
                }
            }
        } ,
    } ;
    RSVariableProductValidation.init() ;
} ) ;