<?php

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

class WoocommerceProductFeedsConfigureSettingsTask extends Task {

	public function get_id() {
		return 'woocommerce-gpf-configure-settings';
	}

	public function get_title() {
		return __( 'WooCommerce Product Feeds: Configure feed settings', 'woocommerce_gpf' );
	}

	public function get_content() {
		return __(
			'Choose what information you want to send to Google, and how to map it from your products.',
			'woocommerce_gpf'
		);
	}

	public function get_time() {
		return _x( '5 minutes', 'Estimated time to complete setup task', 'woocommerce_gpf' );
	}
}
