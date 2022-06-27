<?php
/**
 * A class for representing a delivery day.
 *
 * @package WC_OD/Classes
 * @since   1.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Data', false ) ) {
	include_once WC_OD_PATH . 'includes/abstracts/abstract-class-wc-od-data.php';
}

/**
 * WC_OD_Delivery_Day class.
 */
class WC_OD_Delivery_Day extends WC_OD_Data {

	use WC_OD_Data_Fee;
	use WC_OD_Data_Lockout;
	use WC_OD_Data_Shipping_Methods {
		validate_shipping_method as trait_validate_shipping_method;
	}

	/**
	 * Delivery day ID.
	 *
	 * The ID represents the weekday number. From 0 for Sunday to 6 for Saturday.
	 *
	 * @var int|null
	 */
	protected $id;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'delivery_day';

	/**
	 * Delivery day object data.
	 *
	 * Name value pairs (name + default value).
	 *
	 * @var array
	 */
	protected $data = array(
		'enabled' => 'yes',
	);

	/**
	 * Time frames.
	 *
	 * @since 2.0.0
	 *
	 * @var WC_OD_Time_Frame[]|null
	 */
	protected $time_frames;

	/**
	 * Time frames to delete when saving the delivery day.
	 *
	 * @var WC_OD_Time_Frame[]
	 */
	protected $time_frames_to_delete = array();

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 * @since 2.0.0 The argument '$weekday' is deprecated.
	 *
	 * @throws Exception When the load of the object data fails.
	 *
	 * @param mixed $data    Delivery day object, ID, or an array with data.
	 * @param int   $weekday Deprecated. The weekday number.
	 */
	public function __construct( $data = array(), $weekday = null ) {
		$this->data = array_merge(
			$this->data,
			$this->get_default_lockout_data(),
			$this->get_default_shipping_methods_data(),
			$this->get_default_fee_data()
		);

		$this->default_data = $this->data;

		if ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );

			if ( ! is_null( $weekday ) ) {
				wc_doing_it_wrong( __FUNCTION__, 'The argument $weekday is deprecated. Use the method set_id() instead.', '2.0.0' );
				$this->set_id( $weekday );
			}

