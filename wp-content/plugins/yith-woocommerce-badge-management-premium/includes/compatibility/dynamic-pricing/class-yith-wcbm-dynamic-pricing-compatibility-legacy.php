<?php
/**
 * Dynamic Pricing Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Compatibility
 * @since   1.2.8
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Dynamic_Pricing_Compatibility_Legacy' ) ) {
	/**
	 * Dynamic Pricing Compatibility Class
	 */
	class YITH_WCBM_Dynamic_Pricing_Compatibility_Legacy {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Dynamic_Pricing_Compatibility
		 * @since 1.0.0
		 */
		protected static $instance;


		/**
		 * Store Dynamic pricing and discount rules.
		 *
		 * @var array
		 */
		private $dynamic_rules;

		/**
		 * Store valid Dynamic pricing and discount rules.
		 *
		 * @var array
		 */
		private $valid_dynamic_rules;


		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCBM_Dynamic_Pricing_Compatibility_Legacy|YITH_WCBM_Dynamic_Pricing_Compatibility
		 * @since 1.0.0
		 */
		public static function get_instance() {
			$self = defined( 'YITH_YWDPD_VERSION' ) && version_compare( YITH_YWDPD_VERSION, '3.0.0', '>=' ) && class_exists( 'YITH_WCBM_Dynamic_Pricing_Compatibility' ) ? 'YITH_WCBM_Dynamic_Pricing_Compatibility' : 'YITH_WCBM_Dynamic_Pricing_Compatibility_Legacy';

			return ! is_null( $self::$instance ) ? $self::$instance : $self::$instance = new $self();
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			$this->include_compatibility_classes();

			add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );
			add_filter( 'yith_wcbm_get_badge_rule_dynamic_pricing_lookup_rows', array(
				$this,
				'get_dynamic_pricing_badge_rule_associations'
			), 10, 2 );
			add_filter( 'yith_wcbm_badge_rules_types_before_adding_default_fields', array(
				$this,
				'add_dynamic_pricing_rule_type'
			) );

			add_action( 'wp_ajax_yith_wcbm_search_dynamic_pricing_rules', array(
				$this,
				'ajax_search_dynamic_pricing_rules'
			) );

			// OLD Compatibility.

			add_filter( 'yith_wcbm_advanced_badge_product_price', array( $this, 'get_discounted_price' ), 10, 2 );
		}

		/**
		 * Include compatibility classes
		 */
		public function include_compatibility_classes() {
			require_once 'class-yith-wcbm-badge-rule-dynamic-pricing.php';
			require_once 'class-yith-wcbm-badge-rule-dynamic-pricing-data-store-cpt.php';
		}

		/**
		 * Add Badge rule Dynamic Data Store to WC ones.
		 *
		 * @param array $data_stores WC Data Stores.
		 *
		 * @return array
		 */
		public function register_data_stores( $data_stores ) {
			$data_stores['badge_rule_dynamic_pricing'] = 'YITH_WCBM_Badge_Rule_Dynamic_Pricing_Data_Store_CPT';

			return $data_stores;
		}

		/**
		 * Retrieve the rule associations
		 *
		 * @param array $rule_rows Badge Rule Rows.
		 * @param YITH_WCBM_Associative_Badge_Rule $rule Badge Rule.
		 *
		 * @return array
		 */
		public function get_dynamic_pricing_badge_rule_associations( $rule_rows, $rule ) {
			$associations = $rule->get_associations();
			foreach ( $associations as $association ) {
				if ( ! empty( $association['$association'] ) && ! empty( $association['badge'] ) ) {
					$rule_rows[] = array(
						'value'    => $association['$association'],
						'badge_id' => $association['badge'],
						'enabled'  => absint( $rule->is_enabled() ),
					);
				}
			}

			return $rule_rows;
		}

		/**
		 * Retrieve the discounted price
		 *
		 * @param float $price The price.
		 * @param WC_Product $product The product.
		 *
		 * @return float
		 * @since 1.4.9
		 */
		public function get_discounted_price( $price, $product ) {
			return (float) YITH_WC_Dynamic_Pricing()->get_discount_price( $product->get_price( 'edit' ), $product );
		}

		/**
		 * Get dynamic Pricing Rules
		 *
		 * @return array
		 */
		public function get_rules() {
			if ( ! isset( $this->dynamic_rules ) ) {
				if ( is_callable( array( YITH_WC_Dynamic_Pricing(), 'recover_pricing_rules' ) ) ) {
					$this->dynamic_rules = YITH_WC_Dynamic_Pricing()->recover_pricing_rules();
				} else {
					$this->dynamic_rules = YITH_WC_Dynamic_Pricing()->get_option( 'pricing-rules' );
				}
			}

			return $this->dynamic_rules;
		}

		/**
		 * Get dynamic Pricing Rules
		 *
		 * @return array
		 */
		public function get_valid_rules() {
			if ( ! isset( $this->valid_dynamic_rules ) ) {
				if ( is_callable( array( YITH_WC_Dynamic_Pricing(), 'get_pricing_rules' ) ) ) {
					$this->valid_dynamic_rules = YITH_WC_Dynamic_Pricing()->get_pricing_rules();
				} else {
					$this->valid_dynamic_rules = YITH_WC_Dynamic_Pricing()->get_option( 'pricing-rules' );
				}
			}

			return $this->valid_dynamic_rules;
		}

		/**
		 * Add Dynamic Pricing Rule Type
		 *
		 * @param array $rules_types Rules Types.
		 *
		 * @return array
		 */
		public function add_dynamic_pricing_rule_type( $rules_types ) {
			$rules_types['dynamic-pricing'] = array(
				'title'    => _x( 'Dynamic pricing & discount rules', '[ADMIN] Badge Rule type title', 'yith-woocommerce-badges-management' ),
				'desc'     => _x( 'A badge to show by a rule created with the plugin YITH Dynamic Pricing & Discount.', '[ADMIN] Badge Rule type description', 'yith-woocommerce-badges-management' ),
				'icon'     => 'dynamic-pricing-badge-rule',
				'fields'   => array(
					'badge'        => array( 'type' => '' ),
					'associations' => array(
						'field_position'     => 1.5,
						'label'              => __( 'Dynamic Pricing badges', 'yith-woocommerce-badges-management' ),
						'desc'               => __( 'Choose which badges to show in products of specific dynamic pricing rules.', 'yith-woocommerce-badges-management' ),
						'type'               => 'custom',
						'action'             => 'yith_wcbm_print_badge_rule_associations_field',
						'extra_row_class'    => 'yith-wcbm-associations-badge-rule-field',
						'name'               => 'yith_wcbm_badge_rule[_associations]',
						'associations_field' => array(
							'id'   => 'yith-wcbm-rule-dynamic-rule-badge',
							'type' => 'ajax-posts',
							'data' => array(
								'placeholder'          => __( 'Search dynamic pricing rule...', 'yith-woocommerce-badges-management' ),
								'post_type'            => is_callable( 'YITH_WC_Dynamic_Pricing_Admin' ) ? YITH_WC_Dynamic_Pricing_Admin()->post_type_name : 'ywdpd_discount',
								'minimum_input_length' => '1',
								'action'               => 'yith_wcbm_search_dynamic_pricing_rules',
							),
						),
					),
				),
				'callback' => array( $this, 'get_product_badges_from_dynamic_pricing_rules' ),
			);

			return $rules_types;
		}

		/**
		 * Get product badges from dynamic pricing rules
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return array
		 */
		public function get_product_badges_from_dynamic_pricing_rules( $product_id ) {
			$badges  = array();
			$args    = array(
				'meta_key'   => '_type', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value' => 'dynamic-pricing', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			);
			$results = yith_wcbm_get_badge_rules( $args );

			$rule_ids = array();
			foreach ( $results as $rule_id ) {
				if ( yith_wcbm_is_badge_rule_valid( $rule_id, $product_id ) ) {
					$rule_ids[] = $rule_id;
				}
			}

			foreach ( $rule_ids as $rule_id ) {
				/**
				 * Dynamic Badge Rule
				 *
				 * @var YITH_WCBM_Badge_Rule_Dynamic_Pricing $rule The Dynamic Rule.
				 */
				$rule   = yith_wcbm_get_badge_rule( $rule_id );
				$badges = array_merge( $badges, $rule->get_badges_for_product( $product_id ) );
			}

			return array_unique( $badges );
		}

		/**
		 * Check if a product is in one rule
		 *
		 * @param int $product_id Product ID.
		 * @param array $dynamic_rule Dynamic Rule.
		 *
		 * @return bool
		 */
		public function product_is_in_rule( $product_id, $dynamic_rule ) {
			$is_in_rule = false;
			$product    = wc_get_product( $product_id );
			if ( $product ) {
				if ( ! is_array( $dynamic_rule ) ) {
					$dynamic_rule = $this->get_dynamic_rule_by_id( absint( $dynamic_rule ) );
				}
				if ( $dynamic_rule ) {
					$is_in_rule          = YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply( $dynamic_rule, $product );
					$apply_adjustment_to = isset( $dynamic_rule['apply_adjustment'] ) ? $dynamic_rule['apply_adjustment'] : false;

					if ( $apply_adjustment_to ) {
						switch ( $apply_adjustment_to ) {

							case 'categories_list_excluded':
							case 'tags_list_excluded':
							case 'products_list_excluded':
							case 'brand_list_excluded':
								$is_in_rule = YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_adjustment( $dynamic_rule, $product );
								break;
						}
					}
				}
			}

			return $is_in_rule;
		}

		/**
		 * Get Dynamic Rule by post ID
		 *
		 * @param int $rule_id The dynamic rule post ID.
		 *
		 * @return array|false
		 */
		public function get_dynamic_rule_by_id( $rule_id ) {
			$key   = '';
			$rules = $this->get_rules();
			if ( $rules ) {
				$key = array_search( $rule_id, array_combine( array_keys( $rules ), array_column( $rules, 'id' ) ), true );
			}

			return $key && isset( $rules[ $key ] ) ? $rules[ $key ] : array();
		}

		/**
		 * AJAX search Dynamic Pricing rules
		 */
		public function ajax_search_dynamic_pricing_rules() {
			if ( ! empty( $_REQUEST['term'] ) && ! empty( $_REQUEST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ), 'search-posts' ) ) {
				if ( version_compare( YITH_YWDPD_VERSION, '4.0.0', '<' ) ) {
					$args = array(
						'meta_key'   => '_discount_type',
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						'meta_value' => 'pricing',
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
					);

				} else {
					$args = array(
						'post_type' => 'ywdpd_discount',
						'term'      => $_REQUEST['term']
					);
				}
				if ( method_exists( 'YIT_Ajax', 'instance' ) && method_exists( YIT_Ajax::instance(), 'json_search_posts' ) ) {
					YIT_Ajax::instance()->json_search_posts( $args );
				}
			}
			exit();
		}

		/**
		 * Add dynamic pricing badges
		 *
		 * @param string $badge_html Badge HTML.
		 * @param WC_Product $product Product.
		 *
		 * @return string
		 * @depreacted since 2.0
		 */
		public function add_dynamic_pricing_badges( $badge_html, $product ) {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Dynamic_Pricing_Compatibility::add_dynamic_pricing_badges', '2.0.0' );

			return '';
		}
	}
}

if ( ! function_exists( 'yith_wcbm_dynamic_pricing_compatibility' ) ) {
	/**
	 * Return the class instance
	 *
	 * @return YITH_WCBM_Dynamic_Pricing_Compatibility|YITH_WCBM_Dynamic_Pricing_Compatibility_Legacy
	 */
	function yith_wcbm_dynamic_pricing_compatibility() {
		return YITH_WCBM_Dynamic_Pricing_Compatibility_Legacy::get_instance();
	}
}
