<?php
/*
 * Admin Assests
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSAdminAssets' ) ) {

	class RSAdminAssets {

		public static function init() {
			add_filter( 'woocommerce_custom_nav_menu_items' , array( __CLASS__ , 'set_custom_menu_items' ) ) ;
			add_action( 'add_meta_boxes' , array( __CLASS__ , 'add_meta_box_for_earned' ) ) ;
			add_action( 'manage_shop_order_posts_custom_column' , array( __CLASS__ , 'srp_custom_orders_list_column_content' ) , 12 , 2 ) ;
			add_filter( 'manage_edit-shop_order_columns' , array( __CLASS__ , 'srp_custom_shop_order_column' ) , 12 ) ;
			add_filter( 'views_edit-shop_order' , array( __CLASS__ , 'srp_custom_menu_referrer_name' ) ) ;
			add_filter( 'request' , array( __CLASS__ , 'srp_custom_menu_request_query' ) ) ;
		}

		public static function menu_restriction_based_on_user_role() {
			
			$all_tabs   = array( 'fprsgeneral' , 'fprsmodules' , 'fprsaddremovepoints' , 'fprsmessage' , 'fprslocalization' , 'fprsuserrewardpoints' , 'fprsmasterlog' , 'fprssupport' , 'fprsadvanced' , 'fprsshortcodes' ) ;
			$admin_user = new WP_User( get_current_user_id() ) ;
			if ( ! is_object( $admin_user ) ) {
				return $all_tabs ;
			}

			$admin_roles = $admin_user->roles ;
			$tab_data    = array() ;
			if ( ! srp_check_is_array( $admin_roles ) ) {
				return $all_tabs ;
			}

			foreach ( $admin_roles as $admin_role ) {
				$tab_data[ $admin_role ] = get_option( 'rewardpoints_userrole_menu_restriction' . $admin_role ) ;
			}

			if ( ! srp_check_is_array( array_filter( $tab_data ) ) ) {
				return $all_tabs ;
			}

			$selected_tabs = array() ;
			foreach ( $tab_data as $role => $tabs ) {
				if ( ! srp_check_is_array($tabs) ) {
					continue ;
				}

				foreach ( $tabs as $tab_name ) {
					$selected_tabs[] = $tab_name ;
				}
			}
			
			$tabs = srp_check_is_array( $selected_tabs ) ? $selected_tabs : $all_tabs;

			return apply_filters('rs_display_tabs_in_reward_system', $tabs);
		}

		public static function set_custom_menu_items( $endpoints ) {
			$reward_content_title    = get_option( 'rs_my_reward_content_title' ) ;
			$url_title               = ''!= get_option( 'rs_my_reward_url_title' ) ? get_option( 'rs_my_reward_url_title' ) : 'sumo-rewardpoints' ;
			$endpoints[ $url_title ] = $reward_content_title ;
			return $endpoints ;
		}

		public static function add_meta_box_for_earned() {
			if ( 'yes' != get_option( 'rs_product_purchase_activated' ) && 'yes' != get_option( 'rs_redeeming_activated' )) {
				return ;
			}

			add_meta_box( 'order_earned_points' , 'Earned Point and Redeem Points For Current Order' , array( __CLASS__ , 'add_meta_box_to_earned_and_redeem_points' ) , 'shop_order' , 'normal' , 'low' ) ;
		}

		public static function add_meta_box_to_earned_and_redeem_points( $order ) {
			if ( 'yes' != get_option( 'rs_product_purchase_activated' ) && 'yes' != get_option( 'rs_redeeming_activated' )  ) {
				return ;
			}

			if ( 'yes' != get_option( 'rs_enable_msg_for_earned_points' ) && 'yes' != get_option( 'rs_enable_msg_for_redeem_points' ) ) {
				return ;
			}

			if ( ! is_object( $order ) ) {
				return ;
			}

			$earned_redeemed_message = get_earned_redeemed_points_message( $order->ID ) ;
			if ( ! srp_check_is_array( $earned_redeemed_message ) ) {
				return ;
			}

			foreach ( $earned_redeemed_message as $msgforearnedpoints => $msgforredeempoints ) {
				$replacemsgforearnedpoints = $msgforearnedpoints ;
				$replacemsgforredeempoints = $msgforredeempoints ;
			}
						
						$contents = '.fp-srp-meta-box-table{
                                width : 100%;
                                border-radius: 10px; 
                                border-style: solid; 
                                border-color: #dfdfdf;
                        }
                        .fp-srp-earned-title{
                                text-align:center; 
                                background-color:#F1F1F1
                        }
                        .fp-srp-points{
                                text-align:center; 
                        }' ;
						
			wp_register_style( 'fp-srp-earned-redeemed-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
			wp_enqueue_style( 'fp-srp-earned-redeemed-style' ) ;
			wp_add_inline_style( 'fp-srp-earned-redeemed-style' , $contents ) ; 
						
			?>
			<table class="fp-srp-meta-box-table">
				<tr>
					<?php if ( 'yes' == get_option( 'rs_enable_msg_for_earned_points' ) && 'yes' == get_option( 'rs_product_purchase_activated' ) ) { ?>
						<td class="fp-srp-title">
							<h3><?php esc_html_e( 'Earned Points' , 'rewardsystem' ) ; ?></h3>
						</td>
					<?php } if ( 'yes' == get_option( 'rs_enable_msg_for_redeem_points' ) && 'yes' == get_option( 'rs_redeeming_activated' ) ) { ?>
						<td class="fp-srp-title">
							<h3><?php esc_html_e( 'Redeem Points' , 'rewardsystem' ) ; ?></h3>
						</td>
					<?php } ?>
				</tr>
				<tr>
					<?php if ( 'yes' == get_option( 'rs_enable_msg_for_earned_points' ) && 'yes' == get_option( 'rs_product_purchase_activated' ) ) { ?>
						<td class="fp-srp-points">
							<?php echo wp_kses_post($replacemsgforearnedpoints) ; ?>
						</td>
					<?php } if ( 'yes' == get_option( 'rs_enable_msg_for_redeem_points' ) && 'yes' == get_option( 'rs_redeeming_activated' ) ) { ?>
						<td class="fp-srp-points">
							<?php echo wp_kses_post($replacemsgforredeempoints) ; ?>
						</td>
					<?php } ?>
				</tr>
			</table>
			<?php
		}

		public static function srp_custom_orders_list_column_content( $column, $post_id ) {
			if ( 'yes' != get_option( 'rs_referral_activated' ) ) {
				return ;
			}

			if ( 'referrer_name' != $column ) {
				return ;
			}

			$referrer_name = get_post_meta( $post_id , '_referrer_name' , true ) ;
			if ( ! $referrer_name ) {
				echo esc_attr('-') ;
				return ;
			}

			if ( get_user_by( 'ID' , $referrer_name ) ) {
				$referrer_name = get_user_by( 'ID' , $referrer_name )->user_login ;
			} else {
				if ( 2 == get_option( 'rs_generate_referral_link_based_on_user' ) ) {
					$referrer_name = ! empty( get_user_by( 'ID' , $referrer_name )->user_login ) ? get_user_by( 'ID' , $referrer_name )->user_login : $referrer_name ;
				}
			}

			echo esc_attr(! empty( $referrer_name ) ? $referrer_name : '-') ;
		}

		public static function srp_custom_shop_order_column( $columns ) {
			if (  'yes' != get_option( 'rs_referral_activated' ) ) {
				return $columns ;
			}

			$add_column = array() ;

			foreach ( $columns as $key => $column ) {
				$add_column[ $key ] = $column ;

				if ( 'order_status' == $key ) {
					$add_column[ 'referrer_name' ] = __( 'Referrer Name' , 'rewardsystem' ) ;
				}
			}

			return $add_column ;
		}

		/*
		 * Add Custom a views
		 */

		public static function srp_custom_menu_referrer_name( $views ) {
			if ( 'yes' != get_option( 'rs_referral_activated' ) ) {
				return $views ;
			}

			$referrer_name_count = self::get_referrer_name_count() ;
			if ( $referrer_name_count < 0 ) {
				return $views ;
			}

			global $post_type , $wp_query ;

			$ref_name_class = '' ;
			if ( isset( $wp_query->query[ 'meta_key' ] ) && '_referrer_name' == $wp_query->query[ 'meta_key' ] ) {
				$ref_name_class = isset( $_GET[ 'srp_referrer_name' ] ) ? 'current' : '' ;
			}

			$query_string = admin_url( 'edit.php?post_type=shop_order' ) ;
			$query_string = add_query_arg( 'srp_referrer_name' , 'yes' , $query_string ) ;

			$views[ 'srp_referrer_name' ] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $ref_name_class ) . '">' . __( 'Referrer Name' , 'rewardsystem' ) . ' (' . $referrer_name_count . ')</a>' ;

			return $views ;
		}

		/**
		 * Filters and sorting handler
		 */
		public static function srp_custom_menu_request_query( $vars ) {
			if ( 'yes' != get_option( 'rs_referral_activated' ) ) {
				return $vars ;
			}

			global $typenow , $wp_query , $wp_post_statuses ;

			if ( 'shop_order' === $typenow ) {
				if ( isset( $_GET[ 'srp_referrer_name' ] ) ) {
					$vars[ 'meta_key' ]     = '_referrer_name' ;
					$vars[ 'meta_value' ]   = '' ;
					$vars[ 'meta_compare' ] = '!=' ;
				}
			}

			return $vars ;
		}

		/**
		 *  Get Referrer Name Count
		 */
		public static function get_referrer_name_count() {
			$args = array(
				'posts_per_page' => -1 ,
				'post_type'      => 'shop_order' ,
				'post_status'    => 'any' ,
				'meta_key'       => '_referrer_name' ,
				'meta_compare'   => 'EXISTS' ,
				'fields'         => 'ids'
					) ;

			$count = array_filter( self::srp_check_query_having_posts( $args ) ) ;

			return count( $count ) ;
		}

		public static function srp_check_query_having_posts( $args ) {
			$post       = array() ;
			$query_post = new WP_Query( $args ) ;
			if ( isset( $query_post->posts ) && srp_check_is_array( $query_post->posts ) ) {
				$post       = $query_post->posts ;
			}

			return $post ;
		}

	}

	RSAdminAssets::init() ;
}
