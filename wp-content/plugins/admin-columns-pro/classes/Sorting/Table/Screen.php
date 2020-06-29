<?php

namespace ACP\Sorting\Table;

use AC;
use AC\Asset\Location;
use AC\Asset\Style;
use AC\Table;
use ACP;
use ACP\Sorting\Asset\Script;
use ACP\Sorting\ModelFactory;
use ACP\Sorting\NativeSortableRepository;

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

	public function __construct( AC\ListScreen $list_screen, Location\Absolute $location, NativeSortableRepository $native_sortable_repository ) {
		$this->list_screen = $list_screen;
		$this->location = $location;
		$this->native_sortable_repository = $native_sortable_repository;
	}

	public function register() {
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'ac/table', [ $this, 'register_reset_button' ] );

		/**
		 * @see WP_List_Table::get_column_info
		 */
		add_filter( 'manage_' . $this->list_screen->get_screen_id() . '_sortable_columns', [ $this, 'add_sortable_headings' ] );

		$this->init_sorting();
		$this->handle_sorting();

		// Only save it as a preference if we reach `shutdown` (in case of an error )
		add_action( 'shutdown', [ $this, 'save_preference' ] );
	}

	/**
	 * @return Sorted
	 */
	private function sorted() {
		return new Sorted( $this->list_screen, $this->preference(), $this->native_sortable_repository, (array) $_GET );
	}

	/**
	 * @return Preference
	 */
	public function preference() {
		return new Preference\SortedBy( $this->list_screen->get_storage_key() );
	}

	/**
	 * @param AC\Table\Screen $table
	 */
	public function register_reset_button( AC\Table\Screen $table ) {
		if ( $this->sorted()->is_sorted_default() ) {
			return;
		}

		$column = $this->sorted()->get_column();

		if ( ! $column ) {
			return;
		}

		$label = strip_tags( $column->get_custom_label() );

		if ( empty( $label ) ) {
			$label = $column->get_label();
		}

		$button = new Table\Button( 'edit-columns' );
		$button->set_label( trim( __( 'Sorted by ', 'codepress-admin-columns' ) ) . ' ' . $label )
		       ->set_url( '#' )
		       ->set_text( __( 'Reset Sorting', 'codepress-admin-columns' ) )
		       ->set_attribute( 'class', 'ac-table-button reset-sorting' );

		$table->register_button( $button, 10 );
	}

	/**
	 * When you revisit a page, set the orderby variable so WordPress prints the columns headers properly
	 * @since 4.0
	 */
	public function init_sorting() {

		// Do not apply any preferences when no columns are stored
		if ( ! $this->list_screen->get_settings() ) {
			return;
		}

		if ( filter_input( INPUT_GET, 'orderby' ) ) {
			return;
		}

		// Ignore media grid
		if ( 'grid' === filter_input( INPUT_GET, 'mode' ) ) {
			return;
		}

		$sorted = $this->sorted();

		if ( ! $sorted->get_column() ) {
			return;
		}

		// Set $_GET and $_REQUEST (used by WP_User_Query)
		$_REQUEST['orderby'] = $_GET['orderby'] = $sorted->get_order_by();
		$_REQUEST['order'] = $_GET['order'] = $sorted->get_order();
	}

	/**
	 * @param AC\Column $column
	 *
	 * @return bool
	 */
	private function is_active_column( AC\Column $column ) {
		$setting = $column->get_setting( 'sort' );

		if ( ! $setting instanceof ACP\Sorting\Settings ) {
			return false;
		}

		return $setting->is_active();
	}

	/**
	 * @since 4.0
	 */
	public function handle_sorting() {
		$list_screen = $this->list_screen;

		if ( ! $list_screen instanceof ACP\Sorting\ListScreen ) {
			return;
		}

		$column = $this->sorted()->get_column();

		if ( ! $column ) {
			return;
		}

		$model = ( new ModelFactory() )->create( $column );

		if ( ! $model ) {
			return;
		}

		if ( ! $this->is_active_column( $column ) ) {
			return;
		}

		$strategy = $list_screen->sorting( $model );
		$model->set_strategy( $strategy );

		$strategy->manage_sorting();
	}

	/**
	 * When the orderby (and order) are set, save the preference
	 * @since 4.0
	 */
	public function save_preference() {

		if ( ! isset( $_GET['orderby'], $_GET['order'] ) ) {
			return;
		}

		$this->preference()
		     ->set_order( $_GET['order'] )
		     ->set_order_by( $_GET['orderby'] )
		     ->save();
	}

	/**
	 * @param array $sortable_columns Column name or label
	 *
	 * @return array Column name or Sanitized Label
	 * @since 1.0
	 */
	public function add_sortable_headings( $sortable_columns ) {

		// Stores the default columns on the listings screen
		if ( ! wp_doing_ajax() && current_user_can( AC\Capabilities::MANAGE ) ) {
			$this->native_sortable_repository->update( $this->list_screen->get_key(), $sortable_columns ?: [] );
		}

		if ( ! $this->list_screen->get_settings() ) {
			return $sortable_columns;
		}

		$columns = $this->list_screen->get_columns();

		if ( ! $columns ) {
			return $sortable_columns;
		}

		foreach ( $columns as $column ) {

			if ( ! $this->is_active_column( $column ) ) {
				unset( $sortable_columns[ $column->get_name() ] );

				continue;
			}

			$model = ( new ModelFactory() )->create( $column );

			if ( $model ) {
				$sortable_columns[ $column->get_name() ] = $column->get_name();
			}
		}

		return $sortable_columns;
	}

	/**
	 * Scripts
	 */
	public function scripts() {
		$assets = [
			new Script\Table( 'acp-sorting', $this->location->with_suffix( 'assets/sorting/js/table.js' ), $this->preference() ),
			new Style( 'acp-sorting', $this->location->with_suffix( 'assets/sorting/css/table.css' ) ),
		];

		foreach ( $assets as $asset ) {
			$asset->enqueue();
		}
	}

}