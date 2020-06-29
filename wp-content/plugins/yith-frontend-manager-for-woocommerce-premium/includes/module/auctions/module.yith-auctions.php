<?php
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
 * @class      YITH_WCFM_Auctions
 * @package    Yithemes
 * @since      Version 1.4.13
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCFM_Auctions' ) ) {

	/**
	 * YITH_WCFM_Auctions Class
	 */
	class YITH_WCFM_Auctions {

		/**
		 * Main instance
		 */
		private static $_instance = null;

		/**
		 * YITH_Auctions_Admin instance
		 */
		public $YITH_Auction_Admin_Class = null;

		/**
		 * Construct
		 */
		public function __construct() {
			/* === Register Style === */
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
            add_action( 'yith_wcfm_products_enqueue_scripts', array( $this, 'add_auction_product_scripts' ), 15 );

            /* === Save Product === */
			$this->YITH_Auction_Admin = YITH_Auctions()->admin;

			if ( empty( $this->YITH_Auction_Admin ) ) {
				$YITH_Auction_Admin_Class = $this->include_admin_class();
				$this->YITH_Auction_Admin       = $YITH_Auction_Admin_Class::get_instance();
			}

            /* === Save Product === */
            add_action( 'yith_wcfm_product_save',array( $this->YITH_Auction_Admin, 'save_auction_data' ) );

            /* === Show Product metaboxes === */
            add_action( 'yith_wcfm_show_product_metaboxes', array( $this,'show_auction_metaboxes' ) );

		}

        /**
         * include admin class
         *
         * @author Andrea Grillo <andrea.grillo@yitheme.com>
         * @since  1.0
         * @return string classname
         */
        public function include_admin_class() {
            $classname = 'YITH_Auction_Admin';
            if ( ! class_exists( 'YITH_Auctions_Admin' ) ) {
                $admin_class = YITH_WCACT_PATH . 'includes/class.yith-wcact-auction-admin.php';
                if ( file_exists( $admin_class ) ) {
                    require_once( $admin_class );
                }

                $admin_premium_class = YITH_WCACT_PATH . 'includes/class.yith-wcact-auction-admin-premium.php';
                if ( file_exists( $admin_premium_class ) ) {
                    require_once( $admin_premium_class );
                    $classname = 'YITH_Auction_Admin_Premium';
                }
            }

            return trim( $classname );
        }


		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return YITH_WCFM_Auctions Main instance
		 *
		 * @since  1.7
		 * @author Carlos Rodríguez <carlos.rodriguez@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


		/**
		 * Register style and script
		 */
		public function register_scripts() {

		    global $post;
            wp_register_style('yith-wcact-admin-css', YITH_WCACT_ASSETS_URL . 'css/admin.css');
            wp_register_style('yith-wcact-timepicker-css', YITH_WCACT_ASSETS_URL . 'css/timepicker.css');
            /* === Script === */
            wp_register_script('yith-wcact-datepicker', YITH_WCACT_ASSETS_URL . 'js/datepicker.js', array('jquery', 'jquery-ui-datepicker'), YITH_WCACT_VERSION, 'true');
            wp_register_script('yith-wcact-timepicker', YITH_WCACT_ASSETS_URL . 'js/timepicker.js', array('jquery', 'jquery-ui-datepicker'), YITH_WCACT_VERSION, 'true');

            $premium_suffix = defined( 'YITH_WCACT_PREMIUM' ) && YITH_WCACT_PREMIUM ? '-premium' : '';
            wp_register_script( 'yith-wcact-admin', YITH_WCACT_ASSETS_URL . 'js/admin' . $premium_suffix . '.js', array( 'jquery' ), YITH_WCACT_VERSION, true );

            wp_localize_script('yith-wcact-admin', 'object', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'confirm_delete_bid' => __('Are you sure you want to delete the customer\'s bid?','yith-auctions-for-woocommerce'),
                'id' => $post,
            ));
		}
        /**
         * Enqueue style and script for auctions product
         */
		public function add_auction_product_scripts() {
            wp_enqueue_style('yith-wcact-auction-font', YITH_WCACT_ASSETS_URL . '/fonts/icons-font/style.css');

            /* === CSS === */
            wp_enqueue_style('yith-wcact-timepicker-css');

            /* === Script === */

            wp_enqueue_script('yith-wcact-datepicker');
            wp_enqueue_script('yith-wcact-timepicker');
            wp_enqueue_script('yith-wcact-admin');
            wp_deregister_script('acf-timepicker');

            wp_enqueue_style('yith-wcact-admin-css');
        }

        /**
         * Show Auction metabox
         *
         * @since  1.7
         * @author Carlos Rodríguez <carlos.rodriguez@yithemes.com>
         */
        public function show_auction_metaboxes( $post ) {

            ?>
                <p><?php _e('Auction bid list','yith-auctions-for-woocommerce') ?></p>
            <?php

			$args = array(
				'post_id' => $post->ID
			);
			wc_get_template('admin-list-bids.php', $args , '', YITH_WCACT_TEMPLATE_PATH . 'admin/');
        }
	}
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Frontend_Manager_For_Vendor
 * @since  1.9
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_WCFM_Auctions' ) ) {
	function YITH_WCFM_Auctions() {
		return YITH_WCFM_Auctions::instance();
	}
}