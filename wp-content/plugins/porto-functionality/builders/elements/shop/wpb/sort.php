<?php
extract(
	shortcode_atts(
		array(
			'el_class' => '',
		),
		$atts
	)
);

$is_preview = apply_filters( 'porto_shop_builder_set_preview', false );

if ( ! empty( $shortcode_class ) ) {
	$el_class = $shortcode_class . ' ' . $el_class;
}

if ( $el_class ) {
	echo '<div class="' . esc_attr( trim( $el_class ) ) . '">';
}
woocommerce_catalog_ordering();
if ( $el_class ) {
	echo '</div>';
}

if ( $is_preview ) {
	do_action( 'porto_shop_builder_unset_preview' );
}
