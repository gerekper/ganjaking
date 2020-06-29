<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Admin_Email extends WC_Email {

	/**
	 * Array containing all Report Rows
	 *
	 * @var array<WC_SRE_Report_Row>
	 */
	private $rows = array();

	public function __construct( $order, $score ) {

		// WC_Email basic properties
		$this->id          = 'anti_fraud_admin_notice';
		$this->title       = __( 'Anti Fraud - Admin Notice', 'woocommerce-anti-fraud' );
		$this->description = __( 'Admin notification about an order.', 'woocommerce-anti-fraud' );

		$this->order = $order;
		$this->order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();

		$this->score = $score;

		// Parent Constructor
		parent::__construct();

	}

	/**
	 * Initialize the class via this init method instead of the constructor to enhance performance.
	 *
	 * @access private
	 * @since  1.0.0
	 */
	private function init() {

		// Subject & heading
		$this->subject = __( 'Fraud notification of order #{order_id}', 'woocommerce-anti-fraud' );
		$this->heading = __( 'Fraud notification of order #{order_id}', 'woocommerce-anti-fraud' );

		// Set recipients
		$this->recipient = apply_filters( 'wc_anti_fraud_email_recipient', get_option( 'admin_email', '' ) );
		$this->customemail = get_option( 'wc_settings_anti_fraud_custom_email');
		// Set the template base path
		$this->template_base = plugin_dir_path( WooCommerce_Anti_Fraud::get_plugin_file() ) . 'templates/';

		// Set the templates
		$this->template_html  = 'af-admin-notice.php';
		$this->template_plain = 'plain/af-admin-notice.php';

		// Find & Replace vars
		$this->find['order-id']    = '{order_id}';
		$this->replace['order-id'] = $this->order->get_order_number();

	}

	/**
	 * This method is triggered on WP Cron.
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function send_notification() {

		// All checks are done, initialize the object
		$this->init();

		// Add the 'woocommerce_locate_template' filter so we can load our plugin template file
		add_filter( 'woocommerce_locate_template', array( $this, 'load_plugin_template' ), 10, 3 );

		// Add email header and footer
		if ( ! has_action( 'woocommerce_email_header' ) ) {
			add_action( 'woocommerce_email_header', array( $this, 'email_header' ) );
			add_action( 'woocommerce_email_footer', array( $this, 'email_footer' ) );
		}

		// Send the emails
		$this->send( $this->get_recipient().','.$this->customemail, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		// Remove the woocommerce_locate_template filter
		remove_filter( 'woocommerce_locate_template', array( $this, 'load_plugin_templates' ), 10 );

		// Remove the header and footer actions
		remove_action( 'woocommerce_email_header', array( $this, 'email_header' ) );
		remove_action( 'woocommerce_email_footer', array( $this, 'email_footer' ) );

	}

	/**
	 * Load template files of this plugin
	 *
	 * @param String $template
	 * @param String $template_name
	 * @param String $template_path
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return String
	 */
	public function load_plugin_template( $template, $template_name, $template_path ) {
		if ( 'af-admin-notice.php' == $template_name || 'plain/af-admin-notice.php' == $template_name ) {
			$template = $template_path . $template_name;
		}

		return $template;
	}

	/**
	 * Get the email header.
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @param mixed $email_heading heading for the email
	 *
	 * @return void
	 */
	public function email_header( $email_heading ) {
		wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}

	/**
	 * Get the email footer.
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function email_footer() {
		wc_get_template( 'emails/email-footer.php' );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'email_heading' => $this->get_heading(),
			'order_id'      => $this->order->get_order_number(),
			'score'         => $this->score,
			'order_url'     => admin_url( 'post.php?post=' . $this->order_id . '&action=edit' ),
			'plain_text'    => false
		), $this->template_base );

		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'email_heading' => $this->get_heading(),
			'order_id'      => $this->order->get_order_number(),
			'score'         => $this->score,
			'order_url'     => admin_url( 'post.php?post=' . $this->order_id . '&action=edit' ),
			'plain_text'    => true
		), $this->template_base );

		return ob_get_clean();
	}

}
