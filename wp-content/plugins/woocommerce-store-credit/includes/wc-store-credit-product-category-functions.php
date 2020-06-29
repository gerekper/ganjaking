<?php
/**
 * Product Category Functions
 *
 * @package WC_Store_Credit/Functions
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the product category terms.
 *
 * @since 3.2.0
 *
 * @param array $args Optional. The arguments.
 * @return array An array of WP_Term objects.
 */
function wc_store_credit_get_product_category_terms( $args = array() ) {
	$defaults = array(
		'taxonomy'   => 'product_cat',
		'orderby'    => 'name',
		'hide_empty' => true,
	);

	return get_terms( wp_parse_args( $args, $defaults ) );
}

/**
 * Gets the product category label to use it in a select field.
 *
 * @since 3.2.0
 *
 * @param int|WP_Term $the_category Term object or term ID of the product category.
 * @param int|WP_Term $the_parent   Optional. Term object or term ID of the parent product category.
 * @return string
 */
function wc_store_credit_get_product_category_choice_label( $the_category, $the_parent = null ) {
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
 * @since 3.2.0
 *
 * @param bool $sub_cats Include subcategories?.
 * @return array
 */
function wc_store_credit_get_product_categories_choices( $sub_cats = false ) {
	$choices    = array();
	$categories = wc_store_credit_get_product_category_terms( array( 'parent' => 0 ) );

	foreach ( $categories as $category ) {
		$choices[ $category->term_id ] = wc_store_credit_get_product_category_choice_label( $category );

		if ( $sub_cats ) {
			$children = wc_store_credit_get_product_category_terms( array( 'parent' => $category->term_id ) );

			foreach ( $children as $child ) {
				$choices[ $child->term_id ] = '&nbsp;&nbsp; ' . wc_store_credit_get_product_category_choice_label( $child, $category );
			}
		}
	}

	return $choices;
}
