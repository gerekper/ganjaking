<?php

namespace SearchWP\Results;

use SearchWP\Settings as PluginSettings;

/**
 * Manage Search Results Page settings.
 *
 * @since 4.3.6
 */
class Settings {

	/**
	 * Default settings.
	 *
	 * @since 4.3.6
	 *
	 * @var array
	 */
	const DEFAULTS = [
		'swp-layout-theme'        => 'alpha',
		'swp-layout-style'        => 'list',
		'swp-results-per-row'     => 3,
		'swp-image-size'          => '',
		'swp-title-color'         => '',
		'swp-title-font-size'     => '',
		'swp-price-color'         => '',
		'swp-price-font-size'     => '',
		'swp-description-enabled' => true,
		'swp-button-enabled'      => false,
		'swp-button-label'        => '',
		'swp-button-bg-color'     => '',
		'swp-button-font-color'   => '',
		'swp-button-font-size'    => '',
		'swp-results-per-page'    => 10,
		'swp-pagination-style'    => '',
	];

	/**
	 * Get a single setting or all settings if setting name is not specified.
	 *
	 * @since 4.3.6
	 *
	 * @param string $name Setting name.
	 *
	 * @return mixed|null Setting value or null if setting is not registered.
	 */
	public static function get( $name = '' ) {

		$settings = self::get_all();

		if ( empty( $name ) ) {
			return apply_filters( 'searchwp\results\settings', $settings );
		}

		if ( ! array_key_exists( $name, self::DEFAULTS ) ) {
			return null;
		}

		return apply_filters( 'searchwp\results\setting', $settings[ $name ], $name );
	}

	/**
	 * Update a single setting.
	 *
	 * @since 4.3.6
	 *
	 * @param string $name  Setting name.
	 * @param mixed  $value Setting value.
	 *
	 * @return mixed|null Setting value or null if setting is not registered
	 */
	public static function update( $name, $value ) {

		if ( ! array_key_exists( $name, self::DEFAULTS ) ) {
			return null;
		}

		$settings = self::get_all();

		$settings[ $name ] = sanitize_text_field( $value );

		return self::update_option( $settings ) ? $value : null;
	}

	/**
	 * Update multiple settings.
	 *
	 * @since 4.3.6
	 *
	 * @param array $data Settings data.
	 *
	 * @return array|null
	 */
	public static function update_multiple( $data ) {

		if ( ! is_array( $data ) ) {
			return null;
		}

		$settings = self::get_all();

		foreach ( $data as $name => $value ) {
			if ( array_key_exists( $name, self::DEFAULTS ) ) {
				$settings[ $name ] = sanitize_text_field( $value );
			}
		}

		return self::update_option( $settings );
	}

	/**
	 * Get all settings.
	 * Makes sure all registered settings are included.
	 *
	 * @since 4.3.6
	 *
	 * @return array
	 */
	private static function get_all() {

		$option = self::get_option();

		if ( empty( $option ) ) {
			return self::DEFAULTS;
		}

		// Make sure no unregistered settings are returned.
		$settings = array_intersect_key( self::get_option(), self::DEFAULTS );

		return array_merge( self::DEFAULTS, $settings );
	}

	/**
	 * Get forms DB option.
	 *
	 * @since 4.3.6
	 *
	 * @return array
	 */
	private static function get_option() {

		return json_decode( PluginSettings::get( 'results_page' ), true );
	}

	/**
	 * Update forms DB option.
	 *
	 * @since 4.3.6
	 *
	 * @param array $data Option data.
	 *
	 * @return mixed|null
	 */
	private static function update_option( $data ) {

		return PluginSettings::update( 'results_page', wp_json_encode( $data ) );
	}
}
