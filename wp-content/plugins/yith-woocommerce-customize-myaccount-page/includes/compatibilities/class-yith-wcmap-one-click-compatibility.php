<?php
/**
 * YITH WooCommerce One-Click Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_One_Click_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_One_Click_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_One_Click_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = 'one-click';
			$this->endpoint     = array(
				'slug'    => 'one-click',
				'label'   => __( 'One click checkout', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'    => 'hand-o-up',
				'content' => '[yith_wocc_myaccount]',
			);

			// Register endpoint.
			$this->register_endpoint();

			// Handle compatibility.
			add_action( 'template_redirect', array( $this, 'hooks' ), 5 );
		}

		/**
		 * Compatibility hooks and filters
		 *
		 * @since 3.0.0
		 */
		public function hooks() {
			if ( class_exists( 'YITH_WOCC_User_Account' ) ) {
				// Remove content in my account.
				remove_action( 'woocommerce_after_my_account', array( YITH_WOCC_User_Account(), 'my_account_options' ) );
			}

			add_filter( 'yith_wcmap_endpoint_menu_class', array( $this, 'set_active' ), 10, 3 );
		}

		/**
		 * Assign active class to endpoint one-click
		 *
		 * @since  1.1.0
		 * @param array  $classes The endpoint classes.
		 * @param string $endpoint The current endpoint.
		 * @param array  $options The endpoint options.
		 * @return array
		 */
		public function set_active( $classes, $endpoint, $options ) {

			global $wp;

			if ( 'one-click' === $endpoint && ! in_array( 'active', $classes, true ) && isset( $wp->query_vars['custom-address'] ) ) {
				$classes[] = 'active';
			}

			return $classes;
		}
	}
}
