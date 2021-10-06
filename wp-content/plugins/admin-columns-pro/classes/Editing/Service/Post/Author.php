<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View\AjaxSelect;

class Author extends Service\User {

	public function __construct() {
		parent::__construct(
			new AjaxSelect(),
			new Storage\Post\Field( 'post_author' )
		);
	}

	public function get_value( $id ) {
		if ( current_user_can( 'author' ) ) {
			return null;
		}

		return parent::get_value( $id );
	}

}