<?php
/**
 * Javascript validation
 *
 * @package     weLaunch Framework
 * @subpackage  Validation
 * @author      Kevin Provance (kprovance) & Dovy Paukstys
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Validation_Js', false ) ) {

	/**
	 * Class weLaunch_Validation_Js
	 */
	class weLaunch_Validation_Js extends weLaunch_Validate {

		/**
		 * Field Validation Function.
		 * Takes the vars and validates them
		 *
		 * @since weLaunchFramework 1.0.0
		 */
		public function validate() {
			$this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'Javascript has been successfully escaped.', 'welaunch-framework' );

			$js = esc_js( $this->value );

			if ( $js !== $this->value ) {
				$this->field['current'] = $js;
				$this->warning          = $this->field;
			}

			$this->value = $js;
		}
	}
}
