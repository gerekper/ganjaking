<?php

class Product_Addon_Global_Group {
	/**
	 * Gets a global add-on group from the provided post in a structure intended for a REST API response
	 *
	 * @since 2.9.0
	 *
	 * @throws Exception
	 * @param WP_Post $post
	 * @return array
	 */
	static public function get_group( $post ) {
		if ( ! is_a( $post, 'WP_Post' ) ) {
			throw new Exception( 'Product_Addon_Global_Group::Invalid argument supplied to get_group' );
		}

		$priority = intval( get_post_meta( $post->ID, '_priority', true ) );
		$term_ids = (array) wp_get_post_terms( $post->ID, array( 'product_cat' ), array( 'fields' => 'ids' ) );
		$fields   = array_filter( (array) get_post_meta( $post->ID, '_product_addons', true ) );
		$fields   = Product_Addon_Groups::coerce_options_to_remove_field_type_inappropriate_keys( $fields );

		if ( 1 == get_post_meta( $post->ID, '_all_products', true ) ) {
			$categories = array();
		} else {
			$categories = array();
			foreach ( $term_ids as $term_id ) {
				$term = get_term_by( 'id', $term_id, 'product_cat' );
				if ( $term ) {
					$categories[ $term_id ] = $term->name;
				}
			}
		}

		return array(
			'id'                     => $post->ID,
			'name'                   => $post->post_title,
			'priority'               => $priority,
			'restrict_to_categories' => $categories,
			'fields'                 => $fields
		);
	}

	/**
	 * Updates a new global add-on group post using the provided arguments (after validating them)
	 *
	 * @since 2.9.0
	 *
	 * @throws Exception
	 * @param array $args
	 * @return array
	 */
	static public function create_group( $args ) {
		// Make sure the args only has keys we are expecting
		try {
			Product_Addon_Group_Validator::is_valid_global_addons_group_update( $args );
		} catch ( Exception $e ) {
			return new WP_Error( 'invalid_parameter', $e->getMessage() );
		}

		// All is well, create the post
		$new_post_id = wp_insert_post(
			array(
				'post_title'	=> 'Untitled',
				'post_status'	=> 'publish',
				'post_type'		=> 'global_product_addon',
			)
		);

		$post = WP_Post::get_instance( $new_post_id );
		return self::update_group( $post, $args );
	}

	/**
	 * Updates a global add-on group post using the provided arguments (after validating them)
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
		if ( 'global_product_addon' !== $post->post_type ) {
			return new WP_Error( 'internal_error', 'Attempted to update a post which is not a global add-ons group custom post type' );
		}

		// Make sure the args only has keys we are expecting
		try {
			Product_Addon_Group_Validator::is_valid_global_addons_group_update( $args );
		} catch ( Exception $e ) {
			return new WP_Error( 'invalid_parameter', $e->getMessage() );
		}

		// All is well, commit the changes to the post

		// Name
		if ( isset( $args['name'] ) ) {
			self::set_name( $post, $args['name'] );
		}

		// Priority
		if ( isset( $args['priority'] ) ) {
			self::set_priority( $post, $args['priority'] );
		}

		// Restrict to Categories / Applies to All Products
		if ( isset( $args['restrict_to_categories'] ) ) {
			self::set_restrict_to_categories( $post, $args['restrict_to_categories'] );
		}

		// Fields
		if ( isset( $args['fields'] ) ) {
			self::set_fields( $post, $args['fields'] );
		}

		// Clear the cache and re-fetch the post so we get all fresh data
		clean_post_cache( $post->ID );
		$post = WP_Post::get_instance( $post->ID );

		// Return the updated object using get_group
		return self::get_group( $post );
	}

	/**
	 * Updates a global add-on group post's name
	 *
	 * @since 2.9.0
	 *
	 * @throws Exception
	 * @param WP_Post $post
	 * @param string $name
	 */
	protected static function set_name( $post, $name ) {
		wp_update_post(
			array(
				'ID' => $post->ID,
				'post_title' => $name
			)
		);
	}

	/**
	 * Updates a global add-on group post's priority
	 *
	 * @since 2.9.0
	 *
	 * @throws Exception
	 * @param WP_Post $post
	 * @param int $priority
	 */
	protected static function set_priority( $post, $priority ) {
		update_post_meta( $post->ID, '_priority', $priority );
	}

	/**
	 * Updates a global add-on group post's applicable product categories (or all products)
	 *
	 * @since 2.9.0
	 *
	 * @throws Exception
	 * @param WP_Post $post
	 * @param array $category_ids
	 */
	protected static function set_restrict_to_categories( $post, $category_ids ) {
		$category_ids = array_map( 'intval', $category_ids );
		wp_set_post_terms( $post->ID, $category_ids, 'product_cat', false );
		if ( 0 === count( $category_ids ) ) {
			update_post_meta( $post->ID, '_all_products', 1 );
		} else {
			update_post_meta( $post->ID, '_all_products', 0 );
		}

	}

	/**
	 * Updates a global add-on group post's fields and options
	 *
	 * @since 2.9.0
	 *
	 * @throws Exception
	 * @param WP_Post $post
	 * @param array $fields
	 */
	protected static function set_fields( $post, $fields ) {
		$fields = Product_Addon_Groups::coerce_options_to_contain_all_keys_before_saving_to_meta( $fields );
		update_post_meta( $post->ID, '_product_addons', $fields );
	}
}