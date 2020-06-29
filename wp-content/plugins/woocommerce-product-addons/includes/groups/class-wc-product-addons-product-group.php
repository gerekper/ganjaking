<?php

class WC_Product_Addons_Product_Group {
	/**
	 * Gets a product's add-on "group" from the provided post in a structure intended for a REST API response
	 *
	 * @since 2.9.0
	 *
	 * @throws Exception
	 * @param WP_Post $post
	 * @return array
	 */
	static public function get_group( $post ) {
		if ( ! is_a( $post, 'WP_Post' ) ) {
			throw new Exception( 'WC_Product_Addons_Product_Group::Invalid argument supplied to get_group' );
		}

		$term_ids = (array) wp_get_post_terms( $post->ID, apply_filters( 'woocommerce_product_addons_global_post_terms', array( 'product_cat' ) ), array( 'fields' => 'ids' ) );
		$fields   = array_filter( (array) get_post_meta( $post->ID, '_product_addons', true ) );
		$fields   = WC_Product_Addons_Groups::coerce_options_to_remove_field_type_inappropriate_keys( $fields );

		$categories = array();
		foreach ( $term_ids as $term_id ) {
			$term = get_term_by( 'id', $term_id, 'product_cat' );
			if ( $term ) {
				$categories[ $term_id ] = $term->name;
			}
		}

		$exclude_global_add_ons = self::get_exclude_global_add_ons( $post );

		return array(
			'id'                       => $post->ID,
			'exclude_global_add_ons'   => $exclude_global_add_ons,
			'fields'                   => $fields
		);
	}

	/**
	 * Updates a product's add-ons "group" using the provided arguments (after validating them)
	 *
	 * @since 2.9.0
	 *
	 * @throws Exception
	 * @param WP_Post $post
	 * @param array $args
	 * @return array
	 */
	static public function update_group( $post, $args ) {
		// Make sure this is a global add-ons $post
		if ( 'product' !== $post->post_type ) {
			return new WP_Error( 'internal_error', "Attempted to update a post ({$post->ID}) which is not a 'product' custom post type ({$post->post_type})" );
		}

		// Make sure the args only has keys we are expecting
		try {
			WC_Product_Addons_Group_Validator::is_valid_product_addons_update( $args );
		} catch ( Exception $e ) {
			return new WP_Error( 'invalid_parameter', $e->getMessage() );
		}

		// All is well, commit the changes to the post
		// Exclude Global Add-Ons
		if ( isset( $args['exclude_global_add_ons'] ) ) {
			self::set_exclude_global_add_ons( $post, $args['exclude_global_add_ons'] );
		}

		// Fields
		if ( isset( $args['fields'] ) ) {
			self::set_fields( $post, $args['fields'] );
		}

		// Return the updated object using get_group
		return self::get_group( $post );
	}

	/**
	 * Updates a product's "exclude global add-ons" flag
	 *
	 * @since 2.9.0
	 *
	 * @param WP_Post $post
	 * @param mixed $value
	 * @param array $category_ids
	 */
	protected static function set_exclude_global_add_ons( $post, $value ) {
		$new_wc = defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' );

		// Coerce in a controlled fashion
		$new_value = empty( $value ) ? '0' : '1';
		if ( $new_wc ) {
			$product = wc_get_product( $post->ID );
			$product->update_meta_data( '_product_addons_exclude_global', $new_value );
			$product->save_meta_data();
		} else {
			update_post_meta( $post->ID, '_product_addons_exclude_global', $new_value );
		}
	}

	/**
	 * Gets a product's "exclude global add-ons" flag
	 *
	 * @since 2.9.0
	 * @returns bool
	 */
	protected static function get_exclude_global_add_ons( $post ) {
		$new_wc = defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' );
		if ( $new_wc ) {
			$product = wc_get_product( $post->ID );
			$result = $product->get_meta( '_product_addons_exclude_global' );
		} else {
			$result = get_post_meta( $post->ID, '_product_addons_exclude_global', true );
		}

		// $result will contain a string with "0" or "1", so coerce it to boolean
		$coerced_result = ( ! empty( $result ) );
		return $coerced_result;
	}

	/**
	 * Updates a product's add-on fields and options
	 *
	 * @since 2.9.0
	 *
	 * @throws Exception
	 * @param WP_Post $post
	 * @param array $fields
	 */
	protected static function set_fields( $post, $fields ) {
		$fields = WC_Product_Addons_Groups::coerce_options_to_contain_all_keys_before_saving_to_meta( $fields );

		$new_wc = defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' );
		if ( $new_wc ) {
			$product = wc_get_product( $post->ID );
			$product->update_meta_data( '_product_addons', $fields );
			$product->save_meta_data();
		} else {
			update_post_meta( $post->ID, '_product_addons', $fields );
		}
	}
}