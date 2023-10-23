<?php
/**
 * WooCommerce Subscriptions Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_WC_Subscriptions_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_WC_Subscriptions_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_WC_Subscriptions_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = 'woo-subscription';
			$this->endpoint     = array(
				'slug'    => 'my-subscriptions',
				'label'   => __( 'My Subscription', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'    => 'pencil',
				'content' => '[ywcmap_woocommerce_subscription]',
			);

			// Register endpoint.
			$this->register_endpoint();

			// handle compatibility.
			add_action( 'template_redirect', array( $this, 'hooks' ), 5 );
			add_shortcode( 'ywcmap_woocommerce_subscription', array( $this, 'shortcode' ) );
		}

		/**
		 * Compatibility hooks and filters
		 *
		 * @since 3.0.0
		 */
		public function hooks() {
			// Remove content in my account.
			remove_action( 'woocommerce_before_my_account', array( 'WC_Subscriptions', 'get_my_subscriptions_template' ) );
		}

		/**
		 * WC Subscription compatibility
		 *
		 * @since 3.0.0
		 * @param array $args Shortcode args.
		 * @return string
		 */
		public function shortcode( $args ) {

			global $wp;

			if ( ! class_exists( 'WC_Subscriptions' ) ) {
				return '';
			}

			$subscription_id = ! empty( $wp->query_vars['view-subscription'] ) ? absint( $wp->query_vars['view-subscription'] ) : 0;
			// Handle version 4.0.
			ob_start();
			if ( version_compare( WC_Subscriptions::$version, '4.0', '<' ) ) {

				if ( $subscription_id ) {
					$subscription = wcs_get_subscription( $subscription_id );
					wc_get_template( 'myaccount/view-subscription.php', array( 'subscription' => $subscription ), '', plugin_dir_path( WC_Subscriptions::$plugin_file ) . 'templates/' );

				} else {
					WC_Subscriptions::get_my_subscriptions_template();
				}
			} else {
				if ( $subscription_id ) {
					WCS_Template_Loader::get_view_subscription_template( $subscription_id );
				} else {
					$current_page = ! empty( $wp->query_vars['my-subscriptions'] ) ? $wp->query_vars['my-subscriptions'] : 1;
					WCS_Template_Loader::get_my_subscriptions( $current_page );
				}
			}

			return ob_get_clean();

		}
	}
}
