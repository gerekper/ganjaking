<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $source
 * @var $image
 * @var $custom_src
 * @var $onclick
 * @var $img_size
 * @var $external_img_size
 * @var $caption
 * @var $img_link_large
 * @var $link
 * @var $img_link_target
 * @var $alignment
 * @var $el_class
 * @var $css_animation
 * @var $style
 * @var $external_style
 * @var $border_color
 * @var $css
 *
 * Extra Params
 * @var $lightbox
 * @var $image_gallery
 * @var $container_class
 * @var $zoom_icon
 * @var $hover_effect
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Single_image
 */
$source = $add_caption = $css_animation = $animation_type = $animation_delay = $animation_duration = '';

$floating_start_pos  = '';
$floating_speed      = '';
$floating_transition = 'yes';
$floating_horizontal = '';
$floating_duration   = '';


// dynamic image
$enable_image_dynamic   = false;
$image_dynamic_source   = '';
$image_dynamic_content  = '';
$image_dynamic_fallback = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
//dynamic text
$dynamic_image = false;
if ( $enable_image_dynamic ) {
	if ( ! empty( $image_dynamic_content ) ) {
		$image = Porto_Func_Dynamic_Tags_Content::get_instance()->dynamic_get_data( $image_dynamic_source, $image_dynamic_content, 'image' );
		if ( is_string( $image ) ) {
			$image = array(
				'id' => attachment_url_to_postid( $image ),
			);
		}
	}
	if ( empty( $image['id'] ) && ! empty( $image_dynamic_fallback ) ) {
		$image = array( 'id' => $image_dynamic_fallback );
	}
	$dynamic_image = true;
}

$default_src = vc_asset_url( 'vc/no_image.png' );

// backward compatibility. since 4.6
if ( empty( $onclick ) && isset( $img_link_large ) && 'yes' === $img_link_large ) {
	$onclick = 'img_link_large';
} elseif ( empty( $atts['onclick'] ) && ( ! isset( $atts['img_link_large'] ) || 'yes' !== $atts['img_link_large'] ) ) {
	$onclick = 'custom_link';
}

if ( 'external_link' === $source ) {
	$style = $external_style;
}

$border_color = ( '' !== $border_color ) ? ' vc_box_border_' . $border_color : '';

$img = false;

switch ( $source ) {
	case 'media_library':
	case 'featured_image':
		if ( 'featured_image' === $source ) {
			$post_id = get_the_ID();
			if ( $post_id && has_post_thumbnail( $post_id ) ) {
				$img_id = get_post_thumbnail_id( $post_id );
			} else {
				$img_id = 0;
			}
		} else {
			if ( $dynamic_image && ! empty( $image ) ) {
				$img_id = $image['id'];
				
				// If id is image link
				if ( false !== strpos( $img_id, 'http' )  ) {
					$img_id = attachment_url_to_postid( $img_id );
				}
			} else {
				$img_id = preg_replace( '/[^\d]/', '', $image );
			}
		}

		// set rectangular
		if ( preg_match( '/_circle_2$/', $style ) ) {
			$style    = preg_replace( '/_circle_2$/', '_circle', $style );
			$img_size = $this->getImageSquareSize( $img_id, $img_size );
		}
		
		if ( ! $img_size ) {
			$img_size = 'medium';
		}

		$img = wpb_getImageBySize(
			array(
				'attach_id'  => $img_id,
				'thumb_size' => strtolower( $img_size ),
				'class'      => 'vc_single_image-img',
			)
		);
		
		if ( isset( $img['thumbnail'] ) && false !== strpos( $img['thumbnail'], 'width="0" height="0"' ) ) {
			$img_size_arr = explode( 'x', $img_size );
			if ( 2 === count( $img_size_arr ) && is_numeric( $img_size_arr[0] ) && is_numeric( $img_size_arr[1] ) ) {
				$img['thumbnail'] = str_replace( 'width="0" height="0"', 'width="' . absint( $img_size_arr[0] ) . '" height="' . absint( $img_size_arr[1] ) . '"', $img['thumbnail'] );
			}
		}

		// don't show placeholder in public version if post doesn't have featured image
		if ( 'featured_image' === $source ) {
			if ( ! $img && 'page' === vc_manager()->mode() ) {
				return;
			}
		}

		break;

	case 'external_link':
		$dimensions = function_exists( 'vc_extract_dimensions' ) ? vc_extract_dimensions( $external_img_size ) : vcExtractDimensions( $external_img_size );
		$hwstring   = $dimensions ? image_hwstring( $dimensions[0], $dimensions[1] ) : '';

		$custom_src = $custom_src ? $custom_src : $default_src;

		$img = array(
			'thumbnail' => '<img class="vc_single_image-img" ' . $hwstring . ' src="' . esc_url( $custom_src ) . '" alt="external" />',
		);
		break;

	default:
		$img = false;
}

if ( ! $img ) {
	$img['thumbnail'] = '<img class="vc_img-placeholder vc_single_image-img" src="' . esc_url( $default_src ) . '" alt="placeholder image" />';
}

$el_class = $this->getExtraClass( $el_class );

// backward compatibility
if ( porto_has_class( 'prettyphoto', $el_class ) ) {
	$onclick = 'link_image';
}

