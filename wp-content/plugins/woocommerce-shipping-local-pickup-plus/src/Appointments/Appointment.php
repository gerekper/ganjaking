<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_9 as Framework;

/**
 * An appointment stores a record of the choices made at checkout by a user who chose to pickup their order.
 *
 * Since an order could have items for shipping and items for pickup, we wrap the appointment object around an order shipping item.
 * The appointment data is stored as shipping item meta data.
 *
 * @since 2.7.0
 */
class Appointment extends \WC_Data {


	/** appointment duration types, for internal use */
	const DURATION_TYPE_ANYTIME                = 'anytime';
	const DURATION_TYPE_ANYTIME_WITH_LEAD_TIME = 'anytime-with-lead-time';
	const DURATION_TYPE_PICKUP_TIME            = 'pickup-time';


	/** @var \WC_Order_Item_Shipping the shipping item associated with a new appointment object */
	private $order_shipping_item;

	/** @var string the appointment type */
	private $duration_type;

	/** @var string the type of object - used in action and filter names */
	protected $object_type = 'pickup_appointment';

	/** @var array the data for this appointment object */
	protected $data = [
		'start'                   => null,
		'end'                     => null,
		'pickup_location_id'      => 0,
		'pickup_location_address' => null,
		'pickup_location_name'    => '',
		'pickup_location_phone'   => '',
	];


	/**
	 * Initializes the appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param int|\WC_Order_Item_Shipping $id shipping item ID or shipping item object to initialize this appointment
	 * @throws Framework\SV_WC_Plugin_Exception when the shipping item is not valid, is not a local pickup plus item or has no appointment data
	 */
	public function __construct( $id = 0 ) {

		parent::__construct( $id );

		// non zero shipping item ID
		if ( $id && is_numeric( $id ) ) {

			$this->set_id( $id );

			try {

				// an exception will be thrown if there is no order item with the given ID
				$shipping_item = new \WC_Order_Item_Shipping( $this->get_id() );

			} catch ( \Exception $e ) {

				throw new Framework\SV_WC_Plugin_Exception( $e->getMessage() );
			}

			$this->set_props( $this->get_data_from_shipping_item( $shipping_item ) );
			$this->set_object_read( true );

		} elseif ( $id instanceof \WC_Order_Item_Shipping ) {

			// a shipping item that already exists in the database
			if ( $id->get_id() ) {

				$this->set_id( $id->get_id() );
				$this->set_props( $this->get_data_from_shipping_item( $id ) );

			// a newly created shipping item
			} else {

				$this->order_shipping_item = $id;
			}

			$this->set_object_read( true );

		} elseif ( 0 === $id ) {

			$this->set_object_read( true );

		} else {

			throw new Framework\SV_WC_Plugin_Exception( 'Invalid shipping item id.' );
		}
	}


	/**
	 * Reads appointment data from a shipping item object.
	 *
	 * @since 2.7.1
	 *
	 * @param \WC_Order_Item_Shipping $shipping_item
	 * @return array props for the appointment object
	 * @throws Framework\SV_WC_Plugin_Exception when the shipping item is not a local pickup plus item or has no appointment data
	 */
	private function get_data_from_shipping_item( \WC_Order_Item_Shipping $shipping_item ) {

		if ( wc_local_pickup_plus_shipping_method_id() !== $shipping_item->get_method_id() ) {
			throw new Framework\SV_WC_Plugin_Exception( 'Invalid shipping item.' );
		}

		$order_items = wc_local_pickup_plus()->get_orders_instance()->get_order_items_instance();

		$start = $shipping_item->get_meta( '_pickup_appointment_start', true );
		$end   = $shipping_item->get_meta( '_pickup_appointment_end', true );

		// try to calculate the start and end dates using legacy metadata for past orders
		try {

			if ( ! $start ) {

				// legacy meta data used to store the day of the pickup only, without time
				$date = wc_get_order_item_meta( $shipping_item->get_id(), '_pickup_date' );

				// legacy meta data used to store a value to offset the location's business hours for the given pickup day
				$time_offset = max( 0, (int) wc_get_order_item_meta( $shipping_item->get_id(), '_pickup_minimum_hours' ) );

				$start = $end = null;

				if ( empty( $date ) ) {
					throw new Framework\SV_WC_Plugin_Exception( 'The shipping item has no appointment data.' );
				}

				// anytime appointment with non-zero lead time
				if ( $time_offset > 0 ) {

					$start = new \DateTime( $date, $this->get_pickup_location_address()->get_timezone() );

					// add minimum hours to get the first available pickup time for the pickup date
					$start->add( new \DateInterval( "PT{$time_offset}S" ) );

				// anytime appointment
				} else {

					$start = new \DateTime( $date, $this->get_pickup_location_address()->get_timezone() );
					$end   = ( clone $start )->add( new \DateInterval( 'P1D' ) );
				}

			} else {

				// $end is optional because `_pickup_appointment_end` won't be defined if the appointment is anytime with non-zero lead time
				$start = new \DateTime( "@{$start}" );
				$end   = $end ? new \DateTime( "@{$end}" ) : null;

			}

		} catch ( \Exception $e ) {

			throw new Framework\SV_WC_Plugin_Exception( 'The shipping item has invalid appointment data.' );
		}

		// if we get here then we have appointment data and can attempt to extract pickup location data from the shipping item's meta
		return  [
			'start'                   => $start,
			'end'                     => $end,
			'pickup_location_id'      => $order_items->get_order_item_pickup_location_id( $shipping_item ),
			'pickup_location_address' => $order_items->get_order_item_pickup_location_address( $shipping_item, 'object' ),
			'pickup_location_name'    => $order_items->get_order_item_pickup_location_name( $shipping_item ),
			'pickup_location_phone'   => $order_items->get_order_item_pickup_location_phone( $shipping_item ),
		];
	}


