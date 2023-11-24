<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor icon control.
 *
 * A base control for creating an icon control. Displays a font icon select box
 * field. The control accepts `include` or `exclude` arguments to set a partial
 * list of icons.
 *
 * @since 1.0.0
 */
class Control_UC_Shape extends Base_Data_Control {

	/**
	 * Retrieve code control type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'uc_shape_picker';
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
		<div class="elementor-control-field uc-control-field-shape">
			<label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<input id="<?php echo esc_attr($control_uid); ?>" type="text" class="tooltip-target" data-tooltip="{{ data.title }}" title="{{ data.title }}" data-setting="{{ data.name }}" placeholder="{{ data.placeholder }}" style='display:none'/>
				
				<div class="uc-button-choose-shape uc-button-control" ><?php esc_html_e("Choose Shape","unlimited-elements-for-elementor")?></div>
			
				<div class="uc-shape-picker-dialog-container">
					container
				</div>
				
			</div>		
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		
		<?php
	}
	
	
}
