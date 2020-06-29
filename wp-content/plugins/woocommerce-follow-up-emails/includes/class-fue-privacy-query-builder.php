<?php
class FUE_Privacy_Query_Builder {
	/**
	 * Construct the WHERE part in the query.
	 *
	 * @param array $db_data_where Data for the DB table.
	 * @param array $values        Values to use as mapping data.
	 *
	 * @return array An array strings in the format X=Y to use for a query.
	 */
	public static function construct_where( $db_data_where, $values ) {
		$where   = array();

		// Parse WHERE for SQL
		foreach ( $db_data_where as $field_name => $field_where ) {
			$field_name = esc_sql( $field_name );
			$map_field  = $field_where['map'];

			// Check if field to map value to exists in values array.
			if ( empty( $values[ $map_field ] ) ) {
				continue;
			}

			$value = esc_sql( $values[ $map_field ] );
			$where[] = "`{$field_name}` = '{$value}'";
		}

		return $where;
	}

	/**
	 * Construct the SELECT query as a whole.
	 *
	 * @param array  $fields   Fields to select.
	 * @param string $db_table Table to select from.
	 * @param array  $where    Constructed where values.
	 * @param int    $page     Which page to select.
	 * @param int    $limit    Values to limit per page.
	 *
	 * @return array An array strings in the format X=Y to use for a query.
	 */
	public static function run_select_query( $fields, $db_table, $where, $page = 1, $limit = 10 ) {
		global $wpdb;

		// Construct SQL
		if ( empty( $where ) || empty( $fields ) ) {
			return array();
		}

		$page  = intval( $page );
		$limit = intval( $limit );

		$sql = sprintf(
			'SELECT %s FROM %s WHERE %s LIMIT %d, %d',
			implode( ', ', $fields ),
			$wpdb->prefix . $db_table,
			implode( ' OR ', $where ),
			( $page - 1 ) * $limit,
			$limit
		);

		return $wpdb->get_results( $sql, ARRAY_A );
	}

	/**
	 * Construct the WHERE part in the query.
	 *
	 * @param array $db_data_fields Data for the DB table.
	 * @param array $values         Values to use as mapping data.
	 *
	 * @return array An array strings in the format X=Y to use for a query.
	 */
	public static function construct_set_fields( $db_data_fields ) {
		$values        = array();

		// Parse SET x1=y1, x2=y2, ... for SQL
		foreach ( $db_data_fields as $field_name => $field_data ) {
			$field_name = esc_sql( $field_name );
			if ( isset( $field_data['type'] ) ) {
				$value = esc_sql( wp_privacy_anonymize_data( $field_data['type'] ) );
				$values[] = "`{$field_name}` = '{$value}'";
			} elseif ( isset( $field_data['value'] ) ) {
				$value = esc_sql( $field_data['value'] );
				$values[] = "`{$field_name}` = '{$value}'";
			}
		}

		return $values;
	}

	/**
	 * Construct the UPDATE query as a whole.
	 *
	 * @param string $db_table Table to select from.
	 * @param array  $values   Constructed set values.
	 * @param array  $where    Constructed where values.
	 *
	 * @return array An array strings in the format X=Y to use for a query.
	 */
	public static function run_update_query( $db_table, $values, $where ) {
		global $wpdb;
		// Construct SQL
		if ( empty( $where ) || empty( $values ) ) {
			return 0;
		}

		$sql = sprintf(
			'UPDATE %s SET %s WHERE %s',
			$wpdb->prefix . $db_table,
			implode( ', ', $values ),
			implode( ' OR ', $where )
		);

		return $wpdb->query( $sql );
	}
}
