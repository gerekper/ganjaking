<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCARS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_ARS_Approved_User_Email
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_ARS_Approved_User_Email' ) ) {
	/**
	 * Class YITH_ARS_Approved_User_Email
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_ARS_Approved_User_Email extends WC_Email {

        public $email_body;
        public $request_id;

		public function __construct() {

			$this->id = 'yith_ywcars_approved_user_email';
			$this->customer_email = true;


			$this->title         = esc_html_x( 'YITH Advanced Refund System: Approved Refund Request email for user', 'Email descriptive title',
                'yith-advanced-refund-system-for-woocommerce' );
			$this->description   = esc_html__( 'The user will receive an email when a new request is submitted.', 'yith-advanced-refund-system-for-woocommerce' );
			$this->heading       = esc_html__( 'Your refund request has been approved!', 'yith-advanced-refund-system-for-woocommerce' );
			$this->subject       = esc_html__( 'Your refund request has been approved!', 'yith-advanced-refund-system-for-woocommerce' );
            $this->email_body    = esc_html__( 'Hi {customer_name}! Your refund request {request_number} for order {order_number} has been approved!', 'yith-advanced-refund-system-for-woocommerce' );
			$this->template_html = 'emails/ywcars-email-for-user.php';

			add_action( 'ywcars_send_approved_user', array( $this, 'trigger' ) );

			parent::__construct();
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
			$order = wc_get_order( $request->order_id );
			$this->recipient = $order instanceof WC_Data ? $order->get_billing_email() : $order->billing_email;

            $this->email_body = $this->get_option( 'email_body', esc_html__( 'Hi {customer_name}! Your refund request {request_number} for order {order_number} has been approved!', 'yith-advanced-refund-system-for-woocommerce' ) );

			$this->send( $this->get_recipient(),
				$this->get_subject(),
				$this->get_content(),
				$this->get_headers(),
				$this->get_attachments() );
		}

		public function get_content_html() {
			return wc_get_template_html( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
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
                    'description' => sprintf( esc_html__( 'Defaults to <code>%s</code>', 'yith-advanced-refund-system-for-woocommerce' ), $this->email_body ),
                    'placeholder' => '',
                    'default'     => '',
                )
			);
		}

	}

}
return new YITH_ARS_Approved_User_Email();
