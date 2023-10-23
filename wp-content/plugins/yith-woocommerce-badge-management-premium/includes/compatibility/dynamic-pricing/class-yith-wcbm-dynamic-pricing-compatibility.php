<?php
/**
 * Dynamic Pricing Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Compatibility
 * @since   1.2.8
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Dynamic_Pricing_Compatibility' ) ) {
	/**
	 * Dynamic Pricing Compatibility Class
	 */
	class YITH_WCBM_Dynamic_Pricing_Compatibility extends YITH_WCBM_Dynamic_Pricing_Compatibility_Legacy {

		/**
		 * Retrieve the discounted price
		 *
		 * @param float      $price   The price.
		 * @param WC_Product $product The product.
		 *
		 * @return float
		 * @since 1.4.9
		 */
		public function get_discounted_price( $price, $product ) {
			return YWDPD_Frontend::get_instance()->get_dynamic_price( $product->get_price( 'edit' ), $product );
		}

		/**
		 * Get dynamic Pricing Rules
		 *
		 * @return array
		 */
		public function get_rules() {
			return is_callable( 'ywdpd_get_price_rules' ) ? ywdpd_get_price_rules() : parent::get_valid_rules();
		}

		/**
		 * Get dynamic Pricing Rules
		 *
		 * @return array
		 */
		public function get_valid_rules() {
			$rules = $this->get_rules();

			return array_filter( $rules, array( $this, 'is_rule_valid' ) );
		}

		/**
		 * Check if a dynamic rule is valid
		 *
		 * @param YWDPD_Category_Discount $dynamic_rule The dynamic pricing rule.
		 *
		 * @return  bool
		 */
		public function is_rule_valid( $dynamic_rule ) {
			return $dynamic_rule->is_valid();
		}

		/**
		 * Get Dynamic Rule by ID.
		 *
		 * @param int $rule_id The rule ID.
		 *
		 * @return YWDPD_Rule|false
		 */
		public function get_dynamic_rule_by_id( $rule_id ) {
			return ywdpd_get_rule( $rule_id );
		}

		/**
		 * Check if a product is in one rule
		 *
		 * @param int   $product_id   Product ID.
		 * @param array $dynamic_rule Dynamic Rule.
		 *
		 * @return bool
		 */
		public function product_is_in_rule( $product_id, $dynamic_rule ) {
			$product = wc_get_product( $product_id );
			$rule    = $this->get_dynamic_rule_by_id( $dynamic_rule );
			$valid   = false;
			if ( $rule && $product ) {
				$valid = $rule->is_valid() && ! ( $product->is_on_sale() && $rule->is_disabled_on_sale() ) && $rule->is_valid_to_apply( $product );
				if ( ! $valid ) {
					$valid = $rule->is_enabled_apply_adjustment_to() && $rule->is_valid_to_adjust( $product );
				}
			}

			return $valid;
		}
	}
}
