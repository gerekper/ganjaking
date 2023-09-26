<?php

namespace ACA\YoastSeo\Editing\Service\User;

use AC;
use ACP;
use ACP\Editing\View;

class ToggleOn extends ACP\Editing\Service\BasicStorage {

	public function __construct( $meta_key ) {
		parent::__construct( new ACP\Editing\Storage\User\Meta( $meta_key ) );
	}

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Toggle(
			new AC\Type\ToggleOptions(
				new AC\Helper\Select\Option( '' ),
				new AC\Helper\Select\Option( 'on' )
			)
		);
	}

}