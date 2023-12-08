<?php

// namespace ElementPack\Includes\Shortcodes;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
class Element_Pack_Shortcode_Loader {

	/**
	 * Class instance.
	 *
	 * @since  5.4.2
	 * @access private
	 * @var    null      The single class instance.
	 */
	private static $instance;

	/**
	 * Get class instance.
	 *
	 * @return Element_Pack_Shortcode_Loader
	 * @since  5.4.2
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 * @since   5.4.2
	 */
	public function __construct() {

		$this->load_dependencies();

		$this->register_actions();

		self::$instance = $this;
	}

	private function register_actions() {
		/**
		 * Register available shortcodes.
		 */
		add_action('init', array('Element_Pack_Shortcodes', 'register'));
	}

	private function load_dependencies() {

		require_once BDTEP_INC_PATH . 'shortcodes/class-element-pack-shortcodes.php';

		require_once BDTEP_INC_PATH . 'shortcodes/shortcode-functions.php';

		// All shortcode elements added here
		require_once BDTEP_INC_PATH . 'shortcodes/elements/alert.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/animated-link.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/author-avatar.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/author-name.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/badge.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/button.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/breadcrumbs.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/current-date.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/current-user.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/clipboard.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/countdown.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/label.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/lightbox.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/notification.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/page-title.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/page-url.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/post-date.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/rating.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/site-title.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/site-url.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/tag-list.php';
		require_once BDTEP_INC_PATH . 'shortcodes/elements/tooltip.php';
	}
}

new Element_Pack_Shortcode_Loader();
