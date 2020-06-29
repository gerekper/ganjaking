<?php

class WC_Dynamic_Pricing_Collector {

	protected $_collector_data;
	public $type;

	public function __construct( $collector_data ) {
		$this->_collector_data = $collector_data;
		$this->type            = $collector_data['type'];
	}



}