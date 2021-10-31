<?php

if ( empty( $atts['before_img'] ) && empty( $atts['after_img'] ) ) {
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
?>
	<div class="porto-image-comparison-handle"><i class="<?php echo empty( $atts['icon_cl'] ) ? 'Simple-Line-Icons-cursor-move' : esc_attr( $atts['icon_cl'] ); ?>"></i></div>
</div>
