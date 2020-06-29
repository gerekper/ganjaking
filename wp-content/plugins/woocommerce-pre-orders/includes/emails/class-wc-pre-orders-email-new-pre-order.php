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
 * New Pre-Order Email
 *
 * An email sent to the admin when a new pre-order is received
 *
 * @since 1.0
 */
class WC_Pre_Orders_Email_New_Pre_Order extends WC_Email {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		global $wc_pre_orders;

		$this->id             = 'wc_pre_orders_new_pre_order';
		$this->title          = __( 'New pre-order', 'wc-pre-orders' );
		$this->description    = __( 'New pre-order emails are sent when a pre-order is received.', 'wc-pre-orders' );

		$this->heading        = __( 'New customer pre-order', 'wc-pre-orders' );
		$this->subject        = __( '[{site_title}] New customer pre-order ({order_number}) - {order_date}', 'wc-pre-orders' );

		$this->template_base  = $wc_pre_orders->get_plugin_path() . '/templates/';
		$this->template_html  = 'emails/admin-new-pre-order.php';
		$this->template_plain = 'emails/plain/admin-new-pre-order.php';

		// Triggers for this email
		add_action( 'wc_pre_order_status_new_to_active_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient' );

		if ( ! $this->recipient )
			$this->recipient = get_option( 'admin_email' );
	}


	/**
	 * Dispatch the email
	 *
	 * @since 1.0
	 */
	public function trigger( $order_id ) {

		if ( $order_id ) {
			$pre_wc_30       = version_compare( WC_VERSION, '3.0', '<' );

			$this->object    = new WC_Order( $order_id );

			$this->find[]    = '{order_date}';
			$this->replace[] = date_i18n( wc_date_format(), strtotime( $pre_wc_30 ? $this->object->order_date : ( $this->object->get_date_created() ? gmdate( 'Y-m-d H:i:s', $this->object->get_date_created()->getOffsetTimestamp() ) : '' ) ) );

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
		global $wc_pre_orders;
		ob_start();
		wc_get_template(
			$this->template_html,
			array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'plain_text'    => false,
				'email'         => $this,
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
	function get_content_plain() {
		global $wc_pre_orders;
		ob_start();
		wc_get_template(
			$this->template_plain,
			array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'plain_text'    => true
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}


	/**
	 * Initialise Settings Form Fields
	 *
	 * @since 1.0
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'wc-pre-orders' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'wc-pre-orders' ),
				'default' => 'yes',
			),
			'recipient' => array(
				'title'       => __( 'Recipient(s)', 'wc-pre-orders' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'wc-pre-orders' ), esc_attr( get_option('admin_email') ) ),
				'placeholder' => '',
				'default'     => '',
			),
			'subject' => array(
				'title'       => __( 'Subject', 'wc-pre-orders' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'wc-pre-orders' ), $this->subject ),
				'placeholder' => '',
				'default'     => '',
			),
			'heading' => array(
				'title'       => __( 'Email Heading', 'wc-pre-orders' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'wc-pre-orders' ), $this->heading ),
				'placeholder' => '',
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'wc-pre-orders' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'wc-pre-orders' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options' => array(
					'plain'     => __( 'Plain text', 'wc-pre-orders' ),
					'html'      => __( 'HTML', 'wc-pre-orders' ),
					'multipart' => __( 'Multipart', 'wc-pre-orders' ),
				),
			),
		);
	}
}
