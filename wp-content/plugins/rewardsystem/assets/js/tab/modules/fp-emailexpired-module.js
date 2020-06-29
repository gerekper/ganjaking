/*
 * EmailExpiredModule - Module
 */
jQuery( function ( $ ) {
    var EmailExpiredModule = {
        init : function ( ) {
            this.default_value_for_templates() ;
            this.show_or_hide_for_sender_option() ;
            $( document ).on( 'change' , '#rs_pagination' , this.pagination_for_template ) ;
            $( '#rs_email_templates_table_expired' ).on( 'click' , '.rs_expired_mail_active' , this.activate_or_deactivate_emailexpiry_template ) ;
            $( '#rs_email_templates_table_expired' ).footable( ).on( 'click' , '.rs_delete_expired' , this.delete_template ) ;
            $( document ).on( 'click' , '#rs_save_new_expired_template' , this.save_template ) ;
            $( document ).on( 'click' , '#rs_save_new_expired_template' , this.edit_template ) ;
            $( document ).on( 'change' , '.rs_sender_opt_expired' , this.show_or_hide_for_sender_option ) ;
            $( document ).on( 'change' , '#rs_duration_type' , this.duration_type ) ;
        } ,
        show_or_hide_for_sender_option : function () {
            if ( jQuery( "#rs_sender_woo_expired" ).is( ":checked" ) ) {
                jQuery( ".rs_local_senders_expired" ).css( "display" , "none" ) ;
            } else {
                jQuery( ".rs_local_senders_expired" ).css( "display" , "table-row" ) ;
            }
        } ,
        duration_type : function () {
            jQuery( "span#rs_duration" ).html( jQuery( "#rs_duration_type" ).val() ) ;
        } ,
        get_tinymce_content : function ( ) {
            if ( $( "#wp-rs_email_new_expired-wrap" ).hasClass( "tmce-active" ) ) {
                return tinyMCE.activeEditor.getContent( ) ;
            } else {
                return $( "#rs_email_new_expired" ).val( ) ;
            }
        } ,
        get_tinymce_content_edit : function ( ) {
            if ( jQuery( "#wp-rs_email_expired_template_edit-wrap" ).hasClass( "tmce-active" ) ) {
                return tinyMCE.activeEditor.getContent() ;
            } else {
                return jQuery( "#rs_email_expired_template_edit" ).val() ;
            }
        } ,
        default_value_for_templates : function () {
            if ( fp_emailexpired_params.save_new_template ) {
                $( "#rs_expired_from_name" ).val( "Admin" ) ;
                $( "#rs_subject_expired" ).val( "SUMO Reward Points" ) ;
                jQuery( "#rs_expired_from_email" ).val( fp_emailexpired_params.admin_email ) ;
                jQuery( "#rs_duration_type" ).val( "days" ) ;
                jQuery( "#rs_duration" ).val( "1" ) ;
                jQuery( "#rs_email_new_expired" ).val( "Hi {rsfirstname} {rslastname}, <br><br>Please check the below table which shows about your earned points with an expiry date. You can make use of those points to get discount on future purchases in {rssitelink} {rs_points_expire} <br><br> Thanks" ) ;
            }
        } ,
        activate_or_deactivate_emailexpiry_template : function ( e ) {
            e.preventDefault( ) ;
            var row_id = $( this ).data( 'rsmailid' ) ;
            var obj = $( this ) ;
            jQuery( obj ).attr( 'disabled' , true ) ;
            var status = jQuery( this ).data( 'currentstate' ) ;
            var data = {
                action : "updatestatusforemailexpiry" ,
                row_id : row_id ,
                status : status ,
                sumo_security : fp_emailexpired_params.fp_update_status
            }
            $.post( fp_emailexpired_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    obj.data( 'currentstate' , response.data.content ) ;
                    if ( response.data.content == "ACTIVE" ) {
                        obj.text( "Deactivate" ) ;
                    } else {
                        obj.text( "Activate" ) ;
                    }
                    $( obj ).attr( 'disabled' , false ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        pagination_for_template : function ( e ) {
            e.preventDefault( ) ;
            var pageSize = $( this ).val( ) ;
            $( '.rs_email_template_table' ).data( 'page-size' , pageSize ) ;
            $( '.rs_email_template_table' ).trigger( 'footable_initialized' ) ;
        } ,
        save_template : function ( ) {
            if ( fp_emailexpired_params.save_new_template ) {
                $( this ).prop( "disabled" , true ) ;
                var templatename = $( "#rs_email_expired_name" ).val( ) ;
                var senderoption = $( "input:radio[name=rs_sender_opt_expired]:checked" ).val( ) ;
                var fromname = $( "#rs_expired_from_name" ).val( ) ;
                var fromemail = $( "#rs_expired_from_email" ).val( ) ;
                var subject = $( "#rs_subject_expired" ).val( ) ;
                var noofdays = $( "#rs_no_of_days" ).val( ) ;
                var message = EmailExpiredModule.get_tinymce_content( ) ;
                var templatestatus = $( "#rs_expired_template_status" ).val( ) ;
                var data = {
                    action : "newemailexpirytemplate" ,
                    senderoption : senderoption ,
                    templatename : templatename ,
                    fromname : fromname ,
                    fromemail : fromemail ,
                    subject : subject ,
                    noofdays : noofdays ,
                    message : message ,
                    templatestatus : templatestatus ,
                    sumo_security : fp_emailexpired_params.fp_new_template
                } ;
                $.post( fp_emailexpired_params.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        window.alert( response.data.content ) ;
                        $( "#rs_save_new_expired_template" ).prop( "disabled" , false ) ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
        } ,
        edit_template : function ( ) {
            if ( fp_emailexpired_params.save_edited_template ) {
                $( this ).prop( "disabled" , true ) ;
                var templatename = jQuery( "#rs_email_expired_name" ).val( ) ;
                var senderoption = jQuery( "input:radio[name=rs_sender_opt_expired]:checked" ).val( ) ;
                var fromname = $( "#rs_expired_from_name" ).val( ) ;
                var fromemail = $( "#rs_expired_from_email" ).val( ) ;
                var subject = $( "#rs_subject_expired" ).val( ) ;
                var noofdays = $( "#rs_no_of_days" ).val( ) ;
                var message = EmailExpiredModule.get_tinymce_content_edit( ) ;
                var templatestatus = $( "#rs_expired_template_status" ).val( ) ;
                var durationtype = jQuery( "#rs_duration_type" ).val( ) ;
                var mailduration = jQuery( "span #rs_duration" ).val( ) ;
                var templateid = fp_emailexpired_params.template_id ;
                var data = {
                    action : "editemailexpirytemplate" ,
                    senderoption : senderoption ,
                    templatename : templatename ,
                    fromname : fromname ,
                    fromemail : fromemail ,
                    subject : subject ,
                    noofdays : noofdays ,
                    message : message ,
                    durationtype : durationtype ,
                    mailduration : mailduration ,
                    templateid : templateid ,
                    templatestatus : templatestatus ,
                    sumo_security : fp_emailexpired_params.fp_edit_template
                } ;
                $.post( fp_emailexpired_params.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        window.alert( response.data.content ) ;
                        $( "#rs_save_new_expired_template" ).prop( "disabled" , false ) ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
        } ,
        delete_template : function ( e ) {
            e.preventDefault( ) ;
            var footable = $( '#rs_email_templates_table_expired' ).data( 'footable' ) ;
            var row = $( this ).parents( 'tr:first' ) ;
            footable.removeRow( row ) ;
            var data = {
                action : "deletetemplateforemailexpiry" ,
                row_id : $( this ).data( 'id' ) ,
                sumo_security : fp_emailexpired_params.fp_delete_template
            }
            $.post( fp_emailexpired_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {

                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
    } ;
    EmailExpiredModule.init() ;
} ) ;