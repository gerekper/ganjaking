<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_WC_Email_Vendor_Commissions_Paid' ) ) :

/**
 * New Order Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class 		YITH_WC_Email_Commissions_Paid
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		WooThemes
 * @extends 	WC_Email
 *
 * @property YITH_Commission $object
 */
class YITH_WC_Email_Vendor_Commissions_Paid extends WC_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id 				= 'vendor_commissions_paid';
		$this->title 			= __( 'Commission paid (for Vendor)', 'yith-woocommerce-product-vendors' );
		$this->description		= __( 'New commissions have been credited to vendor', 'yith-woocommerce-product-vendors' );

		$this->heading 			= __( 'Vendor\'s Commission paid', 'yith-woocommerce-product-vendors' );
		$this->subject      	= __( '[{site_title}] - Commission paid', 'yith-woocommerce-product-vendors' );

		$this->template_base    = YITH_WPV_TEMPLATE_PATH;
		$this->template_html 	= 'emails/vendor-commissions-paid.php';
		$this->template_plain 	= 'emails/plain/vendor-commissions-paid.php';

		$this->recipient 		= YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' );

		// Triggers for this email
		add_action( 'yith_vendors_commissions_paid', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 *
	 * @param YITH_Commission $commission Commission paid
	 */
	function trigger( $commission ) {
        if ( ! $commission instanceof YITH_Commission || ! $this->is_enabled() ) {
			return;
		}

		$this->object = $commission;

        /* Get the user email  */
        $user = $this->object->get_user();

        if( $user instanceof WP_User ){
	        $this->recipient = $user->user_email;
        }

        if( ! empty( $this->recipient ) ){
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
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
			'commission'    => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => false,
            'email'         => $this
		), '' );
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
		yith_wcpv_get_template( $this->template_plain, array(
			'commission'    => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => true,
            'email'         => $this
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
				'title' 		=> __( 'Enable/Disable', 'yith-woocommerce-product-vendors' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable notification for this email', 'yith-woocommerce-product-vendors' ),
				'default' 		=> 'yes'
			),
			'subject' => array(
				'title' 		=> __( 'Subject', 'yith-woocommerce-product-vendors' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the email subject line. Leave it blank to use the default subject: <code>%s</code>.', 'yith-woocommerce-product-vendors' ), $this->subject ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'heading' => array(
				'title' 		=> __( 'Email Heading', 'yith-woocommerce-product-vendors' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the main heading contained in the email notification. Leave it blank to use the default heading: <code>%s</code>.', 'yith-woocommerce-product-vendors' ), $this->heading ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'email_type' => array(
				'title' 		=> __( 'Email type', 'yith-woocommerce-product-vendors' ),
				'type' 			=> 'select',
				'description' 	=> __( 'Choose format for the email that will be sent.', 'yith-woocommerce-product-vendors' ),
				'default' 		=> 'html',
				'class'			=> 'email_type wc-enhanced-select',
				'options'		=> $this->get_email_type_options()
			)
		);
	}
}

endif;

return new YITH_WC_Email_Vendor_Commissions_Paid();
