<?php
! defined( 'YITH_WCMBS' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Endpoints' ) ) {
	/**
	 * Class YITH_WCMBS_Endpoints
	 * handle Membership endpoints
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.4.0
	 */
	class YITH_WCMBS_Endpoints {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCMBS_Endpoints
		 */
		private static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCMBS_Endpoints
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			$show = 'yes' === yith_wcmbs_settings()->get_option( 'yith-wcmbs-show-memberships-menu-in-my-account' );

			add_filter( 'woocommerce_get_query_vars', array( $this, 'add_query_vars' ) );
			add_filter( 'woocommerce_settings_pages', array( $this, 'add_endpoint_settings' ) );

			if ( $show ) {
				add_filter( 'woocommerce_account_menu_items', array( $this, 'add_memberships_menu_item' ), 20 );

				foreach ( $this->get_endpoints() as $key => $value ) {
					if ( ! empty( $value ) ) {
						add_action( 'woocommerce_account_' . $value . '_endpoint', array( $this, 'render_endpoint' ) );
					}
				}
			}
		}

		/**
		 * Retrieve the endpoint
		 *
		 * @param string $key
		 *
		 * @return string
		 */
		public function get_endpoint( $key ) {
			$endpoints = $this->get_endpoints();

			return array_key_exists( $key, $endpoints ) ? $endpoints[ $key ] : $key;
		}

		/**
		 * Retrieve the endpoints
		 *
		 * @return array
		 */
		public function get_endpoints() {
			static $endpoints;
			if ( is_null( $endpoints ) ) {
				$endpoints = apply_filters( 'yith_wcmbs_endpoints', array(
					'memberships' => get_option( 'woocommerce_myaccount_memberships_endpoint', 'memberships' ),
				) );
			}

			return $endpoints;
		}

		/**
		 * Add new query var.
		 *
		 * @param array $vars
		 *
		 * @return array
		 */
		public function add_query_vars( $vars ) {
			$vars = array_merge( $vars, $this->get_endpoints() );

			return $vars;
		}

		public function render_endpoint() {
			$endpoint  = $this->get_current_endpoint();
			$endpoints = $this->get_endpoints();

			$memberships_endpoint = $endpoints['memberships'];

			switch ( $endpoint ) {
				case $memberships_endpoint:
					$title     = esc_html__( 'Membership Plans', 'yith-woocommerce-membership' );
					$shortcode = '[membership_history title="' . $title . '" type="membership"]';

					echo wp_kses_post( apply_filters( 'yith_wcmbs_endpoint_content', do_shortcode( $shortcode ), $title ) );
					break;
			}
		}

		/**
		 * Add the menu item to WooCommerce My Account Menu
		 *
		 * @param array $items WC menu items.
		 *
		 * @return mixed
		 */
		public function add_memberships_menu_item( $items ) {
			$a = array_slice( $items, 0, 1, true );
			$b = array_slice( $items, 1 );

			$endpoints            = $this->get_endpoints();
			$memberships_endpoint = $endpoints['memberships'];

			$endpoints_to_add = array(
				$memberships_endpoint => _x( 'Memberships', 'My Account Endpoint title', 'yith-woocommerce-membership' ),
			);

			$items = array_merge( $a, $endpoints_to_add, $b );

			return $items;
		}

		/**
		 * Add Endpoint Settings in WooCommerce endpoint settings
		 *
		 * @param $settings
		 *
		 * @return array
		 */
		public function add_endpoint_settings( $settings ) {

			$endpoint_settings = array(
				array(
					'title' => __( 'Membership endpoints', 'yith-woocommerce-membership' ),
					'type'  => 'title',
					'id'    => 'yith_wcmbs_endpoint_options',
				),

				array(
					'title'    => __( 'Memberships', 'yith-woocommerce-membership' ),
					'desc'     => __( 'Endpoint for the "My Account &rarr; Memberships" page', 'yith-woocommerce-membership' ),
					'id'       => 'woocommerce_myaccount_memberships_endpoint',
					'type'     => 'text',
					'default'  => 'memberships',
					'desc_tip' => true,
				),
				array( 'type' => 'sectionend', 'id' => 'yith_wcmbs_endpoint_options' ),
			);

			return array_merge( $settings, $endpoint_settings );
		}

		/**
		 * Get the current endpoint
		 *
		 * @return bool|int|string
		 */
		public function get_current_endpoint() {
			global $wp;

			if ( is_admin() || ! is_main_query() || ! in_the_loop() || ! is_account_page() ) {
				return false;
			}

			$current_endpoint = false;
			foreach ( $this->get_endpoints() as $endpoint_id => $endpoint ) {
				if ( isset( $wp->query_vars[ $endpoint ] ) ) {
					$current_endpoint = $endpoint_id;
					break;
				}
			}

			return $current_endpoint;
		}

		/**
		 * Plugin install action.
		 * Flush rewrite rules to make our custom endpoint available.
		 */
		public static function install() {
			flush_rewrite_rules();
		}

	}
}