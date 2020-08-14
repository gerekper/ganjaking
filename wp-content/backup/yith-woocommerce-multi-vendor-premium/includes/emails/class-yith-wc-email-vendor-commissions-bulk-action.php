<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_WC_Email_Vendor_Commissions_Bulk_Action' ) ) :

/**
 * New Order Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class 		YITH_WC_Email_Vendor_Commissions_Bulk_Action
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		WooThemes
 * @extends 	WC_Email
 *
 * @property YITH_Commission $object
 */
class YITH_WC_Email_Vendor_Commissions_Bulk_Action extends WC_Email {

	/**
	 * @var string New commission status
	 */
	public $new_commission_status;

	/**
	 * @var YITH_Vendor Crrent vendor object
	 */
	public $current_vendor = null;


	/**
	 * Constructor
	 */
	function __construct() {

		$this->id 				= 'vendor_commissions_bulk_action';
		$this->title 			= __( 'Commissions status changed (bulk action)', 'yith-woocommerce-product-vendors' );
		$this->description		= __( 'Commissions have been updated', 'yith-woocommerce-product-vendors' );

		$this->heading 			= __( 'Commissions Updated', 'yith-woocommerce-product-vendors' );
		$this->subject      	= __( '[{site_title}] - Commissions Updated', 'yith-woocommerce-product-vendors' );

		$this->template_base    = YITH_WPV_TEMPLATE_PATH;
		$this->template_html 	= 'emails/commissions-bulk.php';
		$this->template_plain 	= 'emails/plain/commissions-bulk.php';

		$this->recipient 		= YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' );

		// Triggers for this email
		add_action( 'yith_vendors_commissions_bulk_action', array( $this, 'trigger' ), 10, 3 );

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
	function trigger( $commissions, $vendor_id, $new_commission_status ) {
		$this->object = $commissions;
		$this->new_commission_status = $new_commission_status;

		if ( empty( $commissions ) || ! $this->is_enabled() ) {
			return;
		}

		else {
			/* Get the user email  */
			$vendor = yith_get_vendor( $vendor_id, 'vendor' );

			if( $vendor->is_valid() ){
				$this->current_vendor = $vendor;
				$vendor_email = $vendor->store_email;

				if( empty( $vendor_email ) ){
					$vendor_owner = get_user_by( 'id', absint( $vendor->get_owner() ) );
					$vendor_email = $vendor_owner instanceof WP_User ? $vendor_owner->user_email : false;
				}

				$this->recipient = $vendor_email;

				if( $this->sent_to_admin() ){
					$admin_email = esc_attr( get_option( 'admin_email' ) );
					$this->recipient .= ",{$admin_email}";
				}

				if( $this->recipient ){
					$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
				}
			}
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
			'commissions'           => $this->object,
			'new_commission_status' => $this->new_commission_status,
			'current_vendor'        => $this->current_vendor,
			'show_note'             => $this->show_note(),
			'email_heading'         => $this->get_heading(),
			'sent_to_admin'         => $this->sent_to_admin(),
			'plain_text'            => false,
			'email'                 => $this
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
			'commissions'           => $this->object,
			'new_commission_status' => $this->new_commission_status,
			'current_vendor'        => $this->current_vendor,
			'show_note'             => $this->show_note(),
			'email_heading'         => $this->get_heading(),
			'sent_to_admin'         => $this->sent_to_admin(),
			'plain_text'            => true,
			'email'                 => $this
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
			),
			'show_note' => array(
				'title' 		=> __( 'Show commission note', 'yith-woocommerce-product-vendors' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable commission note column for this email', 'yith-woocommerce-product-vendors' ),
				'default' 		=> 'no'
			),
			'sent_to_admin' => array(
				'title' 		=> __( 'Send a copy of this email to administrator', 'yith-woocommerce-product-vendors' ),
				'type' 			=> 'checkbox',
				'label' 		=> sprintf( "%s <code>%s</code>", __( 'Enable carbon copy to website admin email: ', 'yith-woocommerce-product-vendors' ), esc_attr( get_option( 'admin_email' ) ) ),
				'default' 		=> 'no'
			),
		);
	}

	/**
	 * Retrieve the table for commission details
	 *
	 * @param bool $plain_text
	 *
	 * @return string
	 */
	public function email_commission_bulk_table( $commissions, $new_commission_status, $show_note, $plain_text = false ) {
		ob_start();

		$template = $plain_text ? 'plain/commissions-bulk-table' : 'commissions-bulk-table';

		yith_wcpv_get_template( $template, array( 'commissions' => $commissions, 'new_commission_status' => $new_commission_status, 'show_note' => $show_note ), 'emails' );

		$return = apply_filters( 'woocommerce_email_commission_detail_table', ob_get_clean(), $this );

		return $return;
	}

	/**
	 * Checks if this email is enabled and will be sent.
	 * @return bool
	 */
	public function show_note() {
		return apply_filters( 'woocommerce_email_show_note_' . $this->id, 'yes' === $this->get_option( 'show_note' ), $this->object );
	}

	/**
	 * Checks if this email is enabled and will be sent.
	 * @return bool
	 */
	public function sent_to_admin() {
		return apply_filters( 'woocommerce_email_sent_to_admin_' . $this->id, 'yes' === $this->get_option( 'sent_to_admin' ), $this->object );
	}
}

endif;

return new YITH_WC_Email_Vendor_Commissions_Bulk_Action();
