<?php
namespace ElementPack\Modules\Carousel\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

use Elementor\Group_Control_Image_Size;
use ElementPack\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Alice extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-alice';
	}

	public function get_title() {
		return __( 'Alice', 'bdthemes-element-pack' );
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();

	    $posts_per_page = $settings['posts_per_page'];
	        
		$this->parent->query_posts($posts_per_page);
		
		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->parent->get_posts_tags();

		$this->parent->render_header("alice");

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();

			$this->render_post();
		}

		$this->parent->render_footer();

		wp_reset_postdata();
	}

	public function render_image($image_id, $size) {

		$settings = $this->parent->get_settings_for_display();

		if ( 'yes' == $settings['thumbnail_show'] ) :
			
			$settings['thumbnail_size'] = [
				'id' => get_post_thumbnail_id(),
			];

			$thumbnail_html        = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail_size' );
			$placeholder_image_src = Utils::get_placeholder_image_src();

			if ( ! $thumbnail_html ) {
				$thumbnail_html = '<img src="' . esc_url( $placeholder_image_src ) . '" alt="' . get_the_title() . '" class="bdt-transition-scale-up bdt-transition-opaque">';
			}

			?>
			<div class="bdt-ep-carousel-thumbnail bdt-overflow-hidden">
				<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-background-cover" title="<?php echo get_the_title(); ?>">
					<?php echo wp_kses_post($thumbnail_html); ?>
				</a>
			</div>
			
		<?php else : ?>
			<div class="bdt-ep-carousel-background"></div>
		
		<?php endif;

	}

	public function render_loop_header() {
		$this->parent->add_render_attribute(
			[
				'carousel-item' => [
					'class' => [
						'bdt-ep-carousel-item',
						'swiper-slide',
						'bdt-transition-toggle',
						'bdt-position-relative',
					],
				],
			]
		);

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'carousel-item' ); ?>>
		<?php
	}

	public function render_category() {
		$settings = $this->parent->get_settings_for_display();

		if( ! $settings['show_alice_category']) {
			return;
		}
		
		?>
		<span class="bdt-ep-carousel-categories">
		    <?php
                echo element_pack_get_category_list( $settings[ 'posts_source' ] );
            ?>
		</span>
		<?php 
	}

	public function render_date() {
		?>
		<span class="bdt-ep-carousel-date bdt-transition-slide-bottom"><?php echo apply_filters( 'the_date', get_the_date( get_option( 'date_format' ) ), '', '' ); ?></span>
		<?php 
	}

	public function render_post() {
		$settings = $this->parent->get_settings_for_display();
		global $post;

		$this->render_loop_header();

		$this->render_image(get_post_thumbnail_id( $post->ID ), $image_size = 'full' );

		?>
		<div class="bdt-custom-overlay bdt-position-cover"></div>
		<div class="bdt-post-grid-desc bdt-position-center">
		<?php

		$this->parent->render_overlay_header();
		$this->render_category();
		$this->parent->render_title();
		$this->render_date();
		$this->parent->render_overlay_footer();
		?>
		</div>
		<?php

		$this->parent->render_post_footer();
	}
}

