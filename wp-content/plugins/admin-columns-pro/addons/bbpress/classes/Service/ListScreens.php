<?php

namespace ACA\BbPress\Service;

use AC;
use AC\Registerable;
use ACA\BbPress\ListScreen;

class ListScreens implements Registerable {

	public function register() {
		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_group' ] );
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ], 11 );
	}

	public function register_list_screen_group( AC\Groups $groups ): void {
		$groups->register_group( 'bbpress', __( 'bbPress' ), 8 );
	}

	public function register_list_screens(): void {
		$list_screens = [
			new ListScreen\Topic(),
			new ListScreen\Forum(),
			new ListScreen\Reply(),
		];

		foreach ( $list_screens as $list_screen ) {
			AC\ListScreenTypes::instance()->register_list_screen( $list_screen );
		}
	}

}