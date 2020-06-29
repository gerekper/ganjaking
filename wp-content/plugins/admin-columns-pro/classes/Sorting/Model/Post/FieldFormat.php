<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Sorter;
use ACP\Sorting\Strategy\Post;
use ACP\Sorting\Type\DataType;
use wpdb;

/**
 * @property Post $strategy
 * @since 5.2
 */
class FieldFormat extends AbstractModel {

	/**
	 * @param string $field
	 */
	protected $field;

	/**
	 * @var FormatValue
	 */
	protected $formatter;

	/**
	 * Save memory by limiting the value length of the field
	 * @var int
	 */
	protected $value_length;

	public function __construct( $field, FormatValue $formatter, DataType $data_type = null, $value_length = null ) {
		parent::__construct( $data_type );

		$this->field = (string) $field;
		$this->formatter = $formatter;
		$this->value_length = (int) $value_length;
	}

	/**
	 * @return array
	 */
	public function get_sorting_vars() {
		add_filter( 'posts_fields', [ $this, 'posts_fields_callback' ] );

		$args = [
			'suppress_filters' => false,
			'fields'           => [],
		];

		$ids = [];

		foreach ( $this->strategy->get_results( $args ) as $object ) {
			$ids[ $object->id ] = $this->formatter->format_value( $object->value );

			wp_cache_delete( $object->id, 'posts' );
		}

		return [
			'ids' => ( new Sorter() )->sort( $ids, $this->get_order(), $this->data_type, $this->show_empty ),
		];
	}

	/**
	 * Only return fields required for sorting
	 * @return string
	 * @global wpdb $wpdb
	 */
	public function posts_fields_callback() {
		global $wpdb;

		remove_filter( 'posts_fields', [ $this, __FUNCTION__ ] );

		$field = $this->value_length
			? sprintf( "LEFT( {$wpdb->posts}.%s, %s )", esc_sql( $this->field ), $this->value_length )
			: sprintf( "{$wpdb->posts}.%s", esc_sql( $this->field ) );

		return sprintf( "{$wpdb->posts}.ID AS id, %s AS value ", $field );
	}

}