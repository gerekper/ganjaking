<?php

namespace ACP\Editing\Service\Post;

use AC;
use ACP\Editing\RemoteOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper\Select;

class PostType extends BasicStorage implements RemoteOptions {

	public function __construct() {
		parent::__construct( new Storage\Post\PostType() );
	}

	public function get_view( string $context ): ?View {
		return new View\RemoteSelect();
	}

	public function get_remote_options( $id = null ) {
		$options = new Select\Group\PostTypeType(
			new Select\Formatter\PostTypeLabel( new Select\Entities\PostType() )
		);

		return new AC\Helper\Select\Options( $options->get_copy() );
	}

}