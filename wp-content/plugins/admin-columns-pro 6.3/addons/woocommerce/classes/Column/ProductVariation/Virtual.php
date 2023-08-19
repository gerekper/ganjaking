<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACA\WC\Editing;
use ACA\WC\Filtering;
use ACP;
use WC_Product_Variation;

/**
 * @since 3.0
 */
class Virtual extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Filtering\Filterable {

	public function __construct() {
		$this->set_type( 'column-wc-variation_virtual' )
		     ->set_label( __( 'Virtual', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_virtual';
	}

	public function get_value( $id ) {
		$variation = new WC_Product_Variation( $id );

		return ac_helper()->icon->yes_or_no( $variation->get_virtual() );
	}

	public function editing() {
		return new Editing\ProductVariation\Virtual();
	}

	public function filtering() {
		return new Filtering\ProductVariation\Virtual( $this );
	}

}