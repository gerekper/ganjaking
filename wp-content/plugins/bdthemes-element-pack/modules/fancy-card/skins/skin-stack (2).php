<?php
namespace ElementPack\Modules\FancyCard\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Stack extends Elementor_Skin_Base {

	public function get_id() {
		return 'stack';
	}

	public function get_title() {
		return __( 'Stack', 'bdthemes-element-pack' );
	}


	public function render() {
		$settings  = $this->parent->get_settings_for_display();
		
		if ('yes' == $settings['global_link'] and $settings['global_link_url']['url']) {

			$target = $settings['global_link_url']['is_external'] ? '_blank' : '_self';

			$this->parent->add_render_attribute( 'fancy-card', 'onclick', "window.open('" . $settings['global_link_url']['url'] . "', '$target')" );
		}
		
		if ( 'style1' == $settings['fancy_card_icon_style'] ) {
			$this->parent->add_render_attribute( 'fancy-card', 'class', 'bdt-ep-fancy-card bdt-ep-fancy-card-stack bdt-icon-style1' );
		} elseif ( 'style2' == $settings['fancy_card_icon_style'] ) {
			$this->parent->add_render_attribute( 'fancy-card', 'class', 'bdt-ep-fancy-card bdt-ep-fancy-card-stack bdt-icon-style2' );
		} elseif ( 'style3' == $settings['fancy_card_icon_style'] ) {
			$this->parent->add_render_attribute( 'fancy-card', 'class', 'bdt-ep-fancy-card bdt-ep-fancy-card-stack bdt-icon-style3' );
		} elseif ( 'style4' == $settings['fancy_card_icon_style'] ) {
			$this->parent->add_render_attribute( 'fancy-card', 'class', 'bdt-ep-fancy-card bdt-ep-fancy-card-stack bdt-icon-style4' );
		} else {
			$this->parent->add_render_attribute( 'fancy-card', 'class', 'bdt-ep-fancy-card bdt-ep-fancy-card-stack' );
		}

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'fancy-card' ); ?>>

			<?php $this->parent->render_icon(); ?>

			<div class="bdt-ep-fancy-card-content-overlay"></div>

			<div class="bdt-ep-fancy-card-content">
				<?php $this->parent->render_title(); ?>
				<?php $this->parent->render_text(); ?>
				<?php $this->parent->render_readmore(); ?>
			</div>
		</div>

		<?php $this->parent->render_indicator(); ?>
		<?php $this->parent->render_badge(); ?>

		<?php
	}
}