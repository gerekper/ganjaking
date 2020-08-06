<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $width
 * @var $css
 * @var $offset
 * @var $content - shortcode content
 *
 * Extra Params
 * @var $is_section
 * @var $section_skin
 * @var $section_color_scale
 * @var $section_skin_scale
 * @var $section_text_color
 * @var $text_align
 * @var $remove_margin_top
 * @var $remove_margin_bottom
 * @var $remove_padding_top
 * @var $remove_padding_bottom
 * @var $remove_border
 * @var $show_divider
 * @var $divider_pos
 * @var $divider_color
 * @var $divider_height
 * @var $show_divider_icon
 * @var $divider_icon_type
 * @var $divider_icon_image
 * @var $divider_icon
 * @var $divider_icon_simpleline
 * @var $divider_icon_skin
 * @var $divider_icon_style
 * @var $divider_icon_pos
 * @var $divider_icon_size
 * @var $divider_icon_color
 * @var $divider_icon_bg_color
 * @var $divider_icon_border_color
 * @var $divider_icon_wrap_border_color
 * @var $is_sticky
 * @var $sticky_container_selector
 * @var $sticky_min_width
 * @var $sticky_top
 * @var $sticky_bottom
 * @var $sticky_active_class
 * @var $animation_type
 * @var $animation_duration
 * @var $animation_delay
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Column
 */
$parallax_speed_bg = $parallax_speed_video = $parallax = $parallax_image = $video_bg = $video_bg_url = $video_bg_parallax = '';
$el_id  = '';
$output = '';
$atts   = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$width = wpb_translateColumnWidthToSpan( $width );
$width = vc_column_offset_class_merge( $offset, $width );

$el_class    = $this->getExtraClass( $el_class );
$vc_css      = vc_shortcode_custom_css_class( $css );
$css_classes = array(
	$el_class,
	'vc_column_container',
	$width,
);

if ( vc_is_inline() ) {
	$css_classes[] = 'wpb_column';
}

if ( $is_section ) {
	$css_classes[] .= ' section';
	if ( $section_skin ) {
		$css_classes[] .= 'section-' . $section_skin;
		if ( $section_skin_scale ) {
			$css_classes[] .= 'section-' . $section_skin . '-' . $section_skin_scale;
		}
	}
	if ( 'default' == $section_skin && $section_color_scale ) {
		$css_classes[] .= 'section-default-' . $section_color_scale;
	}
	if ( $section_text_color ) {
		$css_classes[] .= 'section-text-' . $section_text_color;
	}
}

if ( $remove_margin_top ) {
	$css_classes[] .= 'mt-0';
}

if ( $remove_margin_bottom ) {
	$css_classes[] .= 'mb-0';
}

if ( $remove_padding_top ) {
	$css_classes[] .= 'pt-0';
}

if ( $remove_padding_bottom ) {
	$css_classes[] .= 'pb-0';
}

if ( $remove_border ) {
	$css_classes[] .= 'section-no-borders';
}

