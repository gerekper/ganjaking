<?php
/**
 * Str Replace validation
 *
 * @package     weLaunch Framework
 * @subpackage  Validation
 * @author      Kevin Provance (kprovance) & Dovy Paukstys
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Validation_Str_Replace', false ) ) {

	/**
	 * Class weLaunch_Validation_Str_Replace
	 */
	class weLaunch_Validation_Str_Replace extends weLaunch_Validate {

		/**
		 * Field Validate Function.
		 * Takes the vars and validates them
		 *
		 * @since weLaunchFramework 1.0.0
		 */
		public function validate() {
			$this->value = str_replace( $this->field['str']['search'], $this->field['str']['replacement'], $this->value );

			$this->field['current'] = $this->value;
			$this->sanitize         = $this->field;
		}
	}
}
