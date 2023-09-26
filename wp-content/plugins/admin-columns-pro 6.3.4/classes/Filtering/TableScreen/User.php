<?php

namespace ACP\Filtering\TableScreen;

use ACP\Filtering\TableScreen;
use ACP\Helper\FilterButtonFactory;

class User extends TableScreen {

	public function __construct( array $models, $assets ) {
		parent::__construct( $models, $assets );

		add_action( 'restrict_manage_users', [ $this, 'render_markup' ], 1 );

		$filter_button = FilterButtonFactory::create( FilterButtonFactory::SCREEN_USERS );
		$filter_button->register();
	}

	/**
	 * Run once for Users
	 */
	public function render_markup() {
		remove_action( 'restrict_manage_users', [ $this, 'render_markup' ], 1 );

		parent::render_markup();
	}

}