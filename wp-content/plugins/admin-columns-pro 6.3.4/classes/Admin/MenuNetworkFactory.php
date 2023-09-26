<?php

namespace ACP\Admin;

use AC\Admin\Menu;

class MenuNetworkFactory extends MenuFactory {

	public function create( string $current ): Menu
    {
		$menu = parent::create( $current );

		$menu->remove_item( 'pro' );
		$menu->remove_item( 'settings' );

		return $menu;
	}

}