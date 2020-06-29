<?php

/*
Class Name: VI_WBOOSTSALES_Admin_Admin
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2015 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Admin_Admin {
	function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_filter(
			'plugin_action_links_woocommerce-boost-sales/woocommerce-boost-sales.php', array(
				$this,
				'settings_link'
			)
		);
		add_action( 'init', array( $this, 'init' ) );
//		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function admin_init() {

		$params = get_option( '_woocommerce_boost_sales' );
		$key    = '';
		if ( isset( $params['key'] ) && $params['key'] ) {
			$key = $params['key'];
		}
		$setting_url = admin_url( '?page=woocommerce-boost-sales' );;
		/*Check update*/
		if ( class_exists( 'VillaTheme_Plugin_Check_Update' ) ) {
			new VillaTheme_Plugin_Check_Update (
				VI_WBOOSTSALES_VERSION,                    // current version
				'https://villatheme.com/wp-json/downloads/v3',  // update path
				'woocommerce-boost-sales/woocommerce-boost-sales.php',                  // plugin file slug
				'woocommerce-boost-sales',
				'7466',
				$key,
				$setting_url
			);
			new VillaTheme_Plugin_Updater( 'woocommerce-boost-sales/woocommerce-boost-sales.php', 'woocommerce-boost-sales', $setting_url );
		}
	}

	/**
	 * Init Script in Admin
	 */
	public function admin_enqueue_scripts() {
		$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
		if ( $page == 'woocommerce-boost-sales' ) {
			wp_enqueue_style( 'woocommerce-boost-sales', VI_WBOOSTSALES_CSS . 'woocommerce-boost-sales-admin.css', array(), VI_WBOOSTSALES_VERSION );
			wp_enqueue_script( 'woocommerce-boost-sales', VI_WBOOSTSALES_JS . 'woocommerce-boost-sales-admin.js', array( 'jquery' ) );
		}
	}

	/**
	 * Link to Settings
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public function settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=woocommerce-boost-sales" title="' . __( 'Settings', 'woocommerce-boost-sales' ) . '">' . __( 'Settings', 'woocommerce-boost-sales' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}


	/**
	 * Function init when run plugin+
	 */
	function init() {
		load_plugin_textdomain( 'woocommerce-boost-sales' );
		$this->load_plugin_textdomain();
		if ( class_exists( 'VillaTheme_Support_Pro' ) ) {
			new VillaTheme_Support_Pro(
				array(
					'support'   => 'https://villatheme.com/supports/forum/plugins/woocommerce-boost-sales/',
					'docs'      => 'http://docs.villatheme.com/?item=woocommerce-boost-sales',
					'review'    => 'https://codecanyon.net/downloads',
					'css'       => VI_WBOOSTSALES_CSS,
					'image'     => VI_WBOOSTSALES_IMAGES,
					'slug'      => 'woocommerce-boost-sales',
					'menu_slug' => 'woocommerce-boost-sales',
					'version'   => VI_WBOOSTSALES_VERSION
				)
			);
		}
	}


	/**
	 * load Language translate
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		// Global + Frontend Locale
		unload_textdomain( 'woocommerce-boost-sales' );
		load_textdomain( 'woocommerce-boost-sales', VI_WBOOSTSALES_LANGUAGES . "woocommerce-boost-sales-$locale.mo" );
		load_plugin_textdomain( 'woocommerce-boost-sales', false, VI_WBOOSTSALES_LANGUAGES );
	}
}