			$this->set_object_read();
		} else {
			$this->set_object_read();
		}

		$this->read_object_from_database();
	}

	/**
	 * If the object has an ID, read using the data store.
	 *
	 * @since 2.0.0
	 *
	 * @throws Exception When the load of the object data fails.
	 */
	protected function read_object_from_database() {
		$this->data_store = WC_Data_Store::load( $this->object_type );

		if ( ! $this->get_object_read() && ! is_null( $this->get_id() ) ) {
			$this->data_store->read( $this );
		}
	}

	/**
	 * Deletes an object and returns the result.
	 *
	 * @since 2.0.0
	 *
	 * @param  bool $force_delete Should the date be deleted permanently.
	 * @return bool
	 */
	public function delete( $force_delete = false ) {
		$day_id = $this->get_id();
		$result = parent::delete( $force_delete );

		// Preserve the ID.
		$this->set_id( $day_id );

		return $result;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Gets the weekday number.
	 *
	 * @since 1.6.0
	 *
	 * @return int
	 */
	public function get_weekday() {
		return $this->get_id();
	}

	/**
	 * Gets the 'enabled' property.
	 *
	 * @since 1.6.0
	 * @since 2.0.0 Added parameter `$context`.
	 *
	 * @param string $context What the value is for. Accepts: 'view', 'edit'. Default: 'view'.
	 * @return string
	 */
	public function get_enabled( $context = 'view' ) {
		return $this->get_prop( 'enabled', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Sets the 'enabled' property.
	 *
	 * @since 1.6.0
	 *
	 * @param string $enabled The status.
	 */
	public function set_enabled( $enabled ) {
		$this->set_prop( 'enabled', wc_bool_to_string( $enabled ) );
	}

	/**
	 * Sets the weekday number.
	 *
	 * @since 1.6.0
	 *
	 * @param int $weekday The weekday number.
	 */
	public function set_weekday( $weekday ) {
		$this->set_id( $weekday );
	}

	/*
	|--------------------------------------------------------------------------
	| Time frames
	|--------------------------------------------------------------------------
	*/

	/**
	 * Gets the time frames.
	 *
	 * @since 1.6.0
	 *
	 * @return WC_OD_Collection_Time_Frames
	 */
	public function get_time_frames() {
		$this->read_time_frames();

		return new WC_OD_Collection_Time_Frames( $this->time_frames );
	}

	/**
	 * Sets the time frames.
	 *
	 * @since 1.6.0
	 * @since 2.0.0 The parameter `$time_frames` is required. Accepts a time frames collection.
	 *
	 * @param WC_OD_Collection_Time_Frames|array $time_frames The time frames to set.
	 */
	public function set_time_frames( $time_frames ) {
		$this->read_time_frames();

		// Mark all time frames to delete.
		$this->time_frames_to_delete += $this->time_frames;

		// Reset the time frames.
		$this->time_frames = array();

		// Valid for collections and arrays.
		foreach ( $time_frames as $time_frame ) {
			$this->add_time_frame( $time_frame );
		}
	}

	/**
	 * Gets if the delivery day has time frames defined or not.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public function has_time_frames() {
		$this->read_time_frames();

		return ( ! empty( $this->time_frames ) );
	}

	/**
	 * Gets a time frame by ID.
	 *
	 * @since 2.0.0
	 *
	 * @param int $time_frame_id Time Frame ID.
	 * @return WC_OD_Time_Frame|false
	 */
	public function get_time_frame( $time_frame_id ) {
		$time_frames = $this->get_time_frames();

		return ( isset( $time_frames[ $time_frame_id ] ) ? $time_frames[ $time_frame_id ] : false );
	}

	/**
	 * Adds a time frame.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $the_time_frame Time frame object, ID, or an array with data.
	 * @return bool
	 */
	public function add_time_frame( $the_time_frame ) {
		$time_frame = wc_od_get_time_frame( $the_time_frame );

		if ( ! $time_frame ) {
			return false;
		}

		// Load existing time frames first.
		$this->get_time_frames();

		$time_frame_id = $time_frame->get_id();

		if ( $time_frame_id ) {
			// Recover the time frame if added again.
			unset( $this->time_frames_to_delete[ $time_frame_id ] );
		} else {
			$time_frame_id = 'new:' . count( $this->time_frames );
		}

		$this->time_frames[ $time_frame_id ] = $time_frame;

		return true;
	}

	/**
	 * Removes a time frame.
	 *
	 * @since 2.0.0
	 *
	 * @param int $time_frame_id Time Frame ID.
	 */
	public function remove_time_frame( $time_frame_id ) {
		$time_frame = $this->get_time_frame( $time_frame_id );

		if ( ! $time_frame ) {
			return;
		}

		$this->time_frames_to_delete[ $time_frame_id ] = $time_frame;

		unset( $this->time_frames[ $time_frame_id ] );
	}

	/**
	 * Reads the time frames from the database.
	 *
	 * @since 2.0.0
	 */
	protected function read_time_frames() {
		if ( ! is_null( $this->time_frames ) ) {
			return;
		}

		// Lazy load the time frames.
		$this->time_frames = ( $this->data_store ? $this->data_store->read_time_frames( $this ) : array() );
	}

	/**
	 * Saves the time frames in the database.
	 *
	 * @since 2.0.0
	 */
	public function save_time_frames() {
		if ( is_null( $this->time_frames ) ) {
			return;
		}

		foreach ( $this->time_frames_to_delete as $time_frame ) {
			$time_frame->delete();
		}

		$time_frames = array();

		foreach ( $this->time_frames as $time_frame ) {
			$time_frame_id = $time_frame->save();

			$time_frames[ $time_frame_id ] = $time_frame;
		}

		$this->time_frames = $time_frames;
	}

	/*
	|--------------------------------------------------------------------------
	| Other Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Gets if the delivery day is enabled or not.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return wc_string_to_bool( $this->get_enabled() );
	}

	/**
	 * Gets if a shipping method is valid or not.
	 *
	 * @since 1.6.0
	 *
	 * @param string $shipping_method The shipping method to validate.
	 * @return bool
	 */
	public function validate_shipping_method( $shipping_method ) {
		if ( ! $this->has_time_frames() ) {
			return $this->trait_validate_shipping_method( $shipping_method );
		}

		$valid       = false;
		$time_frames = $this->get_time_frames();

		foreach ( $time_frames as $time_frame ) {
			if ( $time_frame->validate_shipping_method( $shipping_method ) ) {
				$valid = true;
				break;
			}
		}

		return $valid;
	}

	/**
	 * Converts the object into an array.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function to_array() {
		$data = $this->get_data_without( array( 'id', 'meta_data' ) );

		$data['time_frames'] = $this->get_time_frames()->to_array();

		return $data;
	}
}
