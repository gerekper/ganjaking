<?php
/**
 * class-woocommerce-product-search-admin-settings.php
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
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine\admin;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use com\itthinx\woocommerce\search\engine\Cache_Settings;
use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Admin Settings.
 */
class Admin_Settings extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Register a hook on the init action.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
	}

	/**
	 * Admin setup.
	 */
	public static function wp_init() {
		add_filter( 'woocommerce_settings_tabs_array', array( __CLASS__, 'woocommerce_settings_tabs_array' ), self::SETTINGS_POSITION );
		add_action( 'woocommerce_settings_' . self::SETTINGS_ID, array( __CLASS__, 'woocommerce_product_search' ) );
		add_action( 'woocommerce_settings_save_' . self::SETTINGS_ID, array( __CLASS__, 'save' ) );
	}

	/**
	 * Adds the Product Search tab to the WooCommerce Settings.
	 *
	 * @param array $pages
	 *
	 * @return array of settings pages
	 */
	public static function woocommerce_settings_tabs_array( $pages ) {
		$pages['product-search'] = __( 'Search', 'woocommerce-product-search' );
		return $pages;
	}

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {

		global $current_section;

		if ( empty( $current_section ) ) {
			$current_section = self::SECTION_GENERAL;
		}

		if ( !current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html( __( 'Access denied.', 'woocommerce-product-search' ) ) );
		}

		if ( isset( $_POST['submit'] ) ) {
			if ( wp_verify_nonce( $_POST[self::NONCE], 'set' ) ) {
				switch ( $current_section ) {
					case self::SECTION_GENERAL:
						require_once 'class-section-general.php';
						Section_General::save();
						break;
					case self::SECTION_WEIGHTS:
						require_once 'class-section-weights.php';
						Section_Weights::save();
						break;
					case self::SECTION_THUMBNAILS:
						require_once 'class-section-thumbnails.php';
						Section_Thumbnails::save();
						break;
					case self::SECTION_CSS:
						require_once 'class-section-css.php';
						Section_CSS::save();
						break;
					case self::SECTION_INDEX:
						require_once 'class-section-index.php';
						Section_Index::save();
						break;
					case self::SECTION_CACHE:
						require_once 'class-section-cache.php';
						Section_Cache::save();
						break;
					case self::SECTION_ASSISTANT:
						require_once 'class-section-assistant.php';
						Section_Assistant::save();
						break;
				}
			}
		}
	}

	/**
	 * Renders the admin section.
	 */
	public static function woocommerce_product_search() {

		global $current_section, $wpdb;

		if ( empty( $current_section ) ) {
			$current_section = self::SECTION_GENERAL;
		}

		if ( !current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html( __( 'Access denied.', 'woocommerce-product-search' ) ) );
		}

		wp_enqueue_script( 'product-search-admin', WOO_PS_PLUGIN_URL . '/js/product-search-admin.js', array( 'jquery', ), WOO_PS_PLUGIN_VERSION, true );
		wp_enqueue_style( 'wps-admin' );

		echo '<style type="text/css">';
		echo 'div.product-search-tabs ul li a { outline: none; }';
		echo '</style>';

		echo '<div class="woocommerce-product-search woocommerce-product-search-settings">';

		echo '<div class="product-search-tabs">';
		echo '<ul class="subsubsub">';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == 'general' ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_GENERAL ) ) );
		echo esc_html( __( 'General', 'woocommerce-product-search' ) );
		echo '</a>';
		echo '|';
		echo '</li>';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == 'weights' ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_WEIGHTS ) ) );
		echo esc_html( __( 'Weights', 'woocommerce-product-search' ) );
		echo '</a>';
		echo '|';
		echo '</li>';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == 'thumbnails' ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_THUMBNAILS ) ) );
		echo esc_html( __( 'Thumbnails', 'woocommerce-product-search' ) );
		echo '</a>';
		echo '|';
		echo '</li>';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == 'css' ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_CSS ) ) );
		echo esc_html( __( 'CSS', 'woocommerce-product-search' ) );
		echo '</a>';
		echo '|';
		echo '</li>';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == self::SECTION_INDEX ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_INDEX ) ) );
		echo esc_html( __( 'Index', 'woocommerce-product-search' ) );
		echo '</a>';
		echo '|';
		echo '</li>';
		echo '<li class="tab-header">';
		printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == self::SECTION_CACHE ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_CACHE ) ) );
		echo esc_html( __( 'Cache', 'woocommerce-product-search' ) );
		echo '</a>';
		if ( current_user_can( self::ASSISTANT_CONTROL_CAPABILITY ) ) {

			if ( self::uses_classic_widgets() ) {
				echo '|';
				echo '</li>';
				echo '<li class="tab-header">';
				printf( '<a class="%s" href="%s">', isset( $current_section ) && $current_section == self::SECTION_ASSISTANT ? 'current' : '', esc_url( self::get_admin_section_url( self::SECTION_ASSISTANT ) ) );
				echo esc_html( __( 'Assistant', 'woocommerce-product-search' ) );
				echo '</a>';
				echo '</li>';
			}
		} else {
			echo '</li>';
		}
		echo '&mdash;';
		echo '<li class="tab-header">';
		echo '<a href="' . esc_url( \WooCommerce_Product_Search_Admin_Navigation::get_report_url( 'searches' ) ) . '">' . esc_html__( 'Reports', 'woocommerce-product-search' ) . '</a>';
		echo '</li>';

		echo '<li class="tab-header">';
		echo '<a id="wps-faq-help-trigger" href="#">';
		echo wc_help_tip(
			wp_kses(
				__( 'The <strong>Search</strong> section in the <strong>Help</strong> tab above provides a brief overview.', 'woocommerce-product-search' ),
				array( 'strong' => array() )
			),
			true
		);
		echo '</a>';
		echo '</li>';

		echo '</ul>';
		echo '</div>';

		echo '<div style="clear:both"></div>';

		echo '<form action="" name="options" method="post">';
		echo '<div>';

		switch ( $current_section ) {
			case self::SECTION_GENERAL:
				require_once 'class-section-general.php';
				Section_General::render();
				break;
			case self::SECTION_WEIGHTS:
				require_once 'class-section-weights.php';
				Section_Weights::render();
				break;
			case self::SECTION_THUMBNAILS:
				require_once 'class-section-thumbnails.php';
				Section_Thumbnails::render();
				break;
			case self::SECTION_CSS:
				require_once 'class-section-css.php';
				Section_CSS::render();
				break;
			case self::SECTION_INDEX:
				require_once 'class-section-index.php';
				Section_Index::render();
				break;
			case self::SECTION_CACHE:
				require_once 'class-section-cache.php';
				Section_Cache::render();
				break;
			case self::SECTION_ASSISTANT:
				require_once 'class-section-assistant.php';
				Section_Assistant::render();
				break;
			case self::SECTION_WELCOME:
				require_once 'class-section-welcome.php';
				Section_Welcome::render();
				break;
		}

		global $hide_save_button;
		$hide_save_button = true;

		wp_nonce_field( 'set', self::NONCE );
		wp_nonce_field( 'woocommerce-settings' );
		echo '<p class="submit">';
		switch ( $current_section ) {
			case self::SECTION_WELCOME:
				break;
			case self::SECTION_ASSISTANT:
				echo '<input id="run-assistant-confirm" class="button button-primary" type="submit" name="submit" value="' . esc_attr( __( 'Add selected', 'woocommerce-product-search' ) ) . '"/>';
				break;
			case self::SECTION_CACHE:
				if ( !Cache_Settings::is_hardwired() ) {
					echo '<input class="button button-primary woocommerce-save-button" type="submit" name="submit" value="' . esc_attr( __( 'Save changes', 'woocommerce-product-search' ) ) . '"/>';
				}
				breaK;
			default:
				echo '<input class="button button-primary woocommerce-save-button" type="submit" name="submit" value="' . esc_attr( __( 'Save changes', 'woocommerce-product-search' ) ) . '"/>';
		}
		echo '</p>';
		echo '</div>';

		echo '<input type="hidden" name="save" value="1" />';

		echo '</form>';

		echo '</div>';

	}

}
Admin_Settings::init();
