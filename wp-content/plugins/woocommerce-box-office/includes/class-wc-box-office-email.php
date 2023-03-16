<?php
/**
 * WC Box Office Email
 *
 * @package woocommerce-box-office
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Box Office Email
 */
class WC_Box_Office_Email extends WC_Email {

	/**
	 * Email message
	 *
	 * @var string
	 */
	public $message;

	/**
	 * Custom heading to override
	 *
	 * @var string
	 */
	public $custom_heading = '';

	/**
	 * Set email defaults
	 */
	public function __construct() {

		$this->id             = 'wc_box_office_email';
		$this->customer_email = true;
		$this->title          = __( 'Box Office Email', 'woocommerce-box-office' );
		$this->template_base  = WCBO()->dir . 'templates/';
		$this->template_html  = 'emails/box-office.php';
		$this->template_plain = 'emails/plain/box-office.php';

		$this->placeholders = array(
			'{site_title}' => $this->get_blogname(),
		);

		parent::__construct();
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return __( 'Box Office Purchase Information', 'woocommerce-box-office' );
	}

	/**
	 * Get email default heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Ticket Details', 'woocommerce-box-office' );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_heading() {
		if ( ! empty( $this->custom_heading ) ) {
			return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->custom_heading ), $this->object, $this );
		}

		return parent::get_heading();
	}

	/**
	 * Prepares email content and triggers the transactional email
	 *
	 * @param string|array $to Recipient.
	 * @param string       $subject Email subject.
	 * @param string       $message Email message.
	 * @param string       $custom_heading Email heading to override. Default empty string to use site-wide setting.
	 * @return void
	 */
	public function trigger( $to, $subject, $message, $custom_heading = '' ) {

		$this->recipient = $to;
		if ( is_array( $this->recipient ) ) {
			$this->recipient = join( ',', $this->recipient );
		}

		// Use product-level subject or a global setting if empty.
		if ( ! empty( $subject ) ) {
			$email_subject = apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $subject ), $this->object, $this );
		} else {
			$email_subject = $this->get_subject();
		}

		if ( ! empty( $custom_heading ) ) {
			$this->custom_heading = $custom_heading;
		}

		$this->message = $message;

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		return $this->send( $this->get_recipient(), $email_subject, $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Returns HTML email content.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html(
			$this->template_html,
			array(
				'email_heading' => $this->get_heading(),
				'email_message' => $this->message,
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,
			),
			$this->template_base,
			$this->template_base
		);
	}

	/**
	 * Returns plain email content.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html(
			$this->template_plain,
			array(
				'email_heading' => $this->get_heading(),
				'email_message' => wp_strip_all_tags( $this->message ),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			),
			$this->template_base,
			$this->template_base
		);
	}

	/**
	 * Initialize settings form fields
	 */
	public function init_form_fields() {

		// Using get_option() here since the $enabled variable is not assigned yet when init_form_fields() is spawned.
		$enabled = 'yes' === $this->get_option( 'enabled' );
		$label   = $enabled ? __( 'Enabled', 'woocommerce-box-office' ) : __( 'Disabled', 'woocommerce-box-office' );

		$this->form_fields = array(
			'enabled'    => array(
				'title'       => __( 'Enable/Disable', 'woocommerce' ),
				'type'        => 'checkbox',
				'label'       => 'Enable this email notification',
				'description' => sprintf(
					// translators: link to Box Office settings page.
					__( 'This enables Box Office emails globally.', 'woocommerce-box-office' ),
					esc_url( admin_url( 'admin.php?page=wc-settings&tab=box_office' ) )
				),
				'default'     => 'yes',
			),
			'subject'    => array(
				'title'       => __( 'Email Subject', 'woocommerce' ),
				'type'        => 'text',
				/* translators: default email subject */
				'description' => sprintf( __( 'This controls the subject line. Will be used if email subject for the ticket product is empty. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-box-office' ), $this->get_default_subject() ),
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'This controls the main heading contained within the email notification. Leave blank to skip.', 'woocommerce-box-office' ),
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}
}
