<?php
namespace ElementPack\Modules\HoverBox\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

use Elementor\Icons_Manager;
use ElementPack\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Envelope extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-envelope';
	}

	public function get_title() {
		return __( 'Envelope', 'bdthemes-element-pack' );
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
					'class' => 'bdt-ep-hover-box bdt-ep-hover-box-skin-envelope',
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

	public function render_navigation() {
		$settings = $this->parent->get_settings_for_display();

		if ( ! $settings['show_navigation_arrows'] ) {
			return;
		}
		
		?>

		<a class="bdt-position-center-left bdt-margin-medium-left bdt-slidenav bdt-hidden-hover" href="#" bdt-slidenav-previous bdt-slider-item="previous"></a>
		<a class="bdt-position-center-right bdt-margin-medium-right bdt-slidenav bdt-hidden-hover" href="#" bdt-slidenav-next bdt-slider-item="next"></a>

		<?php
	}

	public function box_items() {
		$settings = $this->parent->get_settings_for_display();
		$id       = $this->parent->get_id();

		$this->parent->add_render_attribute( 'box-settings', 'data-bdt-ep-hover-box-items', 'connect: #bdt-box-content-' .  esc_attr($id) . ';' );
        $this->parent->add_render_attribute( 'box-settings', 'class', 'bdt-ep-hover-box-item-wrap' );

		$this->parent->add_render_attribute('box-settings', 'data-bdt-grid', '');
		$this->parent->add_render_attribute('box-settings', 'class', ['bdt-grid', 'bdt-grid-small', 'bdt-grid-collapse'] );
		
		$this->parent->add_render_attribute('box-settings', 'class', 'bdt-slider-items');

		$desktop_cols = isset($settings["columns"]) ? (int)$settings["columns"] : 3;
		$tablet_cols  = isset($settings["columns_tablet"]) ? (int)$settings["columns_tablet"] : 2;
		$mobile_cols  = isset($settings["columns_mobile"]) ? (int)$settings["columns_mobile"] : 2;

		$this->parent->add_render_attribute('box-settings', 'class', 'bdt-child-width-1-' . esc_attr($mobile_cols));
		$this->parent->add_render_attribute('box-settings', 'class', 'bdt-child-width-1-' . esc_attr($tablet_cols) .'@s');
		$this->parent->add_render_attribute('box-settings', 'class', 'bdt-child-width-1-' . esc_attr($desktop_cols) .'@m');

		$this->parent->add_render_attribute(
			[
				'slider-settings' => [
					'class' => 'bdt-slider bdt-visible-toggle',
					'data-bdt-slider' => [
						wp_json_encode(array_filter([
							"autoplay"          => false,
							"autoplay-interval" => 7000,
							"finite"            => false,
							"pause-on-hover"    => true,
						]))
					]
				]
			]
		);

		?>
		<div <?php echo ( $this->parent->get_render_attribute_string( 'slider-settings' ) ); ?>>
			<div <?php echo $this->parent->get_render_attribute_string( 'box-settings' ); ?>>

				<?php foreach ( $settings['hover_box'] as $index => $item ) :
					
					$tab_count = $index + 1;
					$tab_id    = 'bdt-box-'. $tab_count . esc_attr($id);
					$active_item = $this->parent->activeItem($settings['hover_box_active_item'], count($settings['hover_box']));
					if ($tab_id    == 'bdt-box-'. $active_item . esc_attr($id)) {
						$this->parent->add_render_attribute( 'box-item', 'class', 'bdt-ep-hover-box-item active', true );
					} else {
						$this->parent->add_render_attribute( 'box-item', 'class', 'bdt-ep-hover-box-item', true );
					}

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

					$this->parent->add_render_attribute(
						[
							'button-link' => [
								'class' => [
									'bdt-ep-hover-box-title',
								],
								'href'   => $item['button_link']['url'] ? esc_url($item['button_link']['url']) : 'javascript:void(0);',
								'target' => $item['button_link']['is_external'] ? '_blank' : '_self'
							]
						], '', '', true
					);
					
					?>
					<div>
						<div <?php echo ( $this->parent->get_render_attribute_string( 'box-item' ) ); ?> data-id="<?php echo esc_attr($tab_id); ?>">

						<div class="bdt-ep-hover-box-description bdt-position-small bdt-position-<?php echo esc_attr( $settings['content_position'] ); ?>">
							<?php if ( 'yes' == $settings['show_icon'] ) : ?>
							<a class="bdt-ep-hover-box-icon-box" href="javascript:void(0);" data-tab-index="<?php echo esc_attr($index); ?>" >
								<span class="bdt-ep-hover-box-icon-wrap">
									<?php Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</span>
							</a>
							<?php endif; ?>
								<?php if ( $item['hover_box_sub_title'] && ( 'yes' == $settings['show_sub_title'] ) ) : ?>
									<div class="bdt-ep-hover-box-sub-title">
										<?php echo wp_kses( $item['hover_box_sub_title'], element_pack_allow_tags('title') ); ?>
									</div>
								<?php endif; ?>

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

								<?php if ( $item['hover_box_content'] && ( 'yes' == $settings['show_content'] ) ) : ?>
									<div class="bdt-ep-hover-box-text">
										<?php echo wp_kses_post( $item['hover_box_content'] ); ?>
									</div>
								<?php endif; ?>

								<?php if ($item['hover_box_button'] && ( 'yes' == $settings['show_button'] )) : ?>
									<div class="bdt-ep-hover-box-button">
										<a <?php echo $this->parent->get_render_attribute_string( 'button-link' ); ?>>
											<?php echo wp_kses_post($item['hover_box_button']); ?>
										</a>
									</div>
								<?php endif; ?>
							</div>

						</div>
					</div>
				<?php endforeach; ?>

			</div>

			<?php $this->render_navigation(); ?>

		</div>

		<?php
	}
}

