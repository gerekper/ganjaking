<?php
/**
 * Date Field.
 *
 * @package     weLaunchFramework/Fields
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'weLaunch_Date', false ) ) {

	/**
	 * Main weLaunch_date class
	 *
	 * @since       1.0.0
	 */
	class weLaunch_Date extends weLaunch_Field {

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since         1.0.0
		 * @access        public
		 * @return        void
		 */
		public function render() {
			$placeholder = ( isset( $this->field['placeholder'] ) ) ? ' placeholder="' . $this->field['placeholder'] . '" ' : '';

			echo '<input 
					data-id="' . esc_attr( $this->field['id'] ) . '" 
					type="text" 
					id="' . esc_attr( $this->field['id'] ) . '-date" 
					name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '"' . esc_attr( $placeholder ) . '
					value="' . esc_attr( $this->value ) . '" 
					class="welaunch-datepicker regular-text ' . esc_attr( $this->field['class'] ) . '" />';
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since         1.0.0
		 * @access        public
		 * @return        void
		 */
		public function enqueue() {
			if ( $this->parent->args['dev_mode'] ) {
				wp_enqueue_style(
					'welaunch-field-date-css',
					weLaunch_Core::$url . 'inc/fields/date/welaunch-date.css',
					array(),
					$this->timestamp,
					'all'
				);
			}

			wp_enqueue_script(
				'welaunch-field-date-js',
				weLaunch_Core::$url . 'inc/fields/date/welaunch-date' . weLaunch_Functions::is_min() . '.js',
				array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'welaunch-js' ),
				$this->timestamp,
				true
			);
		}
	}
}

class_alias( 'weLaunch_Date', 'weLaunchFramework_Date' );
