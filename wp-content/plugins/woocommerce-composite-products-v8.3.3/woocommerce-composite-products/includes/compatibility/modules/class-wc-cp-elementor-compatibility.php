<?php
/**
 * WC_CP_Elementor_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    7.1.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elementor compatibility.
 *
 * @version  7.1.2
 */
class WC_CP_Elementor_Compatibility {

	public static function init() {
		add_filter( 'woocommerce_composite_form_classes', array( __CLASS__, 'additional_form_classes' ), 10, 2 );
	}

	/**
	 * If Elementor is enabled, we add an additional class `grouped_form`
	 * This class does not have additional default WC styling, and
	 * Elementor is using it to exclude it from some styling it does
	 *
	 * @param array                $form_classes
	 * @param WC_Product_Composite $product
	 *
	 * @return array
	 */
	public static function additional_form_classes( $form_classes, $product ) {

		$form_classes[] = 'grouped_form';

		return $form_classes;
	}
}

WC_CP_Elementor_Compatibility::init();
