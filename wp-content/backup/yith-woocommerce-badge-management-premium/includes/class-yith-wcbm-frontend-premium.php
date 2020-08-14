<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_WCBM_PREMIUM' ) ) {
	exit; // Exit if accessed directly
}

require_once( 'functions.yith-wcbm-premium.php' );

/**
 * Implements features of FREE version of YITH WooCommerce Badge Management
 *
 * @class   YWCM_Cart_Messages
 * @package YITH WooCommerce Badge Management
 * @since   1.0.0
 * @author  Yithemes
 */


if ( ! class_exists( 'YITH_WCBM_Frontend_Premium' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCBM_Frontend_Premium extends YITH_WCBM_Frontend {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Frontend_Premium
		 * @since 1.0.0
		 */
		protected static $_instance;

		/**
		 * Constructor
		 *
		 * @access public
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since  1.0.0
		 */
		public function __construct() {
			parent::__construct();

			YITH_WCBM_Shortcodes::init();
		}

		/**
		 * Get the badge Id based on current language
		 *
		 * @access public
		 *
		 * @param $id_badge string id of badge
		 *
		 * @return int
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function get_wmpl_badge_id( $id_badge ) {
			$id_badge = absint( $id_badge );
			global $sitepress;

			if ( isset( $sitepress ) ) {
				if ( function_exists( 'wpml_object_id_filter' ) ) {
					$id_badge = wpml_object_id_filter( $id_badge, 'post', true );
				} elseif ( function_exists( 'icl_object_id' ) ) {
					$id_badge = icl_object_id( $id_badge, 'post', true );
				}
			}

			return $id_badge;
		}

		/**
		 * Return true if is allowed badge showing
		 * for example prevent badge showing in Wishilist Emails
		 *
		 * @access public
		 * @return bool
		 * @since  1.3.14
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function is_allowed_badge_showing() {
			$allowed        = parent::is_allowed_badge_showing();
			$hide_on_single = get_option( 'yith-wcbm-hide-on-single-product' ) == 'yes';
			$is_single      = did_action( 'woocommerce_before_single_product_summary' ) && ! did_action( 'woocommerce_after_single_product_summary' );
			$allowed        = $allowed && ( ! $hide_on_single || ! $is_single );

			$is_ajax_quick_edit = is_ajax() && isset( $_REQUEST['screen'] ) && 'edit-product' === $_REQUEST['screen'];
			$allowed            = $allowed && ! $is_ajax_quick_edit;

			return apply_filters( 'yith_wcbm_is_allowed_badge_showing', $allowed );
		}

		/**
		 * Hide or show default sale flash badge
		 *
		 * @access public
		 *
		 * @param string          $wc_sale_badge The WC sale badge.
		 * @param WP_Post         $post          The Post object.
		 * @param WC_Product|bool $product       The Product object.
		 *
		 * @return string
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function sale_flash( $wc_sale_badge, $post, $product = false ) {
			$on_sale_badge  = get_option( 'yith-wcbm-on-sale-badge', 'none' );
			$hide_on_single = get_option( 'yith-wcbm-hide-on-single-product' );

			if ( ( ! ! $on_sale_badge && $on_sale_badge != 'none' ) || ( is_product() && $hide_on_single == 'yes' ) ) {
				return '';
			}

			return parent::sale_flash( $wc_sale_badge, $post, $product );
		}

		public function enqueue_scripts() {
			parent::enqueue_scripts();
			$advanced_badge = get_option( 'yith-wcbm-advanced-on-sale-badge' );

			if ( ! empty( $advanced_badge ) && $advanced_badge != 'none' ) {
				wp_enqueue_style( 'yith_wcbm_advanced_badge_style_' . $advanced_badge, YITH_WCBM_ASSETS_URL . '/css/advanced-on-sale/' . $advanced_badge . '.css', array(), YITH_WCBM_VERSION );
			}

			if ( ! is_admin() ) {
				global $product, $post;
				$force_pos = get_option( 'yith-wcbm-force-badge-positioning', 'no' );

				wp_register_script( 'yith-wcbm-force-badge-positioning', YITH_WCBM_ASSETS_URL . '/js/force_badge_positioning.js', array(), YITH_WCBM_VERSION, true );
				wp_localize_script( 'yith-wcbm-force-badge-positioning', 'yith_wcbm_fp_params', apply_filters( 'yith_wcbm_force_positioning_params', array(
					'timeout'           => get_option( 'yith-wcbm-force-badge-positioning-timeout', 500 ),
					'on_scroll_mobile'  => get_option( 'yith-wcbm-force-badge-positioning-on-scroll-mobile', 'yes' ),
					'is_mobile'         => wp_is_mobile() ? 'yes' : 'no',
					'force_positioning' => $force_pos,
					'is_product'        => is_product() ? 'yes' : 'no',
					'is_shop'           => is_shop() ? 'yes' : 'no',
					'product_id'        => ! ! $product && is_object( $product ) && is_callable( $product, 'get_id' ) ? $product->get_id() : '',
					'post_id'           => ! ! $post ? $post->ID : '',
				) ) );

				if ( in_array( $force_pos, array( 'single-product', 'single-product-image' ) ) && is_product() ||
					 'shop' === $force_pos && is_shop() ||
					 'everywhere' === $force_pos
				) {
					wp_enqueue_script( 'yith-wcbm-force-badge-positioning' );
				}
			}
		}
	}
}
/**
 * Unique access to instance of YITH_WCBM_Frontend_Premium class
 *
 * @return YITH_WCBM_Frontend_Premium
 * @deprecated since 1.3.0 use YITH_WCBM_Frontend() instead
 * @since      1.0.0
 */
function YITH_WCBM_Frontend_Premium() {
	return YITH_WCBM_Frontend();
}
