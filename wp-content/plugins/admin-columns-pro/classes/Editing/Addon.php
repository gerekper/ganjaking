<?php

namespace ACP\Editing;

use AC;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use ACP;
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
			$collection->add( new Admin\HideOnScreen\InlineEdit() )
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
	public function register_table_screen( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreen ) {
			return;
		}

		$editable_data = ( new EditableDataFactory() )->create( $list_screen );

		if ( ! $editable_data ) {
			return;
		}

		$table_screen = new TableScreen(
			$list_screen,
			$editable_data,
			$this->location,
			new EditState(),
			$this->request
		);

		$table_screen->register();
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