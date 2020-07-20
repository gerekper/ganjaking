<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWRAQ_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements the YITH_YWRAQ_Quote_Status class.
 *
 * @class   YITH_YWRAQ_Quote_Status
 * @package YITH
 * @since   1.0.0
 * @author  YITH
 */
if ( !class_exists( 'YITH_YWRAQ_Quote_Status' ) ) {

    /**
     * YITH_YWRAQ_Quote_Status
     *
     * @since 1.0.0
     */
    class YITH_YWRAQ_Quote_Status extends WC_Email {

	    /**
	     * Constructor method, used to return object of the class to WC
	     *
	     * @since 1.0.0
	     */
        public function __construct() {
            $this->id          = 'ywraq_quote_status';
            $this->title       = __( '[YITH Request a Quote] Accepted/rejected Quote', 'yith-woocommerce-request-a-quote' );
            $this->description = __( 'This email is sent when a user clicks on "Accept/Reject" button in a Proposal', 'yith-woocommerce-request-a-quote' );

            $this->heading = __( 'Request a quote', 'yith-woocommerce-request-a-quote' );
            $this->subject = __( '[Answer to quote request]', 'yith-woocommerce-request-a-quote' );

	        $this->template_base  = YITH_YWRAQ_TEMPLATE_PATH.'/';
            $this->template_html  = 'emails/change-status.php';
	        $this->template_plain  = 'emails/plain/change-status.php';


            // Call parent constructor
            parent::__construct();

            if( $this->enabled == 'no'){
                return;
            }

            // Triggers for this email
            add_action( 'change_status_mail_notification', array( $this, 'trigger' ), 15, 1 );


            // Other settings
            $this->recipient = $this->get_option( 'recipient' );

            if ( !$this->recipient ) {
                $this->recipient = get_option( 'admin_email' );
            }

            $this->enable_cc = $this->get_option( 'enable_cc' );
            $this->enable_cc = $this->enable_cc == 'yes';

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

            if( $this->settings['email_from_email'] == 'no'){
                return;
            }

	        $this->status = $args['status'];
	        $this->order  = $args['order'];
	        $this->reason = isset( $args['reason'] ) ? $args['reason'] : '';
	        $order_id = yit_get_prop( $args['order'], 'id', true );
		    $this->placeholders['{quote_number}'] = apply_filters( 'ywraq_quote_number', $order_id );

	        $this->object = $args['order'];
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments( ) );
        }

	    /**
	     * get_headers function.
	     *
	     * @access public
	     * @return string
	     */
	    public function get_headers() {
	    	$headers = '';

		    if ( $this->enable_cc ) {
		    	$user_email = yit_get_prop( $this->order, 'billing_email' );
			    $headers .= "Cc: " . $user_email . "\r\n";
		    }

		    $headers .= "Content-Type: " . $this->get_content_type() . "\r\n";

		    return apply_filters( 'woocommerce_email_headers', $headers, $this->id, $this->object );
	    }

        /**
         * Get HTML content for the mail
         *
         * @return string HTML content of the mail
         * @since  1.0
         * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
         */
	    public function get_content_html() {
		    ob_start();

		    wc_get_template( $this->template_html, array(
			    'order'         => $this->order,
			    'email_heading' => $this->get_heading(),
			    'email_description' => $this->get_option( 'email-description' ),
			    'status'        => $this->status,
			    'reason'        => $this->reason,
			    'sent_to_admin' => true,
			    'plain_text'    => false,
			    'email'         => $this
		    ), false, $this->template_base  );

		    return ob_get_clean();
	    }

	    /**
	     * Get Plain content for the mail
	     *
	     * @access public
	     * @return string
	     */
	    function get_content_plain() {
		    ob_start();
		    wc_get_template( $this->template_plain, array(
			    'order'         => $this->order,
			    'email_heading' => $this->get_heading(),
			    'email_description' => $this->get_option( 'email-description' ),
			    'status'        => $this->status,
			    'reason'        => $this->reason,
			    'sent_to_admin' => true,
			    'plain_text'    => false,
			    'email'         => $this
		    ), false, $this->template_base );
		    return ob_get_clean();
	    }

	    /**
	     * @return array|mixed|void
	     * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	     */
	    public function get_attachments( ){
            $attachments = array();
            if( !empty($file) && file_exists( $file['file'] ) ){
                $attachments[] = $file['file'];
            }
	        return apply_filters( 'woocommerce_email_attachments', $attachments, $this->id, $this->object );
        }

        /**
         * Get from name for email.
         *
         * @return string
         */
        public function get_from_name( $from_name = ''  ) {
            $email_from_name = ( isset($this->settings['email_from_name']) && $this->settings['email_from_name'] != '' ) ? $this->settings['email_from_name'] : $from_name;
            return wp_specialchars_decode( esc_html( $email_from_name ), ENT_QUOTES );
        }

        /**
         * Get from email address.
         *
         * @return string
         */
        public function get_from_address( $from_email = ''  ) {
            $email_from_email = ( isset($this->settings['email_from_email']) && $this->settings['email_from_email'] != '' ) ? $this->settings['email_from_email'] : $from_email;
            return sanitize_email( $email_from_email );
        }
        /**
         * Init form fields to display in WC admin pages
         *
         * @return void
         * @since  1.0
         * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
         */
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'         => __( 'Enable/Disable', 'yith-woocommerce-request-a-quote' ),
                    'type'          => 'checkbox',
                    'label'         => __( 'Enable this email notification', 'yith-woocommerce-request-a-quote' ),
                    'default'       => 'yes'
                ),
                'email_from_name'    => array(
                    'title'       => __( '"From" Name', 'yith-woocommerce-request-a-quote' ),
                    'type'        => 'text',
                    'description' => '',
                    'placeholder' => '',
                    'default'     => get_option( 'woocommerce_email_from_name' )
                ),
                'email_from_email'    => array(
                    'title'       => __( '"From" Email Address', 'yith-woocommerce-request-a-quote' ),
                    'type'        => 'text',
                    'description' => '',
                    'placeholder' => '',
                    'default'     => get_option( 'woocommerce_email_from_address' )
                ),

                'subject'    => array(
                    'title'       => __( 'Subject', 'yith-woocommerce-request-a-quote' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'This field lets you edit email subject line. Leave it blank to use default subject text: <code>%s</code>. You can use {quote_number} as a placeholder that will show the quote number in the quote.', 'yith-woocommerce-request-a-quote' ), $this->subject ),
                    'placeholder' => '',
                    'default'     => ''
                ),

                'recipient'  => array(
                    'title'       => __( 'Recipient(s)', 'yith-woocommerce-request-a-quote' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'Enter recipients (separated by commas) for this email. Defaults to <code>%s</code>', 'yith-woocommerce-request-a-quote' ), esc_attr( get_option( 'admin_email' ) ) ),
                    'placeholder' => '',
                    'default'     => ''
                ),

                'enable_cc'  => array(
                    'title'       => __( 'Send CC copy', 'yith-woocommerce-request-a-quote' ),
                    'type'        => 'checkbox',
                    'description' => __( 'Send a carbon copy to the user', 'yith-woocommerce-request-a-quote' ),
                    'default'     => 'no'
                ),

                'heading'    => array(
                    'title'       => __( 'Email Heading', 'yith-woocommerce-request-a-quote' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'This field lets you change the main heading in email notification. Leave it blank to use default heading type: <code>%s</code>.', 'yith-woocommerce-request-a-quote' ), $this->heading ),
                    'placeholder' => '',
                    'default'     => ''
                ),

                'email-description'    => array(
                    'title'       => __( 'Email Description', 'yith-woocommerce-request-a-quote' ),
                    'type'        => 'textarea',
                    'placeholder' => '',
                    'default'     =>  __( 'You have received a request for a quote. The request is the following:', 'yith-woocommerce-request-a-quote')
                ),

                'email_type' => array(
	                'title' 		=> __( 'Email type', 'yith-woocommerce-request-a-quote' ),
	                'type' 			=> 'select',
	                'description' 	=> __( 'Choose email format.', 'yith-woocommerce-request-a-quote' ),
	                'default' 		=> 'html',
	                'class'			=> 'email_type wc-enhanced-select',
	                'options'		=> $this->get_email_type_options()
                ),
            );
        }
    }
}


// returns instance of the mail on file include
return new YITH_YWRAQ_Quote_Status();
