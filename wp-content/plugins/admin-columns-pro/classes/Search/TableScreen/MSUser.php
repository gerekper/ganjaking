<?php

namespace ACP\Search\TableScreen;

use ACP\Helper\FilterButtonFactory;
use ACP\Search\TableScreen;

class MSUser extends TableScreen {

	public function register() {
		add_action( 'in_admin_footer', [ $this, 'filters_markup' ], 1 );

		$filter_button = FilterButtonFactory::create( FilterButtonFactory::SCREEN_USERS );
		$filter_button->register();

		parent::register();
	}

	public function filters_markup() {
		remove_action( 'in_admin_footer', [ $this, __FUNCTION__ ], 1 );

		parent::filters_markup();
	}

}