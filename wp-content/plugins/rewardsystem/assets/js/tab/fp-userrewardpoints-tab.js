/*
 * User Reward Points Tab
 */
jQuery( function ( $ ) {
    var UserRewardPointsTabScript = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_filter() ;
            $( document ).on( 'click' , '#rs_remove_point_for_user' , this.validate_the_points_to_remove ) ;
            $( document ).on( 'change' , '#rs_filter_type_for_log' , this.show_or_hide_for_filter ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_userrewardpoints_tab_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_userrole_for_reward_log' ).chosen() ;
            } else {
                $( '#rs_userrole_for_reward_log' ).select2() ;
            }
        } ,
        validate_the_points_to_remove : function ( e ) {
            var points = Number( $( '#rs_points' ).val() ) ;
            if ( fp_userrewardpoints_tab.available_points < points ) {
                e.preventDefault() ;
                jQuery( '.rs_add_remove_points_errors' ).fadeIn() ;
                jQuery( '.rs_add_remove_points_errors' ).html( 'You entered point is more than the current points' ) ;
                jQuery( '.rs_add_remove_points_errors' ).fadeOut( 5000 ) ;
            }
        } ,
        show_or_hide_for_filter : function () {
            if ( fp_userrewardpoints_tab_params.hide_filter ) {
                jQuery( '#rs_filter_type_for_log' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_userrole_for_reward_log' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_select_user_for_reward_log' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_submit_for_user_role_log' ).closest( 'tr' ).hide() ;
            } else {
                if ( jQuery( '#rs_filter_type_for_log' ).val() == '1' ) {
                    jQuery( '#rs_userrole_for_reward_log' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_select_user_for_reward_log' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_userrole_for_reward_log' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_select_user_for_reward_log' ).closest( 'tr' ).hide() ;
                }
            }
        }
    } ;
    UserRewardPointsTabScript.init() ;
} ) ;