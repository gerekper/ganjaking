<?php

namespace ACA\EC\Editing\Service\Event;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACP;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Sticky extends ACP\Editing\Service\BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'menu_order' ) );
	}

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Toggle(
			new ToggleOptions(
				new Option( '0' ),
				new Option( '-1' )
			)
		);
	}

}