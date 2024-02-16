<?php
/**
 * Frontend class
 *
 * @author  YITH
 * @package YITH WooCommerce Color and Label Variations Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCCL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCCL_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCCL_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WCCL_Frontend
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $single_product_attributes = array();

		/**
		 * Add to cart action ajax
		 *
		 * @since 1.8.0
		 * @var string
		 */
		public $action_add_to_cart = 'yith_wccl_add_to_cart';

		/**
		 * Variation gallery ajax
		 *
		 * @since 1.8.0
		 * @var string
		 */
		public $action_variation_gallery = 'yith_wccl_variation_gallery';

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WCCL_Frontend
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
		private function __construct() {

			add_action( 'template_redirect', array( $this, 'init_variables' ), 99 );
			add_action( 'woocommerce_before_single_product', array( $this, 'create_attributes_json' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Add select options in loop.
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'add_select_options' ), 100, 2 );
			// Add image shop_catalaog to available variation array.
			add_filter( 'single_product_large_thumbnail_size', array( $this, 'set_shop_catalog_image' ) );

			// AJAX add to cart.
			add_action( 'wc_ajax_' . $this->action_add_to_cart, array( $this, 'add_to_cart_ajax_loop' ) );
			add_action( 'wp_ajax_nopriv_' . $this->action_add_to_cart, array( $this, 'add_to_cart_ajax_loop' ) );
			// AJAX variation gallery.
			add_action( 'wc_ajax_' . $this->action_variation_gallery, array( $this, 'variation_gallery' ) );
			add_action( 'wp_ajax_nopriv_' . $this->action_variation_gallery, array( $this, 'variation_gallery' ) );

			add_action( 'woocommerce_before_single_product', array( $this, 'remove_scripts_gift_card' ) );

			// Compatibility with WooCommerce Quick View.
			add_action( 'wc_quick_view_before_single_product', array( $this, 'wc_quick_view_json_product_attr' ) );
			// Compatibility with Woocommerce Variations Update in Cart.
			add_action( 'mwb-wvuc-before-product-html', array( $this, 'mwb_wvuc_json_product_attr' ) );
			add_action( 'mwb-wvuc-after-product-html', array( $this, 'mwb_wvuc_add_custom_script' ) );

			// Add custom style also on tab Product Attributes.
			add_filter( 'woocommerce_attribute', array( $this, 'product_attributes_tab' ), 99, 3 );

			// Add variable products to available product type RAQ.
			add_filter( 'yith_ywraq_show_button_in_loop_product_type', array( $this, 'raq_add_variable_products' ), 10, 1 );

			// Show single variation on loop.
			add_action( 'woocommerce_product_query', array( $this, 'show_single_variations' ), 10, 2 );
			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'show_single_variations_shortcode' ), 10, 3 );
			add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_query_arg_to_add_to_cart_url' ), 10, 2 );
			add_action( 'template_redirect', array( $this, 'redirect_after_add_to_cart_for_variation' ), 25 );
			// Support to YITH WooCommerce Request a Quote.
			add_filter( 'yith_ywraq_show_button_in_loop_product_type', array( $this, 'show_add_to_quote_button_on_single_variation' ) );

			// Maybe filter the variation gallery html.
			add_filter( 'yith_wccl_filter_variation_gallery_html', array( $this, 'maybe_wrap_variation_gallery' ) );
		}


		/**
		 * Init plugin variables
		 *
		 * @since  1.2.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function init_variables() {

			global $post;

			if ( is_null( $post ) ) {
				return;
			}

			// Get product.
			$product = wc_get_product( $post->ID );
			if ( ! $product ) {
				return;
			}

			$this->create_custom_attributes_array( $product );
		}

		/**
		 * Dequeue scripts if product is gift card
		 *
		 * @since  1.0.7
		 * @author Francesco Licandro
		 * @return void
		 */
		public function remove_scripts_gift_card() {
			global $product;

			if ( is_product() && $product->is_type( 'gift-card' ) ) {
				wp_dequeue_script( 'wc-add-to-cart-variation' );
				wp_dequeue_script( 'yith_wccl_frontend' );
				wp_dequeue_style( 'yith_wccl_frontend' );
			}
		}

		/**
		 * Enqueue scripts
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function enqueue_scripts() {

			global $post;

			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'yith_wccl_frontend', YITH_WCCL_ASSETS_URL . '/js/yith-wccl' . $min . '.js', array( 'jquery', 'wc-add-to-cart-variation' ), YITH_WCCL_VERSION, true );
			wp_register_style( 'yith_wccl_frontend', YITH_WCCL_ASSETS_URL . '/css/yith-wccl.css', false, YITH_WCCL_VERSION );

			wp_enqueue_script( 'wc-add-to-cart-variation' );
			wp_enqueue_script( 'yith_wccl_frontend' );
			wp_enqueue_style( 'yith_wccl_frontend' );

			$localized = array(
				'ajaxurl'                         => WC_AJAX::get_endpoint( '%%endpoint%%' ),
				'actionAddCart'                   => $this->action_add_to_cart,
				'actionVariationGallery'          => $this->action_variation_gallery,
				'cart_redirect'                   => 'yes' === get_option( 'woocommerce_cart_redirect_after_add', 'no' ),
				'cart_url'                        => wc_get_cart_url(),
				'view_cart'                       => __( 'View Cart', 'yith-woocommerce-color-label-variations' ),
				'tooltip'                         => 'yes' === get_option( 'yith-wccl-enable-tooltip', 'yes' ),
				'tooltip_pos'                     => get_option( 'yith-wccl-tooltip-position', 'top' ),
				'tooltip_ani'                     => get_option( 'yith-wccl-tooltip-animation', 'fade' ),
				'description'                     => 'yes' === get_option( 'yith-wccl-enable-description', 'yes' ),
				'add_cart'                        => apply_filters( 'yith_wccl_add_to_cart_button_content', get_option( 'yith-wccl-add-to-cart-label', __( 'Add to cart', 'yith-woocommerce-color-label-variations' ) ) ),
				'grey_out'                        => 'grey' === get_option( 'yith-wccl-attributes-style', 'grey' ),
				'image_hover'                     => 'yes' === get_option( 'yith-wccl-change-image-hover', 'no' ),
				'wrapper_container_shop'          => apply_filters( 'yith_wccl_wrapper_container_shop_js', yith_wccl_get_frontend_selectors( 'wrapper_container_shop' ) ),
				'image_selector'                  => apply_filters( 'yith_wccl_image_selector', yith_wccl_get_frontend_selectors( 'image_selector' ) ),
				'enable_handle_variation_gallery' => apply_filters( 'yith_wccl_enable_handle_variation_gallery', true, $post ),
				'plugin_compatibility_selectors'  => apply_filters( 'yith_wccl_set_plugin_compatibility_selectors', 'yith-wcan-ajax-filtered yith_infs_adding_elem initialized.owl.carousel post-load ajax-tab-loaded', $post ),
				'single_gallery_selector'         => apply_filters( 'yith_wccl_single_variation_gallery_selector', yith_wccl_get_frontend_selectors( 'single_gallery_selector' ) ),
			);
			wp_localize_script( 'yith_wccl_frontend', 'yith_wccl_general', $localized );

			if ( is_product() && ! is_null( $post ) ) {
				$this->create_attributes_json( $post->ID );
				// Remove standard action.
				remove_action( 'woocommerce_before_single_product', array( $this, 'create_attributes_json' ), 10 );
			}

			$color      = get_option( 'yith-wccl-tooltip-text-color' );
			$background = get_option( 'yith-wccl-tooltip-background' );

			$inline_css = ".select_option .yith_wccl_tooltip > span{background: {$background};color: {$color};}
            .select_option .yith_wccl_tooltip.bottom span:after{border-bottom-color: {$background};}
            .select_option .yith_wccl_tooltip.top span:after{border-top-color: {$background};}";

			wp_add_inline_style( 'yith_wccl_frontend', $inline_css );
		}

		/**
		 * Add select options to loop
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string     $html Current add to cart link html.
		 * @param WC_Product $product Current product object.
		 * @return mixed
		 */
		public function add_select_options( $html = '', $product = false ) {

			if ( ! $product ) {
				global $product;
			}

			// Let's third part skip form on loop for specific product or on specific conditions using filter yith_wccl_skip_form_variable_loop.
			if ( ( isset( $product ) && get_post_type( $product ) && ! $product->is_type( 'variable' ) )
				 || 'yes' !== get_option( 'yith-wccl-enable-in-loop', 'no' )
				 || ( isset( $_REQUEST['action'] ) && 'yith-woocompare-view-table' === sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				 || apply_filters( 'yith_wccl_skip_form_variable_loop', false, $product ) ) {
				return $html;
			}

			$product_id = $product->get_id();
			// Get available variations.
			$available_variations = get_transient( 'yith_wccl_available_variations_' . $product_id );
			if ( false === $available_variations ) {
				$available_variations = $product->get_available_variations();
				$available_variations = $this->format_available_variations_loop( $available_variations, $product );
				set_transient( 'yith_wccl_available_variations_' . $product_id, $available_variations, WEEK_IN_SECONDS );
			}

			// If not there are not available variations return.
			if ( empty( $available_variations ) ) {
				return $html;
			}

			// Get variation attributes.
			$attributes = $product->get_variation_attributes();
			// Form position.
			$position = get_option( 'yith-wccl-position-in-loop', 'after' );
			$new_html = '';
			$inputbox = '';

			if ( class_exists( 'WooCommerce_Thumbnail_Input_Quantity' ) ) {
				$incremental = new WooCommerce_Thumbnail_Input_Quantity();
				$inputbox    = $incremental->print_input_box( null );
			}

			// Get default attributes.
			$selected_attributes     = is_callable( array( $product, 'get_default_attributes' ) ) ? $product->get_default_attributes() : $product->get_variation_default_attributes();
			$data_product_variations = ( 'yes' === get_option( 'yith-wccl-ajax-in-loop', 'no' ) ) ? 'false' : esc_attr( wp_json_encode( $available_variations ) );

			$template_args = apply_filters(
				'yith_wccl_variable_loop_template_attr',
				array(
					'product'                 => $product,
					'product_id'              => $product_id,
					'attributes'              => $attributes,
					'selected_attributes'     => $selected_attributes,
					'attributes_types'        => $this->get_variation_attributes_types( $attributes ),
					'data_product_variations' => $data_product_variations,
				)
			);

			ob_start();
			wc_get_template( 'yith-wccl-variable-loop.php', $template_args, '', YITH_WCCL_DIR . 'templates/' );
			$form = ob_get_contents();
			ob_end_clean();

			// Hide button if catalog mode is active or request a quote hide add to cart is enabled.
			if ( yith_wccl_hide_add_to_cart( $product ) || apply_filters( 'yith_wccl_force_disable_add_to_cart', false, $product )  ) {
				$html = '';
			}

			switch ( $position ) {
				case 'before':
					$new_html = $inputbox . $form . $html;
					break;
				case 'after':
					$new_html = $inputbox . $html . $form;
					break;
			}

			if ( 'flatsome_product_box_after' === current_action() ) {
				echo $new_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				return apply_filters( 'yith_wccl_html_form_in_loop', $new_html, $position, $inputbox, $html, $form, $product );
			}
		}

		/**
		 * Print select option in loop
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function print_select_options() {
			echo $this->add_select_options(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Get an array of types and values for each attribute
		 *
		 * @access public
		 * @since  1.0.0
		 * @param array $attributes An array of product attributes.
		 * @return array
		 */
		public function get_variation_attributes_types( $attributes ) {
			global $wc_product_attributes;
			$types        = array();
			$defined_attr = ywccl_get_custom_tax_types();

			if ( ! empty( $attributes ) ) {
				foreach ( $attributes as $name => $options ) {

					$current = isset( $wc_product_attributes[ $name ] ) ? $wc_product_attributes[ $name ] : false;

					if ( $current && array_key_exists( $current->attribute_type, $defined_attr ) ) {
						$types[ $name ] = $current->attribute_type;
					}
				}
			}

			return $types;
		}

		/**
		 * Create custom attribute json
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param boolean|int $product_id Current product ID.
		 * @param boolean     $return True to return the json, false to enqueue on page.
		 * @return array
		 */
		public function create_attributes_json( $product_id = false, $return = false ) {
			if ( ! $product_id ) {
				global $product;
			} else {
				$product = wc_get_product( $product_id );
			}

			if ( ! $product || ! $product->is_type( 'variable' ) ) {
				return array();
			}

			// Ensure that global array isset.
			empty( $this->single_product_attributes ) && $this->create_custom_attributes_array( $product );

			// If empty global array exit.
			if ( empty( $this->single_product_attributes ) ) {
				return array();
			}

			// Get attribute used for variation.
			$attributes_variations        = $product->get_variation_attributes();
			$custom_attributes_variations = array();

			// Remove unused attribute for variation.
			foreach ( (array) $this->single_product_attributes as $key => $value ) {
				if ( array_key_exists( $key, $attributes_variations ) ) {
					// First set key then add to json array.
					$variation_key                                  = function_exists( 'wc_variation_attribute_name' ) ? wc_variation_attribute_name( $key ) : 'attribute_' . sanitize_title( $key );
					$custom_attributes_variations[ $variation_key ] = $value;
				}
			}

			if ( ! $return && ! empty( $custom_attributes_variations ) ) {
				// Ensure that that script was included.
				wp_enqueue_script( 'yith_wccl_frontend' );
				wp_localize_script( 'yith_wccl_frontend', 'yith_wccl', array( 'attributes' => wp_json_encode( $custom_attributes_variations ) ) );
			} else {
				return $custom_attributes_variations;
			}

		}

		/**
		 * Create product attributes array with custom values
		 *
		 * @since  1.1.1
		 * @author Francesco Licandro
		 * @param object $product The product.
		 * @param array  $attributes_to_check Array with attributes to get values and return.
		 */
		protected function create_custom_attributes_array( $product, $attributes_to_check = array() ) {

			global $wc_product_attributes, $sitepress;

			// Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set.
			$attributes       = $product instanceof WC_Product ? $product->get_attributes() : false;
			$default_language = ! is_null( $sitepress ) ? $sitepress->get_default_language() : ''; // Get default WPML language.

			if ( ! is_array( $attributes ) ) {
				return;
			}

			$custom_tax = ywccl_get_custom_tax_types();
			foreach ( $attributes as $attribute ) {

				// Check if current attribute is used for variations otherwise continue.
				if ( ! isset( $attribute['name'] ) || ( ! empty( $attributes_to_check ) && ! array_key_exists( $attribute['name'], $attributes_to_check ) ) ) {
					continue;
				}

				// Set taxonomy name.
				$taxonomy_name = wc_sanitize_taxonomy_name( $attribute['name'] );
				// Init attr array.
				$this->single_product_attributes[ $taxonomy_name ] = array();

				if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {

					if ( ! taxonomy_exists( $taxonomy_name ) ) {
						continue;
					}

					// Get taxonomy.
					$attribute_taxonomy = $wc_product_attributes[ $taxonomy_name ];
					// Set description and default value.
					$this->single_product_attributes[ $taxonomy_name ]['descr'] = $this->get_attribute_taxonomy_descr( $attribute_taxonomy->attribute_id );

					// If is custom add values and tooltip.
					if ( array_key_exists( $attribute_taxonomy->attribute_type, $custom_tax ) ) {
						// Add type value.
						$this->single_product_attributes[ $taxonomy_name ]['type'] = $attribute_taxonomy->attribute_type;
						// Get terms and add to array.
						$product_id = $product->get_id();
						$terms      = wc_get_product_terms( $product_id, $taxonomy_name, array( 'fields' => 'all' ) );

						foreach ( $terms as $term ) {
							// Get value of attr.
							$value   = ywccl_get_term_meta( $term->term_id, '_yith_wccl_value', true, $taxonomy_name );
							$tooltip = ywccl_get_term_meta( $term->term_id, '_yith_wccl_tooltip', true, $taxonomy_name );

							// WPML: if value is empty, search in the default language term. Only value, skip tooltip.
							if ( $default_language && ! $value && apply_filters( 'yith_wccl_get_default_language_term_value', true, $term ) ) {
								$default_term           = $sitepress->get_object_id( $term->id, $term->taxonomy, false, $default_language );
								$default_term && $value = ywccl_get_term_meta( $default_term, '_yith_wccl_value', true, $taxonomy_name );
							}

							// Add terms values.
							$term_values = apply_filters(
								'yith_wccl_create_custom_attributes_term_attr',
								array(
									'value'   => $value,
									'tooltip' => $tooltip,
								),
								$taxonomy_name,
								$term,
								$product
							);

							$this->single_product_attributes[ $taxonomy_name ]['terms'][ $term->slug ] = $term_values;
						}
					}
				}
			}
		}

		/**
		 * Get product attribute taxonomy description for table yith_wccl_meta
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param integer $id WC attribute taxonomy ID.
		 * @return null|string
		 */
		public function get_attribute_taxonomy_descr( $id ) {

			global $wpdb;

			$meta_value = $wpdb->get_var( $wpdb->prepare( 'SELECT meta_value FROM ' . $wpdb->prefix . 'yith_wccl_meta WHERE wc_attribute_tax_id = %d', $id ) );

			return isset( $meta_value ) ? $meta_value : '';
		}

		/**
		 * Set shop catalog image to available variation array
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @return string
		 */
		public function set_shop_catalog_image() {
			return is_product() ? 'shop_single' : 'shop_catalog';
		}

		/**
		 * Add to cart in ajax
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function add_to_cart_ajax_loop() {

			if ( ! isset( $_REQUEST['action'] ) || sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) !== $this->action_add_to_cart || empty( $_REQUEST['product_id'] ) || empty( $_REQUEST['variation_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				die();
			}

			$product_id        = absint( $_REQUEST['product_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$variation_id      = absint( $_REQUEST['variation_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$quantity          = isset( $_REQUEST['quantity'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['quantity'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id );

			parse_str( $_REQUEST['attr'], $attributes ); // phpcs:ignore
			// Get product status.
			$product_status = get_post_status( $product_id );
			if ( empty( $attributes ) ) {
				die();
			}

			if ( $passed_validation && 'publish' === $product_status && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $attributes ) ) {

				do_action( 'woocommerce_ajax_added_to_cart', $product_id );

				if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
					wc_add_to_cart_message( $product_id );
				}

				// Fragments and mini cart are returned.
				WC_AJAX::get_refreshed_fragments();
			} else {
				// If there was an error adding to the cart, redirect to the product page to show any errors.
				$data = array(
					'error'       => true,
					'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
				);
			}

			wp_send_json( $data );
			die();
		}

		/**
		 * Filter loop attributes for variation form
		 *
		 * @since  1.0.6
		 * @author Francesco Licandro
		 * @param array  $attributes An array of variations attributes.
		 * @param object $product Current product object.
		 * @return array
		 */
		public function format_available_variations_loop( $attributes, $product ) {

			if ( class_exists( 'JCKWooThumbs' ) ) {
				return $attributes;
			}

			foreach ( $attributes as &$attr ) {
				unset(
					$attr['dimensions'],
					$attr['dimensions_html'],
					$attr['max_qty'],
					$attr['min_qty'],
					$attr['is_virtual'],
					$attr['weight'],
					$attr['weight_html'],
					$attr['is_downloadable'],
					$attr['is_sold_individually'],
					$attr['variation_description']
				);
			}

			if ( empty( $attr['image'] ) && ! empty( $attr['variation_id'] ) ) { // Double check for image attr.
				$variation = wc_get_product( $attr['variation_id'] );
				if ( $variation ) {
					$attr['image'] = wc_get_product_attachment_props( $variation->get_image_id() );
				}
			}

			return apply_filters( 'yith_wccl_available_variations_loop', $attributes, $product );
		}

		/**
		 * Create yith-wccl-data attr
		 *
		 * @since    1.10.4
		 * @author    Francesco Licandro
		 * @param integer $product_id Current product ID.
		 * @return void
		 */
		public function create_wccl_data_attr( $product_id ) {
			$attr = $this->create_attributes_json( $product_id, true );

			if ( $attr ) {
				?>
				<div class="yith-wccl-data" style="display:none;"
						data-attr="<?php echo htmlspecialchars( wp_json_encode( $attr ) ); ?>"></div>
				<?php
			}
		}

		/**
		 * Compatibility with WooCommerce Quick View Plugin from WooThemes.
		 * This method adds attribute json for product in quick view
		 *
		 * @since  1.1.0
		 * @author Francesco Licandro
		 */
		public function wc_quick_view_json_product_attr() {
			global $product;
			$this->create_wccl_data_attr( $product->get_id() );
		}

		/**
		 * Compatibility with Woocommerce Variations Update in Cart
		 * This method adds attribute json for product in plugin modal
		 *
		 * @since  1.10.4
		 * @author Francesco Licandro
		 */
		public function mwb_wvuc_json_product_attr() {
			$product_id = ! empty( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! $product_id ) {
				return;
			}
			$this->create_wccl_data_attr( $product_id );
		}

		/**
		 * Compatibility with Woocommerce Variations Update in Cart
		 * This method adds custom JS for form in plugin modal
		 *
		 * @since    1.10.4
		 * @author    Francesco Licandro
		 */
		public function mwb_wvuc_add_custom_script() {
			?>
			<script type="application/javascript">
				(function( $ ) {
					let a = $( '#mwb-wvuc-variation-container' ).find( '.yith-wccl-data' ).data( 'attr' );
					if ( a ) $.yith_wccl( a );
				})( jQuery )
			</script>
			<?php
		}

		/**
		 * Add style also on tab Product Attributes
		 *
		 * @since  1.2.0
		 * @author Francesco Licandro
		 * @param string $html Current tab html.
		 * @param array  $attribute Attribute data array.
		 * @param array  $values Attribute values.
		 * @return string
		 */
		public function product_attributes_tab( $html, $attribute, $values ) {

			global $product;

			if ( ! $attribute['is_taxonomy'] || 'no' === get_option( 'yith-wccl-show-custom-on-tab', 'no' ) ) {
				return $html;
			}

			// Ensure that global attribute array isset.
			empty( $this->single_product_attributes ) && $this->create_custom_attributes_array( $product );

			// Get values from global array.
			$attribute_name = $attribute['name'];
			$custom_values  = isset( $this->single_product_attributes[ $attribute_name ] ) ? $this->single_product_attributes[ $attribute_name ] : array();

			if ( empty( $custom_values ) || ! isset( $custom_values['type'] ) ) {
				return $html;
			}

			$custom_html = apply_filters( 'yith_wccl_custom_html_for_attributes_in_custom_tab', $this->render_attributes_type( $custom_values ), $custom_values );

			return $custom_html ? $custom_html : $html;
		}

		/**
		 * Render custom attributes type for product tab
		 *
		 * @since  1.2.0
		 * @author Francesco Licandro
		 * @param array $values An array of attribute values.
		 * @return string
		 */
		public function render_attributes_type( $values ) {

			$tooltip     = 'yes' === get_option( 'yith-wccl-enable-tooltip', 'yes' );
			$tooltip_pos = get_option( 'yith-wccl-tooltip-position', 'top' );
			$tooltip_ani = get_option( 'yith-wccl-tooltip-animation', 'fade' );

			$html = '<div class="select_box_' . $values['type'] . ' select_box on_ptab">';

			foreach ( $values['terms'] as $term ) {

				$html .= '<div class="select_option_' . $values['type'] . ' select_option">';
				// Get values.
				$term_values = explode( ',', $term['value'] );

				switch ( $values['type'] ) {
					case 'colorpicker': // Type color.
						if ( count( $term_values ) > 1 ) {
							$style = "border-bottom-color:{$term_values[0]};border-left-color:{$term_values[1]}";
							$html .= '<span class="yith_wccl_value"><span class="yith-wccl-bicolor" style="' . $style . '"></span></span>';
						} else {
							$html .= '<span class="yith_wccl_value" style="background-color:' . $term_values[0] . '"></span>';
						}
						break;
					case 'label': // Type label.
						$html .= '<span class="yith_wccl_value">' . $term_values[0] . '</span>';
						break;
					case 'image': // Type image.
						$html .= '<img class="yith_wccl_value" src="' . $term_values[0] . '" alt="" />';
						break;
					default:
						do_action( 'yith_wccl_render_attributes_type_' . $values['type'], $values );
						break;
				}

				// Add tooltip if any.
				if ( $term['tooltip'] && $tooltip ) {
					$tooltip_class   = $tooltip_pos . ' ' . $tooltip_ani;
					$tooltip_content = esc_html( $term['tooltip'] );

					// Replace placeholder for image.
					if ( 'image' === $values['type'] ) {
						$image           = '<img src="' . $term_values[0] . '" />';
						$tooltip_content = str_replace( '{show_image}', $image, $tooltip_content );
					}

					$html .= '<span class="yith_wccl_tooltip ' . $tooltip_class . '"><span>' . $tooltip_content . '</span></span>';
				}

				$html .= '</div>';
			}

			$html .= '</div>';

			return $html;
		}

		/**
		 * Add variable product type to allowed product type for raq
		 *
		 * @since  1.6.0
		 * @author Francesco Licandro
		 * @param array $types An array of allowed product types for raq.
		 * @return array
		 */
		public function raq_add_variable_products( $types ) {
			if ( 'yes' === get_option( 'yith-wccl-enable-in-loop', 'no' ) ) {
				$types[] = 'variable';
			}

			return $types;
		}

		/**
		 * Variation Gallery Ajax
		 *
		 * @since  1.8.0
		 * @author Francesco Licandro
		 */
		public function variation_gallery() {
			if ( ! isset( $_REQUEST['action'] ) || $this->action_variation_gallery !== sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) || empty( $_REQUEST['id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				die();
			}

			// Set the main wp query for the product.
			global $post, $product;

			$html    = '';
			$id      = absint( $_REQUEST['id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post    = get_post( $id ); // To fix junk theme compatibility.
			$product = wc_get_product( $id );

			if ( $product ) {
				$gallery = yith_wccl_get_variation_gallery( $product );
				if ( ! empty( $gallery ) ) {
					// Filter gallery based on current variation.
					add_filter( 'woocommerce_product_variation_get_gallery_image_ids', array( $this, 'filter_gallery_ids' ), 10, 2 );
				} else {
					// Get the variable.
					$product = ( $product instanceof WC_Product_Variation ) ? wc_get_product( $product->get_parent_id() ) : $product;
				}

				ob_start();
				woocommerce_show_product_images();
				$html = ob_get_clean();
			}

			// Let's filter the html.
			echo apply_filters( 'yith_wccl_filter_variation_gallery_html', $html, $product );
			die();
		}

		/**
		 * Filter gallery ids based on current variation
		 *
		 * @since  1.8.0
		 * @author Francesco Licandro
		 * @param array                $gallery The gallery images array.
		 * @param WC_Product_Variation $variation Current product variations.
		 * @return array
		 */
		public function filter_gallery_ids( $gallery, $variation ) {
			return yith_wccl_get_variation_gallery( $variation );
		}


		/**
		 * Customize WP Query to show single variations in WooCommerce archive pages
		 *
		 * @param WP_Query $q WP_Query object.
		 * @param WC_Query $wc_query WC_Query instance.
		 */
		public function show_single_variations( $q, $wc_query ) {

			if ( 'yes' !== get_option( 'yith-wccl-show-single-variations-loop', 'no' ) ) {
				return;
			}

			if ( isset( $q ) && isset( $q->query_vars['wc_query'] ) && $q->is_main_query() ) {

				$post_type   = (array) $q->get( 'post_type' );
				$post_type[] = 'product_variation';
				if ( ! in_array( 'product', $post_type ) ) {
					$post_type[] = 'product';
				}

				$q->set( 'post_type', array_filter( $post_type ) );

				if ( 'yes' === get_option( 'yith-wccl-order-products-by-id', 'no' ) ) {
					$q->set( 'orderby', 'ID' );
				}

				if ( 'yes' === get_option( 'yith-wccl-hide-parent-products-loop', 'yes' ) ) {
					$parent_ids_to_exclude = $this->get_all_parent_product_ids();
					if ( ! empty( $parent_ids_to_exclude ) ) {
						$post__not_in = (array) $q->get( 'post__not_in' );
						$q->set( 'post__not_in', array_merge( $post__not_in, $parent_ids_to_exclude ) );
					}
				}

				$excluded_variations = $this->get_all_excluded_variation();
				if ( ! empty( $excluded_variations ) ) {
					$post__not_in = (array) $q->get( 'post__not_in' );
					$q->set( 'post__not_in', array_merge( $post__not_in, $excluded_variations ) );
				}
			}
		}

		/**
		 * Customize WooCommerce shortcode to show single variations in WooCommerce archive pages
		 *
		 * @author Francesco Licandro
		 * @param array  $query_args An array of query args.
		 * @param array  $attributes An array of variation attributes.
		 * @param string $type Product type.
		 * @return array
		 */
		public function show_single_variations_shortcode( $query_args, $attributes, $type ) {

			if ( 'yes' !== get_option( 'yith-wccl-show-single-variations-loop', 'no' ) || 'products' !== $type ) {
				return $query_args;
			}

			$query_args['post_type'] = array( 'product', 'product_variation' );
			if ( 'yes' === get_option( 'yith-wccl-order-products-by-id', 'no' ) && empty( $query_args['orderby'] ) ) {
				$query_args['orderby'] = 'ID';
			}

			if ( empty( $query_args['post__not_in'] ) || ! is_array( $query_args['post__not_in'] ) ) {
				$query_args['post__not_in'] = array();
			}

			if ( 'yes' === get_option( 'yith-wccl-hide-parent-products-loop', 'yes' ) ) {
				$parent_ids_to_exclude = $this->get_all_parent_product_ids();
				if ( ! empty( $parent_ids_to_exclude ) ) {
					$query_args['post__not_in'] = array_unique( array_merge( $query_args['post__not_in'], $parent_ids_to_exclude ) );
				}
			}

			$excluded_variations = $this->get_all_excluded_variation();
			if ( ! empty( $excluded_variations ) ) {
				$query_args['post__not_in'] = array_unique( array_merge( $query_args['post__not_in'], $excluded_variations ) );
			}

			return $query_args;
		}

		/**
		 * Get IDs of all variations
		 *
		 * @return array|bool
		 */
		private function get_all_parent_product_ids() {

			$transient = 'yith_wccl_variations_parent_product_id';
			if ( false === ( $parents = get_transient( $transient ) ) ) {
				global $wpdb;
				$parents = $wpdb->get_col(
					$wpdb->prepare( "SELECT DISTINCT post_parent FROM {$wpdb->posts} WHERE post_type = %s AND post_parent NOT IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s )", 'product_variation', '_yith_wccl_variable_in_loop' )
				);

				set_transient( $transient, $parents, DAY_IN_SECONDS );
			}

			return $parents;
		}

		/**
		 * Get IDs of all variations
		 *
		 * @return array|bool
		 */
		private function get_all_excluded_variation() {

			$transient = 'yith_wccl_loop_excluded_variations';
			if ( false === ( $excluded = get_transient( $transient ) ) ) {
				global $wpdb;
				$excluded = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", '_yith_wccl_in_loop', 'no' ) );

				set_transient( $transient, $excluded, DAY_IN_SECONDS );
			}

			return $excluded;
		}


		/**
		 * Redirect to shop page once the variation has been added to the cart
		 */
		public function redirect_after_add_to_cart_for_variation() {
			if ( isset( $_GET['ywccl_rd'] ) ) {
				$redirect     = $_GET['ywccl_rd'];
				$redirect_url = add_query_arg( 'ywccl_variation_id', $_GET['variation_id'], $redirect );
				wp_redirect( $redirect_url );
			} elseif ( isset( $_GET['ywccl_variation_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$product      = wc_get_product( absint( $_GET['ywccl_variation_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$product_name = $product->get_name();
				// translators: %1$s stand form the product name.
				wc_add_notice( sprintf( __( ' "%1$s" has been added to cart correctly', 'yith-woocommerce-color-label-variations' ), $product_name ) );
			}
		}


		/**
		 * Add query arg to add to cart url for single variations
		 *
		 * @param string          $url Current add to cart url.
		 * @param WC_Product|null $product Current product object.
		 * @return string
		 */
		public function add_query_arg_to_add_to_cart_url( $url, $product ) {

			if ( $product instanceof WC_Product_Variation ) {
				global $wp;
				$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
				$url        .= '&ywccl_rd=' . $current_url;
			}

			return $url;
		}


		/**
		 * Support to YITH WooCommerce Request a Quote. Show add to quote button on single variations
		 *
		 * @param array $types An array of allowed types.
		 * @return array
		 */
		public function show_add_to_quote_button_on_single_variation( $types ) {
			if ( 'yes' === get_option( 'yith-wccl-show-single-variations-loop', 'no' ) ) {
				$types[] = 'variation';
			}

			return $types;
		}

		/**
		 * Maybe wrap the variation gallery based on theme installed
		 *
		 * @since 1.10.2
		 * @author Francesco Licandro
		 * @param string $html Current variation gallery html.
		 * @return string
		 */
		public function maybe_wrap_variation_gallery( $html ) {

			if ( empty( $html ) ) {
				return $html;
			}

			$theme = yith_wccl_get_current_theme();

			if ( 'flatsome' === $theme && function_exists( 'flatsome_defaults' ) && 'gallery-wide' !== get_theme_mod( 'product_layout', flatsome_defaults( 'product_layout' ) ) ) {
				ob_start();
				?>
				<div class="product-gallery large-<?php echo esc_attr( get_theme_mod( 'product_image_width', flatsome_defaults( 'product_image_width' ) ) ); ?> col">
					<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<?php
				return ob_get_clean();
			}

			return $html;
		}
	}
}


/**
 * Unique access to instance of YITH_WCCL_Frontend class
 *
 * @since 1.0.0
 * @return YITH_WCCL_Frontend
 */
function YITH_WCCL_Frontend() {
	return YITH_WCCL_Frontend::get_instance();
}
