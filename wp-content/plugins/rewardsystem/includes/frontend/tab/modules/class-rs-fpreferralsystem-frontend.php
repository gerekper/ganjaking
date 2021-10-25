<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'RSFunctionForReferralSystem' ) ) {

    class RSFunctionForReferralSystem {

        public static function init() {
            if ( get_option( 'rs_reward_content' ) == 'yes' ) {
                add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'referral_list_table_in_my_account' ) ) ;
            }

            add_action( 'wp_head' , array( __CLASS__ , 'set_cookie_for_referral' ) ) ;

            add_action( 'wp_head' , array( __CLASS__ , 'unset_cookie_based_on_referral_registration_date' ) ) ;

            add_action( 'wp_head' , array( __CLASS__ , 'link_referral_for_lifetime' ) ) ;

            add_action( 'user_register' , array( __CLASS__ , 'award_points_for_referral_account_signup' ) , 10 , 1 ) ;

            if ( get_option( 'rs_display_generate_referral' ) == '2' ) {
                if ( get_option( 'rs_show_hide_generate_referral_link_type' ) == '1' ) {
                    add_action( 'woocommerce_after_my_account' , array( __CLASS__ , 'list_of_generated_link_and_field_in_myaccount' ) ) ;
                } else {
                    add_action( 'woocommerce_after_my_account' , array( __CLASS__ , 'static_referral_link_in_my_account' ) ) ;
                }
            } else {
                if ( get_option( 'rs_show_hide_generate_referral_link_type' ) == '1' ) {
                    add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'list_of_generated_link_and_field_in_myaccount' ) ) ;
                } else {
                    add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'static_referral_link_in_my_account' ) ) ;
                }
            }

            if ( get_option( 'rs_troubleshoot_referral_link_landing_page' ) == '1' ) {
                add_action( 'wp' , array( __CLASS__ , 'referrer_name' ) ) ;
            } else {
                add_action( 'wp_head' , array( __CLASS__ , 'referrer_name' ) ) ;
            }

            if ( get_option( 'rs_message_before_after_cart_table' ) == '1' ) {
                if ( get_option( 'rs_reward_point_troubleshoot_before_cart' ) == '1' ) {
                    add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'message_for_referral_product_purchase' ) ) ;
                } else {
                    add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'message_for_referral_product_purchase' ) ) ;
                }
            } else {
                add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'message_for_referral_product_purchase' ) ) ;
            }
            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'message_for_referral_product_purchase' ) ) ;

            add_action( 'woocommerce_removed_coupon' , array( __CLASS__ , 'message_for_referral_product_purchase' ) , 10 , 1 ) ;
        }

        /* Display Referral List in My Account */

        public static function referral_list_table_in_my_account() {
            $TableData = array(
                'show_table'           => get_option( 'rs_show_hide_referal_table' ) ,
                'sno_label'            => get_option( 'rs_my_referal_sno_label' ) ,
                'userid_or_email'      => get_option( 'rs_select_option_for_referral' ) ,
                'userid_label'         => get_option( 'rs_my_referal_userid_label' ) ,
                'email_id'             => get_option( 'rs_referral_email_ids' ) ,
                'total_referral_label' => get_option( 'rs_my_total_referal_points_label' ) ,
                'title_table'          => get_option( 'rs_referal_table_title' ) ,
                    ) ;

            echo self::referral_list_table( $TableData ) ;
        }

        /* Display Referral List in Menu */

        public static function referral_list_table_in_menu() {
            $TableData = array(
                'show_table'           => get_option( 'rs_show_hide_referal_table_menu_page' ) ,
                'sno_label'            => get_option( 'rs_my_referal_sno_label' ) ,
                'userid_or_email'      => get_option( 'rs_select_option_for_referral' ) ,
                'userid_label'         => get_option( 'rs_my_referal_userid_label' ) ,
                'email_id'             => get_option( 'rs_referral_email_ids' ) ,
                'total_referral_label' => get_option( 'rs_my_total_referal_points_label' ) ,
                'title_table'          => get_option( 'rs_referal_table_title' ) ,
                    ) ;
            echo self::referral_list_table( $TableData ) ;
        }

        /* HTML Elements of Referral List Table */

        public static function referral_list_table( $TableData , $echo = false ) {
            if ( ! is_user_logged_in() )
                return ;

            $UserId  = get_current_user_id() ;
            $BanType = check_banning_type( $UserId ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( ! check_if_referral_is_restricted() )
                return ;

            if ( ! check_if_referral_is_restricted_based_on_history() )
                return ;

            if ( $TableData[ 'show_table' ] == 2 )
                return ;

            ob_start() ;
            ?>
            <h2 class=rs_my_referral_table><?php echo $TableData[ 'title_table' ] ; ?></h2>
            <table class = "referrallog demo shop_table my_account_referal table-bordered"  data-page-size="5" data-page-previous-text = "prev" >
                <thead>
                    <tr>
                        <th><?php echo $TableData[ 'sno_label' ] ; ?></th>
                        <th><?php echo (isset( $TableData[ 'userid_or_email' ] ) && $TableData[ 'userid_or_email' ] == '1' ) ? $TableData[ 'userid_label' ] : $TableData[ 'email_id' ] ; ?></th>
                        <th><?php echo $TableData[ 'total_referral_label' ] ; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ReferralLog = RS_Referral_Log::corresponding_referral_log( get_current_user_id() ) ;
                    if ( srp_check_is_array( $ReferralLog ) ) {
                        if ( get_option( 'rs_points_log_sorting' ) == '1' )
                            krsort( $ReferralLog , SORT_NUMERIC ) ;

                        $i = 1 ;
                        foreach ( $ReferralLog as $Key => $values ) {
                            $UserInfo = get_user_by( 'id' , $Key ) ;
                            if ( ! is_object( $UserInfo ) )
                                continue ;
                            ?>
                            <tr>
                                <td data-value="<?php echo $i ; ?>"><?php echo $i ; ?></td>
                                <td><?php echo (isset( $TableData[ 'userid_or_email' ] ) && $TableData[ 'userid_or_email' ] == '1') ? $UserInfo->user_login : $UserInfo->user_email ; ?></td>
                                <td><?php echo RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $values ) ; ?></td>
                            </tr>
                            <?php
                            $i ++ ;
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr style="clear:both;">
                        <td colspan="7">
                            <div class="pagination pagination-centered"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php
            $content = ob_get_contents() ;
            ob_end_clean() ;
            if ( ! $echo )
                return $content ;

            echo $content ;
        }

        /* Display the field to generate link and list of generated link in both Menu and My Account */

        public static function list_of_generated_link_and_field() {
            if ( ! check_if_referral_is_restricted() )
                return ;

            if ( is_user_logged_in() ) {
                $UserId  = get_current_user_id() ;
                $BanType = check_banning_type( $UserId ) ;
                if ( $BanType == 'earningonly' || $BanType == 'both' )
                    return ;

                if ( ! check_referral_count_if_exist( get_current_user_id() ) ) {
                    _e( "<p>Since you have reached the referral link usage, you don't have the access to refer anymore</p>" , SRP_LOCALE ) ;
                } else {
                    ob_start() ;
                    self::field_to_generate_referral_link() ;
                    self::list_of_generated_link() ;
                    $content = ob_get_contents() ;
                    ob_end_clean() ;
                    return $content ;
                }
            } else {
                _e( 'Please Login to View the Content of  this Page <a href=' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '> Login </a>' , SRP_LOCALE ) ;
            }
        }

        /* Display the field to generate link and list of generated link in My Account */

        public static function list_of_generated_link_and_field_in_myaccount() {
            if ( get_option( 'rs_reward_content' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_generate_referral' ) == '2' )
                return ;

            if ( ! check_if_referral_is_restricted_based_on_history() )
                return ;

            echo self::list_of_generated_link_and_field() ;
        }

        /* Display the Static Referral link in My Account */

        public static function static_referral_link_in_my_account() {
            if ( get_option( 'rs_reward_content' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_generate_referral' ) == '2' )
                return ;

            if ( ! check_if_referral_is_restricted_based_on_history() )
                return ;

            echo self::static_referral_link() ;
        }

        /* Display the Static Referral link in both Menu and My Account */

        public static function static_referral_link() {
            if ( ! is_user_logged_in() )
                return ;

            $UserId  = get_current_user_id() ;
            $BanType = check_banning_type( $UserId ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( ! check_if_referral_is_restricted() )
                return ;

            if ( ! check_if_referral_is_restricted_based_on_history() )
                return ;

            if ( ! check_referral_count_if_exist( $UserId ) ) {
                _e( "<p>Since you have reached the referral link usage, you don't have the access to refer anymore</p>" , SRP_LOCALE ) ;
            } else {
                ob_start() ;
                self::static_url() ;
                $content = ob_get_contents() ;
                ob_end_clean() ;
                return $content ;
            }
        }

        /* Display the input field and button for Generate Referral Link */

        public static function field_to_generate_referral_link() {
            ?>
            <div class="referral_field1" style="margin-top:10px;">
                <input type="text" 
                       size="50" 
                       name="generate_referral_field" 
                       id="generate_referral_field" 
                       required="required" 
                       value="<?php echo esc_url( get_option( 'rs_prefill_generate_link' ) ) ; ?>">

                <input type="submit"  
                       title="<?php echo esc_attr( get_option( 'rs_generate_link_hover_label' , 'Click this button to generate the referral link' ) ) ; ?>" 
                       style="margin-left:10px;" 
                       class="button <?php echo esc_attr( get_option( 'rs_extra_class_name_generate_referral_link' ) ) ; ?>"
                       name="refgeneratenow" 
                       id="refgeneratenow" 
                       value="<?php echo wp_kses_post( get_option( 'rs_generate_link_button_label' ) ) ; ?>"/>

            </div>                
            <?php
        }

        /* Display the list of generated link */

        public static function list_of_generated_link() {
            wp_enqueue_script( 'fp_referral_frontend' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-referral-frontend.js" , array( 'jquery' ) , SRP_VERSION ) ;
            $LocalizedScript = array(
                'ajaxurl'          => SRP_ADMIN_AJAX_URL ,
                'buttonlanguage'   => get_option( 'rs_language_selection_for_button' ) ,
                'wplanguage'       => get_option( 'WPLANG' ) ,
                'fbappid'          => get_option( 'rs_facebook_application_id' ) ,
                'enqueue_footable' => get_option( 'rs_enable_footable_js' , '1' ) ,
                    ) ;
            wp_localize_script( 'fp_referral_frontend' , 'fp_referral_frontend_params' , $LocalizedScript ) ;
            ?>
            <h3  class=rs_my_referral_link_title><?php echo get_option( 'rs_generate_link_label' ) ; ?></h3>
            <table class="referral_link shop_table my_account_referral_link" id="my_account_referral_link">
                <thead>
                    <tr>
                        <th class="referral-number"><span class="nobr"><?php echo get_option( 'rs_generate_link_sno_label' ) ; ?></span></th>
                        <th class="referral-date"><span class="nobr"><?php echo get_option( 'rs_generate_link_date_label' ) ; ?></span></th>
                        <th class="referral-link"><span class="nobr"><?php echo get_option( 'rs_generate_link_referrallink_label' ) ; ?></span></th>
                        <th data-hide='phone,tablet' class="referral-social"><span class="nobr"><?php echo get_option( 'rs_generate_link_social_label' ) ; ?></span></th>
                        <th data-hide='phone,tablet' class="referral-actions"><span class="nobr"><?php echo get_option( 'rs_generate_link_action_label' ) ; ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $UserId          = get_current_user_id() ;
                    if ( srp_check_is_array( get_option( 'arrayref' . $UserId ) ) ) {
                        $i = 1 ;
                        foreach ( get_option( 'arrayref' . $UserId ) as $key => $array ) {
                            $mainkey = explode( ',' , $array ) ;
                            ?>
                            <tr class="referrals" data-url="<?php echo $mainkey[ 0 ] ; ?>">
                                <td><?php echo $i ; ?></td>
                                <td><?php echo $mainkey[ 1 ] ; ?></td>
                                <td class="copy_clip_icon">
                                    <?php if ( get_option( 'rs_enable_copy_to_clipboard' ) == 'yes' ) { ?>
                                        <img data-referralurl="<?php echo $mainkey[ 0 ] ; ?>" title="<?php _e( 'Click to copy the link' , SRP_LOCALE ) ; ?>" alt="<?php _e( 'Click to copy the link' , SRP_LOCALE ) ; ?>" src="<?php echo SRP_PLUGIN_URL ; ?>/assets/images/copy_link.png" id="rs_copy_clipboard_image" class="rs_copy_clipboard_image"/>
                                        <div style="display:none;"class="rs_alert_div_for_copy">
                                            <div class="rs_alert_div_for_copy_content">
                                                <p><?php _e( 'Referral Link Copied' , SRP_LOCALE ) ; ?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php echo $mainkey[ 0 ] ; ?>  
                                </td>
                                <td>
                                    <div class="rs_social_buttons">      
                                        <?php
                                        if ( '1' == get_option( 'rs_account_show_hide_facebook_share_button' ) ) {
                                            ?>
                                            <div class="share_wrapper_default_url" id="share_wrapper_default_url" href="<?php echo $mainkey[ 0 ] ; ?>" data-image="<?php echo get_option( 'rs_fbshare_image_url_upload' ) ?>" data-title="<?php echo get_option( 'rs_facebook_title' ) ?>" data-description="<?php echo get_option( 'rs_facebook_description' ) ?>">
                                                <img class='fb_share_img' src="<?php echo SRP_PLUGIN_URL ; ?>/assets/images/icon1.png"> <span class="label"><?php echo get_option( 'rs_fbshare_button_label' ) ; ?> </span>
                                            </div> 
                                            <?php
                                        }
                                        if ( '1' == get_option( 'rs_account_show_hide_twitter_tweet_button' ) ) {
                                            ?>
                                            <a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-url="<?php echo $mainkey[ 0 ] ; ?>">Tweet</a><br>
                                            <?php
                                        }
                                        if ( '1' == get_option( 'rs_acount_show_hide_google_plus_button' ) ) {
                                            ?>
                                            <div class="g-plusone" data-action="share" data-annotation="none" data-href="<?php echo $mainkey[ 0 ] ; ?>"><g:plusone></g:plusone></div>
                                            <?php
                                        }
                                        if ( '1' == get_option( 'rs_acount_show_hide_whatsapp_button' , '1' ) ) {
                                            $key = isset( $mainkey[ 0 ] ) ? $mainkey[ 0 ] : '' ;
                                            ?>                            
                                            <a class="rs-whatsapp-share-button" href="<?php echo esc_url( "https://web.whatsapp.com://send?text=$key" ) ; ?>" target="_blank">
                                                <img class='whatsapp_share_img' src="<?php echo SRP_PLUGIN_URL ; ?>/assets/images/whatsapp-icon.png"> <span class="rs_whatsapp_label"><?php esc_html_e( 'Share' , SRP_LOCALE ) ; ?> </span>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td><span data-array="<?php echo $key ; ?>" class="referralclick">x</span></td>
                            </tr>
                            <?php
                            $i ++ ;
                        }
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }

        public static function static_url() {
            $UserId         = get_current_user_id() ;
            $UserInfo       = get_userdata( $UserId ) ;
            $referralperson = (get_option( 'rs_generate_referral_link_based_on_user' ) == '1') ? $UserInfo->user_login : $UserId ;
            if ( is_account_page() ) {
                if ( get_option( 'rs_show_hide_generate_referral_link_type' ) == '2' )
                    self::static_url_table( $referralperson ) ;
            } else {
                if ( get_option( '_rs_static_referral_link' ) == '1' )
                    self::static_url_table( $referralperson ) ;
            }
        }

        /* HTML Element for Static URL */

        public static function static_url_table( $referralperson ) {
            wp_enqueue_script( 'fp_referral_frontend' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/modules/fp-referral-frontend.js" , array( 'jquery' ) , SRP_VERSION ) ;
            $LocalizedScript = array(
                'ajaxurl'          => SRP_ADMIN_AJAX_URL ,
                'buttonlanguage'   => get_option( 'rs_language_selection_for_button' ) ,
                'wplanguage'       => get_option( 'WPLANG' ) ,
                'fbappid'          => get_option( 'rs_facebook_application_id' ) ,
                'enqueue_footable' => get_option( 'rs_enable_footable_js' , '1' ) ,
                    ) ;
            wp_localize_script( 'fp_referral_frontend' , 'fp_referral_frontend_params' , $LocalizedScript ) ;
            $query           = (get_option( 'rs_restrict_referral_points_for_same_ip' ) == 'yes') ? array( 'ref' => $referralperson , 'ip' => base64_encode( get_referrer_ip_address() ) ) : array( 'ref' => $referralperson ) ;
            $refurl          = add_query_arg( $query , get_option( 'rs_static_generate_link' ) ) ;
            ?>
            <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
            <h3 class=rs_my_referral_link_title><?php echo get_option( 'rs_my_referral_link_button_label' ) ?></h3>
            <table class="shop_table my_account_referral_link_static" id="my_account_referral_link_static">
                <thead>
                    <tr>
                        <th class="referral-number_static"><span class="nobr"><?php echo get_option( 'rs_generate_link_sno_label' ) ; ?></span></th>                        
                        <th class="referral-link_static"><span class="nobr"><?php echo get_option( 'rs_generate_link_referrallink_label' ) ; ?></span></th>
                        <th class="referral-social_static"><span class="nobr"><?php echo get_option( 'rs_generate_link_social_label' ) ; ?></span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="referrals_static">
                        <td><?php echo 1 ; ?></td>
                        <td class="copy_clip_icon">
                            <?php echo $refurl ; ?>
                            <?php if ( get_option( 'rs_enable_copy_to_clipboard' ) == 'yes' ) { ?>
                                <img data-referralurl="<?php echo $refurl ; ?>" title="<?php _e( 'Click to copy the link' , SRP_LOCALE ) ; ?>" alt="<?php _e( 'Click to copy the link' , SRP_LOCALE ) ; ?>" src="<?php echo SRP_PLUGIN_URL ; ?>/assets/images/copy_link.png" id="rs_copy_clipboard_image" class="rs_copy_clipboard_image"/>
                                <div style="display:none;"class="rs_alert_div_for_copy">
                                    <div class="rs_alert_div_for_copy_content">
                                        <p><?php _e( 'Referral Link Copied' , SRP_LOCALE ) ; ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ( get_option( 'rs_account_show_hide_facebook_share_button' ) == '1' ) { ?>
                                <div class="share_wrapper_static_url" id="share_wrapper_static_url" href="<?php echo $refurl ; ?>" data-image="<?php echo get_option( 'rs_fbshare_image_url_upload' ) ?>" data-title="<?php echo get_option( 'rs_facebook_title' ) ?>" data-description="<?php echo get_option( 'rs_facebook_description' ) ?>">
                                    <img class='fb_share_img' src="<?php echo SRP_PLUGIN_URL ; ?>/assets/images/icon1.png"> <span class="label"><?php echo get_option( 'rs_fbshare_button_label' ) ; ?> </span>
                                </div>
                            <?php } ?>
                            <?php if ( get_option( 'rs_account_show_hide_twitter_tweet_button' ) == '1' ) { ?>
                                <a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-url="<?php echo $refurl ; ?>">Tweet</a>
                            <?php } ?><br>
                            <?php if ( get_option( 'rs_acount_show_hide_google_plus_button' ) == '1' ) { ?>
                                <div class="g-plusone" data-action="share" data-annotation="none" data-href="<?php echo $refurl ; ?>"><g:plusone></g:plusone></div>
                            <?php } ?>
                            <?php if ( get_option( 'rs_acount_show_hide_whatsapp_button' , '1' ) == '1' ) { ?>
                                <a class="rs-whatsapp-share-button" href="<?php echo esc_url( "https://web.whatsapp.com://send?text=$refurl" ) ; ?>">
                                    <img class='whatsapp_share_img' src="<?php echo SRP_PLUGIN_URL ; ?>/assets/images/whatsapp-icon.png"> <span class="rs_whatsapp_label"><?php esc_html_e( 'Share' , SRP_LOCALE ) ; ?> </span>
                                </a>
                            <?php } ?> 
                        </td>
                    </tr>                    
                </tbody>
            </table>
            <?php
        }

        public static function check_limit_for_referral_link() {
            if ( ! isset( $_GET[ 'ref' ] ) )
                return true ;

            $UserInfo = get_user_by( 'login' , $_GET[ 'ref' ] ) ;
            $RefId    = is_object( $UserInfo ) ? $UserInfo->ID : $_GET[ 'ref' ] ;
            if ( get_current_user_id() == $RefId )
                return true ;

            if ( check_referral_count_if_exist( $RefId ) )
                return true ;

            setcookie( 'rsreferredusername' , null , -1 , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
            setcookie( 'referrerip' , null , -1 , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
            return false ;
        }

        /* Set Cookie */

        public static function set_cookie_for_referral() {
            if ( ! check_if_referral_is_restricted() )
                return ;

            if ( isset( $_GET[ 'ref' ] ) && ! is_user_logged_in() && self::check_limit_for_referral_link() ) {
                if ( get_option( 'rs_referral_cookies_expiry' ) == '1' ) {
                    $min = get_option( 'rs_referral_cookies_expiry_in_min' ) == '' ? '1' : get_option( 'rs_referral_cookies_expiry_in_min' ) ;
                    setcookie( 'rsreferredusername' , $_GET[ 'ref' ] , time() + 60 * $min , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
                    if ( isset( $_GET[ 'ip' ] ) )
                        setcookie( 'referrerip' , $_GET[ 'ip' ] , time() + 60 * $min , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
                } elseif ( get_option( 'rs_referral_cookies_expiry' ) == '2' ) {
                    $hour = get_option( 'rs_referral_cookies_expiry_in_hours' ) == '' ? '1' : get_option( 'rs_referral_cookies_expiry_in_hours' ) ;
                    setcookie( 'rsreferredusername' , $_GET[ 'ref' ] , time() + 60 * 60 * $hour , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
                    if ( isset( $_GET[ 'ip' ] ) )
                        setcookie( 'referrerip' , $_GET[ 'ip' ] , time() + 60 * 60 * $hour , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
                } else {
                    $day = get_option( 'rs_referral_cookies_expiry_in_days' ) == '' ? '1' : get_option( 'rs_referral_cookies_expiry_in_days' ) ;
                    setcookie( 'rsreferredusername' , $_GET[ 'ref' ] , time() + 60 * 60 * 24 * $day , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
                    if ( isset( $_GET[ 'ip' ] ) )
                        setcookie( 'referrerip' , $_GET[ 'ip' ] , time() + 60 * 60 * 24 * $day , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
                }
                $UserInfo = get_user_by( 'login' , $_GET[ 'ref' ] ) ;
                $UserId   = is_object( $UserInfo ) ? $UserInfo->ID : $_GET[ 'ref' ] ;
                if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
                    $previouscount = get_user_meta( $UserId , 'rsreferredusernameclickthrough' , true ) ;
                    update_user_meta( $UserId , 'rsreferredusernameclickthrough' , ( float ) $previouscount + 1 ) ;
                }
            }
        }

        /*
         * Unset Cookie based on referral registration date.
         * 
         * @return void. 
         */

        public static function unset_cookie_based_on_referral_registration_date() {

            if ( ! check_if_referral_is_restricted() ) {
                return ;
            }

            if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) || ! is_user_logged_in() || ! self::check_limit_for_referral_link() ) {
                return ;
            }

            // Referrer user object.
            $referrer_user = ('1' == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login' , $_COOKIE[ 'rsreferredusername' ] ) : get_user_by( 'ID' , $_COOKIE[ 'rsreferredusername' ] ) ;
            if ( ! is_object( $referrer_user ) || ! $referrer_user->exists() ) {
                return ;
            }

            // Referred user object.
            $referred_user = get_user_by( 'ID' , get_current_user_id() ) ;
            if ( ! is_object( $referred_user ) || ! $referred_user->exists() ) {
                return ;
            }

            $referrer_registered_date = ! empty( $referrer_user->user_registered ) ? strtotime( $referrer_user->user_registered ) : 0 ;
            $referred_registered_date = ! empty( $referred_user->user_registered ) ? strtotime( $referred_user->user_registered ) : 0 ;
            // Return if referrer registered date less than referred registered date.
            if ( ! $referrer_registered_date || ! $referred_registered_date || $referrer_registered_date < $referred_registered_date ) {
                return ;
            }

            // Unset cookie.
            setcookie( 'rsreferredusername' , null , -1 , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
            wc_add_notice( esc_html__( 'You cannot use this referral link.' , SRP_LOCALE ) , 'error' ) ;
        }

        public static function referrer_name() {
            if ( get_option( 'rs_show_hide_generate_referral_message' ) == 2 )
                return ;

            if ( is_user_logged_in() )
                return ;

            if ( ! isset( $_GET[ 'ref' ] ) )
                return ;

            if ( ! check_if_referral_is_restricted() )
                return ;

            if ( get_option( 'rs_enable_get_header' ) == '1' )
                get_header() ;
            ?>
            <div class="referral_field" style="margin-top:40px;">
                <h4 style="text-align:center;"><?php echo do_shortcode( get_option( 'rs_show_hide_generate_referral_message_text' ) ) ; ?></h4>
            </div>
            <?php
        }

        /* Link Referral for Lifetime */

        public static function link_referral_for_lifetime() {
            if ( ! is_user_logged_in() )
                return ;

            if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) )
                return ;

            if ( get_option( 'rs_enable_referral_link_for_life_time' ) != 'yes' )
                return ;

            $UserId  = get_current_user_id() ;
            $BanType = check_banning_type( $UserId ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( get_post_meta( $UserId , 'reward_manuall_referral_link' , true ) == 'yes' )
                return ;

            $RefUserName       = get_option( 'rs_generate_referral_link_based_on_user' ) == '1' ? get_user_by( 'login' , $_COOKIE[ 'rsreferredusername' ] ) : get_userdata( $_COOKIE[ 'rsreferredusername' ] ) ;
            $RefUserId         = $RefUserName->ID ;
            $ManualRefLinkRule = get_option( 'rewards_dynamic_rule_manual' ) ;
            if ( $UserId == $RefUserId )
                return ;

            if ( srp_check_is_array( $ManualRefLinkRule ) ) {
                $boolvalue = self::check_if_user_and_referrer_are_same( $ManualRefLinkRule , $RefUserId , $UserId ) ;
                if ( $boolvalue ) {
                    $merge[]  = array( 'referer' => esc_html( $RefUserName->ID ) , 'refferal' => esc_html( $UserId ) , 'type' => 'Automatic' ) ;
                    $logmerge = array_merge( ( array ) $ManualRefLinkRule , $merge ) ;
                    update_option( 'rewards_dynamic_rule_manual' , $logmerge ) ;
                }
            } else {
                $merge[] = array( 'referer' => esc_html( $RefUserName->ID ) , 'refferal' => esc_html( $UserId ) , 'type' => 'Automatic' ) ;
                update_option( 'rewards_dynamic_rule_manual' , $merge ) ;
            }
            update_post_meta( $UserId , 'reward_manuall_referral_link' , 'yes' ) ;
        }

        public static function check_if_user_and_referrer_are_same( $ManualRefLinkRule , $RefUserId , $UserId ) {

            foreach ( $ManualRefLinkRule as $EachRule ) {
                if ( ($EachRule[ 'referer' ] == $RefUserId) && ($EachRule[ 'refferal' ] == $UserId) ) {
                    if ( $EachRule[ 'referer' ] == $UserId ) {
                        return false ;
                    }
                }
            }

            return true ;
        }

        public static function message_for_referral_product_purchase() {
            if ( ! is_user_logged_in() && get_option( 'rs_referrer_earn_point_purchase_by_guest_users' ) != 'yes' )
                return ;

            $ShowReferralMsg = is_cart() ? get_option( 'rs_show_hide_message_for_total_points_referrel' ) : get_option( 'rs_show_hide_message_for_total_points_referrel_checkout' ) ;
            echo self::referral_product_purchase_msg_for_payment_plan_product() ;
            echo self::referral_product_purchase_msg_for_each_product( $ShowReferralMsg ) ;
        }

        /* Display Referral Product Purchase message in Cart for SUMO Payment Plan */

        public static function referral_product_purchase_msg_for_payment_plan_product() {
            if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
                $refuser = (get_option( 'rs_generate_referral_link_based_on_user' ) == 1 ) ? get_user_by( 'login' , $_COOKIE[ 'rsreferredusername' ] ) : get_user_by( 'id' , $_COOKIE[ 'rsreferredusername' ] ) ;
                if ( ! $refuser ) {
                    return ;
                }

                $myid = $refuser->ID ;
            } else {
                $myid = check_if_referrer_has_manual_link( get_current_user_id() ) ;
            }

            if ( ! $myid )
                return ;

            $username      = get_user_by( 'id' , $myid )->user_login ;
            $ReferralPoint = self::referrel_points_for_product_in_cart( $myid ) ;
            if ( ! srp_check_is_array( $ReferralPoint ) )
                return ;

            global $referralmsg_global ;
            global $referral_pointsnew ;
            global $ref_pdt_plan ;
            global $producttitle ;
            $referral_pointsnew = $ReferralPoint ;
            foreach ( $ReferralPoint as $ProductId => $Points ) {
                if ( empty( $Points ) )
                    continue ;

                $ProductObj = srp_product_object( $ProductId ) ;
                if ( ! is_object( $ProductObj ) )
                    continue ;

                if ( srp_product_type( $ProductId ) == 'booking' )
                    continue ;

                $producttitle = $ProductId ;
                if ( is_initial_payment( $ProductId ) ) {
                    $ref_pdt_plan    = array( $Points ) ;
                    $ShowReferralMsg = is_cart() ? get_option( 'rs_show_hide_message_for_total_payment_plan_points_referral' ) : get_option( 'rs_show_hide_message_for_total_payment_plan_points_referrel_checkout' ) ;
                    $RefMsg          = is_cart() ? get_option( 'rs_referral_point_message_payment_plan_product_in_cart' ) : get_option( 'rs_referral_point_message_payment_plan_product_in_checkout' ) ;
                    if ( $ShowReferralMsg == 1 ) {
                        $RefMsg = str_replace( '[rsreferredusername]' , $username , $RefMsg ) ;
                        ?>
                        <div class="woocommerce-info rs_referral_payment_plan_message_cart rs_cart_message"> <?php echo do_shortcode( $RefMsg ) ; ?>  </div>
                        <?php
                    }
                } else {
                    $ReferralMsg                      = is_cart() ? get_option( 'rs_referral_point_message_product_in_cart' ) : get_option( 'rs_referral_point_message_product_in_checkout' ) ;
                    $ReferralMsg                      = str_replace( '[rsreferredusername]' , $username , $ReferralMsg ) ;
                    $referralmsg_global[ $ProductId ] = do_shortcode( $ReferralMsg ) . "<br>" ;
                }
            }
        }

        /* Assign Global Value($referral_pointsnew) */

        public static function referrel_points_for_product_in_cart( $UserId , $member_level = true ) {
            $referral_pointsnew = array() ;
            $BanType            = check_banning_type( $UserId ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return $referral_pointsnew ;

            global $referral_pointsnew ;
            foreach ( WC()->cart->cart_contents as $value ) {
                $CheckIfSalePrice = block_points_for_salepriced_product( $value[ 'product_id' ] , $value[ 'variation_id' ] ) ;
                if ( $CheckIfSalePrice == 'yes' )
                    continue ;

                $args      = array(
                    'productid'     => $value[ 'product_id' ] ,
                    'variationid'   => $value[ 'variation_id' ] ,
                    'item'          => $value ,
                    'referred_user' => $UserId ,
                        ) ;
                $Points    = check_level_of_enable_reward_point( $args ) ;
                $Points    = $member_level ? RSMemberFunction::earn_points_percentage( $UserId , ( float ) $Points ) : ( float ) $Points ;
                $ProductId = ! empty( $value[ 'variation_id' ] ) ? $value[ 'variation_id' ] : $value[ 'product_id' ] ;

                $referral_pointsnew[ $ProductId ] = $Points ;
            }

            $referral_pointsnew = self::get_referrer_points_after_coupon_applied( $referral_pointsnew , array( 'referred_user' => $UserId ) ) ;

            return $referral_pointsnew ;
        }

        /* Get Referrer Points After Coupon Applied */

        public static function get_referrer_points_after_coupon_applied( $referrer_points , $args ) {

            if ( 'no' === get_option( 'rs_referral_points_after_discounts' ) || ! get_option( 'rs_referral_points_after_discounts' ) ) {
                return $referrer_points ;
            }

            if ( ! srp_check_is_array( $referrer_points ) || ! array_filter( ( array ) $referrer_points ) ) {
                return $referrer_points ;
            }

            $ModifiedPoints = array() ;

            foreach ( $referrer_points as $ProductId => $Point ) {
                $ModifiedPoints[ $ProductId ] = ( float ) RSFrontendAssets::coupon_points_conversion( $ProductId , $Point , $args ) ;
            }

            return $ModifiedPoints ;
        }

        /* Display Referral Product Purchase message in Cart/Checkout for Product */

        public static function referral_product_purchase_msg_for_each_product( $ShowReferralMsg ) {
            if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
                $refuser = (get_option( 'rs_generate_referral_link_based_on_user' ) == 1) ? get_user_by( 'login' , $_COOKIE[ 'rsreferredusername' ] ) : get_user_by( 'id' , $_COOKIE[ 'rsreferredusername' ] ) ;

                if ( ! $refuser ) {
                    return ;
                }

                $myid = $refuser->ID ;
            } else {
                $myid = check_if_referrer_has_manual_link( get_current_user_id() ) ;
            }

            if ( ! $myid )
                return ;

            if ( $ShowReferralMsg == 2 )
                return ;

            global $referralmsg_global ;
            global $producttitle ;
            if ( ! srp_check_is_array( $referralmsg_global ) )
                return ;
            ?>
            <div class="woocommerce-info"><?php
                foreach ( $referralmsg_global as $ProductId => $msg ) {
                    $producttitle = $ProductId ;
                    echo $msg ;
                }
                ?>
            </div>
            <?php
        }

        public static function award_points_for_referral_account_signup( $user_id ) {
            if ( get_post_meta( $user_id , 'rs_registered_user' , true ) != '' )
                return ;

            if ( get_option( '_rs_referral_enable_signups' ) != 'yes' )
                return ;

            if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) )
                return ;

            $user_info     = new WP_User( $user_id ) ;
            $user_reg_date = date( 'Y-m-d h:i:sa' , strtotime( $user_info->user_registered ) ) ;
            $reg_date      = date( 'Y-m-d h:i:sa' , strtotime( $user_reg_date . ' + ' . get_option( '_rs_select_referral_points_referee_time_content' ) . ' days ' ) ) ;
            $reg_date      = strtotime( $reg_date ) ;
            $current_date  = date( 'Y-m-d h:i:sa' ) ;
            $current_date  = strtotime( $current_date ) ;
            //Is for Immediatly
            if ( get_option( '_rs_select_referral_points_referee_time' ) == '1' ) {
                $limitation = true ;
            } else {
                // Is for Limited Time with Number of Days
                $limitation = ( $current_date > $reg_date ) ? true : false ;
            }
            if ( $limitation == false )
                return ;

            $referreduser = get_user_by( 'login' , $_COOKIE[ 'rsreferredusername' ] ) ;
            $refuserid    = ( $referreduser != false ) ? $referreduser->ID : $_COOKIE[ 'rsreferredusername' ] ;
            $banning_type = check_banning_type( $refuserid ) ;
            if ( $banning_type == 'earningonly' || $banning_type == 'both' )
                return ;

            // Instant Referral Registration Points
            if ( get_option( 'rs_select_referral_points_award' ) == '1' ) {
                if ( get_option( 'rs_referral_reward_signup_after_first_purchase' ) != 'yes' ) {
                    self::award_referral_registration_points_instantly( $user_id , $refuserid ) ;
                } else {
                    self::award_referral_registration_points_after_first_purchase( $user_id , $refuserid ) ;
                }
            } else {
                self::award_referral_registration_points_after_first_purchase( $user_id , $refuserid ) ;
            }

            if ( get_option( 'rs_referral_reward_signup_getting_refer' ) == '1' ) {
                if ( get_option( 'rs_referral_reward_getting_refer_after_first_purchase' ) == 'yes' ) {
                    self::award_getting_referred_points_after_first_purchase( $user_id , $refuserid ) ;
                } else {
                    self::award_getting_referred_points_instantly( $user_id , $refuserid ) ;
                }
            }

            if ( isset( $_COOKIE[ 'rsreferredusername' ] ) && allow_reward_points_for_user( $user_id ) ) {
                $UserInfo = get_user_by( 'login' , $_COOKIE[ 'rsreferredusername' ] ) ;
                $RefId    = ($UserInfo) ? $UserInfo->ID : $_COOKIE[ 'rsreferredusername' ] ;
                if ( $user_id != $RefId ) {
                    $ReferralCount = ( int ) get_user_meta( $RefId , 'referral_link_count_value' , true ) ;
                    update_user_meta( $RefId , 'referral_link_count_value' , $ReferralCount + 1 ) ;
                }
            }
        }

        /* Instant Referral Registration Points */

        public static function award_referral_registration_points_instantly( $user_id , $refuserid ) {
            if ( get_user_meta( $user_id , 'rs_referrer_regpoints_awarded' , true ) == '1' )
                return ;

            $Points  = get_option( 'rs_referral_reward_signup' ) ;
            $new_obj = new RewardPointsOrder( 0 , 'no' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                $new_obj->check_point_restriction( $Points , 0 , 'RRRP' , $refuserid , '' , $user_id , '' , '' , '' ) ;
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $Points , 'event_slug' => 'RRRP' , 'user_id' => $refuserid , 'referred_id' => $user_id , 'totalearnedpoints' => $Points ) ;
                $new_obj->total_points_management( $valuestoinsert ) ;
                $previouslog    = get_option( 'rs_referral_log' ) ;
                RS_Referral_Log::update_referral_log( $refuserid , $user_id , $Points , array_filter( ( array ) $previouslog ) ) ;
                update_user_meta( $user_id , '_rs_i_referred_by' , $refuserid ) ;
            }

            do_action( 'fp_signup_points_for_referrer' , $refuserid , $user_id , $Points ) ;

            add_user_meta( $user_id , 'rs_referrer_regpoints_awarded' , '1' ) ;
        }

        /* After First Purchase Referral Registration Points */

        public static function award_referral_registration_points_after_first_purchase( $user_id , $refuserid ) {
            $mainpoints             = array() ;
            $mainpoints[ $user_id ] = array( 'userid' => $user_id , 'refuserid' => $refuserid , 'refpoints' => ( float ) get_option( 'rs_referral_reward_signup' ) ) ;
            update_user_meta( $user_id , 'srp_data_for_reg_points' , $mainpoints ) ;
        }

        /* After First Purchase Getting Referred Referral Registration Points */

        public static function award_getting_referred_points_after_first_purchase( $user_id , $refuserid ) {
            $mainpoints             = array() ;
            $mainpoints[ $user_id ] = array( 'userid' => $user_id , 'refpoints' => ( float ) get_option( 'rs_referral_reward_getting_refer' ) ) ;
            update_user_meta( $user_id , 'srp_data_for_get_referred_reg_points' , $mainpoints ) ;
        }

        /* Instant Getting Referred Referral Registration Points */

        public static function award_getting_referred_points_instantly( $user_id , $refuserid ) {
            if ( get_user_meta( $user_id , '_points_awarded_get_refer' , true ) == '1' )
                return ;

            $RegPoints          = RSMemberFunction::earn_points_percentage( $user_id , ( float ) get_option( 'rs_referral_reward_getting_refer' ) ) ;
            $restrictuserpoints = get_option( 'rs_max_earning_points_for_user' ) ;
            $PointsData         = new RS_Points_Data( $user_id ) ;
            $Points             = $PointsData->total_available_points() ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                if ( $Points <= $restrictuserpoints ) {
                    $RegPoints = ( ($Points + $RegPoints) <= $restrictuserpoints ) ? $RegPoints : ($restrictuserpoints - $Points) ;
                } else {
                    $RegPoints = 0 ;
                }
            }
            $table_args = array(
                'user_id'           => $user_id ,
                'pointstoinsert'    => $RegPoints ,
                'checkpoints'       => 'RRPGR' ,
                'totalearnedpoints' => $RegPoints ,
                    ) ;
            RSPointExpiry::insert_earning_points( $table_args ) ;
            RSPointExpiry::record_the_points( $table_args ) ;

            do_action( 'fp_signup_points_for_getting_referred' , $refuserid , $user_id , $RegPoints ) ;

            add_user_meta( $user_id , '_points_awarded_get_refer' , '1' ) ;
        }

    }

    RSFunctionForReferralSystem::init() ;
}