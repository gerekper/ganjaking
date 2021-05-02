<?php

namespace ACP\Sorting\Settings\ListScreen;

use AC\ListScreen;
use ACP\Sorting\Type\SortType;

class PreferredSort {

	const FIELD_SORTING = 'sorting';
	const FIELD_SORTING_ORDER = 'sorting_order';

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @param ListScreen $list_screen
	 */
	public function __construct( ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
	}

	/**
	 * @return SortType|null
	 */
	public function get() {
		$order_by = $this->list_screen->get_preference( self::FIELD_SORTING );

		if ( ! $order_by ) {
			return null;
		}

		return new SortType(
			(string) $order_by,
			$this->list_screen->get_preference( self::FIELD_SORTING_ORDER )
		);
	}

}