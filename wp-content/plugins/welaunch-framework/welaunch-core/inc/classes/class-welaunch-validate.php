<?php
/**
 * weLaunch Validate Class
 *
 * @class weLaunch_Validate
 * @version 4.0.0
 * @package weLaunch Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Validate', false ) ) {

	/**
	 * Class weLaunch_Validate
	 */
	abstract class weLaunch_Validate {

		/**
		 * weLaunch_Validate constructor.
		 *
		 * @param object $parent weLaunchFramework pointer.
		 * @param array  $field Fields array.
		 * @param array  $value Values array.
		 * @param mixed  $current Current.
		 */
		public function __construct( $parent, $field, $value, $current ) {
			$this->parent  = $parent;
			$this->field   = $field;
			$this->value   = $value;
			$this->current = $current;

			if ( isset( $this->field['validate_msg'] ) ) {
				$this->field['msg'] = $this->field['validate_msg'];

				unset( $this->field['validate_msg'] );
			}

			$this->validate();
		}

		/**
		 * Validate.
		 *
		 * @return mixed
		 */
		abstract public function validate();
	}
}
