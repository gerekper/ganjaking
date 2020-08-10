<?php
/**
 * Add-ons and events
 *
 */

define( 'CRB_ADDON_PAGE', 'cerber-addons' );
define( 'CRB_ADDON_SIGN', '_addon' );
define( 'CRB_BOOT_ADDONS', 'boot_cerber_addons' );

// Add-ons API -----------------------------------------------------------------

/**
 * @param string $file Add-on main PHP file to be invoked in if event occurs
 * @param string $addon_id Add-on slug
 * @param string $name Name of the add-on
 * @param string $requires Version of WP Cerber required
 * @param null|array $settings Configuration of the add-on setting fields
 * @param null|callable $cb
 *
 * @return bool
 */
function cerber_register_addon( $file, $addon_id, $name, $requires, $settings = null, $cb = null ) {

	return CRB_Addons::register_addon( $file, $addon_id, $name, $requires, $settings, $cb );
}

/**
 * @param string $event
 * @param callable $callback
 * @param string $addon_id
 *
 * @return bool
 */
function cerber_add_handler( $event, $callback, $addon_id = null ) {

	return CRB_Events::add_handler( $event, $callback, $addon_id );
}


// END of Add-ons API ----------------------------------------------------------

cerber_add_handler( 'update_settings', function ( $data ) {
	CRB_Addons::settings_saved( $data['group'], $data['new_values'] );
	crb_x_update_add_on_list();
} );

/**
 * Creates and updates a list of files to be booted
 *
 */
add_action( 'activated_plugin', 'crb_update_add_on_list' );
function crb_update_add_on_list() {
	if ( ! $addons = CRB_Addons::get_all() ) {
		cerber_update_set( CRB_BOOT_ADDONS, array() );

		return;
	}

	$boot = array();

	foreach ( CRB_Events::get_addons() as $event => $listeners ) {
		$to_boot        = array_intersect_key( $addons, array_flip( $listeners ) );
		$boot[ $event ] = array_column( $to_boot, 'file' );
	}

	cerber_update_set( CRB_BOOT_ADDONS, $boot );
}

add_action( 'deactivated_plugin', 'crb_x_update_add_on_list' );
/**
 * Is used when it's not possible to correctly refresh the list during the current request
 *
 */
function crb_x_update_add_on_list() {
	define( 'CRB_POSTPONE_REFRESH', 1 );
	cerber_update_set( 'refresh_add_on_list', 1, null, false );
}

register_shutdown_function( function () {
	if ( cerber_get_set( 'refresh_add_on_list', null, false ) &&
	     ! defined( 'CRB_POSTPONE_REFRESH' ) ) {
		crb_update_add_on_list();
		cerber_update_set( 'refresh_add_on_list', 0, null, false );
	}
} );

final class CRB_Events {
	private static $handlers = array();
	private static $addons = array();
	private static $addon_files = null;
	private static $loaded = array();
	/**
	 * Register a handler for an event
	 *
	 * @param string $event
	 * @param callable $callback
	 * @param string $addon_id
	 *
	 * @return bool
	 */
	static function add_handler( $event, $callback, $addon_id = null ) {

		if ( $addon_id && ! CRB_Addons::is_registered( $addon_id ) ) {
			return false;
		}

		self::$handlers[ $event ][] = $callback;

		if ( $addon_id ) {
			self::$addons[ $event ][] = $addon_id;
		}

		return true;
	}

	static function event_handler( $event, $data ) {

		if ( ! isset( self::$addon_files ) ) {
			if ( ! self::$addon_files = cerber_get_set( CRB_BOOT_ADDONS ) ) {
				self::$addon_files = false;
			}
		}

		if ( ! empty( self::$addon_files[ $event ] )
		     && ! isset( self::$loaded[ $event ] ) ) {
			ob_start();

			self::$loaded[ $event ] = 1; //Avoid processing files for repetitive events

			foreach ( self::$addon_files[ $event ] as $addon_file ) {
				if ( @file_exists( $addon_file ) ) {
					include_once $addon_file;
				}
			}

			ob_end_clean();
		}

		if ( ! isset( self::$handlers[ $event ] ) ) {
			return;
		}

		foreach ( self::$handlers[ $event ] as $handler ) {
			if ( is_callable( $handler ) ) {
				call_user_func( $handler, $data );
			}
		}
	}

	static function get_addons( $event = null ) {
		if ( ! $event ) {
			return self::$addons;
		}

		return crb_array_get( self::$addons, $event, array() );
	}

}

final class CRB_Addons {
	private static $addons = array();
	private static $first = '';

