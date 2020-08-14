<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements the YWRAQ_Avanced_Product_Options class.
 *
 * @class   YWRAQ_Avanced_Product_Options
 * @package YITH
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YWRAQ_Avanced_Product_Options' ) ) {

	/**
	 * Class YWRAQ_Avanced_Product_Options
	 */
	class YWRAQ_Avanced_Product_Options {

		/**
		 * Single instance of the class
		 *
		 * @var \YWRAQ_WooCommerce_Product_Addon
		 */

		protected static $instance;

		/**
		 * Session object
		 */
		public $session_class;


		/**
		 * Content of session
		 */
		public $raq_content = array();


		/**
		 * Returns single instance of the class
		 *
		 * @return \YWRAQ_WooCommerce_Product_Addon
		 * @since 1.0.0
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
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			add_filter( 'ywraq_ajax_validate_uploaded_files', array( $this, 'validate_uploaded_files' ), 10 );
			add_filter( 'ywraq_ajax_add_item_prepare', array( $this, 'ajax_add_item' ), 10, 2 );
			add_filter( 'ywraq_add_item', array( $this, 'add_item' ), 10, 2 );

			add_filter( 'ywraq_request_quote_view_item_data', array( $this, 'request_quote_view' ), 10, 4 );

			//  add_action( 'yith_ywraq_email_before_raq_table', array( $this, 'add_filter_before_raq_table' ), 20, 4 );
			add_action( 'ywraq_order_adjust_price', array( $this, 'adjust_price' ), 10, 2 );
			add_action( 'ywraq_quote_adjust_price', array( $this, 'adjust_price' ), 10, 2 );
			add_action( 'ywraq_from_cart_to_order_item', array( $this, 'add_order_item_meta' ), 10, 3 );
			add_action( 'ywraq_item_data', array( $this, 'add_raq_item_meta' ), 10, 3 );
			add_filter( 'ywraq_add_to_cart', array( $this, 'add_to_cart' ), 10, 2 );

			// email front end price
			add_filter( 'ywraq_exists_in_list', array( $this, 'exists_in_list' ), 10, 5 );
			add_filter( 'ywraq_quote_item_id', array( $this, 'quote_item_id' ), 10, 2 );
			add_filter( 'ywraq_order_cart_item_data', array( $this, 'remove_price' ), 90, 3 );
			add_filter( 'ywraq_raq_content_before_add_item', array( $this, 'set_up_raq_content_on_sold_individually' ), 10, 3 );
			add_filter( 'yith_ywraq_item_class', array( $this, 'add_data_wapo_parent_info' ), 10, 3 );
			add_filter( 'woocommerce_order_item_name', array( $this, 'hide_name_and_qty_on_sold_individually' ), 10, 2 );
			add_filter( 'woocommerce_order_item_quantity_html', array( $this, 'hide_name_and_qty_on_sold_individually' ), 10, 2 );
		}

		public function validate_uploaded_files( $error_list ) {
			$yith_wapo_frontend = YITH_WAPO()->frontend;
			wc_clear_notices();

			if ( ! empty( $_FILES ) ) {

				$upload_data = array();

				foreach ($_FILES as $group_key => $group_values ){

					foreach ($group_values as $prop_key => $prop_values){

						foreach ($prop_values as $field_key => $field_value){

							$upload_data[$group_key][$field_key][$prop_key]= $field_value;

						}

					}

				}

				foreach ( $upload_data as $key => $single_data ) {
					$error_list =  YITH_WAPO_Type::checkUploadedFilesError( $yith_wapo_frontend, $single_data, true, $key );
				}

			}

			return $error_list;
		}


		/**
		 * @param $postdata
		 * @param $product_id
		 *
		 * @return array
		 */
		public function ajax_add_item( $postdata, $product_id ) {
			$yith_wapo_frontend = YITH_WAPO()->frontend;


			if ( empty( $postdata ) ) {
				$postdata = array();
			}

			$postdata['add-to-cart'] = $product_id;

			$t1 = $yith_wapo_frontend->add_cart_item_data( null, $product_id, $postdata );


			if ( defined( 'YITH_WAPO_PREMIUM' ) ) {
				$t2                               = $yith_wapo_frontend->add_cart_item_data( null, $product_id, $postdata, 1 );
				$t['yith_wapo_options']           = array_merge( $t1['yith_wapo_options'], $t2['yith_wapo_options'] );
				$t['yith_wapo_sold_individually'] = empty( $t2['yith_wapo_options'] ) ? '' : 1;
			} else {
				$t['yith_wapo_options'] = $t1['yith_wapo_options'];
			}

			if ( ! empty( $t ) ) {
				$postdata = array_merge( $t, $postdata );
			}

			return $postdata;
		}

		/**
		 * @param      $item_data
		 * @param      $raq
		 * @param      $_product WC_Product
		 * @param bool $show_price
		 *
		 * @return array
		 */
		public function request_quote_view( $item_data, $raq, $_product, $show_price = true ) {


			if ( isset( $raq['yith_wapo_options'] ) ) {
				foreach ( $raq['yith_wapo_options'] as $_r ) {

					$price = '';

					if ( get_option( 'ywraq_hide_price' ) != 'yes' && $show_price && $_r['price'] > 0 ) {
						$price = ' ( +' . strip_tags( wc_price( $_r['price'] ) ) . ' ) ';
					}

					if ( class_exists( 'YITH_WAPO_WPML' ) ) {

						$key = YITH_WAPO_WPML::string_translate( $_r['name'] );
						if ( strpos( $_r['value'], 'Attached file' ) ) {
							$array = new SimpleXMLElement( $_r['value'] );
							$link  = $array['href'];
							$value = '<a href="' . $link . '" target="_blank">' . __( 'Attached file', 'yith-woocommerce-product-add-ons' ) . '</a>';
						} else {
							$value = YITH_WAPO_WPML::string_translate( $_r['value'] );
						}

					} else {
						$key   = $_r['name'];
						$value = $_r['value'];
					}


					$item_data[] = array(
						'key'   => $key . $price,
						'value' => urldecode($value)
					);

				}
			}

			return $item_data;
		}

		/**
		 * @param $raq
		 * @param $product_raq
		 *
		 * @return mixed
		 */
		public function add_item( $raq, $product_raq ) {

			if ( isset( $product_raq['yith_wapo_options'] ) ) {
				/* SOLD INDIVIDUALLY SUPPORT */
				if ( ! empty( $product_raq['yith_wapo_sold_individually'] ) ) {
					$individual_items = array();
					foreach ( $product_raq['yith_wapo_options'] as $key => $option ) {
						if ( ! empty( $option['sold_individually'] ) && $option['sold_individually'] == 1 ) {
							$individual_items[] = $option;
							unset( $product_raq['yith_wapo_options'][$key] );
						}
					}
					$raq['yith_wapo_options'] = $product_raq['yith_wapo_options'];
					$raq['yith_wapo_individually_sold_items'] = $individual_items;
				} else {
					$raq['yith_wapo_options'] = $product_raq['yith_wapo_options'];
				}

			}

			return $raq;
		}

		/**
		 * @param      $item_data
		 * @param null $raq
		 * @param bool $show_price
		 *
		 * @return array
		 */
		public function add_raq_item_meta( $item_data, $raq = null, $show_price = true ) {

			if ( isset( $raq['yith_wapo_options'] ) ) {
				foreach ( $raq['yith_wapo_options'] as $_r ) {

					$price = '';

					if ( $show_price && $_r['price'] > 0 ) {
						$price = ' ( ' . strip_tags( wc_price( $_r['price'] ) ) . ' ) ';
					}

					if ( class_exists( 'YITH_WAPO_WPML' ) ) {

						$key   = YITH_WAPO_WPML::string_translate( $_r['name'] );
						$value = YITH_WAPO_WPML::string_translate( $_r['value'] );
					} else {
						$key   = $_r['name'];
						$value = $_r['value'];
					}

					$item_data[] = array(
						'key'   => $key,
						'value' => urldecode($value) . $price
					);

				}
			}

			return $item_data;
		}

		/**
		 * @param $values
		 * @param $_product
		 */
		public function adjust_price( $values, $_product, $taxes = 'inc' ) {
			if ( isset( $values['yith_wapo_options'] ) ) {
				$addon_price = 0;
				if ( isset( $values['yith_wapo_individual_item'] ) && $values['yith_wapo_individual_item'] ) {
					foreach ( $values['yith_wapo_options'] as $_r ) {
						$addon_price = 'inc' == $taxes ? $addon_price + floatval( $_r['price_original'] ) : $addon_price + floatval( $_r['price'] );
					}
					$_product->set_price( $addon_price );
				} else {
					foreach ( $values['yith_wapo_options'] as $_r ) {
						$addon_price = 'inc' == $taxes ? $_r['price_original'] :  $_r['price'];
						$_product->set_price( $addon_price + $_product->get_price() );
					}
				}
			}
		}

		/**
		 * @param $cart_item_data
		 * @param $item
		 *
		 * @return mixed
		 */
		public function add_to_cart( $cart_item_data, $item ) {
			if ( isset( $item['item_meta']['_ywraq_wc_ywapo'] ) ) {
				$addons = maybe_unserialize( $item['item_meta']['_ywraq_wc_ywapo'] );
				if ( ! empty( $addons ) ) {
					$ad                                  = maybe_unserialize( $addons[0] );
					$cart_item_data['yith_wapo_options'] = $ad;
					$cart_item_data['add-to-cart']       = $item['product_id'];
				}
			}

			return $cart_item_data;

		}

		/**
		 * @param $new_cart
		 * @param $values
		 * @param $item
		 * @param $new_cart_item_key
		 *
		 * @return mixed
		 */
		public function add_to_cart_from_request( $new_cart, $values, $item, $new_cart_item_key ) {

			$cart =  &$new_cart->cart_contents;

			if ( isset( $cart[ $new_cart_item_key ] ) && isset( $values['yith_wapo_options'] ) ) {
				$cart[ $new_cart_item_key ]['yith_wapo_options'] = $values['yith_wapo_options'];
				$ywapo_frontend                                  = YITH_WAPO()->frontend;
				$ywapo_frontend->cart_adjust_price( $cart[ $new_cart_item_key ] );
			}

			return $new_cart;

		}

		/**
		 * @param $values
		 * @param $cart_item_key
		 * @param $item_id
		 */
		public function add_order_item_meta( $values, $cart_item_key, $item_id ) {

			if ( ! empty( $values['yith_wapo_options'] ) ) {
				foreach ( $values['yith_wapo_options'] as $addon ) {
					$name = class_exists( 'YITH_WAPO_WPML' ) ? YITH_WAPO_WPML::string_translate( $addon['name'] ) : $addon['name'];

					if ( class_exists( 'YITH_WAPO_WPML' ) ) {

						$name  = YITH_WAPO_WPML::string_translate( $addon['name'] );
						$value = YITH_WAPO_WPML::string_translate( $addon['value'] );
					} else {
						$name  = $addon['name'];
						$value = urldecode($addon['value']);
					}

					if ( $addon['price'] > 0 ) {
						$name .= ' (' . strip_tags( wc_price( $addon['price'] ) ) . ')';
						if ( isset( $values['yith_wapo_sold_individually'] ) && $values['yith_wapo_sold_individually'] && apply_filters( 'yith_ywraq_wapo_add_sold_individually_tag', true, $values, $addon ) ) {
							$name .= ' ' . _x( '* Sold invidually', 'notice on admin order item meta', 'yith-woocommerce-request-a-quote' );
						}
					}
					wc_add_order_item_meta( $item_id, $name, $value );
				}
				wc_add_order_item_meta( $item_id, '_ywraq_wc_ywapo', $values['yith_wapo_options'] );
			}

		}

		/**
		 * @param $args
		 * @param $cart_item_key
		 * @param $values
		 * @param $new_cart
		 *
		 */
		public function cart_to_order_args( $args, $cart_item_key, $values, $new_cart ) {

			$product = $values['data'];
			if ( isset( $product ) && is_object( $product ) ) {
				if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
					$total = wc_get_price_excluding_tax( $product, array( 'qty' => $values['quantity'] ) );
				} else {
					$total = $product->get_price_excluding_tax( $values['quantity'] );
				}

				$args['totals']['subtotal'] = $total;
				$args['totals']['total']    = $total;
			}


		}

		/**
		 * @param $return
		 * @param $product_id
		 * @param $variation_id
		 * @param $postdata
		 * @param $raqdata
		 *
		 * @return bool
		 */
		public function exists_in_list( $return, $product_id, $variation_id, $postdata, $raqdata ) {

			if ( $postdata ) {

				$this->ajax_add_item( $postdata, $product_id );
				if ( isset( $postdata['yith_wapo_options'] ) && ! empty( $postdata['yith_wapo_options'] ) ) {
					$str = '';
					foreach ( $postdata['yith_wapo_options'] as $ad ) {
						$str .= $ad['name'] . $ad['value'];
					}

					if ( $variation_id ) {
						$key_to_find = md5( $product_id . $variation_id . $str );
					} else {
						$key_to_find = md5( $product_id . $str );
					}


					if ( array_key_exists( $key_to_find, $raqdata ) ) {
						$return = true;
					}
				}
			} else {
				$addons = YITH_WAPO_Type::getAllowedGroupTypes( $product_id );
				if ( ! empty( $addons ) ) {
					$return = false;
				}
			}


			return $return;
		}

		/**
		 * @param $item_id
		 * @param $product_raq
		 *
		 * @return string
		 */
		public function quote_item_id( $item_id, $product_raq ) {
			$str    = '';
			$addons = YITH_WAPO_Type::getAllowedGroupTypes( $product_raq['product_id'] );

			if ( ! empty( $addons ) && isset( $product_raq['yith_wapo_options'] ) && ! empty( $product_raq['yith_wapo_options'] ) ) {

				foreach ( $product_raq['yith_wapo_options'] as $ad ) {
					$str .= $ad['name'] . $ad['value'];
				}
				if ( isset( $product_raq['variation_id'] ) ) {
					$item_id = md5( $product_raq['product_id'] . $product_raq['variation_id'] . $str );
				} else {
					$item_id = md5( $product_raq['product_id'] . $str );
				}

			}

			return $item_id;
		}

		/**
		 *
		 */
		public function remove_action() {
			$yith_wapo_frontend = YITH_WAPO()->frontend;
			remove_filter( 'woocommerce_add_cart_item_data', array( $yith_wapo_frontend, 'add_cart_item_data' ), 10 );
		}

		/**
		 * @param $cart_item
		 * @param $item
		 * @param $order
		 *
		 * @return array
		 */
		public function remove_price( $cart_item, $item, $order ) {
			if ( isset( $cart_item['yith_wapo_options'] ) ) {
				$new_cart_item = array();
				foreach ( $cart_item['yith_wapo_options'] as $k => $opt ) {
					if ( isset( $opt['price_original'] ) ) {
						$opt['price_original'] = 0;
						//$opt['price']                             = 0;
						$new_cart_item[ $k ] = $opt;
					}
				}

				$cart_item['yith_wapo_options'] = $new_cart_item;
			}

			return $cart_item;
		}

		public function add_data_wapo_parent_info( $classes, $raq_content, $key ) {
			if ( isset( $raq_content[$key]['yith_wapo_parent'] ) && $raq_content[$key]['yith_wapo_parent'] == 1 ) {
				$classes .= ' yith-wapo-parent';
			}
			return $classes;
		}

		public function set_up_raq_content_on_sold_individually( $raq_content ) {
			foreach ( $raq_content as $key => &$raq ) {
				if ( ! empty( $raq['yith_wapo_individually_sold_items'] ) ) {
					$raq['yith_wapo_parent'] = 1;
					foreach ( $raq['yith_wapo_individually_sold_items'] as $individual_item ) {
						$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['product_id'] = $raq['product_id'];
						$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['quantity'] = 1;
						$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_parent_key'] = $key;
						$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_individual_item'] = 1;
						$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_sold_individually'] = 1;
						$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_options'][] = $individual_item;
					}
					unset( $raq['yith_wapo_individually_sold_items'] );
				}
			}
			return $raq_content;
		}

		public function hide_name_and_qty_on_sold_individually( $string, $item ) {
			$yith_wapo_option = $item->get_meta( '_ywraq_wc_ywapo' ) ? current( $item->get_meta( '_ywraq_wc_ywapo' ) ) : '';
			if ( isset( $yith_wapo_option['sold_individually'] ) && $yith_wapo_option['sold_individually'] ) {
				$string = '';
			}

			return $string;
		}

	}

	/**
	 * Unique access to instance of YWRAQ_WooCommerce_Product_Addon class
	 *
	 * @return \YWRAQ_WooCommerce_Product_Addon
	 */
	function YWRAQ_Avanced_Product_Options() {
		return YWRAQ_Avanced_Product_Options::get_instance();
	}

	if ( class_exists( 'YITH_WAPO' ) ) {
		YWRAQ_Avanced_Product_Options();
	}

}