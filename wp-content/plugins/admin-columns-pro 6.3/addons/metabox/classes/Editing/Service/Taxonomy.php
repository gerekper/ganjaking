<?php

namespace ACA\MetaBox\Editing\Service;

use ACA;
use ACP;
use ACP\Editing\View;

class Taxonomy implements ACP\Editing\Service, ACP\Editing\PaginatedOptions {

	/**
	 * @var string|array
	 */
	protected $taxonomy;

	/**
	 * @var ACP\Editing\Storage
	 */
	private $storage;

	public function __construct( ACP\Editing\Storage $storage, $taxonomy ) {
		$this->storage = $storage;
		$this->taxonomy = $taxonomy;
	}

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\AjaxSelect();
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, $data );
	}

	public function get_value( $id ) {
		$value = $this->storage->get( $id );

		return $value
			? [ $value => ac_helper()->taxonomy->get_term_display_name( get_term( $value ) ) ]
			: false;
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
		return new ACP\Helper\Select\Paginated\Terms( $s, $paged, $this->taxonomy );
	}

}