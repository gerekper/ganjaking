<?php
/**
 * UAEL Retina Image Module.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\RetinaImage;

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
	 * @since 1.17.0
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
	 * @since 1.17.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'uael-retina';
	}

	/**
	 * Get Widgets.
	 *
	 * @since 1.17.0
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'Retina_Image',
		);
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_filter( 'upload_mimes', array( $this, 'uae_svg_mime_types' ) ); // phpcs:ignore WordPressVIPMinimum.Hooks.RestrictedHooks.upload_mimes -- Added this filter to allow upload of SVGs as feature requirement.
	}
	/**
	 * Provide the SVG support for Retina Image widget.
	 *
	 * @param array $mimes which return mime type.
	 *
	 * @since  1.17.0
	 * @return $mimes.
	 */
	public function uae_svg_mime_types( $mimes ) {

		// New allowed mime types.
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
}
