<?php

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;

class WoocommerceProductFeedsFeedSetupTask extends Task {

	public function get_id() {
		return 'woocommerce-gpf-feed-setup';
	}

	public function get_title() {
		return __( 'WooCommerce Product Feeds: Set up feed in Google Merchant Centre', 'woocommerce_gpf' );
	}

	public function get_content() {
		return __(
			'Submit your feed to Google so they import your product data.',
			'woocommerce_gpf'
		);
	}

	public function get_time() {
		return _x( '5 minutes', 'Estimated time to complete setup task', 'woocommerce_gpf' );
	}
}
