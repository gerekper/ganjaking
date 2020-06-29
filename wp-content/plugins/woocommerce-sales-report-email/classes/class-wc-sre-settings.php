<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_SRE_Settings {

	/**
	 * Setup the WooCommerce settings
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function setup() {

		// Add the section
		add_filter( 'woocommerce_get_sections_email', array( $this, 'add_section' ) );

		// Add the email class
		add_filter( 'woocommerce_email_classes', array( $this, 'add_email_class' ) );

	}

	/**
	 * Add Sales Report Email section
	 *
	 * @param $sections
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return mixed
	 */
	public function add_section( $sections ) {
		$sections['wc_sre_sales_report_email'] = __( 'Sales Reports', 'woocommerce-sales-report-email' );

		return $sections;
	}

	/**
	 * Add Sales Report Email Class to array with email templates
	 *
	 * @param $emails
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return mixed
	 */
	public function add_email_class( $emails ) {
		$emails['WC_SRE_Sales_Report_Email'] = new WC_SRE_Sales_Report_Email();

		return $emails;
	}

}