<?php
/**
 * Color Gradient Field.
 *
 * @package     weLaunchFramework/Fields
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'weLaunch_Color_Gradient', false ) ) {

	/**
	 * Main weLaunch_color_gradient class
	 *
	 * @since       1.0.0
	 */
	class weLaunch_Color_Gradient extends weLaunch_Field {

		/**
		 * weLaunch_Field constructor.
		 *
		 * @param array  $field Field array.
		 * @param string $value Field values.
		 * @param null   $parent weLaunchFramework object pointer.
		 */
		public function __construct( $field = array(), $value = null, $parent = null ) { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod
			parent::__construct( $field, $value, $parent );
		}

		/**
		 * Set field and value defaults.
		 */
		public function set_defaults() {
			// No errors please.
			$defaults = array(
				'from' => '',
				'to'   => '',
			);

			$this->value = weLaunch_Functions::parse_args( $this->value, $defaults );

			$defaults = array(
				'preview'        => false,
				'preview_height' => '150px',
			);

			$this->field = wp_parse_args( $this->field, $defaults );

			if ( weLaunch_Core::$pro_loaded ) {
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				$this->field = apply_filters( 'welaunch/pro/color_gradient/field/set_defaults', $this->field );

				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				$this->value = apply_filters( 'welaunch/pro/color_gradient/value/set_defaults', $this->value );
			}
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
			if ( weLaunch_Core::$pro_loaded ) {
				// phpcs:ignore WordPress.NamingConventions.ValidHookName, WordPress.Security.EscapeOutput
				echo apply_filters( 'welaunch/pro/color_gradient/render/gradient_type', null );
			}

			$mode_arr = array(
				'from',
				'to',
			);

			foreach ( $mode_arr as $idx => $mode ) {
				$uc_mode = ucfirst( $mode );

				echo '<div class="colorGradient ' . esc_html( $mode ) . 'Label">';
				echo '<strong>' . esc_html( $uc_mode . ' ' ) . '</strong>&nbsp;';
				echo '<input ';
				echo 'data-id="' . esc_attr( $this->field['id'] ) . '"';
				echo 'id="' . esc_attr( $this->field['id'] ) . '-' . esc_attr( $mode ) . '"';
				echo 'name="' . esc_attr( $this->field['name'] ) . esc_attr( $this->field['name_suffix'] ) . '[' . esc_attr( $mode ) . ']"';
				echo 'value="' . esc_attr( $this->value[ $mode ] ) . '"';
				echo 'class="color-picker welaunch-color welaunch-color-init ' . esc_attr( $this->field['class'] ) . '"';
				echo 'type="text"';
				echo 'data-default-color="' . esc_attr( $this->field['default'][ $mode ] ) . '"';

				if ( weLaunch_Core::$pro_loaded ) {
					$data = array(
						'field' => $this->field,
						'index' => $mode,
					);

					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					echo esc_html( apply_filters( 'welaunch/pro/render/color_alpha', $data ) );
				}

				echo '/>';

				echo '<input type="hidden" class="welaunch-saved-color" id="' . esc_attr( $this->field['id'] ) . '-' . esc_attr( $mode ) . '-saved-color" value="">';

				if ( ! isset( $this->field['transparent'] ) || false !== $this->field['transparent'] ) {
					$trans_checked = '';

					if ( 'transparent' === $this->value[ $mode ] ) {
						$trans_checked = ' checked="checked"';
					}

					echo '<label for="' . esc_attr( $this->field['id'] ) . '-' . esc_html( $mode ) . '-transparency" class="color-transparency-check">';
					echo '<input type="checkbox" class="checkbox color-transparency ' . esc_attr( $this->field['class'] ) . '" id="' . esc_attr( $this->field['id'] ) . '-' . esc_attr( $mode ) . '-transparency" data-id="' . esc_attr( $this->field['id'] ) . '-' . esc_attr( $mode ) . '" value="1"' . esc_html( $trans_checked ) . '> ' . esc_html__( 'Transparent', 'welaunch-framework' );
					echo '</label>';
				}

				echo '</div>';
			}

			if ( weLaunch_Core::$pro_loaded ) {
				// phpcs:ignore WordPress.NamingConventions.ValidHookName, WordPress.Security.EscapeOutput
				echo apply_filters( 'welaunch/pro/color_gradient/render/preview', null );

				// phpcs:ignore WordPress.NamingConventions.ValidHookName, WordPress.Security.EscapeOutput
				echo apply_filters( 'welaunch/pro/color_gradient/render/extra_inputs', null );
			}
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
			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_script(
				'welaunch-field-color-gradient-js',
				weLaunch_Core::$url . 'inc/fields/color_gradient/welaunch-color-gradient' . weLaunch_Functions::is_min() . '.js',
				array( 'jquery', 'wp-color-picker', 'welaunch-js' ),
				$this->timestamp,
				true
			);

			if ( weLaunch_Core::$pro_loaded ) {
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( 'welaunch/pro/color_gradient/enqueue' );
			}

			if ( $this->parent->args['dev_mode'] ) {
				wp_enqueue_style( 'welaunch-color-picker-css' );

				wp_enqueue_style(
					'welaunch-field-color_gradient-css',
					weLaunch_Core::$url . 'inc/fields/color_gradient/welaunch-color-gradient.css',
					array(),
					$this->timestamp,
					'all'
				);
			}
		}

		/**
		 * Compile CSS styling for output.
		 *
		 * @param string $data CSS data.
		 *
		 * @return mixed|void
		 */
		public function css_style( $data ) {
			if ( weLaunch_Core::$pro_loaded ) {

				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				$pro_data = apply_filters( 'welaunch/pro/color_gradient/output', $data );

				return $pro_data;
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

class_alias( 'weLaunch_Color_Gradient', 'weLaunchFramework_Color_Gradient' );
