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
class Downloadable extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Filtering\Filterable {

	public function __construct() {
		$this->set_type( 'column-wc-variation_downloadable' )
		     ->set_label( __( 'Downloadable', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_downloadable';
	}

	public function get_value( $id ) {
		$variation = new WC_Product_Variation( $id );

		return ac_helper()->icon->yes_or_no( $variation->get_downloadable() );
	}

	public function editing() {
		return new Editing\ProductVariation\Downloadable();
	}

	public function filtering() {
		return new Filtering\ProductVariation\Downloadable( $this );
	}

}