<?php
/**
 * Implements features of FREE version of YITH WooCommerce Badge Management
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Classes
 * @since   1.0.0
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Frontend_Premium' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the Frontend behaviors.
	 */
	class YITH_WCBM_Frontend_Premium extends YITH_WCBM_Frontend {
		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			parent::__construct();

			add_filter( 'wp_kses_allowed_html', array( $this, 'maybe_add_badge_tags_to_the_allowed_html' ) );
			add_filter( 'safe_style_css', array( $this, 'maybe_add_badge_style_to_the_safe_css' ) );

			YITH_WCBM_Shortcodes::init();
		}

		/**
		 * Add the badge tags in the allowed HTML of the wp_kses
		 *
		 * @param array $allowed_html The allowed HTML elements and their allowed attributes.
		 *
		 * @return array
		 */
		public function maybe_add_badge_tags_to_the_allowed_html( $allowed_html ) {
			if ( apply_filters( 'yith_wcbm_add_badge_tags_in_wp_kses_allowed_html', false ) ) {
				$product    = did_action( 'woocommerce_after_register_post_type' ) ? wc_get_product() : false;
				$is_allowed =
					( ! function_exists( 'is_product' ) || is_product() ) &&
					( ! function_exists( 'is_shop' ) || is_shop() ) &&
					( ! function_exists( 'is_category' ) || is_category() ) &&
					( ! function_exists( 'is_tag' ) || is_tag() ) &&
					( ! $product || yith_wcbm_product_has_badges( $product ) );
				if ( apply_filters( 'yith_wcbm_is_allowed_adding_badge_tags_in_wp_kses', $is_allowed ) ) {
					$allowed_html = array_merge( $allowed_html, yith_wcbm_get_badge_allowed_html() );
				}
			}

			return $allowed_html;
		}

		/**
		 * Add the badge css rules in the safe ones
		 *
		 * @param array $styles The list of safe css rules.
		 *
		 * @return array
		 */
		public function maybe_add_badge_style_to_the_safe_css( $styles ) {
			if ( apply_filters( 'yith_wcbm_add_badge_tags_in_wp_kses_allowed_html', false ) ) {
				$styles[] = 'stop-color';
			}

			return $styles;
		}

		/**
		 * Get the badge ID based on current language
		 *
		 * @access public
		 *
		 * @param string $id_badge Badge ID.
		 *
		 * @return int
		 * @since  1.0.0
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
		 * for example prevent badge showing in Wishlist Emails
		 *
		 * @access public
		 * @return bool
		 * @since  1.3.14
		 */
		public function is_allowed_badge_showing() {
			$allowed        = parent::is_allowed_badge_showing();
			$hide_on_single = wc_string_to_bool( get_option( 'yith-wcbm-hide-on-single-product' ) );
			$is_single      = ( did_action( 'woocommerce_before_single_product_summary' ) && ! did_action( 'woocommerce_after_single_product_summary' ) ) || ( did_action( 'yith_proteo_before_booking_product_image_gallery_in_header' ) && ! did_action( 'yith_proteo_after_booking_product_image_gallery_in_header' ) );
			$allowed        = $allowed && ( ! $hide_on_single || ! $is_single );

			$is_ajax_quick_edit = defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['screen'] ) && 'edit-product' === $_REQUEST['screen']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$allowed            = $allowed && ! $is_ajax_quick_edit;

			return apply_filters( 'yith_wcbm_is_allowed_badge_showing', $allowed );
		}

		/**
		 * Enqueue Scripts
		 */
		public function enqueue_scripts() {
			parent::enqueue_scripts();

			wp_register_script( 'yith_wcbm_frontend', YITH_WCBM_ASSETS_JS_URL . 'frontend.js', array( 'jquery' ), YITH_WCBM_VERSION, true );
			wp_register_script( 'yith-wcbm-force-badge-positioning', YITH_WCBM_ASSETS_URL . 'js/force-badge-positioning.js', array(), YITH_WCBM_VERSION, true );

			if ( is_product() ) {
				wp_enqueue_script( 'yith_wcbm_frontend' );
			}

			$data_to_localize = array();

			if ( ! is_admin() ) {
				$data_to_localize['yith-wcbm-force-badge-positioning'] = array(
					'object_name' => 'yithWcbmForceBadgePositioning',
					'data'        => array(
						'timeout'        => get_option( 'yith-wcbm-force-badge-positioning-timeout', 500 ),
						'onMobileScroll' => 'yes' === get_option( 'yith-wcbm-force-badge-positioning-on-scroll-mobile', 'no' ) ? 'yes' : 'no',
						'isMobile'       => wp_is_mobile() ? 'yes' : 'no',
					),
				);
				$data_to_localize['yith-wcbm-force-badge-positioning'] = apply_filters( 'yith_wcbm_force_positioning_params', $data_to_localize['yith-wcbm-force-badge-positioning'] );

				$force_pos = wc_string_to_bool( get_option( 'yith-wcbm-enable-force-badge-positioning', 'no' ) ) ? get_option( 'yith-wcbm-force-badge-positioning', 'single-product' ) : '';
				if ( 'everywhere' === $force_pos || in_array( $force_pos, array( 'single-product', 'single-product-image' ), true ) && is_product() || 'shop' === $force_pos && is_shop() ) {
					wp_enqueue_script( 'yith-wcbm-force-badge-positioning' );
				}
			}

			foreach ( $data_to_localize as $handle => $data ) {
				wp_localize_script( $handle, $data['object_name'], $data['data'] );
			}
		}

		/**
		 * Handle the badge visibility of variation product
		 *
		 * @since 2.0.2
		 */
		public static function is_allowed_variation_badge_showing() {
			$allow = true;

			$allow = $allow && ( ! function_exists( 'is_shop' ) || ! is_shop() );
			$allow = $allow && ( ! function_exists( 'is_product_category' ) || ! is_product_category() );
			$allow = $allow && ( ! function_exists( 'is_product_tag' ) || ! is_product_tag() );

			return apply_filters( 'yith_wcbm_is_allowed_variation_badge_showing', $allow );
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
