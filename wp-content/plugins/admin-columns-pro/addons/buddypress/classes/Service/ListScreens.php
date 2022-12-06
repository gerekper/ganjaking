<?php

namespace ACA\BP\Service;

use AC;
use AC\Registerable;
use ACA\BP\ListScreen;

final class ListScreens implements Registerable {

	public function register() {
		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_group' ] );
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ] );
	}

	/**
	 * @param AC\AdminColumns $admin_columns
	 */
	public function register_list_screens( AC\ListScreens $list_screens ) {
		$list_screens->register_list_screen( new ListScreen\Email() )
		             ->register_list_screen( new ListScreen\Group() );
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function register_list_screen_group( AC\Groups $groups ) {
		$groups->register_group( 'buddypress', __( 'BuddyPress' ), 14 );
	}

}