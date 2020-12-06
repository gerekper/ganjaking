<?php namespace Premmerce\WooCommercePinterest\Admin;

if (!class_exists('WC_Abstract_Privacy')) {
	return;
}

class WCPinterestPrivacy extends \WC_Abstract_Privacy {


	public function __construct() {
		parent::__construct(__('Pinterest for WooCommerce', 'woocommerce-pinterest'));
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		$content =
			'<p>' . __('Our site uses Pinterest feature "Save" button. When you use this feature, Pinterest collects log data from our site. <a href="https://help.pinterest.com/en/article/personalization-and-data">Learn more</a> about this feature.',
				'woocommerce-pinterest') . '</p>' .
			'<p>' . __('Our site shares information with Pinterest to measure or improve the performance of Pinterest ads or to figure out what kinds of ads to show you. This includes information about your visits to our site, your purchases, or information about your interests from a third-party service, which Pinterest might use to help show you ads. <a href="https://help.pinterest.com/en/article/personalized-ads-on-pinterest">Learn more</a> about the types of information advertisers or other info third-parties share with Pinterest .',
				'woocommerce-pinterest') . '</p>'
			. '<p>' . __('Please see the <a href="https://policy.pinterest.com/en/privacy-policy">Pinterest Privacy Policy</a> for more details.',
				'woocommerce-pinterest') . '</p>';

		return wpautop(wp_kses($content, array('a' => array('href' => array()), 'p' => array())));
	}

}
