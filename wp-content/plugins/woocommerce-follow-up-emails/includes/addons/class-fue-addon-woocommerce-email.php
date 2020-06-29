<?php

class FUE_Addon_WooCommerce_Email extends WC_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id = 'fue_email';

		// Call parent constuctor
		parent::__construct();
	}

}