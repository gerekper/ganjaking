<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update;
use AC\Plugin\Version;

class V6002 extends Update {

	public function __construct() {
		parent::__construct( new Version( '6.0.2' ) );
	}

	public function apply_update(): void
    {
		global $wpdb;

		$sql = $wpdb->prepare( "
			SELECT * 
			FROM $wpdb->usermeta 
			WHERE meta_key = %s 
			      AND meta_value != ''
		",
			$wpdb->prefix . 'ac_preferences_export_columns'
		);

		$results = $wpdb->get_results( $sql );

		if ( ! $results ) {
			return;
		}

		foreach ( $results as $row ) {
			$meta_value = unserialize( $row->meta_value, [ 'allowed_classes' => false ] );

			$data = $meta_value
				? $this->convert_to_new_format( $meta_value )
				: [];

			$wpdb->update(
				$wpdb->usermeta,
				[
					'meta_value' => serialize( $data ),
				],
				[
					'umeta_id' => $row->umeta_id,
				]
			);
		}
	}

	private function convert_to_new_format( array $data ): array {
		$new = [];

		foreach ( $data as $list_id => $preference_column_names ) {
			$new_columns = [];

			$preference_column_names = array_filter( $preference_column_names, 'is_string' );

			foreach ( $preference_column_names as $column_name ) {
				$new_columns[] = [
					'column_name' => $column_name,
					'active'      => true,
				];
			}

			$inactive = array_diff( $this->get_stored_column_names( $list_id ), $preference_column_names );

			foreach ( $inactive as $column_name ) {
				$new_columns[] = [
					'column_name' => $column_name,
					'active'      => false,
				];
			}

			$new[ $list_id ] = $new_columns;
		}

		return $new;
	}

	private function get_stored_column_names( string $list_id ): array {
		global $wpdb;

		$sql = $wpdb->prepare( "
			SELECT columns 
			FROM {$wpdb->prefix}admin_columns 
			WHERE list_id = %s
			",
			$list_id
		);

		$column_data = $wpdb->get_var( $sql );

		if ( ! $column_data ) {
			return [];
		}

		$columns = unserialize( $column_data, [ 'allowed_classes' => false ] );

		if ( ! $columns ) {
			return [];
		}

		return array_keys( $columns );
	}

}