<?php

/**
 * Plugin base class
 *
 * @package Happy_Addons_Pro
 */

namespace Happy_Addons_Pro;

defined('ABSPATH') || die();

class Base {

	private static $instance = null;

	public static $appsero = null;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	public function init() {
		$this->init_appsero();

		$this->include_files();

		add_action('init', [$this, 'i18n']);
		add_action('init', [$this, 'include_on_init']);

		// Register custom category
		add_action('elementor/elements/categories_registered', [$this, 'add_category']);

		// Register custom controls
		add_action('elementor/controls/register', [$this, 'register_controls']);
	}

	public function i18n() {
		load_plugin_textdomain('happy-addons-pro');
	}

	/**
	 * Initialize the tracker
	 *
	 * @return void
	 */
	protected function init_appsero() {
		if (!class_exists('\Happy_Addons\Appsero\Client')) {
			include_once HAPPY_ADDONS_DIR_PATH . 'vendor/appsero/src/Client.php';
		}

		self::$appsero = new \Happy_Addons\Appsero\Client(
			'3cb003ad-7dd3-4e34-9c36-90a2e84b537a',
			'Happy Elementor Addons Pro',
			HAPPY_ADDONS_PRO__FILE__
		);

		// Active automatic updater
		self::$appsero->updater();

		// Active license page and checker
		$args = [
			'type'       => 'submenu',
			'menu_title' => esc_html(self::$appsero->license()->is_valid() ? __('License', 'happy-addons-pro') : __('Activate License', 'happy-addons-pro')),
			'page_title' => 'License - Happy Elementor Addons',
			'menu_slug'  => 'happy-addons-license',
			'parent_slug' => 'happy-addons',
		];

		self::$appsero->license()->add_settings_page($args);
	}

	public function include_on_init() {
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'inc/functions-extensions.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'classes/extensions-manager.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'classes/condition-manager.php' );
	}

	public function include_files() {
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'inc/functions.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'inc/functions-template.php');

		if (is_admin()) {
			include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/dashboard.php');
		}

		if (is_user_logged_in()) {
			include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/marvin.php');
			include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/ixporter.php');

			//check if preset is disable
			if (function_exists('ha_get_inactive_features') && !in_array('happy-preset', ha_get_inactive_features())) {
				include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/designs-manager.php');
			} elseif (!function_exists('ha_get_inactive_features')) {
				include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/designs-manager.php');
			}
		}

		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/widgets-manager.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/credentials-manager.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/assets-manager.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/live-copy.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/wpml-manager.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/lazy-query-manager.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/breadcrumbs.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'traits/smart-post-list.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'traits/post-grid.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'traits/post-grid-new.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/ajax-handler.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'classes/template-query-manager.php');
	}

	/**
	 * Add pro category
	 */
	public function add_category() {
		ha_elementor()->elements_manager->add_category(
			'happy_addons_pro_category',
			[
				'title' => __('Happy Addons Pro', 'happy-addons-pro'),
				'icon' => 'fa fa-smile-o',
			]
		);
	}

	/**
	 * Register custom controls
	 *
	 * Include custom controls file and register them
	 *
	 * @access public
	 */
	public function register_controls($controls_manager = null) {
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'controls/mask-image.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'controls/image-selector.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'controls/indicator-selector.php');
		include_once(HAPPY_ADDONS_PRO_DIR_PATH . 'controls/lazy-select.php');

		$mask_image = __NAMESPACE__ . '\Controls\Group_Control_Mask_Image';
		$image_selector = __NAMESPACE__ . '\Controls\Image_Selector';
		$indicator_selector = __NAMESPACE__ . '\Controls\Indicator_Selector';
		$lazy_select = __NAMESPACE__ . '\Controls\Lazy_Select';

		ha_elementor()->controls_manager->add_group_control($mask_image::get_type(), new $mask_image());

		$controls_manager->register(new $image_selector());
		$controls_manager->register(new $indicator_selector());
		$controls_manager->register(new $lazy_select());
	}
}
