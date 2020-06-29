<?php

namespace ACP;

use AC;
use AC\Admin;
use AC\Groups;
use AC\Registrable;

class ListScreens implements Registrable {

	public function register() {
		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_groups' ] );
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ] );
	}

	/**
	 * @param Groups $groups
	 */
	public function register_list_screen_groups( Groups $groups ) {
		$groups->register_group( 'taxonomy', __( 'Taxonomy' ), 15 );
		$groups->register_group( 'network', __( 'Network' ), 5 );
	}

	/**
	 * @return bool
	 */
	private function is_settings_screen() {
		$tab = filter_input( INPUT_GET, 'tab' );

		return Admin::NAME === filter_input( INPUT_GET, 'page' ) && in_array( $tab, [ null, 'columns' ], true );
	}

	/**
	 * @param AC\ListScreens $register
	 *
	 * @since 4.0
	 */
	public function register_list_screens( AC\ListScreens $register ) {
		$list_screens = [];

		// Post types
		foreach ( $register->get_post_types() as $post_type ) {
			$list_screens[] = new ListScreen\Post( $post_type );
		}

		$list_screens[] = new ListScreen\Media();
		$list_screens[] = new ListScreen\Comment();

		foreach ( $this->get_taxonomies() as $taxonomy ) {
			$list_screens[] = new ListScreen\Taxonomy( $taxonomy );
		}

		$list_screens[] = new ListScreen\User();

		if ( is_multisite() ) {

			// Settings UI
			if ( $this->is_settings_screen() ) {

				// Main site
				if ( is_main_site() ) {
					$list_screens[] = new ListScreen\MSUser();
					$list_screens[] = new ListScreen\MSSite();
				}
			} else {

				// Table screen
				$list_screens[] = new ListScreen\MSUser();
				$list_screens[] = new ListScreen\MSSite();
			}
		}

		foreach ( $list_screens as $list_screen ) {
			AC\ListScreenTypes::instance()->register_list_screen( $list_screen );
		}
	}

	/**
	 * Get a list of taxonomies supported by Admin Columns
	 * @return array List of taxonomies
	 * @since 1.0
	 */
	private function get_taxonomies() {
		$taxonomies = get_taxonomies( [ 'show_ui' => true ] );

		if ( isset( $taxonomies['post_format'] ) ) {
			unset( $taxonomies['post_format'] );
		}

		if ( isset( $taxonomies['link_category'] ) && ! get_option( 'link_manager_enabled' ) ) {
			unset( $taxonomies['link_category'] );
		}

		/**
		 * Filter the post types for which Admin Columns is active
		 *
		 * @param array $post_types List of active post type names
		 *
		 * @since 2.0
		 */
		return (array) apply_filters( 'acp/taxonomies', $taxonomies );
	}

}