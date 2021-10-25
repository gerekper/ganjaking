<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ;
}
if ( ! class_exists( 'RS_Bulk_Update_for_Social_Reward' ) ) {

    /**
     * RS_Bulk_Update_for_Social_Reward Class.
     */
    class RS_Bulk_Update_for_Social_Reward extends WP_Background_Process {

        /**
         * @var string
         */
        protected $action = 'rs_bulk_update_for_social_reward_points' ;

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
            $this->update_points_for_social_reward( $item ) ;
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
            $offset = get_option( 'fp_bulk_update_points_for_social_reward' ) ;
            if ( get_option( 'fp_product_selection_type' ) == 2 ) {
                $ProductIds = srp_check_is_array( get_option( 'fp_selected_products' ) ) ? get_option( 'fp_selected_products' ) : explode( ',' , get_option( 'fp_selected_products' ) ) ;
            } else {
                $args       = array( 'post_type' => 'product' , 'posts_per_page' => '-1' , 'post_status' => 'publish' , 'fields' => 'ids' , 'cache_results' => false ) ;
                $ProductIds = get_posts( $args ) ;
            }
            $SlicedArray = array_slice( $ProductIds , $offset , 1000 ) ;
            if ( srp_check_is_array( $SlicedArray ) ) {
                RS_Main_Function_for_Background_Process::callback_to_update_points_for_social_reward( $offset ) ;
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
            } else {
                RS_Main_Function_for_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
                FP_WooCommerce_Log::log( 'Social Reward Points for Product(s) Updated Successfully' ) ;
                delete_option( 'fp_bulk_update_points_for_social_reward' ) ;
            }
        }

        public function update_points_for_social_reward( $ProductId ) {
            if ( $ProductId == 'no_products' )
                return $ProductId ;

            $checkproduct = srp_product_object( $ProductId ) ;
            if ( ! is_object( $checkproduct ) )
                return $ProductId ;

            if ( get_option( 'fp_product_selection_type' ) == 1 || get_option( 'fp_product_selection_type' ) == 2 ) {
                $ProductLevelMetaKey = array(
                    'enabledisablereward'         => '_socialrewardsystemcheckboxvalue' ,
                    'rewardtypefacebook'          => '_social_rewardsystem_options_facebook' ,
                    'facebookrewardpoints'        => '_socialrewardsystempoints_facebook' ,
                    'facebookrewardpercent'       => '_socialrewardsystempercent_facebook' ,
                    'rewardtypefacebook_share'    => '_social_rewardsystem_options_facebook_share' ,
                    'facebookrewardpoints_share'  => '_socialrewardsystempoints_facebook_share' ,
                    'facebookrewardpercent_share' => '_socialrewardsystempercent_facebook_share' ,
                    'rewardtypetwitter'           => '_social_rewardsystem_options_twitter' ,
                    'twitterrewardpoints'         => '_socialrewardsystempoints_twitter' ,
                    'twitterrewardpercent'        => '_socialrewardsystempercent_twitter' ,
                    'rewardtypetwitter_follow'    => '_social_rewardsystem_options_twitter_follow' ,
                    'twitterrewardpoints_follow'  => '_socialrewardsystempoints_twitter_follow' ,
                    'twitterrewardpercent_follow' => '_socialrewardsystempercent_twitter_follow' ,
                    'rewardtypeok_follow'         => '_social_rewardsystem_options_ok_follow' ,
                    'okrewardpoints_follow'       => '_socialrewardsystempoints_ok_follow' ,
                    'okrewardpercent_follow'      => '_socialrewardsystempercent_ok_follow' ,
                    'rewardtypegoogle'            => '_social_rewardsystem_options_google' ,
                    'googlerewardpoints'          => '_socialrewardsystempoints_google' ,
                    'googlerewardpercent'         => '_socialrewardsystempercent_google' ,
                    'rewardtypevk'                => '_social_rewardsystem_options_vk' ,
                    'vkrewardpoints'              => '_socialrewardsystempoints_vk' ,
                    'vkrewardpercent'             => '_socialrewardsystempercent_vk' ,
                    'rewardtypeinstagram'         => '_social_rewardsystem_options_instagram' ,
                    'instagramrewardpoints'       => '_socialrewardsystempoints_instagram' ,
                    'instagramrewardpercent'      => '_socialrewardsystempercent_instagram'
                        ) ;
                $this->update_product_meta_for_social_reward( $ProductId , $ProductLevelMetaKey ) ;
            } elseif ( get_option( 'fp_product_selection_type' ) == 3 || get_option( 'fp_product_selection_type' ) == 4 ) {
                $ProductCat = get_the_terms( $ProductId , 'product_cat' ) ;
                if ( srp_check_is_array( $ProductCat ) ) {
                    $CategoryList = (get_option( 'fp_product_selection_type' ) == 3) ? get_terms( 'product_cat' ) : get_option( 'fp_selected_categories' ) ;
                    if ( srp_check_is_array( $CategoryList ) ) {
                        foreach ( $CategoryList as $CategoryId ) {
                            if ( ! $this->check_if_product_is_in_selected_category( $CategoryId , $ProductCat ) )
                                continue ;

                            $CategoryId = is_object( $CategoryId ) ? $CategoryId->term_id : $CategoryId ;
                            if ( $_POST[ 'enabledisablereward' ] == '1' ) {
                                update_post_meta( $ProductId , '_socialrewardsystemcheckboxvalue' , 'yes' ) ;
                            } else {
                                update_post_meta( $ProductId , '_socialrewardsystemcheckboxvalue' , 'no' ) ;
                            }
                            update_post_meta( $ProductId , '_social_rewardsystem_options_facebook' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempoints_facebook' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempercent_facebook' , '' ) ;

                            update_post_meta( $ProductId , '_social_rewardsystem_options_twitter' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempoints_twitter' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempercent_twitter' , '' ) ;

                            update_post_meta( $ProductId , '_social_rewardsystem_options_twitter_follow' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempoints_twitter_follow' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempercent_twitter_follow' , '' ) ;


                            update_post_meta( $ProductId , '_social_rewardsystem_options_google' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempoints_google' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempercent_google' , '' ) ;

                            update_post_meta( $ProductId , '_social_rewardsystem_options_vk' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempoints_vk' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempercent_vk' , '' ) ;

                            update_post_meta( $ProductId , '_social_rewardsystem_options_instagram' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempoints_instagram' , '' ) ;
                            update_post_meta( $ProductId , '_socialrewardsystempercent_instagram' , '' ) ;

                            $ProductMetaKey = array(
                                'enabledisablereward'         => 'enable_social_reward_system_category' ,
                                'rewardtypefacebook'          => 'social_facebook_enable_rs_rule' ,
                                'facebookrewardpoints'        => 'social_facebook_rs_category_points' ,
                                'facebookrewardpercent'       => 'social_facebook_rs_category_percent' ,
                                'rewardtypefacebook_share'    => 'social_facebook_share_enable_rs_rule' ,
                                'facebookrewardpoints_share'  => 'social_facebook_share_rs_category_points' ,
                                'facebookrewardpercent_share' => 'social_facebook_share_rs_category_percent' ,
                                'rewardtypetwitter'           => 'social_twitter_enable_rs_rule' ,
                                'twitterrewardpoints'         => 'social_twitter_rs_category_points' ,
                                'twitterrewardpercent'        => 'social_twitter_rs_category_percent' ,
                                'rewardtypetwitter_follow'    => 'social_twitter_follow_enable_rs_rule' ,
                                'twitterrewardpoints_follow'  => 'social_twitter_follow_rs_category_points' ,
                                'twitterrewardpercent_follow' => 'social_twitter_follow_rs_category_percent' ,
                                'rewardtypeok_follow'         => 'social_ok_follow_enable_rs_rule' ,
                                'okrewardpoints_follow'       => 'social_ok_follow_rs_category_points' ,
                                'okrewardpercent_follow'      => 'social_ok_follow_rs_category_percent' ,
                                'rewardtypegoogle'            => 'social_google_enable_rs_rule' ,
                                'googlerewardpoints'          => 'social_google_rs_category_points' ,
                                'googlerewardpercent'         => 'social_google_rs_category_percent' ,
                                'rewardtypevk'                => 'social_vk_enable_rs_rule' ,
                                'vkrewardpoints'              => 'social_vk_rs_category_points' ,
                                'vkrewardpercent'             => 'social_vk_rs_category_percent' ,
                                'rewardtypeinstagram'         => 'social_instagram_enable_rs_rule' ,
                                'instagramrewardpoints'       => 'social_instagram_rs_category_points' ,
                                'instagramrewardpercent'      => 'social_instagram_rs_category_percent'
                                    ) ;
                            $this->update_category_meta_for_bulk_update_in_social_reward( $CategoryId , $ProductLevelMetaKey ) ;
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

        public static function update_product_meta_for_social_reward( $ProductId , $ProductMetaKey ) {
            if ( get_option( 'fp_enable_reward' ) == '1' ) {
                update_post_meta( $ProductId , $ProductMetaKey[ 'enabledisablereward' ] , 'yes' ) ;
            } else {
                update_post_meta( $ProductId , $ProductMetaKey[ 'enabledisablereward' ] , 'no' ) ;
            }

            if ( get_option( 'fp_fblike_reward_type' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'rewardtypefacebook' ] , get_option( 'fp_fblike_reward_type' ) ) ;
            if ( get_option( 'fp_fblike_reward_points' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'facebookrewardpoints' ] , get_option( 'fp_fblike_reward_points' ) ) ;
            if ( get_option( 'fp_fblike_reward_percent' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'facebookrewardpercent' ] , get_option( 'fp_fblike_reward_percent' ) ) ;

            if ( get_option( 'fp_fbshare_reward_type' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'rewardtypefacebook_share' ] , get_option( 'fp_fbshare_reward_type' ) ) ;
            if ( get_option( 'fp_fbshare_reward_points' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'facebookrewardpoints_share' ] , get_option( 'fp_fbshare_reward_points' ) ) ;
            if ( get_option( 'fp_fbshare_reward_percent' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'facebookrewardpercent_share' ] , get_option( 'fp_fbshare_reward_percent' ) ) ;

            if ( get_option( 'fp_twitter_reward_type' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'rewardtypetwitter' ] , get_option( 'fp_twitter_reward_type' ) ) ;
            if ( get_option( 'fp_twitter_reward_points' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'twitterrewardpoints' ] , get_option( 'fp_twitter_reward_points' ) ) ;
            if ( get_option( 'fp_twitter_reward_percent' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'twitterrewardpercent' ] , get_option( 'fp_twitter_reward_percent' ) ) ;

            if ( get_option( 'fp_twitter_follow_reward_type' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'rewardtypetwitter_follow' ] , get_option( 'fp_twitter_follow_reward_type' ) ) ;
            if ( get_option( 'fp_twitter_follow_reward_points' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'twitterrewardpoints_follow' ] , get_option( 'fp_twitter_follow_reward_points' ) ) ;
            if ( get_option( 'fp_twitter_follow_reward_percent' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'twitterrewardpercent_follow' ] , get_option( 'fp_twitter_follow_reward_percent' ) ) ;

            if ( get_option( 'fp_ok_reward_type' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'rewardtypeok_follow' ] , get_option( 'fp_ok_reward_type' ) ) ;
            if ( get_option( 'fp_ok_reward_points' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'okrewardpoints_follow' ] , get_option( 'fp_ok_reward_points' ) ) ;
            if ( get_option( 'fp_ok_reward_percent' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'okrewardpercent_follow' ] , get_option( 'fp_ok_reward_percent' ) ) ;

            if ( get_option( 'fp_glpus_reward_type' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'rewardtypegoogle' ] , get_option( 'fp_glpus_reward_type' ) ) ;
            if ( get_option( 'fp_glpus_reward_points' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'googlerewardpoints' ] , get_option( 'fp_glpus_reward_points' ) ) ;
            if ( get_option( 'fp_glpus_reward_percent' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'googlerewardpercent' ] , get_option( 'fp_glpus_reward_percent' ) ) ;

            if ( get_option( 'fp_vk_reward_type' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'rewardtypevk' ] , get_option( 'fp_vk_reward_type' ) ) ;
            if ( get_option( 'fp_vk_reward_points' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'vkrewardpoints' ] , get_option( 'fp_vk_reward_points' ) ) ;
            if ( get_option( 'fp_vk_reward_percent' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'vkrewardpercent' ] , get_option( 'fp_vk_reward_percent' ) ) ;

            if ( get_option( 'fp_instagram_reward_type' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'rewardtypeinstagram' ] , get_option( 'fp_instagram_reward_type' ) ) ;
            if ( get_option( 'fp_instagram_reward_points' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'instagramrewardpoints' ] , get_option( 'fp_instagram_reward_points' ) ) ;
            if ( get_option( 'fp_instagram_reward_percent' ) )
                update_post_meta( $ProductId , $ProductMetaKey[ 'instagramrewardpercent' ] , get_option( 'fp_instagram_reward_percent' ) ) ;
        }

        public static function update_category_meta_for_bulk_update_in_social_reward( $CategoryId , $ProductMetaKey ) {
            if ( get_option( 'fp_enable_reward' ) == '1' ) {
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'enabledisablereward' ] , 'yes' ) ;
            } else {
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'enabledisablereward' ] , 'no' ) ;
            }

            if ( get_option( 'fp_fblike_reward_type' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'rewardtypefacebook' ] , get_option( 'fp_fblike_reward_type' ) ) ;
            if ( get_option( 'fp_fblike_reward_points' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'facebookrewardpoints' ] , get_option( 'fp_fblike_reward_points' ) ) ;
            if ( get_option( 'fp_fblike_reward_percent' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'facebookrewardpercent' ] , get_option( 'fp_fblike_reward_percent' ) ) ;

            if ( get_option( 'fp_fbshare_reward_type' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'rewardtypefacebook_share' ] , get_option( 'fp_fbshare_reward_type' ) ) ;
            if ( get_option( 'fp_fbshare_reward_points' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'facebookrewardpoints_share' ] , get_option( 'fp_fbshare_reward_points' ) ) ;
            if ( get_option( 'fp_fbshare_reward_percent' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'facebookrewardpercent_share' ] , get_option( 'fp_fbshare_reward_percent' ) ) ;

            if ( get_option( 'fp_twitter_reward_type' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'rewardtypetwitter' ] , get_option( 'fp_twitter_reward_type' ) ) ;
            if ( get_option( 'fp_twitter_reward_points' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'twitterrewardpoints' ] , get_option( 'fp_twitter_reward_points' ) ) ;
            if ( get_option( 'fp_twitter_reward_percent' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'twitterrewardpercent' ] , get_option( 'fp_twitter_reward_percent' ) ) ;

            if ( get_option( 'fp_twitter_follow_reward_type' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'rewardtypetwitter_follow' ] , get_option( 'fp_twitter_follow_reward_type' ) ) ;
            if ( get_option( 'fp_twitter_follow_reward_points' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'twitterrewardpoints_follow' ] , get_option( 'fp_twitter_follow_reward_points' ) ) ;
            if ( get_option( 'fp_twitter_follow_reward_percent' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'twitterrewardpercent_follow' ] , get_option( 'fp_twitter_follow_reward_percent' ) ) ;

            if ( get_option( 'fp_ok_reward_type' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'rewardtypeok_follow' ] , get_option( 'fp_ok_reward_type' ) ) ;
            if ( get_option( 'fp_ok_reward_points' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'okrewardpoints_follow' ] , get_option( 'fp_ok_reward_points' ) ) ;
            if ( get_option( 'fp_ok_reward_percent' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'okrewardpercent_follow' ] , get_option( 'fp_ok_reward_percent' ) ) ;

            if ( get_option( 'fp_glpus_reward_type' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'rewardtypegoogle' ] , get_option( 'fp_glpus_reward_type' ) ) ;
            if ( get_option( 'fp_glpus_reward_points' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'googlerewardpoints' ] , get_option( 'fp_glpus_reward_points' ) ) ;
            if ( get_option( 'fp_glpus_reward_percent' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'googlerewardpercent' ] , get_option( 'fp_glpus_reward_percent' ) ) ;

            if ( get_option( 'fp_vk_reward_type' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'rewardtypevk' ] , get_option( 'fp_vk_reward_type' ) ) ;
            if ( get_option( 'fp_vk_reward_points' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'vkrewardpoints' ] , get_option( 'fp_vk_reward_points' ) ) ;
            if ( get_option( 'fp_vk_reward_percent' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'vkrewardpercent' ] , get_option( 'fp_vk_reward_percent' ) ) ;

            if ( get_option( 'fp_instagram_reward_type' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'rewardtypeinstagram' ] , get_option( 'fp_instagram_reward_type' ) ) ;
            if ( get_option( 'fp_instagram_reward_points' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'instagramrewardpoints' ] , get_option( 'fp_instagram_reward_points' ) ) ;
            if ( get_option( 'fp_instagram_reward_percent' ) )
                srp_update_term_meta( $CategoryId , $ProductMetaKey[ 'instagramrewardpercent' ] , get_option( 'fp_instagram_reward_percent' ) ) ;
        }

    }

}