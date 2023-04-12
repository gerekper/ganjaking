<?php
/**
 * Radio Buttons Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Radio Buttons Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_radio extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * The number of columns
	 *
	 * @var float
	 */
	public $items_per_row;

	/**
	 * The number of columns for responisve devices
	 *
	 * @var float
	 */
	public $items_per_row_r;

	/**
	 * The percentage of the item width
	 *
	 * @var float
	 */
	public $percent;

	/**
	 * The number of columns
	 *
	 * @var float
	 */
	public $columns;

	/**
	 * The choice counter
	 *
	 * @var float
	 */
	public $default_value_counter;

	/**
	 * Pre display field array
	 *
	 * @param array $element The element array.
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function display_field_pre( $element = [], $args = [] ) {
		$this->items_per_row   = $element['items_per_row'];
		$this->items_per_row_r = isset( $element['items_per_row_r'] ) ? $element['items_per_row_r'] : [];
		$this->percent         = 100;
		$this->columns         = 0;
		$container_css_id      = 'element_';
		if ( isset( $element['container_css_id'] ) ) {
			$container_css_id = $element['container_css_id'];
		}
		if ( ! isset( $args['product_id'] ) ) {
			$args['product_id'] = '';
		}

		if ( ! empty( $this->items_per_row ) ) {
			if ( 'auto' === $this->items_per_row || ! is_numeric( $this->items_per_row ) || floatval( $this->items_per_row ) === 0 ) {
				$this->items_per_row = 0;
			} else {
				$this->items_per_row = (float) $this->items_per_row;
				$this->percent       = (float) ( 100 / $this->items_per_row );
				$css_string          = '.tm-product-id-' . $args['product_id'] . ' .' . $container_css_id . $args['element_counter'] . $args['form_prefix'] . ' li{-ms-flex: 0 0 ' . $this->percent . '% !important;flex: 0 0 ' . $this->percent . '% !important;max-width:' . $this->percent . '% !important;}';
			}

			$css_string = str_replace( [ "\r", "\n" ], '', $css_string );
			THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );
		} else {
			$this->items_per_row = (float) $element['items_per_row'];
		}

		foreach ( $this->items_per_row_r as $key => $value ) {
			$before = '';
			$after  = '}';
			if ( ! empty( $value ) ) {
				if ( 'desktop' !== $key ) {
					switch ( $key ) {
						case 'tablets_galaxy': // 800-1280
							$before = '@media only screen and (min-device-width : 800px) and (max-device-width : 1280px),only screen and (min-width : 800px) and (max-width : 1280px) {';
							break;
						case 'tablets': // 768-1024
							$before = '@media only screen and (min-device-width : 768px) and (max-device-width : 1024px),only screen and (min-width : 768px) and (max-width : 1024px) {';
							break;
						case 'tablets_small': // 481-767
							$before = '@media only screen and (min-device-width : 481px) and (max-device-width : 767px),only screen and (min-width : 481px) and (max-width : 767px) {';
							break;
						case 'iphone6_plus': // 414-736
							$before = '@media only screen and (min-device-width: 414px) and (max-device-width: 736px) and (-webkit-min-device-pixel-ratio: 2),only screen and (min-width: 414px) and (max--width: 736px) {';
							break;
						case 'iphone6': // 375-667
							$before = '@media only screen and (min-device-width: 375px) and (max-device-width: 667px) and (-webkit-min-device-pixel-ratio: 2),only screen and (min-width: 375px) and (max-width: 667px) {';
							break;
						case 'galaxy': // 320-640
							$before = '@media only screen and (device-width: 320px) and (device-height: 640px) and (-webkit-min-device-pixel-ratio: 2),only screen and (width: 320px) and (height: 640px) {';
							break;
						case 'iphone5': // 320-568
							$before = '@media only screen and (min-device-width: 320px) and (max-device-width: 568px) and (-webkit-min-device-pixel-ratio: 2), only screen and (min-width: 320px) and (max-width: 568px) {';
							break;
						case 'smartphones': // 320-480
							$before = '@media only screen and (min-device-width : 320px) and (max-device-width : 480px), only screen and (min-width : 320px) and (max-width : 480px), only screen and (max-width : 319px){';
							break;

						default:
							// code...
							break;
					}
					$thisitems_per_row = (float) $value;
					$this_percent      = (float) ( 100 / $thisitems_per_row );
					$css_string        = $before . '.tm-product-id-' . $args['product_id'] . ' .' . $container_css_id . $args['element_counter'] . $args['form_prefix'] . ' li{-ms-flex: 0 0 ' . $this_percent . '% !important;flex: 0 0 ' . $this_percent . '% !important;max-width:' . $this_percent . '% !important;}' . $after;
					$css_string        = str_replace( [ "\r", "\n" ], '', $css_string );
					THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );
				}
			}
		}

		$this->default_value_counter = 0;
	}

	/**
	 * Display field array
	 *
	 * @param array $element The element array.
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {
		$this->columns ++;
		$default_value = isset( $element['default_value'] ) ? ( ( '' !== $element['default_value'] ) ? ( (int) $element['default_value'] === (int) $this->default_value_counter ) : false ) : false;

		if ( (float) $this->columns > (float) $this->items_per_row && $this->items_per_row > 0 ) {
			$this->columns = 1;
		}

		$hexclass         = '';
		$li_class         = '';
		$search_for_color = $args['label'];
		if ( isset( $element['color'] ) ) {
			if ( ! is_array( $element['color'] ) ) {
				$search_for_color = $element['color'];
			} else {
				if ( isset( $element['color'][ $this->default_value_counter ] ) ) {
					$search_for_color = $element['color'][ $this->default_value_counter ];
				}
			}
			if ( empty( $search_for_color ) ) {
				$search_for_color = 'transparent';
			}
		}

		$unique_indentifier = $args['element_counter'] . '-' . $args['field_counter'] . '-' . $args['tabindex'] . $args['form_prefix'] . uniqid();

		if ( ( 'image' === $element['replacement_mode'] || 'color' === $element['replacement_mode'] ) && ( 'transparent' === $search_for_color || preg_match( '/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $search_for_color ) ) ) {

			$tmhexcolor   = 'tmhexcolor_' . $unique_indentifier;
			$litmhexcolor = 'tm-li-unique-' . $unique_indentifier;
			$hexclass     = $tmhexcolor;
			$css_string   = '.' . $tmhexcolor . ' .tmhexcolorimage{background-color:' . $search_for_color . ' !important;}';
			if ( ! empty( $element['item_width'] ) ) {
				if ( is_numeric( $element['item_width'] ) ) {
					$element['item_width'] .= 'px';
				}
				$css_string .= '.' . $tmhexcolor . ' img{display: inline-block !important;width:' . $element['item_width'] . ' !important;min-width:' . $element['item_width'] . ' !important;max-width:' . $element['item_width'] . ' !important;}';
				$css_string .= '.tm-extra-product-options ul.tmcp-ul-wrap .' . $tmhexcolor . ' .tmhexcolorimage{padding: 1px !important;display: inline-block !important;width:' . $element['item_width'] . ' !important;min-width:' . $element['item_width'] . ' !important;max-width:' . $element['item_width'] . ' !important;}';
			}
			if ( ! empty( $element['item_height'] ) ) {
				if ( is_numeric( $element['item_height'] ) ) {
					$element['item_height'] .= 'px';
				}
				$css_string .= '.' . $tmhexcolor . ' img{display: inline-block !important;height:' . $element['item_height'] . ' !important;min-height:' . $element['item_height'] . ' !important;max-height:' . $element['item_height'] . ' !important;}';
				$css_string .= '.tm-extra-product-options ul.tmcp-ul-wrap .' . $tmhexcolor . ' .tmhexcolorimage{padding: 1px !important;display: inline-block !important;height:' . $element['item_height'] . ' !important;min-height:' . $element['item_height'] . ' !important;max-height:' . $element['item_height'] . ' !important;}';
			}
			if ( ! empty( $element['item_width'] ) || ! empty( $element['item_height'] ) ) {
				$css_string .= '.tmhexcolorimage-li.tm-li-unique-' . $unique_indentifier . '{display: inline-block;width:auto !important;overflow:hidden;}';
				$li_class   .= 'tmhexcolorimage-li tm-li-unique-' . $unique_indentifier;
			} else {
				$li_class .= 'tmhexcolorimage-li-nowh';
			}
			$css_string = str_replace( [ "\r", "\n" ], '', $css_string );
			THEMECOMPLETE_EPO_DISPLAY()->add_inline_style( $css_string );
		}

		$_css_class = ! empty( $element['class'] ) ? $element['class'] . ' ' . $hexclass : '' . $hexclass;
		$css_class  = apply_filters( 'wc_epo_multiple_options_css_class', '', $element, $this->default_value_counter );
		if ( '' !== $css_class ) {
			$css_class = ' ' . $css_class;
		}
		$css_class = $_css_class . $css_class;

		$use = '';

		if ( 'image' === $element['replacement_mode'] || 'color' === $element['replacement_mode'] ) {
			$use = ' use_images';
		}

		$image                 = isset( $element['images'][ $args['field_counter'] ] ) ? $element['images'][ $args['field_counter'] ] : '';
		$imagec                = isset( $element['imagesc'][ $args['field_counter'] ] ) ? $element['imagesc'][ $args['field_counter'] ] : '';
		$imagep                = isset( $element['imagesp'][ $args['field_counter'] ] ) ? $element['imagesp'][ $args['field_counter'] ] : '';
		$imagel                = isset( $element['imagesl'][ $args['field_counter'] ] ) ? $element['imagesl'][ $args['field_counter'] ] : '';
		$label                 = wptexturize( apply_filters( 'woocommerce_tm_epo_option_name', $args['label'], $element, $this->default_value_counter ) );
		$label_mode            = '';
		$changes_product_image = empty( $element['changes_product_image'] ) ? '' : $element['changes_product_image'];

		if ( THEMECOMPLETE_EPO()->tm_epo_global_image_mode === 'relative' ) {
			if ( strpos( $image, get_site_url() ) !== false ) {
				$image  = wp_make_link_relative( $image );
				$imagec = wp_make_link_relative( $imagec );
				$imagep = wp_make_link_relative( $imagep );
				$imagel = wp_make_link_relative( $imagel );
			}
		}

		$url = isset( $element['url'][ $args['field_counter'] ] ) ? $element['url'][ $args['field_counter'] ] : '';

		if ( empty( $image ) ) {
			$image = '';
		}
		if ( empty( $imagec ) ) {
			$imagec = '';
		}
		if ( empty( $imagep ) || empty( $changes_product_image ) ) {
			$imagep = '';
		}
		if ( ! empty( $changes_product_image ) && 'images' === $changes_product_image ) {
			$imagep = '';
		}
		if ( empty( $imagel ) ) {
			$imagel = '';
		}

		$selected_value = '';
		if ( isset( $args['posted_name'] ) ) {
			$name = $args['posted_name'];
			if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add && isset( $this->post_data[ $name ] ) ) {
				$selected_value = $this->post_data[ $name ];
			} elseif ( empty( $this->post_data ) && isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			} elseif ( ( empty( $this->post_data ) || ( isset( $this->post_data['action'] ) && 'wc_epo_get_associated_product_html' === $this->post_data['action'] ) ) || ! isset( $this->post_data[ $name ] ) || 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add || ( isset( $args['posted_name'] ) && ! empty( $this->post_data ) && ! isset( $_REQUEST[ $args['posted_name'] ] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$selected_value = -1;
			}
		}

		$selected_value = apply_filters( 'wc_epo_default_value', $selected_value, isset( $element ) ? $element : [], esc_attr( $args['value'] ) );

		if ( is_array( $selected_value ) ) {

			if ( isset( $args['get_posted_key'] ) && isset( $selected_value[ $args['get_posted_key'] ] ) ) {
				$selected_value = $selected_value[ $args['get_posted_key'] ];
			} else {
				$selected_value = '';
			}
		}

		$checked = false;

		if ( -1 === $selected_value ) {
			if ( ( empty( $this->post_data ) || ( ! empty( $this->post_data ) && ( ! isset( $this->post_data['quantity'] ) || ( isset( $args['posted_name'] ) && ! isset( $_REQUEST[ $args['posted_name'] ] ) ) ) ) ) && isset( $default_value ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( $default_value ) {
					$checked = true;
				}
			}
			if ( (
				( isset( $this->post_data['action'] ) && 'wc_epo_get_associated_product_html' === $this->post_data['action'] )
				|| 'yes' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add
				) && isset( $default_value ) ) {
				if ( $default_value && ! THEMECOMPLETE_EPO()->is_edit_mode() ) {
					$checked = true;
				}
			}
		} else {
			if ( ! THEMECOMPLETE_EPO()->is_edit_mode() && isset( $element ) && ! empty( $default_value ) && ! empty( $element['default_value_override'] ) && isset( $element['default_value'] ) ) {
				$checked = true;
			} elseif ( esc_attr( stripcslashes( $selected_value ) ) === esc_attr( ( $args['value'] ) ) ) {
				$checked = true;
			}
		}

		$replacement_mode    = $element['replacement_mode'];
		$swatch_position     = $element['swatch_position'];
		$showtooltip         = empty( $element['show_tooltip'] ) || ( 'image' !== $replacement_mode && 'color' !== $replacement_mode ) ? '' : $element['show_tooltip'];
		$use_lightbox        = isset( $element['use_lightbox'] ) ? $element['use_lightbox'] : '';
		$choice_counter      = $this->default_value_counter;
		$show_label          = empty( $element['show_label'] ) ? '' : $element['show_label'];
		$tm_epo_no_lazy_load = THEMECOMPLETE_EPO()->tm_epo_no_lazy_load;
		if ( isset( $element['color'] ) ) {
			$color = $element['color'];
		}
		if ( ! isset( $args['border_type'] ) ) {
			$border_type = '';
		} else {
			$border_type = $args['border_type'];
		}

		$swatch       = [];
		$swatch_class = '';
		$altsrc       = [];

		$label_to_display = $label;

		if ( 'text' === $replacement_mode ) {
			$label_mode = 'text';
		}

		if ( 'image' === $replacement_mode || 'color' === $replacement_mode ) {

			if ( 'swatch' === $showtooltip ) {
				$swatch_class = ' tm-tooltip';
				$swatch[]     = [ 'data-tm-tooltip-swatch' => 'on' ];
			} elseif ( 'swatch_desc' === $showtooltip ) {
				$swatch_class = ' tm-tooltip';
				$swatch[]     = [ 'data-tm-tooltip-swatch-desc' => 'on' ];
			} elseif ( 'swatch_lbl_desc' === $showtooltip ) {
				$swatch_class = ' tm-tooltip';
				$swatch[]     = [ 'data-tm-tooltip-swatch-lbl-desc' => 'on' ];
			} elseif ( 'swatch_img' === $showtooltip ) {
				$swatch_class = ' tm-tooltip';
				$swatch[]     = [ 'data-tm-tooltip-swatch-img' => 'on' ];
			} elseif ( 'swatch_img_lbl' === $showtooltip ) {
				$swatch_class = ' tm-tooltip';
				$swatch[]     = [ 'data-tm-tooltip-swatch-img-lbl' => 'on' ];
			} elseif ( 'swatch_img_desc' === $showtooltip ) {
				$swatch_class = ' tm-tooltip';
				$swatch[]     = [ 'data-tm-tooltip-swatch-img-desc' => 'on' ];
			} elseif ( 'swatch_img_lbl_desc' === $showtooltip ) {
				$swatch_class = ' tm-tooltip';
				$swatch[]     = [ 'data-tm-tooltip-swatch-img-lbl-desc' => 'on' ];
			}

			if ( 'image' === $replacement_mode && ! empty( $image ) ) {
				if ( THEMECOMPLETE_EPO()->tm_epo_global_retrieve_image_sizes === 'yes' ) {
					$attachment_id = THEMECOMPLETE_EPO_HELPER()->get_attachment_id( $image );
					$attachment_id = ( $attachment_id ) ? $attachment_id : 0;
					$image_info    = THEMECOMPLETE_EPO_HELPER()->get_attachment_sizes( $attachment_id, $image );
					$attachment_id = THEMECOMPLETE_EPO_HELPER()->get_attachment_id( $imagec );
					$attachment_id = ( $attachment_id ) ? $attachment_id : 0;
					$imagec_info   = THEMECOMPLETE_EPO_HELPER()->get_attachment_sizes( $attachment_id, $imagec );
				} else {
					$image_info  = [ '', '' ];
					$imagec_info = [ '', '' ];
				}

				if ( 'no' === $tm_epo_no_lazy_load ) {
					if ( $checked && ! empty( $imagec ) ) {
						$altsrc = [
							'src'           => '',
							'data-original' => $imagec,
						];
						if ( $imagec_info ) {
							$altsrc['width']  = $imagec_info[0];
							$altsrc['height'] = $imagec_info[1];
						}
					} else {
						$altsrc = [
							'src'           => '',
							'data-original' => $image,
						];
						if ( $image_info ) {
							$altsrc['width']  = $image_info[0];
							$altsrc['height'] = $image_info[1];
						}
					}
				} else {
					if ( $checked && ! empty( $imagec ) ) {
						$altsrc = [ 'src' => $imagec ];
						if ( $imagec_info ) {
							$altsrc['width']  = $imagec_info[0];
							$altsrc['height'] = $imagec_info[1];
						}
					} else {
						$altsrc = [ 'src' => $image ];
						if ( $image_info ) {
							$altsrc['width']  = $image_info[0];
							$altsrc['height'] = $image_info[1];
						}
					}
				}
				if ( ! empty( $use_lightbox ) && 'lightbox' === $use_lightbox ) {
					$swatch_class .= ' tc-lightbox-image';
				}
				$label_mode = 'images';
			} else {
				// Check for hex color.
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
				if ( 'transparent' === $search_for_color || preg_match( '/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $search_for_color ) ) { // hex color is valid.

					if ( 'transparent' === $search_for_color ) {
						$swatch_class .= ' tm-transparent-swatch';
					}

					$label_mode       = 'color';
					$label_to_display = ( ! isset( $color ) ) ? $search_for_color : $label;
				}
			}

			// For variations.
			if ( ! empty( $show_label ) ) {
				switch ( $show_label ) {
					case 'hide':
						$swatch_class .= ' tm-hide-label';
						break;
					case 'bottom':
						$swatch_class .= ' tm-bottom-label';
						break;
					case 'inside':
						$swatch_class .= ' tm-inside-label';
						break;
					case 'tooltip':
						$swatch_class .= ' tm-tooltip';
						$swatch[]      = [ 'data-tm-tooltip-swatch' => 'on' ];
						break;
				}
			}

			if ( ! empty( $swatch ) ) {
				$swatch[] = [ 'data-tm-hide-label' => THEMECOMPLETE_EPO()->tm_epo_swatch_hide_label ];
			}

			switch ( $swatch_position ) {

				case 'start':
					if ( ! empty( $label_mode ) ) {
						$label_mode = 'start' . $label_mode;
					}
					break;

				case 'end':
					if ( ! empty( $label_mode ) ) {
						$label_mode = 'end' . $label_mode;
					}
					break;
			}
		}

		if ( ! empty( $li_class ) ) {
			$li_class = ' ' . $li_class;
		} else {
			$li_class = '';
		}

		if ( ! empty( $this->items_per_row ) ) {
			$li_class .= ' tm-per-row';
		}

		if ( ! empty( $element['use_url'] ) && 'url' === $element['use_url'] ) {
			$url = themecomplete_do_shortcode( $url );
		} else {
			$url = '';
		}

		$image_variations = [];
		if ( ! empty( $changes_product_image ) ) {
			$image_link       = $image;
			$image_variations = THEMECOMPLETE_EPO_HELPER()->generate_image_array( $image_variations, $image_link, 'image' );

			$image_link       = $imagep;
			$image_variations = THEMECOMPLETE_EPO_HELPER()->generate_image_array( $image_variations, $image_link, 'imagep' );
		}

		$labelclass       = '';
		$labelclass_start = '';
		$labelclass_end   = '';
		if ( 'yes' === THEMECOMPLETE_EPO()->tm_epo_css_styles && ( 'none' === $replacement_mode || ( ( 'image' === $replacement_mode || 'color' === $replacement_mode ) && 'center' !== $swatch_position ) ) ) {
			$labelclass       = THEMECOMPLETE_EPO()->tm_epo_css_styles_style;
			$labelclass_start = THEMECOMPLETE_EPO()->tm_epo_css_styles_style . ( empty( $hexclass ) ? '' : ' ' . $hexclass );
			$labelclass_end   = true;
		}

		$is_separator = '-1' === str_replace( '_' . $choice_counter, '', $args['value'] ) && '-1' !== $args['value'];
		if ( $is_separator ) {
			$li_class .= ' is-separator';
		}
		$display = [
			'is_separator'          => $is_separator,
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
			'value'                 => $args['value'],
			'id'                    => 'tmcp_choice_' . str_replace( '-', '_', $unique_indentifier ),
			'textbeforeprice'       => isset( $element['text_before_price'] ) ? $element['text_before_price'] : '',
			'textafterprice'        => isset( $element['text_after_price'] ) ? $element['text_after_price'] : '',
			'hide_amount'           => $this->get_value( $element, 'hide_amount', '' ),
			'replacement_mode'      => $replacement_mode,
			'swatch_position'       => $swatch_position,
			'use_lightbox'          => $use_lightbox,
			'use_url'               => $element['use_url'],
			'items_per_row'         => $this->items_per_row,
			'items_per_row_r'       => $this->items_per_row_r,
			'percent'               => $this->percent,
			'image'                 => $image,
			'imagec'                => $imagec,
			'imagep'                => $imagep,
			'imagel'                => $imagel,
			'url'                   => $url,
			'limit'                 => empty( $element['limit'] ) ? '' : $element['limit'],
			'exactlimit'            => empty( $element['exactlimit'] ) ? '' : $element['exactlimit'],
			'minimumlimit'          => empty( $element['minimumlimit'] ) ? '' : $element['minimumlimit'],
			'show_tooltip'          => $showtooltip,
			'clear_options'         => empty( $element['clear_options'] ) ? '' : $element['clear_options'],
			'show_label'            => $show_label,
			'tm_epo_no_lazy_load'   => THEMECOMPLETE_EPO()->tm_epo_no_lazy_load,
			'changes_product_image' => $changes_product_image,
			'default_value'         => $default_value,
			'quantity'              => isset( $element['quantity'] ) ? $element['quantity'] : '',
			'choice_counter'        => $this->default_value_counter,
		];

		if ( isset( $color ) ) {
			$display['color'] = $color;
		}

		$display['fieldtype'] = '';
		if ( isset( $args['fieldtype'] ) ) {
			$display['fieldtype'] = $args['fieldtype'];
		}
		if ( ! empty( $changes_product_image ) ) {
			$display['fieldtype'] .= ' tm-product-image';
		}

		if ( ! empty( $args['element_data_attr'] ) && is_array( $args['element_data_attr'] ) ) {
			$display['element_data_attr'] = $args['element_data_attr'];
		} else {
			$display['element_data_attr'] = [];
		}

		$this->default_value_counter ++;

		return apply_filters( 'wc_epo_display_field_radio', $display, $this, $element, $args );

	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {

		$passed  = true;
		$message = [];

		$min_quantity = isset( $this->element['quantity_min'] ) ? (int) $this->element['quantity_min'] : 0;
		if ( apply_filters( 'wc_epo_field_min_quantity_greater_than_zero', true ) && $min_quantity < 0 ) {
			$min_quantity = 0;
		}

		foreach ( $this->tmcp_attributes as $k => $attribute ) {
			$attribute_quantity = $attribute . '_quantity';
			if ( isset( $this->epo_post_fields[ $attribute ] ) && '' !== $this->epo_post_fields[ $attribute ] && isset( $this->epo_post_fields[ $attribute_quantity ] ) && ! ( (int) array_sum( (array) $this->epo_post_fields[ $attribute_quantity ] ) >= $min_quantity ) ) {
				$passed = false;
				/* translators: %1 element label %2 quantity value. */
				$message[] = sprintf( esc_html__( 'The quantity for "%1$s" must be greater than %2$s', 'woocommerce-tm-extra-product-options' ), $this->element['options'][ $this->epo_post_fields[ $attribute ] ], $min_quantity );
				break;
			}

			if ( $this->element['required'] ) {

				$is_cart_fee = $this->element['is_cart_fee'];
				if ( $is_cart_fee ) {
					if ( ! isset( $this->epo_post_fields[ $this->tmcp_attributes_fee[ $k ] ] ) ) {
						$passed    = false;
						$message[] = 'required';
						break;
					}
				} else {
					$is_alt = apply_filters( 'wc_epo_alt_validate_radiobutton', false, $this, $k );
					if ( $is_alt ) {
						$fail = apply_filters( 'wc_epo_validate_radiobutton', false, $this, $k );
						if ( $fail ) {
							$passed    = false;
							$message[] = 'required';
							break;
						}
					} else {
						if ( ! isset( $this->epo_post_fields[ $attribute ] ) ) {
							$passed    = false;
							$message[] = 'required';
							break;
						}
					}
				}
			}
		}

		return [
			'passed'  => $passed,
			'message' => $message,
		];
	}
}
