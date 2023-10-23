<?php
/**
 * Frontend class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Color_Label_Variations_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 2.0.0
	 */
	class YITH_WAPO_Color_Label_Variations_Frontend {

		/**
		 * Variation gallery ajax action
		 *
		 * @since 2.0.0
		 * @var string
		 */
		const ACTION_VARIATION_GALLERY = 'yith_wccl_variation_gallery';

		/**
		 * Single instance of the class
		 *
		 * @since 2.0.0
		 * @var YITH_WAPO_Color_Label_Variations_Frontend
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
		 * Returns single instance of the class
		 *
		 * @since 2.0.0
		 * @return YITH_WAPO_Color_Label_Variations_Frontend
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
			// enqueue scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			// ajax variation gallery.
			add_action( 'wc_ajax_' . self::ACTION_VARIATION_GALLERY, array( $this, 'variation_gallery' ) );
			add_action( 'wp_ajax_nopriv_' . self::ACTION_VARIATION_GALLERY, array( $this, 'variation_gallery' ) );
			// maybe filter the variation gallery html.
			add_filter( 'yith_wccl_filter_variation_gallery_html', array( $this, 'maybe_wrap_variation_gallery' ) );
		}

		/**
		 * Enqueue scripts
		 *
		 * @since  1.0.0
		 */
		public function enqueue_scripts() {

			global $post;

			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'yith_wapo_color_label_frontend', YITH_WAPO_WCCL_ASSETS_URL . 'js/frontend' . $min . '.js', array( 'jquery', 'wc-add-to-cart-variation' ), YITH_WAPO_VERSION, true );
			wp_register_style( 'yith_wapo_color_label_frontend', YITH_WAPO_WCCL_ASSETS_URL . 'css/frontend.css', false, YITH_WAPO_VERSION );

			if ( is_product() ) {

				wp_enqueue_script( 'yith_wapo_color_label_frontend' );
				wp_enqueue_style( 'yith_wapo_color_label_frontend' );


				wp_localize_script(
					'yith_wapo_color_label_frontend',
					'yith_wapo_color_label_attr',
					array(
						'ajaxurl'                         => WC_AJAX::get_endpoint( '%%endpoint%%' ),
						'actionVariationGallery'          => self::ACTION_VARIATION_GALLERY,
						'tooltip'                         => 'yes' === get_option( 'yith-wccl-enable-tooltip', 'yes' ),
						'tooltip_pos'                     => get_option( 'yith-wccl-tooltip-position', 'top' ),
						'tooltip_ani'                     => get_option( 'yith-wccl-tooltip-animation', 'fade' ),
						'description'                     => 'yes' === get_option( 'yith-wccl-enable-description', 'yes' ),
						'grey_out'                        => 'grey' === get_option( 'yith-wccl-attributes-style', 'hide' ),
						'enable_handle_variation_gallery' => apply_filters( 'yith_wccl_enable_handle_variation_gallery', true, $post ),
						'single_gallery_selector'         => apply_filters( 'yith_wccl_single_variation_gallery_selector', 'flatsome' === $this->get_current_theme() ? '.product-gallery' : '.woocommerce-product-gallery' ),
						'attributes'                      => wp_json_encode( $this->create_attributes_json() ),
					)
				);

				wp_add_inline_style( 'yith_wapo_color_label_frontend', $this->get_custom_style() );
			}
		}

		/**
		 * Get inline style for module. Based also on wapo options.
		 *
		 * @since 2.0.0
		 * @return string
		 */
		protected function get_custom_style() {
			// Init variables.
			$variables = array();

			$tooltip_option = get_option(
				'yith_wapo_tooltip_color',
				array(
					'text'       => '#ffffff',
					'background' => '#03bfac',
				)
			);

			$variables['tooltip-background'] = $tooltip_option['background'];
			$variables['tooltip-text-color'] = $tooltip_option['text'];

			// Select option size.
			$variables['select-option-size'] = absint( get_option( 'yith_wapo_style_color_swatch_size', '40' ) ) . 'px';
			// Select option style.
			$variables['select-option-radius'] = 'rounded' === get_option( 'yith_wapo_style_color_swatch_style', 'rounded' ) ? '50%' : '0';

			$variables = apply_filters( 'yith_wapo_color_label_custom_css_variables', array_filter( $variables ) );
			if ( empty( $variables ) ) {
				return '';
			}

			$inline_css = ':root {';
			foreach ( $variables as $key => $value ) {
				$inline_css .= '--yith-wccl-' . $key . ': ' . $value . ';';
			}
			$inline_css .= '}';
			// Remove whitespaces and line breaks.
			$inline_css = trim( preg_replace( '/\s\s+/', ' ', $inline_css ) );

			return $inline_css;
		}

		/**
		 * Get an array of types and values for each attribute
		 *
		 * @access public
		 * @since  1.0.0
		 *
		 * @param array $attributes Attributes.
		 */
		public function get_variation_attributes_types( $attributes ) {
			global $wc_product_attributes;
			$types        = array();
			$defined_attr = YITH_WAPO_Color_Label_Variations::get_custom_attribute_types();

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
		 * @return array
		 */
		public function create_attributes_json() {

			global $post;

			if ( empty( $post ) ) {
				return array();
			}

			$product = wc_get_product( $post->ID );
			if ( empty( $product ) || ! $product->is_type( 'variable' ) ) {
				return array();
			}

			// Ensure that global array isset.
			empty( $this->single_product_attributes ) && $this->create_custom_attributes_array( $product );


			// Get attribute used for variation.
			$attributes_variations        = $product->get_variation_attributes();
			$custom_attributes_variations = array();

			// Remove unused attribute for variation.
			foreach ( (array) $this->single_product_attributes as $key => $value ) {
				if ( array_key_exists( $key, $attributes_variations ) ) {
					// first set key then add to json array.
					$variation_key = function_exists( 'wc_variation_attribute_name' ) ? wc_variation_attribute_name( $key ) : 'attribute_' . sanitize_title( $key );
					$custom_attributes_variations[ $variation_key ] = $value;
				}
			}


			return $custom_attributes_variations;
		}

		/**
		 * Create product attributes array with custom values
		 *
		 * @since  1.1.1
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

			$custom_tax = YITH_WAPO_Color_Label_Variations::get_custom_attribute_types();

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
							$value   = yith_wapo_get_term_meta( $term->term_id, '_yith_wccl_value', true, $taxonomy_name );
							$tooltip = yith_wapo_get_term_meta( $term->term_id, '_yith_wccl_tooltip', true, $taxonomy_name );

							// WPML: if value is empty, search in the default language term. Only value, skip tooltip.
							if ( $default_language && ! $value && apply_filters( 'yith_wccl_get_default_language_term_value', true, $term ) ) {
								$default_term = $sitepress->get_object_id( $term->id, $term->taxonomy, false, $default_language );
								if ( $default_term ) {
									$value = yith_wapo_get_term_meta( $default_term, '_yith_wccl_value', true, $taxonomy_name );
								}
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
		 * @param integer $id ID.
		 */
		public function get_attribute_taxonomy_descr( $id ) {

			global $wpdb;

			$meta_value = $wpdb->get_var( $wpdb->prepare( 'SELECT meta_value FROM ' . $wpdb->prefix . 'yith_wccl_meta WHERE wc_attribute_tax_id = %d', $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

			return isset( $meta_value ) ? $meta_value : '';
		}

		/**
		 * Variation Gallery Ajax
		 *
		 * @since  1.8.0
		 */
		public function variation_gallery() {
			if ( ! isset( $_REQUEST['action'] ) || sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) !== self::ACTION_VARIATION_GALLERY || ! isset( $_REQUEST['id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				die();
			}

			// set the main wp query for the product.
			global $post, $product;

			$html    = '';
			$id      = absint( $_REQUEST['id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post    = get_post( $id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$product = wc_get_product( $id );

			if ( $product ) {
				$gallery = YITH_WAPO_Color_Label_Variations::get_variation_gallery( $product );
				if ( ! empty( $gallery ) ) {
					// filter gallery based on current variation.
					add_filter( 'woocommerce_product_variation_get_gallery_image_ids', array( $this, 'filter_gallery_ids' ), 10, 2 );
				} else {
					// get the variable.
					$product = ( $product instanceof WC_Product_Variation ) ? wc_get_product( $product->get_parent_id() ) : $product;
				}

				ob_start();
				woocommerce_show_product_images();
				$html = ob_get_clean();
			}

			// let's filter the html.
			echo wp_kses_post( apply_filters( 'yith_wccl_filter_variation_gallery_html', $html, $product ) );
			die();
		}

		/**
		 * Filter gallery ids based on current variation
		 *
		 * @since  1.8.0
		 * @param array                $gallery   Gallery.
		 * @param WC_Product_Variation $variation Variation.
		 * @return array
		 */
		public function filter_gallery_ids( $gallery, $variation ) {
			return YITH_WAPO_Color_Label_Variations::get_variation_gallery( $variation );
		}

		/**
		 * Maybe wrap the variation gallery based on theme installed
		 *
		 * @since  1.10.2
		 * @param string $html HTML.
		 * @return string
		 */
		public function maybe_wrap_variation_gallery( $html ) {

			if ( empty( $html ) ) {
				return $html;
			}

			if ( 'flatsome' === $this->get_current_theme() && function_exists( 'flatsome_defaults' ) && 'gallery-wide' !== get_theme_mod( 'product_layout', flatsome_defaults( 'product_layout' ) ) ) {
				ob_start();
				?>
				<div class="product-gallery large-<?php echo esc_attr( get_theme_mod( 'product_image_width', flatsome_defaults( 'product_image_width' ) ) ); ?> col">
					<?php echo wp_kses_post( $html ); ?>
				</div>
				<?php
				return ob_get_clean();
			}

			return $html;
		}

		/**
		 * Return current active theme
		 *
		 * @since 1.10.2
		 * @return string
		 */
		protected function get_current_theme() {

			// Get the installed theme.
			$theme = wp_cache_get( 'yith_wapo_color_label_current_theme', 'yith_wapo_color_label' );
			if ( false === $theme ) {
				$theme = '';
				if ( function_exists( 'wp_get_theme' ) ) {
					if ( is_child_theme() ) {
						$temp_obj  = wp_get_theme();
						$theme_obj = wp_get_theme( $temp_obj->get( 'Template' ) );
					} else {
						$theme_obj = wp_get_theme();
					}

					$theme = $theme_obj->get( 'TextDomain' );
					if ( empty( $theme ) ) {
						$theme = $theme_obj->get( 'Name' );
					}
				}

				wp_cache_set( 'yith_wapo_color_label_current_theme', $theme, 'yith_wapo_color_label' );
			}

			return $theme;
		}
	}
}


/**
 * Unique access to instance of YITH_WAPO_Color_Label_Variations_Frontend class
 *
 * @since 1.0.0
 * @return YITH_WAPO_Color_Label_Variations_Frontend
 */
function YITH_WAPO_Color_Label_Variations_Frontend() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WAPO_Color_Label_Variations_Frontend::get_instance();
}

YITH_WAPO_Color_Label_Variations_Frontend();
