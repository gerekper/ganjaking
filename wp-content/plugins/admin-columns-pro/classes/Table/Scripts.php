<?php

namespace ACP\Table;

use AC\Asset;
use AC\Asset\Style;
use AC\ColumnSize;
use AC\ListScreen;
use AC\Registerable;
use ACP\Asset\Script\Table;

class Scripts implements Registerable {

	/**
	 * @var Asset\Location\Absolute
	 */
	private $location;

	/**
	 * @var ColumnSize\UserStorage
	 */
	private $user_storage;

	/**
	 * @var ColumnSize\ListStorage
	 */
	private $list_storage;

	public function __construct( Asset\Location\Absolute $location, ColumnSize\UserStorage $user_storage, ColumnSize\ListStorage $list_storage ) {
		$this->location = $location;
		$this->user_storage = $user_storage;
		$this->list_storage = $list_storage;
	}

	public function register() {
		add_action( 'ac/table_scripts', [ $this, 'scripts' ] );
	}

	public function scripts( ListScreen $list_screen ) {
		if ( ! $list_screen->has_id() ) {
			return;
		}

		$assets = [
			new Style( 'acp-table', $this->location->with_suffix( 'assets/core/css/table.css' ) ),
			new Table( $this->location->with_suffix( 'assets/core/js/table.js' ), $list_screen, $this->user_storage, $this->list_storage ),
		];

		foreach ( $assets as $asset ) {
			$asset->enqueue();
		}
	}

}