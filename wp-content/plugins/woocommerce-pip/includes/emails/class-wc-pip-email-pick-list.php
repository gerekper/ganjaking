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
 * PIP Pick List Email
 *
 * Pick List emails can be sent manually to designated lists of addresses.
 *
 * @since 3.0.0
 */
class WC_PIP_Email_Pick_List extends \WC_Email {


	/** @var string $document_type WC_PIP_Document type for this email */
	protected $document_type = '';


	/**
	 * Email constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		$this->id             = 'pip_email_pick_list';
		$this->document_type  = 'pick-list';
		$this->title          = __( 'Pick List', 'woocommerce-pip' );
		$this->description    = __( 'Pick list emails can be manually sent via order bulk action to the designated recipients.', 'woocommerce-pip' );
		/* translators: Placeholders: %1$s - merge tag for the shop name, %2$s merge tag number of orders included in the document */
		$this->subject        = $this->get_option( 'subject', sprintf( __( '[%1$s] Pick List for %2$s', 'woocommerce-pip' ), '{site_title}', '{orders}' ) );
		// leave these blank to use our common template in templates/pip
		$this->template_html  = '';
		$this->template_plain = '';
		// this is only manually sent as a bulk action
		$this->manual         = true;

		// triggers
		add_action( 'wc_pip_pick_list_email_trigger', array( $this, 'trigger' ), 10, 2 );
		add_action( 'wc_pip_send_email_pick_list',    array( $this, 'trigger' ), 10, 2 );

		// call parent constructor
		parent::__construct();

		// enforce HTML emails only
		$this->email_type = $this->get_email_type();
		$this->recipient  = $this->get_option( 'recipient', get_option( 'admin_email' ) );
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
		 * Filter the pick list email subject.
		 *
		 * @since 3.0.0
		 * @param string $subject The email subject
		 * @param \WC_PIP_Document $object The document object
		 */
		return apply_filters( 'wc_pip_pick_list_email_subject', $this->format_string( $this->subject ), $this->object );
	}


	/**
	 * Is email enabled
	 *
	 * Overrides parent is_enabled() method
	 *
	 * @since 3.0.0
	 * @return true
	 */
	public function is_enabled() {
		return true;
	}


	/**
	 * Email settings form fields
	 *
	 * Overrides parent method
	 *
	 * @since 3.0.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(

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
				'default'     => '',
			),
		);
	}


	/**
	 * Email trigger
	 *
	 * @since 3.0.0
	 * @param \WC_PIP_Document $document Document object
	 */
	public function trigger( $document ) {

		// bail if the document is not valid or no order ids are passed, or there are no recipients
		if ( ! $document || ! isset( $document->order_ids ) || ! $document->order_ids || ! is_array( $document->order_ids ) || ! $this->get_recipient() ) {
			return;
		}

		// set the document object
		$this->object = $document;

		$orders_count = count( $document->order_ids );

		// replace merge tags
		$this->find['orders']    = '{orders}';
		/* translators: Placeholder: %d orders count */
		$this->replace['orders'] = sprintf( _n( '%d order', '%d orders', $orders_count, 'woocommerce-pip' ), $orders_count );

		// send mail
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		/** This action is documented in includes/emails/class-wc-pip-email-invoice.php */
		do_action( 'wc_pip_send_email', $document->type, $document->order_id, $document->order_ids );
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


}
