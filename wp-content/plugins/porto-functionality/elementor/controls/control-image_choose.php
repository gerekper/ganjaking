<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Porto Image_Choose Control
 *
 * @since 1.5.4
 */

use Elementor\Base_Data_Control;

class Porto_Control_Image_Choose extends Base_Data_Control {
	public function get_type() {
		return 'image_choose';
	}

	public function content_template() {
		$control_uid = $this->get_control_uid( '{{value}}' );
		?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<div class="elementor-choices">
					<# _.each( data.options, function( options, value ) { #>
					<input id="<?php echo $control_uid; ?>" type="radio" name="elementor-choose-{{ data.name }}-{{ data._cid }}" value="{{ value }}" data-setting="{{ data.name }}">
					<label class="elementor-choices-label" for="<?php echo $control_uid; ?>">
						<img src="<?php echo esc_url( PORTO_SHORTCODES_URL . 'assets/images/' ); ?>{{{ options }}}">
						<span class="elementor-screen-only">{{{ options }}}</span>
						<# if ( data.display_label ) { #>
							<span class="porto-image-select-label">{{{ value.replace( /-/g, ' ' ) }}}</span>
						<# } #>
					</label>
					<# } ); #>
				</div>
			</div>
		</div>

		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	protected function get_default_settings() {
		return [
			'options' => [],
			'toggle'  => true,
		];
	}
}
