<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Export;
use ACP;

/**
 * @since 2.0
 */
class Order extends AC\Column
	implements ACP\Filtering\Filterable, ACP\Export\Exportable, ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'order_title' )
		     ->set_original( true );
	}

	public function filtering() {
		return new ACP\Filtering\Model\Post\ID( $this );
	}

	public function export() {
		return new Export\ShopOrder\Order( $this );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\ID();
	}

}