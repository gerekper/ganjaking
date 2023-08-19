<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Excerpt extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'post_excerpt' ) );
	}

	public function get_view( string $context ): ?View {
		return ( new View\TextArea() )->set_placeholder( __( 'Excerpt automatically generated from content.', 'codepress-admin-columns' ) );
	}

}