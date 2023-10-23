<?php
/**
 * Main class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Premium', false ) ) {
	/**
	 * YITH WooCommerce Customize My Account Page
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Premium extends YITH_WCMAP_Extended {

		/**
		 * Banners class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Banners
		 */
		public $banners = null;

		/**
		 * Avatar class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Avatar
		 */
		public $avatar = null;

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 */
		protected function __construct() {
			parent::__construct();

			$this->load_premium_classes();
			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			// Register additional items types.
			add_filter( 'yith_wcmap_get_items_types', array( $this, 'add_items_types' ), 10, 1 );
			add_filter( 'yith_wcmap_get_plugins_endpoints_array', array( $this, 'add_premium_compatibilities' ), 10, 1 );

			// Customize functions.
			add_filter( 'yith_wcmap_get_default_endpoint_options', array( $this, 'get_default_endpoint_options' ), 10, 1 );
			add_filter( 'yith_wcmap_endpoints_list', array( $this, 'filter_endpoint_list' ), 10, 2 );
			add_filter( 'yith_wcmap_get_endpoint_by', array( $this, 'filter_get_endpoint_by' ), 10, 4 );
		}

		/**
		 * Get admin class
		 *
		 * @since  3.12.0
		 * @return string
		 */
		protected function get_admin_class() {
			return 'YITH_WCMAP_Admin_Premium';
		}

		/**
		 * Get frontend class
		 *
		 * @since  3.12.0
		 * @return string
		 */
		protected function get_frontend_class() {
			return 'YITH_WCMAP_Frontend_Premium';
		}

		/**
		 * Load premium classes
		 *
		 * @since  3.12.0
		 * @return void
		 */
		protected function load_premium_classes() {
			$this->avatar  = new YITH_WCMAP_Avatar();
			$this->banners = new YITH_WCMAP_Banners();
		}

		/**
		 * Add items types
		 *
		 * @since  3.12.0
		 * @param array $types An array of items types.
		 * @return array
		 */
		public function add_items_types( $types ) {
			$types = array_merge(
				$types,
				array(
					'group',
					'link',
				)
			);

			return $types;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCMAP_DIR . 'plugin-fw/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCMAP_INIT, YITH_WCMAP_SECRET_KEY, YITH_WCMAP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_WCMAP_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}

			YIT_Upgrade()->register( YITH_WCMAP_SLUG, YITH_WCMAP_INIT );
		}

		/**
		 * Add premium fields to default endpoint values
		 *
		 * @since 3.12.0
		 * @param array $options An array of default options.
		 * @return array
		 */
		public function get_default_endpoint_options( $options ) {
			$options = array_merge(
				$options,
				array(
					'icon_type'        => 'default',
					'icon'             => '',
					'custom_icon'      => '',
					'class'            => '',
					'visibility'       => 'all',
					'content_position' => 'override',
				)
			);
			return $options;
		}

		/**
		 * Filter endpoint list function yith_wcmap_endpoints_list
		 *
		 * @since 3.12.0
		 * @param array $list The current endpoint list.
		 * @param array $fields Fields array.
		 * @return array
		 */
		public function filter_endpoint_list( $list, $fields ) {
			foreach ( $fields as $key => $field ) {
				if ( isset( $field['children'] ) ) {
					foreach ( $field['children'] as $child_key => $child ) {
						if ( isset( $child['slug'] ) ) {
							$return[ $child_key ] = $child['label'];
						}
					}
				}
			}

			return $list;
		}

		/**
		 * Filter function yith_wcmap_get_endpoint_by
		 *
		 * @since 3.12.0
		 * @param array  $find The current find result.
		 * @param string $value The value to search.
		 * @param string $key The value type. Can be key or slug.
		 * @param array  $items Endpoint array.
		 * @return array
		 */
		public function filter_get_endpoint_by( $find, $value, $key, $items ) {
			foreach ( $items as $id => $item ) {
				if ( isset( $item['children'] ) ) {
					foreach ( $item['children'] as $child_id => $child ) {
						if ( ( 'key' === $key && $child_id === $value ) || ( isset( $child[ $key ] ) && $child[ $key ] === $value ) ) {
							$find[ $child_id ] = $child;
						}
					}
				}
			}

			return $find;
		}

		/**
		 * Add premium compatibilities
		 *
		 * @since 3.12.0
		 * @param array $compatibilities An array of compatibilities.
		 * @return array
		 */
		public function add_premium_compatibilities( $compatibilities ) {
			return array_merge(
				$compatibilities,
				array(
					'one-click'        => defined( 'YITH_WOCC_PREMIUM' ) && YITH_WOCC_PREMIUM,
					'waiting-list'     => defined( 'YITH_WCWTL_PREMIUM' ) && YITH_WCWTL_PREMIUM,
					'request-quote'    => defined( 'YITH_YWRAQ_PREMIUM' ) && YITH_YWRAQ_PREMIUM,
					'membership'       => defined( 'YITH_WCMBS_PREMIUM' ) && YITH_WCMBS_PREMIUM,
					'subscriptions'    => defined( 'YITH_YWSBS_PREMIUM' ) && YITH_YWSBS_PREMIUM,
					'payouts'          => defined( 'YITH_PAYOUTS_PREMIUM' ) && YITH_PAYOUTS_PREMIUM,
					'stripe-connect'   => defined( 'YITH_WCSC_PREMIUM' ) && YITH_WCSC_PREMIUM,
					'refund-requests'  => defined( 'YITH_WCARS_PREMIUM' ) && YITH_WCARS_PREMIUM,
					'bookings'         => defined( 'YITH_WCBK_PREMIUM' ) && YITH_WCBK_PREMIUM,
					'funds'            => defined( 'YITH_FUNDS_PREMIUM' ) && YITH_FUNDS_PREMIUM,
					'points'           => defined( 'YITH_YWPAR_PREMIUM' ) && YITH_YWPAR_PREMIUM,
					'auctions'         => defined( 'YITH_WCACT_PREMIUM' ) && YITH_WCACT_PREMIUM,
					'advanced-reviews' => defined( 'YITH_YWAR_PREMIUM' ) && YITH_YWAR_PREMIUM,
					'wt-smart-coupon'  => class_exists( 'WT_MyAccount_SmartCoupon' ),
					'tinv-wishlist'    => class_exists( 'TInvWL' ) && shortcode_exists( 'ti_wishlistsview' ),
					'wc-membership'    => class_exists( 'WC_Memberships' ),
					'wc-subscriptions' => class_exists( 'WC_Subscriptions' ),
					'wc-api-manager'   => class_exists( 'WooCommerce_API_Manager' ),
				)
			);
		}
	}
}
