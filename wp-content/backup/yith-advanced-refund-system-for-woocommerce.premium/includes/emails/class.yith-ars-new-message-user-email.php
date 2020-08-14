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
 * @class      YITH_ARS_New_Message_User_Email
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_ARS_New_Message_User_Email' ) ) {
    /**
     * Class YITH_ARS_New_Message_User_Email
     *
     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_ARS_New_Message_User_Email extends WC_Email {

	    public $email_body;
	    public $message_id;
	    public $request_id;

        public function __construct() {
            $this->id             = 'yith_ywcars_new_message_user_email';
	        $this->customer_email = true;
            $this->title          = esc_html_x( 'YITH Advanced Refund System: New message - email for user', 'Email descriptive title',
                'yith-advanced-refund-system-for-woocommerce' );
            $this->description    = esc_html__( 'The user will receive an email when the admin sends a new message.',
                'yith-advanced-refund-system-for-woocommerce' );
            $this->heading        = esc_html__( 'New message from Shop Manager', 'yith-advanced-refund-system-for-woocommerce' );
            $this->subject        = esc_html__( 'Hi {customer_name}! New message for your refund request', 'yith-advanced-refund-system-for-woocommerce' );
            $this->email_body     = esc_html__( 'Hi {customer_name}! You have received a new message.', 'yith-advanced-refund-system-for-woocommerce' );
            $this->template_html  = 'emails/ywcars-new-message.php';

            add_action( 'ywcars_new_message_from_admin_to_user', array( $this, 'trigger' ) );
	        add_filter( 'woocommerce_email_styles', array( $this, 'style' ) );

            parent::__construct();
            $this->email_type     = 'html';
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

            $order           = wc_get_order( $request->order_id );
	        $this->recipient = $order instanceof WC_Data ? $order->get_billing_email() : $order->billing_email;

	        $customer         = new WP_User( $request->customer_id );
	        $this->subject    = str_replace( '{customer_name}', ucwords( $customer->display_name ), $this->subject );
            $this->email_body = $this->get_option( 'email_body', esc_html__( 'Hi {customer_name}! You have received a new message.', 'yith-advanced-refund-system-for-woocommerce' ) );

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
return new YITH_ARS_New_Message_User_Email();