	/**
	 * @param string $file Add-on main PHP file to be invoked in if event occurs
	 * @param string $addon_id Add-on slug
	 * @param string $name Name of the add-on
	 * @param string $requires Version of WP Cerber required
	 * @param null|array $settings Configuration of the add-on setting fields
	 * @param null|callable $cb
	 *
	 * @return bool
	 */
	static function register_addon( $file, $addon_id, $name, $requires, $settings = null, $cb = null ) {
		if ( isset( self::$addons[ $addon_id ] ) ) {
			return false;
		}

		if ( ! self::$first ) {
			self::$first = $addon_id;
		}

		self::$addons[ $addon_id ] = array(
			'file'     => $file,
			'name'     => $name,
			'settings' => $settings,
			'callback' => $cb
		);

		return true;
	}

	/**
	 * @return array
	 */
	static function get_all() {
		return self::$addons;
	}

	static function settings_saved( $group, $new_values ) {
		if ( ! self::$addons ) {
			return;
		}

		// Detect an add-on from the settings form group name
		$len = strlen( CRB_ADDON_SIGN );
		if ( substr( $group, 0 - $len ) !== CRB_ADDON_SIGN ) {
			return;
		}
		$len      = strlen( $group ) - $len;
		$addon_id = substr( $group, 0, $len );

		// If this add-on has a callback, call it
		if ( ! empty( self::$addons[ $addon_id ]['callback'] ) ) {
			call_user_func( self::$addons[ $addon_id ]['callback'], $new_values );

			return;
		}

	}

	/**
	 * @param string $addon
	 *
	 * @return bool
	 */
	static function is_registered( $addon ) {
		return isset( self::$addons[ $addon ] );
	}

	/**
	 * Is any add-on registered?
	 *
	 * @return bool
	 */
	static function none() {
		return empty( self::$first );
	}

	/**
	 * @return string
	 */
	static function get_first() {
		return self::$first;
	}

	/**
	 * Load code of all active add-ons
	 *
	 */
	static function load_active() {
		if ( ! $list = cerber_get_set( CRB_BOOT_ADDONS ) ) {
			return;
		}

		foreach ( $list as $files ) {
			foreach ( $files as $file ) {
				if ( @file_exists( $file ) ) {
					include_once $file;
				}
			}
		}
	}
}

function crb_event_handler( $event, $data ) {

	CRB_Events::event_handler( $event, $data );

	return;
}

/////// Admin area pages and settings routines

function crb_addon_admin_page( $page ) {

	if ( $page != CRB_ADDON_PAGE ) {
		return false;
	}

	$addons = CRB_Addons::get_all();

	if ( ! $addons ) {
		return false;
	}

	$config = array(
		'title'    => __( 'Add-ons', 'wp-cerber' ),
		'callback' => 'cerber_show_settings_form',
	);

	foreach ( $addons as $id => $addon ) {
		$config['tabs'][ $id . CRB_ADDON_SIGN ] = array( 'bx-cog', htmlspecialchars( $addon['name'] ) );
	}

	return $config;
}

function crb_addon_settings_config( $args ) {
	//global $cerber_addons;

	$addons = CRB_Addons::get_all();

	if ( ! $addons ) {
		return false;
	}

	//if ( $args['screen_id'] == 'add_on_settings' ) {
	/*
	if ( $args['screen_id'] == CRB_ADDON_PAGE ) {
		$addon_id = $cerber_first_addon;
	}
	else {
		$addon_id = $args['screen_id'];
	}


	echo '======================='.$args['screen_id'].' ++ '.$addon_id;

*/
	$settings = array();

	//$settings['screens'][CRB_ADDON_PAGE] = array_keys( $cerber_addons[ $addon_id ]['settings'] );
	//$settings['sections'] = $cerber_addons[ $addon_id ] = $cerber_addons[ $addon_id ]['settings'];

	/*foreach ( $addons as $id => $addon ) {

	}*/

	$settings = array( 'screens' => array(), 'sections' => array() );

	foreach ( $addons as $id => $addon ) {
		$settings['screens'][ $id . CRB_ADDON_SIGN ] = array_keys( $addon['settings'] );
		$settings['sections'] = array_merge( $settings['sections'], $addon['settings'] );
	}

	return $settings;
}

function crb_addon_settings_mapper( &$map ) {

	if ( CRB_Addons::none() ) {
		return;
	}

	$map[ CRB_ADDON_PAGE ] = CRB_Addons::get_first() . CRB_ADDON_SIGN;

	//$map[ CRB_ADDON_PAGE ] = 'add_on_settings';
}
