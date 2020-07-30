<?php

namespace ACP\Settings\ListScreen;

class HideOnScreenCollection {

	const SORT_PRIORITY = 1;

	const SORT_LABEL = 2;

	/**
	 * @var HideOnScreen[]
	 */
	private $items;

	public function __construct( array $items = [] ) {
		array_map( [ $this, 'add' ], $items );
	}

	public function add( HideOnScreen $hide_on_screen, $priority = 10 ) {
		$this->items[] = [
			'priority' => $priority,
			'item'     => $hide_on_screen,
		];

		return $this;
	}

	public function remove( HideOnScreen $hide_on_screen ) {
		foreach ( $this->items as $k => $item ) {
			if ( $item['item']->get_name() === $hide_on_screen->get_name() ) {
				unset( $this->items[ $k ] );
			}
		}

		return $this;
	}

	public function all( $sort_by = null ) {
		switch ( $sort_by ) {
			case self::SORT_LABEL :
				$sorted = $this->sort_by_label( $this->items );

				break;
			default :
				$sorted = $this->sort_by_priority( $this->items );
		}

		return $sorted;
	}

	/**
	 * @param array $items
	 *
	 * @return array
	 */
	private function sort_by_priority( array $items ) {
		$aggregated = $sorted = [];

		foreach ( $items as $item ) {
			$aggregated[ $item['priority'] ][] = $item['item'];
		}

		ksort( $aggregated, SORT_NUMERIC );

		foreach ( $aggregated as $priority => $_items ) {
			$sorted = array_merge( $sorted, $this->sort_by_label( $_items ) );
		}

		return $sorted;
	}

	/**
	 * @param array $items
	 *
	 * @return array
	 */
	private function sort_by_label( array $items ) {
		$sorted = [];

		foreach ( $items as $k => $item ) {
			$sorted[ $k ] = $item->get_label();
		}

		natcasesort( $sorted );

		foreach ( array_keys( $sorted ) as $k ) {
			$sorted[ $k ] = $items[ $k ];
		}

		return $sorted;
	}

}