<?php

/*
 * Buddypress Compatibility
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSBuddypressCompatibility' ) ) {

	class RSBuddypressCompatibility {

		public static function init() {
			//Buddypress group create 
			add_action( 'groups_group_create_complete' , array( __CLASS__ , 'award_points_for_group_creation' ) , 10 , 4 ) ;
			//Buddypress post create  
			add_action( 'bp_activity_comment_posted' , array( __CLASS__ , 'award_points_for_post_comment' ) , 10 , 3 ) ;
			//Buddypress post comment  
			add_action( 'bp_activity_posted_update' , array( __CLASS__ , 'award_points_for_post_creation' ) , 10 , 3 ) ;
		}

		public static function buddypress_restriction( $Action, $Points ) {
			if ( 'group_creation' == $Action ) {
				$Enable = get_option( 'rs_enable_points_for_bp_group_create' ) ;
				$Limit  = ( int ) get_option( 'rs_points_for_bp_group_create_limit' ) ;
				$Count  = ( int ) bp_get_total_group_count_for_user( get_current_user_id() ) ;
			}

			if ( 'post_comment' == $Action ) {
				$Enable = get_option( 'rs_enable_points_for_bp_postcomment' ) ;
			}

			if ( 'post_creation' == $Action ) {
				$Enable = get_option( 'rs_enable_points_for_bp_post_create' ) ;
			}

			if ( 'yes' == $Enable && ! empty( $Points ) ) {
				if ( 'group_creation' == $Action ) {
					if ( $Count <= $Limit ) {
						return true ;
					} else {
						return false ;
					}
				}
				return true ;
			}
			return false ;
		}

		public static function award_points_for_group_creation() {
			if ( ! self::buddypress_restriction( 'group_creation' , get_option( 'rs_points_for_bp_group_create' ) ) ) {
				return ;
			}

			$new_obj = new RewardPointsOrder( 0 , 'no' ) ;
			$Values  = array( 'pointstoinsert' => get_option( 'rs_points_for_bp_group_create' ) , 'event_slug' => 'RPFBPG' , 'user_id' => get_current_user_id() , 'totalearnedpoints' => $Points ) ;
			$new_obj->total_points_management( $Values ) ;
		}

		public static function award_points_for_post_comment( $comment_id, $params, $parent_activity ) {
			if ( ! self::buddypress_restriction( 'post_comment' , get_option( 'rs_points_for_bp_postcomment' ) ) ) {
				return ;
			}

			$PostId = '' ;
			if ( is_object( $parent_activity ) ) {
				$PostId = $parent_activity->id ;
			}

			if ( empty( $PostId ) ) {
				return ;
			}

			$CheckIfAlreadyAwarded = get_user_meta( get_current_user_id() , 'rs_bp_groupcomment_check' . $PostId , true ) ;
			if ( 1 == $CheckIfAlreadyAwarded ) {
				return ;
			}

			$new_obj = new RewardPointsOrder( 0 , 'no' ) ;
			$Values  = array( 'pointstoinsert' => get_option( 'rs_points_for_bp_postcomment' ) , 'event_slug' => 'RPFBPC' , 'user_id' => get_current_user_id() , 'product_id' => $post_id , 'totalearnedpoints' => $Points ) ;
			$new_obj->total_points_management( $Values ) ;
			update_user_meta( get_current_user_id() , 'rs_bp_groupcomment_check' . $PostId , '1' ) ;
		}

		public static function award_points_for_post_creation( $content, $userid, $activity_id ) {
			if ( ! self::buddypress_restriction( 'post_creation' , get_option( 'rs_points_for_bp_post_create' ) ) ) {
				return ;
			}

			$new_obj = new RewardPointsOrder( 0 , 'no' ) ;
			$Values  = array( 'pointstoinsert' => get_option( 'rs_points_for_bp_post_create' ) , 'event_slug' => 'RPFBP' , 'user_id' => get_current_user_id() , 'totalearnedpoints' => $Points ) ;
			$new_obj->total_points_management( $Values ) ;
		}

	}

	RSBuddypressCompatibility::init() ;
}

