<?php
/**
 * @package Polylang-WC
 */

/**
 * Child class of WC_Product_Data_Store_CPT allowing us to access protected methods or properties.
 *
 * @since 1.5
 */
class PLLWC_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {

	/**
	 * Public version of WC_Product_Data_Store_CPT::update_lookup_table().
	 *
	 * @since 1.5
	 *
	 * @param int    $id    ID of object to update.
	 * @param string $table Lookup table name.
	 * @return void
	 */
	public function wc_update_lookup_table( $id, $table ) {
		$this->update_lookup_table( $id, $table );
	}
}
