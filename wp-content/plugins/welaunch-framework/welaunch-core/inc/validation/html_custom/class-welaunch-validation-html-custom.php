<?php
/**
 * CUstom HTML validation
 *
 * @package     weLaunch Framework
 * @subpackage  Validation
 * @author      Kevin Provance (kprovance) & Dovy Paukstys
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Validation_Html_Custom', false ) ) {

	/**
	 * Class weLaunch_Validation_Html_Custom
	 */
	class weLaunch_Validation_Html_Custom extends weLaunch_Validate {

		/**
		 * Field Render Function.
		 * Takes the vars and validates them
		 *
		 * @since weLaunchFramework 1.0.0
		 */
		public function validate() {
			$this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'Unallowed HTML was found in this field and has been removed.', 'welaunch-framework' );

			if ( isset( $this->field['allowed_html'] ) ) {
				$html = wp_kses( $this->value, $this->field['allowed_html'] );

				if ( $html !== $this->value ) {
					$this->field['current'] = $html;
					$this->warning          = $this->field;
				}

				$this->value = $html;
			}
		}
	}
}
