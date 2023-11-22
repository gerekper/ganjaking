<?php
/**
 * class-cache-settings.php
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
 * Cache settings.
 */
class Cache_Settings {

	/**
	 * Default cache configuration.
	 *
	 * @var array
	 */
	private static $cache_settings_default = array(
		'transitory' => array(
			'enabled' => true,
			'priority' => 100,
			'max_count' => null,
			'max_extent' => null,
			'max_magnitude' => null,
			'min_memory' => null,
			'ui' => false,
			'locked' => true
		),
		'redis' => array(
			'enabled' => false,
			'priority' => 40,
			'host' => 'localhost',
			'port' => 6379,
			'username' => null,
			'password' => null
		),
		'memcached' => array(
			'enabled' => false,
			'priority' => 30,
			'host' => 'localhost',
			'port' => 11211,
			'username' => null,
			'password' => null
		),
		'file_cache' => array(
			'enabled' => true,
			'priority' => 20,
			'max_files' => null,
			'max_size' => null,
			'min_free_disk_space' => null,
			'gc_interval' => null,
			'gc_time_limit' => null
		),
		'object_cache' => array(
			'enabled' => false,
			'priority' => 10
		),
	);

	/**
	 * @var Cache_Settings
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
	 * @return \com\itthinx\woocommerce\search\engine\Cache_Settings
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new Cache_Settings();
		}
		return self::$instance;
	}

	/**
	 * Provide the default configuration settings.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		$default = self::$cache_settings_default;

		if ( wp_using_ext_object_cache() ) {
			$default['object_cache']['enabled'] = true;
		}

		if ( function_exists( 'is_wpe' ) ) {

			if ( is_wpe() ) {
				$default['file_cache']['enabled'] = false;
				$default['object_cache']['enabled'] = true;
			}
		}
		return $default;
	}

	/**
	 * Whether cache configuration settings are fixed.
	 *
	 * @return boolean
	 */
	public static function is_hardwired() {
		$is_hardwired = false;
		if ( defined( 'WPS_ENGINE' ) && is_array( WPS_ENGINE ) ) {

			if ( array_key_exists( 'caches', WPS_ENGINE ) && is_array( WPS_ENGINE['caches'] ) ) {
				$is_hardwired = true;
			}
		}
		if ( !$is_hardwired ) {
			$is_hardwired = defined( 'WPS_CACHES' ) && is_array( WPS_CACHES );
		}
		return $is_hardwired;
	}

	/**
	 * Provide the name of the hardwire used to determine configuration settings.
	 *
	 * @return string|null name of hardwire or null if not hardwired
	 */
	public static function which_hardwire() {
		$hardwire = null;
		if ( defined( 'WPS_ENGINE' ) && is_array( WPS_ENGINE ) ) {

			if ( array_key_exists( 'caches', WPS_ENGINE ) && is_array( WPS_ENGINE['caches'] ) ) {
				$hardwire = 'WPS_ENGINE';
			}
		}
		if ( $hardwire === null ) {
			if ( defined( 'WPS_CACHES' ) && is_array( WPS_CACHES ) ) {
				$hardwire = 'WPS_CACHES';
			}
		}
		return $hardwire;
	}

	/**
	 * Provide the cache configuration settings.
	 *
	 * Provides the settings determined via the WPS_ENGINE constant if it is set, the WPS_CACHES constant if set, otherwise the stored or the default settings (in that order).
	 *
	 * @return array
	 */
	public function get() {
		$cache_settings_default = self::get_defaults();
		$cache_settings = null;
		if ( defined( 'WPS_ENGINE' ) && is_array( WPS_ENGINE ) ) {

			if ( array_key_exists( 'caches', WPS_ENGINE ) && is_array( WPS_ENGINE['caches'] ) ) {
				$cache_settings = WPS_ENGINE['caches'];
			}
		}
		if ( defined( 'WPS_CACHES' ) && is_array( WPS_CACHES ) ) {
			$cache_settings = WPS_CACHES;
		}

		if ( $cache_settings !== null && is_array( $cache_settings ) ) {

			foreach ( $cache_settings as $cache_id => $data ) {

				if ( $cache_id === 'id' || $cache_id === 'strategy' ) {
					continue;
				}

				if ( !is_array( $data ) ) {
					$cache_settings[$cache_id] = array();
				}
				if ( !isset( $data['enabled'] ) ) {
					$cache_settings[$cache_id]['enabled'] = true;
				}
				if ( !isset( $data['locked'] ) ) {
					if ( isset( $cache_settings_default[$cache_id]['locked'] ) ) {
						$cache_settings[$cache_id]['locked'] = $cache_settings_default[$cache_id]['locked'];
					} else {
						$cache_settings[$cache_id]['locked'] = false;
					}
				}
				if ( !isset( $data['ui'] ) ) {
					if ( isset( $cache_settings_default[$cache_id]['ui'] ) ) {
						$cache_settings[$cache_id]['ui'] = $cache_settings_default[$cache_id]['ui'];
					} else {
						$cache_settings[$cache_id]['ui'] = true;
					}
				}
			}
		} else {

			$cache_settings = $this->settings->get( 'caches', $cache_settings_default );

			foreach ( $cache_settings_default as $cache_id => $settings ) {
				if ( !array_key_exists( $cache_id, $cache_settings ) ) {
					$cache_settings[$cache_id] = $settings;
				}
			}
		}

		if ( !isset( $cache_settings['transitory'] ) && isset( $cache_settings_default['transitory'] ) ) {
			$cache_settings['transitory'] = $cache_settings_default['transitory'];
		}
		return $cache_settings;
	}

	/**
	 * Set the cache configuration settings.
	 *
	 * Has no effect if settings are hardwired.
	 *
	 * @param array|null $settings cache settings or null to reset to defaults
	 */
	public function set( $settings ) {
		$cache_settings_default = self::get_defaults();
		if ( !self::is_hardwired() ) {

			if ( !is_array( $settings ) ) {
				$settings = $cache_settings_default;
			}

			foreach ( $cache_settings_default as $cache_id => $default_settings ) {
				if ( isset( $default_settings['locked'] ) && $default_settings['locked'] ) {
					$settings[$cache_id] = $default_settings;
				}
			}
			$this->settings->set( 'caches', $settings );
		}
	}

	/**
	 * Save the cache configuration settings.
	 *
	 * Has no effect if settings are hardwired.
	 */
	public function save() {
		if ( !self::is_hardwired() ) {
			$this->settings->save();
		}
	}
}
