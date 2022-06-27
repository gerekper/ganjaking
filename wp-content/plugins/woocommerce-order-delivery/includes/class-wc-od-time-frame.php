<?php
/**
 * A class for representing a time frame.
 *
 * @package WC_OD/Classes
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Data', false ) ) {
	include_once WC_OD_PATH . 'includes/abstracts/abstract-class-wc-od-data.php';
}

/**
 * WC_OD_Time_Frame class.
 */
class WC_OD_Time_Frame extends WC_OD_Data {

	use WC_OD_Data_Lockout;
	use WC_OD_Data_Shipping_Methods;
	use WC_OD_Data_Fee;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'time_frame';

	/**
	 * Time frame object data.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $data = array(
		'title'     => '',
		'time_from' => '',
		'time_to'   => '',
	);

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @throws Exception When the load of the object data fails.
	 *
	 * @param mixed $data Time frame object, ID, or an array with data.
	 * @param int   $id   Optional. The time frame ID.
	 */
	public function __construct( $data = 0, $id = null ) {
		$this->data = array_merge(
			$this->data,
			$this->get_default_lockout_data(),
			$this->get_default_shipping_methods_data(),
			$this->get_default_fee_data()
		);

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
	 * Gets the time frame title.
	 *
	 * @since 1.6.0
	 * @since 2.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_title( $context = 'view' ) {
		return $this->get_prop( 'title', $context );
	}

	/**
	 * Gets the starting time.
	 *
	 * @since 1.6.0
	 * @since 2.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_time_from( $context = 'view' ) {
		return $this->get_prop( 'time_from', $context );
	}

	/**
	 * Gets the ending time.
	 *
	 * @since 1.6.0
	 * @since 2.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_time_to( $context = 'view' ) {
		return $this->get_prop( 'time_to', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

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
}
