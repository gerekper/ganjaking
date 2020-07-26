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

/**
 * Class WC_OD_Delivery_Range.
 */
class WC_OD_Delivery_Range extends WC_Data {

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
	 * Data array, with defaults.
	 *
	 * @var array
	 */
	protected $data = array(
		'title'                   => '',
		'from'                    => 0,
		'to'                      => 0,
		'shipping_methods_option' => '',
		'shipping_methods'        => array(),
	);

	/**
	 * Constructor.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $range Delivery range object or ID.
	 */
	public function __construct( $range = null ) {
		parent::__construct( $range );

		if ( is_numeric( $range ) && ! empty( $range ) ) {
			$this->set_id( $range );
		} elseif ( $range instanceof self ) {
			$this->set_id( $range->get_id() );
		} elseif ( 0 === $range || '0' === $range ) {
			$this->set_id( 0 );
		} else {
			$this->set_object_read( true );
		}

		$this->read_object_from_database();
	}

	/**
	 * If the object has an ID, read using the data store.
	 *
	 * @since 1.7.0
	 */
	protected function read_object_from_database() {
		$this->data_store = WC_Data_Store::load( 'delivery_range' );

		if ( ! $this->get_object_read() ) {
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

	/**
	 * Gets the shipping methods option.
	 *
	 * @since 1.7.0
	 *
	 * @param string $context View or edit context.
	 * @return string
	 */
	public function get_shipping_methods_option( $context = 'view' ) {
		return $this->get_prop( 'shipping_methods_option', $context );
	}

	/**
	 * Gets the shipping methods.
	 *
	 * @since 1.7.0
	 *
	 * @param string $context View or edit context.
	 * @return array
	 */
	public function get_shipping_methods( $context = 'view' ) {
		return $this->get_prop( 'shipping_methods', $context );
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

	/**
	 * Sets the shipping methods option.
	 *
	 * @since 1.7.0
	 *
	 * @param string $option The shipping method option.
	 */
	public function set_shipping_methods_option( $option ) {
		$this->set_prop( 'shipping_methods_option', wc_clean( $option ) );
	}

	/**
	 * Sets the shipping methods.
	 *
	 * @since 1.7.0
	 *
	 * @param array $methods The shipping methods.
	 */
	public function set_shipping_methods( $methods ) {
		$this->set_prop( 'shipping_methods', wc_clean( $methods ) );
	}

	/*
	 * --------------------------------------------------------------------------
	 * Others
	 * --------------------------------------------------------------------------
	 */

	/**
	 * Saves the delivery range.
	 *
	 * @since 1.7.0
	 *
	 * @return int
	 */
	public function save() {
		if ( ! $this->data_store ) {
			return $this->get_id();
		}

		/** This action is documented in woocommerce/includes/abstracts/abstract-wc-data.php */
		do_action( 'woocommerce_before_' . $this->object_type . '_object_save', $this, $this->data_store );

		// The original WC_Data->save() method consider the ID zero as false.
		if ( null !== $this->get_id() ) {
			$this->data_store->update( $this );
		} else {
			$this->data_store->create( $this );
		}

		/** This action is documented in woocommerce/includes/abstracts/abstract-wc-data.php */
		do_action( 'woocommerce_after_' . $this->object_type . '_object_save', $this, $this->data_store );

		return $this->get_id();
	}

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
	 *
	 * @param string $shipping_method The shipping method.
	 * @return bool
	 */
	public function is_valid_for_shipping_method( $shipping_method ) {
		$option = $this->get_shipping_methods_option();

		if ( ! $option ) {
			return true;
		}

		$shipping_methods = wc_od_expand_shipping_methods( $this->get_shipping_methods() );
		$in_array         = in_array( $shipping_method, $shipping_methods, true );

		return ( ( 'specific' === $option && $in_array ) || ( 'all_except' === $option && ! $in_array ) );
	}
}
