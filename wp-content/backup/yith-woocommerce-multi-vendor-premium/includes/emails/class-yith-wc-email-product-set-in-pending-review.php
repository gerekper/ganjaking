<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_WC_Email_Product_Set_In_Pending_Review' ) ) :

    /**
     * Edited Product Email
     *
     * An email sent to the admin when an order is cancelled.
     *
     * @class       YITH_WC_Email_Product_Set_In_Pending_Review
     * @version     2.2.7
     * @package     WooCommerce/Classes/Emails
     * @author      WooThemes
     * @extends     WC_Email
     */
    class YITH_WC_Email_Product_Set_In_Pending_Review extends WC_Email {

        /**
         * @var string
         */
        public $product = '';

        /**
         * @var null
         */
        public $vendor;



        /**
         * Constructor
         */
        function __construct() {

            $this->id               = 'product_set_in_pending_review';
            $this->title            = __( 'Product set in pending review (to admin)', 'yith-woocommerce-product-vendors' );
            $this->description      = __( 'Email sent to administrator when a product is edited by vendor and need of admin approval', 'yith-woocommerce-product-vendors' );

            $this->heading          = __( 'A vendor product needs review', 'yith-woocommerce-product-vendors' );
            $this->subject          = $this->format_string( __( '[{site_title}] Product Edited', 'yith-woocommerce-product-vendors' ) );

			$this->template_base    = YITH_WPV_TEMPLATE_PATH;
            $this->template_html    = 'emails/product-set-in-pending-review.php';
            $this->template_plain   = 'emails/plain/product-set-in-pending-review.php';

            $this->recipient 		= $this->get_option( 'recipient' );

            if ( ! $this->recipient )
                $this->recipient = get_option( 'admin_email' );

            // Triggers for this email
            add_action( 'yith_wcmv_save_post_product', array( $this, 'trigger' ),10, 3 );

            // Call parent constructor
            parent::__construct();

            $this->vendor = null;
        }

        /**
         * trigger function.
         *
         * @access public
         * @return bool
         */
        function trigger( $post_id, $post, $current_vendor ) {

            if ( ! $this->is_enabled() || empty( $post_id ) ) {
                return false;
            }

            $this->product = wc_get_product( $post );

            if( ! $this->product instanceof WC_Product || $this->product->get_status() != 'pending' ){
            	return false;
            }

	        $this->placeholders = array(
		        '{product_name}' => $this->product->get_title(),
		        '{vendor}'       => $current_vendor->name,
		        '{post_link}'    => get_edit_post_link( $this->product->get_id() ),
		        '{site_title}'   => $this->get_blogname()
	        );

            $this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html() {
            ob_start();
            yith_wcpv_get_template( $this->template_html, array(
                'product'       => $this->product,
                'vendor'        => $this->vendor,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => true,
                'plain_text'    => false,
                'yith_wc_email' => $this
            ), '' );
            return $this->format_string( ob_get_clean() );
        }

        /**
         * get_content_plain function.
         *
         * @access public
         * @return string
         */
        function get_content_plain() {
            ob_start();
            yith_wcpv_get_template( $this->template_plain, array(
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => true,
                'plain_text'    => false,
                'yith_wc_email' => $this
            ), '' );
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
                    'title'         => __( 'Enable/Disable', 'yith-woocommerce-product-vendors' ),
                    'type'          => 'checkbox',
                    'label'         => __( 'Enable this email notification', 'yith-woocommerce-product-vendors' ),
                    'default'       => 'yes'
                ),
                'recipient' => array(
	                'title' 		=> __( 'Recipient(s)', 'yith-woocommerce-product-vendors' ),
	                'type' 			=> 'text',
	                'description' 	=> sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'yith-woocommerce-product-vendors' ), esc_attr( get_option('admin_email') ) ),
	                'placeholder' 	=> '',
	                'default' 		=> ''
                ),
                'subject' => array(
                    'title'         => __( 'Subject', 'yith-woocommerce-product-vendors' ),
                    'type'          => 'text',
                    'description'   => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'yith-woocommerce-product-vendors' ), $this->subject ),
                    'placeholder'   => '',
                    'default'       => ''
                ),
                'heading' => array(
                    'title'         => __( 'Email Heading', 'yith-woocommerce-product-vendors' ),
                    'type'          => 'text',
                    'description'   => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'yith-woocommerce-product-vendors' ), $this->heading ),
                    'placeholder'   => '',
                    'default'       => ''
                ),
                'email_type' => array(
                    'title'         => __( 'Email type', 'yith-woocommerce-product-vendors' ),
                    'type'          => 'select',
                    'description'   => __( 'Choose email format.', 'yith-woocommerce-product-vendors' ),
                    'default'       => 'html',
                    'class'         => 'email_type wc-enhanced-select',
                    'options'       => $this->get_email_type_options()
                )
            );
        }
    }

endif;

return new YITH_WC_Email_Product_Set_In_Pending_Review();
