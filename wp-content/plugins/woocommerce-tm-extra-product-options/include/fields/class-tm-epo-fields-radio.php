<?php
/**
 * Radio Buttons Field class
 *
 * @package Extra Product Options/Fields
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_FIELDS_radio extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Pre display field array
	 *
	 * @since 1.0
	 */
	public function display_field_pre( $element = array(), $args = array() ) {
		$this->items_per_row   = $element['items_per_row'];
		$this->items_per_row_r = isset( $element['items_per_row_r'] ) ? $element['items_per_row_r'] : array();
		$this->grid_break      = "";
		$this->_percent        = 100;
		$this->_columns        = 0;
		$container_css_id      = 'element_';
		if ( isset( $element['container_css_id'] ) ) {
			$container_css_id = $element['container_css_id'];
		}
		if ( ! isset( $args['product_id'] ) ) {
			$args['product_id'] = '';
		}

		if ( ! empty( $this->items_per_row ) ) {
			if ( $this->items_per_row == "auto" || ! is_numeric( $this->items_per_row ) || floatval( $this->items_per_row ) === 0 ) {
				$this->items_per_row = 0;
				$css_string          = ".tm-product-id-" . $args['product_id'] . " ." . $container_css_id . $args['element_counter'] . $args["form_prefix"] . " li{float:" . THEMECOMPLETE_EPO_DISPLAY()->float_direction . " !important;width:auto !important;}";
			} else {
				$this->items_per_row = (float) $this->items_per_row;
				$this->_percent      = (float) ( 100 / $this->items_per_row );
				$css_string          = ".tm-product-id-" . $args['product_id'] . " ." . $container_css_id . $args['element_counter'] . $args["form_prefix"] . " li{float:" . THEMECOMPLETE_EPO_DISPLAY()->float_direction . " !important;width:" . $this->_percent . "% !important;}";
			}

			$css_string = str_replace( array( "\r", "\n" ), "", $css_string );
			THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );
		} else {
			$this->items_per_row = (float) $element['items_per_row'];
		}

		foreach ( $this->items_per_row_r as $key => $value ) {
			$before        = "";
			$after         = "}";
			$disable_clear = FALSE;
			if ( ! empty( $value ) ) {
				if ( $key == "desktop" ) {
					$before     = "";
					$after      = "";
					$css_string = $before . ".tm-product-id-" . $args['product_id'] . " ." . $container_css_id . $args['element_counter'] . $args["form_prefix"] . " li:nth-child(n){clear:none !important;}" . $after;
					$css_string .= $before . ".tm-product-id-" . $args['product_id'] . " ." . $container_css_id . $args['element_counter'] . $args["form_prefix"] . " li:nth-child(" . ( intval( $value ) ) . "n+1){clear:both !important;}" . $after;
					$css_string = str_replace( array( "\r", "\n" ), "", $css_string );
					THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );

				} else {
					$disable_clear = TRUE;
					switch ( $key ) {
						case 'tablets_galaxy'://800-1280
							$before = "@media only screen and (min-device-width : 800px) and (max-device-width : 1280px),only screen and (min-width : 800px) and (max-width : 1280px) {";
							break;
						case 'tablets'://768-1024
							$before = "@media only screen and (min-device-width : 768px) and (max-device-width : 1024px),only screen and (min-width : 768px) and (max-width : 1024px) {";
							break;
						case 'tablets_small'://481-767
							$before = "@media only screen and (min-device-width : 481px) and (max-device-width : 767px),only screen and (min-width : 481px) and (max-width : 767px) {";
							break;
						case 'iphone6_plus'://414-736
							$before = "@media only screen and (min-device-width: 414px) and (max-device-width: 736px) and (-webkit-min-device-pixel-ratio: 2),only screen and (min-width: 414px) and (max--width: 736px) {";
							break;
						case 'iphone6'://375-667
							$before = "@media only screen and (min-device-width: 375px) and (max-device-width: 667px) and (-webkit-min-device-pixel-ratio: 2),only screen and (min-width: 375px) and (max-width: 667px) {";
							break;
						case 'galaxy'://320-640
							$before = "@media only screen and (device-width: 320px) and (device-height: 640px) and (-webkit-min-device-pixel-ratio: 2),only screen and (width: 320px) and (height: 640px) {";
							break;
						case 'iphone5'://320-568
							$before = "@media only screen and (min-device-width: 320px) and (max-device-width: 568px) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 320px) and (max-width: 568px) {";
							break;
						case 'smartphones'://320-480
							$before = "@media only screen and (min-device-width : 320px) and (max-device-width : 480px), only screen and (min-width : 320px) and (max-width : 480px),, only screen and (max-width : 319px){";
							break;

						default:
							# code...
							break;
					}

					$thisitems_per_row = (float) $value;
					$this_percent      = (float) ( 100 / $thisitems_per_row );
					$css_string        = $before . ".tm-product-id-" . $args['product_id'] . " ." . $container_css_id . $args['element_counter'] . $args["form_prefix"] . " li{float:" . THEMECOMPLETE_EPO_DISPLAY()->float_direction . " !important;width:" . $this_percent . "% !important;}" . $after;

					$css_string = str_replace( array( "\r", "\n" ), "", $css_string );
					THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );

					if ( $disable_clear ) {
						$css_string = $before . ".tm-product-id-" . $args['product_id'] . " ." . $container_css_id . $args['element_counter'] . $args["form_prefix"] . " li:nth-child(n){clear:none !important;}" . $after;
						$css_string .= $before . ".tm-product-id-" . $args['product_id'] . " ." . $container_css_id . $args['element_counter'] . $args["form_prefix"] . " li:nth-child(" . ( intval( $value ) ) . "n+1){clear:both !important;}" . $after;
						$css_string = str_replace( array( "\r", "\n" ), "", $css_string );
						THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );
					}
				}

			}
		}

		$this->_default_value_counter = 0;
	}

	/**
	 * Display field array
	 *
	 * @since 1.0
	 */
	public function display_field( $element = array(), $args = array() ) {
		$this->_columns ++;
		$this->grid_break = "";
		$default_value    = isset( $element['default_value'] ) ? ( ( $element['default_value'] !== "" ) ? ( (int) $element['default_value'] == $this->_default_value_counter ) : FALSE ) : FALSE;

		if ( (float) $this->_columns > (float) $this->items_per_row && $this->items_per_row > 0 ) {
			$this->_columns = 1;
		}

		$hexclass         = "";
		$li_class         = "";
		$search_for_color = $args['label'];
		if ( isset( $element['color'] ) ) {
			if ( ! is_array( $element['color'] ) ) {
				$search_for_color = $element['color'];
			} else {
				if ( isset( $element['color'][ $this->_default_value_counter ] ) ) {
					$search_for_color = $element['color'][ $this->_default_value_counter ];
				}
			}
			if ( empty( $search_for_color ) ) {
				$search_for_color = 'transparent';
			}
		}
		
		$unique_indentifier = $args['element_counter'] . "-" . $args['field_counter'] . "-" . $args['tabindex'] . $args['form_prefix'] . uniqid();
		
		if ( ( ! empty( $element['use_colors'] ) || ! empty( $element['use_images'] ) ) && ( $search_for_color === 'transparent' || preg_match( '/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $search_for_color ) ) ) {			
			$tmhexcolor   = 'tmhexcolor_' . $unique_indentifier;
			$litmhexcolor = 'tm-li-unique-' . $unique_indentifier;
			$hexclass     = $tmhexcolor;
			$css_string   = "." . $tmhexcolor . " .tmhexcolorimage{background-color:" . $search_for_color . " !important;}";
			if ( ! empty( $element['item_width'] ) ) {
				if ( is_numeric( $element['item_width'] ) ) {
					$element['item_width'] .= "px";
				}
				$css_string .= "." . $litmhexcolor . " label{display: inline-block !important;width:" . $element['item_width'] . " !important;min-width:" . $element['item_width'] . " !important;max-width:" . $element['item_width'] . " !important;}";
				$css_string .= "." . $tmhexcolor . " img{display: inline-block !important;width:" . $element['item_width'] . " !important;min-width:" . $element['item_width'] . " !important;max-width:" . $element['item_width'] . " !important;}";
				$css_string .= "." . $tmhexcolor . " .tmhexcolorimage{display: inline-block !important;width:" . $element['item_width'] . " !important;min-width:" . $element['item_width'] . " !important;max-width:" . $element['item_width'] . " !important;}";
			}
			if ( ! empty( $element['item_height'] ) ) {
				if ( is_numeric( $element['item_height'] ) ) {
					$element['item_height'] .= "px";
				}
				$css_string .= "." . $litmhexcolor . " label{display: inline-block !important;height:" . $element['item_height'] . " !important;min-height:" . $element['item_height'] . " !important;max-height:" . $element['item_height'] . " !important;}";
				$css_string .= "." . $tmhexcolor . " img{display: inline-block !important;height:" . $element['item_height'] . " !important;min-height:" . $element['item_height'] . " !important;max-height:" . $element['item_height'] . " !important;}";
				$css_string .= "." . $tmhexcolor . " .tmhexcolorimage{display: inline-block !important;height:" . $element['item_height'] . " !important;min-height:" . $element['item_height'] . " !important;max-height:" . $element['item_height'] . " !important;}";
			}
			if ( ! empty( $element['item_width'] ) || ! empty( $element['item_height'] ) ) {
				$css_string .= ".tmhexcolorimage-li.tm-li-unique-" . $unique_indentifier . "{display: inline-block;width:auto !important;overflow:hidden;}";
				$li_class   .= "tmhexcolorimage-li tm-li-unique-" . $unique_indentifier;
			} else {
				$li_class .= "tmhexcolorimage-li-nowh";
			}
			$css_string = str_replace( array( "\r", "\n" ), "", $css_string );
			THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );
		}

		$_css_class = ! empty( $element['class'] ) ? $element['class'] . ' ' . $hexclass : "" . $hexclass;
		$css_class  = apply_filters( 'wc_epo_multiple_options_css_class', '', $element, $this->_default_value_counter );
		if ( $css_class !== '' ) {
			$css_class = ' ' . $css_class;
		}
		$css_class = $_css_class . $css_class;

		$use = "";

		if ( ! empty( $element['use_colors'] ) && empty( $element['use_images'] ) ) {
			$element['use_images'] = $element['use_colors'];
			if ( $element['use_images'] == 'color' ) {
				$element['use_images'] = 'images';
			}
		}

		if ( ! empty( $element['use_images'] ) && $element['use_images'] === 'images' ) {
			$use = " use_images";
		}

		$image                 = isset( $element['images'][ $args['field_counter'] ] ) ? $element['images'][ $args['field_counter'] ] : "";
		$imagec                = isset( $element['imagesc'][ $args['field_counter'] ] ) ? $element['imagesc'][ $args['field_counter'] ] : "";
		$imagep                = isset( $element['imagesp'][ $args['field_counter'] ] ) ? $element['imagesp'][ $args['field_counter'] ] : "";
		$imagel                = isset( $element['imagesl'][ $args['field_counter'] ] ) ? $element['imagesl'][ $args['field_counter'] ] : "";
		$label                 = wptexturize( apply_filters( 'woocommerce_tm_epo_option_name', $args['label'], $element, $this->_default_value_counter ) );
		$label_mode            = '';
		$changes_product_image = empty( $element['changes_product_image'] ) ? "" : $element['changes_product_image'];

		$url = isset( $element['url'][ $args['field_counter'] ] ) ? $element['url'][ $args['field_counter'] ] : "";

		if ( empty( $image ) ) {
			$image = '';
		}
		if ( empty( $imagec ) ) {
			$imagec = '';
		}
		if ( empty( $imagep ) || empty( $changes_product_image ) ) {
			$imagep = '';
		}
		if ( ! empty( $changes_product_image ) && $changes_product_image == "images" ) {
			$imagep = '';
		}
		if ( empty( $imagel ) ) {
			$imagel = '';
		}


		$selected_value = '';
		$name           = $args['name'];
		if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $this->post_data[ $name ] ) ) {
			$selected_value = $this->post_data[ $name ];
		} elseif ( isset( $_GET[ $name ] ) ) {
			$selected_value = $_GET[ $name ];
		} elseif ( ( empty( $this->post_data ) || ( isset( $this->post_data['action'] ) && $this->post_data['action'] === 'wc_epo_get_associated_product_html' ) ) || ! isset( $this->post_data[ $name ] ) || THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "yes" ) {
			$selected_value = - 1;
		}

		$selected_value = apply_filters( 'wc_epo_default_value', $selected_value, $element, $args['value'] );

		$checked = FALSE;

		if ( $selected_value == - 1 ) {
			if ( empty( $this->post_data ) && isset( $default_value ) ) {
				if ( $default_value ) {
					$checked = TRUE;
				}
			}
			if ( ( ( isset( $this->post_data['action'] ) && $this->post_data['action'] === 'wc_epo_get_associated_product_html' ) || ! isset( $this->post_data[ $name ] ) || THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "yes" ) && isset( $default_value ) ) {
				if ( $default_value && ! THEMECOMPLETE_EPO()->is_edit_mode() ) {
					$checked = TRUE;
				}
			}
		} else {
			if ( ! THEMECOMPLETE_EPO()->is_edit_mode() && isset( $element ) && ! empty( $default_value ) && ! empty( $element['default_value_override'] ) && isset( $element['default_value'] ) ) {
				$checked = TRUE;
			} elseif ( esc_attr( stripcslashes( $selected_value ) ) == esc_attr( ( $args['value'] ) ) ) {
				$checked = TRUE;
			}
		}

		$use_images          = $element['use_images'];
		$swatchmode          = empty( $element['swatchmode'] ) || $use_images !== "images" ? "" : $element['swatchmode'];
		$use_colors          = isset( $element['use_colors'] ) ? $element['use_colors'] : '';
		$use_lightbox        = isset( $element['use_lightbox'] ) ? $element['use_lightbox'] : "";
		$choice_counter      = $this->_default_value_counter;
		$show_label          = empty( $element['show_label'] ) ? "" : $element['show_label'];
		$tm_epo_no_lazy_load = THEMECOMPLETE_EPO()->tm_epo_no_lazy_load;
		if ( isset( $element['color'] ) ) {
			$color = $element['color'];
		}
		if ( ! isset( $args['border_type'] ) ) {
			$border_type = "";
		} else {
			$border_type = $args['border_type'];
		}

		$swatch       = array();
		$swatch_class = "";
		$altsrc       = array();

		$label_to_display = $label;

		if ( ! empty( $use_images ) ) {

			if ( $swatchmode == 'swatch' ) {
				$swatch_class = " tm-tooltip";
				$swatch[]     = array( 'data-tm-tooltip-swatch' => 'on' );
			} elseif ( $swatchmode == 'swatch_desc' ) {
				$swatch_class = " tm-tooltip";
				$swatch[]     = array( 'data-tm-tooltip-swatch-desc' => 'on' );
			} elseif ( $swatchmode == 'swatch_lbl_desc' ) {
				$swatch_class = " tm-tooltip";
				$swatch[]     = array( 'data-tm-tooltip-swatch-lbl-desc' => 'on' );
			} elseif ( $swatchmode == 'swatch_img' ) {
				$swatch_class = " tm-tooltip";
				$swatch[]     = array( 'data-tm-tooltip-swatch-img' => 'on' );
			} elseif ( $swatchmode == 'swatch_img_lbl' ) {
				$swatch_class = " tm-tooltip";
				$swatch[]     = array( 'data-tm-tooltip-swatch-img-lbl' => 'on' );
			} elseif ( $swatchmode == 'swatch_img_desc' ) {
				$swatch_class = " tm-tooltip";
				$swatch[]     = array( 'data-tm-tooltip-swatch-img-desc' => 'on' );
			} elseif ( $swatchmode == 'swatch_img_lbl_desc' ) {
				$swatch_class = " tm-tooltip";
				$swatch[]     = array( 'data-tm-tooltip-swatch-img-lbl-desc' => 'on' );
			}

			if ( empty( $use_colors ) && ! empty( $image ) ) {

				if ( $tm_epo_no_lazy_load == 'no' ) {
					if ( $checked && ! empty( $imagec ) ) {
						$altsrc = array( 'src' => '', 'data-original' => $imagec );
					} else {
						$altsrc = array( 'src' => '', 'data-original' => $image );
					}
				} else {
					if ( $checked && ! empty( $imagec ) ) {
						$altsrc = array( 'src' => $imagec );
					} else {
						$altsrc = array( 'src' => $image );
					}
				}
				if ( ! empty( $use_lightbox ) && $use_lightbox == "lightbox" ) {
					$swatch_class .= " tc-lightbox-image";
				}
				$label_mode = "images";
			} else {
				// Check for hex color
				$search_for_color = $label;
				if ( isset( $color ) ) {
					if ( ! is_array( $color ) ) {
						$search_for_color = $color;
					} else {
						if ( isset( $color[ $choice_counter ] ) ) {
							$search_for_color = $color[ $choice_counter ];
						}
					}
					if ( empty( $search_for_color ) ) {
						$search_for_color = 'transparent';
					}
				}
				if ( $search_for_color == 'transparent' || preg_match( '/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $search_for_color ) ) { //hex color is valid

					if ( $search_for_color == 'transparent' ) {
						$swatch_class .= " tm-transparent-swatch";
					}

					$label_mode       = "color";
					$label_to_display = ( ! isset( $color ) ) ? $search_for_color : $label;
				}
			}

			// For variations
			if ( ! empty( $show_label ) ) {
				switch ( $show_label ) {
					case 'hide':
						$swatch_class .= " tm-hide-label";
						break;
					case 'bottom':
						$swatch_class .= " tm-bottom-label";
						break;
					case 'inside':
						$swatch_class .= " tm-inside-label";
						break;
					case 'tooltip':
						$swatch_class .= " tm-tooltip";
						$swatch[]     = array( 'data-tm-tooltip-swatch' => 'on' );
						break;
				}
			}

			if ( ! empty( $swatch ) ) {
				$swatch[] = array( 'data-tm-hide-label' => THEMECOMPLETE_EPO()->tm_epo_swatch_hide_label );
			}

			switch ( $use_images ) {

				case "start":
					if ( ! empty( $label_mode ) ) {
						$label_mode = "start" . $label_mode;
					}
					break;

				case "end":
					if ( ! empty( $label_mode ) ) {
						$label_mode = "end" . $label_mode;
					}
					break;
			}
		}

		if ( ! empty( $li_class ) ) {
			$li_class = " " . $li_class;
		} else {
			$li_class = "";
		}

		if ( ! empty( $this->items_per_row ) ) {
			$li_class .= " tm-per-row";
		}

		if ( ! empty( $element['use_url'] ) && $element['use_url'] === 'url' ) {
			$url = do_shortcode( $url );
		} else {
			$url = "";
		}

		$image_variations = array();
		if ( ! empty( $changes_product_image ) ) {
			$image_link        = $image;
			$attachment_id     = THEMECOMPLETE_EPO_HELPER()->get_attachment_id( $image_link );
			$attachment_id     = ( $attachment_id ) ? $attachment_id : 0;
			$attachment_object = get_post( $attachment_id );
			if ( ! $attachment_object && get_transient( 'get_attachment_id_' . $image_link ) ) {
				delete_transient( 'get_attachment_id_' . $image_link );
				$attachment_id     = THEMECOMPLETE_EPO_HELPER()->get_attachment_id( $image_link );
				$attachment_id     = ( $attachment_id ) ? $attachment_id : 0;
				$attachment_object = get_post( $attachment_id );
			}
			if ( $attachment_object ) {
				$full_src      = wp_get_attachment_image_src( $attachment_id, 'large' );
				$image_title   = get_the_title( $attachment_id );
				$image_alt     = wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', TRUE ) );
				$image_srcset  = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, 'shop_single' ) : FALSE;
				$image_sizes   = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $attachment_id, 'shop_single' ) : FALSE;
				$image_caption = $attachment_object->post_excerpt;

				if ( $full_src === FALSE || ! is_array( $full_src ) ) {
					$full_src = array( "", "", "" );
				}

				$image_variations['image'] = array(
					'image_link'    => $image_link,
					'image_title'   => $image_title,
					'image_alt'     => $image_alt,
					'image_srcset'  => $image_srcset,
					'image_sizes'   => $image_sizes,
					'image_caption' => $image_caption,
					'image_id'      => $attachment_id,
					'full_src'      => $full_src[0],
					'full_src_w'    => $full_src[1],
					'full_src_h'    => $full_src[2],
				);
			}

			$image_link        = $imagep;
			$attachment_id     = THEMECOMPLETE_EPO_HELPER()->get_attachment_id( $image_link );
			$attachment_id     = ( $attachment_id ) ? $attachment_id : 0;
			$attachment_object = get_post( $attachment_id );
			if ( ! $attachment_object && get_transient( 'get_attachment_id_' . $image_link ) ) {
				delete_transient( 'get_attachment_id_' . $image_link );
				$attachment_id     = THEMECOMPLETE_EPO_HELPER()->get_attachment_id( $image_link );
				$attachment_id     = ( $attachment_id ) ? $attachment_id : 0;
				$attachment_object = get_post( $attachment_id );
			}
			if ( $attachment_object ) {
				$full_src      = wp_get_attachment_image_src( $attachment_id, 'large' );
				$image_title   = get_the_title( $attachment_id );
				$image_alt     = wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', TRUE ) );
				$image_srcset  = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, 'shop_single' ) : FALSE;
				$image_sizes   = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $attachment_id, 'shop_single' ) : FALSE;
				$image_caption = $attachment_object->post_excerpt;

				if ( $full_src === FALSE || ! is_array( $full_src ) ) {
					$full_src = array( "", "", "" );
				}

				$image_variations['imagep'] = array(
					'image_link'    => $image_link,
					'image_title'   => $image_title,
					'image_alt'     => $image_alt,
					'image_srcset'  => $image_srcset,
					'image_sizes'   => $image_sizes,
					'image_caption' => $image_caption,
					'image_id'      => $attachment_id,
					'full_src'      => $full_src[0],
					'full_src_w'    => $full_src[1],
					'full_src_h'    => $full_src[2],
				);
			}
		}

		$labelclass       = '';
		$labelclass_start = '';
		$labelclass_end   = '';
		if ( THEMECOMPLETE_EPO()->tm_epo_css_styles == "on" && ( empty( $use_images ) || ( isset( $use_images ) && $use_images != "images" ) ) ) {
			$labelclass       = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
			$labelclass_start = THEMECOMPLETE_EPO()->tm_epo_css_styles_style . ( empty( $hexclass ) ? '' : ' ' . $hexclass );
			$labelclass_end   = TRUE;
		}

		$display = array(
			'hexclass'              => $hexclass,
			'label_mode'            => $label_mode,
			'label_to_display'      => $label_to_display,
			'swatch_class'          => $swatch_class,
			'swatch'                => $swatch,
			'altsrc'                => $altsrc,
			'use'                   => $use,
			'labelclass_start'      => $labelclass_start,
			'labelclass'            => $labelclass,
			'labelclass_end'        => $labelclass_end,
			'image_variations'      => wp_json_encode( $image_variations ),
			'checked'               => $checked,
			'li_class'              => $li_class,
			'class'                 => $css_class,
			'label'                 => $label,
			'value'                 => esc_attr( $args['value'] ),
			'id'                    => 'tmcp_choice_' . str_replace("-", "_", $unique_indentifier),
			'textbeforeprice'       => isset( $element['text_before_price'] ) ? $element['text_before_price'] : "",
			'textafterprice'        => isset( $element['text_after_price'] ) ? $element['text_after_price'] : "",
			'hide_amount'           => isset( $element['hide_amount'] ) ? " " . $element['hide_amount'] : "",
			'use_images'            => $use_images,
			'use_colors'            => $use_colors,
			'use_lightbox'          => $use_lightbox,
			'use_url'               => $element['use_url'],
			'grid_break'            => $this->grid_break,
			'items_per_row'         => $this->items_per_row,
			'items_per_row_r'       => $this->items_per_row_r,
			'percent'               => $this->_percent,
			'image'                 => $image,
			'imagec'                => $imagec,
			'imagep'                => $imagep,
			'imagel'                => $imagel,
			'url'                   => $url,
			'limit'                 => empty( $element['limit'] ) ? "" : $element['limit'],
			'exactlimit'            => empty( $element['exactlimit'] ) ? "" : $element['exactlimit'],
			'minimumlimit'          => empty( $element['minimumlimit'] ) ? "" : $element['minimumlimit'],
			'swatchmode'            => $swatchmode,
			'clear_options'         => empty( $element['clear_options'] ) ? "" : $element['clear_options'],
			'show_label'            => $show_label,
			'tm_epo_no_lazy_load'   => THEMECOMPLETE_EPO()->tm_epo_no_lazy_load,
			'changes_product_image' => $changes_product_image,
			'default_value'         => $default_value,
			'quantity'              => isset( $element['quantity'] ) ? $element['quantity'] : "",
			'choice_counter'        => $this->_default_value_counter,
		);

		if ( isset( $color ) ) {
			$display["color"] = $color;
		}

		if ( ! empty( $changes_product_image ) ) {
			$fieldtype            = $args['fieldtype'] . " tm-product-image";
			$display['fieldtype'] = $fieldtype;
		}

		if ( ! empty( $css_class ) ) {
			if ( isset( $display['fieldtype'] ) ) {
				$fieldtype = $display['fieldtype'] . " " . $css_class;
			} else {
				$fieldtype = $args['fieldtype'] . " " . $css_class;
			}
			$display['fieldtype'] = $fieldtype;
		}

		if ( ! empty( $args['element_data_attr'] ) && is_array( $args['element_data_attr'] ) ) {
			$display['element_data_attr'] = $args['element_data_attr'];
		} else {
			$display['element_data_attr'] = array();
		}

		$this->_default_value_counter ++;

		return $display;

	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {

		$passed  = TRUE;
		$message = array();

		$min_quantity = isset( $this->element['quantity_min'] ) ? intval( $this->element['quantity_min'] ) : 0;
		if ( $min_quantity < 0 ) {
			$min_quantity = 0;
		}

		foreach ( $this->tmcp_attributes as $k => $attribute ) {
			if ( isset( $this->epo_post_fields[ $attribute ] ) && isset( $this->epo_post_fields[ $attribute . '_quantity' ] ) && ! ( intval( $this->epo_post_fields[ $attribute . '_quantity' ] ) >= $min_quantity ) ) {
				$passed    = FALSE;
				$message[] = sprintf( esc_html__( 'The quantity for "%s" must be greater than %s', 'woocommerce-tm-extra-product-options' ), $this->element['options'][ $this->epo_post_fields[ $attribute ] ], $min_quantity );
				break;
			}

			if ( $this->element['required'] ) {

				$is_cart_fee = $this->element['is_cart_fee'];
				if ( $is_cart_fee ) {
					if ( ! isset( $this->epo_post_fields[ $this->tmcp_attributes_fee[ $k ] ] ) ) {
						$passed    = FALSE;
						$message[] = 'required';
						break;
					}
				} else {
					$is_alt = apply_filters( 'wc_epo_alt_validate_radiobutton', FALSE, $this, $k );
					if ( $is_alt ) {
						$fail = apply_filters( 'wc_epo_validate_radiobutton', FALSE, $this, $k );
						if ( $fail ) {
							$passed    = FALSE;
							$message[] = 'required';
							break;
						}
					} else {
						if ( ! isset( $this->epo_post_fields[ $attribute ] ) ) {
							$passed    = FALSE;
							$message[] = 'required';
							break;
						}
					}
				}

			}
		}

		return array( 'passed' => $passed, 'message' => $message );
	}
}
