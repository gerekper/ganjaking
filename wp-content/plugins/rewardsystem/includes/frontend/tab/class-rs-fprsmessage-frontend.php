<?php
if( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}

if( ! class_exists( 'RSFunctionForMessage' ) ) {

    class RSFunctionForMessage {

        public static function init() {
            if( get_option( 'rs_reward_table_position' ) == '1' ) {
                add_action( 'woocommerce_after_my_account' , array( __CLASS__ , 'reawrd_log_in_my_account_page' ) ) ;
            } else {
                add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'reawrd_log_in_my_account_page' ) ) ;
            }

            if( '1' == get_option( 'rs_show_or_hide_date_filter' ) ) {
                add_filter( 'rs_my_reward_date_filter' , array( __CLASS__ , 'my_reward_table_date_filter' ) ) ;
            }
        }

        /*
         *  My Reward Table date filter.
         * 
         * @return string.
         */

        public static function my_reward_table_date_filter( $where ) {

            if( isset( $_REQUEST[ 'rs_duration_type' ] , $_REQUEST[ 'rs_submit' ] ) ) {

                $to_date   = time() ;
                $durations = wc_clean( wp_unslash( $_REQUEST[ 'rs_duration_type' ] ) ) ;
                if( '0' == $durations ) {
                    return $where ;
                }

                switch( $durations ) {

                    case '1':

                        $from_date = strtotime( '-1 month' ) ;
                        $where     = "AND earneddate BETWEEN '$from_date' AND '$to_date'" ;
                        break ;
                    case '2':

                        $from_date = strtotime( '-3 month' ) ;
                        $where     = "AND earneddate BETWEEN '$from_date' AND '$to_date'" ;
                        break ;
                    case '3':

                        $from_date = strtotime( '-6 month' ) ;
                        $where     = "AND earneddate BETWEEN '$from_date' AND '$to_date'" ;
                        break ;
                    case '4':

                        $from_date = strtotime( '-12 month' ) ;
                        $where     = "AND earneddate BETWEEN '$from_date' AND '$to_date'" ;
                        break ;
                    case '5':

                        $from_date = strtotime( wc_clean( wp_unslash( $_REQUEST[ 'rs_custom_from_date_field' ] ) ) ) ;
                        $to_date   = strtotime( wc_clean( wp_unslash( $_REQUEST[ 'rs_custom_to_date_field' ] ) ) ) ;
                        $where     = "AND earneddate BETWEEN '$from_date' AND '$to_date'" ;
                        break ;
                }
            }

            return $where ;
        }

        public static function reawrd_log_in_my_account_page() {
            if( get_option( 'rs_reward_content' ) != 'yes' )
                return ;

            if( get_option( 'rs_my_reward_table' ) == 2 )
                return ;

            self::reward_log() ;
        }

        public static function reward_log() {
            ?>
            <style type="text/css">
            <?php echo get_option( 'rs_myaccount_custom_css' ) ; ?>
            </style>
            <?php
            $TableData = array(
                'points_log_sort'        => get_option( 'rs_points_log_sorting' ) ,
                'search_box'             => get_option( 'rs_show_hide_search_box_in_my_rewards_table' ) ,
                'page_size'              => get_option( 'rs_show_hide_page_size_my_rewards' ) ,
                'points_label_position'  => get_option( 'rs_reward_point_label_position' ) ,
                'total_points_label'     => get_option( 'rs_my_rewards_total' ) ,
                'display_currency_value' => get_option( 'rs_reward_currency_value' ) ,
                'sno'                    => get_option( 'rs_my_reward_points_s_no' ) ,
                'points_expiry'          => get_option( 'rs_my_reward_points_expire' ) ,
                'username'               => get_option( 'rs_my_reward_points_user_name_hide' ) ,
                'reward_for'             => get_option( 'rs_my_reward_points_reward_for_hide' ) ,
                'earned_points'          => get_option( 'rs_my_reward_points_earned_points_hide' ) ,
                'redeemed_points'        => get_option( 'rs_my_reward_points_redeemed_points_hide' ) ,
                'total_points'           => get_option( 'rs_my_reward_points_total_points_hide' ) ,
                'earned_date'            => get_option( 'rs_my_reward_points_earned_date_hide' ) ,
                'my_reward_label'        => get_option( 'rs_my_rewards_title' ) ,
                'label_sno'              => get_option( 'rs_my_rewards_sno_label' ) ,
                'label_username'         => get_option( 'rs_my_rewards_userid_label' ) ,
                'label_reward_for'       => get_option( 'rs_my_rewards_reward_for_label' ) ,
                'label_earned_points'    => get_option( 'rs_my_rewards_points_earned_label' ) ,
                'label_redeemed_points'  => get_option( 'rs_my_rewards_redeem_points_label' ) ,
                'label_total_points'     => get_option( 'rs_my_rewards_total_points_label' ) ,
                'label_earned_date'      => get_option( 'rs_my_rewards_date_label' ) ,
                'label_points_expiry'    => get_option( 'rs_my_rewards_points_expired_label' ) ,
                'per_page'               => ('2' == get_option( 'rs_show_hide_page_size_my_rewards' , 1 )) ? get_option( 'rs_number_of_page_size_in_myaccount' , 5 ) : 5 ,
                    ) ;
            echo self::reward_log_table( $TableData ) ;
        }

        public static function reward_log_table( $TableData ) {
            ob_start() ;
            $UserId  = get_current_user_id() ;
            $BanType = check_banning_type( $UserId ) ;
            if( $BanType == 'redeemingonly' || $BanType == 'both' )
                return ;

            global $wpdb ;
            $LogTable = $wpdb->prefix . 'rsrecordpoints' ;

            $where   = apply_filters( 'rs_my_reward_date_filter' , '' ) ;
            $UserLog = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $LogTable WHERE userid = %d AND showuserlog = false $where " , $UserId ) , ARRAY_A ) ;
            $UserLog = $UserLog + ( array ) get_user_meta( $UserId , '_my_points_log' , true ) ;
            if( ! srp_check_is_array( $UserLog ) )
                return ;

            $selected_duration_earned_point   = 0 ;
            $selected_duration_redeemed_point = 0 ;
            if( isset( $_REQUEST[ 'rs_duration_type' ] ) ) {
                foreach( $UserLog as $log ) {
                    $selected_duration_earned_point   = isset( $log[ 'earnedpoints' ] ) ? $selected_duration_earned_point + $log[ 'earnedpoints' ] : 0 ;
                    $selected_duration_redeemed_point = isset( $log[ 'redeempoints' ] ) ? $selected_duration_redeemed_point + $log[ 'redeempoints' ] : 0 ;
                }
            }

            echo "<h2  class=my_rewards_title>" . $TableData[ 'my_reward_label' ] . "</h2>" ;
            $PointData       = new RS_Points_Data( $UserId ) ;
            $AvailablePoints = $PointData->total_available_points() ;
            $DisplayCurrency = $TableData[ 'display_currency_value' ] ;
            if( $DisplayCurrency == '1' ) {
                $msg = '(' . $PointData->total_available_points_as_currency() . ')' ;
            } else {
                $msg = '' ;
            }
            if( $TableData[ 'points_label_position' ] == '1' ) {
                echo "<h4 class=my_reward_total> " . $TableData[ 'total_points_label' ] . " " . round_off_type( $AvailablePoints ) . " " . $msg . "</h4>" ;
            } else {
                echo "<h4 class=my_reward_total> " . round_off_type( $AvailablePoints ) . " " . $msg . $TableData[ 'total_points_label' ] . "</h4>" ;
            }

            $outputtablefields = apply_filters( 'srp_above_reward_table' , '' ) ;
            $outputtablefields .= '<p> ' ;
            if( $TableData[ 'search_box' ] == '1' )
                $outputtablefields .= __( 'Search:' , SRP_LOCALE ) . '<input id="filters" type="text"/> ' ;

            if( $TableData[ 'page_size' ] == '1' ) {
                $outputtablefields .= __( 'Page Size:' , SRP_LOCALE ) . '<select id="change-page-sizes"><option value="5">5</option><option value="10">10</option><option value="50">50</option>
                    <option value="100">100</option>
                </select>' ;
            }
            $outputtablefields .= '</p>' ;
            echo ( $TableData[ 'search_box' ] == '2' && $TableData[ 'page_size' ] == '2' ) ? '' : $outputtablefields ;
            $DefaultColumn     = array(
                'username' ,
                'reward_for' ,
                'earned_points' ,
                'redeemed_points' ,
                'points_expiry' ,
                'total_points' ,
                'earned_date' ,
                    ) ;

            $SortedColumn = srp_check_is_array( get_option( 'sorted_settings_list' ) ) ? get_option( 'sorted_settings_list' ) : $DefaultColumn ;
            $per_page     = isset( $TableData[ 'per_page' ] ) ? $TableData[ 'per_page' ] : '5' ;

            include SRP_PLUGIN_PATH . '/includes/frontend/views/class-rs-frontend-my-reward-table.php' ;
            $content = ob_get_contents() ;
            ob_end_clean() ;
            return $content ;
        }

    }

    RSFunctionForMessage::init() ;
}
