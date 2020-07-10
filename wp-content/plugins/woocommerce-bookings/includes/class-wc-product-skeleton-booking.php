<?php

/**
 * Dummy class for our 'addon' classes to load from.
 */
class WC_Product_Skeleton_Booking extends WC_Product_Booking {

	public function __construct( $product ) {
		parent::__construct( $product );
	}

	public function is_purchasable() {
		return false;
	}

	public function is_skeleton() {
		return true;
	}

	public function is_bookings_addon() {
		return true;
	}

}
