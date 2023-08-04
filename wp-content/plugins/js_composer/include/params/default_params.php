<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder shortcode default attributes functions for rendering.
 *
 * @package WPBakeryPageBuilder
 * @since 4.4
 */
/**
 * Textfield shortcode attribute type generator.
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_textfield_form_field( $settings, $value ) {
	$value = is_string( $value ) ? htmlspecialchars( $value ) : '';

	return '<input name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-textinput ' . $settings['param_name'] . ' ' . $settings['type'] . '" type="text" value="' . $value . '"/>';
}

/**
 * Dropdown(select with options) shortcode attribute type generator.
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_dropdown_form_field( $settings, $value ) {
	$output = '';
	$css_option = str_replace( '#', 'hash-', vc_get_dropdown_option( $settings, $value ) );
	$output .= '<select name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . ' ' . $css_option . '" data-option="' . $css_option . '">';
	if ( is_array( $value ) ) {
		$value = isset( $value['value'] ) ? $value['value'] : array_shift( $value );
	}
	if ( ! empty( $settings['value'] ) ) {
		foreach ( $settings['value'] as $index => $data ) {
			if ( is_numeric( $index ) && ( is_string( $data ) || is_numeric( $data ) ) ) {
				$option_label = $data;
				$option_value = $data;
			} elseif ( is_numeric( $index ) && is_array( $data ) ) {
				$option_label = isset( $data['label'] ) ? $data['label'] : array_pop( $data );
				$option_value = isset( $data['value'] ) ? $data['value'] : array_pop( $data );
			} else {
				$option_value = $data;
				$option_label = $index;
			}
			$selected = '';
			$option_value_string = (string) $option_value;
			$value_string = (string) $value;
			if ( '' !== $value && $option_value_string === $value_string ) {
				$selected = 'selected="selected"';
			}
			$option_class = str_replace( '#', 'hash-', $option_value );
			$output .= '<option class="' . esc_attr( $option_class ) . '" value="' . esc_attr( $option_value ) . '" ' . $selected . '>' . htmlspecialchars( $option_label ) . '</option>';
		}
	}
	$output .= '</select>';

	return $output;
}

/**
 * Checkbox shortcode attribute type generator.
 *
 * @param $settings
 * @param string $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_checkbox_form_field( $settings, $value ) {
	$output = '';
	if ( is_array( $value ) ) {
		$value = ''; // fix #1239
	}
	$current_value = strlen( $value ) > 0 ? explode( ',', $value ) : array();
	$values = isset( $settings['value'] ) && is_array( $settings['value'] ) ? $settings['value'] : array( esc_html__( 'Yes', 'js_composer' ) => 'true' );
	if ( ! empty( $values ) ) {
		foreach ( $values as $label => $v ) {
			// NOTE!! Don't use strict compare here for BC!
			// @codingStandardsIgnoreLine
			$checked = in_array( $v, $current_value ) ? 'checked' : '';
			$output .= ' <label class="vc_checkbox-label"><input id="' . $settings['param_name'] . '-' . $v . '" value="' . $v . '" class="wpb_vc_param_value ' . $settings['param_name'] . ' ' . $settings['type'] . '" type="checkbox" name="' . $settings['param_name'] . '" ' . $checked . '>' . $label . '</label>';
		}
	}

	return $output;
}

add_filter( 'vc_map_get_param_defaults', 'vc_checkbox_param_defaults', 10, 2 );
/**
 * @param $value
 * @param $param
 * @return mixed|string
 */
function vc_checkbox_param_defaults( $value, $param ) {
	if ( 'checkbox' === $param['type'] ) {
		$value = '';
		if ( isset( $param['std'] ) ) {
			$value = $param['std'];
		}
	}

	return $value;
}

/**
 * Checkbox shortcode attribute type generator.
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_posttypes_form_field( $settings, $value ) {
	$output = '';
	$args = array(
		'public' => true,
	);
	$post_types = get_post_types( $args );
	foreach ( $post_types as $post_type ) {
		$checked = '';
		if ( 'attachment' !== $post_type ) {
			if ( in_array( $post_type, explode( ',', $value ), true ) ) {
				$checked = 'checked="checked"';
			}
			$output .= '<label class="vc_checkbox-label"><input id="' . $settings['param_name'] . '-' . $post_type . '" value="' . $post_type . '" class="wpb_vc_param_value ' . $settings['param_name'] . ' ' . $settings['type'] . '" type="checkbox" name="' . $settings['param_name'] . '" ' . $checked . '> ' . $post_type . '</label>';
		}
	}

	return $output;
}

/**
 * Taxonomies shortcode attribute type generator.
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_taxonomies_form_field( $settings, $value ) {
	$output = '';
	$post_types = get_post_types( array(
		'public' => false,
		'name' => 'attachment',
	), 'names', 'NOT' );
	foreach ( $post_types as $type ) {
		$taxonomies = get_object_taxonomies( $type, '' );
		foreach ( $taxonomies as $tax ) {
			$checked = '';
			if ( in_array( $tax->name, explode( ',', $value ), true ) ) {
				$checked = 'checked';
			}
			$output .= ' <label class="vc_checkbox-label" data-post-type="' . $type . '"><input id="' . $settings['param_name'] . '-' . $tax->name . '" value="' . $tax->name . '" data-post-type="' . $type . '" class="wpb_vc_param_value ' . $settings['param_name'] . ' ' . $settings['type'] . '" type="checkbox" name="' . $settings['param_name'] . '" ' . $checked . '> ' . $tax->label . '</label>';
		}
	}

	return $output;
}

/**
 * Exploded textarea shortcode attribute type generator.
 *
 * Data saved and coma-separated values are merged with line breaks and returned in a textarea.
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_exploded_textarea_form_field( $settings, $value ) {
	$value = str_replace( ',', "\n", $value );

	return '<textarea name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-textarea ' . $settings['param_name'] . ' ' . $settings['type'] . '">' . $value . '</textarea>';
}

/**
 * Safe Textarea shortcode attribute type generator.
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.8.2
 */
