<?php
/**
 * A class for representing a time frame.
 *
 * @package WC_OD/Classes
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_OD_Time_Frame class.
 */
class WC_OD_Time_Frame extends WC_OD_Shipping_Methods_Data {

	/**
	 * The time frame ID.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * Time frame object data.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $data = array(
		'title'            => '',
		'time_from'        => '',
		'time_to'          => '',
		'number_of_orders' => 0,
	);

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param array $data The time frame data.
	 * @param int   $id   Optional. The time frame ID.
	 */
	public function __construct( array $data = array(), $id = null ) {
		parent::__construct( $data );

		if ( ! is_null( $id ) ) {
			$this->set_id( $id );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Gets the time frame ID.
	 *
	 * @since 1.6.0
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Gets the time frame title.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->get_prop( 'title' );
	}

	/**
	 * Gets the starting time.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function get_time_from() {
		return $this->get_prop( 'time_from' );
	}

	/**
	 * Gets the ending time.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function get_time_to() {
		return $this->get_prop( 'time_to' );
	}

	/**
	 * Gets the number of orders.
	 *
	 * @since 1.8.0
	 *
	 * @return int
	 */
	public function get_number_of_orders() {
		return $this->get_prop( 'number_of_orders' );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Sets the time frame ID.
	 *
	 * @since 1.6.0
	 *
	 * @param int $id The time frame ID.
	 */
	public function set_id( $id ) {
		$this->id = intval( $id );
	}

	/**
	 * Sets the time frame title.
	 *
	 * @since 1.6.0
	 *
	 * @param string $title The time frame title.
	 */
	public function set_title( $title ) {
		$this->set_prop( 'title', $title );
	}

	/**
	 * Sets the starting time.
	 *
	 * @since 1.6.0
	 *
	 * @param string $time_from The starting time.
	 */
	public function set_time_from( $time_from ) {
		$this->set_prop( 'time_from', $time_from );
	}

	/**
	 * Sets the ending time.
	 *
	 * @since 1.6.0
	 *
	 * @param string $time_to The ending time.
	 */
	public function set_time_to( $time_to ) {
		$this->set_prop( 'time_to', $time_to );
	}

	/**
	 * Sets the number of orders.
	 *
	 * @since 1.8.0
	 *
	 * @param int $number_of_orders The number of orders.
	 */
	public function set_number_of_orders( $number_of_orders ) {
		$this->set_prop( 'number_of_orders', $number_of_orders );
	}
}