	/**
	 * Sets the shipping item object associated with a new appointment object.
	 *
	 * {@see \WC_Order_Item_Shipping::add_meta_data()} will be used to save Appointment metadata if a shipping item instance is set.
	 *
	 * @since 2.7.0
	 *
	 * @param \WC_Order_Item_Shipping $order_shipping_item the shipping item object
	 */
	public function set_order_shipping_item( \WC_Order_Item_Shipping $order_shipping_item ) {

		$this->order_shipping_item = $order_shipping_item;

		$this->id = $order_shipping_item->get_id();
	}


	/**
	 * Gets the ID of the shipping item associated with this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @return int
	 */
	public function get_order_shipping_item_id() {

		return $this->get_id();
	}


	/**
	 * Sets the start date for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param \DateTime|string|int $date the start date
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function set_start( $date ) {

		$this->set_date_prop( 'start', $date );
	}


	/**
	 * Sets a date prop whilst handling formatting and datetime objects.
	 *
	 * @see \WC_Data::set_date_prop()
	 *
	 * @since 2.7.0
	 *
	 * @param string $prop name of prop to set
	 * @param \DateTime|string|int $value value of the prop
	 * @throws Framework\SV_WC_Plugin_Exception if the start date set is invalid
	 */
	protected function set_date_prop( $prop, $value ) {

		// will force re-evaluating the appointment type based on the dates set
		$this->duration_type = null;

		/** {@see \WC_Data::set_date_prop()} supports {@see \WC_DateTime}, string or timestamp */
		if ( $value instanceof \DateTime && ! $value instanceof \WC_DateTime ) {
			$value = $value->getTimestamp();
		}

		parent::set_date_prop( $prop, $value );

		// an appointment must have a start date
		if ( 'start' === $prop && ! $this->get_start() instanceof \DateTime ) {
			throw new Framework\SV_WC_Plugin_Exception( 'Invalid appointment start time.' );
		}

		// maybe set a new type after date prop change
		$this->duration_type = $this->get_duration_type();
	}


	/**
	 * Gets the start date for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return \DateTime
	 */
	public function get_start( $context = 'view' ) {

		$start = $this->get_prop( 'start', $context );

		// set start date timezone to the timezone for the pickup location address
		if ( $start instanceof \DateTime ) {
			$start->setTimezone( $this->get_pickup_location_address()->get_timezone() );
		}

		return $start;
	}


	/**
	 * Sets the end date for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param \DateTime|string|int $date the end date
	 */
	public function set_end( $date ) {

		// an exception is not really thrown for end times, only start times
		try {
			$this->set_date_prop( 'end', $date );
		} catch ( \Exception $e ) {}
	}


