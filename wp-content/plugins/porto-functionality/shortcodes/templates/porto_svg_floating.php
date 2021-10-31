<?php
extract(
	shortcode_atts(
		array(
			'float_svg' => '',
			'float_path'         => '',
			'float_duration'     => 10000,
			'float_easing'       => 'easingQuadraticInOut',
			'float_repeat'       => 20,
			'float_repeat_delay' => 1000,
			'float_yoyo'         => 'yes',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
			'page_builder'       => 'wpb',
		),
		$atts
	)
);

wp_enqueue_script( 'porto-kute' );

$wrapper = '<div';
if ( $el_class ) {
	$wrapper .= ' class=' . $el_class;
}

if ( $animation_type ) {
	$wrapper .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$wrapper .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$wrapper .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$wrapper .= '>';
if ( ! empty( $el_class ) || $animation_type ) {
	echo porto_filter_output( $wrapper );
}
if( 'wpb' == $page_builder ) {
	$float_svg = rawurldecode( base64_decode( porto_strip_script_tags( $float_svg ) ) );
}

echo porto_filter_output( $float_svg );
if ( ! empty( $el_class ) || $animation_type ) {
	echo porto_filter_output( '</div>' );
}

if ( ! is_array( $float_path ) && ! empty( $float_path ) ) {
	$float_path = explode( ',', $float_path );
}

if ( ! empty( $float_path ) && count( $float_path ) > 0 ) :
	?>
<script>
	jQuery(document).ready(function($) {
		if (typeof KUTE != 'undefined') {
			<?php foreach ( $float_path as $path ) : ?>
				<?php $path = trim( $path ); ?>
				if( $('<?php echo porto_filter_output( $path ); ?>').get(0) ) {
					var shape1 = KUTE.fromTo('<?php echo porto_filter_output( $path ); ?>', {
						path: '<?php echo porto_filter_output( $path ); ?>' 
					}, { 
						path: '<?php echo porto_filter_output( str_replace( 'start', 'end', $path ) ); ?>' 
					}, {
						duration: <?php echo porto_filter_output( $float_duration ); ?>,
						easing	: '<?php echo porto_filter_output( $float_easing ); ?>',
						repeat: <?php echo porto_filter_output( $float_repeat ); ?>,
						repeatDelay: <?php echo porto_filter_output( $float_repeat_delay ); ?>,
						yoyo: <?php echo empty( $float_yoyo ) ? esc_js( 'false' ) : esc_js( 'true' ); ?>
					}).start();
				}
			<?php endforeach; ?>
		}
	});
</script>
<?php endif; ?>
