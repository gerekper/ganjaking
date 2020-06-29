<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Bulk_Update_for_Buying_Points' ) ) {

    /**
     * RS_Bulk_Update_for_Buying_Points Class.
     */
    class RS_Bulk_Update_for_Buying_Points extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_bulk_update_for_buying_points' ;

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
            $this->update_buying_points_for_product( $item ) ;
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
            $offset = get_option( 'fp_bulk_update_buying_points_for_product' ) ;
            if ( get_option( 'fp_product_selection_type' ) == 1 ) {
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            } elseif ( get_option( 'fp_product_selection_type' ) == 2 ) {
                $Ids        = srp_check_is_array( get_option( 'fp_include_products' ) ) ? get_option( 'fp_include_products' ) : explode( ',' , get_option( 'fp_include_products' ) ) ;
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'include' => $Ids , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            } else {
                $Ids        = srp_check_is_array( get_option( 'fp_exclude_products' ) ) ? get_option( 'fp_exclude_products' ) : explode( ',' , get_option( 'fp_exclude_products' ) ) ;
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'exclude' => $Ids , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            }
            $SlicedArray = array_slice( $ProductIds , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                RS_Main_Function_for_Background_Process::callback_to_update_buying_points_for_product( $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Buying Points for Product(s) Updated Successfully' ) ;
                delete_option( 'fp_bulk_update_buying_points_for_product' ) ;
            }
        }

        public function update_buying_points_for_product( $ProductId ) {
            if ( $ProductId == 'no_products' )
                return $ProductId ;

            if ( srp_check_is_array( get_variation_id( $ProductId ) ) ) {
                foreach ( get_variation_id( $ProductId ) as $VariationId ) {
                    if ( get_option( 'fp_enable_buying_point' ) )
                        update_post_meta( $VariationId , '_rewardsystem_buying_reward_points' , get_option( 'fp_enable_buying_point' ) ) ;

                    if ( get_option( 'fp_buying_point' ) )
                        update_post_meta( $VariationId , '_rewardsystem_assign_buying_points' , get_option( 'fp_buying_point' ) ) ;
                }
            } else {
                if ( get_option( 'fp_enable_buying_point' ) )
                    update_post_meta( $ProductId , '_rewardsystem_buying_reward_points' , get_option( 'fp_enable_buying_point' ) ) ;

                if ( get_option( 'fp_buying_point' ) )
                    update_post_meta( $ProductId , '_rewardsystem_assign_buying_points' , get_option( 'fp_buying_point' ) ) ;
            }
        }

    }

}