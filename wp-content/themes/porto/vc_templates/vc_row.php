<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $full_width
 * @var $full_height
 * @var $equal_height
 * @var $columns_placement
 * @var $content_placement
 * @var $parallax
 * @var $parallax_image
 * @var $css
 * @var $el_id
 * @var $video_bg
 * @var $video_bg_url
 * @var $video_bg_parallax
 * @var $parallax_speed_bg
 * @var $parallax_speed_video
 * @var $content - shortcode content
 *
 * Extra Params
 * @var $wrap_container
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
 * @var $this WPBakeryShortCode_VC_Row
 */
$el_class        = $full_height = $parallax_speed_bg = $parallax_speed_video = $full_width = $equal_height = $flex_row = $columns_placement = $content_placement = $parallax = $parallax_image = $css = $el_id = $video_bg = $video_bg_url = $video_bg_parallax = $no_padding = '';
$disable_element = '';
global $porto_settings, $porto_layout;
$output = $after_output = '';
$atts   = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

wp_enqueue_script( 'wpb_composer_front_js' );

$el_class = $this->getExtraClass( $el_class );

$css_classes = array(
	'vc_row',
	'wpb_row', //deprecated
	'vc_row-fluid',
	$el_class,
	vc_shortcode_custom_css_class( $css ),
);
if ( $no_padding ) {
	$css_classes[] = 'no-padding';
}

if ( 'yes' === $disable_element ) {
	if ( vc_is_page_editable() ) {
		$css_classes[] = 'vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
	} else {
		return '';
	}
}

if ( ! $wrap_container && ! empty( $atts['gap'] ) ) {
	$css_classes[] = 'vc_column-gap-' . $atts['gap'];
}

if ( $parallax && $parallax_image ) {
	if ( $is_section ) {
		$css_classes[] .= 'section';
		$css_classes[] .= 'section-parallax';
	}
	if ( $section_text_color ) {
		$css_classes[] .= 'section-text-' . $section_text_color;
	}
} elseif ( $is_section ) {
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

if ( vc_shortcode_custom_css_has_property( $css, array( 'border', 'background' ) ) || $video_bg || $parallax ) {
	$css_classes[] = 'vc_row-has-fill';
}

$wrapper_attributes = array();
// build attributes for wrapper
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
if ( ! empty( $full_width ) ) {
	$wrapper_attributes[] = 'data-vc-full-width="true"';
	$wrapper_attributes[] = 'data-vc-full-width-init="false"';
	if ( 'stretch_row_content' === $full_width ) {
		$wrapper_attributes[] = 'data-vc-stretch-content="true"';
	} elseif ( 'stretch_row_content_no_spaces' === $full_width ) {
		$wrapper_attributes[] = 'data-vc-stretch-content="true"';
		$css_classes[]        = 'vc_row-no-padding';
	} elseif ( porto_get_wrapper_type() == 'boxed' || ( porto_get_wrapper_type() != 'boxed' && 'boxed' == $porto_settings['main-wrapper'] ) ) {
		$wrapper_attributes[] = 'data-vc-stretch-content="true"';
	}
	$after_output .= '<div class="vc_row-full-width vc_clearfix"></div>';
}

if ( ! empty( $full_height ) ) {
	$css_classes[] = ' vc_row-o-full-height';
	if ( ! empty( $columns_placement ) ) {
		$flex_row      = true;
		$css_classes[] = ' vc_row-o-columns-' . $columns_placement;
		if ( 'stretch' === $columns_placement ) {
			$css_classes[] = 'vc_row-o-equal-height';
		}
	}
}

if ( ! empty( $equal_height ) ) {
	$flex_row      = true;
	$css_classes[] = ' vc_row-o-equal-height';
}

if ( ! empty( $content_placement ) && ! $wrap_container ) {
	$flex_row      = true;
	$css_classes[] = ' vc_row-o-content-' . $content_placement;
}

if ( ! empty( $flex_row ) ) {
	$css_classes[] = ' vc_row-flex';
}
$video_type = '';
if ( $video_bg && $video_bg_url ) {
	if ( strrpos( $video_bg_url, '.mp4' ) !== false ) {
		$video_type = 'mp4';
	}
	if ( strpos( $video_bg_url, 'youtube.com' ) !== false ) {
		$video_type = 'youtube';
	}
}
$has_video_bg   = ( ! empty( $video_bg ) && ! empty( $video_bg_url ) && vc_extract_youtube_id( $video_bg_url ) );
$parallax_speed = $parallax_speed_bg;
if ( $has_video_bg || ( 'mp4' === $video_type ) ) {
	$parallax       = $video_bg_parallax;
	$parallax_speed = $parallax_speed_video;
	$parallax_image = $video_bg_url;
	$css_classes[]  = ' vc_video-bg-container';
	if ( 'mp4' === $video_type && empty( $parallax ) ) {
		wp_enqueue_script( 'jquery-vide' );
		$css_classes[]        = ' section-video';
		$wrapper_attributes[] = 'data-video-path="' . esc_url( str_replace( '.mp4', '', $video_bg_url ) ) . '"';
		$wrapper_attributes[] = 'data-plugin-video-background';
		$wrapper_attributes[] = 'data-plugin-options="{\'posterType\': \'jpg\', \'position\': \'50% 50%\', \'overlay\': true}"';
	} elseif ( 'youtube' === $video_type ) {
		wp_enqueue_script( 'vc_youtube_iframe_api_js' );
	}
}

if ( ! empty( $parallax ) ) {
	wp_enqueue_script( 'vc_jquery_skrollr_js' );
	if ( 'mp4' === $video_type ) {
	} else {
		$wrapper_attributes[] = 'data-vc-parallax="' . esc_attr( $parallax_speed ) . '"'; // parallax speed
		$css_classes[]        = 'vc_general vc_parallax vc_parallax-' . $parallax;
		if ( false !== strpos( $parallax, 'fade' ) ) {
			$css_classes[]        = 'js-vc_parallax-o-fade';
			$wrapper_attributes[] = 'data-vc-parallax-o-fade="on"';
		} elseif ( false !== strpos( $parallax, 'fixed' ) ) {
			$css_classes[] = 'js-vc_parallax-o-fixed';
		}
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
	$wrapper_attributes[] = 'data-vc-parallax-image="' . esc_url( $parallax_image_src ) . '"';
}
if ( ! $parallax && $has_video_bg ) {
	$wrapper_attributes[] = 'data-vc-video-bg="' . esc_url( $video_bg_url ) . '"';
}
if ( $wrap_container ) {
	$css_classes[] = 'porto-inner-container';
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

$css_class            = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( array_unique( $css_classes ) ) ), $this->settings['base'], $atts ) );
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

