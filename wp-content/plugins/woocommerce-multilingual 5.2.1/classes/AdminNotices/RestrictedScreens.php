<?php

namespace WCML\AdminNotices;

use WPML\FP\Obj;

class RestrictedScreens {

	/**
	 * @return string[]
	 */
	public static function get() {
		return [
			'dashboard',
			'woocommerce_page_wpml-wcml',
			'woocommerce_page_wc-admin',
			'woocommerce_page_wc-reports',
			'woocommerce_page_wc-settings',
			'woocommerce_page_wc-status',
			'woocommerce_page_wc-addons',
			'edit-shop_order',
			'edit-shop_coupon',
			'edit-product',
		];
	}
}
