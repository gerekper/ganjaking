<?php
/**
 * UAEL Grid Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\TemplateBlocks;

use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Style;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Classic
 */
class Skin_Classic extends Skin_Style {


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
	 * Get featured image.
	 *
	 * Returns the featured image HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_featured_image_featured_post() {

		$settings = self::$settings;
		if ( 'none' === $this->get_instance_value( 'image_position' ) ) {
			return;
		}
		$settings['featured_image']      = array(
			'id' => get_post_thumbnail_id(),
		);
		$settings['featured_image_size'] = 'full';

		$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'featured_image' );

		if ( empty( $thumbnail_html ) ) {
			return;
		}
		do_action( 'uael_single_post_before_thumbnail', get_the_ID(), $settings );

		if ( 'yes' === $this->get_instance_value( 'link_img' ) ) {
			$href   = apply_filters( 'uael_single_post_link', get_the_permalink(), get_the_ID(), $settings );
			$target = ( 'yes' === $this->get_instance_value( 'link_new_tab' ) ) ? '_blank' : '_self';
			$this->add_render_attribute( 'img_link' . get_the_ID(), 'target', $target );
		}

		$this->add_render_attribute( 'img_link' . get_the_ID(), 'href', $href );
		$this->add_render_attribute( 'img_link' . get_the_ID(), 'title', get_the_title() );
		?>
		<div class="uael-post__thumbnail">
			<?php if ( 'yes' === $this->get_instance_value( 'link_img' ) ) { ?>
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'img_link' . get_the_ID() ) ); ?>><?php echo wp_kses_post( $thumbnail_html ); ?></a>
		<?php } else { ?>
				<?php echo wp_kses_post( $thumbnail_html ); ?>
		<?php } ?>
		</div>
		<?php
		do_action( 'uael_single_post_after_thumbnail', get_the_ID(), $settings );
	}

}

