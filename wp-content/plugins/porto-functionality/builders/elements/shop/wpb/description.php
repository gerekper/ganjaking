<?php

extract(
	shortcode_atts(
		array(
			'font_size'   => '',
			'font_weight' => '',
			'line_height' => '',
			'ls'          => '',
			'color'       => '',
			'el_class'    => '',
		),
		$atts
	)
);

if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
	echo '<style>';
	include 'style-description.php';
	echo '</style>';
}
echo '<div class="entry-description' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '">';
do_action( 'woocommerce_archive_description' );
echo '</div>';
