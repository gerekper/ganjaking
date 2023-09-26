<?php

namespace ACP\Editing\Service\Comment;

use ACP\Editing;

class User extends Editing\Service\User {

	public function __construct() {
		parent::__construct(
			new Editing\View\AjaxSelect(),
			new Editing\Storage\Comment\Field( 'user_id' )
		);
	}

}