<?php

namespace ACA\EC;

/**
 * Class ACA_EC_API
 * Interface to the EC API that works across the free and pro version
 */
class API {

	/**
	 * @return bool
	 */
	public static function is_pro() {
		return function_exists( 'Tribe_ECP_Load' );
	}

	/**
	 * @return array
	 */
	public static function get_additional_fields() {
		if ( ! self::is_pro() ) {
			return [];
		}

		$fields = wp_cache_get( 'aca_ec_custom_fields' );

		if ( ! $fields ) {
			$fields = tribe_get_option( 'custom-fields', [] );

			wp_cache_add( 'aca_ec_custom_fields', $fields, null, 15 );
		}

		return $fields;
	}

	/**
	 * @param string $meta_key
	 *
	 * @return array
	 */
	public static function get_field( $meta_key ) {
		$fields = self::get_additional_fields();

		foreach ( $fields as $field ) {
			if ( $meta_key === $field['name'] ) {
				return $field;
			}
		}

		return [];
	}

	/**
	 * @param string $meta_key
	 * @param string $var
	 *
	 * @return false|mixed
	 */
	public static function get( $meta_key, $var ) {
		$settings = self::get_field( $meta_key );

		if ( ! array_key_exists( $var, $settings ) ) {
			return false;
		}

		return $settings[ $var ];
	}

}