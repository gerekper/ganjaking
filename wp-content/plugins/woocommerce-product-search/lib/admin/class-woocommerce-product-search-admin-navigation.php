<?php
/**
 * class-woocommerce-product-search-admin-navigation.php
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
 * @since 3.6.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\Loader;
use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen;

/**
 * WooCommerce Admin Navigation.
 */
class WooCommerce_Product_Search_Admin_Navigation {

	public static $menu_position = 37;

	/**
	 * Register a hook on the init action.
	 */
	public static function init() {
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ), -1 );

		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), self::$menu_position );

		add_filter( 'woocommerce_screen_ids', array( __CLASS__, 'woocommerce_screen_ids' ) );
		add_filter( 'woocommerce_reports_screen_ids', array( __CLASS__, 'woocommerce_reports_screen_ids' ) );
	}

	public static function admin_notices() {

		global $current_screen;
		if ( !empty( $current_screen ) ) {
			$screen_ids = self::woocommerce_screen_ids( array() );
			if ( in_array( $current_screen->id, $screen_ids ) ) {
				global $woocommerce_product_search_welcome;
				$woocommerce_product_search_welcome = true;
			}
		}
	}

	/**
	 * Register screen ids.
	 *
	 * @since 1.7.0
	 *
	 * @param array $screen_ids
	 *
	 * @return array
	 */
	public static function woocommerce_screen_ids( $screen_ids ) {
		$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );
		$screen_ids[] = 'toplevel_page_woocommerce-product-search';
		$screen_ids[] = 'admin_page_woocommerce-product-search';
		$screen_ids[] = 'admin_page_woocommerce-product-search-overview';
		$screen_ids[] = 'admin_page_woocommerce-product-search-report-searches';
		$screen_ids[] = 'admin_page_woocommerce-product-search-report-queries';
		$screen_ids[] = $wc_screen_id . '_page_woocommerce-product-search';
		return $screen_ids;
	}

	/**
	 * Register report screen ids.
	 *
	 * @since 1.7.0
	 *
	 * @param array $screen_ids
	 *
	 * @return array
	 */
	public static function woocommerce_reports_screen_ids( $screen_ids ) {
		return self::woocommerce_screen_ids( $screen_ids );
	}

	/**
	 * Whether WooCommerce Admin navigation is enabled.
	 *
	 * @return boolean
	 */
	public static function is_navigation() {
		$result = false;
		if (
			class_exists( '\Automattic\WooCommerce\Admin\Features\Features' ) &&
			method_exists( '\Automattic\WooCommerce\Admin\Features\Features', 'exists' )
		) {
			$result = Features::exists( 'navigation' );
		} else if (
			class_exists( '\Automattic\WooCommerce\Admin\Loader' ) &&
			method_exists( '\Automattic\WooCommerce\Admin\Loader', 'is_feature_enabled' )
		) {
			$result = Loader::is_feature_enabled( 'navigation' );
		}
		return $result;
	}

	public static function get_report_url( $report = null ) {
		$url = null;
		if ( self::is_navigation() ) {
			switch ( $report ) {
				case 'searches' :
					$url = admin_url( 'admin.php?page=woocommerce-product-search-report-searches' );
					break;
				case 'queries' :
					$url = admin_url( 'admin.php?page=woocommerce-product-search-report-queries' );
					break;
				default :
					$url = admin_url( 'admin.php?page=woocommerce-product-search' );
			}
		} else {
			switch ( $report ) {
				case 'searches' :
					$url = admin_url( 'admin.php?page=wc-reports&tab=search&report=searches' );
					break;
				case 'queries' :
					$url = admin_url( 'admin.php?page=wc-reports&tab=search&report=queries' );
					break;
				default :
					$url = admin_url( 'admin.php?page=wc-reports&tab=search' );
			}
		}
		return $url;
	}

	/**
	 * Registers the extension's navigation menu items.
	 */
	public static function admin_menu() {

		if ( !self::is_navigation() ) {
			return;
		}

		if (
			class_exists( '\Automattic\WooCommerce\Admin\Features\Navigation\Menu' ) &&
			class_exists( '\Automattic\WooCommerce\Admin\Features\Navigation\Screen' )
		) {

			add_submenu_page(
				'woocommerce',
				__( 'Search', 'woocommerce-product-search' ),
				__( 'Search', 'woocommerce-product-search' ),
				'manage_woocommerce',
				'woocommerce-product-search',
				array( __CLASS__, 'render_overview' ),
				self::$menu_position
			);
			add_submenu_page(
				'woocommerce-product-search',
				__( 'Welcome', 'woocommerce-product-search' ),
				__( 'Welcome', 'woocommerce-product-search' ),
				'manage_woocommerce',
				'woocommerce-product-search-overview',
				array( __CLASS__, 'render_overview' )
			);
			add_submenu_page(
				'woocommerce-product-search',
				__( 'Search Report', 'woocommerce-product-search' ),
				__( 'Search Report', 'woocommerce-product-search' ),
				'manage_woocommerce',
				'woocommerce-product-search-report-searches',
				array( __CLASS__, 'render_report_searches' )
			);
			add_submenu_page(
				'woocommerce-product-search',
				__( 'Query Report', 'woocommerce-product-search' ),
				__( 'Query Report', 'woocommerce-product-search' ),
				'manage_woocommerce',
				'woocommerce-product-search-report-queries',
				array( __CLASS__, 'render_report_queries' )
			);

			Menu::add_plugin_category(
				array(
					'id'      => 'woocommerce-product-search',
					'title'   => __( 'Search', 'woocommerce-product-search' ),
					'url'     => 'woocommerce-product-search',
					'migrate' => false,
					'order'   => self::$menu_position,

				)
			);

			Menu::add_plugin_item(
				array(
					'id'         => 'woocommerce-product-search-overview',
					'title'      => __( 'Overview', 'woocommerce-product-search' ),
					'capability' => 'manage_woocommerce',
					'url'        => 'woocommerce-product-search-overview',
					'parent'     => 'woocommerce-product-search',
					'matchExpression' => 'woocommerce-product-search|woocommerce-product-search-overview',
					'order' => 0
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'woocommerce-product-search-report-searches',
					'title'      => __( 'Search Report', 'woocommerce-product-search' ),
					'capability' => 'manage_woocommerce',
					'url'        => 'woocommerce-product-search-report-searches',
					'parent'     => 'woocommerce-product-search',

					'order' => 10
				)
			);

			Menu::add_plugin_item(
				array(
					'id'         => 'woocommerce-product-search-report-queries',
					'title'      => __( 'Query Report', 'woocommerce-product-search' ),
					'capability' => 'manage_woocommerce',
					'url'        => 'woocommerce-product-search-report-queries',
					'parent'     => 'woocommerce-product-search',

					'order' => 20
				)
			);
			Menu::add_plugin_item(
				array(
					'id'         => 'woocommerce-product-search-settings',
					'title'      => __( 'Settings', 'woocommerce-product-search' ),
					'capability' => 'manage_woocommerce',
					'url'        => WooCommerce_Product_Search_Admin::get_admin_section_url( WooCommerce_Product_Search_Admin::SECTION_GENERAL ),
					'parent'     => 'woocommerce-product-search',

					'order' => 30
				)
			);

			if ( WooCommerce_Product_Search_Admin::uses_classic_widgets() ) {
				Menu::add_plugin_item(
					array(
						'id'         => 'woocommerce-product-search-assistant',
						'title'      => __( 'Assistant', 'woocommerce-product-search' ),
						'capability' => 'manage_woocommerce',
						'url'        => WooCommerce_Product_Search_Admin::get_admin_section_url( WooCommerce_Product_Search_Admin::SECTION_ASSISTANT ),
						'parent'     => 'woocommerce-product-search',

						'order' => 40
					)
				);
			}

		}
	}

	public static function render_overview() {
		echo '<div class="wrap woocommerce woocommerce-product-search-overview">';
		echo '<h1>' . esc_html__( 'Overview', 'woocommerce-product-search' ) . '</h1>';

		echo '<style type="text/css">';
		echo 'div.wrap.woocommerce > div.updated:not(.wps-welcome), div.wrap.woocommerce > div.error { display: none; }';
		echo '.woocommerce-product-search-overview .updated.wps-welcome { border: none; border-radius: 8px; }';
		echo '.woocommerce-product-search-overview .updated.wps-welcome table > tbody > tr { display: flex; flex-direction: column; }';
		echo '.woocommerce-product-search-overview .updated.wps-welcome table > tbody > tr h3 { border-bottom: 1px solid #ccc; padding-bottom: 8px; }';
		echo '</style>';
		echo '<div style="background-color: #fff">';
		WooCommerce_Product_Search_Admin_Notice::admin_notices_welcome( array( 'force' => true, 'epilogue' => false ) );
		echo '</div>';
		echo '</div>';
	}

	public static function render_report_searches() {
		echo '<div class="wrap woocommerce">';
		echo '<h1>' . esc_html__( 'Searches', 'woocommerce-product-search' ) . '</h1>';
		WooCommerce_Product_Search_Admin_Reports::get_report( 'searches' );
		echo '</div>';
	}

	public static function render_report_queries() {
		echo '<div class="wrap woocommerce">';
		echo '<h1>' . esc_html__( 'Queries', 'woocommerce-product-search' ) . '</h1>';
		WooCommerce_Product_Search_Admin_Reports::get_report( 'queries' );
		echo '</div>';
	}
}

WooCommerce_Product_Search_Admin_Navigation::init();
