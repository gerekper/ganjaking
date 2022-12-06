<?php

namespace ACA\WC\Field\ShopOrder\OrderDate;

use ACA\WC\Field\ShopOrder\OrderDate;
use ACP;
use WC_Order;

/**
 * @since 3.0
 */
class Created extends OrderDate implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\Filtering\Filterable {

	public function set_label() {
		$this->label = __( 'Created', 'codepress-admin-columns' );
	}

	public function get_date( WC_Order $order ) {
		return $order->get_date_created();
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\PostField( 'post_date' );
	}

	public function filtering() {
		return new ACP\Filtering\Model\Post\Date( $this->column );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Date\PostDate();
	}

}