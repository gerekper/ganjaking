<?php

namespace ACP\Editing\Service\Comment;

use AC;
use ACP;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper\Select\Entities;

class CommentParent extends Service\BasicStorage implements PaginatedOptions {

	public function __construct() {
		parent::__construct( new Storage\Comment\Field( 'comment_parent' ) );
	}

	public function get_view( string $context ): ?View {
		$view = new View\AjaxSelect( );
		$view->set_multiple( false );

		return $view;
	}

	public function get_paginated_options( $search, $paged, $id = null ) {
		$entities = new Entities\Comment( compact( 'search', 'paged' ) );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Formatter\CommentSummary( $entities )
		);
	}

}