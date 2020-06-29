<?php
/**
 * class-woocommerce-product-search-admin-reports.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reports.
 */
class WooCommerce_Product_Search_Admin_Reports {

	/**
	 * Register a hook on the init action.
	 */
	public static function init() {
		add_filter( 'woocommerce_admin_reports', array( __CLASS__, 'woocommerce_admin_reports' ) );
	}

	public static function woocommerce_admin_reports( $reports ) {
		$reports['search'] = array(
			'title' => __( 'Search', 'woocommerce-product-search' ),
			'reports' => array(
				'searches' => array(
					'title' => __( 'Searches', 'woocommerce-product-search' ),
					'description' => '',
					'hide_title' => false,
					'callback' => array( __CLASS__, 'get_report' )
				),
				'queries' => array(
					'title' => __( 'Queries', 'woocommerce-product-search' ),
					'description' => '',
					'hide_title' => false,
					'callback' => array( __CLASS__, 'get_report' )
				)
			)
		);
		return $reports;
	}

	public static function get_report( $name ) {
		if ( !WooCommerce_Product_Search_Hit::get_status() ) {
			echo '<p class="notice woocommerce-message" style="padding: 1em;">';
			esc_html_e( 'The recording of live search data is currently deactivated and the search statistics presented here are not updated.', 'woocommerce-product-search' );
			echo ' ';
			echo wp_kses(
				sprintf(
					__( 'To record and view live search stats here, please activate this in the <a href="%s">General</a> settings.', 'woocommerce-product-search' ),
					esc_url( WooCommerce_Product_Search_Admin::get_admin_section_url( WooCommerce_Product_Search_Admin::SECTION_GENERAL ) )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			echo '</p>';
		}

		switch( $name ) {
			case 'queries' :
				require_once 'reports/class-woocommerce-product-search-report-queries.php';
				$report = new WooCommerce_Product_Search_Report_Queries();
				$report->output_report();
				break;
			default :
				require_once 'reports/class-woocommerce-product-search-report-searches.php';
				$report = new WooCommerce_Product_Search_Report_Searches();
				$report->output_report();
		}
	}

}
WooCommerce_Product_Search_Admin_Reports::init();
