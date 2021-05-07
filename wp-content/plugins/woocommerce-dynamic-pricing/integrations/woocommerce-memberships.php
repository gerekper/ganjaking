<?php

/**
 * Class WC_Dynamic_Pricing_Memberships_Integration
 * Determines if membership discounts should be applied before or after Dynamic Pricing.
 * Controls the prices and membership discounting logic depending on it it's first or last.
 */
class WC_Dynamic_Pricing_Memberships_Integration {
	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Memberships_Integration();
		}
	}

	private function __construct() {
		$this->add_membership_filter();
		$this->add_get_price_to_discount_filter();
	}


	/**
	 * This function will check if we should apply membership discounts first, and if so, will return the price dynamic pricing already calculated
	 * which includes the membership discount.
	 *
	 * @param $price
	 * @param $base_price
	 * @param $product_id
	 * @param $member_id
	 * @param $product
	 *
	 * @return mixed
	 */
	public function on_wc_memberships_get_discounted_price( $price, $base_price, $product_id, $member_id, $product ) {
		if ( apply_filters( 'wc_dynamic_pricing_apply_membership_discounts_first', false, $product, get_current_user_id() ) ) {
			$cart_item = WC_Dynamic_Pricing_Context::instance()->get_cart_item_for_product( $product );

			//Product is in the context of the cart, and if we have already applied dynamic pricing discounts, just return the base price.
			if ( $cart_item && isset( $cart_item['discounts'] ) && ! empty( $cart_item['discounts'] ) ) {
				return $base_price;
			}
		}

		return $price;
	}

	/**
	 * This function will check if membership discounts should be applied first.  If so, the membership price is used as the base price that is passed back to
	 * Dynamic Pricing.  Dynamic Pricing will use the result of this function as the base for all it's calcuations for the product.
	 * @param $base_price
	 * @param $cart_item
	 *
	 * @return float|null
	 */
	public function on_get_price_to_discount( $base_price, $cart_item ) {
        $calculated_price = null;
	    if ( apply_filters( 'wc_dynamic_pricing_apply_membership_discounts_first', false, $cart_item['data'], get_current_user_id() ) ) {
            //Get the discounted price from memberships as the base price for all calculations on the product.

            $cart_item = WC_Dynamic_Pricing_Context::instance()->get_cart_item_for_product($cart_item['data']);

            if (isset($cart_item['discounts'])) {
                //Memberships discount has already been calculated.
                $calculated_price = $cart_item['discounts']['price_base'];
            } else {

                $this->remove_membership_filter();

                $calculated_price = wc_memberships()->get_member_discounts_instance()->get_discounted_price($base_price, $cart_item['data']);

                $this->add_membership_filter();
                //Normally we would have the issue of this getting calculated twice since memberships would also get_discounted_price later, however,
                //since we are able to disable the membership calculation ( since it's already been performed ) we can avoid this.
            }
        }

        return empty($calculated_price) ? $base_price : $calculated_price;
	}


	private function add_membership_filter() {
		add_filter( 'wc_memberships_get_discounted_price', array(
			$this,
			'on_wc_memberships_get_discounted_price'
		), 999, 5 );
	}

	private function remove_membership_filter() {
		remove_filter( 'wc_memberships_get_discounted_price', array(
			$this,
			'on_wc_memberships_get_discounted_price'
		), 999, 5 );
	}

	private function add_get_price_to_discount_filter() {
		add_filter( 'woocommerce_dynamic_pricing_get_price_to_discount', array(
			$this,
			'on_get_price_to_discount'
		), 0, 2 );
	}

	private function remove_get_price_to_discount_filter() {
		remove_filter( 'woocommerce_dynamic_pricing_get_price_to_discount', array(
			$this,
			'on_get_price_to_discount'
		), 0, 2 );
	}

}
