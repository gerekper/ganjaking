<?php
/**
 * Simple Store Credit product
 *
 * @package WC_Store_Credit/Products
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Simple Store Credit product class.
 */
class WC_Store_Credit_Product extends WC_Product_Simple {

	/**
	 * Constructor.
	 *
	 * @since 3.2.0
	 *
	 * @param WC_Product|int $product Product instance or ID.
	 */
	public function __construct( $product = 0 ) {
		parent::__construct( $product );

		$this->set_virtual( true );
	}

	/**
	 * Get internal type.
	 *
	 * @since 3.2.0
	 *
	 * @return string
	 */
	public function get_type() {
		return 'store_credit';
	}
}
