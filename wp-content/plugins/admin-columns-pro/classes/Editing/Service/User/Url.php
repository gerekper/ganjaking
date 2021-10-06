<?php

namespace ACP\Editing\Service\User;

use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Url extends Basic {

	public function __construct( $placeholder ) {
		parent::__construct(
			( new View\Url() )->set_placeholder( $placeholder ),
			new Storage\User\Field( 'user_url' )
		);
	}

}