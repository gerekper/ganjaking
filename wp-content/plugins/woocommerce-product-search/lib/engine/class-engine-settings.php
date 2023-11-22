<?php
/**
 * class-engine-settings.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Engine settings.
 */
class Engine_Settings {

	/**
	 * Default engine configuration.
	 *
	 * @var array
	 */
	private static $defaults = array(
		'lifetime' => null
	);

	/**
	 * @var Engine_Settings
	 */
	private static $instance = null;

	/**
	 * @var Settings
	 */
	private $settings = null;

	/**
	 * Create an instance.
	 */
	private function __construct() {
		$this->settings = Settings::get_instance();
	}

	/**
	 * Provide an instance.
	 *
	 * @return \com\itthinx\woocommerce\search\engine\Engine_Stage_Settings
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new Engine_Settings();
		}
		return self::$instance;
	}

	/**
	 * Provide the default configuration settings.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return self::$defaults;
	}

	/**
	 * Whether the configuration settings are fixed.
	 *
	 * @return boolean
	 */
	public static function is_hardwired() {
		return defined( 'WPS_ENGINE' ) && is_array( WPS_ENGINE );
	}

	/**
	 * Provide the configuration settings.
	 *
	 * Provides the settings determined via the WPS_STAGES constant if set, otherwise the stored or default settings (in that order).
	 *
	 * @return array
	 */
	public function get() {
		if ( defined( 'WPS_ENGINE' ) && is_array( WPS_ENGINE ) ) {

			if ( array_key_exists( 'engine', WPS_ENGINE ) && is_array( WPS_ENGINE['engine'] ) ) {
				$engine_settings = WPS_ENGINE['engine'];
			} else {
				$engine_settings = WPS_ENGINE;
			}
		} else {
			$engine_settings = $this->settings->get( 'engine', self::$defaults );
		}
		return $engine_settings;
	}

	/**
	 * Set the configuration settings.
	 *
	 * Has no effect if settings are hardwired.
	 *
	 * @param array $settings
	 */
	public function set( $settings ) {
		if ( !self::is_hardwired() ) {
			$this->settings->set( 'engine', $settings );
		}
	}

	/**
	 * Save the configuration settings.
	 *
	 * Has no effect if settings are hardwired.
	 */
	public function save() {
		if ( !self::is_hardwired() ) {
			$this->settings->save();
		}
	}
}
