<?php

namespace ACA\MetaBox\Editing\Service;

use AC\Helper\Select\Options\Paginated;
use ACA;
use ACP;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\View;
use ACP\Helper\Select\Taxonomy\PaginatedFactory;
use WP_Term;

class TaxonomiesAdvanced implements ACP\Editing\Service, PaginatedOptions {

	/**
	 * @var ACP\Editing\Storage
	 */
	private $storage;

	/**
	 * @var string|array
	 */
	protected $taxonomy;

	public function __construct( ACP\Editing\Storage $storage, $taxonomy ) {
		$this->storage = $storage;
		$this->taxonomy = $taxonomy;
	}

	public function get_view( string $context ): ?View {
		return ( new ACP\Editing\View\AjaxSelect() )->set_clear_button( true )->set_multiple( true );
	}

	public function get_value( $id ) {
		$value = $this->storage->get( $id );

		if ( empty( $value ) ) {
			return false;
		}

		$value = is_array( $value ) ? $value[0] : $value;
		$result = [];

		foreach ( explode( ',', $value ) as $term_id ) {
			$term = get_term( $term_id );
			if ( $term instanceof WP_Term ) {
				$result[ $term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, implode( ',', $data ?? [] ) );
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new PaginatedFactory() )->create( [
			'search'   => $search,
			'page'     => $page,
			'taxonomy' => $this->taxonomy,
		] );
	}

}