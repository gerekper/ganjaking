<?php

/**
 * MegaMenu Support for Navigation Widget
 *
 * @package Happy_Addons_Pro
 */

namespace Happy_Addons_Pro\Extension;

use Happy_Addons_Pro\Cpt;

class Mega_Menu {
	public $dir;
	public $url;
	public $iconManager;

	public static $menuitem_settings_key = 'ha_menuitem_settings';
	public static $megamenu_settings_key = 'megamenu_settings';

	public function __construct() {

		// get current directory path
		//$this->dir = dirname(__FILE__) . '/';

		// enqueue scripts
		add_action('extension_admin_scripts', [$this, 'enqueue_styles']);
		add_action('extension_admin_scripts', [$this, 'enqueue_scripts']);

		// include all necessary files
		$this->include_files();

		new Options();
	}

	public static function init() {
		new Mega_Menu;
	}

	public function include_files() {
		include $this->dir . 'mega-menu/cpt.php';
		include $this->dir . 'mega-menu/options.php';
		include $this->dir . 'mega-menu/walker-nav-menu.php';

		Cpt::flush_rewrites();
	}

	public function enqueue_styles() {
		$screen = get_current_screen();

		if ($screen->base == 'nav-menus') {
			wp_enqueue_style('wp-color-picker');

			wp_enqueue_style(
				'aesthetic-icon-picker',
				HAPPY_ADDONS_PRO_ASSETS . 'vendor/aesthetic-icon-picker/css/aesthetic-icon-picker.min.css',
				false,
				HAPPY_ADDONS_PRO_VERSION
			);

			wp_enqueue_style(
				'aesthetic-icon-picker-fonts',
				HAPPY_ADDONS_PRO_ASSETS . 'vendor/aesthetic-icon-picker/fonts/all.css',
				false,
				HAPPY_ADDONS_PRO_VERSION
			);

			wp_enqueue_style(
				'jquery-modal',
				HAPPY_ADDONS_PRO_ASSETS . 'vendor/jquery-modal/jquery.modal.min.css',
				false,
				'0.9.1'
			);

			wp_enqueue_style(
				'ha-menu-admin-style',
				HAPPY_ADDONS_PRO_ASSETS . 'admin/css/extension-megamenu.css',
				false,
				HAPPY_ADDONS_PRO_VERSION
			);

			wp_enqueue_style(
				'happy-icons',
				HAPPY_ADDONS_ASSETS . 'fonts/style.min.css',
				false,
				HAPPY_ADDONS_ASSETS
			);
		}
	}

	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ($screen->base == 'nav-menus') {
			wp_enqueue_script(
				'aesthetic-icon-picker',
				HAPPY_ADDONS_PRO_ASSETS . 'vendor/aesthetic-icon-picker/js/aesthetic-icon-picker.js',
				array('jquery'),
				HAPPY_ADDONS_PRO_VERSION,
				true
			);

			wp_enqueue_script(
				'jquery-modal-script',
				HAPPY_ADDONS_PRO_ASSETS . 'vendor/jquery-modal/jquery.modal.min.js',
				array('jquery'),
				'0.9.1',
				true
			);

			wp_enqueue_script(
				'ha-menu-admin-script',
				HAPPY_ADDONS_PRO_ASSETS . 'admin/js/extension-megamenu.min.js',
				array('jquery', 'wp-color-picker'),
				HAPPY_ADDONS_PRO_VERSION,
				true
			);

			wp_localize_script(
				'ha-menu-admin-script',
				'happyMenu',
				array(
					'items' => $this->happyMenuItems(),
				)
			);
		}
	}

	function happyMenuItems() {
		$args = array(
			'post_type'   => 'nav_menu_item',
			'post_status' => 'publish',
			'nopaging'    => true,
			'fields'      => 'ids'
		);
		$items = new \WP_Query($args);

		$menuItems = [];

		foreach ($items->posts as $happyItem) {
			$data = get_post_meta($happyItem, \Happy_Addons_Pro\Extension\Mega_Menu::$menuitem_settings_key, true);
			$data = (array) json_decode($data);

			if (isset($data['menu_enable']) && $data['menu_enable'] == 1) {
				$menuItems[] = "#menu-item-" . $happyItem;
			}
		}

		return $menuItems;
	}
}

Mega_Menu::init();
