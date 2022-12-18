<?php

extract(
	shortcode_atts(
		array(
			'el_class'    => '',
		),
		$atts
	)
);

if ( ! empty( $shortcode_class ) ) {
	$el_class = trim( $shortcode_class . ' ' . $el_class );
}

echo '<div class="entry-description' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '">';
if ( function_exists( 'porto_is_elementor_preview' ) && ( porto_is_elementor_preview() || porto_vc_is_inline() ) ) {
	echo '<p>' . esc_html__( 'Category description', 'porto-functionality' ) . '</p>';
} else {
	do_action( 'woocommerce_archive_description' );
}
echo '</div>';
