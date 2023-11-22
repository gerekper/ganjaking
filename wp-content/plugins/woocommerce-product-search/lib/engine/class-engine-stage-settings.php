<?php
/**
 * class-engine-stage-settings.php
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
 * Engine stage settings.
 */
class Engine_Stage_Settings {

	/**
	 * Default engine stage configuration.
	 *
	 * @var array
	 */
	private static $defaults = array(
		'featured' => array(
			'enabled' => true,
			'caching' => null,
			'lifetime' => null,
			'priority' => null
		),
		'pagination' => array(
			'enabled' => true,
			'caching' => null,
			'lifetime' => null,
			'priority' => null
		),
		'posts' => array(
			'enabled' => true,
			'caching' => null,
			'lifetime' => null,
			'priority' => null
		),
		'price' => array(
			'enabled' => true,
			'caching' => null,
			'lifetime' => null,
			'priority' => null
		),
		'rating' => array(
			'enabled' => true,
			'caching' => null,
			'lifetime' => null,
			'priority' => null
		),
		'sale' => array(
			'enabled' => true,
			'caching' => null,
			'lifetime' => null,
			'priority' => null
		),
		'stock' => array(
			'enabled' => true,
			'caching' => null,
			'lifetime' => null,
			'priority' => null
		),
		'terms' => array(
			'enabled' => true,
			'caching' => null,
			'lifetime' => null,
			'priority' => null
		),
		'visibility' => array(
			'enabled' => true,
			'caching' => null,
			'lifetime' => null,
			'priority' => null
		),
		'words' => array(
			'enabled' => true,
			'caching' => null,
			'lifetime' => null,
			'priority' => null
		)
	);

	/**
	 * @var Engine_Stage_Settings
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
			self::$instance = new Engine_Stage_Settings();
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
		return defined( 'WPS_STAGES' ) && is_array( WPS_STAGES );
	}

	/**
	 * Provide the configuration settings.
	 *
	 * Provides the settings determined via the WPS_STAGES constant if set, otherwise the stored or default settings (in that order).
	 *
	 * @return array
	 */
	public function get() {
		$stage_settings = null;
		if ( defined( 'WPS_ENGINE' ) && is_array( WPS_ENGINE ) ) {

			if ( array_key_exists( 'stages', WPS_ENGINE ) && is_array( WPS_ENGINE['stages'] ) ) {
				$stage_settings = WPS_ENGINE['stages'];
			}
		}

		if ( defined( 'WPS_STAGES' ) && is_array( WPS_STAGES ) ) {
			$stage_settings = WPS_STAGES;
		}
		if ( $stage_settings !== null && is_array( $stage_settings ) ) {

			foreach ( $stage_settings as $stage_id => $data ) {

				if ( !is_array( $data ) ) {
					$stage_settings[$stage_id] = array();
				}
				if ( !isset( $data['enabled'] ) ) {
					$stage_settings[$stage_id]['enabled'] = true;
				}
			}
		} else {

			$stage_settings = $this->settings->get( 'stages', self::$defaults );
		}
		return $stage_settings;
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
			$this->settings->set( 'stages', $settings );
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
