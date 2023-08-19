<?php

namespace ACP\Editing\View;

class Menu extends AdvancedSelect {

	public function __construct() {
		parent::__construct( $this->get_menus() );

		$this->set_multiple( true )
		     ->set_clear_button( true );
	}

	private function get_menus() {
		$menus = wp_get_nav_menus();

		if ( ! $menus || is_wp_error( $menus ) ) {
			return [];
		}

		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}

		return $options;
	}

}