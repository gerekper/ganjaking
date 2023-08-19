<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 1.2
 */
class Featured extends AC\Column
	implements ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'featured' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $id ) {
		return wc_get_product( $id )->is_featured();
	}

	public function filtering() {
		return new Filtering\Product\Featured( $this );
	}

	public function sorting() {
		return new Sorting\Product\Featured();
	}

	public function search() {
		return new Search\Product\Featured();
	}

}