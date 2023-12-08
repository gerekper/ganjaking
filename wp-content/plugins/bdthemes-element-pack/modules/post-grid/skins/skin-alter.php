<?php
namespace ElementPack\Modules\PostGrid\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Alter extends Elementor_Skin_Base {

	public function get_id() {
		return 'bdt-alter';
	}

	public function get_title() {
		return __( 'Alter', 'bdthemes-element-pack' );
	}

	public function render_category() {
		$settings = $this->parent->get_settings();
		if ( ! $this->parent->get_settings( 'show_category' ) ) { return; }
		?>
		<div class="bdt-post-grid-category bdt-position-z-index bdt-position-small bdt-position-top-right">
			<?php
				echo element_pack_get_category_list( $this->parent->get_settings( 'posts_source' ) );
			?>
		</div>
		<?php
	}

	public function render_post_grid_layout_alter( $post_id, $image_size, $excerpt_length, $bdt_post_class ) {
		$settings = $this->parent->get_settings();
		global $post;

		if ('yes' == $settings['global_link']) {

		$this->parent->add_render_attribute( 'grid-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true );
		}

		$this->parent->add_render_attribute('grid-item', 'class', 'bdt-post-grid-item bdt-transition-toggle bdt-position-relative bdt-grid bdt-grid-collapse', true);
		$this->parent->add_render_attribute('grid-item', 'data-bdt-grid', '', true);

		?>
			<div <?php echo $this->parent->get_render_attribute_string( 'grid-item' ); ?>>

				<div class="bdt-position-relative bdt-width-auto@s bdt-pg-alter-image">
					<?php $this->parent->render_image(get_post_thumbnail_id( $post_id ), $image_size ); ?>
					<?php $this->render_category(); ?>
				</div>

		  		<div class="bdt-post-grid-desc bdt-width-expand@s bdt-padding<?php echo esc_attr( $bdt_post_class ); ?>">
					<?php $this->parent->render_title(); ?>

					<?php $this->parent->render_excerpt($excerpt_length); ?>
					<?php $this->parent->render_readmore(); ?>
					
					<?php if ($settings['show_author'] or $settings['show_date'] or $settings['show_comments']) : ?>
						<div class="bdt-post-grid-meta bdt-subnav bdt-flex bdt-flex-middle bdt-margin-small-top bdt-padding-remove-horizontal">
							<?php $this->parent->render_author(); ?>
							<?php $this->parent->render_date(); ?>
							<?php $this->parent->render_comments(); ?>
							<?php $this->parent->render_tags(); ?>
						</div>
					<?php endif; ?>
				</div>

			</div>
		<?php
	}

	public function render() {
		
		$settings = $this->parent->get_settings();
		$id       = $this->parent->get_id();

		$this->parent->query_posts( $settings['alter_item_limit']['size'] );
		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->parent->add_render_attribute( 'grid-height', 'class', ['bdt-grid', 'bdt-grid-collapse'] );
		$this->parent->add_render_attribute( 'grid-height', 'data-bdt-grid', '' );

		?> 
		<div id="bdt-post-grid-<?php echo esc_attr($id); ?>" class="bdt-post-grid bdt-post-grid-skin-alter">

			<?php $bdt_count = 0;
		
			while ($wp_query->have_posts()) :
				$wp_query->the_post();
					
	  			$bdt_count++;

	  			if ( $bdt_count % 2 != 0) {
					$bdt_post_class = ' bdt-plane';
	  			} else {
					$bdt_post_class = ' bdt-flex-first@s bdt-alter';
	  			}
				
				$this->render_post_grid_layout_alter( get_the_ID(), $settings['thumbnail_size'], $settings['excerpt_length'], $bdt_post_class );

	  			?>	  			
	  			
			<?php endwhile; ?>
		</div>	
 		<?php

		if ($settings['show_pagination']) { ?>
			<div class="ep-pagination">
				<?php element_pack_post_pagination($wp_query); ?>
			</div>
			<?php
		}
		wp_reset_postdata();
	}
}

