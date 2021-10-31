<?php
$output = $title = $number = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'title'              => '',
			'number'             => 6,
			'view'               => 'grid',
			'columns'            => 2,
			'show_desc'          => '',

			'navigation'         => 1,
			'nav_pos'            => '',
			'nav_pos2'           => '',
			'nav_type'           => '',
			'show_nav_hover'     => false,
			'pagination'         => 0,
			'dots_pos'           => '',
			'autoplay'           => '',
			'autoplay_timeout'   => 5000,

			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

if ( ! $title ) {
	$atts['title'] = '';
}

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="vc_widget_woo_recent_reviews wpb_content_element' . esc_attr( $el_class ) . '"';
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

if ( $show_desc ) {
	add_action( 'woocommerce_widget_product_review_item_end', 'porto_woocommerce_widget_product_review_item_add_desc' );
}

$type = 'WC_Widget_Recent_Reviews';
$args = array( 'widget_id' => 'woocommerce_recent_reviews_' . $number );

ob_start();
the_widget( $type, $atts, $args );
$output .= ob_get_clean();

$output .= '</div>';

if ( 2 !== (int) $columns && 'grid' == $view ) {
	$output = str_replace( '<ul class="product_list_widget has-ccols ccols-2">', '<ul class="product_list_widget has-ccols ' . esc_attr( function_exists( 'porto_generate_column_classes' ) ? porto_generate_column_classes( $columns ) : '' ) . '">', $output );
} elseif ( 'products-slider' == $view ) {
	if ( function_exists( 'porto_generate_column_classes' ) ) {
		$options = porto_generate_column_classes( $columns, true );
		if ( isset( $options['xs'] ) ) {
			$options['ls'] = $options['xs'];
		}
		if ( isset( $options['sm'] ) ) {
			$options['xs'] = $options['sm'];
		}
	} else {
		$options = array();
	}
	if ( $navigation ) {
		$options['nav'] = true;
	}
	if ( $pagination ) {
		$options['dots'] = true;
	}
	if ( 'yes' == $autoplay ) {
		$options['autoplay'] = true;
	} elseif ( $autoplay ) {
		$options['autoplay'] = false;
	}
	if ( $autoplay_timeout && 5000 !== (int) $autoplay_timeout ) {
		$options['autoplayTimeout'] = (int) $autoplay_timeout;
	}
	$wrapper_class = 'product_list_widget has-ccols products-slider owl-carousel';
	if ( function_exists( 'porto_generate_column_classes' ) ) {
		$wrapper_class .= ' ' . porto_generate_column_classes( $columns );
	}
	if ( $navigation ) {
		if ( $nav_pos ) {
			$wrapper_class .= ' ' . $nav_pos;
		}
		if ( ( empty( $nav_pos ) || 'nav-center-images-only' == $nav_pos ) && $nav_pos2 ) {
			$wrapper_class .= ' ' . $nav_pos2;
		}
		if ( $nav_type ) {
			$wrapper_class .= ' ' . $nav_type;
		} else {
			$wrapper_class .= ' show-nav-middle';
		}
		if ( $show_nav_hover ) {
			$wrapper_class .= ' show-nav-hover';
		}
	}

	if ( $pagination && $dots_pos ) {
		$wrapper_class .= ' ' . $dots_pos;
	}

	$options = json_encode( $options );
	$output  = str_replace( '<ul class="product_list_widget has-ccols ccols-2">', '<ul class="' . esc_attr( $wrapper_class ) . '" data-plugin-options="' . esc_attr( $options ) . '">', $output );
}

if ( $show_desc ) {
	remove_action( 'woocommerce_widget_product_review_item_end', 'porto_woocommerce_widget_product_review_item_add_desc' );
}

echo porto_filter_output( $output );
