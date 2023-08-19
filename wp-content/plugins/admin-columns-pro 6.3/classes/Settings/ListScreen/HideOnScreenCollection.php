<?php

namespace ACP\Settings\ListScreen;

use ACP\Type\HideOnScreen\Group;

class HideOnScreenCollection {

	public const SORT_PRIORITY = 1;
	public const SORT_LABEL = 2;

	/**
	 * @var HideOnScreen[]
	 */
	private $items;

	public function __construct( array $items = [] ) {
		array_map( [ $this, 'add' ], $items );
	}

	public function add( HideOnScreen $hide_on_screen, Group $group, int $priority = 10 ): self {
		$this->items[] = [
			'group'    => $group,
			'priority' => $priority,
			'item'     => $hide_on_screen,
		];

		return $this;
	}

	public function remove( HideOnScreen $hide_on_screen ): self {
		foreach ( $this->items as $k => $item ) {
			if ( $item['item']->get_name() === $hide_on_screen->get_name() ) {
				unset( $this->items[ $k ] );
			}
		}

		return $this;
	}

	/**
	 * @param array $args
	 *
	 * @return HideOnScreen[]
	 */
	public function all( array $args = [] ): array {
		$sort_by = $args['sort_by'] ?? self::SORT_PRIORITY;
		$filter_by_group = $args['filter_by_group'] ?? null;

		$items = $this->items;

		if ( $filter_by_group instanceof Group ) {
			$items = $this->filter( $items, $filter_by_group );
		}

		if ( $sort_by ) {
			$items = $this->sort( $items, $sort_by );
		}

		return array_map( [ $this, 'pluck_item' ], $items );
	}

	private function pluck_item( array $item ) {
		return $item['item'];
	}

	private function filter( array $items, Group $group ): array {
		$_items = [];
		foreach ( $items as $item ) {
			if ( $group->equals( $item['group'] ) ) {
				$_items[] = $item;
			}
		}

		return $_items;
	}

	private function sort( array $items, string $sort_by ): array {
		switch ( $sort_by ) {
			case self::SORT_LABEL :
				return $this->sort_by_label( $items );
			default :
				return $this->sort_by_priority( $items );
		}
	}

	private function sort_by_priority( array $items ): array {
		$sorted = [];

		foreach ( $items as $item ) {
			$sorted[ $item['priority'] ][] = $item;
		}

		ksort( $sorted, SORT_NUMERIC );

		return array_merge( ...$sorted );
	}

	private function sort_by_label( array $items ): array {
		$sorted = [];

		foreach ( $items as $k => $item ) {
			$sorted[ $k ] = $item['item']->get_label();
		}

		natcasesort( $sorted );

		foreach ( array_keys( $sorted ) as $k ) {
			$sorted[ $k ] = $items[ $k ];
		}

		return $sorted;
	}

}