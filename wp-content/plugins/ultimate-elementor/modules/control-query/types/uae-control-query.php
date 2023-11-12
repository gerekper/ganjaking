<?php
/**
 * UAEL Control Query.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\ControlQuery\Types;

use Elementor\Base_Data_Control;
use UltimateElementor\Modules\ControlQuery\Module;
use UltimateElementor\Classes\UAEL_Helper;
use \Elementor\Control_Select2;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Presets_Select.
 */
class Uae_Control_Query extends Control_Select2 {

	const CONTROL_ID = 'uael-control-query';

	/**
	 * Get Control Type.
	 *
	 * @since 1.35.1
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return self::CONTROL_ID;
	}

	/**
	 * Get Default Settings.
	 *
	 * @since 1.35.1
	 * @access public
	 *
	 * @return array Settings.
	 */
	protected function get_default_settings() {
		return array(
			'label_block' => false,
			'multiple'    => false,
			'options'     => array(),
		);
	}

	/**
	 * Enqueue control scripts and styles.
	 *
	 * @since 1.35.1
	 * @access public
	 */
	public function enqueue() {
		wp_register_script( 'uael-control-query', UAEL_URL . 'editor-assets/js/control-query.js', array( 'jquery' ), UAEL_VER, false );
		wp_enqueue_script( 'uael-control-query' );
		wp_localize_script(
			'uael-control-query',
			'uael_control_query',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Control content template.
	 *
	 * @since 1.35.1
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{ data.label }}</label>
			<div class="elementor-control-input-wrapper">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php echo esc_attr( $control_uid ); ?>" class="elementor-select2" type="select2" {{ multiple }} data-setting="{{ data.name }}">
					<# _.each( data.options, function( option_title, option_value ) {
						var value = data.controlValue;
						if ( typeof value == 'string' ) {
							var selected = ( option_value === value ) ? 'selected' : '';
						} else if ( null !== value ) {
							var value = _.values( value );
							var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
						}
						#>
					<option {{ selected }} value="{{ option_value }}">{{ option_title }}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{ data.description }}</div>
		<# } #>
		<?php
	}
}
