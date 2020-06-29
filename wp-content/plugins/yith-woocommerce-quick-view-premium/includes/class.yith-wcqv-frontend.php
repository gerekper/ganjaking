<?php
/**
 * Frontend class
 *
 * @author  YITH
 * @package YITH WooCommerce Quick View
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCQV' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCQV_Frontend' ) ) {
	/**
	 * Admin class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCQV_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WCQV_Frontend
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WCQV_VERSION;

		/**
		 * Button quick view position
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $position = '';

		/**
		 * Enable zoom magnifier
		 *
		 * @since 1.0.0
		 * @var boolean
		 */
		public $enable_zoom = false;

		/**
		 * Product images ids
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $product_images = array();

		/**
		 * Load quick view action
		 *
		 * @since 1.3.5
		 * @var string
		 */
		public $quick_view_ajax_action = 'yith_load_product_quick_view';

		/**
		 * Load variation gallery ajax
		 *
		 * @since 1.3.5
		 * @var string
		 */
		public $variation_gallery_ajax_action = 'yith_wcqv_load_variation_gallery';

		/**
		 * Handle add to cart ajax
		 *
		 * @since 1.4.0
		 * @var string
		 */
		public $add_to_cart_ajax_action = 'yith_wcqv_add_to_cart';

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WCQV_Frontend
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			$this->position = get_option( 'yith-wcqv-button-position', 'add-cart' );
			// Enable zoom option.
			$this->enable_zoom = ( get_option( 'yith-wcqv-enable-zoom-magnifier', 'no' ) === 'yes' && get_option( 'yith-wcqv-product-images-mode', 'classic' ) !== 'slider' && defined( 'YITH_YWZM_DIR' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 20 );

			if ( $this->use_wc_ajax() ) {
				add_action( 'wc_ajax_' . $this->quick_view_ajax_action, array( $this, 'load_product_quick_view_ajax' ) );
				add_action( 'wc_ajax_' . $this->variation_gallery_ajax_action, array( $this, 'load_variation_gallery_ajax' ) );
			} else {
				add_action( 'wp_ajax_' . $this->quick_view_ajax_action, array( $this, 'load_product_quick_view_ajax' ) );
				add_action( 'wp_ajax_' . $this->variation_gallery_ajax_action, array( $this, 'load_variation_gallery_ajax' ) );
			}
			// No Priv AJAX.
			add_action( 'wp_ajax_nopriv_' . $this->quick_view_ajax_action, array( $this, 'load_product_quick_view_ajax' ) );
			add_action( 'wp_ajax_nopriv_' . $this->variation_gallery_ajax_action, array( $this, 'load_variation_gallery_ajax' ) );


			add_action( 'init', array( $this, 'quick_view_action_button' ) );
			// Load action for product template.
			add_action( 'init', array( $this, 'yith_quick_view_action_template' ) );
			// Filter image size.
			add_filter( 'single_product_large_thumbnail_size', array( $this, 'yith_set_image_size' ) );
			// Add attachment id to variation data.
			add_filter( 'woocommerce_available_variation', array( $this, 'add_attachment_id' ), 10, 3 );
			// WooCommerce multilingual currency.
			add_filter( 'wcml_multi_currency_is_ajax', array( $this, 'set_correct_currency' ), 10, 1 );
			// Add redirect to checkout.
			add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'get_checkout_url' ), 99, 1 );

			add_shortcode( 'yith_quick_view', array( $this, 'quick_view_shortcode' ) );

			// Add plugin template.
			add_action( 'wp_footer', array( $this, 'yith_quick_view' ) );

			add_filter( 'woocommerce_add_to_cart_form_action', array( $this, 'avoid_redirect_to_single_page' ), 10, 1 );

			// Add product from single product page Ajax.
			add_action( 'wp_loaded', array( $this, 'add_item_cart_ajax' ), 30 );
		}


		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function enqueue_styles_scripts() {

			$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			wp_register_script( 'jquery-blockui', $assets_path . 'js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.60', true );
			wp_register_script( 'yith-wcqv-frontend', YITH_WCQV_ASSETS_URL . '/js/frontend' . $suffix . '.js', array( 'jquery' ), $this->version, true );

			$paths      = apply_filters( 'yith_wcqv_stylesheet_paths', array( WC()->template_path() . 'yith-quick-view.css', 'yith-quick-view.css' ) );
			$located    = locate_template( $paths, false, false );
			$search     = array( get_stylesheet_directory(), get_template_directory() );
			$replace    = array( get_stylesheet_directory_uri(), get_template_directory_uri() );
			$stylesheet = ! empty( $located ) ? str_replace( $search, $replace, $located ) : YITH_WCQV_ASSETS_URL . '/css/yith-quick-view.css';

			wp_register_style( 'yith-quick-view', $stylesheet, array(), $this->version );

			wp_register_style( 'external-plugin', YITH_WCQV_ASSETS_URL . '/css/style-external.css', array(), $this->version );
			wp_register_script( 'external-plugin', YITH_WCQV_ASSETS_URL . '/js/scripts-external.min.js', array( 'jquery' ), $this->version, true );
			wp_register_script( 'bxslider-plugin', YITH_WCQV_ASSETS_URL . '/js/jquery.bxslider.min.js', array( 'jquery' ), $this->version, true );

			wp_enqueue_style( 'external-plugin' );
			wp_enqueue_style( 'yith-quick-view' );
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'yith-wcqv-frontend' );
			wp_enqueue_script( 'external-plugin' );
			wp_enqueue_script( 'bxslider-plugin' );

			if ( $this->enable_zoom ) {
				wp_enqueue_style( 'ywzm-magnifier' );
				wp_enqueue_script( 'ywzm_frontend' );
			}

			// Add custom style!
			$inline_css = yith_wcqv_get_custom_style();
			wp_add_inline_style( 'yith-quick-view', $inline_css );

			if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && ! empty( $GLOBALS['Product_Addon_Display'] ) ) {
				$class = $GLOBALS['Product_Addon_Display'];
				is_callable( array( $class, 'addon_scripts' ) ) && $class->addon_scripts();
			}
		}

		/**
		 * Add quick view button in wc product loop
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param integer $product_id The product ID.
		 * @param string  $label      The button label.
		 * @param string  $type       The button type.
		 * @param boolean $return     True to return html, false to echo.
		 * @param string  $position   The button position.
		 * @return string|void
		 */
		public function yith_add_quick_view_button( $product_id = 0, $label = '', $type = '', $return = false, $position = '' ) {

			global $product;

			if ( ! $product_id && $product instanceof WC_Product ) {
				$product_id = $product->get_id();
			}

			$content = '';
			$button  = '';
			if ( $product_id && apply_filters( 'yith_wcqv_show_quick_view_button', true, $product_id ) ) {
				if ( ! $type ) {
					$type = get_option( 'yith-wcqv-button-type', 'button' );
				}

				if ( 'icon' === $type ) {
					$icon    = get_option( 'yith-wcqv-button-icon', YITH_WCQV_ASSETS_URL . '/image/qv-icon.png' );
					$content = '<img src="' . esc_url( $icon ) . '" class="yith-wcqv-icon"/>';
				} else {
					if ( ! $label ) {
						$label = $this->get_button_label();
					}
					$content = '<span>' . $label . '</span>';
				}

				if ( ! $position ) {
					$position = $this->position;
				}
				if ( 'image' === $position ) {
					$button = '<div class="yith-wcqv-button inside-thumb" data-product_id="' . esc_attr( $product_id ) . '">' . $content . '</div>';
				} else {
					$class  = 'button' === $type ? 'button' : 'qvicon';
					$button = '<a href="#" class="yith-wcqv-button ' . $class . '" data-product_id="' . esc_attr( $product_id ) . '">' . $content . '</a>';
				}

				// Let's third part filter button html!
				$button = apply_filters( 'yith_wcqv_button_html', $button, $product_id, $content );
			}

			if ( $return ) {
				return $button;
			} else {
				echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Add quick view button in wishlist table
		 *
		 * @access public
		 * @since  1.0.7
		 * @author Francesco Licandro
		 */
		public function add_quick_view_button_in_wishlist() {
			global $product;

			if ( ! $product || get_option( 'yith-wcqv-enable-wishlist', 'no' ) !== 'yes' || get_option( 'yith-wcqv-modal-type', 'yith-modal' ) !== 'yith-modal' ) {
				return;
			}

			$label      = $this->get_button_label();
			$product_id = $product->get_id();

			echo '<a href="#" class="yith-wcqv-button button" data-product_id="' . esc_attr( $product_id ) . '">' . $label . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Enqueue scripts and pass variable to js used in quick view
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return bool
		 */
		public function yith_woocommerce_quick_view() {

			wp_enqueue_script( 'wc-add-to-cart-variation' );

			// Enqueue WC Color and Label Variation style.
			wp_enqueue_script( 'yith_wccl_frontend' );
			wp_enqueue_style( 'yith_wccl_frontend' );

			$lightbox_en       = get_option( 'yith-wcqv-enable-lightbox', 'yes' ) === 'yes';
			$mobile            = wp_is_mobile();
			$type              = get_option( 'yith-wcqv-modal-type', 'yith-modal' );
			$closing_cascading = get_option( 'yith-wcqv-closing-cascading', 'not-scroll' );

			// If enabled load prettyPhoto css.
			if ( $lightbox_en ) {
				$assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
				wp_enqueue_script( 'prettyPhoto', $assets_path . 'js/prettyPhoto/jquery.prettyPhoto.min.js', array( 'jquery' ), WC()->version, true );
				wp_enqueue_style( 'woocommerce_prettyPhoto_css', $assets_path . 'css/prettyPhoto.css', array(), WC()->version );
			}

			// Allow user to load custom style and scripts.
			do_action( 'yith_quick_view_custom_style_scripts' );

			wp_localize_script(
				'yith-wcqv-frontend',
				'yith_qv',
				apply_filters(
					'yith_wcqv_localize_script_array',
					array(
						'ajaxurl'                         => $this->use_wc_ajax() ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : admin_url( 'admin-ajax.php', 'relative' ),
						'ajaxQuickView'                   => $this->quick_view_ajax_action,
						'ajaxAddToCart'                   => $this->add_to_cart_ajax_action,
						'ajaxVariationGallery'            => ( defined( 'YITH_WCCL_PREMIUM' ) && function_exists( 'yith_wccl_get_variation_gallery' ) ) ? $this->variation_gallery_ajax_action : '',
						'loader'                          => apply_filters( 'yith_quick_view_loader_gif', YITH_WCQV_ASSETS_URL . '/image/qv-loader.gif' ),
						'increment_plugin'                => class_exists( 'WooCommerce_Quantity_Increment' ), // Added compatibility with woocommerce-quantity-increment plugin.
						'type'                            => $type,
						'closing_cascading'               => $closing_cascading,
						'ismobile'                        => $mobile,
						'imagesMode'                      => get_option( 'yith-wcqv-product-images-mode', 'classic' ),
						'ajaxcart'                        => get_option( 'yith-wcqv-ajax-add-to-cart', 'no' ) === 'yes',
						'closeOnAjaxCart'                 => get_option( 'yith-wcqv-close-after-add-to-cart', 'no' ) === 'yes',
						'lang'                            => defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '',
						'enable_loading'                  => get_option( 'yith-wcqv-enable-loading-effect', 'no' ) === 'yes',
						'loading_text'                    => get_option( 'yith-wcqv-enable-loading-text' ),
						'enable_zoom'                     => get_option( 'yith-wcqv-enable-zoom-magnifier', 'no' ) === 'yes',
						'redirect_checkout'               => get_option( 'yith-wcqv-ajax-redirect-to-checkout', 'no' ) === 'yes',
						'checkout_url'                    => apply_filters( 'yith_wcqv_redirect_checkout_url', wc_get_checkout_url() ),
						'main_product'                    => apply_filters( 'yith_wcqv_main_product_selector', 'li.product, div.product-small.product' ),
						'main_product_link'               => apply_filters( 'yith_wcqv_main_product_link_selector', 'li.product > a, div.product .box-image a' ),
						'popup_size_width'                => get_option( 'yith-quick-view-modal-width', '1000' ),
						'popup_size_height'               => get_option( 'yith-quick-view-modal-height', '500' ),
						'main_image_class'                => apply_filters( 'yith_wcqv_main_image_class', 'img.attachment-shop_catalog,img.kw-prodimage-img,img.attachment-woocommerce_thumbnail,img.woocommerce-placeholder' ),
						'enable_double_tab'               => apply_filters( 'yith_quick_view_enable_double_tap', true ),
						'timeout_close_quick_view'        => apply_filters( 'yith_wcqv_timeout_close_quick_view', 1500 ),
						'enable_images_slider_pagination' => apply_filters( 'yith_wcqv_enable_images_slider_pagination', false ),
					)
				)
			);

			return true;
		}

		/**
		 * Ajax action to load product in quick view
		 *
		 * @access     public
		 * @since      1.0.0
		 * @author     Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 * @deprecated Use instead load_product_quick_view_ajax
		 */
		public function yith_load_product_quick_view_ajax() {
			$this->load_product_quick_view_ajax();
		}

		/**
		 * Ajax action to load product in quick view
		 *
		 * @access public
		 * @since  1.3.5
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function load_product_quick_view_ajax() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_REQUEST['product_id'] ) ) {
				die();
			}

			global $sitepress;

			/**
			 * WPML Suppot:  Localize Ajax Call
			 */
			$lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : '';
			if ( defined( 'ICL_LANGUAGE_CODE' ) && $lang && isset( $sitepress ) ) {
				$sitepress->switch_lang( $lang, true );
			}

			$product_id  = intval( $_REQUEST['product_id'] );
			$nav         = get_option( 'yith-wcqv-enable-nav', 'yes' ) === 'yes';
			$in_same_cat = get_option( 'yith-wcqv-enable-nav-same-category', 'no' ) === 'yes';
			$style       = get_option( 'yith-wcqv-nav-style', 'reveal' );

			// Remove product thumbnails gallery.
			remove_all_actions( 'woocommerce_product_thumbnails' );
			// Remove badge from nav.
			if ( function_exists( 'YITH_WCBM_Frontend' ) ) {
				remove_filter( 'post_thumbnail_html', array( YITH_WCBM_Frontend(), 'add_box_thumb' ), 999 );
			}

			// Fix compatibility with WooCommerce Variation Swatches (product images not showed properly inside the gallery in quick view).
			if ( class_exists( 'Woo_Variation_Swatches' ) ) {
				remove_all_filters( 'wp_get_attachment_image_attributes' );
			}

			$product   = wc_get_product( $product_id );
			$post_type = $product instanceof WC_Product_Variation ? 'product_variation' : 'product';

			// Set the main wp query for the product.
			wp( 'p=' . $product_id . '&post_type=' . $post_type );

			$prev_product_id = false;
			$next_product_id = false;

			if ( $nav ) {
				// If in same category is enabled, get next e prev post.
				if ( $in_same_cat ) {
					$taxonomy = apply_filters( 'yith_wcqv_taxonomy_quick_view_navigation', 'product_cat' );

					$prev_product = wc_get_product( get_previous_post( $in_same_cat, '', $taxonomy ) );
					if ( $prev_product && ! post_password_required( $prev_product ) ) {
						$prev_product_id = $prev_product->get_id();
					}

					$next_product = wc_get_product( get_next_post( $in_same_cat, '', $taxonomy ) );
					if ( $next_product && ! post_password_required( $next_product ) ) {
						$next_product_id = $next_product->get_id();
					}
				} else {
					if ( isset( $_REQUEST['prev_product_id'] ) ) {
						$prev_product_id = intval( $_REQUEST['prev_product_id'] );
					}
					if ( isset( $_REQUEST['next_product_id'] ) ) {
						$next_product_id = intval( $_REQUEST['next_product_id'] );
					}
				}
			}

			// Prev Product Preview.
			$prev_product_preview = $prev_product_id ? get_the_post_thumbnail( $prev_product_id, 'shop_thumbnail' ) : '';
			$prev_product_preview .= ( $prev_product_id && 'diamond' !== $style ) ? '<h4>' . get_the_title( $prev_product_id ) . '</h4>' : '';

			// Next Product Preview.
			$next_product_preview = $next_product_id ? get_the_post_thumbnail( $next_product_id, 'shop_thumbnail' ) : '';
			$next_product_preview .= ( $next_product_id && 'diamond' !== $style ) ? '<h4>' . get_the_title( $next_product_id ) . '</h4>' : '';

			// Re-Add badge from nav.
			if ( function_exists( 'YITH_WCBM_Frontend' ) ) {
				add_filter( 'post_thumbnail_html', array( YITH_WCBM_Frontend(), 'add_box_thumb' ), 999, 2 );
			}

			// Add hidden to add to cart form.
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_hidden_to_cart_form' ) );

			// Change template for variable products.
			$attributes = false;
			if ( isset( $GLOBALS['yith_wccl'] ) ) {
				$GLOBALS['yith_wccl']->obj = new YITH_WCCL_Frontend();
				$GLOBALS['yith_wccl']->obj->override();
			} elseif ( defined( 'YITH_WCCL_PREMIUM' ) && YITH_WCCL_PREMIUM && class_exists( 'YITH_WCCL_Frontend' ) ) {
				$attributes = YITH_WCCL_Frontend()->create_attributes_json( $product_id, true );
			}

			// Allow custom action for user.
			do_action( 'yith_load_product_quick_view_custom_action' );

			$product_link = get_permalink( $product_id );
			if ( is_ssl() ) {
				$product_link = str_replace( 'http://', 'https://', $product_link );
			}

			ob_start();
			wc_get_template( 'yith-quick-view-content.php', array(), '', YITH_WCQV_DIR . 'templates/' );
			$html = ob_get_clean();

			// phpcs:enable WordPress.Security.NonceVerification.Recommended
			wp_send_json(
				array(
					'html'                 => $html,
					'prod_attr'            => $attributes,
					'prev_product'         => $prev_product_id,
					'prev_product_preview' => $prev_product_preview,
					'next_product'         => $next_product_id,
					'next_product_preview' => $next_product_preview,
					'product_link'         => $product_link,
				)
			);
		}

		/**
		 * Add hidden to add to cart form
		 *
		 * @since  1.1.0
		 * @author Francesco Licandro
		 */
		public function add_hidden_to_cart_form() {
			echo '<input type="hidden" name="yith_is_quick_view" id="yith_is_quick_view" value="1"/>';
		}

		/**
		 * Return the checkout url
		 *
		 * @since  1.1.0
		 * @author Francesco Licandro
		 * @param string $url Add to cart redirect url.
		 * @return string
		 */
		public function get_checkout_url( $url ) {
			if ( get_option( 'yith-wcqv-ajax-redirect-to-checkout', 'no' ) !== 'yes' || get_option( 'yith-wcqv-ajax-add-to-cart', 'no' ) === 'yes' ) {
				return $url;
			}

			return wc_get_checkout_url();
		}

		/**
		 * Load quick view template
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function yith_quick_view() {
			$this->yith_woocommerce_quick_view();

			$type = get_option( 'yith-wcqv-modal-type', 'yith-modal' );

			$args = array(
				'type'      => $type,
				'effect'    => ( 'yith-modal' === $type ) ? get_option( 'yith-quick-view-modal-effect', 'slide-in' ) : '',
				'nav'       => get_option( 'yith-wcqv-enable-nav', 'yes' ) === 'yes',
				'nav_style' => get_option( 'yith-wcqv-nav-style', 'reveal' ),
				'is_mobile' => wp_is_mobile() ? 'is-mobile' : '',
			);

			$args = apply_filters( 'yith_args_quick_view_template', $args );

			wc_get_template( 'yith-quick-view.php', $args, '', YITH_WCQV_DIR . 'templates/' );
		}

		/**
		 * Load share template for single product in quick view
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function yith_quick_view_share() {
			wc_get_template( 'yith-quick-view-share.php', array(), '', YITH_WCQV_DIR . 'templates/' );
		}

		/**
		 * Quick view action button
		 *
		 * @access public
		 * @since  1.5.2
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function quick_view_action_button() {
			if ( 'add-cart' === $this->position ) {
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'yith_add_quick_view_button' ), 15 );
			} else {

				if( yith_wcqv_is_flatsome() ) {
					add_action( 'flatsome_woocommerce_shop_loop_images', array( $this, 'yith_add_quick_view_button' ), 15 );
				} else {
					add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'yith_add_quick_view_button' ), 15 );
				}
			}

			add_action( 'yith_wcwl_table_after_product_name', array( $this, 'add_quick_view_button_in_wishlist' ), 15 );
		}

		/**
		 * Load wc action for quick view product template
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function yith_quick_view_action_template() {

		    if( ! $this->yith_is_quick_view() ) {
				return;
			}

			$this->set_action_dynamic_pricing();

			if ( get_option( 'yith-wcqv-product-show-thumb', 'yes' ) === 'yes' ) {

				add_action( 'yith_wcqv_product_image', 'woocommerce_show_product_sale_flash', 10 );
				add_action( 'yith_wcqv_product_image', array( $this, 'custom_thumb_html' ) );
			}
			if ( get_option( 'yith-wcqv-product-show-title', 'yes' ) === 'yes' ) {
				add_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_title', 5 );
			}
			if ( get_option( 'yith-wcqv-product-show-rating', 'yes' ) === 'yes' ) {
				add_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_rating', 10 );
			}
			if ( get_option( 'yith-wcqv-product-show-price', 'yes' ) === 'yes' ) {
				add_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_price', 15 );
			}
			if ( get_option( 'yith-wcqv-product-show-excerpt', 'yes' ) === 'yes' ) {

				if ( get_option( 'yith-wcqv-product-full-description', 'yes' ) === 'no' ) {
					add_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_excerpt', 20 );
				} else {
					add_action( 'yith_wcqv_product_summary', array( $this, 'get_full_description' ), 20 );
				}
			}
			if ( get_option( 'yith-wcqv-product-show-add-to-cart', 'yes' ) === 'yes' ) {
				add_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_add_to_cart', 25 );
				/* Compatibility with YITH WooCommerce Color and Label Variations */
				add_action( 'yith_wcqv_product_summary', array( $this, 'show_add_to_cart_for_single_variation' ), 25 );

				/* Compatibility with YITH WooCommerce One Click Checkout Premium */
				if ( function_exists( 'YITH_WOCC_Frontend' ) ) {
					// TODO find a better solution
					$wocc_class = class_exists( 'YITH_WOCC_Frontend_Premium' ) ? 'YITH_WOCC_Frontend_Premium' : 'YITH_WOCC_Frontend';
					add_action( 'yith_wcqv_product_summary', array( $wocc_class(), 'add_button' ), 22 );
				}
			}

			if ( defined( 'YITH_YWRAQ_VERSION' ) && class_exists( 'YITH_YWRAQ_Frontend' ) && get_option( 'yith-wcqv-product-show-quote', 'yes' ) === 'yes' ) {
				add_action( 'yith_wcqv_product_summary', array( YITH_YWRAQ_Frontend(), 'show_button_single_page' ), 5 );
				add_action( 'yith_wcqv_product_summary', array( $this, 'hide_add_to_cart' ), 27 );
			}

			if ( defined( 'YWCNP_PREMIUM' ) && function_exists( 'YITH_Name_Your_Price_Frontend' ) ) {
				YITH_Name_Your_Price_Frontend();

			}
			if ( defined( 'YITH_WCWL' ) && YITH_WCWL && get_option( 'yith-wcqv-product-show-wishlist', 'yes' ) === 'yes' ) {
				add_action( 'yith_wcqv_product_summary', array( $this, 'yith_wishlist_quick_view' ), 27 );
			}
			if ( shortcode_exists( 'yith_compare_button' ) && get_option( 'yith-wcqv-product-show-compare', 'yes' ) === 'yes' ) {
				add_action( 'yith_wcqv_product_summary', array( $this, 'yith_compare_quick_view' ), 27 );
			}

			if ( get_option( 'yith-wcqv-details-button', 'yes' ) === 'yes' ) {
				add_action( 'yith_wcqv_product_summary', array( $this, 'yith_add_view_details_button' ), 30 );
			}
			if ( get_option( 'yith-wcqv-product-show-meta', 'yes' ) === 'yes' ) {
				add_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_meta', 30 );
			}
			if ( get_option( 'yith-wcqv-enable-share', 'yes' ) === 'yes' ) {
				add_action( 'yith_wcqv_product_summary', array( $this, 'yith_quick_view_share' ), 35 );
			}
		}

		/**
		 * Set action for Dynamic Pricing Plugin
		 *
		 * @since  1.2.3
		 * @author Francesco Licandro
		 */
		public function set_action_dynamic_pricing() {

			if ( ! defined( 'YITH_YWDPD_PREMIUM' )
				|| ! function_exists( 'YITH_WC_Dynamic_Pricing_Frontend' ) || ! function_exists( 'YITH_WC_Dynamic_Pricing' ) ) {
				return;
			}

			if ( YITH_WC_Dynamic_Pricing()->get_option( 'show_quantity_table' ) === 'yes' && get_option( 'yith-wcqv-product-show-discount-table', 'yes' ) === 'yes' ) {
				$position = YITH_WC_Dynamic_Pricing()->get_option( 'show_quantity_table_place' );
				switch ( $position ) {
					case 'before_add_to_cart':
					case 'after_excerpt':
						$priority = 24;
						break;
					case 'before_excerpt':
						$priority = 18;
						break;
					case 'after_meta':
						$priority = 31;
						break;
					default:
						$priority = 26;
						break;
				}

				add_action( 'yith_wcqv_product_summary', array( YITH_WC_Dynamic_Pricing_Frontend(), 'show_table_quantity' ), $priority );
			}

			if ( YITH_WC_Dynamic_Pricing()->get_option( 'show_note_on_products' ) === 'yes' && get_option( 'yith-wcqv-product-show-discount-note', 'yes' ) === 'yes' ) {
				$position = YITH_WC_Dynamic_Pricing()->get_option( 'show_note_on_products_place' );
				switch ( $position ) {
					case 'before_add_to_cart':
					case 'after_excerpt':
						$priority = 24;
						break;
					case 'before_excerpt':
						$priority = 18;
						break;
					case 'after_meta':
						$priority = 31;
						break;
					default:
						$priority = 26;
						break;
				}

				add_action( 'yith_wcqv_product_summary', array( YITH_WC_Dynamic_Pricing_Frontend(), 'show_note_on_products' ), $priority );
			}
		}

		/**
		 * Print custom image thumb html instead of WooCommerce standard
		 *
		 * @access public
		 * @since  1.0.7
		 * @author Francesco Licandro
		 * @param array $attachments_ids
		 */
		public function custom_thumb_html( $attachments_ids = array() ) {
			global $post, $product;

			// if product is null get post.
			if ( null === $product && $post ) {
				$product = wc_get_product( $post->ID );
			}

			if ( ! $product ) {
				return;
			}

			/**
			 * @type $product WC_Product
			 */
			$main_image_id = apply_filters( 'yith_wcqv_get_main_image_id', $product->get_image_id(), $product->get_id() );
			if ( empty( $attachments_ids ) ) {
				$attachments_ids = $this->get_attachments_ids( $product );
			}
			$images_type = get_option( 'yith-wcqv-product-images-mode', 'classic' );

			// Collect images.
			$this->product_images = array_merge( array( $main_image_id ), (array) $attachments_ids );
			// Prevent empty values.
			$this->product_images = array_filter( $this->product_images );
			// Prevent double values.
			$this->product_images = array_unique( $this->product_images );

			// Check for magnifier zoom.
			$zoom_class = $this->enable_zoom ? 'yith_magnifier_zoom' : '';

			$html = '<div class="images">';

			if ( 'slider' === $images_type && count( $this->product_images ) > 1 ) {
				$html .= $this->yith_quick_view_images_slider( '', 0, true );
			} else {
				// Main image.
				$image_title   = esc_attr( get_the_title( $main_image_id ) );
				$image_caption = get_post( $main_image_id )->post_excerpt;
				$image_link    = wp_get_attachment_url( $main_image_id );
				// Check to use or not get image method for prevent https error on certain configuration.
				$use_get_image = apply_filters( 'yith_wcqv_use_get_image_method', true );
				if ( $use_get_image ) {
					$image = $product->get_image(
						'quick_view_image_size',
						array(
							'title' => $image_title,
							'alt'   => $image_title,
						)
					);
				} else {
					$product_id = yit_get_base_product_id( $product );
					$image      = get_the_post_thumbnail(
						$product_id,
						'quick_view_image_size',
						array(
							'title' => $image_title,
							'alt'   => $image_title,
						)
					);
				}

				$html .= '<a href="' . esc_url( $image_link ) . '" itemprop="image" class=" woocommerce-main-image ' . esc_attr( $zoom_class ) . ' zoom" title="' . esc_attr( $image_caption ) . '" data-rel="prettyPhoto[product-gallery]">' . $image . '</a>';
				if ( 'classic' === $images_type && count( $this->product_images ) > 1 ) {
					// Get attachments.
					$html .= $this->yith_quick_view_attachments_images( $this->product_images, true );
				}
			}

			if ( $zoom_class ) {
				$html .= $this->get_zoom_magnifier_options();
			}

			$html .= '</div>';

			// Let's third part filter html.
			$html = apply_filters( 'yith_wcqv_product_image_html', $html );

			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Get YITH WooCommerce Zoom Magnifier options
		 *
		 * @since  1.2.3
		 * @author Francesco Licandro
		 * @return string
		 */
		public function get_zoom_magnifier_options() {
			ob_start(); ?>

			<input type="hidden" id="yith_wczm_traffic_light" value="free">
			<script type="text/javascript" charset="utf-8">
				var yith_magnifier_options = {
					enableSlider: false,
					showTitle: false,
					zoomWidth: '<?php echo esc_html( get_option( 'yith_wcmg_zoom_width' ) ); ?>',
					zoomHeight: '<?php echo esc_html( get_option( 'yith_wcmg_zoom_height' ) ); ?>',
					position: '<?php echo esc_html( get_option( 'yith_wcmg_zoom_position' ) ); ?>',
					lensOpacity: '<?php echo esc_html( get_option( 'yith_wcmg_lens_opacity' ) ); ?>',
					softFocus: <?php echo esc_html( get_option( 'yith_wcmg_softfocus' ) === 'yes' ? 'true' : 'false' ); ?>,
					adjustY: 0,
					disableRightClick: false,
					phoneBehavior: '<?php echo esc_html( get_option( 'yith_wcmg_zoom_mobile_position' ) ); ?>',
					loadingLabel: '<?php echo esc_html( get_option( 'yith_wcmg_loading_label' ) ); ?>',
					zoom_wrap_additional_css: ''
				};
			</script>

			<?php
			return ob_get_clean();
		}

		/**
		 * Get full description instead of short
		 *
		 * @access public
		 * @since  1.0.7
		 * @author Francesco Licandro
		 */
		public function get_full_description() {
			ob_start();
			?>
			<div itemprop="description">
				<?php the_content(); ?>
			</div>
			<?php
			echo ob_get_clean();  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Print wishlist shortcode in quick view
		 *
		 * @access  public
		 * @since   1.0.0
		 * @author  Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function yith_wishlist_quick_view() {
			echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		}

		/**
		 * Print compare shortcode in quick view
		 *
		 * @access  public
		 * @since   1.0.3
		 * @author  Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function yith_compare_quick_view() {
			echo do_shortcode( '[yith_compare_button type="link"]' );
		}

		/**
		 * Add attachments images to a single image in quick view
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array   $attachments
		 * @param boolean $return
		 * @return void | string
		 */
		public function yith_quick_view_attachments_images( $attachments = array(), $return = false ) {

			global $post, $product;

			if ( empty( $attachments ) ) {
				$attachments   = array();
				$attachments[] = get_post_thumbnail_id();
				$attachments   = array_merge( $attachments, $this->get_attachments_ids( $product ) );
			}

			$first = true;

			if ( $return ) {
				ob_start();
			}

			?>
			<div class="yith-quick-view-thumbs">
				<?php foreach ( $attachments as $attachment ) : ?>

					<?php
					$src        = wp_get_attachment_image_src( $attachment, 'quick_view_image_size' );
					$image_link = wp_get_attachment_url( $attachment );
					?>

					<div class="yith-quick-view-single-thumb <?php echo $first ? 'active' : ''; ?>"
						data-img="<?php echo esc_url( $src[0] ); ?>"
						data-href="<?php echo esc_url( $image_link ); ?>"
						data-attachment_id="<?php echo esc_attr( $attachment ); ?>">

						<?php if ( get_option( 'yith-wcqv-enable-lightbox', 'yes' ) === 'yes' ) : ?>
							<a href="<?php echo esc_url( $image_link ); ?>" data-rel="prettyPhoto[product-gallery]"></a>
						<?php
						endif;
						echo wp_get_attachment_image( $attachment, 'shop_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</div>
					<?php
					$first = false;
				endforeach;
				?>
			</div>
			<?php

			if ( $return ) {
				return ob_get_clean();
			}
		}

		/**
		 * Add image slider instead a single image in quick view
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string  $html    Current html.
		 * @param int     $post_id Post ID.
		 * @param boolean $return  Tru to return, false to echo.
		 * @return void|string
		 */
		public function yith_quick_view_images_slider( $html = '', $post_id = 0, $return = false ) {
			/**
			 * @type $product WC_Product
			 */
			if ( $return ) {
				ob_start();
			}
			?>

			<ul class="yith-quick-view-images-slider bxslider">
				<?php foreach ( $this->product_images as $attachment ) : ?>
					<li class="yith-quick-view-slide" data-attachment_id="<?php echo esc_attr( $attachment ); ?>">
						<?php echo wp_get_attachment_image( $attachment, 'quick_view_image_size' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php

			if ( $return ) {
				return ob_get_clean();
			}
		}

		/**
		 * Filter image size if is in quick view
		 *
		 * @access  public
		 * @since   1.0.0
		 * @author  Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $size Current image size to filter.
		 * @return string
		 */
		public function yith_set_image_size( $size ) {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === $this->quick_view_ajax_action && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return 'quick_view_image_size';
			}

			return $size;
		}

		/**
		 * Add View Details button in quick view
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function yith_add_view_details_button() {

			global $product;

			$label = esc_html( get_option( 'yith-wcqv-details-button-label' ) );
			$link  = $product->get_permalink();

			echo '<a href="' . esc_url( $link ) . '" class="yith-wcqv-view-details button">' . $label . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Check if is quick view
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return bool
		 */
		public function yith_is_quick_view() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			return ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && $_REQUEST['action'] === $this->quick_view_ajax_action ) ? true : false;
		}

		/**
		 * Add attachment id to variation data in select variation form
		 *
		 * @since  1.0.4
		 *
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param mixed                $attr default array
		 * @param WC_Product           $variable
		 * @param WC_Product_Variation $variation
		 * @return mixed
		 */
		public function add_attachment_id( $attr, $variable, $variation ) {

			if ( ! $this->yith_is_quick_view() ) {
				return $attr;
			}

			$variation_id = $variation->get_id();

			if ( has_post_thumbnail( $variation_id ) ) {
				$attachment_id              = get_post_thumbnail_id( $variation_id );
				$attr['attachment_id']      = $attachment_id;
				$attr['has_custom_gallery'] = function_exists( 'yith_wccl_get_variation_gallery' ) ? ! empty( yith_wccl_get_variation_gallery( $variation ) ) : false;
			}

			return $attr;
		}

		/**
		 * Set action for get correct currency in WooCommerce multilingual
		 *
		 * @access public
		 * @author Francesco Licandro
		 * @param array $action
		 * @return array
		 */
		public function set_correct_currency( $action ) {
			$action[] = $this->quick_view_ajax_action;

			return $action;
		}

		/**
		 * Quick View shortcode button
		 *
		 * @access public
		 * @since  1.0.7
		 * @author Francesco Licandro
		 * @param array $atts
		 * @return string
		 */
		public function quick_view_shortcode( $atts ) {

			$atts = shortcode_atts(
				array(
					'product_id' => 0,
					'label'      => '',
					'type'       => '',
					'position'   => 'button',
				),
				$atts
			);

			extract( $atts );

			return $this->yith_add_quick_view_button( intval( $product_id ), $label, $type, true, $position );
		}

		/**
		 * Hide add to cart for plugin Request a Quote on quick view
		 *
		 * @since  1.1.5
		 * @author Francesco Licandro
		 */
		public function hide_add_to_cart() {
			global $product;

			if ( get_option( 'ywraq_hide_add_to_cart' ) === 'yes' ) {
				if ( 'variable' === $product->product_type ) {
					$css = '.single_variation_wrap .variations_button button{display:none!important;}';
				} else {
					$css = '.cart button.single_add_to_cart_button{display:none!important;}';
				}

				echo '<style>' . esc_html( $css ) . '</style>';
			}
		}

		/**
		 * Get product attachments ids
		 *
		 * @since  1.2.0
		 * @author Francesco Licandro
		 * @param WC_Product $product The product object.
		 * @return array
		 */
		public function get_attachments_ids( $product ) {
			return is_callable( array( $product, 'get_gallery_image_ids' ) ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();
		}

		/**
		 * Get Quick View button label
		 *
		 * @since  1.2.0
		 * @author Francesco Licandro
		 * @return string
		 */
		public function get_button_label() {
			$label = get_option( 'yith-wcqv-button-label' );
			$label = call_user_func( '__', $label, 'yith-woocommerce-quick-view' );

			return apply_filters( 'yith_wcqv_button_label', esc_html( $label ) );
		}

		/**
		 * Check to use WC Ajax
		 *
		 * @since  1.2.1
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function use_wc_ajax() {
			return apply_filters( 'yith_wcqv_use_wc_ajax', true );
		}

		/**
		 * Avoid redirect to single product page on add to cart action in quick view
		 *
		 * @since  1.3.3
		 * @author Francesco Licandro
		 * @param string $value
		 * @return string
		 */
		public function avoid_redirect_to_single_page( $value ) {
			if ( $this->yith_is_quick_view() ) {
				return '';
			}
			return $value;
		}

		/**
		 * Load custom YITH WooCommerce Color And Label Variation variation gallery in ajax
		 *
		 * @since  1.3.5
		 * @author Francesco Licandro
		 */
		public function load_variation_gallery_ajax() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== $this->variation_gallery_ajax_action || ! isset( $_REQUEST['id'] ) ) {
				die();
			}

			// Set the main wp query for the product.
			wp(
				array(
					'p'         => intval( $_REQUEST['id'] ),
					'post_type' => array( 'product', 'product_variation' ),
				)
			);

			global $post, $product;

			$html       = '';
			$product    = wc_get_product( intval( $_REQUEST['id'] ) );
			$is_default = false;

			if ( $product && has_post_thumbnail() ) {
				$gallery = yith_wccl_get_variation_gallery( $product );
				if ( empty( $gallery ) ) {
					// Get the variable.
					$product    = ( $product instanceof WC_Product_Variation ) ? wc_get_product( $product->get_parent_id() ) : $product;
					$is_default = true;
				}

				ob_start();
				$this->custom_thumb_html( $gallery );
				$html = ob_get_clean();
			}

			wp_send_json(
				array(
					'html'    => $html,
					'default' => $is_default,
				)
			);

			// phpcs:enable WordPress.Security.NonceVerification.Recommended
		}

		/**
		 * Action ajax for add to cart in quick view
		 *
		 * @access public
		 * @since  1.4.0
		 * @author Francesco Licandro
		 */
		public function add_item_cart_ajax() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== $this->add_to_cart_ajax_action || ! isset( $_REQUEST['add-to-cart'] ) ) {
				return;
			}

			$_REQUEST['product_id'] = absint( $_REQUEST['add-to-cart'] );

			// Trigger action for added to cart in AJAX.
			do_action( 'woocommerce_ajax_added_to_cart', intval( $_REQUEST['add-to-cart'] ) );

			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_message_to_fragments' ), 10, 1 );

			WC_AJAX::get_refreshed_fragments();

			// phpcs:enable WordPress.Security.NonceVerification.Recommended
		}

		/**
		 * Add to cart messages to fragments
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @param array $fragments Array of cart fragments.
		 * @return array
		 */
		public function add_message_to_fragments( $fragments ) {

			$notices = wc_get_notices();
			if ( empty( $notices ) ) {
				return $fragments;
			}

			if ( wc_notice_count( 'error' ) ) {
				$fragments['ywcqv_error'] = true;
			}

			ob_start();
			wc_print_notices();
			$messages = ob_get_clean();

			$messages && $fragments['ywcqv_messages'] = $messages;

			return $fragments;
		}


		/**
		 * Compatibility with YITH WooCommerce Color and Label Variations
		 * Get template for add to cart button in case of single variation
		 */
		public function show_add_to_cart_for_single_variation() {
			global $product;
			if ( $product instanceof WC_Product_Variation ) {
				wc_get_template( 'variation.php', array(), '', YITH_WCQV_DIR . 'templates/add-to-cart/' );
			}
		}

	}
}
/**
 * Unique access to instance of YITH_WCQV_Frontend class
 *
 * @since 1.0.0
 * @return YITH_WCQV_Frontend
 */
function YITH_WCQV_Frontend() { // phpcs:ignore
	return YITH_WCQV_Frontend::get_instance();
}
