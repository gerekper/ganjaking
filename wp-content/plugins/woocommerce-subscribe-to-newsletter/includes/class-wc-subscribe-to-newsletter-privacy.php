<?php
/**
 * Newsletter Subscription Privacy
 *
 * @package WC_Newsletter_Subscription
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Newsletter_Subscription_Privacy' ) ) {
	include_once WC_NEWSLETTER_SUBSCRIPTION_PATH . 'includes/class-wc-newsletter-subscription-privacy.php';
}

/**
 * Class WC_Subscribe_To_Newsletter_Privacy
 *
 * @deprecated 3.0.0
 */
class WC_Subscribe_To_Newsletter_Privacy extends WC_Newsletter_Subscription_Privacy {

	/**
	 * Constructor.
	 */
	public function __construct() {
		wc_deprecated_function( 'WC_Subscribe_To_Newsletter_Privacy', '3.0.0', 'WC_Newsletter_Subscription_Privacy' );

		parent::__construct();
	}
}

new WC_Subscribe_To_Newsletter_Privacy();
