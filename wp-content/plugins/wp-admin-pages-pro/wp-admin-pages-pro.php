<?php
/**
 * Plugin Name: WP Admin Pages PRO
 * Description: Create admin pages on your network's subsites with custom HTML, CSS and JavaScript!
 * Plugin URI: https://wpadminpagespro.com
 * Text Domain: wu-apc
 * Version: 1.8.1
 * Author: Arindo Duque - NextPress
 * Author URI: https://nextpress.co/
 * Copyright: Arindo Duque, NextPress
 * Network: false
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * WP Admin Pages PRO is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Admin Pages PRO is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Admin Pages PRO. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author   Arindo Duque
 * @category Core
 * @package  WP_Ultimo_APC
 ***** @version 1.8.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
} // end if;

if (!class_exists('WP_Ultimo_APC')) :

	/**
	 * Here starts our plugin.
	 */
	class WP_Ultimo_APC {

		/**
		 * Version of the Plugin
		 *
		 * @var string
		 */
		public $version = '1.8.1';

		/**
		 * Makes sure we are only using one instance of the plugin

		 * @var object WP_Ultimo_APC
		 */
		public static $instance;

		/**
		 * Keeps tracxk if this is an addon or not
		 *
		 * @since 1.4.0
		 * @var boolean
		 */
		public $is_addon = false;

		/**
		 * Returns the instance of WP_Ultimo_APC
         *
		 * @return object A WP_Ultimo_APC instance
		 */
		public static function get_instance() {

			if (null === self::$instance) {
				self::$instance = new self();
			} // end if;

			return self::$instance;

		} // end get_instance;

		/**
		 * Initializes the plugins
		 */
		public function __construct() {

			// Set the plugins_path
			$this->plugins_path = plugin_dir_path(__DIR__);

			$this->file = __FILE__;

			// Load the text domain
			load_plugin_textdomain('wu-apc', false, dirname(plugin_basename(__FILE__)) . '/lang');

			/**
			 * Require Files
			 */
			require_once $this->path('inc/wu-apc-functions.php');
			
			require_once $this->path('inc/models/wu-admin-page.php');
			require_once $this->path('inc/class-wu-admin-pages-list-table.php');
			require_once $this->path('inc/class-wu-admin-pages.php');

			require_once $this->path('inc/class-wu-page-content-source.php');
			require_once $this->path('inc/class-wu-page-content-source-page-builder.php');

			require_once $this->path('inc/class-wu-external-link-support.php');
			require_once $this->path('inc/class-wu-widget-support.php');
			require_once $this->path('inc/class-wu-hide-page-support.php');

			require_once $this->path('inc/class-wu-beaver-builder-support.php');
			require_once $this->path('inc/class-wu-elementor-support.php');
			require_once $this->path('inc/class-wu-brizy-support.php');
			// require_once $this->path('inc/class-wu-gutenberg-support.php');
			require_once $this->path('inc/class-wu-oxygen-builder-support.php');

			// require_once $this->path('inc/class-wu-divi-support.php');
			// check if need dependencies alone
			require_once $this->path('inc/class-wu-standalone-dependencies.php');

			// Run Forest, run!
			$this->hooks();

		}  // end __construct;

		/**
		 * Check if the plugin is network active
		 *
		 * @since 1.4.0
		 * @return boolean
		 */
		public function is_network_active() {

			return is_multisite() && defined('WPAPP_IS_NETWORK') && WPAPP_IS_NETWORK;

		} // end is_network_active;

		/**
		 * Return url to some plugin subdirectory
		 *
		 * @return string Url to passed path
		 */
		public function path($dir) {

			return plugin_dir_path(__FILE__) . '/' . $dir;

		} // end path;

		/**
		 * Return url to some plugin subdirectory
		 *
		 * @return string Url to passed path
		 */
		public function url($dir) {

			return plugin_dir_url(__FILE__) . '/' . $dir;

		} // end url;

		/**
		 * Return full URL relative to some file in assets
		 *
		 * @return string Full URL to path
		 */
		public function get_asset($asset, $assets_dir = 'img') {

			return $this->url("assets/$assets_dir/$asset");

		} // end get_asset;

		/**
		 * Render Views
		 *
		 * @param string $view View to be rendered.
		 * @param Array  $vars Variables to be made available on the view escope, via extract().
		 */
		public function render($view, $vars = false) {

			// Make passed variables available
			if (is_array($vars)) {
				extract($vars); //phpcs:ignore
			} // end if;

			// Load our view
			include $this->path("views/$view.php");

		} // end render;

		/**
		 * Add the hooks we need to make this work
		 */
		public function hooks() {

			add_action('admin_enqueue_scripts', array($this, 'register_scripts'));

		} // end hooks;

		public function register_scripts() {

			wp_enqueue_style('wu-apc-missing-dashicons', $this->get_asset('wp-admin-page-creator-missing-dashicons.min.css', 'css'), false, $this->version);

		} // end register_scripts;

	}  // end class WP_Ultimo_APC;

	/**
	 * Returns the active instance of the plugin
	 *
	 * @return void
	 */
	function WP_Ultimo_APC() {

		return WP_Ultimo_APC::get_instance();

	} // end WP_Ultimo_APC;

	if (!defined('WPAPP_IS_NETWORK')) {

		if (!function_exists('is_plugin_active_for_network')) {
			
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');
			
		} // end if;

		define('WPAPP_IS_NETWORK', is_plugin_active_for_network(plugin_basename(__FILE__)));

	} // end if;

	// Init
	require_once plugin_dir_path(__FILE__) . 'init.php';

endif;
