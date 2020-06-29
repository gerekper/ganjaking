<?php

/**
 * Class YWSBS_Helper_Points_Product.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class YWSBS_Helper_Points_Product {
	/**
	 * Create a subscription single product.
	 *
	 * @return WC_Product
	 */
	public static function create_points_product( $args = array() ) {
		// Create the product
		$product = wp_insert_post( array(
			'post_title'  => 'Dummy Points Product',
			'post_type'   => 'product',
			'post_status' => 'publish',
		) );

		update_post_meta( $product, '_sku', 'DUMMY SKU' );
		update_post_meta( $product, '_manage_stock', 'no' );
		update_post_meta( $product, '_tax_status', 'taxable' );
		update_post_meta( $product, '_ywpar_point_earned', '' );
		update_post_meta( $product, '_ywpar_point_earned_dates_from', '' );
		update_post_meta( $product, '_ywpar_point_earned_dates_to', '' );
		update_post_meta( $product, '_ywpar_max_point_discount', '' );


		if ( $args ) {
			foreach ( $args as $key => $arg ) {
				update_post_meta( $product, $key, $arg );
			}
		}

		$product = new WC_Product( $product );

		// set 'Allow until' by default to 5 years to prevent issue with 'create_next_year_date'
		return $product;
	}

	/**
	 * @param array $args
	 * @param array $categories
	 *
	 * @return WC_Product
	 */
	public static function create_points_single_product_with_category( $args = array(), $categories = array() ) {
		$product = self::create_points_product( $args );

		if ( $categories ) {
			$cats_id = array();
			foreach ( $categories as $category ) {
				$term = wp_insert_term( $category, 'product_cat' );
				add_term_meta( $term['term_id'], 'point_earned', '', true );
				add_term_meta( $term['term_id'], 'point_earned_dates_from', '', true );
				add_term_meta( $term['term_id'], 'point_earned_dates_to', '', true );
				$cats_id[] = $term['term_id'];
			}
			$product->set_category_ids( $cats_id );
		}

		$product->save();
		return $product;
	}

	/**
	 * Create a subscription variation product.
	 *
	 * @return WC_Product_Variation
	 */
	public static function create_points_variation_product( $args = array() ) {

		$product    = new WC_Product_Variable();
		$product_id = $product->save();

		$variation = new WC_Product_Variation;
		$variation->set_parent_id( $product_id );
		$variation_id = $variation->save();

		update_post_meta( $variation_id, '_ywpar_point_earned', '' );
		update_post_meta( $variation_id, '_ywpar_point_earned_dates_from', '' );
		update_post_meta( $variation_id, '_ywpar_point_earned_dates_to', '' );
		update_post_meta( $variation_id, '_ywpar_max_point_discount', '' );

		if ( $args ) {
			foreach ( $args as $key => $arg ) {
				update_post_meta( $variation_id, $key, $arg );
			}
		}

		return $variation;
	}

	/**
	 * @param array $args
	 * @param array $categories
	 *
	 * @return WC_Product_Variation
	 */
	public static function create_points_variation_product_with_category( $args = array(), $categories = array() ) {
		$product = self::create_points_variation_product( $args );
		$parent_id = $product->get_parent_id();
		$parent = wc_get_product($parent_id);
		if ( $categories ) {
			$cats_id = array();
			foreach ( $categories as $category ) {
				$term = wp_insert_term( $category, 'product_cat' );
				add_term_meta( $term['term_id'], 'point_earned', '', true );
				add_term_meta( $term['term_id'], 'point_earned_dates_from', '', true );
				add_term_meta( $term['term_id'], 'point_earned_dates_to', '', true );
				$cats_id[] = $term['term_id'];
			}
			$parent->set_category_ids( $cats_id );
		}

		$parent->save();
		return $product;
	}

	/**
	 * delete a product
	 *
	 * @param int|WC_Product $product
	 */
	public static function delete_product( $product ) {
		$product = wc_get_product( $product );
		$product && $product->delete( true );
	}
}
