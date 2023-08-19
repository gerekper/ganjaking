<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class FeaturedImage extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\FeaturedImage() );
	}

	public function get_view( string $context ): ?View {
		return ( new View\Image() )->set_clear_button( true );
	}

}