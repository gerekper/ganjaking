<?php

namespace ACP\Sorting\Strategy;

use ACP\Sorting\AbstractModel;

final class Media extends Post {

	public function __construct( AbstractModel $model ) {
		parent::__construct( $model, 'attachment' );
	}

	protected function get_pagination_per_page() {
		return (int) get_user_option( 'upload_per_page' );
	}

}