<?php
/**
 * UAEL Presets Select.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\PresetsSelect\Controls;

use Elementor\Base_Data_Control;
use UltimateElementor\Modules\PresetsSelect\Module;
use UltimateElementor\Classes\UAEL_Helper;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Presets_Select.
 */
class Presets_Select extends Base_Data_Control {

	const CONTROL_ID = 'uael-presets-select';

	/**
	 * Get Control Type.
	 *
	 * @since 1.33.0
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
	 * @since 1.33.0
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
	 * @since 1.33.0
	 * @access public
	 */
	public function enqueue() {

		$folder = UAEL_Helper::get_js_folder();
		$suffix = UAEL_Helper::get_js_suffix();

		wp_register_script( 'uael-presets-select', UAEL_URL . 'assets/' . $folder . '/uael-presets' . $suffix . '.js', array( 'jquery' ), UAEL_VER, false );
		wp_enqueue_script( 'uael-presets-select' );

		wp_localize_script(
			'uael-presets-select',
			'uael_presets',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'uael-presets-nonce' ),
			)
		);
	}

	/**
	 * Control content template.
	 *
	 * @since 1.33.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<# if ( data.label ) {#>
				<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{ data.label }}</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<select id="<?php echo esc_attr( $control_uid ); ?>" data-setting="{{ data.name }}">
				<#
					var printOptions = function( options ) {
						_.each( options, function( option_title, option_value ) { #>
								<option value="{{ option_value }}">{{ option_title }}</option>
						<# } );
					};

					if ( data.groups ) {
						for ( var groupIndex in data.groups ) {
							var groupArgs = data.groups[ groupIndex ];
								if ( groupArgs.options ) { #>
									<optgroup label="{{ groupArgs.label }}">
										<# printOptions( groupArgs.options ) #>
									</optgroup>
								<# } else if ( _.isString( groupArgs ) ) { #>
									<option value="{{ groupIndex }}">{{ groupArgs }}</option>
								<# }
						}
					} else {
						printOptions( data.options );
					}
				#>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{ data.description }}</div>
		<# } #>
		<?php
	}
}
