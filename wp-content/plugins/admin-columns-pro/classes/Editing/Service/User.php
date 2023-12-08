<?php

namespace ACP\Editing\Service;

use AC\Helper\Select\Options\Paginated;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\PaginatedOptionsFactory;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;

class User implements Service, PaginatedOptions {

	/**
	 * @var View\AjaxSelect
	 */
	private $view;

	/**
	 * @var Storage
	 */
	protected $storage;

	/**
	 * @var string[]
	 */
	protected $roles;

	/**
	 * @var PaginatedOptionsFactory
	 */
	protected $options_factory;

	public function __construct( View\AjaxSelect $view, Storage $storage, PaginatedOptionsFactory $options_factory = null ) {
		$this->view = $view;
		$this->storage = $storage;
		$this->options_factory = $options_factory ?: new PaginatedOptions\Users();
	}

	public function get_view( string $context ): ?View {
		return $this->view;
	}

	public function update( int $id, $data ): void {
		$this->storage->update( $id, $this->sanitize_user_id( $data ) );
	}

	private function sanitize_user_id( $user_id ): ?int {
		return $user_id && is_numeric( $user_id )
			? (int) $user_id
			: null;
	}

	private function get_stored_user_id( int $id ): ?int {
		$user_id = $this->storage->get( $id );

		if ( is_array( $user_id ) ) {
			$user_id = reset( $user_id );
		}

		return $this->sanitize_user_id( $user_id );
	}

	public function get_value( int $id ) {
		$user_id = $this->get_stored_user_id( $id );

		if ( ! $user_id || ! get_userdata( $user_id ) ) {
			return false;
		}

		return [
			$user_id => ac_helper()->user->get_display_name( $user_id ),
		];
	}

	public function get_paginated_options( string $search, int $page, int $id = null ): Paginated {
		return $this->options_factory->create( $search, $page, $id );
	}

}