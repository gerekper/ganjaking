<?php

namespace ACA\WC\Field\ShopOrder\OrderDate;

use ACA\WC\Field\ShopOrder\OrderDate;
use ACP;
use WC_Order;

/**
 * @since 3.0
 */
class Modified extends OrderDate implements ACP\Sorting\Sortable, ACP\Search\Searchable {

	public function set_label() {
		$this->label = __( 'Modified', 'codepress-admin-columns' );
	}

	public function get_date( WC_Order $order ) {
		return $order->get_date_modified();
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\PostField( 'post_modified' );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Date\PostModified();
	}

}