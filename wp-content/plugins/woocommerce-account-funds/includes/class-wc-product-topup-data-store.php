<?php

/**
 * Class for topup data store.
 *
 * @package WC_Account_Funds
 */

/**
 * Dummy data store for top-up product.
 */
class WC_Product_Topup_Data_Store extends WC_Product_Data_Store_CPT {
	/**
	 * Read the product.
	 *
	 * For top-up there's no real product stored in DB, so nothing to do in this
	 * method.
	 *
	 * @since 2.1.3
	 *
	 * @version 2.1.3
	 *
	 * @param mixed $product This value is ignored.
	 */
	public function read( &$product ) {
		$product->set_defaults();
		$product->set_id( wc_get_page_id( 'myaccount' ) );
		$product->set_props( array(
			'name'              => $product->get_title(),
			'virtual'           => $product->is_virtual(),
			'tax_status'        => $product->get_tax_status(),
			'sold_individually' => $product->is_sold_individually(),
		) );
		$product->set_object_read( true );
	}
}
