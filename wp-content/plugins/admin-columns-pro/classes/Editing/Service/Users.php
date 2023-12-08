<?php

namespace ACP\Editing\Service;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\PaginatedOptionsFactory;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;
use InvalidArgumentException;

class Users implements Service, PaginatedOptions {

	/**
	 * @var Editing\View\AjaxSelect
	 */
	private $view;

	/**
	 * @var Storage
	 */
	protected $storage;

	/**
	 * @var PaginatedOptionsFactory
	 */
	private $options_factory;

	public function __construct( Editing\View\AjaxSelect $view, Storage $storage, PaginatedOptionsFactory $options_factory ) {
		$this->view = $view;
		$this->storage = $storage;
		$this->options_factory = $options_factory;
	}

	public function get_view( string $context ): ?View {
		$view = $this->view;

		if ( $context === self::CONTEXT_BULK ) {
			$view->has_methods( true );
		}

		return $view;
	}

	public function get_value( int $id ) {
		$ids = $this->get_user_ids( $id );

		return $ids
			? array_filter( array_map( [ ac_helper()->user, 'get_display_name' ], array_combine( $ids, $ids ) ) )
			: [];
	}

	/**
	 * @param int $id
	 *
	 * @return int[]
	 */
	private function get_user_ids( int $id ) {
		$ids = $this->storage->get( $id );

		return $ids && is_array( $ids )
			? array_map( 'intval', array_filter( $ids, 'is_numeric' ) )
			: [];
	}

	public function update( int $id, $data ): void {
		$method = $data['method'] ?? null;

		if ( $method === null ) {
			$this->storage->update( $id, $data && is_array( $data ) ? $this->sanitize_ids( $data ) : null );

			return;
		}

		$ids = $data['value'] ?? null;

		if ( ! is_array( $ids ) ) {
			throw new InvalidArgumentException( 'Invalid value' );
		}

		$ids = $this->sanitize_ids( $ids );

		switch ( $method ) {
			case 'add':
				$this->storage->update( $id, array_merge( $this->get_user_ids( $id ), $ids ) ?: null );
				break;
			case 'remove':
				$this->storage->update( $id, array_diff( $this->get_user_ids( $id ), $ids ) ?: null );
				break;
			case 'replace':
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