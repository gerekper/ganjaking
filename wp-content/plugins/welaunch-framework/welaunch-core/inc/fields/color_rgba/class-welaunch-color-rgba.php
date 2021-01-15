<?php
/**
 * Color RGBA Field.
 *
 * @package     weLaunchFramework/Fields
 * @author      Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'weLaunch_Color_Rgba', false ) ) {

	/**
	 * Main weLaunch_color_rgba class
	 *
	 * @since       1.0.0
	 */
	class weLaunch_Color_Rgba extends weLaunch_Field {

		/**
		 * Set field and value defaults.
		 */
		public function set_defaults() {
			$defaults = array(
				'color' => '',
				'alpha' => 1,
				'rgba'  => '',
			);

			$option_defaults = array(
				'show_input'             => true,
				'show_initial'           => false,
				'show_alpha'             => true,
				'show_palette'           => false,
				'show_palette_only'      => false,
				'max_palette_size'       => 10,
				'show_selection_palette' => false,
				'allow_empty'            => true,
				'clickout_fires_change'  => false,
				'choose_text'            => esc_html__( 'Choose', 'welaunch-framework' ),
				'cancel_text'            => esc_html__( 'Cancel', 'welaunch-framework' ),
				'show_buttons'           => true,
				'input_text'             => esc_html__( 'Select Color', 'welaunch-framework' ),
				'palette'                => null,
			);

			$this->value = wp_parse_args( $this->value, $defaults );

			if ( isset( $this->field ) && ! is_array( $this->field ) ) {
				return;
			}

			$this->field['options'] = isset( $this->field['options'] ) ? wp_parse_args( $this->field['options'], $option_defaults ) : $option_defaults;

			// Convert empty array to null, if there.
			$this->field['options']['palette'] = empty( $this->field['options']['palette'] ) ? null : $this->field['options']['palette'];

			$this->field['output_transparent'] = isset( $this->field['output_transparent'] ) ? $this->field['output_transparent'] : false;
		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {
			$field_id = $this->field['id'];

			// Color picker container.
			echo '<div 
                  class="welaunch-color-rgba-container ' . esc_attr( $this->field['class'] ) . '" 
                  data-id="' . esc_attr( $field_id ) . '"
                  data-show-input="' . esc_attr( $this->field['options']['show_input'] ) . '"
                  data-show-initial="' . esc_attr( $this->field['options']['show_initial'] ) . '"
                  data-show-alpha="' . esc_attr( $this->field['options']['show_alpha'] ) . '"
                  data-show-palette="' . esc_attr( $this->field['options']['show_palette'] ) . '"
                  data-show-palette-only="' . esc_attr( $this->field['options']['show_palette_only'] ) . '"
                  data-show-selection-palette="' . esc_attr( $this->field['options']['show_selection_palette'] ) . '"
                  data-max-palette-size="' . esc_attr( $this->field['options']['max_palette_size'] ) . '"
                  data-allow-empty="' . esc_attr( $this->field['options']['allow_empty'] ) . '"
                  data-clickout-fires-change="' . esc_attr( $this->field['options']['clickout_fires_change'] ) . '"
                  data-choose-text="' . esc_attr( $this->field['options']['choose_text'] ) . '"
                  data-cancel-text="' . esc_attr( $this->field['options']['cancel_text'] ) . '"
                  data-input-text="' . esc_attr( $this->field['options']['input_text'] ) . '"
                  data-show-buttons="' . esc_attr( $this->field['options']['show_buttons'] ) . '"
                  data-palette="' . rawurlencode( wp_json_encode( $this->field['options']['palette'] ) ) . '"
              >';

			// Colour picker layout.
			if ( '' === $this->value['color'] || 'transparent' === $this->value['color'] ) {
				$color = '';
			} else {
				$color = weLaunch_Helpers::hex2rgba( $this->value['color'], $this->value['alpha'] );
			}

			if ( '' === $this->value['rgba'] && '' !== $this->value['color'] ) {
				$this->value['rgba'] = weLaunch_Helpers::hex2rgba( $this->value['color'], $this->value['alpha'] );
			}

			echo '<input
                    name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[color]"
                    id="' . esc_attr( $field_id ) . '-color-display"
                    class="welaunch-color-rgba"
                    type="text"
                    value="' . esc_attr( $this->value['color'] ) . '"
                    data-color="' . esc_attr( $color ) . '"
                    data-id="' . esc_attr( $field_id ) . '"
                    data-current-color="' . esc_attr( $this->value['color'] ) . '"
                    data-block-id="' . esc_attr( $field_id ) . '"
                    data-output-transparent="' . esc_attr( $this->field['output_transparent'] ) . '"
                  />';

			echo '<input
                    type="hidden"
                    class="welaunch-hidden-color"
                    data-id="' . esc_attr( $field_id ) . '-color"
                    id="' . esc_attr( $field_id ) . '-color"
                    value="' . esc_attr( $this->value['color'] ) . '"
                  />';

			// Hidden input for alpha channel.
			echo '<input
                    type="hidden"
                    class="welaunch-hidden-alpha"
                    data-id="' . esc_attr( $field_id ) . '-alpha"
                    name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[alpha]"
                    id="' . esc_attr( $field_id ) . '-alpha"
                    value="' . esc_attr( $this->value['alpha'] ) . '"
                  />';

			// Hidden input for rgba.
			echo '<input
                    type="hidden"
                    class="welaunch-hidden-rgba"
                    data-id="' . esc_attr( $field_id ) . '-rgba"
                    name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[rgba]"
                    id="' . esc_attr( $field_id ) . '-rgba"
                    value="' . esc_attr( $this->value['rgba'] ) . '"
                  />';

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

			// Set up min files for dev_mode = false.
			$min = weLaunch_Functions::is_min();

			// Field dependent JS.
			wp_enqueue_script(
				'welaunch-field-color-rgba-js',
				weLaunch_Core::$url . 'inc/fields/color_rgba/welaunch-color-rgba' . weLaunch_Functions::is_min() . '.js',
				array( 'jquery', 'welaunch-spectrum-js', 'welaunch-js' ),
				$this->timestamp,
				true
			);

			// Spectrum CSS.
			if ( ! wp_style_is( 'welaunch-spectrum-css' ) ) {
				wp_enqueue_style( 'welaunch-spectrum-css' );
			}

			if ( $this->parent->args['dev_mode'] ) {
				wp_enqueue_style(
					'welaunch-field-color-rgba-css',
					weLaunch_Core::$url . 'inc/fields/color_rgba/welaunch-color-rgba.css',
					array(),
					$this->timestamp,
					'all'
				);
			}
		}

		/**
		 * -> getColorVal.  Returns formatted color val in hex or rgba.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since       1.0.0
		 * @access      private
		 * @return      string
		 */
		private function get_color_val() {

			// No notices.
			$color = '';
			$alpha = 1;
			$rgba  = '';

			// Must be an array.
			if ( is_array( $this->value ) ) {

				// Enum array to parse values.
				foreach ( $this->value as $id => $val ) {

					// Sanitize alpha.
					if ( 'alpha' === $id ) {
						$alpha = is_numeric( $val ) ? $val : 1;
					} elseif ( 'color' === $id ) {
						$color = ! empty( $val ) ? $val : '';
					} elseif ( 'rgba' === $id ) {
						$rgba = ! empty( $val ) ? $val : '';
						$rgba = weLaunch_Helpers::hex2rgba( $color, $alpha );
					}
				}

				// Only build rgba output if alpha ia less than 1.
				if ( $alpha < 1 && '' !== $alpha ) {
					$color = $rgba;
				}
			}

			return $color;
		}

		/**
		 * Generate CSS style.
		 *
		 * @param string $data Field data.
		 *
		 * @return array|void
		 */
		public function css_style( $data ) {
			$style = array();

			return $style;
		}

		/**
		 * Output Function.
		 * Used to enqueue to the front-end
		 *
		 * @since   1.0.0
		 * @access  public
		 *
		 * @param   string $style css data.
		 *
		 * @return  void
		 */
		public function output( $style = '' ) {
			if ( ! empty( $this->value ) ) {
				$mode = ( isset( $this->field['mode'] ) && ! empty( $this->field['mode'] ) ? $this->field['mode'] : 'color' );

				$color_val = $this->get_color_val();

				$style = $mode . ':' . $color_val . ';';

				if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
					if ( ! empty( $color_val ) ) {
						$css                      = weLaunch_Functions::parse_css( $this->field['output'], $style, $color_val );
						$this->parent->outputCSS .= esc_attr( $css );
					}
				}

				if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
					if ( ! empty( $color_val ) ) {
						$css                        = weLaunch_Functions::parse_css( $this->field['compiler'], $style, $color_val );
						$this->parent->compilerCSS .= esc_attr( $css );
					}
				}
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

class_alias( 'weLaunch_Color_Rgba', 'weLaunchFramework_Color_Rgba' );
