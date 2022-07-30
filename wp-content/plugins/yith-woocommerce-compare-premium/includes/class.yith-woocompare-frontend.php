<?php
/**
 * Frontend class
 *
 * @author YITH
 * @package YITH Woocommerce Compare
 * @version 1.1.4
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_Woocompare_Frontend' ) ) {
	/**
	 * YITH Custom Login Frontend
	 *
	 * @since 1.0.0
	 */
	class YITH_Woocompare_Frontend {
		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $version = YITH_WOOCOMPARE_VERSION;

		/**
		 * The list of products inside the comparison table
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $products_list = array();

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $template_file = 'compare.php';

		/**
		 * Stylesheet file
		 *
		 * @since 2.1.0
		 * @var string
		 */
		public $stylesheet_file = 'compare.css';

		/**
		 * The name of cookie name
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $cookie_name = 'yith_woocompare_list';

		/**
		 * The action used to view the table comparison
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $action_view = 'yith-woocompare-view-table';

		/**
		 * The action used to add the product to compare list
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $action_add = 'yith-woocompare-add-product';

		/**
		 * The action used to add the product to compare list
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $action_remove = 'yith-woocompare-remove-product';

		/**
		 * The action used to reload the compare list widget
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $action_reload = 'yith-woocompare-reload-product';

		/**
		 * The standard fields
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $default_fields = array();

		/**
		 * Use WC Ajax or WP Admin Ajax
		 *
		 * @since 1.0.0
		 * @var boolean
		 */
		public $use_wc_ajax = true;

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 * @return YITH_Woocompare_Frontend
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'init_variables' ), 1 );
			add_action( 'init', array( $this, 'populate_products_list' ), 10 );

			$this->use_wc_ajax = apply_filters( 'yith_woocompare_use_wc_ajax', $this->use_wc_ajax );

			// Add link or button in the products list.
			if ( 'yes' === get_option( 'yith_woocompare_compare_button_in_product_page', 'yes' ) ) {
				add_action( 'woocommerce_single_product_summary', array( $this, 'add_compare_link' ), 35 );
			}
			if ( 'yes' === get_option( 'yith_woocompare_compare_button_in_products_list', 'no' ) ) {
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_compare_link' ), 20 );
			}
			add_action( 'init', array( $this, 'add_product_to_compare_action' ), 15 );
			add_action( 'init', array( $this, 'remove_product_from_compare_action' ), 15 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'template_redirect', array( $this, 'compare_table_html' ) );

			// Add the shortcode.
			add_shortcode( 'yith_compare_button', array( $this, 'compare_button_sc' ) );

			if ( $this->use_wc_ajax ) {
				add_action( 'wc_ajax_' . $this->action_add, array( $this, 'add_product_to_compare_ajax' ) );
				add_action( 'wc_ajax_' . $this->action_remove, array( $this, 'remove_product_from_compare_ajax' ) );
				add_action( 'wc_ajax_' . $this->action_reload, array( $this, 'reload_widget_list_ajax' ) );
			} else {
				add_action( 'wp_ajax_' . $this->action_add, array( $this, 'add_product_to_compare_ajax' ) );
				add_action( 'wp_ajax_' . $this->action_remove, array( $this, 'remove_product_from_compare_ajax' ) );
				add_action( 'wp_ajax_' . $this->action_reload, array( $this, 'reload_widget_list_ajax' ) );
			}
			// AJAX no priv.
			add_action( 'wp_ajax_nopriv_' . $this->action_add, array( $this, 'add_product_to_compare_ajax' ) );
			add_action( 'wp_ajax_nopriv_' . $this->action_remove, array( $this, 'remove_product_from_compare_ajax' ) );
			add_action( 'wp_ajax_nopriv_' . $this->action_reload, array( $this, 'reload_widget_list_ajax' ) );

			return $this;
		}

		/**
		 * Init class variables
		 *
		 * @since 2.3.4
		 * @author Francesco Licandro
		 */
		public function init_variables() {
			global $sitepress;

			$lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( defined( 'ICL_LANGUAGE_CODE' ) && $lang && isset( $sitepress ) ) {
				$sitepress->switch_lang( $lang, true );
			}

			// Populate default fields for the comparison table.
			$this->default_fields = YITH_Woocompare_Helper::standard_fields();
		}

		/**
		 * Populate the compare product list
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function populate_products_list() {

			global $sitepress;

			// WPML Support.
			$lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			// Get cookie val.
			$the_list = isset( $_COOKIE[ $this->get_cookie_name() ] ) ? json_decode( wp_unslash( $_COOKIE[ $this->get_cookie_name() ] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			// Switch lang for WPML.
			if ( defined( 'ICL_LANGUAGE_CODE' ) && $lang && isset( $sitepress ) ) {
				$sitepress->switch_lang( $lang, true );
			}

			foreach ( $the_list as $product_id ) {
				if ( function_exists( 'wpml_object_id_filter' ) ) {
					$product_id_translated = wpml_object_id_filter( $product_id, 'product', false );
					// Get all product of current lang.
					if ( $product_id_translated !== $product_id ) {
						$product_id = $product_id_translated;
					}
				}

				if ( ! $product_id ) {
					continue;
				}

				// Check for deleted|private products.
				$product = wc_get_product( $product_id );
				if ( ! $product || 'publish' !== $product->get_status() ) {
					continue;
				}

				$this->products_list[] = absint( $product_id );
			}

			do_action( 'yith_woocompare_after_populate_product_list', $this->products_list );
		}

		/**
		 * Enqueue the scripts and styles in the page
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts() {

			$min = ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '';
			wp_register_script( 'yith-woocompare-main', YITH_WOOCOMPARE_ASSETS_URL . '/js/woocompare' . $min . '.js', array( 'jquery' ), YITH_WOOCOMPARE_VERSION, true );

			// Enqueue and add localize.
			wp_enqueue_script( 'yith-woocompare-main' );
			// Localize script args.
			$args = apply_filters(
				'yith_woocompare_main_script_localize_array',
				array(
					'ajaxurl'                         => $this->use_wc_ajax ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : admin_url( 'admin-ajax.php', 'relative' ),
					'actionadd'                       => $this->action_add,
					'actionremove'                    => $this->action_remove,
					'actionview'                      => $this->action_view,
					'actionreload'                    => $this->action_reload,
					'added_label'                     => apply_filters( 'yith_woocompare_compare_added_label', __( 'Added', 'yith-woocommerce-compare' ) ),
					'table_title'                     => apply_filters( 'yith_woocompare_compare_table_title', __( 'Product Comparison', 'yith-woocommerce-compare' ) ),
					'auto_open'                       => get_option( 'yith_woocompare_auto_open', 'yes' ),
					'loader'                          => YITH_WOOCOMPARE_ASSETS_URL . '/images/loader.gif',
					'button_text'                     => get_option( 'yith_woocompare_button_text', __( 'Compare', 'yith-woocommerce-compare' ) ),
					'cookie_name'                     => $this->get_cookie_name(),
					'close_label'                     => _x( 'Close', 'Label for popup close icon', 'yith-woocommerce-compare' ),
					'selector_for_custom_label_compare_button' => apply_filters( 'yith_woocompare_selector_for_custom_label_compare_button', '.product_title' ),
					'custom_label_for_compare_button' => apply_filters( 'yith_woocompare_custom_label_for_compare_button', false ),
					'force_showing_popup'             => apply_filters( 'yith_woocompare_force_showing_popup', false ),
				)
			);
			wp_localize_script( 'yith-woocompare-main', 'yith_woocompare', $args );

			// Colorbox.
			wp_enqueue_style( 'jquery-colorbox', YITH_WOOCOMPARE_ASSETS_URL . '/css/colorbox.css', array(), '1.6.1' );
			wp_enqueue_script( 'jquery-colorbox', YITH_WOOCOMPARE_ASSETS_URL . '/js/jquery.colorbox.min.js', array( 'jquery' ), '1.6.1', true );
			// Widget.
			wp_enqueue_style( 'yith-woocompare-widget', YITH_WOOCOMPARE_ASSETS_URL . '/css/widget.css', array(), YITH_WOOCOMPARE_VERSION );
		}

		/**
		 * The fields to show in the table
		 *
		 * @since 1.0.0
		 * @param array $products An array of products.
		 * @return array
		 */
		public function fields( $products = array() ) {

			$fields = get_option( 'yith_woocompare_fields', array() );

			foreach ( $fields as $field => $show ) {
				if ( $show ) {
					if ( isset( $this->default_fields[ $field ] ) ) {
						$fields[ $field ] = $this->default_fields[ $field ];
					} else {
						if ( taxonomy_exists( $field ) ) {
							$fields[ $field ] = wc_attribute_label( $field );
						}
					}
				} else {
					unset( $fields[ $field ] );
				}
			}

			return apply_filters( 'yith_woocompare_filter_table_fields', $fields, $products );
		}

		/**
		 * Render the maintenance page
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function compare_table_html() {

			if ( ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ( ! isset( $_REQUEST['action'] ) || sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) !== $this->action_view ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			// Set no cache headers.
			nocache_headers();
			// Check if is add to cart.
			if ( isset( $_REQUEST['add-to-cart'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$product_id = absint( $_REQUEST['add-to-cart'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				wp_safe_redirect( get_permalink( $product_id ) );
				exit;
			}

			global $woocommerce, $sitepress;

			// WPML Suppot: Localize Ajax Call.
			$lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( defined( 'ICL_LANGUAGE_CODE' ) && $lang && isset( $sitepress ) ) {
				$sitepress->switch_lang( $lang, true );
			}

			$args           = $this->vars();
			$args['fixed']  = false;
			$args['iframe'] = 'yes';

			// Remove all styles from compare template.
			add_action( 'wp_print_styles', array( $this, 'remove_all_styles' ), 100 );

			// Remove admin bar.
			remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
			remove_action( 'wp_head', '_admin_bar_bump_cb' );

			// Remove filters before render compare popup.
			add_action( 'wp_enqueue_scripts', array( $this, 'actions_before_load_popup' ), 99 );

			$plugin_path = YITH_WOOCOMPARE_TEMPLATE_PATH . '/' . $this->template_file;

			if ( defined( 'WC_TEMPLATE_PATH' ) ) {

				$template_path = get_template_directory() . '/' . WC_TEMPLATE_PATH . $this->template_file;
				$child_path    = get_stylesheet_directory() . '/' . WC_TEMPLATE_PATH . $this->template_file;
			} else {
				$template_path = get_template_directory() . '/' . $woocommerce->template_url . $this->template_file;
				$child_path    = get_stylesheet_directory() . '/' . $woocommerce->template_url . $this->template_file;
			}

			foreach ( array( 'child_path', 'template_path', 'plugin_path' ) as $var ) {
				if ( file_exists( ${$var} ) ) {
					include ${$var};
					exit();
				}
			}
		}

		/**
		 * Return the array with all products and all attributes values
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param array $products An array of products to compare.
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

				// Custom attributes.
				foreach ( $fields as $field => $name ) {

					switch ( $field ) {
						case 'title':
							$product->fields[ $field ] = $product->get_title();
							break;
						case 'price':
							$product->fields[ $field ] = $product->get_price_html();
							break;
						case 'image':
							$product->fields[ $field ] = absint( $product->get_image_id() );
							break;
						case 'description':
							$description               = apply_filters( 'woocommerce_short_description', $product->get_short_description() );
							$product->fields[ $field ] = apply_filters( 'yith_woocompare_products_description', $description );
							break;
						case 'stock':
							$availability = $product->get_availability();
							if ( empty( $availability['availability'] ) ) {
								$availability['availability'] = __( 'In stock', 'yith-woocommerce-compare' );
							}
							$product->fields[ $field ] = sprintf( '<span>%s</span>', esc_html( $availability['availability'] ) );
							break;
						case 'weight':
							$weight = $product->get_weight();
							$weight = $weight ? wc_format_localized_decimal( $weight ) . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ) : '-';

							$product->fields[ $field ] = sprintf( '<span>%s</span>', esc_html( $weight ) );
							break;
						case 'dimensions':
							$dimensions = wc_format_dimensions( $product->get_dimensions( false ) );
							if ( ! $dimensions ) {
								$dimensions = '-';
							}

							$product->fields[ $field ] = sprintf( '<span>%s</span>', esc_html( $dimensions ) );
							break;
						default:
							if ( taxonomy_exists( $field ) ) {
								$product->fields[ $field ] = array();
								$terms                     = get_the_terms( $product_id, $field );

								if ( ! empty( $terms ) ) {
									foreach ( $terms as $term ) {
										$term                        = sanitize_term( $term, $field );
										$product->fields[ $field ][] = $term->name;
									}
								}
								$product->fields[ $field ] = implode( ', ', $product->fields[ $field ] );
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
		 * The URL of product comparison table
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param integer $product_id The product ID.
		 * @return string The url to add the product in the comparison table
		 */
		public function view_table_url( $product_id = false ) {
			$url_args = array(
				'action' => $this->action_view,
				'iframe' => 'yes',
			);

			$lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : false;
			if ( $lang ) {
				$url_args['lang'] = $lang;
			}

			return apply_filters( 'yith_woocompare_view_table_url', esc_url_raw( add_query_arg( $url_args, site_url() ) ), $product_id );
		}

		/**
		 * The URL to add the product into the comparison table
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param integer $product_id ID of the product to add.
		 * @return string The url to add the product in the comparison table
		 */
		public function add_product_url( $product_id ) {
			$url_args = array(
				'action' => $this->action_add,
				'id'     => $product_id,
			);

			$lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : false;
			if ( $lang ) {
				$url_args['lang'] = isset( $_GET['lang'] ) ? $_GET['lang'] : $lang;
			}

			return apply_filters( 'yith_woocompare_add_product_url', esc_url_raw( add_query_arg( $url_args, site_url() ) ), $this->action_add, $url_args );
		}

		/**
		 * The URL to remove the product into the comparison table
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param integer $product_id The ID of the product to remove.
		 * @return string The url to remove the product in the comparison table
		 */
		public function remove_product_url( $product_id ) {
			$url_args = array(
				'action' => $this->action_remove,
				'id'     => $product_id,
			);

			$lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : false;
			if ( $lang ) {

				$url_args['lang'] = isset( $_GET['lang'] ) ? $_GET['lang'] : $lang;
			}

			return apply_filters( 'yith_woocompare_remove_product_url', esc_url_raw( add_query_arg( $url_args, site_url() ) ), $this->action_remove );
		}

		/**
		 *  Add the link to compare
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param mixed $product_id The ID of the product to compare.
		 * @param array $args An array of link arguments.
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
				do_action( 'wpml_register_single_string', 'Plugins', 'plugin_yit_compare_button_text', $button_text );
				$button_text = apply_filters( 'wpml_translate_single_string', $button_text, 'Plugins', 'plugin_yit_compare_button_text' );
			}

			printf( '<a href="%s" class="%s" data-product_id="%d" rel="nofollow">%s</a>', $this->add_product_url( $product_id ), 'compare' . ( 'button' === $is_button ? ' button' : '' ), esc_attr( $product_id ), esc_html( $button_text ) );
		}

		/**
		 * Return the url of stylesheet position
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function stylesheet_url() {
			global $woocommerce;

			$filename = $this->stylesheet_file;

			$plugin_path = array(
				'path' => YITH_WOOCOMPARE_DIR . '/assets/css/style.css',
				'url'  => YITH_WOOCOMPARE_ASSETS_URL . '/css/style.css',
			);

			if ( defined( 'WC_TEMPLATE_PATH' ) ) {
				$template_path = array(
					'path' => get_template_directory() . '/' . WC_TEMPLATE_PATH . $filename,
					'url'  => get_template_directory_uri() . '/' . WC_TEMPLATE_PATH . $filename,
				);
				$child_path    = array(
					'path' => get_stylesheet_directory() . '/' . WC_TEMPLATE_PATH . $filename,
					'url'  => get_stylesheet_directory_uri() . '/' . WC_TEMPLATE_PATH . $filename,
				);
			} else {
				$template_path = array(
					'path' => get_template_directory() . '/' . $woocommerce->template_url . $filename,
					'url'  => get_template_directory_uri() . '/' . $woocommerce->template_url . $filename,
				);
				$child_path    = array(
					'path' => get_stylesheet_directory() . '/' . $woocommerce->template_url . $filename,
					'url'  => get_stylesheet_directory_uri() . '/' . $woocommerce->template_url . $filename,
				);
			}

			foreach ( array( 'child_path', 'template_path', 'plugin_path' ) as $var ) {
				if ( file_exists( ${$var}['path'] ) ) {
					return ${$var}['url'];
				}
			}
		}


		/**
		 * Generate template vars
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @access protected
		 * @return array
		 */
		protected function vars() {
			$vars = array(
				'products'           => $this->get_products_list(),
				'fields'             => $this->fields(),
				'repeat_price'       => get_option( 'yith_woocompare_price_end', 'no' ),
				'repeat_add_to_cart' => get_option( 'yith_woocompare_add_to_cart_end', 'no' ),
			);

			return $vars;
		}

		/**
		 * The action called by the query string
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function add_product_to_compare_action() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX || ! isset( $_REQUEST['id'] ) || ! isset( $_REQUEST['action'] ) || sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) !== $this->action_add ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$product_id = absint( $_REQUEST['id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product    = $this->wc_get_product( $product_id );

			// Don't add the product if doesn't exist.
			if ( isset( $product->id ) && ! in_array( $product_id, $this->products_list, true ) ) {
				$this->add_product_to_compare( $product_id );
			}

			wp_safe_redirect( esc_url( remove_query_arg( array( 'id', 'action' ) ) ) );
			exit();
		}

		/**
		 * The action called by AJAX
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function add_product_to_compare_ajax() {

			if ( ! isset( $_REQUEST['id'] ) || ! isset( $_REQUEST['action'] ) || sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) !== $this->action_add ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				die();
			}

			$product_id = absint( $_REQUEST['id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product    = wc_get_product( $product_id );
			$added      = false;

			// Don't add the product if doesn't exist.
			if ( $product && ! in_array( $product_id, $this->products_list, true ) ) {
				$added = $this->add_product_to_compare( $product_id );
			}

			do_action( 'yith_woocompare_add_product_action_ajax' );

			$lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$json = apply_filters(
				'yith_woocompare_add_product_action_json',
				array(
					'table_url'    => $this->view_table_url( $product_id ),
					'widget_table' => $this->get_widget_template( $lang, true ),
					'added'        => $added,
				)
			);

			echo wp_json_encode( $json );
			die();
		}

		/**
		 * Add a product in the products comparison table
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param int $product_id product ID to add in the comparison table.
		 * @return boolean
		 */
		public function add_product_to_compare( $product_id ) {
			$this->products_list[] = absint( $product_id );
			setcookie( $this->get_cookie_name(), wp_json_encode( $this->products_list ), 0, COOKIEPATH, COOKIE_DOMAIN, false, false );

			do_action( 'yith_woocompare_after_add_product', $product_id );

			return true;
		}

		/**
		 * Get cookie name
		 *
		 * @since 2.3.2
		 * @author Francesco Licandro
		 * @return string
		 */
		public function get_cookie_name() {
			// Set cookie name.
			$suffix = '';
			if ( is_multisite() ) {
				$suffix = '_' . get_current_blog_id();
			} elseif ( '/' !== COOKIEPATH ) {
				$suffix = '_' . sanitize_title( COOKIEPATH );
			}

			return $this->cookie_name . $suffix;
		}

		/**
		 * The action called by the query string
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function remove_product_from_compare_action() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX || ! isset( $_REQUEST['action'] ) || ! isset( $_REQUEST['id'] ) || sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) !== $this->action_remove ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$this->remove_product_from_compare( sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			// Redirect.
			$redirect = esc_url( remove_query_arg( array( 'id', 'action' ) ) );

			if ( isset( $_REQUEST['redirect'] ) && 'view' === sanitize_text_field( wp_unslash( $_REQUEST['redirect'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$redirect = esc_url( remove_query_arg( 'redirect', add_query_arg( 'action', $this->action_view, $redirect ) ) );
			}

			wp_safe_redirect( $redirect );
			exit();
		}

		/**
		 * The action called by AJAX
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function remove_product_from_compare_ajax() {

			if ( ! isset( $_REQUEST['id'] ) || ! isset( $_REQUEST['action'] ) || sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) !== $this->action_remove ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				die();
			}

			$this->remove_product_from_compare( sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			do_action( 'yith_woocompare_remove_product_action_ajax' );

			header( 'Content-Type: text/html; charset=utf-8' );

			if ( isset( $_REQUEST['responseType'] ) && 'product_list' === sanitize_text_field( wp_unslash( $_REQUEST['responseType'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->get_widget_template( $lang );
			} else {
				$this->compare_table_html();
			}

			die();
		}

		/**
		 * Return the list of widget table, used in AJAX
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function reload_widget_list_ajax() {

			if ( ! isset( $_REQUEST['action'] ) || sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) !== $this->action_reload ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				die();
			}

			$lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$this->get_widget_template( $lang );
			die();
		}

		/**
		 * Get the widget template
		 *
		 * @since 2.5.0
		 * @author Francesco Licandro
		 * @param mixed   $lang The lang code, false for default.
		 * @param boolean $return True to return, false otherwise.
		 * @param array   $args An additional arguments array.
		 */
		public function get_widget_template( $lang = false, $return = false, $args = array() ) {

			global $sitepress;

			if ( defined( 'ICL_LANGUAGE_CODE' ) && $lang && isset( $sitepress ) ) {
				$sitepress->switch_lang( $lang, true );
			}

			$args = array_merge(
				$args,
				array(
					'products_list' => $this->products_list,
					'remove_url'    => $this->remove_product_url( 'all' ),
					'view_url'      => $this->view_table_url(),
					'lang'          => $lang,
				)
			);
			// Let's filter template arguments.
			$args = apply_filters( 'yith_woocompare_widget_template_args', $args );

			ob_start();
			wc_get_template( 'yith-compare-widget.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH . '/' );
			$template = ob_get_clean();

			if ( $return ) {
				return $template;
			}

			echo $template; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Remove a product from the comparison table
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param integer $product_id The product ID to remove from the comparison table.
		 */
		public function remove_product_from_compare( $product_id ) {

			if ( 'all' === $product_id ) {
				$this->products_list = array();
			} else {
				foreach ( $this->products_list as $k => $id ) {
					if ( absint( $product_id ) === absint( $id ) ) {
						unset( $this->products_list[ $k ] );
					}
				}
			}

			setcookie( $this->get_cookie_name(), wp_json_encode( array_values( $this->products_list ) ), 0, COOKIEPATH, COOKIE_DOMAIN, false, false );
			do_action( 'yith_woocompare_after_remove_product', $product_id );
		}

		/**
		 * Remove all styles from the compare template
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function remove_all_styles() {
			global $wp_styles;
			foreach ( $wp_styles->queue as $key => $style ) {
				if ( 'yith_wcbm_badge_style' !== $style ) {
					unset( $wp_styles->queue[ $key ] );
				}
			}
		}

		/**
		 * Show the html for the shortcode
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 * @param array $atts Attributes shortcode.
		 * @param mixed $content The shortcode content.
		 * @return string
		 */
		public function compare_button_sc( $atts, $content = null ) {
			$atts = shortcode_atts(
				array(
					'product'   => false,
					'type'      => 'default',
					'container' => 'yes',
				),
				$atts
			);

			$product_id = 0;

			/**
			 * Retrieve the product ID in these steps:
			 * - If "product" attribute is not set, get the product ID of current product loop
			 * - If "product" contains ID, post slug or post title
			 */
			if ( ! $atts['product'] ) {
				global $product;
				$product_id = ! is_null( $product ) ? $product->get_id() : 0;
			} else {
				global $wpdb;
				$product = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d OR post_name = %s OR post_title = %s LIMIT 1", $atts['product'], $atts['product'], $atts['product'] ) ); // phpcs:ignore
				if ( ! empty( $product ) ) {
					$product_id = $product->ID;
				}
			}

			// Make sure to get always the product id of current language.
			if ( function_exists( 'wpml_object_id_filter' ) ) {
				$product_id = wpml_object_id_filter( $product_id, 'product', false );
			}

			// If is Elementor editor and product is empty, get a dummy value.
			if ( empty( $product_id ) && YITH_Woocompare_Helper::is_elementor_editor() ) {
				$products   = wc_get_products(
					array(
						'limit'  => 1,
						'return' => 'ids',
					)
				);
				$product_id = ! empty( $products ) ? array_shift( $products ) : 0;
			}

			// If product ID is 0, maybe the product doesn't exists or is wrong.. in this case, doesn't show the button.
			if ( empty( $product_id ) ) {
				return '';
			}

			ob_start();
			if ( 'yes' === $atts['container'] ) {
				echo '<div class="woocommerce product compare-button">';
			}
			$this->add_compare_link(
				$product_id,
				array(
					'button_or_link' => ( 'default' === $atts['type'] ? false : $atts['type'] ),
					'button_text'    => empty( $content ) ? 'default' : $content,
				)
			);
			if ( 'yes' === $atts['container'] ) {
				echo '</div>';
			}

			return ob_get_clean();
		}

		/**
		 * Alias for wc_get_product
		 *
		 * @param integer $product_id The product ID.
		 * @return mixed
		 * @depreacted
		 */
		public function wc_get_product( $product_id ) {
			$wc_get_product = function_exists( 'wc_get_product' ) ? 'wc_get_product' : 'get_product';

			return $wc_get_product( $product_id );
		}


		/**
		 * Do action before loads compare popup
		 *
		 * @since 2.1.1
		 * @author Francesco Licandro
		 */
		public function actions_before_load_popup() {
			// Removes WooCommerce Product Filter scripts.
			wp_dequeue_script( 'prdctfltr-main-js' );
			wp_dequeue_script( 'prdctfltr-history' );
			wp_dequeue_script( 'prdctfltr-ionrange-js' );
			wp_dequeue_script( 'prdctfltr-isotope-js' );
			wp_dequeue_script( 'prdctfltr-scrollbar-js' );
		}
	}
}
