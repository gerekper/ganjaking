<?php

if (!defined('ABSPATH')) die('No direct access.');

if (!defined('UPDRAFTCENTRAL_SET_TIME_LIMIT')) define('UPDRAFTCENTRAL_SET_TIME_LIMIT', 900);

// We bypass the class declaration if the class already existed. This usually happens if two or more
// plugins integrated the same `UpdraftCentral` client folder.
if (!class_exists('UpdraftCentral_Factory')) :

/**
 * Returns an instance of the host plugin class where the UpdraftCentral "central" folder is being
 * integrated.
 */
class UpdraftCentral_Factory {

	/**
	 * Creates a host plugin instance
	 *
	 * @return object|null
	 */
	public static function create_host() {

		// All other plugins that wish to integrate the "central" libraries into their
		// codebase must use this filter (see WP-Optimize plugin as an example).
		$hosts = apply_filters('updraftcentral_host_plugins', array());

		// If $hosts is empty then we return null
		if (empty($hosts)) return null;

		// N.B. If multiple host plugins (e.g. updraftplus, wp-optimize, etc.) are currently
		// active then we only select one to handle all incoming UpdraftCentral requests for
		// this website in order to avoid conflicts and confusion especially when tracing or
		// debugging issues.
		//
		// You will know which plugin is currently serving and handling the request by checking
		// the "get_plugin_name" method of the global variable "$updraftcentral_host_plugin"
		// (e.g. $updraftcentral_host_plugin->get_plugin_name())
		//
		// N.B. You can add additional host plugins here. Just make sure that you will create
		// a host class for that particular plugin (see central/wp-optimize.php as an example).
		$mapped_classes = array(
			'updraftplus' => 'UpdraftPlus_Host',
			'wp-optimize' => 'WP_Optimize_Host',
		);

		$path = $host_class = '';
		foreach ($hosts as $plugin) {
			// Make sure that we have a registered host class with a valid file that exist
			$host_file = dirname(__FILE__).'/'.$plugin.'.php';
			if (isset($mapped_classes[$plugin]) && file_exists($host_file)) {
				$path = $host_file;
				$host_class = $mapped_classes[$plugin];
				break;
			}
		}

		// The host file was not found under this plugin thus, we let the other plugins
		// create or build the host plugin (global) variable instead.
		if (empty($path)) return null;

		if (!class_exists($host_class)) include_once($path);

		// Re-check host class once again just to make sure that we have the desired
		// class loaded before calling its instance method
		if (class_exists($host_class)) {
			return call_user_func(array($host_class, 'instance'));
		}
		
		return null;
	}
}

endif;

global $updraftcentral_host_plugin;
$updraftcentral_host_plugin = UpdraftCentral_Factory::create_host();

if ($updraftcentral_host_plugin) {
	$updraftcentral_host_plugin->load_updraftcentral();
}
