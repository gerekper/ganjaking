<?php
/**
 * Class to handle display of Chained Products admin notices
 *
 * @package woocommerce-chained-products/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_CP_Admin_Notices' ) ) {

	/**
	 * Class to handle display of Chained Products review notice.
	 */
	class WC_CP_Admin_Notices {

		/**
		 * The msg heading for review.
		 *
		 * @var string
		 */
		public $msg = '';

		/**
		 * Constructor
		 */
		public function __construct() {
			add_filter( 'plugin_action_links_' . plugin_basename( WC_CP_PLUGIN_FILE ), array( $this, 'plugin_action_links' ) );

			add_action( 'admin_notices', array( $this, 'admin_notice_sa_needs_wc_25_above' ) );

			add_action( 'admin_notices', array( $this, 'sa_cp_show_review_notice' ) );
			add_action( 'admin_init', array( $this, 'sa_cp_update_notice_action' ) );
		}

		/**
		 * Function to handle WC compatibility related function call from appropriate class.
		 *
		 * @param string $function_name Function to call.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return mixed Result of function call.
		 */
		public function __call( $function_name, $arguments = array() ) {

			if ( ! is_callable( 'Chained_Products_WC_Compatibility', $function_name ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( 'Chained_Products_WC_Compatibility::' . $function_name, $arguments );
			} else {
				return call_user_func( 'Chained_Products_WC_Compatibility::' . $function_name );
			}
		}

		/**
		 * Function to add more action on plugins page
		 *
		 * @param array $links Existing links.
		 * @return array $links
		 */
		public function plugin_action_links( $links ) {
			$args = array(
				'page'    => 'wc-settings',
				'tab'     => 'products',
				'section' => 'wc_chained_products',
			);

			$settings_url  = add_query_arg( $args, admin_url( 'admin.php' ) );
			$shortcode_url = add_query_arg( 'page', 'cp-shortcode', admin_url( 'admin.php' ) );

			$action_links = array(
				'settings'  => '<a target="_blank" href="' . esc_url( $settings_url ) . '">' . __( 'Settings', 'woocommerce-chained-products' ) . '</a>',
				'shortcode' => '<a target="_blank" href="' . esc_url( $shortcode_url ) . '">' . __( 'Shortcode', 'woocommerce-chained-products' ) . '</a>',
				'docs'      => '<a target="_blank" href="' . esc_url( 'https://docs.woocommerce.com/document/chained-products/' ) . '">' . __( 'Docs', 'woocommerce-chained-products' ) . '</a>',
				'support'   => '<a target="_blank" href="' . esc_url( 'https://woocommerce.com/my-account/create-a-ticket/' ) . '">' . __( 'Support', 'woocommerce-chained-products' ) . '</a>',
				'review'    => '<a target="_blank" href="' . esc_url( 'https://woocommerce.com/products/chained-products/#comments' ) . '">' . __( 'Review', 'woocommerce-chained-products' ) . '</a>',
			);

			return array_merge( $action_links, $links );
		}

		/**
		 * Function to show admin notice that Chained Products works with WC 2.5.0+
		 */
		public function admin_notice_sa_needs_wc_25_above() {

			if ( ! $this->is_wc_gte_25() ) {
				?>
				<div class="updated error">
					<p>
					<?php
						printf(
							'<strong>' . esc_html__( 'Important - ', 'woocommerce-chained-products' ) . '</strong>' . esc_html__( 'WooCommerce Chained Products plugin is active but it will only work with WooCommerce 2.5+. ', 'woocommerce-chained-products' ) . '<a href="%s">' . esc_html__( 'Please update WooCommerce to the latest version', 'woocommerce-chained-products' ) . '</a>',
							esc_url( admin_url( 'plugins.php?' ) )
						);
					?>
					</p>
				</div>
				<?php
			}

		}

		/**
		 * Shows review notice
		 */
		public function sa_cp_show_review_notice() {
			global $post;
			$is_review_page = ( isset( $post->post_type ) && ( 'shop_order' === $post->post_type || 'product' === $post->post_type ) ) ? true : false; // phpcs:ignore WordPress.Security.NonceVerification

			if ( true === $this->may_be_show_review_notice() && true === $is_review_page ) {
				$this->cp_show_review_notice();
			}
		}

		/**
		 * Function to decide whether to show the display notice or not.
		 *
		 * @return bool $show_notice
		 */
		public function may_be_show_review_notice() {
			$show_notice = false;

			$cp_show_review_notice = get_option( 'cp_show_review_notice', 'yes' );

			if ( 'no' !== $cp_show_review_notice ) {
				$this->msg = __( 'Glad to see that you are using', 'woocommerce-chained-products' ) . '&nbsp;<strong>' . __( 'WooCommerce Chained Products.', 'woocommerce-chained-products' ) . '</strong><br>'; // Default msg.

				if ( 'yes' === $cp_show_review_notice ) {
					if ( true === $this->orders_has_chained_item() ) {
						$show_notice = true;
						$this->msg   = '<strong>' . __( 'Congratulations!', 'woocommerce-chained-products' ) . '</strong>&nbsp;' . esc_html__( 'You have successfully sold a product using', 'woocommerce_chained_product' ) . '&nbsp;<strong>' . __( 'WooCommerce Chained Products', 'woocommerce_chained_product' ) . '</strong>&nbsp;' . esc_html__( 'plugin.', 'woocommerce-chained-products' ) . '<br>';
					} elseif ( true === $this->products_has_chained_item() ) {
						$show_notice = true;
					}
				} elseif ( time() >= absint( $cp_show_review_notice ) ) {
					$show_notice = true;
				}
			}

			return $show_notice;
		}

		/**
		 * Function to check if orders contains chained items or not.
		 *
		 * @return bool $has_chained_items
		 */
		public function orders_has_chained_item() {
			global $wpdb;

			$order_count = wp_cache_get( 'wc_cp_order_count', 'woocommerce_chained_product' );

			if ( false === $order_count ) {
				$order_count = $wpdb->get_var( // phpcs:ignore
					$wpdb->prepare(
						"SELECT count( DISTINCT order_id )
					FROM {$wpdb->prefix}woocommerce_order_items AS o
					JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oi ON ( o.order_item_id = oi.order_item_id AND oi.meta_key = %s )
					JOIN {$wpdb->prefix}posts AS p ON (o.order_id = p.ID AND p.post_type = %s AND p.post_status = %s )",
						'_chained_product_of',
						'shop_order',
						'wc-completed'
					)
				);

				wp_cache_set( 'wc_cp_order_count', $order_count, 'woocommerce_chained_product' );
			}

			$has_chained_items = ( 0 < $order_count ) ? true : false;

			return $has_chained_items;
		}

		/**
		 * Function to check if products contains chained items or not.
		 *
		 * @return bool $has_chained_items
		 */
		public function products_has_chained_item() {
			global $wpdb;

			$product_count = wp_cache_get( 'wc_cp_product_count', 'woocommerce_chained_product' );

			if ( false === $product_count ) {
				$product_count = $wpdb->get_var( // phpcs:ignore
					$wpdb->prepare(
						"SELECT count( ID )
					FROM {$wpdb->prefix}posts AS p
					JOIN {$wpdb->prefix}postmeta AS pm
					ON ( p.ID = pm.post_id AND pm.meta_key = %s AND p.post_status = %s AND ( p.post_type = %s || p.post_type = %s ) AND DATEDIFF( now(), p.post_date ) > 30 )",
						'_chained_product_detail',
						'publish',
						'product',
						'product_varitation'
					)
				);

				wp_cache_set( 'wc_cp_product_count', $product_count, 'woocommerce_chained_product' );
			}

			$has_chained_items = ( 0 < $product_count ) ? true : false;

			return $has_chained_items;
		}


		/**
		 * 5 star review notice content.
		 */
		public function cp_show_review_notice() {
			?>
			<div id="wc_cp_review_notice" class="notice updated fade" style="background-color: mintcream;">
				<div class="wc_cp_review_notice_action" style="float: right;padding: 0.5em 0;text-align: right;font-size: 0.9em;">
					<a href="?cp_notice_action=remind" class="wc_cp_review_notice_remind"><?php echo esc_html__( 'Remind me after a month', 'woocommerce-chained-products' ); ?></a><br>
					<a href="?cp_notice_action=dismiss" class="wc_cp_review_notice_remove"><?php echo esc_html__( 'Never show again', 'woocommerce-chained-products' ); ?></a>
				</div>
				<p>
					<?php echo $this->msg . esc_html__( 'It would be great if you ', 'woocommerce-chained-products' ) . ' <a target="__blank" href="' . esc_url( 'https://woocommerce.com/products/chained-products/#comments' ) . '">' . esc_html__( 'give us a 5-star rating', 'woocommerce-chained-products' ) . '</a>&nbsp;' . esc_html__( 'of your experience. Thanking you in advance 😊', 'woocommerce-chained-products' ); // phpcs:ignore ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Function to update notice action on click of Dismiss/Remind me Later.
		 */
		public function sa_cp_update_notice_action() {
			$action = ( isset( $_GET['cp_notice_action'] ) ) ? wc_clean( wp_unslash( $_GET['cp_notice_action'] ) ) : ''; // phpcs:ignore

			if ( ! empty( $action ) ) {
				switch ( $action ) {
					case 'remind':
						$option_value = strtotime( '+1 month' );
						break;
					case 'dismiss':
						$option_value = 'no';
						break;
					default:
						$option_value = 'no';
				}

				update_option( 'cp_show_review_notice', $option_value, 'no' );

				$referer = wp_get_referer();
				wp_safe_redirect( $referer );
				exit();
			}
		}
	}
}

new WC_CP_Admin_Notices();
