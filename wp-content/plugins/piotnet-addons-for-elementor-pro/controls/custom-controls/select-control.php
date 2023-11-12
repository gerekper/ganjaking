<?php
namespace Elementor\PafeCustomControls;

use \Elementor\Base_Data_Control;

class Select_Control extends Base_Data_Control {

	const Select = 'pafe_custom_control_select';

	/**
	 * Set control type.
	 */
	public function get_type() {
		return self::Select;
	}

	/**
	 * Enqueue control scripts and styles.
	 */
	public function enqueue() {

	}

	/**
	 * Set default settings
	 */
	protected function get_default_settings() {
		return [
			'options' => [],
			'multiple' => false,
			'get_fields' => false,
		];
	}
	
	/**
	 * control field markup
	 */
	public function content_template() {
		?>
		<div class="elementor-control-field">
			<# if ( data.label ) {#>
				<label for="<?php $this->print_control_uid(); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<#
					var multiple = ( data.multiple ) ? 'multiple' : '';
					var value = data.controlValue;
				#>
				<select id="<?php $this->print_control_uid(); ?>" class="elementor-select2" type="select2" {{ multiple }} data-setting="{{ data.name }}" <# if ( data.get_fields_include_itself ) { #> data-pafe-get-fields-include-itself<# } #>>
					<# _.each( data.options, function( option_title, option_value ) {
						if ( typeof value == 'string' ) {
							var selected = ( option_value === value ) ? 'selected' : '';
						} else if ( null !== value ) {
							var value = _.values( value );
							var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
						}
						#>
					<option {{ selected }} value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
					<#
						if ( value && data.get_fields ) {
					#>
						<option value="{{ value }}" selected>{{{ value }}}</option>
					<# } #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

}