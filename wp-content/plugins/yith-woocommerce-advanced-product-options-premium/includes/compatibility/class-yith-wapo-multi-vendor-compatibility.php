<?php
/**
 * YITH WooCommerce Multi Vendor compatibility.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddons
 */

defined( 'YITH_WPV_PREMIUM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_WPV_Compatibility' ) ) {
	/**
	 * Compatibility Class
	 *
	 * @class   YITH_WAPO_WPV_Compatibility
	 * @since   4.0.0  ∆€ÆŒ
	 */
	class YITH_WAPO_WPV_Compatibility {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WAPO_WPV_Compatibility
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WAPO_WPV_Compatibility
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WAPO_WPV_Compatibility constructor
		 */
		private function __construct() {

            add_filter( 'yith_wapo_register_panel_capabilities', array( $this, 'add_vendor_capability' ) );
            add_filter( 'yith_wapo_register_panel_args', array( $this, 'register_panel_args' ) );
            add_filter( 'yith_wapo_get_blocks_conditions', array( $this, 'get_blocks_with_vendor_id' ) );
            add_filter( 'yith_wapo_get_blocks_by_product_set_vendor', array( $this, 'get_blocks_by_product_vendor_id' ), 10, 2 );
            add_action( 'yith_wapo_after_block_rules', array( $this, 'add_vendor_option' ) );

		}

        /**
         * Add the vendor capability to the YITH register panel.
         *
         * @param string $capability The vendor capability.
         * @return mixed|string
         */
        public function add_vendor_capability( $capability ) {

            $is_enabled_for_vendor = YITH_WAPO::$instance->is_plugin_enabled_for_vendors();

            if ( class_exists( 'YITH_Vendors' ) && $is_enabled_for_vendor ) {
                $vendor                = yith_wcmv_get_vendor( 'current', 'user' );
                if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
                    $capability = class_exists( 'YITH_Vendors_Capabilities' ) ? YITH_Vendors_Capabilities::ROLE_ADMIN_CAP : YITH_Vendors()->admin->get_special_cap();
                }

            }

            return $capability;
        }

        /**
         * Function to modify the register panel args. Help tab and Your Store Tools tab are deleted for vendors.
         * @param $args The args used in the panel.
         * @return mixed
         */
        public function register_panel_args( $args ) {

            $is_enabled_for_vendor = YITH_WAPO::$instance->is_plugin_enabled_for_vendors();

            if ( class_exists( 'YITH_Vendors' ) && $is_enabled_for_vendor ) {
                $vendor                = yith_wcmv_get_vendor( 'current', 'user' );
                if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
                    unset( $args['help_tab'], $args['your_store_tools'] );
                    $args['parent_page'] = '';
                }

            }

            return $args;
        }

        /**
         * Set the vendor id when getting all the blocks in the dabatase.
         *
         * @param array $conditions The conditions added to the query.
         * @return mixed
         */
        public function get_blocks_with_vendor_id( $conditions ) {

            if ( ! current_user_can( 'administrator' ) && class_exists( 'YITH_Vendors' ) && ! is_product() ) {
                $vendor = yith_wcmv_get_vendor( 'current', 'user' );
                if ( $vendor->is_valid() ) {
                    $vendor_id    = $vendor->get_id();
                    $conditions['vendor_id'] = $vendor_id;
                }
            }
            return $conditions;
        }

        /**
         * Add the vendor option to the block options panel.
         *
         * @param YITH_WAPO_Block $block The block object.
         * @return void
         */
        public function add_vendor_option( $block ) {

            yith_wapo_get_view(
                'compatibility/multi-vendor.php',
                array(
                    'block' => $block
                ),
                defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''
            );

        }

        /**
         * Set the vendor id when getting specific blocks by product of this specific vendor.
         * @param array $vendor_ids The vendor ids added.
         * @param WC_Product $product The product object.
         * @return mixed
         */
        public function get_blocks_by_product_vendor_id( $vendor_ids, $product ) {

            $vendor = yith_wcmv_get_vendor( $product, 'product' );
            if ( $vendor && $vendor->is_valid() ) {
                $vendor_ids[] = $vendor->get_id();
            }
            return $vendor_ids;
        }

	}
}
