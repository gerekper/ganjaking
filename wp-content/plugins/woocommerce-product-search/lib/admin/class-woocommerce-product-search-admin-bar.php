<?php
/**
 * class-woocommerce-product-search-admin-bar.php
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
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Provides information in the admin bar.
 */
class WooCommerce_Product_Search_Admin_Bar {

	/**
	 * Polling interval, milliseconds.
	 *
	 * @var int
	 */
	const INTERVAL = 28000;

	/**
	 * Long polling interval, milliseconds.
	 *
	 * @var int
	 */
	const LONG_INTERVAL = 58000;

	/**
	 * Actions related to the admin bar.
	 */
	public static function init() {
		add_action( 'admin_bar_menu', array( __CLASS__, 'admin_bar_menu' ), 100 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_woocommerce_product_search_status', array( __CLASS__, 'wp_ajax_woocommerce_product_search_status' ) );

	}

	/**
	 * Registers scripts and styles required for the Admin Bar.
	 */
	public static function admin_enqueue_scripts() {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			wp_register_script( 'woocommerce-product-search-status', trailingslashit( WOO_PS_PLUGIN_URL ) . 'js/status.js', array( 'jquery' ), WOO_PS_PLUGIN_VERSION, true );
			wp_register_style( 'woocommerce-product-search-admin-bar', trailingslashit( WOO_PS_PLUGIN_URL ) . 'css/admin-bar.css', array(), WOO_PS_PLUGIN_VERSION );
		} else {
			wp_register_script( 'woocommerce-product-search-status', trailingslashit( WOO_PS_PLUGIN_URL ) . 'js/status.min.js', array( 'jquery' ), WOO_PS_PLUGIN_VERSION, true );
			wp_register_style( 'woocommerce-product-search-admin-bar', trailingslashit( WOO_PS_PLUGIN_URL ) . 'css/admin-bar.min.css', array(), WOO_PS_PLUGIN_VERSION );
		}

		wp_localize_script(
			'woocommerce-product-search-status',
			'woocommerce_product_search_status',
			array(
				'completed'     => _x( 'Completed', 'index status display', 'woocommerce-product-search' ),
				'running'       => _x( 'Running', 'index status display', 'woocommerce-product-search' ),
				'stopped'       => _x( 'Stopped', 'index status display', 'woocommerce-product-search' ),
				'warning'       => _x( 'Warning', 'index status display', 'woocommerce-product-search' ),
				'interval'      => self::INTERVAL,
				'long_interval' => self::LONG_INTERVAL
			)
		);
	}

	/**
	 * Settings and network in the admin bar.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public static function admin_bar_menu( $wp_admin_bar ) {

		if ( !is_admin() || !is_user_logged_in() ) {
			return;
		}
		if ( !current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$settings = Settings::get_instance();
		$show_in_admin_bar = $settings->get( WooCommerce_Product_Search::SHOW_IN_ADMIN_BAR, WooCommerce_Product_Search::SHOW_IN_ADMIN_BAR_DEFAULT );

		if ( $show_in_admin_bar || WPS_ADMIN_BAR_STATUS ) {

			$running = WooCommerce_Product_Search_Worker::get_status();

			$indexer = new WooCommerce_Product_Search_Indexer();
			$processable = $indexer->get_processable_count();
			$total       = $indexer->get_total_count();
			if ( $total > 0 ) {
				$pct = 100 - $processable / $total * 100;
			} else {
				$pct = 100;
			}

			$status = sprintf(
				'<div id="woocommerce-product-search-admin-bar-status" class="woocommerce-product-search-admin-bar-status" data-id="woocommerce-product-search-admin-bar-status" data-url="%s">',
				esc_url(
					add_query_arg(
						array(
							'action' => 'woocommerce_product_search_status',
							'woocommerce_product_search_nonce' => wp_create_nonce( 'woocommerce_product_search_status' )
						),
						admin_url( 'admin-ajax.php' )
					)
				)
			);
			$status .= '<span class="woocommerce-product-search-indexer-label">';
			$status .= esc_html__( 'Index', 'woocommerce-product-search' );
			$status .= ':';
			$status .= '</span>';

			if ( $running ) {
				$status .= sprintf(
					'<span title="%s" class="woocommerce-product-search-indexer-status status-running %s">%s</span>',
					$pct >= 100 ? esc_attr__( 'Completed', 'woocommerce-product-search' ) : esc_attr__( 'Running', 'woocommerce-product-search' ),
					$pct >= 100 ? 'status-completed' : '',
					$pct >= 100 ? esc_html__( 'Completed', 'woocommerce-product-search' ) : esc_html__( 'Running', 'woocommerce-product-search' )
				);
			} else {
				$status .= sprintf(
					'<span title="%s" class="woocommerce-product-search-indexer-status status-stopped %s">%s</span>',
					esc_attr__( 'Stopped', 'woocommerce-product-search' ),
					$pct >= 100 ? 'status-completed' : '',
					esc_html__( 'Stopped', 'woocommerce-product-search' )
				);
			}

			$status .= sprintf(
				'<span class="woocommerce-product-search-indexer-percent">%s</span>',
				sprintf( '%.2f%%', $pct )
			);

			$status .= '</div>';

			if ( $pct < 100 || WPS_ADMIN_BAR_STATUS ) {
				wp_enqueue_script( 'woocommerce-product-search-status' );
				wp_enqueue_style( 'woocommerce-product-search-admin-bar');
				$wp_admin_bar->add_node(
					array(
						'parent' => 0,
						'id'     => 'woocommerce-product-search',
						'title'  => $status,
						'href'   => WooCommerce_Product_Search_Admin::get_admin_section_url( WooCommerce_Product_Search_Admin::SECTION_INDEX )
					)
				);
			}
		}
	}

	/**
	 * Ajax status handler.
	 */
	public static function wp_ajax_woocommerce_product_search_status() {
		ob_start();
		$is_verified_request = false;
		$result = array();
		$is_verified_request = check_ajax_referer( 'woocommerce_product_search_status', 'woocommerce_product_search_nonce' );
		if ( $is_verified_request ) {

			$status = WooCommerce_Product_Search_Worker::get_status();
			$result['status'] = $status;

			$indexer = new WooCommerce_Product_Search_Indexer();
			$processable = $indexer->get_processable_count();
			$total       = $indexer->get_total_count();
			if ( $total > 0 ) {
				$pct = 100 - $processable / $total * 100;
			} else {
				$pct = 100;
			}
			$result['pct'] = $pct;
			$percent = sprintf( '%.2f%%', $pct );
			$result['percent'] = $percent;
		}
		$ob = ob_get_clean();
		if ( strlen( $ob ) > 0 ) {
			wps_log_warning(
				sprintf(
					'WooCommerce Product Search observed unexpected output produced while handling AJAX status request: %s',
					$ob
				)
			);
		}
		if ( $is_verified_request ) {
			echo json_encode( $result );
		} else {

			status_header( 403 );
		}
		exit;
	}
}

WooCommerce_Product_Search_Admin_Bar::init();
