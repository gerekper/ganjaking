<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Update_Earned_Points' ) ) {

    /**
     * RS_Update_Earned_Points Class.
     */
    class RS_Update_Earned_Points extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_earned_points_background_updater' ;

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
            $this->update_earned_points( $item ) ;
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
            $offset = get_option( 'rs_earned_points_background_updater_offset' ) ;
            $ids    = $wpdb->get_results( "SELECT DISTINCT ID FROM {$wpdb->users} as p INNER JOIN {$wpdb->usermeta} as p1 ON p.ID=p1.user_id WHERE p1.meta_key = 'rs_expired_points_before_delete' AND p1.meta_value > '0' LIMIT $offset,1000" ) ;
            if ( is_array( $ids ) && ! empty( $ids ) ) {
                RS_Main_Function_for_Background_Process::callback_to_update_earned_points( $offset ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Earned Points Update Completed' ) ;
                delete_option( 'rs_earned_points_background_updater_offset' ) ;
                update_option( 'rs_points_update_success' , 'yes' ) ;
            }
        }

        public function update_earned_points( $user_id ) {
            if ( $user_id != 'no_users' ) {
                if ( get_user_meta( $user_id , 'rs_check_if_points_updated' , true ) != 'yes' ) {
                    $new_points = ( float ) get_user_meta( $user_id , 'rs_earned_points_before_delete' , true ) + ( float ) get_user_meta( $user_id , 'rs_expired_points_before_delete' , true ) ;
                    update_user_meta( $user_id , 'rs_earned_points_before_delete' , $new_points ) ;
                    update_user_meta( $user_id , 'rs_check_if_points_updated' , 'yes' ) ;
                }
            }
            return $user_id ;
        }

    }

}