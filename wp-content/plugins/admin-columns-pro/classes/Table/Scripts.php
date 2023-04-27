<?php

namespace ACP\Table;

use AC\Asset;
use AC\Asset\Style;
use AC\ColumnSize;
use AC\ListScreen;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use ACP\Asset\Script\Table;
use ACP\Bookmark\SegmentRepository;

class Scripts implements Registerable {

	private $location;

	private $user_storage;

	private $list_storage;

	private $storage;

	private $segment_repository;

	public function __construct(
		Asset\Location\Absolute $location,
		ColumnSize\UserStorage $user_storage,
		ColumnSize\ListStorage $list_storage,
		Storage $storage,
		SegmentRepository $segment_repository
	) {
		$this->location = $location;
		$this->user_storage = $user_storage;
		$this->list_storage = $list_storage;
		$this->storage = $storage;
		$this->segment_repository = $segment_repository;
	}

	public function register() {
		add_action( 'ac/table_scripts', [ $this, 'scripts' ] );
	}

	public function scripts( ListScreen $list_screen ): void {
		if ( ! $list_screen->has_id() ) {
			return;
		}

		$assets = [
			new Style( 'acp-table', $this->location->with_suffix( 'assets/core/css/table.css' ) ),
			new Table(
				$this->location->with_suffix( 'assets/core/js/table.js' ),
				$list_screen,
				$this->user_storage,
				$this->list_storage,
				$this->storage,
				$this->segment_repository
			),
		];

		foreach ( $assets as $asset ) {
			$asset->enqueue();
		}
	}

}