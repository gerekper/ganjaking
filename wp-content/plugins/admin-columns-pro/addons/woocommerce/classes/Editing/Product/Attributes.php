<?php

namespace ACA\WC\Editing\Product;

use ACP;
use ACP\Editing\View;

class Attributes extends ACP\Editing\Service\BasicStorage {

	public function __construct( ACP\Editing\Storage $storage ) {
		parent::__construct( $storage );
	}

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\MultiInput();
	}

}