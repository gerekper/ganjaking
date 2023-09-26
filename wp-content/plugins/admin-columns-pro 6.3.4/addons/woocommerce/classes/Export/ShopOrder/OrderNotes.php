<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

class OrderNotes implements ACP\Export\Service {

	public function get_value( $id ) {
		return (string) get_post( $id )->comment_count;
	}

}