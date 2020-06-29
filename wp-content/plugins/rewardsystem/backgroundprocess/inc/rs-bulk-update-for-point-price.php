<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Bulk_Update_for_Point_Price' ) ) {

    /**
     * RS_Bulk_Update_for_Point_Price Class.
     */
    class RS_Bulk_Update_for_Point_Price extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_bulk_update_for_point_price' ;

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
            $this->update_point_price_for_product( $item ) ;
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
            $offset = get_option( 'fp_bulk_update_point_price_for_product' ) ;
            if ( get_option( 'fp_product_selection_type' ) == 2 ) {
                $ProductIds = srp_check_is_array( get_option( 'fp_selected_products' ) ) ? get_option( 'fp_selected_products' ) : explode( ',' , get_option( 'fp_selected_products' ) ) ;
            } else {
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            }
            $SlicedArray = array_slice( $ProductIds , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                RS_Main_Function_for_Background_Process::callback_to_update_point_price_for_product( $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Point Price for Product(s) Updated Successfully' ) ;
                delete_option( 'fp_bulk_update_point_price_for_product' ) ;
            }
        }

        public function update_point_price_for_product( $ProductId ) {
            if ( $ProductId == 'no_products' )
                return $ProductId ;

            $checkproduct = srp_product_object( $ProductId ) ;
            if ( ! is_object( $checkproduct ) )
                return $ProductId ;

            if ( get_option( 'fp_product_selection_type' ) == 1 || get_option( 'fp_product_selection_type' ) == 2 ) {
                if ( srp_check_is_array( get_variation_id( $ProductId ) ) ) {
                    foreach ( get_variation_id( $ProductId ) as $VariationId ) {
                        $ProductLevelMetaKey = array(
                            'enablepointprice' => '_enable_reward_points_price' ,
                            'pointpricingtype' => '_enable_reward_points_pricing_type' ,
                            'pointpricetype'   => '_enable_reward_points_price_type' ,
                            'pricepoints'      => 'price_points' ,
                                ) ;
                        $this->update_product_meta_for_bulk_update( $VariationId , $ProductLevelMetaKey ) ;
                    }
                } else {
                    if ( (check_if_variable_product( $checkproduct ) || ((srp_product_type( $ProductId ) == 'variable') || (srp_product_type( $ProductId ) == 'variation')) ) ) {
                        $ProductLevelMetaKey = array(
                            'enablepointprice' => '_enable_reward_points_price' ,
                            'pointpricingtype' => '_enable_reward_points_pricing_type' ,
                            'pointpricetype'   => '_enable_reward_points_price_type' ,
                            'pricepoints'      => 'price_points' ,
                                ) ;
                    } else {
                        $ProductLevelMetaKey = array(
                            'enablepointprice' => '_rewardsystem_enable_point_price' ,
                            'pointpricingtype' => '_rewardsystem_enable_point_price_type' ,
                            'pointpricetype'   => '_rewardsystem_point_price_type' ,
                            'pricepoints'      => '_rewardsystem__points' ,
                                ) ;
                    }
                    $this->update_product_meta_for_bulk_update( $ProductId , $ProductLevelMetaKey ) ;
                }
            } elseif ( get_option( 'fp_product_selection_type' ) == 3 || get_option( 'fp_product_selection_type' ) == 4 ) {
                $ProductCat = get_the_terms( $ProductId , 'product_cat' ) ;
                if ( srp_check_is_array( $ProductCat ) ) {
                    $CategoryList = (get_option( 'fp_product_selection_type' ) == 3) ? get_terms( 'product_cat' ) : get_option( 'fp_selected_categories' ) ;
                    if ( srp_check_is_array( $CategoryList ) ) {
                        foreach ( $CategoryList as $CategoryId ) {
                            if ( ! $this->check_if_product_is_in_selected_category( $CategoryId , $ProductCat ) )
                                continue ;

                            $CategoryId = is_object( $CategoryId ) ? $CategoryId->term_id : $CategoryId ;
                            if ( (check_if_variable_product( $checkproduct ) || ((srp_product_type( $ProductId ) == 'variable') || (srp_product_type( $ProductId ) == 'variation')) ) ) {
                                update_post_meta( $getvariation[ 'variation_id' ] , '_enable_reward_points_price' , get_option( 'fp_enable_point_price' ) ) ;

                                if ( get_option( 'fp_point_pricing_type' ) )
                                    update_post_meta( $getvariation[ 'variation_id' ] , '_enable_reward_points_pricing_type' , get_option( 'fp_point_pricing_type' ) ) ;

                                if ( get_option( 'fp_point_price_type' ) )
                                    update_post_meta( $getvariation[ 'variation_id' ] , '_enable_reward_points_price_type' , get_option( 'fp_point_price_type' ) ) ;

                                update_post_meta( $getvariation[ 'variation_id' ] , 'price_points' , '' ) ;
                            } else {
                                $enablepointprice = (get_option( 'fp_enable_point_price' ) == 1) ? 'yes' : 'no' ;
                                update_post_meta( $ProductId , '_rewardsystem_enable_point_price' , $enablepointprice ) ;

                                if ( get_option( 'fp_point_pricing_type' ) )
                                    update_post_meta( $ProductId , '_rewardsystem_options' , get_option( 'fp_point_pricing_type' ) ) ;

                                if ( get_option( 'fp_point_pricing_type' ) )
                                    update_post_meta( $ProductId , '_rewardsystempoints' , get_option( 'fp_point_pricing_type' ) ) ;

                                update_post_meta( $ProductId , '_rewardsystem__points' , '' ) ;
                            }

                            $ProductLevelMetaKey = array(
                                'enablepointprice' => 'enable_point_price_category' ,
                                'pointpricingtype' => 'point_price_category_type' ,
                                'pointpricetype'   => 'point_price_category_type' ,
                                'pricepoints'      => 'rs_category_points_price' ,
                                'pricingcategory'  => 'pricing_category_types'
                                    ) ;
                            $this->update_category_meta_for_bulk_update( $CategoryId , $ProductLevelMetaKey ) ;
                        }
                    }
                }
            }
        }

        public function check_if_product_is_in_selected_category( $CategoryId , $ProductCat ) {
            if ( get_option( 'fp_product_selection_type' ) == 3 )
                return true ;

            foreach ( $ProductCat as $Category ) {
                if ( $CategoryId == $Category->term_id )
                    return true ;
            }

            return false ;
        }

        public function update_product_meta_for_bulk_update( $ProductId , $ProductLevelMetaKey ) {
            $enablepointprice = ($ProductLevelMetaKey[ 'enablepointprice' ] == '_enable_reward_points_price') ? get_option( 'fp_enable_point_price' ) : ((get_option( 'fp_enable_point_price' ) == 1) ? 'yes' : 'no') ;
            update_post_meta( $ProductId , $ProductLevelMetaKey[ 'enablepointprice' ] , $enablepointprice ) ;

            if ( get_option( 'fp_point_price_type' ) )
                update_post_meta( $ProductId , $ProductLevelMetaKey[ 'pointpricetype' ] , get_option( 'fp_point_price_type' ) ) ;

            if ( get_option( 'fp_point_pricing_type' ) )
                update_post_meta( $ProductId , $ProductLevelMetaKey[ 'pointpricingtype' ] , get_option( 'fp_point_pricing_type' ) ) ;

            if ( get_option( 'fp_price_points' ) )
                update_post_meta( $ProductId , $ProductLevelMetaKey[ 'pricepoints' ] , get_option( 'fp_price_points' ) ) ;
        }

        public function update_category_meta_for_bulk_update( $CategoryId , $ProductLevelMetaKey ) {
            $enablepointprice = (get_option( 'fp_enable_point_price' ) == 1) ? 'yes' : 'no' ;
            srp_update_term_meta( $CategoryId , $ProductLevelMetaKey[ 'enablepointprice' ] , $enablepointprice ) ;

            if ( get_option( 'fp_point_pricing_type' ) )
                srp_update_term_meta( $CategoryId , $ProductLevelMetaKey[ 'pointpricingtype' ] , get_option( 'fp_point_pricing_type' ) ) ;

            if ( get_option( 'fp_point_price_type' ) )
                srp_update_term_meta( $CategoryId , $ProductLevelMetaKey[ 'pointpricetype' ] , get_option( 'fp_point_price_type' ) ) ;

            if ( get_option( 'fp_price_points' ) )
                srp_update_term_meta( $CategoryId , $ProductLevelMetaKey[ 'pricepoints' ] , get_option( 'fp_price_points' ) ) ;
        }

    }

}