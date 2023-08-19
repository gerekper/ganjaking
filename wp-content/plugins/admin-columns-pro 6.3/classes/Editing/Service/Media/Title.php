<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Title extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'post_title' ) );
	}

	public function get_view( string $context ): ?View {
		return ( new View\Text() )->set_js_selector( 'strong > a' );
	}

}