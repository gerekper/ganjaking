<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;
use WC_Product_Variation;

/**
 * @since 3.0
 */
class Enabled extends AC\Column
	implements ACP\Editing\Editable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-variation_enabled' )
		     ->set_label( __( 'Enabled', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$variation = new WC_Product_Variation( $id );

		return ac_helper()->icon->yes_or_no( 'publish' === $variation->get_status() );
	}

	public function editing() {
		return new Editing\ProductVariation\Enabled();
	}

	public function search() {
		return new Search\ProductVariation\Enabled();
	}

}