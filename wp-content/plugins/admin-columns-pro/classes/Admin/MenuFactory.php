<?php

namespace ACP\Admin;

use AC;
use ACP\Admin\Main\Tools;

class MenuFactory extends AC\Admin\MenuFactory {

	public function create( $current ) {
		$menu = parent::create( $current );

		$menu->add_item( Tools::NAME, __( 'Tools', 'codepress-admin-columns' ) );

		return $menu;
	}

}