	/**
	 * Gets the end date for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return \DateTime
	 */
	public function get_end( $context = 'view' ) {

		// the end date for appointments with lead time is the end of business hours for the selected date
		if ( $this->is_duration_type( self::DURATION_TYPE_ANYTIME_WITH_LEAD_TIME ) ) {

			$start = $this->get_start();
			$end   = null;

			$order_items     = wc_local_pickup_plus()->get_orders_instance()->get_order_items_instance();
			$pickup_location = $order_items->get_order_item_pickup_location( $this->get_order_shipping_item_id() );

			if ( $pickup_location && $business_hours = $pickup_location->get_business_hours() ) {
				$schedule = $business_hours->get_schedule();
			} else {
				$schedule = wc_local_pickup_plus_shipping_method()->get_default_business_hours();
			}

			$day_of_the_week = (int) $start->format( 'w' );

			if ( ! empty( $schedule[ $day_of_the_week ] ) ) {

				$offset = (int) array_pop( $schedule[ $day_of_the_week ] );

				$end = ( clone $start )->setTime( 0, 0, 0 );
				$end = $end->setTimestamp( $end->getTimestamp() + $offset );

			} else {

				$end = ( clone $start )->setTime( 0, 0, 0 );
				$end = $end->setTimestamp( $end->getTimestamp() + DAY_IN_SECONDS );
			}

		} else {

			$end = $this->get_prop( 'end', $context );
		}

		// set end date timezone to the timezone for the pickup location address
		if ( $end instanceof \DateTime ) {
			$end->setTimezone( $this->get_pickup_location_address()->get_timezone() );
		}

		return $end;
	}


	/**
	 * Sets the ID of the pickup location for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param int $id the ID of the pickup location
	 */
	public function set_pickup_location_id( $id ) {

		$this->set_prop( 'pickup_location_id', absint( $id ) );
	}


	/**
	 * Gets the ID of the pickup location for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return int
	 */
	public function get_pickup_location_id( $context = 'view' ) {

		return (int) $this->get_prop( 'pickup_location_id', $context );
	}


	/**
	 * Sets the address of the pickup location for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param array|\WC_Local_Pickup_Plus_Address $address pickup location address object or address data
	 */
	public function set_pickup_location_address( $address ) {

		// will ensure the type to be an address object
		if ( ! $address instanceof \WC_Local_Pickup_Plus_Address ) {
			$address = new \WC_Local_Pickup_Plus_Address( is_array( $address ) ? $address : [] );
		}

		$this->set_prop( 'pickup_location_address', $address );
	}


	/**
	 * Gets the address of the pickup location for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return \WC_Local_Pickup_Plus_Address
	 */
	public function get_pickup_location_address( $context = 'view' ) {

		$address = $this->get_prop( 'pickup_location_address', $context );

		// return empty address object if one hasn't been defined
		if ( ! $address instanceof \WC_Local_Pickup_Plus_Address ) {
			$address = new \WC_Local_Pickup_Plus_Address( [] );
		}

		return $address;
	}


	/**
	 * Sets the name of the pickup location for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param string $name the name of the pickup location
	 */
	public function set_pickup_location_name( $name ) {

		$this->set_prop( 'pickup_location_name', is_string( $name ) ? $name : '' );
	}


	/**
	 * Gets the name of the pickup location for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_pickup_location_name( $context = 'view' ) {

		$name = $this->get_prop( 'pickup_location_name', $context );

		return is_string( $name ) ? $name : '';
	}


	/**
	 * Sets the phone number of the pickup location for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param string $phone the phone number for the pickup location
	 */
	public function set_pickup_location_phone( $phone ) {

		$this->set_prop( 'pickup_location_phone', is_string( $phone ) ? $phone : '' );
	}


	/**
	 * Gets the phone number of the pickup location for this appointment.
	 *
	 * @since 2.7.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_pickup_location_phone( $context = 'view' ) {

		$phone = $this->get_prop( 'pickup_location_phone', $context );

		return is_string( $phone ) ? $phone : '';
	}


	/**
	 * Gets the type of this appointment.
	 *
	 * The appointment type is calculated on demand based on the values for the start and end props.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	private function get_duration_type() {

		if ( null === $this->duration_type ) {

			/** note: {@see Appointment::get_end()} returns a calculated date for appointments with lead time, so we need to use se {@see Appointment::get_prop()} to get the raw value */
			$start = $this->get_prop( 'start' );
			$end   = $this->get_prop( 'end' );

