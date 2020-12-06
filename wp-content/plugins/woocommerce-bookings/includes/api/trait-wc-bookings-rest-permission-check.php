<?php
/**
 * Trait used by some bookings CRUD controllers.
 *
 * @package WooCommerce\Bookings\Rest\Controller
 */

/**
 * Trait WC_Bookings_Rest_Permission_Check.
 */
trait WC_Bookings_Rest_Permission_Check {

	/**
	 * Override base permission check to add our filter.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		add_filter( 'woocommerce_rest_check_permissions', array( $this, 'rest_check_permissions' ), 10, 4 );

		$return = parent::get_item_permissions_check( $request );

		remove_filter( 'woocommerce_rest_check_permissions', array( $this, 'rest_check_permissions' ) );

		return $return;
	}

	/**
	 * Override base permission check to add our filter.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		add_filter( 'woocommerce_rest_check_permissions', array( $this, 'rest_check_permissions' ), 10, 4 );

		$return = parent::get_items_permissions_check( $request );

		remove_filter( 'woocommerce_rest_check_permissions', array( $this, 'rest_check_permissions' ) );

		return $return;
	}

	/**
	 * Add the filter during get_items() as well because get_items() calls wc_rest_check_post_permissions() for each item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		add_filter( 'woocommerce_rest_check_permissions', array( $this, 'rest_check_permissions' ), 10, 4 );

		$return = parent::get_items( $request );

		remove_filter( 'woocommerce_rest_check_permissions', array( $this, 'rest_check_permissions' ) );

		return $return;
	}

	/**
	 * Filter to override how wc_rest_check_post_permissions works.
	 * Allows non-store owners to see published products and categories.
	 *
	 * @param bool   $permission Current permission.
	 * @param string $context   Request context.
	 * @param int    $object_id Post ID.
	 * @param string $post_type Post Type.
	 * @return bool
	 */
	public function rest_check_permissions( $permission, $context, $object_id, $post_type ) {

		if ( $permission ) {
			return $permission;
		}

		if ( 'revision' === $post_type ) {
			return false;
		}

		$contexts = array(
			'read'   => 'read_post',
			'create' => 'publish_posts',
			'edit'   => 'edit_post',
			'delete' => 'delete_post',
			'batch'  => 'edit_others_posts',
		);

		$cap = $contexts[ $context ];
		if ( 'read_post' === $cap ) {

			if ( ! $object_id ) {
				// Anyone can read all.
				return true;
			}
			$post_status = get_post_status( $object_id );
			if ( $post_status ) {
				if ( 'publish' === $post_status ) {
					// Anyone can read published items.
					return true;
				}
				$post_status_obj = get_post_status_object( $post_status );
				if ( $post_status_obj && $post_status_obj->public ) {
					// Post status is equivalent to published.
					return true;
				}
			}
		}

		$post_type_object = get_post_type_object( $post_type );
		if ( $post_type_object ) {
			$permission = current_user_can( $post_type_object->cap->$cap, $object_id );
		}

		return $permission;
	}
}
