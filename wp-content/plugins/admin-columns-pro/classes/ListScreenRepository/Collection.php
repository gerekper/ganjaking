<?php

namespace ACP\ListScreenRepository;

use AC;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\Type\ListScreenId;

final class Collection implements AC\ListScreenRepository {

	/**
	 * @var ListScreenCollection
	 */
	private $list_screens;

	public function __construct( ListScreenCollection $list_screens ) {
		$this->list_screens = $list_screens;

		// Since this is in memory only, enforce read only
		foreach ( $list_screens as $list_screen ) {
			$list_screen->set_read_only( true );
		}
	}

	/**
	 * @param array $args
	 *
	 * @return ListScreenCollection
	 */
	public function find_all( array $args = [] ) {
		$args = array_merge( [
			self::KEY => null,
		], $args );

		$list_screens = new ListScreenCollection();

		foreach ( $this->list_screens as $list_screen ) {
			if ( $args[ self::KEY ] && $list_screen->get_key() !== $args[ self::KEY ] ) {
				continue;
			}

			$list_screens->add( $list_screen );
		}

		return $list_screens;
	}

	/**
	 * @param ListScreenId $id
	 *
	 * @return ListScreen|null
	 */
	public function find( ListScreenId $id ) {
		foreach ( $this->list_screens as $list_screen ) {
			if ( $id->equals( $list_screen->get_id() ) ) {
				return $list_screen;
			}
		}

		return null;
	}

	public function exists( ListScreenId $id ) {
		return null !== $this->find( $id );
	}

}