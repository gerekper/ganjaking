<?php

namespace ACA\BP\Filtering\Profile;

use ACA\BP\Filtering;
use WP_User_Query;

class Number extends Filtering\Profile {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
		$this->set_ranged( true );
	}

	/**
	 * @param WP_User_Query $query
	 */
	public function filter_by_callback( $query ) {
		$this->filter_by_ranged( $query );
	}

}