<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Wapl_Conditions
 *
 * The Labels Conditions class handles the matching rules for labels
 *
 * @class      Wapl_Conditions
 * @author     Jeroen Sormani
 * @package 	WooCommerce Advanced Product Labels
 * @version    1.0.0
 */
class WAPL_Match_Conditions {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_filter( 'wapl_match_conditions_product', array( $this, 'condition_match_product' ), 10, 3 );
		add_filter( 'wapl_match_conditions_category', array( $this, 'condition_match_product_category' ), 10, 3 );
		add_filter( 'wapl_match_conditions_product_type', array( $this, 'condition_match_product_type' ), 10, 3 );
		add_filter( 'wapl_match_conditions_in_sale', array( $this, 'condition_match_in_sale' ), 10, 3 );
		add_filter( 'wapl_match_conditions_bestseller', array( $this, 'condition_match_bestseller' ), 10, 3 );
		add_filter( 'wapl_match_conditions_age', array( $this, 'condition_match_age' ), 10, 3 );

		add_filter( 'wapl_match_conditions_price', array( $this, 'condition_match_price' ), 10, 3 );
		add_filter( 'wapl_match_conditions_sale_price', array( $this, 'condition_match_sale_price' ), 10, 3 );
		add_filter( 'wapl_match_conditions_stock_status', array( $this, 'condition_match_stock_status' ), 10, 3 );
		add_filter( 'wapl_match_conditions_stock_quantity', array( $this, 'condition_match_stock_quantity' ), 10, 3 );
		add_filter( 'wapl_match_conditions_shipping_class', array( $this, 'condition_match_shipping_class' ), 10, 3 );
		add_filter( 'wapl_match_conditions_tag', array( $this, 'condition_match_tag' ), 10, 3 );
		add_filter( 'wapl_match_conditions_sales', array( $this, 'condition_match_sales' ), 10, 3 );
		add_filter( 'wapl_match_conditions_featured', array( $this, 'condition_match_featured' ), 10, 3 );

	}


	/**
	 * Match bestseller.
	 *
	 * Match the condition value against the top x bestsellers.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function condition_match_bestseller( $match, $operator, $value ) {

		/** @var $product WC_Product */
		global $product;

		if ( false === $bestseller_ids = wp_cache_get( 'bestsellers', 'woocommerce-advanced-product-labels' ) ) {
			$query = new WP_Query( array(
				'fields'                 => 'ids',
				'post_type'              => 'product',
				'post_status'            => 'publish',
				'ignore_sticky_posts'    => 1,
				'posts_per_page'         => $value,
				'meta_key'               => 'total_sales',
				'orderby'                => 'meta_value_num',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'tax_query'              => array(
					'taxonomy'      => 'product_visibility',
					'field'         => 'name',
					'terms'         => array( 'catalog', 'visible' ),
					'rating_filter' => true,
				),
			) );

			$bestseller_ids = $query->posts; // Get bestsellers
			wp_cache_set( 'bestsellers', $bestseller_ids, 'woocommerce-advanced-product-labels' );
		}
		$match = in_array( $product->get_id(), $bestseller_ids );

		return $match;
	}


	/**
	 * Match featured.
	 *
	 * Match the condition value against featured product.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function condition_match_featured( $match, $operator, $value ) {

		/** @var $product WC_Product */
		global $product;

		if ( false === $featured_ids = wp_cache_get( 'featured', 'woocommerce-advanced-product-labels' ) ) {

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			$query = new WP_Query( array(
				'fields'                 => 'ids',
				'post_type'              => 'product',
				'post_status'            => 'publish',
				'posts_per_page'         => - 1,
				'orderby'                => 'date',
				'order'                  => 'DESC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'tax_query'              => array(
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['featured'],
					),
				),
			) );

			$featured_ids = $query->posts; // Get featured products
			wp_cache_set( 'featured', $featured_ids, 'woocommerce-advanced-product-labels' );
		}

		if ( $operator == '==' ) {
			$match = ( in_array( $product->get_id(), $featured_ids ) );
		} elseif ( $operator == '!=' ) {
			$match = ( ! in_array( $product->get_id(), $featured_ids ) );
		}

		return $match;
	}


	/**************************************************************
	 * All methods below are aliases for the new WP Conditions
	 *************************************************************/
	public function condition_match_product( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_product_category( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_category' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_product_type( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_type' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_in_sale( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_on_sale' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_age( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_age' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_price( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_price' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_sale_price( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_sale_price' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_stock_status( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_stock_status' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_stock_quantity( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_stock' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_shipping_class( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_shipping_class' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_tag( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_tag' );
		return $condition->match( $match, $operator, $value );
	}

	public function condition_match_sales( $match, $operator, $value ) {
		$condition = wpc_get_condition( 'product_sales' );
		return $condition->match( $match, $operator, $value );
	}

}
