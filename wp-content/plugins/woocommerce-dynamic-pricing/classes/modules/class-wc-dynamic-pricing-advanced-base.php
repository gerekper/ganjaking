<?php

abstract class WC_Dynamic_Pricing_Advanced_Base extends WC_Dynamic_Pricing_Module_Base {

	public function __construct( $module_id ) {
		parent::__construct( $module_id, 'advanced' );
	}

	public function adjust_cart( $cart ) {
		
	}

}