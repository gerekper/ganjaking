<?php
/**
 * Color Field.
 *
 * @package     weLaunchFramework/Fields
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'weLaunch_Color', false ) ) {

	/**
	 * Main weLaunch_color class
	 *
	 * @since       1.0.0
	 */
	class weLaunch_Color extends weLaunch_Field {

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since         1.0.0
		 * @access        public
		 * @return        void
		 */
		public function render() {
			echo '<input ';
			echo 'data-id="' . esc_attr( $this->field['id'] ) . '"';
			echo 'name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '"';
			echo 'id="' . esc_attr( $this->field['id'] ) . '-color"';
			echo 'class="color-picker welaunch-color welaunch-color-init ' . esc_attr( $this->field['class'] ) . '"';
			echo 'type="text" value="' . esc_attr( $this->value ) . '"';
			echo 'data-oldcolor=""';
			echo 'data-default-color="' . ( isset( $this->field['default'] ) ? esc_attr( $this->field['default'] ) : '' ) . '"';

			if ( weLaunch_Core::$pro_loaded ) {
				$data = array(
					'field' => $this->field,
					'index' => '',
				);

				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				echo esc_html( apply_filters( 'welaunch/pro/render/color_alpha', $data ) );
			}

			echo '/>';

			echo '<input type="hidden" class="welaunch-saved-color" id="' . esc_attr( $this->field['id'] ) . '-saved-color" value="">';

			if ( ! isset( $this->field['transparent'] ) || false !== $this->field['transparent'] ) {
				$trans_checked = '';

				if ( 'transparent' === $this->value ) {
					$trans_checked = ' checked="checked"';
				}

				echo '<label for="' . esc_attr( $this->field['id'] ) . '-transparency" class="color-transparency-check">';
				echo '<input type="checkbox" class="checkbox color-transparency ' . esc_attr( $this->field['class'] ) . '" id="' . esc_attr( $this->field['id'] ) . '-transparency" data-id="' . esc_attr( $this->field['id'] ) . '-color" value="1"' . esc_html( $trans_checked ) . '>';
				echo esc_html__( 'Transparent', 'welaunch-framework' );
				echo '</label>';
			}
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
				wp_enqueue_style( 'welaunch-color-picker-css' );
			}

			if ( ! wp_style_is( 'wp-color-picker' ) ) {
				wp_enqueue_style( 'wp-color-picker' );
			}

			$dep_array = array( 'jquery', 'wp-color-picker', 'welaunch-js' );

			wp_enqueue_script(
				'welaunch-field-color-js',
				weLaunch_Core::$url . 'inc/fields/color/welaunch-color' . weLaunch_Functions::is_min() . '.js',
				$dep_array,
				$this->timestamp,
				true
			);

			if ( weLaunch_Core::$pro_loaded ) {
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( 'welaunch/pro/enqueue/color_alpha', $this->field );
			}
		}

		/**
		 * Generate CSS style (unused, but needed).
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
		 * Output CSS styling.
		 *
		 * @param string $style CSS style.
		 */
		public function output( $style = '' ) {
			if ( ! empty( $this->value ) ) {
				$mode = ( isset( $this->field['mode'] ) && ! empty( $this->field['mode'] ) ? $this->field['mode'] : 'color' );

				$style = $mode . ':' . $this->value . ';';

				if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
					$css                      = weLaunch_Functions::parse_css( $this->field['output'], $style, $this->value );
					$this->parent->outputCSS .= $css;
				}

				if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
					$css                        = weLaunch_Functions::parse_css( $this->field['compiler'], $style, $this->value );
					$this->parent->compilerCSS .= $css;
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

class_alias( 'weLaunch_Color', 'weLaunchFramework_Color' );
