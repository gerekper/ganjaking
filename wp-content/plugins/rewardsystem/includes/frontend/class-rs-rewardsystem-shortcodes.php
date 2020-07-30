<?php
/**
 * Shortcodes
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RS_Rewardsystem_Shortcodes' ) ) {

    /**
     * Class.
     */
    class RS_Rewardsystem_Shortcodes {

        /**
         * Class Initialization.
         */
        public static function init() {

            $shortcodes = array(
                'rewardpoints' ,
                'rsrefferalpoints' ,
                'equalamount' ,
                'referralequalamount' ,
                'variationrewardpoints' ,
                'variationreferralpoints' ,
                'variationpointprice' ,
                'variationpointsvalue' ,
                'variationreferralpointsamount' ,
                'buypoints' ,
                'facebook_like_reward_points' ,
                'facebook_share_reward_points' ,
                'twitter_tweet_reward_points' ,
                'twitter_follow_reward_points' ,
                'instagram_reward_points' ,
                'google_share_reward_points' ,
                'vk_reward_points' ,
                'ok_share_reward_points' ,
                'rs_user_total_earned_points' ,
                'rs_user_total_redeemed_points' ,
                'rs_user_total_expired_points' ,
                'rs_user_total_points_in_value' ,
                'rs_total_earned_points_by_all_users' ,
                'rs_total_available_points_of_all_users' ,
                'rs_rank_based_total_earned_points' ,
                'rs_rank_based_current_reward_points' ,
                'rs_referrer_name' ,
                'rs_referrer_first_name' ,
                'rs_referrer_last_name' ,
                'rs_refer_a_friend' ,
                'rs_generate_referral' ,
                'rs_my_rewards_log' ,
                'rsencashform' ,
                'rs_redeem_vouchercode' ,
                'sumobookingpoints' ,
                'bookingrspoint' ,
                'equalbookingamount' ,
                'bookingproducttitle' ,
                'rs_unsubscribe_email' ,
                'rs_nominee_table' ,
                'rs_order_status' ,
                'rs_list_enable_options' ,
                'redeempoints' ,
                'redeemeduserpoints' ,
                'buypoint' ,
                'buypointvalue' ,
                'buypointvalues' ,
                'referralpoints' ,
                'rs_referral_payment_plan' ,
                'rspoint' ,
                'titleofproduct' ,
                'carteachvalue' ,
                'rsminimumpoints' ,
                'rsmaximumpoints' ,
                'rs_user_name' ,
                'rsequalpoints' ,
                'rs_list_of_orders_with_pending_points' ,
                'userpoints' ,
                'userpoints_value' ,
                'my_userpoints_value' ,
                'rs_points_on_hold' ,
                'totalrewards' ,
                'totalrewardsvalue' ,
                'balanceprice' ,
                'loginlink' ,
                'rs_view_referral_table' ,
                'rs_generate_static_referral' ,
                'rs_my_reward_points' ,
                'fppoint' ,
                'fppointvalue' ,
                'redeeming_threshold_value' ,
                'rsfirstname' ,
                'rslastname' ,
                'rs_referrer_email_id' ,
                'rssendpoints' ,
                'rs_my_cashback_log' ,
                'rs_my_current_earning_level_name' ,
                'rs_my_current_redeem_level_name' ,
                'rs_next_earning_level_points' ,
                'rs_next_redeem_level_points' ,
                'sumo_current_balance'
                    ) ;

            foreach ( $shortcodes as $shortcode_name ) {
                add_shortcode( $shortcode_name , array( __CLASS__ , 'display_shortcode' ) ) ;
            }
        }

        /**
         * Display Shortcode
         */
        public static function display_shortcode( $atts , $content , $tag ) {
            $function = 'shortcode_' . $tag ;
            switch ( $tag ) {
                case 'facebook_like_reward_points':
                case 'facebook_share_reward_points':
                case 'twitter_tweet_reward_points':
                case 'twitter_follow_reward_points':
                case 'instagram_reward_points':
                case 'google_share_reward_points':
                case 'vk_reward_points':
                case 'ok_share_reward_points':
                    ob_start() ;
                    echo self::shortcode_for_social_actions( $tag ) ;
                    $content = ob_get_contents() ;
                    ob_end_clean() ;
                    break ;

                case 'rs_user_total_earned_points':
                case 'rs_user_total_redeemed_points':
                case 'rs_user_total_expired_points':
                case 'rs_user_total_points_in_value':
                case 'rs_rank_based_total_earned_points':
                case 'rs_rank_based_current_reward_points':
                case 'rs_refer_a_friend':
                case 'rs_generate_referral':
                case 'rs_my_rewards_log':
                case 'rs_my_current_earning_level_name':
                case 'rs_my_current_redeem_level_name':
                case 'rs_next_earning_level_points':
                case 'rs_next_redeem_level_points':
                case 'rs_my_cashback_log':
                case 'rs_unsubscribe_email':
                case 'rs_nominee_table':
                case 'userpoints' ;
                case 'userpoints_value':
                case 'my_userpoints_value':
                case 'rs_view_referral_table':
                case 'rs_generate_static_referral':
                case 'rs_my_reward_points':
                case 'rssendpoints':
                    ob_start() ;
                    echo self::shortcode_for_points( $atts , $content , $tag ) ;
                    $content = ob_get_contents() ;
                    ob_end_clean() ;
                    break ;

                case 'rs_referrer_name':
                case 'rs_referrer_first_name':
                case 'rs_referrer_last_name':
                case 'rs_referrer_email_id':
                    ob_start() ;
                    echo self::shortcode_for_referrer_name( $tag ) ;
                    $content = ob_get_contents() ;
                    ob_end_clean() ;
                    break ;

                case 'rsfirstname':
                case 'rslastname':
                    ob_start() ;
                    echo self::shortcode_for_name( $tag ) ;
                    $content = ob_get_contents() ;
                    ob_end_clean() ;
                    break ;

                case 'redeempoints':
                case 'redeemeduserpoints':
                    ob_start() ;
                    echo self::shortcode_for_redeemedpoints( $tag ) ;
                    $content = ob_get_contents() ;
                    ob_end_clean() ;
                    break ;

                case 'rsminimumpoints':
                case 'rsequalpoints':
                    ob_start() ;
                    echo self::shortcode_for_min_redeem_point() ;
                    $content = ob_get_contents() ;
                    ob_end_clean() ;
                    break ;

                case 'rewardpoints':
                case 'equalamount':
                case 'rsrefferalpoints':
                case 'referralequalamount':
                case 'variationrewardpoints':
                case 'variationreferralpoints':
                case 'variationpointprice':
                case 'variationpointsvalue':
                case 'variationreferralpointsamount':
                case 'rsencashform' :
                case 'rs_redeem_vouchercode' :
                case 'sumo_current_balance':
                case 'sumobookingpoints':
                case 'bookingrspoint':
                case 'equalbookingamount':
                case 'bookingproducttitle':
                case 'rs_order_status':
                case 'rs_list_enable_options':
                case 'buypoints':
                case 'buypoint':
                case 'buypointvalue':
                case 'buypointvalues':
                case 'referralpoints':
                case 'rs_referral_payment_plan':
                case 'rspoint':
                case 'titleofproduct':
                case 'carteachvalue':
                case 'rsmaximumpoints':
                case 'rs_user_name':
                case 'rs_list_of_orders_with_pending_points':
                case 'rs_points_on_hold':
                case 'totalrewards':
                case 'balanceprice':
                case 'totalrewardsvalue':
                case 'loginlink':
                case 'fppoint':
                case 'fppointvalue':
                case 'redeeming_threshold_value':
                    ob_start() ;
                    echo self::$function() ; // output for shortcode
                    $content = ob_get_contents() ;
                    ob_end_clean() ;
                    break ;

                case 'rs_total_earned_points_by_all_users':
                case 'rs_total_available_points_of_all_users':
                    ob_start() ;
                    self::overall_total_earned_and_available_points_by_users( $tag ) ;
                    $content = ob_get_contents() ;
                    ob_end_clean() ;
                    break ;
            }
            return $content ;
        }

        /* Shortcode to display Reward Points in Earn Point notice */

        public static function shortcode_rewardpoints() {
            $reward_points = points_for_simple_product() ;
            return round_off_type( $reward_points ) ;
        }

        /* Shortcode to display Reward Points as Currency Value */

        public static function shortcode_equalamount() {
            $singleproductvalue = points_for_simple_product() ;
            $updatedvalue       = redeem_point_conversion( $singleproductvalue , get_current_user_id() , 'price' ) ;
            return srp_formatted_price( round_off_type_for_currency( $updatedvalue ) ) ;
        }

        /* Shortcode to display Referral Reward Points in Earn Point notice */

        public static function shortcode_rsrefferalpoints() {
            $reward_points = referral_points_for_simple_product() ;
            return round_off_type( $reward_points ) ;
        }

        /* Shortcode to display Referral Reward Points as Currency Value */

        public static function shortcode_referralequalamount() {
            $singleproductvalue = referral_points_for_simple_product() ;
            $updatedvalue       = redeem_point_conversion( $singleproductvalue , get_current_user_id() , 'price' ) ;
            return srp_formatted_price( round_off_type_for_currency( $updatedvalue ) ) ;
        }

        /* Shortcode to display Reward Points in Earn Point notice for Variable Product */

        public static function shortcode_variationrewardpoints() {
            return "<span class='variationrewardpoints' style='display:inline-block'></span>" ;
        }

        /* Shortcode to display Referral Reward Points in Earn Point notice for Variable Product */

        public static function shortcode_variationreferralpoints() {
            return "<span class='variationreferralpoints' style='display:inline-block'></span>" ;
        }

        public static function shortcode_variationpointprice() {
            return "<span class='variationpoint_price' style='display:inline-block'></span>" ;
        }

        /* Shortcode to display Reward Points as Currency Value for Variable Product */

        public static function shortcode_variationpointsvalue() {
            if ( get_option( 'woocommerce_currency_pos' ) == 'right' || get_option( 'woocommerce_currency_pos' ) == 'right_space' ) {
                return "<div class='variationrewardpointsamount' style='display:inline-block'></div>" . get_woocommerce_currency_symbol() ;
            } elseif ( get_option( 'woocommerce_currency_pos' ) == 'left' || get_option( 'woocommerce_currency_pos' ) == 'left_space' ) {
                return get_woocommerce_currency_symbol() . "<div class='variationrewardpointsamount' style='display:inline-block'></div>" ;
            }
        }

        /* Shortcode to display Referal Reward Points as Currency Value for Variable Product */

        public static function shortcode_variationreferralpointsamount() {
            if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
                if ( get_option( 'woocommerce_currency_pos' ) == 'right' || get_option( 'woocommerce_currency_pos' ) == 'right_space' ) {
                    return "<div class='variationreferralpointsamount' style='display:inline-block'></div>" . get_woocommerce_currency_symbol() ;
                } elseif ( get_option( 'woocommerce_currency_pos' ) == 'left' || get_option( 'woocommerce_currency_pos' ) == 'left_space' ) {
                    return get_woocommerce_currency_symbol() . "<div class='variationreferralpointsamount' style='display:inline-block'></div>" ;
                }
            }
        }

        /* Shortcode to display Social Buttons Reward Points */

        public static function shortcode_for_social_actions( $tag ) {
            if ( get_option( 'rs_social_reward_activated' ) != 'yes' )
                return ;

            global $post ;
            if ( ! is_object( $post ) )
                return ;

            $item   = array( 'qty' => '1' ) ;
            $postid = $post->ID ;
            switch ( $tag ) {
                case 'facebook_like_reward_points':
                    $args   = array(
                        'productid'    => $postid ,
                        'item'         => $item ,
                        'socialreward' => 'yes' ,
                        'rewardfor'    => 'fb_like'
                            ) ;
                    $Points = is_product() ? check_level_of_enable_reward_point( $args ) : get_option( 'rs_global_social_facebook_reward_points_post' ) ;
                    return $Points ;
                    break ;
                case 'facebook_share_reward_points':
                    $args   = array(
                        'productid'    => $postid ,
                        'item'         => $item ,
                        'socialreward' => 'yes' ,
                        'rewardfor'    => 'fb_share'
                            ) ;
                    $Points = is_product() ? check_level_of_enable_reward_point( $args ) : get_option( 'rs_global_social_facebook_share_reward_points_post' ) ;
                    return $Points ;
                    break ;
                case 'twitter_tweet_reward_points':
                    $args   = array(
                        'productid'    => $postid ,
                        'item'         => $item ,
                        'socialreward' => 'yes' ,
                        'rewardfor'    => 'twitter_tweet'
                            ) ;
                    $Points = is_product() ? check_level_of_enable_reward_point( $args ) : get_option( 'rs_global_social_twitter_reward_points_post' ) ;
                    return $Points ;
                    break ;
                case 'twitter_follow_reward_points':
                    $args   = array(
                        'productid'    => $postid ,
                        'item'         => $item ,
                        'socialreward' => 'yes' ,
                        'rewardfor'    => 'twitter_follow'
                            ) ;
                    $Points = is_product() ? check_level_of_enable_reward_point( $args ) : get_option( 'rs_global_social_twitter_follow_reward_points_post' ) ;
                    return $Points . '<br>' ;
                    break ;
                case 'instagram_reward_points':
                    $args   = array(
                        'productid'    => $postid ,
                        'item'         => $item ,
                        'socialreward' => 'yes' ,
                        'rewardfor'    => 'instagram'
                            ) ;
                    $Points = is_product() ? check_level_of_enable_reward_point( $args ) : get_option( 'rs_global_social_instagram_reward_points_post' ) ;
                    return $Points ;
                    break ;
                case 'google_share_reward_points':
                    $args   = array(
                        'productid'    => $postid ,
                        'item'         => $item ,
                        'socialreward' => 'yes' ,
                        'rewardfor'    => 'g_plus'
                            ) ;
                    $Points = is_product() ? check_level_of_enable_reward_point( $args ) : get_option( 'rs_global_social_google_reward_points_post' ) ;
                    return $Points ;
                    break ;
                case 'vk_reward_points':
                    $args   = array(
                        'productid'    => $postid ,
                        'item'         => $item ,
                        'socialreward' => 'yes' ,
                        'rewardfor'    => 'vk_like'
                            ) ;
                    $Points = is_product() ? check_level_of_enable_reward_point( $args ) : get_option( 'rs_global_social_vk_reward_points_post' ) ;
                    return $Points ;
                    break ;
                case 'ok_share_reward_points':
                    $args   = array(
                        'productid'    => $postid ,
                        'item'         => $item ,
                        'socialreward' => 'yes' ,
                        'rewardfor'    => 'ok_follow'
                            ) ;
                    $Points = is_product() ? check_level_of_enable_reward_point( $args ) : get_option( 'rs_global_social_ok_follow_reward_points_post' ) ;
                    return $Points ;
                    break ;
            }
        }

        /* Shortcode to display Earned, Redeemed and Expired Reward Points */

        public static function shortcode_for_points( $atts , $content , $tag ) {
            if ( is_user_logged_in() ) {
                $UserId     = get_current_user_id() ;
                $PointsData = new RS_Points_Data( $UserId ) ;
                switch ( $tag ) {
                    case 'rs_user_total_earned_points':
                        $TotalEarnedPoints   = $PointsData->total_earned_points() ;
                        return round_off_type( $TotalEarnedPoints ) ;
                        break ;
                    case 'rs_user_total_redeemed_points':
                        $TotalRedeemedPoints = $PointsData->total_redeemed_points() ;
                        return round_off_type( $TotalRedeemedPoints ) ;
                        break ;
                    case 'rs_user_total_expired_points':
                        $TotalExpiredPoints  = $PointsData->total_expired_points() ;
                        return round_off_type( $TotalExpiredPoints ) ;
                        break ;
                    case 'rs_user_total_points_in_value':
                        return currency_value_for_available_points( $UserId ) ;
                        break ;
                    case 'rs_rank_based_total_earned_points':
                        return self::rank_based_total_earned_and_available_points( 'total' ) ;
                        break ;
                    case 'rs_rank_based_current_reward_points':
                        return self::rank_based_total_earned_and_available_points( 'available' ) ;
                        break ;
                    case 'rs_refer_a_friend':
                        return self::form_for_refer_a_friend() ;
                        break ;
                    case 'rs_generate_referral':
                        return self::generate_referral_shortcode( $atts ) ;
                        break ;
                    case 'rs_my_current_earning_level_name':
                        return self::earning_level_name( $UserId ) ;
                        break ;
                    case 'rs_my_current_redeem_level_name':
                        return self::redeem_level_name( $UserId ) ;
                        break ;
                    case 'rs_next_earning_level_points':
                        return self::points_to_reach_next_earning_level( $UserId ) ;
                        break ;
                    case 'rs_next_redeem_level_points':
                        return self::points_to_reach_next_redeem_level( $UserId ) ;
                        break ;
                    case 'rs_my_cashback_log':
                        return self::cash_back_log() ;
                        break ;
                    case 'rs_unsubscribe_email':
                        return self::subscribe_field() ;
                        break ;
                    case 'rs_my_rewards_log':
                        return self::reward_log() ;
                        break ;
                    case 'rs_nominee_table':
                        return self::nominee_field() ;
                        break ;
                    case 'userpoints':
                        return self::available_points_without_caption() ;
                        break ;
                    case 'userpoints_value':
                    case 'my_userpoints_value':
                        return self::currency_value_for_available_points( $tag ) ;
                        break ;
                    case 'rs_view_referral_table':
                        echo self::shortcode_for_referral_list_table() ;
                        break ;
                    case 'rs_generate_static_referral':
                        self::shortcode_for_static_referral_link() ;
                        break ;
                    case 'rs_my_reward_points':
                        return self::shortcode_for_total_points() ;
                        break ;
                    case 'rssendpoints':
                        return self::shortcode_for_send_points() ;
                        break ;
                }
            } else {
                $LinkForMyAccountPage = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ;
                $LoginLink            = add_query_arg( 'redirect_to' , get_permalink() , $LinkForMyAccountPage ) ;
                $MsgForGuest          = get_option( 'rs_message_shortcode_guest_display' ) ;
                $LoginCaption         = get_option( 'rs_message_shortcode_login_name' ) ;
                echo '<br>' . $MsgForGuest . ' <a href="' . $LoginLink . '"> ' . $LoginCaption . '</a>' ;
            }
        }

        /* Shortcode to display Rank based Reward Points */

        public static function rank_based_total_earned_and_available_points( $type ) {
            global $wpdb ;
            $PointsTable     = $wpdb->prefix . 'rspointexpiry' ;
            $TitleForTable   = $type == 'available' ? __( 'Available Points' , SRP_LOCALE ) : __( 'Total Earned Points' , SRP_LOCALE ) ;
            $AvailablePoints = $wpdb->get_results( "SELECT userid ,(earnedpoints-usedpoints) as availablepoints FROM $PointsTable WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) ORDER BY availablepoints DESC" , ARRAY_A ) ;
            $TotalPoints     = $wpdb->get_results( "SELECT userid ,earnedpoints  FROM $PointsTable WHERE earnedpoints NOT IN(0) and expiredpoints IN(0) ORDER BY earnedpoints DESC" , ARRAY_A ) ;
            $UserData        = $type == 'available' ? $AvailablePoints : $TotalPoints ;
            echo '<p><b><big>' . $TitleForTable . '</big></b></p>' ;
            return self::table_for_rank_based_points( $UserData , $type ) ;
        }

        /*
         * Shortcode for Overall Total Earned and Available Points by Users
         */

        public static function overall_total_earned_and_available_points_by_users( $tag ) {

            global $wpdb ;
            $PointsTable    = $wpdb->prefix . 'rspointexpiry' ;
            $overall_points = 0 ;

            if ( 'rs_total_earned_points_by_all_users' == $tag ) {
                /* For Displaying Total Points of all Users */
                $overall_total_points = $wpdb->get_results( "SELECT SUM(earnedpoints) as total_points_of_users FROM $PointsTable WHERE earnedpoints NOT IN(0) and expiredpoints IN(0) ORDER BY earnedpoints DESC" , ARRAY_A ) ;
                $overall_points       = isset( $overall_total_points[ 0 ][ 'total_points_of_users' ] ) ? $overall_total_points[ 0 ][ 'total_points_of_users' ] : 0 ;
            } else {
                /* For Displaying Available Points of all Users */
                $overall_available_points = $wpdb->get_results( "SELECT SUM(earnedpoints-usedpoints) as available_points_of_users FROM $PointsTable WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) ORDER BY available_points_of_users DESC" , ARRAY_A ) ;
                $overall_points           = isset( $overall_available_points[ 0 ][ 'available_points_of_users' ] ) ? $overall_available_points[ 0 ][ 'available_points_of_users' ] : 0 ;
            }

            echo esc_html( round_off_type( $overall_points ) ) ;
        }

        public static function table_for_rank_based_points( $UserData , $type ) {
            ob_start() ;
            $Pagination      = $type == 'available' ? get_option( 'rs_select_pagination_for_available_points' ) : get_option( 'rs_select_pagination_for_total_earned_points' ) ;
            $PaginationValue = $type == 'available' ? get_option( 'rs_value_without_pagination_for_available_points' ) : get_option( 'rs_value_without_pagination_for_total_earned_points' ) ;
            if ( $Pagination == '1' ) {
                $TableFields = '<p>' . __( 'Page Size:' , SRP_LOCALE ) . '<select id="page_size_for_points">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                </p>' ;
                echo $TableFields ;
            }
            ?>
            <table class = "demo shop_table srp_rank_based_points my_account_orders table-bordered" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">
                <thead>
                    <tr>
                        <th><?php echo get_option( 'rs_my_rewards_sno_label' ) ; ?></th>
                        <th data-sortable="false"><?php echo get_option( 'rs_my_rewards_userid_label' ) ; ?></th>
                        <th data-type="numeric"><?php echo get_option( 'rs_my_rewards_points_earned_label' ) ; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ( srp_check_is_array( $UserData ) ) {
                        $i = 1 ;
                        foreach ( $UserData as $Data ) {
                            $UserObj = get_user_by( 'id' , $Data[ 'userid' ] ) ;
                            $Points  = round_off_type( isset( $Data[ 'availablepoints' ] ) ? $Data[ 'availablepoints' ] : $Data[ 'earnedpoints' ] ) ;
                            if ( $Pagination == '2' ) {
                                if ( $i <= $PaginationValue ) {
                                    ?>
                                    <tr>
                                        <td data-value="<?php echo $i ; ?>"><?php echo $i ; ?></td>                                     
                                        <td><?php echo is_object( $UserObj ) ? $UserObj->user_login : 'Guest' ; ?></td>                                     
                                        <td><?php echo $Points ; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td data-value="<?php echo $i ; ?>"><?php echo $i ; ?></td>                                     
                                    <td><?php echo is_object( $UserObj ) ? $UserObj->user_login : 'Guest' ; ?></td>                                     
                                    <td><?php echo $Points ; ?></td>
                                </tr>
                                <?php
                            }
                            $i ++ ;
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr style="clear:both;">
                        <td colspan="3">
                            <div class="pagination pagination-centered"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php
            return ob_get_clean() ;
        }

        /* Shortcode to display Earning Level Name */

        public static function earning_level_name( $UserId ) {
            echo earn_level_name( $UserId ) ;
        }

        /* Shortcode to display Redeeming Level Name */

        public static function redeem_level_name( $UserId ) {
            if ( get_option( 'rs_enable_redeem_level_based_reward_points' ) != 'yes' )
                return ;

            $Pointsdata = new RS_Points_Data( $UserId ) ;
            $Points     = get_option( 'rs_select_redeem_points_based_on' ) == '1' ? $Pointsdata->total_earned_points() : $Pointsdata->total_available_points() ;
            $RuleId     = FPRewardSystem_Free_Product::earning_and_redeeming_level_id( $Points , 'redeeming' ) ;
            $Rules      = get_option( 'rewards_dynamic_rule_for_redeem' ) ;
            $LevelName  = isset( $Rules[ $RuleId ][ 'name' ] ) ? $Rules[ $RuleId ][ 'name' ] : "" ;
            return $LevelName ;
        }

        /* Shortcode to display Points to reach next level in earning */

        public static function points_to_reach_next_earning_level( $UserId ) {
            echo points_to_reach_next_earn_level( $UserId ) ;
        }

        /* Shortcode to display Points to reach next level in redeeming */

        public static function points_to_reach_next_redeem_level( $UserId ) {
            if ( get_option( 'rs_enable_redeem_level_based_reward_points' ) != 'yes' )
                return ;

            $Pointsdata = new RS_Points_Data( $UserId ) ;
            $Points     = get_option( 'rs_select_redeem_points_based_on' ) == '1' ? $Pointsdata->total_earned_points() : $Pointsdata->total_available_points() ;
            $RuleId     = FPRewardSystem_Free_Product::earning_and_redeeming_level_id( $Points , 'redeeming' ) ;
            $Rules      = get_option( 'rewards_dynamic_rule_for_redeem' ) ;
            $LevelName  = isset( $Rules[ $RuleId ][ 'name' ] ) ? $Rules[ $RuleId ][ 'name' ] : "" ;
            if ( ! isset( $Rules[ $RuleId ][ 'rewardpoints' ] ) )
                return ;

            $NextLevelPoints = $Rules[ $RuleId ][ 'rewardpoints' ] - $Points ;
            $Msg             = str_replace( '[balancepoint]' , $NextLevelPoints , str_replace( '[next_level_name]' , $LevelName , get_option( 'rs_point_to_reach_next_level' ) ) ) ;
            return $Msg ;
        }

        /* Shortcode to display Referrer name */

        public static function shortcode_for_referrer_name( $tag ) {
            if ( get_option( 'rs_referral_activated' ) != 'yes' )
                return ;

            /* Cookie 'rsreferredusername' for Signup Email  */
            $cookie_value = isset( $_COOKIE[ 'rsreferredusername' ] ) ? $_COOKIE[ 'rsreferredusername' ] : '' ;
            /* ref - To display the Message to Referral Person  */
            $referrer     = isset( $_GET[ 'ref' ] ) ? $_GET[ 'ref' ] : $cookie_value ;
            if ( ! $referrer )
                return ;

            $LinkType = get_option( 'rs_generate_referral_link_based_on_user' ) ;
            $UserInfo = $LinkType == '1' ? get_user_by( 'login' , $referrer ) : get_userdata( $referrer ) ;
            switch ( $tag ) {
                case 'rs_referrer_name':
                    $UserName  = is_object( $UserInfo ) ? (get_option( 'rs_send_message_by_referrer' ) == '1' ? $UserInfo->user_login : $UserInfo->first_name) : 'Guest' ;
                    return $UserName ;
                    break ;
                case 'rs_referrer_first_name':
                    $FirstName = is_object( $UserInfo ) ? (get_option( 'rs_send_message_by_referrer' ) == '1' ? $UserInfo->first_name : $UserInfo->first_name) : 'Guest' ;
                    return $FirstName ;
                    break ;
                case 'rs_referrer_last_name':
                    $LastName  = is_object( $UserInfo ) ? (get_option( 'rs_send_message_by_referrer' ) == '1' ? $UserInfo->last_name : $UserInfo->first_name) : 'Guest' ;
                    return $LastName ;
                    break ;
                case 'rs_referrer_email_id':
                    $Email     = is_object( $UserInfo ) ? $UserInfo->user_email : 'Guest' ;
                    return $Email ;
                    break ;
            }
        }

        /* Shortcode to display User First and Last name */

        public static function shortcode_for_name( $tag ) {
            $UserInfo = get_user_by( 'id' , get_current_user_id() ) ;
            switch ( $tag ) {
                case 'rsfirstname':
                    $UserName = is_object( $UserInfo ) ? $UserInfo->first_name : 'Guest' ;
                    return $UserName ;
                    break ;
                case 'rslastname':
                    $UserName = is_object( $UserInfo ) ? $UserInfo->last_name : 'Guest' ;
                    return $UserName ;
                    break ;
            }
        }

        /* Shortcode to display Refer A Friend Form */

        public static function form_for_refer_a_friend() {
            if ( get_option( 'rs_referral_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_reward_content_shortcode' ) != 'yes' )
                return ;

            if ( is_account_page() )
                if ( get_option( 'rs_reward_content' ) != 'yes' )
                    return ;

            return self::display_refer_a_friend_form() ;
        }

        /* Shortcode to display Refer A Friend Form */

        public static function display_refer_a_friend_form() {

            if ( get_option( 'rs_enable_message_for_friend_form' ) == '2' )
                return ;

            if ( ! check_if_referral_is_restricted() )
                if ( get_option( 'rs_display_msg_when_access_is_prevented' ) === '1' )
                    echo '<br>' . get_option( 'rs_msg_for_restricted_user' ) ;

            $UserId = get_current_user_id() ;
            if ( ! check_referral_count_if_exist( $UserId ) )
                _e( "<p>Since you have reached the referral link usage, you don't have the access to refer anymore</p>" , SRP_LOCALE ) ;

            if ( ! check_if_referral_is_restricted_based_on_history() )
                return ;

            wp_enqueue_script( 'fp_referafriend_from' , SRP_PLUGIN_DIR_URL . "includes/frontend/js/fp-referafriend-form.js" , array( 'jquery' ) , SRP_VERSION ) ;
            $LocalizedVariables = array(
                'ajaxurl'           => SRP_ADMIN_AJAX_URL ,
                'refnameerrormsg'   => addslashes( get_option( 'rs_my_rewards_friend_name_error_message' ) ) ,
                'refmailiderrormsg' => addslashes( get_option( 'rs_my_rewards_friend_email_error_message' ) ) ,
                'invalidemail'      => addslashes( get_option( 'rs_my_rewards_friend_email_is_not_valid' ) ) ,
                'subjecterror'      => addslashes( get_option( 'rs_my_rewards_email_subject_error_message' ) ) ,
                'messageerror'      => addslashes( get_option( 'rs_my_rewards_email_message_error_message' ) ) ,
                'enableterms'       => get_option( 'rs_show_hide_iagree_termsandcondition_field' ) ,
                'successmessage'    => __( 'Mail Sent Successfully' , SRP_LOCALE ) ,
                'send_mail'         => wp_create_nonce( 'send-mail' ) ) ;
            wp_localize_script( 'fp_referafriend_from' , 'fp_referafriend_from_params' , $LocalizedVariables ) ;
            ob_start() ;
            $UserInfo           = get_userdata( $UserId ) ;
            $Username           = is_object( $UserInfo ) ? $UserInfo->user_login : 'Guest' ;
            $KeyForQuery        = get_option( 'rs_generate_referral_link_based_on_user' ) == '1' ? $Username : $UserId ;
            $query              = (get_option( 'rs_restrict_referral_points_for_same_ip' ) == 'yes') ? array( 'ref' => $KeyForQuery , 'ip' => base64_encode( get_referrer_ip_address() ) ) : array( 'ref' => $KeyForQuery ) ;
            $StrToReplace       = esc_url_raw( add_query_arg( $query , get_option( 'rs_referral_link_refer_a_friend_form' ) ) ) ;
            $RefURL             = str_replace( "[site_referral_url]" , $StrToReplace , htmlentities( get_option( 'rs_friend_referral_link' ) ) ) ;
            $StrToReplace       = "<a href='" . get_option( 'rs_refer_friend_termscondition_url' ) . "' target='_blank'>" . addslashes( get_option( 'rs_refer_friend_termscondition_caption' ) ) . "</a>" ;
            $ReplacedContent    = str_replace( "{termsandconditions}" , $StrToReplace , addslashes( get_option( 'rs_refer_friend_iagreecaption_link' ) ) ) ;
            ?>
            <style type="text/css">
                <?php echo get_option( 'rs_refer_a_friend_custom_css' ) ; ?>;
            </style>
            <form id="rs_refer_a_friend_form" method="post">
                <table class="shop_table my_account_referrals">
                    <tr>
                        <td>
                            <h3><?php echo (get_option( 'rs_my_rewards_friend_name_label' )) ; ?></h3>
                        </td>
                        <td>
                            <input type="text" name="rs_friend_name" placeholder ="<?php echo ( get_option( 'rs_my_rewards_friend_name_placeholder' ) ) ; ?>" id="rs_friend_name" value=""/>
                            <br>
                            <div class="rs_notification"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h3><?php echo (get_option( 'rs_my_rewards_friend_email_label' )) ; ?></h3>
                        </td>
                        <td>
                            <input type="text" name="rs_friend_email" placeholder="<?php echo ( get_option( 'rs_my_rewards_friend_email_placeholder' ) ) ; ?>" id="rs_friend_email" value=""/>
                            <br>
                            <div class="rs_notification"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h3><?php echo (get_option( 'rs_my_rewards_friend_subject_label' )) ; ?></h3>
                        </td>
                        <td>
                            <input type="text" name="rs_friend_subject" id="rs_friend_subject" placeholder ="<?php echo ( get_option( 'rs_my_rewards_friend_email_subject_placeholder' ) ) ; ?>" <?php if ( get_option( 'rs_allow_user_to_request_prefilled_subject' , '1' ) == '2' ) { ?> readonly="readonly" <?php } ?> value="<?php echo esc_html( get_option( 'rs_subject_field' , 'Referral Link' ) ) ; ?>" />
                            <br>
                            <div class="rs_notification"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h3><?php echo (get_option( 'rs_my_rewards_friend_message_label' )) ; ?></h3>
                        </td>
                        <td>
                            <textarea rows="5" cols="35" id="rs_your_message" placeholder ="<?php echo ( get_option( 'rs_my_rewards_friend_email_message_placeholder' ) ) ; ?>"  <?php if ( get_option( 'rs_allow_user_to_request_prefilled_message' ) == '2' ) { ?> readonly="readonly" <?php } ?> name="rs_your_message"><?php echo $RefURL ; ?></textarea>
                            <br>
                            <div class="rs_notification"></div>
                        </td>
                    </tr>
                    <?php
                    if ( get_option( 'rs_show_hide_iagree_termsandcondition_field' ) == '2' ) {
                        ?>    
                        <tr>
                            <td colspan="2">
                                <input type="checkbox" name="rs_terms"  id="rs_terms" /> 
                                <?php echo $ReplacedContent ; ?>
                                <div class ="iagreeerror" style="display:none;"><?php echo ( get_option( 'rs_iagree_error_message' ) ) ; ?></div>
                            </td>
                        </tr>
                    <?php } ?>    
                </table>    
                <input type="submit" class="button-primary rs_send_mail_to_friend" name="submit" id="rs_refer_submit" value="<?php _e( 'Send Mail' , SRP_LOCALE ) ; ?>"/>
                <div class="rs_notification_final"></div>
            </form>
            <?php
            $content = ob_get_contents() ;
            ob_end_clean() ;
            return $content ;
        }

        /* Shortcode to display Generate Referral Link Button */

        public static function generate_referral_shortcode( $atts ) {
            if ( get_option( 'rs_referral_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_reward_content_shortcode' ) != 'yes' )
                return ;

            if ( ! check_if_referral_is_restricted() )
                if ( get_option( 'rs_display_msg_when_access_is_prevented' ) === '1' )
                    echo '<br>' . get_option( 'rs_msg_for_restricted_user' ) ;

            ob_start() ;
            extract( shortcode_atts( array(
                'referralbutton' => 'show' ,
                'referraltable'  => 'show' ,
                            ) , $atts ) ) ;

            if ( $referralbutton == 'show' )
                if ( check_if_referral_is_restricted_based_on_history() )
                    RSFunctionForReferralSystem::field_to_generate_referral_link() ;

            if ( $referraltable == 'show' )
                if ( check_if_referral_is_restricted_based_on_history() )
                    RSFunctionForReferralSystem::list_of_generated_link() ;

            $content = ob_get_contents() ;
            ob_end_clean() ;
            return $content ;
        }

        /* Shortcode to display Cashback Log Table */

        public static function cash_back_log() {
            if ( get_option( 'rs_cashback_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_my_cashback_table_shortcode' ) == '2' )
                return ;

            if ( get_option( 'rs_reward_content_shortcode' ) != 'yes' )
                return ;

            $TableData = array(
                'title'    => get_option( 'rs_my_cashback_title_shortcode' ) ,
                'sno'      => get_option( 'rs_my_cashback_sno_label_shortcode' ) ,
                'username' => get_option( 'rs_my_cashback_userid_label_shortcode' ) ,
                'request'  => get_option( 'rs_my_cashback_requested_label_shortcode' ) ,
                'status'   => get_option( 'rs_my_cashback_status_label_shortcode' ) ,
                'action'   => get_option( 'rs_my_cashback_action_label_shortcode' )
                    ) ;
            RSCashBackFrontend::cash_back_log_table( $TableData ) ;
        }

        /* Shortcode to display Cashback Form */

        public static function shortcode_rsencashform() {
            if ( get_option( 'rs_cashback_activated' ) != 'yes' )
                return ;

            if ( is_user_logged_in() ) {
                if ( ! self::check_if_cashback_form_is_restricted() ) {
                    echo '<p><b>' . __( 'Cashback Form is currently restricted for your account' , SRP_LOCALE ) . '</b></p>' ;
                    return ;
                }

                if ( get_option( 'rs_enable_disable_encashing' ) == '2' )
                    return ;

                $PointsData      = new RS_Points_Data( get_current_user_id() ) ;
                $AvailablePoints = $PointsData->total_available_points() ;
                if ( $AvailablePoints > 0 ) {
                    $BanningType = check_banning_type( get_current_user_id() ) ;
                    if ( $BanningType != 'earningonly' && $BanningType != 'both' ) {
                        wp_enqueue_script( 'wp_google_recaptcha' , 'https://www.google.com/recaptcha/api.js' , array( 'jquery' ) , SRP_VERSION ) ;
                        $MinPointsToReqCashback  = get_option( 'rs_minimum_points_encashing_request' ) == '' ? 0 : get_option( 'rs_minimum_points_encashing_request' ) ;
                        $MaxPointsToReqCashback  = get_option( 'rs_maximum_points_encashing_request' ) == '' ? $AvailablePoints : get_option( 'rs_maximum_points_encashing_request' ) ;
                        $ErrMsgForMinorMaxPoints = get_option( 'rs_error_message_points_lesser_than_minimum_points' ) ;

                        $ErrMsgForMinAvailablePoint = get_option( 'rs_error_message_currentpoints_less_than_minimum_points' ) ;
                        $ErrMsgForAvailablePoints   = str_replace( "[minimum_encash_points]" , $MinPointsToReqCashback , $ErrMsgForMinAvailablePoint ) ;
                        $ConvertionRate             = get_option( 'rs_redeem_point_for_cash_back' ) ;
                        $PointsValue                = get_option( 'rs_redeem_point_value_for_cash_back' ) ;
                        $ConvertedPoints            = $AvailablePoints / $ConvertionRate ;
                        $ConvertedValue             = $ConvertedPoints * $PointsValue ;
                        $PointsToDisplay            = $AvailablePoints . '(' . get_woocommerce_currency_symbol() . ( $ConvertedValue ) . ')' ;
                        $ReplacedErrMsg             = str_replace( "[minimum_encash_points]" , $MinPointsToReqCashback , $ErrMsgForMinorMaxPoints ) ;
                        $ReplacedErrMsg             = str_replace( "[maximum_encash_points]" , $MaxPointsToReqCashback , $ReplacedErrMsg ) ;
                        $AllowToSavePaymentMethod   = get_option( 'rs_allow_admin_to_save_previous_payment_method' ) ;
                        ob_start() ;
                        ?>
                        <style type="text/css">
                        <?php
                        echo get_option( 'rs_encash_form_inbuilt_design' ) == '1' ? get_option( 'rs_encash_form_default_css' ) : get_option( 'rs_encash_form_custom_css' ) ;
                        ?>
                        </style>
                        <form id="encashing_form" method="post" enctype="multipart/form-data">
                            <div class="rs_current_points_value">
                                <p>
                                    <label><?php echo addslashes( get_option( "rs_total_points_for_cashback_request" ) ) ; ?></label>
                                </p>
                                <p>
                                    <input type="text" id="rs_available_points" name="rs_available_points" value="<?php echo $PointsToDisplay ; ?>" readonly="readonly">
                                </p>
                            </div>
                            <div class="rs_encash_points_value">
                                <p>
                                    <label><?php echo addslashes( get_option( "rs_encashing_points_label" ) ) ; ?></label>
                                </p>
                                <p>
                                    <input type="number" step="any" min="1" id="rs_encash_points_value" name="rs_encash_points_value" <?php if ( get_option( 'rs_allow_user_to_request_cashback' ) == '2' ) { ?>readonly="readonly"<?php } ?> value="<?php if ( get_option( 'rs_allow_user_to_request_cashback' ) == '2' ) echo $AvailablePoints ; ?>"/>
                                </p>
                            </div>
                            <div class="error" id="points_empty_error" style="display:none;">
                                <?php echo addslashes( get_option( "rs_error_message_points_empty_encash" ) ) ; ?>
                            </div>
                            <div class="error" id="points_number_error" style="display:none;">
                                <?php echo addslashes( get_option( "rs_error_message_points_number_val_encash" ) ) ; ?>
                            </div>
                            <div class="error" id ="points_greater_than_earnpoints_error" style="display:none;">
                                <?php echo addslashes( get_option( "rs_error_message_points_greater_than_earnpoints" ) ) ; ?>
                            </div>
                            <div class="error" id ="points_lesser_than_minpoints_error" style="display:none;">
                                <?php echo addslashes( $ReplacedErrMsg ) ; ?>
                            </div>
                            <div class="rs_encash_points_reason">
                                <p>
                                    <label><?php echo addslashes( get_option( "rs_encashing_reason_label" ) ) ; ?></label>
                                </p>
                                <p>
                                    <textarea name ="rs_encash_points_reason" id="rs_encash_points_reason" rows= "3" cols= "50"></textarea>
                                </p>
                            </div>
                            <div class="error" id ="reason_empty_error" style="display:none;">
                                <?php echo addslashes( get_option( "rs_error_message_reason_encash_empty" ) ) ; ?>
                            </div>
                            <?php if ( get_option( 'rs_select_payment_method' ) == '3' ) { ?>
                                <div class ="rs_encash_payment_method">
                                    <p>
                                        <label><?php echo addslashes( get_option( "rs_encashing_payment_method_label" ) ) ; ?></label>
                                    </p>
                                    <p>
                                        <select id= "rs_encash_payment_method">
                                            <option value="encash_through_paypal_method" <?php if ( $AllowToSavePaymentMethod == 'yes' && get_user_meta( get_current_user_id() , 'rs_cashback_previous_payment_method' , true ) == 'encash_through_paypal_method' ) { ?>selected="selected"<?php } ?>><?php _e( 'PayPal' , SRP_LOCALE ) ; ?></option>
                                            <option value="encash_through_custom_payment" <?php if ( $AllowToSavePaymentMethod == 'yes' && get_user_meta( get_current_user_id() , 'rs_cashback_previous_payment_method' , true ) == 'encash_through_custom_payment' ) { ?>selected="selected"<?php } ?>><?php _e( 'Custom Payment' , SRP_LOCALE ) ; ?></option>
                                            <?php
                                            if ( check_whether_hoicker_is_active() ) {
                                                $WalletLabel = ! empty( get_option( 'rs_encashing_wallet_menu_label' ) ) ? get_option( 'rs_encashing_wallet_menu_label' ) : __( 'Hoicker Wallet' , SRP_LOCALE ) ;
                                                ?>
                                                <option value="<?php echo $WalletLabel ; ?>"><?php echo $WalletLabel ; ?></option>
                                            <?php } ?>
                                        </select>
                                    </p>
                                </div>
                                <?php
                            }
                            if ( check_whether_hoicker_is_active() ) {
                                $WalletLabel = ! empty( get_option( 'rs_encashing_wallet_menu_label' ) ) ? get_option( 'rs_encashing_wallet_menu_label' ) : __( 'Hoicker Wallet' , SRP_LOCALE ) ;
                                ?>
                                <input type="hidden" value="<?php echo $WalletLabel ; ?>" id="is_walletia_selected" class="is_walletia_selected" name="is_walletia_selected">
                                <div class ="rs_encash_wallet">
                                    <p>
                                        <label><?php echo addslashes( get_option( "rs_encashing_wallet_label" ) ) ; ?></label>
                                    </p>
                                </div>
                                <?php
                            }
                            if ( get_option( 'rs_select_payment_method' ) == '1' || get_option( 'rs_select_payment_method' ) == '3' ) {
                                ?>
                                <div class ="rs_encash_paypal_address">
                                    <p>
                                        <label><?php echo addslashes( get_option( "rs_encashing_payment_paypal_label" ) ) ; ?></label>
                                    </p>
                                    <p>
                                        <input type = "text" id = "rs_encash_paypal_address" name = "rs_encash_paypal_address" value="<?php
                                        if ( $AllowToSavePaymentMethod == 'yes' && get_user_meta( get_current_user_id() , 'rs_paypal_payment_details' , true ) != '' )
                                            echo get_user_meta( get_current_user_id() , 'rs_paypal_payment_details' , true ) ;
                                        ?>"/>
                                    </p>
                                </div>
                                <div class="error" id ="paypal_email_empty_error" style="display:none;">
                                    <?php echo addslashes( get_option( "rs_error_message_paypal_email_empty" ) ) ; ?>
                                </div>
                                <div class="error" id ="paypal_email_format_error" style="display:none;">
                                    <?php echo addslashes( get_option( "rs_error_message_paypal_email_wrong" ) ) ; ?>
                                </div>
                                <?php
                            }
                            if ( get_option( 'rs_select_payment_method' ) == '2' || get_option( 'rs_select_payment_method' ) == '3' ) {
                                ?>
                                <div class ="rs_encash_custom_payment_option_value">
                                    <p>
                                        <label><?php echo addslashes( get_option( "rs_encashing_payment_custom_label" ) ) ; ?></label>
                                    </p>
                                    <p>
                                        <textarea name ="rs_encash_custom_payment_option_value" id="rs_encash_custom_payment_option_value" rows= "3" cols= "50">
                                            <?php
                                            if ( $AllowToSavePaymentMethod == 'yes' && get_user_meta( get_current_user_id() , 'rs_custom_payment_details' , true ) != '' )
                                                echo get_user_meta( get_current_user_id() , 'rs_custom_payment_details' , true ) ;
                                            ?>
                                        </textarea>
                                    </p>
                                </div>
                                <div class="error" id="paypal_custom_option_empty_error" style="display:none;">
                                    <?php echo addslashes( get_option( "rs_error_custom_payment_field_empty" ) ) ; ?>
                                </div>
                                <?php
                            }
                            if ( get_option( 'rs_enable_recaptcha_to_display' ) == 'yes' && get_option( 'rs_google_recaptcha_site_key' ) != '' ) {
                                ?>
                                <div class="rs_enable_recaptcha_to_display">
                                    <p>
                                        <label>
                                            <?php echo addslashes( get_option( "rs_google_recaptcha_label" ) ) ; ?>
                                        </label>
                                    </p>
                                    <p>
                                    <div name="rs_encash_recaptcha" class="g-recaptcha" data-sitekey="<?php echo get_option( 'rs_google_recaptcha_site_key' ) ; ?>"></div>
                                    </p>
                                </div>
                                <div class="error" id="recaptcha_empty_error" style="display:none;">
                                    <?php echo addslashes( get_option( "rs_error_recaptcha_field_empty" ) ) ; ?>
                                </div>
                                <?php
                            }
                            ?>
                            <div class ="rs_encash_submit">
                                <input type="submit" value="<?php echo addslashes( get_option( "rs_encashing_submit_button_label" ) ) ; ?>" id="submit_cashback"/>
                            </div>
                            <div class="success_info" id ="encash_form_success_info" style="display:none;">
                                <b><?php echo addslashes( get_option( "rs_message_encashing_request_submitted" ) ) ; ?></b>
                            </div>
                        </form>
                        <?php
                        $content = ob_get_contents() ;
                        ob_end_clean() ;
                        return $content ;
                    } else {
                        echo get_option( "rs_message_for_banned_users_encashing" ) ;
                    }
                } else {
                    echo get_option( "rs_message_users_nopoints_encashing" ) ;
                }
            } else {
                ob_start() ;
                ?>
                <p>
                    <a href="<?php echo wp_login_url() ; ?>" title="<?php _e( 'Login' , SRP_LOCALE ) ?>"><?php echo get_option( "rs_encashing_login_link_label" ) ; ?></a>
                </p>
                <?php
                $Content     = ob_get_clean() ;
                $Msg         = get_option( "rs_message_for_guest_encashing" ) ;
                $ReplacedMsg = str_replace( "[rssitelogin]" , $Content , $Msg ) ;
                echo $ReplacedMsg ;
            }
        }

        public static function check_if_cashback_form_is_restricted() {
            $UserRole        = wp_get_current_user() ;
            $UserRole        = $UserRole->roles[ 0 ] ;
            $RestrictionType = get_option( 'rs_user_selection_type_for_cashback' ) ;
            if ( $RestrictionType == '1' || $RestrictionType == '4' ) {
                return true ;
            } elseif ( $RestrictionType == '2' ) {
                $IncUser = get_option( 'rs_select_inc_user_search' ) ;
                if ( empty( $IncUser ) )
                    return true ;

                $UserIds = srp_check_is_array( $IncUser ) ? $IncUser : array_filter( array_map( 'absint' , ( array ) explode( ',' , $IncUser ) ) ) ;
                if ( in_array( get_current_user_id() , $UserIds ) )
                    return true ;
            } elseif ( $RestrictionType == '3' ) {
                $ExcUser = get_option( 'rs_select_exc_user_search' ) ;
                if ( empty( $ExcUser ) )
                    return true ;

                $UserIds = srp_check_is_array( $ExcUser ) ? $ExcUser : array_filter( array_map( 'absint' , ( array ) explode( ',' , $ExcUser ) ) ) ;
                if ( ! in_array( get_current_user_id() , $UserIds ) )
                    return true ;
            } elseif ( $RestrictionType == '5' ) {
                $IncUserRole = get_option( 'rs_select_inc_userrole' ) ;
                if ( ! srp_check_is_array( $IncUserRole ) )
                    return true ;

                if ( in_array( $UserRole , $IncUserRole ) )
                    return true ;
            } else {
                $ExcUserRole = get_option( 'rs_select_exc_userrole' ) ;
                if ( ! srp_check_is_array( $ExcUserRole ) )
                    return true ;

                if ( srp_check_is_array( $ExcUserRole ) && ! in_array( $UserRole , $ExcUserRole ) )
                    return true ;
            }
            return false ;
        }

        /* Shortcode to display GiftVocuher */

        public static function shortcode_rs_redeem_vouchercode() {
            if ( get_option( 'rs_gift_voucher_activated' ) != 'yes' )
                return ;

            if ( is_user_logged_in() ) {
                RSGiftVoucherFrontend::giftvoucherfield() ;
            } else {
                $MyAcclink = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ;
                $LoginLink = add_query_arg( 'redirect_to' , get_permalink() , $MyAcclink ) ;
                ob_start() ;
                ?>
                <a href="<?php echo $LoginLink ; ?>"><?php echo addslashes( get_option( 'rs_redeem_voucher_login_link_label' ) ) ; ?></a>
                <?php
                $Msg       = str_replace( "[rs_login_link]" , ob_get_clean() , get_option( "rs_voucher_redeem_guest_error_message" ) ) ;
                return $Msg ;
            }
        }

        /* Shortcode to display Available Points with label */

        public static function shortcode_sumo_current_balance() {
            $PointsData = new RS_Points_Data( get_current_user_id() ) ;
            $Points     = $PointsData->total_available_points() ;
            return "<div id='current_points_caption'><b>" . get_option( 'rs_current_available_balance_caption' ) . "</b> " . $Points . "</div>" ;
        }

        /* Shortcode to display Available Points without label */

        public static function available_points_without_caption() {
            $PointsData = new RS_Points_Data( get_current_user_id() ) ;
            $Points     = $PointsData->total_available_points() ;
            return "<strong>" . $Points . "</strong>" ;
        }

        /* Shortcode to display Booking Points */

        public static function shortcode_sumobookingpoints() {
            if ( ! class_exists( 'WC_Bookings' ) )
                return ;

            global $post ;
            $ProductObj = srp_product_object( $post->ID ) ;
            if ( ! is_object( $ProductObj ) )
                return ;

            if ( srp_product_type( $post->ID ) != 'booking' )
                return ;

            $args   = array(
                'productid'   => $post->ID ,
                'variationid' => $post->ID ,
                'item'        => array( 'qty' => '1' ) ,
                    ) ;
            $Points = check_level_of_enable_reward_point( $args ) ;
            return round_off_type( $Points ) ;
        }

        /* Shortcode to display Booking Points Value */

        public static function shortcode_bookingrspoint() {
            global $totalrewardpoints ;
            global $producttitle ;
            global $bookingvalue ;
            $ProductObj = srp_product_object( $producttitle ) ;
            if ( ! is_object( $ProductObj ) )
                return ;

            if ( srp_product_type( $producttitle ) != 'booking' )
                return ;

            if ( get_post_meta( $bookingvalue[ 'product_id' ] , '_rewardsystemcheckboxvalue' , true ) != 'yes' ) {
                return "<strong>0</strong>" ;
            } else {
                return round_off_type( $totalrewardpoints ) ;
            }
        }

        /* Shortcode to display Booking Points as Amount */

        public static function shortcode_equalbookingamount() {
            $Points         = do_shortcode( '[bookingrspoint]' ) ;
            $ConvertedValue = redeem_point_conversion( $Points , get_current_user_id() , 'price' ) ;
            return srp_formatted_price( round_off_type( $ConvertedValue ) ) ;
        }

        /* Shortcode to display Booking Product Title */

        public static function shortcode_bookingproducttitle() {
            global $producttitle ;
            global $bookingvalue ;
            $ProductObj = srp_product_object( $producttitle ) ;
            if ( is_object( $ProductObj ) && srp_product_type( $producttitle ) == 'booking' )
                return "<strong>" . get_the_title( $bookingvalue[ 'product_id' ] ) . "</strong>" ;
        }

        /* Shortcode to display Subscribe Field */

        public static function subscribe_field() {
            if ( get_option( 'rs_email_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_reward_content_shortcode' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_your_subscribe_link_shortcode' ) == 2 )
                return ;

            RSFunctionForEmailTemplate::field_for_subcribe( true ) ;
        }

        /* Shortcode to display Reward Log */

        public static function reward_log() {
            if ( get_option( 'rs_reward_content_shortcode' ) != 'yes' )
                return ;

            if ( get_option( 'rs_my_reward_table_shortcode' ) == 2 )
                return ;

            $TableData = array(
                'points_log_sort'        => get_option( 'rs_points_log_sorting_shortcode' ) ,
                'search_box'             => get_option( 'rs_show_hide_search_box_in_my_rewards_table_shortcode' ) ,
                'sno'                    => get_option( 'rs_my_reward_points_s_no_shortcode' ) ,
                'points_expiry'          => get_option( 'rs_my_reward_points_expire_shortcode' ) ,
                'username'               => get_option( 'rs_my_reward_points_user_name_hide_shortcode' ) ,
                'reward_for'             => get_option( 'rs_my_reward_points_reward_for_hide_shortcode' ) ,
                'earned_points'          => get_option( 'rs_my_reward_points_earned_points_hide_shortcode' ) ,
                'redeemed_points'        => get_option( 'rs_my_reward_points_redeemed_points_hide_shortcode' ) ,
                'total_points'           => get_option( 'rs_my_reward_points_total_points_hide_shortcode' ) ,
                'earned_date'            => get_option( 'rs_my_reward_points_earned_date_hide_shortcode' ) ,
                'page_size'              => get_option( 'rs_show_hide_page_size_my_rewards_shortcode' ) ,
                'points_label_position'  => get_option( 'rs_reward_point_label_position_shortcode' ) ,
                'total_points_label'     => get_option( 'rs_my_rewards_total_shortcode' ) ,
                'display_currency_value' => get_option( 'rs_reward_currency_value_shortcode' ) ,
                'my_reward_label'        => get_option( 'rs_my_rewards_title_shortcode' ) ,
                'label_sno'              => get_option( 'rs_my_rewards_sno_label_shortcode' ) ,
                'label_username'         => get_option( 'rs_my_rewards_userid_label_shortcode' ) ,
                'label_reward_for'       => get_option( 'rs_my_rewards_rewarder_label_shortcode' ) ,
                'label_earned_points'    => get_option( 'rs_my_rewards_points_earned_label_shortcode' ) ,
                'label_redeemed_points'  => get_option( 'rs_my_rewards_redeem_points_label_shortcode' ) ,
                'label_total_points'     => get_option( 'rs_my_rewards_total_points_label_shortcode' ) ,
                'label_earned_date'      => get_option( 'rs_my_rewards_date_label_shortcode' ) ,
                'label_points_expiry'    => get_option( 'rs_my_rewards_points_expired_label_shortcode' ) ,
                    ) ;
            echo RSFunctionForMessage::reward_log_table( $TableData ) ;
        }

        /* Shortcode to display Nominee Tabel */

        public static function nominee_field() {
            if ( get_option( 'rs_nominee_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_reward_content_shortcode' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_nominee_field_shortcode' ) == 2 )
                return ;

            $NomineeData = array(
                'usertype' => get_option( 'rs_select_type_of_user_for_nominee_shortcode' ) ,
                'userlist' => get_option( 'rs_select_users_list_for_nominee_shortcode' ) ,
                'title'    => get_option( 'rs_my_nominee_title_shortcode' , 'My Nominee' ) ,
                'name'     => get_option( 'rs_select_type_of_user_for_nominee_name_shortcode' ) ,
                'userrole' => get_option( 'rs_select_users_role_for_nominee_shortcode' ) ,
                    ) ;
            RSFunctionForNominee::nominee_field( 'myaccount' , $NomineeData ) ;
        }

        /* Shortcode to display Order Status */

        public static function shortcode_rs_order_status() {

            $earning_order_statuses = get_option( 'rs_order_status_control' ) ;
            if ( ! srp_check_is_array( $earning_order_statuses ) ) {
                return '' ;
            }

            $wc_order_statuses = fp_order_status() ;
            $selected_statues  = array() ;
            foreach ( $earning_order_statuses as $order_status ) {
                $selected_statues[] = isset( $wc_order_statuses[ $order_status ] ) ? $wc_order_statuses[ $order_status ] : '' ;
            }

            return '{' . implode( "," , $selected_statues ) . '}' ;
        }

        /* Shortcode to display the action that can earn points through SUMO Reward Points */

        public static function shortcode_rs_list_enable_options() {
            global $wpdb ;
            $TableName = $wpdb->prefix . 'postmeta' ;
            if ( get_option( 'rs_product_purchase_activated' ) == 'yes' ) {
                $CheckIfProductPurchaseEnabled = $wpdb->get_results( "SELECT meta_value FROM $TableName WHERE meta_key='_rewardsystemcheckboxvalue' AND meta_value='yes' " , ARRAY_A ) ;
                if ( ! empty( $CheckIfProductPurchaseEnabled ) )
                    echo '<br>' . get_option( 'rs_msg_for_product_puchase' ) . '<br>' ;

                $CheckIfBuyPointsEnabled = $wpdb->get_results( "SELECT meta_value FROM $TableName WHERE meta_key='_rewardsystem_buying_reward_points' AND meta_value='yes' " , ARRAY_A ) ;
                if ( ! empty( $CheckIfBuyPointsEnabled ) )
                    echo '<br>' . get_option( 'rs_msg_for_buying_reward_points' ) . '<br>' ;
            }
            if ( get_option( 'rs_referral_activated' ) == 'yes' ) {
                $CheckIfRefProductPurchaseEnabled = $wpdb->get_results( "SELECT meta_value FROM $TableName WHERE meta_key='_rewardsystemreferralcheckboxvalue' AND meta_value='yes' " , ARRAY_A ) ;
                if ( ! empty( $CheckIfRefProductPurchaseEnabled ) ) {
                    echo '<br>' . get_option( 'rs_msg_for_referral_system_product_purcase' ) . '<br>' ;
                    echo '<br>' . get_option( 'rs_msg_for_getting_refer_product_purchase' ) . '<br>' ;
                }
            }
            if ( get_option( 'rs_social_reward_activated' ) == 'yes' ) {
                $CheckIfSocialActionEnabled = $wpdb->get_results( "SELECT meta_value FROM $TableName WHERE meta_key='_socialrewardsystemcheckboxvalue' AND meta_value='yes' " , ARRAY_A ) ;
                if ( ! empty( $CheckIfSocialActionEnabled ) )
                    echo '<br>' . get_option( 'rs_msg_for_social_promotion' ) . '<br>' ;

                if ( get_option( 'rs_global_social_enable_disable_reward_post' ) == '1' )
                    echo '<br>' . get_option( 'rs_msg_for_social_promotion_for_post' ) . '<br>' ;
            }
            if ( get_option( 'rs_reward_action_activated' ) == 'yes' ) {
                if ( get_option( '_rs_enable_signup' ) == 'yes' && get_option( 'rs_reward_signup' ) != '' ) {
                    $SignUpMsg = str_replace( '[rssignuppoints]' , round_off_type( get_option( 'rs_reward_signup' ) ) , get_option( 'rs_msg_for_account_signup' ) ) ;
                    echo '<br>' . $SignUpMsg . '<br>' ;
                }
                if ( get_option( 'rs_enable_product_review_points' ) == 'yes' && get_option( 'rs_reward_product_review' ) != '' ) {
                    $ProReviewMsg = str_replace( '[rsreviewpoints]' , round_off_type( get_option( 'rs_reward_product_review' ) ) , get_option( 'rs_msg_for_product_review' ) ) ;
                    echo '<br>' . $ProReviewMsg . '<br>' ;
                }
                if ( get_option( 'rs_reward_for_comment_Post' ) == 'yes' && get_option( 'rs_reward_post_review' ) != '' ) {
                    $PostCommentmsg = str_replace( '[rspostpoints]' , round_off_type( get_option( 'rs_reward_post_review' ) ) , get_option( 'rs_msg_for_post_review' ) ) ;
                    echo '<br>' . $PostCommentmsg . '<br>' ;
                }
                if ( get_option( 'rs_reward_for_Creating_Post' ) == 'yes' && get_option( 'rs_reward_post' ) != '' ) {
                    $PostCreationMsg = str_replace( '[rspostcreationpoints]' , round_off_type( get_option( 'rs_reward_post' ) ) , get_option( 'rs_msg_for_post_creation' ) ) ;
                    echo '<br>' . $PostCreationMsg . '<br>' ;
                }
                if ( get_option( 'rs_reward_for_comment_Page' ) == 'yes' && get_option( 'rs_reward_page_review' ) != '' ) {
                    $PageCommentMsg = str_replace( '[rspagecommentpoints]' , round_off_type( get_option( 'rs_reward_page_review' ) ) , get_option( 'rs_msg_for_page_comment' ) ) ;
                    echo '<br>' . $PageCommentMsg . '<br>' ;
                }
                if ( get_option( 'rs_reward_for_enable_product_create' ) == 'yes' && get_option( 'rs_reward_Product_create' ) != '' ) {
                    $ProductCreationMsg = str_replace( '[rsproductcreatepoints]' , round_off_type( get_option( 'rs_reward_Product_create' ) ) , get_option( 'rs_msg_for_create_product' ) ) ;
                    echo '<br>' . $ProductCreationMsg . '<br>' ;
                }
                if ( get_option( 'rs_enable_reward_points_for_login' ) == 'yes' && get_option( 'rs_enable_reward_points_for_login' ) != '' ) {
                    $LoginMsg = str_replace( '[rsloginpoints]' , round_off_type( get_option( 'rs_reward_points_for_login' ) ) , get_option( 'rs_msg_for_daily_login' ) ) ;
                    echo '<br>' . $LoginMsg . '<br>' ;
                }
                if ( get_option( 'rs_referral_reward_signup' ) != '' ) {
                    $RefSignUpMsg = str_replace( '[rsreferralpoints]' , round_off_type( get_option( 'rs_referral_reward_signup' ) ) , get_option( 'rs_msg_for_referral_system_login' ) ) ;
                    echo '<br>' . $RefSignUpMsg . '<br>' ;
                }
            }
        }

        /* Shortcode to display Redeemed Points */

        public static function shortcode_for_redeemedpoints( $tag ) {
            if ( ! is_user_logged_in() )
                return ;

            $UserInfo   = get_user_by( 'id' , get_current_user_id() ) ;
            $UserName   = $UserInfo->user_login ;
            $PointsData = new RS_Points_Data( get_current_user_id() ) ;
            $Points     = $PointsData->total_available_points() ;
            $AutoRedeem = 'auto_redeem_' . strtolower( $UserName ) ;
            $Redeem     = 'sumo_' . strtolower( "$UserName" ) ;
            if ( isset( WC()->cart->coupon_discount_amounts[ "$Redeem" ] ) ) {
                $CouponAmnt = WC()->cart->coupon_discount_amounts[ "$Redeem" ] ;
                $TaxAmnt    = isset( WC()->cart->coupon_discount_tax_amounts[ "$Redeem" ] ) ? WC()->cart->coupon_discount_tax_amounts[ "$Redeem" ] : 0 ;
            }
            if ( isset( WC()->cart->coupon_discount_amounts[ "$AutoRedeem" ] ) ) {
                $CouponAmnt = WC()->cart->coupon_discount_amounts[ "$AutoRedeem" ] ;
                $TaxAmnt    = isset( WC()->cart->coupon_discount_tax_amounts[ "$AutoRedeem" ] ) ? WC()->cart->coupon_discount_tax_amounts[ "$AutoRedeem" ] : 0 ;
            }

            $CouponAmnt     = get_option( 'woocommerce_tax_display_cart' ) == 'incl' ? ($CouponAmnt + $TaxAmnt) : $CouponAmnt ;
            $ConvertedValue = redeem_point_conversion( $CouponAmnt , get_current_user_id() ) ;
            $ConvertedValue = (($ConvertedValue > $Points) ? $Points : $ConvertedValue) ;
            $ConvertedValue = ($tag == 'redeemeduserpoints') ? (($Points >= $ConvertedValue) ? ($Points - $ConvertedValue) : $ConvertedValue) : $ConvertedValue ;
            return get_option( 'rs_enable_round_off_type_for_calculation' ) == 'yes' ? $ConvertedValue : round_off_type( $ConvertedValue ) ;
        }

        /* Shortcode to display Buying Points */

        public static function shortcode_buypoint() {
            global $buying_pointsnew ;
            global $producttitle ;
            $buying_points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) ( $buying_pointsnew[ $producttitle ] ) ) ;
            return round_off_type( $buying_points ) ;
        }

        public static function shortcode_buypointvalues() {
            global $buying_pointsnew ;
            global $producttitle ;
            $buying_points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) ( $buying_pointsnew[ $producttitle ] ) ) ;
            $updatedvalue  = redeem_point_conversion( $buying_points , get_current_user_id() , 'price' ) ;
            return srp_formatted_price( round_off_type_for_currency( $updatedvalue ) ) ;
        }

        /* Shortcode to display Buying Points in Single Product Page */

        public static function shortcode_buypoints() {
            global $post ;
            $variation_ids = get_variation_id( $post->ID ) ;
            if ( srp_check_is_array( $variation_ids ) ) {
                foreach ( $variation_ids as $eachvariation ) {
                    if ( get_post_meta( $eachvariation , '_rewardsystem_buying_reward_points' , true ) != '1' )
                        continue ;

                    if ( get_post_meta( $eachvariation , '_rewardsystem_assign_buying_points' , true ) == '' )
                        continue ;

                    $buying_points = get_post_meta( $eachvariation , '_rewardsystem_assign_buying_points' , true ) ;
                    $buying_points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $buying_points ) ;
                    return round_off_type( $buying_points ) ;
                }
            } else {
                $buying_points = get_post_meta( $post->ID , '_rewardsystem_assign_buying_points' , true ) ;
                $buying_points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $buying_points ) ;
                return round_off_type( $buying_points ) ;
            }
        }

        /* Shortcode to display Buying Points Value */

        public static function shortcode_buypointvalue() {
            global $post ;
            $buying_points = get_post_meta( $post->ID , '_rewardsystem_assign_buying_points' , true ) ;
            $buying_points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $buying_points ) ;

            /* Commented this code when thousand separator value given as '.' causes display issue */
//            $buying_points = str_replace( wc_get_price_thousand_separator() , '' , round_off_type( $buying_points ) ) ;

            $buying_points = round_off_type( $buying_points ) ;
            $updatedvalue  = redeem_point_conversion( $buying_points , get_current_user_id() , 'price' ) ;
            return srp_formatted_price( round_off_type_for_currency( $updatedvalue ) ) ;
        }

        /* Shortcode to display Referral Points for Payment Plan Product */

        public static function shortcode_rs_referral_payment_plan() {
            global $ref_pdt_plan ;
            $Points = srp_check_is_array( $ref_pdt_plan ) ? round_off_type( array_sum( $ref_pdt_plan ) ) : 0 ;
            return $Points ;
        }

        /* Shortcode to display Referral Points for Product */

        public static function shortcode_referralpoints() {
            global $referral_pointsnew ;
            global $producttitle ;
            return round_off_type( $referral_pointsnew[ $producttitle ] ) ;
        }

        /* Shortcode to display Points for Product */

        public static function shortcode_rspoint() {
            global $totalrewardpoints ;
            $totalrewardpoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $totalrewardpoints ) ;
            return round_off_type( $totalrewardpoints ) ;
        }

        /* Shortcode to display Product Title for Buying Points */

        public static function shortcode_titleofproduct() {
            global $producttitle ;
            $ProductObj = srp_product_object( $producttitle ) ;
            if ( ! is_object( $ProductObj ) )
                return ;

            return "<strong>" . get_the_title( $producttitle ) . "</strong>" ;
        }

        /* Shortcode to display Currency Value of Points */

        public static function shortcode_carteachvalue() {
            global $totalrewardpoints ;

            /* Commented this code when thousand separator value given as '.' causes display issue */
//            $Points        = str_replace( wc_get_price_thousand_separator() , '' , $totalrewardpoints ) ;

            $CurrencyValue = redeem_point_conversion( $totalrewardpoints , get_current_user_id() , 'price' ) ;
            return srp_formatted_price( round_off_type_for_currency( $CurrencyValue ) ) ;
        }

        /* Shortcode to display Minimum Redeem Points */

        public static function shortcode_for_min_redeem_point() {
            return get_option( 'rs_minimum_redeeming_points' ) ;
        }

        /* Shortcode to display Maximum Redeem Points */

        public static function shortcode_rsmaximumpoints() {
            return get_option( 'rs_maximum_redeeming_points' ) ;
        }

        /* Shortcode to display Username */

        public static function shortcode_rs_user_name() {
            $UserName = get_user_by( 'id' , get_current_user_id() )->display_name ;
            return $UserName ;
        }

        /* Shortcode to display Points which are all pending in an order */

        public static function shortcode_rs_list_of_orders_with_pending_points() {
            ?>
            <p>
                <label><?php _e( 'Page Size:' , SRP_LOCALE ) ; ?></label>
                <select id="change-page-sizesss">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </p>
            <table class = "list_of_orders demo shop_table my_account_orders table-bordered" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">
                <thead>
                    <tr>
                        <th data-type="Numeric"><?php _e( 'S.No' , SRP_LOCALE ) ; ?></th> 
                        <th data-type="Numeric"><?php _e( 'User name' , SRP_LOCALE ) ; ?></th>
                        <th data-type="Numeric"><?php _e( 'Status' , SRP_LOCALE ) ; ?></th>
                        <th data-type="Numeric"><?php _e( 'Description' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $WCOrderStatus   = array_keys( wc_get_order_statuses() ) ;
                    $i               = 1 ;
                    $SUMOOrderStatus = get_option( 'rs_order_status_control' ) ;
                    $SUMOOrderStatus = (srp_check_is_array( $SUMOOrderStatus )) ? $SUMOOrderStatus : array() ;
                    $Status          = array() ;
                    foreach ( $WCOrderStatus as $WCStatus ) {
                        $WCStatus = str_replace( 'wc-' , '' , $WCStatus ) ;
                        if ( ! in_array( $WCStatus , $SUMOOrderStatus ) )
                            $Status[] = 'wc-' . $WCStatus ;
                    }
                    $args      = array(
                        'post_type'     => 'shop_order' ,
                        'numberposts'   => '-1' ,
                        'meta_query'    => array(
                            array(
                                'key'     => 'reward_points_awarded' ,
                                'compare' => 'NOT EXISTS'
                            ) ,
                            array(
                                'key'     => 'rs_points_for_current_order_as_value' ,
                                'value'   => 0 ,
                                'compare' => '>'
                            ) ,
                            array(
                                'key'     => '_customer_user' ,
                                'value'   => get_current_user_id() ,
                                'compare' => '='
                            ) ,
                        ) ,
                        'post_status'   => $Status ,
                        'fields'        => 'ids' ,
                        'cache_results' => false
                            ) ;
                    $OrderList = get_posts( $args ) ;
                    foreach ( $OrderList as $OrderId ) {
                        $OrderObj    = new WC_Order( $OrderId ) ;
                        $OrderObj    = srp_order_obj( $OrderObj ) ;
                        $OrderStatus = $OrderObj[ 'order_status' ] ;
                        $Firstname   = $OrderObj[ 'first_name' ] ;
                        $Points      = ( float ) get_post_meta( $OrderId , 'rs_points_for_current_order_as_value' , true ) ;
                        if ( $Points > 0 ) {
                            echo self::order_status_settings( $OrderId , $OrderStatus , $Firstname , $i , $Points , $SUMOOrderStatus ) ;
                            $i ++ ;
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr style = "clear:both;">
                        <td colspan = "4">
                            <div class = "pagination pagination-centered"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <script type="text/javascript">
                jQuery( document ).ready( function () {
                    jQuery( '.list_of_orders' ).footable() ;
                    jQuery( '#change-page-sizesss' ).change( function ( e ) {
                        e.preventDefault() ;
                        var pageSize = jQuery( this ).val() ;
                        jQuery( '.footable' ).data( 'page-size' , pageSize ) ;
                        jQuery( '.footable' ).trigger( 'footable_initialized' ) ;
                    } ) ;

                } ) ;
            </script>
            <?php
        }

        public static function order_status_settings( $OrderId , $OrderStatus , $Firstname , $i , $Points , $OrderList ) {
            $MyAccLink          = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ;
            $OrderLink          = esc_url_raw( add_query_arg( 'view-order' , $OrderId , $MyAccLink ) ) ;
            $OrderLink          = '<a href="' . $OrderLink . '">#' . $OrderId . '</a>' ;
            $OrderStatusToReach = ucfirst( implode( ',' , $OrderList ) ) ;
            $Message            = __( "Currently, the order status is in [status]. Once the order status reached to the [order_status_to_reach], [reward_points] Points for purchasing the product(s) in this order([order_id]) will be added to your account" , SRP_LOCALE ) ;
            $ReplaceMsg         = str_replace( '[reward_points]' , $Points , str_replace( '[order_id]' , $OrderLink , str_replace( '[status]' , ucfirst( $OrderStatus ) , $Message ) ) ) ;
            $ReplaceMsg         = str_replace( '[order_status_to_reach]' , $OrderStatusToReach , $ReplaceMsg ) ;
            ?>
            <tr>
                <td data-value="<?php echo $i ; ?>"><?php echo $i ; ?></td>  
                <td><?php echo $Firstname ?> </td> 
                <td><?php echo ucfirst( $OrderStatus ) ; ?></td>
                <td><?php echo $ReplaceMsg ; ?></td> <?php ?>
            </tr>
            <?php
        }

        /* Shortcode to display Currency Value of Available Points */

        public static function currency_value_for_available_points( $tag ) {
            $PointsData    = new RS_Points_Data( get_current_user_id() ) ;
            $Points        = $PointsData->total_available_points() ;
            $CurrencyValue = redeem_point_conversion( $Points , get_current_user_id() , 'price' ) ;
            return ($tag == 'my_userpoints_value') ? get_option( 'rs_label_shortcode' ) . ' ' . srp_formatted_price( round_off_type_for_currency( $CurrencyValue ) ) : srp_formatted_price( round_off_type_for_currency( $CurrencyValue ) ) ;
        }

        /* Shortcode to display point on hold */

        public static function shortcode_rs_points_on_hold() {
            global $totalrewardpoints_payment_plan ;
            global $buying_pts_payment_plan ;
            $PaymentPoints = srp_check_is_array( $totalrewardpoints_payment_plan ) ? round_off_type( array_sum( $totalrewardpoints_payment_plan ) ) : 0 ;
            $BuyingPoints  = srp_check_is_array( $buying_pts_payment_plan ) ? round_off_type( array_sum( $buying_pts_payment_plan ) ) : 0 ;
            return ($PaymentPoints + $BuyingPoints) ;
        }

        /* Shortcode to display Total Reward */

        public static function shortcode_totalrewards() {
            $Points = total_points_for_current_purchase( WC()->cart->total , get_current_user_id() ) ;
            return round_off_type( $Points ) ;
        }

        /* Shortcode to display Total Reward Value */

        public static function shortcode_totalrewardsvalue() {
            $Points         = total_points_for_current_purchase( WC()->cart->total , get_current_user_id() ) ;
            $ConvertedValue = redeem_point_conversion( $Points , get_current_user_id() , 'price' ) ;
            return srp_formatted_price( round_off_type_for_currency( $ConvertedValue ) ) ;
        }

        /* Shortcode to display Balance Price */

        public static function shortcode_balanceprice() {

            /* Commented this code when thousand separator value given as '.' causes display issue */
//            $TotalPoints    = str_replace( wc_get_price_thousand_separator() , '' , do_shortcode( '[redeemeduserpoints]' ) ) ;

            $TotalPoints    = do_shortcode( '[redeemeduserpoints]' ) ;
            $ConvertedValue = redeem_point_conversion( $TotalPoints , get_current_user_id() , 'price' ) ;
            return srp_formatted_price( round_off_type_for_currency( $ConvertedValue ) ) ;
        }

        /* Shortcode to display link for login */

        public static function shortcode_loginlink() {
            $MyAccLink  = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ;
            $MyAccTitle = get_the_title( get_option( 'woocommerce_myaccount_page_id' ) ) ;
            return '<a href="' . $MyAccLink . '">' . $MyAccTitle . '</a>' ;
        }

        /* Shortcode to display Referral List */

        public static function shortcode_for_referral_list_table() {
            if ( get_option( 'rs_referral_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_reward_content_shortcode' ) != 'yes' )
                return ;

            if ( check_if_referral_is_restricted() ) {
                $TableData = array(
                    'show_table'           => get_option( 'rs_show_hide_referal_table_shortcode' ) ,
                    'sno_label'            => get_option( 'rs_my_referal_sno_label_shortcode' ) ,
                    'userid_or_email'      => get_option( 'rs_select_option_for_referral_shortcode' ) ,
                    'userid_label'         => get_option( 'rs_my_referal_userid_label_shortcode' ) ,
                    'email_id'             => get_option( 'rs_referral_email_ids_shortcode' ) ,
                    'total_referral_label' => get_option( 'rs_my_total_referal_points_label_shortcode' ) ,
                    'title_table'          => get_option( 'rs_referal_table_title_shortcode' ) ,
                        ) ;
                RSFunctionForReferralSystem::referral_list_table( $TableData , true ) ;
            } else {
                if ( get_option( 'rs_display_msg_when_access_is_prevented' ) === '1' )
                    echo '<br>' . get_option( 'rs_msg_for_restricted_user' ) ;
            }
        }

        /* Shortcode to display Static Referral Link */

        public static function shortcode_for_static_referral_link() {
            if ( get_option( 'rs_referral_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_reward_content_shortcode' ) != 'yes' )
                return ;

            if ( check_if_referral_is_restricted() ) {
                if ( ! check_referral_count_if_exist( get_current_user_id() ) ) {
                    _e( "<p>Since you have reached the referral link usage, you don't have the access to refer anymore</p>" , SRP_LOCALE ) ;
                } else {
                    ob_start() ;
                    RSFunctionForReferralSystem::static_url() ;
                    return ob_get_contents() ;
                }
            } else {
                if ( get_option( 'rs_display_msg_when_access_is_prevented' ) === '1' )
                    echo '<br>' . get_option( 'rs_msg_for_restricted_user' ) ;
            }
        }

        public static function shortcode_for_total_points() {
            ob_start() ;
            $PointsData = new RS_Points_Data( get_current_user_id() ) ;
            $Points     = $PointsData->total_available_points() ;
            echo get_option( 'rs_my_rewards_total' ) . " " . round_off_type( number_format( ( float ) $Points , 2 , '.' , '' ) ) . "</h4>" ;
            return ob_get_clean() ;
        }

        public static function shortcode_fppoint() {
            $Points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) get_option( 'rs_reward_points_for_first_purchase_in_fixed' ) ) ;
            return round_off_type( $Points ) ;
        }

        public static function shortcode_fppointvalue() {
            $Points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) get_option( 'rs_reward_points_for_first_purchase_in_fixed' ) ) ;

            /* Commented this code when thousand separator value given as '.' causes display issue */
