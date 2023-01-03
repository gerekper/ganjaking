<?php
/**
 * Extra Product Options HTML creation class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options HTML creation class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_HTML_Base {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_HTML_Base|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
	}

	/**
	 * Ouputs a fontawesome icon
	 *
	 * @param string          $id The icon identifier.
	 * @param integer|boolean $echo If we should echo the result or not.
	 * @since 1.0
	 */
	public function create_icon( $id = '', $echo = 1 ) {

		ob_start();

		echo wp_kses(
			apply_filters( 'wc_epo_create_icon', "<i class='tcfa tcfa-" . esc_attr( $id ) . "'></i>" ),
			[
				'i' => [ 'class' => true ],
			]
		);

		if ( $echo ) {
			ob_end_flush();
		} else {
			return ob_get_clean();
		}

	}

	/**
	 * Displays attributes list from an array
	 *
	 * @param array $element_data_attr Element data attributes.
	 * @since 1.0
	 */
	public function create_attribute_list( $element_data_attr = [] ) {
		if ( is_array( $element_data_attr ) ) {
			foreach ( $element_data_attr as $k => $v ) {
				if ( 'required' === $k ) {
					echo 'required="required" ';
				} elseif ( 'disabled' === $k ) {
					echo 'disabled="disabled" ';
				} else {
					echo esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '" ';
				}
			}
		}
	}

	/**
	 * Creates a button
	 *
	 * @param array           $args Array of arguments.
	 * @param integer|boolean $echo If the result should be displayed or retuned.
	 * @since 1.0
	 */
	public function create_button( $args = [], $echo = 0 ) {

		if ( empty( $args ) || ! is_array( $args ) ) {
			return;
		}

		if ( ! is_array( $args['tags'] ) ) {
			$args['tags'] = [];
		}

		if ( ! isset( $args['tags']['type'] ) ) {
			$args['tags']['type'] = 'button';
		}

		ob_start();

		if ( 'a' === $args['tags']['type'] ) {
			echo '<a';
		} else {
			echo '<button';
		}

		$this->create_field_attributes( $args['tags'] );

		echo '>';
		if ( isset( $args['icon'] ) ) {
			$this->create_icon( $args['icon'], 1 );
		}
		echo apply_filters( 'wc_epo_kses', wp_kses_post( $args['text'] ), $args['text'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
		if ( 'a' === $args['tags']['type'] ) {
			echo '</a>';
		} else {
			echo '</button>';
		}

		if ( $echo ) {
			ob_end_flush();
		} else {
			return ob_get_clean();
		}

	}

	/**
	 * Creates field attributes
	 *
	 * @param array $atts Array of arguments.
	 * @since 1.0
	 */
	public function create_field_attributes( $atts = [] ) {
		if ( is_array( $atts ) ) {
			foreach ( $atts as $k => $v ) {
				if ( 'required' === $k ) {
					echo ' required="required"';
				} elseif ( 'disabled' === $k ) {
					echo ' disabled="disabled"';
				} else {
					echo ' ' . esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '"';
				}
			}
		}
	}

	/**
	 * Creates a select box
	 *
	 * @param array           $select_array Array of arguments.
	 * @param array           $option_array Array of choices.
	 * @param string          $selected_value The selecte value.
	 * @param integer|boolean $label If the label should be displayed.
	 * @param integer|boolean $echo If the result should be displayed or retuned.
	 * @since 1.0
	 */
	public function create_dropdown( $select_array, $option_array, $selected_value = '/n', $label = 1, $echo = 1 ) {

		if ( ! is_array( $select_array ) ) {
			return '';
		}
		if ( ! is_array( $option_array ) ) {
			return '';
		}

		ob_start();

		if ( ! empty( $select_array['id'] ) && ! empty( $label ) ) {
			echo "<label for='" . esc_attr( $select_array['id'] ) . "'>";
		}

		echo '<select';

		if ( ! empty( $select_array['class'] ) ) {
			echo ' class="' . esc_attr( $select_array['class'] ) . '"';
		}
		if ( ! empty( $select_array['id'] ) ) {
			echo ' id="' . esc_attr( $select_array['id'] ) . '"';
		}
		if ( ! empty( $select_array['name'] ) ) {
			echo ' name="' . esc_attr( $select_array['name'] ) . '"';
		}
		if ( isset( $select_array['size'] ) ) {
			echo ' size="' . esc_attr( $select_array['size'] ) . '"';
		}
		if ( isset( $select_array['multiple'] ) ) {
			echo ' multiple="multiple"';
		}
		if ( ! empty( $select_array['disabled'] ) ) {
			echo ' disabled="disabled"';
		}
		if ( ! empty( $select_array['required'] ) ) {
			echo ' required="required"';
		}
		if ( isset( $select_array['atts'] ) && is_array( $select_array['atts'] ) ) {
			unset( $select_array['atts']['class'] );
			unset( $select_array['atts']['id'] );
			unset( $select_array['atts']['name'] );
			unset( $select_array['atts']['size'] );
			unset( $select_array['atts']['multiple'] );
			unset( $select_array['atts']['disabled'] );
			$this->create_field_attributes( $select_array['atts'] );
		}

		echo '>';

		$count_option_array = count( $option_array );
		for ( $i = 0; $i < $count_option_array; $i ++ ) {
			$sel = false;
			if ( isset( $option_array[ $i ]['selected'] ) && true === $option_array[ $i ]['selected'] ) {
				$sel = true;
			} elseif ( '/n' !== $selected_value && ! is_array( $selected_value ) ) {
				if ( (string) $selected_value === (string) $option_array[ $i ]['value'] ) {
					$sel = true;
				}
			} else {
				if ( is_array( $selected_value ) && in_array( $option_array[ $i ]['value'], $selected_value ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
					$sel = true;
				}
			}

			echo '<option';

			if ( isset( $option_array[ $i ]['title'] ) ) {
				echo ' title="' . esc_attr( $option_array[ $i ]['title'] ) . '"';
			}
			if ( isset( $option_array[ $i ]['id'] ) ) {
				echo ' id="' . esc_attr( $option_array[ $i ]['id'] ) . '"';
			}
			if ( isset( $option_array[ $i ]['class'] ) ) {
				echo ' class="' . esc_attr( $option_array[ $i ]['class'] ) . '"';
			}
			if ( $sel ) {
				echo ' selected="selected"';
			}

			if ( isset( $option_array[ $i ]['atts'] ) && is_array( $option_array[ $i ]['atts'] ) ) {
				$this->create_field_attributes( $option_array[ $i ]['atts'] );
			}

			echo ' value="' . esc_attr( $option_array[ $i ]['value'] ) . '">';
			echo esc_html( wp_strip_all_tags( $option_array[ $i ]['text'] ) );
			echo '</option>';
		}

		echo '</select>';

		if ( ! empty( $select_array['id'] ) && ! empty( $label ) ) {
			echo '</label>';
		}

		if ( $echo ) {
			ob_end_flush();
		} else {
			return ob_get_clean();
		}

	}

	/**
	 * Creates a form field
	 *
	 * @param array           $args Array of arguments.
	 * @param integer|boolean $echo If the result should be displayed or retuned.
	 * @since 1.0
	 */
	public function create_field( $args, $echo = 0 ) {

		if ( ! is_array( $args ) ) {
			return;
		}
		if ( isset( $args['noecho'] ) ) {
			return;
		}

		ob_start();

		$tags = [];
		if ( isset( $args['tags'] ) && is_array( $args['tags'] ) ) {
			if ( isset( $args['type'] ) && ( 'range' === $args['type'] || 'input' === $args['type'] || 'text' === $args['type'] || 'number' === $args['type'] || 'hidden' === $args['type'] ) ) {
				if ( ! isset( $args['default'] ) ) {
					$args['default'] = '';
				}
				$args['tags']['value'] = $args['default'];
				if ( 'number' === $args['type'] ) {
					if ( ! isset( $args['tags']['step'] ) ) {
						$args['tags']['step'] = 'any';
					}
				}
			}
			if ( isset( $args['type'] ) && 'range' === $args['type'] && ! isset( $args['tags']['class'] ) ) {
				$args['tags']['class'] = 'range';
			} elseif ( isset( $args['type'] ) && 'range' === $args['type'] && isset( $args['tags']['class'] ) ) {
				$args['tags']['class'] = 'range ' . $args['tags']['class'];
			}

			$args['tags_original'] = $args['tags'];
			$tags                  = $args['tags'];
		}

		$disabled = false;
		if ( ! empty( $args['disabled'] ) ) {
			$disabled = true;
			if ( isset( $args['message0x0_class'] ) ) {
				$args['message0x0_class'] .= ' tm-setting-row-disabled';
			} else {
				$args['message0x0_class'] = 'tm-setting-row-disabled';
			}
			if ( isset( $args['tags'] ) && is_array( $args['tags'] ) ) {
				if ( isset( $args['tags']['class'] ) ) {
					$args['tags']['class'] .= ' tm-wmpl-disabled';
				} else {
					$args['tags']['class'] = 'tm-wmpl-disabled';
				}
			}
		}

		if ( empty( $args['nodiv'] ) ) {
			if ( empty( $args['nostart'] ) ) {
				if ( empty( $args['nowrap_start'] ) ) {
					echo '<div';
					if ( isset( $args['divid'] ) ) {
						echo ' id="' . esc_attr( $args['divid'] ) . '"';
					}
					if ( isset( $args['required'] ) ) {
						echo ' data-required="' . esc_attr( wp_json_encode( $args['required'] ) ) . '"';
					}
					echo ' class="message0x0 tc-clearfix';
					if ( isset( $args['message0x0_class'] ) ) {
						echo ' ' . esc_attr( $args['message0x0_class'] );
					}
					echo '">';
				}
				if ( ! empty( $args['nowrap_start'] ) && ! empty( $args['noclear'] ) ) {
					echo '<div class="clear">&nbsp;</div>';
				}
				if ( isset( $args['wrap_div'] ) && is_array( $args['wrap_div'] ) ) {
					echo '<div ';
					foreach ( $args['wrap_div'] as $k => $v ) {
						echo ' ' . esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '"';
					}
					echo '>';
				}
				if ( empty( $args['nolabel'] ) && ! empty( $args['label'] ) ) {
					echo '<div class="message2x1';
					if ( ! empty( $args['leftclass'] ) ) {
						echo ' ' . esc_attr( $args['leftclass'] );
					}
					echo '">';
					if ( isset( $args['tags'] ) && isset( $args['tags']['id'] ) ) {
						echo '<label for="' . esc_attr( $args['tags']['id'] ) . ( 'radio' === $args['type'] ? '0' : '' ) . '">';
					}
					echo '<span>' . wp_kses_post( $args['label'] ) . '</span>';
					if ( isset( $args['tags'] ) && isset( $args['tags']['id'] ) ) {
						echo '</label>';
					}
					if ( ! empty( $args['desc'] ) ) {
						echo "<div class='messagexdesc'>";
						echo apply_filters( 'wc_epo_kses', wp_kses_post( $args['desc'] ), $args['desc'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
						echo '</div>';
					}
					echo '</div>';
				}
			} else {
				if ( isset( $args['tags'] ) && isset( $args['tags']['id'] ) && isset( $args['label'] ) ) {
					echo '<label for="' . esc_attr( $args['tags']['id'] ) . '"><span>' . esc_html( $args['label'] ) . '</span></label>';
				}
			}
		}

		if ( empty( $args['nodiv'] ) && empty( $args['nostart'] ) ) {
			echo '<div class="message2x2';
			if ( ! empty( $args['rightclass'] ) ) {
				echo ' ' . esc_attr( $args['rightclass'] );
			}
			echo '">';
		}
		if ( isset( $args['prepend_element_html'] ) ) {
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $args['prepend_element_html'] ), $args['prepend_element_html'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( ! empty( $args['html_before_field'] ) ) {
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $args['html_before_field'] ), $args['html_before_field'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( isset( $args['type'] ) ) {
			switch ( $args['type'] ) {
				case 'div':
					echo '<div';
					$this->create_field_attributes( $tags );
					echo '>';
					if ( isset( $args['html'] ) ) {
						echo apply_filters( 'wc_epo_kses', wp_kses_post( $args['html'] ), $args['html'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					echo '</div>';
					break;
				case 'custom':
				case 'custom_multiple':
					if ( isset( $args['html'] ) ) {
						if ( is_array( $args['html'] ) ) {
							$method     = $args['html'][0];
							$methodargs = $args['html'][1];
							call_user_func_array( $method, $methodargs );
						} else {
							echo apply_filters( 'wc_epo_kses', wp_kses_post( $args['html'] ), $args['html'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
						}
					}
					break;
				case 'hidden':
				case 'text':
				case 'number':
				case 'file':
					echo '<input';
					disabled( $disabled, true );
					echo ' type="' . esc_attr( $args['type'] ) . '"';
					$this->create_field_attributes( $tags );
					echo '>';
					break;
				case 'input':
					if ( isset( $args['input_type'] ) ) {
						echo '<input';
						disabled( $disabled, true );
						echo ' type="' . esc_attr( $args['input_type'] ) . '"';
						$this->create_field_attributes( $tags );
						echo '>';
					}
					break;
				case 'range':
					echo '<div class="rangewrapper">';
					echo '<input';
					disabled( $disabled, true );
					echo ' type="text"';
					$this->create_field_attributes( $tags );
					echo '>';
					echo '</div>';
					break;
				case 'textarea':
					echo '<textarea';
					disabled( $disabled, true );
					$this->create_field_attributes( $tags );
					echo '>' . esc_textarea( $args['default'] ) . '</textarea>';
					break;
				case 'checkbox':
					echo '<input';
					disabled( $disabled, true );
					echo ' type="checkbox"';
					$this->create_field_attributes( $tags );
					checked( ( (string) $args['default'] === (string) $args['tags']['value'] ), true );
					echo '>';
					break;
				case 'radio':
					$tags_original = $tags;
					foreach ( $args['options'] as $tx => $vl ) {
						echo '<input';
						disabled( $disabled, true );
						checked( ( (string) $args['default'] === (string) $vl['value'] ), true );
						$tags          = $tags_original;
						$tags['id']    = $tags_original['id'] . $tx;
						$tags['value'] = $vl['value'];
						$this->create_field_attributes( $tags );
						echo ' type="radio">';
						echo "<label for='" . esc_attr( $args['tags']['id'] ) . esc_attr( $tx ) . "'><span class='tc-radio-text'>" . esc_html( $vl['text'] ) . '</span></label>';
					}
					break;
				case 'select':
					$select_array = [
						'class'    => isset( $args['tags']['class'] ) ? $args['tags']['class'] : '',
						'id'       => $args['tags']['id'],
						'name'     => $args['tags']['name'],
						'disabled' => ! empty( $disabled ),
						'atts'     => $args['tags'],
					];
					if ( isset( $args['multiple'] ) ) {
						$select_array['multiple'] = $args['multiple'];
					}
					if ( isset( $args['size'] ) ) {
						$select_array['size'] = $args['size'];
					}
					$this->create_dropdown( $select_array, $args['options'], $args['default'], 0, 1 );
					break;
				case 'select2':
					$select_array = [
						'class'    => $args['tags']['class'],
						'id'       => $args['tags']['id'],
						'name'     => $args['tags']['name'],
						'disabled' => ! empty( $disabled ),
						'atts'     => $args['tags'],
					];

					if ( isset( $args['multiple'] ) ) {
						$select_array['multiple'] = $args['multiple'];
					}
					if ( isset( $args['size'] ) ) {
						$select_array['size'] = $args['size'];
					}
					$this->create_dropdown( $select_array, $args['options'], $args['default'], 1, 1 );
					break;
				case 'select3':
					$select_array = [
						'class'    => isset( $args['tags']['class'] ) ? $args['tags']['class'] : '',
						'id'       => $args['tags']['id'],
						'name'     => $args['tags']['name'],
						'disabled' => ! empty( $disabled ),
						'atts'     => $args['tags'],
					];
					if ( isset( $args['multiple'] ) ) {
						$select_array['multiple'] = $args['multiple'];
					}
					if ( isset( $args['size'] ) ) {
						$select_array['size'] = $args['size'];
					}
					echo '<span class="select_wrap' . ( isset( $args['multiple'] ) ? ' multiple' : '' ) . '"><span class="select_style">';
					$this->create_dropdown( $select_array, $args['options'], $args['default'], 0, 1 );
					echo '<span class="select_value"></span><span class="select_icon"></span></span></span>';
					break;
			}
		}
		if ( ! empty( $args['html_after_field'] ) ) {
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $args['html_after_field'] ), $args['html_after_field'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( isset( $args['extra'] ) ) {
			if ( is_array( $args['extra'] ) ) {
				$method     = $args['extra'][0];
				$methodargs = $args['extra'][1];
				call_user_func_array( $method, $methodargs );
			} else {
				echo apply_filters( 'wc_epo_kses', wp_kses_post( $args['extra'] ), $args['extra'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}
		if ( empty( $args['nodiv'] ) && empty( $args['noend'] ) ) {
			echo '</div>';
			if ( isset( $args['extra_fields'] ) && is_array( $args['extra_fields'] ) ) {
				foreach ( $args['extra_fields'] as $k => $extra_field ) {
					echo '<div class="message2x' . esc_attr( $k + 3 ) . '">';
					$this->create_field( $extra_field, true );
					echo '</div>';
				}
			}
			if ( isset( $args['wrap_div'] ) && is_array( $args['wrap_div'] ) ) {
				echo '</div>';
			}
			if ( empty( $args['nowrap_end'] ) ) {
				echo '</div>';
			}
		}

		if ( $echo ) {
			ob_end_flush();
		} else {
			return ob_get_clean();
		}
	}

}
