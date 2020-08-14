<?php
/**
 * Frontend Premium class
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WACP_Frontend_Premium' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WACP_Frontend_Premium extends YITH_WACP_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WACP_Frontend_Premium
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WACP_VERSION;

		/**
		 * Plugin enable on single product page
		 *
		 * @since 1.1.0
		 * @var boolean
		 */
		public $enable_single = false;

		/**
		 * Plugin enable on archive
		 *
		 * @since 1.1.0
		 * @var boolean
		 */
		public $enable_loop = false;

		/**
		 * Remove action
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $action_remove = 'yith_wacp_remove_item_cart';

		/**
		 * Add to cart action
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $action_add = 'yith_wacp_add_item_cart';

		/**
		 * Add to cart action for YITH WooCommerce Frequently Bought Together
		 *
		 * @since 1.4.0
		 * @var string
		 */
		public $action_add_wfbt = 'yith_wacp_add_wfbt_cart';

		/**
		 * Update cart popup action
		 *
		 * @since 1.3.0
		 * @var string
		 */
		public $action_update = 'yith_wacp_update_item_cart';

		/**
		 * Floating cart class instance
		 *
		 * @since 1.4.0
		 * @var YITH_WACP_Floating_Cart|null
		 */
		public $floating_cart = null;

		/**
		 * Last cart item key added
		 *
		 * @since 1.4.7
		 * @var string
		 */
		public $last_cart_item = '';

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WACP_Frontend_Premium
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

			parent::__construct();

			if ( class_exists( 'YITH_WACP_Mini_Cart' ) ) {
				$this->floating_cart = new YITH_WACP_Mini_Cart();
			}
			$this->enable_single = get_option( 'yith-wacp-enable-on-single' ) === 'yes';
			$this->enable_loop   = get_option( 'yith-wacp-enable-on-archive' ) === 'yes';

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_premium' ), 20 );

			add_action( 'wc_ajax_' . $this->action_remove, array( $this, 'remove_item_cart_ajax' ) );
			add_action( 'wc_ajax_' . $this->action_update, array( $this, 'update_item_cart_ajax' ) );
			// No priv AJAX actions.
			add_action( 'wp_ajax_nopriv' . $this->action_remove, array( $this, 'remove_item_cart_ajax' ) );
			add_action( 'wp_ajax_nopriv' . $this->action_update, array( $this, 'update_item_cart_ajax' ) );

			// Add product from single product page Ajax.
			add_action( 'wp_loaded', array( $this, 'add_item_cart_ajax' ), 30 );
			// Add YITH WooCommerce Frequently Bought Products.
			add_action( 'yith_wfbt_group_added_to_cart', array( $this, 'add_wfbt_cart_ajax' ), 100, 3 );
			// Prevent redirect after ajax add to cart.
			add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'prevent_redirect_url' ), 100, 1 );
			// Prevent WooCommerce option Redirect to the cart page after successful addition.
			add_filter( 'pre_option_woocommerce_cart_redirect_after_add', array( $this, 'prevent_cart_redirect' ), 10, 2 );
			// Prevent add to cart AJAX.
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'exclude_single' ) );
			// Add popup message.
			add_action( 'yith_wacp_before_popup_content', array( $this, 'add_message' ), 10, 1 );
			// Add action button to popup.
			add_action( 'yith_wacp_after_popup_content', array( $this, 'add_actions_button' ), 10, 1 );
			// Add related to popup.
			add_action( 'yith_wacp_after_popup_content', array( $this, 'add_related' ), 20, 1 );
			// Add cart info.
			add_action( 'yith_wacp_add_cart_info', array( $this, 'add_cart_info' ), 10 );
			// Add args to popup template.
			add_filter( 'yith_wacp_popup_template_args', array( $this, 'popup_args' ), 10, 1 );
			// Store last cart item key.
			add_action( 'woocommerce_add_to_cart', array( $this, 'store_cart_item_key' ), 10, 6 );
			// Let's filter item data for cart.
			add_filter( 'woocommerce_product_variation_get_name', array( $this, 'filter_get_name_product' ), 1, 2 );
			// Add param to related query args.
			add_filter( 'yith_wacp_related_products_args', array( $this, 'add_related_query_args' ), 10, 1 );
		}

		/**
		 * Enqueue scripts and styles premium
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_premium() {

			$min        = ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? '.min' : '';
			$inline_css = yith_wacp_get_style_options();

			wp_add_inline_style( 'yith-wacp-frontend', $inline_css );

			// Scroll plugin.
			wp_enqueue_style( 'wacp-scroller-plugin-css', YITH_WACP_ASSETS_URL . '/css/perfect-scrollbar.css', array(), $this->version );
			wp_enqueue_script( 'wacp-scroller-plugin-js', YITH_WACP_ASSETS_URL . '/js/perfect-scrollbar' . $min . '.js', array( 'jquery' ), $this->version, true );

			$selectors = array(
				'body.single-product form.cart:not(.in_loop)',
				'body.single-product form.bundle_form',
				'body.singular-product form.cart',
				'.yith-quick-view.yith-inline form.cart',
			);

			$localized = array(
				'ajaxurl'               => WC_AJAX::get_endpoint( '%%endpoint%%' ),
				'actionAdd'             => $this->action_add,
				'actionRemove'          => $this->action_remove,
				'actionUpdate'          => $this->action_update,
				'loader'                => apply_filters( 'yith_wacp_loader_image_url', YITH_WACP_ASSETS_URL . '/images/loader.gif' ),
				'enable_single'         => $this->enable_single,
				'is_mobile'             => wp_is_mobile(),
				'popup_size'            => get_option( 'yith-wacp-box-size' ),
				'form_selectors'        => apply_filters( 'yith_wacp_form_selectors_filter', implode( ',', $selectors ) ),
				'allow_automatic_popup' => apply_filters( 'yith_wacp_allow_automatic_popup', true ),
				'open_popup_selectors'  => apply_filters( 'yith_wacp_open_popup_selectors', '#yith-wacp-mini-cart' ),
			);

			if ( get_option( 'yith-wacp-enable-wfbt', 'no' ) === 'yes' ) {
				$localized['actionAddFBT'] = $this->action_add_wfbt;
				$localized['nonceFBT']     = wp_create_nonce( 'yith_bought_together' );
			}

			wp_localize_script( 'yith-wacp-frontend-script', 'yith_wacp', apply_filters( 'yith_wacp_frontend_script_localized_args', $localized ) );
		}

		/**
		 * Add args to popup template
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param mixed $args An array of popup arguments.
		 * @return mixed
		 */
		public function popup_args( $args ) {
			// Set new animation.
			$args['animation'] = get_option( 'yith-wacp-box-animation' );

			return $args;
		}

		/**
		 * Get content html for added to cart popup
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param boolean|object $product  Current product added.
		 * @param string         $layout   Layout to load.
		 * @param string|integer $quantity Product quantity added.
		 * @return mixed
		 */
		public function get_popup_content( $product, $layout = '', $quantity = 1 ) {

			if ( ! $layout ) {
				$layout = get_option( 'yith-wacp-layout-popup', 'product' );
			}

			$args = apply_filters(
				'yith_wacp_get_popup_content',
				array(
					'thumb'              => get_option( 'yith-wacp-show-thumbnail', 'yes' ) === 'yes',
					'product_info'       => get_option( 'yith-wacp-show-info', 'yes' ) === 'yes',
					'cart_total'         => get_option( 'yith-wacp-show-cart-totals', 'yes' ) === 'yes',
					'cart_shipping'      => get_option( 'yith-wacp-show-cart-shipping', 'yes' ) === 'yes',
					'cart_tax'           => get_option( 'yith-wacp-show-cart-tax', 'yes' ) === 'yes',
					'product'            => $product,
					'quantity'           => $quantity,
					'last_cart_item_key' => $this->last_cart_item,
				)
			);

			// If $layout is cart just define constant WOOCOMMERCE_CART to get full compatibility.
			( 'cart' === $layout && ! defined( 'WOOCOMMERCE_CART' ) ) && define( 'WOOCOMMERCE_CART', true );

			ob_start();

			do_action( 'yith_wacp_before_popup_content', $product );

			wc_get_template( 'yith-wacp-popup-' . $layout . '.php', $args, '', YITH_WACP_TEMPLATE_PATH . '/' );

			do_action( 'yith_wacp_after_popup_content', $product );

			return ob_get_clean();
		}

		/**
		 * Added to cart success popup box
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $datas An array of popup data.
		 * @return array
		 */
		public function add_to_cart_success_ajax( $datas ) {

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_REQUEST['product_id'] ) || ( ! isset( $_REQUEST['ywacp_is_single'] ) && ! $this->enable_loop ) ) {
				return $datas;
			}

			$variation_id = ! empty( $_REQUEST['variation_id'] ) ? intval( $_REQUEST['variation_id'] ) : 0;
			$product_id   = $variation_id ? $variation_id : intval( $_REQUEST['product_id'] );
			$product      = wc_get_product( $product_id );
			if ( ! $product ) {
				return $datas;
			}
			$product_id = yit_get_base_product_id( $product );
			// Check if is excluded.
			if ( $this->is_in_exclusion( $product_id ) ) {
				return $datas;
			}

			$quantity                   = isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1;
			$layout                     = get_option( 'yith-wacp-layout-popup', 'product' );
			$datas['yith_wacp_message'] = $this->get_popup_content( $product, $layout, $quantity );

			// phpcs:enable WordPress.Security.NonceVerification.Recommended
			return apply_filters( 'yith_wacp_add_to_cart_success_data', $datas, $product, $layout, $quantity );
		}

		/**
		 * Action ajax for remove item from cart
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function remove_item_cart_ajax() {

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== $this->action_remove || ! isset( $_REQUEST['item_key'] ) ) {
				die();
			}

			$item_key = sanitize_text_field( wp_unslash( $_REQUEST['item_key'] ) );

			WC()->cart->remove_cart_item( $item_key );
			$return = '';

			if ( ! WC()->cart->is_empty() ) {
				$cart    = WC()->cart->get_cart();
				$last    = end( $cart );
				$product = isset( $last['product_id'] ) ? wc_get_product( $last['product_id'] ) : false;

				// Remove popup message.
				remove_action( 'yith_wacp_before_popup_content', array( $this, 'add_message' ), 10 );

			} else {
				$product = null;
			}

			$return = $this->get_popup_content( $product, 'cart' );

			// phpcs:enable WordPress.Security.NonceVerification.Recommended
			wp_send_json(
				array(
					'html'  => $return,
					'items' => ! is_null( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0,
				)
			);
		}

		/**
		 * Action ajax for update item from cart
		 *
		 * @access public
		 * @since  1.3.0
		 * @author Francesco Licandro
		 */
		public function update_item_cart_ajax() {

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== $this->action_update || ! isset( $_REQUEST['item_key'] ) ) {
				die();
			}

			$item_key = sanitize_text_field( wp_unslash( $_REQUEST['item_key'] ) );
			$qty      = apply_filters( 'woocommerce_stock_amount_cart_item', wc_stock_amount( ! empty( $_REQUEST['qty'] ) ? intval( $_REQUEST['qty'] ) : 1 ), $item_key );
			$item     = WC()->cart->get_cart_item( $item_key );

			if ( ! empty( $item ) ) {
				$passed_validation = apply_filters( 'woocommerce_update_cart_validation', true, $item_key, $item, $qty );
				// If validation passed -> set new quantity.
				$passed_validation && WC()->cart->set_quantity( $item_key, $qty, true );
			} else {
				$cart = WC()->cart->get_cart();
				$item = end( $cart );
			}

			$product = isset( $item['product_id'] ) ? wc_get_product( $item['product_id'] ) : false;

			// Remove popup message.
			remove_action( 'yith_wacp_before_popup_content', array( $this, 'add_message' ), 10 );
			add_action( 'yith_wacp_before_popup_content', 'wc_print_notices', 10 );

			// phpcs:enable WordPress.Security.NonceVerification.Recommended
			wp_send_json(
				array(
					'html'  => $this->get_popup_content( $product, 'cart' ),
					'items' => ! is_null( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0,
				)
			);
		}

		/**
		 * Action ajax for add to cart in single product page
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function add_item_cart_ajax() {

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== $this->action_add || ! isset( $_REQUEST['add-to-cart'] ) ) {
				return;
			}

			$error      = '';
			$popup_html = '';
			$product_id = isset( $_REQUEST['product_id'] ) ? intval( $_REQUEST['product_id'] ) : intval( $_REQUEST['add-to-cart'] );
			$quantity   = isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1;

			// Get WooCommerce error notice.
			$error_notices = wc_get_notices( 'error' );

			if ( ! $this->last_cart_item && function_exists( 'wc_gfpa' ) && method_exists( wc_gfpa(), 'get_gravity_form_data' ) ) {
				// Check if we need a gravity form!
				$context           = ! empty( $_POST['add-variations-to-cart'] ) ? 'bulk' : 'single';
				$gravity_form_data = wc_gfpa()->get_gravity_form_data( $product_id, $context );
				if ( ! empty( $gravity_form_data ) ) {
					$error_notices = array( __( 'There was a problem with your submission. Please check required fields below.', 'yith-woocommerce-added-to-cart-popup' ) );
				}
			}
			
			if ( $error_notices ) {
				// Print notices if any!
				ob_start();
				foreach ( $error_notices as $value ) {
					$value = ( is_array( $value ) && isset( $value['notice'] ) ) ? $value['notice'] : $value;
					$value && wc_print_notice( $value, 'error' );
				}
				$error = ob_get_clean();
			} else {
				// Trigger action for added to cart in AJAX.
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );

				if ( ! empty( $_REQUEST['variation_id'] ) ) {
					$product_id = intval( $_REQUEST['variation_id'] );
				}

				$product    = wc_get_product( $product_id );
				$layout     = get_option( 'yith-wacp-layout-popup', 'product' );
				$popup_html = $this->get_popup_content( $product, $layout, $quantity );
			}

			// Clear other notices.
			wc_clear_notices();

			// phpcs:enable WordPress.Security.NonceVerification.Recommended
			wp_send_json(
				array(
					'error'     => $error,
					'html'      => $popup_html,
					'cart_html' => ( ! $popup_html || 'product' !== $layout ) ? $popup_html : YITH_WACP_Frontend_Premium()->get_popup_content( $product, 'cart', $quantity ),
					'items'     => ! is_null( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0,
				)
			);
		}

		/**
		 * Action ajax for frequently bought together form
		 *
		 * @access public
		 * @since  1.4.0
		 * @author Francesco Licandro
		 * @param array      $products_added
		 * @param WC_Product $main_product
		 * @param array      $offered
		 */
		public function add_wfbt_cart_ajax( $products_added, $main_product, $offered ) {

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_REQUEST['actionAjax'] ) || $_REQUEST['actionAjax'] !== $this->action_add_wfbt ) {
				return;
			}

			$error      = '';
			$popup_html = '';

			// Get WooCommerce error notices!
			$error_notices = wc_get_notices( 'error' );
			if ( $error_notices ) {
				ob_start();
				foreach ( $error_notices as $value ) {
					wc_print_notice( $value, 'error' );
				}
				$error = ob_get_clean();
			} else {
				$product = wc_get_product( $main_product );
				if ( $product->is_type( 'variation' ) ) {
					$product = wc_get_product( $product->get_parent_id() );
				}
				$popup_html = $this->get_popup_content( $product, 'cart' );
			}

			// Clear other notices.
			wc_clear_notices();

			wp_send_json(
				array(
					'error' => $error,
					'html'  => $popup_html,
					'items' => ! is_null( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0,
				)
			);
		}

		/**
		 * Prevent url redirect in add to cart ajax for single product page
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $url The url destination after add to cart action in single product page.
		 * @return boolean | string
		 */
		public function prevent_redirect_url( $url ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] === $this->action_add ) {
				return false;
			}

			return $url;
		}

		/**
		 * Add message before popup content
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param WC_Product $product The product object.
		 */
		public function add_message( $product ) {
			$message = get_option( 'yith-wacp-popup-message' );

			if ( ! $message || ! $product ) {
				return;
			}

			ob_start();
			?>

			<div class="yith-wacp-message">
				<span><?php echo wp_kses_post( $message ); ?></span>
			</div>

			<?php
			$html = ob_get_clean();

			echo apply_filters( 'yith_wacp_message_popup_html', $html, $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Add action button to popup
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param WC_Product $product The product object.
		 */
		public function add_actions_button( $product ) {

			$cart     = get_option( 'yith-wacp-show-go-cart', 'yes' ) === 'yes';
			$checkout = get_option( 'yith-wacp-show-go-checkout', 'yes' ) === 'yes';
			$continue = get_option( 'yith-wacp-show-continue-shopping', 'yes' ) === 'yes';

			if ( ! $cart && ! $checkout && ! $continue ) {
				return;
			}

			$cart_url     = wc_get_cart_url();
			$checkout_url = wc_get_checkout_url();

			$args = array(
				'cart'                  => $cart,
				'cart_url'              => apply_filters( 'yith_wacp_go_cart_url', $cart_url ),
				'checkout'              => $checkout,
				'checkout_url'          => apply_filters( 'yith_wacp_go_checkout_url', $checkout_url ),
				'continue'              => $continue,
				'continue_shopping_url' => apply_filters( 'yith_wacp_continue_shopping_url', '#' ),
			);

			ob_start();
			wc_get_template( 'yith-wacp-popup-action.php', $args, '', YITH_WACP_TEMPLATE_PATH . '/' );
			$html = ob_get_clean();

			echo apply_filters( 'yith_wacp_actions_button_html', $html, $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Add suggested/related product to popup
		 *
		 * @access public
		 * @since  1.1.0
		 * @author Francesco Licandro
		 * @param WC_Product $product The product object.
		 */
        public function add_related( $product ) {

            $suggested = get_option( 'yith-wacp-related-products', array() );

            if( 'yes' === get_option( 'yith-wacp-show-related', 'yes' ) && ( ! empty( $suggested ) || $product ) ){

                $number_of_suggested = get_option( 'yith-wacp-related-number', 4 );
                // First check in custom list.

                if ( ! is_array( $suggested ) ) {
                    $suggested = explode( ',', $suggested );
                }
                $suggested = array_filter( $suggested ); // Remove empty if any.

                $product_id = yit_get_base_product_id( $product );
                // Get correct product if original is variation.
                if ( $product instanceof WC_Product && $product->is_type( 'variation' ) ) {
                    $product = wc_get_product( $product_id );
                }

                // Get standard WC related if option is empty.
                if ( empty( $suggested ) ) {

                    $suggested_type = get_option( 'yith-wacp-suggested-products-type', 'related' );

                    switch ( $suggested_type ) {
                        case 'crossell':
                            $suggested = WC()->cart->get_cross_sells();
                            break;
                        case 'upsell':
                            $suggested = $product->get_upsell_ids();
                            break;
                        default:
                            $suggested = wc_get_related_products( $product_id, $number_of_suggested );
                            break;
                    }
                }

                $suggested = $this->filter_product_already_in_cart( $suggested );

                if ( empty( $suggested ) ) {
                    return;
                }

                $args = apply_filters(
                    'yith_wacp_popup_related_args',
                    array(
                        'title'              => get_option( 'yith-wacp-related-title', '' ),
                        'items'              => $suggested,
                        'posts_per_page'     => $number_of_suggested,
                        'columns'            => get_option( 'yith-wacp-related-columns', 4 ),
                        'current_product_id' => $product_id,
                        'show_add_to_cart'   => get_option( 'yith-wacp-suggested-add-to-cart', 'yes' ) === 'yes',
                    ),
                    $product
                );

                wc_get_template( 'yith-wacp-popup-related.php', $args, '', YITH_WACP_TEMPLATE_PATH . '/' );
            }

        }

		/**
		 * From given product array remove products already in cart
		 *
		 * @since 1.5.5
		 * @author Francesco Licandro
		 * @param array $products
		 * @return array
		 */
		public function filter_product_already_in_cart( $products ) {
			if( empty( $products ) ) {
				return array();
			}

			$products 	= array_unique( $products );
			$in_cart 	= array();
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				if ( $values['quantity'] > 0 ) {
					$in_cart[]   = $values['product_id'];
				}
			}

			return array_diff( $products, $in_cart );
		}

		/**
		 * Exclude product from added to cart popup process in single product page
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function exclude_single() {
			global $product;

			$product_id = yit_get_base_product_id( $product );

			if ( $this->is_in_exclusion( $product_id ) ) {
				echo '<input type="hidden" name="yith-wacp-is-excluded" value="1" />';
			}
		}

		/**
		 * Check if product is in exclusion list
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param integer $product_id The product ID to check.
		 * @return boolean
		 */
		public function is_in_exclusion( $product_id ) {

			$exclusion_prod = array_filter( explode( ',', get_option( 'yith-wacp-exclusions-prod-list', '' ) ) );
			$exclusion_cat  = array_filter( explode( ',', get_option( 'yith-wacp-exclusions-cat-list', '' ) ) );

			$product_cat        = array();
			$product_categories = get_the_terms( $product_id, 'product_cat' );

			if ( ! empty( $product_categories ) ) {
				foreach ( $product_categories as $cat ) {
					$product_cat[] = $cat->term_id;
				}
			}

			$intersect = array_intersect( $product_cat, $exclusion_cat );
			if ( in_array( $product_id, $exclusion_prod ) || ! empty( $intersect ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Prevent cart redirect. WC Option Redirect to the cart page after successful addition
		 *
		 * @since  1.1.0
		 * @author Francesco Licandro
		 * @param mixed  $value  Current value.
		 * @param string $option Option name.
		 * @return mixed
		 */
		public function prevent_cart_redirect( $value, $option ) {
			if ( ( is_product() && $this->enable_single ) || $this->enable_loop ) {
				return 'no';
			}

			return $value;
		}

		/**
		 * Store last cart item key
		 *
		 * @since  1.1.1
		 * @author Francesco Licandro
		 * @param string         $cart_item_key  The cart item key.
		 * @param string|integer $product_id     The product object ID.
		 * @param string|integer $quantity       Quantity value.
		 * @param string|integer $variation_id   The variation object ID.
		 * @param array          $variation      The variation data.
		 * @param array          $cart_item_data The cart item data.
		 */
		public function store_cart_item_key( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

			if ( in_array( '_wjecf_free_product_coupon', $cart_item_data ) ) {
				return;
			}

			$this->last_cart_item = $cart_item_key;
		}

		/**
		 * Filter item data and add item
		 *
		 * @since  1.2.1
		 * @author Francesco Licandro
		 * @param string     $value   Current value.
		 * @param WC_Product $product The product instance.
		 * @return string
		 */
		public function filter_get_name_product( $value, $product ) {
			$id = $product->get_id();

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_REQUEST['ywacp_is_single'] ) && 'yes' === $_REQUEST['ywacp_is_single'] && isset( $_REQUEST['variation_id'] ) && intval( $_REQUEST['variation_id'] ) === intval( $id )
				&& 'product' === get_option( 'yith-wacp-layout-popup', 'product' ) ) {
				// Return parent name.
				return $product->get_title();
			}

			return $value;
		}

		/**
		 * Add param to related query args
		 *
		 * @since  1.2.2
		 * @author Francesco Licandro
		 * @param array $args Add tax query to related query.
		 * @return array
		 */
		public function add_related_query_args( $args ) {
			$args['tax_query'] = WC()->query->get_tax_query();
			return $args;
		}

		/**
		 * Add cart info (total, discount, shipping..) to popup template
		 *
		 * @since  1.4.0
		 * @author Francesco Licandro
		 */
		public function add_cart_info() {
			$cart_total    = get_option( 'yith-wacp-show-cart-totals', 'yes' ) === 'yes';
			$cart_shipping = get_option( 'yith-wacp-show-cart-shipping', 'yes' ) === 'yes';
			$cart_tax      = get_option( 'yith-wacp-show-cart-tax', 'yes' ) === 'yes';

			if ( ! $cart_total && ! $cart_shipping && ! $cart_tax ) {
				return;
			}

			wc_get_template(
				'yith-wacp-popup-cart-info.php',
				array(
					'cart_info'     => yith_wacp_get_cart_info(),
					'cart_total'    => $cart_total,
					'cart_shipping' => $cart_shipping,
					'cart_tax'      => $cart_tax,
				),
				'',
				YITH_WACP_TEMPLATE_PATH . '/'
			);
		}
	}
}

/**
 * Unique access to instance of YITH_WACP_Frontend_Premium class
 *
 * @since 1.0.0
 * @return \YITH_WACP_Frontend_Premium
 */
function YITH_WACP_Frontend_Premium() { // phpcs:ignore
	return YITH_WACP_Frontend_Premium::get_instance();
}
