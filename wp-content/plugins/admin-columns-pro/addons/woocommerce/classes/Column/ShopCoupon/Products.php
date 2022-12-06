<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Export;
use ACA\WC\Search;
use ACP;

/**
 * @since 2.2
 */
class Products extends AC\Column
	implements ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'products' )
		     ->set_original( true );
	}

	public function export() {
		return new Export\ShopCoupon\Products( $this );
	}

	public function search() {
		return new Search\ShopCoupon\Products( 'product_ids' );
	}

}