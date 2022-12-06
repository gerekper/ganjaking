<?php

namespace ACP\Editing\Service\Post;

use AC\Type\ToggleOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Editing\View\Toggle;

class Sticky extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Sticky() );
	}

	public function get_view( string $context ): ?View {
		return new Toggle( ToggleOptions::create_from_array( [ 'no' => __( 'Not sticky', 'codepress-admin-columns' ), 'yes' => __( 'Sticky', 'codepress-admin-columns' ) ] ) );
	}

}