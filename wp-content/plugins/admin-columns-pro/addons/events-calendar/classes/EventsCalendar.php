<?php

namespace ACA\EC;

use AC;
use AC\Registerable;
use ACA\EC\ImportListscreens;
use ACP\Service\IntegrationStatus;
use ACP\Storage\ListScreen\DecoderFactory;

final class EventsCalendar implements Registerable {

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		if ( ! class_exists( 'Tribe__Events__Main', false ) ) {
			return;
		}

		$services = [
			new Service\ColumnGroups(),
			new Service\ListScreens(),
			new Service\Scripts( $this->location ),
			new Service\TableScreen( $this->location ),
			new ImportListscreens\Message( new ImportListscreens\ImportedSetting() ),
			new ImportListscreens\Controller( new AC\Request(), AC()->get_storage(), new DecoderFactory( AC\ListScreenTypes::instance() ), $this->location ),
			new IntegrationStatus( 'ac-addon-events-calendar' ),
		];

		array_map( [ $this, 'register_service' ], $services );
	}

	private function register_service( Registerable $service ) {
		$service->register();
	}

}