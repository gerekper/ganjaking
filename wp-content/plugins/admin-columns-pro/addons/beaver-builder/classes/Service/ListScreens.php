<?php

namespace ACA\BeaverBuilder\Service;

use AC;
use AC\Groups;
use AC\Registerable;
use ACA\BeaverBuilder\ListScreen;

class ListScreens implements Registerable {

	public function register(): void {
		add_action( 'ac/list_screen_groups', [ $this, 'register_beaver_builder_group' ] );
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ] );
	}

	public function register_beaver_builder_group( Groups $groups ): void {
		$groups->register_group( 'beaver_builder', __( 'Beaver Builder', 'codepress-admin-columns' ), 6 );
	}

	public function register_list_screens( AC\ListScreens $list_screens ): void {
		if ( ! post_type_exists( PostTypes::POST_TYPE_TEMPLATE ) ) {
			return;
		}

		$bb_list_screens = [
			new ListScreen\Template( 'layout', __( 'Templates', 'fl-builder' ) ),
			new ListScreen\Template( 'row', __( 'Saved Rows', 'fl-builder' ) ),
			new ListScreen\Template( 'column', __( 'Saved Columns', 'fl-builder' ) ),
			new ListScreen\Template( 'module', __( 'Saved Modules', 'fl-builder' ) ),
		];

		foreach ( $bb_list_screens as $list_screen ) {
			$list_screens->register_list_screen( $list_screen );
		}
	}

}