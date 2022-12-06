<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;
use WP_User_Query;

/**
 * Sorts a user list table on a meta key. The meta value may contain mixed values, as long
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

		$this->formatter = $formatter;
		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$query->query_orderby = sprintf( "ORDER BY %s, $wpdb->users.ID",
			SqlOrderByFactory::create_with_ids( "$wpdb->users.ID", $this->get_sorted_ids(), $this->get_order() ) ?: $query->query_orderby
		);
	}

	/**
	 * @return array
	 */
	private function get_sorted_ids() {
		global $wpdb;

		$sql = $wpdb->prepare( "
			SELECT uu.ID AS id, um.meta_value AS value
			FROM $wpdb->users AS uu
			LEFT JOIN $wpdb->usermeta AS um ON uu.ID = um.user_id
				AND um.meta_key = %s AND um.meta_value <> ''
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

		return ( new Sorter() )->sort( $values, $this->data_type );
	}

}
