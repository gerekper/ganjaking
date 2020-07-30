<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Refresh_Points_For_User' ) ) {

    /**
     * RS_Refresh_Points_For_User Class.
     */
    class RS_Refresh_Points_For_User extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_refresh_points_for_user_updater' ;

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
            $this->refresh_expired_points_for_user( $item ) ;
            return false ;
        }
        
        /**
         * Complete
         *
         * Override if applicable, but ensure that the below actions are
         * performed, or, call parent::complete().
         */
        protected function complete() {
            parent::complete() ;
            $offset      = get_option( 'rs_refresh_points_background_updater_offset' ) ;
            $args        = array( 'fields' => 'ID' ) ;
            $UserId      = get_users( $args ) ;
            $SlicedArray = array_slice( $UserId , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                RS_Main_Function_for_Background_Process::callback_to_refresh_points_for_user( $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Points for User(s) updated Successfully' ) ;
                delete_option( 'rs_refresh_points_background_updater_offset' ) ;
            }
        }

        public function refresh_expired_points_for_user( $UserId ) {
            if ( $UserId != 'no_users' ) {
                $this->check_if_expiry_on_admin( $UserId ) ;
            }
            return $UserId ;
        }

        public function check_if_expiry_on_admin( $UserId ) {
            global $wpdb ;
            $table_name       = $wpdb->prefix . 'rspointexpiry' ;
            $currentdate      = time() ;
            $GetExpiredPoints = $wpdb->get_results( "SELECT * FROM $table_name WHERE expirydate < $currentdate and expirydate NOT IN(999999999999) and expiredpoints IN(0) and userid=$UserId" , ARRAY_A ) ;
            if ( ! srp_check_is_array( $GetExpiredPoints ) )
                return ;

            foreach ( $GetExpiredPoints as $ExpiredPoints ) {
                $wpdb->update( $table_name , array( 'expiredpoints' => $ExpiredPoints[ 'earnedpoints' ] - $ExpiredPoints[ 'usedpoints' ] ) , array( 'id' => $ExpiredPoints[ 'id' ] ) ) ;
            }
        }

    }

}