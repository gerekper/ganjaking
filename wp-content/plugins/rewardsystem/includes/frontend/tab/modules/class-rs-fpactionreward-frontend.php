<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSActionRewardModule' ) ) {

    class RSActionRewardModule {

        public static function init() {
            add_action( 'wp_insert_post' , array( __CLASS__ , 'award_points_for_blog_post_creation' ) , 10 , 3 ) ;

            add_action( 'user_register' , array( __CLASS__ , 'award_points_for_account_signup' ) , 10 , 1 ) ;

            add_action( 'wp_head' , array( __CLASS__ , 'reward_points_for_login' ) ) ;

            add_action( 'fpsl_loggedin_successfully' , array( __CLASS__ , 'award_points_for_social_login' ) , 10 , 3 ) ;

            add_action( 'fpsl_linked_successfully' , array( __CLASS__ , 'award_points_for_social_link' ) , 10 , 2 ) ;

            add_action( 'fpcf_cus_fields_after_save' , array( __CLASS__ , 'award_points_for_cus_fields' ) , 10 , 2 ) ;

            add_action( 'rs_award_points_for_datepicker_in_cusfields' , array( __CLASS__ , 'award_points_for_datepicker_in_cus_fields_repeatedly' ) , 10 , 4 ) ;

            if ( get_option( 'rs_message_before_after_cart_table' ) == '1' ) {
                add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'display_message_for_coupon_reward_points' ) , 999 ) ;
            } else {
                add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'display_message_for_coupon_reward_points' ) , 999 ) ;
            }

            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'display_message_for_coupon_reward_points' ) , 999 ) ;

            add_action( 'woocommerce_before_customer_login_form' , array( __CLASS__ , 'signup_msg_in_my_account' ) ) ;

            add_action( 'woocommerce_before_customer_login_form' , array( __CLASS__ , 'login_msg_in_my_account_page' ) ) ;

            add_action( 'bbp_new_topic_post_extras' , array( __CLASS__ , 'award_points_for_topic_creation_in_bbpress' ) , 10 , 1 ) ;

            add_action( 'bbp_new_reply_post_extras' , array( __CLASS__ , 'award_points_for_reply_in_bbpress' ) , 10 , 1 ) ;

            add_action( 'fp_wl_user_subscribed' , array( __CLASS__ , 'award_points_for_subscribing_waitlist' ) , 10 , 2 ) ;

            add_action( 'fp_wl_sale_converted' , array( __CLASS__ , 'award_points_for_waitlist_sale_converion' ) , 10 , 2 ) ;

            add_filter( 'woocommerce_gateway_description' , array( __CLASS__ , 'shortcode_for_gateway_description' ) , 10 , 2 ) ;
        }

        /* Shortcode for Gateway Description */

        public static function shortcode_for_gateway_description( $gateway_description , $gateway_id ) {

            if ( '1' === get_option( 'rs_reward_type_for_payment_gateways_' . $gateway_id , '1' ) ) {

                $gateway_point = get_option( 'rs_reward_payment_gateways_' . $gateway_id ) ;
                if ( ! $gateway_point ) {
                    return $gateway_description ;
                }
            } else {
                $percentage_value = get_option( 'rs_reward_points_for_payment_gateways_in_percent_' . $gateway_id ) ;
                if ( ! $percentage_value ) {
                    return $gateway_description ;
                }

                $cart_subtotal   = srp_cart_subtotal( true ) ;
                $point_coversion = ((( float ) $percentage_value / 100 ) * $cart_subtotal) ;
                $gateway_point   = earn_point_conversion( $point_coversion ) ;
            }

            $gateway_point       = sprintf( "<span class='rs_gateway_points'><b>%s</b></span>" , esc_html( $gateway_point ) ) ;
            $gateway_description = str_replace( '[gatewaypoints]' , $gateway_point , $gateway_description ) ;

            return $gateway_description ;
        }

        /* Award Points for Blog Post Creation */

        public static function award_points_for_blog_post_creation( $PostId , $Post , $update ) {
            if ( ! is_user_logged_in() )
                return ;

            $UserId  = get_current_user_id() ;
            $BanType = check_banning_type( $UserId ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            $visibility_type = is_object( $Post ) ? $Post->post_status : '' ;
            if ( empty( $visibility_type ) )
                return ;

            if ( $Post->post_type == 'shop_coupon' )
                return ;

            if ( get_option( 'rs_reward_for_Creating_Post' ) != 'yes' )
                return ;

            $PointsForPostCreation = get_option( 'rs_reward_post' ) ;
            if ( empty( $PointsForPostCreation ) )
                return ;

            if ( $visibility_type == 'publish' && get_option( 'rs_post_visible_for' ) == '1' ) {
                self::insert_blog_post_points( $PostId , $PointsForPostCreation ) ;
            } else if ( $visibility_type == 'private' && get_option( 'rs_post_visible_for' ) == '2' ) {
                self::insert_blog_post_points( $PostId , $PointsForPostCreation ) ;
            }
        }

        public static function insert_blog_post_points( $PostId , $PointsForPostCreation ) {
            if ( get_post_meta( $PostId , 'rewardpointsforblogpost' , true ) == "yes" )
                return ;

            $table_args = array(
                'user_id'           => get_current_user_id() ,
                'pointstoinsert'    => $PointsForPostCreation ,
                'checkpoints'       => 'RPFP' ,
                'totalearnedpoints' => $PointsForPostCreation ,
                'productid'         => $PostId ,
                    ) ;
            RSPointExpiry::insert_earning_points( $table_args ) ;
            RSPointExpiry::record_the_points( $table_args ) ;
            update_post_meta( $PostId , 'rewardpointsforblogpost' , 'yes' ) ;
        }

        public static function award_points_for_account_signup( $user_id ) {
            $BanType = check_banning_type( $user_id ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( get_option( 'rs_select_account_signup_points_award' ) == '1' ) {
                self::rs_add_registration_rewards_points( $user_id ) ;
            } else {
                if ( isset( $_COOKIE[ 'rsreferredusername' ] ) )
                    self::rs_add_registration_rewards_points( $user_id ) ;
            }
        }

        public static function rs_add_registration_rewards_points( $user_id ) {
            if ( get_post_meta( $user_id , 'rs_registered_user' , true ) != '' )
                return ;

            if ( (get_option( 'rs_reward_signup_after_first_purchase' ) == 'yes' ) ) {
                // After First Purchase Registration Points
                self::rs_add_regpoints_to_user_after_first_purchase( $user_id ) ;
            } else {
                // Instant Registration Points
                self::rs_add_regpoints_to_user_instantly( $user_id ) ;
            }
            do_action( 'fp_reward_point_for_registration' ) ;
            update_post_meta( $user_id , 'rs_registered_user' , 1 ) ;
        }

        /* Instant Registration Points */

        public static function rs_add_regpoints_to_user_instantly( $user_id ) {
            if ( get_option( '_rs_enable_signup' ) != 'yes' )
                return ;

            $pointstoinsert = get_option( 'rs_reward_signup' ) ;
            if ( $pointstoinsert == '' )
                return ;

            self::award_reg_points_instantly( $pointstoinsert , $user_id , $event_slug = 'RRP' , $Network    = '' ) ;
        }

        /* After First Purchase Registration Points */

        public static function rs_add_regpoints_to_user_after_first_purchase( $user_id ) {
            if ( get_option( '_rs_enable_signup' ) != 'yes' )
                return ;

            $pointstoaward = get_option( 'rs_reward_signup' ) ;
            if ( $pointstoaward == '' )
                return ;

            self::award_reg_points_after_first_purchase( $pointstoaward , $user_id , $event_slug = 'RRP' , $Network    = '' ) ;
        }

        public static function reward_points_for_login() {
            $pointsforlogin = get_option( 'rs_reward_points_for_login' ) ;
            self::insert_points_for_login( $pointsforlogin , 'LRP' , get_current_user_id() , '' ) ;
            do_action( 'fp_reward_point_for_login' ) ;
        }

        /* Award Reward Points for Social Login - Compatability with Social Login Plugin */

        public static function award_points_for_social_login( $UserId , $Network , $Type ) {
            $BanType = check_banning_type( $UserId ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            $network_name = fpsl_get_plugin_networks() ;
            $Network      = $network_name[ $Network ] ;
            if ( $Type == '2' || $Type == '3' ) {
                $pointsforsociallogin = get_option( 'rs_reward_for_social_network_login' ) ;
                self::insert_points_for_login( $pointsforsociallogin , $event_slug           = 'SLRP' , $UserId , $Network ) ;
            }
            if ( $Type == '1' ) {
                $pointsforsocialsignup = get_option( 'rs_reward_for_social_network_signup' ) ;
                self::award_points_for_signup( $pointsforsocialsignup , $UserId , $Network ) ;
            }
        }

        /* Insert Points for Login */

        public static function insert_points_for_login( $pointsforlogin , $event_slug , $userid , $Network = '' ) {
            if ( ! is_user_logged_in() )
                return ;

            $BanType = check_banning_type( $userid ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( ! allow_reward_points_for_user( $userid ) )
                return ;

            if ( get_option( 'rs_reward_action_activated' ) != 'yes' )
                return ;

            if ( get_option( 'rs_enable_reward_points_for_login' ) != 'yes' )
                return ;

            $strtotime   = array() ;
            $strtotime   = strtotime( date( 'y-m-d' ) ) ;
            $getusermeta = ( array ) get_user_meta( $userid , 'rs_login_date' , true ) ;
            if ( in_array( $strtotime , $getusermeta ) )
                return ;

            if ( empty( $pointsforlogin ) )
                return ;

            $new_obj = new RewardPointsOrder( 0 , 'no' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                $new_obj->check_point_restriction( $pointsforlogin , $pointsredeemed = 0 , $event_slug , $userid , $nomineeid      = '' , $referrer_id    = '' , $productid      = '' , $variationid    = '' , $reasonindetail = $Network ) ;
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $pointsforlogin , 'event_slug' => $event_slug , 'user_id' => $userid , 'reasonindetail' => $Network , 'totalearnedpoints' => $pointsforlogin ) ;
                $new_obj->total_points_management( $valuestoinsert ) ;
            }

            $oldlogindata = ( array ) get_user_meta( $userid , 'rs_login_date' , true ) ;
            $newdata      = ( array ) $strtotime ;
            $mergedata    = array_merge( $oldlogindata , $newdata ) ;
            update_user_meta( $userid , 'rs_login_date' , $mergedata ) ;
        }

        /* Award Points for Social Signup - Compatability with Social Login Plugin */

        public static function award_points_for_signup( $pointsforsocialsignup , $UserId , $Network ) {
            if ( ! allow_reward_points_for_user( $UserId ) )
                return ;

            if ( $pointsforsocialsignup == '' )
                return ;

            if ( get_option( 'rs_select_account_signup_points_award' ) == '1' ) {
                self::insert_point_for_social_signup( $UserId , $pointsforsocialsignup , $Network ) ;
            } else {
                if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
                    self::insert_point_for_social_signup( $UserId , $pointsforsocialsignup , $Network ) ;
                }
            }
        }

        /* Insert Points for Social Signup */

        public static function insert_point_for_social_signup( $user_id , $pointsforsocialsignup , $Network ) {
            $CheckAlreadyRegistedUser = get_post_meta( $user_id , 'rs_check_already_reg_user_through_social_login' , true ) ;
            if ( $CheckAlreadyRegistedUser != '' )
                return ;

            $AwardAfterFirstPurchase = get_option( 'rs_reward_signup_after_first_purchase' ) ;
            if ( ($AwardAfterFirstPurchase == 'yes' ) ) {
                // After First Purchase Registration Points for Social Signup
                self::award_reg_points_after_first_purchase( $pointsforsocialsignup , $user_id , $event_slug = 'SLRRP' , $Network ) ;
            } else {
                // Instant Registration Points for Social Signup
                self::award_reg_points_instantly( $pointsforsocialsignup , $user_id , $event_slug = 'SLRRP' , $Network ) ;
            }

            update_post_meta( $user_id , 'rs_check_already_reg_user_through_social_login' , 1 ) ;
        }

        public static function award_reg_points_instantly( $pointstoaward , $user_id , $event_slug , $Network ) {
            if ( ! allow_reward_points_for_user( $user_id ) )
                return ;

            $registration_points = RSMemberFunction::earn_points_percentage( $user_id , ( float ) $pointstoaward ) ;
            $restrictuserpoints  = get_option( 'rs_max_earning_points_for_user' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' && $restrictuserpoints != '' ) {
                $currentregistrationpoints = ($registration_points <= $restrictuserpoints) ? $registration_points : $restrictuserpoints ;
            } else {
                $currentregistrationpoints = $registration_points ;
            }
            $table_args = array(
                'user_id'           => $user_id ,
                'pointstoinsert'    => $currentregistrationpoints ,
                'checkpoints'       => $event_slug ,
                'totalearnedpoints' => $currentregistrationpoints ,
                'reason'            => $Network ,
                    ) ;
            RSPointExpiry::insert_earning_points( $table_args ) ;
            RSPointExpiry::record_the_points( $table_args ) ;
            add_user_meta( $user_id , '_points_awarded' , '1' ) ;
        }

        public static function award_reg_points_after_first_purchase( $pointstoaward , $user_id , $event_slug , $Network ) {
            global $wpdb ;
            $table_name   = $wpdb->prefix . 'rspointexpiry' ;
            $banning_type = check_banning_type( $user_id ) ;
            if ( $banning_type != 'earningonly' && $banning_type != 'both' ) {
                $registration_points       = RSMemberFunction::earn_points_percentage( $user_id , ( float ) $pointstoaward ) ;
                $restrictuserpoints        = get_option( 'rs_max_earning_points_for_user' ) ;
                $oldpoints                 = $wpdb->get_results( "SELECT SUM((earnedpoints-usedpoints)) as availablepoints FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid=$user_id" , ARRAY_A ) ;
                $totaloldpoints            = $oldpoints[ 0 ][ 'availablepoints' ] ;
                $currentregistrationpoints = $totaloldpoints + $registration_points ;
                if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' && ($restrictuserpoints != '') ) {
                    $currentregistrationpoints = ($currentregistrationpoints <= $restrictuserpoints) ? $registration_points : $restrictuserpoints ;
                }

                $extra_args = get_user_meta( $user_id , 'srp_data_for_reg_points' , true ) ;
                if ( srp_check_is_array( $extra_args ) ) {
                    $reg_point_args   = array( 'userid' => $user_id , 'points' => $currentregistrationpoints , 'refuserid' => '' , 'refpoints' => '' , 'event_slug' => $event_slug , 'reaseonidetail' => $Network ) ;
                    $args[ $user_id ] = isset( $extra_args[ $user_id ] ) ? array_merge( $reg_point_args , $extra_args[ $user_id ] ) : $reg_point_args ;
                } else {
                    $args[ $user_id ] = array( 'userid' => $user_id , 'points' => $currentregistrationpoints , 'refuserid' => '' , 'refpoints' => '' , 'event_slug' => $event_slug , 'reaseonidetail' => $Network ) ;
                }
                update_user_meta( $user_id , 'srp_data_for_reg_points' , $args ) ;
            }
        }

        /* Award Points for Social Linking - Compatability with Social Login Plugin */

        public static function award_points_for_social_link( $userid , $Network ) {
            if ( ! is_user_logged_in() )
                return ;

            $BanType = check_banning_type( $userid ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( ! allow_reward_points_for_user( $userid ) )
                return ;

            if ( get_option( 'rs_enable_for_social_account_linking' ) != 'yes' )
                return ;

            $pointstoinsert = get_option( 'rs_reward_for_social_account_linking' ) ;
            if ( empty( $pointstoinsert ) )
                return ;

            $getusermeta  = ( array ) get_user_meta( $userid , 'rs_restrict_points_for_social_linking' , true ) ;
            $network_name = fpsl_get_plugin_networks() ;
            $Network      = $network_name[ $Network ] ;
            if ( in_array( $Network , $getusermeta ) )
                return ;

            $new_obj                     = new RewardPointsOrder( $order_id                    = 0 , $apply_previous_order_points = 'no' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                $new_obj->check_point_restriction( $pointstoinsert , $pointsredeemed = 0 , $event_slug     = 'SLLRP' , $UserId , $nomineeid      = '' , $referrer_id    = '' , $productid      = '' , $variationid    = '' , $reasonindetail = $Network ) ;
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $pointstoinsert , 'event_slug' => 'SLLRP' , 'user_id' => $userid , 'reasonindetail' => $Network , 'totalearnedpoints' => $pointstoinsert ) ;
                $new_obj->total_points_management( $valuestoinsert ) ;
            }

            $oldlogindata = ( array ) get_user_meta( $userid , 'rs_restrict_points_for_social_linking' , true ) ;
            $newdata      = ( array ) $Network ;
            $mergedata    = array_merge( $oldlogindata , $newdata ) ;
            update_user_meta( $userid , 'rs_restrict_points_for_social_linking' , array_filter( $mergedata ) ) ;
        }

        /* Award Points for Custom Fields */

        public static function award_points_for_cus_fields( $userid , $custom_fields ) {
            $BanType = check_banning_type( $userid ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( ! allow_reward_points_for_user( $userid ) )
                return ;

            if ( get_option( 'rs_enable_points_for_cus_field_reg' ) != 'yes' )
                return ;

            $Rules = get_option( 'rs_rule_for_custom_reg_field' ) ;
            if ( ! srp_check_is_array( $Rules ) )
                return ;

            foreach ( $Rules as $individual_rule ) {
                $field_id                   = $individual_rule[ 'custom_fields' ] ;
                $pointstoinsert             = $individual_rule[ 'reward_points' ] ;
                $pointsforfillingdatepicker = isset( $individual_rule[ 'award_points_for_filling' ] ) ? $individual_rule[ 'award_points_for_filling' ] : 'no' ;
                $repeat_points              = isset( $individual_rule[ 'repeat_points' ] ) ? $individual_rule[ 'repeat_points' ] : 'no' ;

                if ( empty( $pointstoinsert ) || empty( $field_id ) )
                    continue ;

                $field_data = fpcf_get_custom_fields( $field_id ) ;
                if ( ! $field_data )
                    continue ;

                $getusermeta = ( array ) get_user_meta( $userid , 'rs_points_awarded_for_' . $field_data->field_type , true ) ;
                if ( in_array( $field_id , $getusermeta ) )
                    continue ;

                if ( ! in_array( $field_id , $custom_fields ) )
                    continue ;

                $field_value = fpcf_get_user_meta( $userid , $field_data->field_name ) ;
                if ( empty( $field_value ) )
                    continue ;

                if ( $field_data->field_type == 'datepicker' ) {
                    $current_date = strtotime( date( 'd-m-Y' ) ) ;
                    $bday_date    = strtotime( $field_value ) ? strtotime( $field_value ) : strtotime( $field_value . '-' . date( 'Y' ) ) ;
                    if ( ! $bday_date )
                        continue ;

                    $next_schedule = true ;
                    if ( $current_date > $bday_date ) {
                        $currentbdaydate          = strtotime( date( 'd-m' , $bday_date ) . '-' . date( 'Y' ) ) ;
                        $TimeStampForNextSchedule = ($current_date < $currentbdaydate) ? ($current_date + ($currentbdaydate - $current_date)) : strtotime( '+1 year' , $currentbdaydate ) ;
                        if ( $current_date == $currentbdaydate ) {
                            self::insert_points_for_custom_fields( $pointstoinsert , 'CRPFDP' , $userid , $field_data , $field_id ) ;
                            $next_schedule = ($repeat_points == 'yes') ? true : false ;
                        }
                    } else {
                        $TimeStampForNextSchedule = ($current_date < $bday_date) ? ($current_date + ($bday_date - $current_date)) : strtotime( '+1 year' , $bday_date ) ;
                        if ( $current_date == $bday_date ) {
                            self::insert_points_for_custom_fields( $pointstoinsert , 'CRPFDP' , $userid , $field_data , $field_id ) ;
                            $next_schedule = ($repeat_points == 'yes') ? true : false ;
                        }
                    }

                    if ( $next_schedule )
                        wp_schedule_single_event( $TimeStampForNextSchedule , 'rs_award_points_for_datepicker_in_cusfields' , array( $TimeStampForNextSchedule , $field_data , $pointstoinsert , $userid ) ) ;

                    if ( $pointsforfillingdatepicker == 'no' )
                        continue ;
                }

                self::insert_points_for_custom_fields( $pointstoinsert , 'CRFRP' , $userid , $field_data , $field_id ) ;
            }
        }

        /* Award Points for Datepicker(i.e for Birthday) */

        public static function award_points_for_datepicker_in_cus_fields_repeatedly( $timestamp , $field_data , $pointstoinsert , $userid ) {
            $BanType = check_banning_type( $userid ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( ! allow_reward_points_for_user( $userid ) )
                return ;

            if ( get_option( 'rs_enable_points_for_cus_field_reg' ) != 'yes' )
                return ;

            $Rules = get_option( 'rs_rule_for_custom_reg_field' ) ;
            if ( ! srp_check_is_array( $Rules ) )
                return ;

            foreach ( $Rules as $individual_rule ) {
                $field_id       = $individual_rule[ 'custom_fields' ] ;
                $pointstoinsert = $individual_rule[ 'reward_points' ] ;
                $repeat_points  = isset( $individual_rule[ 'repeat_points' ] ) ? $individual_rule[ 'repeat_points' ] : 'no' ;

                if ( empty( $pointstoinsert ) || empty( $field_id ) )
                    continue ;

                $field_data = fpcf_get_custom_fields( $field_id ) ;
                if ( ! $field_data )
                    continue ;

                if ( $field_data->field_type == 'datepicker' ) {
                    if ( $repeat_points == 'yes' ) {
                        self::insert_points_for_custom_fields( $pointstoinsert , 'CRPFDP' , $userid , $field_data , $field_id ) ;
                        $TimeStampForNextSchedule = strtotime( '+1 year' , $timestamp ) ;
                        wp_schedule_single_event( $TimeStampForNextSchedule , 'rs_award_points_for_datepicker_in_cusfields' , array( $TimeStampForNextSchedule , $field_data , $pointstoinsert , $userid ) ) ;
                    } else {
                        self::insert_points_for_custom_fields( $pointstoinsert , 'CRPFDP' , $userid , $field_data , $field_id ) ;
                    }
                }
            }
        }

        public static function insert_points_for_custom_fields( $pointstoinsert , $event_slug , $userid , $field_data , $field_id ) {
            $new_obj = new RewardPointsOrder( 0 , 'no' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                $new_obj->check_point_restriction( $pointstoinsert , 0 , $event_slug , $userid , '' , '' , '' , '' , $field_data->field_label ) ;
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $pointstoinsert , 'event_slug' => $event_slug , 'user_id' => $userid , 'reasonindetail' => $field_data->field_label , 'totalearnedpoints' => $pointstoinsert ) ;
                $new_obj->total_points_management( $valuestoinsert ) ;
            }
            $oldlogindata = ( array ) get_user_meta( $userid , 'rs_points_awarded_for_' . $field_data->field_type , true ) ;
            $newdata      = ( array ) $field_id ;
            $mergedata    = array_merge( $oldlogindata , $newdata ) ;
            update_user_meta( $userid , 'rs_points_awarded_for_' . $field_data->field_type , array_filter( $mergedata ) ) ;
        }

        /* Display Earn Points Message for Coupon */

        public static function display_message_for_coupon_reward_points() {
            $BanType = check_banning_type( get_current_user_id() ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( get_option( 'rs_enable_coupon_reward_success_msg' ) != 'yes' )
                return ;

            $SortType = (get_option( 'rs_choose_priority_level_selection_coupon_points' ) == '1') ? 'desc' : 'asc' ;
            $Rules    = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_couponpoints' ) , 'reward_points' , $SortType ) ;
            $Codes    = array() ;
            $Datas    = array() ;
            if ( ! srp_check_is_array( $Rules ) )
                return ;

            foreach ( $Rules as $Key => $Rule ) {
                if ( ! isset( $Rule[ 'coupon_codes' ] ) )
                    continue ;

                if ( ! srp_check_is_array( $Rule[ 'coupon_codes' ] ) )
                    continue ;

                foreach ( $Rule[ 'coupon_codes' ] as $Code ) {
                    if ( in_array( $Code , $Codes ) )
                        continue ;

                    $Codes[ $Key ] = $Code ;
                }
            }

            if ( ! srp_check_is_array( $Codes ) )
                return ;

            foreach ( $Codes as $KeyToFind => $Value ) {
                $Datas[] = $Rules[ $KeyToFind ] ;
            }

            if ( ! srp_check_is_array( $Datas ) )
                return ;

            foreach ( $Datas as $Data ) {
                $CouponCodes = $Data[ "coupon_codes" ] ;
                if ( ! srp_check_is_array( $CouponCodes ) )
                    continue ;

                $Points = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $Data[ "reward_points" ] ) ;

                foreach ( $CouponCodes as $Code ) {
                    if ( ! check_if_coupon_exist_in_cart( $Code , WC()->cart->applied_coupons ) )
                        continue ;

                    $Msg = str_replace( array( "[coupon_name]" , "[coupon_rewardpoints]" ) , array( $Code , $Points ) , get_option( 'rs_coupon_applied_reward_success' ) ) ;
                    ?>
                    <div class="woocommerce-message">
                        <?php echo $Msg ; ?>
                    </div>
                    <?php
                }
            }
        }

        /* Display Earn Points Message for Signup */

        public static function signup_msg_in_my_account() {
            if ( is_user_logged_in() )
                return ;

            if ( ! is_account_page() )
                return ;

            if ( get_option( '_rs_enable_signup' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_message_for_sign_up' ) == '2' )
                return ;

            $SignUpPoints = get_option( 'rs_reward_signup' ) ;
            if ( empty( $SignUpPoints ) )
                return ;

            $ReplacedMessage = str_replace( '[rssignuppoints]' , round_off_type( $SignUpPoints ) , get_option( 'rs_message_user_points_for_sign_up' ) ) ;
            $BoolVal         = get_option( 'rs_select_account_signup_points_award' ) == '1' ? true : isset( $_COOKIE[ 'rsreferredusername' ] ) ;
            if ( ! $BoolVal )
                return ;
            ?>
            <div class="woocommerce-info">
                <?php
                echo $ReplacedMessage ;
                ?>
            </div>
            <?php
        }

        /* Display Earn Points Message for Login */

        public static function login_msg_in_my_account_page() {
            if ( ! is_account_page() )
                return ;

            if ( is_user_logged_in() )
                return ;

            if ( get_option( 'rs_enable_reward_points_for_login' ) != 'yes' )
                return ;

            if ( get_option( 'rs_show_hide_message_for_daily_login' ) == '2' )
                return ;

            $LoginPoints = get_option( 'rs_reward_points_for_login' ) ;
            if ( empty( $LoginPoints ) )
                return ;

            $ReplacedMessage = str_replace( '[rsdailyloginpoints]' , round_off_type( $LoginPoints ) , get_option( 'rs_message_user_points_for_daily_login' ) ) ;
            ?>
            <div class="woocommerce-info">
                <?php
                echo $ReplacedMessage ;
                ?>
            </div>
            <?php
        }

        /* Award Points for Topic Creation in BBPress */

        public static function award_points_for_topic_creation_in_bbpress( $topic_id ) {
            if ( ! is_user_logged_in() )
                return ;

            $BanType = check_banning_type( get_current_user_id() ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( get_option( 'rs_enable_reward_points_for_create_topic' ) != 'yes' )
                return ;

            $Points = get_option( 'rs_reward_points_for_creatic_topic' ) ;
            if ( empty( $Points ) )
                return ;

            $Post = get_post( $topic_id ) ;
            if ( ! is_object( $Post ) )
                return ;

            $PostType = $Post->post_type ;
            if ( $PostType != 'topic' )
                return ;

            $PostParent            = $Post->post_parent ;
            $CheckIfAlreadyAwarded = get_user_meta( get_current_user_id() , 'topiccreation' . $PostParent , true ) ;
            if ( $CheckIfAlreadyAwarded == '1' )
                return ;

            $new_obj = new RewardPointsOrder( 0 , 'no' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                $new_obj->check_point_restriction( $Points , $pointsredeemed = 0 , 'RPCT' , get_current_user_id() , $nomineeid      = '' , $referrer_id    = '' , $productid      = '' , $variationid    = '' , $reasonindetail ) ;
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $Points , 'event_slug' => 'RPCT' , 'user_id' => get_current_user_id() , 'totalearnedpoints' => $Points ) ;
                $new_obj->total_points_management( $valuestoinsert ) ;
            }
            update_user_meta( get_current_user_id() , 'topiccreation' . $PostParent , '1' ) ;
        }

        /* Award Points for Reply Topic in BBPress */

        public static function award_points_for_reply_in_bbpress( $topic_id ) {
            if ( ! is_user_logged_in() )
                return ;

            $BanType = check_banning_type( get_current_user_id() ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( get_option( 'rs_enable_reward_points_for_reply_topic' ) != 'yes' )
                return ;

            $Points = get_option( 'rs_reward_points_for_reply_topic' ) ;
            if ( empty( $Points ) )
                return ;

            $Post = get_post( $topic_id ) ;
            if ( ! is_object( $Post ) )
                return ;

            $PostType = $Post->post_type ;
            if ( $PostType != 'reply' )
                return ;

            $PostParent            = $Post->post_parent ;
            $CheckIfAlreadyAwarded = get_user_meta( get_current_user_id() , 'userreplytopic' . $PostParent , true ) ;
            if ( $CheckIfAlreadyAwarded == '1' )
                return ;

            $new_obj = new RewardPointsOrder( 0 , 'no' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                $new_obj->check_point_restriction( get_option( 'rs_max_earning_points_for_user' ) , $Points , $pointsredeemed = 0 , 'RPRT' , get_current_user_id() , $nomineeid      = '' , $referrer_id    = '' , $productid      = '' , $variationid    = '' , $reasonindetail ) ;
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $Points , 'event_slug' => 'RPRT' , 'user_id' => get_current_user_id() , 'totalearnedpoints' => $Points ) ;
                $new_obj->total_points_management( $valuestoinsert ) ;
            }
            update_user_meta( get_current_user_id() , 'userreplytopic' . $PostParent , '1' ) ;
        }

        /* Award Points for Waitlist Subscription */

        public static function award_points_for_subscribing_waitlist( $product_id , $user_id ) {
            $BanType = check_banning_type( $user_id ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( get_option( 'rs_enable_for_waitlist_subscribing' ) != 'yes' )
                return ;

            if ( get_option( 'rs_reward_for_waitlist_subscribing' ) == '' )
                return ;

            $Points  = get_option( "rs_reward_for_waitlist_subscribing" ) ;
            $new_obj = new RewardPointsOrder( 0 , 'no' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                $new_obj->check_point_restriction( $Points , $pointsredeemed = 0 , $event_slug     = 'RPFWLS' , $user_id , $nomineeid      = '' , $referrer_id    = '' , $product_id , $variationid    = '' , $reasonindetail = '' ) ;
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $Points , 'event_slug' => 'RPFWLS' , 'user_id' => $user_id , 'product_id' => $product_id , 'totalearnedpoints' => $Points ) ;
                $new_obj->total_points_management( $valuestoinsert ) ;
            }
        }

        public static function award_points_for_waitlist_sale_converion( $product_id , $order_id ) {
            if ( get_option( 'rs_enable_for_waitlist_sale_conversion' ) != 'yes' )
                return ;

            if ( get_option( 'rs_reward_for_waitlist_sale_conversion' ) == '' )
                return ;

            $order      = wc_get_order( $order_id ) ;
            $order_data = srp_order_obj( $order ) ;
            $user_id    = $order_data[ 'order_userid' ] ;
            $BanType    = check_banning_type( $user_id ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            $sale_converted_points       = get_option( "rs_reward_for_waitlist_sale_conversion" ) ;
            $new_obj                     = new RewardPointsOrder( $order_id , $apply_previous_order_points = 'no' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                $new_obj->check_point_restriction( $sale_converted_points , $pointsredeemed = 0 , $event_slug     = 'RPFWLSC' , $user_id , $nomineeid      = '' , $referrer_id    = '' , $product_id , $variationid    = '' , $reasonindetail = '' ) ;
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $sale_converted_points , 'event_slug' => 'RPFWLSC' , 'user_id' => $user_id , 'product_id' => $product_id , 'totalearnedpoints' => $sale_converted_points ) ;
                $new_obj->total_points_management( $valuestoinsert ) ;
            }
        }

    }

    RSActionRewardModule::init() ;
}