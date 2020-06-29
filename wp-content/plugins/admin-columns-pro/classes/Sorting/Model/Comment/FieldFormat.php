<?php

namespace ACP\Sorting\Model\Comment;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Sorter;
use ACP\Sorting\Strategy;
use ACP\Sorting\Type\DataType;

/**
 * @property Strategy\Comment $strategy
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
	 * Save memory by limiting the value lenght of the field
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
		return [
			'ids' => $this->get_sorted_ids(),
		];
	}

	/**
	 * @param string $var
	 *
	 * @return string|null
	 */
	private function get_query_var( $var ) {
		$query = $this->strategy->get_query();

		if ( ! $query || ! isset( $query->query_vars[ $var ] ) ) {
			return null;
		}

		return $query->query_vars[ $var ];
	}

	private function get_comment_status() {

		switch ( $this->get_query_var( 'status' ) ) {
			case 'hold' :
				return 0;
			case 'spam' :
				return 'spam';
			case 'trash' :
				return 'trash';
			case 'approve' :
				return 1;
		}

		return null;
	}

	/**
	 * @return array
	 */
	private function get_sorted_ids() {
		global $wpdb;

		$field = $this->value_length
			? sprintf( "LEFT( cc.%s, %s )", esc_sql( $this->field ), $this->value_length )
			: sprintf( "cc.%s", esc_sql( $this->field ) );

		$sql = sprintf( "
			SELECT cc.comment_ID AS id, %s AS value 
			FROM {$wpdb->comments} AS cc
		", $field );

		$status = $this->get_comment_status();

		if ( $status ) {
			$sql .= $wpdb->prepare( " WHERE cc.comment_approved = %s", $status );
		}

		$results = $wpdb->get_results( $sql );

		if ( ! $results ) {
			return [];
		}

		$values = [];

		foreach ( $results as $object ) {
			$values[ $object->id ] = $this->formatter->format_value( $object->value );
		}

		return ( new Sorter() )->sort( $values, $this->get_order(), $this->data_type, $this->show_empty );
	}

}