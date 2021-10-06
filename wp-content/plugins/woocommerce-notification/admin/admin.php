<?php

/*
Class Name: VI_WNOTIFICATION_Admin_Admin
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2016-2019 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WNOTIFICATION_Admin_Admin {
	protected $settings;

	function __construct() {
		$this->settings = new VI_WNOTIFICATION_Data();
		add_filter( 'plugin_action_links_woocommerce-notification/woocommerce-notification.php', array(
			$this,
			'settings_link'
		) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 9999999999 );
	}

	/*Check Auto update*/
	public function admin_init() {
		$key = VI_WNOTIFICATION_Admin_Settings::get_field( 'key', '' );
		/*Check update*/
		if ( class_exists( 'VillaTheme_Plugin_Check_Update' ) ) {
			new VillaTheme_Plugin_Check_Update ( VI_WNOTIFICATION_VERSION,                    // current version
				'https://villatheme.com/wp-json/downloads/v3',  // update path
				'woocommerce-notification/woocommerce-notification.php',                  // plugin file slug
				'woocommerce-notification', '5846', $key );
			$setting_url = admin_url( 'admin.php?page=woocommerce-notification' );
			new VillaTheme_Plugin_Updater( 'woocommerce-notification/woocommerce-notification.php', 'woocommerce-notification', $setting_url );
		}
	}
	public function admin_print_styles(){
		$background_image   = $this->settings->get_background_image();
		$custom_css='';
		if ( $background_image ) {
			$background_image_url  = woocommerce_notification_background_images( $background_image );

			$custom_css .= "#message-purchased .message-purchase-main::before{
				background-image: url('{$background_image_url}');  
				 border-radius:0;
			}";
		}
		?>
		<style id="woocommerce-notification-close-icon-color"></style>
		<style id="woocommerce-notification-background-image"><?php echo $custom_css?></style>
		<?php
	}
	/**
	 * Init Script in Admin
	 */
	public function admin_enqueue_scripts() {
		$this->settings = new VI_WNOTIFICATION_Data();
		$page           = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
		if ( $page == 'woocommerce-notification' ) {
			add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );

			global $wp_scripts, $wp_styles;
			$scripts = $wp_scripts->registered;
			if ( isset( $wp_styles->registered['rich-reviews'] ) ) {
				unset( $wp_styles->registered['rich-reviews'] );
				wp_dequeue_style( 'rich-reviews' );
			}
			foreach ( $scripts as $k => $script ) {
				preg_match( '/^\/wp-/i', $script->src, $result );
				if ( count( array_filter( $result ) ) ) {
					preg_match( '/(wp-content\/plugins|wp-content\/themes)/i', $script->src, $result1 );
					if ( count( array_filter( $result1 ) ) ) {
						wp_dequeue_script( $script->handle );
					}
				} else {
					if ( $script->handle !== 'query-monitor' ) {
						wp_dequeue_script( $script->handle );
					}
				}
			}
			/*Stylesheet*/
			wp_enqueue_style( 'woocommerce-notification-icons-close', VI_WNOTIFICATION_CSS . 'icons-close.css', array(), VI_WNOTIFICATION_VERSION );
			wp_enqueue_style( 'woocommerce-notification-input', VI_WNOTIFICATION_CSS . 'input.min.css' );
			wp_enqueue_style( 'woocommerce-notification-label', VI_WNOTIFICATION_CSS . 'label.min.css' );
			wp_enqueue_style( 'woocommerce-notification-image', VI_WNOTIFICATION_CSS . 'image.min.css' );
			wp_enqueue_style( 'woocommerce-notification-transition', VI_WNOTIFICATION_CSS . 'transition.min.css' );
			wp_enqueue_style( 'woocommerce-notification-form', VI_WNOTIFICATION_CSS . 'form.min.css' );
			wp_enqueue_style( 'woocommerce-notification-icon', VI_WNOTIFICATION_CSS . 'icon.min.css' );
			wp_enqueue_style( 'woocommerce-notification-dropdown', VI_WNOTIFICATION_CSS . 'dropdown.min.css' );
			wp_enqueue_style( 'woocommerce-notification-checkbox', VI_WNOTIFICATION_CSS . 'checkbox.min.css' );
			wp_enqueue_style( 'woocommerce-notification-segment', VI_WNOTIFICATION_CSS . 'segment.min.css' );
			wp_enqueue_style( 'woocommerce-notification-menu', VI_WNOTIFICATION_CSS . 'menu.min.css' );
			wp_enqueue_style( 'woocommerce-notification-tab', VI_WNOTIFICATION_CSS . 'tab.css' );
			wp_enqueue_style( 'woocommerce-notification-button', VI_WNOTIFICATION_CSS . 'button.min.css' );
			wp_enqueue_style( 'woocommerce-notification-grid', VI_WNOTIFICATION_CSS . 'grid.min.css' );
			wp_enqueue_style( 'woocommerce-notification-front', VI_WNOTIFICATION_CSS . 'woocommerce-notification.css' );
			wp_enqueue_style( 'woocommerce-notification-admin', VI_WNOTIFICATION_CSS . 'woocommerce-notification-admin.css' );
			wp_enqueue_style( 'woocommerce-notification-admin-templates', VI_WNOTIFICATION_CSS . 'woocommerce-notification-templates.css' );
			wp_enqueue_style( 'select2', VI_WNOTIFICATION_CSS . 'select2.min.css' );
			if ( woocommerce_version_check( '3.0.0' ) ) {
				wp_enqueue_script( 'select2' );
			} else {
				wp_enqueue_script( 'select2-v4', VI_WNOTIFICATION_JS . 'select2.js', array( 'jquery' ), '4.0.3' );
			}
			/*Script*/
			wp_enqueue_script( 'woocommerce-notification-dependsOn', VI_WNOTIFICATION_JS . 'dependsOn-1.0.2.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'woocommerce-notification-transition', VI_WNOTIFICATION_JS . 'transition.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'woocommerce-notification-dropdown', VI_WNOTIFICATION_JS . 'dropdown.js', array( 'jquery' ) );
			wp_enqueue_script( 'woocommerce-notification-checkbox', VI_WNOTIFICATION_JS . 'checkbox.js', array( 'jquery' ) );
			wp_enqueue_script( 'woocommerce-notification-tab', VI_WNOTIFICATION_JS . 'tab.js', array( 'jquery' ) );
			wp_enqueue_script( 'woocommerce-notification-address', VI_WNOTIFICATION_JS . 'jquery.address-1.6.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'woocommerce-notification-admin', VI_WNOTIFICATION_JS . 'woocommerce-notification-admin.js', array( 'jquery' ) );

			/*Color picker*/
			wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array(
				'jquery-ui-draggable',
				'jquery-ui-slider',
				'jquery-touch-punch'
			), false, 1 );

			/*Custom*/
			$highlight_color  = $this->settings->get_highlight_color();
			$text_color       = $this->settings->get_text_color();
			$background_color = $this->settings->get_background_color();
			$border_radius    = $this->settings->get_border_radius();
			$image_padding    = $this->settings->image_padding();
			$close_icon_color      = $this->settings->close_icon_color();
			$custom_css       = '#notify-close:before{color:'.$close_icon_color.';}';
			$custom_css       .= "#message-purchased .message-purchase-main{
                background-color: {$background_color};                       
                color:{$text_color};
                border-radius:{$border_radius}px;
                overflow:hidden;}
                .tab.segment #message-purchased img{border-radius:{$border_radius} 0 0 {$border_radius};}
                .tab.segment #message-purchased a, #message-purchased p span{color:{$highlight_color};}";

			$is_rtl             = is_rtl();
			if ( $image_padding ) {
				$padding_right = 20 - $image_padding;
				$custom_css    .= "#message-purchased .wn-notification-image-wrapper{padding:{$image_padding}px;}";
				if ( $is_rtl ) {
					$custom_css .= "#message-purchased .wn-notification-message-container{padding-right:{$padding_right}px;}";
				} else {
					$custom_css .= "#message-purchased .wn-notification-message-container{padding-left:{$padding_right}px;}";
				}
				$custom_css .= "#message-purchased .wn-notification-image{border-radius:{$border_radius}px;}";
			} else {
				$custom_css .= "#message-purchased .wn-notification-image-wrapper{padding:0;}";
				if ( $is_rtl ) {
					$custom_css .= "#message-purchased .wn-notification-message-container{padding-right:20px;}";
				} else {
					$custom_css .= "#message-purchased .wn-notification-message-container{padding-left:20px;}";
				}
			}

			wp_add_inline_style( 'woocommerce-notification-admin', $custom_css );
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
		$settings_link = '<a href="admin.php?page=woocommerce-notification" title="' . __( 'Settings', 'woocommerce-notification' ) . '">' . __( 'Settings', 'woocommerce-notification' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}


	/**
	 * Function init when run plugin+
	 */
	function init() {
		/*Register post type*/

		load_plugin_textdomain( 'woocommerce-notification' );
		$this->load_plugin_textdomain();
		if ( class_exists( 'VillaTheme_Support_Pro' ) ) {
			new VillaTheme_Support_Pro(
				array(
					'support'   => 'https://villatheme.com/supports/forum/plugins/woocommerce-notification/',
					'docs'      => 'http://docs.villatheme.com/?item=woocommerce-notification',
					'review'    => 'https://codecanyon.net/downloads',
					'css'       => VI_WNOTIFICATION_CSS,
					'image'     => VI_WNOTIFICATION_IMAGES,
					'slug'      => 'woocommerce-notification',
					'menu_slug' => 'woocommerce-notification',
					'version'   => VI_WNOTIFICATION_VERSION
				)
			);
		}
	}


	/**
	 * load Language translate
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-notification' );
		// Admin Locale
		if ( is_admin() ) {
			load_textdomain( 'woocommerce-notification', VI_WNOTIFICATION_LANGUAGES . "woocommerce-notification-$locale.mo" );
		}

		// Global + Frontend Locale
		load_textdomain( 'woocommerce-notification', VI_WNOTIFICATION_LANGUAGES . "woocommerce-notification-$locale.mo" );
		load_plugin_textdomain( 'woocommerce-notification', false, VI_WNOTIFICATION_LANGUAGES );
	}

	/**
	 * Register a custom menu page.
	 */
	public function menu_page() {
		add_menu_page( esc_html__( 'WooCommerce Notification', 'woocommerce-notification' ), esc_html__( 'Woo Notification', 'woocommerce-notification' ), 'manage_options', 'woocommerce-notification', array(
			'VI_WNOTIFICATION_Admin_Settings',
			'page_callback'
		), 'dashicons-megaphone', 2 );

	}

}
