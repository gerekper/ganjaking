/*
 * Keyup Validation in Product Settings
 */
jQuery( function () {
    if ( jQuery( '#rs_send_mail_payment_gateway' ).is( ':checked' ) == true ) {
        jQuery( '#rs_email_subject_payment_gateway' ).closest( 'tr' ).show() ;
        jQuery( '#rs_email_message_payment_gateway' ).closest( 'tr' ).show() ;
    } else {
        jQuery( '#rs_email_subject_payment_gateway' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_email_message_payment_gateway' ).closest( 'tr' ).hide() ;
    }

    jQuery( '#rs_send_mail_payment_gateway' ).change( function () {
        if ( jQuery( '#rs_send_mail_payment_gateway' ).is( ':checked' ) == true ) {
            jQuery( '#rs_email_subject_payment_gateway' ).closest( 'tr' ).show() ;
            jQuery( '#rs_email_message_payment_gateway' ).closest( 'tr' ).show() ;
        } else {
            jQuery( '#rs_email_subject_payment_gateway' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_email_message_payment_gateway' ).closest( 'tr' ).hide() ;
        }
    } ) ;

    if ( jQuery( '#rs_send_mail_point_url' ).is( ':checked' ) == true ) {
        jQuery( '#rs_email_subject_point_url' ).closest( 'tr' ).show() ;
        jQuery( '#rs_email_message_point_url' ).closest( 'tr' ).show() ;
    } else {
        jQuery( '#rs_email_subject_point_url' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_email_message_point_url' ).closest( 'tr' ).hide() ;
    }

    jQuery( '#rs_send_mail_point_url' ).change( function () {
        if ( jQuery( '#rs_send_mail_point_url' ).is( ':checked' ) == true ) {
            jQuery( '#rs_email_subject_point_url' ).closest( 'tr' ).show() ;
            jQuery( '#rs_email_message_point_url' ).closest( 'tr' ).show() ;
        } else {
            jQuery( '#rs_email_subject_point_url' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_email_message_point_url' ).closest( 'tr' ).hide() ;
        }
    } ) ;
    // Confirm Dialog for Resetting the Tab

    jQuery( '#resettab' ).click( function ( e ) {
        var status = confirm( rewardsystem.reset_confirm_msg ) ;
        if ( status === true ) {
        } else {
            e.preventDefault() ;
        }
    } ) ;

    jQuery( '.button-primary' ).click( function ( e ) {
        if ( jQuery( '#_rs_select_referral_points_referee_time_content' ).val() != '' && jQuery( '#_rs_days_for_redeeming_points' ).val() != '' ) {
            var referral_points_referee_time_content = Number( jQuery( '#_rs_select_referral_points_referee_time_content' ).val() ) ;
            var days_for_redeeming_points = Number( jQuery( '#_rs_days_for_redeeming_points' ).val() ) ;
            if ( referral_points_referee_time_content > days_for_redeeming_points ) {
                e.preventDefault() ;
                jQuery( '#_rs_days_for_redeeming_points' ).focus() ;
                jQuery( "#_rs_days_for_redeeming_points" ).after( "<div class='validation' style='color:red;margin-bottom: 20px;'>Please enter</div>" ) ;
            }
        }
    } ) ;


    //To show or hide gift icon
    if ( jQuery( '#_rs_enable_disable_gift_icon' ).val() == '2' ) {
        jQuery( '#rs_image_url_upload' ).parent().parent().hide() ;
    } else {
        jQuery( '#rs_image_url_upload' ).parent().parent().show() ;
    }

    jQuery( '#_rs_enable_disable_gift_icon' ).change( function () {
        if ( jQuery( '#_rs_enable_disable_gift_icon' ).val() == '2' ) {
            jQuery( '#rs_image_url_upload' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_image_url_upload' ).parent().parent().show() ;
        }
    } ) ;

    /*Show or Hide for bbPress - Start*/
    if ( jQuery( '#rs_enable_reward_points_for_create_topic' ).is( ":checked" ) == false ) {
        jQuery( '#rs_reward_points_for_creatic_topic' ).parent().parent().hide() ;
    } else {
        jQuery( '#rs_reward_points_for_creatic_topic' ).parent().parent().show() ;
    }

    jQuery( '#rs_enable_reward_points_for_create_topic' ).change( function () {
        if ( jQuery( '#rs_enable_reward_points_for_create_topic' ).is( ":checked" ) == false ) {
            jQuery( '#rs_reward_points_for_creatic_topic' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_reward_points_for_creatic_topic' ).parent().parent().show() ;
        }
    } ) ;

    if ( jQuery( '#rs_enable_reward_points_for_reply_topic' ).is( ":checked" ) == false ) {
        jQuery( '#rs_reward_points_for_reply_topic' ).parent().parent().hide() ;
    } else {
        jQuery( '#rs_reward_points_for_reply_topic' ).parent().parent().show() ;
    }

    jQuery( '#rs_enable_reward_points_for_reply_topic' ).change( function () {
        if ( jQuery( '#rs_enable_reward_points_for_reply_topic' ).is( ":checked" ) == false ) {
            jQuery( '#rs_reward_points_for_reply_topic' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_reward_points_for_reply_topic' ).parent().parent().show() ;
        }
    } ) ;
    /*Show or Hide bbPress - End*/

    if ( jQuery( '#rs_local_enable_disable_reward' ).val() == '2' ) {
        jQuery( '.show_if_enable_in_reward' ).parent().parent().hide() ;
    } else {
        jQuery( '.show_if_enable_in_reward' ).parent().parent().show() ;

        if ( jQuery( '#rs_local_reward_type' ).val() === '1' ) {
            jQuery( '#rs_local_reward_points' ).parent().parent().show() ;
            jQuery( '#rs_local_reward_percent' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_reward_points' ).parent().parent().hide() ;
            jQuery( '#rs_local_reward_percent' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_reward_type' ).change( function () {
            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_reward_points' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent' ).parent().parent().show() ;
            }
        } ) ;
    }

    jQuery( '#rs_local_enable_disable_reward' ).change( function () {
        if ( jQuery( this ).val() == '2' ) {
            jQuery( '.show_if_enable_in_reward' ).parent().parent().hide() ;
        } else {
            jQuery( '.show_if_enable_in_reward' ).parent().parent().show() ;

            if ( jQuery( '#rs_local_reward_type' ).val() === '1' ) {
                jQuery( '#rs_local_reward_points' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_reward_type' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_reward_points' ).parent().parent().show() ;
                    jQuery( '#rs_local_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_reward_points' ).parent().parent().hide() ;
                    jQuery( '#rs_local_reward_percent' ).parent().parent().show() ;
                }
            } ) ;
        }
    } ) ;

    if ( jQuery( '#rs_local_enable_disable_referral_reward' ).val() == '2' ) {
        jQuery( '.show_if_enable_in_update_referral' ).parent().parent().hide() ;
        jQuery( '#rs_send_mail_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_send_mail_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
    } else {
        jQuery( '.show_if_enable_in_update_referral' ).parent().parent().show() ;

        if ( jQuery( '#rs_local_referral_reward_type' ).val() === '1' ) {
            jQuery( '#rs_local_referral_reward_point' ).parent().parent().show() ;
            jQuery( '#rs_local_referral_reward_percent' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_referral_reward_point' ).parent().parent().hide() ;
            jQuery( '#rs_local_referral_reward_percent' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_referral_reward_type' ).change( function () {
            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_referral_reward_point' ).parent().parent().show() ;
                jQuery( '#rs_local_referral_reward_percent' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_referral_reward_point' ).parent().parent().hide() ;
                jQuery( '#rs_local_referral_reward_percent' ).parent().parent().show() ;
            }
        } ) ;

        if ( jQuery( '#rs_local_referral_reward_type_get_refer' ).val() === '1' ) {
            jQuery( '#rs_local_referral_reward_point_for_getting_referred' ).parent().parent().show() ;
            jQuery( '#rs_local_referral_reward_percent_for_getting_referred' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_referral_reward_point_for_getting_referred' ).parent().parent().hide() ;
            jQuery( '#rs_local_referral_reward_percent_for_getting_referred' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_referral_reward_type_get_refer' ).change( function () {
            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_referral_reward_point_for_getting_referred' ).parent().parent().show() ;
                jQuery( '#rs_local_referral_reward_percent_for_getting_referred' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_referral_reward_point_for_getting_referred' ).parent().parent().hide() ;
                jQuery( '#rs_local_referral_reward_percent_for_getting_referred' ).parent().parent().show() ;
            }
        } ) ;
        jQuery( '#rs_send_mail_pdt_purchase_referral' ).closest( 'tr' ).show() ;
        if ( jQuery( '#rs_send_mail_pdt_purchase_referral' ).is( ':checked' ) ) {
            jQuery( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).show() ;
            jQuery( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).show() ;
        } else {
            jQuery( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
        }
        jQuery( '#rs_send_mail_pdt_purchase_referral' ).change( function () {
            if ( jQuery( '#rs_send_mail_pdt_purchase_referral' ).is( ':checked' ) ) {
                jQuery( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).show() ;
                jQuery( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
            }
        } ) ;

        jQuery( '#rs_send_mail_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
        if ( jQuery( '#rs_send_mail_pdt_purchase_referrer' ).is( ':checked' ) ) {
            jQuery( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
            jQuery( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
        } else {
            jQuery( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
        }
        jQuery( '#rs_send_mail_pdt_purchase_referrer' ).change( function () {
            if ( jQuery( '#rs_send_mail_pdt_purchase_referrer' ).is( ':checked' ) ) {
                jQuery( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
                jQuery( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
            }
        } ) ;
    }

    jQuery( '#rs_local_enable_disable_referral_reward' ).change( function () {
        if ( jQuery( '#rs_local_enable_disable_referral_reward' ).val() == '2' ) {
            jQuery( '.show_if_enable_in_update_referral' ).parent().parent().hide() ;
            jQuery( '#rs_send_mail_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_send_mail_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
        } else {
            jQuery( '.show_if_enable_in_update_referral' ).parent().parent().show() ;

            if ( jQuery( '#rs_local_referral_reward_type' ).val() === '1' ) {
                jQuery( '#rs_local_referral_reward_point' ).parent().parent().show() ;
                jQuery( '#rs_local_referral_reward_percent' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_referral_reward_point' ).parent().parent().hide() ;
                jQuery( '#rs_local_referral_reward_percent' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_referral_reward_type' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_referral_reward_point' ).parent().parent().show() ;
                    jQuery( '#rs_local_referral_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_referral_reward_point' ).parent().parent().hide() ;
                    jQuery( '#rs_local_referral_reward_percent' ).parent().parent().show() ;
                }
            } ) ;

            if ( jQuery( '#rs_local_referral_reward_type_get_refer' ).val() === '1' ) {
                jQuery( '#rs_local_referral_reward_point_for_getting_referred' ).parent().parent().show() ;
                jQuery( '#rs_local_referral_reward_percent_for_getting_referred' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_referral_reward_point_for_getting_referred' ).parent().parent().hide() ;
                jQuery( '#rs_local_referral_reward_percent_for_getting_referred' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_referral_reward_type_get_refer' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_referral_reward_point_for_getting_referred' ).parent().parent().show() ;
                    jQuery( '#rs_local_referral_reward_percent_for_getting_referred' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_referral_reward_point_for_getting_referred' ).parent().parent().hide() ;
                    jQuery( '#rs_local_referral_reward_percent_for_getting_referred' ).parent().parent().show() ;
                }
            } ) ;

            jQuery( '#rs_send_mail_pdt_purchase_referral' ).closest( 'tr' ).show() ;
            if ( jQuery( '#rs_send_mail_pdt_purchase_referral' ).is( ':checked' ) ) {
                jQuery( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).show() ;
                jQuery( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
            }
            jQuery( '#rs_send_mail_pdt_purchase_referral' ).change( function () {
                if ( jQuery( '#rs_send_mail_pdt_purchase_referral' ).is( ':checked' ) ) {
                    jQuery( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_pdt_purchase_referral' ).closest( 'tr' ).hide() ;
                }
            } ) ;

            jQuery( '#rs_send_mail_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
            if ( jQuery( '#rs_send_mail_pdt_purchase_referrer' ).is( ':checked' ) ) {
                jQuery( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
                jQuery( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
            }
            jQuery( '#rs_send_mail_pdt_purchase_referrer' ).change( function () {
                if ( jQuery( '#rs_send_mail_pdt_purchase_referrer' ).is( ':checked' ) ) {
                    jQuery( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_pdt_purchase_referrer' ).closest( 'tr' ).hide() ;
                }
            } ) ;
        }
    } ) ;

    /*
     * End of Show or Hide For Reward Points In Update
     */


    /*
     * Show or Hide For Social Reward Points In Update
     */
    if ( jQuery( '#rs_local_enable_disable_social_reward' ).val() == '2' ) {
        jQuery( '.show_if_social_enable_in_update' ).parent().parent().hide() ;
    } else {
        jQuery( '.show_if_social_enable_in_update' ).parent().parent().show() ;

        if ( jQuery( '#rs_local_reward_type_for_facebook' ).val() === '1' ) {
            jQuery( '#rs_local_reward_points_facebook' ).parent().parent().show() ;
            jQuery( '#rs_local_reward_percent_facebook' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_reward_points_facebook' ).parent().parent().hide() ;
            jQuery( '#rs_local_reward_percent_facebook' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_reward_type_for_facebook' ).change( function () {
            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_reward_points_facebook' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_facebook' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_facebook' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_facebook' ).parent().parent().show() ;
            }
        } ) ;

        if ( jQuery( '#rs_local_reward_type_for_facebook_share' ).val() === '1' ) {
            jQuery( '#rs_local_reward_points_facebook_share' ).parent().parent().show() ;
            jQuery( '#rs_local_reward_percent_facebook_share' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_reward_points_facebook_share' ).parent().parent().hide() ;
            jQuery( '#rs_local_reward_percent_facebook_share' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_reward_type_for_facebook_share' ).change( function () {
            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_reward_points_facebook_share' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_facebook_share' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_facebook_share' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_facebook_share' ).parent().parent().show() ;
            }
        } ) ;
        if ( jQuery( '#rs_local_reward_type_for_twitter' ).val() === '1' ) {
            jQuery( '#rs_local_reward_points_twitter' ).parent().parent().show() ;
            jQuery( '#rs_local_reward_percent_twitter' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_reward_points_twitter' ).parent().parent().hide() ;
            jQuery( '#rs_local_reward_percent_twitter' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_reward_type_for_twitter' ).change( function () {
            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_reward_points_twitter' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_twitter' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_twitter' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_twitter' ).parent().parent().show() ;
            }
        } ) ;
        if ( jQuery( '#rs_local_reward_type_for_twitter_follow' ).val() === '1' ) {
            jQuery( '#rs_local_reward_points_twitter_follow' ).parent().parent().show() ;
            jQuery( '#rs_local_reward_percent_twitter_follow' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_reward_points_twitter_follow' ).parent().parent().hide() ;
            jQuery( '#rs_local_reward_percent_twitter_follow' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_reward_type_for_twitter_follow' ).change( function () {

            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_reward_points_twitter_follow' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_twitter_follow' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_twitter_follow' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_twitter_follow' ).parent().parent().show() ;
            }
        } ) ;
        if ( jQuery( '#rs_local_reward_type_for_vk' ).val() === '1' ) {
            jQuery( '#rs_local_reward_points_vk' ).parent().parent().show() ;
            jQuery( '#rs_local_reward_percent_vk' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_reward_points_vk' ).parent().parent().hide() ;
            jQuery( '#rs_local_reward_percent_vk' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_reward_type_for_vk' ).change( function () {
            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_reward_points_vk' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_vk' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_vk' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_vk' ).parent().parent().show() ;
            }
        } ) ;

        if ( jQuery( '#rs_local_reward_type_for_ok_follow' ).val() === '1' ) {
            jQuery( '#rs_local_reward_points_ok_follow' ).parent().parent().show() ;
            jQuery( '#rs_local_reward_percent_ok_follow' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_reward_points_ok_follow' ).parent().parent().hide() ;
            jQuery( '#rs_local_reward_percent_ok_follow' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_reward_type_for_ok_follow' ).change( function () {

            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_reward_points_ok_follow' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_ok_follow' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_ok_follow' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_ok_follow' ).parent().parent().show() ;
            }
        } ) ;
        if ( jQuery( '#rs_local_reward_type_for_instagram' ).val() === '1' ) {
            jQuery( '#rs_local_reward_points_instagram' ).parent().parent().show() ;
            jQuery( '#rs_local_reward_percent_instagram' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_reward_points_instagram' ).parent().parent().hide() ;
            jQuery( '#rs_local_reward_percent_instagram' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_reward_type_for_instagram' ).change( function () {
            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_reward_points_instagram' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_instagram' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_instagram' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_instagram' ).parent().parent().show() ;
            }
        } ) ;
        if ( jQuery( '#rs_local_reward_type_for_google' ).val() === '1' ) {
            jQuery( '#rs_local_reward_points_google' ).parent().parent().show() ;
            jQuery( '#rs_local_reward_percent_google' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_local_reward_points_google' ).parent().parent().hide() ;
            jQuery( '#rs_local_reward_percent_google' ).parent().parent().show() ;
        }

        jQuery( '#rs_local_reward_type_for_google' ).change( function () {
            if ( ( jQuery( this ).val() ) === '1' ) {
                jQuery( '#rs_local_reward_points_google' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_google' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_google' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_google' ).parent().parent().show() ;
            }
        } ) ;
    }

    jQuery( '#rs_local_enable_disable_social_reward' ).change( function () {
        if ( jQuery( this ).val() == '2' ) {
            jQuery( '.show_if_social_enable_in_update' ).parent().parent().hide() ;
        } else {
            jQuery( '.show_if_social_enable_in_update' ).parent().parent().show() ;

            if ( jQuery( '#rs_local_reward_type_for_facebook' ).val() === '1' ) {
                jQuery( '#rs_local_reward_points_facebook' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_facebook' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_facebook' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_facebook' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_reward_type_for_facebook' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_reward_points_facebook' ).parent().parent().show() ;
                    jQuery( '#rs_local_reward_percent_facebook' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_reward_points_facebook' ).parent().parent().hide() ;
                    jQuery( '#rs_local_reward_percent_facebook' ).parent().parent().show() ;
                }
            } ) ;

            if ( jQuery( '#rs_local_reward_type_for_facebook_share' ).val() === '1' ) {
                jQuery( '#rs_local_reward_points_facebook_share' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_facebook_share' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_facebook_share' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_facebook_share' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_reward_type_for_facebook_share' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_reward_points_facebook_share' ).parent().parent().show() ;
                    jQuery( '#rs_local_reward_percent_facebook_share' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_reward_points_facebook_share' ).parent().parent().hide() ;
                    jQuery( '#rs_local_reward_percent_facebook_share' ).parent().parent().show() ;
                }
            } ) ;

            if ( jQuery( '#rs_local_reward_type_for_twitter' ).val() === '1' ) {
                jQuery( '#rs_local_reward_points_twitter' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_twitter' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_twitter' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_twitter' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_reward_type_for_twitter' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_reward_points_twitter' ).parent().parent().show() ;
                    jQuery( '#rs_local_reward_percent_twitter' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_reward_points_twitter' ).parent().parent().hide() ;
                    jQuery( '#rs_local_reward_percent_twitter' ).parent().parent().show() ;
                }
            } ) ;

            if ( jQuery( '#rs_local_reward_type_for_twitter_follow' ).val() === '1' ) {
                jQuery( '#rs_local_reward_points_twitter_follow' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_twitter_follow' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_twitter_follow' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_twitter_follow' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_reward_type_for_twitter_follow' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_reward_points_twitter_follow' ).parent().parent().show() ;
                    jQuery( '#rs_local_reward_percent_twitter_follow' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_reward_points_twitter_follow' ).parent().parent().hide() ;
                    jQuery( '#rs_local_reward_percent_twitter_follow' ).parent().parent().show() ;
                }
            } ) ;
            if ( jQuery( '#rs_local_reward_type_for_vk' ).val() === '1' ) {
                jQuery( '#rs_local_reward_points_vk' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_vk' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_vk' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_vk' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_reward_type_for_vk' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_reward_points_vk' ).parent().parent().show() ;
                    jQuery( '#rs_local_reward_percent_vk' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_reward_points_vk' ).parent().parent().hide() ;
                    jQuery( '#rs_local_reward_percent_vk' ).parent().parent().show() ;
                }
            } ) ;
            if ( jQuery( '#rs_local_reward_type_for_instagram' ).val() === '1' ) {
                jQuery( '#rs_local_reward_points_instagram' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_instagram' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_instagram' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_instagram' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_reward_type_for_instagram' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_reward_points_instagram' ).parent().parent().show() ;
                    jQuery( '#rs_local_reward_percent_instagram' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_reward_points_instagram' ).parent().parent().hide() ;
                    jQuery( '#rs_local_reward_percent_instagram' ).parent().parent().show() ;
                }
            } ) ;
            if ( jQuery( '#rs_local_reward_type_for_google' ).val() === '1' ) {
                jQuery( '#rs_local_reward_points_google' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_google' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_google' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_google' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_reward_type_for_google' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_reward_points_google' ).parent().parent().show() ;
                    jQuery( '#rs_local_reward_percent_google' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_reward_points_google' ).parent().parent().hide() ;
                    jQuery( '#rs_local_reward_percent_google' ).parent().parent().show() ;
                }
            } ) ;

            if ( jQuery( '#rs_local_reward_type_for_ok_follow' ).val() === '1' ) {
                jQuery( '#rs_local_reward_points_ok_follow' ).parent().parent().show() ;
                jQuery( '#rs_local_reward_percent_ok_follow' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_local_reward_points_ok_follow' ).parent().parent().hide() ;
                jQuery( '#rs_local_reward_percent_ok_follow' ).parent().parent().show() ;
            }

            jQuery( '#rs_local_reward_type_for_ok_follow' ).change( function () {
                if ( ( jQuery( this ).val() ) === '1' ) {
                    jQuery( '#rs_local_reward_points_ok_follow' ).parent().parent().show() ;
                    jQuery( '#rs_local_reward_percent_ok_follow' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_local_reward_points_ok_follow' ).parent().parent().hide() ;
                    jQuery( '#rs_local_reward_percent_ok_follow' ).parent().parent().show() ;
                }
            } ) ;
        }
    } ) ;

    /*Show or hide Settings for Bulk Update Tab - End*/

    /*Show or hide Settings for Import/Export Tab - Start*/

    if ( ( jQuery( 'input[name=rs_export_import_user_option]:checked' ).val() ) === '2' ) {
        jQuery( '#rs_import_export_users_list' ).parent().parent().show() ;
    } else {
        jQuery( '#rs_import_export_users_list' ).parent().parent().hide() ;
    }
    jQuery( 'input[name=rs_export_import_user_option]:radio' ).change( function () {
        jQuery( '#rs_import_export_users_list' ).parent().parent().toggle() ;
    } ) ;

    if ( ( jQuery( 'input[name=rs_export_import_date_option]:checked' ).val() ) === '2' ) {
        jQuery( '#rs_point_export_start_date' ).parent().parent().show() ;
        jQuery( '#rs_point_export_end_date' ).parent().parent().show() ;
    } else {
        jQuery( '#rs_point_export_start_date' ).parent().parent().hide() ;
        jQuery( '#rs_point_export_end_date' ).parent().parent().hide() ;
    }
    jQuery( 'input[name=rs_export_import_date_option]:radio' ).change( function () {
        jQuery( '#rs_point_export_start_date' ).parent().parent().toggle() ;
        jQuery( '#rs_point_export_end_date' ).parent().parent().toggle() ;
    } ) ;

    /*Show or hide Settings for Import/Export Tab - End*/

    /*Show or hide Settings for Report in CSV Tab - Start*/

    if ( ( jQuery( 'input[name=rs_export_user_report_option]:checked' ).val() ) === '2' ) {
        jQuery( '#rs_export_users_report_list' ).parent().parent().show() ;
    } else {
        jQuery( '#rs_export_users_report_list' ).parent().parent().hide() ;
    }
    jQuery( 'input[name=rs_export_user_report_option]:radio' ).change( function () {
        jQuery( '#rs_export_users_report_list' ).parent().parent().toggle() ;
    } ) ;

    if ( ( jQuery( 'input[name=rs_export_report_date_option]:checked' ).val() ) === '2' ) {
        jQuery( '#rs_point_export_report_start_date' ).parent().parent().show() ;
        jQuery( '#rs_point_export_report_end_date' ).parent().parent().show() ;
    } else {
        jQuery( '#rs_point_export_report_start_date' ).parent().parent().hide() ;
        jQuery( '#rs_point_export_report_end_date' ).parent().parent().hide() ;
    }
    jQuery( 'input[name=rs_export_report_date_option]:radio' ).change( function () {
        jQuery( '#rs_point_export_report_start_date' ).parent().parent().toggle() ;
        jQuery( '#rs_point_export_report_end_date' ).parent().parent().toggle() ;
    } ) ;

    /*Show or hide Settings for Report in CSV Tab - End*/

    /*Show or hide Settings for Form for Send Points Tab - Start*/
    if ( jQuery( '#rs_enable_msg_for_send_point' ).val() == '1' ) {
        jQuery( '#rs_select_send_points_user_type' ).closest( 'tr' ).show() ;
        /*Show or Hide for User Selection - Start*/
        if ( jQuery( '#rs_select_send_points_user_type' ).val() == '1' ) {
            jQuery( '#rs_select_users_list_for_send_point' ).parent().parent().hide() ;
        }
        jQuery( '#rs_select_send_points_user_type' ).change( function () {
            if ( jQuery( this ).val() == '2' ) {
                jQuery( '#rs_select_users_list_for_send_point' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_select_users_list_for_send_point' ).parent().parent().hide() ;
            }
        } ) ;
        /*Show or Hide for User Selection - End*/
        jQuery( '#rs_total_send_points_request' ).closest( 'tr' ).show() ;
        jQuery( '#rs_points_to_send_request' ).closest( 'tr' ).show() ;
        jQuery( '#rs_select_user_label' ).closest( 'tr' ).show() ;
        jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).show() ;
        jQuery( '#rs_select_points_submit_label' ).closest( 'tr' ).show() ;
        jQuery( '#rs_request_approval_type' ).closest( 'tr' ).show() ;
        jQuery( '#rs_limit_for_send_point' ).closest( 'tr' ).show() ;
        jQuery( '#rs_reason_for_send_points' ).closest( 'tr' ).show() ;

        /*Show or Hide for Maximum Restriction Points to Send - Start*/
        if ( jQuery( '#rs_limit_for_send_point' ).val() == '1' ) {
            jQuery( '#rs_limit_send_points_request' ).parent().parent().show() ;
            jQuery( '#rs_err_when_point_greater_than_limit' ).parent().parent().show() ;
        } else {
            jQuery( '#rs_limit_send_points_request' ).parent().parent().hide() ;
            jQuery( '#rs_err_when_point_greater_than_limit' ).parent().parent().hide() ;
        }
        jQuery( '#rs_limit_for_send_point' ).change( function () {
            if ( jQuery( this ).val() == '1' ) {
                jQuery( '#rs_limit_send_points_request' ).parent().parent().show() ;
                jQuery( '#rs_err_when_point_greater_than_limit' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_limit_send_points_request' ).parent().parent().hide() ;
                jQuery( '#rs_err_when_point_greater_than_limit' ).parent().parent().hide() ;
            }
        } ) ;
        /*Show or Hide for Maximum Restriction Points to Send - End*/
        jQuery( '#rs_request_approval_type' ).closest( 'tr' ).show() ;

        /*Show or Hide for Approval Type - Start*/
        if ( jQuery( '#rs_request_approval_type' ).val() == '1' ) {
            jQuery( '#rs_message_send_point_request_submitted' ).parent().parent().show() ;
            jQuery( '#rs_message_send_point_request_submitted_for_auto' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_message_send_point_request_submitted' ).parent().parent().hide() ;
            jQuery( '#rs_message_send_point_request_submitted_for_auto' ).parent().parent().show() ;
        }
        jQuery( '#rs_request_approval_type' ).change( function () {
            if ( jQuery( '#rs_request_approval_type' ).val() == '1' ) {
                jQuery( '#rs_message_send_point_request_submitted' ).parent().parent().show() ;
                jQuery( '#rs_message_send_point_request_submitted_for_auto' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_message_send_point_request_submitted' ).parent().parent().hide() ;
                jQuery( '#rs_message_send_point_request_submitted_for_auto' ).parent().parent().show() ;
            }
        } ) ;
        /*Show or Hide for Approval Type - End*/

        jQuery( '#rs_send_points_user_selection_field' ).closest( 'tr' ).show() ;
        jQuery( '#rs_send_points_user_selection_field' ).change( function () {
            if ( jQuery( '#rs_send_points_user_selection_field' ).val() == '1' ) {
                jQuery( '#rs_select_user_label' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_send_points_username_field_label' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_send_points_username_placeholder' ).closest( 'tr' ).hide( ) ;
            } else {
                jQuery( '#rs_select_user_label' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_send_points_username_field_label' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_send_points_username_placeholder' ).closest( 'tr' ).show( ) ;
            }
        } ) ;

    } else {
        jQuery( '#rs_select_send_points_user_type' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_select_users_list_for_send_point' ).parent().parent().hide() ;
        jQuery( '#rs_total_send_points_request' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_points_to_send_request' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_select_user_label' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_select_points_submit_label' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_request_approval_type' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_limit_for_send_point' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_limit_send_points_request' ).parent().parent().hide() ;
        jQuery( '#rs_err_when_point_greater_than_limit' ).parent().parent().hide() ;
        jQuery( '#rs_request_approval_type' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_message_send_point_request_submitted' ).parent().parent().hide() ;
        jQuery( '#rs_message_send_point_request_submitted_for_auto' ).parent().parent().hide() ;
        jQuery( '#rs_reason_for_send_points' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_reason_for_send_points_user' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_send_points_user_selection_field' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_select_user_label' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_send_points_username_field_label' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_send_points_username_placeholder' ).closest( 'tr' ).hide() ;
    }

    jQuery( '#rs_enable_msg_for_send_point' ).change( function () {
        if ( jQuery( '#rs_enable_msg_for_send_point' ).val() == '1' ) {
            jQuery( '#rs_select_send_points_user_type' ).closest( 'tr' ).show() ;

            /*Show or Hide for User Selection - Start*/
            if ( jQuery( '#rs_select_send_points_user_type' ).val() == '1' ) {
                jQuery( '#rs_select_users_list_for_send_point' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_select_users_list_for_send_point' ).parent().parent().show() ;
            }
            jQuery( '#rs_select_send_points_user_type' ).change( function () {
                if ( jQuery( this ).val() == '2' ) {
                    jQuery( '#rs_select_users_list_for_send_point' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_select_users_list_for_send_point' ).parent().parent().hide() ;
                }
            } ) ;
            /*Show or Hide for User Selection - End*/
            jQuery( '#rs_total_send_points_request' ).closest( 'tr' ).show() ;
            jQuery( '#rs_points_to_send_request' ).closest( 'tr' ).show() ;
            jQuery( '#rs_select_user_label' ).closest( 'tr' ).show() ;
            jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).show() ;
            jQuery( '#rs_select_points_submit_label' ).closest( 'tr' ).show() ;
            jQuery( '#rs_request_approval_type' ).closest( 'tr' ).show() ;
            jQuery( '#rs_limit_for_send_point' ).closest( 'tr' ).show() ;

            /*Show or Hide for Maximum Restriction Points to Send - Start*/
            if ( jQuery( '#rs_limit_for_send_point' ).val() == '1' ) {
                jQuery( '#rs_limit_send_points_request' ).parent().parent().show() ;
                jQuery( '#rs_err_when_point_greater_than_limit' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_limit_send_points_request' ).parent().parent().hide() ;
                jQuery( '#rs_err_when_point_greater_than_limit' ).parent().parent().hide() ;
            }
            jQuery( '#rs_limit_for_send_point' ).change( function () {
                if ( jQuery( this ).val() == '1' ) {
                    jQuery( '#rs_limit_send_points_request' ).parent().parent().show() ;
                    jQuery( '#rs_err_when_point_greater_than_limit' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_limit_send_points_request' ).parent().parent().hide() ;
                    jQuery( '#rs_err_when_point_greater_than_limit' ).parent().parent().hide() ;
                }
            } ) ;
            /*Show or Hide for Maximum Restriction Points to Send - End*/
            jQuery( '#rs_request_approval_type' ).closest( 'tr' ).show() ;

            /*Show or Hide for Approval Type - Start*/
            if ( jQuery( '#rs_request_approval_type' ).val() == '1' ) {
                jQuery( '#rs_message_send_point_request_submitted' ).parent().parent().show() ;
                jQuery( '#rs_message_send_point_request_submitted_for_auto' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_message_send_point_request_submitted' ).parent().parent().hide() ;
                jQuery( '#rs_message_send_point_request_submitted_for_auto' ).parent().parent().show() ;
            }
            jQuery( '#rs_request_approval_type' ).change( function () {
                if ( jQuery( '#rs_request_approval_type' ).val() == '1' ) {
                    jQuery( '#rs_message_send_point_request_submitted' ).parent().parent().show() ;
                    jQuery( '#rs_message_send_point_request_submitted_for_auto' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_message_send_point_request_submitted' ).parent().parent().hide() ;
                    jQuery( '#rs_message_send_point_request_submitted_for_auto' ).parent().parent().show() ;
                }
            } ) ;
            /*Show or Hide for Approval Type - End*/
            jQuery( '#rs_reason_for_send_points' ).closest( 'tr' ).show() ;
            jQuery( '#rs_reason_for_send_points_user' ).closest( 'tr' ).show() ;

            jQuery( '#rs_send_points_user_selection_field' ).closest( 'tr' ).show() ;
            jQuery( '#rs_send_points_user_selection_field' ).change( function () {
                if ( jQuery( '#rs_send_points_user_selection_field' ).val() == '1' ) {
                    jQuery( '#rs_select_user_label' ).closest( 'tr' ).show( ) ;
                    jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).show( ) ;
                    jQuery( '#rs_send_points_username_field_label' ).closest( 'tr' ).hide( ) ;
                    jQuery( '#rs_send_points_username_placeholder' ).closest( 'tr' ).hide( ) ;
                } else {
                    jQuery( '#rs_select_user_label' ).closest( 'tr' ).hide( ) ;
                    jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).hide( ) ;
                    jQuery( '#rs_send_points_username_field_label' ).closest( 'tr' ).show( ) ;
                    jQuery( '#rs_send_points_username_placeholder' ).closest( 'tr' ).show( ) ;
                }
            } ) ;
        } else {
            jQuery( '#rs_select_send_points_user_type' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_select_users_list_for_send_point' ).parent().parent().hide() ;
            jQuery( '#rs_total_send_points_request' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_points_to_send_request' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_select_user_label' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_select_points_submit_label' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_request_approval_type' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_limit_for_send_point' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_limit_send_points_request' ).parent().parent().hide() ;
            jQuery( '#rs_err_when_point_greater_than_limit' ).parent().parent().hide() ;
            jQuery( '#rs_request_approval_type' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_message_send_point_request_submitted' ).parent().parent().hide() ;
            jQuery( '#rs_message_send_point_request_submitted_for_auto' ).parent().parent().hide() ;
            jQuery( '#rs_reason_for_send_points' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_reason_for_send_points_user' ).closest( 'tr' ).hide() ;

            jQuery( '#rs_send_points_user_selection_field' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_select_user_label' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_select_user_placeholder' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_send_points_username_field_label' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_send_points_username_placeholder' ).closest( 'tr' ).hide() ;
        }
    } ) ;

    /*Show or hide Settings for Form for Send Points Tab - End*/

    /*Show or hide Settings for Point URL Tab - Start*/

    if ( jQuery( '#rs_time_limit_for_pointurl' ).val() == '1' ) {
        jQuery( '#rs_expiry_time_for_pointurl' ).parent().parent().hide() ;
    } else {
        jQuery( '#rs_expiry_time_for_pointurl' ).parent().parent().show() ;
    }

    jQuery( '#rs_time_limit_for_pointurl' ).change( function () {
        if ( jQuery( '#rs_time_limit_for_pointurl' ).val() == '1' ) {
            jQuery( '#rs_expiry_time_for_pointurl' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_expiry_time_for_pointurl' ).parent().parent().show() ;
        }
    } ) ;

    if ( jQuery( '#rs_count_limit_for_pointurl' ).val() == '1' ) {
        jQuery( '#rs_count_for_pointurl' ).parent().parent().hide() ;
    } else {
        jQuery( '#rs_count_for_pointurl' ).parent().parent().show() ;
    }

    jQuery( '#rs_count_limit_for_pointurl' ).change( function () {
        if ( jQuery( '#rs_count_limit_for_pointurl' ).val() == '1' ) {
            jQuery( '#rs_count_for_pointurl' ).parent().parent().hide() ;
        } else {
            jQuery( '#rs_count_for_pointurl' ).parent().parent().show() ;
        }
    } ) ;

    /*Show or hide Settings for Point URL Tab - End*/

    /*Show or hide Settings for Auto Redeeming in Checkout - Start*/

    var enable_auto_redeem_checkbox = jQuery( '#rs_enable_disable_auto_redeem_points' ).is( ':checked' ) ? 'yes' : 'no' ;
    if ( enable_auto_redeem_checkbox === 'yes' ) {
        jQuery( '#rs_percentage_cart_total_auto_redeem' ).parent().parent().show() ;
        jQuery( '#rs_enable_disable_auto_redeem_checkout' ).parent().parent().parent().parent().show() ;
    } else {
        jQuery( '#rs_percentage_cart_total_auto_redeem' ).parent().parent().hide() ;
        jQuery( '#rs_enable_disable_auto_redeem_checkout' ).parent().parent().parent().parent().hide() ;
    }

    jQuery( '#rs_enable_disable_auto_redeem_points' ).click( function () {
        var enable_auto_redeem_checkbox = jQuery( '#rs_enable_disable_auto_redeem_points' ).is( ':checked' ) ? 'yes' : 'no' ;
        if ( enable_auto_redeem_checkbox == 'yes' ) {
            jQuery( '#rs_percentage_cart_total_auto_redeem' ).parent().parent().show() ;
            jQuery( '#rs_enable_disable_auto_redeem_checkout' ).parent().parent().parent().parent().show() ;
        } else {
            jQuery( '#rs_percentage_cart_total_auto_redeem' ).parent().parent().hide() ;
            jQuery( '#rs_enable_disable_auto_redeem_checkout' ).parent().parent().parent().parent().hide() ;
        }
    } ) ;
    /*Show or hide Settings for Auto Redeeming in Checkout - End*/

    /*Show or hide for Points can Earned in Thank You Page - Start*/
    if ( jQuery( '#rs_show_hide_total_points_order_field' ).val() == '1' ) {
        jQuery( '#rs_total_earned_point_caption_thank_you' ).closest( 'tr' ).show() ;
        jQuery( '#rs_show_hide_equivalent_price_for_points_thankyou' ).closest( 'tr' ).show() ;

    } else {
        jQuery( '#rs_total_earned_point_caption_thank_you' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_show_hide_equivalent_price_for_points_thankyou' ).closest( 'tr' ).hide() ;
    }

    jQuery( '#rs_show_hide_total_points_order_field' ).change( function () {
        if ( jQuery( '#rs_show_hide_total_points_order_field' ).val() == '1' ) {
            jQuery( '#rs_total_earned_point_caption_thank_you' ).closest( 'tr' ).show() ;
            jQuery( '#rs_show_hide_equivalent_price_for_points_thankyou' ).closest( 'tr' ).show() ;
        } else {
            jQuery( '#rs_total_earned_point_caption_thank_you' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_show_hide_equivalent_price_for_points_thankyou' ).closest( 'tr' ).hide() ;
        }
    } ) ;
    /*Show or hide for Points can Earned in Thank You Page - End*/

    jQuery( "#rs_enable_earned_level_based_reward_points" ).change( function () {
        if ( jQuery( "#rs_enable_earned_level_based_reward_points" ).is( ':checked' ) == true ) {
            jQuery( '.rs_sample' ).css( 'border' , '1px solid #ccc' ) ;
        } else {
            jQuery( '.rs_sample' ).css( 'border' , 'none' ) ;
        }
    } ) ;

    /*Show or hide Settings for My Account Tab - Start*/
    jQuery( '#rs_show_hide_iagree_termsandcondition_field' ).change( function () {
        if ( jQuery( '#rs_show_hide_iagree_termsandcondition_field' ).val() == '2' ) {
            jQuery( '#rs_refer_friend_iagreecaption_link' ).closest( 'tr' ).show() ;
            jQuery( '#rs_refer_friend_termscondition_caption' ).closest( 'tr' ).show() ;
            jQuery( '#rs_refer_friend_termscondition_url' ).closest( 'tr' ).show() ;
        } else {
            jQuery( '#rs_refer_friend_iagreecaption_link' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_refer_friend_termscondition_caption' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_refer_friend_termscondition_url' ).closest( 'tr' ).hide() ;
        }
    } ) ;
    /*Show or hide Settings for My Account Tab - End*/

    /*Show or Hide Referrer Label Settings - Start*/

    /*Show/Hide for Point Price in Bulk Update Settings - Start*/
    if ( jQuery( '#rs_local_enable_disable_point_price' ).val() == '1' ) {
        jQuery( '#rs_local_point_pricing_type' ).closest( 'tr' ).show() ;

        /*Show/Hide for Currency & Point Pricing Option in Bulk Update Settings - Start*/
        if ( jQuery( '#rs_local_point_pricing_type' ).val() == '1' ) {
            jQuery( '#rs_local_point_price_type' ).closest( 'tr' ).show() ;

            /*Show/Hide for Fixed/Conversion Option in Bulk Update Settings - Start*/
            if ( jQuery( '#rs_local_point_price_type' ).val() == '1' ) {
                jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_local_price_points' ).closest( 'tr' ).hide() ;
            }

            jQuery( '#rs_local_point_price_type' ).change( function () {
                if ( jQuery( '#rs_local_point_price_type' ).val() == '1' ) {
                    jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_local_price_points' ).closest( 'tr' ).hide() ;
                }
            } ) ;
            /*Show/Hide for Fixed/Conversion Option in Bulk Update Settings - End*/
        } else {
            jQuery( '#rs_local_point_price_type' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
        }

        jQuery( '#rs_local_point_pricing_type' ).change( function () {
            if ( jQuery( '#rs_local_point_pricing_type' ).val() == '1' ) {
                jQuery( '#rs_local_point_price_type' ).closest( 'tr' ).show() ;

                /*Show/Hide for Fixed/Conversion Option in Bulk Update Settings - Start*/
                if ( jQuery( '#rs_local_point_price_type' ).val() == '1' ) {
                    jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_local_price_points' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_local_point_price_type' ).change( function () {
                    if ( jQuery( '#rs_local_point_price_type' ).val() == '1' ) {
                        jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_local_price_points' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
                /*Show/Hide for Fixed/Conversion Option in Bulk Update Settings - End*/
            } else {
                jQuery( '#rs_local_point_price_type' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
            }
        } ) ;
        /*Show/Hide for Currency & Point Pricing Option in Bulk Update Settings - End*/
    } else {
        jQuery( '#rs_local_point_pricing_type' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_local_point_price_type' ).closest( 'tr' ).hide() ;
        jQuery( '#rs_local_price_points' ).closest( 'tr' ).hide() ;
    }

    jQuery( '#rs_local_enable_disable_point_price' ).change( function () {
        if ( jQuery( '#rs_local_enable_disable_point_price' ).val() == '1' ) {
            jQuery( '#rs_local_point_pricing_type' ).closest( 'tr' ).show() ;

            /*Show/Hide for Currency & Point Pricing Option in Bulk Update Settings - Start*/
            if ( jQuery( '#rs_local_point_pricing_type' ).val() == '1' ) {
                jQuery( '#rs_local_point_price_type' ).closest( 'tr' ).show() ;

                /*Show/Hide for Fixed/Conversion Option in Bulk Update Settings - Start*/
                if ( jQuery( '#rs_local_point_price_type' ).val() == '1' ) {
                    jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_local_price_points' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_local_point_price_type' ).change( function () {
                    if ( jQuery( '#rs_local_point_price_type' ).val() == '1' ) {
                        jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_local_price_points' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
                /*Show/Hide for Fixed/Conversion Option in Bulk Update Settings - End*/
            } else {
                jQuery( '#rs_local_point_price_type' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
            }

            jQuery( '#rs_local_point_pricing_type' ).change( function () {
                if ( jQuery( '#rs_local_point_pricing_type' ).val() == '1' ) {
                    jQuery( '#rs_local_point_price_type' ).closest( 'tr' ).show() ;

                    /*Show/Hide for Fixed/Conversion Option in Bulk Update Settings - Start*/
                    if ( jQuery( '#rs_local_point_price_type' ).val() == '1' ) {
                        jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_local_price_points' ).closest( 'tr' ).hide() ;
                    }

                    jQuery( '#rs_local_point_price_type' ).change( function () {
                        if ( jQuery( '#rs_local_point_price_type' ).val() == '1' ) {
                            jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
                        } else {
                            jQuery( '#rs_local_price_points' ).closest( 'tr' ).hide() ;
                        }
                    } ) ;
                    /*Show/Hide for Fixed/Conversion Option in Bulk Update Settings - End*/
                } else {
                    jQuery( '#rs_local_point_price_type' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_local_price_points' ).closest( 'tr' ).show() ;
                }
            } ) ;
            /*Show/Hide for Currency & Point Pricing Option in Bulk Update Settings - End*/
        } else {
            jQuery( '#rs_local_point_pricing_type' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_local_point_price_type' ).closest( 'tr' ).hide() ;
            jQuery( '#rs_local_price_points' ).closest( 'tr' ).hide() ;
        }
    } ) ;
    /*Show/Hide for Point Price in Bulk Update Settings - End*/
} ) ;