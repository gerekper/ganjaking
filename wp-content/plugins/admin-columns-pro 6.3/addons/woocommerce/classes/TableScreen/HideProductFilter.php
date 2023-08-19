<?php

namespace ACA\WC\TableScreen;

use AC\ListScreen;
use AC\Registerable;
use ACP\Settings\ListScreen\HideOnScreen;

class HideProductFilter implements Registerable {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @var HideOnScreen
	 */
	private $hide_on_screen;

	/**
	 * @var string
	 */
	private $filter_name;

	public function __construct( ListScreen $list_screen, HideOnScreen $hide_on_screen, $filter_name ) {
		$this->list_screen = $list_screen;
		$this->hide_on_screen = $hide_on_screen;
		$this->filter_name = $filter_name;
	}

	public function register(): void
    {
		add_filter( 'woocommerce_products_admin_list_table_filters', [ $this, 'hide_filter' ] );
	}

	public function hide_filter( $filters ) {
		if ( $this->hide_on_screen->is_hidden( $this->list_screen ) ) {
			unset( $filters[ $this->filter_name ] );
		}

		return $filters;
	}

}