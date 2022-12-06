<?php

namespace ACA\WC\Settings\ShopOrder;

use AC;

class ProductMeta extends AC\Settings\Column\CustomField {

	protected function get_post_type() {
		return 'product';
	}

}