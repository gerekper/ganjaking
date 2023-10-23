<?php
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\CategoryAccordion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get container colors
 *
 * @param $colors
 * @param $post_id
 * @return string
 */
function get_container_colors( $colors, $post_id ) {
	$colors          = unserialize( $colors[0] );
	$container_color_var = '';

	if ( is_array( $colors ) ) {
		$container_background_color = $colors['container_bg'] ?? '';
		$container_border_color     = $colors['container_border'] ?? '';

		$container_color_var = "--ywcacc-container-bg-$post_id: $container_background_color;\n--ywcacc-container-border-$post_id: $container_border_color;\n";
	}
	return $container_color_var;
}


/**
 * This function get the font weight style for all tabs
 *
 * @param $weight
 * @param $type_string
 * @param $post_id
 *
 * @return string
 */
function get_font_weight_var($weight, $type_string, $post_id ) {

	$weight_style = $weight[0];
	switch ( $weight_style ) {

		case 'bold':
			$font_weight_style = "--ywcacc" . $type_string . "-font-weight-$post_id: 700;\n--ywcacc" . $type_string . "-font-style-$post_id: normal;\n";
			break;

		case 'extra-bold':
			$font_weight_style = "--ywcacc" . $type_string . "-font-weight-$post_id: 800;\n--ywcacc" . $type_string . "-font-style-$post_id: normal;\n";
			break;

		case 'italic':
			$font_weight_style = "--ywcacc" . $type_string . "-font-weight-$post_id: normal;\n--ywcacc" . $type_string . "-font-style-$post_id: italic;\n";
			break;

		case 'bold-italic':
			$font_weight_style = "--ywcacc" . $type_string . "-font-weight-$post_id: 700;\n--ywcacc" . $type_string . "-font-style-$post_id: italic;\n";
			break;

		case 'regular':
			$font_weight_style = "--ywcacc" . $type_string . "-font-weight-$post_id: 400;\n--ywcacc" . $type_string . "-font-style-$post_id: normal;\n";
			break;

		default:
			$font_weight_style = "--ywcacc" . $type_string . "-font-weight-$post_id: 400;\n--ywcacc" . $type_string . "-font-style-$post_id: normal;\n";
	}

	return $font_weight_style;
}

/**
 * This function get the font size style for all tabs
 *
 * @param $font
 * @param $type_string
 * @param $post_id
 *
 * @return string
 */
function get_font_size_var( $font, $type_string, $post_id ) {
	$font_data = unserialize( $font );
	$font_var  = '';
	if ( ! empty( $font_data ) ) {
		$font_size      = $font_data['font_size'];
		$font_type_size = $font_data['type_font_size'];

		$font_var = "--ywcacc$type_string-font-size-$post_id: $font_size$font_type_size;\n";
	}

	return $font_var;
}

// Title options functions !
/**
 * Get the title style font weight
 *
 * @param $weight
 * @param $post_id
 *
 * @return string
 */
function get_font_weight( $weight, $post_id ) {
	return get_font_weight_var( $weight, '', $post_id );
}

/**
 * Get the title style font size
 *
 * @param $font
 * @param $post_id
 *
 * @return string
 */
function get_font_size( $font, $post_id ) {
	return get_font_size_var( $font[0], '', $post_id );
}

/**
 * Get the title border style
 *
 * @param $border_style
 * @param $post_id
 *
 * @return string
 */
function get_border_style( $border_style, $post_id ) {
	$border_style = $border_style[0];

	switch ( $border_style ) {
		case 'no_border':
			$border_width = '';
			$border_style = 'none';
			break;
		case 'single_line':
			$border_width = '1px';
			$border_style = 'solid';
			break;
		case 'thick_line':
			$border_width = '3px';
			$border_style = 'solid';
			break;
		case 'double_lines':
			$border_width = '4px';
			$border_style = 'double';
			break;
		default:
			$border_width = '1px';
			$border_style = 'solid';
			break;
	}
	$border_style_var = "--ywcacc-border-width-$post_id: $border_width;\n--ywcacc-border-style-$post_id: $border_style;\n";

	return $border_style_var;
}

// ________________________________________________________________________________

//Parent options functions
/**
 * Get the parent color
 *
 * @param $color
 * @param $post_id
 *
 * @return string
 */
