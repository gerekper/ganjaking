<?php
/**
 * CSS validation
 *
 * @package     weLaunch Framework
 * @subpackage  Validation
 * @author      Kevin Provance (kprovance) & Dovy Paukstys
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Validation_Css', false ) ) {

	/**
	 * Class weLaunch_Validation_Css
	 */
	class weLaunch_Validation_Css extends weLaunch_Validate {

		/**
		 * Field Validation Function.
		 * Takes the vars and validates them
		 *
		 * @since weLaunchFramework 3.0.0
		 */
		public function validate() {
			$this->field['msg'] = isset( $this->field['msg'] ) ? $this->field['msg'] : esc_html__( 'Unsafe strings were found in your CSS and have been filtered out.', 'welaunch-framework' );

			$data = $this->value;

			$data = wp_filter_nohtml_kses( $data );
			$data = str_replace( '&gt;', '>', $data );
			$data = stripslashes( $data );

			if ( $data !== $this->value ) {
				$this->field['current'] = $data;
				$this->warning          = $this->field;
			}

			$this->value = $data;
		}
	}
}
