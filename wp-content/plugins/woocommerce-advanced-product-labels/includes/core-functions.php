<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Get Product Label posts.
 *
 * Get a list of all the labels that are set.
 *
 * @since 1.0.8
 *
 * @param  array $args List of arguments to merge with the default args.
 * @return array       List of 'wapl' posts.
 */
function wapl_get_advanced_product_labels( $args = array() ) {

	$query_args = wp_parse_args( $args, array(
		'post_type'              => 'wapl',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'menu_order',
		'order'                  => 'ASC',
		'update_post_term_cache' => false
	) );
	$post_query = new WP_Query( $query_args );
	$posts      = $post_query->posts;

	return apply_filters( 'woocommerce_advanced_product_labels_get_labels', $posts );
}


/**
 * Get label types.
 *
 * Get the different label types. Extensible by users.
 *
 * @since 1.0.0
 * @since 1.0.8 - Add $type param
 *
 * @param  string       $type Slug of the type to get.
 * @return array|string       Returns pretty name when $type is set, otherwise a list of available types.
 */
function wapl_get_label_types( $type = '' ) {

	$types = apply_filters( 'wapl_label_types', array(
		'label'        => __( 'Label', 'woocommerce-advanced-product-labels' ),
		'flash'        => __( 'Flash', 'woocommerce-advanced-product-labels' ),
		'diagonal-bar' => __( 'Diagonal bar', 'woocommerce-advanced-product-labels' ),
		'corner'       => __( 'Corner', 'woocommerce-advanced-product-labels' ),
		'ribbon'       => __( 'Ribbon', 'woocommerce-advanced-product-labels' ),
		'ribbon2'      => __( 'Ribbon style 2', 'woocommerce-advanced-product-labels' ),
	) );

	if ( ! empty( $type ) && isset( $types[ $type ] ) ) {
		return $types[ $type ];
	}

	return $types;
}


/**
 * Label styles.
 *
 * Set the available label styles.
 *
 * @since 1.0.0
 */
function wapl_get_label_styles() {

	return apply_filters( 'wapl_label_styles', array(
		'red'    => __( 'Red', 'woocommerce-advanced-product-labels' ),
		'blue'   => __( 'Blue', 'woocommerce-advanced-product-labels' ),
		'green'  => __( 'Green', 'woocommerce-advanced-product-labels' ),
		'yellow' => __( 'Yellow', 'woocommerce-advanced-product-labels' ),
		'orange' => __( 'Orange', 'woocommerce-advanced-product-labels' ),
		'gray'   => __( 'Gray', 'woocommerce-advanced-product-labels' ),
		'black'  => __( 'Black', 'woocommerce-advanced-product-labels' ),
		'white'  => __( 'White', 'woocommerce-advanced-product-labels' ),
		'custom' => __( 'Custom', 'woocommerce-advanced-product-labels' ),
	) );
}


/**
 * Get the label HTML.
 *
 * Get the formatted HTML of a label based on the passed arguments.
 * This is a replacement of the WAPL_Label object.
 *
 * @since 1.1.0
 *
 * @param             $args
 * @return mixed|void
 */
function wapl_get_label_html( $args ) {

	$label = wp_parse_args( $args, array(
		'id'                => '',
		'text'              => '',
		'style'             => '',
		'style_attr'        => '',
		'type'              => '',
		'align'             => '',
		'custom_bg_color'   => '',
		'custom_text_color' => '',
	) );

	ob_start();

		?><style>.wapl-label-id-<?php echo esc_attr( $label['id'] ); ?> .product-label:after { border-color: <?php echo $label['custom_bg_color']; ?>; }</style><?php

		?><div class="wapl-label-id-<?php echo esc_attr( $label['id'] ); ?> label-wrap wapl-<?php echo sanitize_html_class( $label['type'] ); ?> label-<?php echo sanitize_html_class( $label['style'] ); ?> wapl-align<?php echo esc_attr( $label['align'] ); ?>">
			<span class="woocommerce-advanced-product-label product-label label-<?php echo sanitize_html_class( $label['style'] ); ?>" <?php echo ( $label['style_attr'] ); ?>>
				<span class="wapl-label-text"><?php echo wp_kses_post( $label['text'] ); ?></span>
			</span>
		</div><?php

	$label = ob_get_clean();

	return apply_filters( 'wapl_product_label', $label );
}


/**
 * SMART labels.
 *
 * Add filter to convert SMART labels.
 *
 * @since 1.0.0
 *
 * @param  string $label Label text value.
 * @return string        Modified label text value.
 */
