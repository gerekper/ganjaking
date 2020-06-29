<?php
/**
 * Frontend class
 *
 * @author  YITH
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WFBT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WFBT_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WFBT_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var \YITH_WFBT_Frontend
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WFBT_VERSION;

		/**
		 * Discount class
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $discount = null;

		/**
		 * Refresh form action
		 *
		 * @since 1.3.0
		 * @var string
		 */
		public $actionRefresh = 'yith_wfbt_refresh_form';

		/**
		 * Load select variations dialog content
		 *
		 * @since 1.5.0
		 * @var string
		 */
		public $actionVariationsDialogContent = 'yith_wfbt_load_variations_dialog_content';


		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WFBT_Frontend
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

			$this->discount = new YITH_WFBT_Discount();

			// enqueue scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
			add_action( 'template_redirect', array( $this, 'before_add_form' ) );
			// register shortcode
			add_shortcode( 'ywfbt_form', array( $this, 'wfbt_shortcode' ) );
			add_shortcode( 'yith_wfbt', array( $this, 'bought_together_shortcode' ) );

			// ajax update price
			add_action( 'wc_ajax_' . $this->actionRefresh, array( $this, 'refresh_form' ) );
			add_action( 'wp_ajax_nopriv_' . $this->actionRefresh, array( $this, 'refresh_form' ) );

			// ajax load variations dialog
			add_action( 'wc_ajax_' . $this->actionVariationsDialogContent, array( $this, 'load_variations_dialog' ) );
			add_action( 'wp_ajax_nopriv_' . $this->actionVariationsDialogContent, array( $this, 'load_variations_dialog' ) );

		}

		/**
		 * Register scripts and styles for plugin
		 *
		 * @since  1.0.4
		 * @author Francesco Licandro
		 */
		public function register_scripts() {

			$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

			wp_register_script( 'jquery-blockui', $assets_path . 'js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.60' );

			$paths      = apply_filters( 'yith_wfbt_stylesheet_paths', array( WC()->template_path() . 'yith-wfbt-frontend.css', 'yith-wfbt-frontend.css' ) );
			$located    = locate_template( $paths, false, false );
			$search     = array( get_stylesheet_directory(), get_template_directory() );
			$replace    = array( get_stylesheet_directory_uri(), get_template_directory_uri() );
			$stylesheet = ! empty( $located ) ? str_replace( $search, $replace, $located ) : YITH_WFBT_ASSETS_URL . '/css/yith-wfbt.css';

			wp_register_style( 'yith-wfbt-style', $stylesheet );
			wp_register_script( 'yith-wfbt', YITH_WFBT_ASSETS_URL . '/js/yith-wfbt' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), $this->version, true );
			wp_register_style( 'yith-wfbt-query-dialog-style', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css' );
			wp_register_script( 'yith-wfbt-query-dialog', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js', array( 'jquery' ), $this->version, true );

			// register script for carousel
			wp_register_style( 'yith-wfbt-carousel-style', YITH_WFBT_ASSETS_URL . '/css/owl.carousel.css' );
			wp_register_script( 'yith-wfbt-carousel-js', YITH_WFBT_ASSETS_URL . '/js/owl.carousel.min.js', array( 'jquery' ), false, true );
		}

		/**
		 * Enqueue scripts
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function enqueue_scripts() {

			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'yith-wfbt' );
			wp_enqueue_script( 'yith-wfbt-query-dialog' );

			wp_localize_script( 'yith-wfbt', 'yith_wfbt', array(
				'ajaxurl'              => WC_AJAX::get_endpoint( "%%endpoint%%" ),
				'refreshForm'          => $this->actionRefresh,
				'loadVariationsDialog' => $this->actionVariationsDialogContent,
				'loader'               => get_option( 'yith-wfbt-loader' ),
				'visible_elem'         => get_option( 'yith-wfbt-slider-elems' ),
			) );

			wp_enqueue_style( 'yith-wfbt-query-dialog-style' );

			wp_enqueue_style( 'yith-wfbt-style' );

			$form_background  = get_option( 'yith-wfbt-form-background-color' );
			$background       = yith_wfbt_get_proteo_default( 'yith-wfbt-button-color', '#222222' );
			$background_hover = yith_wfbt_get_proteo_default( 'yith-wfbt-button-color-hover', '#222222' );
			$text_color       = yith_wfbt_get_proteo_default( 'yith-wfbt-button-text-color', '#ffffff' );
			$text_color_hover = yith_wfbt_get_proteo_default( 'yith-wfbt-button-text-color-hover', '#ffffff' );

			$inline_css = "
                .yith-wfbt-submit-block .yith-wfbt-submit-button{background: {$background};color: {$text_color};border-color: {$background};}
                .yith-wfbt-submit-block .yith-wfbt-submit-button:hover{background: {$background_hover};color: {$text_color_hover};border-color: {$background_hover};}
                .yith-wfbt-form{background: {$form_background};}";

			wp_add_inline_style( 'yith-wfbt-style', $inline_css );

			/* Enqueue variation add to cart for select variation dialog */
			wp_enqueue_script( 'wc-add-to-cart-variation', WC()->plugin_url() . 'assets/js/frontend/add-to-cart-variation.min.js', array(
				'jquery',
				'wp-util',
				'jquery-blockui',
			), WC()->version );

			$params = array(
				'wc_ajax_url'                      => WC_AJAX::get_endpoint( '%%endpoint%%' ),
				'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
				'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
				'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
			);

			wp_localize_script( 'wc-add-to-cart-variation', 'wc_add_to_cart_variation_params', $params );
		}

		/**
		 * Handle action before print form
		 *
		 * @since  1.0.4
		 * @author Francesco Licandro
		 */
		public function before_add_form() {

			global $post;

			if ( is_null( $post ) ) {
				return;
			}

			$position = get_option( 'yith-wfbt-form-position', 2 );

			if ( $post instanceof WP_Post && $post->post_type != 'product' || $position == 4 ) {
				return;
			}

			// include style and scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 15 );

			// print form

			switch ( $position ) {
				case 1:
					add_action( 'woocommerce_single_product_summary', array( $this, 'add_bought_together_form' ), 99 );
					break;
				case 2:
					add_action( 'woocommerce_after_single_product_summary', array( $this, 'add_bought_together_form' ), 5 );
					break;
				case 3:
					add_action( 'woocommerce_after_single_product_summary', array( $this, 'add_bought_together_form' ), 99 );
					break;
				case 5:
					add_action( 'woocommerce_single_product_summary', array( $this, 'add_bought_together_form' ), 35 );
					break;
			}
		}

		/**
		 * Form Template
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string|boolean $product_id The product id or false to get global product
		 */
		public function add_bought_together_form( $product_id = false ) {

			global $sitepress;

			$n_variable_products = 0;

			if ( ! $product_id || is_bool( $product_id ) ) {
				global $product;
			} else {
				// make sure to get always translated products
				$product_id = function_exists( 'wpml_object_id_filter' ) ? wpml_object_id_filter( $product_id, 'product', true ) : $product_id;
				$product    = wc_get_product( $product_id );
			}


			if ( ! $product )
				return;

			$product_id = yit_get_base_product_id( $product );
			$variation  = null;

			// exit if grouped or external
			if ( $product->is_type( array( 'grouped', 'external' ) ) || ! $this->can_be_added( $product ) ) {
				return;
			}

			// get meta for current product
			$metas = yith_wfbt_get_meta( $product );

			switch ( $metas['products_type'] ) {
				case 'related':
					$group = wc_get_related_products( $product_id );
					break;
				case 'cross-sells':
					$group = $product->get_cross_sell_ids();
					break;
				case 'up-sells':
					$group = $product->get_upsell_ids();
					break;
				default:
					$group = isset( $metas['products'] ) ? $metas['products'] : array();
					break;
			}

			// if group is empty
			// first get group of original product
			$original_product = $original_product_id = false;
			if ( empty( $group ) && function_exists( 'wpml_object_id_filter' ) && get_option( 'yith-wcfbt-wpml-association', 'yes' ) == 'yes' && function_exists( 'get_default_language' ) ) {
				$original_product_id = wpml_object_id_filter( $product_id, 'product', true, $sitepress->get_default_language() );
				$original_product    = wc_get_product( $original_product_id );
				$metas               = yith_wfbt_get_meta( $original_product );
				$group               = isset( $metas['products'] ) ? $metas['products'] : array();
			}

			if ( empty( $group ) ) {
				return;
			}

			// sort random array key products
			shuffle( $group );

			// check for variation
			if ( $product->is_type( 'variable' ) ) {

				$variation_id = isset( $metas['default_variation'] ) ? intval( $metas['default_variation'] ) : '';
				if ( $original_product && $variation_id ) {
					function_exists( 'wpml_object_id_filter' ) && $variation_id = wpml_object_id_filter( $variation_id, 'product', false );
				}

				if ( ! $variation_id || is_null( $variation_id ) ) {
					return;
				}

				$variation = wc_get_product( $variation_id );
				! $this->can_be_added( $variation ) && $variation = $this->get_first_available_variation( $product, $variation_id );
				if ( ! $variation ) {
					return;
				}

				$product_id = $variation->get_id();
			}

			// if $num is empty set it to 2
			$num = ! empty( $metas['num_visible'] ) ? intval( $metas['num_visible'] ) : 2;

			$products[] = empty( $variation ) ? $product : $variation;
			if ( $product instanceof WC_Product_Variable ) {
				$n_variable_products++;
			}
			$index          = 0;
			$total          = apply_filters( 'yith_wfbt_price_to_display', wc_get_price_to_display( empty( $variation ) ? $product : $variation ), $product, $variation );
			$total_discount = null;

			foreach ( $group as $the_id ) {
				if ( $index >= $num ) {
					break;
				}
				$the_id = function_exists( 'wpml_object_id_filter' ) ? wpml_object_id_filter( $the_id, 'product', false ) : $the_id;
				if ( is_null( $the_id ) ) {
					continue;
				}
				$current = wc_get_product( $the_id );

				if ( ! $this->can_be_added( $current ) ) {
					continue;
				}
				// add to main array
				$products[] = $current;

				if ( $current instanceof WC_Product_Variable ) {
					$n_variable_products++;
				} else {
					$total += apply_filters( 'yith_wfbt_price_to_display', wc_get_price_to_display( $current ), $current, null );
					$index++;
				}

			}
			$products     = apply_filters( 'yith_wfbt_filter_group_products_front', $products );
			$num_products = count( $products ) - $n_variable_products;

			// calculate discount if main product is selected
			$discount = floatval( $this->discount->get_discount_amount( $product, $products, null, $total ) );
			$discount && $total_discount = ( $total - $discount );

			$total_discount = apply_filters( 'yith_wfbt_total_discount', $total_discount );

			// set labels
			$label       = $this->get_label_option( 'button', $num_products );
			$label_total = $this->get_label_option( 'total', $num_products );

			wc_get_template( 'yith-wfbt-form.php', array(
				'main_product_id'    => $product_id,
				'products'           => $products,
				'unchecked'          => array(),
				'additional_text'    => yith_wfbt_get_meta( $product, 'additional_text' ),
				'label'              => $label,
				'label_total'        => $label_total,
				'title'              => get_option( 'yith-wfbt-form-title' ),
				'total'              => apply_filters( 'yith_wfbt_total_html', ! is_null( $total_discount ) ? wc_format_sale_price( $total, $total_discount ) : wc_price( $total ), $total, $total_discount ),
				'total_discount'     => $total_discount,
				'is_empty'           => $metas['show_unchecked'] == 'yes',
				'show_unchecked'     => $metas['show_unchecked'] == 'yes',
				'popup_button_label' => get_option( 'yith-wfbt-open-popup-button-label', __( 'Choose your product', 'yith-woocommerce-frequently-bought-together' ) ),
			), '', YITH_WFBT_DIR . 'templates/' );

			wp_reset_postdata();
		}

		/**
		 * Get the first available variation for passed product
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param WC_Product $product
		 * @param integer    $variation_id Variation id to exclude
		 * @return WC_Product_Variation|false
		 */
		public function get_first_available_variation( $product, $variation_id = 0 ) {

			$variations = $product->get_children();

			if ( is_array( $variations ) ) {
				foreach ( $variations as $variation ) {
					if ( $variation == $variation_id ) {
						continue;
					}
					$product_variation = wc_get_product( $variation );
					if ( $this->can_be_added( $product_variation ) ) {
						return $product_variation;
					}
				}
			}

			return false;
		}

		/**
		 * Check if product can be added to frequently form
		 *
		 * @access public
		 * @since  1.0.5
		 * @author Francesco Licandro
		 * @param object|int $product
		 * @return boolean
		 */
		public function can_be_added( $product ) {

			if ( ! is_object( $product ) ) {
				$product = wc_get_product( intval( $product ) );
			}

			$can = $product && ( ! $product->managing_stock() || $product->is_in_stock() || $product->backorders_allowed() ) && $product->is_purchasable();

			return apply_filters( 'yith_wfbt_product_can_be_added', $can, $product );
		}

		/**
		 * Frequently Bought Together Shortcode
		 *
		 * @since  1.0.5
		 * @author Francesco Licandro
		 * @param array $atts
		 * @return string
		 */
		public function wfbt_shortcode( $atts ) {

			$atts = shortcode_atts( array(
				'product_id' => 0,
			), $atts );

			extract( $atts );

			// include style and scripts
			$this->enqueue_scripts();

			ob_start();
			$this->add_bought_together_form( intval( $product_id ) );
			return ob_get_clean();
		}


		/**
		 * Register Frequently Bought Together shortcode
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param mixed $atts
		 * @param null  $content
		 */
		public function bought_together_shortcode( $atts, $content = null ) {

			extract( shortcode_atts( array(
				"products" => "",
			), $atts ) );

			$products = explode( ",", $products );
			$elems    = array();

			// take products to show
			foreach ( $products as $product_id ) {
				$product     = wc_get_product( $product_id );
				$product_ids = yith_wfbt_get_meta( $product, 'products' );

				if ( ! $product_ids ) {
					continue;
				}

				foreach ( $product_ids as $id ) {
					// add elem only if is not present in array products
					if ( ! in_array( $id, $products ) ) {
						$elems[] = $id;
					}
				}
			}
			// remove duplicate
			$elems = array_unique( $elems );

			if ( empty( $elems ) )
				return;

			$this->enqueue_scripts();

			wc_get_template( 'yith-wfbt-shortcode.php', array( 'products' => $elems ), '', YITH_WFBT_DIR . 'templates/' );
		}

		/**
		 * Refresh form in ajax
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function refresh_form() {
			if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != $this->actionRefresh || ! isset( $_REQUEST['product_id'] ) ) {
				die();
			}

			$main_id      = intval( $_REQUEST['product_id'] );
			$variation_id = isset( $_REQUEST['variation_id'] ) ? intval( $_REQUEST['variation_id'] ) : false;
			$group        = is_array( $_REQUEST['group'] ) ? $_REQUEST['group'] : array();
			$unchecked    = ( isset( $_REQUEST['unchecked'] ) && is_array( $_REQUEST['unchecked'] ) ) ? $_REQUEST['unchecked'] : array();

			$total          = 0;
			$total_discount = null;
			$checked        = array();

			// get main product
			$product = wc_get_product( $main_id );
			$product->is_type( 'variation' ) && $product = wc_get_product( $product->get_parent_id() );

			foreach ( $group as $key => $product_id ) {
				if ( $product_id == $main_id && $variation_id ) {
					$variation = wc_get_product( $variation_id );
					if ( $this->can_be_added( $variation ) ) {
						// set new main and new product
						$main_id    = $variation_id;
						$products[] = $variation;
						$checked[]  = $variation_id;
						$total      += wc_get_price_to_display( $variation );
						continue;
					}
				}

				$p          = wc_get_product( $product_id );
				$products[] = $p;
				if ( ! in_array( $product_id, $unchecked ) ) {
					$checked[] = $product_id;
					$total     += wc_get_price_to_display( $p );
				}
			}

			// calculate discount if main product is selected
			if ( ! in_array( $main_id, $unchecked ) ) {
				$discount = floatval( $this->discount->get_discount_amount( $main_id, $checked, null, $total ) );
				$discount && $total_discount = ( $total - $discount );
			}

			$total_discount = apply_filters( 'yith_wfbt_total_discount', $total_discount );

			$num_products = count( $group ) - count( $unchecked );

			// set labels
			$label       = $this->get_label_option( 'button', $num_products );
			$label_total = $this->get_label_option( 'total', $num_products );

			ob_start();

			wc_get_template( 'yith-wfbt-form.php', array(
				'product'            => $product,
				'main_product_id'    => $main_id,
				'products'           => $products,
				'unchecked'          => $unchecked,
				'additional_text'    => yith_wfbt_get_meta( $product, 'additional_text' ),
				'label'              => $label,
				'label_total'        => $label_total,
				'title'              => get_option( 'yith-wfbt-form-title', __( 'Frequently Bought Together', 'yith-woocommerce-frequently-bought-together' ) ),
                'total'              => apply_filters( 'yith_wfbt_total_html', ! is_null( $total_discount ) ? wc_format_sale_price( $total, $total_discount ) : wc_price( $total ), $total, $total_discount ),
				'total_discount'     => $total_discount,
                'is_empty'           => ! $num_products,
				'show_unchecked'     => false,
				'popup_button_label' => get_option( 'yith-wfbt-open-popup-button-label', __( 'Choose your product', 'yith-woocommerce-frequently-bought-together' ) ),
			), '', YITH_WFBT_DIR . 'templates/' );

			echo ob_get_clean(); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Get option based on number of products and type
		 *
		 * @since  1.3.0
		 * @author Francesco Licandro
		 * @param string  $type
		 * @param integer $number
		 * @return string
		 */
		public function get_label_option( $type, $number ) {
			if ( $number < 2 ) {
				$o = $type == 'button' ? 'yith-wfbt-button-single-label' : 'yith-wfbt-total-single-label';
			} elseif ( $number == 2 ) {
				$o = $type == 'button' ? 'yith-wfbt-button-double-label' : 'yith-wfbt-total-double-label';
			} elseif ( $number == 3 ) {
				$o = $type == 'button' ? 'yith-wfbt-button-three-label' : 'yith-wfbt-total-three-label';
			} else {
				$o = $type == 'button' ? 'yith-wfbt-button-multi-label' : 'yith-wfbt-total-multi-label';
			}

			return get_option( $o );
		}


		/**
		 * Load content for variation gallery
		 * @since  1.4.0
		 * @author Alessio Torrisi
		 */
		public function load_variations_dialog() {
			if ( isset( $_POST['product_id'] ) && $_POST['product_id'] != 0 ) {
				$product = wc_get_product( $_POST['product_id'] );
				wc_get_template( 'yith-wfbt-dialog-variations.php', array( 'single_product' => $product, 'product_id' => $_POST['product_id'] ), '', YITH_WFBT_DIR . 'templates/' );
			}

			die();
		}
	}
}
/**
 * Unique access to instance of YITH_WFBT_Frontend class
 *
 * @since 1.0.0
 * @return \YITH_WFBT_Frontend
 */
function YITH_WFBT_Frontend() {
	return YITH_WFBT_Frontend::get_instance();
}