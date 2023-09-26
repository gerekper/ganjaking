<?php

namespace ACA\WC\Column\ProductVariation;

use ACP;

/**
 * @since 3.0
 */
class ID extends ACP\Column\Post\ID {

	public function __construct() {
		parent::__construct();

		$this->set_label( null )
		     ->set_type( 'variation_id' )
		     ->set_original( true );
	}

	public function get_raw_value( $id ) {
		return $id;
	}

}