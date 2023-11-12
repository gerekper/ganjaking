<?php
/**
 * Class RTWWDPD_Module_Base to calculate discount according to Simple Modules.
 *
 * @since    1.0.0
 */
abstract class RTWWDPD_Simple_Base extends RTWWDPD_Module_Base {

	/**
	 * variable to check available rules.
	 *
	 * @since    1.0.0
	 */
	public $available_rulesets = array();

	/**
	 * construct function.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $module_id ) {
		parent::__construct( $module_id, 'simple' );

		add_action( 'init', array(&$this, 'initialize_rules'), 0 );
	}

	/**
	 * function to initialize rules.
	 *
	 * @since    1.0.0
	 */
	public abstract function initialize_rules();

	/**
	 * function to check if the product is same on which discount rule is made.
	 *
	 * @since    1.0.0
	 */
	public abstract function is_applied_to_product( $rtwwdpd_product );

	/**
	 * function to get product discounted amount.
	 *
	 * @since    1.0.0
	 */
	public abstract function get_discounted_price_for_shop( $rtwwdpd_product, $rtwwdpd_working_price );

	/**
	 * Function to check if a product is already discounted by the same rule.
	 *
	 * @since    1.0.0
	 */
	protected function rtwwdpd_is_cumulative( $cart_item, $cart_item_key, $default = false ) {
		global $woocommerce;
		$rtwwdpd_cumulative = null;
		if ( isset( WC()->cart->cart_contents[$cart_item_key]['discounts'] ) ) {
			if ( in_array( $this->module_id, WC()->cart->cart_contents[$cart_item_key]['discounts']['by'] ) ) {
				
				return false;
			} elseif ( count( array_intersect( array('simple_category', 'simple_membership', 'simple_group'), WC()->cart->cart_contents[$cart_item_key]['discounts']['by'] ) ) > 0 ) {
				$rtwwdpd_cumulative = true;
			}
		} else {
			$rtwwdpd_cumulative = $default;
		}

		return apply_filters( 'rtwwdpd_dynamic_pricing_is_cumulative', $rtwwdpd_cumulative, $this->module_id, $cart_item, $cart_item_key );
	}

	/**
	 * Function to get product price.
	 *
	 * @since    1.0.0
	 */
	public function get_product_working_price( $rtwwdpd_working_price, $rtwwdpd_product ) {
		return apply_filters( 'rtwwdpd_dynamic_pricing_get_product_price_to_discount', $rtwwdpd_working_price, $rtwwdpd_product );
	}

}