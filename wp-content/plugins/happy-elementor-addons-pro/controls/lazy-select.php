<?php
/**
 * Lazy Select control class
 *
 * And improved version of ha-select2 from HA Free
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Controls;

use Elementor\Base_Data_Control;
use Happy_Addons_Pro\Lazy_Query_Manager;

defined( 'ABSPATH' ) || die();

class Lazy_Select extends Base_Data_Control {

	/**
	 * Control identifier
	 */
	const TYPE = 'ha-lazy-select';

	/**
	 * Set control type.
	 */
	public function get_type() {
		return self::TYPE;
	}

	/**
	 * Enqueue control scripts and styles.
	 */
	public function enqueue() {
		wp_enqueue_script(
			'ha-lazy-select',
			HAPPY_ADDONS_PRO_ASSETS . 'admin/js/lazy-select.min.js',
			['jquery'],
			HAPPY_ADDONS_PRO_VERSION
		);

		wp_localize_script(
			'ha-lazy-select',
			'ha_lazy',
			[
				'nonce' => wp_create_nonce( Lazy_Query_Manager::ACTION ),
				'action' => Lazy_Query_Manager::ACTION,
				'ajax_url' => admin_url( 'admin-ajax.php' )
			]
		);
	}

	/**
	 * Get select2 control default settings.
	 *
	 * Retrieve the default settings of the select2 control. Used to return the
	 * default settings while initializing the select2 control.
	 *
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'options' => [],
			'multiple' => false,
			'select2options' => [],
			'lazy_args' => []
		];
	}

	/**
	 * Render select2 control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<# if ( data.label ) {#>
			<label for="<?php echo $control_uid; ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php echo $control_uid; ?>" class="elementor-select2" type="select2" {{ multiple }} data-setting="{{ data.name }}">
					<# _.each( data.options, function( option_title, option_value ) {
					var value = data.controlValue;
					if ( typeof value == 'string' ) {
					var selected = ( option_value === value ) ? 'selected' : '';
					} else if ( null !== value ) {
					var value = _.values( value );
					var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
					}
					#>
					<option {{ selected }} value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
