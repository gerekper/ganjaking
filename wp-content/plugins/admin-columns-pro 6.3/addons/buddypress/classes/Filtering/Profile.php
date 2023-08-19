<?php

namespace ACA\BP\Filtering;

use ACA\BP\Column;
use ACP;
use WP_User_Query;

/**
 * @property Column\Profile $column
 */
class Profile extends ACP\Filtering\Model {

	public function __construct( Column\Profile $column ) {
		parent::__construct( $column );
	}

	/**
	 * @param WP_User_Query $query
	 */
	public function filter_by_callback( $query ) {
		global $wpdb;

		$where = $wpdb->prepare( 'value = %s', $this->get_filter_value() );

		$this->add_sql_where( $query, $where );
	}

	/**
	 * @param WP_User_Query $query
	 * @param string        $where
	 */
	protected function add_sql_where( $query, $where ) {
		global $wpdb, $bp;

		// Unique alias; when filtering on multiple items.
		$alias = 'bpx' . uniqid();

		// Join
		$query->query_from .= " INNER JOIN {$bp->profile->table_name_data} AS {$alias} ON ( {$alias}.user_id = {$wpdb->users}.ID )";

		// Where
		$query->query_where .= ' AND ( ' . $wpdb->prepare( "{$alias}.field_id = %d", $this->column->get_buddypress_field_id() ) . " AND {$alias}.{$where} )";
	}

	/**
	 * @param WP_User_Query $query
	 * @param string        $type
	 */
	protected function filter_by_ranged( $query, $type = 'numeric' ) {
		global $wpdb;

		$value = $this->get_filter_value();

		$directive = '%d';

		if ( 'CHAR' === strtoupper( $type ) ) {
			$directive = '%s';
		}

		if ( $value['min'] ) {
			$this->add_sql_where( $query, $wpdb->prepare( 'value >= ' . $directive, $value['min'] ) );
		}

		if ( $value['max'] ) {
			$this->add_sql_where( $query, $wpdb->prepare( 'value <= ' . $directive, $value['max'] ) );
		}
	}

	private function add_sql_empty( $query ) {
		global $wpdb, $bp;

		// Unique alias; when filtering on multiple items.
		$alias_first = 'xpdf' . uniqid();
		$alias_second = 'xpds' . uniqid();

		$query->query_from .= " LEFT JOIN {$bp->profile->table_name_data} AS " . $alias_first . " ON ( {$wpdb->users}.ID = {$alias_first}.user_id )";
		$query->query_from .= $wpdb->prepare( " AND {$alias_first}.field_id = %d", $this->column->get_buddypress_field_id() );
		$query->query_from .= " LEFT JOIN {$bp->profile->table_name_data} AS " . $alias_second . " ON ( {$wpdb->users}.ID = {$alias_second}.user_id )";

		$where = $wpdb->prepare( " AND ( {$alias_first}.user_id IS NULL OR ( {$alias_second}.field_id = %d AND {$alias_second}.value IN ( '0', 'no', 'false', 'off', '' ) ) )", $this->column->get_buddypress_field_id() );

		$query->query_where .= $where;
	}

	/**
	 * @param WP_User_Query $query
	 */
	public function filter_by_empty_value( $query ) {

		switch ( $this->get_filter_value() ) {

			case 'cpac_empty' :
				$this->add_sql_empty( $query );

				break;
			case 'cpac_nonempty' :
				$this->add_sql_where( $query, "value != ''" );

				break;
		}
	}

	/**
	 * @return bool
	 */
	private function is_empty_value() {
		return in_array( $this->get_filter_value(), [ 'cpac_empty', 'cpac_nonempty' ] );
	}

	public function get_filtering_vars( $vars ) {
		if ( $this->is_empty_value() ) {

			add_action( 'pre_user_query', [ $this, 'filter_by_empty_value' ] );
		} else {

			add_action( 'pre_user_query', [ $this, 'filter_by_callback' ] );
		}

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'empty_option' => true,
			'options'      => $this->get_xprofile_values(),
		];
	}

	// Utility

	protected function get_xprofile_values() {
		global $wpdb, $bp;

		$sql = $wpdb->prepare( "SELECT value FROM {$bp->profile->table_name_data} WHERE field_id = %d", $this->column->get_buddypress_field_id() );

		$values = (array) $wpdb->get_col( $sql );

		if ( ! $values ) {
			return [];
		}

		return array_combine( $values, $values );
	}

}