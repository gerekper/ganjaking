<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Pending Order Survey
 *
 * @class   YITH_WC_Send_Pending_Order_Email
 * @package YITH WooCommerce Pending Order Survey
 * @since   1.0.0
 * @author  Yithemes
 */
if (!class_exists('YITH_WC_Send_Pending_Order_Email')) {

    /**
     * YITH_WC_Send_Pending_Order_Email
     *
     * @since 1.0.0
     */
    class YITH_WC_Send_Pending_Order_Email extends WC_Email
    {

        /**
         * Constructor method, used to return object of the class to WC
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            $this->id = 'ywcpos_email';
            $this->customer_email = true;
            $this->title = __('Pending Order Survey Email', 'yith-woocommerce-pending-order-survey');
            $this->description = __('This is the email sent by admin to the customer using YITH WooCommerce Pending Order Survey', 'yith-woocommerce-pending-order-survey');

            $this->heading = get_option('ywcpos_user_sender_name');
            $this->subject = get_option('ywcpos_user_email_sender');
            $this->reply_to = get_option('ywcpos_user_email_reply');

            $this->template_html = 'emails/email-template.php';

            // Triggers for this email
            add_action( 'send_wcpos_mail_notification', array( $this, 'trigger' ), 15 );

            // Call parent constructor
            parent::__construct();
        }

        /**
         * Method triggered to send email
         *
         * @param int $args
         *
         * @return void
         * @since  1.0
         * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
         */
        public function trigger( $args )
        {

            $this->recipient = $args['user_email'];
            $this->email_content = $args['email_content'];
            $this->subject = $args['email_subject'];

            $return = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content_html(), $this->get_headers(), $this->get_attachments() );
            $order = wc_get_order( $args['order_id'] );
            if( $return ) {


                yit_save_prop( $order, '_ywcpos_email_sent', 'yes' );

                //update email template meta '_ywcpos_order_email_send'
                $order_emails_sent = get_post_meta( $args['email_id'], '_ywcpos_order_email_send', true );
                $order_emails_sent = empty( $order_emails_sent ) ? array() : $order_emails_sent ;
                $order_emails_sent[] = $args['order_id'];
                update_post_meta( $args['email_id'], '_ywcpos_order_email_send', $order_emails_sent );

                //update counter
                ywcpos_update_counter_meta( $args['email_id'], '_ywcpos_send_count' );
                ywcpos_update_counter( '_ywcpos_tot_email_send' );

            }
            else {
                yit_save_prop( $order, '_ywcpos_email_sent', 'no' );
            }
        }

        /**
         * get_headers function.
         *
         * @access public
         * @return string
         */
        public function get_headers()
        {
            $headers = "Reply-to: " . $this->reply_to . "\r\n";
            $headers .= "Content-Type: " . $this->get_content_type() . "\r\n";

            return apply_filters('woocommerce_email_headers', $headers, $this->id, $this->object);
        }

        /**
         * Get HTML content for the mail
         *
         * @return string HTML content of the mail
         * @since  1.0
         * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
         */
        public function get_content_html()
        {
            ob_start();
            wc_get_template($this->template_html, array(
                'email_content' => $this->email_content,
                'email_heading' => $this->heading,
                'sent_to_admin' => true,
                'plain_text' => false
            ), YITH_WCPO_SURVEY_TEMPLATE_PATH,YITH_WCPO_SURVEY_TEMPLATE_PATH );
            return ob_get_clean();
        }


    }
}


// returns instance of the mail on file include
return new YITH_WC_Send_Pending_Order_Email();