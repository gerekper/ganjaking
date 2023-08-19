<?php

namespace ACA\WC\Search\ProductSubscription;

use AC;
use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Options extends Comparison\Meta
	implements Comparison\Values {

	/**
	 * @var array
	 */
	private $options;

	public function __construct( $meta_key, $options ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::NEQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		$this->options = $options;

		parent::__construct( $operators, $meta_key, MetaType::POST );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( $this->options );
	}

}