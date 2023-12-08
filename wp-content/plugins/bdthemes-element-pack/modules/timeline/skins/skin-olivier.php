<?php
namespace ElementPack\Modules\Timeline\Skins;
use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Olivier extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-olivier';
	}

	public function get_title() {
		return __( 'Olivier', 'bdthemes-element-pack' );
	}

	public function render_script() {
		$settings = $this->parent->get_settings_for_display();

		$this->parent->add_render_attribute( 'timeline', 'class', [ 'bdt-timeline', 'bdt-timeline-skin-olivier' ] );
		$this->parent->add_render_attribute( 'timeline', 'data-visible_items', $settings['visible_items'] );
	}

	public function render_custom() {
		$id             = $this->parent->get_id();
		$settings       = $this->parent->get_settings_for_display();
		$timeline_items = $settings['timeline_items'];
		
		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'timeline' ); ?>>
			<div class="bdt-timeline-wrapper">
				<div class="bdt-timeline-items">					
					
					<?php foreach ( $timeline_items as $item ) : ?>							
						
						<div class="bdt-timeline-item">
							<div class="bdt-timeline-content">
								<?php $this->parent->render_item( '', '', $item ); ?>
							</div>
						</div>

					<?php endforeach; ?>

				</div>
			</div>
		</div>
 		<?php
	}

	public function render_post() {
		$settings = $this->parent->get_settings_for_display();

		// TODO need to delete after v6.5
        if (isset($settings['posts_limit']) and $settings['posts_per_page'] == 6) {
            $limit = $settings['posts_limit'];
        } else {
            $limit = $settings['posts_per_page'];
        }

        $this->parent->query_posts($limit);

        $wp_query = $this->parent->get_query();

        if ( ! $wp_query->found_posts ) {
            return;
        }

		if( $wp_query->have_posts() ) :
		
			?>
			<div <?php echo $this->parent->get_render_attribute_string( 'timeline' ); ?>>
				<div class="bdt-timeline-wrapper">
					<div class="bdt-timeline-items">

						<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>							
						
							<div class="bdt-timeline-item">
								<div class="bdt-timeline-content">
									<?php $this->parent->render_item( '', '', '' ); ?>
								</div>
							</div>

						<?php endwhile; wp_reset_postdata(); ?>

					</div>
				</div>
			</div>
 		<?php endif;

	}

	public function render() {

		$settings = $this->parent->get_settings_for_display();

		$this->render_script();

		if ( 'post'  === $settings['timeline_source'] ) {
			$this->render_post();
		} else if ( 'custom'  === $settings['timeline_source'] ) {
			$this->render_custom();
		} else {
			return;
		}

	}
}