<?php

namespace ACA\BP\Service;

use AC;
use AC\Registerable;
use ACA\BP\Column;
use ReflectionException;

class Columns implements Registerable {

	public function register(): void
    {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
		add_action( 'acp/column_types', [ $this, 'register_columns' ] );
	}

	/**
	 * Add custom columns
	 *
	 * @param AC\ListScreen $list_screen
	 *
	 * @throws ReflectionException
	 */
	public function register_columns( AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof AC\ListScreen\User ) {
			$classes = [
				Column\Profile::class,
				Column\User\ActivityUpdates::class,
				Column\User\Friends::class,
				Column\User\Groups::class,
				Column\User\LastActivity::class,
				Column\User\LastSeen::class,
				Column\User\MemberType::class,
				Column\User\Status::class,
			];

			foreach ( $classes as $class ) {
				$list_screen->register_column_type( new $class );
			}
		}
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function register_column_groups( AC\Groups $groups ) {
		$groups->add( 'buddypress', 'BuddyPress', 11 );
	}

}