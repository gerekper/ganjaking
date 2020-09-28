<?php

namespace GroovyMenu;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class StyleStorage
 */
class StyleStorage {
	/**
	 * Self object instance
	 *
	 * @var null|object
	 */
	private static $instance = null;


	/**
	 * Disable storage
	 *
	 * @var array
	 */
	private $disable_storage_flag = false;

	/**
	 * Storage of preset options (configs)
	 *
	 * @var array
	 */
	private $preset_storage_config = array();

	/**
	 * Storage of preset settings
	 *
	 * @var array
	 */
	private $preset_storage = array();

	/**
	 * Storage of preset settings
	 *
	 * @var array
	 */
	private $preset_storage_serialize = array();

	/**
	 * Storage of global options (configs)
	 *
	 * @var array
	 */
	private $global_storage_config = array();

	/**
	 * Storage of global settings
	 *
	 * @var array
	 */
	private $global_storage = array();

	/**
	 * Singleton self instance
	 *
	 * @return StyleStorage
	 */
	public static function getInstance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __clone() {
	}

	private function __construct() {
	}

	public function set_disable_storage() {
		$this->disable_storage_flag = true;
	}

	public function set_enable_storage() {
		$this->disable_storage_flag = false;
	}


	public function set_preset_config( $configs ) {
		if ( is_array( $configs ) ) {
			$this->preset_storage_config = $configs;
		}
	}

	public function get_preset_config() {
		if ( ! empty( $this->preset_storage_config ) ) {
			return $this->preset_storage_config;
		}

		return array();
	}

	public function set_global_config( $configs ) {
		if ( is_array( $configs ) ) {
			$this->global_storage_config = $configs;
		}
	}

	public function get_global_config() {
		if ( ! empty( $this->global_storage_config ) ) {
			return $this->global_storage_config;
		}

		return array();
	}

	public function get_preset_settings( $preset_id ) {
		$return_value = null;

		if ( $this->disable_storage_flag ) {
			return $return_value;
		}

		$preset_id = intval( $preset_id );

		if ( isset( $this->preset_storage[ $preset_id ] ) ) {
			$return_value = $this->preset_storage[ $preset_id ];
		}

		return $return_value;
	}

	public function set_preset_settings( $preset_id, $settings ) {
		$preset_id = intval( $preset_id );

		$this->preset_storage[ $preset_id ] = $settings;
	}

	public function get_preset_settings_serialized( $preset_id, $get_all = false, $camelize = true, $get_global = true ) {
		$return_value = null;

		if ( $this->disable_storage_flag ) {
			return $return_value;
		}

		$preset_id = intval( $preset_id );

		$key  = $get_all ? '1' : '0';
		$key .= $camelize ? '1' : '0';
		$key .= $get_global ? '1' : '0';

		if ( isset( $this->preset_storage_serialize[ $key ] ) && isset( $this->preset_storage_serialize[ $key ][ $preset_id ] ) ) {
			$return_value = $this->preset_storage_serialize[ $key ][ $preset_id ];
		}

		return $return_value;
	}

	public function set_preset_settings_serialized( $preset_id, $settings, $get_all = false, $camelize = true, $get_global = true ) {
		$preset_id = intval( $preset_id );

		$key  = $get_all ? '1' : '0';
		$key .= $camelize ? '1' : '0';
		$key .= $get_global ? '1' : '0';

		$this->preset_storage_serialize[ $key ][ $preset_id ] = $settings;
	}

	public function get_global_settings() {
		return $this->global_storage;
	}

	public function set_global_settings( $settings ) {
		$this->global_storage = $settings;
	}

	public function get_stored_preset_list() {
		$return_value = array();

		if ( ! empty( $this->preset_storage ) ) {
			foreach ( $this->preset_storage as $index => $item ) {
				$return_value[] = $index;
			}
		}

		return $return_value;
	}

	public function remove_preset_settings() {
		$this->preset_storage = array();
	}

	public function remove_global_settings() {
		$this->global_storage = array();
	}

	public function get_all_preset_settings() {
		return $this->preset_storage;
	}


}
