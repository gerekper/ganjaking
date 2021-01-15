<?php
/**
 * Preg Replace validation
 *
 * @package     weLaunch Framework
 * @subpackage  Validation
 * @author      Kevin Provance (kprovance) & Dovy Paukstys
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Validation_Preg_Replace', false ) ) {

	/**
	 * Class weLaunch_Validation_Preg_Replace
	 */
	class weLaunch_Validation_Preg_Replace extends weLaunch_Validate {

		/**
		 * Field Validate Function.
		 * Takes the vars and validates them
		 *
		 * @since weLaunchFramework 1.0.0
		 */
		public function validate() {
			$that                   = $this;
			$this->value            = preg_replace( $this->field['preg']['pattern'], $that->field['preg']['replacement'], $this->value );
			$this->field['current'] = $this->value;

			$this->sanitize = $this->field;
		}
	}
}
