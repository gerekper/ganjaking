<?php
/**
 * Compatibility with YITH WooCommerce Multivendor
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * YWDPD_Multivendor class to add compatibility with YITH WooCommerce Multivendor
 *
 * @class   YWDPD_Multivendor
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YWDPD_Multivendor' ) ) {

	/**
	 * Class YWDPD_Multivendor
	 */
	class YWDPD_Multivendor {

		/**
		 * Single instance of the class
		 *
		 * @var YWDPD_Multivendor
		 */
		protected static $instance;



		/**
		 * Returns single instance of the class
		 *
		 * @return YWDPD_Multivendor
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			add_filter( 'yit_ywdpd_pricing_rules_options', array( $this, 'add_pricing_rule_option' ) );
			add_filter( 'yit_ywdpd_cart_rules_options', array( $this, 'add_cart_rule_option' ) );
			add_filter( 'yith_ywdpd_admin_localize', array( $this, 'add_localize_params' ) );
			add_filter( 'ywdpd_pricing_discount_metabox_options', array( $this, 'add_vendor_pricing_options' ) );

			// panel type category search
			add_action( 'wp_ajax_ywdpd_vendor_search', array( $this, 'json_search_vendors' ) );
			add_action( 'wp_ajax_nopriv_ywdpd_vendor_search', array( $this, 'json_search_vendors' ) );

		}


		/**
		 * Add pricing rules options in settings panels
		 *
		 * @param $rules
		 *
		 * @return array
		 */
		public function add_pricing_rule_option( $rules ) {
			$new_rule = array();
			foreach ( $rules as $key => $rule ) {
				$new_rule[ $key ] = $rule;

				if ( $key == 'apply_to' || $key == 'apply_adjustment' ) {
					$new_rule[ $key ]['vendor_list']          = __( 'Include a list of vendors', 'ywdpd' );
					$new_rule[ $key ]['vendor_list_excluded'] = __( 'Exclude a list of vendors', 'ywdpd' );
				}
			}

			return $new_rule;
		}


		/**
		 * Add pricing rules options in settings panels
		 *
		 * @param $rules
		 *
		 * @return array
		 */
		public function add_cart_rule_option( $rules ) {

			$new_rule['rules_type'] = array();
			if ( isset( $rules['rules_type'] ) ) {
				foreach ( $rules['rules_type'] as $key => $rule ) {
					$new_rule['rules_type'][ $key ] = $rule;

					if ( $key == 'products' ) {
						$new_rule['rules_type'][ $key ]['options']['vendor_list']          = __( 'Include a list of vendors', 'ywdpd' );
						$new_rule['rules_type'][ $key ]['options']['vendor_list_excluded'] = __( 'Exclude a list of vendors', 'ywdpd' );
					}
				}
			}
			$new_rule['discount_type'] = $rules['discount_type'];
			return $new_rule;
		}


		/**
		 * Add localize params to javascript
		 *
		 * @param $params
		 *
		 * @return mixed
		 */
		public function add_localize_params( $params ) {
			$params['search_vendor_nonce'] = wp_create_nonce( 'search-vendor' );

			return $params;
		}


		public function json_search_vendors() {

			check_ajax_referer( 'search-products', 'security' );

			ob_start();

			$term = (string) wc_clean( stripslashes( $_GET['term'] ) );

			if ( empty( $term ) ) {
				die();
			}
			global $wpdb;
			$terms = $wpdb->get_results( 'SELECT name, slug, wpt.term_id FROM ' . $wpdb->prefix . 'terms wpt, ' . $wpdb->prefix . 'term_taxonomy wptt WHERE wpt.term_id = wptt.term_id AND wptt.taxonomy = "' . YITH_Vendors()->get_taxonomy_name() . '" and wpt.name LIKE "%' . $term . '%" ORDER BY name ASC;' );

			$found_vendors = array();

			if ( $terms ) {
				foreach ( $terms as $cat ) {
					$found_vendors[ $cat->term_id ] = ( $cat->name ) ? $cat->name : 'ID: ' . $cat->slug;
				}
			}

			wp_send_json( $found_vendors );
		}

		/**
		 * @param $pricing_options
		 * @return mixed
		 */
		public function add_vendor_pricing_options( $pricing_options ) {

			$start        = $pricing_options['tabs']['settings']['fields'];
			$position     = array_search( 'apply_to_tags_list_excluded', array_keys( $start ) );
			$begin        = array_slice( $start, 0, $position + 1 );
			$end          = array_slice( $start, $position );
			$vendor_items = array(
				'apply_to_vendors_list'          => array(
					'label'       => __( 'Search for a vendor', 'ywdpd' ),
					'type'        => 'vendors',
					'desc'        => '',
					'placeholder' => __( 'Search for a vendor', 'ywdpd' ),
					'deps'        => array(
						'ids'    => '_apply_to',
						'values' => 'vendor_list',
					),
				),
				'apply_to_vendors_list_excluded' => array(
					'label'       => __( 'Search for a vendor', 'ywdpd' ),
					'type'        => 'vendors',
					'desc'        => '',
					'placeholder' => __( 'Search for a vendor', 'ywdpd' ),
					'deps'        => array(
						'ids'    => '_apply_to',
						'values' => 'vendor_list_excluded',
					),
				),
			);

			$start        = $begin + $vendor_items + $end;
			$position     = array_search( 'apply_adjustment_tags_list', array_keys( $start ) );
			$begin        = array_slice( $start, 0, $position + 1 );
			$end          = array_slice( $start, $position );
			$vendor_items = array(
				'apply_adjustment_vendor_list'          => array(
					'label'       => __( 'Search for a vendor', 'ywdpd' ),
					'type'        => 'vendors',
					'desc'        => '',
					'placeholder' => __( 'Search for a vendor', 'ywdpd' ),
					'deps'        => array(
						'ids'    => '_apply_adjustment',
						'values' => 'vendor_list',
					),
				),
				'apply_adjustment_vendor_list_excluded' => array(
					'label'       => __( 'Search for a vendor', 'ywdpd' ),
					'type'        => 'vendors',
					'desc'        => '',
					'placeholder' => __( 'Search for a vendor', 'ywdpd' ),
					'deps'        => array(
						'ids'    => '_apply_adjustment',
						'values' => 'vendor-list-excluded',
					),
				),
			);

			$pricing_options['tabs']['settings']['fields'] = $begin + $vendor_items + $end;

			return $pricing_options;

		}

	}

}

/**
 * Unique access to instance of YWDPD_Multivendor class
 *
 * @return YWDPD_Multivendor
 */
function YWDPD_Multivendor() {
	return YWDPD_Multivendor::get_instance();
}

YWDPD_Multivendor();
