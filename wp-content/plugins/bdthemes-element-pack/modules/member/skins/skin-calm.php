<?php
namespace ElementPack\Modules\Member\Skins;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use ElementPack\Base\Module_Base;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Calm extends Elementor_Skin_Base {
	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action( 'elementor/element/bdt-member/section_style/before_section_start', [ $this, 'register_calm_style_controls' ] );

	}

	public function get_id() {
		return 'bdt-calm';
	}

	public function get_title() {
		return __( 'Calm', 'bdthemes-element-pack' );
	}

	public function register_calm_style_controls(Module_Base $widget) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_style_calm',
			[
				'label' => __( 'Calm', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'calm_overlay_color',
			[
				'label'     => __( 'Overlay Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-member .bdt-member-overlay' => 'background: -webkit-linear-gradient(top, rgba(0,0,0,0) 0%,{{VALUE)}} 100%); background: linear-gradient(to bottom, rgba(0,0,0,0) 0%,{{VALUE)}} 100%);',
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$calm_id  = 'calm' . $this->parent->get_id();
		$settings = $this->parent->get_settings_for_display();

		$image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';

		$this->parent->add_render_attribute( 'skin-calm', 'class', 'bdt-member skin-calm bdt-transition-toggle bdt-inline' . $image_mask );

		if(($settings['member_alternative_photo']) and ( ! empty( $settings['alternative_photo']['url']))) {
			$this->parent->add_render_attribute( 'skin-calm', 'class', ['bdt-position-relative', 'bdt-overflow-hidden', 'bdt-transition-toggle'] );
			$this->parent->add_render_attribute( 'skin-calm', 'bdt-toggle', 'target: > div > .bdt-member-photo-flip; mode: hover; animation: bdt-animation-fade; queued: true; duration: 300;' );
		}

		if ( ! isset( $settings['social_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['social_icon'] = 'fab fa-facebook-f';
		}

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'skin-calm' ); ?>>
		<?php

			if ( ! empty( $settings['photo']['url'] ) ) :
				$photo_hover_animation = ( '' != $settings['photo_hover_animation'] ) ? ' bdt-transition-scale-'.$settings['photo_hover_animation'] : ''; ?>

				<div class="bdt-member-photo-wrapper">

					<?php if(($settings['member_alternative_photo']) and ( ! empty( $settings['alternative_photo']['url']))) : ?>
						<div class="bdt-member-photo-flip bdt-position-absolute bdt-position-z-index">
							<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'alternative_photo' ); ?>
						</div>
					<?php endif; ?>

					<div class="bdt-member-photo">
						<div class="<?php echo ($photo_hover_animation); ?>">
							<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'photo' ); ?>
						</div>
					</div>

				</div>

			<?php endif; ?>

			<div class="bdt-member-overlay bdt-overlay bdt-position-bottom bdt-text-center bdt-position-z-index">
				<div class="bdt-member-desc">
					<div class="bdt-member-content bdt-transition-slide-bottom-small">
						<?php if ( ! empty( $settings['name'] ) ) : ?>
							<span class="bdt-member-name"><?php echo wp_kses( $settings['name'], element_pack_allow_tags('title') ); ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $settings['role'] ) ) : ?>
							<span class="bdt-member-role"><?php echo wp_kses( $settings['role'], element_pack_allow_tags('title') ); ?></span>
						<?php endif; ?>
					</div>
					
					<?php if ( 'yes' == $settings['member_social_icon'] ) : ?>
					<div class="bdt-member-icons bdt-transition-slide-bottom">
						<?php 
						foreach ( $settings['social_link_list'] as $link ) :
							$tooltip = ( 'yes' == $settings['social_icon_tooltip'] ) ? ' title="'.esc_attr( $link['social_link_title'] ).'"  
							data-bdt-tooltip' : ''; ?>

							<?php 
							$migrated  = isset( $link['__fa4_migrated']['social_share_icon'] );
							$is_new    = empty( $link['social_icon'] ) && Icons_Manager::is_migration_allowed();
							?>
							
							<a href="<?php echo esc_url( $link['social_link'] ); ?>" class="bdt-member-icon elementor-repeater-item-<?php echo esc_attr($link['_id']); ?>" target="_blank"<?php echo wp_kses_post($tooltip); ?>>
								
								<?php if ( $is_new || $migrated ) :
									Icons_Manager::render_icon( $link['social_share_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
								else : ?>
									<i class="<?php echo esc_attr( $link['social_icon'] ); ?>" aria-hidden="true"></i>
								<?php endif; ?>

							</a>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
					
				</div>
			</div>			
		</div>
		<?php
	}
}

