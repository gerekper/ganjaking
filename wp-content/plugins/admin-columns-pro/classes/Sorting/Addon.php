<?php

namespace ACP\Sorting;

use AC;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use ACP\Sorting\Admin;
use ACP\Sorting\Controller;

/**
 * Sorting Addon class
 * @since 1.0
 */
class Addon implements AC\Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/**
	 * @var AC\Admin
	 */
	private $admin;

	public function __construct( Storage $storage, Location\Absolute $location, AC\Admin $admin ) {
		$this->storage = $storage;
		$this->location = $location;
		$this->admin = $admin;
	}

	public function register() {
		add_action( 'ac/table/list_screen', [ $this, 'init_table' ], 11 ); // After filtering
		add_action( 'ac/column/settings', [ $this, 'register_column_settings' ] );

		$services = [
			new Controller\ResetSorting(),
			new Controller\AjaxResetSorting( $this->storage ),
		];

		foreach ( $services as $service ) {
			if ( $service instanceof AC\Registrable ) {
				$service->register();
			}
		}

		$this->register_admin_elements();
	}

	private function register_admin_elements() {
		$this->admin->get_page( 'settings' )->add_section( new Admin\Section\ResetSorting() );

		/** @var AC\Admin\Section\General $general */
		$general = $this->admin->get_page( 'settings' )->get_section( 'general' );
		$general->add_option( new Admin\ShowAllResults() );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function init_table( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreen ) {
			return;
		}

		$table = new Table\Screen( $list_screen, $this->location, new NativeSortableRepository() );
		$table->register();
	}

	/**
	 * Register field settings for sorting
	 *
	 * @param AC\Column $column
	 */
	public function register_column_settings( $column ) {
		$model = ( new ModelFactory() )->create( $column );

		if ( $model ) {
			$column->add_setting( new Settings( $column ) );
		}

		$native = new NativeSortableRepository();

		if ( $native->is_column_sortable( $column->get_list_screen()->get_key(), $column->get_type() ) ) {

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