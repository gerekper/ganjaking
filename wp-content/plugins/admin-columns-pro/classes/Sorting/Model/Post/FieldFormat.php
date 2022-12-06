<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\SqlOrderByFactory;
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

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['orderby'] = SqlOrderByFactory::create_with_ids(
			"$wpdb->posts.ID",
			$this->get_sorted_ids(),
			$this->get_order()
		);

		return $clauses;
	}

	private function get_sorted_ids() {
		add_filter( 'posts_fields', [ $this, 'posts_fields_callback' ] );

		$values = [];

		$args = [
			'suppress_filters' => false,
			'fields'           => [],
		];

		foreach ( $this->strategy->get_results( $args ) as $object ) {
			$values[ $object->id ] = $this->formatter->format_value( $object->value );

			wp_cache_delete( $object->id, 'posts' );
		}

		return ( new Sorter() )->sort( $values, $this->data_type );
	}

	/**
	 * Only return fields required for sorting
	 * @return string
	 * @global wpdb $wpdb
	 */
	public function posts_fields_callback() {
		remove_filter( 'posts_fields', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$field = $this->value_length
			? sprintf( "LEFT( $wpdb->posts.%s, %s )", esc_sql( $this->field ), $this->value_length )
			: sprintf( "$wpdb->posts.%s", esc_sql( $this->field ) );

		return sprintf( "$wpdb->posts.ID AS id, %s AS value ", $field );
	}

}