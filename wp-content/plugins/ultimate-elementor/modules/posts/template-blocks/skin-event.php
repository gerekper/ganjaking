<?php
/**
 * UAEL Event Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\TemplateBlocks;

use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Style;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Event
 */
class Skin_Event extends Skin_Style {


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
	public function render_featured_image() {

		$settings          = self::$settings;
		$settings['image'] = array(
			'id' => get_post_thumbnail_id(),
		);

		$settings['image_size'] = $this->get_instance_value( 'image_size' );

		$settings['image_custom_dimension'] = $this->get_instance_value( 'image_custom_dimension' );

		$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings );

		if ( 'none' === $this->get_instance_value( 'image_position' ) ) {
			$thumbnail_html = '';
		}

		do_action( 'uael_single_post_before_thumbnail', get_the_ID(), $settings );

		if ( 'yes' === $this->get_instance_value( 'link_img' ) ) {
			$href   = apply_filters( 'uael_single_post_link', get_the_permalink(), get_the_ID(), $settings );
			$target = ( 'yes' === $this->get_instance_value( 'link_new_tab' ) ) ? '_blank' : '_self';
			$this->add_render_attribute( 'img_link' . get_the_ID(), 'target', $target );
			$this->add_render_attribute( 'img_link' . get_the_ID(), 'href', $href );
		}

		$this->add_render_attribute( 'img_link' . get_the_ID(), 'title', get_the_title() );
		?>
		<div class="uael-post__thumbnail <?php echo wp_kses_post( $this->get_thumbnail_no_image_class() ); ?>">
			<?php if ( 'yes' === $this->get_instance_value( 'link_img' ) ) { ?>
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'img_link' . get_the_ID() ) ); ?>><?php echo wp_kses_post( $thumbnail_html ); ?></a>
		<?php } else { ?>
				<?php echo wp_kses_post( $thumbnail_html ); ?>
		<?php } ?>
			<div class="uael-post__datebox <?php echo wp_kses_post( $this->get_no_image_class() ); ?>">
				<div class="uael-post__date-wrap">
					<?php
					$date  = "<span class='uael-post__date-month'>";
					$date .= date_i18n( 'M', strtotime( get_the_date() ) );
					$date .= '</span>';
					$date .= "<span class='uael-post__date-day'>";
					$date .= date_i18n( 'd', strtotime( get_the_date() ) );
					$date .= '</span>';
					?>
					<?php echo wp_kses_post( apply_filters( 'uael_post_event_date', $date, get_the_ID(), get_option( 'date_format' ), '', '' ) ); ?>
				</div>				
			</div>
		</div>
		<?php
		do_action( 'uael_single_post_after_thumbnail', get_the_ID(), $settings );
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

