<?php
/**
 * Background Field.
 *
 * @package     weLaunchFramework/Fields
 * @author      Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Palette', false ) ) {

	/**
	 * Class weLaunch_Palette
	 */
	class weLaunch_Palette extends weLaunch_Field {

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settingss
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {
			if ( empty( $this->field['palettes'] ) ) {
				echo 'No palettes have been set.';

				return;
			}

			echo '<div id="' . esc_attr( $this->field['id'] ) . '" class="buttonset">';

			foreach ( $this->field['palettes'] as $value => $color_set ) {
				$checked = checked( $this->value, $value, false );

				echo '<input 
						type="radio" 
						value="' . esc_attr( $value ) . '" 
						name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '" 
						class="welaunch-palette-set ' . esc_attr( $this->field['class'] ) . '" 
						id="' . esc_attr( $this->field['id'] . '-' . $value ) . '"' . esc_html( $checked ) . '>';

				echo '<label for="' . esc_attr( $this->field['id'] . '-' . $value ) . '">';

				foreach ( $color_set as $color ) {
					echo '<span style=background:' . esc_attr( $color ) . '>' . esc_attr( $color ) . '</span>';
				}

				echo '</label>';
				echo '</input>';
			}

			echo '</div>';
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function enqueue() {
			$min = weLaunch_Functions::is_min();

			wp_enqueue_script(
				'welaunch-field-palette-js',
				weLaunch_Core::$url . 'inc/fields/palette/welaunch-palette' . $min . '.js',
				array( 'jquery', 'welaunch-js', 'jquery-ui-button', 'jquery-ui-core' ),
				$this->timestamp,
				true
			);

			if ( $this->parent->args['dev_mode'] ) {
				wp_enqueue_style(
					'welaunch-field-palette-css',
					weLaunch_Core::$url . 'inc/fields/palette/welaunch-palette.css',
					array(),
					$this->timestamp,
					'all'
				);
			}
		}

		/**
		 * Enable output_variables to be generated.
		 *
		 * @since       4.0.3
		 * @return void
		 */
		public function output_variables() {
			// No code needed, just defining the method is enough.
		}
	}
}

class_alias( 'weLaunch_Palette', 'weLaunchFramework_Palette' );
