<?php

namespace ACA\WC\Search\ShopOrder\Address;

use AC;
use AC\MetaType;
use ACP;
use ACP\Search\Comparison;

class Country extends Comparison\Meta
	implements Comparison\RemoteValues {

	public function __construct( $meta_key ) {
		$operators = new ACP\Search\Operators(
			[
				ACP\Search\Operators::EQ,
				ACP\Search\Operators::NEQ,
			]
		);

		parent::__construct( $operators, $meta_key, MetaType::POST );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( WC()->countries->get_countries() );
	}

}