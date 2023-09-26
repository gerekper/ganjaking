<?php

namespace ACA\WC\Editing\Strategy;

use ACP\Editing\Strategy;

class Product extends Strategy\Post {

	public function user_can_edit(): bool {
		return current_user_can( 'manage_woocommerce' ) && parent::user_can_edit();
	}

}