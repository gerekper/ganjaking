<?php
/**
 * Auctions Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManegementPremium\Compatibility
 * @since   1.2.23
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Auctions_Compatibility' ) ) {
	/**
	 * Auctions Compatibility Class
	 */
	class YITH_WCBM_Auctions_Compatibility {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Auctions_Compatibility
		 */
		protected static $instance;


		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCBM_Auctions_Compatibility
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->include_compatibility_classes();

			add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );
			add_filter( 'yith_wcbm_badge_rules_types_before_adding_default_fields', array( $this, 'add_auction_rule_type' ) );
		}

		/**
		 * Include compatibility classes
		 */
		public function include_compatibility_classes() {
			require_once 'class-yith-wcbm-badge-rule-auction.php';
			require_once 'class-yith-wcbm-badge-rule-auction-data-store-cpt.php';
		}

		/**
		 * Add Badge rule Auction Data Store to WC ones.
		 *
		 * @param array $data_stores WC Data Stores.
		 *
		 * @return array
		 */
		public function register_data_stores( $data_stores ) {
			$data_stores['badge_rule_auction'] = 'YITH_WCBM_Badge_Rule_Auction_Data_Store_CPT';

			return $data_stores;
		}

		/**
		 * Add Dynamic Pricing Rule Type
		 *
		 * @param array $rules_types Rules Types.
		 *
		 * @return array
		 */
		public function add_auction_rule_type( $rules_types ) {
			$rules_types['auction'] = array(
				'title'    => _x( 'Auction rules', '[ADMIN] Badge Rule type title', 'yith-woocommerce-badges-management' ),
				// translators: %s is the name of out plugin YITH WooCommerce Auction.
				'desc'     => sprintf( _x( 'A badge to show in auction products created with the plugin %s.', '[ADMIN] Badge Rule type description', 'yith-woocommerce-badges-management' ), 'YITH WooCommerce Auction' ),
				'icon'     => 'auction-badge-rule',
				'fields'   => array(
					'badge'                     => array( 'type' => '' ),
					'badge_auction_not_started' => array(
						'label'          => __( 'Badge for auctions with status "scheduled"', 'yith-woocommerce-badges-management' ),
						'desc'           => __( 'Select the badge for all auctions marked as "Not Started".', 'yith-woocommerce-badges-management' ),
						'type'           => 'ajax-posts',
						'name'           => 'yith_wcbm_badge_rule[_badge_auction_not_started]',
						'data'           => array(
							'placeholder'          => __( 'Search Badge...', 'yith-woocommerce-badges-management' ),
							'post_type'            => YITH_WCBM_Post_Types::$badge,
							'minimum_input_length' => '1',
						),
						'field_position' => 1.2,
					),
					'badge_auction_started'     => array(
						'label'          => __( 'Badge for auctions with status "started"', 'yith-woocommerce-badges-management' ),
						'desc'           => __( 'Select the badge for all auctions marked as "Started".', 'yith-woocommerce-badges-management' ),
						'type'           => 'ajax-posts',
						'name'           => 'yith_wcbm_badge_rule[_badge_auction_started]',
						'data'           => array(
							'placeholder'          => __( 'Search Badge...', 'yith-woocommerce-badges-management' ),
							'post_type'            => YITH_WCBM_Post_Types::$badge,
							'minimum_input_length' => '1',
						),
						'field_position' => 1.5,
					),
					'badge_auction_finished'    => array(
						'label'          => __( 'Badge for auctions with status "finished"', 'yith-woocommerce-badges-management' ),
						'desc'           => __( 'Select the badge for all auctions marked as "Finished".', 'yith-woocommerce-badges-management' ),
						'type'           => 'ajax-posts',
						'name'           => 'yith_wcbm_badge_rule[_badge_auction_finished]',
						'data'           => array(
							'placeholder'          => __( 'Search Badge...', 'yith-woocommerce-badges-management' ),
							'post_type'            => YITH_WCBM_Post_Types::$badge,
							'minimum_input_length' => '1',
						),
						'field_position' => 1.8,
					),
				),
				'callback' => array( $this, 'get_product_badges_from_auction_rules' ),
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
		public function get_product_badges_from_auction_rules( $product_id ) {
			$badges  = array();
			$args    = array(
				'meta_key'   => '_type', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value' => 'auction', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			);
			$results = yith_wcbm_get_badge_rules( $args );

			$rule_ids = array();
			foreach ( $results as $rule_id ) {
				$rule_ids[] = $rule_id;
			}

			foreach ( $rule_ids as $rule_id ) {
				/**
				 * Dynamic Badge Rule
				 *
				 * @var YITH_WCBM_Badge_Rule_Auction $rule The Dynamic Rule.
				 */
				$rule = yith_wcbm_get_badge_rule( $rule_id );
				if ( $rule ) {
					$badges[] = $rule->get_badge_for_product( $product_id );
				}
			}

			return array_unique( $badges );
		}
	}
}
