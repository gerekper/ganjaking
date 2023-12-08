<?php

namespace ACA\Types\Editing\Service;

use AC\Helper\Select\Options\Paginated;
use ACP;
use ACP\Editing\View;
use ACP\Helper\Select\Post\PaginatedFactory;

class Relationship extends ACP\Editing\Service\BasicStorage
	implements ACP\Editing\PaginatedOptions {

	/**
	 * @var string
	 */
	protected $related_post_type;

	public function __construct( ACP\Editing\Storage $storage, $related_post_type ) {
		$this->related_post_type = $related_post_type;

		parent::__construct( $storage );
	}

	public function get_view( string $context ): ?View {
		return ( new ACP\Editing\View\AjaxSelect() )->set_multiple( true )->set_clear_button( true );
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'paged'     => $page,
			's'         => $search,
			'post_type' => $this->related_post_type,
		] );
	}

}