<?php

namespace ACP\ThirdParty\YoastSeo\Search;

use AC;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;
use ACP\Search\Value;

class IsIndexed extends Meta
	implements Values {

	/**
	 * @var int
	 */
	private $null_value;

	public function __construct( $meta_key, $null_value = null ) {
		$operators = new Operators( [
			Operators::EQ,
		] );

		$this->null_value = $null_value;

		parent::__construct( $operators, $meta_key, 'post', Value::INT );
	}

	public function is_valid() {
		return $this->get_meta_type() === 'post';
	}

	public function get_values() {
		return AC\Helper\Select\Options::create_from_array( [
			0 => __( 'Default for Post Type', 'codepress-admin-columns' ),
			1 => __( 'No' ),
			2 => __( 'Yes' ),
		] );
	}

	protected function get_meta_query( $operator, Value $value ) {
		$base_query = parent::get_meta_query( $operator, $value );

		if ( $value->get_value() == 0 ) {
			$operator = Operators::IS_EMPTY;

			return parent::get_meta_query( $operator, $value );
		}

		$query = [
			'relation' => 'OR',
			$base_query,
		];

		if ( $this->null_value === (int) $value->get_value() ) {
			$query[] = [
				'key'     => $this->get_meta_key(),
				'compare' => 'NOT EXISTS',
			];
		}

		return $query;
	}

}