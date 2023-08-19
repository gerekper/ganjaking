<?php

namespace ACA\MetaBox\Editing\Service;

use ACA;
use ACP;
use ACP\Editing\View;
use ACP\Editing\View\AjaxSelect;

class Taxonomies implements ACP\Editing\Service, ACP\Editing\PaginatedOptions {

	/**
	 * @var ACP\Service\Storage
	 */
	private $storage;

	/**
	 * @var string|array
	 */
	private $taxonomy;

	public function __construct( ACP\Editing\Storage $storage, $taxonomy ) {
		$this->storage = $storage;
		$this->taxonomy = $taxonomy;
	}

	public function get_view( string $context ): ?View {
		return ( new AjaxSelect() )->set_multiple( true )->set_clear_button( true );
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, $data );
	}

	public function get_value( $id ) {
		return $this->storage->get( $id );
	}

	public function get_paginated_options( $search, $page, $id = null ) {
		return new ACP\Helper\Select\Paginated\Terms( $search, $page, (array) $this->taxonomy );
	}

}