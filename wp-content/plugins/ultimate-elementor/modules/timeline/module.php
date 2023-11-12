<?php
/**
 * UAEL Timeline Module.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Timeline;

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
	 * @since 1.5.2
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * Get Module Name.
	 *
	 * @since 1.5.2
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'uael-timeline';
	}

	/**
	 * Get Widgets.
	 *
	 * @since 1.5.2
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'Timeline',
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		parent::__construct();
	}
}
