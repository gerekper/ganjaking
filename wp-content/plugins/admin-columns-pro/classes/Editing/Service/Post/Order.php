<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Order extends Service\BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'menu_order' ) );
	}

	public function get_view( $context ) {
		return new View\Number();
	}

}