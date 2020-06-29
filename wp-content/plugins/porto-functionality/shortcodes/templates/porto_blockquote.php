<?php
$output = $footer_before = $footer_after = $view = $dir = $skin = $color = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'footer_before'      => '',
			'footer_after'       => '',
			'view'               => '',
			'dir'                => '',
			'skin'               => 'custom',
			'color'              => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( ! $view && 'custom' == $skin && $color ) {
	$sc_class_escaped = 'porto-blockquote' . rand();
	$el_class        .= ' ' . $sc_class_escaped;
	?>
	<style>
		.<?php echo $sc_class_escaped; ?> blockquote { border-color: <?php echo esc_html( $color ); ?>; }
	</style>
	<?php
}

$output = '<div class="porto-blockquote wpb_content_element ' . esc_attr( $el_class ) . '"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= '>';

$output .= '<blockquote class="' . esc_attr( $view ) . ' ' . esc_attr( $dir ) . ' ' . ( ! $view && 'custom' != $skin ? 'blockquote-' . esc_attr( $skin ) : '' ) . '">';
$output .= '<p>' . do_shortcode( $content ) . '</p>';
if ( $footer_before || $footer_after ) {
	$output .= '<footer>' . porto_strip_script_tags( $footer_before ) . ' <cite title="' . esc_attr( $footer_after ) . '">' . porto_strip_script_tags( $footer_after ) . '</cite></footer>';
}
$output .= '</blockquote>';

$output .= '</div>';

echo porto_filter_output( $output );
