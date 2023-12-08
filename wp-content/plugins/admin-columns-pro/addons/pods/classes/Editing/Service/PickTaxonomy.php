<?php

namespace ACA\Pods\Editing\Service;

use AC\Helper\Select\Options\Paginated;
use ACP;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Storage;
use ACP\Editing\View;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;

class PickTaxonomy implements ACP\Editing\Service, PaginatedOptions {

	/**
	 * @var boolean
	 */
	private $multiple;

	/**
	 * @var string
	 */
	private $taxonomy;

	/**
	 * @var string
	 */
	private $storage;

	public function __construct( Storage $storage, $multiple, $taxonomy ) {
		$this->storage = $storage;
		$this->multiple = (bool) $multiple;
		$this->taxonomy = $taxonomy;
	}

	public function get_view( string $context ): ?View {
		return ( new ACP\Editing\View\AjaxSelect() )
			->set_multiple( $this->multiple )
			->set_clear_button( true );
	}

	public function get_value( $id ) {
		$term_ids = $this->storage->get( $id );

		if ( empty( $term_ids ) ) {
			return [];
		}

		$value = [];
		foreach ( $term_ids as $term_id ) {
			$term = get_term_by( 'id', $term_id, $this->taxonomy );

			$value[ $term_id ] = $term ? htmlspecialchars_decode( $term->name ) : $term_id;
		}

		return $value;
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, $data );
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'search'   => $search,
			'page'     => $page,
			'taxonomy' => $this->taxonomy,
		] );
	}

}