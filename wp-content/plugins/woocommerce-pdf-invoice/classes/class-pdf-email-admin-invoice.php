<?php
/**
 * Class PDF_Invoice_Admin_PDF_Invoice file.
 *
 * @package PDF Invoice\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'PDF_Invoice_Admin_PDF_Invoice', false ) ) :

	/**
	 * Customer Invoice.
	 *
	 * An email sent to the customer via admin.
	 *
	 * @class       PDF_Invoice_Admin_PDF_Invoice
	 * @package     WooCommerce/Classes/Emails
	 * @extends     WC_Email
	 */
	class PDF_Invoice_Admin_PDF_Invoice extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'pdf_admin_invoice';
			$this->title          = __( 'Admin PDF invoice', 'woocommerce-pdf-invoice' );
			$this->description    = __( 'This email, if enabled, will send a one time email to the address specified at the point the invoice is created.', 'woocommerce-pdf-invoice' );
			$this->template_html  = 'emails/pdf-admin-invoice.php';
			$this->template_plain = 'emails/plain/pdf-admin-invoice.php';
			$this->template_base  = PDFPLUGINPATH . 'templates/';
			$this->placeholders   = array(
				'{order_date}'   => '',
				'{order_number}' => '',
			);

			// Call parent constructor.
			parent::__construct();

			// We want all the parent's methods, with none of its properties, so call its parent's constructor, rather than my parent constructor
			WC_Email::__construct();

			// Other settings.
			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );

		}

		/**
		 * Get default recipient.
		 *
		 * @return string
		 */
		public function get_default_recipient() {
			return get_option( 'admin_email' );
		}

		/**
		 * Get email subject.
		 *
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Your invoice for order #{order_number} on {site_title} is attached', 'woocommerce-pdf-invoice' );
		}

		/**
		 * Get email heading.
		 *
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Your invoice for order #{order_number} is attached', 'woocommerce-pdf-invoice' );
		}

		/**
		 * Get email subject.
		 *
		 * @return string
		 */
		public function get_subject() {
			$subject = $this->get_option( 'subject', $this->get_default_subject() );
			return $this->format_string( $subject );
		}

		/**
		 * Get email heading.
		 *
		 * @return string
		 */
		public function get_heading() {
			$heading = $this->get_option( 'heading', $this->get_default_heading() );
			return $this->format_string( $heading );
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'Thanks for using {site_address}!', 'woocommerce-pdf-invoice' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int      $order_id The order ID.
		 * @param WC_Order $order Order object.
		 */
		public function trigger( $order_id, $order = false ) {

			$this->setup_locale();

			if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
				$order = wc_get_order( $order_id );
			}

			if ( is_a( $order, 'WC_Order' ) ) {
				$this->object                         = $order;
				$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{order_number}'] = $this->object->get_order_number();

				$email_already_sent = $order->get_meta( '_admin_pdf_email_sent' );
			}

			if ( 'true' === $email_already_sent ) {
				return;
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

				$order->update_meta_data( '_admin_pdf_email_sent', 'true' );
				$order->save();
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email'              => $this,
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
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => true,
					'plain_text'         => true,
					'email'              => $this,
				),
				'',
				$this->template_base
			);
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			/* translators: %s: list of placeholders */
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'woocommerce-pdf-invoice' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
			$this->form_fields = array(
				'enabled'            => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => 'no',
				),
				'recipient'   	  => array(
					'title'       => __( 'Recipient', 'woocommerce-pdf-invoice' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'woocommerce-pdf-invoice' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
					'placeholder' => '',
					'default'     => esc_attr( get_option( 'admin_email' ) ),
				),
				'subject'      => array(
					'title'       => __( 'Subject', 'woocommerce-pdf-invoice' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'      => array(
					'title'       => __( 'Email heading', 'woocommerce-pdf-invoice' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'subject_paid' => array(
					'title'       => __( 'Subject (paid)', 'woocommerce-pdf-invoice' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject( true ),
					'default'     => '',
				),
				'heading_paid' => array(
					'title'       => __( 'Email heading (paid)', 'woocommerce-pdf-invoice' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading( true ),
					'default'     => '',
				),
				'additional_content' => array(
					'title'       => __( 'Additional content', 'woocommerce-pdf-invoice' ),
					'description' => __( 'Text to appear below the main email content.', 'woocommerce-pdf-invoice' ) . ' ' . $placeholder_text,
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( 'N/A', 'woocommerce-pdf-invoice' ),
					'type'        => 'textarea',
					'default'     => $this->get_default_additional_content(),
					'desc_tip'    => true,
				),
				'email_type'   => array(
					'title'       => __( 'Email type', 'woocommerce-pdf-invoice' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce-pdf-invoice' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}

endif;

return new PDF_Invoice_Admin_PDF_Invoice();