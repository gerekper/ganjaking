<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class MimeType extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'post_mime_type' ) );
	}

	public function get_view( string $context ): ?View {
		return new View\Select( array_combine( wp_get_mime_types(), wp_get_mime_types() ) );
	}

}