/*
 * Email - Module
 */
jQuery( function ( $ ) {
    var EmailModule = {
        init : function () {
            this.trigger_on_page_load() ;
            this.default_value_for_templates() ;
            this.show_or_hide_for_sender_option() ;
            this.show_or_hide_for_mailsending_option() ;
            this.show_or_hide_for_user_option() ;
            this.add_note() ;
            $( document ).on( 'change' , '#rs_pagination' , this.pagination_for_template ) ;
            this.show_or_hide_for_enable_mail_for_thershold_points() ;
            $( document ).on( 'change' , '#rs_mail_enable_threshold_points' , this.enable_mail_for_thershold_points ) ;
            $( '#rs_email_templates_table' ).on( 'click' , '.rs_mail_active' , this.activate_or_deactivate_email_template ) ;
            $( document ).on( 'click' , '.rs_unsubscribe_user' , this.unsubscribe_selected_user ) ;
            $( '#rs_email_templates_table' ).footable( ).on( 'click' , '.rs_delete' , this.delete_template ) ;
            $( document ).on( 'click' , '#rs_save_new_template' , this.save_template ) ;
            $( document ).on( 'click' , '#rs_save_new_template' , this.edit_template ) ;
            $( document ).on( 'change' , '.rs_sender_opt' , this.show_or_hide_for_sender_option ) ;
            $( document ).on( 'change' , '#rs_duration_type' , this.duration_type ) ;
            $( document ).on( 'change' , '.rsmailsendingoptions' , this.show_or_hide_for_mailsending_option ) ;
            $( document ).on( 'change' , '.rs_sendmail_options_all' , this.show_or_hide_for_user_option ) ;
            $( document ).on( 'click' , '#send_button' , this.send_mail ) ;
            $( document ).on( 'click' , '#rs_select_mail_function' , this.add_note ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_email_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_multiselect_mail_send' ).chosen() ;
            } else {
                $( '#rs_multiselect_mail_send' ).select2() ;
            }
        } ,
        add_note : function () {
            if ( fp_email_params.fp_wc_version > '2.2.0' ) {
                if ( jQuery( '#rs_select_mail_function' ).val() === '1' ) {
                    jQuery( '.prependedrc' ).remove() ;
                    jQuery( '#rs_select_mail_function' ).parent().append( '<span class="prependedrc">For WooCommerce 2.3 or higher version mail() function will not load the WooCommerce default template. This option will be deprecated </span>' ) ;
                } else {
                    jQuery( '.prependedrc' ).remove() ;
                }
            }
        } ,
        show_or_hide_for_mailsending_option : function () {
            if ( jQuery( '.rsmailsendingoptions' ).filter( ':checked' ).val() === '3' ) {
                jQuery( '#earningpoints' ).parent().parent().hide() ;
                jQuery( '#redeemingpoints' ).parent().parent().hide() ;
            } else if ( jQuery( '.rsmailsendingoptions' ).filter( ':checked' ).val() === '2' ) {
                jQuery( '#earningpoints' ).parent().parent().hide() ;
                jQuery( '#redeemingpoints' ).parent().parent().show() ;
            } else {
                jQuery( '#earningpoints' ).parent().parent().show() ;
                jQuery( '#redeemingpoints' ).parent().parent().hide() ;
            }
        } ,
        show_or_hide_for_user_option : function () {
            if ( jQuery( '.rs_sendmail_options' ).filter( ':checked' ).val() == '1' ) {
                jQuery( '#rs_multiselect_mail_send' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_multiselect_mail_send' ).parent().parent().show() ;
            }

            jQuery( '.rs_sendmail_options' ).change( function () {
                if ( jQuery( '.rs_sendmail_options' ).filter( ':checked' ).val() == '1' ) {
                    jQuery( '#rs_multiselect_mail_send' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_multiselect_mail_send' ).parent().parent().show() ;
                }
            } ) ;
        } ,
        default_value_for_templates : function () {
            if ( fp_email_params.save_new_template ) {
                $( '#mailsendingoptions2' ).attr( 'checked' , 'checked' ) ;
                $( '#rsmailsendingoptions3' ).attr( 'checked' , 'checked' ) ;
                jQuery( "#rs_subject" ).val( "SUMO Reward Points" ) ;
                jQuery( "#rs_from_email" ).val( fp_email_params.admin_email ) ;
                jQuery( "#rs_duration_type" ).val( "days" ) ;
                jQuery( "#rs_duration" ).val( "1" ) ;
                jQuery( "#rs_email_template_new" ).val( "Hi {rsfirstname} {rslastname}, <br><br> You have Earned Reward Points: {rspoints} on {rssitelink}  <br><br> You can use this Reward Points to make discounted purchases on {rssitelink} <br><br> Thanks" ) ;
            }
        } ,
        duration_type : function () {
            jQuery( "span#rs_duration" ).html( jQuery( "#rs_duration_type" ).val() ) ;
        } ,
        show_or_hide_for_sender_option : function () {
            if ( jQuery( "#rs_sender_woo" ).is( ":checked" ) ) {
                jQuery( ".rs_local_senders" ).css( "display" , "none" ) ;
            } else {
                jQuery( ".rs_local_senders" ).css( "display" , "table-row" ) ;
            }
        } ,
        get_tinymce_content : function () {
            if ( $( "#wp-rs_email_template_new-wrap" ).hasClass( "tmce-active" ) ) {
                return tinyMCE.get( 'rs_email_template_new' ).getContent( ) ;
            } else {
                return $( "#rs_email_template_new" ).val() ;
            }
        } ,
        get_tinymce_content_edit : function () {
            if ( jQuery( "#wp-rs_email_template_edit-wrap" ).hasClass( "tmce-active" ) ) {
                return tinyMCE.get( 'rs_email_template_edit' ).getContent() ;
            } else {
                return jQuery( "#rs_email_template_edit" ).val() ;
            }
        } ,
        enable_mail_for_thershold_points : function () {
            EmailModule.show_or_hide_for_enable_mail_for_thershold_points() ;
        } ,
        show_or_hide_for_enable_mail_for_thershold_points : function () {
            if ( jQuery( '#rs_mail_enable_threshold_points' ).is( ':checked' ) == true ) {
                jQuery( '#rs_mail_threshold_points' ).closest( 'tr' ).show() ;
                jQuery( '#rs_email_subject_threshold_points' ).closest( 'tr' ).show() ;
                jQuery( '#rs_email_message_threshold_points' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_mail_threshold_points' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_subject_threshold_points' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_threshold_points' ).closest( 'tr' ).hide() ;
            }
        } ,
        pagination_for_template : function ( e ) {
            e.preventDefault() ;
            var pageSize = $( this ).val() ;
            $( '.rs_email_template_table' ).data( 'page-size' , pageSize ) ;
            $( '.rs_email_template_table' ).trigger( 'footable_initialized' ) ;
        } ,
        activate_or_deactivate_email_template : function ( e ) {
            e.preventDefault() ;
            var row_id = $( this ).data( 'rsmailid' ) ;
            var obj = $( this ) ;
            jQuery( obj ).attr( 'disabled' , true ) ;
            var status = jQuery( this ).data( 'currentstate' ) ;
            var data = {
                action : "updatestatusforemail" ,
                row_id : row_id ,
                status : status ,
                sumo_security : fp_email_params.fp_update_status
            }
            $.post( fp_email_params.ajaxurl , data , function ( response ) {
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
        unsubscribe_selected_user : function () {
            jQuery( '.gif_rs_sumo_reward_button_for_unsubscribe' ).css( 'display' , 'inline-block' ) ;
            var unsubscribe = jQuery( '#rs_select_user_to_unsubscribe' ).val() ;
            var emailsubject = jQuery( '#rs_subject_for_user_unsubscribe' ).val() ;
            var emailmessage = jQuery( '#rs_message_for_user_unsubscribe' ).val() ;
            var data = ( {
                action : 'unsubscribeuser' ,
                unsubscribe : unsubscribe ,
                emailsubject : emailsubject ,
                emailmessage : emailmessage ,
                sumo_security : fp_email_params.fp_unsubscribe_email
            } ) ;
            $.post( fp_email_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    console.log( 'Ajax Done Successfully' ) ;
                    jQuery( '.button-primary' ).trigger( 'click' ) ;
                    jQuery( '.gif_rs_sumo_reward_button_for_unsubscribe' ).css( 'display' , 'none' ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        save_template : function ( ) {
            if ( fp_email_params.save_new_template ) {
                $( this ).prop( "disabled" , true ) ;
                var templatename = $( "#rs_template_name" ).val( ) ;
                var senderoption = $( "input:radio[name=rs_sender_opt]:checked" ).val( ) ;
                var fromname = $( "#rs_from_name" ).val( ) ;
                var fromemail = $( "#rs_from_email" ).val( ) ;
                var subject = $( "#rs_subject" ).val( ) ;
                var message = EmailModule.get_tinymce_content( ) ;
                var templatestatus = $( "#rs_template_status" ).val( ) ;
                var multivalue = jQuery( '#rs_multiselect_mail_send' ).val() ;
                var sendmail = jQuery( 'input:radio[name=mailsendingoptions]:checked' ).val() ;
                var sendmailtypes = jQuery( 'input:radio[name=rsmailsendingoptions]:checked' ).val() ;
                var earningpoints = jQuery( '#earningpoints' ).val() ;
                var redeemingpoints = jQuery( '#redeemingpoints' ).val() ;
                var sendmailoptions = jQuery( "input:radio[name=rs_sendmail_options]:checked" ).val() ;
                var minuserpoints = jQuery( '#rs_minimum_userpoints' ).val() ;
                var durationtype = jQuery( "#rs_duration_type" ).val() ;
                var mailduration = jQuery( "span #rs_duration" ).val() ;
                var data = {
                    action : "newemailtemplate" ,
                    senderoption : senderoption ,
                    templatename : templatename ,
                    fromname : fromname ,
                    fromemail : fromemail ,
                    subject : subject ,
                    message : message ,
                    templatestatus : templatestatus ,
                    mailsendingoptions : sendmail ,
                    rsmailsendingoptions : sendmailtypes ,
                    earningpoints : earningpoints ,
                    redeemingpoints : redeemingpoints ,
                    minuserpoints : minuserpoints ,
                    sendmailoptions : sendmailoptions ,
                    sendmailselected : multivalue ,
                    durationtype : durationtype ,
                    mailduration : mailduration ,
                    sumo_security : fp_email_params.fp_new_template
                } ;
                $.post( fp_email_params.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        window.alert( response.data.content ) ;
                        $( "#rs_save_new_template" ).prop( "disabled" , false ) ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
        } ,
        edit_template : function ( ) {
            if ( fp_email_params.save_edited_template ) {
                $( this ).prop( "disabled" , true ) ;
                var templatename = $( "#rs_template_name" ).val( ) ;
                var senderoption = $( "input:radio[name=rs_sender_opt]:checked" ).val( ) ;
                var fromname = $( "#rs_from_name" ).val( ) ;
                var fromemail = $( "#rs_from_email" ).val( ) ;
                var subject = $( "#rs_subject" ).val( ) ;
                var message = EmailModule.get_tinymce_content( ) ;
                var templatestatus = $( "#rs_template_status" ).val( ) ;
                var multivalue = jQuery( '#rs_multiselect_mail_send' ).val() ;
                var sendmail = jQuery( 'input:radio[name=mailsendingoptions]:checked' ).val() ;
                var sendmailtypes = jQuery( 'input:radio[name=rsmailsendingoptions]:checked' ).val() ;
                var earningpoints = jQuery( '#earningpoints' ).val() ;
                var redeemingpoints = jQuery( '#redeemingpoints' ).val() ;
                var sendmailoptions = jQuery( "input:radio[name=rs_sendmail_options]:checked" ).val() ;
                var minuserpoints = jQuery( '#rs_minimum_userpoints' ).val() ;
                var durationtype = jQuery( "#rs_duration_type" ).val() ;
                var mailduration = jQuery( "span #rs_duration" ).val() ;
                var templateid = fp_email_params.template_id ;
                var data = {
                    action : "editemailtemplate" ,
                    senderoption : senderoption ,
                    templatename : templatename ,
                    fromname : fromname ,
                    fromemail : fromemail ,
                    subject : subject ,
                    message : message ,
                    mailsendingoptions : sendmail ,
                    rsmailsendingoptions : sendmailtypes ,
                    earningpoints : earningpoints ,
                    redeemingpoints : redeemingpoints ,
                    minuserpoints : minuserpoints ,
                    sendmailoptions : sendmailoptions ,
                    sendmailselected : multivalue ,
                    durationtype : durationtype ,
                    mailduration : mailduration ,
                    templateid : templateid ,
                    templatestatus : templatestatus ,
                    sumo_security : fp_email_params.fp_edit_template
                } ;
                $.post( fp_email_params.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        window.alert( response.data.content ) ;
                        $( "#rs_save_new_template" ).prop( "disabled" , false ) ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
        } ,
        delete_template : function ( e ) {
            e.preventDefault( ) ;
            EmailModule.block( '#rs_email_templates_table' ) ;
            var footable = $( '#rs_email_templates_table' ).data( 'footable' ) ;
            var row = $( this ).parents( 'tr:first' ) ;
            var data = {
                action : "deletetemplateforemail" ,
                row_id : $( this ).data( 'id' ) ,
                sumo_security : fp_email_params.fp_delete_template
            }
            $.post( fp_email_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    footable.removeRow( row ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
                EmailModule.unblock( '#rs_email_templates_table' ) ;
            } ) ;
        } ,
        send_mail : function () {
            var email_id = jQuery( '#rs_send_email_to' ).val() ;
            if ( email_id == '' ) {
                alert( 'Please enter the email id' ) ;
                return false ;
            }
            var rs_subject = jQuery( "#rs_subject" ).val() ;
            var rs_status_template = jQuery( "#rs_template_status" ).val() ;
            var rs_sender_options = jQuery( "input:radio[name=rs_sender_opt]:checked" ).val() ;
            var rs_from_name = jQuery( "#rs_from_name" ).val() ;
            var rs_from_email = jQuery( "#rs_from_email" ).val() ;
            var data = {
                action : "sendmail" ,
                email_id : email_id ,
                rs_subject : rs_subject ,
                rs_status_template : rs_status_template ,
                rs_sender_options : rs_sender_options ,
                rs_from_name : rs_from_name ,
                rs_from_email : rs_from_email ,
                rs_email_template_id : fp_email_params.template_id ,
                sumo_security : fp_email_params.fp_send_mail ,
            } ;
            $.post( fp_email_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    if ( response.data.content == 'Mail Sent' ) {
                        alert( "Email Sent Successfully" ) ;
                        jQuery( "#send_button" ).prop( "disabled" , false ) ;
                        jQuery( '#rs_send_email_to' ).val( '' ) ;
                    } else {
                        alert( "Email not Sent" ) ;
                    }
                } else {
                    window.alert( response.data.error ) ;
                    jQuery( '#rs_send_email_to' ).val( '' ) ;
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
    EmailModule.init() ;
} ) ;