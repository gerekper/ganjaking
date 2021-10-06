<?php

/**
 * WC Bookable Product Data Store: Stored in CPT.
 *
 * @todo When 2.6 support is dropped, implement WC_Object_Data_Store_Interface
 */
class WC_Product_Booking_Data_Store_CPT extends WC_Product_Data_Store_CPT {

	/**
	 * Meta keys and how they transfer to CRUD props.
	 *
	 * @var array
	 */
	private $booking_meta_key_to_props = array(
		'_has_additional_costs'                  => 'has_additional_costs',
		'_wc_booking_apply_adjacent_buffer'      => 'apply_adjacent_buffer',
		'_wc_booking_availability'               => 'availability',
		'_wc_booking_block_cost'                 => 'block_cost',
		'_wc_booking_buffer_period'              => 'buffer_period',
		'_wc_booking_calendar_display_mode'      => 'calendar_display_mode',
		'_wc_booking_cancel_limit_unit'          => 'cancel_limit_unit',
		'_wc_booking_cancel_limit'               => 'cancel_limit',
		'_wc_booking_check_availability_against' => 'check_start_block_only',
		'_wc_booking_cost'                       => 'cost',
		'_wc_booking_default_date_availability'  => 'default_date_availability',
		'_wc_booking_duration_type'              => 'duration_type',
		'_wc_booking_duration_unit'              => 'duration_unit',
		'_wc_booking_duration'                   => 'duration',
		'_wc_booking_enable_range_picker'        => 'enable_range_picker',
		'_wc_booking_first_block_time'           => 'first_block_time',
		'_wc_booking_has_person_types'           => 'has_person_types',
		'_wc_booking_has_persons'                => 'has_persons',
		'_wc_booking_has_resources'              => 'has_resources',
		'_wc_booking_has_restricted_days'        => 'has_restricted_days',
		'_wc_booking_max_date_unit'              => 'max_date_unit',
		'_wc_booking_max_date'                   => 'max_date_value',
		'_wc_booking_max_duration'               => 'max_duration',
		'_wc_booking_max_persons_group'          => 'max_persons',
		'_wc_booking_min_date_unit'              => 'min_date_unit',
		'_wc_booking_min_date'                   => 'min_date_value',
		'_wc_booking_min_duration'               => 'min_duration',
		'_wc_booking_min_persons_group'          => 'min_persons',
		'_wc_booking_person_cost_multiplier'     => 'has_person_cost_multiplier',
		'_wc_booking_person_qty_multiplier'      => 'has_person_qty_multiplier',
		'_wc_booking_pricing'                    => 'pricing',
		'_wc_booking_qty'                        => 'qty',
		'_wc_booking_requires_confirmation'      => 'requires_confirmation',
		'_wc_booking_resources_assignment'       => 'resources_assignment',
		'_wc_booking_restricted_days'            => 'restricted_days',
		'_wc_booking_user_can_cancel'            => 'user_can_cancel',
		'_wc_display_cost'                       => 'display_cost',
		'wc_booking_resource_label'              => 'resource_label',
		'_price'                                 => 'price',
	);

	public function __construct() {
		if ( is_callable( 'parent::__construct' ) ) {
			parent::__construct();
		}

		$this->internal_meta_keys = array_merge( $this->internal_meta_keys, array_keys( $this->booking_meta_key_to_props ) );
	}

