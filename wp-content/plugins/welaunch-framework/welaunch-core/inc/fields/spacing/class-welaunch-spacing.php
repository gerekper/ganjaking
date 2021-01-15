<?php
/**
 * Spacing Field
 *
 * @package     weLaunch Framework/Fields
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Spacing', false ) ) {

	/**
	 * Class weLaunch_Spacing
	 */
	class weLaunch_Spacing extends weLaunch_Field {

		/**
		 * Set field a value defaults.
		 */
		public function set_defaults() {
			$defaults = array(
				'units'          => 'px',
				'mode'           => 'padding',
				'top'            => true,
				'bottom'         => true,
				'all'            => false,
				'left'           => true,
				'right'          => true,
				'units_extended' => false,
				'display_units'  => true,
			);

			$this->field = wp_parse_args( $this->field, $defaults );

			// Set default values.
			$defaults = array(
				'top'    => '',
				'right'  => '',
				'bottom' => '',
				'left'   => '',
				'units'  => '',
			);

			$this->value = wp_parse_args( $this->value, $defaults );
		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since weLaunchFramework 1.0.0
		 */
		public function render() {
			/*
			 * Acceptable values checks.  If the passed variable doesn't pass muster, we unset them
			 * and reset them with default values to avoid errors.
			 */

			$unit_arr = weLaunch_Helpers::$array_units;

			$unit_check   = $unit_arr;
			$unit_check[] = false;

			// If units field has a value but is not an acceptable value, unset the variable.
			if ( ! weLaunch_Helpers::array_in_array( $this->field['units'], $unit_check ) ) {
				$this->field['units'] = 'px';
			}

			// If there is a default unit value  but is not an accepted value, unset the variable.
			if ( ! weLaunch_Helpers::array_in_array( $this->value['units'], $unit_check ) ) {
				$this->value['units'] = 'px';
			}

			if ( false === $this->field['units'] ) {
				'' === $this->value['units'];
			}

			if ( ! in_array( $this->field['mode'], array( 'margin', 'padding' ), true ) ) {
				if ( 'absolute' === $this->field['mode'] ) {
					$this->field['mode'] = '';
				} else {
					$this->field['mode'] = 'padding';
				}
			}

			$value = array(
				'top'    => isset( $this->value[ $this->field['mode'] . '-top' ] ) ? filter_var( $this->value[ $this->field['mode'] . '-top' ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['top'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
				'right'  => isset( $this->value[ $this->field['mode'] . '-right' ] ) ? filter_var( $this->value[ $this->field['mode'] . '-right' ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['right'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
				'bottom' => isset( $this->value[ $this->field['mode'] . '-bottom' ] ) ? filter_var( $this->value[ $this->field['mode'] . '-bottom' ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['bottom'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
				'left'   => isset( $this->value[ $this->field['mode'] . '-left' ] ) ? filter_var( $this->value[ $this->field['mode'] . '-left' ], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : filter_var( $this->value['left'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ),
			);

			// if field units has a value and is NOT an array, then evaluate as needed.
			if ( ! is_array( $this->field['units'] ) ) {

				// If units fields has a value and is not empty but units value does not then make units value the field value.
				if ( '' === $this->value['units'] && ( '' !== $this->field['units'] || false === $this->field['units'] ) ) {
					$this->value['units'] = $this->field['units'];

					// If units field does NOT have a value and units value does NOT have a value, set both to blank (default?).
				} elseif ( '' === $this->field['units'] && '' === $this->value['units'] ) {
					$this->field['units'] = 'px';
					$this->value['units'] = 'px';

					// If units field has NO value but units value does, then set unit field to value field.
				} elseif ( '' === $this->field['units'] && '' !== $this->value['units'] ) {
					$this->field['units'] = $this->value['units'];

					// if unit value is set and unit value doesn't equal unit field (coz who knows why)
					// then set unit value to unit field.
				} elseif ( '' !== $this->value['units'] && $this->field['units'] !== $this->value['units'] ) {
					$this->value['units'] = $this->field['units'];
				}

				// do stuff based on unit field NOT set as an array.
				// phpcs:ignore Generic.CodeAnalysis.EmptyStatement
			} elseif ( ! empty( $this->field['units'] ) && is_array( $this->field['units'] ) ) {
				// nothing to do here, but I'm leaving the construct just in case I have to debug this again.
			}

			if ( '' !== $this->field['units'] ) {
				$value['units'] = $this->value['units'];
			}

			$this->value = $value;

			if ( '' !== $this->field['mode'] ) {
				$this->field['mode'] = $this->field['mode'] . '-';
			}

			if ( isset( $this->field['select2'] ) ) {
				$this->field['select2'] = wp_parse_args( $this->field['select2'], $this->select2_config );
			} else {
				$this->field['select2'] = $this->select2_config;
			}

			$this->field['select2'] = weLaunch_Functions::sanitize_camel_case_array_keys( $this->field['select2'] );

			$select2_data = weLaunch_Functions::create_data_string( $this->field['select2'] );

			echo '<input 
					type="hidden" 
					name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] ) . '[units]" 
					class="field-units" value="' . esc_attr( $this->value['units'] ) . '">';

			if ( true === $this->field['all'] ) {
				$this->field['top']    = true;
				$this->field['right']  = true;
				$this->field['bottom'] = true;
				$this->field['left']   = true;

				$this->value['bottom'] = $this->value['top'];
				$this->value['left']   = $this->value['top'];
				$this->value['right']  = $this->value['top'];

				echo '<div class="field-spacing-input input-prepend">
                        <span class="add-on">
                            <i class="el el-fullscreen icon-large"></i>
                        </span>
                        <input 
                            type="text" 
                            class="welaunch-spacing-all welaunch-spacing-input mini ' . esc_attr( $this->field['class'] ) . '" 
                            placeholder="' . esc_html__( 'All', 'welaunch-framework' ) . '" 
                            rel="' . esc_attr( $this->field['id'] ) . '-all" 
                            value="' . esc_attr( $this->value['top'] ) . '"
                        >
                      </div>';
			}

			if ( true === $this->field['top'] ) {
				echo '<input 
                        type="hidden" 
                        class="welaunch-spacing-value" 
                        id="' . esc_attr( $this->field['id'] ) . '-top" 
                        name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] . '[' . $this->field['mode'] ) . 'top]" 
                        value="' . esc_attr( $this->value['top'] ) . ( ! empty( $this->value['top'] ) ? esc_attr( $this->value['units'] ) : '' ) . '"
                      >';
			}

			if ( true === $this->field['right'] ) {
				echo '<input 
                        type="hidden" 
                        class="welaunch-spacing-value" 
                        id="' . esc_attr( $this->field['id'] ) . '-right" 
                        name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] . '[' . $this->field['mode'] ) . 'right]" 
                        value="' . esc_attr( $this->value['right'] ) . ( ! empty( $this->value['right'] ) ? esc_attr( $this->value['units'] ) : '' ) . '"
                      >';
			}

			if ( true === $this->field['bottom'] ) {
				echo '<input 
                        type="hidden" 
                        class="welaunch-spacing-value" 
                        id="' . esc_attr( $this->field['id'] ) . '-bottom" 
                        name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] . '[' . $this->field['mode'] ) . 'bottom]" 
                        value="' . esc_attr( $this->value['bottom'] ) . ( ! empty( $this->value['bottom'] ) ? esc_attr( $this->value['units'] ) : '' ) . '"
                      >';
			}

			if ( true === $this->field['left'] ) {
				echo '<input 
                        type="hidden" 
                        class="welaunch-spacing-value" 
                        id="' . esc_attr( $this->field['id'] ) . '-left" 
                        name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] . '[' . $this->field['mode'] ) . 'left]" 
                        value="' . esc_attr( $this->value['left'] ) . ( ! empty( $this->value['left'] ) ? esc_attr( $this->value['units'] ) : '' ) . '"
                      >';
			}

			if ( false === $this->field['all'] ) {
				/**
				 * Top
				 * */
				if ( true === $this->field['top'] ) {
					echo '<div class="field-spacing-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-up icon-large"></i>
                            </span>
                            <input type="text" 
                                   class="welaunch-spacing-top welaunch-spacing-input mini ' . esc_attr( $this->field['class'] ) . '"
                                   placeholder="' . esc_html__( 'Top', 'welaunch-framework' ) . '"
                                   rel="' . esc_attr( $this->field['id'] ) . '-top"
                                   value="' . esc_attr( $this->value['top'] ) . '"/>
                        </div>';
				}

				/**
				 * Right
				 * */
				if ( true === $this->field['right'] ) {
					echo '<div class="field-spacing-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-right icon-large"></i>
                            </span>
                            <input type="text" 
                                   class="welaunch-spacing-right welaunch-spacing-input mini ' . esc_attr( $this->field['class'] ) . '"
                                   placeholder="' . esc_html__( 'Right', 'welaunch-framework' ) . '"
                                   rel="' . esc_attr( $this->field['id'] ) . '-right"
                                   value="' . esc_attr( $this->value['right'] ) . '"/>
                        </div>';
				}

				/**
				 * Bottom
				 * */
				if ( true === $this->field['bottom'] ) {
					echo '<div class="field-spacing-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-down icon-large"></i>
                            </span>
                            <input type="text" 
                                   class="welaunch-spacing-bottom welaunch-spacing-input mini ' . esc_attr( $this->field['class'] ) . '"
                                   placeholder="' . esc_html__( 'Bottom', 'welaunch-framework' ) . '"
                                   rel="' . esc_attr( $this->field['id'] ) . '-bottom"
                                   value="' . esc_attr( $this->value['bottom'] ) . '">
                        </div>';
				}

				/**
				 * Left
				 * */
				if ( true === $this->field['left'] ) {
					echo '<div class="field-spacing-input input-prepend">
                            <span class="add-on">
                                <i class="el el-arrow-left icon-large"></i>
                            </span>
                            <input type="text" 
                                   class="welaunch-spacing-left welaunch-spacing-input mini ' . esc_attr( $this->field['class'] ) . '"
                                   placeholder="' . esc_html__( 'Left', 'welaunch-framework' ) . '"
                                   rel="' . esc_attr( $this->field['id'] ) . '-left"
                                   value="' . esc_attr( $this->value['left'] ) . '"/>
                        </div>';
				}
			}

			/**
			 * Units
			 * */
			if ( false !== $this->field['units'] && true === $this->field['display_units'] ) {
				echo '<div class="select_wrapper spacing-units" original-title="' . esc_html__( 'Units', 'welaunch-framework' ) . '">';
				echo '<select data-placeholder="' . esc_html__( 'Units', 'welaunch-framework' ) . '" class="welaunch-spacing welaunch-spacing-units select ' . esc_attr( $this->field['class'] ) . '" original-title="' . esc_html__( 'Units', 'welaunch-framework' ) . '" id="' . esc_attr( $this->field['id'] ) . '_units"' . esc_attr( $select2_data ) . '>';

				if ( $this->field['units_extended'] ) {
					$test_units = $unit_arr;
				} else {
					$test_units = array( 'px', 'em', 'pt', 'rem', '%' );
				}

				if ( '' !== $this->field['units'] || is_array( $this->field['units'] ) ) {
					$test_units = $this->field['units'];
				}

				echo '<option></option>';

				if ( ! is_array( $this->field['units'] ) ) {
					echo '<option value="' . esc_attr( $this->field['units'] ) . '" selected="selected">' . esc_attr( $this->field['units'] ) . '</option>';
				} else {
					foreach ( $test_units as $a_unit ) {
						echo '<option value="' . esc_attr( $a_unit ) . '" ' . selected( $this->value['units'], $a_unit, false ) . '>' . esc_html( $a_unit ) . '</option>';
					}
				}

				echo '</select></div>';
			}
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since weLaunchFramework 1.0.0
		 */
		public function enqueue() {
			wp_enqueue_style( 'select2-css' );

			wp_enqueue_script(
				'welaunch-field-spacing-js',
				weLaunch_Core::$url . 'inc/fields/spacing/welaunch-spacing' . weLaunch_Functions::is_min() . '.js',
				array( 'jquery', 'select2-js', 'welaunch-js' ),
				$this->timestamp,
				true
			);

			if ( $this->parent->args['dev_mode'] ) {
				wp_enqueue_style(
					'welaunch-field-spacing-css',
					weLaunch_Core::$url . 'inc/fields/spacing/welaunch-spacing.css',
					array(),
					$this->timestamp,
					'all'
				);
			}
		}

		/**
		 * Compile CSS data for output.
		 *
		 * @param string $data CSS data.
		 *
		 * @return string|void
		 */
		public function css_style( $data ) {
			$style = '';
			if ( ! isset( $this->field ) ) {
				return;
			}

			if ( ! in_array( $this->field['mode'], array( 'padding', 'absolute', 'margin' ), true ) ) {
				$this->field['mode'] = 'padding';
			}

			$mode  = ( 'absolute' !== $this->field['mode'] ) ? $this->field['mode'] : '';
			$units = isset( $data['units'] ) ? $data['units'] : '';

			foreach ( $data as $key => $value ) {
				if ( 'units' === $key ) {
					continue;
				}
				$the_units = $units;

				// Strip off any alpha for is_numeric test - kp.
				$num_no_alpha = preg_replace( '/[^\d.-]/', '', $value );
				if ( empty( $the_units ) ) {
					$the_units = str_replace( $num_no_alpha, '', $value );
				}

				// Output if it's a numeric entry.
				if ( isset( $value ) && is_numeric( $num_no_alpha ) ) {
					$style .= $key . ':' . $num_no_alpha . $the_units . ';';
				}
			}

			return $style;
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

class_alias( 'weLaunch_Spacing', 'weLaunchFramework_Spacing' );
