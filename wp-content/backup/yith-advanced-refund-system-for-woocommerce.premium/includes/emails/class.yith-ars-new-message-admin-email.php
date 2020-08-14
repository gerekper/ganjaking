<?php

if ( ! defined( 'YITH_WCARS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_ARS_New_Message_Admin_Email
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_ARS_New_Message_Admin_Email' ) ) {
	/**
	 * Class YITH_ARS_New_Message_Admin_Email
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_ARS_New_Message_Admin_Email extends WC_Email {

		public $email_body;
		public $message_id;
		public $request_id;

		public function __construct() {
			$this->id            = 'yith_ywcars_new_message_admin_email';
			$this->title         = esc_html_x( 'YITH Advanced Refund System: New message - email for admin', 'Email descriptive title',
                'yith-advanced-refund-system-for-woocommerce' );
			$this->description   = esc_html__( 'The admin will receive an email when a customer submits a new message.',
                'yith-advanced-refund-system-for-woocommerce' );
			$this->heading       = esc_html__( 'New message from customer', 'yith-advanced-refund-system-for-woocommerce' );
			$this->subject       = esc_html__( 'New message from {customer_name}', 'yith-advanced-refund-system-for-woocommerce' );
			$this->email_body    = esc_html__( 'Hi admin! New message from {customer_name}.', 'yith-advanced-refund-system-for-woocommerce' );
			$this->template_html = 'emails/ywcars-new-message.php';

			add_action( 'ywcars_new_message_from_user_to_admin', array( $this, 'trigger' ) );
			add_filter( 'woocommerce_email_styles', array( $this, 'style' ) );

			parent::__construct();
			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
			$this->email_type = 'html';
		}

		public function trigger( $message_id ) {
			if ( ! $this->is_enabled() ) {
				return;
			}
			$message = null;
			if ( is_numeric( $message_id ) ) {
				$message = new YITH_Request_Message( $message_id );
			}
			if ( ! ( $message instanceof YITH_Request_Message ) ) {
				return;
			}
			$request = null;
			if ( is_numeric( $message->request ) ) {
				$request = new YITH_Refund_Request( $message->request );
			}
			if ( ! $request->exists() ) {
				return;
			}
			$this->message_id = $message->ID;
			$this->request_id = $request->ID;
			$customer         = new WP_User( $request->customer_id );
			$this->subject    = str_replace( '{customer_name}', ucwords( $customer->display_name ), $this->subject );
			$this->email_body = $this->get_option( 'email_body', esc_html__( 'Hi admin! New message from {customer_name}.', 'yith-advanced-refund-system-for-woocommerce' ) );

			$this->send( $this->get_recipient(),
				$this->get_subject(),
				$this->get_content(),
				$this->get_headers(),
				$this->get_attachments() );
		}

		public function style( $style ) {
			$style = $style . " .ywcars_refund_info_message_box {
	background-color: #f1f1f1;
    margin-bottom: 30px;
    border-color: #dddddd;
    border-width: 1px;
    border-style: solid;
    padding: 27px;
    width: auto;
}
.ywcars_refund_info_message_author {
    display: inline-block;
    font-size: 12pt;
    font-weight: 600;
}
.ywcars_refund_info_message_date {
    font-size: 8pt;
    float: right;
}

.ywcars_refund_info_message_body {
    font-size: 10pt;
    margin: 20px 0;
}
.ywcars_attachments_line_separator {
    height:1px;
    background-color: #dddddd;
    margin-bottom: 12px;
    margin-top: 20px;
}

.ywcars_attachments_title {
    font-size: 12px;
    font-weight: bold;
}

.ywcars_single_attachment {
    display: inline-block;
    width: 140px;
    height: auto;
    margin-right: 5px;
    margin-top: 12px;
    font-size: 9px;
}

.ywcars_attachment_thumbnail {
    border: 1px solid black;
    border-radius: 3px;
    width: 140px;
}";
			return $style;
		}

		public function get_content_html() {
			return wc_get_template_html( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => true,
				'plain_text'    => false,
				'email'         => $this
			),
				'',
				YITH_WCARS_TEMPLATE_PATH );
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => esc_html__( 'Enable/Disable', 'yith-advanced-refund-system-for-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Enable this email notification', 'yith-advanced-refund-system-for-woocommerce' ),
					'default' => 'yes'
				),
				'recipient' => array(
					'title'         => esc_html__( 'Recipient(s)', 'yith-advanced-refund-system-for-woocommerce' ),
					'type'          => 'text',
					'description'   => sprintf( esc_html__( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'yith-advanced-refund-system-for-woocommerce' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
					'placeholder'   => '',
					'default'       => '',
					'desc_tip'      => true,
				),
				'subject'    => array(
					'title'       => esc_html__( 'Subject', 'yith-advanced-refund-system-for-woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( esc_html__( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'yith-advanced-refund-system-for-woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => ''
				),
				'heading'    => array(
					'title'       => esc_html__( 'Email Heading', 'yith-advanced-refund-system-for-woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( esc_html__( 'This controls the main heading in the email notification. Leave blank to use the default heading: <code>%s</code>.', 'yith-advanced-refund-system-for-woocommerce' ), $this->heading ),
					'placeholder' => '',
					'default'     => ''
				),
				'email_body' => array(
					'title'       => esc_html__( 'Email Body', 'yith-advanced-refund-system-for-woocommerce' ),
					'type'        => 'textarea',
					'description' => sprintf( esc_html__( 'Defaults to <code>%s</code>', 'yith-advanced-refund-system-for-woocommerce' ), $this->email_body )
					                 . '<br>' . esc_html_x( 'You can use the following placeholders:', 'yith-advanced-refund-system-for-woocommerce' )
					                 . '<code>{customer_name}, {request_number}, {order_number}</code>',
					'placeholder' => '',
					'default'     => '',
				)
			);
		}

	}

}
return new YITH_ARS_New_Message_Admin_Email();
