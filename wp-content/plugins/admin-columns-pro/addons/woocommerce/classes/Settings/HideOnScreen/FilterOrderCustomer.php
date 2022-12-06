<?php

namespace ACA\WC\Settings\HideOnScreen;

use ACP;

class FilterOrderCustomer extends ACP\Settings\ListScreen\HideOnScreen {

	const NAME = 'hide_filter_order_customer';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Registered Customer', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ ACP\Settings\ListScreen\HideOnScreen\Filters::NAME ];
	}

}