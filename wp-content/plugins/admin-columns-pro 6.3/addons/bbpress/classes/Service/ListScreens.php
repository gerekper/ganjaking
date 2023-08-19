<?php

namespace ACA\BbPress\Service;

use AC;
use AC\ListScreenPost;
use AC\Registerable;
use ACA\BbPress\ListScreenFactory\TopicFactory;

class ListScreens implements Registerable {

	public function register(): void
    {
		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_group' ] );
		add_action( 'ac/admin/menu_group', [ $this, 'update_menu_list_groups' ], 10, 2 );

		AC\ListScreenFactory\Aggregate::add( new TopicFactory() );
	}

	public function register_list_screen_group( AC\Groups $groups ): void {
		$groups->add( 'bbpress', __( 'bbPress' ), 8 );
	}

	private function get_post_list_keys(): array {
		return [
			'forum',
			'reply',
			'topic',
		];
	}

	public function update_menu_list_groups( string $group, AC\ListScreen $list_screen ): string {
		if ( $list_screen instanceof ListScreenPost && in_array( $list_screen->get_post_type(), $this->get_post_list_keys(), true ) ) {
			return 'bbpress';
		}

		return $group;
	}

}