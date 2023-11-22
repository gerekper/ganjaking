<?php
namespace ElementPack\Modules\PostGrid\Skins;
use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Paddle extends Elementor_Skin_Base {

	public function get_id() {
		return 'bdt-paddle';
	}

	public function get_title() {
		return __( 'Paddle', 'bdthemes-element-pack' );
	}

	public function render() {
		$settings     = $this->parent->get_settings();
		$id           = $this->parent->get_id();
		
		$odd_columns  = $settings['odd_item_columns'];
		$even_columns = $settings['even_item_columns'];

		$this->parent->query_posts( $settings['paddle_item_limit']['size'] );

		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		?> 
		<div id="bdt-post-grid-<?php echo esc_attr($id); ?>" class="bdt-post-grid bdt-post-grid-skin-default">
	  		<div class="bdt-grid bdt-grid-<?php echo esc_attr($settings['column_gap']); ?>" data-bdt-grid>

				<?php $bdt_count = 0;

				$bdt_sum = $odd_columns + $even_columns;
			
				while ($wp_query->have_posts()) :
					$wp_query->the_post();						

		  			if ( $bdt_count == $bdt_sum ) {
		  				$bdt_count = 0;
		  			}

		  			$bdt_count++;

		  			if ( $bdt_count <= $odd_columns ) {
						$bdt_grid_cols   = $odd_columns;
						$bdt_post_class = ' bdt-primary';
						$thumbnail_size = $settings['primary_thumbnail_size'];
		  			} else {
						$bdt_grid_cols   = $even_columns;
						$bdt_post_class = ' bdt-secondary';
						$thumbnail_size = $settings['secondary_thumbnail_size'];
		  			}

		  			?>
		  			<div class="bdt-width-1-<?php echo esc_attr($bdt_grid_cols); ?>@m<?php echo esc_attr($bdt_post_class); ?>">
						<?php $this->parent->render_post_grid_item( get_the_ID(), $thumbnail_size, $settings['excerpt_length'] ); ?>
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

