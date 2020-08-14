<?php
/**
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_Frontend_Manager_For_Tab_Manager
 * @package    Yithemes
 * @since      Version 1.7
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Frontend_Manager_For_Tab_Manager' ) ) {

	class YITH_Frontend_Manager_For_Tab_Manager {

		private static  $_instance = null;

		/**
		 * @var YITH_WCTM_Admin_Premium $yith_tab_manager
		 */
		public $yith_tab_manager = null;


		public function __construct() {


			if( function_exists('YWTM_Product_Tab' ) && ( defined( 'YWTM_VERSION' ) && version_compare( YWTM_VERSION, '1.2.3','>=') ) ){


				$this->yith_tab_manager = YWTM_Product_Tab();

				add_action( 'yith_wcfm_products_enqueue_scripts', array( $this, 'add_product_scripts' ), 15 );

				add_action('yith_wcfm_product_save', array( $this->yith_tab_manager, 'save_product_tab_metabox' ), 30, 2 );

			}
		}


		/**
		 * enqueue product scripts
		 * @author Salvatore Strano
		 */
		public function add_product_scripts(){

			if( !wp_style_is( 'yit-tab-style' ) ){
				wp_enqueue_style( 'yit-tab-style', YWTM_ASSETS_URL . 'css/yith-tab-manager-admin.css', array(), YWTM_VERSION );
			}

			if( !wp_script_is( 'yit-tab-manager-script' ) ){

				wp_enqueue_script( 'yit-tab-manager-script', YWTM_ASSETS_URL . 'js/backend/' . yit_load_js_file( 'admin_tab_product.js' ), array( 'jquery' ), YWTM_VERSION, true );

			}

			YIT_Assets::instance()->register_styles_and_scripts();
			if( !wp_script_is('yith-plugin-fw-fields') ){
				wp_enqueue_script( 'yith-plugin-fw-fields' );
				wp_enqueue_script( 'codemirror' );
				wp_enqueue_script( 'codemirror-javascript' );

			}

			wp_enqueue_style( 'font-retina', YWTM_ASSETS_URL.'/fonts/retinaicon-font/style.css', array(), YWTM_VERSION );
			YIT_Icons::get_instance()->enqueue_scripts();
			wp_enqueue_style( 'font-awesome' );

		}

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return YITH_Frontend_Manager_For_Tab_Manager Main instance
		 *
		 * @since  1.7
		 * @author Salvatore Strano
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

/**
 * Get the module instance
 * @author Salvatore Strano
 */
if( !function_exists('YITH_Frontend_Manager_For_Tab_Manager' ) ){
	function YITH_Frontend_Manager_For_Tab_Manager(){

		return YITH_Frontend_Manager_For_Tab_Manager::instance();
	}
}