function get_parent_color( $color, $post_id ) {

	$parent_color     = unserialize( $color[0] );
	$parent_color_var = '';

	if ( is_array( $parent_color ) ) {
		$parent_text_color  = $parent_color['parent_text_color'] ?? '';
		$parent_hover_color = $parent_color['parent_hover_color'] ?? '';
		$parent_color_var   = "--ywcacc-parent-color-$post_id: $parent_text_color;\n--ywcacc-parent-hover-color-$post_id: $parent_hover_color;\n";
	}

	return $parent_color_var;
}

/**
 * Get the parent font weight
 *
 * @param $weight
 * @param $post_id
 *
 * @return string
 */
function get_parent_font_weight( $weight, $post_id ) {
	return get_font_weight_var( $weight, '-parent', $post_id );
}

/**
 * Get the parent font size
 *
 * @param $font
 * @param $post_id
 *
 * @return string
 */
function get_parent_font_size( $font, $post_id ) {
	return get_font_size_var( $font[0], '-parent', $post_id );
}

/**
 * Get the parent background color
 *
 * @param $bg_color
 * @param $post_id
 *
 * @return string
 */
function get_parent_bg_color( $bg_color, $post_id ) {

	$parent_bg_color  = unserialize( $bg_color[0] );
	$parent_color_var = '';
	if ( is_array( $parent_bg_color ) ) {
		$parent_bg_hover_color = $parent_bg_color['parent_hover_color'] ?? '';
		$parent_bg_color       = $parent_bg_color['parent_default_color'] ?? '';
		$parent_color_var      = "--ywcacc-parent-bg-color-$post_id: $parent_bg_color;\n--ywcacc-parent-bg-hover-color-$post_id: $parent_bg_hover_color;\n";
	}

	return $parent_color_var;
}

// ________________________________________________________________________________
//Child options functions

/**
 * Get the child color
 *
 * @param $color
 * @param $post_id
 *
 * @return string
 */
function get_child_color( $color, $post_id ) {

	$child_color     = unserialize( $color[0] );
	$child_color_var = '';
	if ( is_array( $child_color ) ) {
		$child_text_color  = $child_color['child_text_color'] ?? '';
		$child_hover_color = $child_color['child_hover_color'] ?? '';
		$child_color_var   = "--ywcacc-child-color-$post_id: $child_text_color;\n--ywcacc-child-hover-color-$post_id: $child_hover_color;\n";
	}

	return $child_color_var;
}

/**
 * Get the child font weight
 *
 * @param $weight
 * @param $post_id
 *
 * @return string
 */
function get_child_font_weight( $weight, $post_id ) {
	return get_font_weight_var( $weight, '-child', $post_id );
}

/**
 * Get the child font size
 *
 * @param $font
 * @param $post_id
 *
 * @return string
 */
function get_child_font_size( $font, $post_id ) {
	return get_font_size_var( $font[0], '-child', $post_id );
}

/**
 * Get the child background color
 *
 * @param $bg_color
 * @param $post_id
 *
 * @return string
 */
function get_child_bg_color( $bg_color, $post_id ) {
	$child_bg_color  = unserialize( $bg_color[0] );
	$child_color_var = '';
	if ( is_array( $child_bg_color ) ) {
		$child_bg_hover_color = $child_bg_color['child_hover_color'] ?? '';
		$child_bg_color       = $child_bg_color['child_default_color'] ?? '';
		$child_color_var      = "--ywcacc-child-bg-color-$post_id: $child_bg_color;\n--ywcacc-child-bg-hover-color-$post_id: $child_bg_hover_color;\n";
	}

	return $child_color_var;
}

// ________________________________________________________________________________

/**
 * Get the border radius
 *
 * @param $borders
 * @param $post_id
 *
 * @return string
 */
function get_border_radius( $borders, $post_id ) {

	$borders           = unserialize( $borders[0] );
	$border_radius_var = '';
	if ( is_array( $borders ) ) {
		$border_top    = $borders['dimensions']['top'] ?? 0;
		$border_right  = $borders['dimensions']['right'] ?? 0;
		$border_bottom = $borders['dimensions']['bottom'] ?? 0;
		$border_left   = $borders['dimensions']['left'] ?? 0;

		$border_radius_var = "--ywcacc-border-radius-top-$post_id: $border_top". "px;\n --ywcacc-border-radius-right-$post_id: $border_right". "px;\n--ywcacc-border-radius-bottom-$post_id: $border_bottom". "px;\n--ywcacc-border-radius-left-$post_id: $border_left". "px;\n ";
	}

	return $border_radius_var;
}