			// if the end date is define, then it's a whole day appointment or an appointment with an explicit pickup time
			if ( $end instanceof \DateTime ) {

				if ( DAY_IN_SECONDS === $end->getTimestamp() - $start->getTimestamp() ) {
					$this->duration_type = self::DURATION_TYPE_ANYTIME;
				} else {
					$this->duration_type = self::DURATION_TYPE_PICKUP_TIME;
				}

			// if the end type is not defined, then it's an anytime appointment with lead time
			} else {

				$this->duration_type = self::DURATION_TYPE_ANYTIME_WITH_LEAD_TIME;
			}
		}

		return $this->duration_type;
	}


	/**
	 * Determines whether the appointment is of a specified type.
	 *
	 * @since 2.7.0
	 *
	 * @param string $duration appointment duration type
	 * @return bool
	 */
	private function is_duration_type( $duration ) {

		return $duration === $this->get_duration_type();
	}


	/**
	 * Determines whether this appointment is an anytime appointment.
	 *
	 * Appointments of this type expect the customer to be able to pickup items at any moment between the start date and the end of the business hours for that day.
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function is_anytime() {

		return $this->is_duration_type( self::DURATION_TYPE_ANYTIME );
	}


	/**
	 * Determines whether this appointment has a pickup time.
	 *
	 * Appointments of this type expect customers to pick up their items at the time slot (start - end time) chosen at checkout.
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function has_pickup_time() {

		return $this->is_duration_type( self::DURATION_TYPE_PICKUP_TIME );
	}


	/**
	 * Saves the appointment properties as meta data for the associated shipping item.
	 *
	 * If an instance of a {@see \WC_Order_Item_Shipping} is set, metadata will
	 * be saved using {@see \WC_Order_Item_Shipping::add_meta_data()} but
	 * {@see \WC_Order_Item_Shipping::save()} won't be called, because we expect
	 * that shipping item to be added to an order later during the request.
	 *
	 * @see \WC_Data::delete()
	 *
	 * @since 2.7.0
	 *
	 * @return int the ID of the associated shipping item if one is set or zero if the shipping item hasn't been added to the database yet
	 */
	public function save() {

		if ( $this->order_shipping_item instanceof \WC_Order_Item_Shipping ) {
			$order_item = $this->order_shipping_item;
		} else {
			$order_item = $this->get_order_shipping_item_id();
		}

		// get_end() returns a calculated date for appointments with lead time, so we use get_prop() to get the value that should be stored in the shipping item metadata
		$start = $this->get_prop( 'start' );
		$end   = $this->get_prop( 'end' );

		$this->set_order_item_meta_data( $order_item, '_pickup_appointment_start', $start ? $start->getTimestamp() : null );
		$this->set_order_item_meta_data( $order_item, '_pickup_appointment_end', $end ? $end->getTimestamp() : null );
		$this->set_order_item_meta_data( $order_item, '_pickup_location_id', $this->get_pickup_location_id() );
		$this->set_order_item_meta_data( $order_item, '_pickup_location_address', $this->get_pickup_location_address()->get_array() );
		$this->set_order_item_meta_data( $order_item, '_pickup_location_name', $this->get_pickup_location_name() );
		$this->set_order_item_meta_data( $order_item, '_pickup_location_phone', $this->get_pickup_location_phone() );

		return $this->get_id();
	}


	/**
	 * Sets an order item's meta data.
	 *
	 * @since 2.7.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the shipping item object or the ID of a shipping item
	 * @param string $meta_key the name of the meta data
	 * @param null|array|string|int $meta_value the value to add or update
	 * @return bool success
	 */
	private function set_order_item_meta_data( $order_item, $meta_key, $meta_value ) {

		$success = true;

		if ( $order_item instanceof \WC_Order_Item_Shipping ) {

			try {
				$order_item->add_meta_data( $meta_key, $meta_value, true );
			} catch ( \Exception $e ) {
				$success = false;
			}

		} else {

			try {
				$success = wc_update_order_item_meta( (int) $order_item, $meta_key, $meta_value );
			} catch ( \Exception $e ) {
				$success = false;
			}
		}

		return $success;
	}


	/**
	 * Deletes appointment properties from the associated shipping item meta data.
	 *
	 * @see \WC_Data::delete()
	 *
	 * @param bool $force_delete (not used)
	 * @return bool whether all metadata was successfully deleted or not
	 */
	public function delete( $force_delete = false ) {

		$success = true;

		try {

			$shipping_item_id = $this->get_order_shipping_item_id();

			$success = wc_delete_order_item_meta( $shipping_item_id, '_pickup_appointment_start' ) && $success;
			$success = wc_delete_order_item_meta( $shipping_item_id, '_pickup_appointment_end' )   && $success;
			$success = wc_delete_order_item_meta( $shipping_item_id, '_pickup_location_id' )       && $success;
			$success = wc_delete_order_item_meta( $shipping_item_id, '_pickup_location_address' )  && $success;
			$success = wc_delete_order_item_meta( $shipping_item_id, '_pickup_location_name' )     && $success;
			$success = wc_delete_order_item_meta( $shipping_item_id, '_pickup_location_phone' )    && $success;

		} catch ( \Exception $e ) {

			$success = false;
		}

		if ( $success ) {
			$this->set_id( 0 );
		}

		return $success;
	}


}