	/**
	 * Force meta values on save.
	 *
	 * @param  WC_Product $product
	 */
	private function force_meta_values( &$product ) {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			update_post_meta( $product->get_id(), '_regular_price', '' );
			update_post_meta( $product->get_id(), '_sale_price', '' );
			update_post_meta( $product->get_id(), '_manage_stock', 'no' );
			update_post_meta( $product->get_id(), '_price', WC_Bookings_Cost_Calculation::calculated_base_cost( $product ) );
		} else {
			$product->set_regular_price( '' );
			$product->set_sale_price( '' );
			$product->set_manage_stock( false );
			$product->set_stock_status( 'instock' );

			// Set price so filters work.
			$product->set_price( WC_Bookings_Cost_Calculation::calculated_base_cost( $product ) );
		}
	}

	/**
	 * Method to create a new product in the database.
	 *
	 * @param WC_Product_Booking $product
	 */
	public function create( &$product ) {

		// If we're not using 3.0.x we can only store meta data here.
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {

			$id = wp_insert_post( apply_filters( 'woocommerce_new_product_data', array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'post_author'    => get_current_user_id(),
				'post_title'     => $product->get_name() ? $product->get_name() : __( 'Product', 'woocommerce-bookings' ),
				'post_content'   => $product->get_description(),
				'post_excerpt'   => $product->get_short_description(),
				'post_parent'    => $product->get_parent_id(),
				'comment_status' => $product->get_reviews_allowed() ? 'open' : 'closed',
				'ping_status'    => 'closed',
				'menu_order'     => $product->get_menu_order(),
				'post_date'      => date( 'Y-m-d H:i:s', $product->get_date_created() ),
				'post_date_gmt'  => get_gmt_from_date( date( 'Y-m-d H:i:s', $product->get_date_created() ) ),
			) ), true );

			$product->set_id( $id );
			$this->update_post_meta( $product, true );
			$this->force_meta_values( $product );
		} else {
			parent::create( $product );
		}
		$this->force_meta_values( $product );
		WC_Bookings_Cache::delete_booking_slots_transient();
	}

	/**
	 * Method to read product data.
	 *
	 * @param WC_Product
	 */
	public function read( &$product ) {
		// If we're not using 3.0.x we can only store meta data here.
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$this->read_product_data( $product );
		} else {
			parent::read( $product );
		}
	}

	/**
	 * Method to update a product in the database.
	 *
	 * @param WC_Product
	 */
	public function update( &$product ) {
		$this->force_meta_values( $product );

		// If we're not using 3.0.x we can only store meta data here.
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$this->update_post_meta( $product, true );
		} else {
			parent::update( $product );
		}
		WC_Bookings_Cache::delete_booking_slots_transient( $product->get_id() );
	}

	/**
	 * Method to delete a product from the database.
	 * @param WC_Product
	 * @param array $args Array of args to pass to the delete method.
	 */
	public function delete( &$product, $args = array() ) {
		parent::delete( $product, $args );
		WC_Bookings_Cache::delete_booking_slots_transient( $product->get_id() );
	}

	/**
	 * Helper method that updates all the post meta for a product based on it's settings in the WC_Product class.
	 *
	 * @param WC_Product
	 * @param bool $force Force all props to be written even if not changed. This is used during creation.
	 * @since 3.0.0
	 */
	public function update_post_meta( &$product, $force = false ) {
		// Only call parent method if using full CRUD object as of 3.0.x.
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			parent::update_post_meta( $product, $force );
		}

		foreach ( $this->booking_meta_key_to_props as $key => $prop ) {
			if ( is_callable( array( $product, "get_$prop" ) ) ) {

				// in version 2.6 and below WC expects the data for
				// for has resources and has persons to be stored as meta data with yes/no values
				if ( version_compare( WC_VERSION, '3.0', '<' ) && in_array( $prop, array( 'has_persons', 'has_resources' ) ) ) {
					update_post_meta( $product->get_id(), $key, $product->{ "get_$prop" }( 'edit' ) ? 'yes' : 'no' );
				} else {
					update_post_meta( $product->get_id(), $key, $product->{ "get_$prop" }( 'edit' ) );
				}
			}
		}

		$this->update_resources( $product );
		$this->update_person_types( $product );
	}

	/**
	 * Read product data. Can be overridden by child classes to load other props.
	 *
	 * @param WC_Product
	 */
	public function read_product_data( &$product ) {
		// Only call parent method if using full CRUD object as of 3.0.x.
		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			parent::read_product_data( $product );
		}

		$set_props = array();

		foreach ( $this->booking_meta_key_to_props as $key => $prop ) {
			if ( ! metadata_exists( 'post', $product->get_id(), $key ) ) {
				continue;
			}

			$value = get_post_meta( $product->get_id(), $key, true );

			switch ( $prop ) {
				case 'check_start_block_only':
					$set_props[ $prop ] = ( 'start' === $value || '1' === $value );
					break;
				default:
					$set_props[ $prop ] = $value;
					break;
			}

			// from WC 3.0 onwards people may still be upgrading later and
			// the fields below still be in 'yes'/'no' format. We need check for this.
			if ( in_array( $prop, array( 'has_persons', 'has_resources' ) ) ) {
				$set_props[ $prop ] = wc_bookings_string_to_bool( $value );
			}
		}

		$product->set_props( $set_props );
		$this->read_resources( $product );
		$this->read_person_types( $product );
	}

	/**
	 * Read resources from the database.
	 *
	 * @param WC_Product
	 */
	protected function read_resources( &$product ) {
		global $wpdb;
		$transient_name = 'book_res_ids_' . md5( http_build_query( array( $product->get_id(), WC_Cache_Helper::get_transient_version( 'bookings' ) ) ) );
		$resource_ids   = WC_Bookings_Cache::get( $transient_name );

		if ( false === $resource_ids ) {
			$resource_ids = wp_parse_id_list(
				$wpdb->get_col(
					$wpdb->prepare(
						"SELECT posts.ID
						FROM {$wpdb->prefix}wc_booking_relationships AS relationships
						LEFT JOIN $wpdb->posts AS posts ON posts.ID = relationships.resource_id
						WHERE relationships.product_id = %d
						ORDER BY sort_order ASC
						",
						$product->get_id()
					)
				)
			);
			// update cache.
			WC_Bookings_Cache::set( $transient_name, $resource_ids, DAY_IN_SECONDS * 30 );
		}

		$product->set_resource_ids( $resource_ids );
		$product->set_resource_base_costs( get_post_meta( $product->get_id(), '_resource_base_costs', true ) );
		$product->set_resource_block_costs( get_post_meta( $product->get_id(), '_resource_block_costs', true ) );
	}

	/**
	 * Read person types from the database.
	 *
	 * @param WC_Product
	 */
	protected function read_person_types( &$product ) {
		$person_type_objects = get_posts( array(
			'post_parent'    => $product->get_id(),
			'post_type'      => 'bookable_person',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'asc',
		) );
		$person_types = array();

		foreach ( $person_type_objects as $person_type_object ) {
			$person_types[ $person_type_object->ID ] = new WC_Product_Booking_Person_Type( $person_type_object );
		}

		$product->set_person_types( $person_types );
	}

	/**
	 * Update resources.
	 *
	 * @param WC_Product
	 */
	protected function update_resources( &$product ) {
		global $wpdb;

		update_post_meta( $product->get_id(), '_resource_base_costs', $product->get_resource_base_costs( 'edit' ) );
		update_post_meta( $product->get_id(), '_resource_block_costs', $product->get_resource_block_costs( 'edit' ) );

		$index = 0;

		$current_resource_ids = $wpdb->get_results( $wpdb->prepare( "
			SELECT *
			FROM {$wpdb->prefix}wc_booking_relationships
			WHERE `product_id` = %d
			ORDER BY sort_order ASC
		", $product->get_id() ), ARRAY_A );

		$current_temp = array();
		foreach ( $current_resource_ids as $resource ) {
			$current_temp[ $resource['resource_id'] ] = $resource;
		}
		$current_resource_ids = $current_temp;

		foreach ( $product->get_resource_ids( 'edit' ) as $resource_id ) {

			$replace = array(
				'sort_order'  => ( $index ++ ),
				'product_id'  => $product->get_id(),
				'resource_id' => $resource_id,
			);

			if ( isset( $current_resource_ids[ $resource_id ] ) ) {
				$replace['ID'] = $current_resource_ids[ $resource_id ]['ID'];
				unset( $current_resource_ids[ $resource_id ] );
			}

			$wpdb->replace(
				"{$wpdb->prefix}wc_booking_relationships",
				$replace
			);
		}

		if ( ! empty( $current_resource_ids ) ) {
			foreach ( $current_resource_ids as $resource ) {
				$wpdb->delete(
					"{$wpdb->prefix}wc_booking_relationships",
					array(
						'ID' => $resource['ID'],
					)
				);
			}
		}
		WC_Bookings_Cache::delete_booking_resources_transient( $product->get_id() );
	}

	/**
	 * Update person types. Person types are objects.
	 *
	 * @param WC_Product
	 */
	protected function update_person_types( &$product ) {
		$person_type_ids = get_posts( array(
			'post_parent'    => $product->get_id(),
			'post_type'      => 'bookable_person',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'asc',
			'fields'         => 'ids',
		) );
		$saved_type_ids = array();

		foreach ( $product->get_person_types( 'edit' ) as $person_type ) {
			$person_type->save();
			$saved_type_ids[] = $person_type->get_id();
		}

		$remove_person_types = array_diff( $person_type_ids, $saved_type_ids );

		foreach ( $remove_person_types as $person_type_id ) {
			$remove_person_type = new WC_Product_Booking_Person_Type( $person_type_id );

			$remove_person_type->set_parent_id( 0 );
			$remove_person_type->save();
		}
	}

	private static $booking_products_query_args = array(
		'post_status'      => 'publish',
		'post_type'        => 'product',
		'posts_per_page'   => -1,
		'tax_query'        => array(
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'booking',
			),
		),
		'suppress_filters' => true,
		'fields'           => 'ids',
	);

	/**
	 * Get all booking products.
	 *
	 * @return array
	 */
	public static function get_bookable_product_ids() {
		$ids = get_posts( apply_filters( 'get_booking_products_args', self::$booking_products_query_args ) );
		return wp_parse_id_list( $ids );
	}

	/**
	 * Get all booking products for rest endpoint.
	 *
	 * @return array
	 */
	public static function get_bookable_product_ids_for_slots_rest_endpoint() {
		$ids = get_posts( apply_filters( 'get_booking_products_args_for_slots_rest_endpoint', self::$booking_products_query_args ) );
		return wp_parse_id_list( $ids );
	}

	/**
	 * Read all persons from the database.
	 */
	public static function get_person_types_ids() {
		$ids = get_posts( apply_filters( 'woocommerce_bookings_get_person_types_ids', array(
			'post_type'      => 'bookable_person',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'suppress_filters' => true,
			'fields'           => 'ids',
		) ) );

		return wp_parse_id_list( $ids );
	}
}
