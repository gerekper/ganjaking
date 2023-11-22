<?php
namespace ElementPack\Modules\PostGrid\Skins;
use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Carmie extends Elementor_Skin_Base {

	public function get_id() {
		return 'bdt-carmie';
	}

	public function get_title() {
		return __( 'Carmie', 'bdthemes-element-pack' );
	}

	public function render() {

		$settings = $this->parent->get_settings();		
		$id       = $this->parent->get_id();

		$columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;
		$columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
		$columns 		 = isset($settings['columns']) ? $settings['columns'] : 3;

		$this->parent->add_render_attribute('post-grid-item', 'class', 'bdt-width-1-'. $columns_mobile);
		$this->parent->add_render_attribute('post-grid-item', 'class', 'bdt-width-1-'. $columns_tablet .'@s');
		$this->parent->add_render_attribute('post-grid-item', 'class', 'bdt-width-1-'. $columns .'@m');

		$this->parent->query_posts($settings['carmie_item_limit']['size']);
		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		?> 
		<div id="bdt-post-grid-<?php echo esc_attr($id); ?>" class="bdt-post-grid bdt-post-grid-skin-carmie">
	  		<div class="bdt-grid bdt-grid-<?php echo esc_attr($settings['column_gap']); ?>" data-bdt-grid>

				<?php			
				while ($wp_query->have_posts()) :
					$wp_query->the_post();
		  				
					if ('yes' == $settings['global_link']) {

					$this->parent->add_render_attribute( 'grid-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true );
					}

					$this->parent->add_render_attribute('grid-item', 'class', 'bdt-post-grid-item bdt-transition-toggle bdt-position-relative', true);

					?>
					
		            <div <?php echo $this->parent->get_render_attribute_string( 'post-grid-item' ); ?>>
						<div <?php echo $this->parent->get_render_attribute_string( 'grid-item' ); ?>>
								
							<?php $this->parent->render_image(get_post_thumbnail_id( get_the_ID() ), $settings['thumbnail_size'] ); ?>

							<div class="bdt-custom-overlay bdt-position-cover"></div>
					  		
					  		<div class="bdt-post-grid-desc bdt-position-bottom-left">
						  		<div class="bdt-position-medium ">
					            	<?php if (('yes' == $settings['show_author']) or ('yes' == $settings['show_date'])) : ?>
										<div class="bdt-post-grid-meta bdt-subnav bdt-flex-inline bdt-flex-middle">
											<?php $this->parent->render_author(); ?>
											<?php $this->parent->render_date(); ?>
											<?php $this->parent->render_comments(); ?>
											<?php $this->parent->render_tags(); ?>
										</div>
									<?php endif; ?>

									<?php $this->parent->render_title(); ?>

							  		<div class="bdt-transition-slide-bottom">
										<?php $this->parent->render_excerpt($settings['excerpt_length']); ?>
									</div>
								</div>
							</div>

							<?php $this->parent->render_category(); ?>

						</div>
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