$divider_output = '';
if ( $is_section && $show_divider ) {
	if ( 'bottom' === $divider_pos ) {
		$css_classes[] .= 'section-with-divider-footer';
	} else {
		$css_classes[] .= 'section-with-divider';
	}

	$divider_classes = array( 'section-divider', 'divider', 'divider-solid' );
	if ( 'custom' != $divider_icon_skin ) {
		$divider_classes[] = 'divider-' . $divider_icon_skin;
	}
	if ( $divider_icon_style ) {
		$divider_classes[] = 'divider-' . $divider_icon_style;
	}
	if ( $divider_icon_size ) {
		$divider_classes[] = 'divider-icon-' . $divider_icon_size;
	}
	if ( $divider_icon_pos ) {
		$divider_classes[] = 'divider-' . $divider_icon_pos;
	}

	$divider_inline_style = '';
	if ( $divider_color ) {
		$divider_inline_style .= 'background-color:' . $divider_color . ';';
	}
	if ( $divider_height ) {
		$divider_inline_style .= 'height:' . (int) $divider_height . 'px;';
	}
	if ( $remove_border ) {
		if ( 'bottom' === $divider_pos ) {
			$divider_inline_style .= 'margin-bottom: -51px;';
		} else {
			$divider_inline_style .= 'margin-top: -51px;';
		}
	}
	if ( $divider_inline_style ) {
		$divider_inline_style = ' style="' . esc_attr( $divider_inline_style ) . '"';
	}

	switch ( $divider_icon_type ) {
		case 'simpleline':
			$divider_icon_class = $divider_icon_simpleline;
			break;
		case 'image':
			$divider_icon_class = 'icon-image';
			break;
		default:
			$divider_icon_class = $divider_icon;
	}

	$divider_class = 'divider' . rand();
	if ( $show_divider_icon && $divider_icon_class && 'custom' == $divider_icon_skin && ( $divider_icon_color || $divider_icon_bg_color || $divider_icon_border_color || $divider_icon_wrap_border_color ) ) :
		$divider_classes[] = $divider_class;
		?>
		<style>
		<?php
		if ( $divider_icon_color || $divider_icon_bg_color || $divider_icon_border_color ) :
			?>
			.<?php echo esc_html( $divider_class ); ?> i {
				<?php
				if ( $divider_icon_color ) :

					?>
				color: <?php echo esc_html( $divider_icon_color ); ?> !important;
					<?php
endif;
				if ( $divider_icon_bg_color ) :

					?>
				background-color: <?php echo esc_html( $divider_icon_bg_color ); ?> !important;
					<?php
endif;
				if ( $divider_icon_border_color ) :

					?>
				border-color: <?php echo esc_html( $divider_icon_border_color ); ?> !important;
					<?php
endif;
				?>
			}
			<?php
			endif;
		if ( $divider_icon_wrap_border_color ) :
			?>
			.<?php echo esc_html( $divider_class ); ?> i:after {
				<?php
				if ( $divider_icon_wrap_border_color ) :

					?>
				border-color: <?php echo esc_html( $divider_icon_wrap_border_color ); ?> !important;
					<?php
endif;
				?>
			}
			<?php
			endif;
		?>
			</style>
		<?php
	endif;

	$divider_output = '<div class="' . esc_attr( implode( ' ', $divider_classes ) ) . '"' . $divider_inline_style . '>';
	if ( $show_divider_icon && $divider_icon_class ) {
		$divider_output .= '<i class="' . esc_attr( $divider_icon_class ) . '">';
		if ( 'icon-image' == $divider_icon_class && $divider_icon_image ) {
			$divider_icon_image = preg_replace( '/[^\d]/', '', $divider_icon_image );
			$divider_image_url  = wp_get_attachment_url( $divider_icon_image );
			$alt_text           = get_post_meta( $divider_icon_image, '_wp_attachment_image_alt', true );
			$divider_image_url  = str_replace( array( 'http:', 'https:' ), '', $divider_image_url );
			if ( $divider_image_url ) {
				$divider_output .= '<img alt="' . esc_attr( $alt_text ) . '" src="' . esc_url( $divider_image_url ) . '">';
			}
		}
		$divider_output .= '</i>';
	}
	$divider_output .= '</div>';
}

if ( $text_align ) {
	$css_classes[] .= 'text-' . $text_align;
}

$wrapper_attributes = array();

/* parallax */
$has_video_bg = ( ! empty( $video_bg ) && ! empty( $video_bg_url ) && vc_extract_youtube_id( $video_bg_url ) );

$parallax_speed = $parallax_speed_bg;
if ( $has_video_bg ) {
	$parallax       = $video_bg_parallax;
	$parallax_speed = $parallax_speed_video;
	$parallax_image = $video_bg_url;
	$css_classes[]  = 'vc_video-bg-container';
	wp_enqueue_script( 'vc_youtube_iframe_api_js' );
}

