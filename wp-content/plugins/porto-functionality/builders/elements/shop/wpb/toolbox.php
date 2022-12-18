<?php
extract(
	shortcode_atts(
		array(
			'el_class' => '',
		),
		$atts
	)
);

echo '<div class="shop-loop-' . ( apply_filters( 'porto_sb_products_rendered', false ) ? 'after' : 'before' ) . ' shop-builder' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '">';
echo do_shortcode( $content );
echo '</div>';
