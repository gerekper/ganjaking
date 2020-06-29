
jQuery( function ( $ ) {

    var FPAdminScripts = {
        init : function () {
            jQuery( '.gif_rs_sumo_reward_button_for_unsubscribe' ).css( 'display' , 'none' ) ;
            $( document ).on( 'click' , '#rs_enable_reward_program' , this.save_reward_program_disable_option ) ;
            $( document ).on( 'change' , '#changepagesizertemplates' , this.pagination_for_templates ) ;
            $( 'body' ).on( 'blur' , adminscripts_params.field_ids , this.validation_in_product_settings_on_blur ) ;
            $( 'body' ).on( 'keyup change' , adminscripts_params.field_ids , this.validation_in_product_settings_on_keyup ) ;
            $( 'body' ).on( 'click' , 'body' , this.validation_in_product_settings_on_body_click ) ;
        } ,
        pagination_for_templates : function ( e ) {
            e.preventDefault() ;
            var pageSize = jQuery( this ).val() ;
            jQuery( '.footable' ).data( 'page-size' , pageSize ) ;
            jQuery( '.footable' ).trigger( 'footable_initialized' ) ;
        } ,
        validation_in_product_settings_on_blur : function () {
            $( '.wc_error_tip' ).fadeOut( '100' , function () {
                $( this ).remove() ;
            } ) ;
            return this ;
        } ,
        validation_in_product_settings_on_keyup : function () {
            var value = $( this ).val() ;
            var regex = new RegExp( "[^\+0-9\%.\\" + woocommerce_admin.mon_decimal_point + "]+" , "gi" ) ;
            var newvalue = value.replace( regex , '' ) ;

            if ( value !== newvalue ) {
                $( this ).val( newvalue ) ;
                if ( $( this ).parent().find( '.wc_error_tip' ).size() == 0 ) {
                    $( this ).after( '<div class="wc_error_tip">' + woocommerce_admin.i18n_mon_decimal_error + " Negative Values are not allowed" + '</div>' ) ;
                    $( '.wc_error_tip' )
                            .css( 'left' , offset.left + $( this ).width() - ( $( this ).width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
                            .css( 'top' , offset.top + $( this ).height() )
                            .fadeIn( '100' ) ;
                }
            }
            return this ;
        } ,
        validation_in_product_settings_on_body_click : function () {
            $( '.wc_error_tip' ).fadeOut( '100' , function () {
                $( this ).remove() ;
            } ) ;
            return this ;
        } ,
        save_reward_program_disable_option : function () {
            if ( jQuery( '#rs_enable_reward_program' ).is( ':checked' ) == false ) {
                if ( confirm( 'Are you sure you want to turn off this option? Please note, If You Turn Off this option ,then all the users on your site will be in part of SUMO Reward Points)' ) ) {
                    return true ;
                }
                return false ;
            }
        } ,
    } ;
    FPAdminScripts.init() ;
} ) ;