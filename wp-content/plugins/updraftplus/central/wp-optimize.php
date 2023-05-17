<?php

if (class_exists('WP_Optimize_Host')) return;

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) define('UPDRAFTCENTRAL_CLIENT_DIR', dirname(__FILE__));
if (!defined('UPDRAFTCENTRAL_CLIENT_URL')) define('UPDRAFTCENTRAL_CLIENT_URL', plugins_url('', __FILE__));
if (!class_exists('UpdraftCentral_Host')) {
	include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/host.php');
}

/**
 * This class is the basic bridge between UpdraftCentral and WP_Optimize.
 */
class WP_Optimize_Host extends UpdraftCentral_Host {

	public $plugin_name = 'wp-optimize';

	public $translations = array();

	protected static $_instance = null;

	/**
	 * Creates an instance of this class. Singleton Pattern
	 *
	 * @return object Instance of this class
	 */
	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		parent::__construct();

		// Load wp-optimize translations
		if (defined('UPDRAFTCENTRAL_CLIENT_DIR') && file_exists(UPDRAFTCENTRAL_CLIENT_DIR.'/translations-central.php')) {
			$this->translations = include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/translations-central.php');
		}
	}

	/**
	 * Whether the current user can perform key control AJAX actions
	 *
	 * @return Boolean
	 */
	public function current_user_can_ajax() {
		return current_user_can(WP_Optimize()->capability_required());
	}
	
	/**
	 * Loads the UpdraftCentral_Main instance
	 *
	 * @return void
	 */
	public function load_updraftcentral() {
		$central_path = $this->is_host_dir_set() ? trailingslashit(WPO_PLUGIN_MAIN_PATH) : '';

		if (!empty($central_path) && file_exists($central_path.'central/bootstrap.php')) {
			include_once($central_path.'central/bootstrap.php');
		}
	}

	/**
	 * Checks whether the plugin's DIR constant is currently define or not
	 *
	 * @return bool
	 */
	public function is_host_dir_set() {
		return defined('WPO_PLUGIN_MAIN_PATH') ? true : false;
	}

	/**
	 * Get the host plugin's dir path
	 *
	 * @return string
	 */
	public function get_host_dir() {
		return defined('WPO_PLUGIN_MAIN_PATH') ? WPO_PLUGIN_MAIN_PATH : dirname(dirname(__FILE__));
	}

	/**
	 * Returns the current version of the host plugin
	 *
	 * @return string|bool
	 */
	public function get_version() {
		return defined('WPO_VERSION') ? WPO_VERSION : false;
	}

	/**
	 * Returns the instance of the host plugin
	 *
	 * @return object|bool
	 */
	public function get_instance() {
		global $wp_optimize;

		if ($wp_optimize) {
			return $wp_optimize;
		}

		return false;
	}

	/**
	 * Checks whether debug mod is set
	 *
	 * @return bool
	 */
	public function get_debug_mode() {
		return (defined('WP_OPTIMIZE_DEBUG_OPTIMIZATIONS') && WP_OPTIMIZE_DEBUG_OPTIMIZATIONS);
	}

	/**
	 * Logs the given line
	 *
	 * @param string         $line    The log line
	 * @param string         $level   The log level: notice, warning, error, etc.
	 * @param boolean|string $uniq_id Each of these will only be logged once
	 *
	 * @return void
	 */
	public function log($line, $level = 'notice', $uniq_id = false) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused parameter is present because the the abstract UpdraftCentral_Host class uses 3 arguments.
		global $wp_optimize;

		if ($wp_optimize) {
			if (is_callable(array($wp_optimize, 'log'))) {
				call_user_func(array($wp_optimize, 'log'), $line);
			}
		}
	}

	/**
	 * Developer Note:
	 *
	 * You can add your class methods below if ever you want to extend or modify
	 * the module handlers of UpdraftCentral located at central/modules. Just be
	 * sure to use this class to abstract any functionality that would link to the
	 * wp-optimize plugin.
	 *
	 * N.B. All custom methods added here will then be available from the global
	 * variable $updraftcentral_host_plugin (e.g. $updraftcentral_host_plugin->YOUR_METHOD)
	 */
}
