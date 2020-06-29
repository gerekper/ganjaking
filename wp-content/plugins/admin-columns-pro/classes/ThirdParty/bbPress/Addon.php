<?php

namespace ACP\ThirdParty\bbPress;

use AC;
use AC\ListScreenTypes;
use ACP;
use ReflectionException;

final class Addon implements AC\Registrable {

	public function register() {
		add_action( 'ac/column_types', [ $this, 'set_columns' ] );
		add_action( 'ac/column_groups', [ $this, 'register_column_group' ] );
		add_action( 'ac/list_screen_groups', [ $this, 'register_list_screen_group' ] );
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ], 11 );
		add_filter( 'ac/editing/role_group', [ $this, 'editing_role_group' ], 10, 2 );

	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @throws ReflectionException
	 */
	public function set_columns( $list_screen ) {
		$list_screen->register_column_types_from_dir( __NAMESPACE__ . '\Column' );
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function register_column_group( $groups ) {
		$groups->register_group( 'bbpress', __( 'bbPress' ), 25 );
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function register_list_screen_group( $groups ) {
		$groups->register_group( 'bbpress', __( 'bbPress' ), 8 );
	}

	/**
	 * @since 4.0
	 */
	public function register_list_screens() {
		foreach ( $this->get_post_types() as $post_type ) {

			$list_screen = new ACP\ListScreen\Post( $post_type );
			$list_screen->set_group( 'bbpress' );

			ListScreenTypes::instance()->register_list_screen( $list_screen );
		}
	}

	/**
	 * @return bool
	 */
	private function is_active() {
		return class_exists( 'bbPress' );
	}

	/**
	 * @return string[]
	 */
	private function get_post_types() {
		if ( ! $this->is_active() ) {
			return [];
		}

		return [ 'forum', 'topic', 'reply' ];
	}

	/**
	 * @param $group
	 * @param $role
	 *
	 * @return string
	 */
	public function editing_role_group( $group, $role ) {
		if ( substr( $role, 0, 4 ) === "bbp_" ) {
			$group = __( 'bbPress', 'codepress-admin-columns' );
		}

		return $group;
	}

}