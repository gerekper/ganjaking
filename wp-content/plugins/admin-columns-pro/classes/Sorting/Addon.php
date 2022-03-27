<?php

namespace ACP\Sorting;

use AC;
use AC\Asset\Location;
use AC\Column;
use AC\ColumnRepository;
use AC\ListScreenRepository\Storage;
use AC\Registrable;
use ACP\Bookmark;
use ACP\Sorting\Controller;

class Addon implements Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/**
	 * @var NativeSortableFactory
	 */
	private $native_sortable_factory;

	/**
	 * @var ModelFactory
	 */
	private $model_factory;

	/**
	 * @var Bookmark\SegmentRepository
	 */
	private $segment_repository;

	public function __construct( Storage $storage, Location\Absolute $location, Bookmark\SegmentRepository $segment_repository ) {
		$this->storage = $storage;
		$this->location = $location;
		$this->segment_repository = $segment_repository;
		$this->native_sortable_factory = new NativeSortableFactory();
		$this->model_factory = new ModelFactory();
	}

	public function register() {
		add_action( 'ac/table/list_screen', [ $this, 'init_table' ], 11 ); // After filtering
		add_action( 'ac/column/settings', [ $this, 'register_column_settings' ] );

		$services = [
			new Controller\ResetSorting(),
			new Controller\AjaxResetSorting( $this->storage ),
		];

		foreach ( $services as $service ) {
			if ( $service instanceof Registrable ) {
				$service->register();
			}
		}
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function init_table( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreen ) {
			return;
		}

		$table = new Table\Screen(
			$list_screen,
			$this->location,
			$this->native_sortable_factory->create( $list_screen ),
			$this->model_factory,
			new ColumnRepository( $list_screen ),
			new Settings\ListScreen\PreferredSort( $list_screen ),
			new Settings\ListScreen\PreferredSegmentSort( new Bookmark\Setting\PreferredSegment( $list_screen, $this->segment_repository ) )
		);

		$table->register();
	}

	/**
	 * Register field settings for sorting
	 *
	 * @param Column $column
	 */
	public function register_column_settings( $column ) {
		$model = $this->model_factory->create( $column );

		if ( $model ) {
			$column->add_setting( new Settings( $column ) );
		}

		$native_repository = $this->native_sortable_factory->create( $column->get_list_screen() );

		if ( $native_repository->find( $column->get_type() ) ) {

			$setting = new Settings( $column );
			$setting->set_default( 'on' );

			$column->add_setting( $setting );
		}
	}

	/**
	 * Hide or show empty results
	 * @return boolean
	 * @since      4.0
	 * @deprecated 5.1
	 */
	public function show_all_results() {
		_deprecated_function( __METHOD__, '5.1', 'acp_sorting_show_all_results()' );

		return acp_sorting_show_all_results();
	}

}