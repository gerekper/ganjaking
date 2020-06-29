<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $onclick
 * @var $custom_links
 * @var $custom_links_target
 * @var $img_size
 * @var $images
 * @var $el_class
 * @var $el_id
 * @var $mode
 * @var $slides_per_view
 * @var $wrap
 * @var $autoplay
 * @var $hide_pagination_control
 * @var $hide_prev_next_buttons
 * @var $speed
 * @var $partial_view
 * @var $css
 * @var $css_animation
 * Shortcode class
 * @var WPBakeryShortCode_Vc_images_carousel $this
 */
$title = $onclick = $custom_links = $custom_links_target = $img_size = $images = $el_class = $el_id = $mode = $slides_per_view = $wrap = $autoplay = $hide_pagination_control = $hide_prev_next_buttons = $speed = $partial_view = $css = $css_animation = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$gal_images = '';
$link_start = '';
$link_end = '';
$el_start = '';
$el_end = '';
$slides_wrap_start = '';
$slides_wrap_end = '';
$pretty_rand = 'link_image' === $onclick ? ' data-rel="prettyPhoto[rel-' . get_the_ID() . '-' . wp_rand() . ']"' : '';

wp_enqueue_script( 'vc_carousel_js' );
wp_enqueue_style( 'vc_carousel_css' );
if ( 'link_image' === $onclick ) {
	wp_enqueue_script( 'prettyphoto' );
	wp_enqueue_style( 'prettyphoto' );
}

if ( '' === $images ) {
	$images = '-1,-2,-3';
}

if ( 'custom_link' === $onclick ) {
	$custom_links = vc_value_from_safe( $custom_links );
	$custom_links = explode( ',', $custom_links );
}

$images = explode( ',', $images );
$i = - 1;

$class_to_filter = 'wpb_images_carousel wpb_content_element vc_clearfix';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$carousel_id = 'vc_images-carousel-' . WPBakeryShortCode_Vc_Images_Carousel::getCarouselIndex();
$slider_width = $this->getSliderWidth( $img_size );

$output = '';
$output .= '<div' . ( ! empty( $el_id ) ? ' id="' . esc_attr( $el_id ) . '"' : '' ) . ' class="' . esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_class, $this->settings['base'], $atts ) ) . '">';
$output .= '<div class="wpb_wrapper">';

$output .= wpb_widget_title( array(
	'title' => $title,
	'extraclass' => 'wpb_gallery_heading',
) );

$output .= '<div id="' . esc_attr( $carousel_id ) . '" data-ride="vc_carousel" data-wrap="' . ( 'yes' === $wrap ? 'true' : 'false' ) . '" style="width: ' . esc_attr( $slider_width ) . ';" data-interval="' . ( 'yes' === $autoplay ? esc_attr( $speed ) : 0 ) . '" data-auto-height="yes" data-mode="' . esc_attr( $mode ) . '" data-partial="' . ( 'yes' === $partial_view ? 'true' : 'false' ) . '" data-per-view="' . esc_attr( $slides_per_view ) . '" data-hide-on-end="' . ( 'yes' === $autoplay ? 'false' : 'true' ) . '" class="vc_slide vc_images_carousel">';
if ( 'yes' !== $hide_pagination_control ) {
	$output .= '<ol class="vc_carousel-indicators">';
	$count = count( $images );
	for ( $z = 0; $z < $count; $z ++ ) {
		$output .= '<li data-target="#' . esc_attr( $carousel_id ) . '" data-slide-to="' . esc_attr( $z ) . '"></li>';
	}
	$output .= '</ol>';
}

$output .= '<div class="vc_carousel-inner"><div class="vc_carousel-slideline"><div class="vc_carousel-slideline-inner">';
foreach ( $images as $attach_id ) {
	$i ++;
	if ( $attach_id > 0 ) {
		$post_thumbnail = wpb_getImageBySize( array(
			'attach_id' => $attach_id,
			'thumb_size' => $img_size,
		) );
	} else {
		$post_thumbnail = array();
		$post_thumbnail['thumbnail'] = '<img src="' . esc_url( vc_asset_url( 'vc/no_image.png' ) ) . '" />';
		$post_thumbnail['p_img_large'][0] = vc_asset_url( 'vc/no_image.png' );
	}
	$thumbnail = $post_thumbnail['thumbnail'];

	$output .= '<div class="vc_item"><div class="vc_inner">';
	if ( 'link_image' === $onclick ) {
		$p_img_large = $post_thumbnail['p_img_large'];
		$output .= '<a class="prettyphoto" href="' . esc_url( $p_img_large[0] ) . '"';
		$output .= $pretty_rand;
		$output .= '>';
		$output .= $thumbnail;

		$output .= '</a>';
	} elseif ( 'custom_link' === $onclick && isset( $custom_links[ $i ] ) && '' !== $custom_links[ $i ] ) {
		$output .= '<a href="' . esc_url( $custom_links[ $i ] ) . '"' . ( ! empty( $custom_links_target ) ? ' target="' . esc_attr( $custom_links_target ) . '"' : '' ) . '>';
		$output .= $thumbnail;
		$output .= '</a>';
	} else {
		$output .= $thumbnail;
	}
	$output .= '</div></div>';
}
$output .= '</div></div></div>';

if ( 'yes' !== $hide_prev_next_buttons ) {
	$output .= '<a class="vc_left vc_carousel-control" href="#' . esc_attr( $carousel_id ) . '" data-slide="prev"><span class="icon-prev"></span></a>';
	$output .= '<a class="vc_right vc_carousel-control" href="#' . esc_attr( $carousel_id ) . '" data-slide="next"><span class="icon-next"></span></a>';
}
$output .= '</div></div></div>';

return $output;