/**
 * Get the count category colors
 *
 * @param $colors
 * @param $post_id
 *
 * @return string
 */
function get_count_colors( $colors, $post_id ) {
	$colors          = unserialize( $colors[0] );
	$count_color_var = '';

	if ( is_array( $colors ) ) {
		$count_color        = $colors['text_color'] ?? '';
		$count_bg_color     = $colors['background_color'] ?? '';
		$count_border_color = $colors['border_color'] ?? '';

		$count_color_var = "--ywcacc-count-color-$post_id: $count_color;\n--ywcacc-count-bg-color-$post_id: $count_bg_color;\n--ywcacc-count-border-color-$post_id: $count_border_color;\n";
	}

	return $count_color_var;
}

/**
 * Get the toggle icon
 *
 * @param $toggle_icon
 * @param $post_id
 *
 * @return string
 */
function get_toggle_icon( $toggle_icon, $post_id ) {

	$img_toggle     = array();
	$toggle_icon    = $toggle_icon[0];
	$img_toggle_var = '';

	if ( ! empty( $toggle_icon ) ) {
		switch ( $toggle_icon ) {
			case 'plus_icon':
				$img_toggle = [
					'close'      => '\\e902',
					'open'       => '\\e903',
					'close_size' => 3 . 'px',
					'open_size'  => 12 . 'px',
				];
				break;

			case 'arrow_icon':
				$img_toggle = [
					'close'      => '\\e900',
					'open'       => '\\e901',
					'close_size' => 7 . 'px',
					'open_size'  => 15 . 'px',
				];
				break;
		}

		$img_toggle_var = "--ywcacc-image-url-icon-close-$post_id: '" . $img_toggle['close'] . "';\n--ywcacc-image-url-icon-open-$post_id:'" . $img_toggle['open'] . "';\n--ywcacc-style-size-close-$post_id:" . $img_toggle['close_size'] . ";\n--ywcacc-style-size-open-$post_id:" . $img_toggle['open_size'] . ";
    \n";
	}

	return $img_toggle_var;
}

/**
 * Get the toggle icon style
 *
 * @param $style_icon
 * @param $post_id
 *
 * @return string
 */
function get_toggle_icon_style( $style_icon, $post_id ) {

	$style_icon     = $style_icon[0];
	$style_icon_var = "--ywcacc-style-margin-right-$post_id: -2px;\n";

	if ( 'circle_style' === $style_icon ) {
		$style_icon_var = "--ywcacc-style-icon-border-$post_id: 50%;\n--ywcacc-style-icon-plus-padding-$post_id: 1px 1px;\n--ywcacc-style-icon-minus-padding-$post_id: 5px 1px;\n--ywcacc-style-margin-right-$post_id: 2px;\n";
	} elseif ( 'square_style' === $style_icon ) {
		$style_icon_var = "--ywcacc-style-icon-border-$post_id: 0px; \n--ywcacc-style-icon-minus-padding-$post_id: 3px\n;--ywcacc-style-margin-right-$post_id: 5px;\n";
	}

	return $style_icon_var;
}

/**
 * Get the toggle colors
 *
 * @param $colors
 * @param $post_id
 *
 * @return string
 */
function get_toggle_colors( $colors, $post_id ) {
	$colors_var = '';
	if ( $colors[0] ) {
		$colors = unserialize( $colors[0] );

		foreach ( $colors as $key => $color ) {
			$colors_var .= "--ywcacc-toggle-" . str_replace( '_', '-', $key ) . - $post_id . ":" . $color . ";\n";
		}

        if(count($colors) ==2){
            $colors_var .= "--ywcacc-toggle-border-color-" . $post_id . ":transparent;\n";
            $colors_var .= "--ywcacc-toggle-border-hover-color-" . $post_id . ":transparent;\n";
        }
	}

	return $colors_var;
}

/**
 * Get the toggle icon position
 *
 * @param $position
 * @param $post_id
 *
 * @return string
 */

