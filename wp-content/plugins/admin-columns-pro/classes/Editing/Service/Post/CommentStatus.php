<?php

namespace ACP\Editing\Service\Post;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class CommentStatus extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'comment_status' ) );
	}

	public function get_view( string $context ): ?View {
		return new View\Toggle(
			new ToggleOptions(
				new Option( 'closed', __( 'Closed', 'codepress-admin-columns' ) ),
				new Option( 'open', __( 'Open', 'codepress-admin-columns' ) )
			)
		);
	}

}