function wapl_smart_product_label_filter( $label ) {

	global $product;

	// This in here for the admin preview to select one random product
	if ( ! $product ) {
		$product_posts = get_posts( array( 'post_type' => 'product', 'posts_per_page' => 1 ) );
		$product       = reset( $product_posts );
    }

	$product            = wc_get_product( $product );
	$highest_percentage = 0;

	if ( ! $product ) { // Check to be sure the global $product is set properly
		trigger_error( 'The global $product is not a valid variable type: ' . gettype( $product ) );
		return $label;
	}

	if ( $product->is_type( 'composite' ) ) {

		$regular_price      = $product->get_composite_regular_price();
		$sale_price         = $product->get_composite_price();
		$highest_percentage = ( $sale_price !== '' && $regular_price != 0 ) ? ( ( $regular_price - $sale_price ) / $regular_price * 100 ) : $highest_percentage;

    } elseif ( ! $product->is_type( 'variable' ) ) {

		$regular_price      = $product->get_regular_price();
		$sale_price         = $product->get_sale_price();
		$highest_percentage = ( $sale_price !== '' && $regular_price != 0 ) ? ( ( $regular_price - $sale_price ) / $regular_price * 100 ) : $highest_percentage;

    } else { // Get the right variable percentage

		$var_prices = $product->get_variation_prices();
		foreach ( $product->get_children() as $child_id ) {
			$price = isset( $var_prices['regular_price'][ $child_id ] ) ? $var_prices['regular_price'][ $child_id ] : false;
			$sale  = isset( $var_prices['sale_price'][ $child_id ] ) ? $var_prices['sale_price'][ $child_id ] : false;

			$percentage = $price != 0 && ! empty( $sale ) ? ( ( $price - $sale ) / $price * 100 ) : $highest_percentage;

			if ( $percentage >= $highest_percentage ) {
				$highest_percentage = $percentage;
				$regular_price      = $product->get_variation_regular_price( 'min' );
				$sale_price         = $product->get_variation_sale_price( 'min' );
            }
        }
    }

	$label = str_replace( '{percentage}', round( $highest_percentage, apply_filters( 'wapl_filter_discount_round', 1 ) ) . '%', $label );
	$label = str_replace( '{discount}', wc_price( (float) $regular_price - (float) $sale_price ), $label );
	$label = str_replace( '{price}', wc_price( $regular_price ), $label );
	$label = str_replace( '{saleprice}', wc_price( $sale_price ), $label );
	$label = str_replace( '{delprice}', '<del>' . wc_price( $regular_price ) . '</del>', $label );

	return $label;
}
add_filter( 'wapl_product_label', 'wapl_smart_product_label_filter' );


/**
 * Gallery fix JS line.
 *
 * Add a line of JS that changes the positioning of the HTML for galleries
 * as the styles are not applied as wished there.
 *
 * @since 1.1.5
 */
function wapl_add_js_gallery_fix() {
    wc_enqueue_js( "jQuery('.woocommerce-product-gallery .label-wrap').appendTo('.woocommerce-product-gallery');" );
}
add_action( 'init', 'wapl_add_js_gallery_fix' );


/**************************************************************
 * Backwards compatibility for WP Conditions
 *************************************************************/
function wapl_add_bc_filter_condition_values( $condition ) {
	if ( has_filter( 'woocommerce_advanced_product_labels_condition_values' ) ) {
		$condition = apply_filters( 'woocommerce_advanced_product_labels_condition_values', $condition );
	}

	return $condition;
}
add_action( 'wp-conditions\condition', 'wapl_add_bc_filter_condition_values' );

/**
 * Add the filters required for backwards-compatibility for the matching functionality.
 *
 * @since 1.1.0
 */
function wapl_add_bc_filter_condition_match( $match, $condition, $operator, $value, $args = array() ) {

	if ( ! isset( $args['context'] ) || $args['context'] != 'wapl' ) {
		return $match;
	}

	if ( has_filter( 'wapl_match_conditions_' . $condition ) ) {
		$match = apply_filters( 'wapl_match_conditions_' . $condition, $match = false, $operator, $value );
	}

	if ( has_filter( 'woocommerce_advanced_product_labels_match_condition_' . $condition ) ) {
		$match = apply_filters( 'woocommerce_advanced_product_labels_match_condition_' . $condition, $match = false, $operator, $value );
	}

	return $match;
}
add_action( 'wp-conditions\condition\match', 'wapl_add_bc_filter_condition_match', 10, 5 );


/**
 * Add condition descriptions of custom conditions.
 *
 * @since 1.1.0
 */
function wapl_add_bc_filter_condition_descriptions( $descriptions ) {
	return apply_filters( 'wapl_descriptions', $descriptions );
}
add_filter( 'wp-conditions\condition_descriptions', 'wapl_add_bc_filter_condition_descriptions' );


/**
 * Add custom field BC.
 *
 * @since 1.1.0
 */
function wapl_add_bc_action_custom_fields( $type, $args ) {
	if ( has_action( 'wpc_html_field_type_' . $type ) ) {
		do_action( 'wpc_html_field_type_' . $args['type'], $args );
	}
}
add_action( 'wp-conditions\html_field_hook', 'wapl_add_bc_action_custom_fields', 10, 2 );


/**
 * Map conditions to the proper class names.
 */
function wapl_add_bc_condition_class_names( $class_name, $condition ) {

	switch ( $condition ) {
		case 'age' :
			$class_name = 'WPC_Product_Age_Condition';
			break;
		case 'in_sale' :
			$class_name = 'WPC_Product_On_Sale_Condition';
			break;
		case 'shipping_class' :
			$class_name = 'WPC_Product_Shipping_Class_Condition';
			break;
		case 'category' :
			$class_name = 'WPC_Product_Category_Condition';
			break;
		case 'tag' :
			$class_name = 'WPC_Product_Tag_Condition';
			break;
	}

	return $class_name;
}
add_filter( 'wpc_get_condition_class_name', 'wapl_add_bc_condition_class_names', 10, 2 );


/**************************************************************
 * Deprecated
 *************************************************************/

/**
 * @deprecated 1.1.0 - See wpc_match_conditions() for replacement.
 */
function wapl_match_conditions( $condition_groups = array() ) {
	_deprecated_function( __FUNCTION__, '1.1.0', 'wpc_match_conditions' );
	return wpc_match_conditions( $condition_groups );
}
