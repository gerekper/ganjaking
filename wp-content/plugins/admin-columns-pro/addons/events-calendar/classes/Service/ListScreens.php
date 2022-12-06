<?php

namespace ACA\EC\Service;

use AC;
use AC\Registerable;
use ACA\EC\ListScreen;

final class ListScreens implements Registerable {

	public function register() {
		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_groups' ] );
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ] );
	}

	public function register_list_screen_groups( AC\Groups $groups ): void {
		$groups->register_group( 'events-calendar', 'Events Calendar', 7 );
	}

	public function register_list_screens( AC\ListScreens $list_screens ): void {
		$list_screens->register_list_screen( new ListScreen\Event() )
		             ->register_list_screen( new ListScreen\Venue() )
		             ->register_list_screen( new ListScreen\Organizer() );

		if( post_type_exists( 'tribe_event_series' ) ){
			$list_screens->register_list_screen( new ListScreen\EventSeries() );
		}
	}

}