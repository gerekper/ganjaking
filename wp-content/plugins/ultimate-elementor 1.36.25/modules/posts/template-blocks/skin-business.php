<?php
/**
 * UAEL Business Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\TemplateBlocks;

use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Style;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Business
 */
class Skin_Business extends Skin_Style {


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
	 * @since 1.10.1
	 * @access public
	 */
	public function render_separator() {

		$settings = self::$settings;

		do_action( 'uael_single_post/skin_business/before_separator', get_the_ID(), $settings );

		printf( '<div class="uael-post__separator-wrap"><div class="uael-post__separator uael-post__gradient-separator"></div></div>' );

		do_action( 'uael_single_post/skin_business/after_separator', get_the_ID(), $settings );
	}

	/**
	 * Render Author Box HTML.
	 *
	 * @since 1.10.1
	 * @access public
	 */
	public function render_authorbox() {
		$settings = self::$settings;

		$avatar         = get_avatar( get_the_author_meta( 'ID' ) );
		$writtenby_text = $this->get_instance_value( 'writtenby_text' );

		do_action( 'uael_single_post/skin_business/before_authorbox', get_the_ID(), $settings );
		?>
		<div class="uael-post__authorbox-wrapper">
			<div class="uael-post__authorbox">
				<?php if ( false !== $avatar ) { ?>
					<div class="uael-post__authorbox-image">
						<?php echo wp_kses_post( $avatar ); ?>
					</div>
				<?php } ?>
				<div class="uael-post__authorbox-content">
					<?php if ( '' !== $writtenby_text ) { ?>
						<div class="uael-post__authorbox-desc"><?php echo wp_kses_post( $this->get_instance_value( 'writtenby_text' ) ); ?></div>
					<?php } ?>
					<div class="uael-post__authorbox-name">
						<?php
						if ( 'yes' === $this->get_instance_value( 'link_meta' ) ) {
							the_author_posts_link();
						} else {
							the_author();
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
		do_action( 'uael_single_post/skin_business/after_authorbox', get_the_ID(), $settings );
	}

	/**
	 * Get featured image.
	 *
	 * Returns the featured image HTML wrap.
	 *
	 * @since 1.10.1
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
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'img_link' . get_the_ID() ) ); ?>></a>
		<?php } else { ?>
				<?php echo wp_kses_post( $thumbnail_html ); ?>
		<?php } ?>
		</div>
		<?php
		do_action( 'uael_single_post_after_thumbnail', get_the_ID(), $settings );
	}

}

