<?php

namespace ACA\WC\Search\Binding;

use ACP\Query\Bindings;

class OrderItems extends Bindings {

	/**
	 * @var string
	 */
	private $order_item_alias;

	/**
	 * @var string
	 */
	private $order_item_meta_alias;

	public function __construct() {
		$this->order_item_alias = $this->get_unique_alias( 'oi' );
		$this->order_item_meta_alias = $this->get_unique_alias( 'oim' );
	}

	public function get_order_item_alias() {
		return $this->order_item_alias;
	}

	public function get_order_item_meta_alias() {
		return $this->order_item_meta_alias;
	}

	public function join_item_meta() {
		global $wpdb;

		$alias_order = $this->get_order_item_alias();
		$alias_meta = $this->get_order_item_meta_alias();

		$join = " LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS {$alias_order} ON ( {$wpdb->posts}.ID = {$alias_order}.order_id ) ";
		$join .= "LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS {$alias_meta} ON ( {$alias_order}.order_item_id = {$alias_meta}.order_item_id ) ";

		$this->join( $join );

		return $this;
	}

}