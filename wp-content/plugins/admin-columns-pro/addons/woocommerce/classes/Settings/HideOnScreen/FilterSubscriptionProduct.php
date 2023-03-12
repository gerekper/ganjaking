<?php

namespace ACA\WC\Settings\HideOnScreen;

use ACP;

class FilterSubscriptionProduct extends ACP\Settings\ListScreen\HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_subscription_products', __( 'Product', 'codepress-admin-columns' ), ACP\Settings\ListScreen\HideOnScreen\Filters::NAME );
	}

}