<?php

namespace ACP\Editing;

use AC;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use AC\Request;
use ACP\Editing\Ajax\TableRowsFactory;
use ACP\Editing\Factory\BulkEditFactory;
use ACP\Editing\Factory\InlineEditFactory;
use ACP\Settings\ListScreen\HideOnScreenCollection;
use ACP\Type\HideOnScreen\Group;

class Addon implements Registerable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/**
	 * @var Request
	 */
	private $request;

	public function __construct( Storage $storage, Location\Absolute $location, Request $request ) {
		$this->storage = $storage;
		$this->location = $location;
		$this->request = $request;
	}

	public function register(): void
    {
		add_action( 'ac/table/list_screen', [ $this, 'load_table' ] );
		add_action( 'ac/table/list_screen', [ $this, 'handle_request_rows' ] );
		add_action( 'ac/table/list_screen', [ $this, 'handle_request_query' ] );
		add_action( 'ac/column/settings', [ $this, 'register_column_settings' ] );
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
		add_action( 'wp_ajax_acp_editing_request', [ $this, 'ajax_edit_request' ] );
	}

	public function load_table( AC\ListScreen $list_screen ) {
		$table = new TableScreen(
			$list_screen,
			$this->location,
			new InlineEditFactory( $list_screen ),
			new BulkEditFactory( $list_screen )
		);
		$table->register();
	}

	public function add_hide_on_screen( HideOnScreenCollection $collection, AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof ListScreen ) {
			$collection->add( new HideOnScreen\InlineEdit(), new Group( Group::FEATURE ) )
			           ->add( new HideOnScreen\BulkEdit(), new Group( Group::FEATURE ), 20 );
		}
		if ( $list_screen instanceof BulkDelete\ListScreen ) {
			$collection->add( new HideOnScreen\BulkDelete(), new Group( Group::FEATURE ), 30 );
		}
	}

	public function handle_request_query() {
		$factory = new RequestHandlerFactory( $this->storage );

		$request_handler = $factory->create( $this->request );

		if ( $request_handler ) {
			$request_handler->handle( $this->request );
		}
	}

	public function ajax_edit_request() {
		check_ajax_referer( 'ac-ajax' );

		$factory = new RequestHandlerAjaxFactory( $this->storage );

		$factory->create( $this->request )
		        ->handle( $this->request );
	}

	public function handle_request_rows( AC\ListScreen $list_screen ) {
		$table_rows = TableRowsFactory::create( $this->request, $list_screen );

		if ( $table_rows && $table_rows->is_request() ) {
			$table_rows->register();
		}
	}

	public function register_column_settings( AC\Column $column ) {
		( new ColumnInlineSettingsSetter() )->register( $column );
		( new ColumnBulkSettingsSetter() )->register( $column );
	}

}