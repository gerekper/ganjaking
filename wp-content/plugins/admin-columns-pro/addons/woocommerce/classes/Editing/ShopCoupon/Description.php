<?php

namespace ACA\WC\Editing\ShopCoupon;

use ACP;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Description extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'post_excerpt' ) );
	}

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\TextArea();
	}

}