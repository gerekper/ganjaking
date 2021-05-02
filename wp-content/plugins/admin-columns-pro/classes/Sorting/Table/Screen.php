<?php

namespace ACP\Sorting\Table;

use AC;
use AC\Asset\Location;
use AC\Asset\Script;
use AC\Asset\Style;
use AC\ColumnRepository;
use ACP;
use ACP\Sorting\ApplyFilter;
use ACP\Sorting\Controller;
use ACP\Sorting\ModelFactory;
use ACP\Sorting\NativeSortable\NativeSortableRepository;
use ACP\Sorting\Request;
use ACP\Sorting\Settings;
use ACP\Sorting\Type\SortType;
use ACP\Sorting\UserPreference;

class Screen implements AC\Registrable {

	/**
	 * @var AC\ListScreen
	 */
	private $list_screen;

	/**
	 * @var Location\Absolute $location
	 */
	private $location;

	/**
	 * @var NativeSortableRepository
	 */
	private $native_sortable_repository;

	/**
	 * @var ModelFactory
	 */
	private $model_factory;

	/**
	 * @var ColumnRepository
	 */
	private $column_respository;

	/**
	 * @var Settings\ListScreen\PreferredSort
	 */
	private $preferred_sort;

	/**
	 * @var Settings\ListScreen\PreferredSegmentSort
	 */
	private $preferred_segment_sort;

	public function __construct( AC\ListScreen $list_screen, Location\Absolute $location, NativeSortableRepository $native_sortable_repository, ModelFactory $model_factory, ColumnRepository $column_respository, Settings\ListScreen\PreferredSort $preferred_sort, Settings\ListScreen\PreferredSegmentSort $preferred_segment_sort ) {
		$this->list_screen = $list_screen;
		$this->location = $location;
		$this->native_sortable_repository = $native_sortable_repository;
		$this->model_factory = $model_factory;
		$this->column_respository = $column_respository;
		$this->preferred_sort = $preferred_sort;
		$this->preferred_segment_sort = $preferred_segment_sort;
	}

	private function user_preference() {
		return new UserPreference\SortType( $this->list_screen->get_storage_key() );
	}

	public function register() {
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );

		add_filter( 'manage_' . $this->list_screen->get_screen_id() . '_sortable_columns', [ $this, 'add_sortable_headings' ] );

		$this->request_setter()->handle( new AC\Request() );
		$this->manage_sort()->handle( $_GET );

		add_action( 'ac/table', [ $this, 'add_reset_button' ] );
		add_action( 'shutdown', [ $this, 'save_user_preference' ] );
	}

	public function add_reset_button( AC\Table\Screen $table ) {
		$sort_type = SortType::create_by_request( Request\Sort::create_from_globals() );

		$button = $this->reset_button()->get( $sort_type );

		if ( $button ) {
			$table->register_button( $button );
		}
	}

	private function request_setter() {
		return new Controller\RequestSetterHandler(
			$this->user_preference(),
			$this->preferred_sort,
			$this->preferred_segment_sort,
			new ApplyFilter\DefaultSort( $this->list_screen )
		);
	}

	private function manage_sort() {
		return new Controller\ManageSortHandler(
			$this->list_screen,
			$this->model_factory
		);
	}

	private function reset_button() {
		return new ResetButton(
			$this->column_respository,
			$this->preferred_sort,
			$this->preferred_segment_sort,
			new ApplyFilter\DefaultSort( $this->list_screen )
		);
	}

	/**
	 * When the orderby (and order) are set, save the preference
	 * @since 4.0
	 */
	public function save_user_preference() {
		$request = Request\Sort::create_from_globals();

		if ( $request->get_order_by() ) {
			$this->user_preference()->save( SortType::create_by_request( $request ) );
		}
	}

	/**
	 * @param array $sortable_columns Column name or label
	 *
	 * @return array Column name or Sanitized Label
	 */
	public function add_sortable_headings( $sortable_columns ) {

		// Stores the default columns on the listings screen
		if ( ! wp_doing_ajax() && current_user_can( AC\Capabilities::MANAGE ) ) {
			$this->native_sortable_repository->update( $sortable_columns ?: [] );
		}

		if ( ! $this->list_screen->get_settings() ) {
			return $sortable_columns;
		}

		$columns = $this->column_respository->find_all( [
			ColumnRepository::ARG_FILTER => new Filter\SortableColumns( $this->model_factory ),
		] );

		foreach ( $columns as $column ) {
			$column_name = $column->get_name();

			$sortable_columns[ $column_name ] = $column_name;
		}

		return $sortable_columns;
	}

	public function scripts() {
		$assets = [
			new Script( 'acp-sorting', $this->location->with_suffix( 'assets/sorting/js/table.js' ) ),
			new Style( 'acp-sorting', $this->location->with_suffix( 'assets/sorting/css/table.css' ) ),
		];

		foreach ( $assets as $asset ) {
			$asset->enqueue();
		}
	}

}