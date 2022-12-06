<?php

namespace ACA\BbPress\ListScreen;

use ACA\BbPress\Column;
use ACP;

class Reply extends ACP\ListScreen\Post {

	public function __construct() {
		parent::__construct( 'reply' );

		$this->set_group( 'bbpress' );
	}

}