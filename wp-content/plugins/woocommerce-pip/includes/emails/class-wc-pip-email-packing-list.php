<?php
/**
 * WooCommerce Print Invoices/Packing Lists
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * PIP Packing List Email
 *
 * Packing Lists can be sent by email to designated recipients when an order is paid
 *
 * @since 3.0.0
 */
class WC_PIP_Email_Packing_List extends \WC_Email {


	/** @var string $document_type WC_PIP_Document type for this email */
	protected $document_type = '';


	/**
	 * Email constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		$this->id             = 'pip_email_packing_list';
		$this->document_type  = 'packing-list';
		$this->title          = __( 'Packing List', 'woocommerce-pip' );
		$this->description    = __( 'If enabled, packing list emails are sent to designated recipients when an order needs to be processed.', 'woocommerce-pip' );
		/* translators: Placeholders: %1$s - merge tag for the shop name, %2$s merge tag for generated invoice number, %3$s merge tag for order number, %4$s merge tag for date of the order */
		$this->subject        = $this->get_option( 'subject', sprintf( __( '[%1$s] Packing List for invoice %2$s - order %3$s from %4$s', 'woocommerce-pip' ), '{site_title}', '{invoice_number}', '{order_number}', '{order_date}' ) );
		// Leave these blank to use our common template in templates/pip
		$this->template_html  = '';
		$this->template_plain = '';

		// triggers
		add_action( 'wc_pip_packing_list_email_trigger', array( $this, 'trigger' ) );
		add_action( 'wc_pip_send_email_packing_list',    array( $this, 'trigger' ) );

		// trigger on order status changes
		$action_hooks = $this->get_trigger_actions();
		if ( ! empty( $action_hooks ) ) {
			foreach ( $action_hooks as $trigger ) {
				add_action( $trigger, array( $this, 'trigger' ) );
			}
		}

		// call parent constructor
		parent::__construct();

		// enforce HTML emails only
		$this->email_type = $this->get_email_type();

		// get recipient (defaults to admin)
		$this->recipient  = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}


	/**
	 * Checks whether the email is enabled.
	 *
	 * Returns true if manually sending an invoice from the orders admin screens.
	 *
	 * @since 3.5.0
	 *
	 * @return bool
	 */
	public function is_enabled() {

		if ( did_action( 'wc_pip_sending_manual_order_email' ) || doing_action( 'wc_pip_sending_manual_order_email' ) ) {
			$is_enabled = true;
		} else {
			$is_enabled = parent::is_enabled();
		}

		return $is_enabled;
	}


	/**
	 * Is customer email
	 *
	 * @since 3.0.0
	 * @return false
	 */
	public function is_customer_email() {
		return false;
	}


	/**
	 * Get email type
	 *
	 * Override parent method to return html emails only
	 *
	 * @return string
	 */
	public function get_email_type() {
		return 'html';
	}


	/**
	 * Get subject
	 *
	 * Overrides parent method with new filter
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_subject() {

		/**
		 * Filter the packing list email subject.
		 *
		 * @since 3.0.0
		 * @param string $subject The email subject
		 * @param \WC_PIP_Document $document The document object
		 */
		return apply_filters( 'wc_pip_packing_list_email_subject', $this->format_string( $this->subject ), $this->object );
	}


	/**
	 * Email settings form fields
	 *
	 * Overrides parent method
	 *
	 * @since 3.0.0
	 */
	public function init_form_fields() {

		$this->form_fields    = array(

			'enabled'         => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-pip' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable this email notification', 'woocommerce-pip' ),
				'default'     => 'yes'
			),

			'recipient' => array(
				'title'         => __( 'Recipient(s)', 'woocommerce-pip' ),
				'type'          => 'textarea',
				'description'   => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to admin email: <code>%s</code>.', 'woocommerce-pip' ), esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder'   => '',
				'default'       => get_bloginfo( 'admin_email' ),
			),

			'subject'         => array(
				'title'       => __( 'Subject', 'woocommerce-pip' ),
				'type'        => 'text',
				/* translators: Placeholder: %s - Default email subject */
				'description' => sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce-pip' ), $this->subject ),
				'placeholder' => '',
				'default'     => $this->get_subject(),
			),
		);
	}


	/**
	 * Email trigger
	 *
	 * @see \WC_PIP_Document::send_email()
	 * @see \WC_PIP_Email_Packing_List::__construct()
	 *
	 * @since 3.0.0
	 * @param null|int|WC_Order|\WC_PIP_Document Email object passed by hooks
	 */
	public function trigger( $object ) {

		// if not a PIP document, grab the order first
		if ( is_int( $object ) || $object instanceof \WC_Order ) {

			$wc_order = wc_get_order( $object );

			// sanity check, bail out early if we still don't have an order to begin with
			if ( ! $wc_order ) {
				return;
			}

			// now get the PIP document
			$object = wc_pip()->get_document( 'packing-list', array( 'order' => $wc_order ) );
		}

		// No need to send mail for empty order, i.e., for virtual products if exclude virtual setting is on.
		if ( 0 === $object->get_items_count() ) {
			return;
		}

		// bail if there's no document, the email is not enabled or there's no valid recipient
		if ( ! $object || ! isset( $object->order ) || ! $object->order || ! $this->get_recipient() || ! $this->is_enabled() ) {
			return;
		}

		// set the document as the email object
		$this->object = $object;

		if ( isset( $object->order ) && $object->order instanceof \WC_Order ) {
			$order_timestamp = ( $date_created = $object->order->get_date_created( 'edit' ) ) ? $date_created->getOffsetTimestamp() : 0;
			$order_number    = $object->order->get_order_number();
		}

		// replace merge tags
		$this->find['order-date']        = '{order_date}';
		$this->find['order-number']      = '{order_number}';
		$this->find['invoice-number']    = '{invoice_number}';
		$this->replace['order-date']     = ! empty( $order_timestamp ) ? date_i18n( wc_date_format(), $order_timestamp ) : '';
		$this->replace['order-number']   = ! empty( $order_number ) ? $order_number : '';
		$this->replace['invoice-number'] = $this->object instanceof \WC_PIP_Document ? $this->object->get_invoice_number() : '';

		// send mail
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		/** this action is documented in includes/emails/class-wc-pip-email-invoice.php */
		do_action( 'wc_pip_send_email', $this->object->type, $this->object->order_id, $this->object->order_ids );
	}


	/**
	 * Get email template content HTML
	 *
	 * @since 3.0.0
	 * @return string HTML
	 */
	public function get_content_html() {

		if ( ! $this->object instanceof \WC_PIP_Document ) {
			return '';
		}

		ob_start();

		$this->object->output_template( array( 'action' => 'send_email' ) );

		return ob_get_clean();
	}


	/**
	 * Get actions for send email triggers
	 *
	 * @since 3.1.1
	 * @return string[] Array of action hook names.
	 */
	private function get_trigger_actions() {

		$actions = array(
			'woocommerce_order_status_failed_to_processing_notification',
			'woocommerce_order_status_failed_to_completed_notification',
			'woocommerce_order_status_pending_to_processing_notification',
			'woocommerce_order_status_pending_to_completed_notification',
			'woocommerce_order_status_on-hold_to_processing_notification',
			'woocommerce_order_status_on-hold_to_completed_notification',
		);

		/** this filter is documented in includes/emails/class-wc-pip-email-invoice.php */
		return apply_filters( 'wc_pip_invoice_email_order_status_change_trigger_actions', $actions, $this );
	}


}
