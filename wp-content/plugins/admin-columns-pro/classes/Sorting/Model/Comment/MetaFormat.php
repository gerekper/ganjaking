<?php

namespace ACP\Sorting\Model\Comment;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;

/**
 * Sorts a comment list table on a meta key. The meta value may contain mixed values, as long
 * as the supplied formatter can process them into a string.
 * @since 5.2
 */
class MetaFormat extends AbstractModel {

	/**
	 * @var string
	 */
	private $meta_key;

	/**
	 * @var FormatValue
	 */
	private $formatter;

	/**
	 * @param FormatValue   $formatter
	 * @param string        $meta_key
	 * @param DataType|null $data_type
	 */
	public function __construct( FormatValue $formatter, $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_key = $meta_key;
		$this->formatter = $formatter;
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
			SELECT cc.comment_ID AS id, cm.meta_value AS value
			FROM {$wpdb->comments} AS cc
			{$join_type} JOIN {$wpdb->commentmeta} AS cm ON cm.comment_id = cc.comment_ID
				AND cm.meta_key = %s AND cm.meta_value <> ''
		", $this->meta_key );

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
