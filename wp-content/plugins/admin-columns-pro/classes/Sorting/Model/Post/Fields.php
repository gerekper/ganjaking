<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;
use wpdb;

class Fields extends AbstractModel {

	/**
	 * @param array
	 */
	private $fields;

	/**
	 * @var FormatValue
	 */
	protected $formatter;

	public function __construct( array $fields, FormatValue $formatter = null, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->fields = $fields;
		$this->formatter = $formatter;
	}

	/**
	 * @return array
	 */
	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['orderby'] = SqlOrderByFactory::create_with_ids( "$wpdb->posts.ID", $this->get_sorted_ids(), $this->get_order() ) ?: $clauses['orderby'];

		return $clauses;
	}

	private function get_sorted_ids() {
		add_filter( 'posts_fields', [ $this, 'posts_fields_callback' ] );

		$args = [
			'suppress_filters' => false,
			'fields'           => [],
		];

		$ids = [];

		$results = $this->strategy->get_results( $args );

		foreach ( $results as $object ) {
			$value = '';

			foreach ( $this->fields as $field ) {
				$field_value = $object->{$field};

				$value .= $this->formatter
					? $this->formatter->format_value( $field_value )
					: $field_value;
			}

			$ids[ $object->id ] = $value;

			wp_cache_delete( $object->id, 'posts' );
		}

		$ids = array_filter( $ids );

		natcasesort( $ids );

		return array_keys( $ids );
	}

	/**
	 * Only return fields required for sorting
	 * @return string
	 * @global wpdb $wpdb
	 */
	public function posts_fields_callback() {
		remove_filter( 'posts_fields', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$field = sprintf( '%s.ID AS id', $wpdb->posts );

		foreach ( $this->fields as $_field ) {
			$field .= sprintf( ", LEFT( $wpdb->posts.%s, 100 ) AS %s", esc_sql( $_field ), esc_sql( $_field ) );
		}

		return $field;
	}

}