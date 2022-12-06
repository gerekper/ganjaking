<?php

namespace ACA\WC\Search\ProductSubscription;

use AC;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Period extends Comparison
	implements Comparison\Values {

	public function __construct() {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators, Value::STRING );
	}

	private function get_delimiter() {
		return '|';
	}

	private function get_period_options() {
		$options = [];

		foreach ( wcs_get_subscription_period_interval_strings() as $interval_key => $interval_label ) {

			foreach ( wcs_get_subscription_period_strings() as $period_key => $period_label ) {
				$key = $interval_key . $this->get_delimiter() . $period_key;
				$options[ $key ] = sprintf( '%s %s', $interval_label, $period_label );
			}

		}

		return $options;
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$values = explode( $this->get_delimiter(), $value->get_value() );

		$meta_query = [
			[
				'key'   => '_subscription_period_interval',
				'value' => $values[0],
			],
			[
				'key'   => '_subscription_period',
				'value' => $values[1],
			],
		];

		$bindings = new Bindings();
		$bindings->meta_query(
			$meta_query
		);

		return $bindings;
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( $this->get_period_options() );
	}

}