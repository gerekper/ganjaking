<?php
/**
 * Class YITH_WCBK_Resource_Data_Store
 *
 * Data store for Resources
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class YITH_WCBK_Resource_Data_Store
 */
class YITH_WCBK_Resource_Data_Store extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {

	/**
	 * Meta keys and how they transfer to CRUD props.
	 *
	 * @var array
	 */
	protected $meta_keys_to_props = array(
		'_available_quantity'   => 'available_quantity',
		'_default_availability' => 'default_availability',
		'_availability_rules'   => 'availability_rules',
		'_image_id'             => 'image_id',
	);

	/**
	 * Stores updated props.
	 *
	 * @var array
	 */
	protected $updated_props = array();

	/**
	 * Create
	 *
	 * @param YITH_WCBK_Resource $resource The resource.
	 */
	public function create( &$resource ) {
		$id = wp_insert_post(
			array(
				'post_type'   => YITH_WCBK_Post_Types::RESOURCE,
				'post_status' => 'publish',
				'post_title'  => $resource->get_name(),
			),
			true
		);

		if ( $id && ! is_wp_error( $id ) ) {
			$resource->set_id( $id );

			$this->update_post_meta( $resource, true );

			$resource->save_meta_data();
			$resource->apply_changes();
		}
	}

	/**
	 * Read
	 *
	 * @param YITH_WCBK_Resource $resource The resource.
	 *
	 * @throws Exception If passed booking is invalid.
	 */
	public function read( &$resource ) {
		$resource->set_defaults();
		$post_object = get_post( $resource->get_id() );
		if ( ! $resource->get_id() || ! $post_object || YITH_WCBK_Post_Types::RESOURCE !== $post_object->post_type ) {
			throw new Exception( __( 'Invalid resource.', 'yith-booking-for-woocommerce' ) );
		}

		$resource->set_props(
			array(
				'name' => $post_object->post_title,
			)
		);

		$this->read_object_data( $resource );
		$resource->set_object_read( true );
	}

