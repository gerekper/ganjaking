<?php

namespace ACA\WC\Search\ShopOrder;

use AC;
use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Currency extends Comparison\Meta
	implements Comparison\RemoteValues {

	public function __construct() {
		$operators = new Operators(
			[
				Operators::EQ,
			]
		);

		parent::__construct( $operators, '_order_currency', MetaType::POST );
	}

	public function get_values() {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT DISTINCT(meta_value) FROM {$wpdb->postmeta} WHERE meta_key = %s", $this->get_meta_key() );
		$entities = $wpdb->get_col( $sql );

		$options = array_combine( $entities, $entities );

		return AC\Helper\Select\Options::create_from_array( $options );
	}

}