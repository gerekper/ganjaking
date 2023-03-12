<?php

namespace ACP\ListScreenRepository;

use AC;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Filter;
use AC\ListScreenRepository\ListScreenPermissionTrait;
use AC\ListScreenRepository\Sort;
use AC\Type\ListScreenId;
use WP_User;

/**
 * Repository for the deprecated PHP storage
 */
final class Collection implements AC\ListScreenRepository {

	use ListScreenPermissionTrait;

	private $list_screens;

	public function __construct( ListScreenCollection $list_screens ) {
		$this->list_screens = $list_screens;

		// Since this is in memory only, enforce read only
		foreach ( $list_screens as $list_screen ) {
			$list_screen->set_read_only( true );
		}
	}

	public function find_by_user( ListScreenId $id, WP_User $user ): ?ListScreen {
		$list_screens = ( new Filter\ListId( $id ) )->filter( $this->find_all() );

		$list_screen = $list_screens->get_first() ?: null;

		return $list_screen && $this->user_can_view_list_screen( $list_screen, $user )
			? $list_screen
			: null;
	}

	public function find_all_by_user( string $key, WP_User $user, Sort $sort = null ): ListScreenCollection {
		$list_screens = $this->find_all_by_key( $key );

		return ( new Filter\User( $user ) )->filter( $list_screens );
	}

	public function find_all_by_key( string $key, Sort $sort = null ): ListScreenCollection {
		$list_screens = ( new Filter\ListKey( $key ) )->filter( $this->list_screens );

		return $sort
			? $sort->sort( $list_screens )
			: $list_screens;
	}

	public function find_all( Sort $sort = null ): ListScreenCollection {
		return $this->list_screens;
	}

	public function find( ListScreenId $id ): ?ListScreen {
		$list_screens = ( new Filter\ListId( $id ) )->filter( $this->list_screens );

		return $list_screens->get_first() ?: null;
	}

	public function exists( ListScreenId $id ): bool {
		return null !== $this->find( $id );
	}

}