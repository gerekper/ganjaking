<?php

namespace ACP\Sorting\Model\Taxonomy;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Sorter;
use ACP\Sorting\Strategy\Taxonomy;
use ACP\Sorting\Type\DataType;

/**
 * @property Taxonomy $strategy
 */
class MetaFormat extends AbstractModel {

	/**
	 * @var FormatValue
	 */
	private $formatter;

	/**
	 * @var string
	 */
	private $meta_key;

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
			SELECT terms.term_id AS id, tm.meta_value AS value
			FROM {$wpdb->terms} AS terms
			{$join_type} JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_id = terms.term_id
			    AND tt.taxonomy = %s
			INNER JOIN {$wpdb->termmeta} AS tm ON tm.term_id = terms.term_id
				AND tm.meta_key = %s AND tm.meta_value <> ''
		", $this->strategy->get_taxonomy(), $this->meta_key );

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

		return ( new Sorter() )->sort( $values, 'ASC', $this->data_type, $this->show_empty );
	}

}
