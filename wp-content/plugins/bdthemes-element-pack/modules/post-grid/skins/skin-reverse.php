<?php
namespace ElementPack\Modules\PostGrid\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Reverse extends Elementor_Skin_Base {

	public function get_id() {
		return 'bdt-reverse';
	}

	public function get_title() {
		return __( 'Reverse', 'bdthemes-element-pack' );
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

	public function render_post_grid_layout( $post_id, $image_size, $excerpt_length ) {
		$settings = $this->parent->get_settings();

		if ('yes' == $settings['global_link']) {

		$this->parent->add_render_attribute( 'grid-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true );
		}

		$this->parent->add_render_attribute('grid-item', 'class', 'bdt-post-grid-item bdt-transition-toggle bdt-position-relative', true);

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'grid-item' ); ?>>
			<?php $this->parent->render_image(get_post_thumbnail_id( $post_id ), $image_size ); ?>

	  		
	  		<div class="bdt-post-grid-desc bdt-padding">
				<?php $this->parent->render_title(); ?>

				<?php $this->parent->render_excerpt($excerpt_length); ?>
				<?php $this->parent->render_readmore(); ?>
				
				<?php if ($settings['show_author'] or $settings['show_date'] or $settings['show_comments']) : ?>
					<div class="bdt-post-grid-meta bdt-subnav bdt-flex-inline bdt-flex-middle bdt-margin-small-top bdt-padding-remove-horizontal">
						<?php $this->parent->render_author(); ?>
						<?php $this->parent->render_date(); ?>
						<?php $this->parent->render_comments(); ?>
						<?php $this->parent->render_tags(); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php $this->render_category(); ?>
		</div>
		<?php
	}

	public function render() {
		
		$settings = $this->parent->get_settings();
		$id       = $this->parent->get_id();

		$this->parent->query_posts( $settings['reverse_item_limit']['size'] );
		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->parent->add_render_attribute( 'grid-height', 'class', ['bdt-grid', 'bdt-grid-collapse'] );
		$this->parent->add_render_attribute( 'grid-height', 'data-bdt-grid', '' );

		?> 
		<div id="bdt-post-grid-<?php echo esc_attr($id); ?>" class="bdt-post-grid bdt-post-grid-skin-reverse">
	  		<div <?php echo $this->parent->get_render_attribute_string( 'grid-height' ); ?>>

				<?php 
					$bdt_desktop_class = 0;
					$bdt_tablet_class = 0;
					$bdt_mobile_class = 0;

					$columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;
					$columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
					$columns 		= isset($settings['columns']) ? $settings['columns'] : 3;
					
					while ($wp_query->have_posts()) :
						$wp_query->the_post();
					
						$bdt_item_class = '';
						
					if(isset($settings['columns']) && ($bdt_desktop_class == $settings['columns']) ) {
		  				$bdt_desktop_class = 0;
		  			}
					if(isset($settings['columns_tablet']) && ($bdt_tablet_class == $settings['columns_tablet']) ) {
		  				$bdt_tablet_class = 0;
		  			}
					if(isset($settings['columns_mobile']) && ($bdt_mobile_class == $settings['columns_mobile']) ) {
		  				$bdt_mobile_class = 0;
		  			}
						
		  			$bdt_desktop_class++;
		  			$bdt_tablet_class++;
		  			$bdt_mobile_class++;

		  			if ( $bdt_desktop_class % 2 != 0) {
						$bdt_item_class   .= ' bdt-width-1-' . esc_attr($columns) . '@m bdt-plane-desktop';
		  			} else {
						$bdt_item_class   .= ' bdt-width-1-' . esc_attr($columns) . '@m bdt-reverse-desktop';
		  			}
					  
					
					if ( $bdt_tablet_class % 2 != 0) {
						$bdt_item_class   .= ' bdt-width-1-' . esc_attr($columns_tablet) . '@s bdt-plane-tablet';
		  			} else {
						$bdt_item_class   .= ' bdt-width-1-' . esc_attr($columns_tablet) . '@s bdt-reverse-tablet';
		  			}
					
					if ( $bdt_mobile_class % 2 != 0) {
						$bdt_item_class   .= ' bdt-width-1-' . esc_attr($columns_mobile) . ' bdt-plane-mobile';
		  			} else {
						$bdt_item_class   .= ' bdt-width-1-' . esc_attr($columns_mobile) . ' bdt-reverse-mobile';
		  			}

		  			?>

					<div class="<?php echo esc_attr($bdt_item_class); ?>">
						<?php $this->render_post_grid_layout( get_the_ID(), $settings['thumbnail_size'], $settings['excerpt_length']); ?>
					</div>	  			
		  			
				<?php endwhile; ?>
			</div>
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

