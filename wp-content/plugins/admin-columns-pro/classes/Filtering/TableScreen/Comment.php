<?php

namespace ACP\Filtering\TableScreen;

use ACP\Filtering\TableScreen;

class Comment extends TableScreen {

	public function __construct( array $models, $assets ) {
		parent::__construct( $models, $assets );

		add_action( 'restrict_manage_comments', [ $this, 'render_markup' ] );
	}

}