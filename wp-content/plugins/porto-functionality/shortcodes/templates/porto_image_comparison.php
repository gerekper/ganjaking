<?php

$enable_image_before_dynamic   = isset( $atts['enable_image_before_dynamic'] ) ? $atts['enable_image_before_dynamic'] : false;
$image_before_dynamic_source   = isset( $atts['image_before_dynamic_source'] ) ? $atts['image_before_dynamic_source'] : '';
$image_before_dynamic_content  = isset( $atts['image_before_dynamic_content'] ) ? $atts['image_before_dynamic_content'] : '';
$image_before_dynamic_fallback = isset( $atts['image_before_dynamic_fallback'] ) ? $atts['image_before_dynamic_fallback'] : '';

$enable_image_after_dynamic   = isset( $atts['enable_image_after_dynamic'] ) ? $atts['enable_image_after_dynamic'] : false;
$image_after_dynamic_source   = isset( $atts['image_after_dynamic_source'] ) ? $atts['image_after_dynamic_source'] : '';
$image_after_dynamic_content  = isset( $atts['image_after_dynamic_content'] ) ? $atts['image_after_dynamic_content'] : '';
$image_after_dynamic_fallback = isset( $atts['image_after_dynamic_fallback'] ) ? $atts['image_after_dynamic_fallback'] : '';

if ( $enable_image_before_dynamic ) {
	if ( ! empty( $image_before_dynamic_content ) ) {
		$image = Porto_Func_Dynamic_Tags_Content::get_instance()->dynamic_get_data( $image_before_dynamic_source, $image_before_dynamic_content, 'image' );
		if ( is_string( $image ) ) {
			$image = array(
				'id' => attachment_url_to_postid( $image ),
			);
		}
		if ( ! empty( $image['id'] ) ) {
			$atts['before_img'] = $image['id'];
		}
	}
	if ( empty( $banner_image ) && ! empty( $image_before_dynamic_fallback ) ) {
		$atts['before_img'] = $image_before_dynamic_fallback;
	}
}
if ( $enable_image_after_dynamic ) {
	if ( ! empty( $image_after_dynamic_content ) ) {
		$image = Porto_Func_Dynamic_Tags_Content::get_instance()->dynamic_get_data( $image_after_dynamic_source, $image_after_dynamic_content, 'image' );
		if ( is_string( $image ) ) {
			$image = array(
				'id' => attachment_url_to_postid( $image ),
			);
		}
		if ( ! empty( $image['id'] ) ) {
			$atts['after_img'] = $image['id'];
		}
	}
	if ( empty( $banner_image ) && ! empty( $image_after_dynamic_fallback ) ) {
		$atts['after_img'] = $image_after_dynamic_fallback;
	}
}

if ( ( empty( $atts['before_img'] ) && empty( $atts['after_img'] ) ) ) {
	if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
		wp_enqueue_script( 'jquery-event-move' );
		wp_enqueue_script( 'porto-image-comparison' );
	}
	return;
}

wp_enqueue_script( 'jquery-event-move' );
wp_enqueue_script( 'porto-image-comparison' );

$orientation   = isset( $atts['orientation'] ) ? $atts['orientation'] : 'horizontal';
$offset        = isset( $atts['offset'] ) ? $atts['offset'] : 50;
$handle_action = isset( $atts['movement'] ) ? $atts['movement'] : 'click';
$el_class      = '';
if ( ! empty( $shortcode_class ) ) {
	$el_class .= ' ' . $shortcode_class;
}
if ( ! empty( $atts['el_class'] ) ) {
	$el_class .= ' ' . trim( $atts['el_class'] );
}

$animation_type  = ! empty( $atts['animation_type'] ) ? $atts['animation_type'] : '';
$animation_attrs = '';
if ( $animation_type ) {
	$animation_attrs .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';

	$animation_delay    = isset( $atts['animation_delay'] ) ? $atts['animation_delay'] : '';
	$animation_duration = ! empty( $atts['animation_duration'] ) ? $atts['animation_duration'] : '';
	if ( $animation_delay ) {
		$animation_attrs .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 !== (int) $animation_duration ) {
		$animation_attrs .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
?>

<div class="porto-image-comparison<?php echo 'vertical' == $orientation ? ' porto-image-comparison-vertical' : '', esc_attr( $el_class ); ?>" data-orientation="<?php echo esc_attr( $orientation ); ?>" data-offset="<?php echo (int) $offset / 100; ?>" data-handle-action="<?php echo esc_attr( $handle_action ); ?>"<?php echo porto_filter_output( $animation_attrs ); ?>>
<?php
if ( ! empty( $atts['before_img'] ) ) {
	echo wp_get_attachment_image( $atts['before_img'], 'full', false, array( 'class' => 'porto-image-comparison-before' ) );
}

if ( ! empty( $atts['after_img'] ) ) {
	echo wp_get_attachment_image( $atts['after_img'], 'full', false, array( 'class' => empty( $atts['before_img'] ) ? 'porto-image-comparison-before' : 'porto-image-comparison-after' ) );
}
if ( empty( $atts['hide_overlay'] ) ) :
	?>
	<div class="porto-image-comparison-overlay">
		<div class="before-label"></div>
		<div class="after-label"></div>
	</div>
<?php endif; ?>
	<div class="porto-image-comparison-handle"><i class="<?php echo empty( $atts['icon_cl'] ) ? 'porto-compare-icon' : esc_attr( $atts['icon_cl'] ); ?>"></i></div>
</div>
