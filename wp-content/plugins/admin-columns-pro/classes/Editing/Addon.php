<?php

namespace ACP\Editing;

use AC;
use AC\Asset\Location;
use AC\Asset\Style;
use AC\ListScreenRepository\Storage;
use ACP;
use ACP\Editing\Ajax\EditableRowsFactory;
use ACP\Editing\Ajax\TableRowsFactory;
use ACP\Editing\Asset\Script;
use ACP\Editing\Controller;
use ACP\Editing\Preference\EditState;

class Addon implements AC\Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/** @var AC\Request */
	private $request;

	public function __construct( Storage $storage, Location\Absolute $location, AC\Request $request ) {
		$this->storage = $storage;
		$this->location = $location;
		$this->request = $request;
	}

	public function register() {
		add_action( 'ac/column/settings', [ $this, 'register_column_settings' ] );
		add_action( 'ac/table/list_screen', [ $this, 'register_table_screen' ] );
		add_action( 'wp_ajax_acp_editing_single_request', [ $this, 'ajax_single_request' ] );
		add_action( 'wp_ajax_acp_editing_bulk_request', [ $this, 'ajax_bulk_request' ] );
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
	}

	public function add_hide_on_screen( ACP\Settings\ListScreen\HideOnScreenCollection $collection, AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof ListScreen ) {
			$collection->add( new Admin\HideOnScreen\InlineEdit(), 10 )
			           ->add( new Admin\HideOnScreen\BulkEdit(), 20 );
		}
	}

	public function ajax_single_request() {
		check_ajax_referer( 'ac-ajax' );

		$controller = new Controller\Single( $this->storage, $this->request, new EditState() );
		$controller->dispatch( $this->request->get( 'method' ) );
	}

	public function ajax_bulk_request() {
		check_ajax_referer( 'ac-ajax' );

		$controller = new Controller\Bulk( $this->storage, $this->request );
		$controller->dispatch( $this->request->get( 'method' ) );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function register_table_screen( $list_screen ) {
		$editable_columns = $this->get_editable_columns( $list_screen );

		// Don't register anything when no column in configured to be editable
		if ( empty( $editable_columns ) ) {
			return;
		}

		$edit_state = new EditState();

		$assets = [
			new Style( 'acp-editing-table', $this->location->with_suffix( 'assets/editing/css/table.css' ) ),
			new Script\Table(
				'acp-editing-table',
				$this->location->with_suffix( 'assets/editing/js/table.js' ),
				$list_screen,
				$editable_columns,
				$edit_state
			),
		];

		$table_screen = new TableScreen( $list_screen, $assets, $edit_state );
		$table_screen->register();

		$table_rows = TableRowsFactory::create( $this->request, $list_screen );

		if ( $table_rows && $table_rows->is_request() ) {
			$table_rows->register();
		}

		$editable_rows = EditableRowsFactory::create( $this->request, $list_screen );

		if ( $editable_rows && $editable_rows->is_request() ) {
			$editable_rows->register();
		}
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return array
	 */
	private function get_editable_columns( AC\ListScreen $list_screen ) {
		$editable_columns = [];

		foreach ( $list_screen->get_columns() as $column ) {
			if ( ! $column instanceof Editable ) {
				continue;
			}

			$model = $column->editing();

			if ( ! $model ) {
				continue;
			}

			$editable_columns[ $column->get_name() ] = $column;
		}

		return $editable_columns;
	}

	/**
	 * Register setting for editing
	 *
	 * @param AC\Column $column
	 */
	public function register_column_settings( $column ) {
		if ( $column instanceof Editable ) {
			$column->editing()->register_settings();
		}
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return bool
	 * @deprecated 5.1
	 */
	public function is_editing_active( AC\ListScreen $list_screen ) {
		_deprecated_function( __METHOD__, '5.1' );

		return false;
	}

	/**
	 * @return Helper
	 * @deprecated 4.5.4
	 */
	public function helper() {
		_deprecated_function( __METHOD__, '4.5.4' );

		return new Helper();
	}

}