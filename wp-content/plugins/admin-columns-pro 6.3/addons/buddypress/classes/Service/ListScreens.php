<?php

namespace ACA\BP\Service;

use AC;
use AC\Registerable;
use AC\Table\ListKeyCollection;
use AC\Type\ListKey;

final class ListScreens implements Registerable {

	public function register(): void
    {
		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_group' ] );
		add_filter( 'ac/admin/menu_group', [ $this, 'update_menu_list_groups' ], 10, 2 );
		add_action( 'ac/list_keys', [ $this, 'add_list_keys' ] );
	}

	public function add_list_keys( ListKeyCollection $list_keys ): void {
		if ( bp_is_active( 'groups' ) ) {
			$list_keys->add( new ListKey( 'bp-groups' ) );
		}
	}

	public function register_list_screen_group( AC\Groups $groups ): void {
		$groups->add( 'buddypress', __( 'BuddyPress' ), 14 );
	}

	public function update_menu_list_groups( string $group, AC\ListScreen $list_screen ): string {
		$keys = [
			'bp-email',
		];

		if ( in_array( $list_screen->get_key(), $keys, true ) ) {
			return 'buddypress';
		}

		return $group;
	}

}