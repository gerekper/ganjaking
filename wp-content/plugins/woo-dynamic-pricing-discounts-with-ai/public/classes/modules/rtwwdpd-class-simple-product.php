<?php
/**
 * Class RTWWDPD_Module_Base to calculate discount according to simple product Modules.
 *
 * @since    1.0.0
 */
class RTWWDPD_Simple_Product extends RTWWDPD_Simple_Base {
	/**
	 * variable to set instance of simple product module.
	 *
	 * @since    1.0.0
	 */
	private static $instance;

	public static function rtwwdpd_instance() {
		if ( self::$instance == null ) {
			self::$instance = new RTWWDPD_Simple_Product( 'simple_product' );
		}
		return self::$instance;
	}
	/**
	 * function to set instance of simple product module.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $module_id ) {
		parent::__construct( $module_id );
	}

	/**
	 * Function to initialze rule.
	 *
	 * @since    1.0.0
	 */
	public function initialize_rules() {
		return false;
	}

	/**
	 * Function to perform discount on cart product.
	 *
	 * @since    1.0.0
	 */
	public function rtwwdpd_adjust_cart( $cart ) {
		return false;
	}

	/**
	 * Function to check product has allready disocunted.
	 *
	 * @since    1.0.0
	 */
	public function is_applied_to_product( $product ) {
		return false;
	}

	/**
	 * Function to check product has allready disocunted.
	 *
	 * @since    1.0.0
	 */
	public function get_discounted_price_for_shop( $product, $working_price ) {
		return false;
	}

}