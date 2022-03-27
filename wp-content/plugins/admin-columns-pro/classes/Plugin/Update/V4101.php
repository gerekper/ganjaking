<?php

namespace ACP\Plugin\Update;

use AC;
use AC\Plugin\Update;
use AC\Plugin\Version;

class V4101 extends Update {

	public function __construct() {
		parent::__construct( new Version( '4.1.1' ) );
	}

	public function apply_update() {
		$this->migrate_site_and_user_specific_settings();
		$this->rename_user_specific_settings();
		$this->delete_deprecated_settings();
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	private function validate_key( $key ) {
		if ( empty( $key ) ) {
			return false;
		}

		if ( ! is_string( $key ) ) {
			return false;
		}

		if ( strlen( $key ) < 10 ) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $key
	 *
	 * @return array
	 */
	private function get_meta( $key ) {
		global $wpdb;

		if ( ! $this->validate_key( $key ) ) {
			return [];
		}

		$sql = $wpdb->prepare( "
			SELECT *
			FROM {$wpdb->usermeta}
			WHERE meta_key LIKE %s
			ORDER BY user_id
		", $key );

		$results = $wpdb->get_results( $sql );

		if ( ! $results ) {
			return [];
		}

		return $results;
	}

	/**
	 * Migrate USER and SITE specific preferences
	 */
	private function migrate_site_and_user_specific_settings() {
		global $wpdb;

		$mapping = [
			'ac_sortedby'             => 'sorted_by',
			'cpac_layout_table'       => 'layout_table',
			'cpac_layout_columns'     => 'layout_columns',
			'cacie_editability_state' => 'editability_state',
		];

		foreach ( $mapping as $current => $new ) {
			$sql_meta_key = $wpdb->esc_like( $current ) . '%' . $wpdb->esc_like( get_current_blog_id() );

			foreach ( $this->get_meta( $sql_meta_key ) as $row ) {

				// Get original storage key from the meta_key
				$key = str_replace( $current, '', $row->meta_key );
				$key = ltrim( $key, '_' );
				$key = rtrim( $key, get_current_blog_id() );

				// Store as new preference
				$preferences = new AC\Preferences\Site( $new, $row->user_id );
				$preferences->set( $key, maybe_unserialize( $row->meta_value ) );
			}

			$this->delete( $current );
		}
	}

	/**
	 * Rename USER and SITE specific preferences
	 */
	private function rename_user_specific_settings() {
		global $wpdb;

		$mapping = [
			'acp_show_overflow_table' => 'show_overflow_table',
		];

		foreach ( $mapping as $current => $new ) {

			$meta_key = $wpdb->get_blog_prefix() . $current;

			foreach ( $this->get_meta( $wpdb->esc_like( $meta_key ) . '%' ) as $row ) {

				// Get original storage key from the meta_key
				$key = str_replace( $meta_key, '', $row->meta_key );

				// Store as new preference
				$preferences = new AC\Preferences\Site( $new, $row->user_id );
				$preferences->set( $key, maybe_unserialize( $row->meta_value ) );
			}

			$this->delete( $meta_key );
		}
	}

	/**
	 * Preference to be REMOVED
	 */
	private function delete_deprecated_settings() {
		$this->delete( 'cpac_sorting_preference' );
		$this->delete( 'cpac_layouts' );
		$this->delete( 'cpac_layout_' );
	}

	/**
	 * Remove meta data
	 *
	 * @param string $key
	 */
	private function delete( $key ) {
		global $wpdb;

		if ( ! $this->validate_key( $key ) ) {
			return;
		}

		$sql = $wpdb->prepare( "
				DELETE
				FROM {$wpdb->usermeta}
				WHERE meta_key LIKE %s
			", $wpdb->esc_like( $key ) . '%' );

		$wpdb->query( $sql );
	}

}