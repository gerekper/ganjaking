<?php
/**
 * Delivery range
 *
 * This class represents a delivery range object.
 *
 * @package WC_OD
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Data', false ) ) {
	include_once WC_OD_PATH . 'includes/abstracts/abstract-class-wc-od-data.php';
}

/**
 * Class WC_OD_Delivery_Range.
 */
class WC_OD_Delivery_Range extends WC_OD_Data {

	use WC_OD_Data_Shipping_Methods;

	/**
	 * Range ID
	 *
	 * @var int|null
	 */
	protected $id = null;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'delivery_range';

	/**
	 * Delivery range object data.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $data = array(
		'title' => '',
		'from'  => 0,
		'to'    => 0,
	);

	/**
	 * Constructor.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $range Delivery range object or ID.
	 */
	public function __construct( $range = null ) {
		$this->data = array_merge(
			$this->data,
			$this->get_default_shipping_methods_data()
		);

		$this->default_data = $this->data;

		if ( is_numeric( $range ) ) {
			$this->set_id( $range );
		} elseif ( $range instanceof self ) {
			$this->set_id( $range->get_id() );
		} else {
			$this->set_object_read();
		}

		$this->read_object_from_database();
	}

	/**
	 * If the object has an ID, read using the data store.
	 *
	 * @since 1.7.0
	 */
	protected function read_object_from_database() {
		$this->data_store = WC_Data_Store::load( $this->object_type );

		if ( ! $this->get_object_read() && ! is_null( $this->get_id() ) ) {
			$this->data_store->read( $this );
		}
	}

	/*
	 * --------------------------------------------------------------------------
	 * Getters
	 * --------------------------------------------------------------------------
	 */

	/**
	 * Gets the title.
	 *
	 * @since 1.7.0
	 *
	 * @param string $context View or edit context.
	 * @return string
	 */
	public function get_title( $context = 'view' ) {
		return $this->get_prop( 'title', $context );
	}

	/**
	 * Gets the minimum number of days.
	 *
	 * @since 1.7.0
	 *
	 * @param string $context View or edit context.
	 * @return int
	 */
	public function get_from( $context = 'view' ) {
		return $this->get_prop( 'from', $context );
	}

	/**
	 * Gets the maximum number of days.
	 *
	 * @since 1.7.0
	 *
	 * @param string $context View or edit context.
	 * @return int
	 */
	public function get_to( $context = 'view' ) {
		return $this->get_prop( 'to', $context );
	}

	/*
	 * --------------------------------------------------------------------------
	 * Setters
	 * --------------------------------------------------------------------------
	 */

	/**
	 * Sets the title.
	 *
	 * @since 1.7.0
	 *
	 * @param string $title The title.
	 */
	public function set_title( $title ) {
		$this->set_prop( 'title', wc_clean( $title ) );
	}

	/**
	 * Sets the minimum number of days.
	 *
	 * @since 1.7.0
	 *
	 * @param int $from The minimum number of days.
	 */
	public function set_from( $from ) {
		$this->set_prop( 'from', intval( $from ) );
	}

	/**
	 * Sets the maximum number of days.
	 *
	 * @since 1.7.0
	 *
	 * @param int $to The maximum number of days.
	 */
	public function set_to( $to ) {
		$this->set_prop( 'to', intval( $to ) );
	}

	/*
	 * --------------------------------------------------------------------------
	 * Others
	 * --------------------------------------------------------------------------
	 */

	/**
	 * Deletes a delivery range.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $force_delete Should the object be deleted permanently.
	 * @return bool
	 */
	public function delete( $force_delete = false ) {
		if ( ! $this->data_store || 0 === $this->get_id() ) {
			return false;
		}

		// This method doesn't return anything.
		$this->data_store->delete( $this, array( 'force_delete' => $force_delete ) );

		return true;
	}

	/**
	 * Gets if this delivery range is valid for the specified shipping method.
	 *
	 * @since 1.7.0
	 * @deprecated 2.0.0
	 *
	 * @param string $shipping_method The shipping method.
	 * @return bool
	 */
	public function is_valid_for_shipping_method( $shipping_method ) {
		wc_deprecated_function( __FUNCTION__, '2.0.0', 'WC_OD_Delivery_Range->validate_shipping_method()' );

		return $this->validate_shipping_method( $shipping_method );
	}
}
