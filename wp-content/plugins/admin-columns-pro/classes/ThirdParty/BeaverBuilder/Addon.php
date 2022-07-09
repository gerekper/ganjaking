<?php

namespace ACP\ThirdParty\BeaverBuilder;

use AC;
use AC\Groups;
use AC\Registrable;
use ACP\ThirdParty\BeaverBuilder\ListScreen;

class Addon implements Registrable {

	public function register() {
		add_filter( 'ac/post_types', [ $this, 'deregister_global_post_type' ] );
		add_action( 'ac/list_screen_groups', [ $this, 'register_beaver_builder_group' ] );
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ] );
	}

	public function deregister_global_post_type( $post_types ) {
		unset( $post_types['fl-builder-template'] );

		return $post_types;
	}

	public function register_beaver_builder_group( Groups $groups ) {
		$groups->register_group( 'beaver_builder', __( 'Beaver Builder', 'codepress-admin-columns' ), 6 );
	}

	public function register_list_screens( AC\ListScreens $list_screens ) {
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