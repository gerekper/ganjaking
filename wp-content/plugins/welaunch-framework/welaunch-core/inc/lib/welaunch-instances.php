<?php
/**
 * weLaunch_Instances Functions
 *
 * @package     weLaunch_Framework
 * @subpackage  Core
 * @deprecated Maintained for backward compatibility with v3.
 */

/**
 * Retreive an instance of weLaunchFramework
 *
 * @depreciated
 *
 * @param  string $opt_name the defined opt_name as passed in $args.
 *
 * @return object                weLaunchFramework
 */
function get_welaunch_instance( $opt_name ) {
	_deprecated_function( __FUNCTION__, '4.0', 'weLaunch::instance($opt_name)' );

	return weLaunch::instance( $opt_name );
}

/**
 * Retreive all instances of weLaunchFramework
 * as an associative array.
 *
 * @depreciated
 * @return array        format ['opt_name' => $weLaunchFramework]
 */
function get_all_welaunch_instances() {
	_deprecated_function( __FUNCTION__, '4.0', 'weLaunch::all_instances()' );

	return weLaunch::all_instances();
}
