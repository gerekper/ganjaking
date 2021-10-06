<?php

/**
 * Booking Addons Screen.
 */
class WC_Bookings_Admin_Add_Ons {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_addons_sections', array( $this, 'add_section' ) );
	}

	/**
	 * Adds a new section for "bookings" add-ons
	 */
	public function add_section( $sections ) {
		$sections['bookings'] = new stdClass;
		$sections['bookings']->slug = wc_clean( __( 'bookings-extensions', 'woocommerce-bookings' ) );
		$sections['bookings']->label = wc_clean( __( 'Bookings Addons', 'woocommerce-bookings' ) );
		return $sections;
	}
}

