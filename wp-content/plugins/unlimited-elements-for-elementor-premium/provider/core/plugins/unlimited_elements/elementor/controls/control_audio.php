<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Control_UC_AUDIO extends Base_Data_Control {

	/**
	 * Retrieve code control type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'uc_mp3';
	}

	/**
	 * get hr default settings
	 */
	protected function get_default_settings() {
		return [
		];
	}

	/**
	* get content template
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field uc-control-field-audio">
			<label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<input id="<?php echo esc_attr($control_uid); ?>" type="text" class="tooltip-target" data-tooltip="{{ data.title }}" title="{{ data.title }}" data-setting="{{ data.name }}" placeholder="{{ data.placeholder }}" />
				<input type="button" class="uc-button-choose-audio uc-button-control" value="<?php esc_html_e("Choose Audio","unlimited-elements-for-elementor")?>">
				<div class='uc-audio-control-text'></div>
			</div>			
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		
		<?php
	}
}
