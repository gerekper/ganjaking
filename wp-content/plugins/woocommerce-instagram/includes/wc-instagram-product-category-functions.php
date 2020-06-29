<?php
/**
 * Product Category Functions
 *
 * @package WC_Instagram/Functions
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the product category terms.
 *
 * @since 3.0.0
 *
 * @param array $args Optional. The arguments.
 * @return array An array of WP_Term objects.
 */
function wc_instagram_get_product_category_terms( $args = array() ) {
	$defaults = array(
		'taxonomy'   => 'product_cat',
		'orderby'    => 'name',
		'hide_empty' => true,
	);

	return wc_instagram_get_terms( wp_parse_args( $args, $defaults ) );
}

/**
 * Gets the product category label to use it in a select field.
 *
 * @since 3.0.0
 *
 * @param int|WP_Term $the_category Term object or term ID of the product category.
 * @param int|WP_Term $the_parent   Optional. Term object or term ID of the parent product category.
 * @return string
 */
function wc_instagram_get_product_category_choice_label( $the_category, $the_parent = null ) {
	$category = get_term( $the_category, 'product_cat' );

	if ( empty( $category ) ) {
		return '';
	}

	$label = $category->name;

	if ( 0 !== $category->parent ) {
		$parent_category = get_term( ( is_null( $the_parent ) ? $category->parent : $the_parent ), 'product_cat' );

		if ( ! empty( $parent_category ) ) {
			$label = "{$parent_category->name} — {$category->name}";
		}
	}

	return $label;
}

/**
 * Gets the product categories choices to use them in a select field.
 *
 * @since 3.0.0
 *
 * @return array
 */
function wc_instagram_get_product_categories_choices() {
	$choices    = array();
	$categories = wc_instagram_get_product_category_terms( array( 'parent' => 0 ) );

	foreach ( $categories as $category ) {
		$choices[ $category->term_id ] = wc_instagram_get_product_category_choice_label( $category );

		$children = wc_instagram_get_product_category_terms( array( 'parent' => $category->term_id ) );

		foreach ( $children as $child ) {
			$choices[ $child->term_id ] = '&nbsp;&nbsp; ' . wc_instagram_get_product_category_choice_label( $child, $category );
		}
	}

	return $choices;
}
