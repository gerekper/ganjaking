<?php

declare(strict_types=1);

namespace ACA\EC\Service;

use AC;
use AC\Registerable;
use ACA\EC\ListScreenFactory;

final class ListScreens implements Registerable {

	public function register(): void
    {
        AC\ListScreenFactory\Aggregate::add( new ListScreenFactory\EventFactory() );
        AC\ListScreenFactory\Aggregate::add( new ListScreenFactory\OrganizerFactory() );
        AC\ListScreenFactory\Aggregate::add( new ListScreenFactory\VenueFactory() );

		if ( post_type_exists( 'tribe_event_series' ) ) {
            AC\ListScreenFactory\Aggregate::add( new ListScreenFactory\EventSeriesFactory() );
		}

		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_groups' ] );
		add_filter( 'ac/admin/menu_group', [ $this, 'update_menu_list_groups' ], 10, 2 );
	}

	public function register_list_screen_groups( AC\Groups $groups ): void {
		$groups->add( 'events-calendar', 'Events Calendar', 7 );
	}

	private function get_post_list_keys(): array {
		return [
			'tribe_organizer',
			'tribe_events',
			'tribe_event_series',
			'tribe_venue',
		];
	}

	public function update_menu_list_groups( string $group, AC\ListScreen $list_screen ): string {
		$keys = $this->get_post_list_keys();

		if ( in_array( $list_screen->get_key(), $keys, true ) ) {
			return 'events-calendar';
		}

		return $group;
	}

}