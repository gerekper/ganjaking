<?php
/**
 * Multi Text Field.
 *
 * @package     weLaunchFramework/Fields
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'weLaunch_Multi_Text', false ) ) {

	/**
	 * Main weLaunch_multi_text class
	 *
	 * @since       1.0.0
	 */
	class weLaunch_Multi_Text extends weLaunch_Field {

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {
			$this->add_text   = ( isset( $this->field['add_text'] ) ) ? $this->field['add_text'] : esc_html__( 'Add More', 'welaunch-framework' );
			$this->show_empty = ( isset( $this->field['show_empty'] ) ) ? $this->field['show_empty'] : true;

			echo '<ul id="' . esc_attr( $this->field['id'] ) . '-ul" class="welaunch-multi-text ' . esc_attr( $this->field['class'] ) . '">';

			if ( isset( $this->value ) && is_array( $this->value ) ) {
				foreach ( $this->value as $k => $value ) {
					if ( '' !== $value || ( '' === $value && true === $this->show_empty ) ) {
						echo '<li>';
						echo '<input
								type="text"
								id="' . esc_attr( $this->field['id'] . '-' . $k ) . '"
								name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[]"
								value="' . esc_attr( $value ) . '"
								class="regular-text" /> ';

						echo '<a
								data-id="' . esc_attr( $this->field['id'] ) . '-ul"
								href="javascript:void(0);"
								class="deletion welaunch-multi-text-remove">' .
								esc_html__( 'Remove', 'welaunch-framework' ) . '</a>';
						echo '</li>';
					}
				}
			} elseif ( true === $this->show_empty ) {
				echo '<li>';
				echo '<input
						type="text"
						id="' . esc_attr( $this->field['id'] . '-0' ) . '"
						name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[]"
						value=""
						class="regular-text" /> ';

				echo '<a
						data-id="' . esc_attr( $this->field['id'] ) . '-ul"
						href="javascript:void(0);"
						class="deletion welaunch-multi-text-remove">' .
						esc_html__( 'Remove', 'welaunch-framework' ) . '</a>';

				echo '</li>';
			}

			$the_name = '';
			if ( isset( $this->value ) && empty( $this->value ) && false === $this->show_empty ) {
				$the_name = $this->field['name'] . $this->field['name_suffix'];
			}

			echo '<li style="display:none;">';
			echo '<input
					type="text"
					id="' . esc_attr( $this->field['id'] ) . '"
					name="' . esc_attr( $the_name ) . '"
					value=""
					class="regular-text" /> ';

			echo '<a
					data-id="' . esc_attr( $this->field['id'] ) . '-ul"
					href="javascript:void(0);"
					class="deletion welaunch-multi-text-remove">' .
					esc_html__( 'Remove', 'welaunch-framework' ) . '</a>';

			echo '</li>';
			echo '</ul>';

			echo '<span style="clear:both;display:block;height:0;"></span>';
			$this->field['add_number'] = ( isset( $this->field['add_number'] ) && is_numeric( $this->field['add_number'] ) ) ? $this->field['add_number'] : 1;
			echo '<a href="javascript:void(0);" class="button button-primary welaunch-multi-text-add" data-add_number="' . esc_attr( $this->field['add_number'] ) . '" data-id="' . esc_attr( $this->field['id'] ) . '-ul" data-name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '">' . esc_html( $this->add_text ) . '</a><br/>';
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
			wp_enqueue_script(
				'welaunch-field-multi-text-js',
				weLaunch_Core::$url . 'inc/fields/multi_text/welaunch-multi-text' . weLaunch_Functions::is_min() . '.js',
				array( 'jquery', 'welaunch-js' ),
				$this->timestamp,
				true
			);

			if ( $this->parent->args['dev_mode'] ) {
				wp_enqueue_style(
					'welaunch-field-multi-text-css',
					weLaunch_Core::$url . 'inc/fields/multi_text/welaunch-multi-text.css',
					array(),
					$this->timestamp,
					'all'
				);
			}
		}
	}
}

class_alias( 'weLaunch_Multi_Text', 'weLaunchFramework_Multi_Text' );

