<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Product Finder Widget.
 *
 * @since 1.0.0
 * @deprecated 1.4.0
 */
class WC_Product_Finder_Widget extends \Themesquad\WC_Product_Finder\Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		wc_deprecated_function(
			__FUNCTION__,
			'1.4.0',
			'\Themesquad\WC_Product_Finder\Widget'
		);
		parent::__construct();
	}

	/**
	 * Handle widget registration
	 */
	public static function register() {
		register_widget( __CLASS__ );
	}
}
