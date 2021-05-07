<?php

class WC_Dynamic_Pricing_Simple_Product extends WC_Dynamic_Pricing_Simple_Base {

	private static $instance;

	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Simple_Product( 'simple_product' );
		}
		return self::$instance;
	}

	public function __construct( $module_id ) {
		parent::__construct( $module_id );
	}

	public function initialize_rules() {
		
	}

	public function adjust_cart( $cart ) {
		
	}

	public function is_applied_to_product( $product ) {
		return false;
	}

	public function get_discounted_price_for_shop( $product, $working_price ) {
		return false;
	}

}