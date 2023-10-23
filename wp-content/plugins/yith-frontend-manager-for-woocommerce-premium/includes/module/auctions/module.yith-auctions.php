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
 * @author     YITH <plugins@yithemes.com>
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
				$this->YITH_Auction_Admin = $YITH_Auction_Admin_Class::get_instance();
			}

            /* === Save Product === */
            add_action( 'yith_wcfm_product_save',array( $this->YITH_Auction_Admin, 'save_auction_data' ) );
            add_action( 'yith_wcfm_product_save',array( $this->YITH_Auction_Admin, 'set_product_meta_before_saving' ) );

            /* === Show Product metaboxes === */
            add_action( 'yith_wcfm_show_product_metaboxes', array( $this,'show_auction_metaboxes' ) );

		}

        /**
         * include admin class
         *
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
			$sections = YITH_Frontend_Manager()->get_section();
			$section_products = ! empty( $sections['products'] ) ? $sections['products'] : false;

			if( $section_products instanceof YITH_Frontend_Manager_Section && $section_products->is_current() && class_exists( 'YIT_Assets' ) ){
				YIT_Assets::instance()->register_styles_and_scripts();
			}

			$settings_section_dependencies = array( 'jquery', 'yith-plugin-fw-fields' );

			/* === CSS === */
			wp_register_style( 'yith-wcact-admin-settings-sections', YITH_WCACT_ASSETS_URL . 'css/admin-settings-sections.css', array(), YITH_WCACT_VERSION );
			wp_register_style( 'yith-wcact-admin-css', YITH_WCACT_ASSETS_URL . 'css/admin.css', array( 'yith-plugin-fw-fields', 'yith-wcact-admin-settings-sections' ), YITH_WCACT_VERSION );
			wp_register_style( 'yith-wcact-timepicker-css', YITH_WCACT_ASSETS_URL . 'css/timepicker.css', array(), YITH_WCACT_VERSION );
			/* === Script === */
			wp_register_script( 'yith-wcact-datepicker', YITH_WCACT_ASSETS_URL . 'js/datepicker.js', array( 'jquery', 'jquery-ui-datepicker' ), YITH_WCACT_VERSION, 'true' );
			wp_register_script( 'yith-wcact-timepicker', YITH_WCACT_ASSETS_URL . 'js/timepicker.js', array( 'jquery', 'jquery-ui-datepicker' ), YITH_WCACT_VERSION, 'true' );
			$premium_suffix = defined( 'YITH_WCACT_PREMIUM' ) && YITH_WCACT_PREMIUM ? '-premium' : '';
			wp_register_script( 'yith-wcact-admin', YITH_WCACT_ASSETS_URL . 'js/admin' . $premium_suffix . '.js', array( 'jquery' ), YITH_WCACT_VERSION, true );
			wp_register_script( 'yith-wcact-admin-settings-sections', YITH_WCACT_ASSETS_URL . 'js/admin-settings-sections.js', $settings_section_dependencies, YITH_WCACT_VERSION, true );

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

			$is_gui = ! empty( YITH_Frontend_Manager()->gui );
			$is_section = ! empty( YITH_Frontend_Manager()->gui->get_current_section_obj() );

			if( $is_gui && $is_section && false === YITH_Frontend_Manager()->gui->get_current_section_obj()->is_current( 'product' )  ){
				return false;
			}

			global $post;
            wp_enqueue_style('yith-wcact-auction-font', YITH_WCACT_ASSETS_URL . '/fonts/icons-font/style.css');

            /* === CSS === */
			wp_localize_script(
				'yith-wcact-admin',
				'object',
				array(
					'ajaxurl'            => admin_url( 'admin-ajax.php' ),
					'confirm_delete_bid' => esc_html__( 'Are you sure you want to delete the customer\'s bid?', 'yith-auctions-for-woocommerce' ),
					'id'                 => $post,
					'auction_by_default' => isset( $_GET['ywcact-create-first-auction'] ) ? true : false,
					'error_validation'	 => esc_html('Error: You have to set the %s for this auction.','yith-auctions-for-woocommerce'),
				)
			);
			wp_localize_script(
				'yith-wcact-admin-settings-sections',
				'admin_settings_section',
				array(
					'ajaxurl'                       => admin_url( 'admin-ajax.php' ),
					'minimun_increment_amount'      => esc_html__( 'Minimum increment amount', 'yith-auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
					'minimun_increment_amount_desc' => esc_html__( 'Set the minimum increment amount for manual bids', 'yith-auctions-for-woocommerce' ),
					'minimun_decrement_amount'      => esc_html__( 'Minimum decrement amount', 'yith-auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
					'minimun_decrement_amount_desc' => esc_html__( 'Set the minimum decrement amount for manual bids', 'yith-auctions-for-woocommerce' ),
				)
			);
			wp_enqueue_style( 'yith-wcact-auction-font', YITH_WCACT_ASSETS_URL . '/fonts/icons-font/style.css' );
			/* === CSS === */
			wp_enqueue_style( 'yith-wcact-timepicker-css' );
			/* === Script === */
			wp_enqueue_script( 'yith-wcact-datepicker' );
			wp_enqueue_script( 'yith-wcact-timepicker' );
			wp_enqueue_script( 'yith-wcact-admin' );
			wp_enqueue_script( 'yith-wcact-admin-settings-sections' );
			wp_deregister_script( 'acf-timepicker' );
			wp_enqueue_script( 'yith-wcact-admin' );
			wp_enqueue_script( 'yith-wcact-admin-settings-sections' );
			wp_enqueue_style( 'yith-wcact-admin-css' );
        }

        /**
         * Show Auction metabox
         *
         * @since  1.7
         */
        public function show_auction_metaboxes( $post ) {

            ?>
                <p><?php _e('Auction bid list','yith-auctions-for-woocommerce') ?></p>
            <?php

			$product = wc_get_product( $post );

			if ( $product ) {
				$instance = YITH_Auctions()->bids;
				$auction_list = $instance->get_bids_auction($product->get_id());

				$args = array(
					'post_id' => $post->ID,
					'auction_list' => $auction_list,
					'product' => $product,
					'pagination' => false,
				);

				wc_get_template('admin-list-bids.php', $args, '', YITH_WCACT_TEMPLATE_PATH . 'admin/');
			}
        }
	}
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Frontend_Manager_For_Vendor
 * @since  1.9
 */
if ( ! function_exists( 'YITH_WCFM_Auctions' ) ) {
	function YITH_WCFM_Auctions() {
		return YITH_WCFM_Auctions::instance();
	}
}
