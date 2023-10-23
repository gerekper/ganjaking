<?php
/**
 * Tag Badge Rule class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Objects
 * @since   2.0
 */

if ( ! class_exists( 'YITH_WCBM_Badge_Rule_Tag' ) ) {
	/**
	 * Badge Rule Tag Class
	 */
	class YITH_WCBM_Badge_Rule_Tag extends YITH_WCBM_Associative_badge_Rule {

		/**
		 * Badge rule object type
		 *
		 * @var string
		 */
		protected $badge_rule_type = 'tag';

		/**
		 * Data Store object type.
		 *
		 * @var string
		 */
		protected $data_store_object_type = 'badge_rule_tag';

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
			$valid   = false;
			$product = $product_id ? wc_get_product( $product_id ) : wc_get_product();
			if ( $product ) {
				if ( ! $this->is_product_excluded( $product->get_id() ) ) {
					$product_tags = $product->get_tag_ids();
					if ( array_intersect( $product_tags, $this->get_rules_associations_ids() ) ) {
						$valid = true;
					}
				}
			}

			return apply_filters( 'yith_wcbm_badge_rule_is_valid_for_product', $valid, $this, $product_id );
		}
	}
}
