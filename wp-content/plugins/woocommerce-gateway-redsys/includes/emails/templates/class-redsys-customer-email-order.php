<?php
/**
 * Redsys Customer Email Order
 *
 * An email sent to the customer when there was a Card payment problem.
 *
 * @package WooCommerce Redsys Gateway
 * @since 1.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'WC_Email' ) ) {
	return;
}
/**
 * Class WC_Customer_Cancel_Order
 */

/**
 * Copyright: (C) 2013 - 2024 José Conti
 */
class Redsys_Customer_Email_Order extends WC_Email {
	/**
	 * Create an instance of the class.
	 *
	 * @return void
	 */

	/**
	 * Copyright: (C) 2013 - 2024 José Conti
	 */
	public function __construct() {
		// Email slug we can use to filter other data.
		$this->id          = 'redsys_customer_email_order';
		$this->title       = __( 'Card payment problem', 'woocommerce-redsys' );
		$this->description = __( 'An email sent to the customer when there was a Card payment problem.', 'woocommerce-redsys' );

		// For admin area to let the user know we are sending this email to customers.
		$this->customer_email = true;
		$this->heading        = __( 'Card payment problem', 'woocommerce-redsys' );
		// translators: placeholder is {blogname}, a variable that will be substituted when email is sent out.
		$this->subject = sprintf( _x( 'Card payment problem at [%s] ', 'default email subject for cancelled emails sent to the customer', 'woocommerce-redsys' ), '{blogname}' );

		// Template paths.
		$this->template_html  = 'html/redsys-html-customer-problem.php';
		$this->template_plain = 'plain/redsys-plain-customer-problem.php';
		$this->template_base  = REDSYS_PLUGIN_PATH_P . 'includes/emails/templates/';

		// Action to which we hook onto to send the email.
		add_action( 'redsys_sent_email_payment_error_to_customers', array( $this, 'trigger' ) );
		WC_Email::__construct();
	}

	/**
	 * Function that will send this email to the customer.
	 *
	 * @param int $order_id Order ID.
	 */
	public function trigger( $order_id ) {
		$this->setup_locale();

		$this->object    = wc_get_order( $order_id );
		$order_email     = $this->object->get_billing_email();
		$this->recipient = $this->object->get_billing_email();
		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}
		if ( $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
		$this->restore_locale();
	}

	/**
	 * Get content html.
	 *
	 * @return string
	 */

	/**
	 * Copyright: (C) 2013 - 2024 José Conti
	 */
	public function get_content_html() {
		return wc_get_template_html(
			$this->template_html,
			array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,
			),
			'',
			$this->template_base
		);
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */

	/**
	 * Copyright: (C) 2013 - 2024 José Conti
	 */
	public function get_content_plain() {
		return wc_get_template_html(
			$this->template_plain,
			array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			),
			'',
			$this->template_base
		);
	}
}
