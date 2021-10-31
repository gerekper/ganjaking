<?php

extract(
	shortcode_atts(
		array(
			'attribute'    => '',
			'hide_empty'   => '',
			'display_type' => 'dropdown',
			'query_type'   => 'or',
			'el_class'     => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( ! $attribute ) {
	return;
}
$taxonomy = wc_attribute_taxonomy_name( $attribute );
if ( ! taxonomy_exists( $taxonomy ) ) {
	return;
}

$get_terms_args = array(
	'taxonomy' => $taxonomy,
);
if ( $hide_empty ) {
	$get_terms_args['hide_empty'] = '1';
}

$orderby = wc_attribute_orderby( $taxonomy );

switch ( $orderby ) {
	case 'name':
		$get_terms_args['orderby']    = 'name';
		$get_terms_args['menu_order'] = false;
		break;
	case 'id':
		$get_terms_args['orderby']    = 'id';
		$get_terms_args['order']      = 'ASC';
		$get_terms_args['menu_order'] = false;
		break;
	case 'menu_order':
		$get_terms_args['menu_order'] = 'ASC';
		break;
}

$terms = get_terms( $get_terms_args );

if ( 0 === count( $terms ) ) {
	return;
}

switch ( $orderby ) {
	case 'name_num':
		usort( $terms, '_wc_get_product_terms_name_num_usort_callback' );
		break;
	case 'parent':
		usort( $terms, '_wc_get_product_terms_parent_usort_callback' );
		break;
}

$current_values = array();

if ( 'list' == $display_type ) {
	echo '<ul class="porto_shortcodes_product_filter">';
	foreach ( $terms as $term ) {
		$option_is_set = in_array( $term->slug, $current_values, true );
		echo '<li' . ( $option_is_set ? ' class="chosen"' : '' ) . '><a href="' . esc_url( get_term_link( $term ) ) . '" data-slug="' . esc_attr( urldecode( $term->slug ) ) . '">' . esc_html( $term->name ) . '</a></li>';
	}
	echo '</ul>';
} elseif ( 'label' == $display_type ) {
	echo '<ul class="porto_shortcodes_product_filter filter-item-list">';
	foreach ( $terms as $term ) {
		$option_is_set = in_array( $term->slug, $current_values, true );
		$color_value   = get_term_meta( $term->term_id, 'color_value', true );
		$attrs         = '';
		if ( $color_value ) {
			$attrs = ' class="filter-color" style="background-color: ' . esc_attr( $color_value ) . '"';
		} else {
			$attrs = ' class="filter-item"';
		}

		echo '<li' . ( $option_is_set ? ' class="chosen"' : '' ) . '><a href="' . esc_url( get_term_link( $term ) ) . '"' . $attrs . ' data-slug="' . esc_attr( urldecode( $term->slug ) ) . '" title="' . esc_attr( $term->name ) . '">' . esc_html( $term->name ) . '</a></li>';
	}
	echo '</ul>';
} else {
	echo '<form method="get" action="" class="porto_shortcodes_product_filter">';
		echo '<select class="porto_dropdown_product_attributes">';
			echo '<option value="">' . sprintf( esc_html__( 'By %s', 'porto-functionality' ), $attribute ) . '</option>';
	foreach ( $terms as $term ) {
		$option_is_set = in_array( $term->slug, $current_values, true );
		echo '<option data-url="' . esc_url( get_term_link( $term ) ) . '" value="' . esc_attr( urldecode( $term->slug ) ) . '" ' . selected( $option_is_set, true, false ) . '>' . esc_html( $term->name ) . '</option>';
	}
		echo '</select>';
	echo '</form>';
	wc_enqueue_js(
		"
			jQuery( '.porto_dropdown_product_attributes' ).on('change', function() {
				if ( jQuery(this).val() != '' ) {
					var this_page = jQuery(this).find('option:selected').data('url');
					location.href = this_page;
				}
			});
		"
	);
}
