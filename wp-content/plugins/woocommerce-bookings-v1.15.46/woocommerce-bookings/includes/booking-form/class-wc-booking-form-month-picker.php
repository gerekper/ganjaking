<?php
/**
 * Class dependencies
 */

/**
 * Month Picker class
 */
class WC_Booking_Form_Month_Picker extends WC_Booking_Form_Picker {

	private $field_type = 'month-picker';
	private $field_name = 'start_date';

	/**
	 * Constructor
	 * @param object $booking_form The booking form which called this picker
	 */
	public function __construct( $booking_form ) {
		$this->booking_form                    = $booking_form;
		$this->args                            = array();
		$this->args['type']                    = $this->field_type;
		$this->args['name']                    = $this->field_name;
		$this->args['min_date']                = $this->booking_form->product->get_min_date();
		$this->args['max_date']                = $this->booking_form->product->get_max_date();
		$this->args['default_availability']    = $this->booking_form->product->get_default_availability();
		$this->args['display']                 = $this->booking_form->product->get_calendar_display_mode();
		$this->args['is_range_picker_enabled'] = $this->booking_form->product->is_range_picker_enabled();
		$this->args['label']                   = $this->get_field_label( __( 'Month', 'woocommerce-bookings' ) );
		$this->args['blocks']                  = $this->get_booking_blocks();
		$this->args['availability_rules']      = array();
		$this->args['availability_rules'][0]   = $this->booking_form->product->get_availability_rules();

		if ( $this->booking_form->product->has_resources() ) {
			foreach ( $this->booking_form->product->get_resources() as $resource ) {
				$this->args['availability_rules'][ $resource->ID ] = $this->booking_form->product->get_availability_rules( $resource->ID );
			}
		}

		$fully_booked_blocks = $this->find_fully_booked_blocks();

		$this->args = array_merge( $this->args, $fully_booked_blocks );
	}

	/**
	 * Return the available blocks for this booking in array format
	 *
	 * @return array Array of blocks
	 */
	public function get_booking_blocks() {
		$min_date = $this->args['min_date'];
		$max_date = $this->args['max_date'];

		// Generate a range of blocks for months
		if ( $min_date ) {
			if ( 0 === $min_date['value'] ) {
				$min_date['value'] = 1;
			}
			$from = strtotime( date( 'Y-m-01', strtotime( "+{$min_date['value']} {$min_date['unit']}" ) ) );
		} else {
			$from = strtotime( date( 'Y-m-01', strtotime( '+28 days' ) ) );
		}
		$to = strtotime( date( 'Y-m-t', strtotime( "+{$max_date['value']} {$max_date['unit']}" ) ) );

		return $this->booking_form->product->get_blocks_in_range( $from, $to );
	}

	/**
	 * Finds months which are fully booked already so they can be blocked on the date picker
	 */
	protected function find_fully_booked_blocks() {
		$booked = WC_Bookings_Controller::find_booked_month_blocks( $this->booking_form->product->get_id() );

		return array(
			'fully_booked_months' => $booked['fully_booked_months'],
		);
	}
}

