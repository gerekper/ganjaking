<?php

namespace ACA\WC\Settings\HideOnScreen;

use ACP;

class FilterSubscriptionCustomer extends ACP\Settings\ListScreen\HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_subscription_customer', __( 'Customer', 'codepress-admin-columns' ), ACP\Settings\ListScreen\HideOnScreen\Filters::NAME );
	}

}