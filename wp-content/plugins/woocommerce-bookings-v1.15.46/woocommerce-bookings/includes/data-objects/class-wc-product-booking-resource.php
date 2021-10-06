<?php

/**
 * Class for a booking product's resource type.
 * This object is associated with a bookable product and some data comes from that relationship.
 */
class WC_Product_Booking_Resource extends WC_Bookings_Data {

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'product_booking_resource';

	/**
	 * Data array.
	 *
	 * @var array
	 */
	protected $data = array(
		'availability' => array(),
		'base_cost'    => 0,
		'block_cost'   => 0,
		'name'         => '',
		'parent_id'    => 0,
		'qty'          => 1,
		'sort_order'   => 0,
	);

	/**
	 * Constructor. Needs a product ID in order to load data from the product/resource relationship.
	 *
	 * @param int|object|array $resource
	 * @param int $product_id
	 */
	public function __construct( $resource = 0, $product_id = 0 ) {
		parent::__construct( $resource );

		if ( is_numeric( $resource ) && $resource > 0 ) {
			$this->set_id( $resource );
		} elseif ( $resource instanceof self ) {
			$this->set_id( $resource->get_id() );
		} elseif ( $resource instanceof WP_Post || ! empty( $resource->ID ) ) {
			$this->set_id( $resource->ID );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = WC_Data_Store::load( 'product-booking-resource' );

		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}

		if ( $product_id > 0 ) {
			$this->set_parent_id( $product_id );
		}
	}

	/**
	 * Save should create or update based on object existance.
	 *
	 * @return int
	 */
	public function save() {
		if ( $this->data_store ) {
			if ( $this->get_id() ) {
				$this->data_store->update( $this );
			} else {
				$this->data_store->create( $this );
			}
			return $this->get_id();
		}
	}

	/**
	 * __isset function for backwards compatibility with post data.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return in_array( $key, array( 'post_title', 'ID', 'menu_order' ) );
	}

	/**
	 * __get function for backwards compatibility with post data.
	 *
	 * @param string $key
	 * @return string
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'post_title':
				return $this->get_name();
			case 'ID':
				return $this->get_id();
			case 'menu_order':
				return $this->get_sort_order();
			default:
				return '';
		}
	}

	/**
	 * Return if we have qty at resource level.
	 *
	 * @return boolean
	 */
	public function has_qty() {
		return $this->get_qty() > 0;
	}

	/**
	 * Get the title of the resource.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->get_name();
	}

	/**
	 * Return the base cost (set at parent level).
	 *
	 * @return float
	 */
	public function get_base_cost() {
		$parent_id = $this->get_parent_id();
		$parent    = $parent_id ? wc_get_product( $parent_id ) : false;

		if ( $parent_id && $parent && $parent->is_type( 'booking' ) ) {
			$costs = $parent->get_resource_base_costs();
			$cost  = isset( $costs[ $this->get_id() ] ) ? $costs[ $this->get_id() ] : '';
		} else {
			$cost   = '';
		}

		return (float) $cost;
	}

	/**
	 * Return the block cost  (set at parent level).
	 *
	 * @return float
	 */
	public function get_block_cost() {
		$parent_id = $this->get_parent_id();
		$parent    = $parent_id ? wc_get_product( $parent_id ) : false;

		if ( $parent_id && $parent && $parent->is_type( 'booking' ) ) {
			$costs  = $parent->get_resource_block_costs();
			$cost   = isset( $costs[ $this->get_id() ] ) ? $costs[ $this->get_id() ] : '';
		} else {
			$cost   = '';
		}
		return (float) $cost;
	}

	/**
	 * Get availability.
	 *
	 * @param  string $context
	 * @return array
	 */
	public function get_availability( $context = 'view' ) {
		return $this->get_prop( 'availability', $context );
	}

	/**
	 * Set availability.
	 *
	 * @param array $value
	 */
	public function set_availability( $value ) {
		$this->set_prop( 'availability', (array) $value );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Getters and setters.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get parent ID.
	 *
	 * @param  string $context
	 * @return int
	 */
	public function get_parent_id( $context = 'view' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Set parent ID.
	 * The Parent is the product to which the resource belongs.
	 * @access protected
	 * @param  int $value
	 */
	protected function set_parent_id( $value ) {
		$this->set_prop( 'parent_id', absint( $value ) );
	}

	/**
	 * Get name.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set name.
	 *
	 * @param string $value
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', $value );
	}

	/**
	 * Get the quantity set at resource level.
	 *
	 * @param  string $context
	 * @return int
	 */
	public function get_qty( $context = 'view' ) {
		return $this->get_prop( 'qty', $context );
	}

	/**
	 * Set qty.
	 *
	 * @param int $value
	 */
	public function set_qty( $value ) {
		$this->set_prop( 'qty', $value );
	}

	/**
	 * Get sort_order.
	 *
	 * @param  string $context
	 * @return int
	 */
	public function get_sort_order( $context = 'view' ) {
		return $this->get_prop( 'sort_order', $context );
	}

	/**
	 * Set sort_order.
	 *
	 * @param int $value
	 */
	public function set_sort_order( $value ) {
		$this->set_prop( 'sort_order', $value );
	}

	/**
	 * Flush transients for all products related to a specific resource.
	 *
	 * @since  1.15.18
	 */
	public function flush_resource_transients() {
		if ( $this->data_store ) {
			$this->data_store->flush_resource_transients( $this );
		}
	}
}
