<?php
/**
 * UAEL Card Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\TemplateBlocks;

use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Style;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Card
 */
class Skin_Card extends Skin_Style {


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

	/**
	 * Render Separator HTML.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_separator() {

		$settings = self::$settings;

		do_action( 'uael_single_post/skin_card/before_separator', get_the_ID(), $settings );

		printf( '<div class="uael-post__separator"></div>' );

		do_action( 'uael_single_post/skin_card/after_separator', get_the_ID(), $settings );
	}

	/**
	 * Get Classes array for outer wrapper class.
	 *
	 * Returns the array for outer wrapper class.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_outer_wrapper_classes() {

		$classes = array(
			'uael-post-grid',
			'uael-posts',
		);

		if ( 'featured' === $this->get_instance_value( 'post_structure' ) ) {

			$classes[] = 'uael-post_structure-' . $this->get_instance_value( 'post_structure' );
			$classes[] = 'uael-featured_post_structure-' . $this->get_instance_value( 'featured_post' );
		}

		return $classes;
	}
}

