<?php

namespace ACP\Editing\Service\User;

use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;
use ACP\Editing\View\Text;

class Nicename extends Basic {

	public function __construct( $placeholder ) {
		parent::__construct(
			( new Text() )->set_placeholder( (string) $placeholder ),
			new Storage\User\Field( 'user_nicename' )
		);
	}

}