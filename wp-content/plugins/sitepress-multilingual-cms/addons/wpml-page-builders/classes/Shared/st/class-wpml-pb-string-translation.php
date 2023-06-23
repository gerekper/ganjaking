<?php

use WPML\LIB\WP\Cache;
class WPML_PB_String_Translation {

	/** @var  wpdb $wpdb */
	protected $wpdb;

	/** @const string CACHE_GROUP_KEY **/
	const CACHE_GROUP_KEY = 'wpml-pb-package-strings';

	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
		wp_cache_add_non_persistent_groups( self::CACHE_GROUP_KEY );
	}

	/**
	 * @param array $package_data
	 *
	 * @return array
	 */
	public function get_package_strings( array $package_data ) {
		$strings    = array();
		$package_id = $this->get_package_id( $package_data );

		if ( $package_id ) {
			$getPackageStrings = Cache::memorize(
				self::CACHE_GROUP_KEY,
				HOUR_IN_SECONDS,
				function( $packageId ) {
					return $this->getPackageStringsFromDb( $packageId );
				}
			);

			$strings = $getPackageStrings( $package_id );
		}

		return $strings;
	}

	/**
	 * @param int $packageId
	 *
	 * @return array
	 */
	private function getPackageStringsFromDb( $packageId ) {
		$strings = [];
		// phpcs:disable WordPress.WP.PreparedSQL.NotPrepared
		$sql_to_get_strings_with_package_id = $this->wpdb->prepare( "SELECT *
			FROM {$this->wpdb->prefix}icl_strings s 
			WHERE s.string_package_id=%d",
		$packageId );

		$packageStrings = $this->wpdb->get_results( $sql_to_get_strings_with_package_id );
		// phpcs:enable

		if ( ! empty( $packageStrings ) ) {
			foreach ( $packageStrings as $string ) {
				$strings[ $this->get_string_hash( $string->value ) ] = array(
					'value'      => $string->value,
					'context'    => $string->context,
					'name'       => $string->name,
					'id'         => $string->id,
					'package_id' => $packageId,
					'location'   => $string->location,
				);
			}
		}

		return $strings;
	}

	public function remove_string( array $string_data ) {
		icl_unregister_string( $string_data['context'], $string_data['name'] );

		$field_type = 'package-string-' . $string_data['package_id'] . '-' . $string_data['id'];
		$job_id = $this->get_job_id( $field_type );
		if ( ! $job_id || ! $this->is_job_in_progress( $job_id ) ) {
			$this->wpdb->delete( $this->wpdb->prefix . 'icl_translate', array( 'field_type' => $field_type ), array( '%s' ) );
		}
	}

	/**
	 * @param string $field_type
	 *
	 * @return string|null
	 */
	private function get_job_id( $field_type ) {
		return $this->wpdb->get_var( $this->wpdb->prepare( "SELECT MAX(job_id) FROM {$this->wpdb->prefix}icl_translate WHERE field_type = %s", $field_type ) );
	}

	/**
	 * @param int $job_id
	 *
	 * @return bool
	 */
	private function is_job_in_progress( $job_id ) {
		return ! (bool) $this->wpdb->get_var( $this->wpdb->prepare( "SELECT translated FROM {$this->wpdb->prefix}icl_translate_job WHERE job_id = %d", $job_id ) );
	}

	/**
	 * @param array $package_data
	 *
	 * @return bool
	 */
	private function get_package_id( array $package_data ) {
		$package_id = false;
		$sql_to_get_package_id = $this->wpdb->prepare( "SELECT s.ID
		FROM {$this->wpdb->prefix}icl_string_packages s
		WHERE s.kind=%s AND s.name=%s AND s.title=%s AND s.post_id=%s",
		$package_data['kind'], $package_data['name'], $package_data['title'], $package_data['post_id'] );

		$result = $this->wpdb->get_row( $sql_to_get_package_id );

		if ( isset( $result->ID ) ) {
			$package_id = $result->ID;
		}

		return $package_id;
	}

	/**
	 * @param string $string_value
	 *
	 * @return string
	 */
	public function get_string_hash( $string_value ) {
		return md5( $string_value );
	}
}
