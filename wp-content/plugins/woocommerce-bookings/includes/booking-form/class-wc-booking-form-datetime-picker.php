<?php
/**
 * Class dependencies
 */

/**
 * Date and time Picker class
 */
class WC_Booking_Form_Datetime_Picker extends WC_Booking_Form_Date_Picker {

	private $field_type = 'datetime-picker';
	private $field_name = 'start_date';

	/**
	 * Constructor
	 * @param WC_Booking_Form $booking_form The booking form which called this picker
	 */
	public function __construct( $booking_form ) {
		$this->booking_form                    = $booking_form;
		$this->args                            = array();
		$this->args['type']                    = $this->field_type;
		$this->args['name']                    = $this->field_name;
		$this->args['min_date']                = $this->booking_form->product->get_min_date();
		$this->args['max_date']                = $this->booking_form->product->get_max_date();
		$this->args['default_availability']    = $this->booking_form->product->get_default_availability();
		$this->args['first_block_time']        = $this->booking_form->product->get_first_block_time();
		$this->args['min_date_js']             = $this->get_min_date();
		$this->args['max_date_js']             = $this->get_max_date();
		$this->args['duration_type']           = $this->booking_form->product->get_duration_type();
		$this->args['is_range_picker_enabled'] = false; // Datetime has no end date field
		$this->args['interval']                = $this->booking_form->product->get_duration();
		$this->args['display']                 = $this->booking_form->product->get_calendar_display_mode();
		$this->args['label']                   = $this->get_field_label( __( 'Date', 'woocommerce-bookings' ) );

		if ( 'hour' === $this->booking_form->product->get_duration_unit() ) {
			$this->args['interval'] = $this->args['interval'] * 60;
		}

		$this->args['default_date'] = date( 'Y-m-d', $this->get_default_date() );
	}

	public static function set_duration_to_day() {
		return 'day';
	}
}
