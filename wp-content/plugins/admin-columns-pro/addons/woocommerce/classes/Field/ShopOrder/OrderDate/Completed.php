<?php

namespace ACA\WC\Field\ShopOrder\OrderDate;

use ACA\WC\Field\ShopOrder\OrderDate;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACP;
use ACP\Sorting\Type\DataType;
use WC_Order;

/**
 * @since 3.0
 */
class Completed extends OrderDate implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\Filtering\Filterable {

	public function set_label() {
		$this->label = __( 'Completed', 'codepress-admin-columns' );
	}

	public function get_date( WC_Order $order ) {
		return $order->get_date_completed();
	}

	public function get_meta_key() {
		return '_date_completed';
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

	public function filtering() {
		return new Filtering\ShopOrder\MetaDate( $this->column );
	}

	public function search() {
		return new Search\Meta\Date\Timestamp( $this->get_meta_key(), 'post' );
	}

}