/*
 * Variable Product Validation
 */
jQuery( function ( $ ) {
    var RSVariableProductValidation = {
        init : function () {
            this.toggle_variable_settings() ;
            $( document ).on( 'click' , '#publish' , this.validate_point_price_for_variation ) ;
            $( document ).on( 'change' , "select[name='_enable_reward_points_price['" + fp_variation_params.loop + "']']" , this.show_or_hide_for_point_price ) ;
        } ,

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