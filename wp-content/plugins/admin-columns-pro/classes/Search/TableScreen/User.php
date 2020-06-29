<?php

namespace ACP\Search\TableScreen;

use ACP\Helper\FilterButtonFactory;
use ACP\Search\TableScreen;

class User extends TableScreen {

	public function register() {
		add_action( 'restrict_manage_users', [ $this, 'filters_markup' ], 1 );

		$filter_button = FilterButtonFactory::create( FilterButtonFactory::SCREEN_USERS );
		$filter_button->register();

		parent::register();
	}

	/**
	 * @inheritDoc
	 */
	public function filters_markup() {
		remove_action( 'restrict_manage_users', [ $this, __FUNCTION__ ], 1 );

		return parent::filters_markup();
	}

}