<?php

namespace ACP\Filtering;

use AC;
use AC\Asset;
use AC\Asset\Location;
use AC\Registrable;
use AC\Request;
use AC\Type\ListScreenId;
use ACP;

/**
 * @since 4.0
 */
class Addon implements Registrable {

	/**
	 * @var AC\ListScreenRepository\Storage
	 */
	private $storage;

	/**
	 * @var Location
	 */
	private $location;

	/**
	 * @var Request
	 */
	private $request;

	public function __construct( AC\ListScreenRepository\Storage $storage, Location $location, Request $request ) {
		$this->storage = $storage;
		$this->location = $location;
		$this->request = $request;
	}

	public function register() {
		add_action( 'ac/column/settings', [ $this, 'settings' ] );
		add_action( 'ac/admin_scripts/columns', [ $this, 'settings_scripts' ] );
		add_action( 'ac/table/list_screen', [ $this, 'table_screen' ] );
		add_action( 'ac/table/list_screen', [ $this, 'handle_filtering' ] );
		add_action( 'wp_ajax_acp_update_filtering_cache', [ $this, 'ajax_update_dropdown_cache' ] );
	}

	public function ajax_update_dropdown_cache() {
		check_ajax_referer( 'ac-ajax' );

		$layout_id = $this->request->get( 'layout' );

		if ( ! $layout_id ) {
			exit;
		}

		$list_screen = $this->storage->find( new ListScreenId( $this->request->get( 'layout' ) ) );

		if ( ! $list_screen ) {
			exit;
		}

		$table_screen = $this->table_screen( $list_screen );

		if ( ! $table_screen ) {
			exit;
		}

		wp_send_json_success( $table_screen->update_dropdown_cache() );
	}

	/**
	 * @return Helper
	 */
	public function helper() {
		return new Helper();
	}

	/**
	 * @param AC\Column $column
	 *
	 * @return Model|false
	 */
	public function get_filtering_model( $column ) {
		if ( ! $column instanceof Filterable ) {
			return false;
		}

		$list_screen = $column->get_list_screen();

		if ( ! $list_screen instanceof ListScreen ) {
			return false;
		}

		$model = $column->filtering();
		$model->set_strategy( $list_screen->filtering( $model ) );

		return $model;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return array|false
	 */
	private function get_models( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreen ) {
			return false;
		}

		$models = [];

		foreach ( $list_screen->get_columns() as $column ) {
			$model = $this->get_filtering_model( $column );

			if ( $model ) {
				$models[] = $model;
			}
		}

		return $models;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return TableScreen|false
	 */
	public function table_screen( AC\ListScreen $list_screen ) {
		$models = $this->get_models( $list_screen );

		if ( ! $models ) {
			return false;
		}

		$assets[] = new Asset\Style( 'acp-filtering-table', $this->location->with_suffix( 'assets/filtering/css/table.css' ) );
		$assets[] = new Asset\Script( 'acp-filtering-table', $this->location->with_suffix( 'assets/filtering/js/table.js' ), [ 'jquery', 'jquery-ui-datepicker' ] );

		switch ( true ) {
			case $list_screen instanceof ACP\ListScreen\MSUser :
				return new TableScreen\MSUser( $models, $assets );

			case $list_screen instanceof ACP\ListScreen\User :
				return new TableScreen\User( $models, $assets );

			case $list_screen instanceof ACP\ListScreen\Post :
			case $list_screen instanceof ACP\ListScreen\Media :
				return new TableScreen\Post( $models, $assets );

			case $list_screen instanceof ACP\ListScreen\Comment :
				return new TableScreen\Comment( $models, $assets );

			case $list_screen instanceof ACP\ListScreen\Taxonomy :
				return new TableScreen\Taxonomy( $models, $assets );
		}

		return false;
	}

	public function settings_scripts() {
		$script = new Asset\Script( 'acp-filtering-settings', $this->location->with_suffix( 'assets/filtering/js/settings.js' ), [ 'jquery' ] );
		$script->enqueue();
	}

	/**
	 * Register field settings for filtering
	 *
	 * @param AC\Column $column
	 */
	public function settings( $column ) {
		$model = $this->get_filtering_model( $column );

		if ( $model ) {
			$model->register_settings();
		}
	}

	/**
	 * Handle filtering request
	 *
	 * @param AC\ListScreen $list_screen
	 */
	public function handle_filtering( AC\ListScreen $list_screen ) {
		foreach ( $list_screen->get_columns() as $column ) {
			$model = $this->get_filtering_model( $column );

			if ( $model && $model->is_active() && false !== $model->get_filter_value() ) {
				$model->get_strategy()->handle_request();
			}
		}
	}

}