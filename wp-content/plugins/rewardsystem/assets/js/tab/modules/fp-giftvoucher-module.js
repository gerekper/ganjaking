/*
 * Gift Voucher - Module
 */
jQuery( function ( $ ) {
    var GiftVoucherModule = {
        init : function ( ) {
            this.show_or_hide_for_enable_voucher_code_settings( ) ;
            this.show_or_hide_for_redeem_voucher( ) ;
            this.show_or_hide_for_mail_gift_voucher( ) ;
            $( '#rs_gift_voucher_expiry' ).datepicker( { dateFormat : 'yy-mm-dd' , minDate : 0 } ) ;
            $( document ).on( 'change' , '#rs_enable_voucher_code' , this.enable_voucher_code_settings ) ;
            $( document ).on( 'change' , '#rs_show_hide_redeem_voucher' , this.redeem_voucher ) ;
            $( document ).on( 'change' , '#rs_send_mail_gift_voucher' , this.mail_gift_voucher ) ;
            $( document ).on( 'click' , '.rs_create_voucher_codes_offline_online' , this.generate_voucher_codes ) ;
        } ,
        enable_voucher_code_settings : function ( ) {
            GiftVoucherModule.show_or_hide_for_enable_voucher_code_settings( ) ;
        } ,
        show_or_hide_for_enable_voucher_code_settings : function ( ) {
            if ( jQuery( '#rs_enable_voucher_code' ).val( ) == 'Enable' ) {
                jQuery( '#rs_enable_prefix' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_reward_code_type' ).closest( 'tr' ).show( ) ;
                if ( jQuery( '#rs_reward_code_type' ).val( ) == 'numeric' ) {
                    jQuery( '#rs_alphabets_from_voucher_code_creation' ).closest( 'tr' ).hide( ) ;
                    jQuery( '.rs_exclude_characters_code_generation_label' ).closest( 'tr' ).hide( ) ;
                } else {
                    jQuery( '#rs_alphabets_from_voucher_code_creation' ).closest( 'tr' ).show( ) ;
                    jQuery( '.rs_exclude_characters_code_generation_label' ).closest( 'tr' ).show( ) ;
                }
                jQuery( '#rs_reward_code_type' ).change( function ( ) {
                    if ( jQuery( '#rs_reward_code_type' ).val( ) == 'numeric' ) {
                        jQuery( '#rs_alphabets_from_voucher_code_creation' ).closest( 'tr' ).hide( ) ;
                        jQuery( '.rs_exclude_characters_code_generation_label' ).closest( 'tr' ).hide( ) ;
                    } else {
                        jQuery( '#rs_alphabets_from_voucher_code_creation' ).closest( 'tr' ).show( ) ;
                        jQuery( '.rs_exclude_characters_code_generation_label' ).closest( 'tr' ).show( ) ;
                    }
                } ) ;
                jQuery( '#rs_voucher_code_length' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_voucher_code_to_generate' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_voucher_code_user_for' ).closest( 'tr' ).show( ) ;
                if ( jQuery( '#rs_voucher_code_user_for' ).val( ) == '1' ) {
                    jQuery( '#rs_voucher_code_usage_limit' ).closest( 'tr' ).hide( ) ;
                    jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).hide( ) ;
                } else {
                    jQuery( '#rs_voucher_code_usage_limit' ).closest( 'tr' ).show( ) ;
                    if ( jQuery( '#rs_voucher_code_usage_limit' ).val( ) == '1' ) {
                        jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).show( ) ;
                    } else {
                        jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).hide( ) ;
                    }
                    jQuery( '#rs_voucher_code_usage_limit' ).change( function ( ) {
                        if ( jQuery( '#rs_voucher_code_usage_limit' ).val( ) == '1' ) {
                            jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).show( ) ;
                        } else {
                            jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).hide( ) ;
                        }
                    } ) ;
                    jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).show( ) ;
                }
                jQuery( '#rs_voucher_code_user_for' ).change( function ( ) {
                    if ( jQuery( '#rs_voucher_code_user_for' ).val( ) == '1' ) {
                        jQuery( '#rs_voucher_code_usage_limit' ).closest( 'tr' ).hide( ) ;
                        jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).hide( ) ;
                    } else {
                        jQuery( '#rs_voucher_code_usage_limit' ).closest( 'tr' ).show( ) ;
                        if ( jQuery( '#rs_voucher_code_usage_limit' ).val( ) == '1' ) {
                            jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).show( ) ;
                        } else {
                            jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).hide( ) ;
                        }
                        jQuery( '#rs_voucher_code_usage_limit' ).change( function ( ) {
                            if ( jQuery( '#rs_voucher_code_usage_limit' ).val( ) == '1' ) {
                                jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).show( ) ;
                            } else {
                                jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).hide( ) ;
                            }
                        } ) ;
                        jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).show( ) ;
                    }
                } ) ;
                jQuery( '#rs_per_voucher_code' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_gift_voucher_expiry' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_create_Voucher_Codes' ).closest( 'tr' ).show( ) ;
            } else {
                jQuery( '#rs_enable_prefix' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_reward_code_type' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_voucher_code_length' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_voucher_code_to_generate' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_per_voucher_code' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_voucher_code_user_for' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_gift_voucher_expiry' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_voucher_code_usage_limit_value' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_voucher_code_usage_limit' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_alphabets_from_voucher_code_creation' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_create_Voucher_Codes' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        redeem_voucher : function ( ) {
            GiftVoucherModule.show_or_hide_for_redeem_voucher( ) ;
        } ,
        show_or_hide_for_redeem_voucher : function ( ) {
            if ( jQuery( '#rs_show_hide_redeem_voucher' ).val( ) == '1' ) {
                jQuery( '#rs_redeem_your_gift_voucher_label' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_redeem_gift_voucher_button_label' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_redeem_voucher_position' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_redeem_your_gift_voucher_placeholder' ).closest( 'tr' ).show( ) ;
            } else {
                jQuery( '#rs_redeem_your_gift_voucher_label' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_redeem_gift_voucher_button_label' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_redeem_voucher_position' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_redeem_your_gift_voucher_placeholder' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        mail_gift_voucher : function ( ) {
            GiftVoucherModule.show_or_hide_for_mail_gift_voucher( ) ;
        } ,
        show_or_hide_for_mail_gift_voucher : function ( ) {
            if ( jQuery( '#rs_send_mail_gift_voucher' ).is( ':checked' ) == true ) {
                jQuery( '#rs_email_subject_gift_voucher' ).closest( 'tr' ).show( ) ;
                jQuery( '#rs_email_message_gift_voucher' ).closest( 'tr' ).show( ) ;
            } else {
                jQuery( '#rs_email_subject_gift_voucher' ).closest( 'tr' ).hide( ) ;
                jQuery( '#rs_email_message_gift_voucher' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        generate_voucher_codes : function ( ) {
            var block = $( this ).closest( '.rs_section_wrapper' ) ;
            GiftVoucherModule.block( block ) ;
            var prefix_enabled_value = $( '.rs_enable_prefix_offline_online_rewards' ).is( ":checked" ) ? 'yes' : 'no' ;
            var prefix_content = $( '.rs_voucher_prefix_offline_online' ).val( ) ;
            var suffix_enabled_value = $( '.rs_enable_suffix_offline_online_rewards' ).is( ":checked" ) ? 'yes' : 'no' ;
            var suffix_content = $( '.rs_voucher_suffix_offline_online' ).val( ) ;
            var reward_code_type = $( '#rs_reward_code_type' ).val( ) ;
            var exclude_content_code = $( '.rs_exclude_characters_code_generation' ).val( ) ;
            var length_of_voucher_code = $( '.rs_voucher_code_length_offline_online' ).val( ) ;
            var points_value_of_voucher_code = $( '.rs_voucher_code_points_value_offline_online' ).val( ) ;
            var number_of_vouchers_to_be_created = $( '.rs_voucher_code_count_offline_online' ).val( ) ;
            if ( prefix_enabled_value === 'yes' && suffix_enabled_value === 'yes' ) {
                if ( prefix_content === '' ) {
                    jQuery( '.rs_prefix_error' ).fadeIn( ) ;
                    jQuery( '.rs_prefix_error' ).html( fp_giftvoucher_module_param.prefix ) ;
                    jQuery( '.rs_prefix_error' ).fadeOut( 5000 ) ;
                    GiftVoucherModule.unblock( block ) ;
                    return false ;
                }

                if ( suffix_content === '' ) {
                    jQuery( '.rs_suffix_error' ).fadeIn( ) ;
                    jQuery( '.rs_suffix_error' ).html( fp_giftvoucher_module_param.suffix ) ;
                    jQuery( '.rs_suffix_error' ).fadeOut( 5000 ) ;
                    GiftVoucherModule.unblock( block ) ;
                    return false ;
                }
            } else if ( prefix_enabled_value == 'yes' && suffix_enabled_value != 'yes' ) {
                if ( prefix_content == '' ) {
                    jQuery( '.rs_prefix_error' ).fadeIn( ) ;
                    jQuery( '.rs_prefix_error' ).html( fp_giftvoucher_module_param.prefix ) ;
                    jQuery( '.rs_prefix_error' ).fadeOut( 5000 ) ;
                    GiftVoucherModule.unblock( block ) ;
                    return false ;
                }
            } else if ( prefix_enabled_value != 'yes' && suffix_enabled_value == 'yes' ) {
                if ( suffix_content == '' ) {
                    jQuery( '.rs_suffix_error' ).fadeIn( ) ;
                    jQuery( '.rs_suffix_error' ).html( fp_giftvoucher_module_param.suffix ) ;
                    jQuery( '.rs_suffix_error' ).fadeOut( 5000 ) ;
                    GiftVoucherModule.unblock( block ) ;
                    return false ;
                }
            }
            if ( length_of_voucher_code === '' ) {
                jQuery( '.rs_character_error' ).fadeIn( ) ;
                jQuery( '.rs_character_error' ).html( fp_giftvoucher_module_param.character ) ;
                jQuery( '.rs_character_error' ).fadeOut( 5000 ) ;
                GiftVoucherModule.unblock( block ) ;
                return false ;
            }
            if ( points_value_of_voucher_code === '' ) {
                jQuery( '.rs_points_error' ).fadeIn( ) ;
                jQuery( '.rs_points_error' ).html( fp_giftvoucher_module_param.points ) ;
                jQuery( '.rs_points_error' ).fadeOut( 5000 ) ;
                GiftVoucherModule.unblock( block ) ;
                return false ;
            }
            if ( number_of_vouchers_to_be_created === '' ) {
                jQuery( '.rs_noofcode_error' ).fadeIn( ) ;
                jQuery( '.rs_noofcode_error' ).html( fp_giftvoucher_module_param.noofcodes ) ;
                jQuery( '.rs_noofcode_error' ).fadeOut( 5000 ) ;
                GiftVoucherModule.unblock( block ) ;
                return false ;
            }
            var dataparam = ( {
                action : 'generatevouchercode' ,
                enableprefix : prefix_enabled_value ,
                prefixvalue : prefix_content ,
                enablesuffix : suffix_enabled_value ,
                noofvoucher : number_of_vouchers_to_be_created ,
                suffixvalue : suffix_content ,
                codetype : reward_code_type ,
                codelength : length_of_voucher_code ,
                voucherpoint : points_value_of_voucher_code ,
                excludecontent : exclude_content_code ,
                expirydate : $( '#rs_gift_voucher_expiry' ).val( ) ,
                vouchercreated : fp_giftvoucher_module_param.date ,
                usertype : $( '#rs_voucher_code_user_for' ).val( ) ,
                usagelimit : $( '#rs_voucher_code_usage_limit' ).val( ) ,
                usagelimitvalue : $( '#rs_voucher_code_usage_limit_value' ).val( ) ,
                sumo_security : fp_giftvoucher_module_param.fp_create_code ,
            } ) ;
            $.post( fp_giftvoucher_module_param.ajaxurl , dataparam ,
                    function ( response ) {
                        if ( true == response.success ) {
                            var uniquekey = [ ] ;
                            $.each( response.data.content , function ( i , el ) {
                                if ( $.inArray( el , uniquekey ) === - 1 ) {
                                    uniquekey.push( el ) ;
                                }
                            } ) ;
                            if ( number_of_vouchers_to_be_created > uniquekey.length + 1 ) {
                                GiftVoucherModule.unblock( block ) ;
                                $( "#dialog1" ).dialog( {
                                    buttons : [
                                        {
                                            text : "Ok" ,
                                            icons : {
                                                primary : "ui-icon-heart"
                                            } ,
                                            click : function ( ) {
                                                jQuery( this ).dialog( "close" ) ;
                                                window.location.href = fp_giftvoucher_module_param.redirecturl ;
                                            }

                                        }
                                    ]

                                } ) ;
                                $( 'div#dialog1' ).on( 'dialogclose' , function ( ) {
                                    window.location.href = fp_giftvoucher_module_param.redirecturl ;
                                } ) ;
                                $( "#dialog1" ).html( + uniquekey.length + ' Unique code is Generated Please Increase number of Character to Create More Voucher' ) ;
                            } else {
                                window.location.href = fp_giftvoucher_module_param.redirecturl ;
                            }
                            $( '.rs_voucher_prefix_offline_online' ).val( '' ) ;
                            $( '.rs_voucher_suffix_offline_online' ).val( '' ) ;
                            $( '#rs_reward_code_type' ).val( '' ) ;
                            $( '.rs_exclude_characters_code_generation' ).val( '' ) ;
                            $( '.rs_voucher_code_length_offline_online' ).val( '' ) ;
                            $( '.rs_voucher_code_points_value_offline_online' ).val( '' ) ;
                            $( '.rs_voucher_code_count_offline_online' ).val( '' ) ;
                            $( '#rs_gift_voucher_expiry' ).val( '' ) ;
                            $( '.preloader_image_online_offline_rewards' ).css( "display" , "none" ) ;
                        } else {
                            window.alert( response.data.error ) ;
                        }
                    } , 'json' ) ;
            return false ;
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
    GiftVoucherModule.init( ) ;
} ) ;