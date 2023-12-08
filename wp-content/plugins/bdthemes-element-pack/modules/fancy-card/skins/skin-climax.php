<?php
namespace ElementPack\Modules\FancyCard\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;

use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Climax extends Elementor_Skin_Base {

	public function get_id() {
		return 'climax';
	}

	public function get_title() {
		return __( 'Climax', 'bdthemes-element-pack' );
	}

	public function render() {
		$settings  = $this->parent->get_settings_for_display();

		if ('yes' == $settings['global_link'] and $settings['global_link_url']['url']) {

			$target = $settings['global_link_url']['is_external'] ? '_blank' : '_self';

			$this->parent->add_render_attribute( 'fancy-card', 'onclick', "window.open('" . $settings['global_link_url']['url'] . "', '$target')" );
		}

		$this->parent->add_render_attribute( 'toggle_input_position', 'class', 'bdt-checkbox' );
		$this->parent->add_render_attribute( 'toggle_input_position', 'class', 'bdt-position-'. $settings['toggle_position'] );

		$this->parent->add_render_attribute( 'toggle_position', 'class', 'bdt-ep-fancy-card-toggole' );
		$this->parent->add_render_attribute( 'toggle_position', 'class', 'bdt-position-'. $settings['toggle_position'] );

		$this->parent->add_render_attribute( 'fancy-card', 'class', 'bdt-ep-fancy-card bdt-ep-fancy-card-climax' );

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'fancy-card' ); ?>>

			<input <?php echo $this->parent->get_render_attribute_string( 'toggle_input_position' ); ?> type="checkbox">
			<div <?php echo $this->parent->get_render_attribute_string( 'toggle_position' ); ?>>
				<?php Icons_Manager::render_icon( $settings['toggle_icon'], [ 'aria-hidden' => 'true' ] ); ?>
			</div>

			<?php $this->parent->render_icon(); ?>

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