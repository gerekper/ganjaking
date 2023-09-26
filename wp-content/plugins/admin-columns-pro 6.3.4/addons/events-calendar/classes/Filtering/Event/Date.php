<?php

namespace ACA\EC\Filtering\Event;

use ACP;

class Date extends ACP\Filtering\Model\MetaDate {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_date_format( 'Y-m-d H:i:s' );
	}

	public function get_filtering_data() {
		$data = parent::get_filtering_data();
		$data['empty_option'] = false;

		return $data;
	}

}