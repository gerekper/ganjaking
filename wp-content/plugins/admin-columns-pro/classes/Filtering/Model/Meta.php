<?php

namespace ACP\Filtering\Model;

use AC;
use ACP\Filtering\Model;

/**
 * @property AC\Column\Meta $column
 */
class Meta extends Model {

	/**
	 * @var bool
	 */
	private $serialized;

	public function __construct( AC\Column\Meta $column, $serialized = null ) {
		parent::__construct( $column );

		if ( null === $serialized ) {
			$serialized = $column->is_serialized();
		}

		$this->serialized = (bool) $serialized;
	}

	/**
	 * Get meta values by meta key
	 * @return array
	 */
	public function get_meta_values() {
		$query = new AC\Meta\Query( $this->column->get_meta_type() );
		$query->select( 'meta_value' )
		      ->distinct()
		      ->join()
		      ->where( 'meta_value', '!=', '' )
		      ->where( 'meta_key', $this->column->get_meta_key() )
		      ->order_by( 'meta_value' );

		if ( $this->column->get_post_type() ) {
			$query->where_post_type( $this->column->get_post_type() );
		}

		return $query->get();
	}

	/**
	 * @return array Filtered meta values
	 */
	public function get_meta_values_filtered() {
		$values = [];

		// SQL ignores whitespace when filtering
		$filtered = array_map( 'trim', $this->get_meta_values() );

		foreach ( $filtered as $value ) {
			if ( $this->validate_value( $value ) ) {
				$values[] = $value;
			}
		}

		return $values;
	}

	/**
	 * Get meta query empty_not_empty
	 *
	 * @param array $vars
	 *
	 * @return array Query vars
	 * @since 4.0
	 */
	protected function get_filtering_vars_empty_nonempty( $vars ) {
		if ( ! isset( $vars['meta_query'] ) ) {
			$vars['meta_query'] = [];
		}

		// Check if empty or nonempty is in string (also check for like operators)
		foreach ( $vars['meta_query'] as $id => $query ) {
			if ( isset( $query['value'] ) && in_array( $query['value'], [ 'cpac_empty', 'cpac_nonempty' ] ) ) {
				unset( $vars['meta_query'][ $id ] );
			}
		}

		switch ( $this->get_filter_value() ) {

			case 'cpac_empty' :
				$vars['meta_query'][] = [
					'relation' => 'OR',
					[
						'key'     => $this->column->get_meta_key(),
						'compare' => 'NOT EXISTS',
					],
					[
						'key'   => $this->column->get_meta_key(),
						'value' => '',
					],
				];
				break;

			case 'cpac_nonempty' :
				$vars['meta_query'][] = [
					[
						'key'     => $this->column->get_meta_key(),
						'value'   => '',
						'compare' => '!=',
					],
				];
				break;
		}

		return $vars;
	}

	/**
	 * @param array $vars Query args
	 * @param array $args Options
	 *
	 * @return array
	 * @since 4.0
	 */
	protected function get_filtering_vars_ranged( $vars, $args = [] ) {
		$defaults = [
			'min'  => false,
			'max'  => false,
			'key'  => $this->column->get_meta_key(),
			'type' => $this->get_data_type(),
		];

		$args = array_merge( $defaults, (array) $args );

		if ( $args['min'] ) {
			$vars['meta_query'][] = [
				'key'     => $args['key'],
				'value'   => $args['min'],
				'compare' => '>=',
				'type'    => $args['type'],
			];
		}

		if ( $args['max'] ) {
			$vars['meta_query'][] = [
				'key'     => $args['key'],
				'value'   => $args['max'],
				'compare' => '<=',
				'type'    => $args['type'],
			];
		}

		return $vars;
	}

	/**
	 * @param array $vars
	 *
	 * @return array
	 */
	public function get_filtering_vars( $vars ) {
		if ( $this->is_ranged() ) {
			return $this->get_filtering_vars_ranged( $vars, $this->get_filter_value() );
		}

		if ( $this->serialized ) {
			// Serialized
			$vars = $this->get_filtering_vars_serialized( $vars, $this->get_filter_value() );

		} else {
			// Exact
			$vars['meta_query'][] = [
				'key'   => $this->column->get_meta_key(),
				'value' => $this->get_filter_value(),
				'type'  => $this->get_data_type(),
			];
		}

		return $this->get_filtering_vars_empty_nonempty( $vars );
	}

	/**
	 * @return array
	 */
	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values() as $value ) {
			$options[ $value ] = $this->column->get_formatted_value( $value );
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

	/**
	 * @return array
	 */
	protected function get_meta_values_unserialized() {
		$values = [];

		foreach ( $this->get_meta_values() as $value ) {
			if ( is_serialized( $value ) ) {
				$value = unserialize( $value );

				if ( is_array( $value ) ) {
					$values = array_merge( $values, $value );
				}
			}
		}

		return array_filter( $values );
	}

	/**
	 * @param array  $vars
	 * @param string $value
	 *
	 * @return array
	 */
	protected function get_filtering_vars_serialized( $vars, $value ) {
		if ( in_array( $value, [ 'cpac_empty', 'cpac_nonempty' ] ) ) {
			return $vars;
		}

		$vars['meta_query'][] = [
			'key'     => $this->column->get_meta_key(),
			'value'   => serialize( $value ),
			'compare' => 'LIKE',
		];

		return $vars;
	}

}