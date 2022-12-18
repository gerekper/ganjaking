<?php

extract(
	shortcode_atts(
		array(
			'action'   => '',
			'el_class' => '',
		),
		$atts
	)
);

if ( $action ) {
	if ( ! empty( $shortcode_class ) ) {
		$el_class = $shortcode_class . ' ' . $el_class;
	}

	if ( $el_class ) {
		echo '<div class="' . esc_attr( trim( $el_class ) ) . '">';
	}

	do_action( $action );

	if ( $el_class ) {
		echo '</div>';
	}
}
