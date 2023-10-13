<?php
/**
 * Class YITH_WCBK_Service_Data_Store
 *
 * Data store for Services
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Services
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class YITH_WCBK_Service_Data_Store
 *
 * @since 4.0.0
 */
class YITH_WCBK_Service_Data_Store extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {

	/**
	 * Meta type.
	 *
	 * @var string
	 */
	protected $meta_type = 'term';

	/**
	 * Meta type.
	 *
	 * @var string
	 */
	protected $taxonomy = YITH_WCBK_Post_Types::SERVICE_TAX;

	/**
	 * Meta keys and how they transfer to CRUD props.
	 *
	 * @var array
	 */
	protected $meta_keys_to_props = array(
		'price'                  => 'base_price',
		'optional'               => 'optional',
		'hidden'                 => 'hidden',
		'hidden_in_search_forms' => 'hidden_in_search_forms',
		'multiply_per_blocks'    => 'multiply_per_blocks',
		'multiply_per_persons'   => 'multiply_per_persons',
		'price_for_person_types' => 'price_for_person_types',
		'quantity_enabled'       => 'quantity_enabled',
		'min_quantity'           => 'min_quantity',
		'max_quantity'           => 'max_quantity',
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
	 * @param YITH_WCBK_Service $service The service.
	 */
	public function create( &$service ) {
		$result = wp_insert_term(
			$service->get_name( 'edit' ),
			$this->taxonomy,
			array(
				'description' => $service->get_description( 'edit' ),
				'slug'        => $service->get_slug( 'edit' ),
			),
			true
		);

		$id = ! is_wp_error( $result ) ? ( $result['term_id'] ?? false ) : false;
		if ( $id ) {
			$service->set_id( $id );

			$this->update_object_meta( $service, true );

			$service->save_meta_data();
			$service->apply_changes();
		}
	}

	/**
	 * Read
	 *
	 * @param YITH_WCBK_Service $service The service.
	 *
	 * @throws Exception If passed service is invalid.
	 */
	public function read( &$service ) {
		$service->set_defaults();
		$term = get_term( $service->get_id(), $this->taxonomy );
		if ( ! $service->get_id() || ! $term ) {
			throw new Exception( __( 'Invalid service.', 'yith-booking-for-woocommerce' ) );
		}

		$service->set_props(
			array(
				'name'        => $term->name,
				'description' => $term->description,
				'slug'        => $term->slug,
			)
		);

		$this->read_object_data( $service );
		$service->set_object_read( true );
	}

	/**
	 * Update
	 *
	 * @param YITH_WCBK_Service $service The service.
	 */
	public function update( &$service ) {
		$service->save_meta_data();
		$changes = $service->get_changes();

		// Only update the post when the post data changes.
		if ( array_intersect( array( 'name', 'description', 'slug' ), array_keys( $changes ) ) ) {
			$term_data = array(
				'name'        => $service->get_name( 'edit' ),
				'description' => $service->get_description( 'edit' ),
				'slug'        => $service->get_slug( 'edit' ),
			);
			wp_update_term( $service->get_id(), $this->taxonomy, $term_data );

			$service->read_meta_data( true ); // Refresh internal meta data.
		}

		$this->update_object_meta( $service, true );

		$service->apply_changes();
	}

	/**
	 * Delete
	 *
	 * @param YITH_WCBK_Service $service The service.
	 * @param array             $args    Arguments.
	 */
	public function delete( &$service, $args = array() ) {
		$id = $service->get_id();

		if ( ! $id ) {
			return;
		}

		/**
		 * DO_ACTION: yith_wcbk_before_delete_service
		 * Hook to perform any action before deleting a Service.
		 *
		 * @param int               $id      The service ID.
		 * @param YITH_WCBK_Service $service The service.
		 */
		do_action( 'yith_wcbk_before_delete_service', $id, $service );
		wp_delete_term( $id, $this->taxonomy );
		$service->set_id( 0 );

		/**
		 * DO_ACTION: yith_wcbk_delete_service
		 * Hook to perform any action after deleting a Service.
		 *
		 * @param int $id The service ID.
		 */
		do_action( 'yith_wcbk_delete_service', $id );
	}

	/**
	 * Read object data.
	 *
	 * @param YITH_WCBK_Service $service The service.
	 */
	protected function read_object_data( &$service ) {
		$id               = $service->get_id();
		$post_meta_values = get_term_meta( $id );
		$set_props        = array();

		foreach ( $this->meta_keys_to_props as $meta_key => $prop ) {
			$meta_value         = $post_meta_values[ $meta_key ][0] ?? null;
			$set_props[ $prop ] = maybe_unserialize( $meta_value ); // get_term_meta only un-serializes single values.
		}

		$service->set_props( $set_props );
	}

	/**
	 * Helper method that updates all the post meta.
	 *
	 * @param YITH_WCBK_Service $service Service object.
	 * @param bool              $force   Force update. Used during create.
	 */
	protected function update_object_meta( &$service, $force = false ) {
		$meta_key_to_props = $this->meta_keys_to_props;
		$props_to_update   = $force ? $meta_key_to_props : $this->get_props_to_update( $service, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $service->{"get_$prop"}( 'edit' );
			$value = is_string( $value ) ? wp_slash( $value ) : $value;

			$updated = update_term_meta( $service->get_id(), $meta_key, $value );

			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}
	}
}