// backward compatibility. will be removed in 4.7+
if ( ! empty( $atts['img_link'] ) ) {
	$link = $atts['img_link'];
	if ( ! preg_match( '/^(https?\:\/\/|\/\/)/', $link ) ) {
		$link = 'http://' . $link;
	}
}

// backward compatibility
if ( in_array( $link, array( 'none', 'link_no' ), true ) ) {
	$link = '';
}

$a_attrs = array();

switch ( $onclick ) {
	case 'img_link_large':
		if ( 'external_link' === $source ) {
			$link = $custom_src;
		} else {
			$link = wp_get_attachment_image_src( $img_id, 'large' );
			if ( isset( $link[0] ) ) {
				$link = $link[0];
			}
		}

		break;

	case 'link_image':
		wp_enqueue_script( 'prettyphoto' );
		wp_enqueue_style( 'prettyphoto' );

		$a_attrs['class']    = 'prettyphoto';
		$a_attrs['data-rel'] = 'prettyPhoto[rel-' . get_the_ID() . '-' . wp_rand() . ']';

		// backward compatibility
		if ( ! porto_has_class( 'prettyphoto', $el_class ) && 'external_link' === $source ) {
			$link = $custom_src;
		} elseif ( ! porto_has_class( 'prettyphoto', $el_class ) ) {
			$link = wp_get_attachment_image_src( $img_id, 'large' );
			$link = $link[0];
		}

		break;

	case 'custom_link':
		// $link is already defined
		break;

	case 'zoom':
		wp_enqueue_script( 'vc_image_zoom' );

		if ( 'external_link' === $source ) {
			$large_img_src = $custom_src;
		} else {
			$large_img_src = wp_get_attachment_image_src( $img_id, 'large' );
			if ( $large_img_src ) {
				$large_img_src = $large_img_src[0];
			}
		}

		$img['thumbnail'] = str_replace( '<img ', '<img data-vc-zoom="' . esc_url( $large_img_src ) . '" ', $img['thumbnail'] );

		break;
}

// backward compatibility
if ( porto_has_class( 'prettyphoto', $el_class ) ) {
	$el_class = vc_remove_class( 'prettyphoto', $el_class );
}

$html = ( 'vc_box_shadow_3d' === $style ) ? '<span class="vc_box_shadow_3d_wrap">' . $img['thumbnail'] . '</span>' : $img['thumbnail'];

if ( in_array( $source, array( 'media_library', 'featured_image' ), true ) && 'yes' === $add_caption ) {
	$post    = get_post( $img_id );
	$caption = $post->post_excerpt;
} elseif ( 'external_link' === $source ) {
	$add_caption = 'yes';
}

/* porto lightbox */
if ( $lightbox && 'img_link_large' == $onclick ) {
	if ( $hover_effect ) {
		$a_attrs['class'] = 'porto-vc-zoom porto-vc-zoom-hover-icon';
	} else {
		$a_attrs['class'] = 'porto-vc-zoom';
	}
	$a_attrs['data-gallery'] = $image_gallery ? 'true' : 'false';
	$a_attrs['data-title']   = $caption;
	if ( $image_gallery && 'vc_row' != $container_class ) {
		$a_attrs['data-container'] = $container_class;
	}
	if ( ! $hover_effect && $zoom_icon ) {
		$html .= '<div class="zoom-icon"></div>';
	}
}

$html = '<div class="vc_single_image-wrapper ' . esc_attr( $style ) . ' ' . esc_attr( $border_color ) . '">' . $html . '</div>';

if ( $link ) {
	$a_attrs['href']   = $link;
	$a_attrs['target'] = $img_link_target;
	$html              = '<a ' . porto_stringify_attributes( $a_attrs ) . '>' . $html . '</a>';
}

$class_to_filter  = 'wpb_single_image wpb_content_element vc_align_' . $alignment . ' ' . $this->getCSSAnimation( $css_animation );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$wrapper_attributes = '';
if ( $animation_type ) {
	$wrapper_attributes .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$wrapper_attributes .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$wrapper_attributes .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
} elseif ( $floating_start_pos && $floating_speed ) {
	$floating_options = array(
		'startPos' => $floating_start_pos,
		'speed'    => $floating_speed,
	);
	if ( $floating_transition ) {
		$floating_options['transition'] = true;
	} else {
		$floating_options['transition'] = false;
	}
	if ( $floating_horizontal ) {
		$floating_options['horizontal'] = true;
	} else {
		$floating_options['horizontal'] = false;
	}
	if ( $floating_duration ) {
		$floating_options['transitionDuration'] = absint( $floating_duration );
	}
	$wrapper_attributes .= ' data-plugin-float-element data-plugin-options="' . esc_attr( json_encode( $floating_options ) ) . '"';
}

if ( 'yes' === $add_caption && '' !== $caption ) {
	$html = '
		<figure class="vc_figure">
			' . $html . '
			<figcaption class="vc_figure-caption">' . esc_html( $caption ) . '</figcaption>
		</figure>
	';
}

$output = '
	<div class="' . esc_attr( trim( $css_class ) ) . '"' . $wrapper_attributes . '>
		<div class="wpb_wrapper">
			' . wpb_widget_title(
	array(
		'title'      => $title,
		'extraclass' => 'wpb_singleimage_heading',
	)
) . '
			' . $html . '
		</div>
	</div>
';

echo porto_filter_output( $output );
