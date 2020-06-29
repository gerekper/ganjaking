<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Role_Based_Prices_Product' ) ) {

	class YITH_Role_Based_Prices_Product {
		protected static $instance;

		protected $global_rules = array();

		/**
		 * YITH_Role_Based_Prices_Product constructor
		 */
		public function __construct() {

			$this->post_type = YITH_Role_Based_Type();

			if ( $this->is_frontend() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'include_frontend_product_style_script' ) );

				add_action( 'init', array( $this, 'init_user_info' ), 10 );

				add_action( 'woocommerce_single_product_summary', array(
					$this,
					'remove_add_to_cart_with_request_a_quote'
				), 10 );
				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'hide_add_to_cart_loop' ), 10, 2 );
				add_filter( 'option_woocommerce_tax_display_shop', array( $this, 'show_price_incl_excl_tax' ), 10, 2 );
				add_filter( 'option_woocommerce_tax_display_cart', array( $this, 'show_price_incl_excl_tax' ), 10, 2 );
				add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 15, 2 );
				add_filter( 'woocommerce_variation_is_purchasable', array( $this, 'is_purchasable' ), 15, 2 );


				add_action( 'woocommerce_single_product_summary', array( $this, 'single_product_summary' ), 5 );


				add_filter( 'woocommerce_product_get_price', array( $this, 'get_price' ), 20, 2 );
				add_filter( 'woocommerce_product_variation_get_price', array( $this, 'get_price' ), 20, 2 );


				// support to WooCommerce Product Bundles
				add_filter( 'woocommerce_bundle_get_base_price', array( $this, 'get_price' ), 5, 2 );

				add_filter( 'woocommerce_get_price_html', array( $this, 'get_price_html' ), 11, 2 );
				add_filter( 'woocommerce_get_variation_price_html', array( $this, 'get_price_html' ), 11, 2 );

				add_filter( 'yith_wcpb_ajax_get_bundle_total_price', array(
					$this,
					'get_ajax_bundle_total_price_html'
				), 10, 3 );
				add_filter( 'yith_wcpb_woocommerce_get_price_html', array(
					$this,
					'get_bundle_total_price_html'
				), 10, 2 );
				add_filter( 'woocommerce_show_variation_price', array( $this, 'show_variation_price' ), 5, 3 );

				$hook = $this->get_hook_position( 'ywcrbp_position_user_txt' );
				add_action( 'woocommerce_single_product_summary', array( $this, 'print_custom_message' ), $hook );
				add_action( 'yith_wcqv_product_summary', array( $this, 'print_custom_message' ), $hook );


				if ( defined( 'YITH_YWPI_PREMIUM' ) ) {
					add_filter( 'ywpi_get_item_product_regular_price', array(
						$this,
						'return_role_based_price_for_pdf'
					), 10, 2 );
				}

				if ( defined( 'YITH_YWRAQ_PREMIUM' ) ) {

					add_action( 'wp_loaded', array( $this, 'validate_add_to_cart_action' ), 15 );
				}

				add_action( 'init', array( $this, 'load_global_rules' ), 20 );
			}

			if( is_admin() ){
				add_action( 'admin_enqueue_scripts', array( $this, 'include_admin_product_style_script' ) );
				add_action( 'woocommerce_product_options_general_product_data', array(
					$this,
					'show_product_price_rule'
				) );
				add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_meta' ), 25, 2 );
				add_action( 'woocommerce_save_product_variation', array(
					$this,
					'save_product_variation_meta'
				), 25, 2 );
				add_action( 'woocommerce_variation_options_pricing', array(
					$this,
					'show_product_variation_price_rule'
				), 10, 3 );
				add_action( 'wp_ajax_add_new_price_role', array( $this, 'add_new_price_role' ) );
				add_action( 'wp_ajax_add_new_variation_price_role', array( $this, 'add_new_variation_price_role' ) );
			}
		}

		/**
		 * @return YITH_Role_Based_Prices_Product
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * include style and script in admin
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function include_admin_product_style_script() {

			if ( ! isset( $_GET['post'] ) ) {
				global $post;
			} else {
				$post = $_GET['post'];
			}

			$right_post_type = ( isset( $post ) && get_post_type( $post ) == 'product' );
			$suffix          = ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '';

			if ( $right_post_type ) {

				wp_enqueue_script( 'ywcrbp_product_admin', YWCRBP_ASSETS_URL . 'js/ywcrbp_product_admin' . $suffix . '.js', array( 'jquery' ), time(), true );
				wp_enqueue_style( 'ywcrbp_product_admin_style', YWCRBP_ASSETS_URL . 'css/ywcrbp_product_admin.css', array(), YWCRBP_VERSION );

				$params = array(
					'admin_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'actions'   => array(
						'add_new_price_role'           => 'add_new_price_role',
						'add_new_variation_price_role' => 'add_new_variation_price_role'
					),
					'plugin'    => YWCRBP_SLUG
				);

				wp_localize_script( 'ywcrbp_product_admin', 'ywcrbp_prd', $params );
			}
		}

		/**
		 * include style and script in frontend
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function include_frontend_product_style_script() {

			if ( ! isset( $_GET['post'] ) ) {
				global $post;
			} else {
				$post = $_GET['post'];
			}

			$right_post_type = ( isset( $post ) && get_post_type( $post ) == 'product' );

			if ( $right_post_type ) {

				wp_enqueue_style( 'ywcrbp_product_frontend_style', YWCRBP_ASSETS_URL . 'css/ywcrbp_product_frontend.css', array(), YWCRBP_VERSION );
			}
		}

		/**
		 * show product metaboxes
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function show_product_price_rule() {

			wc_get_template( 'metaboxes/product-rules.php', array(), '', YWCRBP_TEMPLATE_PATH );
		}

		/** show product variation metaboxes
		 *
		 * @param $loop
		 * @param $variation_data
		 * @param $variation
		 *
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function show_product_variation_price_rule( $loop, $variation_data, $variation ) {
			wc_get_template( 'metaboxes/product-variation-rules.php', array(
				'loop'           => $loop,
				'variation_data' => $variation_data,
				'variation'      => $variation
			), '', YWCRBP_TEMPLATE_PATH );

		}

		/**
		 * save product meta
		 *
		 * @param $product_id
		 * @param $product
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		public function save_product_meta( $product_id, $product ) {

			if ( isset( $_REQUEST['type_price_rule'] ) ) {

				$product_rules = isset( $_REQUEST['_product_rules'] ) ? $_REQUEST['_product_rules'] : '';
				$how_apply     = isset( $_REQUEST['how_apply_product_rule'] ) ? $_REQUEST['how_apply_product_rule'] : 'only_this';

				$product = wc_get_product( $product_id );

				$product_meta = array(
					'how_apply_product_rule' => $how_apply,
					'_product_rules'         => $product_rules
				);

				foreach ( $product_meta as $meta_key => $meta_value ) {
					$product->update_meta_data( $meta_key, $meta_value );
				}
				$product->save();
			}

			delete_site_transient( 'ywcrb_rolebased_prices' );
		}


		/**
		 * save variation meta
		 *
		 * @param $variation_id
		 * @param $loop
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		public function save_product_variation_meta( $variation_id, $loop ) {

			if ( isset( $_REQUEST['type_price_rule'] ) ) {

				$variation_rule = isset( $_REQUEST['_product_variable_rule'][ $loop ] ) ? $_REQUEST['_product_variable_rule'][ $loop ] : '';
				$how_apply      = isset( $_REQUEST['how_apply_product_rule'][ $loop ] ) ? $_REQUEST['how_apply_product_rule'][ $loop ] : 'only_this';

				$product = wc_get_product( $variation_id );
				$product->update_meta_data( 'how_apply_product_rule', $how_apply );
				$product->update_meta_data( '_product_rules', $variation_rule );

				$product->save();

			}

			delete_site_transient( 'ywcrb_rolebased_prices' );
		}


		/**
		 * @author YITHEMES
		 * @since  1.0.0
		 * delete wc_var_prices for variable product
		 */
		public function single_product_summary() {

			global $post;

			if ( isset( $post ) && 'product' == $post->post_type ) {

				$product_id = $post->ID;
				$product    = wc_get_product( $product_id );

				if ( $product->is_type( 'variable' ) ) {

					delete_transient( 'wc_var_prices_' . $product_id );
				}

			}
		}

		/**
		 * if a product has user role set as non sale
		 *
		 * @param bool $is_on_sale
		 * @param WC_Product $product
		 *
		 * @return bool
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function product_is_on_sale( $is_on_sale, $product ) {


			if ( $product->is_type( 'variable' ) ) {

				$role_base_price = $this->get_variation_new_prices( $product );
				$is_on_sale      = empty( $role_base_price ) ? $is_on_sale : false;
			} else {
				$role_base_price = $this->get_role_based_price( $product );
				$is_on_sale      = ( $role_base_price == 'no_price' ) ? $is_on_sale : false;
			}

			return $is_on_sale;
		}

		/**
		 * get a new price for user role
		 *
		 * @param string $price
		 * @param WC_Product $product
		 *
		 * @return mixed
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function get_price( $price, $product ) {

			$return_original_price = apply_filters( 'yith_wcrbp_return_original_price', false, $price, $product );
			$is_custom_price       = $this->is_custom_price( $product );


			if ( ! $product || ( is_admin() && ! is_ajax() ) || $return_original_price || $is_custom_price ) {
				return $price;
			}


			if ( $price !== '' && ! is_null( $product ) && ! $product->is_type( 'variable' ) && ! $product->is_type( 'grouped' ) ) {

				$role_price = $this->get_role_based_price( $product );

				if ( $role_price !== 'no_price' ) {

					$price = apply_filters( 'yith_wcrbp_get_role_based_price', $role_price, $product );

				}
			}

			return $price;
		}


		/**
		 * @param WC_Product $product
		 *
		 * @return bool
		 */
		public function is_custom_price( $product ) {

			$has_dynamic_price             = $product->get_meta( 'has_dynamic_price' );
			$yith_wapo_adjust_price        = $product->get_meta( 'yith_wapo_price' );
			$ywcp_composite_info           = $product->get_meta( 'ywcp_composite_info' );
			$ywcpb_bundled_item_price_zero = $product->get_meta( 'bundled_item_price_zero' );
			$is_wc_bundle                  = $product->is_type( 'bundle' );
			$is_deposit                    = $product->get_meta( 'yith_wcdp_deposit' );
			$is_balance                    = $product->get_meta( 'yith_wcdp_balance' );
			$is_gift_card                  = $product->is_type( 'gift-card' );

			$is_subscription = false;

			if ( function_exists( 'YITH_WC_Subscription' ) && is_callable( array(
					YITH_WC_Subscription(),
					'is_subscription'
				) ) ) {
				$is_subscription = YITH_WC_Subscription()->is_subscription( $product );
			}


			return $has_dynamic_price || $yith_wapo_adjust_price || is_array( $ywcp_composite_info ) || $is_gift_card || $ywcpb_bundled_item_price_zero || $is_deposit || $is_balance || $is_wc_bundle || $is_subscription;
		}

		/**
		 * @param WC_Product $product
		 */
		public function get_role_based_price( $product ) {

			global $woocommerce_wpml, $sitepress;

			$product_id = $product->get_id();

			if ( ! isset( $this->user_role['role'] ) ) {
				$this->init_user_info();
			}
			$current_rule = $this->user_role['role'];

			$all_role_based_prices = get_site_transient( 'ywcrb_rolebased_prices' );
			$all_role_based_prices = empty( $all_role_based_prices ) ? array() : $all_role_based_prices;
			$active_currency       = get_woocommerce_currency();

			if (  ! isset( $all_role_based_prices[ $product_id ][ $active_currency ][ $current_rule ] ) ) {

				$role_based_price                                                          = ywcrbp_calculate_product_price_role( $product, $this->global_rules, $current_rule );
				$all_role_based_prices[ $product_id ][ $active_currency ][ $current_rule ] = $role_based_price;


				set_site_transient( 'ywcrb_rolebased_prices', $all_role_based_prices );
			} else {
				$role_based_price = $all_role_based_prices[ $product_id ][ $active_currency ][ $current_rule ];
			}



			return apply_filters( 'yith_ywrbp_price', $role_based_price, $product );
		}

		/**
		 * @param string $price
		 * @param WC_Product|WC_Product_Variable $product
		 *
		 * @return string
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function get_price_html( $price, $product ) {

			$is_custom_price       = $this->is_custom_price( $product );
			$return_original_price = apply_filters( 'yith_wcrbp_return_original_price', false, $price, $product );
			if ( $price == '' && ! $product || $return_original_price || $is_custom_price || ( is_admin() && ! is_ajax() ) || '' === $price ) {
				return $price;
			}
			$product_type = $product->get_type();


			switch ( $product_type ) {

				case 'simple':
				case 'ticket-event':
				case 'variation':
				case 'yith-composite':
					$price = $this->get_simple_price_html( $price, $product );
					break;
				case 'variable':
					$price = $this->get_variable_price_html( $price, $product );
					break;
				case 'grouped':
					$price = $this->get_grouped_price_html( $price, $product );
					break;

				case 'yith_bundle':

					$per_items_pricing = $product->get_meta( '_yith_wcpb_per_item_pricing' );

					if ( $per_items_pricing !== 'yes' ) {
						$price = $this->get_simple_price_html( $price, $product );
					}
					break;

			}

			return apply_filters( 'ywcrbp_get_price_html', $price, $product );
		}


		/**
		 * @param string $price
		 * @param float $price
		 * @param bool|WC_Product_Yith_Bundle $product
		 */
		public function get_ajax_bundle_total_price_html( $price_html, $price = 0, $product = false ) {
			if ( ! $product && isset( $_POST['bundle_id'] ) ) {
				/** @var WC_Product_Yith_Bundle $product */
				$product = wc_get_product( $_POST['bundle_id'] );
			}

			if ( $product && is_callable( array( $product, 'get_wpml_parent_id' ) ) ) {
				$wpml_parent_id = $product->get_wpml_parent_id();

				if ( isset( $_POST['bundle_id'] ) && $wpml_parent_id != $_POST['bundle_id'] ) {
					$product = wc_get_product( $wpml_parent_id );
				}
			}

			if ( $product ) {
				if ( ! isset( $this->user_role ) ) {
					$this->init_user_info();
				}
				$show_regular_price = $this->user_role['show_regular_price'];
				$show_your_price    = $this->user_role['show_your_price'];
				$role_price_html    = '';
				$regular_price_html = '';
				$from_txt           = '';
				$your_price_txt     = get_option( 'ywcrbp_your_price_txt' );
				$regular_price_txt  = get_option( 'ywcrbp_regular_price_txt' );
				$per_items_pricing  = $product->get_meta( '_yith_wcpb_per_item_pricing' );


				if ( $per_items_pricing === 'yes' ) {
					ywcrbp_remove_bundle_actions();

					$array_qty     = isset( $_POST['array_qty'] ) ? $_POST['array_qty'] : array();
					$array_opt     = isset( $_POST['array_opt'] ) ? $_POST['array_opt'] : array();
					$array_var     = isset( $_POST['array_var'] ) ? $_POST['array_var'] : array();
					$regular_price = $product->get_per_item_price_tot_with_params( $array_qty, $array_opt, $array_var, false, 'edit' );

					ywcrbp_add_bundle_actions();

					$your_price = $product->get_per_item_price_tot_with_params( $array_qty, $array_opt, $array_var, false, 'edit' );

					if ( ( $your_price !== 'no_price' && $your_price !== $regular_price ) && $show_your_price ) {

						$role_price_html = $your_price_txt . ' ' . $from_txt . ' ' . $this->get_your_price_html( $product, $your_price, false );
					}

					if ( $show_regular_price ) {
						$regular_price_html = $regular_price_txt . ' ' . $from_txt . ' ' . $this->get_regular_price_html( $product, $regular_price, false );
					}

					if ( $regular_price_html !== '' && ( $your_price !== 'no_price' && $your_price !== $regular_price ) ) {
						$show_del           = ( $show_your_price && 'no_price' !== $your_price );
						$regular_price_html = $this->get_formatted_price_html( $regular_price_html, $show_del, 'regular' );
					}

					if ( $show_your_price && $role_price_html !== '' ) {
						$role_price_html = $this->get_formatted_price_html( $role_price_html );
					}

					$price_html = $regular_price_html . ' ' . $role_price_html;
				}

			}

			return $price_html;
		}

		public function get_bundle_total_price_html( $price, $product ) {
			if ( $product && is_callable( array( $product, 'get_wpml_parent_id' ) ) ) {
				$wpml_parent_id = $product->get_wpml_parent_id();

				if ( isset( $_POST['bundle_id'] ) && $wpml_parent_id != $_POST['bundle_id'] ) {
					$product = wc_get_product( $wpml_parent_id );
				}
			}
			if ( $product ) {
				/** @var WC_Product_Yith_Bundle $product */

				$show_regular_price = $this->user_role['show_regular_price'];
				$show_your_price    = $this->user_role['show_your_price'];
				$per_items_pricing  = $product->get_meta( '_yith_wcpb_per_item_pricing' );

				if ( $per_items_pricing === 'yes' ) {

					$bundle_price_display = get_option( 'yith-wcpb-pip-bundle-pricing', 'from-min' );
					$min_your_price       = $product->get_per_item_price_tot();
					$max_your_price       = $product->get_per_item_price_tot_max( true, true, true );

					ywcrbp_remove_bundle_actions();

					$min_regular_price = $product->get_per_item_price_tot( 'edit' );
					if ( 'regular-and-discounted' == $bundle_price_display ) {
						$max_regular_price = $product->get_per_item_price_tot_max( true, false, false, 'edit' );
					} else {
						$max_regular_price = $product->get_per_item_price_tot_max( true, true, true, 'edit' );
					}

					$role_price_html    = '';
					$regular_price_html = '';
					$from_txt           = '';
					$your_price_txt     = get_option( 'ywcrbp_your_price_txt' );
					$regular_price_txt  = get_option( 'ywcrbp_regular_price_txt' );
					if ( 'from-min' == $bundle_price_display ) {
						$from_txt = trim( sprintf( _x( 'From %s', 'From price', 'yith-woocommerce-product-bundles' ), '' ) );
					}

					if ( ( $min_your_price !== 'no_price' && $min_your_price !== $min_regular_price ) && $show_your_price ) {
						$role_price_html = $your_price_txt . ' ' . $from_txt . ' ' . $this->get_your_price_html( $product, $min_your_price, false );
					}

					if ( $show_regular_price ) {
						$regular_price_html = $regular_price_txt . ' ' . $from_txt . ' ' . $this->get_regular_price_html( $product, $min_regular_price, false );
					}

					if ( 'min-max' == $bundle_price_display ) {

						if ( $role_price_html !== '' && $max_your_price > $min_your_price ) {
							$max_your_price_html = $this->get_your_price_html( $product, $max_your_price, false );
							$role_price_html     = ywcrbp_get_format_price_from_to( $product, $role_price_html, $max_your_price_html );
						}
						if ( $regular_price_html !== '' && $min_regular_price < $max_regular_price ) {

							$max_regular_price_html = $this->get_regular_price_html( $product, $max_regular_price, false );
							$regular_price_html     = ywcrbp_get_format_price_from_to( $product, $regular_price_html, $max_regular_price_html );
						}
					} elseif ( 'regular-and-discounted' == $bundle_price_display ) {
						$regular_price_html = $this->get_regular_price_html( $product, $max_regular_price, false );
					}

					if ( $regular_price_html !== '' && ( $min_your_price !== 'no_price' && $min_your_price !== $min_regular_price ) ) {
						$show_del           = ( $show_your_price && 'no_price' !== $min_your_price );
						$regular_price_html = $this->get_formatted_price_html( $regular_price_html, $show_del, 'regular' );
					}

					if ( $show_your_price && $role_price_html !== '' ) {
						$role_price_html = $this->get_formatted_price_html( $role_price_html );
					}
					ywcrbp_add_bundle_actions();
					$price = $regular_price_html . $role_price_html;
				}
			}

			return $price;
		}

		/**
		 * @param WC_Product $product
		 * @param string $price
		 * @param int $qty
		 *
		 * @return mixed|null|void
		 */
		public function get_price_suffix( $product, $price = '', $qty = 1 ) {

			if ( $price == '' ) {
				$price = $product->get_price();
			}
			$how_show = isset( $this->user_role['how_show_price'] ) ? $this->user_role['how_show_price'] : get_option( 'woocommerce_tax_display_shop' );;

			$price_display_suffix = get_option( "ywcrbp_price_{$how_show}_suffix" );

			if ( $price_display_suffix ) {

				$replacements = array(
					'{price_including_tax}' => wc_price( wc_get_price_including_tax( $product, array( 'qty' => $qty, 'price' => $price ) ) ), // @phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.ArrayItemNoNewLine, WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
					'{price_excluding_tax}' => wc_price( wc_get_price_excluding_tax( $product, array( 'qty' => $qty, 'price' => $price ) ) ), // @phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
				);
				$price_display_suffix         = str_replace( array_keys( $replacements ), array_values( $replacements ), ' <small class="woocommerce-price-suffix">' . wp_kses_post( $price_display_suffix ) . '</small>' );

			} else {

				$price_display_suffix = $product->get_price_suffix();
			}

			return apply_filters( 'yith_role_based_prices_get_price_suffix', $price_display_suffix, $this );
		}

		/**
		 * get simple price html
		 *
		 * @param WC_Product $product_id
		 *
		 * @return string
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		public function get_simple_price_html( $price, $product ) {
			$regular_price = $product->get_regular_price();
			$sale_price    = $product->get_sale_price();
			$role_price    = $this->get_role_based_price( $product );

			$show_regular_price       = $this->user_role['show_regular_price'];
			$show_on_sale_price       = $this->user_role['show_on_sale_price'];
			$show_your_price          = $this->user_role['show_your_price'];
			$show_how_markup_discount = $this->user_role['show_percentage'];

			$regular_price_html = '';
			$sale_price_html    = '';
			$role_price_html    = '';

			$can_show_price = apply_filters( 'ywcrbp_can_show_rolebased_price', true, $regular_price, $role_price, $product );
			if ( $show_regular_price && $can_show_price ) {


				$regular_price_html = $this->get_regular_price_html( $product, $regular_price );


				$show_del = ( $show_on_sale_price && $product->is_on_sale( 'edit' ) && ( $regular_price !== $sale_price ) ) || ( $show_your_price && 'no_price' !== $role_price && ( $regular_price != $role_price ) );


				$regular_price_html = $this->get_formatted_price_html( $regular_price_html, $show_del, 'regular' );

				$regular_price_html = apply_filters( 'ywcrbp_simple_regular_price_html', $regular_price_html, $regular_price, $product );

			}

			if ( $show_on_sale_price && $product->is_on_sale( 'edit' ) ) {

				$sale_price_html = $this->get_sale_price_html( $product, $sale_price );

				$show_del = ( $show_your_price && ( 'no_price' != $role_price && $sale_price !== $role_price ) );

				$sale_price_html = $this->get_formatted_price_html( $sale_price_html, $show_del, 'sale' );

				$sale_price_html = apply_filters( 'ywcrbp_simple_sale_price_html', $sale_price_html, $sale_price, $product );
			}

			if ( $show_your_price && 'no_price' !== $role_price ) {
				$role_price_html = $this->get_your_price_html( $product, $role_price );
				$role_price_html = $this->get_formatted_price_html( $role_price_html );
				$role_price_html = apply_filters( 'ywcrbp_simple_your_price_html', $role_price_html, $role_price, $product );

				if ( ! empty( $role_price_html ) && $show_how_markup_discount && is_product() ) {
					$markup_discount_html = $this->get_total_discount_markup_formatted( $product );
					$role_price_html      .= $markup_discount_html;
				}
			}

			$their_price_html = $this->get_formatted_price_html( $this->get_their_price_html( $product ), false, 'their' );
			$price_html       = $regular_price_html . '' . $sale_price_html . '' . $role_price_html . ' ' . $their_price_html;

			return $price_html;
		}

		/**
		 * @param WC_Product_Variable $product
		 *
		 * @return string
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		public function get_variable_price_html( $price_html, $product ) {

			$price                    = '';
			$variation_prices         = $product->get_variation_prices( false );
			$variation_regular_prices = $variation_prices['regular_price'];
			$variation_sale_prices    = $variation_prices['sale_price'];

			$variation_your_prices = $this->get_variation_new_prices( $product );

			$min_regular_price = current( $variation_regular_prices );

			$max_regular_price = end( $variation_regular_prices );
			$min_sale_price    = current( $variation_sale_prices );
			$max_sale_price    = end( $variation_sale_prices );
			$has_role_price    = false;

			if ( count( $variation_your_prices ) > 0 ) {
				$min_your_price = floatval( current( $variation_your_prices ) );
				$max_your_price = floatval( end( $variation_your_prices ) );
				$has_role_price = true;
			}
			$show_your_price    = $this->user_role['show_your_price'];
			$show_regular_price = $this->user_role['show_regular_price'];
			$show_on_sale_price = $this->user_role['show_on_sale_price'];

			$regular_price_html = '';
			$sale_price_html    = '';
			$your_price_html    = '';

			if ( $show_regular_price ) {
				$regular_price_txt = get_option( 'ywcrbp_regular_price_txt' );

				$regular_price_html = $regular_price_txt . ' ' . $this->get_regular_price_html( $product, $min_regular_price );

				if ( $min_regular_price !== $max_regular_price ) {

					$max_regular_price_html = $this->get_regular_price_html( $product, $max_regular_price );
					$regular_price_html     = ywcrbp_get_format_price_from_to( $product, $regular_price_html, $max_regular_price_html );

				}

				$show_del           = ( $show_on_sale_price && ( $min_regular_price != $min_sale_price ) ) || ( $show_your_price && $has_role_price && ( $min_regular_price != $min_your_price ) );
				$regular_price_html = $this->get_formatted_price_html( $regular_price_html, $show_del, 'regular' );
				$regular_price_html = apply_filters( 'ywcrbp_variable_regular_price_html', $regular_price_html, $product, $min_regular_price, $max_regular_price );

			}

			if ( $show_on_sale_price && $min_regular_price !== $min_sale_price ) {
				$sale_price_txt  = get_option( 'ywcrbp_sale_price_txt' );
				$sale_price_html = $sale_price_txt . ' ' . $this->get_sale_price_html( $product, $min_sale_price );

				if ( $min_sale_price !== $max_sale_price ) {

					$max_sale_price_html = $this->get_sale_price_html( $product, $max_sale_price );

					$sale_price_html = ywcrbp_get_format_price_from_to( $product, $sale_price_html, $max_sale_price_html );
				}

				$show_del = ( $show_your_price && $has_role_price && $min_your_price !== '' );

				$sale_price_html = $this->get_formatted_price_html( $sale_price_html, $show_del, 'sale' );
				$sale_price_html = apply_filters( 'ywcrbp_variable_sale_price_html', $sale_price_html, $product, $min_sale_price, $max_sale_price );

			}


			if ( $show_your_price && $has_role_price ) {
				$your_price_txt  = get_option( 'ywcrbp_your_price_txt' );
				$your_price_html = $your_price_txt . ' ' . $this->get_your_price_html( $product, $min_your_price );

				if ( $min_your_price !== $max_your_price ) {
					$max_your_price_html = $this->get_your_price_html( $product, $max_your_price );
					$your_price_html     = ywcrbp_get_format_price_from_to( $product, $your_price_html, $max_your_price_html );
				}

				$your_price_html = $this->get_formatted_price_html( $your_price_html );
				$your_price_html = apply_filters( 'ywcrbp_variable_your_price_html', $your_price_html, $product, $min_your_price, $max_your_price );

			}

			$their_price_html = $this->get_formatted_price_html( $this->get_their_variable_price_html( $product ), false, 'their' );
			$price_html       = $regular_price_html . '' . $sale_price_html . '' . $your_price_html . '' . $their_price_html;

			return $price_html;
		}

		/**
		 * @param string $price_html
		 * @param WC_Product_Grouped $product
		 *
		 * @return string
		 */
		public function get_grouped_price_html( $price_html, $product ) {

			$show_your_price    = $this->user_role['show_your_price'];
			$show_regular_price = $this->user_role['show_regular_price'];

			$role_based_child_price = array();

			$min_original_price = '';
			$max_original_price = '';
			$regular_price_html = '';
			$your_price_html    = '';

			$min_role_based_price = '';
			$max_role_based_price = '';

			$original_child_price = array();


			$product_children = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );


			foreach ( $product_children as $child ) {

				$price = $this->get_role_based_price( $child );

				if ( 'no_price' !== $price ) {
					$role_based_child_price[] = $price;
				} else {
					$role_based_child_price[] = $child->get_price( 'edit' );
				}

				$original_child_price[] = $child->get_price( 'edit' );
			}

			if ( count( $original_child_price ) > 0 ) {
				$min_original_price = min( $original_child_price );
				$max_original_price = max( $original_child_price );
			}


			if ( count( $role_based_child_price ) > 0 ) {
				$min_role_based_price = min( $role_based_child_price );
				$max_role_based_price = max( $role_based_child_price );
			}

			if ( $show_regular_price && $min_original_price !== '' ) {

				$regular_price_txt = get_option( 'ywcrbp_regular_price_txt' );

				$regular_price_html = $regular_price_txt . ' ' . $this->get_regular_price_html( $product, $min_original_price );

				if ( $min_original_price !== $max_original_price ) {

					$max_regular_price_html = $this->get_regular_price_html( $product, $max_original_price );
					$regular_price_html     = ywcrbp_get_format_price_from_to( $product, $regular_price_html, $max_regular_price_html );
				}
				$show_del           = ( $show_your_price && $min_role_based_price !== '' );
				$regular_price_html = $this->get_formatted_price_html( $regular_price_html, $show_del, 'regular' );


			}

			if ( $show_your_price && $min_role_based_price !== '' ) {
				$your_price_txt  = get_option( 'ywcrbp_your_price_txt' );
				$your_price_html = $your_price_txt . ' ' . $this->get_your_price_html( $product, $min_role_based_price );

				if ( $min_role_based_price !== $max_role_based_price ) {
					$max_your_price_html = $this->get_your_price_html( $product, $max_role_based_price );
					$your_price_html     = ywcrbp_get_format_price_from_to( $product, $your_price_html, $max_your_price_html );
				}

				$your_price_html = $this->get_formatted_price_html( $your_price_html );
			}

			return $regular_price_html . $your_price_html;
		}


		public function get_variation_role_price( $price, $product, $min_or_max, $for_display ) {

			$prices = $this->get_variation_new_prices( $product );

			if ( count( $prices ) > 0 ) {
				if ( 'min' == $min_or_max ) {
					$price = min( $prices );
				} else {
					$price = max( $prices );
				}
			}

			return $price;
		}

		/**
		 * @param WC_Product_Variable $product
		 *
		 * @return array
		 */
		public function get_variation_new_prices( $product, $for_display = false ) {

			$new_prices = array();

			$variation_ids = version_compare( WC()->version, '2.7.0', '>=' ) ? $product->get_visible_children() : $product->get_children( true );


			foreach ( $variation_ids as $variation_id ) {

				$variation = wc_get_product( $variation_id );
				if ( $variation instanceof WC_Product_Variation ) {


					$new_price = $this->get_role_based_price( $variation );

					if ( 'no_price' !== $new_price && '' !== $new_price ) {

						if ( $for_display ) {

							if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
								$new_prices[ $variation_id ] = wc_get_price_including_tax( $product, array(
									'qty'   => 1,
									'price' => $new_price
								) );
							} else {
								$new_prices[ $variation_id ] = wc_get_price_excluding_tax( $product, array(
									'qty'   => 1,
									'price' => $new_price
								) );
							}
						} else {
							$new_prices[ $variation_id ] = $new_price;
						}

					}
				}
				asort( $new_prices );
			}


			return $new_prices;
		}


		/**
		 * @param WC_Product $product
		 *
		 * @param            $regular_price
		 * @param bool $display_price
		 *
		 * @return string
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		public function get_regular_price_html( $product, $regular_price, $display_price = true ) {
			$regular_price_html = wc_price( $regular_price ) . $this->get_price_suffix( $product, $regular_price );
			if ( $display_price ) {

				$display_regular_price = wc_get_price_to_display( $product, array(
					'qty'   => 1,
					'price' => $regular_price
				) );
				if ( $regular_price > 0 ) {
					$regular_price_html = wc_price( $display_regular_price ) . $this->get_price_suffix( $product, $regular_price );
				} else {
					$regular_price_html = wc_price( $display_regular_price );
				}
			}
			$regular_price_txt = get_option( 'ywcrbp_regular_price_txt' );

			$regular_html = ! empty( $regular_price_txt ) && ( ! $product->is_type( 'simple' ) ) ? $regular_price_html : $regular_price_txt . ' ' . $regular_price_html;


			return apply_filters( 'ywcrbp_get_regular_price_html', $regular_html, $product );
		}


		/**
		 * @param        $price_html
		 * @param bool $del
		 * @param string $price_type
		 *
		 * @return string
		 */
		public function get_formatted_price_html( $price_html, $del = false, $price_type = 'your' ) {
			$price_formatted_html = '';
		    if( !(empty( $price_html) )) {
			    $price_formatted_html = "<span class='ywcrbp_" . $price_type . "_price'>";
			    $price_formatted_html .= $del ? '<del>' : '';
			    $price_formatted_html .= $price_html;
			    $price_formatted_html .= $del ? '</del>' : '';
			    $price_formatted_html .= '</span>';
		    }
			return $price_formatted_html;
		}

		/**
		 * @param WC_Product $product
		 * @param float $sale_price
		 *
		 * @return string
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function get_sale_price_html( $product, $sale_price ) {


			$sale_price_txt = get_option( 'ywcrbp_sale_price_txt' );

			$display_sale_price = wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $sale_price ) );
			$sale_price_html    = wc_price( $display_sale_price ) . $this->get_price_suffix( $product, $sale_price );

			$sale_price_html = ! empty( $sale_price_txt ) && ( $product->is_type( 'variable' ) || $product->is_type( 'grouped' ) ) ? $sale_price_html : $sale_price_txt . ' ' . $sale_price_html;

			return apply_filters( 'ywcrbp_get_sale_price_html', $sale_price_html, $product );
		}

		/**
		 * @param WC_Product $product
		 * @param            $role_price
		 * @param bool $display_price
		 *
		 * @return string
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		public function get_your_price_html( $product, $role_price, $display_price = true ) {
			$your_price_html = wc_price( $role_price ) . $this->get_price_suffix( $product, $role_price );
			if ( $display_price ) {
				$display_your_price = wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $role_price ) );
				$your_price_html    = wc_price( $display_your_price ) . $this->get_price_suffix( $product, $role_price );
			}
			$your_price_txt = apply_filters( 'yith_role_based_price_your_price_label', get_option( 'ywcrbp_your_price_txt' ) );

			if ( $role_price == 0 ) {

				$free_text       = apply_filters( 'ywcrbp_change_free_price_text', '<span class="amount">' . __( 'Free!', 'woocommerce' ) . '</span>', $product );
				$your_price_html = ! empty( $your_price_txt ) && ( ! $product->is_type( 'simple' ) ) ? $free_text : $your_price_txt . ' ' . $free_text;
			} else {
				$your_price_html = ! empty( $your_price_txt ) && ( ! $product->is_type( 'simple' ) ) ? $your_price_html : $your_price_txt . ' ' . $your_price_html;
			}


			return apply_filters( 'ywcrbp_get_your_price_html', $your_price_html, $product );
		}

		/**
		 * get the price for another user role
		 *
		 * @param WC_Product $product
		 *
		 *
		 * @return string
		 */
		public function get_their_price_html( $product ) {

			$their_price_html = '';
			$different_role   = $this->user_can_show_price_of_different_role( $this->user_role['role'] );


			if ( ! empty( $different_role ) ) {
				$your_price_txt = get_option( 'ywcrbp_their_price_txt', '' );

				$user_role = current( array_keys( $different_role ) );


				$your_price_txt  = str_replace( "{role_name}", $different_role[ $user_role ], $your_price_txt );
				$old_user_info   = $this->user_role;
				$old_global_rule = $this->global_rules;

				$this->global_rules = array();

				$this->user_role['show_your_price'] = $this->user_can_show_prices( $user_role, 'your_price' );
				$this->user_role['role']            = $user_role;
				$this->user_role['how_show_price']  = $this->how_show_price( $user_role );

				$this->load_global_rules();
				$role_price = $this->get_role_based_price( $product );


				if ( 'no_price' !== $role_price ) {

					$their_price_html = wc_price( wc_get_price_to_display( $product, array(
							'qty'   => 1,
							'price' => $role_price
						) ) ) . $this->get_price_suffix( $product, $role_price );

					if ( $role_price == 0 ) {

						$free_text        = apply_filters( 'ywcrbp_change_free_price_text', '<span class="amount">' . __( 'Free!', 'woocommerce' ) . '</span>', $product );
						$their_price_html = ( empty( $your_price_txt ) && ! $product->is_type( 'variable' ) && ! $product->is_type( 'grouped' ) ) ? $free_text : $your_price_txt . ' ' . $free_text;
					} else {
						$their_price_html = ( empty( $your_price_txt ) && ! $product->is_type( 'variable' ) && ! $product->is_type( 'grouped' ) ) ? $their_price_html : $your_price_txt . ' ' . $their_price_html;
					}

				}
				$this->global_rules = $old_global_rule;
				$this->user_role    = $old_user_info;
			}


			return apply_filters( 'ywcrbp_get_their_price_html', $their_price_html, $product );
		}

		/**
		 * @param WC_Product_Variable $product
		 *
		 * @return string
		 */
		public function get_their_variable_price_html( $product ) {
			$their_price_html = '';
			$different_role   = $this->user_can_show_price_of_different_role( $this->user_role['role'] );

			if ( ! empty( $different_role ) ) {
				$your_price_txt = get_option( 'ywcrbp_their_price_txt', '' );

				$user_role = current( array_keys( $different_role ) );


				$your_price_txt  = str_replace( "{role_name}", $different_role[ $user_role ], $your_price_txt );
				$old_user_info   = $this->user_role;
				$old_global_rule = $this->global_rules;

				$this->global_rules = array();

				$this->user_role['show_your_price'] = $this->user_can_show_prices( $user_role, 'your_price' );
				$this->user_role['role']            = $user_role;
				$this->user_role['how_show_price']  = $this->how_show_price( $user_role );

				$this->load_global_rules();


				$variable_prices = $this->get_variation_new_prices( $product );

				if ( count( $variable_prices ) > 0 ) {

					$min                  = current( $variable_prices );
					$max                  = end( $variable_prices );
					$their_min_price_html = wc_price( wc_get_price_to_display( $product, array( 'price' => $min ) ) ) . $this->get_price_suffix( $product, $min );
					$their_max_price_html = wc_price( wc_get_price_to_display( $product, array( 'price' => $max ) ) ) . $this->get_price_suffix( $product, $max );

					if ( $min !== $max ) {
						$their_price_html = $your_price_txt . ' ' . ywcrbp_get_format_price_from_to( $product, $their_min_price_html, $their_max_price_html );
					} elseif ( $min > 0 ) {
						$their_price_html = $your_price_txt . ' ' . $their_min_price_html;
					} else {
						$free_text        = apply_filters( 'ywcrbp_change_free_price_text', '<span class="amount">' . __( 'Free!', 'woocommerce' ) . '</span>', $product );
						$their_price_html = $your_price_txt . ' ' . $free_text;
					}
				}
				$this->global_rules = $old_global_rule;
				$this->user_role    = $old_user_info;
			}

			return $their_price_html;
		}

		/**
		 * @param WC_Product_Yith_Bundle $product
		 */
		public function get_bundle_price_html( $product, $price ) {


			$show_your_price = $this->user_role['show_regular_price'];

			if ( $show_your_price ) {

				return $price;
			}

			return '';


		}


		/**
		 * set user info
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function init_user_info() {


			if ( ! is_user_logged_in() ) {

				$this->user_role['show_add_to_cart']   = $this->user_can_show_add_to_cart();
				$this->user_role['show_regular_price'] = $this->user_can_show_prices( 'guest', 'regular' );
				$this->user_role['show_on_sale_price'] = $this->user_can_show_prices( 'guest', 'on_sale' );
				$this->user_role['show_your_price']    = $this->user_can_show_prices( 'guest', 'your_price' );
				$this->user_role['role']               = 'guest';
				$this->user_role['how_show_price']     = $this->how_show_price();
				$this->user_role['show_percentage']    = $this->user_can_show_tot_discount();
			} else {

				$user_id   = get_current_user_id();
				$user      = get_user_by( 'id', $user_id );
				$user_role = apply_filters( 'yith_wcrbp_get_user_role', get_first_user_role( $user->roles ), $user_id );


				$this->user_role['show_add_to_cart']   = $this->user_can_show_add_to_cart( $user_role );
				$this->user_role['show_regular_price'] = $this->user_can_show_prices( $user_role, 'regular' );
				$this->user_role['show_on_sale_price'] = $this->user_can_show_prices( $user_role, 'on_sale' );
				$this->user_role['show_your_price']    = $this->user_can_show_prices( $user_role, 'your_price' );
				$this->user_role['role']               = $user_role;
				$this->user_role['how_show_price']     = $this->how_show_price( $user_role );
				$this->user_role['show_percentage']    = $this->user_can_show_tot_discount( $user_role );

			}

		}


		public function how_show_price( $user_role = 'guest' ) {

			$option = get_option( 'ywcrbp_show_prices_for_role' );

			$how_show_price = isset( $option[ $user_role ]['how_show_price'] ) ? 'incl' : 'excl';

			return apply_filters( 'ywcrbp_get_how_show_price_include_or_exclude_tax', $how_show_price, $user_role );
		}

		/**
		 * check if user can show add to cart
		 *
		 * @param string $user_role
		 *
		 * @return bool
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		public function user_can_show_add_to_cart( $user_role = 'guest' ) {

			$option = get_option( 'ywcrbp_show_prices_for_role' );

			return isset( $option[ $user_role ]['add_to_cart'] ) ? true : false;
		}

		/**
		 * check if user can show price
		 *
		 * @param string $user_role
		 * @param        $price_type
		 *
		 * @return bool
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function user_can_show_prices( $user_role = 'guest', $price_type ) {

			$option = get_option( 'ywcrbp_show_prices_for_role' );

			return isset( $option[ $user_role ][ $price_type ] ) ? true : false;
		}

		/**
		 * check if user can show tot discount/markup
		 *
		 * @param string $user_role
		 *
		 * @return bool
		 * @author YITHEMES
		 * @since  1.0.11
		 *
		 */
		public function user_can_show_tot_discount( $user_role = 'guest' ) {

			$option = get_option( 'ywcrbp_show_prices_for_role' );

			return isset( $option[ $user_role ]['show_percentage'] ) ? true : false;
		}

		/**
		 * @param string $user_role
		 *
		 * @return array
		 */
		public function user_can_show_price_of_different_role( $user_role = 'guest' ) {

			$option = get_option( 'ywcrbp_show_prices_for_role' );

			$different_role = array();

			if ( isset( $option[ $user_role ]['show_price_as'] ) && '' !== $option[ $user_role ]['show_price_as'] ) {

				$different_role[ $option[ $user_role ]['show_price_as'] ] = get_user_role_label_by_slug( $option[ $user_role ]['show_price_as'] );
			}

			return $different_role;
		}


		/**
		 * @param WC_Product $product
		 *
		 * @return bool
		 */
		public function has_price_rule( $product ) {


			$product_rule = $product->get_meta( '_product_rules', true );

			if ( ! empty( $product_rule ) ) {
				return true;
			} else {

				$this->load_global_rules();
				$current_rule  = $this->user_role['role'];
				$filtered_rule = get_global_rule_for_product( $product->get_id(), $product, $this->global_rules, $current_rule );

				if ( ! empty( $filtered_rule ) ) {
					return true;
				}

				return false;
			}

		}


		/**
		 * set regular price and price for product variation
		 *
		 * @param $price
		 * @param $variation
		 * @param $product
		 *
		 * @return float|int|mixed|null
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		public function variation_prices_price( $price, $variation, $product ) {


			if ( $price !== '' ) {
				$new_price = $this->get_role_based_price( $variation );

				if ( 'no_price' === $new_price ) {
					return $price;
				} else {
					return $new_price;
				}
			}

			return $price;

		}

		/**
		 * show variation price in frontend
		 *
		 * @param $show
		 * @param $product
		 * @param $variation
		 *
		 * @return string
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		public function show_variation_price( $show, $product, $variation ) {

			return true;
		}

		/**
		 * add new price role in admin
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function add_new_price_role() {

			if ( isset( $_REQUEST['ywcrbp_plugin'] ) && YWCRBP_SLUG === $_REQUEST['ywcrbp_plugin'] ) {

				if ( isset( $_REQUEST['ywcrbp_index'] ) && isset( $_REQUEST['ywcrbp_type'] ) ) {

					$index = $_REQUEST['ywcrbp_index'];
					$type  = $_REQUEST['ywcrbp_type'];
					$args  = array(
						'index' => $index,
						'rule'  => array( 'rule_type' => $type )
					);

					$args['args'] = $args;

					ob_start();
					wc_get_template( 'metaboxes/view/product-single-rule.php', $args, '', YWCRBP_TEMPLATE_PATH );
					$template = ob_get_contents();
					ob_end_clean();

					wp_send_json( array( 'result' => $template ) );
				}
			}
		}

		/**
		 * add new price rule in product variation
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function add_new_variation_price_role() {
			if ( isset( $_REQUEST['ywcrbp_plugin'] ) && YWCRBP_SLUG === $_REQUEST['ywcrbp_plugin'] ) {

				if ( isset( $_REQUEST['ywcrbp_index'] ) && isset( $_REQUEST['ywcrbp_type'] ) && isset( $_REQUEST['ywcrbp_loop'] ) ) {

					$index = $_REQUEST['ywcrbp_index'];
					$type  = $_REQUEST['ywcrbp_type'];
					$loop  = $_REQUEST['ywcrbp_loop'];
					$args  = array(
						'index' => $index,
						'loop'  => $loop,
						'rule'  => array( 'rule_type' => $type )
					);

					$args['args'] = $args;

					ob_start();
					wc_get_template( 'metaboxes/view/product-variation-single-rule.php', $args, '', YWCRBP_TEMPLATE_PATH );
					$template = ob_get_contents();
					ob_end_clean();

					wp_send_json( array( 'result' => $template ) );
				}
			}
		}


		public function show_price_incl_excl_tax( $value, $option ) {

			if ( ! isset( $this->user_role ) ) {
				$this->init_user_info();
			}

			if ( isset( $this->user_role['how_show_price'] ) && ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) ) {
				$value = maybe_unserialize( $this->user_role['how_show_price'] );
			}


			return $value;
		}

		/**
		 * if the add to cart is hide, the products are unpurchasable
		 *
		 * @param bool $purchasable
		 * @param WC_Product $product
		 *
		 * @return bool
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function is_purchasable( $purchasable, $product ) {

			if ( ! $this->user_role['show_add_to_cart'] && ! defined( 'YITH_YWRAQ_PREMIUM' ) ) {
				return false;
			}

			return $purchasable;
		}

		/**
		 * remove add to cart in loop and in single product
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function remove_add_to_cart() {

			if ( ! $this->user_role['show_add_to_cart'] ) {

				$priority = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart' );

				if ( false !== $priority ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', $priority );
				}

				$priority = has_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

				if ( false !== $priority ) {
					remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', $priority );
					add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );
				}
			}
		}

		public function remove_add_to_cart_with_request_a_quote() {

			if ( ! $this->user_role['show_add_to_cart'] ) {

				global $product;
				if ( isset( $product ) && $product->is_type( 'variable' ) ) {

					$hide_quantity = defined( 'YITH_YWRAQ_PREMIUM' ) ? '' : "$('.single_variation_wrap .variations_button .quantity' ).hide();";
					$inline_js
					               = "
                        $( '.single_variation_wrap .variations_button button' ).hide();" .
					                 $hide_quantity .
					                 "$( document).on( 'woocommerce_variation_has_changed', function() {
                         $( '.single_variation_wrap .variations_button button' ).hide();"
					                 . $hide_quantity .
					                 "});";

					wc_enqueue_js( $inline_js );

				} else {

					$inline_js = "$( '.cart button.single_add_to_cart_button' ).hide();";

					wc_enqueue_js( $inline_js );

				}
			}
		}

		/**
		 * @param            $link
		 * @param WC_Product $product
		 *
		 * @return string
		 */
		public function hide_add_to_cart_loop( $link, $product ) {

			if ( ! $this->user_role['show_add_to_cart'] && ! $product->is_type( 'variable' ) ) {
				return '';
			}

			return $link;
		}


		/**
		 * return priority hook
		 *
		 * @param $option_name
		 *
		 * @return int
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		private function get_hook_position( $option_name ) {

			$woocommerce_hook = get_option( $option_name );

			switch ( $woocommerce_hook ) {

				case 'template_single_title':
					return 4;
					break;
				case 'template_single_price':
					return 9;
					break;
				case 'template_single_excerpt':
					return 19;
					break;
				case 'template_single_add_to_cart':
					return 29;
					break;
				case 'template_single_meta':
					return 39;
					break;
				case 'template_single_sharing':
					return 49;
					break;
			}
		}

		/**
		 * print custom message
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function print_custom_message() {
			if ( ( ! $this->user_role['show_regular_price'] && ! $this->user_role['show_on_sale_price'] && ! $this->user_role['show_your_price'] ) ) {

				$custom_message = '';
				$class_message  = apply_filters( 'ywcrbp_add_custom_message_class', 'ywcrbp_custom_message' );
				$custom_message = apply_filters( 'ywcrbp_get_user_message', get_option( 'ywcrbp_message_user' ) );
				$color_message  = get_option( 'ywcrbp_message_color_user' );
				$message        = sprintf( '<p class="%s">%s</p>', $class_message, $custom_message );
				?>
                <style type="text/css">
                    p.<?php echo $class_message;?> {
                        color: <?php echo $color_message;?>;
                    }
                </style>
				<?php echo $message;

			}
		}

		/**
		 * @param WC_Product $product
		 *
		 * @return float
		 */
		public function calculate_total_discount_markup( $product ) {

			$role_price = $this->get_role_based_price( $product );

			$percentage = 0;

			if ( 'no_price' !== $role_price ) {
				$regular_price = $product->get_regular_price();

				$percentage = 1 - ( $role_price / $regular_price );
			}

			return $percentage;
		}

		/**
		 * @param WC_Product $product
		 *
		 * @return string
		 */
		public function get_total_discount_markup_formatted( $product ) {

			$discount = $this->calculate_total_discount_markup( $product );

			if ( $discount == 0 ) {
				return '';
			}

			if ( $discount > 0 ) {

				$discount_formatted = sprintf( '%s', round( $discount * 100, 2 ) . '%' );
				$discount_class     = 'ywcrpb_discount';
				$discount_text      = get_option( 'ywcrbp_total_discount_mess' );
				$discount_text      = str_replace( '{ywcrbp_total_discount}', $discount_formatted, $discount_text );
				$filter_id          = 'discount';

			} else {

				$discount_formatted = sprintf( '%s', round( abs( $discount * 100 ), 2 ) . '%' );
				$discount_class     = 'ywcrpb_markup';
				$discount_text      = get_option( 'ywcrbp_total_markup_mess' );
				$discount_text      = str_replace( '{ywcrbp_total_markup}', $discount_formatted, $discount_text );
				$filter_id          = 'markup';
			}

			$discount_html = sprintf( '<span class="%s">%s</span>', $discount_class, $discount_text );

			return apply_filters( 'ywcrbp_get_total_' . $filter_id . '_html', $discount_html, $discount, $product );
		}

		/**
		 * check if the add to cart query string is valid
		 *
		 * @author Salvatore Strano
		 */
		public function validate_add_to_cart_action() {

			if ( ! empty( $_GET['add-to-cart'] ) ) {

				$this->init_user_info();

				if ( ! $this->user_role['show_add_to_cart'] ) {
					$url = remove_query_arg( array( 'add-to-cart', 'variation_id' ) );
					wc_add_notice( __( 'Sorry, this product cannot be purchased.', 'woocommerce' ), 'error' );

					wp_safe_redirect( $url );
					exit;
				}
			}
		}


		/**
		 * @param float $regular_price
		 * @param WC_Order_Item $item
		 *
		 * @return float;
		 */
		public function return_role_based_price_for_pdf( $regular_price, $item ) {

			$product = $item->get_product();

			if ( ! $product instanceof WC_Product_Gift_Card ) {

				$order   = $item->get_order();
				$user_id = $order->get_customer_id();

				$user = get_user_by( 'id', $user_id );

				if ( $user instanceof WP_User ) {
					$user_role               = apply_filters( 'yith_wcrbp_get_user_role', get_first_user_role( $user->roles ), $user_id );
					$this->user_role['role'] = $user_role;
				} else {

					$this->user_role['role'] = 'guest';
				}

				$role_price = $this->get_role_based_price( $product );

				if ( 'no_price' !== $role_price ) {
					$regular_price = $role_price;
				}

			}

			return $regular_price;
		}

		/**
		 * load global rules
		 */
		public function load_global_rules() {

			if ( count( $this->global_rules ) == 0 ) {

				$current_rule = $this->user_role['role'];

				$this->global_rules = YITH_Role_Based_Type()->get_price_rule_by_user_role( $current_rule, false );


			}
		}

		public function is_frontend() {

			return ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		}
	}
}

/**
 * @return YITH_Role_Based_Prices_Product
 * @since  1.0.0
 * @author YITHEMES
 */
function YITH_Role_Based_Prices_Product() {

	return YITH_Role_Based_Prices_Product::get_instance();
}

