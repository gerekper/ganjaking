<?php
/**
 * No HTML validation
 *
 * @package     weLaunch Framework
 * @subpackage  Validation
 * @author      Kevin Provance (kprovance) & Dovy Paukstys
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Validation_No_Html', false ) ) {

	/**
	 * Class weLaunch_Validation_No_Html
	 */
	class weLaunch_Validation_No_Html extends weLaunch_Validate {

		/**
		 * Validate Function.
		 * Takes the vars and validates them
		 *
		 * @since weLaunchFramework 1.0.0
		 */
		public function validate() {
			$this->field['msg'] = ( isset( $this->field['msg'] ) ) ? $this->field['msg'] : esc_html__( 'You must not enter any HTML in this field.  All HTML has been removed.', 'welaunch-framework' );

			$newvalue = wp_strip_all_tags( $this->value );

			if ( $this->value !== $newvalue ) {
				$this->field['current'] = $newvalue;
				$this->warning          = $this->field;
			}

			$this->value = $newvalue;
		}
	}
}
