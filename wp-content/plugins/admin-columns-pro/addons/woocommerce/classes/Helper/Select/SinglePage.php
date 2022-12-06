<?php

namespace ACA\WC\Helper\Select;

use AC;

class SinglePage
	implements AC\Helper\Select\Paginated {

	public function get_total_pages() {
		return 1;
	}

	public function is_last_page() {
		return true;
	}

	public function get_page() {
		return 1;
	}

}