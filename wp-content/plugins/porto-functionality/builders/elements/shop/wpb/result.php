<?php
extract(
	shortcode_atts(
		array(
			'el_class' => '',
		),
		$atts
	)
);

if ( ! empty( $shortcode_class ) ) {
	$el_class = $shortcode_class . ' ' . $el_class;
}

if ( $el_class ) {
	echo '<div class="' . esc_attr( $el_class ) . '">';
}

if ( function_exists( 'porto_is_elementor_preview' ) && ( porto_is_elementor_preview() || porto_vc_is_inline() ) ) {
	// show dummy content
	global $porto_settings;
	if ( $porto_settings['category-item'] ) {
		$per_page = explode( ',', $porto_settings['category-item'] );
	} else {
		$per_page = explode( ',', '12,24,36' );
	}
	$args = array(
		'total'    => (int) $per_page[0],
		'per_page' => (int) $per_page[0],
		'current'  => 1,
	);

	wc_get_template( 'loop/result-count.php', $args );
} else {
	if ( porto_is_ajax() && isset( $_REQUEST['load_posts_only'] ) && 2 === (int) $_REQUEST['load_posts_only'] && isset( $_REQUEST['type'] ) && 'load_more' == $_REQUEST['type'] ) {
		echo '<p class="woocommerce-result-count">';
		$total        = (int) wc_get_loop_prop( 'total' );
		$total_pages  = (int) wc_get_loop_prop( 'total_pages' );
		$per_page     = (int) wc_get_loop_prop( 'per_page' );
		$current_page = (int) wc_get_loop_prop( 'current_page' );
		if ( 1 === $total ) {
			_e( 'Showing the single result', 'woocommerce' );
		} elseif ( $total <= $per_page || -1 === $per_page || $total_pages === $current_page ) {
			/* translators: %d: total results */
			printf( _n( 'Showing all %d result', 'Showing all %d results', $total, 'woocommerce' ), $total );
		} else {
			$first = 1;
			$last  = min( $total, $per_page * $current_page );
			/* translators: 1: first result 2: last result 3: total results */
			printf( _nx( 'Showing %1$d&ndash;%2$d of %3$d result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, 'with first and last result', 'woocommerce' ), $first, $last, $total );
		}
		echo '</p>';
	} else {
		woocommerce_result_count();
	}
}

if ( $el_class ) {
	echo '</div>';
}
