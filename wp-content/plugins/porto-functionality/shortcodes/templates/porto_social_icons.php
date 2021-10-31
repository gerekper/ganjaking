<?php

$el_class = '';
if ( ! empty( $shortcode_class ) ) {
	$el_class .= ' ' . $shortcode_class;
}
if ( isset( $atts['el_class'] ) ) {
	$el_class .= ' ' . $atts['el_class'];
}

echo porto_header_socials( trim( $el_class ) );
