<?php

namespace ACP\Editing;

use AC;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use AC\Registrable;
use AC\Request;
use ACP;
use ACP\Settings\ListScreen\HideOnScreenCollection;

class Addon implements Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/** @var Request */
	private $request;

	public function __construct( Storage $storage, Location\Absolute $location, Request $request ) {
		$this->storage = $storage;
		$this->location = $location;
		$this->request = $request;
	}

	public function register() {
		add_action( 'ac/column/settings', [ $this, 'register_column_settings' ] );
		add_action( 'ac/table/list_screen', [ $this, 'register_table_screen' ] );
		add_action( 'wp_ajax_acp_editing_request', [ $this, 'ajax_edit_request' ] );
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
	}

	public function add_hide_on_screen( HideOnScreenCollection $collection, AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof ListScreen ) {
			$collection->add( new Admin\HideOnScreen\InlineEdit() )
			           ->add( new Admin\HideOnScreen\BulkEdit(), 20 );
		}
	}

	public function ajax_edit_request() {
		check_ajax_referer( 'ac-ajax' );

		$factory = new RequestHandlerFactory( $this->storage );

		$factory->create( $this->request )
		        ->handle( $this->request );
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
			new Preference\EditState(),
			$this->request
		);

		$table_screen->register();
	}

	public function register_column_settings( AC\Column $column ) {
		( new ColumnInlineSettingsSetter() )->register( $column );
		( new ColumnBulkSettingsSetter() )->register( $column );
	}

	/**
	 * @return bool
	 * @deprecated 5.1
	 */
	public function is_editing_active() {
		_deprecated_function( __METHOD__, '5.1' );

		return false;
	}

	/**
	 * @deprecated 4.5.4
	 */
	public function helper() {
		_deprecated_function( __METHOD__, '4.5.4' );
	}

}