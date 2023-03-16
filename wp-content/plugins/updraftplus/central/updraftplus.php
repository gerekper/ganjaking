<?php

if (class_exists('UpdraftPlus_Host')) return;

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) define('UPDRAFTCENTRAL_CLIENT_DIR', dirname(__FILE__));
if (!defined('UPDRAFTCENTRAL_CLIENT_URL')) define('UPDRAFTCENTRAL_CLIENT_URL', plugins_url('', __FILE__));
if (!class_exists('UpdraftCentral_Host')) {
	include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/host.php');
}

/**
 * This class is the basic bridge between UpdraftCentral and UpdraftPlus.
 */
class UpdraftPlus_Host extends UpdraftCentral_Host {

	public $plugin_name = 'updraftplus';

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
		
		add_action('updraftplus_debugtools_dashboard', array($this, 'debugtools_dashboard'), 20);

		$this->maybe_initialize_required_objects();
	}

	/**
	 * Loads the UpdraftCentral_Main instance
	 *
	 * @return void
	 */
	public function load_updraftcentral() {
		$central_path = $this->is_host_dir_set() ? trailingslashit(UPDRAFTPLUS_DIR) : '';

		if (file_exists($central_path.'central/bootstrap.php')) {
			include_once($central_path.'central/bootstrap.php');
		}
	}

	/**
	 * Whether the current user can perform key control AJAX actions
	 *
	 * @return Boolean
	 */
	public function current_user_can_ajax() {
		return UpdraftPlus_Options::user_can_manage();
	}
	
	/**
	 * Below are interface methods' implementations that are required by UpdraftCentral to function properly. Please
	 * see the "interface.php" to check all the required interface methods.
	 */

	/**
	 * Checks whether the plugin's DIR constant is currently define or not
	 *
	 * @return bool
	 */
	public function is_host_dir_set() {
		return defined('UPDRAFTPLUS_DIR') ? true : false;
	}

	/**
	 * Get the host plugin's dir path
	 *
	 * @return string
	 */
	public function get_host_dir() {
		return defined('UPDRAFTPLUS_DIR') ? UPDRAFTPLUS_DIR : dirname(dirname(__FILE__));
	}

	/**
	 * Retrieves the filter used by UpdraftPlus to log errors or certain events
	 *
	 * @return string
	 */
	public function get_logline_filter() {
		return 'updraftplus_logline';
	}

	/**
	 * Checks whether debug mod is set
	 *
	 * @return bool
	 */
	public function get_debug_mode() {
		if (class_exists('UpdraftPlus_Options')) {
			return UpdraftPlus_Options::get_updraft_option('updraft_debug_mode');
		}
		return false;
	}

	/**
	 * Used as a central location (to avoid repetition) to register or de-register hooks into the WP HTTP API
	 *
	 * @param bool $register True to register, false to de-register
	 * @return void
	 */
	public function register_wp_http_option_hooks($register = true) {
		global $updraftplus;

		if ($updraftplus) {
			$updraftplus->register_wp_http_option_hooks($register);
		}
	}

	/**
	 * Retrieves the class name of the host plugin
	 *
	 * @return string|bool
	 */
	public function get_class_name() {
		global $updraftplus;

		if ($updraftplus) {
			return get_class($updraftplus);
		}

		return false;
	}

	/**
	 * Returns the instance of the host plugin
	 *
	 * @return object|bool
	 */
	public function get_instance() {
		global $updraftplus;

		if ($updraftplus) {
			return $updraftplus;
		}

		return false;
	}

	/**
	 * Returns the admin instance of the host plugin
	 *
	 * @return object|bool
	 */
	public function get_admin_instance() {
		global $updraftplus_admin;

		if ($updraftplus_admin) {
			return $updraftplus_admin;
		} else {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/admin.php')) {
				updraft_try_include_file('admin.php', 'include_once');
				$updraftplus_admin = new UpdraftPlus_Admin();
				return $updraftplus_admin;
			}
		}

		return false;
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
	public function log($line, $level = 'notice', $uniq_id = false) {
		global $updraftplus;

		if ($updraftplus) {
			if (is_callable(array($updraftplus, 'log'))) {
				call_user_func(array($updraftplus, 'log'), $line, $level, $uniq_id);
			}
		}
	}

	/**
	 * Returns the current version of the host plugin
	 *
	 * @return string|bool
	 */
	public function get_version() {
		global $updraftplus;

		if ($updraftplus) {
			return $updraftplus->version;
		}

		return false;
	}

	/**
	 * Returns the filesystem class of the host's plugin
	 *
	 * @return class|bool
	 */
	public function get_filesystem_functions() {
		if ($this->has_filesystem_functions()) {
			return UpdraftPlus_Filesystem_Functions;
		}

		return false;
	}

	/**
	 * Checks whether the filesystem class of the host plugin exists
	 *
	 * @return bool
	 */
	public function has_filesystem_functions() {
		return class_exists('UpdraftPlus_Filesystem_Functions');
	}

	/**
	 * Checks whether force debugging is set
	 *
	 * @return bool
	 */
	public function is_force_debug() {
		return (defined('UPDRAFTPLUS_UDRPC_FORCE_DEBUG') && UPDRAFTPLUS_UDRPC_FORCE_DEBUG) ? true : false;
	}

	/**
	 * Initializes required objects (if not yet initialized) for UpdraftCentral usage
	 *
	 * @return void
	 */
	private function maybe_initialize_required_objects() {
		global $updraftplus;

		if (!class_exists('UpdraftPlus')) {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/class-updraftplus.php')) {
				updraft_try_include_file('class-updraftplus.php', 'include_once');
				if (empty($updraftplus) || !is_a($updraftplus, 'UpdraftPlus')) {
					$updraftplus = new UpdraftPlus();
				}
			}
		}

		if (!class_exists('UpdraftPlus_Options')) {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/options.php')) {
				updraft_try_include_file('options.php', 'require_once');
			}
		}

		if (!class_exists('UpdraftPlus_Filesystem_Functions')) {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/includes/class-filesystem-functions.php')) {
				updraft_try_include_file('includes/class-filesystem-functions.php', 'require_once');
			}
		}

		// Load updraftplus translations
		if (defined('UPDRAFTCENTRAL_CLIENT_DIR') && file_exists(UPDRAFTCENTRAL_CLIENT_DIR.'/translations-central.php')) {
			$this->translations = include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/translations-central.php');
		}
	}
}
