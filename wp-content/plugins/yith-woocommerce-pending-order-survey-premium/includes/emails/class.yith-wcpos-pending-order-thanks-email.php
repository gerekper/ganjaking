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
if (!class_exists('YITH_WC_Send_Pending_Order_Thanks_Email')) {

    /**
     * YITH_WC_Send_Pending_Order_Email
     *
     * @since 1.0.0
     */
    class YITH_WC_Send_Pending_Order_Thanks_Email extends WC_Email
    {

        /**
         * Constructor method, used to return object of the class to WC
         *
         * @since 1.0.0
         */
        public function __construct()
        {
            $subject_mail   =   sprintf( '%s', __( 'Thanks for your feedback!', 'yith-woocommerce-pending-order-survey' ) );
            $mail_content   =   sprintf( '%s {customer_name},'."\n\n".'%s:'."\n\n".'{survey}'."\n\n".'%s.'."\n\n".'%s.'."\n\n".'%s,'."\n\n".'{site_title}',
                __( 'Dear', 'yith-woocommerce-pending-order-survey' ),
                __( 'thanks for answering this survey', 'yith-woocommerce-pending-order-survey' ),
                __( 'and for your precious time', 'yith-woocommerce-pending-order-survey' ),
                __( 'This information is very important for us', 'yith-woocommerce-pending-order-survey'),
                __( 'Best regards', 'yith-woocommerce-pending-order-survey' )
            );

            $this->id = 'ywcpos_thanks_email';
            $this->customer_email = true;
            $this->title = __('Thank-you email', 'yith-woocommerce-pending-order-survey');
            $this->description = __('This is the email a customer receives after answering a survey',
                'yith-woocommerce-pending-order-survey');


            $this->heading = get_option( 'ywcpos_user_email_sender');
            $this->subject = $subject_mail;


            $this->custom_message = $this->get_option( 'custom_message', $mail_content );
            $this->reply_to = get_option('ywcpos_user_email_reply');

            $this->template_html = 'emails/email-thanks-template.php';

            // Triggers for this email
            add_action( 'send_thanks_email_notification', array( $this, 'trigger' ), 15 );

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
        public function trigger( $args ) {


            $survey_title = $args['survey'];
            $order_id = $args['order_id'];

            $this->set_thanks_email_content( $survey_title, $order_id );

            $return = $this->send($this->get_recipient(), $this->get_subject(), $this->get_content_html(), $this->get_headers(), $this->get_attachments());

        }

        public function set_thanks_email_content( $survey_title, $order_id ){

            $order = wc_get_order( $order_id );

            $user = $order->get_user();

            if( $user ){

                $user_name = $user->last_name.' '.$user->first_name;
                $user_mail = $user->user_email;
            }
            else{

                $user_name = __('User', 'yith-woocommerce-pending-order-survey' );
                $user_mail = $order->get_billing_email();
            }

            $this->recipient = $user_mail;

            $order_content = ywcpos_get_email_template_order_content( $order );
            $email_content = nl2br( $this->custom_message );

            $email_content = str_replace( '{site_title}', get_bloginfo( 'name', 'display' ), $email_content );
            $email_content = str_replace( '{survey}', $survey_title, $email_content );
            $email_content = str_replace( '{order_content}', $order_content, $email_content );
            $email_content = str_replace( '{customer_name}', $user_name, $email_content );
            $email_content = str_replace( '{customer_email}', $user_mail, $email_content );


            $this->email_content = $email_content;

        }

        /**
         * custom email fields
         * @author YIThemes
         * @since 1.0.0
         */
        public function init_form_fields() {

            $subject_mail   =   sprintf( '%s', __( 'Thanks for your feedback!', 'yith-woocommerce-pending-order-survey' ) );
            $mail_content   =   sprintf( '%s {customer_name},'."\n\n".'%s:'."\n\n".'{survey}'."\n\n".'%s.'."\n\n".'%s.'."\n\n".'%s,'."\n\n".'{site_title}',
                __( 'Dear', 'yith-woocommerce-pending-order-survey' ),
                __( 'thanks for answering this survey', 'yith-woocommerce-pending-order-survey' ),
                __( 'and for your precious time', 'yith-woocommerce-pending-order-survey' ),
                __( 'This information is very important for us', 'yith-woocommerce-pending-order-survey'),
                __( 'Best regards', 'yith-woocommerce-pending-order-survey' )
            );

            $desc_tip   =   sprintf( '%s<ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
                __('You can use these placeholders', 'yith-woocommerce-pending-order-survey'),
                __('{site_title} replaced with site title', 'yith-woocommerce-pending-order-survey'),
                __('{survey} replaced with survey details', 'yith-woocommerce-pending-order-survey'),
                __('{customer_name} replaced with customer\'s name', 'yith-woocommerce-pending-order-survey'),
                __('{customer_email} replaced with customer\'s email address', 'yith-woocommerce-pending-order-survey'),
                __('{order_content} replaced with order', 'yith-woocommerce-pending-order-survey' )
            );

            $this->form_fields = array(
                'subject'        => array(
                    'title'       => __( 'Email Subject', 'woocommerce' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->subject ),
                    'placeholder' => '',
                    'default'     => $subject_mail,
                ),
                'heading'        => array(
                    'title'       => __( 'Email Heading', 'woocommerce' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->heading ),
                    'placeholder' => '',
                    'default'     => ''
                ),
                'custom_message' => array(
                    'title'       => __( 'Custom Message', 'yith-woocommerce-membership' ),
                    'type'        => 'textarea',
                    'description' => '',
                    'placeholder' => '',
                    'default'     => $mail_content,
                    'desc_tip'  =>  $desc_tip,
                    'css'   => 'height:300px'
                ),

            );
        }

        /**
         * @author YIThemes
         * @since 1.0.0
         * @return bool
         */
        public function is_enabled()
        {
            return get_option('ywcpos_send_email_after') == 'yes';
        }

        /**
         * Get email content type.
         *
         * @since   1.0.0
         * @return  string
         * @author  YIThemes
         */
        public function get_content_type($default_type ='' ) {
            return 'text/html';
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
return new YITH_WC_Send_Pending_Order_Thanks_Email();
