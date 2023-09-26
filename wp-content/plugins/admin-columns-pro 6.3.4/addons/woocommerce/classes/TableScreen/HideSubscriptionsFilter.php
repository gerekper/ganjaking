<?php

namespace ACA\WC\TableScreen;

use AC\ListScreen;
use AC\Registerable;
use ACP\Settings\ListScreen\HideOnScreen;

class HideSubscriptionsFilter implements Registerable {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @var HideOnScreen
	 */
	private $hide_on_screen;

	public function __construct( ListScreen $list_screen, HideOnScreen $hide_on_screen ) {
		$this->list_screen = $list_screen;
		$this->hide_on_screen = $hide_on_screen;
	}

	public function register(): void
    {
		add_filter( 'admin_body_class', [ $this, 'hide_filter' ] );
	}

	public function hide_filter( $class ) {
		if ( $this->hide_on_screen->is_hidden( $this->list_screen ) ) {
			$class .= ' ac-filter-' . $this->hide_on_screen->get_name();
		}

		return $class;
	}

}