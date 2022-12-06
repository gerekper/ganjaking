<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.0
 */
class Product extends AC\Column
	implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'variation_product' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		$product_id = (int) $this->get_raw_value( $id );

		return ac_helper()->html->link( get_edit_post_link( $product_id ) . '#variation_' . $id, get_the_title( $product_id ) );
	}

	public function get_raw_value( $id ) {
		return get_post_field( 'post_parent', $id );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\PostParent();
	}

	public function search() {
		return new Search\ProductVariation\Product();
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}