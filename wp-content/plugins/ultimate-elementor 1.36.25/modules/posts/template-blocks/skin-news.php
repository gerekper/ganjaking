<?php
/**
 * UAEL News Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Posts\TemplateBlocks;

use Elementor\Group_Control_Image_Size;
use UltimateElementor\Modules\Posts\TemplateBlocks\Skin_Style;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_News
 */
class Skin_News extends Skin_Style {


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

		$settings = self::$settings;
		$html_tag = 'span';

		if ( 'none' === $this->get_instance_value( 'image_position' ) ) {
			return;
		}
		$settings['image'] = array(
			'id' => get_post_thumbnail_id(),
		);

		$settings['image_size'] = $this->get_instance_value( 'image_size' );

		$settings['image_custom_dimension'] = $this->get_instance_value( 'image_custom_dimension' );

		$thumbnail_url = Group_Control_Image_Size::get_attachment_image_src( $settings['image']['id'], 'image', $settings );

		$thumbnail_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'image' );

		if ( empty( $thumbnail_url ) ) {
			return;
		}

		do_action( 'uael_single_post_before_thumbnail', get_the_ID(), $settings );

		if ( 'yes' === $this->get_instance_value( 'link_img' ) ) {
			$target = ( 'yes' === $this->get_instance_value( 'link_new_tab' ) ) ? '_blank' : '_self';
			$href   = apply_filters( 'uael_single_post_link', get_the_permalink(), get_the_ID(), $settings );

			$this->add_render_attribute( 'img_link' . get_the_ID(), 'target', $target );
			$this->add_render_attribute( 'img_link' . get_the_ID(), 'href', $href );

			$html_tag = 'a';
		}

		$this->add_render_attribute( 'img_link' . get_the_ID(), 'title', get_the_title() );
		$this->add_render_attribute( 'img_link' . get_the_ID(), 'style', "background-image: url('" . $thumbnail_url . "');" );
		?>
		<div class="uael-post__thumbnail">
			<<?php echo esc_html( $html_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'img_link' . get_the_ID() ) ); ?>>
				<?php
				if ( 'yes' === $this->get_instance_value( 'post_stack_on' ) ) {
					echo wp_kses_post( $thumbnail_html );
				}
				?>
				</<?php echo esc_html( $html_tag ); ?>>
		</div>
		<?php
		do_action( 'uael_single_post_after_thumbnail', get_the_ID(), $settings );
	}

	/**
	 * Get Classes array for wrapper class.
	 *
	 * Returns the array for wrapper class.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_wrapper_classes() {

		$classes = array(
			'uael-post-grid__inner',
			'uael-post__columns-' . $this->get_instance_value( 'slides_to_show' ),
			'uael-post__columns-tablet-' . $this->get_instance_value( 'slides_to_show_tablet' ),
			'uael-post__columns-mobile-' . $this->get_instance_value( 'slides_to_show_mobile' ),
		);

		if ( 'infinite' === $this->get_instance_value( 'pagination' ) ) {
			$classes[] = 'uael-post-infinite-scroll';
		}

		return $classes;
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
			'uael-post-image-' . $this->get_instance_value( 'image_position' ),
			'uael-post-grid',
			'uael-posts',
		);

		$classes[] = 'uael-post_structure-featured';
		$classes[] = 'uael-featured_post_structure-' . $this->get_instance_value( 'featured_post' );
		return $classes;
	}

	/**
	 * Get Pagination.
	 *
	 * Returns the Pagination HTML.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_pagination() {

		$settings = self::$settings;

		if ( 'none' === $this->get_instance_value( 'pagination' ) ) {
			return;
		}

		// Get current page number.
		$paged = self::$query_obj->get_paged();

		$query = self::$query;

		$total_pages = $query->max_num_pages;

		if ( '' !== $this->get_instance_value( 'max_pages' ) ) {
			$total_pages = min( $this->get_instance_value( 'max_pages' ), $total_pages );
		}

		// Return pagination html.
		if ( $total_pages > 1 ) {

			$current_page = $paged;
			if ( ! $current_page ) {
				$current_page = 1;
			}

			$links = paginate_links(
				array(
					'current' => $current_page,
					'total'   => $total_pages,
					'type'    => 'array',
				)
			);
			$class = (
					'infinite' === $this->get_instance_value( 'pagination' )
				) ? 'style="display:none;"' : '';
			?>
			<nav class="uael-grid-pagination" <?php echo wp_kses_post( $class ); ?> role="navigation" aria-label="<?php esc_attr_e( 'Pagination', 'uael' ); ?>">
				<?php echo wp_kses_post( implode( PHP_EOL, $links ) ); ?>
			</nav>
			<?php
		}
	}

	/**
	 * Get body.
	 *
	 * Returns body.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_body() {

		global $wp_query;

		$settings            = self::$settings;
		$query               = self::$query;
		$count               = 0;
		$is_featured         = false;
		$skin                = self::$skin;
		$wrapper             = $this->get_wrapper_classes();
		$outer_wrapper       = $this->get_outer_wrapper_classes();
		$structure           = 'featured';
		$layout              = '';
		$page_id             = $wp_query->get_queried_object_id();
		$filter_default_text = $this->get_instance_value( 'filters_all_text' );

		if ( in_array( $structure, array( 'masonry', 'normal' ), true ) ) {

			if ( 'yes' === $this->get_instance_value( 'show_filters' ) ) {

				$layout = ( 'normal' === $structure ) ? 'fitRows' : 'masonry';
			}
		}
		$offset_top = apply_filters( 'uael_post_offset_top', 30 );
		$this->add_render_attribute( 'wrapper', 'class', $wrapper );
		$this->add_render_attribute( 'outer_wrapper', 'class', $outer_wrapper );
		$this->add_render_attribute( 'outer_wrapper', 'data-query-type', $settings['query_type'] );
		$this->add_render_attribute( 'outer_wrapper', 'data-structure', $structure );
		$this->add_render_attribute( 'outer_wrapper', 'data-layout', $layout );
		$this->add_render_attribute( 'outer_wrapper', 'data-page', $page_id );
		$this->add_render_attribute( 'outer_wrapper', 'data-skin', 'news' );
		$this->add_render_attribute( 'outer_wrapper', 'data-filter-default', $filter_default_text );
		$this->add_render_attribute( 'outer_wrapper', 'data-offset-top', $offset_top );
		?>

		<div <?php echo wp_kses_post( sanitize_text_field( $this->get_render_attribute_string( 'outer_wrapper' ) ) ); ?> <?php echo wp_kses_post( sanitize_text_field( $this->get_slider_attr() ) ); ?>>

			<?php
			if ( 0 === $count ) {

				while ( $query->have_posts() ) {

					$is_featured = true;
					$query->the_post();

					include UAEL_MODULES_DIR . 'posts/templates/content-post-' . $skin . '.php';
					$count++;
					break;
				}
			}
			?>

			<?php do_action( '_uael_posts_before_wrap', $settings ); ?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
			<?php

			while ( $query->have_posts() ) {

				$is_featured = false;

				if ( 0 === $count ) {
					continue;
				}

				$query->the_post();

				include UAEL_MODULES_DIR . 'posts/templates/content-post-' . $skin . '.php';

				$count++;
			}

			wp_reset_postdata();
			?>
				</div>
			<?php do_action( '_uael_posts_after_wrap', $settings ); ?>

		</div>
		<?php
	}

	/**
	 * Render post HTML via AJAX call.
	 *
	 * @param array|string $style_id  The style ID.
	 * @param array|string $widget    Widget object.
	 * @since 1.7.0
	 * @access public
	 */
	public function inner_render( $style_id, $widget ) {

		ob_start();

		check_ajax_referer( 'uael-posts-widget-nonce', 'nonce' );

		$category = ( isset( $_POST['category'] ) ) ? sanitize_text_field( $_POST['category'] ) : '';

		self::$settings  = $widget->get_settings();
		self::$query_obj = new Build_Post_Query( $style_id, self::$settings, $category );
		self::$query_obj->query_posts();
		self::$query = self::$query_obj->get_query();
		self::$skin  = $style_id;
		$query       = self::$query;
		$settings    = self::$settings;
		$is_featured = false;
		$count       = 0;
		$skin        = self::$skin;
		$wrapper     = $this->get_wrapper_classes();

		$this->add_render_attribute( 'wrapper', 'class', $wrapper );

		if ( 0 === $count ) {

			while ( $query->have_posts() ) {

				$is_featured = true;
				$query->the_post();

				include UAEL_MODULES_DIR . 'posts/templates/content-post-' . $skin . '.php';
				$count++;
				break;
			}
		}
		?>

		<?php do_action( '_uael_posts_before_wrap', $settings ); ?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
		<?php

		while ( $query->have_posts() ) {

			$is_featured = false;

			if ( 0 === $count ) {
				continue;
			}

			$query->the_post();

			include UAEL_MODULES_DIR . 'posts/templates/content-post-' . $skin . '.php';

			$count++;
		}

		wp_reset_postdata();
		?>
		</div>
		<?php

		do_action( '_uael_posts_after_wrap', $settings );

		return ob_get_clean();
	}
}

