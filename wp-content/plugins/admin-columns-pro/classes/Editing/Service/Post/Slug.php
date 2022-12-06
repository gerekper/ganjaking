<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Slug extends BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'post_name' ) );
	}

	public function get_value( int $id ) {
		return urldecode( parent::get_value( $id ) );
	}

	public function get_view( string $context ): ?View {
		if ( $context === self::CONTEXT_BULK ) {
			return null;
		}

		return ( new View\Text() )->set_placeholder( __( 'Enter slug', 'codepress-admin-columns' ) );
	}

}