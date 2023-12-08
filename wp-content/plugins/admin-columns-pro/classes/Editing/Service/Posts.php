<?php

namespace ACP\Editing\Service;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\PaginatedOptionsFactory;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;
use InvalidArgumentException;

class Posts implements Service, PaginatedOptions {

	/**
	 * @var View\AjaxSelect
	 */
	protected $view;

	/**
	 * @var Storage
	 */
	protected $storage;

	/**
	 * @var PaginatedOptionsFactory
	 */
	protected $options_factory;

	public function __construct( View\AjaxSelect $view, Storage $storage, PaginatedOptionsFactory $options_factory = null ) {
		$this->view = $view;
		$this->storage = $storage;
		$this->options_factory = $options_factory ?: new PaginatedOptions\Posts();
	}

	public function get_view( string $context ): ?View {
		$view = $this->view->set_multiple( true );

		if ( $context === self::CONTEXT_BULK ) {
			$view->has_methods( true )->set_revisioning( false );
		}

		return $view;
	}

	private function get_post_title( int $id ) {
		return get_the_title( $id ) ?: sprintf( __( '#%d (no title)' ), $id );
	}

	public function get_value( int $id ) {
		$ids = $this->get_current_post_ids( $id );

		return $ids
			? array_map( [ $this, 'get_post_title' ], array_combine( $ids, $ids ) )
			: [];
	}

	/**
	 * @param int $id
	 *
	 * @return int[]
	 */
	private function get_current_post_ids( int $id ) {
		$ids = $this->storage->get( $id );

		return $ids && is_array( $ids )
			? array_map( 'intval', array_filter( $ids, 'is_numeric' ) )
			: [];
	}

	public function update( int $id, $data ): void {
		$method = $data['method'] ?? null;

		if ( null === $method ) {
			$this->storage->update( $id, $data && is_array( $data ) ? $this->sanitize_ids( $data ) : null );

			return;
		}

		$ids = $data['value'] ?? [];

		if ( ! is_array( $ids ) ) {
			throw new InvalidArgumentException( 'Invalid value' );
		}

		$ids = $this->sanitize_ids( $ids );

		switch ( $method ) {
			case 'add':
				if ( $ids ) {
					$this->storage->update( $id, array_merge( $this->get_current_post_ids( $id ), $ids ) ?: null );
				}
				break;
			case 'remove':
				if ( $ids ) {
					$this->storage->update( $id, array_diff( $this->get_current_post_ids( $id ), $ids ) ?: null );
				}
				break;
			default:
				$this->storage->update( $id, $ids ?: null );
		}
	}

	protected function sanitize_ids( array $ids ): array {
		return array_map( 'intval', array_unique( array_filter( $ids ) ) );
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return $this->options_factory->create( $search, $page, $id );
	}

}