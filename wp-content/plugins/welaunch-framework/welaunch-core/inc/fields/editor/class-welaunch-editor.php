<?php
/**
 * WordPress Tiny MCE Editor Field.
 *
 * @package     weLaunchFramework/Fields
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'weLaunch_Editor', false ) ) {

	/**
	 * Main weLaunch_editor class
	 *
	 * @since       1.0.0
	 */
	class weLaunch_Editor extends weLaunch_Field {

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {
			if ( ! isset( $this->field['args'] ) ) {
				$this->field['args'] = array();
			}

			$this->field['args']['onchange_callback'] = "alert('here')";

			// Setup up default args.
			$defaults = array(
				'textarea_name' => esc_attr( $this->field['name'] . $this->field['name_suffix'] ),
				'editor_class'  => esc_attr( $this->field['class'] ),
				'textarea_rows' => 10, // Wordpress default.
				'teeny'         => true,
			);

			if ( isset( $this->field['editor_options'] ) && empty( $this->field['args'] ) ) {
				$this->field['args'] = $this->field['editor_options'];
				unset( $this->field['editor_options'] );
			}

			$this->field['args'] = wp_parse_args( $this->field['args'], $defaults );

			wp_editor( $this->value, $this->field['id'], $this->field['args'] );
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
			if ( $this->parent->args['dev_mode'] ) {
				wp_enqueue_style(
					'welaunch-field-editor-css',
					weLaunch_Core::$url . 'inc/fields/editor/welaunch-editor.css',
					array(),
					$this->timestamp,
					'all'
				);
			}

			wp_enqueue_script(
				'welaunch-field-editor-js',
				weLaunch_Core::$url . 'inc/fields/editor/welaunch-editor' . weLaunch_Functions::is_min() . '.js',
				array( 'jquery', 'welaunch-js' ),
				$this->timestamp,
				true
			);
		}
	}
}

class_alias( 'weLaunch_Editor', 'weLaunchFramework_Editor' );
