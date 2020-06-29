<?php

namespace ACP\Filtering\TableScreen;

use ACP\Helper\FilterButton;
use ACP\Helper\FilterButtonFactory;

class MSUser extends User {

	public function __construct( array $models, array $assets ) {
		parent::__construct( $models, $assets );

		add_action( 'in_admin_footer', [ $this, 'render_markup' ] );
		add_action( 'in_admin_footer', [ $this, 'render_button' ] );
	}

	public function render_button() {
		$button = new FilterButton\Users( FilterButtonFactory::SCREEN_USERS );

		echo $button->display_button();
	}

}