//            $Points         = str_replace( wc_get_price_thousand_separator() , '' , $Points ) ;

            $ConvertedValue = redeem_point_conversion( $Points , get_current_user_id() , 'price' ) ;
            return srp_formatted_price( round_off_type_for_currency( $ConvertedValue ) ) ;
        }

        public static function shortcode_redeeming_threshold_value() {
            if ( get_option( 'rs_redeeming_activated' ) == 'no' && get_option( 'rs_max_redeem_discount' ) == '1' && get_option( 'rs_percent_max_redeem_discount' ) == '' )
                return ;

            $PointsData      = new RS_Points_Data( get_current_user_id() ) ;
            $AvailablePoints = $PointsData->total_available_points() ;
            $RedeemPercent   = RSMemberFunction::redeem_points_percentage( get_current_user_id() ) ;
            $PriceValue      = srp_cart_subtotal() * ( float ) get_option( 'rs_percent_max_redeem_discount' ) / 100 ;
            $Value           = $PriceValue / $RedeemPercent ;
            if ( $Value <= $AvailablePoints )
                return '<b>' . $Value . '</b>' . '( ' . wc_price( $PriceValue ) . ')' ;
        }

        /* Shortcode to display Send Points form */

        public static function shortcode_for_send_points() {
            if ( get_option( 'rs_send_points_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_enable_msg_for_send_point' ) == 2 )
                return ;

            $PointsData = new RS_Points_Data( get_current_user_id() ) ;
            $Points     = $PointsData->total_available_points() ;
            if ( $Points == 0 )
                return get_option( 'rs_msg_when_user_have_no_points' ) ;

            wp_enqueue_script( 'formforsendpoints' , false , array() , '' , true ) ;

            ob_start() ;
            ?>
            <form id="sendpoint_form" method="post" enctype="multipart/form-data">
                <table>
                    <tr>
                        <th>
                            <label><?php echo get_option( "rs_total_send_points_request" ) ; ?></label>
                        </th>
                        <td style="border:none;padding: 6px 10px 6px 10px;">
                            <input style="min-width:250px !important;height:30px !important;" type = "text" id = "rs_total_send_points_request" name = "rs_total_send_points_request" readonly="readonly" value="<?php echo $Points ; ?>">
                            <div class = "points_more_than_current_points"></div>
                        </td>
                    </tr>
                    <tr>
                        <?php if ( '1' == get_option( 'rs_send_points_user_selection_field' , 1 ) ) : ?>
                            <th>
                                <label><?php echo get_option( "rs_select_user_label" ) ; ?></label>
                            </th>
                            <td style="border:none;padding: 6px 10px 6px 10px;">
                                <?php
                                global $woocommerce ;
                                if ( ( float ) $woocommerce->version < ( float ) '3.0' ) {
                                    ?>
                                    <input id="select_user_ids" type="text" placeholder="<?php echo get_option( 'rs_select_user_placeholder' ) ; ?>" style="font-size:14px;min-width:250px !important;height:30px !important;"/>
                                <?php } else { ?>
                                    <select id="select_user_ids" name="select_user_ids"  data-placeholder="<?php echo get_option( 'rs_select_user_placeholder' ) ; ?>" style="min-width:250px !important;height:30px !important;" data-allow_clear="true" ></select>
                                <?php } ?>
                                <div class = "error_empty_user" ></div>
                            </td>
                        <?php else: ?>
                            <th>
                                <label><?php echo wp_kses_post( get_option( "rs_send_points_username_field_label" ) ) ; ?></label>
                            </th>
                            <td style="border:none;padding: 6px 10px 6px 10px;">
                                <input type ="text" placeholder="<?php echo esc_attr( get_option( 'rs_send_points_username_placeholder' ) ) ; ?>" class ="rs_user_name_field" style="min-width:250px !important;height:30px !important;">
                                <div class = "error_empty_user" ></div>
                            </td>
                        <?php endif ; ?>
                    </tr>
                    <tr>
                        <th>
                            <label><?php echo get_option( "rs_points_to_send_request" ) ; ?></label>
                        </th>
                        <td style="border:none;padding: 6px 10px 6px 10px;">
                            <input style="min-width:250px !important;height:30px !important;" type = "text" id = "rs_total_reward_value_send" name = "rs_total_reward_value_send" value=""/>
                            <div class = "error_points_not_number" ></div>
                            <div class = "error_greater_than_limit"> </div>
                            <div class = "error_point_empty"></div>
                            <div class = "points_less_than_current_points"> </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label><?php echo get_option( "rs_reason_for_send_points" ) ; ?></label>
                        </th>
                        <td style="border:none;padding: 6px 10px 6px 10px;">
                            <input style="min-width:250px !important;height:30px !important;" type = "text" id = "rs_reason_for_send_points" name = "rs_reason_for_send_points" placeholder="<?php echo addslashes( get_option( 'rs_reason_for_send_points' ) ) ; ?>"  value=""/>
                            <div class = "rs_reason_for_send_points"> </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border:none;padding: 6px 10px 6px 10px;">
                            <div class = "success_info"></div>
                        </td>
                    </tr>
                    <tr>
                        <td  style="border:none;padding: 6px 10px 6px 10px;">
                            <input type = "submit" name= "rs_send_points_submit_button" value="<?php echo addslashes( get_option( "rs_select_points_submit_label" ) ) ; ?>" id="rs_send_points_submit_button"/>
                        </td>
                    </tr>
                </table>                                                                                                                                                                                               
            </form>
            <?php
            return ob_get_clean() ;
        }

    }

    RS_Rewardsystem_Shortcodes::init() ;
}