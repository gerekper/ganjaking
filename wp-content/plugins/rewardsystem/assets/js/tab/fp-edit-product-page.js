/*
 * Edit Product Page
 */
jQuery( function ( $ ) {
    var EditProductPageScript = {
        init : function () {
            jQuery( '#_rewardsystem__points_based_on_conversion' ).attr( 'readonly' , 'true' ) ;
            this.show_or_hide_for_enable_point_price() ;
            this.show_or_hide_for_buying_reward_points() ;
            this.show_or_hide_for_social_reward_points() ;
            this.show_or_hide_for_product_purchase_reward_points() ;
            this.show_or_hide_for_referral_product_purchase_reward_points() ;
            $( document ).on( 'change' , '#_rewardsystem_enable_point_price' , this.enable_point_price ) ;
            $( document ).on( 'change' , '#_rewardsystem_enable_point_price_type' , this.point_price_type ) ;
            $( document ).on( 'change' , '#_rewardsystem_buying_reward_points' , this.buying_reward_points ) ;
            $( document ).on( 'change' , '#_socialrewardsystemcheckboxvalue' , this.social_reward_points ) ;
            $( document ).on( 'change' , '#_rewardsystemcheckboxvalue' , this.product_purchase_reward_points ) ;
            $( document ).on( 'change' , '#_rewardsystemreferralcheckboxvalue' , this.referral_product_purchase_reward_points ) ;
            $( document ).on( 'click' , '#publish' , this.validate_point_price_settings ) ;
        } ,

        validate_point_price_settings : function ( e ) {
            if ( jQuery( '._rewardsystem_enable_point_price_type' ).val() == '2' ) {
                if ( jQuery( '#_rewardsystem__points' ).val() == '' ) {
                    jQuery( '#_rewardsystem__points' ).css( {
                        "border" : "1px solid red" ,
                        "background" : "#FFCECE"
                    } ) ;
                    if ( jQuery( '#_rewardsystem__points' ).parent().find( '.wc_error_tip' ).size() == 0 ) {
                        var offset = jQuery( '#_rewardsystem__points' ).position() ;
                        jQuery( '#_rewardsystem__points' ).after( '<div class="wc_error_tip">' + "Please Enter Points" + '</div>' ) ;
                        jQuery( '.wc_error_tip' )
                                .css( 'left' , offset.left + jQuery( this ).width() - ( jQuery( this ).width() / 2 ) - ( jQuery( '.wc_error_tip' ).width() / 2 ) )
                                .css( 'top' , offset.top + jQuery( this ).height() )
                                .fadeIn( '100000000' ) ;
                    }

                    e.preventDefault() ;
                }
            }
        } ,

        enable_point_price : function () {
            EditProductPageScript.show_or_hide_for_enable_point_price() ;
        } ,
        show_or_hide_for_enable_point_price : function () {
            if ( jQuery( '#_rewardsystem_enable_point_price' ).val() == 'no' ) {
                jQuery( '#_rewardsystem_enable_point_price_type' ).parent().hide() ;
                jQuery( '#_rewardsystem_point_price_type' ).parent().hide() ;
                jQuery( '#_rewardsystem__points' ).parent().hide() ;
                jQuery( '#_rewardsystem__points_based_on_conversion' ).parent().hide() ;
                jQuery( '#_rewardsystem_enable_point_price_type_booking' ).parent().hide() ;
                jQuery( '#_regular_price' ).parent().show() ;
                jQuery( '#_sale_price' ).parent().show() ;
            } else {
                jQuery( '#_rewardsystem_enable_point_price_type_booking' ).parent().hide() ;
                jQuery( '#_rewardsystem_enable_point_price_type' ).parent().show() ;
                EditProductPageScript.show_or_hide_for_point_price_type() ;
            }
        } ,
        point_price_type : function () {
            EditProductPageScript.show_or_hide_for_point_price_type() ;
        } ,
        show_or_hide_for_point_price_type : function () {
            if ( jQuery( '#_rewardsystem_enable_point_price_type' ).val() == '2' ) {
                jQuery( '#_rewardsystem__points' ).parent().show() ;
                jQuery( '#_rewardsystem__points_based_on_conversion' ).parent().hide() ;
                jQuery( '#_rewardsystem_point_price_type' ).parent().hide() ;
                if ( jQuery( '#rs_point_price_visibility' ).val() == '1' ) {
                    jQuery( '#_regular_price' ).parent().hide() ;
                    jQuery( '#_sale_price' ).parent().hide() ;
                } else {
                    jQuery( '#_regular_price' ).parent().show() ;
                    jQuery( '#_sale_price' ).parent().show() ;
                }

                jQuery( '#rs_point_price_visibility' ).change( function () {
                    if ( jQuery( '#rs_point_price_visibility' ).val() == '1' ) {
                        jQuery( '#_regular_price' ).parent().hide() ;
                        jQuery( '#_sale_price' ).parent().hide() ;
                    } else {
                        jQuery( '#_regular_price' ).parent().show() ;
                        jQuery( '#_sale_price' ).parent().show() ;
                    }
                } ) ;
            } else {
                jQuery( '#_regular_price' ).parent().show() ;
                jQuery( '#_sale_price' ).parent().show() ;
                jQuery( '#_rewardsystem_point_price_type' ).parent().show() ;
                if ( jQuery( '#_rewardsystem_point_price_type' ).val() == '1' ) {
                    jQuery( '#_rewardsystem__points' ).parent().show() ;
                    jQuery( '#_rewardsystem__points_based_on_conversion' ).parent().hide() ;
                } else {
                    jQuery( '#_rewardsystem__points' ).parent().hide() ;
                    jQuery( '#_rewardsystem__points_based_on_conversion' ).parent().show() ;
                }

                jQuery( '#_rewardsystem_point_price_type' ).change( function () {
                    if ( jQuery( '#_rewardsystem_point_price_type' ).val() == '1' ) {
                        jQuery( '#_rewardsystem__points' ).parent().show() ;
                        jQuery( '#_rewardsystem__points_based_on_conversion' ).parent().hide() ;
                    } else {
                        jQuery( '#_rewardsystem__points' ).parent().hide() ;
                        jQuery( '#_rewardsystem__points_based_on_conversion' ).parent().show() ;
                    }
                } ) ;
            }
        } ,
        buying_reward_points : function () {
            EditProductPageScript.show_or_hide_for_buying_reward_points() ;
        } ,
        show_or_hide_for_buying_reward_points : function () {
            if ( jQuery( '#_rewardsystem_buying_reward_points' ).val() == 'no' ) {
                jQuery( '.show_if_buy_reward_points_enable' ).parent().hide() ;
            } else {
                jQuery( '.show_if_buy_reward_points_enable' ).parent().show() ;
            }
        } ,
        social_reward_points : function () {
            EditProductPageScript.show_or_hide_for_social_reward_points() ;
        } ,
        show_or_hide_for_social_reward_points : function () {
            if ( jQuery( '#_socialrewardsystemcheckboxvalue' ).val() === 'no' ) {
                jQuery( '.show_if_social_enable' ).closest( 'p' ).hide() ;
            } else {
                jQuery( '.show_if_social_enable' ).parent().show() ;

                /* Social Reward System for facebook */
                if ( jQuery( '.social_rewardsystem_options_facebook' ).val() === '' ) {
                    jQuery( '._socialrewardsystempoints_facebook_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempercent_facebook_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '.social_rewardsystem_options_facebook' ).val() === '1' ) {
                    jQuery( '._socialrewardsystempercent_facebook_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempoints_facebook_field' ).css( 'display' , 'block' ) ;
                } else {
                    jQuery( '._socialrewardsystempercent_facebook_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._socialrewardsystempoints_facebook_field' ).css( 'display' , 'none' ) ;
                }

                /* On Change Event Triggering for Social Rewards Facebook */
                jQuery( '.social_rewardsystem_options_facebook' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._socialrewardsystempoints_facebook_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempercent_facebook_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._socialrewardsystempercent_facebook_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempoints_facebook_field' ).css( 'display' , 'block' ) ;
                    } else {
                        jQuery( '._socialrewardsystempercent_facebook_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._socialrewardsystempoints_facebook_field' ).css( 'display' , 'none' ) ;
                    }
                } ) ;


                /* Social Reward System for facebook */
                if ( jQuery( '._social_rewardsystem_options_facebook_share' ).val() === '' ) {
                    jQuery( '._socialrewardsystempoints_facebook_share_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempercent_facebook_share_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '._social_rewardsystem_options_facebook_share' ).val() === '1' ) {
                    jQuery( '._socialrewardsystempercent_facebook_share_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempoints_facebook_share_field' ).css( 'display' , 'block' ) ;
                } else {
                    jQuery( '._socialrewardsystempercent_facebook_share_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._socialrewardsystempoints_facebook_share_field' ).css( 'display' , 'none' ) ;
                }

                /* On Change Event Triggering for Social Rewards Facebook */
                jQuery( '._social_rewardsystem_options_facebook_share' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._socialrewardsystempoints_facebook_share_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempercent_facebook_share_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._socialrewardsystempercent_facebook_share_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempoints_facebook_share_field' ).css( 'display' , 'block' ) ;
                    } else {
                        jQuery( '._socialrewardsystempercent_facebook_share_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._socialrewardsystempoints_facebook_share_field' ).css( 'display' , 'none' ) ;
                    }
                } ) ;


                /* Social Reward System for twitter */
                if ( jQuery( '.social_rewardsystem_options_twitter' ).val() === '' ) {
                    jQuery( '._socialrewardsystempoints_twitter_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempercent_twitter_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '.social_rewardsystem_options_twitter' ).val() === '1' ) {
                    jQuery( '._socialrewardsystempercent_twitter_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempoints_twitter_field' ).css( 'display' , 'block' ) ;
                } else {
                    jQuery( '._socialrewardsystempercent_twitter_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._socialrewardsystempoints_twitter_field' ).css( 'display' , 'none' ) ;
                }

                /* On Change Event Triggering for Social Rewards twitter */
                jQuery( '.social_rewardsystem_options_twitter' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._socialrewardsystempoints_twitter_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempercent_twitter_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._socialrewardsystempercent_twitter_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempoints_twitter_field' ).css( 'display' , 'block' ) ;
                    } else {
                        jQuery( '._socialrewardsystempercent_twitter_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._socialrewardsystempoints_twitter_field' ).css( 'display' , 'none' ) ;
                    }
                } ) ;

                /* Social Reward System for Google+ */
                if ( jQuery( '.social_rewardsystem_options_google' ).val() === '' ) {
                    jQuery( '._socialrewardsystempoints_google_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempercent_google_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '.social_rewardsystem_options_google' ).val() === '1' ) {
                    jQuery( '._socialrewardsystempercent_google_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempoints_google_field' ).css( 'display' , 'block' ) ;
                } else {
                    jQuery( '._socialrewardsystempercent_google_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._socialrewardsystempoints_google_field' ).css( 'display' , 'none' ) ;
                }

                /* On Change Event Triggering for Social Rewards Google+ */
                jQuery( '.social_rewardsystem_options_google' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._socialrewardsystempoints_google_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempercent_google_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._socialrewardsystempercent_google_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempoints_google_field' ).css( 'display' , 'block' ) ;
                    } else {
                        jQuery( '._socialrewardsystempercent_google_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._socialrewardsystempoints_google_field' ).css( 'display' , 'none' ) ;
                    }
                } ) ;

                /* Social Reward System for VK */
                if ( jQuery( '.social_rewardsystem_options_vk' ).val() === '' ) {
                    jQuery( '._socialrewardsystempoints_vk_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempercent_vk_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '.social_rewardsystem_options_vk' ).val() === '1' ) {
                    jQuery( '._socialrewardsystempercent_vk_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempoints_vk_field' ).css( 'display' , 'block' ) ;
                } else {
                    jQuery( '._socialrewardsystempercent_vk_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._socialrewardsystempoints_vk_field' ).css( 'display' , 'none' ) ;
                }

                /* On Change Event Triggering for Social Rewards VK */
                jQuery( '.social_rewardsystem_options_vk' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._socialrewardsystempoints_vk_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempercent_vk_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._socialrewardsystempercent_vk_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempoints_vk_field' ).css( 'display' , 'block' ) ;
                    } else {
                        jQuery( '._socialrewardsystempercent_vk_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._socialrewardsystempoints_vk_field' ).css( 'display' , 'none' ) ;
                    }
                } ) ;
                if ( jQuery( '._social_rewardsystem_options_instagram' ).val() === '' ) {
                    jQuery( '._socialrewardsystempoints_instagram_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempercent_instagram_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '._social_rewardsystem_options_instagram' ).val() === '1' ) {
                    jQuery( '._socialrewardsystempercent_instagram_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempoints_instagram_field' ).css( 'display' , 'block' ) ;
                } else {
                    jQuery( '._socialrewardsystempercent_instagram_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._socialrewardsystempoints_instagram_field' ).css( 'display' , 'none' ) ;
                }

                /* On Change Event Triggering for Social Rewards VK */
                jQuery( '._social_rewardsystem_options_instagram' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._socialrewardsystempoints_instagram_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempercent_instagram_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._socialrewardsystempercent_instagram_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempoints_instagram_field' ).css( 'display' , 'block' ) ;
                    } else {
                        jQuery( '._socialrewardsystempercent_instagram_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._socialrewardsystempoints_instagram_field' ).css( 'display' , 'none' ) ;
                    }
                } ) ;

                if ( jQuery( '._social_rewardsystem_options_ok_follow' ).val() === '' ) {
                    jQuery( '._socialrewardsystempoints_ok_follow_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempercent_ok_follow_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '._social_rewardsystem_options_ok_follow' ).val() === '1' ) {
                    jQuery( '._socialrewardsystempercent_ok_follow_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempoints_ok_follow_field' ).css( 'display' , 'block' ) ;
                } else {
                    jQuery( '._socialrewardsystempercent_ok_follow_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._socialrewardsystempoints_ok_follow_field' ).css( 'display' , 'none' ) ;
                }

                /* On Change Event Triggering for Social Rewards VK */
                jQuery( '._social_rewardsystem_options_ok_follow' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._socialrewardsystempoints_ok_follow_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempercent_ok_follow_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._socialrewardsystempercent_ok_follow_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempoints_ok_follow_field' ).css( 'display' , 'block' ) ;
                    } else {
                        jQuery( '._socialrewardsystempercent_ok_follow_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._socialrewardsystempoints_ok_follow_field' ).css( 'display' , 'none' ) ;
                    }
                } ) ;

                if ( jQuery( '._social_rewardsystem_options_twitter_follow' ).val() === '' ) {
                    jQuery( '._socialrewardsystempoints_twitter_follow_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempercent_twitter_follow_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '._social_rewardsystem_options_twitter_follow' ).val() === '1' ) {
                    jQuery( '._socialrewardsystempercent_twitter_follow_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._socialrewardsystempoints_twitter_follow_field' ).css( 'display' , 'block' ) ;
                } else if ( jQuery( '._social_rewardsystem_options_twitter_follow' ).val() === '2' ) {
                    jQuery( '._socialrewardsystempercent_twitter_follow_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._socialrewardsystempoints_twitter_follow_field' ).css( 'display' , 'none' ) ;
                }

                /* On Change Event Triggering for Social Rewards twitter */
                jQuery( '._social_rewardsystem_options_twitter_follow' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._socialrewardsystempoints_twitter_follow_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempercent_twitter_follow_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._socialrewardsystempercent_twitter_follow_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._socialrewardsystempoints_twitter_follow_field' ).css( 'display' , 'block' ) ;
                    } else if ( jQuery( this ).val() === '2' ) {
                        jQuery( '._socialrewardsystempercent_twitter_follow_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._socialrewardsystempoints_twitter_follow_field' ).css( 'display' , 'none' ) ;
                    }
                } ) ;
            }
        } ,
        product_purchase_reward_points : function () {
            EditProductPageScript.show_or_hide_for_product_purchase_reward_points() ;
        } ,
        show_or_hide_for_product_purchase_reward_points : function () {
            if ( jQuery( '#_rewardsystemcheckboxvalue' ).val() == 'no' ) {
                jQuery( '.show_if_enable' ).parent().hide() ;
            } else {
                jQuery( '.show_if_enable' ).parent().show() ;
                if ( jQuery( '.rewardsystem_options' ).val() === '' ) {
                    jQuery( '._rewardsystempercent_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._rewardsystempoints_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '.rewardsystem_options' ).val() === '1' ) {
                    jQuery( '._rewardsystempercent_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._rewardsystempoints_field' ).css( 'display' , 'block' ) ;
                } else {
                    jQuery( '._rewardsystempercent_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._rewardsystempoints_field' ).css( 'display' , 'none' ) ;
                }

                jQuery( '.rewardsystem_options' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._rewardsystempercent_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._rewardsystempoints_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._rewardsystempercent_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._rewardsystempoints_field' ).css( 'display' , 'block' ) ;
                    } else {
                        jQuery( '._rewardsystempercent_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._rewardsystempoints_field' ).css( 'display' , 'none' ) ;
                    }

                } ) ;
            }
        } ,
        referral_product_purchase_reward_points : function () {
            EditProductPageScript.show_or_hide_for_referral_product_purchase_reward_points() ;
        } ,
        show_or_hide_for_referral_product_purchase_reward_points : function () {
            if ( jQuery( '#_rewardsystemreferralcheckboxvalue' ).val() == 'no' ) {
                jQuery( '.show_if_referral_enable' ).parent().hide() ;
            } else {
                jQuery( '.show_if_referral_enable' ).parent().show() ;
                if ( jQuery( '.referral_rewardsystem_options_get' ).val() === '' ) {
                    jQuery( '._referralrewardsystempoints_for_getting_referred_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._referralrewardsystempercent_for_getting_referred_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '.referral_rewardsystem_options_get' ).val() === '1' ) {
                    jQuery( '._referralrewardsystempoints_for_getting_referred_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._referralrewardsystempercent_for_getting_referred_field' ).css( 'display' , 'none' ) ;
                } else {
                    jQuery( '._referralrewardsystempoints_for_getting_referred_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._referralrewardsystempercent_for_getting_referred_field' ).css( 'display' , 'block' ) ;
                }

                if ( jQuery( '.referral_rewardsystem_options' ).val() === '' ) {
                    jQuery( '._referralrewardsystempercent_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._referralrewardsystempoints_field' ).css( 'display' , 'none' ) ;
                } else if ( jQuery( '.referral_rewardsystem_options' ).val() === '1' ) {
                    jQuery( '._referralrewardsystempercent_field' ).css( 'display' , 'none' ) ;
                    jQuery( '._referralrewardsystempoints_field' ).css( 'display' , 'block' ) ;
                } else {
                    jQuery( '._referralrewardsystempercent_field' ).css( 'display' , 'block' ) ;
                    jQuery( '._referralrewardsystempoints_field' ).css( 'display' , 'none' ) ;
                }


                jQuery( '.referral_rewardsystem_options' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._referralrewardsystempercent_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._referralrewardsystempoints_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._referralrewardsystempercent_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._referralrewardsystempoints_field' ).css( 'display' , 'block' ) ;
                    } else {
                        jQuery( '._referralrewardsystempercent_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._referralrewardsystempoints_field' ).css( 'display' , 'none' ) ;
                    }


                } ) ;
                jQuery( '.referral_rewardsystem_options_get' ).change( function () {
                    if ( jQuery( this ).val() === '' ) {
                        jQuery( '._referralrewardsystempoints_for_getting_referred_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._referralrewardsystempercent_for_getting_referred_field' ).css( 'display' , 'none' ) ;
                    } else if ( jQuery( this ).val() === '1' ) {
                        jQuery( '._referralrewardsystempoints_for_getting_referred_field' ).css( 'display' , 'block' ) ;
                        jQuery( '._referralrewardsystempercent_for_getting_referred_field' ).css( 'display' , 'none' ) ;
                    } else {
                        jQuery( '._referralrewardsystempoints_for_getting_referred_field' ).css( 'display' , 'none' ) ;
                        jQuery( '._referralrewardsystempercent_for_getting_referred_field' ).css( 'display' , 'block' ) ;

                    }
                } ) ;
            }
        } ,
    } ;
    EditProductPageScript.init() ;
} ) ;