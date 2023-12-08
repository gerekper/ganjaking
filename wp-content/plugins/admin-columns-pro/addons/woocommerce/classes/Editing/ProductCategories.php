<?php

namespace ACA\WC\Editing;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;
use InvalidArgumentException;

class ProductCategories implements Service, PaginatedOptions {

	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function get_view( string $context ): ?View {
		$view = ( new View\AjaxSelect() )
			->set_multiple( true )
			->set_clear_button( true );

		if ( $context === self::CONTEXT_BULK ) {
			$view->has_methods( true )
			     ->set_revisioning( false );
		}

		return $view;
	}

	public function get_value( $id ) {
		$values = [];

		foreach ( $this->storage->get( $id ) as $term_id ) {
			$term = get_term( $term_id, 'product_cat' );

			if ( $term ) {
				$values[ $term->term_id ] = htmlspecialchars_decode( $term->name ?: $term->term_id );
			}
		}

		return $values;
	}

	/**
	 * @param array $term_ids
	 *
	 * @return int[]
	 */
	private function sanitize_ids( $term_ids ): array {
		return $term_ids
			? array_map( 'intval', array_filter( $term_ids, 'is_numeric' ) )
			: [];
	}

	private function get_term_ids( $id ) {
		$ids = $this->storage->get( $id );

		return $ids && is_array( $ids )
			? $ids
			: [];
	}

	public function update( int $id, $data ): void {
		$method = $data['method'] ?? null;

		if ( ! $method ) {
			$this->storage->update( $id, $this->sanitize_ids( $data ) );

			return;
		}

		$term_ids = $data['value'] ?? [];

		if ( ! is_array( $term_ids ) ) {
			throw new InvalidArgumentException( 'Invalid value' );
		}

		$term_ids = $this->sanitize_ids( $term_ids );

		switch ( $method ) {
			case 'add':
				$this->storage->update( $id, array_merge( $this->get_term_ids( $id ), $term_ids ) );

				break;
			case 'remove':
				$this->storage->update( $id, array_diff( $this->get_term_ids( $id ), $term_ids ) );

				break;
			default:
				$this->storage->update( $id, $term_ids );
		}
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return ( new \ACP\Helper\Select\Taxonomy\PaginatedFactory() )->create( [
			'search'   => (string) $search,
			'page'     => (int) $page,
			'taxonomy' => 'product_cat',
		] );
	}

}