	/**
	 * Update
	 *
	 * @param YITH_WCBK_Resource $resource The resource.
	 */
	public function update( &$resource ) {
		$resource->save_meta_data();
		$changes = $resource->get_changes();

		// Only update the post when the post data changes.
		if ( array_intersect( array( 'name' ), array_keys( $changes ) ) ) {
			$post_data = array(
				'post_type'         => YITH_WCBK_Post_Types::RESOURCE,
				'post_title'        => $resource->get_name( 'edit' ),
				'post_modified'     => current_time( 'mysql' ),
				'post_modified_gmt' => current_time( 'mysql', 1 ),
			);

			/**
			 * When updating this object, to prevent infinite loops, use $wpdb
			 * to update data, since wp_update_post spawns more calls to the
			 * save_post action.
			 *
			 * This ensures hooks are fired by either WP itself (admin screen save),
			 * or an update purely from CRUD.
			 */
			if ( doing_action( 'save_post' ) ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $resource->get_id() ) );
				clean_post_cache( $resource->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $resource->get_id() ), $post_data ) );
			}
			$resource->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.
		} else { // Only update post modified time to record this save event.
			$GLOBALS['wpdb']->update(
				$GLOBALS['wpdb']->posts,
				array(
					'post_modified'     => current_time( 'mysql' ),
					'post_modified_gmt' => current_time( 'mysql', 1 ),
				),
				array(
					'ID' => $resource->get_id(),
				)
			);
			clean_post_cache( $resource->get_id() );
		}

		$this->update_post_meta( $resource, true );

		$this->clear_caches( $resource );

		$resource->apply_changes();
	}

	/**
	 * Delete
	 *
	 * @param YITH_WCBK_Resource $resource The resource.
	 * @param array              $args     Arguments.
	 */
	public function delete( &$resource, $args = array() ) {
		$id = $resource->get_id();

		if ( ! $id ) {
			return;
		}

		/**
		 * DO_ACTION: yith_wcbk_before_delete_resource
		 * Hook to perform any action before deleting a Resource.
		 *
		 * @param int                $id       The resource ID.
		 * @param YITH_WCBK_Resource $resource The resource.
		 */
		do_action( 'yith_wcbk_before_delete_resource', $id, $resource );
		wp_delete_post( $id );
		$resource->set_id( 0 );

		/**
		 * DO_ACTION: yith_wcbk_delete_resource
		 * Hook to perform any action after deleting a Resource.
		 *
		 * @param int $id The resource ID.
		 */
		do_action( 'yith_wcbk_delete_resource', $id );
	}

	/**
	 * Read object data.
	 *
	 * @param YITH_WCBK_Resource $resource The resource.
	 */
	protected function read_object_data( &$resource ) {
		$id               = $resource->get_id();
		$post_meta_values = get_post_meta( $id );
		$set_props        = array();

		foreach ( $this->meta_keys_to_props as $meta_key => $prop ) {
			$meta_value         = $post_meta_values[ $meta_key ][0] ?? null;
			$set_props[ $prop ] = maybe_unserialize( $meta_value ); // get_post_meta only un-serializes single values.
		}

		$resource->set_props( $set_props );
	}

	/**
	 * Helper method that updates all the post meta.
	 *
	 * @param YITH_WCBK_Resource $resource Resource object.
	 * @param bool               $force    Force update. Used during create.
	 */
	protected function update_post_meta( &$resource, $force = false ) {
		$meta_key_to_props = $this->meta_keys_to_props;
		$props_to_update   = $force ? $meta_key_to_props : $this->get_props_to_update( $resource, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $resource->{"get_$prop"}( 'edit' );
			$value = is_string( $value ) ? wp_slash( $value ) : $value;
			switch ( $prop ) {
				case 'default_availability':
				case 'availability_rules':
					$value = yith_wcbk_simple_object_to_array( $value );
					break;
			}

			$updated = update_post_meta( $resource->get_id(), $meta_key, $value );

			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}
	}

	/**
	 * Clear any caches.
	 *
	 * @param YITH_WCBK_Resource $resource Resource object.
	 */
	protected function clear_caches( &$resource ) {
		yith_wcbk_clear_resource_related_caches( $resource->get_id() );
	}

	/**
	 * Retrieve the default query args.
	 *
	 * @return array
	 */
	protected function get_default_query_args() {
		return array(
			'items_per_page' => 10,
			'search'         => '',
			'paginate'       => false,
			'page'           => 1,
			'post_status'    => 'publish',
			'order'          => 'ASC',
			'order_by'       => 'title',
			'return'         => 'ids', // allowed values: ids, objects.
		);
	}

	/**
	 * Retrieve the default query args.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	protected function map_args_from_wp_args( $args ) {
		$key_mapping = array(
			'items_per_page' => 'posts_per_page',
			'order_by'       => 'orderby',
			'search'         => 's',
			'page'           => 'paged',
			'include'        => 'post__in',
		);

		foreach ( $key_mapping as $query_key => $wp_key ) {
			if ( isset( $args[ $query_key ] ) ) {
				$args[ $wp_key ] = $args[ $query_key ];
				unset( $args[ $query_key ] );
			}
		}

		$args['post_type'] = YITH_WCBK_Post_Types::RESOURCE;
		$args['fields']    = 'ids';

		return $args;
	}

	/**
	 * Query resources.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array|int[]|object|WP_Post[]
	 */
	public function query( $args ) {
		$args = wp_parse_args( $args, $this->get_default_query_args() );

		$args     = $this->map_args_from_wp_args( $args );
		$return   = $args['return'] ?? 'ids';
		$paginate = $args['paginate'] ?? false;

		$query = new WP_Query( $args );

		$items = 'ids' === $return ? $query->posts : array_filter( array_map( 'yith_wcbk_get_resource', $query->posts ) );

		if ( $paginate ) {
			return (object) array(
				'items'       => $items,
				'total'       => $query->found_posts,
				'total_pages' => $query->max_num_pages,
			);
		}

		return $items;
	}
}
