<?php

namespace ACP\Search\TableScreen;

use ACP\Search\TableScreen;

class Post extends TableScreen {

	public function register() {
		add_action( 'restrict_manage_posts', [ $this, 'filters_markup' ], 1 );

		parent::register();
	}

}