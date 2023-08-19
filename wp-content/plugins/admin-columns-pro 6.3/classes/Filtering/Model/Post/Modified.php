<?php

namespace ACP\Filtering\Model\Post;

class Modified extends Date {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_date_field( 'post_modified' );
	}

}