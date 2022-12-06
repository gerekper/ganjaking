<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage\Post\Format;
use ACP\Editing\View;

class Formats extends BasicStorage {

	public function __construct() {
		parent::__construct( new Format() );
	}

	public function get_view( string $context ): ?View {
		return new View\Select( get_post_format_strings() );
	}

}