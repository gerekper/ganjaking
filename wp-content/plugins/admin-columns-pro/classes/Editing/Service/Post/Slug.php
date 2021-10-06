<?php

namespace ACP\Editing\Service\Post;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class Slug extends Service\BasicStorage {

	public function __construct() {
		parent::__construct( new Storage\Post\Field( 'post_name' ) );
	}

	public function get_value( $id ) {
		return urldecode( parent::get_value( $id ) );
	}

	public function get_view( $context ) {
		return $context === self::CONTEXT_BULK
			? false
			: ( new View\Text() )->set_placeholder( __( 'Enter slug', 'codepress-admin-columns' ) );
	}

}