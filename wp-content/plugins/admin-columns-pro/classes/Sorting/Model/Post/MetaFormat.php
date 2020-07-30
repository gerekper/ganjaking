<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Sorter;
use ACP\Sorting\Strategy\Post;
use ACP\Sorting\Type\DataType;

/**
 * Sorts a post list table on a meta key. The meta value may contain mixed values, as long
 * as the supplied formatter can process them into a string.
 * @property Post $strategy
 * @since 5.2
 */
class MetaFormat extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	/**
	 * @var FormatValue
	 */
	protected $formatter;

	/**
	 * @param FormatValue   $formatter
	 * @param string        $meta_key
	 * @param DataType|null $data_type
	 */
	public function __construct( FormatValue $formatter, $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->formatter = $formatter;
		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		return [
			'ids' => $this->get_sorted_ids(),
		];
	}

	/**
	 * @return array
	 */
	private function get_sorted_ids() {
		global $wpdb;

		$join_type = $this->show_empty ? 'LEFT' : 'INNER';

		$sql = $wpdb->prepare( "
			SELECT pp.ID AS id, pm.meta_value AS value
			FROM {$wpdb->posts} AS pp
			{$join_type} JOIN {$wpdb->postmeta} AS pm ON pm.post_id = pp.ID
				AND pm.meta_key = %s AND pm.meta_value <> ''
			WHERE pp.post_type = %s
		", $this->meta_key, $this->strategy->get_post_type() );

		$status = $this->strategy->get_post_status();

		if ( $status ) {
			$sql .= sprintf( " AND pp.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $status ) ) );
		}

		$results = $wpdb->get_results( $sql );

		if ( ! $results ) {
			return [];
		}

		$values = [];

		foreach ( $results as $object ) {
			$values[ $object->id ][] = $this->formatter->format_value( $object->value );
		}

		foreach ( $values as $id => $meta_values ) {
			$values[ $id ] = trim( implode( ' ', $meta_values ) );
		}

		return ( new Sorter() )->sort( $values, $this->get_order(), $this->data_type, $this->show_empty );
	}

}
