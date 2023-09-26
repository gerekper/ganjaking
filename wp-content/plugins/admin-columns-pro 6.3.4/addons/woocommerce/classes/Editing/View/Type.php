<?php

namespace ACA\WC\Editing\View;

use ACP;

class Type extends ACP\Editing\View {

	public function __construct( array $simple_types ) {
		parent::__construct( 'wc_product_type' );

		$this->set( 'options', wc_get_product_types() );
		$this->set( 'simple_types', $simple_types );
	}

}
