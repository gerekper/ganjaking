jQuery( function ( $ ) {

    function checkemail( email ) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/ ;
        return regex.test( email ) ;
    }
    var RSReferAFriend = {
        init : function () {
            $( document ).on( 'click' , '.rs_send_mail_to_friend' , this.send_referral_link_to_friend ) ;
        } ,
        send_referral_link_to_friend : function ( evt ) {
            evt.preventDefault() ;
            var firstname = jQuery( '#rs_friend_name' ).val() ;
            var friendemail = jQuery( '#rs_friend_email' ).val() ;
            var friendmessage = jQuery( '#rs_your_message' ).val() ;
            var friendsubject = jQuery( '#rs_friend_subject' ).val() ;

            if ( firstname === '' ) {
                jQuery( '#rs_friend_name' ).css( 'border' , '2px solid red' ) ;
                jQuery( '#rs_friend_name' ).parent().find( '.rs_notification' ).css( 'color' , 'red' ) ;
                jQuery( '#rs_friend_name' ).parent().find( '.rs_notification' ).html( fp_referafriend_from_params.refnameerrormsg ).css( 'color' , 'red' ) ;
                return false ;
            } else {
                jQuery( '#rs_friend_name' ).css( 'border' , '' ) ;
                jQuery( '#rs_friend_name' ).parent().find( '.rs_notification' ).html( '' ) ;
            }
            if ( friendemail === '' ) {
                jQuery( '#rs_friend_email' ).css( 'border' , '2px solid red' ) ;
                jQuery( '#rs_friend_email' ).parent().find( '.rs_notification' ).css( 'color' , 'red' ) ;
                jQuery( '#rs_friend_email' ).parent().find( '.rs_notification' ).html( fp_referafriend_from_params.refmailiderrormsg ).css( 'color' , 'red' ) ;
                return false ;
            } else {
                jQuery( '#rs_friend_email' ).css( 'border' , '' ) ;
                jQuery( '#rs_friend_email' ).parent().find( '.rs_notification' ).html( '' ) ;
            }
            var emailArray = friendemail.split( "," ) ;
            for ( i = 0 ; i <= ( emailArray.length - 1 ) ; i ++ ) {
                if ( checkemail( emailArray[i] ) ) {
                    //Do what ever with the email.
                    jQuery( '#rs_friend_email' ).css( 'border' , '' ) ;
                    jQuery( '#rs_friend_email' ).parent().find( '.rs_notification' ).html( '' ) ;
                } else {
                    jQuery( '#rs_friend_email' ).css( 'border' , '2px solid red' ) ;
                    jQuery( '#rs_friend_email' ).parent().find( '.rs_notification' ).css( 'color' , 'red' ) ;
                    jQuery( '#rs_friend_email' ).parent().find( '.rs_notification' ).html( fp_referafriend_from_params.invalidemail ) ;
                    return false ;
                }
            }

            if ( friendsubject === '' ) {
                jQuery( '#rs_friend_subject' ).css( 'border' , '2px solid red' ) ;
                jQuery( '#rs_friend_subject' ).parent().find( '.rs_notification' ).css( 'color' , 'red' ) ;
                jQuery( '#rs_friend_subject' ).parent().find( '.rs_notification' ).html( fp_referafriend_from_params.subjecterror ) ;
                return false ;
            } else {
                jQuery( '#rs_friend_subject' ).css( 'border' , '' ) ;
                jQuery( '#rs_friend_subject' ).parent().find( '.rs_notification' ).html( '' ) ;
            }
            if ( friendmessage === '' ) {
                jQuery( '#rs_your_message' ).css( 'border' , '2px solid red' ) ;
                jQuery( '#rs_your_message' ).parent().find( '.rs_notification' ).css( 'color' , 'red' ) ;
                jQuery( '#rs_your_message' ).parent().find( '.rs_notification' ).html( fp_referafriend_from_params.messageerror ) ;
                return false ;
            } else {
                jQuery( '#rs_your_message' ).css( 'border' , '' ) ;
                jQuery( '#rs_your_message' ).parent().find( '.rs_notification' ).html( '' ) ;
            }
            var enableterms = fp_referafriend_from_params.enableterms ;
            if ( enableterms == '2' ) {
                var terms = jQuery( '#rs_terms' ).is( ':checked' ) ? 'yes' : 'no' ;
                if ( terms == 'no' ) {
                    //jQuery('#rs_terms').parent().find('.rs_notification').css('color', 'red');
                    jQuery( ".iagreeerror" ).css( "display" , "block" ) ;
                    jQuery( ".iagreeerror" ).css( "color" , "red" ) ;
                    return false ;
                }
            }
            RSReferAFriend.block( '#rs_refer_a_friend_form' ) ;
            var data = {
                action : 'rs_refer_a_friend_ajax' ,
                friendname : firstname ,
                friendemail : friendemail ,
                friendsubject : friendsubject ,
                friendmessage : friendmessage ,
                sumo_security : fp_referafriend_from_params.send_mail
            } ;
            $.post( fp_referafriend_from_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    jQuery( '.rs_notification_final' ).css( 'color' , 'green' ) ;
                    document.getElementById( "rs_refer_a_friend_form" ).reset() ;
                    jQuery( ".rs_notification_final" ).css( "display" , "block" ) ;
                    jQuery( '.rs_notification_final' ).html( fp_referafriend_from_params.successmessage ) ;
                    jQuery( '.rs_notification_final' ).fadeOut( 6000 ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
                RSReferAFriend.unblock( '#rs_refer_a_friend_form' ) ;
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
    RSReferAFriend.init() ;
} ) ;




