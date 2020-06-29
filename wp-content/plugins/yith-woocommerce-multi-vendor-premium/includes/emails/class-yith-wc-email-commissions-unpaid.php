<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_WC_Email_Commissions_Unpaid' ) ) :

/**
 * New Order Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class 		YITH_WC_Email_Commissions_Unpaid
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		WooThemes
 * @extends 	WC_Email
 */
class YITH_WC_Email_Commissions_Unpaid extends WC_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id 				= 'commissions_unpaid';
		$this->title 			= __( 'Commissions unpaid', 'yith-woocommerce-product-vendors' );
		$this->description		= __( 'New commissions are credited to vendors', 'yith-woocommerce-product-vendors' );

		$this->heading 			= __( 'Commissions unpaid', 'yith-woocommerce-product-vendors' );
		$this->subject      	= __( '[{site_title}] - Commissions unpaid', 'yith-woocommerce-product-vendors' );

		$this->template_base    = YITH_WPV_TEMPLATE_PATH;
		$this->template_html 	= 'emails/commissions-unpaid.php';
		$this->template_plain 	= 'emails/plain/commissions-unpaid.php';

		// Triggers for this email
		add_action( 'yith_vendors_commissions_unpaid', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient' );

		if ( ! $this->recipient )
			$this->recipient = get_option( 'admin_email' );
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $commission ) {
		if ( ! $commission instanceof YITH_Commission || ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->object = $commission;
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
		yith_wcpv_get_template( $this->template_html, array(
			'commission'    => $this->object,
			'order' 		=> $this->object,
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
			'order' 		=> $this->object,
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
				'label' 		=> __( 'Enable this email notification', 'yith-woocommerce-product-vendors' ),
				'default' 		=> 'yes'
			),
			'recipient' => array(
				'title' 		=> __( 'Recipient(s)', 'yith-woocommerce-product-vendors' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'yith-woocommerce-product-vendors' ), esc_attr( get_option('admin_email') ) ),
				'placeholder' 	=> '',
				'default' 		=> ''
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
				'description' 	=> __( 'Choose format for emails that will be sent.', 'yith-woocommerce-product-vendors' ),
				'default' 		=> 'html',
				'class'			=> 'email_type wc-enhanced-select',
				'options'		=> $this->get_email_type_options()
			)
		);
	}
}

endif;

return new YITH_WC_Email_Commissions_Unpaid();
