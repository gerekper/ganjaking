<?php

namespace ACA\BbPress\ListScreen;

use ACA\BbPress\Column;
use ACP;

class Topic extends ACP\ListScreen\Post {

	public function __construct() {
		parent::__construct( 'topic' );

		$this->set_group( 'bbpress' );
	}

	protected function register_column_types() {
		parent::register_column_types();

		$this->register_column_type( new Column\Topic\Forum() );
	}

}