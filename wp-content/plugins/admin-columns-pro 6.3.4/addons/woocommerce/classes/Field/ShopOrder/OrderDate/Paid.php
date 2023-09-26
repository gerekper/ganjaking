<?php

namespace ACA\WC\Field\ShopOrder\OrderDate;

use ACA\WC\Field\ShopOrder\OrderDate;
use ACA\WC\Search;
use ACP;
use ACP\Sorting\Type\DataType;
use WC_Order;

/**
 * @since 3.0
 */
class Paid extends OrderDate implements ACP\Sorting\Sortable, ACP\Search\Searchable {

	public function set_label() {
		$this->label = __( 'Paid', 'codepress-admin-columns' );
	}

	public function get_date( WC_Order $order ) {
		return $order->get_date_paid();
	}

	public function get_meta_key() {
		return '_date_paid';
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

	public function search() {
		return new Search\Meta\Date\Timestamp( $this->get_meta_key(), 'post' );
	}

}