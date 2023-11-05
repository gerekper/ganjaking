<?php

namespace Smush\Core\Modules\Background;

class Background_Utils {
	/**
	 * Thread safe version of get_site_option, queries the database directly to prevent use of cached values
	 *
	 * @param $option_id string
	 * @param $default
	 *
	 * @return false|mixed
	 */
	public function get_site_option( $option_id, $default = false ) {
		global $wpdb;

		$table        = $wpdb->options;
		$column       = 'option_name';
		$key_column   = 'option_id';
		$value_column = 'option_value';

		if ( is_multisite() ) {
			$table        = $wpdb->sitemeta;
			$column       = 'meta_key';
			$key_column   = 'meta_id';
			$value_column = 'meta_value';
		}

		return $this->get_value_from_db( $table, $column, $key_column, $option_id, $value_column, $default );
	}

	public function get_option( $option_id, $default = false ) {
		global $wpdb;

		$table        = $wpdb->options;
		$column       = 'option_name';
		$key_column   = 'option_id';
		$value_column = 'option_value';

		return $this->get_value_from_db( $table, $column, $key_column, $option_id, $value_column, $default );
	}

	private function get_value_from_db( $table, $column, $key_column, $option_id, $value_column, $default ) {
		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare( "
			SELECT *
			FROM {$table}
			WHERE {$column} = %s
			ORDER BY {$key_column} ASC
			LIMIT 1
		", $option_id ) );

		if ( empty( $row->$value_column ) || ! is_object( $row ) ) {
			return $default;
		}

		return maybe_unserialize( $row->$value_column );
	}
}