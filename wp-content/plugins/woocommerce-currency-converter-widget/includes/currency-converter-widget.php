<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Currency Converter Widget
 *
 * @deprecated 1.9.0
 */
class WooCommerce_Widget_Currency_Converter extends \Themesquad\WC_Currency_Converter\Widget {

	/**
	 * Construct.
	 */
	public function __construct() {
		wc_deprecated_function( __FUNCTION__, '1.9.0', '\Themesquad\WC_Currency_Converter\Widget()' );
	}
}
