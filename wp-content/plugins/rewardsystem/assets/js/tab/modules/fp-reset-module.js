/*
 * Reset - Module
 */
jQuery( function ( $ ) {
    var ResetModule = {
        init : function () {
            this.trigger_on_page_load() ;
            jQuery( '.gif_rs_reset_tab_settings' ).css( 'display' , 'none' ) ;
            jQuery( '.gif_rs_sumo_reward_button_for_reset' ).css( 'display' , 'none' ) ;
            jQuery( '#rs_reset_selected_user_data' ).closest( 'tr' ).hide() ;
            $( document ).on( 'change' , '.rs_reset_data_for_users' , this.user_type_to_reset_data ) ;
            $( document ).on( 'click' , '#rs_reset_tab' , this.reset_tab_settings ) ;
            $( document ).on( 'click' , '#rs_reset_data_submit' , this.reset_data_for_user ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_reset_module_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_reset_selected_user_data' ).chosen() ;
            } else {
                $( '#rs_reset_selected_user_data' ).select2() ;
            }
        } ,
        user_type_to_reset_data : function () {
            ResetModule.show_or_hide_for_user_type_to_reset_data( this ) ;
        } ,
        show_or_hide_for_user_type_to_reset_data : function ( event ) {
            if ( event.value === '1' ) {
                jQuery( '#rs_reset_selected_user_data' ).closest( 'tr' ).hide() ;
            } else {
                jQuery( '#rs_reset_selected_user_data' ).closest( 'tr' ).show() ;
            }
        } ,
        reset_tab_settings : function () {
            if ( confirm( "Are You Sure ? Do You Want to Reset Your Tab Settings?" ) == true ) {
                jQuery( '.gif_rs_reset_tab_settings' ).css( 'display' , 'inline-block' ) ;
                var dataparam = ( {
                    action : 'fp_reset_settings' ,
                    sumo_security : fp_reset_module_params.rs_reset_tab
                } ) ;
                $.post( fp_reset_module_params.ajaxurl , dataparam , function ( response ) {
                    if ( true === response.success ) {
                        jQuery( '.gif_rs_reset_tab_settings' ).css( 'display' , 'none' ) ;
                        jQuery( '.rs_reset_tab_setting_success' ).fadeIn() ;
                        jQuery( '.rs_reset_tab_setting_success' ).html( "Settings Resetted Successfully" ) ;
                        jQuery( '.rs_reset_tab_setting_success' ).fadeOut( 5000 ) ;
                        location.reload( true ) ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
        } ,
        reset_data_for_user : function () {
            if ( confirm( "Are You Sure ? Do You Want to Reset Your Data?" ) == true ) {
                jQuery( '.gif_rs_sumo_reward_button_for_reset' ).css( 'display' , 'inline-block' ) ;
                var dataparam = ( {
                    action : 'fp_reset_users_data' ,
                    resetdatafor : $( '.rs_reset_data_for_users' ).filter( ":checked" ).val() ,
                    rsselectedusers : $( '#rs_reset_selected_user_data' ).val() ,
                    rsresetuserpoints : $( '#rs_reset_user_reward_points' ).filter( ":checked" ).val() ,
                    rsresetuserlogs : $( '#rs_reset_user_log' ).filter( ":checked" ).val() ,
                    rsresetmasterlogs : $( '#rs_reset_master_log' ).filter( ":checked" ).val() ,
                    resetpreviousorder : $( '#rs_reset_previous_order' ).filter( ":checked" ).val() ,
                    resetreferrallog : $( '#rs_reset_referral_log_table' ).filter( ":checked" ).val() ,
                    resetmanualreferral : $( '#rs_reset_manual_referral_link' ).filter( ":checked" ).val() ,
                    resetrecordlogtable : $( '#rs_reset_record_log_table' ).filter( ":checked" ).val() ,
                    sumo_security : fp_reset_module_params.rs_reset_data_for_user
                } ) ;
                $.post( fp_reset_module_params.ajaxurl , dataparam , function ( response ) {
                    if ( true === response.success ) {
                        if ( response.data.content != 'success' ) {
                            var j = 1 ;
                            var i , j , temparray , chunk = 10 ;
                            for ( i = 0 , j = response.data.content.length ; i < j ; i += chunk ) {
                                temparray = response.data.content.slice( i , i + chunk ) ;
                                ResetModule.chunk_and_reset_data_for_user( temparray ) ;
                            }
                            jQuery.when( ResetModule.chunk_and_reset_data_for_user( 'done' ) ).done( function ( a1 ) {
                                console.log( 'Ajax Done Successfully' ) ;
                                jQuery( '.gif_rs_sumo_reward_button_for_reset' ).css( 'display' , 'none' ) ;
                                jQuery( '.rs_reset_success_data' ).fadeIn() ;
                                jQuery( '.rs_reset_success_data' ).html( "Data Resetted Successfully" ) ;
                                jQuery( '.rs_reset_success_data' ).fadeOut( 5000 ) ;
                                location.reload( true ) ;
                            } ) ;
                        } else {
                            jQuery( '.gif_rs_sumo_reward_button_for_reset' ).css( 'display' , 'none' ) ;
                            jQuery( '.rs_reset_success_data' ).fadeIn() ;
                            jQuery( '.rs_reset_success_data' ).html( "Data Resetted Successfully" ) ;
                            jQuery( '.rs_reset_success_data' ).fadeOut( 5000 ) ;
                            location.reload( true ) ;
                        }
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
        } ,
        chunk_and_reset_data_for_user : function ( id ) {
            jQuery( '.gif_rs_sumo_reward_button_for_reset' ).css( 'display' , 'inline-block' ) ;
            var dataparam = ( {
                action : 'fp_reset_order_meta' ,
                ids : id ,
                sumo_security : fp_reset_module_params.rs_reset_previous_order_meta
            } ) ;
            $.post( ajaxurl , dataparam , function ( response ) {
                if ( true === response.success ) {
                    console.log( 'Ajax Done Successfully' ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
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
    ResetModule.init() ;
} ) ;