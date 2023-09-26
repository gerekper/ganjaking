<?php

namespace ACP\Editing\Service\User;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class FullName extends BasicStorage {

	public function __construct( ) {
		parent::__construct( new Storage\User\FullName() );
	}

	public function get_view( string $context ): ?View {
		return new View\FullName();
	}

}