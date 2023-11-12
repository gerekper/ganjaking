<?php
/**
 * UAEL WpfStyler Module.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\WpfStyler;

use UltimateElementor\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module.
 */
class Module extends Module_Base {

	/**
	 * Module should load or not.
	 *
	 * @since 1.15.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		if ( class_exists( 'WPForms_Pro' ) || class_exists( 'WPForms_Lite' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Get Module Name.
	 *
	 * @since 1.15.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'uael-wpf-styler';
	}

	/**
	 * Get Widgets.
	 *
	 * @since 1.15.0
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'WpfStyler',
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		parent::__construct();
	}
}
