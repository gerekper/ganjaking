<?php

namespace ACA\BbPress\ListScreen;

use ACA\BbPress\Column;
use ACP;

class Forum extends ACP\ListScreen\Post {

	public function __construct() {
		parent::__construct( 'forum' );

		$this->set_group( 'bbpress' );
	}

}