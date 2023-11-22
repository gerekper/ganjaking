<?php
namespace ElementPack\Modules\HoverBox\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

use Elementor\Icons_Manager;
use ElementPack\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Flexure extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-flexure';
	}

	public function get_title() {
		return __( 'Flexure', 'bdthemes-element-pack' );
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();

		if ($settings['hover_box_event']) {
			$hoverBoxEvent = $settings['hover_box_event'];
		} else {
			$hoverBoxEvent = false;
        }
        
        if ($settings['box_image_effect']) {
			$this->parent->add_render_attribute('hover_box', 'class', 'bdt-ep-hover-box-img-effect bdt-' . $settings['box_image_effect_select']);
		}

		$this->parent->add_render_attribute(
			[
				'hover_box' => [
					'id' => 'bdt-ep-hover-box-' . $this->parent->get_id(),
					'class' => 'bdt-ep-hover-box bdt-ep-hover-box-skin-flexure',
					'data-settings' => [
						wp_json_encode(array_filter([
							'box_id' => 'bdt-ep-hover-box-' . $this->parent->get_id(),
							'mouse_event' => $hoverBoxEvent,
						]))
					]
				]
			]
		);

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'hover_box' ); ?>>

			<?php $this->parent->box_content(); ?>
			<?php $this->box_items(); ?>
			
		</div>

		<?php
	}

	public function box_items() {
		$settings = $this->parent->get_settings_for_display();
		$id       = $this->parent->get_id();

        $this->parent->add_render_attribute( 'box-settings', 'data-bdt-ep-hover-box-items', 'connect: #bdt-box-content-' .  esc_attr($id) . ';' );
		$this->parent->add_render_attribute( 'box-settings', 'class', ['bdt-ep-hover-box-item-wrap', 'bdt-position-' . $settings['content_gap'], 'bdt-position-' . $settings['default_content_position']] );


		?>
			<div <?php echo ( $this->parent->get_render_attribute_string( 'box-settings' ) ); ?>>
				<div>
 
					<?php  foreach ( $settings['hover_box'] as $index => $item ) :
						
						$tab_count = $index + 1;
						$tab_id    = 'bdt-box-'. $tab_count . esc_attr($id);
 
						$this->parent->add_render_attribute( 'box-item', 'class', 'bdt-ep-hover-box-item', true );
						
						$this->parent->add_render_attribute( 'bdt-ep-hover-box-title', 'class', 'bdt-ep-hover-box-title', true );
						$this->parent->add_render_attribute(
							[
								'title-link' => [
									'class' => [
										'bdt-ep-hover-box-title-link',
									],
									'href'   => $item['title_link']['url'] ? esc_url($item['title_link']['url']) : 'javascript:void(0);',
									'target' => $item['title_link']['is_external'] ? '_blank' : '_self'
								]
							], '', '', true
						);
						
						?>
                        <div <?php echo ( $this->parent->get_render_attribute_string( 'box-item' ) ); ?> data-id="<?php echo esc_attr($tab_id); ?>">

                            <?php if ( $item['hover_box_title'] && ( 'yes' == $settings['show_title'] ) ) : ?>
                                <<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->parent->get_render_attribute_string('bdt-ep-hover-box-title'); ?>>
									<?php if ( '' !== $item['title_link']['url'] ) : ?>
										<a <?php echo $this->parent->get_render_attribute_string( 'title-link' ); ?>>
									<?php endif; ?>
										<?php echo wp_kses( $item['hover_box_title'], element_pack_allow_tags('title') ); ?>
									<?php if ( '' !== $item['title_link']['url'] ) : ?>
										</a>
									<?php endif; ?>
                                </<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
                            <?php endif; ?>

                            <?php if ( $item['hover_box_sub_title'] && ( 'yes' == $settings['show_sub_title'] ) ) : ?>
                                <div class="bdt-ep-hover-box-sub-title">
                                    <?php echo wp_kses( $item['hover_box_sub_title'], element_pack_allow_tags('title') ); ?>
                                </div>
                            <?php endif; ?>

                        </div>
					<?php endforeach; ?>

				</div>
			</div>
		<?php
	}
}
