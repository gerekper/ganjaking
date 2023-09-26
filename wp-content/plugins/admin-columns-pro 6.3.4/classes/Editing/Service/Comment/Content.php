<?php

namespace ACP\Editing\Service\Comment;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Content extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Comment\Field( 'comment_content' ) );
	}

	public function get_view( string $context ): ?View {
		return ( new View\TextArea() )->set_clear_button( true );
	}

}