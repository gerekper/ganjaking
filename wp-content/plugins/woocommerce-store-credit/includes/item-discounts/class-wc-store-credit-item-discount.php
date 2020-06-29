<?php
/**
 * Class to handle a discount for an item.
 *
 * @package WC_Store_Credit/Item Discounts
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Item_Discount class.
 */
class WC_Store_Credit_Item_Discount {

	/**
	 * The item which to apply the discount.
	 *
	 * @var mixed
	 */
	protected $item;

	/**
	 * Item discount.
	 *
	 * @var float
	 */
	protected $discount = 0.0;

	/**
	 * Item tax discount.
	 *
	 * @var float
	 */
	protected $discount_tax = 0.0;

	/**
	 * Applied discounts to the taxes.
	 *
	 * @var array
	 */
	protected $discount_taxes = array();

	/**
	 * Whether the discount has taxes included.
	 *
	 * @var bool
	 */
	protected $inc_tax = false;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $item               The item which to apply the discount.
	 * @param float $discounting_amount Item discounting amount.
	 * @param bool  $inc_tax            Whether the passed discount has taxes included.
	 */
	public function __construct( $item, $discounting_amount = 0.0, $inc_tax = false ) {
		$this->item    = $item;
		$this->inc_tax = $inc_tax;

		$this->calculate_discounts( $discounting_amount );
	}

	/**
	 * Gets the item which to apply the discount.
	 *
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	public function get_item() {
		return $this->item;
	}

	/**
	 * Gets the item discount.
	 *
	 * @since 3.0.0
	 *
	 * @return float
	 */
	public function get_discount() {
		return $this->discount;
	}

	/**
	 * Gets the item tax discount.
	 *
	 * @since 3.0.0
	 *
	 * @return float
	 */
	public function get_discount_tax() {
		return $this->discount_tax;
	}

	/**
	 * Gets the discounts applied to the taxes.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_discount_taxes() {
		return $this->discount_taxes;
	}

	/**
	 * Gets all the discounts.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_discounts() {
		return array(
			'base'  => $this->get_discount(),
			'tax'   => $this->get_discount_tax(),
			'taxes' => $this->get_discount_taxes(),
		);
	}

	/**
	 * Calculates the discounts applied to the item.
	 *
	 * @since 3.0.0
	 *
	 * @param float $discounting_amount Item discounting amount.
	 */
	protected function calculate_discounts( $discounting_amount ) {
		$this->calculate_taxes( $discounting_amount );
		$this->calculate_tax( $this->discount_taxes );

		$this->discount = ( $this->inc_tax ? $discounting_amount - $this->discount_tax : $discounting_amount );
	}

	/**
	 * Calculates the item taxes.
	 *
	 * @since 3.0.0
	 *
	 * @param float $discounting_amount Item discounting amount.
	 */
	protected function calculate_taxes( $discounting_amount ) {
		$this->discount_taxes = WC_Tax::calc_tax( $discounting_amount, $this->get_item()->tax_rates, $this->inc_tax );
	}

	/**
	 * Calculates the item tax discount.
	 *
	 * @since 3.0.0
	 *
	 * @param array $taxes An array with the item taxes.
	 */
	protected function calculate_tax( $taxes ) {
		$taxes = ( wc_store_credit_round_tax_at_subtotal() ? $taxes : array_map( 'wc_round_tax_total', $taxes ) );

		$this->discount_tax = array_sum( $taxes );
	}
}
