<?php
/**
 * WooCommerce Authorize.Net Reporting
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net Reporting to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net Reporting for your
 * needs please refer to http://www.skyverge.com/contact/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Admin class.
 *
 * Loads / saves admin settings.
 *
 * @since 1.0
 */
class WC_Authorize_Net_Reporting_Admin {


	/** @var SV_WP_Admin_Message_Handler admin message handler instance */
	public $message_handler;


	/**
	 * Sets up the admin class.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// load custom admin styles / scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// add the 'Authorize.Net' tab to the set of report tabs
		add_action( 'woocommerce_reports_charts', array( $this, 'add_reports' ) );

		// process CSV export
		add_action( 'admin_init', array( $this, 'process_export' ) );
	}


	/**
	 * Loads admin styles & scripts only on needed pages.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param $hook_suffix
	 */
	public function load_styles_scripts( $hook_suffix ) {
		global $wp_scripts;

		// only load on report pages
		if ( Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'wc-reports' ) !== $hook_suffix ) {
			return;
		}

		// enqueue script
		wp_enqueue_script( 'jquery-ui-datepicker' );

		// get jQuery UI version
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		// enqueue UI CSS
		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
	}


	/**
	 * Adds settings/export screens to available WC reports.
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param array $reports
	 * @return array $reports with new screens added
	 */
	public function add_reports( $reports ) {

		$reports['authorize_net'] = array(

			'title' => __( 'Authorize.Net', 'woocommerce-authorize-net-reporting' ),

			'charts' => array(

				// Download CSV Export page
				array(
					'title'       => __( 'CSV Export', 'woocommerce-authorize-net-reporting' ),
					'hide_title'  => true,
					'description' => '',
					'function'    => array( $this, 'render_export_page' ),
				),

				// Export settings page
				array(
					'title'       => __( 'Settings', 'woocommerce-authorize-net-reporting' ),
					'hide_title'  => true,
					'description' => '',
					'function'    => array( $this, 'render_settings_page' ),
				),
			)
		);

		return $reports;
	}


	/**
	 * Renders the export transactions page.
	 *
	 * @since 1.0
	 */
	public function render_export_page() {

		// show any error messages
		$this->message_handler->show_messages();

		?><form method="post" id="mainform" action="" enctype="multipart/form-data"><?php

		// show export form
		woocommerce_admin_fields( $this->get_fields( 'export' ) );

		// jQuery UI Datepicker
		wc_enqueue_js( '
			// start date
			$( "#wc_authorize_net_reporting_start_date" ).datepicker( {
					dateFormat     : "yy-mm-dd",
					numberOfMonths : 1,
					showButtonPanel: true,
					showOn         : "button",
					buttonImage    : "' . WC()->plugin_url() . "/assets/images/calendar.png" . '",
					buttonImageOnly: true,
					minDate        : new Date( "'. ( (int) date( 'Y' ) - 2 ) . '-01-01" ),
					maxDate        : "today"
			} );
			// end date
			$( "#wc_authorize_net_reporting_end_date" ).datepicker( {
					dateFormat     : "yy-mm-dd",
					numberOfMonths : 1,
					showButtonPanel: true,
					showOn         : "button",
					buttonImage    : "' . WC()->plugin_url() . "/assets/images/calendar.png" . '",
					buttonImageOnly: true,
					minDate        : new Date( "'. ( (int) date( 'Y' ) - 2 ) . '-01-01" ),
					maxDate        : "today"
			} );
		' );


		// helper input
		?><input type="hidden" name="wc_authorize_net_reporting_csv_export" value="1" /><?php

		wp_nonce_field( __FILE__ );

		submit_button( __( 'Download CSV', 'woocommerce-authorize-net-reporting' ) )

		?></form><?php
	}


	/**
	 * Processes the transaction export download.
	 *
	 * @internal
	 *
	 * @since 1.0
	 */
	public function process_export() {

		if ( ! isset( $_POST['wc_authorize_net_reporting_csv_export'] ) ) {
			return;
		}

		// security/permissions check
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], __FILE__ ) || ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce-authorize-net-reporting' ) );
		}

		// start date
		$start_date = ( ! empty( $_POST['wc_authorize_net_reporting_start_date'] ) ) ? $_POST['wc_authorize_net_reporting_start_date'] : null;

		// end date
		$end_date = ( ! empty( $_POST['wc_authorize_net_reporting_end_date'] ) ) ? $_POST['wc_authorize_net_reporting_end_date'] : null;

		$export = new WC_Authorize_Net_Reporting_Export( $start_date, $end_date );

		$export->download();
	}


	/**
	 * Renders the settings page.
	 *
	 * @since 1.0
	 */
	public function render_settings_page() {

		// add a notice if API credentials were copied
		if ( get_option( 'wc_authorize_net_reporting_api_copied' ) ) {

			$this->message_handler->add_message( __( 'Your API credentials have been copied from the active Authorize.Net Payment Gateway. If this information is not correct, please update them now.', 'woocommerce-authorize-net-reporting' ) );

			delete_option( 'wc_authorize_net_reporting_api_copied' );
		}

		// save settings
		if (  ! empty( $_POST ) ) {

			// security check
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], __FILE__ ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce-authorize-net-reporting' ) );
			}

			woocommerce_update_options( $this->get_fields( 'settings' )  );

			$this->message_handler->add_message( __( 'Settings Saved', 'woocommerce-authorize-net-reporting' ) );
		}

		// show success/error messages
		$this->message_handler->show_messages();

		?><form method="post" id="mainform" action="" enctype="multipart/form-data"><?php

		woocommerce_admin_fields( $this->get_fields( 'settings' ) );

		submit_button( __( 'Save Settings', 'woocommerce-authorize-net-reporting' ) );

		wp_nonce_field( __FILE__ );

		?></form><?php
	}


	/**
	 * Gets the settings or export fields.
	 *
	 * @since 1.0
	 *
	 * @param string $section the section to get fields for
	 * @return array the request fields
	 */
	public static function get_fields( $section ) {

		$fields = array(

			'settings' => array(

				array(
					'name' => __( 'Export Settings', 'woocommerce-authorize-net-reporting' ),
					'type' => 'title'
				),

				array(
					'id'       => 'wc_authorize_net_reporting_email_recipients',
					'name'     => __( 'Email Recipient(s)', 'woocommerce-authorize-net-reporting' ),
					'desc_tip' => sprintf( __( 'Enter recipients (comma separated) that will receive the daily CSV export. Defaults to %s', 'woocommerce-authorize-net-reporting' ), get_option( 'admin_email' ) ),
					'default'  => get_option( 'admin_email' ),
					'css'      => 'min-width: 300px;',
					'type'     => 'text',
				),

				array( 'type' => 'sectionend' ),

				array(
					'name' => __( 'Authorize.Net API Settings', 'woocommerce-authorize-net-reporting' ),
					'type' => 'title'
				),

				array(
					'id'       => 'wc_authorize_net_reporting_api_login_id',
					'name'     => __( 'API Login ID', 'woocommerce-authorize-net-reporting' ),
					'desc_tip' => __( 'Enter your Authorize.Net API Login ID.', 'woocommerce-authorize-net-reporting' ),
					'default'  => '',
					'type'     => 'text',
				),

				array(
					'id'       => 'wc_authorize_net_reporting_api_transaction_key',
					'name'     => __( 'API Transaction Key', 'woocommerce-authorize-net-reporting' ),
					'desc_tip' => __( 'Enter your Authorize.Net API Transaction Key.', 'woocommerce-authorize-net-reporting' ),
					'default'  => '',
					'type'     => 'password',
				),

				array(
					'id'       => 'wc_authorize_net_reporting_api_environment',
					'name'     => __( 'API Environment', 'woocommerce-authorize-net-reporting' ),
					'desc_tip' => __( 'Select the API environment to get transactions from.', 'woocommerce-authorize-net-reporting' ),
					'default'  => 'production',
					'type'     => 'select',
					'options' => array(
						'production' => __( 'Production', 'woocommerce-authorize-net-reporting' ),
						'test'       => __( 'Test', 'woocommerce-authorize-net-reporting' ),
					),
				),

				array(
					'id'          => 'wc_authorize_net_reporting_debug_mode',
					'title'       => __( 'Debug Mode', 'woocommerce-authorize-net-reporting' ),
					'type'        => 'select',
					'desc'        => sprintf( __( 'Save API requests/responses and Detailed Error Messages to the debug log: %s', 'woocommerce-authorize-net-reporting' ), '<strong class="nobr">' . wc_get_log_file_path( wc_authorize_net_reporting()->get_id() ) . '</strong>' ),
					'default'     => 'off',
					'options'     => array(
						'off' => __( 'Off', 'woocommerce-authorize-net-reporting' ),
						'on'  => __( 'On', 'woocommerce-authorize-net-reporting' ),
					),
				),

				array( 'type' => 'sectionend' ),
			),

			'export' => array(

				array(
					'name' => __( 'Date Range', 'woocommerce-authorize-net-reporting' ),
					'type' => 'title',
					'desc' => __( 'Transactions occurring between these dates will be included in the exported CSV file. Leave dates blank to fetch all transactions from the past 24 hours.', 'woocommerce-authorize-net-reporting' )
				),

				array(
					'id'                => 'wc_authorize_net_reporting_start_date',
					'name'              => __( 'Start Date', 'woocommerce-authorize-net-reporting' ),
					'desc_tip'          => __( 'Start date of transactions to include in the exported file, in the format YYYY-MM-DD.', 'woocommerce-authorize-net-reporting' ),
					'custom_attributes' => array( 'placeholder' => 'YYYY-MM-DD' ),
					'type'              => 'text',
				),

				array(
					'id'                => 'wc_authorize_net_reporting_end_date',
					'name'              => __( 'End Date', 'woocommerce-authorize-net-reporting' ),
					'desc_tip'          => __( 'End date of transactions to include in the exported file, in the format YYYY-MM-DD.', 'woocommerce-authorize-net-reporting' ),
					'custom_attributes' => array( 'placeholder' => 'YYYY-MM-DD' ),
					'type'              => 'text',
				),

				array( 'type' => 'sectionend' ),
			)
		);

		return ( isset( $fields[ $section ] ) ) ? $fields[ $section ] : array();
	}


}
