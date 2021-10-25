<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Apply_Points_For_Previous_Order' ) ) {

    /**
     * RS_Update_for_Simple_Product Class.
     */
    class RS_Apply_Points_For_Previous_Order extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_apply_points_for_previous_order_updater' ;

        /**
         * Task
         *
         * Override this method to perform any actions required on each
         * queue item. Return the modified item for further processing
         * in the next pass through. Or, return false to remove the
         * item from the queue.
         *
         * @param mixed $item Queue item to iterate over
         *
         * @return mixed
         */
        protected function task( $item ) {
            $this->rs_update_points_for_previous_order( $item ) ;
            return false ;
        }
        
        /**
         * Complete
         *
         * Override if applicable, but ensure that the below actions are
         * performed, or, call parent::complete().
         */
        protected function complete() {
            global $wpdb ;
            parent::complete() ;
            $offset                   = get_option( 'rs_applied_previous_order_background_updater_offset' ) ;
            $OrderStatusList          = array( 'wc-completed' ) ;
            $OrderStatusToApplyPoints = get_option( 'rs_order_status_control' ) ;
            foreach ( $OrderStatusToApplyPoints as $OrderStatus ) {
                $OrderStatusList[] = 'wc-' . $OrderStatus ;
            }
            $PreviousOrderPointsFor = get_option( 'rs_previous_order_points_for' ) ;
            if ( $PreviousOrderPointsFor == '1' ) {
                $args    = array( 'post_type' => 'shop_order' , 'numberposts' => '-1' , 'meta_query' => array( array( 'key' => 'reward_points_awarded' , 'compare' => 'NOT EXISTS' ) ) , 'post_status' => $OrderStatusList , 'fields' => 'ids' , 'cache_results' => false ) ;
                $OrderId = get_posts( $args ) ;
            } else {
                $args    = array( 'post_type' => 'shop_order' , 'numberposts' => '-1' , 'meta_query' => array( array( 'key' => 'reward_points_awarded' , 'compare' => 'EXISTS' ) ) , 'post_status' => $OrderStatusList , 'fields' => 'ids' , 'cache_results' => false ) ;
                $OrderId = get_posts( $args ) ;
            }
            $SlicedArray = array_slice( $OrderId , $offset , 1000 ) ;
            if ( is_array( $SlicedArray ) && ! empty( $SlicedArray ) ) {
                RS_Main_Function_for_Background_Process::callback_to_apply_points_for_previous_order( $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Points for Previous Order applied Successfully' ) ;
                delete_option( 'rs_applied_previous_order_background_updater_offset' ) ;
            }
        }

        public static function rs_update_points_for_previous_order( $OrderId ) {
            if ( $OrderId != 'no_orders' ) {
                $OrderObj               = new WC_Order( $OrderId ) ;
                $OrderObj               = srp_order_obj( $OrderObj ) ;
                $OrderedUserId          = $OrderObj[ 'order_userid' ] ;
                $modified_date          = get_the_time( 'Y-m-d' , $OrderId ) ;
                $CheckIfPointsAwarded   = get_post_meta( $OrderId , 'reward_points_awarded' , true ) ;
                $PreviousOrderPointsFor = get_option( 'rs_previous_order_points_for' ) ;
                $AwardPointsOn          = get_option( 'rs_award_points_on' ) ;
                $FromDate               = get_option( 'rs_apply_points_fromdate' ) ;
                $ToDate                 = get_option( 'rs_apply_points_todate' ) ;
                if ( $AwardPointsOn == '1' ) {
                    if ( $PreviousOrderPointsFor == '1' ) {
                        if ( $CheckIfPointsAwarded != 'yes' ) {
                            $new_obj                     = new RewardPointsOrder( $OrderId , $apply_previous_order_points = 'yes' ) ;
                            $new_obj->update_earning_points_for_user() ;
                        }
                    } else {
                        $pointstodelete[]            = self::get_already_earned_points( $OrderId , $OrderedUserId ) ;
                        $totalpoints                 = array_sum( $pointstodelete ) ;
                        self::replace_the_points_already_earned_in_order( $OrderId , $OrderedUserId , $totalpoints ) ;
                        delete_post_meta( $OrderId , 'reward_points_awarded' ) ;
                        delete_post_meta( $OrderId , 'earning_point_once' ) ;
                        $new_obj                     = new RewardPointsOrder( $OrderId , $apply_previous_order_points = 'yes' ) ;
                        $new_obj->update_earning_points_for_user( 'Replaced' ) ;
                    }
                } else {
                    if ( $FromDate != '' || $ToDate != '' ) {
                        $From_Date     = strtotime( $FromDate ) ;
                        $To_Date       = strtotime( $ToDate ) ;
                        $DateToCompare = strtotime( $modified_date ) ;
                        if ( ($From_Date <= $DateToCompare) && ($DateToCompare <= $To_Date) ) {
                            if ( $PreviousOrderPointsFor == '1' ) {
                                if ( $CheckIfPointsAwarded != 'yes' ) {
                                    $new_obj                     = new RewardPointsOrder( $OrderId , $apply_previous_order_points = 'yes' ) ;
                                    $new_obj->update_earning_points_for_user() ;
                                }
                            } else {
                                $pointstodelete[]            = self::get_already_earned_points( $OrderId , $OrderedUserId ) ;
                                $totalpoints                 = array_sum( $pointstodelete ) ;
                                self::replace_the_points_already_earned_in_order( $OrderId , $OrderedUserId , $totalpoints ) ;
                                delete_post_meta( $OrderId , 'reward_points_awarded' ) ;
                                delete_post_meta( $OrderId , 'earning_point_once' ) ;
                                $new_obj                     = new RewardPointsOrder( $OrderId , $apply_previous_order_points = 'yes' ) ;
                                $new_obj->update_earning_points_for_user( 'Replaced' ) ;
                            }
                        }
                    }
                }
            }
            return $OrderId ;
        }

        public static function replace_the_points_already_earned_in_order( $order_id , $user_id , $totalpoints ) {
            global $wpdb ;
            $table_name  = $wpdb->prefix . 'rspointexpiry' ;
            $table_name1 = $wpdb->prefix . 'rsrecordpoints' ;
            $query       = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE userid = %d AND expirydate = 999999999999" , $user_id ) , ARRAY_A ) ;
            if ( ! empty( $query ) ) {
                $id              = $query[ 'id' ] ;
                $AvailablePoints = $query[ 'earnedpoints' ] - $query[ 'usedpoints' ] ;
                if ( $totalpoints >= $AvailablePoints ) {
                    $PointsToUpdate = $query[ 'usedpoints' ] + $AvailablePoints ;
                } else {
                    $PointsToUpdate = $query[ 'usedpoints' ] + $totalpoints ;
                }
                $wpdb->update( $table_name , array( 'usedpoints' => $PointsToUpdate ) , array( 'id' => $id ) ) ;
            }
            $query2 = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE checkpoints = 'PPRP' AND orderid = %d AND userid = %d AND expirydate != 999999999999" , $order_id , $user_id ) , ARRAY_A ) ;
            if ( ! empty( $query2 ) ) {
                $id              = $query2[ 'id' ] ;
                $AvailablePoints = $query2[ 'earnedpoints' ] - $query2[ 'usedpoints' ] ;
                if ( $totalpoints >= $AvailablePoints ) {
                    $PointsToUpdate = $query2[ 'usedpoints' ] + $AvailablePoints ;
                } else {
                    $PointsToUpdate = $query2[ 'usedpoints' ] + $totalpoints ;
                }
                $wpdb->update( $table_name , array( 'usedpoints' => $PointsToUpdate ) , array( 'id' => $id ) ) ;
            }
        }

        public static function get_already_earned_points( $order_id , $user_id ) {
            global $wpdb ;
            $earned_points        = 0 ;
            $table_name           = $wpdb->prefix . 'rsrecordpoints' ;
            $rsrecordpoints_table = $wpdb->get_results( $wpdb->prepare( "SELECT earnedpoints,userid FROM $table_name WHERE checkpoints = 'PPRP' AND orderid = %d AND userid = %d" , $order_id , $user_id ) , ARRAY_A ) ;
            foreach ( $rsrecordpoints_table as $earnedpoints ) {
                if ( $earnedpoints[ 'userid' ] == $user_id ) {
                    $earned_points = $earnedpoints[ 'earnedpoints' ] ;
                }
            }
            return $earned_points ;
        }

    }

}