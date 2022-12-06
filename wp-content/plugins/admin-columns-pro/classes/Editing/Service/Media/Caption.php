<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Caption extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'post_excerpt' ) );
	}

	public function get_view( string $context ): ?View {
		return new View\TextArea();
	}

}