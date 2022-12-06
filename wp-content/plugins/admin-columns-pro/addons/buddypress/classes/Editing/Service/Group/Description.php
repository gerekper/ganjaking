<?php

namespace ACA\BP\Editing\Service\Group;

use ACA\BP\Editing\Storage\Group;
use ACP;
use ACP\Editing\View;

class Description extends ACP\Editing\Service\BasicStorage {

	public function __construct() {
		parent::__construct( new Group( 'description' ) );
	}

	public function get_view( string $context ): ?View {
		return ( new ACP\Editing\View\TextArea() )->set_clear_button( true );
	}

}