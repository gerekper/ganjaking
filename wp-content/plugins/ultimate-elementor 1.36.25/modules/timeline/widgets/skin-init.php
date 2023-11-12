<?php
/**
 * UAEL Skin Init.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Timeline\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Init
 */
class Skin_Init {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $skin_instance;

	/**
	 * Initiator
	 *
	 * @param string $style Skin.
	 */
	public static function get_instance( $style ) {

		$skin_class = 'UltimateElementor\\Modules\\Timeline\\Widgets\\Skin_style';

		if ( class_exists( $skin_class ) ) {
			$skin_instance = new $skin_class();
		}
		return $skin_instance;

	}
}
