<?php

namespace ACP\Editing\Controller;

use AC;
use AC\Request;
use AC\Response\Json;
use AC\Type\ListScreenId;
use ACP\Controller;
use ACP\Editing\Editable;
use ACP\Editing\ListScreen;
use ACP\Editing\Model;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\RemoteOptions;

abstract class Column extends Controller {

	/**
	 * @var AC\ListScreenRepository\Storage;
	 */
	private $storage;

	public function __construct( AC\ListScreenRepository\Storage $storage, Request $request ) {
		$this->storage = $storage;

		parent::__construct( $request );

	}

	/**
	 * @return AC\ListScreen|ListScreen|false
	 */
	protected function get_list_screen_from_request() {
		$list_id = $this->request->get( 'layout' );

		if ( ! $list_id ) {
			return false;
		}

		$list_screen = $this->storage->find( new ListScreenId( $list_id ) );

		if ( ! $list_screen || ! $list_screen instanceof ListScreen ) {
			return false;
		}

		return $list_screen;
	}

	/**
	 * @return AC\Column|Editable|false
	 */
	protected function get_column_from_request() {
		$list_screen = $this->get_list_screen_from_request();

		if ( ! $list_screen ) {
			return false;
		}

		$column = $list_screen->get_column_by_name( $this->request->get( 'column' ) );

		if ( ! $column instanceof Editable ) {
			return false;
		}

		return $column;
	}

	/**
	 * @return Model|false
	 */
	protected function get_model_from_request() {
		$column = $this->get_column_from_request();

		if ( ! $column ) {
			return false;
		}

		$model = $column->editing();

		if ( ! $model ) {
			return false;
		}

		return $model;
	}

	public function get_select_values_action() {
		$response = new Json();

		$model = $this->get_model_from_request();

		switch ( true ) {
			case $model instanceof RemoteOptions:
				$options = $model->get_remote_options(
					$this->request->filter( 'item_id', null, FILTER_SANITIZE_NUMBER_INT )
				);
				$has_more = false;
				break;
			case $model instanceof PaginatedOptions:
				$options = $model->get_paginated_options(
					$this->request->filter( 'searchterm' ),
					$this->request->filter( 'page', 1, FILTER_SANITIZE_NUMBER_INT ),
					$this->request->filter( 'item_id', null, FILTER_SANITIZE_NUMBER_INT )
				);
				$has_more = ! $options->is_last_page();

				break;
			default:
				$response->error();
		}

		$select = new AC\Helper\Select\Response( $options, $has_more );

		$response
			->set_parameters( $select() )
			->success();
	}

}