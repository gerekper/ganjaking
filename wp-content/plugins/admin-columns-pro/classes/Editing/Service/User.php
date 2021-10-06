<?php

namespace ACP\Editing\Service;

use AC\Request;
use ACP;
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

	public function get_view( $context ) {
		return $this->view;
	}

	public function get_value( $id ) {
		$user_id = $this->storage->get( $id );

		if ( is_array( $user_id ) && ! empty( $user_id ) ) {
			$user_id = reset( $user_id );
		}

		return $user_id && is_scalar( $user_id )
			? [ $user_id => ac_helper()->user->get_display_name( $user_id ) ]
			: false;
	}

	public function update( Request $request ) {
		$this->storage->update( $request->get( 'id' ), $request->get( 'value' ) );
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
		return $this->options_factory->create( $s, $paged, $id );
	}

}