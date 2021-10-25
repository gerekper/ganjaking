jQuery( function ( $ ) {
    var formforsendpoints = {
        init : function ( ) {
            $( document.body ).on( 'click' , '#rs_send_points_submit_button' , this.formforsendpointsclick ) ;
            jQuery( ".error" ).hide( ) ;
            jQuery( ".success_info" ).hide( ) ;
            this.select_user_field( ) ;
        } ,
        formforsendpointsclick : function ( evt ) {
            evt.preventDefault( ) ;

            if ( '1' == fp_sendpoint_frontend_params.user_selection_fieldtype ) {
                var receiver_info = jQuery( '#select_user_ids' ).val( ) ;
            } else {
                var receiver_info = jQuery( '.rs_user_name_field' ).val( ) ;
            }

            var send_points = parseFloat( jQuery( "#rs_total_reward_value_send" ).val( ) ) ;
            if ( fp_sendpoint_frontend_params.sendpointlimit == '1' ) {
                if ( fp_sendpoint_frontend_params.limittosendreq != '' && fp_sendpoint_frontend_params.limittosendreq != '0' ) {
                    if ( send_points > fp_sendpoint_frontend_params.limittosendreq ) {
                        jQuery( '.error_greater_than_limit' ).css( 'color' , 'red' ) ;
                        jQuery( '.error_greater_than_limit' ).html( fp_sendpoint_frontend_params.limit_err ) ;
                        jQuery( ".error_greater_than_limit" ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                        return false ;
                    }
                }
            }

            if ( receiver_info == '' || receiver_info == null ) {
                jQuery( '.error_empty_user' ).css( 'color' , 'red' ) ;
                jQuery( '.error_empty_user' ).html( fp_sendpoint_frontend_params.user_emty_err ) ;
                jQuery( ".error_empty_user" ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                return false ;
            }

            if ( send_points == "" ) {
                jQuery( '.error_point_empty' ).css( 'color' , 'red' ) ;
                jQuery( '.error_point_empty' ).html( fp_sendpoint_frontend_params.point_emp_err ) ;
                jQuery( ".error_point_empty" ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                return false ;
            }

            if ( isNaN( send_points ) ) {
                jQuery( '.error_points_not_number' ).css( 'color' , 'red' ) ;
                jQuery( '.error_points_not_number' ).html( fp_sendpoint_frontend_params.point_not_num ) ;
                jQuery( ".error_points_not_number" ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                return false ;
            }

            if ( fp_sendpoint_frontend_params.send_points_reason == 'yes' ) {
                if ( $( '#rs_reason_for_send_points' ).val( ) == '' ) {
                    jQuery( '.rs_reason_for_send_points' ).css( 'color' , 'red' ) ;
                    jQuery( '.rs_reason_for_send_points' ).html( fp_sendpoint_frontend_params.error_for_reason_field_empty ) ;
                    jQuery( ".rs_reason_for_send_points" ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                    return false ;
                }
            }

            if ( send_points > fp_sendpoint_frontend_params.currentuserpoint ) {
                jQuery( '.points_more_than_current_points' ).css( 'color' , 'red' ) ;
                jQuery( '.points_more_than_current_points' ).html( fp_sendpoint_frontend_params.errorforgreaterpoints ) ;
                jQuery( ".points_more_than_current_points" ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                return false ;
            }
            if ( send_points < 0 ) {
                jQuery( '.points_less_than_current_points' ).css( 'color' , 'red' ) ;
                jQuery( '.points_less_than_current_points' ).html( fp_sendpoint_frontend_params.errorforlesserpoints ) ;
                jQuery( ".points_less_than_current_points" ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                return false ;
            }

            formforsendpoints.block( '#sendpoint_form' ) ;

            var data = ( {
                action : "send_points_data" ,
                sumo_security : fp_sendpoint_frontend_params.fp_send_points_data ,
                points : send_points ,
                receiver_info : receiver_info ,
                senderid : fp_sendpoint_frontend_params.user_id ,
                sendername : fp_sendpoint_frontend_params.username ,
                senderpoints : fp_sendpoint_frontend_params.currentuserpoint ,
                status : "Due" ,
                reason : $( '#rs_reason_for_send_points' ).val( ) ,
            } ) ;

            $.post( fp_sendpoint_frontend_params.wp_ajax_url , data , function ( response ) {
                if ( true === response.success ) {
                    formforsendpoints.unblock( '#sendpoint_form' ) ;
                    jQuery( '.error_empty_user' ).css( 'color' , 'green' ) ;
                    jQuery( "#rs_total_reward_value_send" ).val( '' ) ;
                    jQuery( ".success_info" ).fadeIn( ) ;
                    jQuery( ".success_info" ).html( fp_sendpoint_frontend_params.success_info ) ;
                    jQuery( ".success_info" ).fadeOut( 3000 , function ( ) {
                        location.reload( true ) ;
                    } ) ;
                } else {

                    formforsendpoints.unblock( '#sendpoint_form' ) ;

                    var error_message = false ,
                            $class_name = false ;

                    if ( 'invalid_username_error' == response.data.error && ! error_message && ! $class_name ) {
                        error_message = fp_sendpoint_frontend_params.invalid_username_error ;
                        $class_name = '.error_empty_user' ;
                    }

                    if ( 'restricted_username_error' == response.data.error && ! error_message && ! $class_name ) {
                        error_message = fp_sendpoint_frontend_params.restricted_username_error ;
                        $class_name = '.error_empty_user' ;
                    }

                    if ( error_message && $class_name ) {
                        jQuery( $class_name ).css( 'color' , 'red' ) ;
                        jQuery( $class_name ).html( error_message ) ;
                        jQuery( $class_name ).fadeIn( ).delay( 5000 ).fadeOut( ) ;
                    } else {
                        alert( response.data.error ) ;
                    }

                }
            } ) ;
        } ,
        select_user_field : function ( ) {
            jQuery( "#select_user_ids" ).select2( {
                allowClear : true ,
                minimumInputLength : 3 ,
                escapeMarkup : function ( m ) {
                    return m ;
                } ,
                ajax : {
                    url : fp_sendpoint_frontend_params.wp_ajax_url ,
                    dataType : 'json' ,
                    quietMillis : 250 ,
                    data : function ( params ) {
                        return {
                            term : params.term ,
                            action : 'srp_user_search' ,
                            sumo_security : fp_sendpoint_frontend_params.fp_user_search
                        } ;
                    } ,
                    processResults : function ( data ) {
                        var terms = [ ] ;
                        if ( data ) {
                            jQuery.each( data , function ( id , text ) {
                                terms.push( {
                                    id : id ,
                                    text : text
                                } ) ;
                            } ) ;
                        }
                        return {
                            results : terms
                        } ;
                    } ,
                    cache : true
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
            $( id ).unblock( ) ;
        } ,
    } ;
    formforsendpoints.init( ) ;
} ) ;




