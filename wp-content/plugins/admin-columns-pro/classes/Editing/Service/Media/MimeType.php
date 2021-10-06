<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View\Select;

class MimeType extends Service\Basic {

	public function __construct() {
		parent::__construct(
			new Select( array_combine( wp_get_mime_types(), wp_get_mime_types() ) ),
			new Storage\Post\Field( 'post_mime_type' )
		);
	}

}