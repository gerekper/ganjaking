<?php

namespace ACA\MetaBox\Service;

use AC;
use AC\Groups;
use AC\Registerable;
use ACA\MetaBox\ListScreen;

class ListScreens implements Registerable {

	public function register(): void
    {
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ] );
		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_groups' ] );
	}

	public function register_list_screen_groups( Groups $groups ) {
		$groups->register_group( 'metabox', 'MetaBox', 7 );
	}

	public function register_list_screens() {
		$list_screens = [];
		$post_type_mapping = [
			'meta-box'        => ListScreen\MetaBox::class,
			'mb-taxonomy'     => ListScreen\Taxonomy::class,
			'mb-relationship' => ListScreen\Relationship::class,
			'mb-post-type'    => ListScreen\PostType::class,
		];

		foreach ( $post_type_mapping as $post_type => $class_name ) {
			if ( post_type_exists( $post_type ) ) {
				$list_screens[] = new $class_name();
			}
		}

		foreach ( $list_screens as $list_screen ) {
			AC\ListScreenTypes::instance()->register_list_screen( $list_screen );
		}
	}

}