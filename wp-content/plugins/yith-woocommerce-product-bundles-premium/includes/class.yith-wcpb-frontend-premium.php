<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_WCPB_PREMIUM' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of YITH WooCommerce Product Bundles
 *
 * @class   YITH_WCPB_Frontend_Premium
 * @package YITH WooCommerce Product Bundles
 * @since   1.0.0
 * @author  Yithemes
 */


if ( ! class_exists( 'YITH_WCPB_Frontend_Premium' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCPB_Frontend_Premium extends YITH_WCPB_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCPB_Frontend_Premium
		 * @since 1.0.0
		 */
		protected static $_instance;

		private $check_cart_contents = false;

		public $hide_bundle_items_in_cart = false;

		public $show_download_links_in_bundle = false;

		public $show_item_prices_in_cart_and_checkout = false;

		public $pip_bundle_order_pricing;
		public $order_price_in_bundle_pip;

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			parent::__construct();

			$this->init();

			add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'woocommerce_cart_item_thumbnail' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_visible', array( $this, 'woocommerce_cart_item_visible' ), 10, 2 );
			add_filter( 'woocommerce_checkout_cart_item_visible', array( $this, 'woocommerce_cart_item_visible' ), 10, 2 );
			add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'woocommerce_cart_item_visible' ), 10, 2 );
			add_filter( 'woocommerce_order_item_visible', array( $this, 'woocommerce_cart_item_visible' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_name', array( $this, 'woocommerce_cart_item_name' ), 10, 3 );

			add_filter( 'woocommerce_empty_price_html', array( $this, 'woocommerce_empty_price_html' ), 15, 2 );

			add_action( 'widgets_init', array( $this, 'register_widgets' ) );

			add_action( 'wp_ajax_yith_wcpb_get_bundle_total_price', array( $this, 'ajax_get_bundle_total_price' ) );
			add_action( 'wp_ajax_nopriv_yith_wcpb_get_bundle_total_price', array( $this, 'ajax_get_bundle_total_price' ) );

			add_action( 'woocommerce_after_calculate_totals', array( $this, 'woocommerce_check_cart_items' ) );
			YITH_WCPB_Shortcodes();

			add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'woocommerce_coupon_is_valid_for_product' ), 10, 4 );

			add_filter( 'woocommerce_order_item_visible', array( $this, 'hide_hidden_bundled_items_in_orders' ), 10, 2 );

			/**
			 * issue with dynamic pricing
			 * set the price of bundled items to zero (FIXED Bundle)
			 *
			 * @since 1.1.3
			 */
			$get_price_hook = version_compare( WC()->version, '2.7', '>=' ) ? 'woocommerce_product_get_price' : 'woocommerce_get_price';
			add_filter( $get_price_hook, array( $this, 'set_bundled_item_price_zero' ), 99, 2 );

			// add-ons
			add_filter( 'yith_wapo_product_type_list', array( $this, 'wapo_product_type_list' ) );

			if ( $this->show_download_links_in_bundle ) {
				add_action( 'woocommerce_order_item_meta_end', array( $this, 'show_download_links_in_bundle_order_details' ), 10, 4 );
			}

		}

		public function init() {
			$this->hide_bundle_items_in_cart             = get_option( 'yith-wcpb-hide-bundled-items-in-cart', 'no' ) === 'yes';
			$this->pip_bundle_order_pricing              = get_option( 'yith-wcpb-pip-bundle-order-pricing', 'price-in-bundle' );
			$this->show_item_prices_in_cart_and_checkout = get_option( 'yith-wcpb-show-bundled-item-prices', 'no' ) === 'yes';
			$this->order_price_in_bundle_pip             = $this->pip_bundle_order_pricing === 'price-in-bundle';
			$this->show_download_links_in_bundle         = $this->hide_bundle_items_in_cart;
		}

		/**
		 * Hide hidden bundled items in order tables
		 *
		 * @param bool  $visible
		 * @param array $item
		 *
		 * @return bool
		 * @since 1.1.5
		 */
		public function hide_hidden_bundled_items_in_orders( $visible, $item ) {
			if ( isset( $item['_yith_wcpb_hidden'] ) && $item['_yith_wcpb_hidden'] ) {
				$visible = false;
			}

			return $visible;
		}


		/**
		 * Show download link of the bundled items in the bundle
		 * in Order detail table (frontend and email)
		 *
		 * @param int      $item_id
		 * @param array    $item
		 * @param WC_Order $order
		 * @param string   $plain_text
		 *
		 * @since 1.1.4
		 */
		public function show_download_links_in_bundle_order_details( $item_id, $item, $order, $plain_text = '' ) {
			if ( isset( $item['cartstamp'] ) && isset( $item['yith_bundle_cart_key'] ) ) {
				$bundle_key  = $item['yith_bundle_cart_key'];
				$order_items = $order->get_items();
				foreach ( $order_items as $order_item ) {
					if ( isset( $order_item['bundled_by'] ) && $bundle_key === $order_item['bundled_by'] ) {
						if ( function_exists( 'wc_display_item_downloads' ) ) {
							wc_display_item_downloads(
								$order_item,
								array(
									'before'    => '<br /><small>',
									'after'     => '</small>',
									'separator' => '</small><br /><small>',
								)
							);
						} else {
							$order->display_item_downloads( $order_item );
						}
					}
				}
			}
		}


		/**
		 * Coupon for bundle product
		 *
		 * @param bool       $valid
		 * @param WC_Product $product
		 * @param WC_Coupon  $coupon
		 * @param array      $values
		 *
		 * @return mixed
		 */
		public function woocommerce_coupon_is_valid_for_product( $valid, $product, $coupon, $values ) {
			if ( isset( $values['bundled_by'] ) ) {
				$bundle_cart_item = WC()->cart->get_cart_item( $values['bundled_by'] );
				if ( $bundle_cart_item && isset( $bundle_cart_item['data'] ) ) {
					$bundle = $bundle_cart_item['data'];

					return $coupon->is_valid_for_product( $bundle, $bundle_cart_item );
				}
			}

			return $valid;
		}

		/**
		 * before creating order with bundle product
		 * edit price and tax in cart for bundle product and its items
		 *
		 * @param WC_Cart $cart
		 */
		public function woocommerce_check_cart_items( $cart ) {
			$cart_contents = $cart->cart_contents;

			foreach ( $cart_contents as $item_key => $item ) {
				if ( isset( $item['cartstamp'] ) && isset( $item['bundled_items'] ) && isset( $item['data'] ) && $item['data']->per_items_pricing ) {
					if ( ! apply_filters( 'yith_wcpb_woocommerce_check_cart_items_for_bundle', true, $item ) ) {
						continue;
					}

					// BUNDLE
					$bundled_items = $item['bundled_items'];

					$line_total        = 0;
					$line_subtotal     = 0;
					$line_tax          = 0;
					$line_subtotal_tax = 0;
					$line_tax_data     = array(
						'total'    => array(),
						'subtotal' => array(),
					);

					foreach ( $bundled_items as $bundled_item_key ) {
						if ( isset( $cart_contents[ $bundled_item_key ] ) ) {
							$bundled_item = $cart_contents[ $bundled_item_key ];
							if ( isset( $bundled_item['line_total'] ) ) {
								$line_total                += $bundled_item['line_total'];
								$bundled_item['line_total'] = 0;
							}
							if ( isset( $bundled_item['line_subtotal'] ) ) {
								$line_subtotal                += $bundled_item['line_subtotal'];
								$bundled_item['line_subtotal'] = 0;
							}
							if ( isset( $bundled_item['line_tax'] ) ) {
								$line_tax                += $bundled_item['line_tax'];
								$bundled_item['line_tax'] = 0;
							}
							if ( isset( $bundled_item['line_subtotal_tax'] ) ) {
								$line_subtotal_tax                += $bundled_item['line_subtotal_tax'];
								$bundled_item['line_subtotal_tax'] = 0;
							}
							if ( isset( $bundled_item['line_tax_data'] ) ) {
								$bundle_tax_data = $bundled_item['line_tax_data'];
								foreach ( $bundle_tax_data as $type => $values ) {
									foreach ( $values as $t_index => $t_value ) {
										$line_tax_data[ $type ][ $t_index ] = isset( $line_tax_data[ $type ][ $t_index ] ) ? $line_tax_data[ $type ][ $t_index ] + $t_value : $t_value;
									}
								}
								$bundled_item['line_tax_data'] = array(
									'total'    => array( 0 ),
									'subtotal' => array( 0 ),
								);
							}
							if ( $this->order_price_in_bundle_pip ) {
								$cart_contents[ $bundled_item_key ] = $bundled_item;
							}
						}
					}
					if ( $this->order_price_in_bundle_pip ) {
						$cart_contents[ $item_key ]['line_tax']          = $line_tax;
						$cart_contents[ $item_key ]['line_subtotal_tax'] = $line_subtotal_tax;
						$cart_contents[ $item_key ]['line_tax_data']     = $line_tax_data;
						$cart_contents[ $item_key ]['line_total']        = $line_total;
						$cart_contents[ $item_key ]['line_subtotal']     = $line_subtotal;
					} else {
						$cart_contents[ $item_key ]['yith_bundle_totals']['line_tax']          = $line_tax;
						$cart_contents[ $item_key ]['yith_bundle_totals']['line_subtotal_tax'] = $line_subtotal_tax;
						$cart_contents[ $item_key ]['yith_bundle_totals']['line_tax_data']     = $line_tax_data;
						$cart_contents[ $item_key ]['yith_bundle_totals']['line_total']        = $line_total;
						$cart_contents[ $item_key ]['yith_bundle_totals']['line_subtotal']     = $line_subtotal;
					}
				}
			}

			$this->check_cart_contents = $cart_contents;
			$cart->cart_contents       = $cart_contents;
		}


		public function ajax_get_bundle_total_price() {
			if ( isset( $_POST['bundle_id'] ) ) {

				$product = wc_get_product( $_POST['bundle_id'] );
				if ( $product instanceof WC_Product && ! $product->is_type( 'yith_bundle' ) ) {
					die();
				}

				/**
				 * @var WC_Product_Yith_Bundle $product
				 */

				$array_qty = isset( $_POST['array_qty'] ) ? $_POST['array_qty'] : array();
				$array_opt = isset( $_POST['array_opt'] ) ? $_POST['array_opt'] : array();
				$array_var = isset( $_POST['array_var'] ) ? $_POST['array_var'] : array();

				$price      = $product->get_per_item_price_tot_with_params( $array_qty, $array_opt, $array_var, false );
				$price_html = wc_price( $price );
				$price_html = apply_filters( 'yith_wcpb_ajax_get_bundle_total_price', $price_html, $price, $product );

				$response = compact( 'price', 'price_html' );
				$response = apply_filters( 'yith_wcpb_ajax_get_bundle_total_price_response', $response, $product );

				wp_send_json( $response );
			}
			die();
		}

		/**
		 * register Widget for bundle products
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function register_widgets() {
			register_widget( 'YITH_WCPB_Bundle_Widget' );
		}

		/**
		 * hide thumbnail in cart if it's requested
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_cart_item_thumbnail( $thumbnail, $cart_item, $cart_item_key ) {
			if ( ! isset( $cart_item['bundled_by'] ) ) {
				return $thumbnail;
			}

			$hide_thumbnail = isset( $cart_item['yith_wcpb_hide_thumbnail'] ) ? $cart_item['yith_wcpb_hide_thumbnail'] : 0;

			if ( $hide_thumbnail ) {
				return '';
			}

			return $thumbnail;
		}

		/**
		 * hide item in cart and cart widget if it's requested
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_cart_item_visible( $value, $cart_item ) {
			if ( ! isset( $cart_item['bundled_by'] ) ) {
				return $value;
			}

			if ( $this->hide_bundle_items_in_cart ) {
				return false;
			}

			$hidden = isset( $cart_item['yith_wcpb_hidden'] ) ? $cart_item['yith_wcpb_hidden'] : 0;
			if ( $hidden ) {
				return false;
			}

			return true;
		}

		/**
		 * Modify title of bundled products
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_cart_item_name( $title, $cart_item, $cart_item_key ) {
			if ( ! isset( $cart_item['bundled_by'] ) ) {
				return $title;
			}

			/** @var WC_Product_Yith_Bundle $_product */
			$_product = $cart_item['data'];

			$custom_title = isset( $cart_item['yith_wcpb_title'] ) ? $cart_item['yith_wcpb_title'] : $title;

			if ( $_product instanceof WC_Data && $_product->is_type( 'variation' ) && $attributes = (array) $_product->get_attributes() ) {
				$include_attribute_names = false;
				// Determine whether to include attribute names through counting the number of one-word attribute values.
				$one_word_attributes = 0;
				foreach ( $attributes as $name => $value ) {
					if ( false === strpos( $value, '-' ) ) {
						++ $one_word_attributes;
					}
					if ( $one_word_attributes > 1 ) {
						$include_attribute_names = true;
						break;
					}
				}

				$include_attribute_names = apply_filters( 'woocommerce_product_variation_title_include_attribute_names', $include_attribute_names, $_product );
				$title_base_text         = $custom_title;
				$title_attributes_text   = wc_get_formatted_variation( $_product, true, $include_attribute_names );
				$separator               = ! empty( $title_attributes_text ) ? ' &ndash; ' : '';

				$custom_title = apply_filters(
					'woocommerce_product_variation_title',
					$title_base_text . $separator . $title_attributes_text,
					$_product,
					$title_base_text,
					$title_attributes_text
				);
			}

			if ( $_product->is_visible() ) {
				$custom_title = '<a href="' . $_product->get_permalink() . '">' . $custom_title . ' </a>';
			}

			return $custom_title;
		}

		/**
		 * get template for Bundle Product add to cart in product page [PREMIUM]
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_yith_bundle_add_to_cart() {
			/** @var WC_Product_Yith_Bundle $product */
			global $product;
			$bundled_items = $product->get_bundled_items();
			wc_get_template(
				'single-product/add-to-cart/yith-bundle.php',
				array(
					'available_variations' => $product->get_available_bundle_variations(),
					'attributes'           => $product->get_bundle_variation_attributes(),
					'selected_attributes'  => $product->get_selected_bundle_variation_attributes(),
					'bundled_items'        => $bundled_items,
				),
				'',
				YITH_WCPB_TEMPLATE_PATH . '/premium/'
			);
		}


		/**
		 * create item data [create the cartstamp if not exist]
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_add_cart_item_data( $cart_item_data, $product_id ) {
			/** @var WC_Product_Yith_Bundle $product */
			$product = wc_get_product( $product_id );

			if ( empty( $product ) ) {
				return $cart_item_data;
			}

			$add_cart_item_data_check = $product->is_type( 'yith_bundle' ) && ( ! isset( $cart_item_data['cartstamp'] ) || ! isset( $cart_item_data['bundled_items'] ) );
			$add_cart_item_data_check = apply_filters( 'yith_wcpb_add_cart_item_data_check', $add_cart_item_data_check, $cart_item_data, $product_id );

			if ( ! $add_cart_item_data_check ) {
				return $cart_item_data;
			}

			do_action( 'yith_wcpb_before_add_cart_item_bundle_data' );

			if ( ! ! $bundled_items = $product->get_bundled_items() ) {
				$bundle_add_to_cart_params = isset( $cart_item_data['yith-bundle-add-to-cart-params'] ) ? $cart_item_data['yith-bundle-add-to-cart-params'] : $_REQUEST;

				$cartstamp = array();

				foreach ( $bundled_items as $bundled_item_id => $bundled_item ) {
					if ( ! $bundled_item instanceof YITH_WC_Bundled_Item ) {
						continue;
					}

					$bundled_optional_checked = isset( $bundle_add_to_cart_params[ 'yith_bundle_optional_' . $bundled_item_id ] ) ? true : false;
					if ( $bundled_item->is_optional() && ! $bundled_optional_checked ) {
						continue;
					}

					$bundled_product_quantity = isset( $bundle_add_to_cart_params[ 'yith_bundle_quantity_' . $bundled_item_id ] ) ? absint( $bundle_add_to_cart_params[ 'yith_bundle_quantity_' . $bundled_item_id ] ) : $bundled_item->get_quantity();

					if ( ! $bundled_product_quantity ) {
						continue;
					}

					$id                   = $bundled_item->product_id;
					$bundled_item_product = wc_get_product( $id );
					$bundled_product_type = $bundled_item->product->get_type();

					$cartstamp[ $bundled_item_id ]['product_id']     = $id;
					$cartstamp[ $bundled_item_id ]['type']           = $bundled_product_type;
					$cartstamp[ $bundled_item_id ]['quantity']       = $bundled_product_quantity;
					$cartstamp[ $bundled_item_id ]['hide_thumbnail'] = $bundled_item->hide_thumbnail;
					$cartstamp[ $bundled_item_id ]['hidden']         = $bundled_item->is_hidden();
					$cartstamp[ $bundled_item_id ]['title']          = $bundled_item->title;
					$cartstamp[ $bundled_item_id ]['discount']       = $bundled_item->discount;

					// VARIABLE
					if ( $bundled_product_type === 'variable' ) {
						if ( isset( $cart_item_data['cartstamp'][ $bundled_item_id ]['attributes'] ) && isset( $_GET['order_again'] ) ) {
							$cartstamp[ $bundled_item_id ]['attributes']   = $cart_item_data['cartstamp'][ $bundled_item_id ]['attributes'];
							$cartstamp[ $bundled_item_id ]['variation_id'] = $cart_item_data['cartstamp'][ $bundled_item_id ]['variation_id'];
							continue;
						}

						$attr_stamp = array();
						$attributes = (array) maybe_unserialize( yit_get_prop( $bundled_item_product, '_product_attributes', true ) );

						foreach ( $attributes as $attribute ) {
							if ( ! empty( $attribute['is_variation'] ) ) {
								$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

								if ( isset( $bundle_add_to_cart_params[ 'yith_bundle_' . $taxonomy . '_' . $bundled_item_id ] ) ) {
									$value = sanitize_title( trim( stripslashes( $bundle_add_to_cart_params[ 'yith_bundle_' . $taxonomy . '_' . $bundled_item_id ] ) ) );

									if ( $attribute['is_taxonomy'] ) {
										$attr_stamp[ $taxonomy ] = $value;
									} else {
										// For custom attributes, get the name from the slug
										$options = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
										foreach ( $options as $option ) {
											if ( sanitize_title( $option ) == $value ) {
												$value = $option;
												break;
											}
										}
										$attr_stamp[ $taxonomy ] = $value;
									}
								}
							}
						}
						$cartstamp[ $bundled_item_id ]['attributes'] = $attr_stamp;

						if ( isset( $bundle_add_to_cart_params[ 'yith_bundle_variation_id_' . $bundled_item_id ] ) ) {
							$cartstamp[ $bundled_item_id ]['variation_id'] = $bundle_add_to_cart_params[ 'yith_bundle_variation_id_' . $bundled_item_id ];
						}
					}

					$cartstamp[ $bundled_item_id ] = apply_filters( 'woocommerce_yith_bundled_item_cart_item_identifier', $cartstamp[ $bundled_item_id ], $bundled_item_id );
				}

				$cart_item_data['cartstamp']     = $cartstamp;
				$cart_item_data['bundled_items'] = array();
			}

			do_action( 'yith_wcpb_after_add_cart_item_bundle_data' );

			return $cart_item_data;
		}

		/**
		 * Add to cart for Product Bundle
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

			if ( isset( $cart_item_data['cartstamp'] ) && ! isset( $cart_item_data['bundled_by'] ) ) {
				do_action( 'yith_wcpb_before_bundle_woocommerce_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data );
				$bundled_items_cart_data = array(
					'bundled_by' => $cart_item_key,
				);

				foreach ( $cart_item_data['cartstamp'] as $bundled_item_id => $bundled_item_stamp ) {
					$bundled_item_cart_data                             = $bundled_items_cart_data;
					$bundled_item_cart_data['bundled_item_id']          = $bundled_item_id;
					$bundled_item_cart_data['discount']                 = $bundled_item_stamp['discount'];
					$bundled_item_cart_data['yith_wcpb_hide_thumbnail'] = $bundled_item_stamp['hide_thumbnail'];
					$bundled_item_cart_data['yith_wcpb_hidden']         = $bundled_item_stamp['hidden'];
					$bundled_item_cart_data['yith_wcpb_title']          = $bundled_item_stamp['title'];

					$item_quantity        = $bundled_item_stamp['quantity'];
					$i_quantity           = $item_quantity * $quantity;
					$prod_id              = $bundled_item_stamp['product_id'];
					$bundled_product_type = $bundled_item_stamp['type'];

					if ( $bundled_product_type === 'simple' ) {
						$variation_id = '';
						$variations   = array();
					} elseif ( $bundled_product_type === 'variable' ) {
						$variation_id = $bundled_item_stamp['variation_id'];
						$variations   = $bundled_item_stamp['attributes'];
					}

					$bundled_item_cart_key = $this->bundled_add_to_cart( $product_id, $prod_id, $i_quantity, $variation_id, $variations, $bundled_item_cart_data );

					if ( ! isset( WC()->cart->cart_contents[ $cart_item_key ]['bundled_items'] ) || ! is_array( WC()->cart->cart_contents[ $cart_item_key ]['bundled_items'] ) ) {
						WC()->cart->cart_contents[ $cart_item_key ]['bundled_items'] = array();
					}

					if ( $bundled_item_cart_key && ! in_array( $bundled_item_cart_key, WC()->cart->cart_contents[ $cart_item_key ]['bundled_items'] ) ) {
						WC()->cart->cart_contents[ $cart_item_key ]['bundled_items'][] = $bundled_item_cart_key;
						WC()->cart->cart_contents[ $cart_item_key ]['yith_parent']     = $cart_item_key;
					}
				}
				do_action( 'yith_wcpb_after_bundle_woocommerce_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data );
			}
		}


		/**
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_add_cart_item( $cart_item, $cart_key ) {
			$cart_contents = WC()->cart->cart_contents;

			if ( isset( $cart_item['cartstamp'] ) && $cart_item['data']->per_items_pricing ) {
				/** wc2.7 */
				yit_set_prop( $cart_item['data'], 'price', 0 );
			}

			if ( isset( $cart_item['bundled_by'] ) ) {
				do_action( 'yith_wcpb_before_bundle_woocommerce_add_cart_item' );
				$bundle_cart_key = $cart_item['bundled_by'];
				if ( isset( $cart_contents[ $bundle_cart_key ] ) ) {
					$parent = $cart_contents[ $bundle_cart_key ]['data'];

					if ( ! $parent->per_items_pricing ) {
						yit_set_prop( $cart_item['data'], 'price', 0 );
						yit_set_prop( $cart_item['data'], 'bundled_item_price_zero', true );
					} else {
						$discount             = floatval( $cart_item['discount'] ) * floatval( $cart_item['data']->get_regular_price() ) / 100;
						$discount_filter_args = array(
							'cart_item'        => $cart_item,
							'bundle_cart_item' => $cart_contents[ $bundle_cart_key ],
						);
						$discount             = apply_filters(
							'yith_wcpb_bundled_item_calculated_discount',
							$discount,
							$cart_item['discount'],
							$cart_item['data']->get_regular_price(),
							$cart_item['data']->get_id(),
							$discount_filter_args
						);
						$price                = floatval( $cart_item['data']->get_regular_price() ) - $discount;
						$price                = yith_wcpb_round_bundled_item_price( $price );
						yit_set_prop( $cart_item['data'], 'price', $price );
					}

					yit_set_prop( $cart_item['data'], 'bundled_item_price', yit_get_prop( $cart_item['data'], 'price', true, 'edit' ) );
				}

				do_action( 'yith_wcpb_after_bundle_woocommerce_add_cart_item' );
			}

			return $cart_item;
		}

		/**
		 * Set bundle item price to zero if bundled_item_price_zero = true
		 * (only for Fixed bundle)
		 *
		 * @param $price
		 * @param $product
		 *
		 * @return int
		 */
		public function set_bundled_item_price_zero( $price, $product ) {

			if ( isset( $product->bundled_item_price_zero ) && yit_get_prop( $product, 'bundled_item_price_zero', true ) ) {
				return 0;
			}

			return $price;
		}

		/**
		 * get cart item from session
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_get_cart_item_from_session( $cart_item, $item_session_values, $cart_item_key ) {
			$cart_contents = ! empty( WC()->cart ) ? WC()->cart->cart_contents : '';
			if ( isset( $item_session_values['bundled_items'] ) && ! empty( $item_session_values['bundled_items'] ) ) {
				$cart_item['bundled_items'] = $item_session_values['bundled_items'];
			}

			if ( isset( $item_session_values['cartstamp'] ) ) {
				/** wc2.7 */
				if ( $cart_item['data']->per_items_pricing ) {
					yit_set_prop( $cart_item['data'], 'price', 0 );
				}

				$cart_item['cartstamp'] = $item_session_values['cartstamp'];
				do_action( 'yith_wcpb_after_bundle_woocommerce_get_cart_item_from_session_bundle', $cart_item );
			}

			if ( isset( $item_session_values['bundled_by'] ) ) {
				do_action( 'yith_wcpb_before_bundle_woocommerce_get_cart_item_from_session_bundled_by' );
				$cart_item['bundled_by']      = $item_session_values['bundled_by'];
				$cart_item['bundled_item_id'] = $item_session_values['bundled_item_id'];
				$bundle_cart_key              = $cart_item['bundled_by'];

				if ( isset( $cart_contents[ $bundle_cart_key ] ) ) {
					$parent          = $cart_contents[ $bundle_cart_key ]['data'];
					$bundled_item_id = $cart_item['bundled_item_id'];
					if ( $parent->per_items_pricing == false ) {
						yit_set_prop( $cart_item['data'], 'price', 0 );
						yit_set_prop( $cart_item['data'], 'bundled_item_price_zero', true );
					} else {
						$discount = isset( $cart_item['discount'] ) ? floatval( $cart_item['discount'] ) * $cart_item['data']->get_regular_price() / 100 : 0;

						$discount_filter_args = array(
							'cart_item'        => $cart_item,
							'bundle_cart_item' => $cart_contents[ $bundle_cart_key ],
						);
						$discount             = (float) apply_filters(
							'yith_wcpb_bundled_item_calculated_discount',
							$discount,
							isset( $cart_item['discount'] ) ? $cart_item['discount'] : 0,
							$cart_item['data']->get_regular_price(),
							$cart_item['data']->get_id(),
							$discount_filter_args
						);
						$price                = $cart_item['data']->get_regular_price() - $discount;
						$price                = yith_wcpb_round_bundled_item_price( $price );
						yit_set_prop( $cart_item['data'], 'price', $price );
					}
					yit_set_prop( $cart_item['data'], 'bundled_item_price', yit_get_prop( $cart_item['data'], 'price', true, 'edit' ) );
				}
				do_action( 'yith_wcpb_after_bundle_woocommerce_get_cart_item_from_session_bundled_by', $cart_item );
			}

			return apply_filters( 'yith_wcpb_woocommerce_get_cart_item_from_session', $cart_item, $item_session_values, $cart_item_key );
		}


		/**
		 * remove cart item price for bundled product
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_cart_item_price( $price, $cart_item, $cart_item_key ) {
			do_action( 'yith_wcpb_before_bundle_woocommerce_cart_item_price' );

			if ( isset( $cart_item['bundled_by'] ) ) {
				$bundle_key                 = $cart_item['bundled_by'];
				$bundle                     = isset( WC()->cart->cart_contents[ $bundle_key ] ) && isset( WC()->cart->cart_contents[ $bundle_key ]['data'] ) ? WC()->cart->cart_contents[ $bundle_key ]['data'] : false;
				$bundle_is_per_item_pricing = $bundle && ! empty( $bundle->per_items_pricing );
				if ( ! $bundle_is_per_item_pricing || ! $this->show_item_prices_in_cart_and_checkout ) {
					$price = '';
				}
			} elseif ( isset( $cart_item['bundled_items'] ) ) {
				if ( $cart_item['data']->per_items_pricing == true ) {
					$bundled_items_price = $this->calculate_bundled_items_price_by_cart( $cart_item );
					$price               = wc_price( $bundled_items_price );
					$price               = apply_filters( 'yith_wcpb_woocommerce_cart_item_price', $price, $bundled_items_price, $cart_item, $cart_item_key );
				}
			}

			do_action( 'yith_wcpb_after_bundle_woocommerce_cart_item_price' );

			return $price;
		}

		/**
		 * Calculate the price of the bundle by information in cart
		 *
		 * @param array $cart_item
		 *
		 * @return int
		 */
		public function calculate_bundled_items_price_by_cart( $cart_item ) {
			if ( isset( $cart_item['bundled_items'] ) && $cart_item['data']->per_items_pricing ) {
				$bundled_items_price = 0;

				$bundle_quantity = intval( $cart_item['quantity'] );

				foreach ( $cart_item['bundled_items'] as $bundled_item_key ) {
					if ( ! isset( WC()->cart->cart_contents[ $bundled_item_key ] ) ) {
						continue;
					}

					$item_values = WC()->cart->cart_contents[ $bundled_item_key ];
					$product     = $item_values['data'];

					$item_quantity = intval( $item_values['quantity'] / $bundle_quantity );
					/**
					 * fixed for Role Based integration
					 *
					 * @since 1.1.1
					 */
					$discount             = isset( $item_values['discount'] ) ? $item_values['discount'] * $product->get_regular_price() / 100 : 0;
					$discount_filter_args = array(
						'cart_item'        => $item_values,
						'bundle_cart_item' => $cart_item,
					);
					$discount             = (float) apply_filters(
						'yith_wcpb_bundled_item_calculated_discount',
						$discount,
						isset( $item_values['discount'] ) ? $item_values['discount'] : 0,
						$product->get_regular_price(),
						$product->get_id(),
						$discount_filter_args
					);
					$bundled_item_price   = ( $product->get_regular_price() - $discount ) * $item_quantity;
					$bundled_item_price   = yith_wcpb_get_price_to_display( $product, $bundled_item_price );
					$bundled_item_price   = yith_wcpb_round_bundled_item_price( $bundled_item_price );

					$bundled_items_price += $bundled_item_price;
				}

				$price = $bundled_items_price;

				return apply_filters( 'yith_wcpb_calculate_bundle_price_by_cart', $price, $cart_item );
			} else {
				return $cart_item['data']->get_price();
			}
		}

		/**
		 * remove cart item subtotal for bundled product
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function bundles_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
			do_action( 'yith_wcpb_before_bundle_bundles_item_subtotal' );

			if ( isset( $cart_item['bundled_by'] ) ) {
				$bundle_key                 = $cart_item['bundled_by'];
				$bundle                     = isset( WC()->cart->cart_contents[ $bundle_key ] ) && isset( WC()->cart->cart_contents[ $bundle_key ]['data'] ) ? WC()->cart->cart_contents[ $bundle_key ]['data'] : false;
				$bundle_is_per_item_pricing = $bundle && ! empty( $bundle->per_items_pricing );
				if ( ! $bundle_is_per_item_pricing || ! $this->show_item_prices_in_cart_and_checkout ) {
					$subtotal = '';
				}
			} elseif ( isset( $cart_item['bundled_items'] ) ) {
				$is_per_item_pricing = isset( $cart_item['data']->per_items_pricing ) && $cart_item['data']->per_items_pricing;

				if ( $is_per_item_pricing ) {
					/**
					 * @var WC_Product_Yith_Bundle $bundle_product
					 */
					$bundle_product = $cart_item['data'];
					$bundle_price   = ! $is_per_item_pricing ? yith_wcpb_get_price_to_display( $bundle_product, $bundle_product->get_regular_price(), absint( $cart_item['quantity'] ) ) : 0;

					foreach ( $cart_item['bundled_items'] as $bundled_item_key ) {
						if ( ! isset( WC()->cart->cart_contents[ $bundled_item_key ] ) ) {
							continue;
						}

						$item_values = WC()->cart->cart_contents[ $bundled_item_key ];
						/** @var WC_Product $product */
						$product = $item_values['data'];

						/**
						 * fixed for Role Based integration
						 *
						 * @since 1.1.1
						 */
						$discount             = isset( $item_values['discount'] ) ? $item_values['discount'] * $product->get_regular_price() / 100 : 0;
						$discount_filter_args = array(
							'cart_item'        => $item_values,
							'bundle_cart_item' => $cart_item,
						);
						$discount             = apply_filters(
							'yith_wcpb_bundled_item_calculated_discount',
							$discount,
							isset( $item_values['discount'] ) ? $item_values['discount'] : 0,
							$product->get_regular_price(),
							$product->get_id(),
							$discount_filter_args
						);
						$bundled_item_price   = ( $product->get_regular_price() - $discount ) * absint( $item_values['quantity'] );
						$bundled_item_price   = yith_wcpb_get_price_to_display( $product, $bundled_item_price );
						$bundled_item_price   = yith_wcpb_round_bundled_item_price( $bundled_item_price );

						$bundle_price += $bundled_item_price;
					}
					$subtotal = $this->format_product_subtotal( $cart_item['data'], $bundle_price );
					$subtotal = apply_filters( 'yith_wcpb_bundle_pip_bundled_items_subtotal', $subtotal, $cart_item, $bundle_price );
				}
			}

			do_action( 'yith_wcpb_after_bundle_bundles_item_subtotal' );

			return $subtotal;
		}

		/**
		 * @param WC_Product $product
		 * @param            $subtotal
		 *
		 * @return string
		 */
		public function format_product_subtotal( $product, $subtotal ) {
			$cart = WC()->cart;

			if ( $product->is_taxable() ) {
				// Taxable
				if ( $cart->tax_display_cart == 'excl' ) {
					$product_subtotal = wc_price( $subtotal );

					if ( $cart->prices_include_tax && $cart->tax_total > 0 ) {
						$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
					}
				} else {
					$product_subtotal = wc_price( $subtotal );
					if ( ! $cart->prices_include_tax && $cart->tax_total > 0 ) {
						$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
					}
				}
			} else {
				// Non-taxable
				$product_subtotal = wc_price( $subtotal );
			}

			return $product_subtotal;
		}

		/**
		 * @param            $price
		 * @param WC_Product $product
		 *
		 * @return mixed|string
		 */
		public function woocommerce_empty_price_html( $price, $product ) {
			if ( $product->is_type( 'yith_bundle' ) ) {
				remove_filter( 'woocommerce_empty_price_html', array( $this, __FUNCTION__ ), 15 );
				$price = $product->get_price_html();
				add_filter( 'woocommerce_empty_price_html', array( $this, __FUNCTION__ ), 15, 2 );
			}

			return $price;
		}

		/**
		 * woocommerce Validation Bundle Product for add to cart
		 *
		 * @param        $add_flag
		 * @param        $product_id
		 * @param        $product_quantity
		 * @param string           $variation_id
		 * @param array            $variations
		 * @param array            $cart_item_data
		 *
		 * @access public
		 * @return bool
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since  1.0.0
		 */
		public function woocommerce_add_to_cart_validation( $add_flag, $product_id, $product_quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {
			$product = wc_get_product( $product_id );

			if ( $product && $product->is_type( 'yith_bundle' ) ) {
				/**@var WC_Product_Yith_Bundle $product */
				if ( get_option( 'woocommerce_manage_stock' ) == 'yes' ) {

					$bundled_items = $product->get_bundled_items();
					if ( $bundled_items ) {
						$bundle_add_to_cart_params = isset( $cart_item_data['yith-bundle-add-to-cart-params'] ) ? $cart_item_data['yith-bundle-add-to-cart-params'] : $_REQUEST;

						foreach ( $bundled_items as $bundled_item ) {
							/** @var YITH_WC_Bundled_Item $bundled_item */
							$bundled_prod = $bundled_item->get_product();

							// if is optional -> Continue
							$optional_checked = isset( $bundle_add_to_cart_params[ 'yith_bundle_optional_' . $bundled_item->item_id ] ) ? true : false;
							if ( $bundled_item->optional && ! $optional_checked ) {
								continue;
							}

							$bundled_item_id = $bundled_item->item_id;

							$bundled_item_quantity = isset( $bundle_add_to_cart_params[ 'yith_bundle_quantity_' . $bundled_item->item_id ] ) ? absint( $bundle_add_to_cart_params[ 'yith_bundle_quantity_' . $bundled_item->item_id ] ) : $bundled_item->get_quantity();

							if ( ! $bundled_item_quantity ) {
								continue;
							}

							if ( $bundled_item_quantity < $bundled_item->min_quantity ) {
								$notice = sprintf( __( 'The minimum number of &quot;%1$s&quot; required is %2$s', 'yith-woocommerce-product-bundles' ), $bundled_item->product->get_title(), $bundled_item->min_quantity );
								wc_add_notice( $notice, 'error' );

								return false;
							}

							if ( $bundled_item_quantity > $bundled_item->max_quantity && $bundled_item->max_quantity != 0 ) {
								$notice = sprintf( __( 'The maximum number of &quot;%1$s&quot; allowed is %2$s', 'yith-woocommerce-product-bundles' ), $bundled_item->product->get_title(), $bundled_item->max_quantity );
								wc_add_notice( $notice, 'error' );

								return false;
							}

							// VARIABLE
							if ( $bundled_prod->is_type( 'variable' ) ) {
								if ( isset( $cart_item_data['cartstamp'][ $bundled_item_id ]['variation_id'] ) ) {

									$variation_id = $cart_item_data['cartstamp'][ $bundled_item_id ]['variation_id'];

								} elseif ( isset( $bundle_add_to_cart_params[ 'yith_bundle_variation_id_' . $bundled_item->item_id ] ) ) {

									$variation_id = $bundle_add_to_cart_params[ 'yith_bundle_variation_id_' . $bundled_item->item_id ];
								}

								if ( isset( $variation_id ) && is_numeric( $variation_id ) && $variation_id > 1 ) {

									if ( get_post_meta( $variation_id, '_price', true ) === '' ) {

										wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart. The selected variation of &quot;%2$s&quot; cannot be purchased.', 'yith-woocommerce-product-bundles' ), get_the_title( $product_id ), $bundled_item->product->get_title() ), 'error' );

										return false;
									}

									$b_variation = $bundled_prod instanceof WC_Data ? wc_get_product( $variation_id ) : $bundled_prod->get_child( $variation_id );
									// Purchasable
									if ( ! $b_variation->is_purchasable() ) {
										wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart because &quot;%2$s&quot; cannot be purchased at the moment.', 'yith-woocommerce-product-bundles' ), get_the_title( $product_id ), $bundled_item->product->get_title() ), 'error' );

										return false;
									}

									if ( ! $b_variation->has_enough_stock( $bundled_item_quantity * intval( $product_quantity ) ) ) {
										wc_add_notice( __( 'You cannot add this quantity of items, because there are not enough in stock.', 'yith-woocommerce-product-bundles' ), 'error' );

										return false;
									}
								} else {
									wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart. Please choose an option for &quot;%2$s&quot;&hellip;', 'yith-woocommerce-product-bundles' ), get_the_title( $product_id ), $bundled_item->product->get_title() ), 'error' );

									return false;
								}
							} else {

								// Purchasable
								if ( ! $bundled_prod->is_purchasable() ) {
									wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart because &quot;%2$s&quot; cannot be purchased at the moment.', 'yith-woocommerce-product-bundles' ), get_the_title( $product_id ), $bundled_item->product->get_title() ), 'error' );

									return false;
								}
								if ( ! $bundled_prod->has_enough_stock( $bundled_item_quantity * intval( $product_quantity ) ) ) {
									wc_add_notice( __( 'You cannot add this quantity of items, because there are not enough in stock.', 'yith-woocommerce-product-bundles' ), 'error' );

									return false;
								}
							}
						}
					}
				}

				// check quantity (Min Max bundled items)
				$min          = absint( $product->get_advanced_options( 'min' ) );
				$max          = absint( $product->get_advanced_options( 'max' ) );
				$min_distinct = absint( $product->get_advanced_options( 'min_distinct' ) );
				$max_distinct = absint( $product->get_advanced_options( 'max_distinct' ) );

				if ( $min || $max || $min_distinct || $max_distinct ) {

					$bundled_items                 = $product->get_bundled_items();
					$bundled_items_number          = 0;
					$bundled_items_number_distinct = 0;
					if ( $bundled_items ) {
						foreach ( $bundled_items as $bundled_item ) {
							// if is optional -> Continue
							$optional_checked = isset( $_REQUEST[ 'yith_bundle_optional_' . $bundled_item->item_id ] ) ? true : false;
							if ( $bundled_item->optional && ! $optional_checked ) {
								continue;
							}

							$bundled_item_quantity = isset( $_REQUEST[ 'yith_bundle_quantity_' . $bundled_item->item_id ] ) ? absint( $_REQUEST[ 'yith_bundle_quantity_' . $bundled_item->item_id ] ) : $bundled_item->get_quantity();

							if ( ! $bundled_item_quantity ) {
								continue;
							}

							$bundled_items_number          += absint( $bundled_item_quantity );
							$bundled_items_number_distinct += 1;
						}
					}

					if ( ! ! $min && $bundled_items_number < $min ) {
						$notice = sprintf( __( 'The minimum number of items in the bundle &quot;%1$s&quot; required is %2$s', 'yith-woocommerce-product-bundles' ), get_the_title( $product_id ), $min );
						$notice = apply_filters( 'yith_wcpb_cart_error_notice_minimum_not_reached', $notice, $bundled_items_number, $min, $max, $product );
						wc_add_notice( $notice, 'error' );

						return false;
					}

					if ( ! ! $max && $bundled_items_number > $max ) {
						$notice = sprintf( __( 'The maximum number of items in the bundle &quot;%1$s&quot; allowed is %2$s', 'yith-woocommerce-product-bundles' ), get_the_title( $product_id ), $max );
						$notice = apply_filters( 'yith_wcpb_cart_error_notice_maximum_exceeded', $notice, $bundled_items_number, $min, $max, $product );
						wc_add_notice( $notice, 'error' );

						return false;
					}

					if ( ! ! $min_distinct && $bundled_items_number_distinct < $min_distinct ) {
						$notice = sprintf( __( 'The minimum number of different items in the bundle &quot;%1$s&quot; required is %2$s', 'yith-woocommerce-product-bundles' ), get_the_title( $product_id ), $min_distinct );
						$notice = apply_filters( 'yith_wcpb_cart_error_notice_minimum_distinct_not_reached', $notice, $bundled_items_number_distinct, $min_distinct, $max_distinct, $product );
						wc_add_notice( $notice, 'error' );

						return false;
					}

					if ( ! ! $max_distinct && $bundled_items_number_distinct > $max_distinct ) {
						$notice = sprintf( __( 'The maximum number of different items in the bundle &quot;%1$s&quot; allowed is %2$s', 'yith-woocommerce-product-bundles' ), get_the_title( $product_id ), $max_distinct );
						$notice = apply_filters( 'yith_wcpb_cart_error_notice_maximum_distinct_exceeded', $notice, $bundled_items_number_distinct, $min_distinct, $max_distinct, $product );
						wc_add_notice( $notice, 'error' );

						return false;
					}
				}
			}

			return $add_flag;
		}


		/**
		 * delete subtotal for bundled items in order
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_order_formatted_line_subtotal( $subtotal, $item, $order ) {
			if ( isset( $item['bundled_by'] ) ) {
				return '';
			} // -> CHILD of bundle

			if ( ! isset( $item['yith_bundle_totals'] ) ) {
				return $subtotal;
			}

			$bundle_totals = $item['yith_bundle_totals'];

			if ( isset( $bundle_totals['line_subtotal'], $bundle_totals['line_subtotal_tax'] ) ) {
				$price = $bundle_totals['line_subtotal'];
				if ( 'excl' !== get_option( 'woocommerce_tax_display_cart' ) ) {
					$price += $bundle_totals['line_subtotal_tax'];
				}

				return wc_price( $price );
			}

			// PARENT or NOT BUNDLE PRODUCT
			return $subtotal;
		}

		/**
		 * Find the parent of a bundled item in an order.
		 *
		 * @param array    $item
		 * @param WC_Order $order
		 *
		 * @return array
		 */
		function get_bundled_order_item_parent( $item, $order ) {
			// find container item
			foreach ( $order->get_items() as $order_item ) {

				if ( isset( $order_item['yith_bundle_cart_key'] ) ) {
					$is_parent = $item['bundled_by'] == $order_item['yith_bundle_cart_key'] ? true : false;
					if ( $is_parent ) {
						$parent_item = $order_item;

						return $parent_item;
					}
				}
			}

			return false;
		}

		/**
		 * add meta in order
		 *
		 * @access      public
		 * @since       1.0.0
		 * @author      Leanza Francesco <leanzafrancesco@gmail.com>
		 * @deprecated  since 1.2.11
		 */
		public function woocommerce_add_order_item_meta( $item_id, $values, $cart_item_key ) {
			// DO NOTHING
		}

		/**
		 * add bundle data to order items
		 *
		 * @param WC_Order_Item_Product $item
		 * @param string                $cart_item_key
		 * @param array                 $values
		 * @param WC_Order              $order
		 *
		 * @since 1.2.11
		 */
		public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
			$is_bundle       = isset( $values['cartstamp'] );
			$is_bundled_item = isset( $values['bundled_by'] );
			$_product        = $values['data'];
			$meta_to_store   = array();

			if ( $is_bundle ) {
				$meta_to_store = array(
					'_cartstamp'            => $values['cartstamp'],
					'_bundled_items'        => ! empty( $values['bundled_items'] ) ? $values['bundled_items'] : array(),
					'_per_items_pricing'    => ! ! $_product->per_items_pricing ? 'yes' : 'no',
					'_non_bundled_shipping' => ! ! $_product->non_bundled_shipping ? 'yes' : 'no',
					'_yith_bundle_cart_key' => $cart_item_key,
				);
			} elseif ( $is_bundled_item ) {
				$meta_to_store = array(
					'_bundled_by'       => $values['bundled_by'],
					'_yith_wcpb_hidden' => isset( $values['yith_wcpb_hidden'] ) ? ! ! $values['yith_wcpb_hidden'] : false,
				);
			}

			if ( isset( $values['yith_bundle_totals'] ) ) {
				$meta_to_store['_yith_bundle_totals'] = $values['yith_bundle_totals'];
			}

			if ( $meta_to_store ) {
				foreach ( $meta_to_store as $key => $value ) {
					$item->add_meta_data( $key, $value );
				}
			}
		}

		public function woocommerce_cart_shipping_packages( $packages ) {
			if ( ! empty( $packages ) ) {
				$items_to_remove_from_packages = array();
				foreach ( $packages as $package_key => $package ) {
					if ( ! empty( $package['contents'] ) ) {
						foreach ( $package['contents'] as $cart_item => $cart_item_data ) {
							if ( isset( $cart_item_data['bundled_items'] ) ) {
								$bundle = clone $cart_item_data['data'];

								if ( ! $bundle->non_bundled_shipping ) {
									$line_total        = 0;
									$line_subtotal     = 0;
									$line_tax          = 0;
									$line_subtotal_tax = 0;
									$line_tax_data     = array(
										'total'    => array(),
										'subtotal' => array(),
									);

									foreach ( $cart_item_data['bundled_items'] as $child_item_key ) {
										$items_to_remove_from_packages[] = $child_item_key;
										if ( isset( $package['contents'][ $child_item_key ] ) ) {
											$item = $package['contents'][ $child_item_key ];

											$line_total += isset($item['line_total']) ? $item['line_total'] : 0;
											$line_subtotal += isset($item['line_subtotal']) ? $item['line_subtotal'] : 0;
											$line_tax += isset($item['line_tax']) ? $item['line_tax'] : 0;
											$line_subtotal_tax += isset($item['line_subtotal_tax']) ? $item['line_subtotal_tax'] : 0;

											if ( isset( $item['line_tax_data'] ) ) {
												$_tax_data = $item['line_tax_data'];
												foreach ( $_tax_data as $type => $values ) {
													foreach ( $values as $t_index => $t_value ) {
														$line_tax_data[ $type ][ $t_index ] = isset( $line_tax_data[ $type ][ $t_index ] ) ? $line_tax_data[ $type ][ $t_index ] + $t_value : $t_value;
													}
												}
											}

											unset( $packages[ $package_key ]['contents'][ $child_item_key ] );
										}
									}

									$packages[ $package_key ]['contents'][ $cart_item ]['line_tax']          = $line_tax;
									$packages[ $package_key ]['contents'][ $cart_item ]['line_subtotal_tax'] = $line_subtotal_tax;
									$packages[ $package_key ]['contents'][ $cart_item ]['line_tax_data']     = $line_tax_data;
									$packages[ $package_key ]['contents'][ $cart_item ]['line_total']        = $line_total;
									$packages[ $package_key ]['contents'][ $cart_item ]['line_subtotal']     = $line_subtotal;

								} else {
									// SINGULAR SHIPPING
									if ( isset( $cart_item_data['yith_parent'] ) ) {
										$parent_bundle_key               = $cart_item_data['yith_parent'];
										$items_to_remove_from_packages[] = $parent_bundle_key;
										if ( isset( $package['contents'][ $parent_bundle_key ] ) ) {
											unset( $packages[ $package_key ]['contents'][ $parent_bundle_key ] );
										}
									}
								}
							}
						}
					}
				}

				if ( $items_to_remove_from_packages ) {
					foreach ( $packages as $package_key => $package ) {
						if ( ! empty( $package['contents'] ) ) {
							foreach ( $items_to_remove_from_packages as $item_key ) {
								if ( isset( $package['contents'][ $item_key ] ) ) {
									unset( $packages[ $package_key ]['contents'][ $item_key ] );
								}
							}
						}
					}
				}
			}

			return $packages;
		}

		public function enqueue_scripts() {
			parent::enqueue_scripts();

			$yith_wcpb_params = apply_filters(
				'yith_wcpb_frontend_js_params',
				array(
					'price_handler_parent'     => '.product',
					'price_handler'            => '.price',
					'price_handler_parent_alt' => '.summary',
					'price_handler_alt'        => '.price',
					'price_handler_only_first' => true,
					'update_price_on_load'     => 'yes',
					'photoswipe_enabled'       => get_option( 'yith-wcpb-photoswipe-for-bundled-images', 'yes' ),
					'i18n'                     => array(
						'variation_selection_needed' => esc_attr__( 'Please select some product options before adding this product to your cart.', 'yith-woocommerce-product-bundles' ),
						'out_of_stock_item_selected' => esc_attr__( 'Sorry, you selected an out-of-stock item. Please choose a different combination.', 'yith-woocommerce-product-bundles' ),
					),
				)
			);

			wp_enqueue_script(
				'yith_wcpb_bundle_frontend_add_to_cart',
				yit_load_js_file( YITH_WCPB_ASSETS_URL . '/js/frontend_add_to_cart.js' ),
				array(
					'jquery',
					'wc-add-to-cart-variation',
				),
				YITH_WCPB_VERSION,
				true
			);

			wp_localize_script( 'yith_wcpb_bundle_frontend_add_to_cart', 'ajax_obj', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

			wp_localize_script( 'yith_wcpb_bundle_frontend_add_to_cart', 'yith_wcpb_params', $yith_wcpb_params );

		}

		/**
		 * @param $allows_type
		 *
		 * @return array
		 */
		public function wapo_product_type_list( $allows_type ) {

			$allows_type = array_merge( $allows_type, array( 'yith_bundle' ) );

			return $allows_type;

		}

	}
}
/**
 * Unique access to instance of YITH_WCPB_Frontend_Premium class
 *
 * @return YITH_WCPB_Frontend_Premium
 * @deprecated since 1.2.0 use YITH_WCPB_Frontend() instead
 * @since      1.0.0
 */
function YITH_WCPB_Frontend_Premium() {
	return YITH_WCPB_Frontend();
}
