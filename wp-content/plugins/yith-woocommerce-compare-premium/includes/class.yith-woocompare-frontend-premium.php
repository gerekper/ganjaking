<?php
/**
 * Frontend class premium
 *
 * @author YITH
 * @package YITH Woocommerce Compare Premium
 * @version 1.1.4
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_Woocompare_Frontend_Premium' ) ) {
	/**
	 * YITH Custom Login Frontend
	 *
	 * @since 1.0.0
	 */
	class YITH_Woocompare_Frontend_Premium extends YITH_Woocompare_Frontend {

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WOOCOMPARE_VERSION;

		/**
		 * The list of current cat inside the comparison table
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $current_cat = array();

		/**
		 * An array of excluded categories from plugin options
		 *
		 * @since 2.1.0
		 * @var array
		 */
		public $excluded_categories = array();

		/**
		 * Check only categories in the exclusion list will have the compare feature
		 *
		 * @since 2.1.0
		 * @var boolean
		 */
		public $excluded_categories_inverse = false;

		/**
		 * Option for use page or popup
		 *
		 * @since 2.1.0
		 * @var array
		 */
		public $page_or_popup = 'popup';

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $template_file = 'yith-compare-popup.php';

		/**
		 * Stylesheet file
		 *
		 * @since 2.1.0
		 * @var string
		 */
		public $stylesheet_file = 'compare-premium.css';

		/**
		 * The name of categories cookie name
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $cat_cookie = 'yith_woocompare_current_cat';

		/**
		 * The name of limit reached cookie name
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $limit_cookie = 'yith_woocompare_limit_reached';

		/**
		 * If product limits is reached
		 *
		 * @since 1.0.0
		 * @var boolean
		 */
		public $limit_reached = false;

		/**
		 * Check if related is enabled
		 *
		 * @since 2.1.0
		 * @var boolean
		 */
		public $related_enabled = false;

		/**
		 * Check if share is enabled
		 *
		 * @since 2.1.0
		 * @var boolean
		 */
		public $share_enabled = false;

		/**
		 * Check if option for Compare by category is active
		 *
		 * @since 2.1.0
		 * @var boolean
		 */
		public $compare_by_cat = false;

		/**
		 * The action used to view the comparison table
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $action_filter = 'yith_woocompare_filter_by_cat';

		/**
		 * The compare page id
		 *
		 * @since 2.1.0
		 * @var int
		 */
		public $page_id = 0;

		/**
		 * Is or not an iFrame
		 *
		 * @since 2.2.2
		 * @var string
		 */
		public $is_iframe = 'no';

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			parent::__construct();

			add_action( 'init', array( $this, 'init_variables' ), 1 );

			// Populate the list of current categories.
			add_action( 'init', array( $this, 'update_cat_cookie' ), 11 );

			// Before table.
			add_action( 'yith_woocompare_before_main_table', array( $this, 'add_error_limit_message' ), 5 );
			add_action( 'yith_woocompare_before_main_table', array( $this, 'add_logo_to_compare' ), 10 );
			add_action( 'yith_woocompare_before_main_table', array( $this, 'print_filter_by_cat' ), 20, 2 );
			add_action( 'yith_woocompare_before_main_table', array( $this, 'add_clear_all_button' ), 30, 2 );
			add_action( 'yith_woocompare_before_main_table', array( $this, 'wc_bundle_compatibility' ) );

			// After table.
			add_action( 'yith_woocompare_after_main_table', array( $this, 'social_share' ), 10 );
			add_action( 'yith_woocompare_after_main_table', array( $this, 'related_product' ), 20, 2 );

			// List action.
			add_action( 'yith_woocompare_after_add_product', array( $this, 'update_cat_cookie_after_add' ), 10, 1 );
			add_action( 'yith_woocompare_after_remove_product', array( $this, 'update_cat_cookie_after_remove' ), 10, 1 );

			add_filter( 'yith_woocompare_exclude_products_from_list', array( $this, 'exclude_product_from_list' ), 10, 1 );
			add_filter( 'yith_woocompare_view_table_url', array( $this, 'filter_table_url' ), 10, 2 );

			// Filter compare link.
			add_filter( 'yith_woocompare_remove_compare_link_by_cat', array( $this, 'exclude_compare_by_cat' ), 10, 2 );

			// Enqueue scripts styles premium.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_premium_scripts' ), 5 );

			// Localize args filter.
			add_filter( 'yith_woocompare_main_script_localize_array', array( $this, 'premium_localize_args' ), 10, 1 );

			// Add the shortcode.
			add_shortcode( 'yith_woocompare_table', array( $this, 'compare_table_sc' ) );

			if ( $this->use_wc_ajax ) {
				add_action( 'wc_ajax_' . $this->action_filter, array( $this, $this->action_filter . '_ajax' ) );
			} else {
				add_action( 'wp_ajax_' . $this->action_filter, array( $this, $this->action_filter . '_ajax' ) );
			}
			add_action( 'wp_ajax_nopriv' . $this->action_filter, array( $this, $this->action_filter . '_ajax' ) );

			// Filter remove response.
			add_action( 'yith_woocompare_remove_product_action_ajax', array( $this, 'remove_product_ajax_premium' ) );
			// Filter added response.
			add_action( 'yith_woocompare_add_product_action_ajax', array( $this, 'added_related_to_compare' ) );

			add_filter( 'yith_woocompare_get_different_fields_value', array( $this, 'add_different_class' ), 99, 1 );

			add_action( 'yith_woocompare_popup_head', array( $this, 'add_styles_themes_and_custom' ) );

			add_filter( 'yith_woocompare_add_product_action_json', array( $this, 'premium_add_product_action_json' ), 10, 1 );

			// Remove scripts from popup footer.
			add_action( 'yith_woocompare_popup_footer', array( $this, 'dequeue_footer_scripts' ), 10, 1 );

			// Add counter shortcode.
			add_shortcode( 'yith_woocompare_counter', array( $this, 'counter_compare_shortcode' ) );

			// Compatibility with YITH WooCommerce Color and Label Variations (single variations module).
			add_filter( 'yith_woocompare_single_variation_field_value', array( $this, 'yith_woocompare_set_single_variation_field_value' ), 10, 3 );
		}

		/**
		 * Init class variables
		 *
		 * @since 2.1.0
		 * @author Francesco Licandro
		 */
		public function init_variables() {

			parent::init_variables();

			$this->page_id                     = yith_woocompare_get_page_id();
			$this->page_or_popup               = get_option( 'yith_woocompare_use_page_popup', 'popup' );
			$this->related_enabled             = 'yes' === get_option( 'yith-woocompare-show-related', 'yes' );
			$this->share_enabled               = 'yes' === get_option( 'yith-woocompare-enable-share', 'yes' );
			$this->excluded_categories         = $this->get_excluded_categories();
			$this->excluded_categories_inverse = 'yes' === get_option( 'yith_woocompare_excluded_category_inverse', 'no' );
			$this->compare_by_cat              = 'yes' === get_option( 'yith_woocompare_use_category', 'no' );

			if ( isset( $_REQUEST['iframe'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->is_iframe = sanitize_text_field( wp_unslash( $_REQUEST['iframe'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			if ( isset( $_COOKIE[ $this->limit_cookie ] ) && 'yes' === sanitize_text_field( wp_unslash( $_COOKIE[ $this->limit_cookie ] ) ) ) {
				setcookie( $this->limit_cookie, 'no', 0, COOKIEPATH, COOKIE_DOMAIN, false, false );
				$this->limit_reached = true;
			}
		}

		/**
		 * Add a product in the products comparison table
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param integer $product_id Product ID to add in the comparison table.
		 * @return boolean
		 */
		public function add_product_to_compare( $product_id ) {

			if ( $this->limit_reached() ) {
				setcookie( $this->limit_cookie, 'yes', 0, COOKIEPATH, COOKIE_DOMAIN, false, false );

				return false;
			}

			$this->products_list[] = absint( $product_id );
			$expiry                = apply_filters( 'yith_woocompare_cookie_expiration', 0 );

			setcookie( $this->get_cookie_name(), wp_json_encode( $this->products_list ), $expiry, COOKIEPATH, COOKIE_DOMAIN, false, false );

			do_action( 'yith_woocompare_after_add_product', $product_id );

			return true;
		}

		/**
		 * Check if compared product limit is reached
		 *
		 * @since 2.3.2
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function limit_reached() {
			$limit = absint( get_option( 'yith_woocompare_num_product_compared', 0 ) );
			if ( $limit && count( $this->products_list ) >= $limit ) {
				$this->limit_reached = true;
			} else {
				$this->limit_reached = false;
			}

			return $this->limit_reached;
		}

		/**
		 * Update current categories cookie
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 * @param mixed $categories An array of categories.
		 */
		public function update_cat_cookie( $categories = array() ) {

			if ( isset( $_REQUEST['yith_compare_prod'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$products = explode( ',', rawurldecode( sanitize_text_field( wp_unslash( $_REQUEST['yith_compare_prod'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->set_variables_prod_cat( $products );
			} elseif ( isset( $_REQUEST['yith_compare_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->current_cat = array( absint( $_REQUEST['yith_compare_cat'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			} elseif ( ! empty( $categories ) ) {
				$this->current_cat = $categories;
			} elseif ( ! empty( $this->products_list ) ) {
				$products   = $this->products_list;
				$product    = array_pop( $products );
				$categories = array_keys( $this->get_product_categories( $product ) );

				$this->current_cat = $categories;
			} else {
				$this->current_cat = $categories;
			}
		}

		/**
		 * Set products_list and current_cat variables if $_REQUEST['yith_compare_prod'] is set
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 * @param array $products An array of products.
		 */
		public function set_variables_prod_cat( $products ) {

			$product = array_pop( $products );
			if ( ! isset( $_REQUEST['yith_compare_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$categories = array_keys( $this->get_product_categories( $product ) );
			} else {
				$categories = array( absint( $_REQUEST['yith_compare_cat'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			$this->current_cat = $categories;
		}

		/**
		 * Update cookie after add to compare table
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 * @param integer $id The product ID.
		 */
		public function update_cat_cookie_after_add( $id ) {
			$categories = array_keys( $this->get_product_categories( $id ) );

			if ( ! empty( $categories ) ) {
				$this->update_cat_cookie( $categories );
			}
		}

		/**
		 * Update cookie after remove product from compare table
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 * @param integer $id The product ID.
		 */
		public function update_cat_cookie_after_remove( $id ) {
			// Get categories.
			$categories = $this->get_product_categories( $id );
			$exist      = array();

			if ( ! empty( $categories ) ) {
				foreach ( $this->products_list as $product ) {
					$cat = $this->get_product_categories( $product );
					foreach ( $cat as $id => $name ) {
						if ( array_key_exists( $id, $categories ) && ! in_array( $id, $exist, true ) ) {
							$exist[] = $id;
						}
					}
				}
			}

			// Remove old cookie.
			if ( isset( $_COOKIE[ $this->cat_cookie ] ) ) {
				unset( $_COOKIE[ $this->cat_cookie ] );
			}

			$this->update_cat_cookie( $exist );
		}

		/**
		 * Enqueue premium scripts and styles
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function enqueue_premium_scripts() {

			global $sitepress, $post;

			wp_register_style( 'yith_woocompare_page', $this->stylesheet_url(), array(), YITH_WOOCOMPARE_VERSION, 'all' );
			wp_register_script( 'yith_woocompare_owl', YITH_WOOCOMPARE_ASSETS_URL . '/js/owl.carousel.min.js', array( 'jquery' ), '2.0.0', true );
			wp_register_style( 'yith_woocompare_owl_style', YITH_WOOCOMPARE_ASSETS_URL . '/css/owl.carousel.css', array(), '2.0.0', 'all' );

			// dataTables.
			wp_register_script( 'jquery-fixedheadertable', YITH_WOOCOMPARE_ASSETS_URL . '/js/jquery.dataTables.min.js', array( 'jquery' ), '1.10.18', true );
			wp_register_style( 'jquery-fixedheadertable-style', YITH_WOOCOMPARE_ASSETS_URL . '/css/jquery.dataTables.css', array(), '1.10.18', 'all' );
			wp_register_script( 'jquery-fixedcolumns', YITH_WOOCOMPARE_ASSETS_URL . '/js/FixedColumns.min.js', array( 'jquery', 'jquery-fixedheadertable' ), '3.2.6', true );
			wp_register_script( 'jquery-imagesloaded', YITH_WOOCOMPARE_ASSETS_URL . '/js/imagesloaded.pkgd.min.js', array( 'jquery' ), '3.1.8', true );

			// If page remove colorbox.
			if ( 'page' === $this->page_or_popup ) {
				wp_dequeue_style( 'jquery-colorbox' );
				wp_dequeue_script( 'jquery-colorbox' );
			}

			// Get custom style.
			$inline_css = yith_woocompare_user_style();
			wp_add_inline_style( 'yith_woocompare_page', $inline_css );

			wp_enqueue_style( 'yith_woocompare_page' );
			wp_enqueue_style( 'jquery-fixedheadertable-style' );

			if ( apply_filters( 'yith_woocompare_enqueue_frontend_scripts_always', false ) || is_page( $this->page_id ) || ( ! is_null( $post ) && strpos( $post->post_content, '[yith_woocompare_table' ) !== false ) ) {

				wp_enqueue_script( 'jquery-fixedheadertable' );
				wp_enqueue_script( 'jquery-fixedcolumns' );
				wp_enqueue_script( 'jquery-imagesloaded' );

				if ( ! empty( $this->products_list ) || $this->related_enabled || ! isset( $_REQUEST['yith_compare_prod'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wp_enqueue_script( 'yith_woocompare_owl' );
					wp_enqueue_style( 'yith_woocompare_owl_style' );
				}
			}
		}

		/**
		 * Set custom style based on user options
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 * @return string
		 */
		public function custom_user_style() {
			return yith_woocompare_user_style();
		}

		/**
		 * Add premium args to localize script
		 *
		 * @since 2.1.0
		 * @author Francesco Licandro
		 * @param array $args An array of script args.
		 * @return array
		 */
		public function premium_localize_args( $args ) {
			return array_merge(
				$args,
				array(
					'is_page'          => 'page' === $this->page_or_popup,
					'page_url'         => $this->get_compare_page_url(),
					'im_in_page'       => is_page( $this->page_id ),
					'view_label'       => get_option( 'yith_woocompare_button_text_added', __( 'View Compare', 'yith-woocommerce-compare' ) ),
					'actionfilter'     => $this->action_filter,
					'num_related'      => get_option( 'yith-woocompare-related-visible-num', 4 ),
					'autoplay_related' => 'yes' === get_option( 'yith-woocompare-related-autoplay', 'no' ),
					'loader'           => YITH_WOOCOMPARE_ASSETS_URL . '/images/loader.gif',
					'button_text'      => get_option( 'yith_woocompare_button_text', __( 'Compare', 'yith-woocommerce-compare' ) ),
					'fixedcolumns'     => get_option( 'yith_woocompare_num_fixedcolumns', 1 ),
				)
			);
		}

		/**
		 * Add scripts for shortcodes
		 *
		 * @since 2.0.3
		 * @author Francesco Licandro
		 */
		public function add_scripts() {
			wp_enqueue_script( 'jquery-fixedheadertable' );
			wp_enqueue_script( 'jquery-fixedcolumns' );
			wp_enqueue_script( 'jquery-imagesloaded' );
		}

		/**
		 * The fields to show in the table
		 *
		 * @since 1.0.0
		 * @param array $products An array of products to compare.
		 * @return mixed
		 */
		public function fields( $products = array() ) {

			// Get options.
			$fields  = get_option( 'yith_woocompare_fields', array() );
			$dynamic = 'yes' === get_option( 'yith_woocompare_dynamic_attributes', 'no' );
			$custom  = 'yes' === get_option( 'yith_woocompare_custom_attributes', 'no' );

			// Remove disabled attributes.
			$fields = array_filter( $fields );

			foreach ( $fields as $key => $value ) {
				if ( isset( $this->default_fields[ $key ] ) ) { // Add if default.
					$fields[ $key ] = $this->default_fields[ $key ];
				} elseif ( taxonomy_exists( $key ) && ! $dynamic ) { // Add if in options.
					$fields[ $key ] = wc_attribute_label( $key );
				}
			}

			if ( ! empty( $products ) && ( $dynamic || $custom ) ) {

				foreach ( $products as $product_id ) {
					$product = wc_get_product( $product_id );
					if ( ! $product ) {
						continue;
					}

					if ( $product instanceof WC_Product_Variation ) {
						$parent_product = wc_get_product( $product->get_parent_id() );
						$attrs          = $parent_product->get_attributes();
					} else {
						$attrs = $product->get_attributes();
					}

					foreach ( $attrs as $key => $value ) {
						$key = urldecode( $key );

						if ( ! isset( $value['is_taxonomy'] ) || ( $value['is_taxonomy'] && ! array_key_exists( $key, $fields ) ) || ( $value['is_taxonomy'] && ! $dynamic ) || ( ! $value['is_taxonomy'] && ! $custom ) ) {
							continue;
						}

						$fields[ $key ] = wc_attribute_label( $key, $product );
					}
				}

				if ( $dynamic ) {
					foreach ( $fields as $key => $value ) {
						if ( true === $value ) {
							unset( $fields[ $key ] );
						}
					}
				}
			}

			return apply_filters( 'yith_woocompare_filter_table_fields', $fields, $products );
		}

		/**
		 * Return the array with all products and all attributes values
		 *
		 * @param mixed $products An array of products.
		 * @return array The complete list of products with all attributes value
		 */
		public function get_products_list( $products = array() ) {
			$list = array();

			if ( empty( $products ) ) {
				$products = $this->products_list;
			}
			$products = apply_filters( 'yith_woocompare_exclude_products_from_list', $products );
			$fields   = $this->fields( $products );

			foreach ( $products as $product_id ) {
				$product = $this->wc_get_product( $product_id );
				if ( ! $product ) {
					continue;
				}

				$product->fields = array();
				$attributes      = $product->get_attributes();

				// Custom attributes.
				foreach ( $fields as $field => $name ) {

					switch ( $field ) {
						case 'price':
							$product->fields[ $field ] = $product->get_price_html();
							break;
						case 'description':
							// Get description.
							$description = ( 'yes' === get_option( 'yith_woocompare_use_full_description', 'no' ) ) ? $product->get_description() : '';
							if ( ! $description ) {
								$description = apply_filters( 'woocommerce_short_description', $product->get_short_description() );
							}

							$product->fields[ $field ] = apply_filters( 'yith_woocompare_products_description', $description, $product );
							break;
						case 'stock':
							$availability = $product->get_availability();
							if ( empty( $availability['availability'] ) ) {
								$availability['availability'] = __( 'In stock', 'yith-woocommerce-compare' );
							}
							$product->fields[ $field ] = sprintf( '<span>%s</span>', esc_html( $availability['availability'] ) );
							break;
						case 'sku':
							$sku                       = $product->get_sku() ? $product->get_sku() : '-';
							$product->fields[ $field ] = $sku;
							break;
						case 'weight':
							$weight = $product->get_weight();
							$weight = $weight ? ( wc_format_localized_decimal( $weight ) . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ) ) : '-';

							$product->fields[ $field ] = sprintf( '<span>%s</span>', esc_html( $weight ) );
							break;
						case 'dimensions':
							$dimensions                = wc_format_dimensions( $product->get_dimensions( false ) );
							$product->fields[ $field ] = sprintf( '<span>%s</span>', esc_html( $dimensions ) );
							break;
						default:
							$sfield    = strtolower( rawurlencode( $field ) );
							$field_sep = apply_filters( 'yith_woocompare_field_separator', ', ', $field, $product );

							if ( taxonomy_exists( $field ) ) {
								$product->fields[ $field ] = array();
								$the_product_id            = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product_id; // Get always parent attributes for variations.
								$terms                     = get_the_terms( $the_product_id, $field );

								if ( ! empty( $terms ) ) {
									foreach ( $terms as $term ) {
										$term                        = sanitize_term( $term, $field );
										$product->fields[ $field ][] = $term->name;
									}
								}
								$product->fields[ $field ] = implode( $field_sep, $product->fields[ $field ] );
							} elseif ( ! empty( $attributes ) && isset( $attributes[ $sfield ] ) ) {

								$current_attribute = $attributes[ $sfield ];

								if ( ! empty( $current_attribute['options'] ) ) {
									$product->fields[ $field ] = implode( $field_sep, $current_attribute['options'] );
								}
							} else {
								do_action_ref_array( 'yith_woocompare_field_' . $field, array( $product, &$product->fields ) );
							}
							break;
					}
				}

				$list[ $product_id ] = $product;
			}

			return $list;
		}

		/**
		 *  Add the link to compare
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param integer $product_id The product ID.
		 * @param array   $args An array of link arguments.
		 * @return void
		 */
		public function add_compare_link( $product_id = false, $args = array() ) {
			extract( $args ); // phpcs:ignore

			if ( ! $product_id ) {
				global $product;
				$product_id = ! is_null( $product ) ? $product->get_id() : 0;
			}

			// Return if product doesn't exist.
			if ( empty( $product_id ) || apply_filters( 'yith_woocompare_remove_compare_link_by_cat', false, $product_id ) ) {
				return;
			}

			$is_button = ! isset( $button_or_link ) || ! $button_or_link ? get_option( 'yith_woocompare_is_button', 'button' ) : $button_or_link;

			if ( ! isset( $button_text ) || 'default' === $button_text ) {
				$button_text = get_option( 'yith_woocompare_button_text', __( 'Compare', 'yith-woocommerce-compare' ) );
			}

			// Set class.
			$class = '';
			if ( in_array( $product_id, $this->products_list, true ) ) {
				$categories = get_the_terms( $product_id, 'product_cat' );
				$cat        = array();

				if ( ! empty( $categories ) ) {
					foreach ( $categories as $category ) {
						$cat[] = $category->term_id;
					}
				}

				$link        = $this->view_table_url( $product_id );
				$class       = ' added';
				$button_text = get_option( 'yith_woocompare_button_text_added', __( 'View Compare', 'yith-woocommerce-compare' ) );
			} else {
				if ( apply_filters( 'yith_woocompare_skip_display_button', false ) ) {
					return;
				}
				$link = $this->add_product_url( $product_id );
			}

			// Add button class.
			$class = 'button' === $is_button ? $class . ' button' : $class;

			$button_text = apply_filters( 'yith_woocompare_compare_button_text', $button_text, $product_id );
			// Use this method to prevent wp_targeted_link_rel_callback.
			$button_target = apply_filters( 'yith_woocompare_compare_button_target', '' );
			$button_target = $button_target ? 'target="' . esc_attr( $button_target ) . '"' : '';

			printf( '<a href="%s" class="%s" data-product_id="%d" rel="nofollow" target="%s">%s</a>', esc_url( $link ), esc_attr( 'compare' . $class ), esc_attr( $product_id ), apply_filters( 'yith_woocompare_target_view_compare_button', '_self' ), esc_html( $button_text ) ); }

		/**
		 * Generate template vars
		 *
		 * @since 1.0.0
		 * @access protected
		 * @param array $products An array of products to compare.
		 * @return array
		 */
		protected function vars( $products = array() ) {

			// Get product list.
			$products = $this->get_products_list( $products );
			// Get field list.
			$fields        = $this->fields( $products );
			$different     = apply_filters( 'yith_woocompare_get_different_fields_value', $products );
			$show_title    = 'yes' === get_option( 'yith_woocompare_fields_product_info_title', 'yes' );
			$show_image    = 'yes' === get_option( 'yith_woocompare_fields_product_info_image', 'yes' );
			$show_add_cart = 'yes' === get_option( 'yith_woocompare_fields_product_info_add_cart', 'yes' );

			if ( ! $show_title && ! $show_image && ! $show_add_cart ) {
				unset( $fields['product_info'] );
			}

			$vars = array(
				'products'           => $products,
				'fields'             => $fields,
				'show_image'         => $show_image,
				'show_title'         => $show_title,
				'show_add_cart'      => $show_add_cart,
				'show_request_quote' => get_option( 'yith_woocompare_fields_product_info_request_quote', 'no' ),
				'repeat_price'       => get_option( 'yith_woocompare_price_end', 'no' ),
				'repeat_add_to_cart' => get_option( 'yith_woocompare_add_to_cart_end', 'no' ),
				'different'          => $different,
			);

			return $vars;
		}

		/**
		 * Add logo/image to compare table
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function add_logo_to_compare() {

			$image   = get_option( 'yith-woocompare-table-image', '' );
			$to_show = 'yes' === get_option( 'yith-woocompare-table-image-in-page', 'yes' ) && 'yes' !== $this->is_iframe;
			if ( ! $to_show ) {
				$to_show = 'yes' === get_option( 'yith-woocompare-table-image-in-popup', 'yes' );
			}

			if ( ! $image || ! $to_show ) {
				return;
			}

			ob_start();
			?>
			<div class="yith_woocompare_table_image">
				<img src="<?php echo esc_url( $image ); ?>"/>
			</div>
			<?php

			echo wp_kses_post( ob_get_clean() );
		}

		/**
		 * Get product categories
		 *
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param integer $product_id The product ID.
		 * @return mixed
		 */
		public function get_product_categories( $product_id ) {

			$cat        = array();
			$categories = array();

			if ( ! is_array( $product_id ) ) {
				$categories = get_the_terms( $product_id, 'product_cat' );
			} else {
				foreach ( $product_id as $id ) {

					$single_cat = get_the_terms( $id, 'product_cat' );

					if ( empty( $single_cat ) ) {
						continue;
					}
					// Get values.
					$single_values = array_values( $single_cat );

					$categories = array_merge( $categories, $single_values );
				}
			}

			if ( empty( $categories ) ) {
				return $cat;
			}

			foreach ( $categories as $category ) {
				if ( ! $category ) {
					continue;
				}
				$cat[ $category->term_id ] = $category->name;
			}

			return apply_filters( 'yith_woocompare_get_product_categories', $cat, $categories, $product_id );
		}

		/**
		 * Filter vars for compare table
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param mixed $products An array of products to compare.
		 * @return mixed
		 */
		public function exclude_product_from_list( $products ) {

			// Exit if less then 2 products.
			if ( count( $products ) < 2 ) {
				return $products;
			}

			$new_products = array();
			foreach ( $products as $product ) {
				$product_cat = array_keys( $this->get_product_categories( $product ) );

				// First check for excluded cat.
				if ( ! empty( $this->excluded_categories ) ) {
					$intersect = array_intersect( $product_cat, $this->excluded_categories );

					if ( ! $this->excluded_categories_inverse && ! empty( $intersect ) ) {
						continue;
					} elseif ( $this->excluded_categories_inverse && empty( $intersect ) ) {
						continue;
					}
				}

				// Now check for same cat.
				if ( $this->compare_by_cat ) {

					if ( ! empty( $this->current_cat ) ) {
						// Else intersect array to find same cat.
						$intersect = array_intersect( $product_cat, $this->current_cat );
						// Cat is different.
						if ( empty( $intersect ) ) {
							continue;
						}
					}
				}

				$new_products[] = $product;
			}

			return $new_products;
		}

		/**
		 * Filter view table link
		 *
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string         $link The link url.
		 * @param string|integer $product_id The product ID.
		 * @return string
		 */
		public function filter_table_url( $link, $product_id ) {

			if ( 'page' !== $this->page_or_popup ) {
				return $link;
			}

			$page = $this->get_compare_page_url();
			if ( ! $page ) {
				return $link;
			}

			return $page;
		}


		/**
		 * Get page url
		 *
		 * @since 2.0.6
		 * @author Francesco Licandro
		 * @return string
		 */
		public function get_compare_page_url() {
			return get_permalink( $this->page_id );
		}

		/**
		 * Filter compare link
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param boolean $default The default filter value.
		 * @param integer $product_id The product ID.
		 * @return bool
		 */
		public function exclude_compare_by_cat( $default, $product_id ) {

			if ( empty( $this->excluded_categories ) ) {
				return false;
			}

			$product_cat = array_keys( $this->get_product_categories( $product_id ) );
			$intersect   = array_intersect( $product_cat, $this->excluded_categories );

			if ( ! $this->excluded_categories_inverse && ! empty( $intersect ) ) {
				return true;
			} elseif ( $this->excluded_categories_inverse && empty( $intersect ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Shortcode to show the compare table
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $atts Shortcode attributes.
		 * @param mixed $content Shortcode content.
		 * @return string
		 */
		public function compare_table_sc( $atts, $content = null ) {

			$atts = shortcode_atts(
				array(
					'products' => '',
				),
				$atts
			);

			// Set products.
			$products = ( is_page( $this->page_id ) && isset( $_REQUEST['yith_compare_prod'] ) ) ? rawurldecode( sanitize_text_field( wp_unslash( $_REQUEST['yith_compare_prod'] ) ) ) : $atts['products']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$products = array_filter( explode( ',', $products ) );

			// Is share or a custom table -> fixed to true.
			if ( ! empty( $products ) ) {
				$products = array_unique( $products );
				// Then set correct products list and current cat.
				$this->set_variables_prod_cat( $products );
			}

			// Add scripts.
			if ( ! has_action( 'wp_footer', array( $this, 'add_scripts' ) ) ) {
				add_action( 'wp_footer', array( $this, 'add_scripts' ) );
			}

			// Get args.
			$args           = $this->vars( $products );
			$args['fixed']  = ! empty( $products );
			$args['iframe'] = $this->is_iframe;

			ob_start();
			wc_get_template( 'yith-compare-table.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH . '/' );

			$html = ob_get_clean();

			// Reset query.
			wp_reset_postdata();

			return $html;
		}

		/**
		 * Share compare table
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function social_share() {

			$social = get_option( 'yith-woocompare-share-socials', array() );

			if ( ! $this->share_enabled || empty( $social ) || empty( $this->products_list ) || isset( $_REQUEST['yith_compare_prod'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			// Check for page/popup.
			$to_show = 'yes' === get_option( 'yith-woocompare-share-in-page', 'yes' ) && 'yes' !== $this->is_iframe;
			if ( ! $to_show ) {
				$to_show = 'yes' === get_option( 'yith-woocompare-share-in-popup', 'yes' );
			}

			if ( ! $to_show ) {
				return;
			}

			$products = implode( ',', $this->products_list );
			// Get facebook image.
			$facebook_image = get_option( 'yith_woocompare_facebook_image', '' );

			$args = array(
				'socials'          => $social,
				'share_title'      => get_option( 'yith-woocompare-share-title', __( 'Share on:', 'yith-woocommerce-compare' ) ),
				'share_link_url'   => esc_url_raw( add_query_arg( 'yith_compare_prod', rawurlencode( $products ), $this->get_compare_page_url() ) ),
				'share_link_title' => rawurlencode( get_option( 'yith_woocompare_socials_title', __( 'My Compare', 'yith-woocommerce-compare' ) ) ),
				'share_summary'    => rawurlencode( str_replace( '%compare_url%', '', get_option( 'yith_woocompare_socials_text', '' ) ) ),
				'facebook_appid'   => get_option( 'yith_woocompare_facebook_appid', '' ),
				'facebook_image'   => $facebook_image,
			);

			wc_get_template( 'yith-compare-share.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add category filter for compare page
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 * @param array   $products The products on compare table.
		 * @param boolean $fixed True it is a fixed table, false otherwise.
		 */
		public function print_filter_by_cat( $products, $fixed ) {

			if ( ! $this->compare_by_cat || $fixed ) {
				return;
			}

			// Get all categories.
			$all_cat = $this->get_product_categories( $this->products_list );
			// Let's third part filter categories.
			$all_cat = apply_filters( 'yith_woocompare_product_categories_table_filter', $all_cat, $this->products_list );

			if ( empty( $all_cat ) ) {
				return;
			}

			// Set data for compare share.
			$data = isset( $_REQUEST['yith_compare_prod'] ) ? 'data-product_ids="' . sanitize_text_field( wp_unslash( $_REQUEST['yith_compare_prod'] ) ) . '"' : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			?>

			<div id="yith-woocompare-cat-nav">
				<h3><?php echo esc_html( get_option( 'yith-woocompare-categories-filter-title', esc_html__( 'Category Filter', 'yith-woocommerce-compare' ) ) ); ?></h3>

				<ul class="yith-woocompare-nav-list" data-iframe="<?php echo wp_kses_post( $this->is_iframe ); ?>" <?php echo wp_kses_post( $data ); ?>>

					<?php
					$current_categories = array_map( 'absint', (array) $this->current_cat );
					foreach ( $all_cat as $cat_id => $cat_name ) :
						$active = in_array( $cat_id, $current_categories, true );
						?>
						<li>
							<?php if ( $active ) : ?>
								<span class="active"><?php echo esc_html( $cat_name ); ?></span>
							<?php else : ?>
								<a href="<?php echo esc_url_raw( add_query_arg( 'yith_compare_cat', $cat_id ) ); ?>" data-cat_id="<?php echo esc_attr( $cat_id ); ?>"><?php echo esc_html( $cat_name ); ?></a>
							<?php endif; ?>
						</li>

					<?php endforeach; ?>

				</ul>
			</div>

			<?php
		}

		/**
		 * Ajax compare table filter for category
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 */
		public function yith_woocompare_filter_by_cat_ajax() {

			if ( ! isset( $_REQUEST['yith_compare_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				die();
			}

			if ( 'yes' === $this->is_iframe ) {
				header( 'Content-Type: text/html; charset=utf-8' );
				$this->compare_table_html();
			} else {
				echo do_shortcode( '[yith_woocompare_table]' );
			}

			die();
		}

		/**
		 * Add related product in compare table
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 * @param array   $products The products on compare table.
		 * @param boolean $fixed True it is a fixed table, false otherwise.
		 */
		public function related_product( $products, $fixed ) {

			if ( ! $this->related_enabled || empty( $this->products_list ) || $fixed || apply_filters( 'yith_woocompare_hide_related_products', false ) ) {
				return;
			}
			// Check for page/popup.
			if ( ( 'yes' === $this->is_iframe && 'yes' === get_option( 'yith-woocompare-related-in-popup' ) ) || ( 'no' === $this->is_iframe && 'yes' === get_option( 'yith-woocompare-related-in-page', 'yes' ) ) ) {
				$to_show = true;
			} else {
				$to_show = false;
			}

			if ( ! $to_show ) {
				return;
			}

			// Filter product.
			$related = array();

			foreach ( $this->products_list as $product_id ) {
				if ( function_exists( 'wpml_object_id_filter' ) ) {
					$product_id = wpml_object_id_filter( $product_id, 'product', false );
					if ( ! $product_id ) {
						continue;
					}
				}

				$product = wc_get_product( $product_id );
				if ( ! $product ) {
					continue;
				}

				$product_id      = $product->get_id();
				$current_related = function_exists( 'wc_get_related_products' ) ? wc_get_related_products( $product_id ) : $product->get_related();
				$related         = array_merge( $current_related, $related );
			}
			// Remove duplicate.
			$related = array_unique( $related );
			// Remove products already in compare.
			$related = array_diff( $related, $this->products_list );
			// Filter related by cat.
			$related = $this->exclude_product_from_list( $related );

			// Exit if related is empty.
			if ( empty( $related ) ) {
				return;
			}

			$args = array(
				'iframe'        => $this->is_iframe,
				'products'      => $related,
				'related_title' => get_option( 'yith-woocompare-related-title', __( 'Related Products', 'yith-woocommerce-compare' ) ),
			);

			wc_get_template( 'yith-compare-related.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH . '/' );
		}

		/**
		 * Filter remove action for compare page
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 */
		public function remove_product_ajax_premium() {
			if ( 'yes' !== $this->is_iframe && ! ( isset( $_REQUEST['responseType'] ) && 'product_list' === sanitize_text_field( wp_unslash( $_REQUEST['responseType'] ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				echo do_shortcode( '[yith_woocompare_table]' );
				die();
			}
		}

		/**
		 * Added related product to compare
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro
		 */
		public function added_related_to_compare() {

			if ( isset( $_REQUEST['is_related'] ) && 'false' !== sanitize_text_field( wp_unslash( $_REQUEST['is_related'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				if ( 'yes' === $this->is_iframe ) {
					header( 'Content-Type: text/html; charset=utf-8' );
					$this->compare_table_html();
				} else {
					echo do_shortcode( '[yith_woocompare_table]' );
				}

				die();
			}
		}

		/**
		 * Add different class to let's compare products attributes
		 *
		 * @access public
		 * @since 2.0.3
		 * @author Francesco Licandro
		 * @param array $products_list The products list in compare.
		 * @return array
		 */
		public function add_different_class( $products_list ) {

			if ( 'yes' !== get_option( 'yith_woocompare_highlights_different', 'no' ) || empty( $products_list ) || count( $products_list ) < 2 ) {
				return array();
			}

			$prev_value      = array();
			$different_value = array();

			foreach ( $products_list as $key => $product ) {

				$value = $product->fields;

				// Remove unused fields.
				unset(
					$value['description'],
					$value['stock']
				);

				$new_value = array();
				foreach ( $value as $name => $val ) {

					if ( 'price' === $name ) {
						$new_value[ $name ] = $val;
						continue;
					}

					$val = strtolower( $val );
					$val = explode( ',', $val );
					foreach ( $val as $index => $elem ) {
						$val[ $index ] = trim( $elem );
					}
					natsort( $val );
					$val = implode( ',', $val );

					$new_value[ $name ] = $val;
				}

				// If prev value is not empty compare with current and save difference.
				if ( ! empty( $prev_value ) ) {
					$diff            = array_diff_assoc( $prev_value, $new_value );
					$different_value = array_merge( $different_value, $diff );
				}

				// Save current value.
				$prev_value = $new_value;
			}

			// If no difference return list.
			if ( empty( $different_value ) ) {
				return array();
			}

			return array_keys( $different_value );
		}

		/**
		 * Add style for YITH Themes Compatibility
		 *
		 * @since 2.0.4
		 * @author Francesco Licandro
		 */
		public function add_styles_themes_and_custom() {

			echo '<style>' . yith_woocompare_user_style() . '</style>'; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

			// Get theme and woocommerce assets.
			$assets_yit = class_exists( 'YIT_Asset' ) ? YIT_Asset()->get() : array();
			$assets_yit = isset( $assets_yit['style'] ) ? $assets_yit['style'] : array();
			$assets_wc  = class_exists( 'WC_Frontend_Scripts' ) ? WC_Frontend_Scripts::get_styles() : array();
			if ( ! is_array( $assets_wc ) ) {
				$assets_wc = array();
			}
			$assets     = array_merge( $assets_yit, $assets_wc );
			$to_include = apply_filters(
				'yith_woocompare_popup_assets',
				array(
					'google-fonts',
					'font-awesome',
					'theme-stylesheet',
					'woocommerce-general',
					'yit-layout',
					'cache-dynamics',
					'custom',
				)
			);

			if ( function_exists( 'yith_proteo_scripts' ) ) {
				yith_proteo_scripts();
				// Add also inline customize style if any.
				function_exists( 'yith_proteo_inline_style' ) && yith_proteo_inline_style();
			}

			// First if is child include parent css.
			if ( is_child_theme() && function_exists( 'yit_enqueue_parent_theme_style' ) ) {
				yit_enqueue_parent_theme_style();
			}

			foreach ( $to_include as $css ) {
				if ( ! isset( $assets[ $css ]['src'] ) ) {
					continue;
				}
				echo '<link rel="stylesheet" href=' . esc_attr( $assets[ $css ]['src'] ) . ' type="text/css" media="all">'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
			}

		}

		/**
		 * WooCommerce Product Bundle Compatibility
		 *
		 * @since 2.0.6
		 * @author Francesco Licandro
		 */
		public function wc_bundle_compatibility() {
			// Remove description from bundled items.
			remove_action( 'wc_bundles_bundled_item_details', 'wc_bundles_bundled_item_description', 20 );
			remove_action( 'wc_bundles_bundled_item_details', 'wc_bundles_bundled_item_product_details', 25 );
			add_action( 'wc_bundles_bundled_item_details', array( $this, 'print_bundled_item_price' ), 25, 2 );
		}

		/**
		 * Print bundled single item price
		 *
		 * @since 2.0.6
		 * @author Francesco Licandro
		 * @param object $bundled_item The bundles items.
		 * @param object $product The product object.
		 */
		public function print_bundled_item_price( $bundled_item, $product ) {

			if ( ! function_exists( 'WC_PB' ) ) {
				return;
			}

			wc_get_template(
				'single-product/bundled-item-price.php',
				array(
					'bundled_item' => $bundled_item,
				),
				false,
				WC_PB()->woo_bundles_plugin_path() . '/templates/'
			);
		}

		/**
		 * Get excluded categories from plugin option
		 *
		 * @since 2.1.0
		 * @author Francesco Licandro
		 * @return array
		 */
		public function get_excluded_categories() {
			$excluded_cat = get_option( 'yith_woocompare_excluded_category', array() );
			if ( ! is_array( $excluded_cat ) ) {
				$excluded_cat = explode( ',', $excluded_cat );
			}

			// Remove empty values and return.
			return array_filter( $excluded_cat );
		}

		/**
		 * Premium add product json args
		 *
		 * @since 2.1.0
		 * @author Francesco Licandro
		 * @param array $json Json arguments.
		 * @return array
		 */
		public function premium_add_product_action_json( $json ) {
			$json['only_one'] = ( 'yes' === get_option( 'yith_woocompare_open_after_second', 'no' ) && count( $this->products_list ) <= 1 );

			return $json;
		}

		/**
		 * Add clear all button on compare table
		 *
		 * @since 2.1.0
		 * @author Francesco Licandro
		 * @param array   $products The products on compare table.
		 * @param boolean $fixed True it is a fixed table, false otherwise.
		 */
		public function add_clear_all_button( $products, $fixed ) {

			if ( 'yes' !== get_option( 'yith_woocompare_show_clear_all_table', 'no' ) || $fixed ) {
				return;
			}

			$label = get_option( 'yith_woocompare_label_clear_all_table', __( 'Clear all', 'yith-woocommerce-compare' ) );

			$html  = '<div class="compare-table-clear">';
			$html .= '<a href="' . $this->remove_product_url( 'all' ) . '" data-product_id="all" class="button yith_woocompare_clear" rel="nofollow">' . esc_html( $label ) . '</a>';
			$html .= '</div>';

			echo apply_filters( 'yith_woocompare_table_clear_all', $html ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Dequeue footer scripts from compare popup
		 *
		 * @since 2.2.0
		 * @author Francesco Licandro
		 * @access public
		 */
		public function dequeue_footer_scripts() {
			if ( wp_script_is( 'responsive-theme', 'enqueued' ) ) {
				wp_dequeue_script( 'responsive-theme' );
			}
			if ( wp_script_is( 'woodmart-libraries-base', 'enqueued' ) ) {
				wp_dequeue_script( 'woodmart-libraries-base' );
			}
		}

		/**
		 * Counter compare items shortcode
		 *
		 * @since 2.3.2
		 * @author Francesco Licandro
		 * @param array $atts Array of shortcode attributes.
		 * @return string
		 */
		public function counter_compare_shortcode( $atts ) {
			$args = shortcode_atts(
				array(
					'type'      => 'text',
					'show_icon' => 'yes',
					'text'      => '',
					'icon'      => '',
				),
				$atts
			);

			$c = count( $this->products_list );
			// Builds template arguments.
			$args['items']       = $this->products_list;
			$args['items_count'] = $c;
			if ( ! $args['icon'] ) {
				$args['icon'] = YITH_WOOCOMPARE_ASSETS_URL . '/images/compare-icon.png';
			}
			if ( ! $args['text'] ) {
				$args['text'] = _n( '{{count}} product in compare', '{{count}} products in compare', $c, 'yith-woocommerce-compare' );
			}
			// Add count in text.
			$args['text_o'] = $args['text'];
			$args['text']   = str_replace( '{{count}}', $c, $args['text'] );

			$args = apply_filters( 'yith_woocompare_shortcode_counter_args', $args );

			ob_start();
			wc_get_template( 'yith-compare-counter.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH . '/' );

			return ob_get_clean();
		}

		/**
		 * Add limit error messages in compare table if any
		 *
		 * @since 2.3.2
		 * @author Francesco Licandro
		 */
		public function add_error_limit_message() {

			if ( ! $this->limit_reached ) {
				return;
			}

			$message = apply_filters( 'yith_woocompare_limit_reached_message', __( 'You have reached the maximum number of products for compare table.', 'yith-woocommerce-compare' ) );
			echo '<div class="yith-woocompare-error"><p>' . wp_kses_post( $message ) . '</p></div>';
		}


		/**
		 * Compatibility with YITH WooCommerce Colors and Labels Variations (show fields for single variations)
		 *
		 * @param mixed                $value The variation field value.
		 * @param WC_Product_Variation $product The variation object.
		 * @param string               $field The field.
		 * @return string
		 */
		public function yith_woocompare_set_single_variation_field_value( $value, $product, $field ) {

			if ( $product instanceof WC_Product_Variation ) {

				switch ( $field ) {
					case 'price':
						$value = $product->get_price_html();
						break;

					case 'description':
						$value = $product->get_description();
						break;

					default:
						break;
				}
			}

			return $value;
		}
	}
}
