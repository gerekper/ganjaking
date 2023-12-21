<?php

namespace Happy_Addons\Elementor\Theme_Hooks;

use Happy_Addons\Elementor\Condition_Manager;

defined('ABSPATH') || exit;

/**
 * Force fully replace the header footer.
 */
class Theme_Support {


	/**
	 * Run all the Actions / Filters.
	 */
	function __construct() {

		// add_action('init', [$this, 'init']);
		// // if ($template_ids[0] != null) {
		// // 	add_action('get_header', [$this, 'get_header']);
		// // }

		// // if ($template_ids[1] != null) {
		// // 	add_action('get_footer', [$this, 'get_footer']);
		// // }
		$headers = Condition_Manager::instance()->get_documents_for_location('header');
		$footers = Condition_Manager::instance()->get_documents_for_location('footer');

		if (!empty($headers)) {
			add_action('get_header', [$this, 'get_header']);
			add_filter('show_admin_bar', [$this, 'filter_admin_bar_from_body_open']);
		}
		if (!empty($footers)) {
			add_action('get_footer', [$this, 'get_footer']);
		}
	}

	public function init() {


	}

	public function get_header($name) {
		// require __DIR__ . '/../views/theme-support-header.php';

		require(HAPPY_ADDONS_DIR_PATH . 'templates/builder/theme-support-header.php');

		$templates = [];
		$name = (string) $name;
		if ('' !== $name) {
			$templates[] = "header-{$name}.php";
		}

		$templates[] = 'header.php';

		// Avoid running wp_head hooks again
		remove_all_actions('wp_head');
		ob_start();
		// It cause a `require_once` so, in the get_header it self it will not be required again.
		locate_template($templates, true);
		ob_get_clean();
	}

	public function get_footer($name) {
		// require __DIR__ . '/../views/theme-support-footer.php';

		require(HAPPY_ADDONS_DIR_PATH . 'templates/builder/theme-support-footer.php');

		$templates = [];
		$name = (string) $name;
		if ('' !== $name) {
			$templates[] = "footer-{$name}.php";
		}

		$templates[] = 'footer.php';

		ob_start();
		// It cause a `require_once` so, in the get_header it self it will not be required again.
		locate_template($templates, true);
		ob_get_clean();
	}


	public function filter_admin_bar_from_body_open($show_admin_bar) {
		global $wp_current_filter;

		// A flag to mark if $show_admin_bar is switched to false during this filter,
		// if so, it needed to switch back on the next filter (wp_footer).
		static $switched = false;

		if ($show_admin_bar && in_array('wp_body_open', $wp_current_filter)) {
			$show_admin_bar = false;
			$switched = true;
		} elseif ($switched) {
			$show_admin_bar = true;
		}

		return $show_admin_bar;
	}
}
