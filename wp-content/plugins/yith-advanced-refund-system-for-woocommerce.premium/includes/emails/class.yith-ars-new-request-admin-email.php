<?php

if ( ! defined( 'YITH_WCARS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_ARS_New_Request_Admin_Email
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_ARS_New_Request_Admin_Email' ) ) {
	/**
	 * Class YITH_ARS_New_Request_Admin_Email
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_ARS_New_Request_Admin_Email extends WC_Email {

		public $email_body;
        public $request_id;

		public function __construct() {

			$this->id = 'yith_ywcars_new_request_admin_email';

			$this->title         = esc_html_x( 'YITH Advanced Refund System: New request - email for admin', 'Email descriptive title',
                'yith-advanced-refund-system-for-woocommerce' );
			$this->description   = esc_html__( 'The admin will receive an email when a new request is submitted.', 'yith-advanced-refund-system-for-woocommerce' );
			$this->heading       = esc_html__( 'New refund request received', 'yith-advanced-refund-system-for-woocommerce' );
			$this->subject       = esc_html__( 'New refund request received', 'yith-advanced-refund-system-for-woocommerce' );
			$this->email_body    = esc_html__( "Hi Admin, there's a new Refund Request ({request_number}) from user {customer_name} on order {order_number}. The Refund Request is for the following items: {items_table}", 'yith-advanced-refund-system-for-woocommerce' );
			$this->template_html = 'emails/ywcars-new-request-admin.php';

			add_action( 'ywcars_send_new_request_admin', array( $this, 'trigger' ) );

			parent::__construct();
            $this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
            $this->email_type = 'html';
		}

		public function trigger( $request_id ) {
            if ( ! $this->is_enabled() ) {
                return;
            }
            $request = null;
            if ( is_numeric( $request_id ) ) {
                $request = new YITH_Refund_Request( $request_id );
            }
            if ( ! ( $request instanceof YITH_Refund_Request ) ) {
                return;
            }
            $this->request_id = $request_id;

			$this->email_body = $this->get_option( 'email_body', esc_html__( 'Hi Admin, there\'s a new Refund Request ({request_number}) from user
			{customer_name} on order {order_number}.', 'yith-advanced-refund-system-for-woocommerce' ) );

            $this->send( $this->get_recipient(),
                $this->get_subject(),
                $this->get_content(),
                $this->get_headers(),
                $this->get_attachments() );
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
					                 . '<code>{customer_name}, {request_number}, {order_number}, {items_table}</code>',
					'placeholder' => '',
					'default'     => '',
				)
			);
		}

	}

}
return new YITH_ARS_New_Request_Admin_Email();
