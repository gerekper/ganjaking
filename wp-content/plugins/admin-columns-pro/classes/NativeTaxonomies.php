<?php

namespace ACP;

use AC\ListScreen;
use AC\ListScreenPost;
use AC\Registrable;

class NativeTaxonomies implements Registrable {

	public function register() {
		add_action( 'ac/column_types', [ $this, 'register_columns' ] );
	}

	/**
	 * @param ListScreen $list_screen
	 */
	public function register_columns( ListScreen $list_screen ) {
		$this->register_column_native_taxonomies( $list_screen );

		/**
		 * @deprecated 4.1 Use 'ac/column_types'
		 */
		do_action( 'acp/column_types', $list_screen );
	}

	/**
	 * Register Taxonomy columns that are set by WordPress. These native columns are registered
	 * by setting 'show_admin_column' to 'true' as an argument in register_taxonomy();
	 * Only supports Post Types.
	 *
	 * @param ListScreen $list_screen
	 *
	 * @see register_taxonomy
	 */
	private function register_column_native_taxonomies( ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreenPost ) {
			return;
		}

		$taxonomies = get_taxonomies(
			[
				'show_ui'           => 1,
				'show_admin_column' => 1,
				'_builtin'          => 0,
			],
			'object'
		);

		foreach ( $taxonomies as $taxonomy ) {
			if ( in_array( $list_screen->get_post_type(), $taxonomy->object_type ) ) {
				$column = new Column\NativeTaxonomy();
				$column->set_type( 'taxonomy-' . $taxonomy->name );

				$list_screen->register_column_type( $column );
			}
		}
	}

}