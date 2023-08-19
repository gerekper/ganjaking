<?php

namespace ACA\WC\Editing\Product;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACP;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class ReviewsEnabled extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'comment_status' ) );
	}

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Toggle(
			new ToggleOptions(
				new Option( 'closed' ), new Option( 'open' )
			)
		);
	}
}