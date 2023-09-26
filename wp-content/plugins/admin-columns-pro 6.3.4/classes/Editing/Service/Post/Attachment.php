<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Attachment extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Attachments() );
	}

	public function get_view( string $context ): ?View {
		return ( new View\Media() )->set_multiple( true );
	}

}