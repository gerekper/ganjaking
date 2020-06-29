<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Given_Order' ) ) :

/**
 * Given Order Email
 *
 * An email sent to the customer when they are giften an order.
 *
 * @class   WC_Given_Order
 * @author  WooThemes
 * @extends WC_Email
 */
class WC_Given_Order extends WC_Email {

    /**
     * Constructor
     */
    function __construct() {

        $this->id             = 'given_order';
        $this->title          = __( 'Given order', 'woocommerce-give-products' );
        $this->description    = __( 'Given order emails are sent when an order is given to a user.', 'woocommerce-give-products' );

        $this->heading        = __( 'Gifted Order', 'woocommerce-give-products' );
        $this->subject        = __( '[{site_title}] Gifted order ({order_number}) - {order_date}', 'woocommerce-give-products' );

        $this->template_html  = 'emails/given-order.php';
        $this->template_plain = 'emails/plain/given-new-order.php';
        $this->template_base  = plugin_dir_path( WC_Give_Products::$plugin_file ) . 'templates/';

        // Triggers for this email
        add_action( 'woocommerce_order_given', array( $this, 'trigger' ) );

        // Call parent constructor
        parent::__construct();

    }

    /**
     * trigger function.
     *
     * @access public
     * @return void
     */
    function trigger( $order_id ) {

        if ( $order_id ) {
            $this->object                  = wc_get_order( $order_id );
            $pre_wc_30                     = version_compare( WC_VERSION, '3.0', '<' );
            $this->recipient               = $pre_wc_30 ? $this->object->billing_email : $this->object->get_billing_email();

            $this->find['order-date']      = '{order_date}';
            $this->find['order-number']    = '{order_number}';

            $this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $pre_wc_30 ? $this->object->order_date : ( $this->object->get_date_created() ? gmdate( 'Y-m-d H:i:s', $this->object->get_date_created()->getOffsetTimestamp() ) : '' ) ) );
            $this->replace['order-number'] = $this->object->get_order_number();
        }

        if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
            return;
        }

        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    /**
     * get_content_html function.
     *
     * @access public
     * @return string
     */
    function get_content_html() {
        ob_start();
        wc_get_template(
            $this->template_html,
            array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false
            ),
            '',
            $this->template_base
        );
        return ob_get_clean();
    }

    /**
     * get_content_plain function.
     *
     * @access public
     * @return string
     */
    function get_content_plain() {
        ob_start();
        wc_get_template(
            $this->template_plain,
            array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => true
            ),
            '',
            $this->template_base
        );
        return ob_get_clean();
    }

    /**
     * Initialise Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' 		=> __( 'Enable/Disable', 'woocommerce-give-products' ),
                'type' 			=> 'checkbox',
                'label' 		=> __( 'Enable this email notification', 'woocommerce-give-products' ),
                'default' 		=> 'yes'
            ),
            'subject' => array(
                'title' 		=> __( 'Subject', 'woocommerce-give-products' ),
                'type' 			=> 'text',
                'description' 	=> sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-give-products' ), $this->subject ),
                'placeholder' 	=> '',
                'default' 		=> ''
            ),
            'heading' => array(
                'title' 		=> __( 'Email Heading', 'woocommerce-give-products' ),
                'type' 			=> 'text',
                'description' 	=> sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-give-products' ), $this->heading ),
                'placeholder' 	=> '',
                'default' 		=> ''
            ),
            'email_type' => array(
                'title' 		=> __( 'Email type', 'woocommerce-give-products' ),
                'type' 			=> 'select',
                'description' 	=> __( 'Choose which format of email to send.', 'woocommerce-give-products' ),
                'default' 		=> 'html',
                'class'			=> 'email_type',
                'options'		=> array(
                    'plain'		 	=> __( 'Plain text', 'woocommerce-give-products' ),
                    'html' 			=> __( 'HTML', 'woocommerce-give-products' ),
                    'multipart' 	=> __( 'Multipart', 'woocommerce-give-products' ),
                )
            )
        );
    }
}

endif;

return new WC_Given_Order();
