<?php
/**
 * class-woocommerce-product-search-admin.php
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
 * @since 4.13.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings API
 *
 * This class is *not* thread-safe.
 */
class Settings {

	/**
	 * The settings instance.
	 *
	 * @var \com\itthinx\woocommerce\search\engine\Settings
	 */
	private static $instance = null;

	/**
	 * The settings storage.
	 *
	 * @var array
	 */
	private $data = null;

	/**
	 * Obtain an instance.
	 *
	 * @return \com\itthinx\woocommerce\search\engine\Settings
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new Settings();
		}
		return self::$instance;
	}

	/**
	 * Create an instance.
	 */
	private function __construct() {
		$data = get_option( 'woocommerce-product-search', null );
		if ( $data === null ) {
			if ( add_option( 'woocommerce-product-search', array(), '', 'no' ) ) {
				$data = get_option( 'woocommerce-product-search' );
			}
		}
		if ( !is_array( $data ) ) {
			$data = array();
		}
		$this->data = $data;
	}

	/**
	 * Get the value for the given key. Returns the $default provided if there is no value stored for the key.
	 *
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed|null
	 */
	public function get( $key, $default = null ) {
		$value = $default;
		if ( is_string( $key ) ) {
			if ( isset( $this->data[$key] ) ) {
				$value = $this->data[$key];
			}
		}
		return $value;
	}

	/**
	 * Set the value for the given key.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $key, $value ) {
		if ( is_string( $key ) ) {
			$this->data[$key] = $value;
		}
	}

	/**
	 * Delete the value for the given key.
	 *
	 * @param string $key
	 */
	public function delete( $key ) {
		if ( is_string( $key ) ) {
			if ( isset( $this->data[$key] ) ) {
				unset( $this->data[$key] );
			}
		}
	}

	/**
	 * Persist the settings.
	 */
	public function save() {
		update_option( 'woocommerce-product-search', $this->data );
	}

	/**
	 * Delete the persisted settings.
	 */
	public function flush() {
		delete_option( 'woocommerce-product-search' );
		$this->data = array();
	}
}
