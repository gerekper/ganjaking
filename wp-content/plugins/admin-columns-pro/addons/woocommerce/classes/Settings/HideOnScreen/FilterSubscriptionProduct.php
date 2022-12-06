<?php

namespace ACA\WC\Settings\HideOnScreen;

use ACP;

class FilterSubscriptionProduct extends ACP\Settings\ListScreen\HideOnScreen {

	const NAME = 'hide_filter_subscription_products';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Product', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ ACP\Settings\ListScreen\HideOnScreen\Filters::NAME ];
	}

}