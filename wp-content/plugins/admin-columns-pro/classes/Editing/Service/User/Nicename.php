<?php

namespace ACP\Editing\Service\User;

use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;
use ACP\Editing\View\Text;

class Nicename extends Basic {

	public function __construct( Text $view ) {
		parent::__construct( $view, new Storage\User\Field( 'user_nicename' ) );
	}

}