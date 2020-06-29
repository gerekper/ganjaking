<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Product_Deposit
 */
class WC_Product_Deposit extends WC_Product_Simple {

	/**
	 * Constructor.
	 *
	 * @param WC_Product $product Product instance.
	 */
	public function __construct( $product ) {
		parent::__construct( $product );
		$this->product_type = $this->get_type();
	}

	/**
	 * Product type.
	 *
	 * @since 2.1.5
	 *
	 * @version 2.1.5
	 *
	 * @return string Type of product.
	 */
	public function get_type() {
		return 'deposit';
	}


}
