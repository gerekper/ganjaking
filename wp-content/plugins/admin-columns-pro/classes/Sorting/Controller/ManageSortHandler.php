<?php

namespace ACP\Sorting\Controller;

use AC\ListScreen;
use ACP\Sorting;
use ACP\Sorting\ModelFactory;

class ManageSortHandler {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @var ModelFactory
	 */
	private $model_factory;

	public function __construct( ListScreen $list_screen, ModelFactory $model_factory ) {
		$this->list_screen = $list_screen;
		$this->model_factory = $model_factory;
	}

	public function handle( $request ) {
		$list_screen = $this->list_screen;

		if ( ! $list_screen instanceof Sorting\ListScreen ) {
			return;
		}

		if ( ! isset( $request['orderby'] ) ) {
			return;
		}

		$column = $this->list_screen->get_column_by_name( $request['orderby'] );

		if ( ! $column ) {
			return;
		}

		$model = $this->model_factory->create( $column );

		if ( ! $model ) {
			return;
		}

		$strategy = $list_screen->sorting( $model );
		$model->set_strategy( $strategy );

		$strategy->manage_sorting();
	}

}