<?php
/**
 * Frontend class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCP_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCP_Frontend {

		/**
		 * @var YITH_WCP
		 */
		protected $_wcp_object = null;

		/**
		 * @var YITH_WCP_Cart
		 */
		protected $_wcp_cart = null;

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct( $wcp_object ) {

			if ( get_option( 'yith_wcp_settings_enable_plugin_features' ) == 'no' ) {
				return;
			}

			$this->_wcp_object = $wcp_object;

			$wc_deprecated_filters = YITH_WCP::yit_wc_deprecated_filters();

			// Actions

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) , 999);

			add_action( 'woocommerce_yith-composite_add_to_cart' , array( $this, 'woocommerce_ywcp_add_to_cart' ) );

			$form_position = apply_filters( 'ywcp_component_list_position' , 'before' ) ;

			add_action( 'woocommerce_'.$form_position.'_add_to_cart_button', array( $this, 'show_component_list' ), 5 );

			add_filter( 'ywcp_post_per_page_items_number' , array( $this , 'post_per_page_items_number' ) , 10 , 3 );
			
			// Price

			if ( version_compare( WC()->version, '2.7', '<' ) ) {

				add_filter( 'woocommerce_get_price', array( $this, 'ywcp_woocommerce_get_price' ), 16, 2 );
				add_filter( 'woocommerce_get_regular_price', array( $this, 'ywcp_woocommerce_get_regular_price' ), 16, 2 );
				add_filter( 'woocommerce_get_sale_price', array( $this, 'ywcp_woocommerce_get_sale_price' ), 16, 2 );

			} else {

				add_filter( $wc_deprecated_filters['woocommerce_get_price'], array( $this, 'ywcp_woocommerce_get_price' ), 16, 2 );
				add_filter( $wc_deprecated_filters['woocommerce_get_regular_price'], array( $this, 'ywcp_woocommerce_get_regular_price' ), 16, 2 );
				add_filter( $wc_deprecated_filters['woocommerce_get_sale_price'], array( $this, 'ywcp_woocommerce_get_sale_price' ), 16, 2 );

			}

			add_filter( 'woocommerce_variation_prices_price', array( $this, 'ywcp_woocommerce_get_price' ), 16, 2 );
			add_filter( 'woocommerce_variation_prices_regular_price', array( $this, 'ywcp_woocommerce_get_regular_price' ), 16, 2 );
			add_filter( 'woocommerce_variation_prices_sale_price', array( $this, 'ywcp_woocommerce_get_sale_price' ), 16, 2 );

			// Product Variation

			add_action( 'woocommerce_before_cart', array( $this, 'my_woocommerce_variation_get_price' ), 16, 2 );
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'my_woocommerce_variation_get_price' ) );
			add_filter( 'woocommerce_get_price_html', array( $this, 'ywcp_woocommerce_get_price_html' ), 5 ,2 ) ;
			add_filter( 'woocommerce_get_variation_price_html', array( $this, 'ywcp_woocommerce_get_price_html' ), 5, 2 );
			add_filter( 'woocommerce_available_variation', array( $this, 'ywcp_woocommerce_available_variation' ), 10, 3 );
			add_filter( 'woocommerce_free_price_html', array( $this, 'ywcp_woocommerce_free_price_html' ), 10 ,2 );
			add_filter( 'woocommerce_get_variation_prices_hash', array( $this, 'ywcp_woocommerce_get_variation_prices_hash' ), 10 ,2 );

			// Ajax
			add_action( 'wc_ajax_ywcp_component_items_page_changed', array( $this, 'component_items_page_changed' ) );
			add_action( 'wc_ajax_ywcp_component_items_selected', array( $this, 'component_items_selected' ) );
			
			// cart
			$this->_wcp_cart = new YITH_WCP_Cart();

			// Disable shippings if virtual
			add_filter( 'woocommerce_product_needs_shipping', array( $this, 'check_composite_shipping' ), 10, 2 );
			add_filter( 'woocommerce_order_item_needs_processing', array( $this, 'check_composite_processing' ), 10, 3 );

			// add-ons
			add_filter( 'yith_wapo_product_type_list', array( $this, 'wapo_product_type_list' ) );

			// dynamic pricing
			add_filter( 'ywdpd_deny_apply_discount', array( $this, 'ywdpd_deny_apply_discount' ), 10, 2 );

			// YITH WAPO Loaded
			do_action( 'yith_wcp_frontend_loaded' );

			add_filter( 'woocommerce_cart_contents_count', array( $this, 'composite_cart_contents_count' ), 20 );

		}

		function composite_cart_contents_count() {
			$cart_items_count = 0;
		    foreach( WC()->cart->get_cart() as $cart_item ){
		    	$product = $cart_item['data'];
		    	$quantity = $cart_item['quantity'] > 0 ? $cart_item['quantity'] : 0;
		        if ( ! isset( $product->ywcp_composite_info ) ) { $cart_items_count = $cart_items_count + $quantity; }
		    }
		    return $cart_items_count;

		}

		public function getCartObject() {
			return $this->_wcp_cart;
		}
		
		/**
		 * Enqueue frontend styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public  function enqueue_styles_scripts() {

			if ( is_product() ) {

				global $post;

				if ( $post ) {
					$product = wc_get_product( $post->ID );
					if ( $product->get_type() != 'yith-composite' ) {
						return;
					}
				}

			}

			$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

			// css

			wp_register_style( 'yith_wcp_frontend', YITH_WCP_ASSETS_URL . '/css/yith-wcp-frontend.css' , false, $this->_wcp_object->version  );
			wp_enqueue_style( 'yith_wcp_frontend' );

			// js

			wp_register_script( 'yith_wcp_frontend-accounting', YITH_WCP_ASSETS_URL . '/js/accounting'. $suffix .'.js', '', '0.4.2', true );
			wp_register_script( 'yith_wcp_frontend', YITH_WCP_ASSETS_URL . '/js/yith-wcp-frontend'. $suffix .'.js', array( 'jquery', 'wc-add-to-cart-variation' ), $this->_wcp_object->version, true );
			wp_enqueue_script( 'yith_wcp_frontend-accounting' );
			wp_enqueue_script( 'yith_wcp_frontend' );

			$script_params = array(
				'ajax_url'                     => admin_url( 'admin-ajax' ) . '.php',
				'wc_ajax_url'                  => WC_AJAX::get_endpoint( "%%endpoint%%" ),
				'currency_format_num_decimals' => absint( get_option( 'woocommerce_price_num_decimals' ) ),
				'currency_format_symbol'       => get_woocommerce_currency_symbol(),
				'currency_format_decimal_sep'  => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
				'currency_format_thousand_sep' => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
				'currency_format'              => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
			);

			wp_localize_script( 'yith_wcp_frontend', 'yith_wcp_general', $script_params );

		}

		/**
		 *
		 */
		public function woocommerce_ywcp_add_to_cart() {
			wc_get_template( 'ywcp-add-to-cart.php',
				array(),
				'',
				YITH_WCP_TEMPLATE_FRONTEND_PATH );
		}

		/**
		 *	Show Components List
		 */
		public function show_component_list() {
			global $product;
			if ( is_object( $product ) && $product->get_type() =='yith-composite' ) {
				wc_get_template( 'ywcp-composite-list.php', array(), '', YITH_WCP_TEMPLATE_FRONTEND_PATH );
			}
		}

		/**
		 * @param $number
		 * @param $post_id
		 * @param $option_style
		 *
		 * @return int
		 */
		public function post_per_page_items_number( $number, $post_id, $option_style ) {

			if ( $post_id > 0 && $option_style == 'dropdowns' ) {
				$number = -1;
			}

			return $number;

		}

		/**
		 * @param     $post_id
		 * @param     $key
		 * @param     $wcp_data_single_item
		 * @param int $current_page
		 */
		public function printOptions( $post_id, $key, $wcp_data_single_item, $current_page = 1 ) {
			if ( $wcp_data_single_item['option_style'] ) {
				$item_number = apply_filters( 'ywcp_post_per_page_items_number', 5, $post_id, $wcp_data_single_item['option_style'] );
				$custom_order_by = isset( $wcp_data_single_item['product_order'] ) ? $wcp_data_single_item['product_order'] : 'menu_order';
				$custom_order = isset( $wcp_data_single_item['product_order_direction'] ) ? $wcp_data_single_item['product_order_direction'] : 'asc';
				$args = YITH_WCP()->getProductsQueryArgs( $post_id, $wcp_data_single_item, $item_number, $current_page, $custom_order_by, $custom_order );

				// Multi Vendor integration
				$tax_query = $this->filter_vendors_product( $post_id );
				if ( isset( $tax_query ) && is_array( $tax_query ) ) {
					$args['tax_query'] = $tax_query;
				}
				// End Multi Vendor integration

				$loop = new WP_Query( $args );
				$option_name = $this->getFormComponentName( $key );
				wc_get_template( 'component-item/ywcp-component-item-options-' . $wcp_data_single_item['option_style'] . '.php',
					array(
						'component_product_id'  => $post_id,
						'products_loop'         => $loop,
						'wcp_key'               => $key,
						'wcp_data_single_item'  => $wcp_data_single_item,
						'option_name'           => $option_name,
						'current_page'          => $current_page
					), '', YITH_WCP_TEMPLATE_FRONTEND_PATH );
				wp_reset_postdata();
			}
		}

		/**
		 * @param $post_id
		 *
		 * @return array|void
		 */
		public function filter_vendors_product( $post_id ) {

			if ( ! YITH_WCP()->_is_vendor_installed ) {
				return;
			}

			$vendor_object = YITH_WCP()->get_multivendor_by_id( $post_id , 'product' );

			if( is_object( $vendor_object ) ) {

				$tax_query = array(
					array(
						'taxonomy' => $vendor_object->taxonomy,
						'field'    => 'id',
						'terms'    => $vendor_object->term_id,
						'operator' => 'IN'
					)
				);

				return $tax_query;

			}


		}

		/**
		 * @param $key
		 *
		 * @return string
		 */
		public function getFormComponentName( $key ) {

			 return 'ywcp_selection['.$key.']';

		}

		/**
		 * @param     $wp_query
		 * @param int $current_page
		 */
		public function getNavigationLinks( $wp_query , $current_page = 1 ) {

			if ( $wp_query->max_num_pages <= 1 ) {
				return;
			}
			
			?>

			<nav class="woocommerce-pagination">
				<?php
				echo paginate_links( apply_filters( 'ywcp_woocommerce_pagination_args', array(
					'base'      => '#',
					'format'    => '',
					'add_args'  => false,
					'current'   => max( 1, $wp_query->query['paged'] ),
					'total'     => $wp_query->max_num_pages,
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
					'end_size'  => 3,
					'mid_size'  => 3
				) ) );
				?>
			</nav>

			<?php

		}

		/**
		 *
		 */
		public function component_items_page_changed() {

			if( isset( $_REQUEST['product_id'] ) && isset( $_REQUEST['key'] ) && isset( $_REQUEST['ywcp_current_page'] ) ) {

				$product_id = intval( $_REQUEST['product_id'] );

				if( $product_id > 0 ) {

					$key =  $_REQUEST['key'];

					$product = wc_get_product( $product_id );

					$product_parent_id = yit_get_base_product_id( $product );

					$components = $product->getComponentsData();

					$current_page = isset( $_REQUEST['ywcp_current_page'] ) ? intval ( $_REQUEST['ywcp_current_page'] ) : 1 ;

					if( $current_page  > 0 ) {

						$this->printOptions( $product_parent_id  , $key, $components[$key] , $current_page );

					}

				}

			}

			die();
		}

		/**
		 * 
		 */
		public function component_items_selected() {

			if( isset( $_REQUEST['master_id'] ) && isset( $_REQUEST['product_id'] ) && isset( $_REQUEST['key'] ) ) {

				$master_id = intval( $_REQUEST['master_id'] );
				$product_id = intval( $_REQUEST['product_id'] );

				if ( $product_id > 0 ) {

					$key = $_REQUEST['key'];

					if ( $key ) {

						$product = wc_get_product( $product_id );
						YITH_WCP_Frontend::markProductAsCompositeProcessed( $product, $master_id, $key );

						ob_start();
						wc_get_template( 'component-item/ywcp-component-item-selected.php',
							array(
								'composite_product' => wc_get_product( $master_id ),
								'product'           => $product,
								'key'               => $key,
								'post'              => get_post( $product_id ),
							),
							'',
							YITH_WCP_TEMPLATE_FRONTEND_PATH );
						$html = ob_get_clean();

						$attr_wccl = function_exists( 'YITH_WCCL_Frontend' ) ? YITH_WCCL_Frontend()->create_attributes_json( $product_id, true ) : '';

						wp_send_json( array( 'html' => $html, 'attr_wccl' => $attr_wccl ) );

					}

				}

			}

			die();
			
		}

		/**
		 * @param $product
		 * @param $composite_product_id
		 * @param $key
		 */
		public static function markProductAsCompositeProcessed( &$product, $composite_product_id, $key ) {

			$product->ywcp_composite_info = array(
				'composite_product_id' => $composite_product_id,
				'composite_item_key'   => $key
			);

		}

		/**
		 * @param $price
		 * @param $product
		 *
		 * @return float|int
		 */
		public function ywcp_woocommerce_get_price( $price, $product ) {

			// Variations Discount Fix
			$this->ywcp_variations_discount_fix( $product );

			$ywcp_composite_info = $this->getCompositeInfo( $product );

			if ( isset( $ywcp_composite_info ) && is_array( $ywcp_composite_info ) && isset( $ywcp_composite_info['composite_product_id'] ) && $ywcp_composite_info['composite_product_id'] > 0 ) {

				$composite_product = wc_get_product( $ywcp_composite_info['composite_product_id'] );

				if ( is_object( $composite_product ) ) {

					if ( method_exists( $composite_product, 'isPerItemPricing' ) && $composite_product->isPerItemPricing() ) {

						$wcp_component_item = $composite_product->getComponentItemByKey( $ywcp_composite_info['composite_item_key'] );

						$discount = isset( $wcp_component_item['discount'] ) ? $wcp_component_item['discount'] : 0;

						$product_sale_price = yit_get_prop( $product, '_sale_price', true, 'edit' );
						$product_regular_price = yit_get_prop( $product, '_regular_price', true, 'edit' );
						$product_price = yit_get_prop( $product, '_price', true, 'edit' );

						if ( $discount > 0 ) {

							$apply_discount_to_sale_price = $wcp_component_item['apply_discount_to_sale_price'];

							if ( $apply_discount_to_sale_price ) {
								$price = $product_sale_price > 0 ? $product_sale_price : $product_regular_price;
							} else {
								$price = $product_regular_price;
							}

							$price = empty( $price ) ? $price : round( ( double ) $price * ( 100 - $discount ) / 100, absint( get_option( 'woocommerce_price_num_decimals' ) ) );

						}

					} else {

						return ( double ) 0;

					}

				}

			}

			return $price;

		}

		public function my_woocommerce_variation_get_price() {

			add_filter( 'woocommerce_product_variation_get_price', array( $this, 'ywcp_woocommerce_get_price' ), 16, 2 );

		}

		/**
		 * @param $price
		 * @param $product
		 *
		 * @return float
		 */
		public function ywcp_woocommerce_get_regular_price( $price, $product ) {

			// Variations Discount Fix
			$this->ywcp_variations_discount_fix( $product );

			$ywcp_composite_info = $this->getCompositeInfo( $product );

			if( isset( $ywcp_composite_info ) && is_array( $ywcp_composite_info ) && isset( $ywcp_composite_info['composite_product_id'] ) && $ywcp_composite_info['composite_product_id'] > 0  ) {

				$composite_product = wc_get_product( $ywcp_composite_info['composite_product_id'] );

				if( is_object( $composite_product ) ) {

					if ( method_exists( $composite_product , 'isPerItemPricing' ) && $composite_product->isPerItemPricing() ) {

						$wcp_component_item = $composite_product->getComponentItemByKey( $ywcp_composite_info['composite_item_key'] );

						$discount = $wcp_component_item['discount'];

						$product_sale_price = yit_get_prop( $product , '_sale_price' , true , 'edit' );
						$product_regular_price = yit_get_prop( $product , '_regular_price' , true , 'edit' );
						$product_price = yit_get_prop( $product , '_price' , true , 'edit' );

						if ( $discount > 0 && ! empty( $product_sale_price ) && $wcp_component_item['apply_discount_to_sale_price']  ) {

							$price = $product_sale_price > 0 ? $product_sale_price : $product_price;

						} else if ( empty( $product_regular_price ) ) {

							$price = $product_price;

						}  else {

							$price = $product_regular_price ;

						}

					}
					else {

						return ( double ) 0;

					}

				}

			}

			return $price;
		}

		/**
		 * @param $price
		 * @param $product
		 *
		 * @return float
		 */
		public function ywcp_woocommerce_get_sale_price( $price, $product ) {

			// Variations Discount Fix
			$this->ywcp_variations_discount_fix( $product );

			$ywcp_composite_info = $this->getCompositeInfo( $product );

			if ( isset( $ywcp_composite_info ) && is_array( $ywcp_composite_info ) ) {

				$composite_product = wc_get_product( $ywcp_composite_info['composite_product_id'] );

				if ( is_object( $composite_product ) &&  $composite_product->is_type('yith-composite') ) {

					if ( $composite_product->isPerItemPricing() ) {

						$wcp_component_item = $composite_product->getComponentItemByKey( $ywcp_composite_info['composite_item_key'] );

						if ( $wcp_component_item['discount'] > 0 ) {

							$discount = $wcp_component_item['discount'];

							$price = $this->ywcp_woocommerce_get_price( $price, $product );
							
						}

					} else {

						return ( double ) 0;

					}

				}

			}

			return $price;

		}

		/**
		 * @param $price
		 * @param $product
		 *
		 * @return string
		 */
		public function ywcp_woocommerce_get_price_html( $price, $product ) {

			// Variations Discount Fix
			$this->ywcp_variations_discount_fix( $product );

			$ywcp_composite_info = $this->getCompositeInfo( $product );

			if ( isset( $ywcp_composite_info ) && is_array( $ywcp_composite_info ) ) {
				
				$composite_product = wc_get_product( $ywcp_composite_info['composite_product_id'] );

				if ( is_object( $composite_product ) ) {

					if ( ! $composite_product->isPerItemPricing() ) {

						return '';

					}

				}

			}

			return $price;

		}

		// Variations Discount Fix
		public function ywcp_variations_discount_fix( $product ) {

			global $ywcp_variations_discount_fix_info;

			if ( $product->get_type() == 'variation' && ! isset( $product->ywcp_composite_info ) ) {

				if ( isset( $_REQUEST['master_id'] ) && isset( $_REQUEST['key'] ) ) {

					$composite_product_id = $_REQUEST['master_id'];
					$composite_item_key = $_REQUEST['key'];

				} elseif ( isset( $ywcp_variations_discount_fix_info ) ) {

					$composite_product_id = $ywcp_variations_discount_fix_info['composite_product_id'];
					$composite_item_key = $ywcp_variations_discount_fix_info['composite_item_key'];

				}

				if ( isset( $composite_product_id ) && isset( $composite_item_key ) ) {

					$product->ywcp_composite_info = array(
						'composite_product_id' => $composite_product_id,
						'composite_item_key'   => $composite_item_key,
					);

					$composite_product = wc_get_product( $composite_product_id );
					$wcp_component_item = $composite_product->getComponentItemByKey( $composite_item_key );
					$discount = $wcp_component_item['discount'];
					// echo '<span class="variations_discount_fix" style="display: none;">' . $discount . '</span>';

				}

			}

		}

		/**
		 * @param $variation_data
		 * @param $product
		 * @param $variation
		 *
		 * @return mixed
		 */
		public function ywcp_woocommerce_available_variation( $variation_data, $product, $variation ) {

			$ywcp_composite_info = $this->getCompositeInfo( $product );

			if( isset( $ywcp_composite_info ) ) {

				$composite_product = wc_get_product( $ywcp_composite_info['composite_product_id'] );

				if( is_object( $composite_product ) ) {

					$wcp_component_item = $composite_product->getComponentItemByKey( $ywcp_composite_info['composite_item_key'] );

					// Add price data.
					$price_incl_tax                           = yit_get_price_including_tax( $variation, 1, 1000 );
					$price_excl_tax                           = yit_get_price_excluding_tax( $variation, 1, 1000 );

					$variation_data[ 'price' ]                = $variation->get_price();
					$variation_data[ 'regular_price' ]        = $variation->get_regular_price();

					$variation_data[ 'price_tax' ]            = $price_incl_tax / $price_excl_tax;

					$variation_data[ 'price_html' ]           = $composite_product->isPerItemPricing() ? ( $variation_data[ 'price_html' ] === '' ? '<span class="price">' . $variation->get_price_html() . '</span>' : $variation_data[ 'price_html' ] ) : '';

					$variation_data[ 'is_sold_individually' ] = $variation_data[ 'is_sold_individually' ] && $wcp_component_item[ 'min_quantity' ] == 1 ? true : false;

					$variation_data[ 'min_qty' ]              = $wcp_component_item[ 'min_quantity' ];
					$variation_data[ 'max_qty' ]              = $variation_data[ 'max_qty' ] === null ? '' : $variation_data[ 'max_qty' ];

					// Max variation quantity can't be greater than the component Max Quantity.
					if ( $wcp_component_item[ 'max_quantity' ] > 0 ) {
						$variation_data[ 'max_qty' ] = ( $variation_data[ 'max_qty' ] !== '' ) ? min( $wcp_component_item[ 'max_quantity' ], $variation_data[ 'max_qty' ] ) : $wcp_component_item[ 'max_quantity' ];
					}

					// Max variation quantity can't be lower than the min variation quantity - if it is, then the variation is not in stock.
					if ( $variation_data[ 'max_qty' ] !== '' ) {
						if ( $wcp_component_item[ 'min_quantity' ] > $variation_data[ 'max_qty' ] ) {
							$variation_data[ 'is_in_stock' ] = false;
							$variation_data[ 'max_qty' ]     = $wcp_component_item[ 'min_quantity' ];
						}
					}

				}

			}

			return $variation_data;
		}

		/**
		 * @param $product
		 *
		 * @return null
		 */
		private function getCompositeInfo( $product ) {

			$ywcp_composite_info = null;

			if ( isset( $product->ywcp_composite_info ) && !empty( $product->ywcp_composite_info ) ) {

				$ywcp_composite_info = $product->ywcp_composite_info;

			}
			else {
				if ( ( get_class( $product ) == 'WC_Product_Variation' ) ) {

					$product_parent_id = yit_get_base_product_id( $product );

					$product_parent = wc_get_product( $product_parent_id );

					if( ( is_object( $product_parent ) && isset( $product_parent->ywcp_composite_info ) && !empty( $product_parent->ywcp_composite_info ) ) ) {

						$ywcp_composite_info = $product_parent->ywcp_composite_info;

					}
				}
			}

			return $ywcp_composite_info;
		}

		/**
		 * @param $price
		 * @param $product
		 *
		 * @return string
		 */
		public function ywcp_woocommerce_free_price_html( $price, $product ) {

			if ( $product->get_type() == 'yith-composite' ) {
				return '';
			}
			else {
				return $price;
			}

		}

		/**
		 * @param $price_hash
		 * @param $product
		 *
		 * @return mixed
		 */
		function ywcp_woocommerce_get_variation_prices_hash( $price_hash, $product ) {
			$transient_name = 'wc_var_prices_' . yit_get_base_product_id( $product );
			set_transient( $transient_name, false );
			return $price_hash;
		}

		/**
		 * @param $allows_type
		 *
		 * @return array
		 */
		public function wapo_product_type_list( $allows_type ) {

			$allows_type =  array_merge( $allows_type, array('yith-composite') );

			return $allows_type;

		}

		/**
		 * @param $deny
		 * @param $product
		 *
		 * @return bool
		 */
		public function ywdpd_deny_apply_discount( $deny, $product ) {

			if ( ! $deny ) {

				return ( isset( $product->ywcp_composite_info ) && is_array( $product->ywcp_composite_info ) );

			}

			return $deny;

		}

		/**
		 * @param $product
		 * @return string
		 */
		public static function getAvailabilityHtml( $product ) {

			$availability = $product->get_availability();

			$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . wp_kses_post( $availability['availability'] ) . '</p>';
			$availability_html = apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );

			return $availability_html;

		}

		/**
		 * @param $product
		 * @return string
		 */
		public static function getAvailabilityText( $product ) {

			$availability = $product->get_availability();

			$availability_text = empty( $availability['availability'] ) ? '' : $availability['availability'];

			return $availability_text;

		}

		/**
		 * Disable shipping if virtual
		 */
		function check_composite_shipping( $needs_shipping, $product ) {
			if ( $product->is_type( 'yith-composite' ) && $product->is_virtual() ) {
				$needs_shipping = false;
			}
			return $needs_shipping;
		}

		function check_composite_processing( $virtual_downloadable_item, $product, $this_get_id ) {
			if ( $product->is_type( 'yith-composite' ) && $product->is_virtual() && $product->is_downloadable() ) {
				return false;
			}
			return true;
		}
		
	}

}
