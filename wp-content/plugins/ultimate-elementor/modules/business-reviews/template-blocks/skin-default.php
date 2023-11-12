<?php
/**
 * UAEL Default Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\BusinessReviews\TemplateBlocks;

use UltimateElementor\Modules\BusinessReviews\TemplateBlocks\Skin_Style;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Default
 */
class Skin_Default extends Skin_Style {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

