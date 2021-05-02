<?php

namespace ACP\Editing;

use AC;
use AC\Asset\Location;
use AC\Asset\Style;
use ACP\Editing;
use ACP\Editing\Ajax\EditableRowsFactory;
use ACP\Editing\Ajax\EditableRowsFactoryAggregate;
use ACP\Editing\Ajax\TableRowsFactory;
use ACP\Editing\Preference\EditState;
use LogicException;

class TableScreen implements AC\Registrable {

	/**
	 * @var AC\ListScreen
	 */
	protected $list_screen;

	/**
	 * @var array
	 */
	private $editable_data;

	/**
	 * @var AC\Asset\Location\Absolute
	 */
	protected $location;

	/**
	 * @var Preference\EditState
	 */
	protected $edit_state;

	/**
	 * @var AC\Request
	 */
	private $request;

	public function __construct(
		AC\ListScreen $list_screen,
		array $editable_data,
		Location\Absolute $location,
		EditState $edit_state,
		AC\Request $request
	) {
		if ( ! $list_screen instanceof Editing\ListScreen ) {
			throw new LogicException( 'ListScreen should be of type Editing\ListScreen.' );
		}

		$this->list_screen = $list_screen;
		$this->editable_data = $editable_data;
		$this->location = $location;
		$this->edit_state = $edit_state;
		$this->request = $request;
	}

	public function register() {
		add_action( 'ac/table_scripts', [ $this, 'scripts' ] );
		add_action( 'ac/table/actions', [ $this, 'edit_button' ] );

		// Register request handlers
		$table_rows = TableRowsFactory::create( $this->request, $this->list_screen );

		if ( $table_rows && $table_rows->is_request() ) {
			$table_rows->register();
		}

		EditableRowsFactoryAggregate::add_factory( new EditableRowsFactory() );

		$editable_rows = EditableRowsFactoryAggregate::create( $this->request, $this->list_screen );

		if ( $editable_rows && $editable_rows->is_request() ) {
			$editable_rows->register();
		}
	}

	public function scripts() {
		$style = new Style( 'acp-editing-table', $this->location->with_suffix( 'assets/editing/css/table.css' ) );
		$style->enqueue();

		$script = new Asset\Script\Table(
			'acp-editing-table',
			$this->location->with_suffix( 'assets/editing/js/table.js' ),
			$this->list_screen,
			$this->editable_data,
			$this->edit_state
		);
		$script->enqueue();

		// Select 2
		wp_enqueue_script( 'ac-select2' );
		wp_enqueue_style( 'ac-select2' );

		// WP Media picker
		wp_enqueue_media();
		wp_enqueue_style( 'ac-jquery-ui' );

		// WP Color picker
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

		// WP Content Editor
		wp_enqueue_editor();

		do_action( 'ac/table_scripts/editing', $this->list_screen );
	}

	public function edit_button() {
		if ( ! $this->list_screen->has_id() ) {
			return;
		}

		$view = new AC\View( [
			'is_active' => $this->edit_state->is_active( $this->list_screen->get_key() ),
		] );

		echo $view->set_template( 'table/edit-button' );
	}

}