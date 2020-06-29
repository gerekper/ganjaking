<?php
/**
 * Extra Product Options HTML creation class
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_HTML_base {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
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
	 * @since 1.0
	 */
	public function tm_icon( $id = "", $echo = 1 ) {

		ob_start();

		echo apply_filters( 'wc_epo_tm_icon', "<i class='tcfa tcfa-" . esc_attr( $id ) . "'></i>" );

		if ( $echo ) {
			ob_end_flush();
		} else {
			return ob_get_clean();
		}

	}

	/**
	 * Displays attributes list from an array
	 *
	 * @since 1.0
	 */
	public function create_attribute_list( $element_data_attr = array() ) {
		if ( is_array( $element_data_attr ) ) {
			foreach ( $element_data_attr as $k => $v ) {
				echo esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '" ';
			}
		}
	}

	/**
	 * Creates a button
	 *
	 * @since 1.0
	 */
	public function tm_make_button( $args, $echo = 0 ) {

		if ( empty( $args ) || ! is_array( $args ) ) {
			return;
		}

		ob_start();

		echo "<button";

		if ( is_array( $args["tags"] ) ) {
			if ( ! isset( $args["tags"]['type'] ) ) {
				$args["tags"]['type'] = "button";
			}
			foreach ( $args["tags"] as $k => $v ) {
				echo ' ' . esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '"';
			}
		}

		echo ">";
		if ( isset( $args["icon"] ) ) {
			$this->tm_icon( $args["icon"], 1 );
		}
		echo esc_html( $args["text"] );
		echo "</button>";

		if ( $echo ) {
			ob_end_flush();
		} else {
			return ob_get_clean();
		}

	}

	/**
	 * Creates tag attributes
	 *
	 * @since 1.0
	 */
	public function tm_make_tags( $atts = array() ) {

		if ( is_array( $atts ) ) {
			foreach ( $atts as $k => $v ) {
				echo ' ' . esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '"';
			}
		}

	}

	/**
	 * Creates a select box
	 *
	 * @since 1.0
	 */
	public function tm_make_select( $selectArray, $optionArray, $selectedvalue = "/n", $label = 1, $echo = 1 ) {

		if ( ! is_array( $selectArray ) ) {
			return "";
		}
		if ( ! is_array( $optionArray ) ) {
			return "";
		}

		ob_start();

		if ( ! empty( $selectArray['id'] ) && ! empty( $label ) ) {
			echo "<label for='" . esc_attr( $selectArray['id'] ) . "'>";
		}

		echo "<select";

		if ( ! empty( $selectArray['class'] ) ) {
			echo ' class="' . esc_attr( $selectArray['class'] ) . '"';
		}
		if ( ! empty( $selectArray['id'] ) ) {
			echo ' id="' . esc_attr( $selectArray['id'] ) . '"';
		}
		if ( ! empty( $selectArray['name'] ) ) {
			echo ' name="' . esc_attr( $selectArray['name'] ) . '"';
		}
		if ( isset( $selectArray['size'] ) ) {
			echo ' size="' . esc_attr( $selectArray['size'] ) . '"';
		}
		if ( isset( $selectArray['multiple'] ) ) {
			echo ' multiple="multiple"';
		}
		if ( ! empty( $selectArray['disabled'] ) ) {
			echo ' disabled="disabled"';
		}
		if ( isset( $selectArray['atts'] ) && is_array( $selectArray['atts'] ) ) {
			unset( $selectArray['atts']['class'] );
			unset( $selectArray['atts']['id'] );
			unset( $selectArray['atts']['name'] );
			unset( $selectArray['atts']['size'] );
			unset( $selectArray['atts']['multiple'] );
			unset( $selectArray['atts']['disabled'] );
			$this->tm_make_tags( $selectArray['atts'] );
		}

		echo ">";

		for ( $i = 0; $i < count( $optionArray ); $i ++ ) {
			$sel = FALSE;
			if ( $selectedvalue != "/n" && ! is_array( $selectedvalue ) ) {
				if ( $selectedvalue == $optionArray[ $i ]['value'] ) {
					$sel = TRUE;
				}
			} else {
				if ( is_array( $selectedvalue ) && in_array( $optionArray[ $i ]['value'], $selectedvalue ) ) {
					$sel = TRUE;
				}
			}

			echo "<option";

			if ( isset( $optionArray[ $i ]['title'] ) ) {
				echo ' title="' . esc_attr( $optionArray[ $i ]['title'] ) . '"';
			}
			if ( isset( $optionArray[ $i ]['id'] ) ) {
				echo ' id="' . esc_attr( $optionArray[ $i ]['id'] ) . '"';
			}
			if ( isset( $optionArray[ $i ]['class'] ) ) {
				echo ' class="' . esc_attr( $optionArray[ $i ]['class'] ) . '"';
			}
			if ( $sel ) {
				echo ' selected="selected"';
			}

			echo ' value="' . esc_attr( $optionArray[ $i ]['value'] ) . '">';
			echo esc_html( $optionArray[ $i ]['text'] );
			echo "</option>";
		}

		echo "</select>";

		if ( ! empty( $selectArray['id'] ) && ! empty( $label ) ) {
			echo "</label>";
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
	 * @since 1.0
	 */
	public function tm_make_field( $args, $echo = 0 ) {

		if ( ! is_array( $args ) ) {
			return;
		}
		if ( isset( $args["noecho"] ) ) {
			return;
		}

		ob_start();

		$tags = array();
		if ( isset( $args["tags"] ) && is_array( $args["tags"] ) ) {
			if ( isset( $args["type"] ) && ( $args["type"] == "range" || $args["type"] == "text" || $args["type"] == "number" || $args["type"] == "hidden" ) ) {
				$args["tags"]["value"] = $args["default"];
				if ( $args["type"] == "number" ) {
					if ( ! isset( $args["tags"]["step"] ) ) {
						$args["tags"]["step"] = "any";
					}
				}
			}
			if ( isset( $args["type"] ) && $args["type"] == "range" && ! isset( $args["tags"]["class"] ) ) {
				$args["tags"]["class"] = "range";
			} elseif ( isset( $args["type"] ) && $args["type"] == "range" && isset( $args["tags"]["class"] ) ) {
				$args["tags"]["class"] = "range " . $args["tags"]["class"];
			}

			$args["tags_original"] = $args["tags"];
			$tags                  = $args["tags"];
		}

		if ( ! empty( $args["disabled"] ) ) {
			if ( isset( $args["message0x0_class"] ) ) {
				$args["message0x0_class"] .= ' tm-setting-row-disabled';
			} else {
				$args["message0x0_class"] = 'tm-setting-row-disabled';
			}
			if ( isset( $args["tags"] ) && is_array( $args["tags"] ) ) {
				if ( isset( $args["tags"]["class"] ) ) {
					$args["tags"]["class"] .= ' tm-wmpl-disabled';
				} else {
					$args["tags"]["class"] = 'tm-wmpl-disabled';
				}
			}
		}

		if ( empty( $args["nodiv"] ) ) {
			if ( empty( $args["nostart"] ) ) {
				if ( empty( $args["nowrap_start"] ) ) {
					echo '<div';
					if ( isset( $args["divid"] ) ) {
						echo ' id="' . esc_attr( $args["divid"] ) . '"';
					}
					if ( isset( $args["required"] ) ) {
						echo ' data-required="' . esc_attr( json_encode( $args["required"] ) ) . '"';
					}
					echo ' class="message0x0 tc-clearfix';
					if ( isset( $args["message0x0_class"] ) ) {
						echo " " . esc_attr( $args["message0x0_class"] );
					}
					echo '">';
				}
				if ( ! empty( $args["nowrap_start"] ) && ! empty( $args["noclear"] ) ) {
					echo '<div class="clear">&nbsp;</div>';
				}
				if ( isset( $args["wrap_div"] ) && is_array( $args["wrap_div"] ) ) {
					echo '<div ';
					foreach ( $args["wrap_div"] as $k => $v ) {
						echo ' ' . esc_html( sanitize_key( $k ) ) . '="' . esc_attr( $v ) . '"';
					}
					echo '>';
				}
				if ( empty( $args["nolabel"] ) && ! empty( $args["label"] ) ) {
					echo '<div class="message2x1';
					if ( ! empty( $args["leftclass"] ) ) {
						echo " " . esc_attr( $args["leftclass"] );
					}
					echo '">';
					if ( isset( $args["tags"] ) && isset( $args["tags"]["id"] ) ) {
						echo '<label for="' . esc_attr( $args["tags"]["id"] ) . ( $args["type"] === "radio" ? "0" : "" ) . '">';
					}
					echo '<span>' . wp_kses_post( $args["label"] ) . '</span>';
					if ( isset( $args["tags"] ) && isset( $args["tags"]["id"] ) ) {
						echo '</label>';
					}
					if ( ! empty( $args["desc"] ) ) {
						echo "<div class='messagexdesc'>";
						echo apply_filters( 'wc_epo_kses', wp_kses_post( $args["desc"] ), $args["desc"], FALSE );
						echo "</div>";
					}
					echo '</div>';
				}
			} else {
				if ( isset( $args["tags"] ) && isset( $args["tags"]["id"] ) && isset( $args["label"] ) ) {
					echo '<label for="' . esc_attr( $args["tags"]["id"] ) . '"><span>' . esc_html( $args["label"] ) . '</span></label>';
				}
			}
		}

		if ( empty( $args["nodiv"] ) && empty( $args["nostart"] ) ) {
			echo '<div class="message2x2';
			if ( ! empty( $args["rightclass"] ) ) {
				echo " " . esc_attr( $args["rightclass"] );
			}
			echo '">';
		}
		if ( isset( $args["prepend_element_html"] ) ) {
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $args["prepend_element_html"] ), $args["prepend_element_html"], FALSE );
		}
		$disabled = FALSE;
		if ( ! empty( $args["disabled"] ) ) {
			$disabled = TRUE;
		}
		if ( ! empty( $args["html_before_field"] ) ) {
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $args["html_before_field"] ), $args["html_before_field"], FALSE );
		}
		if ( isset( $args["type"] ) ) {
			switch ( $args["type"] ) {
				case "custom":
				case "custom_multiple":
					if ( isset( $args["html"] ) ) {
						if ( is_array( $args["html"] ) ) {
							$method     = $args["html"][0];
							$methodargs = $args["html"][1];
							call_user_func_array( $method, $methodargs );
						} else {
							echo apply_filters( 'wc_epo_kses', wp_kses_post( $args["html"] ), $args["html"], FALSE );
						}
					}
					break;
				case "hidden":
				case "text":
				case "number":
					echo "<input";
					disabled( $disabled, TRUE );
					echo ' type="' . esc_attr( $args["type"] ) . '"';
					$this->tm_make_tags( $tags );
					echo ' />';
					break;
				case "range":
					echo '<div class="rangewrapper">';
					echo '<input';
					disabled( $disabled, TRUE );
					echo ' type="text"';
					$this->tm_make_tags( $tags );
					echo ' />';
					echo '</div>';
					break;

				case "textarea":
					echo "<textarea";
					disabled( $disabled, TRUE );
					$this->tm_make_tags( $tags );
					echo " >" . esc_textarea( $args["default"] ) . "</textarea>";
					break;
				case "checkbox":
					echo "<input";
					disabled( $disabled, TRUE );
					echo ' type="checkbox"';
					$this->tm_make_tags( $tags );
					checked( ( $args["default"] == $args["tags"]["value"] ), TRUE );
					echo ' />';
					break;
				case "radio":
					$tags_original = $tags;
					foreach ( $args["options"] as $tx => $vl ) {
						echo '<input';
						disabled( $disabled, TRUE );
						checked( ( $args["default"] == $vl["value"] ), TRUE );
						$tags          = $tags_original;
						$tags["id"]    = $tags_original["id"] . $tx;
						$tags["value"] = $vl["value"];
						$this->tm_make_tags( $tags );
						echo ' type="radio" />';
						echo "<label for='" . esc_attr( $args["tags"]["id"] ) . esc_attr( $tx ) . "'><span class='tc-radio-text'>" . esc_html( $vl["text"] ) . '</span></label>';
					}
					break;
				case "select":
					$selectArray = array(
						"class"    => isset( $args["tags"]["class"] ) ? $args["tags"]["class"] : "",
						"id"       => $args["tags"]["id"],
						"name"     => $args["tags"]["name"],
						"disabled" => ! empty( $disabled ),
						"atts"     => $args["tags"],
					);
					if ( isset( $args["multiple"] ) ) {
						$selectArray["multiple"] = $args["multiple"];
					}
					if ( isset( $args["size"] ) ) {
						$selectArray["size"] = $args["size"];
					}
					$this->tm_make_select( $selectArray, $args["options"], $args["default"], 0, 1 );
					break;
				case "select2":
					$selectArray = array(
						"class"    => $args["tags"]["class"],
						"id"       => $args["tags"]["id"],
						"name"     => $args["tags"]["name"],
						"disabled" => ! empty( $disabled ),
						"atts"     => $args["tags"],
					);

					if ( isset( $args["multiple"] ) ) {
						$selectArray["multiple"] = $args["multiple"];
					}
					if ( isset( $args["size"] ) ) {
						$selectArray["size"] = $args["size"];
					}
					$this->tm_make_select( $selectArray, $args["options"], $args["default"], 1, 1 );
					break;
				case "select3":
					$selectArray = array(
						"class"    => isset( $args["tags"]["class"] ) ? $args["tags"]["class"] : "",
						"id"       => $args["tags"]["id"],
						"name"     => $args["tags"]["name"],
						"disabled" => ! empty( $disabled ),
						"atts"     => $args["tags"],
					);
					if ( isset( $args["multiple"] ) ) {
						$selectArray["multiple"] = $args["multiple"];
					}
					if ( isset( $args["size"] ) ) {
						$selectArray["size"] = $args["size"];
					}
					echo '<span class="select_wrap' . ( isset( $args["multiple"] ) ? ' multiple' : '' ) . '"><span class="select_style">';
					$this->tm_make_select( $selectArray, $args["options"], $args["default"], 0, 1 );
					echo '<span class="select_value"></span><span class="select_icon"></span></span></span>';
					break;
			}
		}
		if ( ! empty( $args["html_after_field"] ) ) {
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $args["html_after_field"] ), $args["html_after_field"], FALSE );
		}
		if ( isset( $args["extra"] ) ) {
			if ( is_array( $args["extra"] ) ) {
				$method     = $args["extra"][0];
				$methodargs = $args["extra"][1];
				call_user_func_array( $method, $methodargs );
			} else {
				echo apply_filters( 'wc_epo_kses', wp_kses_post( $args["extra"] ), $args["extra"], FALSE );
			}
		}
		if ( empty( $args["nodiv"] ) && empty( $args["noend"] ) ) {
			echo "</div>";
			if ( isset( $args["wrap_div"] ) && is_array( $args["wrap_div"] ) ) {
				echo "</div>";
			}
			if ( empty( $args["nowrap_end"] ) ) {
				echo "</div>";
			}
		}

		if ( $echo ) {
			ob_end_flush();
		} else {
			return ob_get_clean();
		}
	}

}


