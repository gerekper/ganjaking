<?php
extract( // @codingStandardsIgnoreLine
	shortcode_atts(
		array(
			'source'             => '',
			'cf7_form'           => '',
			'wpform'             => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$wrap_cls   = '';
$wrap_attrs = '';
if ( ! empty( $shortcode_class ) ) {
	$wrap_cls .= $shortcode_class;
}
if ( $el_class ) {
	$wrap_cls .= ' ' . trim( $el_class );
}
if ( $wrap_cls ) {
	$wrap_attrs .= ' class="' . esc_attr( trim( $wrap_cls ) ) . '"';
}
if ( $animation_type ) {
	$wrap_attrs .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$wrap_attrs .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$wrap_attrs .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
if ( $wrap_attrs ) {
	echo '<div' . $wrap_attrs . '>';
}

if ( empty( $source ) && $cf7_form ) { // Contact Form 7
	$form_id = absint( $cf7_form );

	echo do_shortcode( '[contact-form-7 id="' . $form_id . '" title=""]' );
} elseif ( 'wpforms' == $source && $wpform ) { // WPForms Lite
	$form_id = absint( $wpform );

	echo do_shortcode( '[wpforms id="' . $form_id . '" title="false"]' );
}

if ( $wrap_attrs ) {
	echo '</div>';
}
