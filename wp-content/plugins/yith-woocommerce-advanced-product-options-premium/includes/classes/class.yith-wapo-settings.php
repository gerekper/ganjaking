<?php
/**
 * Group class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Product Add-Ons
 * @version 1.0.0
 */

defined( 'ABSPATH' ) or exit;

/*
 *  
 */

if ( ! class_exists( 'YITH_WAPO_Settings' ) ) {

	class YITH_WAPO_Settings {

		public function __construct( $id = 0 ) {

			add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
			add_action( 'woocommerce_settings_tabs_yith_wapo_settings', __CLASS__ . '::settings_tab' );
			add_action( 'woocommerce_update_options_yith_wapo_settings', __CLASS__ . '::update_settings' );

		}

	    public static function add_settings_tab( $settings_tabs ) {

	        $settings_tabs['yith_wapo_settings'] = 'YITH WooCommerce Product Add-Ons'; //@since 1.1.0
	        return $settings_tabs;

	    }

		static function settings_tab() {

		    woocommerce_admin_fields( self::get_settings() );
		}

		static function update_settings() {
		    woocommerce_update_options( self::get_settings() );
		}

		static function get_settings() {

		    $settings = array();

		    return apply_filters( 'wc_yith_wapo_settings_settings', $settings );

		}

	}

}