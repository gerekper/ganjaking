<?php

namespace ACP\Search\Comparison\Meta;

use AC;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;
use ACP\Search\Value;

class Checkmark extends Meta
	implements Values {

	public function __construct( $meta_key, $meta_type ) {
		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $operators, $meta_key, $meta_type );
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			'1' => __( 'True', 'codepress-admin-columns' ),
			'0' => __( 'False', 'codepress-admin-columns' ),
		] );
	}

	public function get_meta_query( $operator, Value $value ) {
		$meta_query = [];

		switch ( $value->get_value() ) {

			case '1' :
				$meta_query = [
					'key'     => $this->get_meta_key(),
					'value'   => [ '0', 'no', 'false', 'off', '' ],
					'compare' => 'NOT IN',
				];

				break;
			case '0' :
				$meta_query = [
					'relation' => 'OR',
					[
						'key'     => $this->get_meta_key(),
						'compare' => 'NOT EXISTS',
					],
					[
						'key'   => $this->get_meta_key(),
						'value' => [ '0', 'no', 'false', 'off', '' ],
					],
				];

				break;
		}

		return $meta_query;
	}

}