<?php
/**
 * Frontend class
 *
 * @package YITH\BadgeManagement\Classes
 * @since   1.0.0
 * @author  YITH <plugins@yithemes.com>
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the Frontend behaviors.
	 */
	class YITH_WCBM_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WCBM_Frontend
		 */
		protected static $instance;

		/**
		 * Is in sidebar?
		 *
		 * @var bool
		 */
		private $is_in_sidebar = false;

		/**
		 * Is in mini cart?
		 *
		 * @since 1.2.7
		 * @var bool
		 */
		private $is_in_minicart = false;

		/**
		 * Plugin filters
		 *
		 * @since 1.3.7
		 * @var array
		 */
		public $badge_filters = array();

		/**
		 * Badges inline CSS transient key.
		 *
		 * @since 2.6.0
		 * @var string
		 */
		public static $badges_inline_css_transient = 'yith_wcbm_badges_inline_css';

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCBM_Frontend|YITH_WCBM_Frontend_Premium
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			return ! is_null( $self::$instance ) ? $self::$instance : $self::$instance = new $self();
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->badge_filters = array(
				array( 'woocommerce_single_product_image_html', array( $this, 'show_badge_on_product' ), 99, 2 ),
				array( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'show_badge_on_product_thumbnail' ), 99, 1 ),
				array( 'post_thumbnail_html', array( $this, 'show_badge_on_product' ), 999, 2 ),
				array( 'woocommerce_product_get_image', array( $this, 'show_badge_on_product' ), 999, 2 ),
			);

			add_filter( 'yith_wcbm_product_thumbnail_container', array( $this, 'show_badge_on_product' ), 999, 2 );

			$this->add_badge_filters();

			// edit sale flash badge.
			add_filter( 'woocommerce_sale_flash', array( $this, 'sale_flash' ), 20, 3 );
			add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'handle_default_wc_on_sale_badge_visibility' ), 10, 3 );
			add_filter( 'render_block_data', array( $this, 'handle_sale_flash_rendering_on_blocks' ) );

			// action to set this->is_in_sidebar.
			add_action( 'dynamic_sidebar_before', array( $this, 'set_is_in_sidebar' ) );
			add_action( 'dynamic_sidebar_after', array( $this, 'unset_is_in_sidebar' ) );

			// action to set this->is_in_minicart.
			add_action( 'woocommerce_before_mini_cart', array( $this, 'set_is_in_minicart' ) );
			add_action( 'woocommerce_after_mini_cart', array( $this, 'unset_is_in_minicart' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			/**
			 * Theme support!
			 */
			add_action( 'yith_wcbm_theme_badge_container_start', array( $this, 'theme_badge_container_start' ) );
			add_action( 'yith_wcbm_theme_badge_container_end', array( $this, 'theme_badge_container_end' ) );

			// add product classes based on badges.
			add_filter( 'woocommerce_post_class', array( $this, 'product_classes' ), 10, 2 );
		}

		/**
		 * Filter product classes to add specific classes based on badges
		 *
		 * @param array      $classes CSS Classes.
		 * @param WC_Product $product The Product.
		 *
		 * @return array
		 */
		public function product_classes( $classes, $product ) {
			if ( yith_wcbm_product_has_badges( $product ) ) {
				$classes[] = 'yith-wcbm-product-has-badges';
			}

			return $classes;
		}

		/**
		 * Add Badge Filters
		 *
		 * @since 1.3.7
		 */
		public function add_badge_filters() {
			foreach ( $this->badge_filters as $badge_filter ) {
				add_filter( $badge_filter[0], $badge_filter[1], $badge_filter[2], $badge_filter[3] );
			}
		}

		/**
		 * Remove Badge Filters
		 *
		 * @since 1.3.7
		 */
		public function remove_badge_filters() {
			foreach ( $this->badge_filters as $badge_filter ) {
				remove_filter( $badge_filter[0], $badge_filter[1], $badge_filter[2] );
			}
		}

		/**
		 * THEME SUPPORT
		 * start the container and start an OB
		 */
		public function theme_badge_container_start() {
			if ( ! apply_filters( 'yith_wcbm_theme_badge_container_start_check', true ) ) {
				return;
			}

			$this->remove_badge_filters();
			$this->badge_container_start();
		}

		/**
		 * Theme Support
		 * print the OB saved with the badges
		 */
		public function theme_badge_container_end() {
			if ( ! apply_filters( 'yith_wcbm_theme_badge_container_end_check', true ) ) {
				return;
			}

			$this->badge_container_end();
			$this->add_badge_filters();
		}

		/**
		 * Start the container and start an OB
		 */
		public function badge_container_start() {
			ob_start();
		}

		/**
		 * Print the OB saved with the badges
		 */
		public function badge_container_end() {
			global $post;
			global $product;
			$post_id = ! ! $post ? $post->ID : 0;
			$post_id = $product instanceof WC_Product ? $product->get_id() : $post_id;

			echo apply_filters( 'yith_wcbm_product_thumbnail_container', ob_get_clean(), $post_id ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}


		/**
		 * Show the badge on product
		 *
		 * @param string $thumb   The thumbnail.
		 * @param int    $post_id The Post ID.
		 *
		 * @return string
		 * @deprecated 1.2.12 free | 1.2.27 premium Use 'show_badge_on_product' instead.
		 */
		public function add_box_thumb( $thumb, $post_id ) {
			return $this->show_badge_on_product( $thumb, $post_id );
		}

		/**
		 * Set this->is in mini cart to true
		 *
		 * @access public
		 * @since  1.2.7
		 */
		public function set_is_in_minicart() {
			$this->is_in_minicart = true;
		}

		/**
		 * Set this->is in minicart to false
		 *
		 * @access public
		 * @since  1.2.7
		 */
		public function unset_is_in_minicart() {
			$this->is_in_minicart = false;
		}

		/**
		 * Set this->is in sidebar to true
		 *
		 * @access public
		 * @since  1.1.4
		 */
		public function set_is_in_sidebar() {
			$this->is_in_sidebar = true;
		}

		/**
		 * Set this->is in sidebar to false
		 *
		 * @access public
		 * @since  1.1.4
		 */
		public function unset_is_in_sidebar() {
			$this->is_in_sidebar = false;
		}

		/**
		 * Return true if is in sidebar
		 *
		 * @access public
		 * @return bool
		 * @since  1.1.4
		 */
		public function is_in_sidebar() {
			return $this->is_in_sidebar;
		}

		/**
		 * Return true if is in email
		 *
		 * @access public
		 * @return bool
		 * @since  1.2.15 [premium]
		 */
		public function is_in_email() {
			return ! ! did_action( 'woocommerce_email_header' );
		}

		/**
		 * Return true if is allowed badge showing
		 * for example prevent badge showing in Wishlist Emails
		 *
		 * @access public
		 * @return bool
		 * @since  2.0.0
		 */
		public function is_allowed_badge_showing() {
			$show_in_sidebar = 'yes' !== get_option( 'yith-wcbm-hide-in-sidebar', 'yes' );

			// not allowed in admin ajax actions.
			$wc_admin_ajax_actions = array( 'woocommerce_calc_line_taxes', 'woocommerce_save_order_items', 'woocommerce_add_order_item' );
			$is_wc_admin_ajax      = isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $wc_admin_ajax_actions, true ); // phpcs:ignore WordPress.Security.NonceVerification

			// Single product page.
			$hide_on_single = 'yes' === get_option( 'yith-wcbm-hide-on-single-product' );
			$is_single      = ( did_action( 'woocommerce_before_single_product_summary' ) && ! did_action( 'woocommerce_after_single_product_summary' ) ) || ( is_product() && function_exists( 'did_filter' ) && did_filter( 'woocommerce_single_product_image_thumbnail_html' ) && ! did_filter( 'render_block_woocommerce/product-image-gallery' ) );

			// not use is_cart() function to prevent issues with mini-cart.
			$is_cart = is_page( wc_get_page_id( 'cart' ) ) || wc_post_content_has_shortcode( 'woocommerce_cart' );

			$allowed = ( ! $this->is_in_sidebar() || $show_in_sidebar );
			$allowed = $allowed && ( ! $hide_on_single || ! $is_single );
			$allowed = $allowed && ! $this->is_in_minicart;
			$allowed = $allowed && ! $is_wc_admin_ajax;
			$allowed = $allowed && ( ! $is_cart || ( apply_filters( 'yith_wcbm_allow_badges_in_cart_page', false ) ) );
			$allowed = $allowed && ( ! is_checkout() || ( apply_filters( 'yith_wcbm_allow_badges_in_checkout_page', false ) ) );

			$allowed = $allowed && ( ! yith_wcbm_is_my_account_page() || ( apply_filters( 'yith_wcbm_allow_badges_in_my_account_page', false ) ) );
			$allowed = $allowed && ! $this->is_in_email();
			$allowed = $allowed && ! is_feed();

			// YITH WooCommerce Waiting list.
			$allowed = $allowed && ! did_action( 'send_yith_waitlist_mail_instock' );
			$allowed = $allowed && ! did_action( 'send_yith_waitlist_mail_subscribe' );

			// YITH WooCommerce Recently Viewed Products.
			$allowed = $allowed && ! did_action( 'send_yith_wrvp_mail' );

			// YITH WooCommerce Question & Answer.
			$allowed = $allowed && ! did_action( 'yith_questions_answers_after_new_answer' );
			$allowed = $allowed && ! did_action( 'yith_questions_answers_after_new_question' );

			// YITH WooCommerce Wishlist.
			if ( function_exists( 'yith_wcwl_is_wishlist_page' ) && function_exists( 'yith_wcwl_is_wishlist' ) ) {
				$allowed = $allowed && ! yith_wcwl_is_wishlist_page() && ! yith_wcwl_is_wishlist();
			}

			// YITH Frontend Manager.
			$allowed = $allowed && ! yith_wcbm_is_frontend_manager();

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
		 */
		public function sale_flash( $wc_sale_badge, $post, $product = false ) {
			return $this->is_default_on_sale_wc_badge_allowed( $product ) ? $wc_sale_badge : '';
		}

		/**
		 * Handle default WC "on sale" badge visibility
		 *
		 * @param string     $html    The Product HTML.
		 * @param object     $data    The Product HTML Contents.
		 * @param WC_Product $product The product.
		 *
		 * @return string
		 */
		public function handle_default_wc_on_sale_badge_visibility( $html, $data, $product ) {
			return $this->is_default_on_sale_wc_badge_allowed( $product ) ? $html : str_replace( $data->badge, '', $html );
		}

		/**
		 * Handle the visibility of the sale flash in product-image block
		 *
		 * @param array $block Block data.
		 * @return array
		 */
		public function handle_sale_flash_rendering_on_blocks( $block ) {
			if ( $block['blockName'] === 'woocommerce/product-image' ) {
				global $product;
				$block['attrs']['showSaleBadge'] = $this->is_default_on_sale_wc_badge_allowed( $product );
			}

			return $block;
		}

		/**
		 * Check whether the default WC "on sale" badge is visible
		 *
		 * @param WC_Product $product The product.
		 *
		 * @return bool
		 */
		public function is_default_on_sale_wc_badge_allowed( $product ) {
			$product = wc_get_product( $product );
			if ( $product && yith_wcbm_wpml_autosync_product_badge_translations() ) {
				$product = wc_get_product( $this->get_wpml_parent_id( $product->get_id() ) );
			}

			$hide_on_sale_default = wc_string_to_bool( get_option( 'yith-wcbm-hide-on-sale-default', 'no' ) ) ? get_option( 'yith-wcbm-when-hide-on-sale', 'all-products' ) : false;

			return ! ( 'all-products' === $hide_on_sale_default || ( $product && 'products-with-badge' === $hide_on_sale_default && yith_wcbm_product_has_badges( $product ) ) );
		}


		/**
		 * Get the badge Id based on current language
		 *
		 * @param int $badge_id Badge ID.
		 *
		 * @return int
		 */
		public function get_wmpl_badge_id( $badge_id ) {
			return $badge_id;
		}

		/**
		 * Edit image in products
		 *
		 * @param string              $image_html Product image.
		 * @param int|bool|WC_Product $product    The Product.
		 *
		 * @return string
		 */
		public function show_badge_on_product( $image_html, $product = false ) {
			// prevent multiple badge copies.
			if ( strpos( $image_html, 'container-image-and-badge' ) > 0 || ! $this->is_allowed_badge_showing() ) {
				return $image_html;
			}

			global $post;

			if ( $product instanceof WC_Product ) {
				$product_id = $product->get_id();
			} elseif ( false === $product && isset( $post->ID ) ) {
				$product_id = $post->ID;
			} else {
				$product_id = $product;
			}
			if ( yith_wcbm_wpml_autosync_product_badge_translations() ) {
				$product_id = $this->get_wpml_parent_id( $product_id );
			}
			$product = wc_get_product( $product_id );
			if ( $product ) {
				if ( ! defined( 'YITH_WCBM_PREMIUM' ) ) {
					$badge = yith_wcbm_get_badge_object( yith_wcbm_get_product_badge( $product_id ) );
					if ( $badge && $badge->is_enabled() ) {
						ob_start();
						$badge->display( $product->get_id() );
						$badges_html = ob_get_clean();
					}
				} else {
					$badges_html = yith_wcbm_get_badges_premium( $product );
				}

				if ( ! empty( $badges_html ) ) {
					$image_html .= $badges_html;

					if ( apply_filters( 'yith_wcbm_print_container_image_and_badge', true ) ) {
						$clearfix_class = apply_filters( 'yith_wcbm_clearfix_class', '' ); // through this filter you can set yith-wcbm-clearfix.
						$extra_classes  = apply_filters( 'yith_wcbm_container_image_and_badge_extra_classes', '' );
						$image_html     = "<div class='container-image-and-badge $clearfix_class $extra_classes'>" . $image_html . '</div><!--container-image-and-badge-->';
					}
				}
			}

			return $image_html;

		}

		/**
		 * Show badge on product thumbnail
		 *
		 * @param string $image_html The image HTML.
		 *
		 * @return bool|string
		 */
		public function show_badge_on_product_thumbnail( $image_html ) {
			global $product;
			if ( $product && $product instanceof WC_Product && ! did_action( 'woocommerce_product_thumbnails' ) ) {
				$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
				if ( yith_wcbm_wpml_autosync_product_badge_translations() ) {
					$product_id = $this->get_wpml_parent_id( $product_id );
				}

				if ( wc_get_product( $product_id ) && $this->is_allowed_badge_showing() ) {
					$print_badges_directly = false;
					$div_close             = '</div>';
					if ( version_compare( WC()->version, '3.0', '>=' ) && get_theme_support( 'wc-product-gallery-slider' ) ) {
						$content = rtrim( $image_html );
						if ( strrpos( $content, $div_close ) === strlen( $content ) - strlen( $div_close ) ) {
							$print_badges_directly = true;
							$image_html            = $content;
						}
					}

					if ( $print_badges_directly ) {
						$image_html = substr( $image_html, 0, -strlen( $div_close ) );
						if ( ! defined( 'YITH_WCBM_PREMIUM' ) || ! YITH_WCBM_PREMIUM ) {
							$badge_id = yith_wcbm_get_product_badge( $product_id );
							if ( yith_wcbm_is_badge_enabled( $badge_id ) ) {
								$image_html .= yith_wcbm_get_badge( $badge_id, $product_id );
							}
						} else {
							$image_html .= yith_wcbm_get_badges_premium( $product_id );
						}
						$image_html .= $div_close;
					} else {
						$image_html = $this->show_badge_on_product( $image_html, $product_id );
					}
				}
			}

			return $image_html;
		}

		/**
		 * Enqueue frontend styles and scripts
		 */
		public function enqueue_scripts() {
			wp_register_style( 'yith_wcbm_badge_style', YITH_WCBM_ASSETS_URL . 'css/frontend.css', array(), YITH_WCBM_VERSION );
			wp_register_style( 'yith-gfont-open-sans', YITH_WCBM_ASSETS_URL . 'fonts/open-sans/style.css', array(), YITH_WCBM_VERSION );

			wp_add_inline_style( 'yith_wcbm_badge_style', $this->get_inline_css() );

			wp_enqueue_style( 'yith_wcbm_badge_style' );
			wp_enqueue_style( 'yith-gfont-open-sans' );
		}

		/**
		 * Get the inline style
		 *
		 * @return string
		 */
		public function get_inline_css() {
			$badges     = yith_wcbm_badges()->get_badge_ids();
			$screen     = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			$screen_id  = $screen->id ?? false;
			$is_preview = 'edit-' . YITH_WCBM_Post_Types::$badge === $screen_id;
			$css        = get_transient( self::$badges_inline_css_transient );
			if ( ! $css ) {
				ob_start();
				if ( $badges ) {
					foreach ( $badges as $badge_id ) {
						$badge = yith_wcbm_get_badge_object( $badge_id );
						if ( $badge instanceof YITH_WCBM_Badge_Premium ) {
							$badge_meta = defined( 'YITH_WCBM_PREMIUM' ) && YITH_WCBM_PREMIUM && function_exists( 'yith_wcbm_get_badge_meta_premium' ) ? yith_wcbm_get_badge_meta_premium( $badge_id ) : yith_wcbm_get_badge_meta( $badge_id );
							if ( $is_preview ) {
								$badge_meta['position_type'] = 'fixed';
								$badge_meta['position']      = 'top';
								$badge_meta['alignment']     = 'left';
							}

							echo esc_html( $badge->get_style() );
						}

						echo esc_html( $badge->get_style() );
					}
				}

				$css = ob_get_clean();
				set_transient( self::$badges_inline_css_transient, $css );
			}

			if ( 'yes' === get_option( 'yith-wcbm-hide-on-mobile', 'no' ) ) {
				$mobile_breakpoint = get_option( 'yith-wcbm-mobile-breakpoint', 768 );

				$css .= "
				@media only screen and (max-width: {$mobile_breakpoint}px) {
					.yith-wcbm-badge{ display:none !important; }
				}
				";
			}

			return $css;
		}

		/**
		 * Delete badges inline CSS transient if the edited post is a Badge.
		 *
		 * @return void
		 * @since 2.6.0
		 */
		public static function delete_badges_inline_css_transient() {
			delete_transient( self::$badges_inline_css_transient );
		}


		/**
		 * Get WPML parent ID
		 *
		 * @param int    $id        The ID.
		 * @param string $post_type The post type.
		 *
		 * @return mixed
		 */
		public function get_wpml_parent_id( $id, $post_type = 'product' ) {
			if ( ! yith_wcbm_is_wpml_parent_based_on_default_language() ) {
				/**
				 * WPML Post Translation class
				 *
				 * @var WPML_Post_Translation $wpml_post_translations WPML Post Translation.
				 */
				global $wpml_post_translations;
				if ( $wpml_post_translations ) {
					$parent_id = $wpml_post_translations->get_original_element( $id );
					if ( $parent_id ) {
						$id = $parent_id;
					}
				}
			} else {
				// get the id in the default language.
				global $sitepress;
				if ( isset( $sitepress ) ) {
					$default_language = $sitepress->get_default_language();
					if ( function_exists( 'icl_object_id' ) ) {
						$id = icl_object_id( $id, $post_type, true, $default_language );
					} else {
						if ( function_exists( 'wpml_object_id_filter' ) ) {
							$id = wpml_object_id_filter( $id, $post_type, true, $default_language );
						}
					}
				}
			}

			return $id;
		}
	}
}

/**
 * Unique access to instance of YITH_WCBM_Frontend class
 *
 * @return YITH_WCBM_Frontend|YITH_WCBM_Frontend_Premium
 */
function yith_wcbm_frontend() {
	return YITH_WCBM_Frontend::get_instance();
}
