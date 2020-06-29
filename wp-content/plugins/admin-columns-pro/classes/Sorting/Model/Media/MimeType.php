<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\Model\Post;

class MimeType extends Post\PostField {

	public function __construct() {
		parent::__construct( 'post_mime_type' );
	}

}