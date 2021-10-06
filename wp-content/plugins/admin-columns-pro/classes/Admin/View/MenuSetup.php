<?php

namespace ACP\Admin\View;

use AC\Admin\Menu;
use AC\View;

class MenuSetup extends View {

	public function __construct( Menu $menu ) {
		parent::__construct( [
			'menu_items' => $menu->get_items(),
		] );

		$this->set_template( 'admin/menu-setup' );
	}

}