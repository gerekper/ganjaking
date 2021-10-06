<?php

/**
 * Class for a booking product's person types.
 */
class WC_Product_Booking_Person_Type extends WC_Bookings_Data {

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'product_booking_person_type';

	/**
	 * Data array.
	 *
	 * @var array
	 */
	protected $data = array(
		'block_cost'  => 0,
		'cost'        => 0,
		'description' => '',
		'max'         => '',
		'min'         => 0,
		'name'        => '',
		'parent_id'   => 0,
		'sort_order'  => 0,
	);

	/**
	 * Constructor.
	 *
	 * @param int|object|array $person_type
	 */
	public function __construct( $person_type = 0 ) {
		parent::__construct( $person_type );

		if ( is_numeric( $person_type ) && $person_type > 0 ) {
			$this->set_id( $person_type );
		} elseif ( $person_type instanceof self ) {
			$this->set_id( $person_type->get_id() );
		} elseif ( $person_type instanceof WP_Post ) {
			$this->set_id( $person_type->ID );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = WC_Data_Store::load( 'product-booking-person-type' );

		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
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
		return in_array( $key, array( 'post_title', 'ID', 'menu_order', 'post_parent' ) );
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
			case 'post_parent':
				return $this->get_parent_id();
			default:
				return '';
		}
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
	 *
	 * @param  int $value
	 */
	public function set_parent_id( $value ) {
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
	 * Get description.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_description( $context = 'view' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Set description.
	 *
	 * @param string $value
	 */
	public function set_description( $value ) {
		$this->set_prop( 'description', $value );
	}

	/**
	 * Get min.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_min( $context = 'view' ) {
		return $this->get_prop( 'min', $context );
	}

	/**
	 * Set min.
	 *
	 * @param int $value
	 */
	public function set_min( $value ) {
		$this->set_prop( 'min', $value );
	}

	/**
	 * Get max.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_max( $context = 'view' ) {
		return $this->get_prop( 'max', $context );
	}

	/**
	 * Set max.
	 *
	 * @param int $value
	 */
	public function set_max( $value ) {
		$this->set_prop( 'max', $value );
	}

	/**
	 * Get cost.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_cost( $context = 'view' ) {
		return $this->get_prop( 'cost', $context );
	}

	/**
	 * Set cost.
	 *
	 * @param int $value
	 */
	public function set_cost( $value ) {
		$this->set_prop( 'cost', $value );
	}

	/**
	 * Get block_cost.
	 *
	 * @param  string $context
	 * @return string
	 */
	public function get_block_cost( $context = 'view' ) {
		return $this->get_prop( 'block_cost', $context );
	}

	/**
	 * Set block_cost.
	 *
	 * @param int $value
	 */
	public function set_block_cost( $value ) {
		$this->set_prop( 'block_cost', $value );
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
}
