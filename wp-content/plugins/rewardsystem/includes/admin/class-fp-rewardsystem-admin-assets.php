<?php
/*
 * Admin Assests
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSAdminAssets' ) ) {

	class RSAdminAssets {

		public static function init() {
			add_filter( 'woocommerce_custom_nav_menu_items', array( __CLASS__, 'set_custom_menu_items' ) );
			add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box_for_earned' ) );
			add_action( 'manage_shop_order_posts_custom_column', array( __CLASS__, 'srp_custom_orders_list_column_content' ), 12, 2 );
			add_filter( 'manage_edit-shop_order_columns', array( __CLASS__, 'srp_custom_shop_order_column' ), 12 );
			add_filter( 'woocommerce_shop_order_list_table_columns', array( __CLASS__, 'srp_custom_shop_order_column' ), 12 );
			add_action( 'woocommerce_shop_order_list_table_custom_column', array( __CLASS__, 'srp_custom_orders_list_column_content' ), 11, 2 );
			add_filter( 'views_edit-shop_order', array( __CLASS__, 'srp_custom_menu_referrer_name' ) );
			add_filter( 'request', array( __CLASS__, 'srp_custom_menu_request_query' ) );
			add_action( 'admin_init', array( __CLASS__, 'preview_emails' ) );
		}

		public static function menu_restriction_based_on_user_role() {

			$all_tabs   = array( 'fprsgeneral', 'fprsmodules', 'fprsaddremovepoints', 'fprsmessage', 'fprslocalization', 'fprsuserrewardpoints', 'fprsmasterlog', 'fprssupport', 'fprsadvanced', 'fprsshortcodes' );
			$admin_user = new WP_User( get_current_user_id() );
			if ( ! is_object( $admin_user ) ) {
				return $all_tabs;
			}

			$admin_roles = $admin_user->roles;
			$tab_data    = array();
			if ( ! srp_check_is_array( $admin_roles ) ) {
				return $all_tabs;
			}

			foreach ( $admin_roles as $admin_role ) {
				$tab_data[ $admin_role ] = get_option( 'rewardpoints_userrole_menu_restriction' . $admin_role );
			}

			if ( ! srp_check_is_array( array_filter( $tab_data ) ) ) {
				return $all_tabs;
			}

			$selected_tabs = array();
			foreach ( $tab_data as $role => $tabs ) {
				if ( ! srp_check_is_array( $tabs ) ) {
					continue;
				}

				foreach ( $tabs as $tab_name ) {
					$selected_tabs[] = $tab_name;
				}
			}

			$tabs = srp_check_is_array( $selected_tabs ) ? $selected_tabs : $all_tabs;
						/**
						 * Hook:rs_display_tabs_in_reward_system.
						 *
						 * @since 1.0
						 */
			return apply_filters( 'rs_display_tabs_in_reward_system', $tabs );
		}

		public static function set_custom_menu_items( $endpoints ) {
			$reward_content_title    = get_option( 'rs_my_reward_content_title' );
			$url_title               = '' != get_option( 'rs_my_reward_url_title' ) ? get_option( 'rs_my_reward_url_title' ) : 'sumo-rewardpoints';
			$endpoints[ $url_title ] = $reward_content_title;
			return $endpoints;
		}

		public static function add_meta_box_for_earned() {
			if ( 'yes' != get_option( 'rs_product_purchase_activated' ) && 'yes' != get_option( 'rs_redeeming_activated' ) ) {
				return;
			}

			add_meta_box( 'order_earned_points', 'Earned Point and Redeem Points For Current Order', array( __CLASS__, 'add_meta_box_to_earned_and_redeem_points' ), 'shop_order', 'normal', 'low' );
		}

		public static function add_meta_box_to_earned_and_redeem_points( $order ) {
			if ( 'yes' != get_option( 'rs_product_purchase_activated' ) && 'yes' != get_option( 'rs_redeeming_activated' ) ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_enable_msg_for_earned_points' ) && 'yes' != get_option( 'rs_enable_msg_for_redeem_points' ) ) {
				return;
			}

			if ( ! is_object( $order ) ) {
				return;
			}

			$earned_redeemed_message = get_earned_redeemed_points_message( $order->ID );
			if ( ! srp_check_is_array( $earned_redeemed_message ) ) {
				return;
			}

			foreach ( $earned_redeemed_message as $msgforearnedpoints => $msgforredeempoints ) {
				$replacemsgforearnedpoints = $msgforearnedpoints;
				$replacemsgforredeempoints = $msgforredeempoints;
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
                        }';

			wp_register_style( 'fp-srp-earned-redeemed-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
			wp_enqueue_style( 'fp-srp-earned-redeemed-style' );
			wp_add_inline_style( 'fp-srp-earned-redeemed-style', $contents );

			?>
			<table class="fp-srp-meta-box-table">
				<tr>
					<?php if ( 'yes' == get_option( 'rs_enable_msg_for_earned_points' ) && 'yes' == get_option( 'rs_product_purchase_activated' ) ) { ?>
						<td class="fp-srp-title">
							<h3><?php esc_html_e( 'Earned Points', 'rewardsystem' ); ?></h3>
						</td>
					<?php } if ( 'yes' == get_option( 'rs_enable_msg_for_redeem_points' ) && 'yes' == get_option( 'rs_redeeming_activated' ) ) { ?>
						<td class="fp-srp-title">
							<h3><?php esc_html_e( 'Redeem Points', 'rewardsystem' ); ?></h3>
						</td>
					<?php } ?>
				</tr>
				<tr>
					<?php if ( 'yes' == get_option( 'rs_enable_msg_for_earned_points' ) && 'yes' == get_option( 'rs_product_purchase_activated' ) ) { ?>
						<td class="fp-srp-points">
							<?php echo do_shortcode( $replacemsgforearnedpoints ); ?>
						</td>
					<?php } if ( 'yes' == get_option( 'rs_enable_msg_for_redeem_points' ) && 'yes' == get_option( 'rs_redeeming_activated' ) ) { ?>
						<td class="fp-srp-points">
							<?php echo do_shortcode( $replacemsgforredeempoints ); ?>
						</td>
					<?php } ?>
				</tr>
			</table>
			<?php
		}

		public static function srp_custom_orders_list_column_content( $column, $post_id ) {
			self::referrer_name_column( $column, $post_id );

			self::bonus_awarded_column( $column, $post_id );
		}

		public static function referrer_name_column( $column, $post_id ) {

			if ( 'yes' !== get_option( 'rs_referral_activated' ) ) {
				return;
			}

			if ( 'referrer_name' !== $column ) {
				return;
			}

			$order         = wc_get_order( $post_id );
			$referrer_name = $order->get_meta( '_referrer_name' );
			if ( ! $referrer_name ) {
				echo esc_attr( '-' );
				return;
			}

			if ( get_user_by( 'ID', $referrer_name ) ) {
				$referrer_name = get_user_by( 'ID', $referrer_name )->user_login;
			} elseif ( '2' === get_option( 'rs_generate_referral_link_based_on_user' ) ) {
					$referrer_name = ! empty( get_user_by( 'ID', $referrer_name )->user_login ) ? get_user_by( 'ID', $referrer_name )->user_login : $referrer_name;
			}

			echo esc_attr( ! empty( $referrer_name ) ? $referrer_name : '-' );
		}

		public static function bonus_awarded_column( $column, $post_id ) {

			if ( 'yes' !== get_option( 'rs_bonus_points_activated' ) ) {
					return;
			}

			if ( 'rs_bonus_awarded' !== $column ) {
					return;
			}

			$order = wc_get_order( $post_id );
			echo esc_attr( ! empty( $order->get_meta( 'rs_recorded_order_no_of_orders_type' ) ) ? __( 'Yes', 'rewardsystem' ) : '-' );
		}

		public static function srp_custom_shop_order_column( $columns ) {
			$display_referrer_column = false;
			if ( 'yes' === get_option( 'rs_referral_activated' ) ) {
				$display_referrer_column = true;
			}

			$display_bonus_column = false;
			if ( 'yes' === get_option( 'rs_bonus_points_activated' ) ) {
				$display_bonus_column = true;
			}

			$add_column = array();

			foreach ( $columns as $key => $column ) {
				$add_column[ $key ] = $column;

				if ( ( 'order_status' === $key ) && $display_referrer_column ) {
					$add_column['referrer_name'] = __( 'Referrer Name', 'rewardsystem' );
				}

				if ( ( 'order_status' === $key ) && $display_bonus_column ) {
						$add_column['rs_bonus_awarded'] = __( 'Bonus Points', 'rewardsystem' );
				}
			}

			return $add_column;
		}

		/*
		 * Add Custom a views
		 */

		public static function srp_custom_menu_referrer_name( $views ) {
			if ( 'yes' != get_option( 'rs_referral_activated' ) ) {
				return $views;
			}

			$referrer_name_count = self::get_referrer_name_count();
			if ( $referrer_name_count < 0 ) {
				return $views;
			}

			global $post_type, $wp_query;

			$ref_name_class = '';
			if ( isset( $wp_query->query['meta_key'] ) && '_referrer_name' == $wp_query->query['meta_key'] ) {
				$ref_name_class = isset( $_GET['srp_referrer_name'] ) ? 'current' : '';
			}

			$query_string = admin_url( 'edit.php?post_type=shop_order' );
			$query_string = add_query_arg( 'srp_referrer_name', 'yes', $query_string );

			$views['srp_referrer_name'] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $ref_name_class ) . '">' . __( 'Referrer Name', 'rewardsystem' ) . ' (' . $referrer_name_count . ')</a>';

			return $views;
		}

		/**
		 * Filters and sorting handler
		 */
		public static function srp_custom_menu_request_query( $vars ) {
			if ( 'yes' != get_option( 'rs_referral_activated' ) ) {
				return $vars;
			}

			global $typenow, $wp_query, $wp_post_statuses;

			if ( 'shop_order' === $typenow ) {
				if ( isset( $_GET['srp_referrer_name'] ) ) {
					$vars['meta_key']     = '_referrer_name';
					$vars['meta_value']   = '';
					$vars['meta_compare'] = '!=';
				}
			}

			return $vars;
		}

		/**
		 *  Get Referrer Name Count
		 */
		public static function get_referrer_name_count() {
			$args = array(
				'posts_per_page' => -1,
				'post_type'      => 'shop_order',
				'post_status'    => 'any',
				'meta_key'       => '_referrer_name',
				'meta_compare'   => 'EXISTS',
				'fields'         => 'ids',
			);

			$count = array_filter( self::srp_check_query_having_posts( $args ) );

			return count( $count );
		}

		public static function srp_check_query_having_posts( $args ) {
			$post       = array();
			$query_post = new WP_Query( $args );
			if ( isset( $query_post->posts ) && srp_check_is_array( $query_post->posts ) ) {
				$post = $query_post->posts;
			}

			return $post;
		}

				/**
				 *  Preview emails.
				 */
		public static function preview_emails() {
			if ( ! isset( $_GET['rs_preview_email_template'] ) ) {
				return;
			}

			if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'rs-preview-mail' ) ) ) {
				return;
			}

			if ( ! isset( $_REQUEST['rs_email_template_id'] ) ) {
				return;
			}

			global $wpdb;
			$template_id   = absint( $_REQUEST['rs_email_template_id'] );
			$template_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rs_templates_email WHERE id=%d", $template_id ), OBJECT );
			if ( ! srp_check_is_array( $template_data ) ) {
				return;
			}

			$template_object = isset( $template_data[0] ) ? $template_data[0] : false;
			if ( ! is_object( $template_object ) ) {
				return;
			}

			$user_ids = get_users( array( 'fields' => 'ids' ) );
			if ( ! srp_check_is_array( $user_ids ) ) {
				return;
			}

			$user_id = array_rand( $user_ids, 1 );
			$user    = get_user_by( 'ID', $user_id );
			if ( ! is_object( $user ) ) {
				return;
			}

			$points_data = new RS_Points_Data( $user_id );
			$points      = $points_data->total_available_points();
			if ( ! $points ) {
				$points = 100;
			}

			$points_in_currency = currency_value_for_available_points( $user_id );
			if ( ! $points_in_currency ) {
				'<span class="rs_user_total_points"><b>' . $points . ' (' . srp_formatted_price( round_off_type_for_currency( redeem_point_conversion( $points, $user_id, 'price' ) ) ) . ')</b></span>';
			}

			$referral_url      = '' != get_option( 'rs_referral_link_site_referral_url' ) ? get_option( 'rs_referral_link_site_referral_url' ) : site_url();
			$site_referral_url = 'yes' == get_option( 'rs_restrict_referral_points_for_same_ip' ) ? esc_url_raw(
				add_query_arg(
					array(
						'ref' => $user->user_login,
						'ip'  => base64_encode( get_referrer_ip_address() ),
					),
					$referral_url
				)
			) : esc_url_raw( add_query_arg( array( 'ref' => $user->user_login ), $referral_url ) );
			$site_referral_url = 'yes' == get_option( 'rs_referral_activated' ) ? '<a href=' . $site_referral_url . '>' . $site_referral_url . '</a>' : '';

			$search        = array( '{rspoints}', '{rs_points_in_currency}', '{rssitelink}', '{rsfirstname}', '{rslastname}', '{rs_earned_points}', '{rs_redeemed_points}', '{site_referral_url}' );
			$replace       = array( $points, currency_value_for_available_points( $user_id ), '<a href=' . site_url() . '>' . site_url() . '</a>', $user->first_name, $user->last_name, 10, 10, $site_referral_url );
			$email_message = str_replace( $search, $replace, $template_object->message );

			$mailer        = WC()->mailer();
			$email         = new WC_Email();
			$email_heading = $template_object->subject;
						/**
			 * Hook:woocommerce_mail_content.
			 *
			 * @since 1.0
			 */
			$message = apply_filters( 'woocommerce_mail_content', $email->style_inline( $mailer->wrap_message( $email_heading, $email_message ) ) );
			echo wp_kses_post( $message );
			exit;
		}
	}

	RSAdminAssets::init();
}
