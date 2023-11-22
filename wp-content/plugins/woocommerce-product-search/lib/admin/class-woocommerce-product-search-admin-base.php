<?php
/**
 * class-woocommerce-product-search-admin-base.php
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

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Abstract admin base class.
 */
abstract class WooCommerce_Product_Search_Admin_Base {

	const NONCE                 = 'woocommerce-product-search-admin-nonce';
	const SETTINGS_POSITION     = 999;
	const SETTINGS_ID           = 'product-search';
	const SECTION_GENERAL       = 'general';
	const SECTION_WEIGHTS       = 'weights';
	const SECTION_THUMBNAILS    = 'thumbnails';
	const SECTION_CSS           = 'css';
	const SECTION_INDEX         = 'index';
	const SECTION_CACHE         = 'cache';
	const SECTION_ASSISTANT     = 'assistant';
	const SECTION_HELP          = 'help';
	const SECTION_WELCOME       = 'welcome';
	const HELP_POSITION         = 999;
	const INDEXER_CONTROL_CAPABILITY = 'manage_woocommerce';
	const ASSISTANT_CONTROL_CAPABILITY = 'edit_theme_options';
	const WIDGET_NUMBER_START   = 2;

	/**
	 * Returns the admin URL for the default or given section.
	 *
	 * @param string $section
	 */
	public static function get_admin_section_url( $section = '' ) {
		$path = 'admin.php?page=wc-settings&tab=product-search';
		switch ( $section ) {
			case self::SECTION_GENERAL :
			case self::SECTION_WEIGHTS :
			case self::SECTION_THUMBNAILS :
			case self::SECTION_CSS :
			case self::SECTION_INDEX :
			case self::SECTION_CACHE :
			case self::SECTION_ASSISTANT :
			case self::SECTION_HELP :
			case self::SECTION_WELCOME :
				break;
			default :
				$section = '';
		}
		if ( !empty( $section ) ) {
			$path .= '&section=' . $section;
		}
		return admin_url( $path );
	}

	/**
	 * Classic widgets used?
	 *
	 * @since 4.0.0
	 *
	 * @return boolean false if the widgets block editor is used
	 */
	public static function uses_classic_widgets() {

		global $wp_version;

		$result = false;

		if ( version_compare( $wp_version, '5.8.0' ) < 0 ) {
			$result = true;
		}

		if ( function_exists( 'wp_use_widgets_block_editor' ) ) {
			$result = !wp_use_widgets_block_editor();
		}
		return $result;
	}

}
