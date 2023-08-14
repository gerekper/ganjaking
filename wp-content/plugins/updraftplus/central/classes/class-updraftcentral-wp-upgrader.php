<?php

if (!defined('ABSPATH') || !defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

/**
 * The extended class of Plugin_Upgrader that is mostly used for overriding some of the parent methods to short-circuit their native behaviour or to manipulate some data, parameters and/or method arguments
 */
class UpdraftCentral_Plugin_Upgrader extends Plugin_Upgrader {

	/**
	 * Run an upgrade/installation
	 *
	 * @param Array $options {
	 * Array or string of arguments for upgrading/installing a package.
	 *
	 * @type bool $clear_destination Whether to delete any files already in the destination folder. Default false. (since 2.8.0)
	 * }
	 *
	 * @return Array|False|WP_Error The result from self::install_package() on success, otherwise a WP_Error,
	 *                              or false if unable to connect to the filesystem.
	 */
	public function run($options) {
		$options['clear_destination'] = true; // force overwritting the existing one, in case WP < 5.5.0 is in use where "overwrite_package" parameter doesn't exist
		return parent::run($options);
	}
}

/**
 * The extended class of Plugin_Upgrader that is mostly used for overriding some of the parent methods to short-circuit their native behaviour or to manipulate some data, parameters and/or method arguments
 */
class UpdraftCentral_Theme_Upgrader extends Theme_Upgrader {

	/**
	 * Run an upgrade/installation
	 *
	 * @param Array $options {
	 *     Array or string of arguments for upgrading/installing a package.
	 *
	 * @type bool $clear_destination Whether to delete any files already in the destination folder. Default false. (since 2.8.0)
	 * }
	 *
	 * @return Array|False|WP_Error The result from self::install_package() on success, otherwise a WP_Error,
	 *                              or false if unable to connect to the filesystem.
	 */
	public function run($options) {
		$options['clear_destination'] = true; // force overwritting the existing one, in case WP < 5.5.0 is in use where "overwrite_package" parameter doesn't exist
		return parent::run($options);
	}
}
