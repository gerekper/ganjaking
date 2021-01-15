<?php
/**
 * No Special Chars validation
 *
 * @package     weLaunch Framework
 * @subpackage  Validation
 * @author      Kevin Provance (kprovance) & Dovy Paukstys
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Validation_No_Special_Chars', false ) ) {

	/**
	 * Class weLaunch_Validation_No_Special_Chars
	 */
	class weLaunch_Validation_No_Special_Chars extends weLaunch_Validate {

		/**
		 * Field Render Function.
		 * Takes the vars and validates them
		 *
		 * @since weLaunchFramework 1.0.0
		 */
		public function validate() {
			$this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'You must not enter any special characters in this field, all special characters have been removed.', 'welaunch-framework' );

			if ( 0 === ! preg_match( '/[^a-zA-Z0-9_ -]/s', $this->value ) ) {
				$this->field['current'] = $this->current;

				$this->warning = $this->field;
			}

			$this->value = preg_replace( '/[^a-zA-Z0-9_ -]/s', '', $this->value );
		}
	}
}
