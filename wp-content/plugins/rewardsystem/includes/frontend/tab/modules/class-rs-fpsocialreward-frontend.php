<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionForSocialRewards' ) ) {

    class RSFunctionForSocialRewards {

        public static function init() {
            if ( get_option( 'rs_global_position_sumo_social_buttons' ) == '1' ) {

                add_action( 'woocommerce_before_single_product' , array( __CLASS__ , 'social_buttons_for_products' ) ) ;
            } elseif ( get_option( 'rs_global_position_sumo_social_buttons' ) == '2' ) {

                add_action( 'woocommerce_before_single_product_summary' , array( __CLASS__ , 'social_buttons_for_products' ) ) ;
            } elseif ( get_option( 'rs_global_position_sumo_social_buttons' ) == '3' ) {

                add_action( 'woocommerce_single_product_summary' , array( __CLASS__ , 'social_buttons_for_products' ) ) ;
            } elseif ( get_option( 'rs_global_position_sumo_social_buttons' ) == '4' ) {

                add_action( 'woocommerce_after_single_product' , array( __CLASS__ , 'social_buttons_for_products' ) ) ;
            } elseif ( get_option( 'rs_global_position_sumo_social_buttons' ) == '6' ) {

                add_action( 'woocommerce_product_meta_end' , array( __CLASS__ , 'social_buttons_for_products' ) ) ;
            } else {
                add_action( 'woocommerce_after_single_product_summary' , array( __CLASS__ , 'social_buttons_for_products' ) ) ;
            }

            if ( get_option( 'rs_global_position_sumo_social_share_buttons' ) == '2' ) {
                add_action( 'get_footer' , array( __CLASS__ , 'social_buttons_for_post_and_page' ) ) ;
            } else {
                add_action( 'loop_start' , array( __CLASS__ , 'social_buttons_for_post_and_page' ) ) ;
            }
        }

        public static function localized_values_for_script( $post , $type , $data ) {
            ?>
            <style type="text/css">
                .fb_edge_widget_with_comment span.fb_edge_comment_widget iframe.fb_ltr {
                    display: none !important;
                }
                .fb-like{
                    height: 20px !important;
                    overflow: hidden !important;
                }
                .tipsy-inner {
                    background-color:#<?php echo get_option( 'rs_social_tooltip_bg_color' ) ; ?>;

                    color:#<?php echo get_option( 'rs_social_tooltip_text_color' ) ; ?>;
                }
                .tipsy-arrow-s { border-top-color: #<?php echo get_option( 'rs_social_tooltip_bg_color' ) ; ?>; }
            </style>
            <?php
            $FBLikeScript      = $FBShareScript     = $TweetScript       = $FollowScript      = $InstaFollowScript = $VKLikeScript      = $GplusScript       = $OKruScript        = array() ;
            $LocalizedScript   = array(
                'ajaxurl'                => SRP_ADMIN_AJAX_URL ,
                'post_id'                => $post->ID ,
                'buttonlanguage'         => get_option( 'rs_language_selection_for_button' ) ,
                'wplanguage'             => get_option( 'WPLANG' ) ,
                'type'                   => $type ,
                'fbappid'                => get_option( 'rs_facebook_application_id' ) ,
                'vkappid'                => get_option( 'rs_vk_application_id' ) ,
                'showfblike'             => get_option( 'rs_global_show_hide_facebook_like_button' ) ,
                'showfbshare'            => get_option( 'rs_global_show_hide_facebook_share_button' ) ,
                'showtweet'              => get_option( 'rs_global_show_hide_twitter_tweet_button' ) ,
                'showtwitterfollow'      => get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) ,
                'showgplus'              => get_option( 'rs_global_show_hide_google_plus_button' ) ,
                'showvk'                 => get_option( 'rs_global_show_hide_vk_button' ) ,
                'showinstagram'          => get_option( 'rs_global_show_hide_instagram_button' ) ,
                'instagram_button_type'  => get_option( 'rs_social_button_instagram' , '1' ) ,
                'instagram_profile_name' => get_option( 'rs_instagram_profile_name' ) ,
                'showok'                 => get_option( 'rs_global_show_hide_ok_button' ) ,
                    ) ;
            if ( get_option( 'rs_facebook_application_id' ) != '' ) {
                if ( get_option( 'rs_global_show_hide_facebook_like_button' ) == '1' ) {
                    $AllowFBlike  = allow_points_for_social_action( get_current_user_id() , 'fb_like_count_per_day' , get_option( 'rs_enable_fblike_restriction' ) , get_option( 'rs_no_of_fblike_count' ) ) ;
                    $FBLikeScript = array(
                        'fb_like'                => wp_create_nonce( 'fb-like' ) ,
                        'allowfblike'            => $AllowFBlike ,
                        'fbliketooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_facebook' ) ,
                        'fbliketooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_facebook' ) ) ,
                        'fbliketooltipclassname' => get_option( 'rs_social_button_like' ) == '1' ? 'fb-like' : 'rs_custom_fblike_button' ,
                            ) ;
                }
                if ( get_option( 'rs_global_show_hide_facebook_share_button' ) == '1' ) {
                    $AllowFBShare  = allow_points_for_social_action( get_current_user_id() , 'fb_share_count_per_day' , get_option( 'rs_enable_fbshare_restriction' ) , get_option( 'rs_no_of_fbshare_count' ) ) ;
                    $PostImage     = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ) ;
                    $URL           = get_option( 'rs_global_social_facebook_share_url' ) == '1' ? get_permalink() : get_option( 'rs_global_social_facebook_share_url_custom' ) ;
                    $Classname     = is_product() ? 'share_wrapper1' : 'share_wrapper11' ;
                    $FBShareScript = array(
                        'fb_share'                => wp_create_nonce( 'fb-share' ) ,
                        'allowfbshare'            => $AllowFBShare ,
                        'post_title'              => $post->post_title ,
                        'post_desc'               => $post->post_content ,
                        'post_url'                => $URL ,
                        'post_caption'            => $post->post_excerpt ,
                        'post_image'              => $PostImage[ 0 ] ,
                        'fbsharetooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_facebook_share' ) ,
                        'fbsharetooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_facebook_share' ) ) ,
                        'fbsharetooltipclassname' => get_option( 'rs_social_button_share' ) == '1' ? $Classname : 'rs_custom_fbshare_button' ,
                            ) ;
                }
            }
            if ( get_option( 'rs_global_show_hide_twitter_tweet_button' ) == '1' ) {
                $AllowTweet  = allow_points_for_social_action( get_current_user_id() , 'twitter_tweet_count_per_day' , get_option( 'rs_enable_tweet_restriction' ) , get_option( 'rs_no_of_tweet_count' ) ) ;
                $TweetScript = array(
                    'twitter_tweet'         => wp_create_nonce( 'twitter-tweet' ) ,
                    'allowtweet'            => $AllowTweet ,
                    'tweettooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_twitter' ) ,
                    'tweettooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_twitter' ) ) ,
                    'tweettooltipclassname' => get_option( 'rs_social_button_tweet' ) == '1' ? 'rstwitter-button-msg' : 'rs_custom_tweet_button' ,
                        ) ;
            }
            if ( get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) == '1' && get_option( 'rs_global_social_twitter_profile_name' ) != '' ) {
                $AllowFollow  = allow_points_for_social_action( get_current_user_id() , 'twitter_follow_count_per_day' , get_option( 'rs_enable_twitter_follow_restriction' ) , get_option( 'rs_no_of_twitter_follow_count' ) ) ;
                $FollowScript = array(
                    'twitter_follow'         => wp_create_nonce( 'twitter-follow' ) ,
                    'allowfollow'            => $AllowFollow ,
                    'followtooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_twitter_follow' ) ,
                    'followtooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_twitter_follow' ) ) ,
                    'followtooltipclassname' => get_option( 'rs_social_button_twitter_follow' ) == '1' ? 'rstwitterfollow-button-msg' : 'rs_custom_tweetfollow_button' ,
                        ) ;
            }
            if ( get_option( 'rs_global_show_hide_instagram_button' ) == '1' && get_option( 'rs_instagram_profile_name' ) != '' ) {
                $AllowInstaFollow  = allow_points_for_social_action( get_current_user_id() , 'instagram_count_per_day' , get_option( 'rs_enable_instagram_restriction' ) , get_option( 'rs_no_of_instagram_count' ) ) ;
                $Classname         = is_product() ? 'instagram_button' : 'instagram_button_post' ;
                $InstaFollowScript = array(
                    'instagram_follow'      => wp_create_nonce( 'instagram-follow' ) ,
                    'allowinstagramfollow'  => $AllowInstaFollow ,
                    'instagramtooltip'      => get_option( 'rs_global_show_hide_social_tooltip_for_instagram' ) ,
                    'instagramtooltipmsg'   => do_shortcode( get_option( 'rs_social_message_for_instagram' ) ) ,
                    'instatooltipclassname' => get_option( 'rs_social_button_instagram' ) == '1' ? $Classname : 'rs_custom_instagram_button' ,
                        ) ;
            }
            if ( get_option( 'rs_global_show_hide_vk_button' ) == '1' && get_option( 'rs_vk_application_id' ) != '' ) {
                $AllowVKLike  = allow_points_for_social_action( get_current_user_id() , 'vk_like_count_per_day' , get_option( 'rs_enable_vk_restriction' ) , get_option( 'rs_no_of_vk_count' ) ) ;
                $VKLikeScript = array(
                    'vk_like'            => wp_create_nonce( 'vk-like' ) ,
                    'allowvklike'        => $AllowVKLike ,
                    'vktooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_vk' ) ,
                    'vktooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_vk' ) ) ,
                    'vktooltipclassname' => get_option( 'rs_social_button_vk_like' ) == '1' ? 'vk-like' : 'rs_custom_vklike_button' ,
                        ) ;
            }
            if ( get_option( 'rs_global_show_hide_google_plus_button' ) == '1' ) {
                $AllowGplus  = allow_points_for_social_action( get_current_user_id() , 'gplus_share_count_per_day' , get_option( 'rs_enable_gplus_restriction' ) , get_option( 'rs_no_of_gplus_count' ) ) ;
                $GplusScript = array(
                    'gplus_share'           => wp_create_nonce( 'gplus-share' ) ,
                    'allowgplus'            => $AllowGplus ,
                    'gplustooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_google' ) ,
                    'gplustooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_google_plus' ) ) ,
                    'gplustooltipclassname' => get_option( 'rs_social_button_gplus' ) == '1' ? 'google-plus-one' : 'rs_custom_gplus_button' ,
                        ) ;
            }
            if ( get_option( 'rs_global_show_hide_ok_button' ) == '1' ) {
                $AllowOKru  = allow_points_for_social_action( get_current_user_id() , 'ok_follow_count_per_day' , get_option( 'rs_enable_ok_restriction' ) , get_option( 'rs_no_of_ok_count' ) ) ;
                $URL        = get_option( 'rs_global_social_ok_url' ) == '1' ? get_permalink() : get_option( 'rs_global_social_ok_url_custom' ) ;
                $OKruScript = array(
                    'url'                => $URL ,
                    'okru_share'         => wp_create_nonce( 'okru-share' ) ,
                    'allowokru'          => $AllowOKru ,
                    'oktooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_ok_follow' ) ,
                    'oktooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_ok_follow' ) ) ,
                    'oktooltipclassname' => get_option( 'rs_social_button_ok_ru' ) == '1' ? 'ok-share-button' : 'rs_custom_ok_button' ,
                        ) ;
            }
            $MergedScript = array_merge( $LocalizedScript , $FBLikeScript , $FBShareScript , $TweetScript , $FollowScript , $InstaFollowScript , $VKLikeScript , $GplusScript , $OKruScript ) ;
            return $MergedScript ;
        }

        public static function social_buttons_for_post_and_page() {
            $did_action = get_option( 'rs_global_position_sumo_social_share_buttons' ) == 1 ? did_action( 'loop_start' ) : did_action( 'get_footer' ) ;
            if ( $did_action > 1 )
                return ;

            if ( ! is_user_logged_in() )
                return ;

            $UserId      = get_current_user_id() ;
            $BanningType = check_banning_type( $UserId ) ;
            if ( $BanningType == 'earningonly' || $BanningType == 'both' )
                return ;

            if ( get_option( 'rs_global_social_enable_disable_reward_post' ) == '2' )
                return ;

            if ( is_shop() || is_cart() || is_checkout() || is_product() || is_account_page() || is_product_category() )
                return ;

            global $post ;
            if ( ! $post ) {
                return ;
            }

            $OldData         = array(
                'fblike'    => get_user_meta( $UserId , '_rsfacebooklikes_post' , true ) ,
                'fbshare'   => get_user_meta( $UserId , '_rsfacebookshare_post' , true ) ,
                'tweet'     => get_user_meta( $UserId , '_rstwittertweet_post' , true ) ,
                'follow'    => get_user_meta( $UserId , '_rstwitterfollow_post' , true ) ,
                'okfollow'  => get_user_meta( $UserId , '_rsokfollow_post' , true ) ,
                'gplus'     => get_user_meta( $UserId , '_rsgoogleshares_post' , true ) ,
                'vklike'    => get_user_meta( $UserId , '_rsvklike_post' , true ) ,
                'instagram' => get_user_meta( $UserId , '_rsinstagram_post' , true )
                    ) ;
            wp_enqueue_script( 'fp_social_action' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-social-action-frontend.js" , array() , SRP_VERSION ) ;
            $LocalizedScript = self::localized_values_for_script( $post , 'postorpage' , $OldData ) ;
            wp_localize_script( 'fp_social_action' , 'fp_social_action_params' , $LocalizedScript ) ;
            if ( get_option( 'rs_facebook_application_id' ) != '' && (get_option( 'rs_global_show_hide_facebook_like_button' ) == '1' || get_option( 'rs_global_show_hide_facebook_share_button' ) == '1') ) {
                ?>
                <div id="fb-root"></div>
                <?php
            }
            if ( get_option( 'rs_global_show_hide_google_plus_button' ) == '1' ) {
                ?>
                <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
                <?php
            }
            ?>
            <style type="text/css">
            <?php echo get_option( 'rs_social_custom_css' ) ; ?>
            </style>

            <table class="rs_social_sharing_buttons" style="display:<?php echo get_option( 'rs_social_button_position_troubleshoot' ) ; ?>">
                <tr>
                    <?php
                    if ( get_option( 'rs_facebook_application_id' ) != '' ) {
                        if ( get_option( 'rs_global_show_hide_facebook_like_button' ) == '1' ) {
                            self::fb_like_button() ;
                        }
                        if ( get_option( 'rs_global_show_hide_facebook_share_button' ) == '1' ) {
                            self::fb_share_button() ;
                        }
                    }
                    if ( get_option( 'rs_global_show_hide_twitter_tweet_button' ) == '1' ) {
                        self::tweet_button() ;
                    }
                    if ( get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) == '1' && get_option( 'rs_global_social_twitter_profile_name' ) != '' ) {
                        self::twitter_follow_button() ;
                    }
                    if ( get_option( 'rs_global_show_hide_google_plus_button' ) == '1' ) {
                        self::gplus_share_button() ;
                    }
                    if ( get_option( 'rs_global_show_hide_vk_button' ) == '1' && get_option( 'rs_vk_application_id' ) != '' ) {
                        self::vk_like_button() ;
                    }
                    if ( get_option( 'rs_global_show_hide_instagram_button' ) == '1' && get_option( 'rs_instagram_profile_name' ) != '' ) {
                        self::instagram_follow_button() ;
                    }
                    if ( get_option( 'rs_global_show_hide_ok_button' ) == '1' ) {
                        self::ok_share_button() ;
                    }
                    ?>
                </tr>
            </table>
            <div class="social_promotion_success_message"></div>
            <?php
        }

        public static function social_buttons_for_products() {
            if ( ! is_user_logged_in() )
                return ;

            $UserId      = get_current_user_id() ;
            $BanningType = check_banning_type( $UserId ) ;
            if ( $BanningType == 'earningonly' || $BanningType == 'both' )
                return ;

            global $post ;
            if ( ! $post ) {
                return ;
            }
            if ( get_option( 'rs_enable_product_category_level_for_social_reward' ) == 'no' ) {
                $Options        = array(
                    'applicable_for'      => get_option( 'rs_social_reward_global_level_applicable_for' ) ,
                    'included_products'   => get_option( 'rs_include_products_for_social_reward' ) ,
                    'excluded_products'   => get_option( 'rs_exclude_products_for_social_reward' ) ,
                    'included_categories' => get_option( 'rs_include_particular_categories_for_social_reward' ) ,
                    'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_social_reward' )
                        ) ;
                $product_filter = srp_product_filter_for_quick_setup( $post->ID , $post->ID , $Options ) == '2' ? true : false ;
                $product_filter = (get_option( 'rs_global_social_enable_disable_reward' ) === '1') ? $product_filter : false ;
            } elseif ( get_option( 'rs_enable_product_category_level_for_social_reward' ) == 'yes' ) {
                $product_filter = (get_post_meta( @$post->ID , '_socialrewardsystemcheckboxvalue' , true ) == 'yes') ;
            }

            if ( ! $product_filter )
                return ;
            
            $array_social = array();
            if ( get_option( 'rs_global_show_hide_facebook_like_button' ) == '1' ) {
                $array_social[ 'fb_like' ] = "show" ;
            }
            if ( get_option( 'rs_global_show_hide_facebook_share_button' ) == '1' ) {
                $array_social[ 'fb_share' ] = "show" ;
            }
            if ( get_option( 'rs_global_show_hide_twitter_tweet_button' ) == '1' ) {
                $array_social[ 'twitter' ] = "show" ;
            }
            if ( get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) == '1' ) {
                if ( get_option( 'rs_global_social_twitter_profile_name' ) != '' ) {
                    $array_social[ 'twitter_follow' ] = "show" ;
                }
            }
            if ( get_option( 'rs_global_show_hide_google_plus_button' ) == '1' ) {
                $array_social[ 'google_share' ] = "show" ;
            }
            if ( get_option( 'rs_global_show_hide_vk_button' ) == '1' ) {
                $array_social[ 'vk_like' ] = "show" ;
            }
            if ( get_option( 'rs_global_show_hide_instagram_button' ) == '1' ) {
                if ( get_option( 'rs_instagram_profile_name' ) != '' ) {
                    $array_social[ 'instagram' ] = "show" ;
                }
            }
            if ( get_option( 'rs_global_show_hide_ok_button' ) == '1' ) {
                $array_social[ 'ok_share' ] = "show" ;
            }

            $OldData         = array(
                'fblike'    => get_user_meta( $UserId , '_rsfacebooklikes' , true ) ,
                'fbshare'   => get_user_meta( $UserId , '_rsfacebookshare' , true ) ,
                'tweet'     => get_user_meta( $UserId , '_rstwittertweet' , true ) ,
                'follow'    => get_user_meta( $UserId , '_rstwitterfollow' , true ) ,
                'okfollow'  => get_user_meta( $UserId , '_rsokfollow' , true ) ,
                'gplus'     => get_user_meta( $UserId , '_rsgoogleshares' , true ) ,
                'vklike'    => get_user_meta( $UserId , '_rsvklike' , true ) ,
                'instagram' => get_user_meta( $UserId , '_rsinstagram' , true )
                    ) ;
            wp_enqueue_script( 'fp_social_action' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-social-action-frontend.js" , array() , SRP_VERSION ) ;
            $LocalizedScript = self::localized_values_for_script( $post , 'product' , $OldData ) ;
            wp_localize_script( 'fp_social_action' , 'fp_social_action_params' , $LocalizedScript ) ;
            if ( get_option( 'rs_facebook_application_id' ) != '' && (get_option( 'rs_global_show_hide_facebook_like_button' ) == '1' || get_option( 'rs_global_show_hide_facebook_share_button' ) == '1') ) {
                ?>
                <div id="fb-root"></div>
                <?php
            }
            if ( get_option( 'rs_global_show_hide_google_plus_button' ) == '1' ) {
                ?>
                <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
                <?php
            }
            ?>                 
            <style type="text/css">
            <?php echo get_option( 'rs_social_custom_css' ) ; ?>
            </style>

            <?php if ( srp_check_is_array($array_social) && count( $array_social ) < 6 ) { ?>
                <table class="rs_social_sharing_buttons" style="display:<?php echo get_option( 'rs_social_button_position_troubleshoot' ) ; ?>">
                    <?php if ( get_option( 'rs_display_position_social_buttons' ) == '1' ) { ?>
                        <tr>
                            <?php
                            if ( get_option( 'rs_facebook_application_id' ) != '' ) {
                                if ( get_option( 'rs_global_show_hide_facebook_like_button' ) == '1' )
                                    self::fb_like_button() ;

                                if ( get_option( 'rs_global_show_hide_facebook_share_button' ) == '1' )
                                    self::fb_share_button() ;
                            }
                            if ( get_option( 'rs_global_show_hide_twitter_tweet_button' ) == '1' )
                                self::tweet_button() ;

                            if ( get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) == '1' && get_option( 'rs_global_social_twitter_profile_name' ) != '' )
                                self::twitter_follow_button() ;

                            if ( get_option( 'rs_global_show_hide_google_plus_button' ) == '1' )
                                self::gplus_share_button() ;

                            if ( get_option( 'rs_vk_application_id' ) != '' && get_option( 'rs_global_show_hide_vk_button' ) == '1' )
                                self::vk_like_button() ;

                            if ( get_option( 'rs_instagram_profile_name' ) != '' && get_option( 'rs_global_show_hide_instagram_button' ) == '1' )
                                self::instagram_follow_button() ;

                            if ( get_option( 'rs_global_show_hide_ok_button' ) == '1' )
                                self::ok_share_button() ;
                            ?>
                        </tr>
                        <?php
                    } else {
                        if ( get_option( 'rs_facebook_application_id' ) != '' ) {
                            if ( get_option( 'rs_global_show_hide_facebook_like_button' ) == '1' ) {
                                ?>
                                <tr>
                                    <?php self::fb_like_button() ; ?>
                                </tr>
                                <?php
                            }
                            if ( get_option( 'rs_global_show_hide_facebook_share_button' ) == '1' ) {
                                ?>
                                <tr>
                                    <?php self::fb_share_button() ; ?>
                                </tr>
                                <?php
                            }
                        }
                        if ( get_option( 'rs_global_show_hide_twitter_tweet_button' ) == '1' ) {
                            ?>
                            <tr>
                                <?php self::tweet_button() ; ?>
                            </tr>
                            <?php
                        }
                        if ( get_option( 'rs_global_social_twitter_profile_name' ) != '' && get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) == '1' ) {
                            ?>
                            <tr class="twitter_follow_btn">
                                <?php self::twitter_follow_button() ; ?>
                            </tr>
                            <?php
                        }
                        if ( get_option( 'rs_global_show_hide_google_plus_button' ) == '1' ) {
                            ?>
                            <tr>
                                <?php self::gplus_share_button() ; ?>
                            </tr>
                            <?php
                        }
                        if ( get_option( 'rs_vk_application_id' ) != '' && get_option( 'rs_global_show_hide_vk_button' ) == '1' ) {
                            ?>
                            <tr>
                                <?php self::vk_like_button() ; ?>
                            </tr>
                            <?php
                        }
                        if ( get_option( 'rs_instagram_profile_name' ) != '' && get_option( 'rs_global_show_hide_instagram_button' ) == '1' ) {
                            ?>
                            <tr>
                                <?php self::instagram_follow_button() ; ?>
                            </tr>
                            <?php
                        }
                        if ( get_option( 'rs_global_show_hide_ok_button' ) == '1' ) {
                            ?>
                            <tr>
                                <?php self::ok_share_button() ; ?>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
                <?php
            } else {
                if ( get_option( 'rs_display_position_social_buttons' ) == '1' ) {
                    ?>
                    <table class="rs_social_sharing_buttons" style="display:<?php echo get_option( 'rs_social_button_position_troubleshoot' ) ; ?>">
                        <tr>
                            <?php
                            if ( get_option( 'rs_facebook_application_id' ) != '' ) {
                                if ( get_option( 'rs_global_show_hide_facebook_like_button' ) == '1' )
                                    self::fb_like_button() ;

                                if ( get_option( 'rs_global_show_hide_facebook_share_button' ) == '1' )
                                    self::fb_share_button() ;
                            }
                            if ( get_option( 'rs_global_show_hide_twitter_tweet_button' ) == '1' )
                                self::tweet_button() ;

                            if ( get_option( 'rs_global_social_twitter_profile_name' ) != '' && get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) == '1' )
                                self::twitter_follow_button() ;

                            if ( get_option( 'rs_global_show_hide_google_plus_button' ) == '1' )
                                self::gplus_share_button() ;
                            ?>
                        </tr>
                    </table>
                    <table class="rs_social_sharing_buttons" style="display:<?php echo get_option( 'rs_social_button_position_troubleshoot' ) ; ?>">
                        <tr>
                            <?php
                            if ( get_option( 'rs_vk_application_id' ) != '' && get_option( 'rs_global_show_hide_vk_button' ) == '1' )
                                self::vk_like_button() ;

                            if ( get_option( 'rs_instagram_profile_name' ) != '' && get_option( 'rs_global_show_hide_instagram_button' ) == '1' )
                                self::instagram_follow_button() ;

                            if ( get_option( 'rs_global_show_hide_ok_button' ) == '1' )
                                self::ok_share_button() ;
                            ?>
                        </tr>
                    </table>
                    <?php
                } else {
                    ?>
                    <table class="rs_social_sharing_buttons" style="display:<?php echo get_option( 'rs_social_button_position_troubleshoot' ) ; ?>">
                        <?php
                        if ( get_option( 'rs_facebook_application_id' ) != '' ) {
                            if ( get_option( 'rs_global_show_hide_facebook_like_button' ) == '1' ) {
                                ?>
                                <tr>
                                    <?php self::fb_like_button() ; ?>
                                </tr>
                                <?php
                            }
                            if ( get_option( 'rs_global_show_hide_facebook_share_button' ) == '1' ) {
                                ?>
                                <tr>
                                    <?php self::fb_share_button() ; ?>
                                </tr>
                                <?php
                            }
                        }
                        if ( get_option( 'rs_global_show_hide_twitter_tweet_button' ) == '1' ) {
                            ?>
                            <tr>
                                <?php self::tweet_button() ; ?>
                            </tr>
                            <?php
                        }
                        if ( get_option( 'rs_global_social_twitter_profile_name' ) != '' && get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) == '1' ) {
                            ?>
                            <tr class="twitter_follow_btn">
                                <?php self::twitter_follow_button() ; ?>
                            </tr>
                            <?php
                        }
                        if ( get_option( 'rs_global_show_hide_google_plus_button' ) == '1' ) {
                            ?>
                            <tr>
                                <?php self::gplus_share_button() ; ?>
                            </tr>
                            <?php
                        }
                        if ( get_option( 'rs_vk_application_id' ) != '' && get_option( 'rs_global_show_hide_vk_button' ) == '1' ) {
                            ?>
                            <tr>
                                <?php self::vk_like_button() ; ?>
                            </tr>
                            <?php
                        }
                        if ( get_option( 'rs_instagram_profile_name' ) != '' && get_option( 'rs_global_show_hide_instagram_button' ) == '1' ) {
                            ?>
                            <tr>
                                <?php self::instagram_follow_button() ; ?>
                            </tr>
                            <?php
                        }
                        if ( get_option( 'rs_global_show_hide_ok_button' ) == '1' ) {
                            ?>
                            <tr>
                                <?php self::ok_share_button() ; ?>
                            </tr>
                        <?php } ?>
                    </table>
                    <?php
                }
            }
            ?>
            <div class="social_promotion_success_message"></div>
            <?php
        }

        public static function fb_like_button() {
            $custom_url = get_option( 'rs_global_social_facebook_url_custom' ) ;
            if ( get_option( 'rs_social_button_like' ) == 1 ) {
                ?>
                <td>
                    <div class="fb-like" data-size="<?php echo get_option( 'rs_facebook_like_icon_size' ) ; ?>" data-href="<?php echo get_option( 'rs_global_social_facebook_url' ) == '1' ? get_permalink() : $custom_url ; ?>" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div>
                </td>
                <?php
            } else {
                $fb_like_url = '1' == get_option( 'rs_global_social_facebook_url' , '1' ) ? 'http://www.facebook.com/login.php' : ('' != $custom_url ? $custom_url : 'http://www.facebook.com/login.php') ;
                ?>
                <td>
                    <a class="rs_custom_social_icon_a fb_like_a" href="<?php echo esc_url( $fb_like_url ) ; ?>" onClick = "window.open( this.href , 'like' , 'toolbar=0,status=0,width=580,height=325' ) ;return false ;"><input type="button" value="<?php _e( 'FB Like' , SRP_LOCALE ) ; ?>" class="rs_custom_fblike_button"/></a>
                </td>
                <?php
            }
        }

        public static function fb_share_button() {
            $Classname = is_product() ? 'share_wrapper1' : 'share_wrapper11' ;
            if ( get_option( 'rs_social_button_share' ) == 1 ) {
                ?>
                <td>
                    <div class="<?php echo $Classname ; ?>">
                        <img class='fb_share_img' src="<?php echo SRP_PLUGIN_URL ; ?>/assets/images/icon1.png"> <span class="label"><?php echo get_option( 'rs_fbshare_button_label' ) ; ?> </span>
                    </div>
                </td>
                <?php
            } else {
                ?>
                <td>
                    <a class="rs_custom_social_icon_a" href="http://www.facebook.com/sharer.php?s=100&u=<?php echo get_option( 'rs_global_social_facebook_share_url' ) == '1' ? get_permalink() : get_option( 'rs_global_social_facebook_share_url_custom' ) ; ?>" onClick = "window.open( this.href , 'sharer' , 'toolbar=0,status=0,width=580,height=325' ) ;return false ;">
                        <input type="button" value="<?php _e( 'FB Share' , SRP_LOCALE ) ; ?>" class="rs_custom_fbshare_button"/>
                    </a>
                </td>
                <?php
            }
        }

        public static function tweet_button() {
            if ( get_option( 'rs_social_button_tweet' ) == 1 ) {
                ?>
                <td>
                    <div class="rstwitter-button-msg">
                        <a href="https://twitter.com/share" class="twitter-share-button" style="width:88px !important;" id="twitter-share-button" data-url="<?php echo get_option( 'rs_global_social_twitter_url' ) == '1' ? get_permalink() : get_option( 'rs_global_social_twitter_url_custom' ) ; ?>"></a>
                    </div>
                </td>
                <?php
            } else {
                ?>
                <td>
                    <a class="rs_custom_social_icon_a" href="http://twitter.com/share?url=<?php echo get_option( 'rs_global_social_twitter_url' ) == '1' ? get_permalink() : get_option( 'rs_global_social_twitter_url_custom' ) ; ?>" target="_blank" onClick = "javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600' ) ;return false ;"><input type="button" value="<?php _e( 'Tweet' , SRP_LOCALE ) ; ?>" class="rs_custom_tweet_button"/></a>
                </td>
                <?php
            }
        }

        public static function twitter_follow_button() {
            if ( get_option( 'rs_social_button_twitter_follow' ) == 1 ) {
                ?>
                <td>
                    <div class="rstwitterfollow-button-msg">
                        <a href='https://twitter.com/<?php echo get_option( 'rs_global_social_twitter_profile_name' ) ; ?>'   class="twitter-follow-button" data-show-count="false"><?php _e( 'Follow @twitter' , SRP_LOCALE ) ; ?></a>
                    </div>
                </td>
                <?php
            } else {
                ?>
                <td>
                    <a class="rs_custom_social_icon_a" href='https://twitter.com/<?php echo get_option( 'rs_global_social_twitter_profile_name' ) ; ?>' target="_blank" onClick = "javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600' ) ;return false ;"><input type="button" value="<?php _e( 'Follow@Twitter' , SRP_LOCALE ) ; ?>" class="rs_custom_tweetfollow_button"/></a>
                </td>
                <?php
            }
        }

        public static function instagram_follow_button() {
            $Classname = is_product() ? 'instagram_button' : 'instagram_button_post' ;
            if ( get_option( 'rs_social_button_instagram' ) == 1 ) {
                ?>
                <td>
                    <div class ="<?php echo $Classname ; ?>">
                        <a href="https://www.instagram.com/<?php echo get_option( 'rs_instagram_profile_name' ) ; ?>/?ref=badge" class="ig-b- ig-b-32" target="_blank"><img src="//badges.instagram.com/static/images/ig-badge-32.png" alt="Instagram" /></a>
                    </div>
                </td>
                <?php
            } else {
                ?>
                <td>
                    <a class="rs_custom_social_icon_a" href="https://www.instagram.com/<?php echo get_option( 'rs_instagram_profile_name' ) ; ?>/?ref=badge" target="_blank" onClick = "javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600' ) ;return false ;"><input type="button" value="<?php _e( 'Instagram' , SRP_LOCALE ) ; ?>" class="rs_custom_instagram_button"/></a>
                </td>
                <?php
            }
        }

        public static function vk_like_button() {
            if ( get_option( 'rs_social_button_vk_like' ) == 1 ) {
                ?>
                <td>
                    <div id="vk_like" class='vk-like' style="width:88px !important;"></div>
                </td>
                <?php
            } else {
                ?>
                <td>
                    <a class="rs_custom_social_icon_a" href="https://www.vk.com/"  target="_blank" onClick = "javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600' ) ;return false ;"><input type="button" id="vk_like" value="<?php _e( 'VK Like' , SRP_LOCALE ) ; ?>" class="rs_custom_vklike_button"/></a>
                </td>
                <?php
            }
        }

        public static function gplus_share_button() {
            $url = get_option( 'rs_global_social_google_url' ) == '1' ? get_permalink() : get_option( 'rs_global_social_google_url_custom' ) ;
            if ( get_option( 'rs_social_button_gplus' ) == 1 ) {
                ?>
                <td>
                    <div id="google-plus-one"> 
                        <a href="https://plus.google.com/share?url=<?php echo $url ; ?>" id='google-plus-one' class="google-plus-one fp_gplus_share" target='_blank' onclick="javascript:window.open( this.href ,
                                                        '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600' ) ;
                                                return false ;"><img src="https://www.gstatic.com/images/icons/gplus-32.png" alt="Share on Google+"/></a>
                    </div>
                </td>
                <?php
            } else {
                ?>
                <td>
                    <a class="rs_custom_social_icon_a" href="https://plus.google.com/share?url=<?php echo $url ; ?>" target='_blank' onclick="javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600' ) ;
                                            return false ;"><input type="button" value="<?php _e( 'G PLus' , SRP_LOCALE ) ; ?>" class="rs_custom_gplus_button"/></a>
                </td>
                <?php
            }
        }

        public static function ok_share_button() {
            if ( get_option( 'rs_social_button_ok_ru' ) == 1 ) {
                ?>
                <td>
                    <div class="ok-share-button" id="ok_shareWidget" style="width:30px;">
                        <a href="https://ok.ru/" class="ok-share-button" id="ok-share-button" data-url="<?php echo get_option( 'rs_global_social_ok_url' ) == '1' ? get_permalink() : get_option( 'rs_global_social_ok_url_custom' ) ; ?>"></a>
                    </div>
                </td>
                <?php
            } else {
                ?>
                <td>
                    <a class="rs_custom_social_icon_a" href="https://ok.ru/" target='_blank' onclick="javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600' ) ;
                                            return false ;"><input type="button" value="<?php _e( 'OK.ru' , SRP_LOCALE ) ; ?>" class="rs_custom_ok_button"/></a>
                </td>
                <?php
            }
        }

    }

    RSFunctionForSocialRewards::init() ;
}