<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_WC_Email_Cancelled_Order' ) ) :

/**
 * Cancelled Order Email
 *
 * An email sent to the admin when an order is cancelled.
 *
 * @class       WC_Email_Cancelled_Order
 * @version     2.2.7
 * @package     WooCommerce/Classes/Emails
 * @author      WooThemes
 * @extends     WC_Email
 */
class YITH_WC_Email_Cancelled_Order extends WC_Email {

    /**
     * Order number
     */
    public $order_number = '';

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id               = 'cancelled_order_to_vendor';
		$this->title            = __( 'Cancelled order (to vendor)', 'yith-woocommerce-product-vendors' );
		$this->description      = __( 'Cancelled order emails are sent when orders have been marked as cancelled (if they were previously set as pending or on-hold).', 'yith-woocommerce-product-vendors' );

		$this->heading          = __( 'Cancelled order', 'yith-woocommerce-product-vendors' );
		$this->subject          = __( '[{site_title}] Cancelled order ({order_number})', 'yith-woocommerce-product-vendors' );

		$this->template_base    = YITH_WPV_TEMPLATE_PATH;
		$this->template_html    = 'emails/vendor-cancelled-order.php';
		$this->template_plain   = 'emails/plain/vendor-cancelled-order.php';

		$this->recipient 		= YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' );

		// Triggers for this email
		add_action( 'woocommerce_order_status_pending_to_cancelled_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_on-hold_to_cancelled_notification', array( $this, 'trigger' ) );

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
	function trigger( $order_id ) {


		if ( ! $this->is_enabled() || empty( $order_id ) ) {
			return false;
		}

		$suborder_ids = array();

		/**
		 * is a parent order
		 */
		if( ! wp_get_post_parent_id( $order_id ) ){
			$suborder_ids = 'woocommerce_order_action_cancelled_order_to_vendor' == current_action() ? YITH_Orders::get_suborder( $order_id ) : array();
		}

		else{
			$suborder_ids = array( $order_id );
		}

		if( empty( $suborder_ids ) ){
			return false;
		}

		foreach ( $suborder_ids as $suborder_id ) {
			$this->object = wc_get_order( $suborder_id );
			$this->vendor = yith_get_vendor( get_post_field( 'post_author', yit_get_prop( $this->object, 'id' ) ), 'user' );

			if ( ! $this->vendor->is_valid() ) {
				return false;
			}

			$this->order_number = yith_wcmv_get_email_order_number( $this->object, 'yes' == $this->get_option( 'show_parent_order_id', 'no' ) );

			$this->find['order-date']   = '{order_date}';
			$this->find['order-number'] = '{order_number}';

			$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( yit_get_prop( $this->object, 'order_date' ) ) );
			$this->replace['order-number'] = $this->order_number;

			$vendor_email = $this->vendor->store_email;

			if ( empty( $vendor_email ) ) {
				$vendor_owner = get_user_by( 'id', absint( $this->vendor->get_owner() ) );
				$vendor_email = $vendor_owner instanceof WP_User ? $vendor_owner->user_email : false;
			}

			$headers = $this->get_headers();

			if ( 'yes' == $this->get_option( 'send_cc_to_admin', 'no' ) ) {
				$admin_name  = get_option( 'woocommerce_email_from_name' );
				$admin_email = get_option( 'woocommerce_email_from_address' );
				if ( $admin_email && $admin_name ) {
					$headers .= "Cc: {$admin_name} <{$admin_email}>";
				}
			}

			$to = apply_filters('yith_wcmv_email_address_recipients_cancelled_order_vendor_email',$vendor_email);
			do_action( 'wpml_switch_language_for_email', $to );
			$this->send( $to, $this->get_subject(), $this->get_content(), $headers, $this->get_attachments() );
			do_action( 'wpml_restore_language_from_email' );
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
			'order' 		=> $this->object,
            'order_number'  => $this->order_number,
            'vendor'        => $this->vendor,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => false,
            'yith_wc_email' => $this
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
			'order' 		=> $this->object,
            'order_number'  => $this->order_number,
            'vendor'        => $this->vendor,
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
			),
            'show_parent_order_id' => array(
                'title'         => __( 'Order id', 'yith-woocommerce-product-vendors' ),
                'type'          => 'checkbox',
                'label'         => __( 'Show parent order id instead of vendor suborder id', 'yith-woocommerce-product-vendors' ),
                'default'       => 'no'
            ),
			'send_cc_to_admin' => array(
				'title'         => __( 'CC to Admin', 'yith-woocommerce-product-vendors' ),
				'type'          => 'checkbox',
				'label'         => __( 'Send a copy of this email to website admin', 'yith-woocommerce-product-vendors' ),
				'default'       => 'no'
			),
		);
	}
}

endif;

return new YITH_WC_Email_Cancelled_Order();
