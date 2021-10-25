/*
 * Nominee - Module
 */
jQuery( function ( $ ) {
    var NomineeScripts = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_nominee_field() ;
            this.show_or_hide_for_nominee_field_in_shortcode() ;
            this.show_or_hide_for_nominee_field_in_checkout() ;
            $( document ).on( 'change' , '#rs_show_hide_nominee_field' , this.show_or_hide_for_nominee_field ) ;
            $( document ).on( 'change' , '#rs_show_hide_nominee_field_shortcode' , this.show_or_hide_for_nominee_field_in_shortcode ) ;
            $( document ).on( 'change' , '#rs_show_hide_nominee_field_in_checkout' , this.show_or_hide_for_nominee_field_in_checkout ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_nominee_module_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_select_users_role_for_nominee' ).chosen() ;
                $( '#rs_select_users_role_for_nominee_checkout' ).chosen() ;
                $( '#rs_select_users_role_for_nominee_shortcode' ).chosen() ;
            } else {
                $( '#rs_select_users_role_for_nominee' ).select2() ;
                $( '#rs_select_users_role_for_nominee_checkout' ).select2() ;
                $( '#rs_select_users_role_for_nominee_shortcode' ).select2() ;
            }
        } ,
        show_or_hide_for_nominee_field : function () {
            if ( jQuery( '#rs_show_hide_nominee_field' ).val() == '1' ) {
                jQuery( '#rs_my_nominee_title' ).parent().parent().show() ;
                jQuery( '#rs_select_type_of_user_for_nominee' ).parent().parent().show() ;
                jQuery( '#rs_select_type_of_user_for_nominee_name' ).parent().parent().show() ;

                if ( jQuery( '#rs_select_type_of_user_for_nominee' ).val() == '1' ) {
                    jQuery( '#rs_select_users_list_for_nominee' ).parent().parent().show() ;
                    jQuery( '#rs_select_users_role_for_nominee' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_select_users_list_for_nominee' ).parent().parent().hide() ;
                    jQuery( '#rs_select_users_role_for_nominee' ).parent().parent().show() ;
                }

                jQuery( '#rs_select_type_of_user_for_nominee' ).change( function () {
                    if ( jQuery( '#rs_select_type_of_user_for_nominee' ).val() == '1' ) {
                        jQuery( '#rs_select_users_list_for_nominee' ).parent().parent().show() ;
                        jQuery( '#rs_select_users_role_for_nominee' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_select_users_list_for_nominee' ).parent().parent().hide() ;
                        jQuery( '#rs_select_users_role_for_nominee' ).parent().parent().show() ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_my_nominee_title' ).parent().parent().hide() ;
                jQuery( '#rs_select_type_of_user_for_nominee' ).parent().parent().hide() ;
                jQuery( '#rs_select_users_list_for_nominee' ).parent().parent().hide() ;
                jQuery( '#rs_select_users_role_for_nominee' ).parent().parent().hide() ;
                jQuery( '#rs_select_type_of_user_for_nominee_name' ).parent().parent().hide() ;
            }
        } ,
        show_or_hide_for_nominee_field_in_shortcode : function () {
            if ( jQuery( '#rs_show_hide_nominee_field_shortcode' ).val() == '1' ) {
                jQuery( '#rs_my_nominee_title_shortcode' ).parent().parent().show() ;
                jQuery( '#rs_select_type_of_user_for_nominee_shortcode' ).parent().parent().show() ;
                jQuery( '#rs_select_type_of_user_for_nominee_name_shortcode' ).parent().parent().show() ;

                if ( jQuery( '#rs_select_type_of_user_for_nominee_shortcode' ).val() == '1' ) {
                    jQuery( '#rs_select_users_list_for_nominee_shortcode' ).parent().parent().show() ;
                    jQuery( '#rs_select_users_role_for_nominee_shortcode' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_select_users_list_for_nominee_shortcode' ).parent().parent().hide() ;
                    jQuery( '#rs_select_users_role_for_nominee_shortcode' ).parent().parent().show() ;
                }

                jQuery( '#rs_select_type_of_user_for_nominee_shortcode' ).change( function () {
                    if ( jQuery( '#rs_select_type_of_user_for_nominee_shortcode' ).val() == '1' ) {
                        jQuery( '#rs_select_users_list_for_nominee_shortcode' ).parent().parent().show() ;
                        jQuery( '#rs_select_users_role_for_nominee_shortcode' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_select_users_list_for_nominee_shortcode' ).parent().parent().hide() ;
                        jQuery( '#rs_select_users_role_for_nominee_shortcode' ).parent().parent().show() ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_my_nominee_title_shortcode' ).parent().parent().hide() ;
                jQuery( '#rs_select_type_of_user_for_nominee_shortcode' ).parent().parent().hide() ;
                jQuery( '#rs_select_users_list_for_nominee_shortcode' ).parent().parent().hide() ;
                jQuery( '#rs_select_users_role_for_nominee_shortcode' ).parent().parent().hide() ;
                jQuery( '#rs_select_type_of_user_for_nominee_name_shortcode' ).parent().parent().hide() ;
            }
        } ,
        show_or_hide_for_nominee_field_in_checkout : function () {
            if ( jQuery( '#rs_show_hide_nominee_field_in_checkout' ).val() == '1' ) {
                jQuery( '#rs_my_nominee_title_in_checkout' ).parent().parent().show() ;
                jQuery( '#rs_select_type_of_user_for_nominee_checkout' ).parent().parent().show() ;
                jQuery( '#rs_select_type_of_user_for_nominee_name_checkout' ).parent().parent().show() ;

                if ( jQuery( '#rs_select_type_of_user_for_nominee_checkout' ).val() == '1' ) {
                    jQuery( '#rs_select_users_list_for_nominee_in_checkout' ).parent().parent().show() ;
                    jQuery( '#rs_select_users_role_for_nominee_checkout' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_select_users_list_for_nominee_in_checkout' ).parent().parent().hide() ;
                    jQuery( '#rs_select_users_role_for_nominee_checkout' ).parent().parent().show() ;
                }

                jQuery( '#rs_select_type_of_user_for_nominee_checkout' ).change( function () {
                    if ( jQuery( '#rs_select_type_of_user_for_nominee_checkout' ).val() == '1' ) {
                        jQuery( '#rs_select_users_list_for_nominee_in_checkout' ).parent().parent().show() ;
                        jQuery( '#rs_select_users_role_for_nominee_checkout' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_select_users_list_for_nominee_in_checkout' ).parent().parent().hide() ;
                        jQuery( '#rs_select_users_role_for_nominee_checkout' ).parent().parent().show() ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_my_nominee_title_in_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_select_users_list_for_nominee_in_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_select_users_role_for_nominee_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_select_type_of_user_for_nominee_checkout' ).parent().parent().hide() ;
                jQuery( '#rs_select_type_of_user_for_nominee_name_checkout' ).parent().parent().hide() ;
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
    NomineeScripts.init() ;
} ) ;