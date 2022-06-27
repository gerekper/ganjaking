<?php
/**
 * Class to handle a discount for a shipping item.
 *
 * @package WC_Store_Credit/Item Discounts
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Item_Discount_Shipping class.
 */
class WC_Store_Credit_Item_Discount_Shipping extends WC_Store_Credit_Item_Discount {

	/**
	 * Calculates the item taxes.
	 *
	 * @since 3.0.0
	 *
	 * @param float $discounting_amount Item discounting amount.
	 */
	protected function calculate_taxes( $discounting_amount ) {
		parent::calculate_taxes( $discounting_amount );

		$price = $discounting_amount;

		if ( $this->inc_tax ) {
			$this->calculate_tax( $this->discount_taxes );
			$price -= $this->discount_tax;
		}

		/** This filter is documented in woocommerce/includes/class-wc-tax.php */
		$this->discount_taxes = apply_filters( 'woocommerce_calc_shipping_tax', $this->discount_taxes, $price, $this->get_item()->tax_rates ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingSinceComment
	}
}
