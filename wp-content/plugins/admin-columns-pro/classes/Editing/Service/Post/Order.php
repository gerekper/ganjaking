<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Order extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'menu_order' ) );
	}

	public function get_view( string $context ): ?View {
		return new View\Number();
	}

}