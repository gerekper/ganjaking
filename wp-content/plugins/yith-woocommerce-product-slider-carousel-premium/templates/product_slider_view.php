<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
$path_js = file_exists( YWCPS_ASSETS_PATH . 'js/yith_product_slider_custom.js' ) ? YWCPS_ASSETS_URL . 'js/yith_product_slider_custom.js' : YWCPS_ASSETS_URL . 'js/yith_product_slider' . $suffix . '.js';

wp_register_script( 'yith_wc_product_slider', $path_js, array( 'jquery' ), YWCPS_VERSION, true );

$enable_mousewhell = get_option( 'ywcps_enable_mousewhell', 'no');

$enable_mousewhell = $enable_mousewhell == 'yes' ? 'true' : 'false';
$js_args = array( 'yit_theme' => defined( 'YIT' ) ? 'true' : 'false' ,'enable_mousewheel' => $enable_mousewhell );
wp_localize_script( 'yith_wc_product_slider', 'ywcps_params', $js_args );
wp_enqueue_style( 'fontawesome' );
wp_enqueue_style( 'owl-carousel-style' );
if( apply_filters('ywcps_enqueue_animate_style', true ) ) {
	wp_enqueue_style( 'yith-animate' );
	wp_enqueue_style( 'owl-carousel-style-3d' );
}
wp_enqueue_style( 'yith-product-slider-style' );

if( !wp_script_is( 'jquery.mousewheel')) {
wp_enqueue_script( 'jquery.mousewheel', YWCPS_ASSETS_URL.'js/jquery.mousewheel.min.js', array(), false,true );
}
wp_enqueue_script( 'owl-carousel' );
wp_enqueue_script( 'yith_wc_product_slider' );


$query_args = array(
	'posts_per_page'   => $posts_per_page,
	'post_type'        => 'product',
	'post_status'      => 'publish',
	'suppress_filters' => false
);

$hide_out_of_stock = get_post_meta( $id, '_ywcps_hide_out_stock_product', true );
$tax_query         = WC()->query->get_tax_query();

if ( $hide_out_of_stock ) {

	if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {

		$out_of_stock_query = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'outofstock',
			'operator' => 'NOT IN',
		);
		$tax_query[]        = $out_of_stock_query;
	} else {
		$product_out_of_stock       = yith_wc_get_product_ids_out_of_stock();
		$query_args['post__not_in'] = $product_out_of_stock;
	}
}

if ( ! empty( $how_category ) && 'all' !== $how_category ) {

	if ( 'custom_category' == $how_category ) {
		$categories = get_post_meta( $id, '_ywcps_categories', true );
		$operator   = 'IN';

		if ( ! is_array( $categories ) ) {
			$categories = explode( ',', $categories );
		}


	} elseif ( 'exclude_category' == $how_category ) {

		$categories = get_post_meta( $id, '_ywcps_exclude_categories', true );
		$operator   = 'NOT IN';

		if ( ! is_array( $categories ) ) {
			$categories = explode( ',', $categories );
		}



	}
	$categories = ywcps_get_term_id_by_slug( $categories );

	if ( ! empty( $categories ) ) {

		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => $categories,
			'operator' => $operator

		);

	}
}

if ( ! empty( $how_brands ) && 'custom_brand' == $how_brands ) {

	$brands = get_post_meta( $id, '_ywcps_brands', true );
	if ( is_array( $brands ) ) {
		$brands = implode( ',', $brands );
	}

	if ( ! empty( $brands ) && class_exists( 'YITH_WCBR_Premium' ) ) {
		$query_args[ YITH_WCBR_Premium::$brands_taxonomy ] = $brands;
	}
}

if ( $product_type !== 'on_sale' ) {

	$hide_on_sale = get_post_meta( $id, '_ywcps_hide_on_sale_product', true );

	if ( $hide_on_sale ) {
		$product_ids_on_sale = wc_get_product_ids_on_sale();

		if ( isset( $query_args['post__not_in'] ) && is_array( $query_args['post__not_in'] ) ) {
			$query_args['post__not_in'] = array_merge( $product_ids_on_sale, $query_args['post__not_in'] );
		} else {
			$query_args['post__not_in'] = $product_ids_on_sale;
		}
	}
}

if ( ! empty( $product_type ) ) {

	switch ( $product_type ) {

		case 'on_sale'  :
			$product_ids_on_sale    = wc_get_product_ids_on_sale();
			$product_ids_on_sale[]  = 0;
			$query_args['post__in'] = $product_ids_on_sale;
			break;
		case 'best_seller'  :
			$query_args['meta_key'] = 'total_sales';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'DESC';
			break;
		case 'last_ins' :
			$query_args['orderby'] = 'date';
			$query_args['order']   = 'DESC';
			break;
		case 'free'  :
			$query_args['meta_query'][] = array(
				'key'     => '_price',
				'value'   => 0,
				'compare' => '=',
				'type'    => 'DECIMAL',
			);

			break;
		case 'featured' :

			if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {

				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					'operator' => 'IN',
				);

			} else {
				$query_args['meta_query']   = array();
				$query_args['meta_query'][] = array(
					'key'   => '_featured',
					'value' => 'yes'
				);
			}
			break;

		case 'custom_select' :
			$product_ids = get_post_meta( $id, '_ywcps_products', true );

			if ( ! is_array( $product_ids ) ) {
				$product_ids = explode( ',', $product_ids );
			}
			if ( ! empty( $product_ids ) ) {
				$query_args['post__in'] = $product_ids;
				unset ( $query_args['product_cat'] );
			}
			break;

		case 'custom_select_tag' :
			$product_tags = get_post_meta( $id, '_ywcps_product_tag', true );
			if ( is_array( $product_tags ) ) {
				$product_tags = implode( ',', $product_tags );
			}
			$query_args['product_tag'] = $product_tags;
			break;
		case 'top_rated':
			$query_args['meta_key'] = '_wc_average_rating';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'DESC';
			break;
	}


	$query_args['tax_query']             = $tax_query;
	$query_args['tax_query']['relation'] = 'AND';

	$order = strtoupper( $order );
	switch ( $order_by ) {


		case 'date':
			if ( ! isset( $query_args['orderby'] ) ) {
				$query_args['orderby'] = 'date';
				$query_args['order']   = $order;
			}
			break;

		case 'price' :

			$query_args['meta_key'] = '_price';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = $order;

			break;

		case 'name' :
			if ( ! isset( $query_args['orderby'] ) ) {
				$query_args['orderby'] = 'title';
				$query_args['order']   = $order;
			}
			break;

		case 'rand':
			if ( ! isset( $query_args['orderby'] ) ) {
				$query_args['orderby'] = 'rand';
				$query_args['order']   = $order;
			}
			break;
	}
}

// exclude current product if slider is in single product page
if ( is_product() ) {
	global $product;
	$query_args['post__not_in'][] = $product->get_id();
}

$query_args = apply_filters('ywcps_query_args', $query_args, $id );

$atts['query_args'] = $query_args;
$atts['slideBy']    = apply_filters( 'ywcps_slideby_arg', 1 );


if ( $template_slider !== 'default' ) {

	$atts['layouts'] = $template_slider;
	$template_name   = 'product_slider_view_custom_template.php';
} else {
	$template_name = 'product_slider_view_default.php';
}

do_action( 'yith_wcps_before_print_slider', $atts );

wc_get_template( $template_name, $atts, '', YWCPS_TEMPLATE_PATH );

do_action( 'yith_wcps_after_print_slider', $atts );
