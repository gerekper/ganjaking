<?php
namespace ElementPack\Modules\PostGrid\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Elanza extends Elementor_Skin_Base {

	public function get_id() {
		return 'bdt-elanza';
	}

	public function get_title() {
		return __( 'Elanza', 'bdthemes-element-pack' );
	}

	public function render() {
		
		$settings = $this->parent->get_settings();
		$id       = $this->parent->get_id();

		$this->parent->query_posts(7);
		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		?> 
		<div id="bdt-post-grid-<?php echo esc_attr($id); ?>" class="bdt-post-grid bdt-post-grid-skin-elanza">
	  		<div class="bdt-grid bdt-grid-<?php echo esc_attr($settings['column_gap']); ?>" data-bdt-grid>

				<?php 
				$bdt_count = 0;

				$secondary_columns_mobile = isset($settings['secondary_columns_mobile']) ? $settings['secondary_columns_mobile'] : 1;
				$secondary_columns_tablet = isset($settings['secondary_columns_tablet']) ? $settings['secondary_columns_tablet'] : 3;
				$secondary_columns 		 = isset($settings['secondary_columns']) ? $settings['secondary_columns'] : 3;

				while ($wp_query->have_posts()) :
					$wp_query->the_post();
					$bdt_count++;
		  			?>
		  			
					<?php if (1 == $bdt_count) : ?>
						<div class="bdt-width-2-5@m bdt-primary">
						    <div>
						        <?php $this->parent->render_post_grid_item( get_the_ID(), $settings['primary_thumbnail_size'], $settings['excerpt_length'] ); ?>
						    </div>
						</div>

						<div class="bdt-width-3-5@m bdt-secondary">
						    <div>
						        <div class="bdt-grid bdt-grid-<?php echo esc_attr($settings['column_gap']); ?>" data-bdt-grid>
					<?php endif; ?>

									<?php if (1 < $bdt_count) : 
										$item_columns   = ' bdt-width-1-' . esc_attr($secondary_columns) . '@m bdt-width-1-' . esc_attr($secondary_columns_tablet) . '@s bdt-width-1-' . esc_attr($secondary_columns_mobile) ;
										?>
							            <div class="<?php echo esc_attr($item_columns); ?>">
							                <?php $this->parent->render_post_grid_item( get_the_ID(), $settings['secondary_thumbnail_size'], $settings['excerpt_length'] ); ?>
							            </div>
						            <?php endif; ?>

					<?php if (8 == $bdt_count) : ?>
						        </div>
						    </div>
						</div>
					<?php endif; ?>

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

