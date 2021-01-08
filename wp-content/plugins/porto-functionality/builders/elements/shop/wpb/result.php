<?php
extract(
	shortcode_atts(
		array(
			'el_class' => '',
		),
		$atts
	)
);

if ( $el_class ) {
	echo '<div class="' . esc_attr( $el_class ) . '">';
}
woocommerce_result_count();
if ( $el_class ) {
	echo '</div>';
}
