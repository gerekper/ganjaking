<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Excerpt extends Basic {

	public function __construct() {
		parent::__construct(
			( new View\TextArea() )->set_placeholder( __( 'Excerpt automatically generated from content.', 'codepress-admin-columns' ) ),
			new Storage\Post\Field( 'post_excerpt' )
		);
	}

}