if ( ! function_exists( 'ywcca_generate_style_from_post' ) ) {

	/**
	 * This function generates the styles from the post passed through the shortcode
	 *
	 * @param $post_id
	 *
	 * @return string
	 */
	function ywcca_generate_style_from_post( $post_id ) {

		$post_metas      = get_post_meta( $post_id );

		$prefix_css_post = ".ywcca_container.ywcca_widget_container_$post_id";

		$special_cases = array(
			//General Style
            '_ywcacc_container_colors',
			'_ywcacc_border_radius',
			'_ywcacc_count_colors',
			'_ywcacc_toggle_icon_style',
			'_ywcacc_toggle_icon',
			'_ywcacc_toggle_colors',

			//Title Options
			'_ywcacc_font_weight',
			'_ywcacc_font_size',
			'_ywcacc_border_style',

			// Parent Category options
			'_ywcacc_parent_color',
			'_ywcacc_parent_font_weight',
			'_ywcacc_parent_font_size',
			'_ywcacc_parent_bg_color',

			// Child category options
			'_ywcacc_child_color',
			'_ywcacc_child_font_weight',
			'_ywcacc_child_font_size',
			'_ywcacc_child_bg_color',

		);
		$css           = ":root {
    ";

		if(is_array($post_metas) && ! empty($post_metas)) {
			foreach ( $post_metas as $key => $meta_value ) {
				if ( str_contains( $key, '_ywcacc' ) ) { // Ojo!! version 8.0 PHP, use strpos()
					$variable_name = '-' . str_replace( '_', '-', $key );

					if ( in_array( $key, $special_cases ) ) {
						$function_special_name = 'get' . str_replace( '_ywcacc', '', $key );
						if ( function_exists( $function_special_name ) ) {
							$css .= $function_special_name( $meta_value, $post_id );
						}
					} else {
						$css .= $variable_name . "-$post_id" . ": " . $meta_value[0] . ";\n";
					}
				}
			}
		}
		$css .= "}";
		$css .= "
			$prefix_css_post{
				background-color: var(--ywcacc-container-bg-" . $post_id . ");
				border: 1px solid var(--ywcacc-container-border-" . $post_id . ");
				border-top-left-radius: var(--ywcacc-border-radius-top-" . $post_id . ");
				border-top-right-radius: var(--ywcacc-border-radius-right-" . $post_id . ");
				border-bottom-left-radius: var(--ywcacc-border-radius-bottom-" . $post_id . ");
				border-bottom-right-radius: var(--ywcacc-border-radius-left-" . $post_id . ");
			}
			
			$prefix_css_post .ywcca_widget_title {
				color: var(--ywcacc-color-title-" . $post_id . ");
				font-weight: var(--ywcacc-font-weight-" . $post_id . ");
				text-align:  var(--ywcacc-alignment-" . $post_id . ");
				text-transform: var(--ywcacc-text-transform-" . $post_id . ");
				font-weight: var(--ywcacc-font-weight-" . $post_id . ");
				font-style: var(--ywcacc-font-style-" . $post_id . ");
				font-size: var(--ywcacc-font-size-" . $post_id . ");
				border-bottom-style: var(--ywcacc-border-style-" . $post_id . "); 
				border-bottom-color: var(--ywcacc-border-color-" . $post_id . ");
				border-bottom-width: var(--ywcacc-border-width-" . $post_id . ");
				padding-bottom: 7px;
			}
			
			$prefix_css_post ul.category_accordion > .cat-item > a{
				color: var(--ywcacc-parent-color-" . $post_id . ");
				text-transform:  var(--ywcacc-parent-text-transform-" . $post_id . ");
				font-weight: var(--ywcacc-parent-font-weight-" . $post_id . ");
				font-style: var(--ywcacc-parent-font-style-" . $post_id . ");
				font-size: var(--ywcacc-parent-font-size-" . $post_id . ");
			}

			$prefix_css_post ul.category_accordion > .cat-item > a:hover{
				color: var(--ywcacc-parent-hover-color-" . $post_id . ");
			}

			$prefix_css_post ul.category_accordion > .cat-item {
				background-color: var(--ywcacc-parent-bg-color-" . $post_id . ");
				border-top: 2px solid var(--ywcacc-parent-border-color-" . $post_id . ");
				padding-top: 10px;
			}

			$prefix_css_post .yith-children li.cat-item a{
				color: var(--ywcacc-child-color-" . $post_id . ");
				text-transform:  var(--ywcacc-child-text-transform-" . $post_id . ");
				font-weight: var(--ywcacc-child-font-weight-" . $post_id . ");
				font-style: var(--ywcacc-child-font-style-" . $post_id . ");
				font-size: var(--ywcacc-child-font-size-" . $post_id . ");
			}

			$prefix_css_post .yith-children {
				background-color: var(--ywcacc-child-bg-color-" . $post_id . ");
			}

			$prefix_css_post .yith-children li.cat-item {
				border-top: 2px solid var(--ywcacc-child-border-color-" . $post_id . ");
				margin-left: 28px;
			}

			$prefix_css_post .yith-children li.cat-item a:hover{ 
				color: var(--ywcacc-child-hover-color-" . $post_id . ");
			}

			$prefix_css_post .yith-children li.cat-item:hover{
				background-color: var(--ywcacc-child-bg-hover-color-" . $post_id . ");
			}

			$prefix_css_post ul.ywcca_category_accordion_widget li span.rectangle_count{
				border: 1px solid var(--ywcacc-count-border-color-" . $post_id . ");
			}

			$prefix_css_post ul.ywcca_category_accordion_widget li span.round_count{
				border: 1px solid var(--ywcacc-count-border-color-" . $post_id . ");
			}
			
			$prefix_css_post ul.ywcca_category_accordion_widget li span.default_count span.default_count_bracket{
				color: var(--ywcacc-count-border-color-" . $post_id . ");
			}

			$prefix_css_post .category_accordion li.cat-item span {
				color: var(--ywcacc-count-color-" . $post_id . ");
				background-color: var(--ywcacc-count-bg-color-" . $post_id . ");
			}

			ul.category_accordion > .cat-item .icon-plus_$post_id{
				cursor: pointer;
				display: inline-block;
				width: 20px;
				margin-right: var(--ywcacc-style-margin-right-$post_id);
				margin-top: 3px;
			}

			ul.category_accordion > .cat-item .icon-plus_$post_id:before{
				font-family: 'ywcca_font';
				content: var(--ywcacc-image-url-icon-open-" . $post_id . ");
				color: var(--ywcacc-toggle-icon-color-" . $post_id . ");
				font-size: var(--ywcacc-style-size-open-" . $post_id . ");
				border-width: 1px;
				border-style: solid;
				border-color: var(--ywcacc-toggle-border-color-" . $post_id . ");
				background-color: var(--ywcacc-toggle-background-color-" . $post_id . ");
				border-radius: var(--ywcacc-style-icon-border-" . $post_id . ");
				padding: var(--ywcacc-style-icon-plus-padding-" . $post_id . ");
				vertical-align: middle;
			}

			ul.category_accordion > .cat-item .icon-plus_$post_id:hover:before{
				color: var(--ywcacc-toggle-icon-hover-color-" . $post_id . ");
				border-width: 1px;
				border-style: solid;
				border-color: var(--ywcacc-toggle-border-hover-color-" . $post_id . ");
				background-color: var(--ywcacc-toggle-background-hover-color-" . $post_id . ");
			}

			ul.category_accordion > .cat-item .icon-minus_$post_id{
				cursor: pointer;
				display: inline-block;
				width: 20px;
				margin-right: var(--ywcacc-style-margin-right-$post_id);
				margin-top: 3px;
				vertical-align: middle;
			}

			ul.category_accordion > .cat-item .icon-minus_$post_id:before{
				font-family: 'ywcca_font';
				content: var(--ywcacc-image-url-icon-close-" . $post_id . ");
				color: var(--ywcacc-toggle-icon-color-" . $post_id . ");
				font-size: var(--ywcacc-style-size-close-" . $post_id . ");
				vertical-align: inherit;
				border-width: 1px;
				border-style: solid;
				border-color: var(--ywcacc-toggle-border-color-" . $post_id . ");
				background-color: var(--ywcacc-toggle-background-color-" . $post_id . ");
				border-radius: var(--ywcacc-style-icon-border-" . $post_id . ");
				padding: var(--ywcacc-style-icon-minus-padding-" . $post_id . ");
				vertical-align: super;
			}
			
			ul.category_accordion > .cat-item .icon-minus_$post_id:hover:before{
				color: var(--ywcacc-toggle-icon-hover-color-" . $post_id . ");
				border-width: 1px;
				border-style: solid;
				border-color: var(--ywcacc-toggle-border-hover-color-" . $post_id . ");
				background-color: var(--ywcacc-toggle-background-hover-color-" . $post_id . ");
			}
			
			li.cat-item-none {
				color: var(--ywcacc-parent-color-" . $post_id . ");
				text-transform:  var(--ywcacc-parent-text-transform-" . $post_id . ");
				font-weight: var(--ywcacc-parent-font-weight-" . $post_id . ");
				font-style: var(--ywcacc-parent-font-style-" . $post_id . ");
				font-size: var(--ywcacc-parent-font-size-" . $post_id . ");
				margin-left: 25px;
			}
";

		return $css;
	}
}
