<?php
namespace ElementPack\Modules\AdvancedImageGallery\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Hidden extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-hidden';
	}

	public function get_title() {
		return __( 'Hidden', 'bdthemes-element-pack' );
	}

	public function render_header() {

		$settings = $this->parent->get_settings_for_display();
		$id       = $this->parent->get_id();

		$this->parent->add_render_attribute('advanced-image-gallery', 'id', 'bdt-avdg-' . esc_attr($id) );

		$this->parent->add_render_attribute('advanced-image-gallery', 'class', ['bdt-ep-advanced-image-gallery', 'bdt-ep-advanced-image-gallery-skin-hidden'] );

		if ( $settings['show_lightbox'] ) {
			$this->parent->add_render_attribute('advanced-image-gallery', 'data-bdt-lightbox', 'animation: ' . $settings['lightbox_animation'] . ';');
			if ($settings['lightbox_autoplay']) {
				$this->parent->add_render_attribute('advanced-image-gallery', 'data-bdt-lightbox', 'autoplay: 500;');

				if ($settings['lightbox_pause']) {
					$this->parent->add_render_attribute('advanced-image-gallery', 'data-bdt-lightbox', 'pause-on-hover: true;');
				}
			}
		}

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'advanced-image-gallery' ); ?>>
		<?php
	}

	public function render_loop_item() {
		$settings = $this->parent->get_settings_for_display();

		$this->parent->add_render_attribute('advanced-image-gallery-item', 'class', ['bdt-ep-advanced-image-gallery-item', 'bdt-transition-toggle']);

		$this->parent->add_render_attribute('advanced-image-gallery-inner', 'class', 'bdt-ep-advanced-image-gallery-inner');
		
		if ($settings['tilt_show']) {
			$this->parent->add_render_attribute('advanced-image-gallery-inner', 'data-tilt', '');
		}

		foreach ( $settings['avd_gallery_images'] as $index => $item ) : ?>
			
			<?php $this->parent->link_only($item); ?>

		<?php endforeach;
	}

	public function render() {
		$settings = $this->parent->get_settings_for_display();
		$id       = $this->parent->get_id();

		if ( empty( $settings['avd_gallery_images'] ) ) {
			return;
		}

		$this->render_header();
		$this->render_loop_item();
		$this->parent->render_footer();
	}
}

