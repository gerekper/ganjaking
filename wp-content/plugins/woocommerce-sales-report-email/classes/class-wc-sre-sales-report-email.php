<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_SRE_Sales_Report_Email extends WC_Email {

	/**
	 * Array containing all Report Rows
	 *
	 * @var array<WC_SRE_Report_Row>
	 */
	private $rows = array();

	public function __construct() {

		// WC_Email basic properties
		$this->id          = 'sales_report_email';
		$this->title       = __( 'Sales Reports', 'woocommerce-sales-report-email' );
		$this->description = __( 'The Sales Report Emails plugin extends WooCommerce by emailing you a daily, weekly or monthly sales report.', 'woocommerce-sales-report-email' );

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

		// Create the date range object
		$date_range = new WC_SRE_Date_Range( WC_SRE_Options::get_interval() );

		// Add the elements
		$this->rows = array(
			new WC_SRE_Row_Total_Sign_Ups( $date_range ),
			new WC_SRE_Row_Total_Orders( $date_range ),
			new WC_SRE_Row_Total_Items( $date_range ),
			new WC_SRE_Row_Total_Sales( $date_range ),
			new WC_SRE_Row_Top_Sellers( $date_range ),
		);

		if ( class_exists( 'WC_Subscriptions_Manager' ) ) {
			$this->rows[] = new WC_SRE_Row_Active_Subscribers( $date_range );
		}

		// Subject & heading
		$this->subject = __( 'Sales Report of {site_title}', 'woocommerce-sales-report-email' );
		$this->heading = __( 'Your {interval} report of {site_title}', 'woocommerce-sales-report-email' );

		// Set recipients
		$this->recipient = WC_SRE_Options::get_recipients();

		// Set the template base path
		$this->template_base = plugin_dir_path( WooCommerce_Sales_Report_Email::get_plugin_file() ) . 'templates/';

		// Set the templates
		$this->template_html  = 'sales-report.php';
		$this->template_plain = 'plain/sales-report.php';

		// Find & Replace vars
		$this->find['site-title']    = '{site_title}';
		$this->find['interval']      = '{interval}';
		$this->replace['site-title'] = $this->get_blogname();
		$this->replace['interval']   = WC_SRE_Options::get_interval();

	}

	/**
	 * Return the sales report rows
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function get_rows() {
		/**
		 * Filter: 'wc_sales_report_email_rows' - Allow altering sales report email rows
		 *
		 * @api string $rows The report rows
		 */
		return apply_filters( 'wc_sales_report_email_rows', $this->rows );
	}

	/**
	 * This method is triggered on WP Cron.
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function trigger() {

		// Check if extension is active
		if ( true !== WC_SRE_Options::is_enabled() ) {
			return;
		}

		// Check if an email should be send
		$send_today = false;
		$interval   = WC_SRE_Options::get_interval();
		$now        = new DateTime( null, new DateTimeZone( wc_timezone_string() ) );

		switch ( $interval ) {
			case 'monthly':
				// Send monthly reports on the first day of the month
				if ( 1 == (int) $now->format( 'j' ) ) {
					$send_today = true;
				}
				break;
			case 'weekly':
				// Send weekly reports on monday
				if ( 1 == (int) $now->format( 'w' ) ) {
					$send_today = true;
				}
				break;
			case 'daily':
				// Send everyday if the interval is daily
				$send_today = true;
				break;
		}

		// Check if we need to send an email today
		if ( true !== $send_today ) {
			return;
		}

		// Check if there are any recipients
		if ( '' == WC_SRE_Options::get_recipients() ) {
			return;
		}

		// All checks are done, initialize the object
		$this->init();

		// Add the 'woocommerce_locate_template' filter so we can load our plugin template file
		add_filter( 'woocommerce_locate_template', array( $this, 'load_plugin_template' ), 10, 3 );

		// Send the emails
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		// Remove the woocommerce_locate_template filter
		remove_filter( 'woocommerce_locate_template', array( $this, 'load_plugin_templates' ), 10 );

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
		if ( 'sales-report.php' == $template_name || 'plain/sales-report.php' == $template_name ) {
			$template = $template_path . $template_name;
		}

		return $template;
	}

	/**
	 * Get the email header.
	 *
	 * @deprecated since 1.1.2 as the WC()->mailer() already ads header, this is called in woocommerce-sales-report-email/woocommerce-sales-report-email.php(191)
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
	 * @deprecated since 1.1.2 as the WC()->mailer() already ads footer, this is called in woocommerce-sales-report-email/woocommerce-sales-report-email.php(191)
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
			'rows'          => $this->get_rows(),
			'interval'      => WC_SRE_Options::get_interval(),
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
			'rows'          => $this->get_rows(),
			'interval'      => WC_SRE_Options::get_interval(),
			'plain_text'    => true
		), $this->template_base );

		return ob_get_clean();
	}

	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {
	
		$send_time_array = array();
		
		for ( $hour = 0; $hour < 24; $hour++ ) {
			for ( $min = 0; $min < 60; $min = $min + 15 ) {
				$this_time = date( 'H:i', strtotime( "2015-01-01 $hour:$min" ) );
				$send_time_array[ $this_time ] = $this_time;
			}
		}
		
		$this->form_fields = array(
			'enabled'        => array(
				'title'      => __( 'Enable/Disable', 'woocommerce' ),
				'type'       => 'checkbox',
				'label'      => __( 'Enable this email notification', 'woocommerce' ),
				'default'    => 'yes'
			),
			'recipients' => array(
				'title'       => __( 'Recipients', 'woocommerce-sales-report-email' ),
				'type'        => 'text',
				'description' => __( 'The email addresses reports are sent to, separated by commas.', 'woocommerce-sales-report-email' ),
				'default'     => '',
			),
			'interval'   => array(
				'title'       => __( 'Interval', 'woocommerce-sales-report-email' ),
				'type'        => 'select',
				'options'     => array(
					'daily'   => __( 'Daily', 'woocommerce-sales-report-email' ),
					'weekly'  => __( 'Weekly', 'woocommerce-sales-report-email' ),
					'monthly' => __( 'Monthly', 'woocommerce-sales-report-email' )
				),
				'description' => __( 'The frequency of which reports should be sent.', 'woocommerce-sales-report-email' ),
			),

			// Send time
			'send_time' => array(
				'title' 	  => __( 'Send Email At', 'woocommerce-sales-report-email' ),
				'type' 		  => 'select',
				'description' => __( 'Choose the time of day to send out the sales report (based on the interval above).', 'woocommerce-sales-report-email' ),
				'default' 	  => '03:00',
				'options'     => $send_time_array
			),
			
			'email_type' => array(
				'title' 		=> __( 'Email type', 'woocommerce' ),
				'type' 			=> 'select',
				'description' 	=> __( 'Choose which format of email to send.', 'woocommerce' ),
				'default' 		=> 'html',
				'class'			=> 'email_type',
				'options'		=> array(
					'plain' 		=> __( 'Plain text', 'woocommerce' ),
					'html' 			=> __( 'HTML', 'woocommerce' ),
					'multipart' 	=> __( 'Multipart', 'woocommerce' ),
				)
			)
		);
	}
		
}