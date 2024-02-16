<?php
/**
 * Class Custom_WC_Email
 *
 * @package WooCommerce Redsys Gateway
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

/**
 * Class Redsys_WC_Email
 */
class Redsys_WC_Email {
	/**
	 * Custom_WC_Email constructor.
	 */
	public function __construct() {
		// Filtering the emails and adding our own email.
		add_action( 'woocommerce_email_classes', array( $this, 'register_email' ), 90, 1 );
	}
	/**
	 * Register email
	 *
	 * @param array $emails Emails.
	 */
	public function register_email( $emails ) {
		require_once REDSYS_PLUGIN_PATH_P . 'includes/emails/templates/class-redsys-customer-email-order.php';
		$emails['Redsys_Customer_Email_Order'] = new Redsys_Customer_Email_Order();
		return $emails;
	}
}
new Redsys_WC_Email();
