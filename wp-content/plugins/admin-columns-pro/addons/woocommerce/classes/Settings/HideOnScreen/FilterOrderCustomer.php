<?php

namespace ACA\WC\Settings\HideOnScreen;

use ACP;

class FilterOrderCustomer extends ACP\Settings\ListScreen\HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_order_customer', __( 'Registered Customer', 'codepress-admin-columns' ), ACP\Settings\ListScreen\HideOnScreen\Filters::NAME );
	}

}