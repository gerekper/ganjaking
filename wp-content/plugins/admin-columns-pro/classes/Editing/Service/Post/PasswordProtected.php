<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage\Post\Field;
use ACP\Editing\View;

class PasswordProtected extends BasicStorage {

	public function __construct() {
		parent::__construct( new Field( 'post_password' ) );
	}

	public function get_view( string $context ): ?View {
		return new View\Text();
	}

}