<?php

namespace ACP\Search\TableScreen;

use ACP\Helper\FilterButtonFactory;
use ACP\Search\TableScreen;

class Taxonomy extends TableScreen {

	public function register() {
		add_action( 'in_admin_footer', [ $this, 'filters_markup' ], 1 );

		$filter_button = FilterButtonFactory::create( FilterButtonFactory::SCREEN_TAXONOMY );
		$filter_button->register();

		parent::register();
	}

}