if ( ! empty( $parallax ) ) {
	wp_enqueue_script( 'vc_jquery_skrollr_js' );
	$wrapper_attributes[] = 'data-vc-parallax="' . esc_attr( $parallax_speed ) . '"'; // parallax speed
	$css_classes[]        = 'vc_general vc_parallax vc_parallax-' . $parallax;
	if ( false !== strpos( $parallax, 'fade' ) ) {
		$css_classes[]        = 'js-vc_parallax-o-fade';
		$wrapper_attributes[] = 'data-vc-parallax-o-fade="on"';
	} elseif ( false !== strpos( $parallax, 'fixed' ) ) {
		$css_classes[] = 'js-vc_parallax-o-fixed';
	}
}

if ( ! empty( $parallax_image ) ) {
	if ( $has_video_bg ) {
		$parallax_image_src = $parallax_image;
	} else {
		$parallax_image_id  = preg_replace( '/[^\d]/', '', $parallax_image );
		$parallax_image_src = wp_get_attachment_image_src( $parallax_image_id, 'full' );
		if ( ! empty( $parallax_image_src[0] ) ) {
			$parallax_image_src = $parallax_image_src[0];
		}
	}
	$wrapper_attributes[] = 'data-vc-parallax-image="' . esc_attr( $parallax_image_src ) . '"';
}
if ( ! $parallax && $has_video_bg ) {
	$wrapper_attributes[] = 'data-vc-video-bg="' . esc_attr( $video_bg_url ) . '"';
}

$use_inner_col = vc_is_inline() && ( $parallax || $has_video_bg );
if ( ! $use_inner_col ) {
	$css_classes[] = $vc_css;
}

// lazy load background image
global $porto_settings_optimize;
if ( isset( $porto_settings_optimize['lazyload'] ) && $porto_settings_optimize['lazyload'] && ! vc_is_inline() ) {
	preg_match( '/\.vc_custom_[^}]*(background-image:[^(]*([^)]*)|background:\s#[A-Fa-f0-9]{3,6}\s*url\(([^)]*))/', $css, $matches );
	if ( ! empty( $matches[2] ) || ! empty( $matches[3] ) ) {
		$image_url            = ! empty( $matches[2] ) ? $matches[2] : $matches[3];
		$wrapper_attributes[] = 'data-original="' . esc_url( trim( str_replace( array( '(', ')' ), '', $image_url ) ) ) . '"';
		$css_classes[]        = 'porto-lazyload';
	}
}


$css_class            = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

if ( $animation_type ) {
	$wrapper_attributes[] = 'data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$wrapper_attributes[] = 'data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$wrapper_attributes[] = 'data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}

$output .= '<div ' . implode( ' ', $wrapper_attributes ) . '>';

if ( $show_divider && ! $divider_pos ) {
	$output .= $divider_output;
}

if ( $is_sticky ) {
	$options                      = array();
	$options['containerSelector'] = $sticky_container_selector;
	$options['minWidth']          = (int) $sticky_min_width;
	$options['padding']['top']    = (int) $sticky_top;
	$options['padding']['bottom'] = (int) $sticky_bottom;
	$options['activeClass']       = $sticky_active_class;
	$options                      = json_encode( $options );

	$output .= '<div class="vc_column-inner' . ( $use_inner_col ? ' ' . esc_attr( trim( $vc_css ) ) : '' ) . '" data-plugin-sticky data-plugin-options="' . esc_attr( $options ) . '">';
}

$output .= '<div class="wpb_wrapper' . ( $is_sticky ? '' : ' vc_column-inner' . ( $use_inner_col ? ' ' . esc_attr( trim( $vc_css ) ) : '' ) ) . '">';
$output .= wpb_js_remove_wpautop( $content );
$output .= '</div>';

if ( $show_divider && 'bottom' === $divider_pos ) {
	$output .= $divider_output;
}

if ( $is_sticky ) {
	$output .= '</div>';
}

$output .= '</div>';

echo porto_filter_output( $output );
