<?php
/**
 * Class YITH_WCBK_Search_Form_Data_Store
 *
 * Data store for Search Forms
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\SearchForms
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class YITH_WCBK_Search_Form_Data_Store
 */
class YITH_WCBK_Search_Form_Data_Store extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {

	/**
	 * Meta keys and how they transfer to CRUD props.
	 *
	 * @var array
	 */
	protected $meta_keys_to_props = array(
		'_yith_wcbk_admin_search_form_fields' => 'fields',
		'_layout'                             => 'layout',
		'_colors'                             => 'colors',
		'_search-button-colors'               => 'search_button_colors',
		'_show-results'                       => 'show_results',
		'_search_button_border_radius'        => 'search_button_border_radius',
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
	 * @param YITH_WCBK_Search_Form $search_form The search form.
	 */
	public function create( &$search_form ) {
		$id = wp_insert_post(
			array(
				'post_type'   => YITH_WCBK_Post_Types::SEARCH_FORM,
				'post_status' => 'publish',
				'post_title'  => $search_form->get_name(),
			),
			true
		);

		if ( $id && ! is_wp_error( $id ) ) {
			$search_form->set_id( $id );

			$this->update_post_meta( $search_form, true );

			$search_form->save_meta_data();
			$search_form->apply_changes();
		}
	}

	/**
	 * Read
	 *
	 * @param YITH_WCBK_Search_Form $search_form The search form.
	 *
	 * @throws Exception If passed booking is invalid.
	 */
	public function read( &$search_form ) {
		$search_form->set_defaults();
		$post_object = get_post( $search_form->get_id() );
		if ( ! $search_form->get_id() || ! $post_object || YITH_WCBK_Post_Types::SEARCH_FORM !== $post_object->post_type ) {
			throw new Exception( __( 'Invalid search form.', 'yith-booking-for-woocommerce' ) );
		}

		$search_form->set_props(
			array(
				'name' => $post_object->post_title,
			)
		);

		$this->read_object_data( $search_form );
		$search_form->set_object_read( true );
	}

	/**
	 * Update
	 *
	 * @param YITH_WCBK_Search_Form $search_form The search form.
	 */
	public function update( &$search_form ) {
		$search_form->save_meta_data();
		$changes = $search_form->get_changes();

		// Only update the post when the post data changes.
		if ( array_intersect( array( 'name' ), array_keys( $changes ) ) ) {
			$post_data = array(
				'post_type'         => YITH_WCBK_Post_Types::SEARCH_FORM,
				'post_title'        => $search_form->get_name( 'edit' ),
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
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $search_form->get_id() ) );
				clean_post_cache( $search_form->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $search_form->get_id() ), $post_data ) );
			}
			$search_form->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.
		} else { // Only update post modified time to record this save event.
			$GLOBALS['wpdb']->update(
				$GLOBALS['wpdb']->posts,
				array(
					'post_modified'     => current_time( 'mysql' ),
					'post_modified_gmt' => current_time( 'mysql', 1 ),
				),
				array(
					'ID' => $search_form->get_id(),
				)
			);
			clean_post_cache( $search_form->get_id() );
		}

		$this->update_post_meta( $search_form, true );

		$search_form->apply_changes();
	}

	/**
	 * Delete
	 *
	 * @param YITH_WCBK_Search_Form $search_form The search form.
	 * @param array                 $args        Arguments.
	 */
	public function delete( &$search_form, $args = array() ) {
		$id = $search_form->get_id();

		if ( ! $id ) {
			return;
		}

		/**
		 * DO_ACTION: yith_wcbk_before_delete_search_form
		 * Hook to perform any action before deleting a Search Form.
		 *
		 * @param int                   $id          The resource ID.
		 * @param YITH_WCBK_Search_Form $search_form The search form.
		 */
		do_action( 'yith_wcbk_before_delete_search_form', $id, $search_form );
		wp_delete_post( $id );
		$search_form->set_id( 0 );

		/**
		 * DO_ACTION: yith_wcbk_delete_search_form
		 * Hook to perform any action after deleting a Search Form.
		 *
		 * @param int $id The resource ID.
		 */
		do_action( 'yith_wcbk_delete_search_form', $id );
	}

	/**
	 * Read object data.
	 *
	 * @param YITH_WCBK_Search_Form $search_form The search form.
	 */
	protected function read_object_data( &$search_form ) {
		$id               = $search_form->get_id();
		$post_meta_values = get_post_meta( $id );
		$set_props        = array();

		foreach ( $this->meta_keys_to_props as $meta_key => $prop ) {
			$meta_value         = $post_meta_values[ $meta_key ][0] ?? null;
			$set_props[ $prop ] = maybe_unserialize( $meta_value ); // get_post_meta only un-serializes single values.
		}

		$search_form->set_props( $set_props );
	}

	/**
	 * Helper method that updates all the post meta.
	 *
	 * @param YITH_WCBK_Search_Form $search_form The search form object.
	 * @param bool                  $force       Force update. Used during create.
	 */
	protected function update_post_meta( &$search_form, $force = false ) {
		$meta_key_to_props = $this->meta_keys_to_props;
		$props_to_update   = $force ? $meta_key_to_props : $this->get_props_to_update( $search_form, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $search_form->{"get_$prop"}( 'edit' );
			$value = is_string( $value ) ? wp_slash( $value ) : $value;

			$updated = update_post_meta( $search_form->get_id(), $meta_key, $value );

			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}
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

		$args['post_type'] = YITH_WCBK_Post_Types::SEARCH_FORM;
		$args['fields']    = 'ids';

		return $args;
	}

	/**
	 * Query search forms.
	 *
	 * @param array $args Arguments.
	 *
	 * @return int[]|object|YITH_WCBK_Search_Form[]
	 */
	public function query( $args ) {
		$args = wp_parse_args( $args, $this->get_default_query_args() );

		$args     = $this->map_args_from_wp_args( $args );
		$return   = $args['return'] ?? 'ids';
		$paginate = $args['paginate'] ?? false;

		$query = new WP_Query( $args );

		$items = 'ids' === $return ? $query->posts : array_filter( array_map( 'yith_wcbk_get_search_form', $query->posts ) );

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
