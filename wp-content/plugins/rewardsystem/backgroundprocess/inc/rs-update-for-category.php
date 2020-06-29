<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Update_for_Category' ) ) {

    /**
     * RS_Update_for_Category Class.
     */
    class RS_Update_for_Category extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_category_background_updater' ;

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
            $this->rs_update_category_data( $item ) ;
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
            $offset = get_option( 'rs_category_background_updater_offset' ) ;
            $ids    = $wpdb->get_col( "SELECT DISTINCT term_id FROM {$wpdb->termmeta} WHERE meta_key = 'enable_reward_system_category' AND meta_value = 'yes' LIMIT $offset,1000" ) ;
            if ( is_array( $ids ) && ! empty( $ids ) ) {
                RS_Main_Function_for_Background_Process::callback_to_update_category( $offset ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Category Upgrade Completed' ) ;
                FP_WooCommerce_Log::log( 'v18.0 Upgrade Completed' ) ;
                delete_option( 'rs_category_background_updater_offset' ) ;
                update_option( 'rs_upgrade_success' , 'yes' ) ;
            }
        }

        public static function rs_update_category_data( $product_id ) {
            if ( $product_id != 'rs_data' ) {
                if ( srp_term_meta( $product_id , 'rs_check_if_already_exists' ) === '' ) {
                    srp_update_term_meta( $product_id , 'enable_referral_reward_system_category' , 'yes' ) ;
                    srp_update_term_meta( $product_id , 'rs_check_if_already_exists' , 'yes' ) ;
                }
            }
            return $product_id ;
        }

    }

}