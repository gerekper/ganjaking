<?php
/**
 * Data store class
 *
 * @package WordPress
 * @subpackage Importer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PCSVIS_Product_Data_Store extends WC_Product_Data_Store_CPT {

	/**
	 * Update the lookup table for a single product.
	 *
	 * @param int $product_id Product ID.
	 */
	public function update_lookup( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( $product && is_a( $product, '\WC_Product' ) ) {
			$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );
		}
	}

}
