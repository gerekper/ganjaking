<?php

namespace ACP\Table;

use AC;
use AC\Asset;
use AC\ColumnSize;
use AC\Registrable;
use ACP\Settings\ListScreen\HideOnScreen;

class Scripts implements Registrable {

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

	private function is_width_configurator_enabled( AC\ListScreen $list_screen ) {
		$hide_on_screen = new HideOnScreen\ColumnResize();

		return apply_filters( 'acp/resize_columns/active', ! $hide_on_screen->is_hidden( $list_screen ), $list_screen );
	}

	public function scripts( AC\ListScreen $list_screen ) {
		if ( ! $list_screen->has_id() ) {
			return;
		}

		$assets = [
			new AC\Asset\Style( 'acp-table', $this->location->with_suffix( 'assets/core/css/table.css' ) ),
			new AC\Asset\Script( 'acp-table', $this->location->with_suffix( 'assets/core/js/table.js' ) ),
		];

		if ( $this->is_width_configurator_enabled( $list_screen ) ) {

			$assets[] = new Script\ColumnResize(
				$this->location->with_suffix( 'assets/core/js/width-configurator.js' ),
				$list_screen,
				$this->user_storage,
				$this->list_storage
			);
		}

		foreach ( $assets as $asset ) {
			$asset->enqueue();
		}
	}

}