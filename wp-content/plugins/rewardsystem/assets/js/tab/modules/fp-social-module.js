/*
 * Social Reward Points - Module
 */
jQuery( function ( $ ) {
    var SocialRewardPointsScripts = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_global_settings() ;
            this.show_or_hide_for_post_or_page_settings() ;
            this.show_or_hide_for_fb_like_settings() ;
            this.show_or_hide_for_fb_share_settings() ;
            this.show_or_hide_for_twitter_tweet_settings() ;
            this.show_or_hide_for_twitter_follow_settings() ;
            this.show_or_hide_for_instagram_settings() ;
            this.show_or_hide_for_vk_settings() ;
            this.show_or_hide_for_gplus_settings() ;
            this.show_or_hide_for_ok_settings() ;
            this.show_or_hide_for_global_level_enable_settings() ;
            this.show_or_hide_for_fblike_restriction_settings() ;
            this.show_or_hide_for_fbshare_restriction_settings() ;
            this.show_or_hide_for_twitter_tweet_restriction_settings() ;
            this.show_or_hide_for_twitter_follow_restriction_settings() ;
            this.show_or_hide_for_instagram_restriction_settings() ;
            this.show_or_hide_for_vk_restriction_settings() ;
            this.show_or_hide_for_gplus_restriction_settings() ;
            this.show_or_hide_for_ok_restriction_settings() ;
            this.show_or_hide_for_product_category_selection() ;
            $( document ).on( 'change' , '.rs_enable_product_category_level_for_social_reward' , this.global_settings ) ;
            $( document ).on( 'change' , '#rs_global_social_enable_disable_reward_post' , this.post_or_page_settings ) ;
            $( document ).on( 'change' , '#rs_global_show_hide_facebook_like_button' , this.fb_like_settings ) ;
            $( document ).on( 'change' , '#rs_global_show_hide_facebook_share_button' , this.fb_share_settings ) ;
            $( document ).on( 'change' , '#rs_global_show_hide_twitter_tweet_button' , this.twitter_tweet_settings ) ;
            $( document ).on( 'change' , '#rs_global_show_hide_twitter_follow_tweet_button' , this.twitter_follow_settings ) ;
            $( document ).on( 'change' , '#rs_global_show_hide_instagram_button' , this.instagram_settings ) ;
            $( document ).on( 'change' , '#rs_global_show_hide_vk_button' , this.vk_settings ) ;
            $( document ).on( 'change' , '#rs_global_show_hide_google_plus_button' , this.gplus_settings ) ;
            $( document ).on( 'change' , '#rs_global_show_hide_ok_button' , this.ok_settings ) ;
            $( document ).on( 'change' , '#rs_global_social_enable_disable_reward' , this.global_level_enable_settings ) ;
            $( document ).on( 'change' , '#rs_enable_fblike_restriction' , this.fblike_restriction_settings ) ;
            $( document ).on( 'change' , '#rs_enable_fbshare_restriction' , this.fbshare_restriction_settings ) ;
            $( document ).on( 'change' , '#rs_enable_tweet_restriction' , this.twitter_tweet_restriction_settings ) ;
            $( document ).on( 'change' , '#rs_enable_twitter_follow_restriction' , this.twitter_follow_restriction_settings ) ;
            $( document ).on( 'change' , '#rs_enable_instagram_restriction' , this.instagram_restriction_settings ) ;
            $( document ).on( 'change' , '#rs_enable_vk_restriction' , this.vk_restriction_settings ) ;
            $( document ).on( 'change' , '#rs_enable_gplus_restriction' , this.gplus_restriction_settings ) ;
            $( document ).on( 'change' , '#rs_enable_ok_restriction' , this.ok_restriction_settings ) ;
            $( document ).on( 'change' , '.rs_which_social_product_selection' , this.product_category_selection ) ;
            $( document ).on( 'click' , '.rs_sumo_reward_button_social' , this.bulk_update_points_for_social_reward ) ;
        } ,
        trigger_on_page_load : function () {
            if ( fp_social_params.fp_wc_version <= parseFloat( '2.2.0' ) ) {
                $( '#rs_select_particular_social_categories' ).chosen() ;
                $( '#rs_include_particular_categories_for_social_reward' ).chosen() ;
                $( '#rs_exclude_particular_categories_for_social_reward' ).chosen() ;
            } else {
                $( '#rs_select_particular_social_categories' ).select2() ;
                $( '#rs_include_particular_categories_for_social_reward' ).select2() ;
                $( '#rs_exclude_particular_categories_for_social_reward' ).select2() ;
            }
        } ,
        global_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_global_settings() ;
        } ,
        show_or_hide_for_global_settings : function () {
            if ( jQuery( 'input[name=rs_enable_product_category_level_for_social_reward]:checked' ).val() == 'no' ) {
                jQuery( '#rs_social_reward_global_level_applicable_for' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_social_reward_global_level_applicable_for' ).val() == '1' ) {
                    jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                } else if ( jQuery( '#rs_social_reward_global_level_applicable_for' ).val() == '2' ) {
                    jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                } else if ( jQuery( '#rs_social_reward_global_level_applicable_for' ).val() == '3' ) {
                    jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                } else if ( jQuery( '#rs_social_reward_global_level_applicable_for' ).val() == '4' ) {
                    jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                } else if ( jQuery( '#rs_social_reward_global_level_applicable_for' ).val() == '5' ) {
                    jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                } else {
                    jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).show() ;
                }

                jQuery( '#rs_social_reward_global_level_applicable_for' ).change( function () {
                    if ( jQuery( '#rs_social_reward_global_level_applicable_for' ).val() == '1' ) {
                        jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                    } else if ( jQuery( '#rs_social_reward_global_level_applicable_for' ).val() == '2' ) {
                        jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                    } else if ( jQuery( '#rs_social_reward_global_level_applicable_for' ).val() == '3' ) {
                        jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                    } else if ( jQuery( '#rs_social_reward_global_level_applicable_for' ).val() == '4' ) {
                        jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                    } else if ( jQuery( '#rs_social_reward_global_level_applicable_for' ).val() == '5' ) {
                        jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                    } else {
                        jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).show() ;
                    }
                } ) ;
                jQuery( '.rs_hide_bulk_update_for_social_reward_start' ).hide() ;
            } else {
                jQuery( '#rs_social_reward_global_level_applicable_for' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_products_for_social_reward' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_products_for_social_reward' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_include_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_exclude_particular_categories_for_social_reward' ).closest( 'tr' ).hide() ;
                jQuery( '.rs_hide_bulk_update_for_social_reward_start' ).show() ;
            }
        } ,
        post_or_page_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_post_or_page_settings() ;
        } ,
        show_or_hide_for_post_or_page_settings : function () {
            if ( ( jQuery( '#rs_global_social_enable_disable_reward_post' ).val() ) == '1' ) {
                jQuery( '#rs_global_social_facebook_reward_points_post' ).closest( 'tr' ).show() ;
                jQuery( '#rs_global_social_facebook_share_reward_points_post' ).closest( 'tr' ).show() ;
                jQuery( '#rs_global_social_twitter_reward_points_post' ).closest( 'tr' ).show() ;
                jQuery( '#rs_global_social_twitter_follow_reward_points_post' ).closest( 'tr' ).show() ;
                jQuery( '#rs_global_social_google_reward_points_post' ).closest( 'tr' ).show() ;
                jQuery( '#rs_global_social_vk_reward_points_post' ).closest( 'tr' ).show() ;
                jQuery( '#rs_global_social_instagram_reward_points_post' ).closest( 'tr' ).show() ;
                jQuery( '#rs_global_social_ok_follow_reward_points_post' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_global_social_facebook_share_reward_points_post' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_global_social_facebook_reward_points_post' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_global_social_twitter_reward_points_post' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_global_social_twitter_follow_reward_points_post' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_global_social_google_reward_points_post' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_global_social_vk_reward_points_post' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_global_social_instagram_reward_points_post' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_global_social_ok_follow_reward_points_post' ).closest( 'tr' ).hide() ;
            }
        } ,
        fb_like_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_fb_like_settings() ;
        } ,
        show_or_hide_for_fb_like_settings : function () {
            if ( ( jQuery( '#rs_global_show_hide_facebook_like_button' ).val() ) === '1' ) {
                jQuery( '#rs_global_show_hide_social_tooltip_for_facebook' ).parent().parent().show() ;
                jQuery( '#rs_global_social_facebook_url' ).parent().parent().show() ;
                jQuery( '#rs_facebook_like_icon_size' ).parent().parent().show() ;
                jQuery( '.rs_social_button_like' ).parent().parent().show() ;

                /*Show or Hide for FB Custom URL - Start*/
                if ( ( jQuery( '#rs_global_social_facebook_url' ).val() ) === '2' ) {
                    jQuery( '#rs_global_social_facebook_url_custom' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_global_social_facebook_url_custom' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_social_facebook_url' ).change( function () {
                    if ( ( jQuery( '#rs_global_social_facebook_url' ).val() ) === '2' ) {
                        jQuery( '#rs_global_social_facebook_url_custom' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_global_social_facebook_url_custom' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for FB Custom URL - End*/

                /*Show or Hide for FB Like Tooltip - Start*/
                if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_facebook' ).val() ) === '1' ) {
                    jQuery( '#rs_social_message_for_facebook' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_social_message_for_facebook' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_show_hide_social_tooltip_for_facebook' ).change( function () {
                    if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_facebook' ).val() ) === '1' ) {
                        jQuery( '#rs_social_message_for_facebook' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_social_message_for_facebook' ).parent().parent().hide() ;
                    }
                } ) ;
                jQuery( '#rs_send_mail_Facebook_like' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_Facebook_like' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_facebook_like' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_facebook_like' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_facebook_like' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_facebook_like' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_send_mail_Facebook_like' ).change( function () {
                    if ( jQuery( '#rs_send_mail_Facebook_like' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_facebook_like' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_email_message_facebook_like' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_email_subject_facebook_like' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message_facebook_like' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
                /*Show or Hide for FB Like Tooltip - End*/

                jQuery( '#rs_send_mail_post_fb_like' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_post_fb_like' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_post_fb_like' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_post_fb_like' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_post_fb_like' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_post_fb_like' ).closest( 'tr' ).hide() ;
                }
                jQuery( '#rs_send_mail_post_fb_like' ).change( function () {
                    if ( jQuery( '#rs_send_mail_post_fb_like' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_post_fb_like' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_email_message_post_fb_like' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_email_subject_post_fb_like' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message_post_fb_like' ).closest( 'tr' ).hide() ;
                    }
                } ) ;

                jQuery( '#rs_succcess_message_for_facebook_like' ).parent().parent().show() ;
                jQuery( '#rs_unsucccess_message_for_facebook_unlike' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_global_show_hide_social_tooltip_for_facebook' ).parent().parent().hide() ;
                jQuery( '#rs_social_message_for_facebook' ).parent().parent().hide() ;
                jQuery( '#rs_global_social_facebook_url' ).parent().parent().hide() ;
                jQuery( '#rs_global_social_facebook_url_custom' ).parent().parent().hide() ;
                jQuery( '#rs_succcess_message_for_facebook_like' ).parent().parent().hide() ;
                jQuery( '#rs_unsucccess_message_for_facebook_unlike' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_Facebook_like' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_facebook_like' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_facebook_like' ).parent().parent().hide() ;
                jQuery( '#rs_facebook_like_icon_size' ).parent().parent().hide() ;
                jQuery( '.rs_social_button_like' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_post_fb_like' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_post_fb_like' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_post_fb_like' ).parent().parent().hide() ;
            }
        } ,
        fb_share_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_fb_share_settings() ;
        } ,
        show_or_hide_for_fb_share_settings : function () {
            if ( ( jQuery( '#rs_global_show_hide_facebook_share_button' ).val() ) === '1' ) {
                jQuery( '#rs_global_show_hide_social_tooltip_for_facebook_share' ).parent().parent().show() ;
                jQuery( '#rs_fbshare_button_label' ).parent().parent().show() ;
                jQuery( '#rs_global_social_facebook_share_url' ).parent().parent().show() ;
                jQuery( '.rs_social_button_share' ).parent().parent().show() ;

                /*Show or Hide for FB Custom URL - Start*/
                if ( ( jQuery( '#rs_global_social_facebook_share_url' ).val() ) === '2' ) {
                    jQuery( '#rs_global_social_facebook_share_url_custom' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_global_social_facebook_share_url_custom' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_social_facebook_share_url' ).change( function () {
                    if ( ( jQuery( '#rs_global_social_facebook_share_url' ).val() ) === '2' ) {
                        jQuery( '#rs_global_social_facebook_share_url_custom' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_global_social_facebook_share_url_custom' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for FB Custom URL - End*/

                /*Show or Hide for FB Share Tooltip - Start*/
                if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_facebook_share' ).val() ) === '1' ) {
                    jQuery( '#rs_social_message_for_facebook_share' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_social_message_for_facebook_share' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_show_hide_social_tooltip_for_facebook_share' ).change( function () {
                    if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_facebook_share' ).val() ) === '1' ) {
                        jQuery( '#rs_social_message_for_facebook_share' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_social_message_for_facebook_share' ).parent().parent().hide() ;
                    }
                } ) ;
                jQuery( '#rs_send_mail_facebook_share' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_facebook_share' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_facebook_share' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_facebook_share' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_facebook_share' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_facebook_share' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_facebook_share' ).change( function () {
                    if ( jQuery( '#rs_send_mail_facebook_share' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_facebook_share' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_facebook_share' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_facebook_share' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_facebook_share' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for FB Share Tooltip - End*/

                jQuery( '#rs_send_mail_post_fb_share' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_post_fb_share' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_post_fb_share' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_post_fb_share' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_post_fb_share' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_post_fb_share' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_post_fb_share' ).change( function () {
                    if ( jQuery( '#rs_send_mail_post_fb_share' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_post_fb_share' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_post_fb_share' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_post_fb_share' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_post_fb_share' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_succcess_message_for_facebook_share' ).parent().parent().show() ;
                jQuery( '#rs_unsucccess_message_for_facebook_share' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_global_show_hide_social_tooltip_for_facebook_share' ).parent().parent().hide() ;
                jQuery( '#rs_social_message_for_facebook_share' ).parent().parent().hide() ;
                jQuery( '#rs_succcess_message_for_facebook_share' ).parent().parent().hide() ;
                jQuery( '#rs_unsucccess_message_for_facebook_share' ).parent().parent().hide() ;
                jQuery( '#rs_global_social_facebook_share_url' ).parent().parent().hide() ;
                jQuery( '#rs_global_social_facebook_share_url_custom' ).parent().parent().hide() ;
                jQuery( '#rs_fbshare_button_label' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_facebook_share' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_facebook_share' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_facebook_share' ).parent().parent().hide() ;
                jQuery( '.rs_social_button_share' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_post_fb_share' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_post_fb_share' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_post_fb_share' ).parent().parent().hide() ;
            }
        } ,
        twitter_tweet_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_twitter_tweet_settings() ;
        } ,
        show_or_hide_for_twitter_tweet_settings : function () {
            if ( ( jQuery( '#rs_global_show_hide_twitter_tweet_button' ).val() ) === '1' ) {
                jQuery( '#rs_global_show_hide_social_tooltip_for_twitter' ).parent().parent().show() ;
                jQuery( '#rs_global_social_twitter_url' ).parent().parent().show() ;
                jQuery( '.rs_social_button_tweet' ).parent().parent().show() ;

                /*Show or Hide for Twitter Custom URL - Start*/
                if ( ( jQuery( '#rs_global_social_twitter_url' ).val() ) === '2' ) {
                    jQuery( '#rs_global_social_twitter_url_custom' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_global_social_twitter_url_custom' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_social_twitter_url' ).change( function () {
                    jQuery( '#rs_global_social_twitter_url_custom' ).parent().parent().toggle() ;
                } ) ;
                /*Show or Hide for Twitter Custom URL - End*/

                /*Show or Hide for Twitter Tweet Tooltip - Start*/
                if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_twitter' ).val() ) === '1' ) {
                    jQuery( '#rs_social_message_for_twitter' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_social_message_for_twitter' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_show_hide_social_tooltip_for_twitter' ).change( function () {
                    if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_twitter' ).val() ) === '1' ) {
                        jQuery( '#rs_social_message_for_twitter' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_social_message_for_twitter' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_send_mail_tewitter_tweet' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_tewitter_tweet' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_twitter_tweet' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_twitter_tweet' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_twitter_tweet' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_twitter_tweet' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_tewitter_tweet' ).change( function () {
                    if ( jQuery( '#rs_send_mail_tewitter_tweet' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_twitter_tweet' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_twitter_tweet' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_twitter_tweet' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_twitter_tweet' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for Twitter Tweet Tooltip - End*/

                jQuery( '#rs_send_mail_post_tweet' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_post_tweet' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_post_tweet' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_post_tweet' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_post_tweet' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_post_tweet' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_post_tweet' ).change( function () {
                    if ( jQuery( '#rs_send_mail_post_tweet' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_post_tweet' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_post_tweet' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_post_tweet' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_post_tweet' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_succcess_message_for_twitter_share' ).parent().parent().show() ;
                jQuery( '#rs_unsucccess_message_for_twitter_unshare' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_global_show_hide_social_tooltip_for_twitter' ).parent().parent().hide() ;
                jQuery( '#rs_social_message_for_twitter' ).parent().parent().hide() ;
                jQuery( '#rs_succcess_message_for_twitter_share' ).parent().parent().hide() ;
                jQuery( '#rs_global_social_twitter_url' ).parent().parent().hide() ;
                jQuery( '#rs_global_social_twitter_url_custom' ).parent().parent().hide() ;
                jQuery( '#rs_unsucccess_message_for_twitter_unshare' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_tewitter_tweet' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_twitter_tweet' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_message_twitter_tweet' ).parent().parent().hide() ;
                jQuery( '.rs_social_button_tweet' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_post_tweet' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_post_tweet' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_message_post_tweet' ).parent().parent().hide() ;
            }
        } ,
        twitter_follow_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_twitter_follow_settings() ;
        } ,
        show_or_hide_for_twitter_follow_settings : function () {
            if ( ( jQuery( '#rs_global_show_hide_twitter_follow_tweet_button' ).val() ) === '1' ) {
                jQuery( '#rs_global_show_hide_social_tooltip_for_twitter_follow' ).parent().parent().show() ;
                jQuery( '#rs_global_social_twitter_profile_name' ).parent().parent().show() ;
                jQuery( '.rs_social_button_twitter_follow' ).closest( 'tr' ).show() ;

                /*Show or Hide for Twitter Follow Tooltip - Start*/
                if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_twitter_follow' ).val() ) === '1' ) {
                    jQuery( '#rs_social_message_for_twitter_follow' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_social_message_for_twitter_follow' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_show_hide_social_tooltip_for_twitter_follow' ).change( function () {
                    if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_twitter_follow' ).val() ) === '1' ) {
                        jQuery( '#rs_social_message_for_twitter_follow' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_social_message_for_twitter_follow' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_send_mail_twitter_follow' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_twitter_follow' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_twitter_follow' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_twitter_follow' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_twitter_follow' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_twitter_follow' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_twitter_follow' ).change( function () {
                    if ( jQuery( '#rs_send_mail_twitter_follow' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_twitter_follow' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_twitter_follow' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_twitter_follow' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_twitter_follow' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for Twitter Follow Tooltip - End*/

                jQuery( '#rs_send_mail_post_follow' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_post_follow' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_post_follow' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_post_follow' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_post_follow' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_post_follow' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_post_follow' ).change( function () {
                    if ( jQuery( '#rs_send_mail_post_follow' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_post_follow' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_post_follow' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_post_follow' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_post_follow' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_succcess_message_for_twitter_follow' ).parent().parent().show() ;
                jQuery( '#rs_unsucccess_message_for_twitter_unfollow' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_global_show_hide_social_tooltip_for_twitter_follow' ).parent().parent().hide() ;
                jQuery( '#rs_global_social_twitter_profile_name' ).parent().parent().hide() ;
                jQuery( '#rs_social_message_for_twitter_follow' ).parent().parent().hide() ;
                jQuery( '#rs_succcess_message_for_twitter_follow' ).parent().parent().hide() ;
                jQuery( '#rs_unsucccess_message_for_twitter_unfollow' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_twitter_follow' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_twitter_follow' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_twitter_follow' ).parent().parent().hide() ;
                jQuery( '.rs_social_button_twitter_follow' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_send_mail_post_follow' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_post_follow' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_post_follow' ).parent().parent().hide() ;
            }
        } ,
        instagram_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_instagram_settings() ;
        } ,
        show_or_hide_for_instagram_settings : function () {
            if ( ( jQuery( '#rs_global_show_hide_instagram_button' ).val() ) === '1' ) {
                jQuery( '#rs_instagram_profile_name' ).parent().parent().show() ;
                jQuery( '#rs_global_show_hide_social_tooltip_for_instagram' ).parent().parent().show() ;
                jQuery( '.rs_social_button_instagram' ).closest( 'tr' ).show() ;

                /*Show or Hide for Instagram Tooltip - Start*/
                if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_instagram' ).val() ) === '1' ) {
                    jQuery( '#rs_social_message_for_instagram' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_social_message_for_instagram' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_show_hide_social_tooltip_for_instagram' ).change( function () {
                    if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_instagram' ).val() ) === '1' ) {
                        jQuery( '#rs_social_message_for_instagram' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_social_message_for_instagram' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_send_mail_instagram' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_instagram' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_instagram' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_instagram' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_instagram' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_instagram' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_instagram' ).change( function () {
                    if ( jQuery( '#rs_send_mail_instagram' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_instagram' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_instagram' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_instagram' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_instagram' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for Instagram Tooltip - End*/

                jQuery( '#rs_send_mail_post_instagram' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_post_instagram' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_post_instagram' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_post_instagram' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_post_instagram' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_post_instagram' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_post_instagram' ).change( function () {
                    if ( jQuery( '#rs_send_mail_post_instagram' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_post_instagram' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_post_instagram' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_post_instagram' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_post_instagram' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_succcess_message_for_instagram' ).parent().parent().show() ;
                jQuery( '#rs_unsucccess_message_for_instagram' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_instagram_profile_name' ).parent().parent().hide() ;
                jQuery( '#rs_global_show_hide_social_tooltip_for_instagram' ).parent().parent().hide() ;
                jQuery( '#rs_social_message_for_instagram' ).parent().parent().hide() ;
                jQuery( '#rs_succcess_message_for_instagram' ).parent().parent().hide() ;
                jQuery( '#rs_unsucccess_message_for_instagram' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_instagram' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_instagram' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_instagram' ).parent().parent().hide() ;
                jQuery( '.rs_social_button_instagram' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_send_mail_post_instagram' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_post_instagram' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_post_instagram' ).parent().parent().hide() ;
            }
        } ,
        vk_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_vk_settings() ;
        } ,
        show_or_hide_for_vk_settings : function () {
            if ( ( jQuery( '#rs_global_show_hide_vk_button' ).val() ) === '1' ) {
                jQuery( '#rs_vk_application_id' ).parent().parent().show() ;
                jQuery( '#rs_global_show_hide_social_tooltip_for_vk' ).parent().parent().show() ;
                jQuery( '.rs_social_button_vk_like' ).parent().parent().show() ;

                /*Show or Hide for VK.Com Tooltip - Start*/
                if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_vk' ).val() ) === '1' ) {
                    jQuery( '#rs_social_message_for_vk' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_social_message_for_vk' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_show_hide_social_tooltip_for_vk' ).change( function () {
                    if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_vk' ).val() ) === '1' ) {
                        jQuery( '#rs_social_message_for_vk' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_social_message_for_vk' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for VK.Com Tooltip - End*/

                jQuery( '#rs_send_mail_vk' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_vk' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_vk' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_vk' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_vk' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_vk' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_vk' ).change( function () {
                    if ( jQuery( '#rs_send_mail_vk' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_vk' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_vk' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_vk' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_vk' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_send_mail_post_vk' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_post_vk' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_post_vk' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_post_vk' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_post_vk' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_post_vk' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_post_vk' ).change( function () {
                    if ( jQuery( '#rs_send_mail_post_vk' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_post_vk' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_post_vk' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_post_vk' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_post_vk' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_succcess_message_for_vk' ).parent().parent().show() ;
                jQuery( '#rs_unsucccess_message_for_vk' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_vk_application_id' ).parent().parent().hide() ;
                jQuery( '#rs_global_show_hide_social_tooltip_for_vk' ).parent().parent().hide() ;
                jQuery( '#rs_social_message_for_vk' ).parent().parent().hide() ;
                jQuery( '#rs_succcess_message_for_vk' ).parent().parent().hide() ;
                jQuery( '#rs_unsucccess_message_for_vk' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_vk' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_vk' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_vk' ).parent().parent().hide() ;
                jQuery( '.rs_social_button_vk_like' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_post_vk' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_post_vk' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_post_vk' ).parent().parent().hide() ;
            }
        } ,
        gplus_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_gplus_settings() ;
        } ,
        show_or_hide_for_gplus_settings : function () {
            if ( ( jQuery( '#rs_global_show_hide_google_plus_button' ).val() ) === '1' ) {
                jQuery( '#rs_global_social_google_url' ).parent().parent().show() ;
                jQuery( '#rs_global_show_hide_social_tooltip_for_google' ).parent().parent().show() ;
                jQuery( '.rs_social_button_gplus' ).parent().parent().show() ;

                /*Show or Hide for Google+1 Custom URL - Start*/
                if ( ( jQuery( '#rs_global_social_google_url' ).val() ) === '2' ) {
                    jQuery( '#rs_global_social_google_url_custom' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_global_social_google_url_custom' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_social_google_url' ).change( function () {
                    if ( ( jQuery( '#rs_global_social_google_url' ).val() ) === '2' ) {
                        jQuery( '#rs_global_social_google_url_custom' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_global_social_google_url_custom' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for Google+1 Custom URL - End*/

                /*Show or Hide for Google+1 Tooltip - Start*/
                if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_google' ).val() ) === '1' ) {
                    jQuery( '#rs_social_message_for_google_plus' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_social_message_for_google_plus' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_show_hide_social_tooltip_for_google' ).change( function () {
                    if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_google' ).val() ) === '1' ) {
                        jQuery( '#rs_social_message_for_google_plus' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_social_message_for_google_plus' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_send_mail_google' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_google' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_google' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_google' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_google' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_google' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_google' ).change( function () {
                    if ( jQuery( '#rs_send_mail_google' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_google' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_google' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_google' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_google' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for Google+1 Tooltip - End*/

                jQuery( '#rs_send_mail_post_gplus' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_send_mail_post_gplus' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_post_gplus' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_post_gplus' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_post_gplus' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_post_gplus' ).parent().parent().hide() ;
                }
                jQuery( '#rs_send_mail_post_gplus' ).change( function () {
                    if ( jQuery( '#rs_send_mail_post_gplus' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_post_gplus' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_post_gplus' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_post_gplus' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_post_gplus' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_succcess_message_for_google_share' ).parent().parent().show() ;
                jQuery( '#rs_unsucccess_message_for_google_unshare' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_global_social_google_url' ).parent().parent().hide() ;
                jQuery( '#rs_global_social_google_url_custom' ).parent().parent().hide() ;
                jQuery( '#rs_global_show_hide_social_tooltip_for_google' ).parent().parent().hide() ;
                jQuery( '#rs_social_message_for_google_plus' ).parent().parent().hide() ;
                jQuery( '#rs_succcess_message_for_google_share' ).parent().parent().hide() ;
                jQuery( '#rs_unsucccess_message_for_google_unshare' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_google' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_google' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_google' ).parent().parent().hide() ;
                jQuery( '.rs_social_button_gplus' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_post_gplus' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_subject_post_gplus' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_post_gplus' ).parent().parent().hide() ;
            }
        } ,
        ok_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_ok_settings() ;
        } ,
        show_or_hide_for_ok_settings : function () {
            if ( ( jQuery( '#rs_global_show_hide_ok_button' ).val() ) === '1' ) {
                jQuery( '#rs_global_social_ok_url' ).parent().parent().show() ;
                jQuery( '#rs_global_show_hide_social_tooltip_for_ok_follow' ).parent().parent().show() ;
                jQuery( '.rs_social_button_ok_ru' ).parent().parent().show() ;

                /*Show or Hide for Google+1 Custom URL - Start*/
                if ( ( jQuery( '#rs_global_social_ok_url' ).val() ) === '2' ) {
                    jQuery( '#rs_global_social_ok_url_custom' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_global_social_ok_url_custom' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_social_ok_url' ).change( function () {
                    if ( ( jQuery( '#rs_global_social_ok_url' ).val() ) === '2' ) {
                        jQuery( '#rs_global_social_ok_url_custom' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_global_social_ok_url_custom' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for Google+1 Custom URL - End*/

                /*Show or Hide for OK.ru Tooltip - Start*/
                if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_ok_follow' ).val() ) === '1' ) {
                    jQuery( '#rs_social_message_for_ok_follow' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_social_message_for_ok_follow' ).parent().parent().hide() ;
                }

                jQuery( '#rs_global_show_hide_social_tooltip_for_ok_follow' ).change( function () {
                    if ( ( jQuery( '#rs_global_show_hide_social_tooltip_for_ok_follow' ).val() ) === '1' ) {
                        jQuery( '#rs_social_message_for_ok_follow' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_social_message_for_ok_follow' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_send_mail_ok' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_ok' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_ok' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_ok' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_ok' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_ok' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_ok' ).change( function () {
                    if ( jQuery( '#rs_send_mail_ok' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_ok' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_ok' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_ok' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_ok' ).parent().parent().hide() ;
                    }
                } ) ;
                /*Show or Hide for OK.ru Tooltip - End*/

                jQuery( '#rs_send_mail_post_ok_ru' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_send_mail_post_ok_ru' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_post_ok_ru' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_post_ok_ru' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_post_ok_ru' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_post_ok_ru' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_post_ok_ru' ).change( function () {
                    if ( jQuery( '#rs_send_mail_post_ok_ru' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_post_ok_ru' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_post_ok_ru' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_post_ok_ru' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_post_ok_ru' ).parent().parent().hide() ;
                    }
                } ) ;

                jQuery( '#rs_succcess_message_for_ok_follow' ).parent().parent().show() ;
                jQuery( '#rs_unsucccess_message_for_ok_unfollow' ).parent().parent().show() ;
            } else {
                jQuery( '#rs_global_social_ok_url' ).parent().parent().hide() ;
                jQuery( '#rs_global_show_hide_social_tooltip_for_ok_follow' ).parent().parent().hide() ;
                jQuery( '#rs_social_message_for_ok_follow' ).parent().parent().hide() ;
                jQuery( '#rs_succcess_message_for_ok_follow' ).parent().parent().hide() ;
                jQuery( '#rs_unsucccess_message_for_ok_unfollow' ).parent().parent().hide() ;
                jQuery( '#rs_global_social_ok_url_custom' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_ok' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_ok' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_ok' ).parent().parent().hide() ;
                jQuery( '.rs_social_button_ok_ru' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_post_ok_ru' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_subject_post_ok_ru' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_post_ok_ru' ).parent().parent().hide() ;
            }
        } ,
        global_level_enable_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_global_level_enable_settings() ;
        } ,
        show_or_hide_for_global_level_enable_settings : function () {
            if ( jQuery( '#rs_global_social_enable_disable_reward' ).val() == '2' ) {
                jQuery( '.show_if_social_tab_enable' ).parent().parent().hide() ;
            } else {
                jQuery( '.show_if_social_tab_enable' ).parent().parent().show() ;
                /*Facebook Reward Type Validation in jQuery Start*/
                if ( ( jQuery( '#rs_global_social_reward_type_facebook' ).val() ) === '1' ) {
                    jQuery( '#rs_global_social_facebook_reward_points' ).parent().parent().show() ;
                    jQuery( '#rs_global_social_facebook_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_global_social_facebook_reward_points' ).parent().parent().hide() ;
                    jQuery( '#rs_global_social_facebook_reward_percent' ).parent().parent().show() ;
                }
                jQuery( '#rs_global_social_reward_type_facebook' ).change( function () {
                    if ( ( jQuery( this ).val() ) === '1' ) {
                        jQuery( '#rs_global_social_facebook_reward_points' ).parent().parent().show() ;
                        jQuery( '#rs_global_social_facebook_reward_percent' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_social_facebook_reward_points' ).parent().parent().hide() ;
                        jQuery( '#rs_global_social_facebook_reward_percent' ).parent().parent().show() ;
                    }
                } ) ;
                if ( ( jQuery( '#rs_global_social_reward_type_facebook_share' ).val() ) === '1' ) {
                    jQuery( '#rs_global_social_facebook_share_reward_points' ).parent().parent().show() ;
                    jQuery( '#rs_global_social_facebook_share_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_global_social_facebook_share_reward_points' ).parent().parent().hide() ;
                    jQuery( '#rs_global_social_facebook_share_reward_percent' ).parent().parent().show() ;
                }
                jQuery( '#rs_global_social_reward_type_facebook_share' ).change( function () {
                    if ( ( jQuery( this ).val() ) === '1' ) {
                        jQuery( '#rs_global_social_facebook_share_reward_points' ).parent().parent().show() ;
                        jQuery( '#rs_global_social_facebook_share_reward_percent' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_social_facebook_share_reward_points' ).parent().parent().hide() ;
                        jQuery( '#rs_global_social_facebook_share_reward_percent' ).parent().parent().show() ;
                    }
                } ) ;
                /*Facebook Reward Type Validation in jQuery Ends*/

                /*Twitter Reward Type Validation in jQuery Start*/
                if ( ( jQuery( '#rs_global_social_reward_type_twitter' ).val() ) === '1' ) {
                    jQuery( '#rs_global_social_twitter_reward_points' ).parent().parent().show() ;
                    jQuery( '#rs_global_social_twitter_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_global_social_twitter_reward_points' ).parent().parent().hide() ;
                    jQuery( '#rs_global_social_twitter_reward_percent' ).parent().parent().show() ;
                }
                jQuery( '#rs_global_social_reward_type_twitter' ).change( function () {
                    if ( ( jQuery( this ).val() ) === '1' ) {
                        jQuery( '#rs_global_social_twitter_reward_points' ).parent().parent().show() ;
                        jQuery( '#rs_global_social_twitter_reward_percent' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_social_twitter_reward_points' ).parent().parent().hide() ;
                        jQuery( '#rs_global_social_twitter_reward_percent' ).parent().parent().show() ;
                    }
                } ) ;

                /*Twitter Reward Type Validation in jQuery Ends*/
                if ( ( jQuery( '#rs_global_social_reward_type_twitter_follow' ).val() ) === '1' ) {
                    jQuery( '#rs_global_social_twitter_follow_reward_points' ).parent().parent().show() ;
                    jQuery( '#rs_global_social_twitter_follow_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_global_social_twitter_follow_reward_points' ).parent().parent().hide() ;
                    jQuery( '#rs_global_social_twitter_follow_reward_percent' ).parent().parent().show() ;
                }
                jQuery( '#rs_global_social_reward_type_twitter_follow' ).change( function () {
                    if ( ( jQuery( this ).val() ) === '1' ) {
                        jQuery( '#rs_global_social_twitter_follow_reward_points' ).parent().parent().show() ;
                        jQuery( '#rs_global_social_twitter_follow_reward_percent' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_social_twitter_follow_reward_points' ).parent().parent().hide() ;
                        jQuery( '#rs_global_social_twitter_follow_reward_percent' ).parent().parent().show() ;
                    }
                } ) ;

                /*ok.ru Reward Type Validation in jQuery Ends*/
                if ( ( jQuery( '#rs_global_social_reward_type_ok_follow' ).val() ) === '1' ) {
                    jQuery( '#rs_global_social_ok_follow_reward_points' ).parent().parent().show() ;
                    jQuery( '#rs_global_social_ok_follow_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_global_social_ok_follow_reward_points' ).parent().parent().hide() ;
                    jQuery( '#rs_global_social_ok_follow_reward_percent' ).parent().parent().show() ;
                }
                jQuery( '#rs_global_social_reward_type_ok_follow' ).change( function () {
                    if ( ( jQuery( this ).val() ) === '1' ) {
                        jQuery( '#rs_global_social_ok_follow_reward_points' ).parent().parent().show() ;
                        jQuery( '#rs_global_social_ok_follow_reward_percent' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_social_ok_follow_reward_points' ).parent().parent().hide() ;
                        jQuery( '#rs_global_social_ok_follow_reward_percent' ).parent().parent().show() ;
                    }
                } ) ;
                /*Google Reward Type Validation in jQuery Start*/
                if ( ( jQuery( '#rs_global_social_reward_type_google' ).val() ) === '1' ) {
                    jQuery( '#rs_global_social_google_reward_points' ).parent().parent().show() ;
                    jQuery( '#rs_global_social_google_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_global_social_google_reward_points' ).parent().parent().hide() ;
                    jQuery( '#rs_global_social_google_reward_percent' ).parent().parent().show() ;
                }
                jQuery( '#rs_global_social_reward_type_google' ).change( function () {
                    if ( ( jQuery( this ).val() ) === '1' ) {
                        jQuery( '#rs_global_social_google_reward_points' ).parent().parent().show() ;
                        jQuery( '#rs_global_social_google_reward_percent' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_social_google_reward_points' ).parent().parent().hide() ;
                        jQuery( '#rs_global_social_google_reward_percent' ).parent().parent().show() ;
                    }
                } ) ;
                /*Google Reward Type Validation in jQuery Ends*/

                /*VK Reward Type Validation in jQuery Start*/
                if ( ( jQuery( '#rs_global_social_reward_type_vk' ).val() ) === '1' ) {
                    jQuery( '#rs_global_social_vk_reward_points' ).parent().parent().show() ;
                    jQuery( '#rs_global_social_vk_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_global_social_vk_reward_points' ).parent().parent().hide() ;
                    jQuery( '#rs_global_social_vk_reward_percent' ).parent().parent().show() ;
                }
                jQuery( '#rs_global_social_reward_type_vk' ).change( function () {
                    if ( ( jQuery( this ).val() ) === '1' ) {
                        jQuery( '#rs_global_social_vk_reward_points' ).parent().parent().show() ;
                        jQuery( '#rs_global_social_vk_reward_percent' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_social_vk_reward_points' ).parent().parent().hide() ;
                        jQuery( '#rs_global_social_vk_reward_percent' ).parent().parent().show() ;
                    }
                } ) ;
                if ( ( jQuery( '#rs_global_social_reward_type_instagram' ).val() ) === '1' ) {
                    jQuery( '#rs_global_social_instagram_reward_points' ).parent().parent().show() ;
                    jQuery( '#rs_global_social_instagram_reward_percent' ).parent().parent().hide() ;
                } else {
                    jQuery( '#rs_global_social_instagram_reward_points' ).parent().parent().hide() ;
                    jQuery( '#rs_global_social_instagram_reward_percent' ).parent().parent().show() ;
                }
                jQuery( '#rs_global_social_reward_type_instagram' ).change( function () {
                    if ( ( jQuery( this ).val() ) === '1' ) {
                        jQuery( '#rs_global_social_instagram_reward_points' ).parent().parent().show() ;
                        jQuery( '#rs_global_social_instagram_reward_percent' ).parent().parent().hide() ;
                    } else {
                        jQuery( '#rs_global_social_instagram_reward_points' ).parent().parent().hide() ;
                        jQuery( '#rs_global_social_instagram_reward_percent' ).parent().parent().show() ;
                    }
                } ) ;
                /*VK Reward Type Validation in jQuery Ends*/
            }
        } ,
        fblike_restriction_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_fblike_restriction_settings() ;
        } ,
        show_or_hide_for_fblike_restriction_settings : function () {
            if ( jQuery( '#rs_enable_fblike_restriction' ).is( ':checked' ) == true ) {
                jQuery( '#rs_no_of_fblike_count' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restriction_message_for_fblike' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_no_of_fblike_count' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_restriction_message_for_fblike' ).closest( 'tr' ).hide() ;
            }
        } ,
        fbshare_restriction_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_fbshare_restriction_settings() ;
        } ,
        show_or_hide_for_fbshare_restriction_settings : function () {
            if ( jQuery( '#rs_enable_fbshare_restriction' ).is( ':checked' ) == true ) {
                jQuery( '#rs_no_of_fbshare_count' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restriction_message_for_fbshare' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_no_of_fbshare_count' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_restriction_message_for_fbshare' ).closest( 'tr' ).hide() ;
            }
        } ,
        twitter_tweet_restriction_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_twitter_tweet_restriction_settings() ;
        } ,
        show_or_hide_for_twitter_tweet_restriction_settings : function () {
            if ( jQuery( '#rs_enable_tweet_restriction' ).is( ':checked' ) == true ) {
                jQuery( '#rs_no_of_tweet_count' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restriction_message_for_tweet' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_no_of_tweet_count' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_restriction_message_for_tweet' ).closest( 'tr' ).hide() ;
            }
        } ,
        twitter_follow_restriction_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_twitter_follow_restriction_settings() ;
        } ,
        show_or_hide_for_twitter_follow_restriction_settings : function () {
            if ( jQuery( '#rs_enable_twitter_follow_restriction' ).is( ':checked' ) == true ) {
                jQuery( '#rs_no_of_twitter_follow_count' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restriction_message_for_twitter_follow' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_no_of_twitter_follow_count' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_restriction_message_for_twitter_follow' ).closest( 'tr' ).hide() ;
            }
        } ,
        instagram_restriction_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_instagram_restriction_settings() ;
        } ,
        show_or_hide_for_instagram_restriction_settings : function () {
            if ( jQuery( '#rs_enable_instagram_restriction' ).is( ':checked' ) == true ) {
                jQuery( '#rs_no_of_instagram_count' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restriction_message_for_instagram' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_no_of_instagram_count' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_restriction_message_for_instagram' ).closest( 'tr' ).hide() ;
            }
        } ,
        vk_restriction_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_vk_restriction_settings() ;
        } ,
        show_or_hide_for_vk_restriction_settings : function () {
            if ( jQuery( '#rs_enable_vk_restriction' ).is( ':checked' ) == true ) {
                jQuery( '#rs_no_of_vk_count' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restriction_message_for_vk' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_no_of_vk_count' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_restriction_message_for_vk' ).closest( 'tr' ).hide() ;
            }
        } ,
        gplus_restriction_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_gplus_restriction_settings() ;
        } ,
        show_or_hide_for_gplus_restriction_settings : function () {
            if ( jQuery( '#rs_enable_gplus_restriction' ).is( ':checked' ) == true ) {
                jQuery( '#rs_no_of_gplus_count' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restriction_message_for_gplus' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_no_of_gplus_count' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_restriction_message_for_gplus' ).closest( 'tr' ).hide() ;
            }
        } ,
        ok_restriction_settings : function () {
            SocialRewardPointsScripts.show_or_hide_for_ok_restriction_settings() ;
        } ,
        show_or_hide_for_ok_restriction_settings : function () {
            if ( jQuery( '#rs_enable_ok_restriction' ).is( ':checked' ) == true ) {
                jQuery( '#rs_no_of_ok_count' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restriction_message_for_ok' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_no_of_ok_count' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_restriction_message_for_ok' ).closest( 'tr' ).hide() ;
            }
        } ,
        product_category_selection : function () {
            SocialRewardPointsScripts.show_or_hide_for_product_category_selection() ;
        } ,
        show_or_hide_for_product_category_selection : function () {
            if ( ( jQuery( '.rs_which_social_product_selection' ).val() === '1' ) ) {
                jQuery( '#rs_select_particular_social_products' ).parent().parent().hide() ;
                jQuery( '#rs_select_particular_social_categories' ).parent().parent().hide() ;
            } else if ( jQuery( '.rs_which_social_product_selection' ).val() === '2' ) {
                jQuery( '#rs_select_particular_social_products' ).parent().parent().show() ;
                jQuery( '#rs_select_particular_social_categories' ).parent().parent().hide() ;
            } else if ( jQuery( '.rs_which_social_product_selection' ).val() === '3' ) {
                jQuery( '#rs_select_particular_social_products' ).parent().parent().hide() ;
                jQuery( '#rs_select_particular_social_categories' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_select_particular_social_categories' ).parent().parent().show() ;
                jQuery( '#rs_select_particular_social_products' ).parent().parent().hide() ;
            }
        } ,
        bulk_update_points_for_social_reward : function ( ) {
            var rsconfirm = confirm( "It is strongly recommended that you do not reload or refresh page. Are you sure you wish to update now?" ) ;
            if ( rsconfirm === true ) {
                SocialRewardPointsScripts.block( '.rs_hide_bulk_update_for_social_reward_start' ) ;
                var data = {
                    action : 'bulk_update_points_for_social_rewards' ,
                    sumo_security : fp_social_params.social_reward_bulk_update ,
                    productselection : $( '#rs_which_social_product_selection' ).val() ,
                    enablereward : $( '#rs_local_enable_disable_social_reward' ).val() ,
                    selectedproducts : $( '#rs_select_particular_social_products' ).val() ,
                    selectedcategories : $( '#rs_select_particular_social_categories' ).val() ,
                    fblikerewardtype : $( '#rs_local_reward_type_for_facebook' ).val() ,
                    fblikerewardpoints : $( '#rs_local_reward_points_facebook' ).val() ,
                    fblikerewardpercent : $( '#rs_local_reward_percent_facebook' ).val() ,
                    fbsharerewardtype : $( '#rs_local_reward_type_for_facebook_share' ).val() ,
                    fbsharerewardpoints : $( '#rs_local_reward_points_facebook_share' ).val() ,
                    fbsharerewardpercent : $( '#rs_local_reward_percent_facebook_share' ).val() ,
                    twitterrewardtype : $( '#rs_local_reward_type_for_twitter' ).val() ,
                    twitterrewardpoints : $( '#rs_local_reward_points_twitter' ).val() ,
                    twitterrewardpercent : $( '#rs_local_reward_percent_twitter' ).val() ,
                    gplusrewardtype : $( '#rs_local_reward_type_for_google' ).val() ,
                    gplusrewardpoints : $( '#rs_local_reward_points_google' ).val() ,
                    gplusrewardpercent : $( '#rs_local_reward_percent_google' ).val() ,
                    vkrewardtype : $( '#rs_local_reward_type_for_vk' ).val() ,
                    vkrewardpoints : $( '#rs_local_reward_points_vk' ).val() ,
                    vkrewardpercent : $( '#rs_local_reward_percent_vk' ).val() ,
                    twitterfollowrewardtype : $( '#rs_local_reward_type_for_twitter_follow' ).val() ,
                    twitterfollowrewardpoints : $( '#rs_local_reward_points_twitter_follow' ).val() ,
                    twitterfollowrewardpercent : $( '#rs_local_reward_percent_twitter_follow' ).val() ,
                    instagramrewardtype : $( '#rs_local_reward_type_for_instagram' ).val() ,
                    instagramrewardpoints : $( '#rs_local_reward_points_instagram' ).val() ,
                    instagramrewardpercent : $( '#rs_local_reward_percent_instagram' ).val() ,
                    okrewardtype : $( '#rs_local_reward_type_for_ok_follow' ).val() ,
                    okrewardpoints : $( '#rs_local_reward_points_ok_follow' ).val() ,
                    okrewardpercent : $( '#rs_local_reward_percent_ok_follow' ).val() ,
                } ;
                $.post( fp_social_params.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                        window.location.href = fp_social_params.redirecturl ;
                    } else {
                        window.alert( response.data.error ) ;
                    }
                    SocialRewardPointsScripts.unblock( '.rs_hide_bulk_update_for_social_reward_start' ) ;
                } ) ;
            }
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
            $( id ).unblock() ;
        } ,
    } ;
    SocialRewardPointsScripts.init() ;
} ) ;