<?php

namespace ACA\Types\Editing\Service;

use ACP;
use ACP\Editing\View;

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

	public function get_paginated_options( $search, $page, $id = null ) {
		return new ACP\Helper\Select\Paginated\Posts( $search, $page, [
			'post_type' => $this->related_post_type,
		] );
	}

}