function vc_exploded_textarea_safe_form_field( $settings, $value ) {
	$value = vc_value_from_safe( $value, true );
	$value = str_replace( ',', "\n", $value );

	return '<textarea name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-textarea ' . $settings['param_name'] . ' ' . $settings['type'] . '">' . $value . '</textarea>';
}

/**
 * Textarea raw html shortcode attribute type generator.
 *
 * This attribute type allows safely add custom html to your post/page.
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_textarea_raw_html_form_field( $settings, $value ) {
	// @codingStandardsIgnoreLine
	return sprintf( '<textarea name="%s" class="wpb_vc_param_value wpb-textarea_raw_html %s %s" rows="16">%s</textarea>', $settings['param_name'], $settings['param_name'], $settings['type'], htmlentities( rawurldecode( base64_decode( $value ) ), ENT_COMPAT, 'UTF-8' ) );
}

/**
 * Safe Textarea shortcode attribute type generator.
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_textarea_safe_form_field( $settings, $value ) {
	return '<textarea name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-textarea_raw_html ' . $settings['param_name'] . ' ' . $settings['type'] . '">' . vc_value_from_safe( $value, true ) . '</textarea>';

}

/**
 * Textarea shortcode attribute type generator.
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_textarea_form_field( $settings, $value ) {
	return '<textarea name="' . $settings['param_name'] . '" class="wpb_vc_param_value wpb-textarea ' . $settings['param_name'] . ' ' . $settings['type'] . '">' . $value . '</textarea>';
}

/**
 * Attach images shortcode attribute type generator.
 *
 * @param $settings
 * @param $value
 *
 * @param $tag
 * @param bool $single
 *
 * @return string - html string.
 * @since 4.4
 *
 */
function vc_attach_images_form_field( $settings, $value, $tag, $single = false ) {
	$output = '';
	$param_value = wpb_removeNotExistingImgIDs( $value );
	$output .= '<input type="hidden" class="wpb_vc_param_value gallery_widget_attached_images_ids ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '" name="' . esc_attr( $settings['param_name'] ) . '" value="' . $value . '"/>';
	$output .= '<div class="gallery_widget_attached_images">';
	$output .= '<ul class="gallery_widget_attached_images_list">';
	$output .= ( '' !== $param_value ) ? vc_field_attached_images( explode( ',', $value ) ) : '';
	$output .= '</ul>';
	$output .= '</div>';
	$output .= '<div class="gallery_widget_site_images">';
	$output .= '</div>';
	if ( true === $single ) {
		$output .= '<a class="gallery_widget_add_images" href="javascript:;" use-single="true" title="' . esc_attr__( 'Add image', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-icon-add"></i>' . esc_html__( 'Add image', 'js_composer' ) . '</a>';
	} else {
		$output .= '<a class="gallery_widget_add_images" href="javascript:;" title="' . esc_attr__( 'Add images', 'js_composer' ) . '"><i class="vc-composer-icon vc-c-icon-add"></i>' . esc_html__( 'Add images', 'js_composer' ) . '</a>';
	}

	return $output;
}

/**
 * Attach image shortcode attribute type generator.
 *
 * @param $settings
 * @param $value
 *
 * @param $tag
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_attach_image_form_field( $settings, $value, $tag ) {
	return vc_attach_images_form_field( $settings, $value, $tag, true );
}

/**
 * Widgetised sidebars shortcode attribute type generator.
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_widgetised_sidebars_form_field( $settings, $value ) {
	$output = '';
	$sidebars = $GLOBALS['wp_registered_sidebars'];

	$output .= '<select name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value dropdown wpb-input wpb-select ' . $settings['param_name'] . ' ' . $settings['type'] . '">';
	foreach ( $sidebars as $sidebar ) {
		$selected = '';
		if ( $sidebar['id'] === $value ) {
			$selected = 'selected';
		}
		$sidebar_name = $sidebar['name'];
		$output .= '<option value="' . esc_attr( $sidebar['id'] ) . '" ' . $selected . '>' . $sidebar_name . '</option>';
	}
	$output .= '</select>';

	return $output;
}
