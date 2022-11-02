<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/Email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Admin Pre-Order Cancelled Email
 *
 * An email sent to the admin when a pre-order is cancelled.
 */
class WC_Pre_Orders_Email_Admin_Pre_Order_Cancelled extends WC_Email {

	/** @var string optional message to include in email */
	private $message;


	/**
	 * Constructor
	 */
	public function __construct() {

		global $wc_pre_orders;

		$this->id          = 'wc_pre_orders_admin_pre_order_cancelled';
		$this->title       = __( 'Pre-order Cancelled (Admin)', 'wc-pre-orders' );
		$this->description = __( 'This is an order notification sent to the admin after a pre-order is cancelled.', 'wc-pre-orders' );

		$this->heading = __( 'Pre-order Cancelled', 'wc-pre-orders' );
		$this->subject = __( '[{site_title}] pre-order from {order_date} has been cancelled', 'wc-pre-orders' );

		$this->template_base  = $wc_pre_orders->get_plugin_path() . '/templates/';
		$this->template_html  = 'emails/admin-pre-order-cancelled.php';
		$this->template_plain = 'emails/plain/admin-pre-order-cancelled.php';

		// Triggers for this email
		add_action( 'wc_pre_order_status_active_to_cancelled_notification', array( $this, 'trigger' ), 10, 2 );

		// Call parent constructor
		parent::__construct();
	}


	/**
	 * Dispatch the email
	 */
	public function trigger( $order_id, $message ) {

		if ( $order_id ) {
			$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );

			$this->object    = new WC_Order( $order_id );
			$this->recipient = get_option( 'admin_email' );
			$this->message   = $message;

			$this->find[]    = '{order_date}';
			$this->replace[] = date_i18n( wc_date_format(), strtotime( $pre_wc_30 ? $this->object->order_date : ( $this->object->get_date_created() ? gmdate( 'Y-m-d H:i:s', $this->object->get_date_created()->getOffsetTimestamp() ) : '' ) ) );

			$this->find[]    = '{release_date}';
			$this->replace[] = WC_Pre_Orders_Product::get_localized_availability_date( WC_Pre_Orders_Order::get_pre_order_product( $this->object ) );

			$this->find[]    = '{order_number}';
			$this->replace[] = $this->object->get_order_number();
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}


	/**
	 * Gets the email HTML content
	 *
	 * @return string the email HTML content
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template(
			$this->template_html,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'message'            => $this->message,
				'plain_text'         => false,
				'email'              => $this,
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}


	/**
	 * Gets the email plain content
	 *
	 * @return string the email plain content
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template(
			$this->template_plain,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'message'            => $this->message,
				'plain_text'         => true,
				'email'              => $this,
				'sent_to_admin'      => true,
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}
}
