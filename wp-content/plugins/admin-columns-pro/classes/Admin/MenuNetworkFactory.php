<?php

namespace ACP\Admin;

class MenuNetworkFactory extends MenuFactory {

	public function create( $current ) {
		$menu = parent::create( $current );

		$menu->remove_item( 'pro' );
		$menu->remove_item( 'settings' );

		return $menu;
	}

}