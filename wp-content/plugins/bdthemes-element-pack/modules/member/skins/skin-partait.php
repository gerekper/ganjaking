<?php

namespace ElementPack\Modules\Member\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;

use Elementor\Skin_Base as Elementor_Skin_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Partait extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-partait';
	}

	public function get_title() {
		return __('Partait', 'bdthemes-element-pack');
	}

	public function render() {
		$partait_id = 'partait' . $this->parent->get_id();
		$settings   = $this->parent->get_settings_for_display();

		if (!isset($settings['social_icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['social_icon'] = 'fab fa-facebook-f';
		}

		$image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';
		$this->parent->add_render_attribute('image-wrap', 'class', 'bdt-member-photo-wrapper' . $image_mask);

		?>
		<div class="bdt-member skin-partait">
			<div class="bdt-grid bdt-grid-collapse bdt-child-width-1-2@m" data-bdt-grid>
				<?php
				if (!empty($settings['photo']['url'])) :
					$photo_hover_animation = ('' != $settings['photo_hover_animation']) ? ' bdt-transition-scale-' . $settings['photo_hover_animation'] : ''; ?>

					<div <?php echo $this->parent->get_render_attribute_string('image-wrap'); ?>>

						<?php if (($settings['member_alternative_photo']) and (!empty($settings['alternative_photo']['url']))) : ?>
							<div class="bdt-position-relative bdt-overflow-hidden" bdt-toggle="target: > .bdt-member-photo-flip; mode: hover; animation: bdt-animation-fade; queued: true; duration: 300;">

								<div class="bdt-member-photo-flip bdt-position-absolute bdt-position-z-index">
									<?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'alternative_photo'); ?>
								</div>
							<?php endif; ?>

							<div class="bdt-member-photo">
								<div class="<?php echo ($photo_hover_animation); ?>">
									<?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'photo'); ?>
								</div>
							</div>

							<?php if (($settings['member_alternative_photo']) and (!empty($settings['alternative_photo']['url']))) : ?>
							</div>
						<?php endif; ?>

					</div>

				<?php endif; ?>

				<div class="bdt-member-desc bdt-position-relative bdt-flex bdt-flex-middle">
					<div class="bdt-text-center bdt-member-desc-wrapper">
						<div class="bdt-member-content">
							<?php if (!empty($settings['name'])) : ?>
								<span class="bdt-member-name"><?php echo wp_kses($settings['name'], element_pack_allow_tags('title')); ?></span>
							<?php endif; ?>

							<?php if (!empty($settings['role'])) : ?>
								<span class="bdt-member-role"><?php echo wp_kses($settings['role'], element_pack_allow_tags('title')); ?></span>
							<?php endif; ?>

							<?php if (!empty($settings['description_text'])) : ?>
								<div class="bdt-member-text bdt-content-wrap"><?php echo wp_kses($settings['description_text'], element_pack_allow_tags('text')); ?></div>
							<?php endif; ?>
						</div>

						<?php if ('yes' == $settings['member_social_icon']) : ?>
							<div class="bdt-member-icons">
								<?php
								foreach ($settings['social_link_list'] as $link) :
									$tooltip = ('yes' == $settings['social_icon_tooltip']) ? ' title="' . esc_attr($link['social_link_title']) . '" 
								 data-bdt-tooltip' : '';

									$migrated  = isset($link['__fa4_migrated']['social_share_icon']);
									$is_new    = empty($link['social_icon']) && Icons_Manager::is_migration_allowed();
								?>
									<a href="<?php echo esc_url($link['social_link']); ?>" class="bdt-member-icon elementor-repeater-item-<?php echo esc_attr($link['_id']); ?>" target="_blank" <?php echo wp_kses_post($tooltip); ?>>

										<?php if ($is_new || $migrated) :
											Icons_Manager::render_icon($link['social_share_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
										else : ?>
											<i class="<?php echo esc_attr($link['social_icon']); ?>" aria-hidden="true"></i>
										<?php endif; ?>

									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

					</div>
				</div>
			</div>
		</div>
<?php
	}
}
