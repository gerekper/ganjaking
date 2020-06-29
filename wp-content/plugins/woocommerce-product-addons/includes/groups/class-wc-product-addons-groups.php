<?php

class WC_Product_Addons_Groups {
	/**
	 * Returns all the global groups (if any) and their add-ons
	 *
	 * @since 2.9.0
	 *
	 * @return array
	 */
	public static function get_all_global_groups() {
		$global_groups = array();

		$args = array(
			'posts_per_page'  => -1,
			'orderby'         => 'title',
			'order'           => 'ASC',
			'post_type'       => 'global_product_addon',
			'post_status'     => 'any',
			'suppress_filters' => true
		);

		$global_group_posts = get_posts( $args );

		foreach ( (array) $global_group_posts as $global_group_post ) {
			$global_groups[] = WC_Product_Addons_Global_Group::get_group( $global_group_post );
		}

		return $global_groups;
	}

	/**
	 * Given a global group ID or a product ID, returns add-ons in a structure intended for a REST API response
	 *
	 * @since 2.9.0
	 *
	 * @param integer $id
	 * @return array
	 */
	public static function get_group( $id ) {
		$post = WP_Post::get_instance( $id );

		if ( self::is_a_global_group_id( $id ) ) {
			return WC_Product_Addons_Global_Group::get_group( $post );
		}

		if ( $post && 'product' === $post->post_type ) {
			return WC_Product_Addons_Product_Group::get_group( $post );
		}

		return new WP_Error( 'invalid_id', __( 'Unable to retrieve data. Invalid global add ons group or product ID.', 'woocommerce-product-addons' ) );
	}

	/**
	 * Given a global group ID or a product ID, updates add-ons from the args provided
	 *
	 * @since 2.9.0
	 *
	 * @param integer $id
	 * @param array $args
	 * @return array
	 */
	public static function update_group( $id, $args ) {
		$post = WP_Post::get_instance( $id );

		if ( self::is_a_global_group_id( $id ) ) {
			return WC_Product_Addons_Global_Group::update_group( $post, $args );
		}

		return WC_Product_Addons_Product_Group::update_group( $post, $args );
	}

	/**
	 * Given a global group ID, deletes it
	 *
	 * @since 2.9.0
	 *
	 * @param integer $id
	 * @return array
	 */
	public static function delete_group( $id ) {
		if ( ! self::is_a_global_group_id( $id ) ) {
			return new WP_Error( 'invalid_id', __( 'Unable to delete group. Invalid global add ons group ID.', 'woocommerce-product-addons' ) );
		}

		$post = WP_Post::get_instance( $id );
		$trashed_post = WC_Product_Addons_Global_Group::get_group( $post );
		wp_delete_post( $id, true );

		return $trashed_post;
	}

	/**
	 * Tells you if the passed ID corresponds to a global group
	 *
	 * @since 2.9.0
	 *
	 * @param integer $id
	 * @return array
	 */
	public static function is_a_global_group_id( $id ) {
		$post = WP_Post::get_instance( $id );

		if ( ! is_a( $post, 'WP_Post') ) {
			return false;
		}

		return ( 'global_product_addon' === $post->post_type );
	}

	/**
	 * For backwards compatibility, each option needs to have a label, price, min and max
	 * key when stored in meta, even if the field type does not require such a key
	 *
	 * @since 2.9.0
	 *
	 * @param array $fields
	 * @return array
	 */
	public static function coerce_options_to_contain_all_keys_before_saving_to_meta( $fields ) {
		$option_defaults = array(
			'label' => '(empty)',
			'price' => '',
			'min' => '',
			'max' => '',
		);

		foreach ( $fields as $key => $field ) {
			if ( array_key_exists( 'options', $field ) ) {
				$coerced_options = array();
				foreach ( $field['options'] as $option ) {
					$coerced_options[] = wp_parse_args( $option, $option_defaults );
				}
				$fields[ $key ]['options'] = $coerced_options;
			}
		}

		return $fields;
	}

	/**
	 * For backwards compatibility, we let each option have a label, price, min and max
	 * key when stored in meta, even though all those fields are not used by each field type.
	 * This function removes inappropriate keys in options based on the field type, and is
	 * used to help keep the REST API responses consistent with the structure of the requests.
	 *
	 * @since 2.9.0
	 *
	 * @param array $fields
	 * @return array
	 */
	public static function coerce_options_to_remove_field_type_inappropriate_keys( $fields ) {
		foreach ( $fields as $field_key => $field ) {
			if ( array_key_exists( 'options', $field ) ) {
				foreach ( $field['options'] as $option_key => $option ) {
					switch ( $field['type'] ) {
						case 'custom_price':
							unset( $option['price'] );
							break;
						case 'checkbox':
						case 'custom_email':
						case 'file_upload':
						case 'multiple_choice':
						case 'select':
							unset( $option['min'] );
							unset( $option['max'] );
							break;
					}

					$fields[ $field_key ]['options'][ $option_key ] = $option;
				}
			}
		}

		return $fields;
	}

}
