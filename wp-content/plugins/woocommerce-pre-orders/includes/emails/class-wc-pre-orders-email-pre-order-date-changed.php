<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package     WC_Pre_Orders/Email
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Customer Pre-Order Date Changed Email
 *
 * An email sent to the customer when a pre-order release date is changed
 *
 * @since 1.0
 */
class WC_Pre_Orders_Email_Pre_Order_Date_Changed extends WC_Email {


	/** @var string optional message to include in email */
	private $message;

	/** @var localized availability date for pre-order  */
	private $availability_date;


	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		global $wc_pre_orders;

		$this->id             = 'wc_pre_orders_pre_order_date_changed';
		$this->title          = __( 'Pre-order Release Date Changed', 'wc-pre-orders' );
		$this->description    = __( 'This is an order notification sent to the customer after a pre-order release date is changed.', 'wc-pre-orders' );

		$this->heading        = __( 'Pre-order Release Date Changed', 'wc-pre-orders' );
		$this->subject        = __( 'The release date for your {site_title} pre-order from {order_date} has been changed', 'wc-pre-orders' );

		$this->template_base  = $wc_pre_orders->get_plugin_path() . '/templates/';
		$this->template_html  = 'emails/customer-pre-order-date-changed.php';
		$this->template_plain = 'emails/plain/customer-pre-order-date-changed.php';

		// Triggers for this email
		add_action( 'wc_pre_orders_pre_order_date_changed_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}


	/**
	 * Dispatch the email
	 *
	 * @since 1.0
	 */
	public function trigger( $args ) {

		if ( ! empty( $args ) ) {

			$defaults = array(
				'order'   => '',
				'message' => ''
			);

			$args = wp_parse_args( $args, $defaults );

			extract( $args );

			if ( ! is_object( $order ) )
				return;

			$pre_wc_30       = version_compare( WC_VERSION, '3.0', '<' );

			$this->object    = $order;
			$this->recipient = $pre_wc_30 ? $this->object->billing_email : $this->object->get_billing_email();
			$this->message   = $message;
			$this->availability_date = WC_Pre_Orders_Product::get_localized_availability_date( WC_Pre_Orders_Order::get_pre_order_product( $this->object ) );

			$this->find[]    = '{order_date}';
			$this->replace[] = date_i18n( wc_date_format(), strtotime( $pre_wc_30 ? $this->object->order_date : ( $this->object->get_date_created() ? gmdate( 'Y-m-d H:i:s', $this->object->get_date_created()->getOffsetTimestamp() ) : '' ) ) );

			$this->find[]    = '{release_date}';
			$this->replace[] = $this->availability_date;

			$this->find[]    = '{order_number}';
			$this->replace[] = $this->object->get_order_number();
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}


	/**
	 * Gets the email HTML content
	 *
	 * @since 1.0
	 * @return string the email HTML content
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template(
			$this->template_html,
			array(
				'order'             => $this->object,
				'email_heading'     => $this->get_heading(),
				'message'           => $this->message,
				'availability_date' => $this->availability_date,
				'plain_text'        => false,
				'email'             => $this,
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}


	/**
	 * Gets the email plain content
	 *
	 * @since 1.0
	 * @return string the email plain content
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template(
			$this->template_plain,
			array(
				'order'             => $this->object,
				'email_heading'     => $this->get_heading(),
				'message'           => $this->message,
				'availability_date' => $this->availability_date,
				'plain_text'        => true
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}
}
