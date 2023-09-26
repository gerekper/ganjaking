<?php

namespace ACA\BeaverBuilder\Service;

use AC\Groups;
use AC\ListScreen;
use AC\Registerable;
use AC\Table\ListKeyCollection;
use AC\Type\ListKey;
use ACA\BeaverBuilder\ListScreen\Template;
use ACP\ListScreen\Taxonomy;

class ListScreens implements Registerable {

	public function register(): void
    {
		add_action( 'ac/list_screen_groups', [ $this, 'register_beaver_builder_group' ] );
		add_filter( 'ac/admin/menu_group', [ $this, 'update_menu_list_groups' ], 10, 2 );
		add_action( 'ac/list_keys', [ $this, 'add_list_keys' ] );
	}

	public function add_list_keys( ListKeyCollection $list_keys ): void {
		$templates = [
			'column',
			'module',
			'row',
			'layout',
		];

		foreach ( $templates as $template ) {
			$list_keys->add( new ListKey( Template::POST_TYPE . $template ) );
		}
	}

	public function register_beaver_builder_group( Groups $groups ): void {
		$groups->add( 'beaver_builder', __( 'Beaver Builder', 'codepress-admin-columns' ), 6 );
	}

	public function update_menu_list_groups( string $group, ListScreen $list_screen ): string {
		$keys = [
			Template::POST_TYPE . 'column',
			Template::POST_TYPE . 'module',
			Template::POST_TYPE . 'row',
			Template::POST_TYPE . 'layout',
			Taxonomy::KEY_PREFIX . 'fl-builder-template-category',
		];

		if ( in_array( $list_screen->get_key(), $keys, true ) ) {
			$group = 'beaver_builder';
		}

		return $group;
	}

}