$output .= '<div ' . implode( ' ', $wrapper_attributes ) . '>';
if ( 'mp4' === $video_type && ! empty( $parallax ) ) {
	wp_enqueue_script( 'jquery-vide' );
	$parallax_wrapper_attributes[] = 'data-vc-parallax="' . esc_attr( $parallax_speed ) . '"'; // parallax speed
	$parallax_css_classes          = 'vc_general vc_parallax vc_parallax-' . $parallax;
	$parallax_wrapper_attributes[] = 'data-video-path="' . esc_url( str_replace( '.mp4', '', $video_bg_url ) ) . '"';
	$parallax_wrapper_attributes[] = 'data-plugin-video-background';
	$parallax_wrapper_attributes[] = 'data-plugin-options="{\'posterType\': \'jpg\', \'position\': \'50% 50%\', \'overlay\': true}"';
	$end_percent                   = ( (float) $parallax_speed - 1 ) * 100;
	$parallax_wrapper_attributes[] = 'data-bottom-top="top:-' . esc_attr( $end_percent ) . '%;"';
	$parallax_wrapper_attributes[] = 'data-top-bottom="top: 0;"';
	$parallax_wrapper_attributes[] = 'style="height: ' . ( esc_attr( $parallax_speed ) * 100 ) . '%;"';
	$output                       .= '<div class="section-video skrollable skrollable-between ' . esc_attr( $parallax_css_classes ) . '" ' . implode( ' ', $parallax_wrapper_attributes ) . '></div>';
}

if ( $wrap_container ) {
	$align_items_cls_arr = array(
		'middle' => 'align-items-center',
		'top'    => 'align-items-start',
		'bottom' => 'align-items-end',
	);
	$output             .= '<div class="porto-wrap-container container"><div class="row' . ( $content_placement ? ' ' . $align_items_cls_arr[ $content_placement ] : '' ) . ( ! empty( $atts['gap'] ) ? ' vc_column-gap-' . esc_attr( $atts['gap'] ) : '' ) . '">';
}

if ( $is_sticky ) {
	$options                      = array();
	$options['containerSelector'] = $sticky_container_selector;
	$options['minWidth']          = (int) $sticky_min_width;
	$options['padding']['top']    = (int) $sticky_top;
	$options['padding']['bottom'] = (int) $sticky_bottom;
	$options['activeClass']       = $sticky_active_class;
	$options                      = json_encode( $options );

	$output .= '<div data-plugin-sticky data-plugin-options="' . esc_attr( $options ) . '">';
}

if ( $show_divider && ! $divider_pos ) {
	$output .= $divider_output;
}

$output .= wpb_js_remove_wpautop( $content );

if ( $show_divider && 'bottom' === $divider_pos ) {
	$output .= $divider_output;
}

if ( $is_sticky ) {
	$output .= '</div>';
}

if ( $wrap_container ) {
	$output .= '</div></div>';
}

$output .= '</div>';
$output .= $after_output;

// @codingStandardsIgnoreLine
echo porto_filter_output( $output );
