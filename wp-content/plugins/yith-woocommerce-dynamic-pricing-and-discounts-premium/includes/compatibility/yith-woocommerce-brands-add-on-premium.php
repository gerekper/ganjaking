<?php
/**
 * Compatibility with YITH WooCommerce Brands Add-on Premium
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
 * YWDPD_Brands class to add compatibility with YITH WooCommerce Brands Add-on Premium
 *
 * @class   YWDPD_Brands
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.1.7
 * @author  YITH
 */
if ( ! class_exists( 'YWDPD_Brands' ) ) {

	/**
	 * Class YWDPD_Brands
	 */
	class YWDPD_Brands {

		/**
		 * Single instance of the class
		 *
		 * @var YWDPD_Brands
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YWDPD_Brands
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
		 * @since  1.1.7
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			add_filter( 'yit_ywdpd_pricing_rules_options', array( $this, 'add_pricing_rule_option' ) );
			add_filter( 'yit_ywdpd_cart_rules_options', array( $this, 'add_cart_rule_option' ) );
			add_filter( 'yith_ywdpd_admin_localize', array( $this, 'add_localize_params' ) );

			// panel type category search
			add_action( 'wp_ajax_ywdpd_brand_search', array( $this, 'json_search_brands' ) );
			add_action( 'wp_ajax_nopriv_ywdpd_brand_search', array( $this, 'json_search_brands' ) );

			add_filter( 'ywdpd_pricing_discount_metabox_options', array( $this, 'add_brands_pricing_options' ) );
			// helper class filters
			add_filter( 'ywdpd_is_in_exclusion_rule', array( $this, 'is_in_exclusion_rule' ), 10, 5 );
			add_filter( 'ywdpd_validate_apply_to', array( $this, 'is_validate_apply_to' ), 10, 5 );
			add_filter( 'ywdpd_valid_product_to_adjust', array( $this, 'valid_product_to_adjust' ), 10, 5 );
			add_filter( 'ywdpd_valid_product_to_apply_bulk', array( $this, 'valid_product_to_apply_bulk' ), 10, 5 );
			add_filter( 'ywdpd_get_cumulative_quantity', array( $this, 'get_cumulative_quantity' ), 10, 3 );
			add_filter( 'ywdpd_validate_product_in_cart', array( $this, 'validate_product_in_cart' ), 10, 3 );

		}


		/**
		 * @param $excluded
		 * @param $apply_to
		 * @param $cart_item_product_id
		 * @param $rule
		 * @param $cart_item
		 *
		 * @return bool
		 */
		public function is_in_exclusion_rule( $excluded, $apply_to, $cart_item_product_id, $rule, $cart_item ) {

			if ( $apply_to == 'brand_list' ) {
				$excluded = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_brands_list'], $cart_item_product_id, YITH_WCBR::$brands_taxonomy );
			} elseif ( $apply_to == 'brand_list_excluded' ) {
				$excluded = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_brands_list_excluded'], $cart_item_product_id, YITH_WCBR::$brands_taxonomy, false );
			}

			return $excluded;
		}

		/**
		 * @param $is_valid
		 * @param $apply_to
		 * @param $cart_item_product_id
		 * @param $rule
		 * @param $cart_item
		 *
		 * @return bool
		 */
		public function is_validate_apply_to( $is_valid, $apply_to, $cart_item_product_id, $rule, $cart_item ) {

			if ( $apply_to == 'brand_list' ) {
				$is_valid = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_brands_list'], $cart_item_product_id, YITH_WCBR::$brands_taxonomy );
			} elseif ( $apply_to == 'brand_list_excluded' ) {
				$is_valid = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_brands_list_excluded'], $cart_item_product_id, YITH_WCBR::$brands_taxonomy, false );
			}

			return $is_valid;
		}

		/**
		 * @param $is_valid
		 * @param $apply_adjustment
		 * @param $cart_item_product_id
		 * @param $rule
		 * @param $cart_item
		 *
		 * @return bool
		 */
		public function valid_product_to_adjust( $is_valid, $apply_adjustment, $cart_item_product_id, $rule, $cart_item ) {

			if ( $apply_adjustment == 'brand_list' ) {
				$is_valid = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_brands_list'], $cart_item_product_id, YITH_WCBR::$brands_taxonomy );
			} elseif ( $apply_adjustment == 'brand_list_excluded' ) {
				$is_valid = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_brands_list_excluded'], $cart_item_product_id, YITH_WCBR::$brands_taxonomy, false );
			}

			return $is_valid;
		}

		/**
		 * @param $is_valid
		 * @param $apply_to
		 * @param $product_id
		 * @param $rule
		 * @param $product
		 *
		 * @return bool
		 */
		public function valid_product_to_apply_bulk( $is_valid, $apply_to, $product_id, $rule, $product ) {

			if ( $apply_to == 'brand_list' ) {
				$is_valid = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_brands_list'], $product_id, YITH_WCBR::$brands_taxonomy );
			} elseif ( $apply_to == 'brand_list_excluded' ) {
				$is_valid = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_brands_list_excluded'], $product_id, YITH_WCBR::$brands_taxonomy, false );
			}

			return $is_valid;
		}

		/**
		 * @param $quantity
		 * @param $apply_to
		 * @param $rule
		 *
		 * @return mixed
		 */
		public function get_cumulative_quantity( $quantity, $apply_to, $rule ) {

			if ( $apply_to == 'brand_list' ) {
				$quantity = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy_quantity( $rule['apply_to_brands_list'], YITH_WCBR::$brands_taxonomy );
			} elseif ( $apply_to == 'brand_list_excluded' ) {
				$quantity = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy_quantity( $rule['apply_to_brand_list_excluded'], YITH_WCBR::$brands_taxonomy );
			}

			return $quantity;
		}

		/**
		 * @param $is_valid
		 * @param $type
		 * @param $brand_list
		 * @return bool
		 */
		public function validate_product_in_cart( $is_valid, $type, $brand_list ) {

			if ( $type == 'brand_list' ) {
				foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
					$brands_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_WCBR::$brands_taxonomy, array( 'fields' => 'ids' ) );
					$intersect      = array_intersect( $brands_of_item, $brand_list );
					if ( ! empty( $intersect ) ) {
						$is_valid = true;
					}
				}
			} elseif ( $type == 'brand_list_and' ) {
				foreach ( $brand_list as $brand_id ) {
					if ( YITH_WC_Dynamic_Pricing_Helper()->find_taxonomy_in_cart( $brand_id, YITH_WCBR::$brands_taxonomy ) != '' ) {
						$is_valid = true;
					} else {
						$is_valid = false;
						break;
					}
				}
			} elseif ( $type == 'brand_list_excluded' ) {
				$is_valid = true;
				foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
					$brands_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_WCBR::$brands_taxonomy, array( 'fields' => 'ids' ) );
					$intersect      = array_intersect( $brands_of_item, $brand_list );
					if ( ! empty( $intersect ) ) {
						$is_valid = false;
					}
				}
			}

			return $is_valid;
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
					// @since 1.1.7
					$new_rule[ $key ]['brand_list']          = __( 'Include a list of brands', 'ywdpd' );
					$new_rule[ $key ]['brand_list_excluded'] = __( 'Exclude a list of brands', 'ywdpd' );
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
						$new_rule['rules_type'][ $key ]['options']['brand_list']          = __( 'At least a selected brand', 'ywdpd' );
						$new_rule['rules_type'][ $key ]['options']['brand_list_and']      = __( 'All selected brands in cart', 'ywdpd' );
						$new_rule['rules_type'][ $key ]['options']['brand_list_excluded'] = __( 'Brands not selected', 'ywdpd' );
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
			$params['search_brand_nonce'] = wp_create_nonce( 'search-brand' );

			return $params;
		}

		/**
		 * Return the list of brands that match with the query digit
		 */
		public function json_search_brands() {

			check_ajax_referer( 'search-products', 'security' );

			ob_start();

			$term = (string) wc_clean( stripslashes( $_GET['term'] ) );

			if ( empty( $term ) ) {
				die();
			}
			global $wpdb;

			$terms = $wpdb->get_results( 'SELECT name, slug, wpt.term_id FROM ' . $wpdb->prefix . 'terms wpt, ' . $wpdb->prefix . 'term_taxonomy wptt WHERE wpt.term_id = wptt.term_id AND wptt.taxonomy = "' . YITH_WCBR::$brands_taxonomy . '" and wpt.name LIKE "%' . $term . '%" ORDER BY name ASC;' );

			$found_brands = array();

			if ( $terms ) {
				foreach ( $terms as $cat ) {
					$found_brands[ $cat->term_id ] = ( $cat->name ) ? $cat->name : 'ID: ' . $cat->slug;
				}
			}

			wp_send_json( $found_brands );
		}

		/**
		 * @param $pricing_options
		 * @return mixed
		 */
		public function add_brands_pricing_options( $pricing_options ) {

			$start        = $pricing_options['tabs']['settings']['fields'];
			$position     = array_search( 'apply_to_tags_list_excluded', array_keys( $start ) );
			$begin        = array_slice( $start, 0, $position + 1 );
			$end          = array_slice( $start, $position );
			$brands_items = array(
				'apply_to_brands_list'          => array(
					'label'       => __( 'Search for a brand', 'ywdpd' ),
					'type'        => 'brands',
					'desc'        => '',
					'placeholder' => __( 'Search for a brand', 'ywdpd' ),
					'deps'        => array(
						'ids'    => '_apply_to',
						'values' => 'brand_list',
					),
				),
				'apply_to_brands_list_excluded' => array(
					'label'       => __( 'Search for a brand', 'ywdpd' ),
					'type'        => 'brands',
					'desc'        => '',
					'placeholder' => __( 'Search for a branch', 'ywdpd' ),
					'deps'        => array(
						'ids'    => '_apply_to',
						'values' => 'brand_list_excluded',
					),
				),
			);

			$start        = $begin + $brands_items + $end;
			$position     = array_search( 'apply_adjustment_tags_list', array_keys( $start ) );
			$begin        = array_slice( $start, 0, $position + 1 );
			$end          = array_slice( $start, $position );
			$brands_items = array(
				'apply_adjustment_brands_list'          => array(
					'label'       => __( 'Search for a brand', 'ywdpd' ),
					'type'        => 'brands',
					'desc'        => '',
					'placeholder' => __( 'Search for a brand', 'ywdpd' ),
					'deps'        => array(
						'ids'    => '_apply_adjustment',
						'values' => 'brand_list',
					),
				),
				'apply_adjustment_brands_list_excluded' => array(
					'label'       => __( 'Search for a brand', 'ywdpd' ),
					'type'        => 'brands',
					'desc'        => '',
					'placeholder' => __( 'Search for a branch', 'ywdpd' ),
					'deps'        => array(
						'ids'    => '_apply_adjustment',
						'values' => 'brand_list_excluded',
					),
				),
			);

			$pricing_options['tabs']['settings']['fields'] = $begin + $brands_items + $end;

			return $pricing_options;

		}

	}

}

/**
 * Unique access to instance of YWDPD_Brands class
 *
 * @return YWDPD_Brands
 */
function YWDPD_Brands() {
	return YWDPD_Brands::get_instance();
}

YWDPD_Brands();
