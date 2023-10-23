<?php
/**
 * Dynamic Pricing Badge Rule class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Compatibility
 * @since   2.0
 */

if ( ! class_exists( 'YITH_WCBM_Badge_Rule_Dynamic_Pricing' ) ) {
	/**
	 * Badge Rule Dynamic Pricing Class
	 */
	class YITH_WCBM_Badge_Rule_Dynamic_Pricing extends YITH_WCBM_Associative_Badge_Rule {

		/**
		 * Badge rule object type
		 *
		 * @var string
		 */
		protected $badge_rule_type = 'dynamic-pricing';

		/**
		 * Valid Dynamic Rules
		 *
		 * @var array
		 */
		protected static $valid_dynamic_pricing_rules = array();

		/**
		 * YITH_WCBM_Badge_Rule_Dynamic_Pricing constructor
		 *
		 * @param int|YITH_WCBM_Badge_Rule|WP_Post $rule Rule, Rule ID or Rule Post.
		 */
		public function __construct( $rule = 0 ) {
			parent::__construct( $rule );
			$this->init_valid_dynamic_pricing_rules();
		}

		/**
		 * Init Valid Dynamic Pricing Rules
		 */
		protected function init_valid_dynamic_pricing_rules() {
			if ( ! self::$valid_dynamic_pricing_rules && is_callable( 'yith_wcbm_dynamic_pricing_compatibility' ) ) {
				self::$valid_dynamic_pricing_rules = yith_wcbm_dynamic_pricing_compatibility()->get_valid_rules();
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		|
		| Checks if a condition is true or false.
		|
		*/

		/**
		 * Check if the badge rule is valid for the Product
		 *
		 * @param int $product_id User ID.
		 *
		 * @return bool
		 */
		public function is_valid_for_product( $product_id = 0 ) {
			$product = wc_get_product( $product_id );

			return $this->is_enabled() && ! $this->is_product_excluded( $product->get_id() ) && $this->is_product_in_dynamic_rules( $product->get_id() );
		}

		/**
		 * Check if a product is in a dynamic rule
		 *
		 * @param int $product_id      Product ID.
		 * @param int $dynamic_rule_id Dynamic Rule ID.
		 *
		 * @return bool
		 */
		protected function is_product_in_dynamic_rule( $product_id, $dynamic_rule_id ) {
			return yith_wcbm_dynamic_pricing_compatibility()->product_is_in_rule( $product_id, $dynamic_rule_id );
		}

		/**
		 * Check if a product is in rules
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return bool
		 */
		protected function is_product_in_dynamic_rules( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				foreach ( $this->get_rules_associations_ids() as $dynamic_rule ) {
					if ( $this->is_product_in_dynamic_rule( $product_id, $dynamic_rule ) ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Get Badges for product
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return array|int[]
		 */
		public function get_badges_for_product( $product_id ) {
			$badges = array();
			foreach ( $this->get_associations() as $association ) {
				if ( $this->is_product_in_dynamic_rule( $product_id, $association['association'] ) ) {
					$badges[] = $association['badge'];
				}
			}

			return $badges;
		}
	}
}
