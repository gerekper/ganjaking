<?php

namespace ACA\MetaBox;

use AC;
use AC\Registerable;
use ACA\MetaBox\Service;
use ACP\Service\IntegrationStatus;
use MB_Comment_Meta_Box;

class MetaBox implements Registerable {

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register(): void {
		if ( ! class_exists( 'RWMB_Loader', false ) ) {
			return;
		}

		$relationship_repository = new RelationshipRepository();

		$services = [
			new Service\Columns( new ColumnFactory(), new RelationColumnFactory(), $relationship_repository ),
			new Service\ColumnInstantiate( $relationship_repository ),
			new Service\QuickAdd(),
			new Service\Scripts( $this->location ),
			new Service\Storage(),
			new IntegrationStatus( 'ac-addon-metabox' ),
		];

		foreach ( $services as $service ) {
			if ( $service instanceof Registerable ) {
				$service->register();
			}
		}
	}

}