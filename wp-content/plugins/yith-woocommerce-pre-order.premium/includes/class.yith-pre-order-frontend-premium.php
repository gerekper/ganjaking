<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Pre_Order_Frontend_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_Frontend_Premium' ) ) {
	/**
	 * Class YITH_Pre_Order_Frontend_Premium
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_Frontend_Premium extends YITH_Pre_Order_Frontend {

		public $_product_from_availability;

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since  1.0
		 */
		public function __construct() {
			parent::__construct();
			if ( 'no' == get_option( 'yith_wcpo_enable_pre_order', 'no' ) ) {
				return;
			}
			add_filter( 'woocommerce_get_availability', array( $this, 'get_product_from_availability' ), 10, 2 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'show_date_on_loop' ), 8 );
            add_shortcode( 'yith_wcpo_availability_date', array( $this, 'availability_date_shortcode' ) );
			add_action( 'woocommerce_after_cart_item_name', array( $this, 'show_date_on_cart' ), 100 );
			add_filter( 'woocommerce_variation_prices_price', array( $this, 'variable_price_range' ), 10, 3 );
			add_action( 'woocommerce_before_variations_form', array( $this, 'variable_product_label' ) );
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'variable_product_label_on_loop' ) );

			if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
				add_filter( 'woocommerce_stock_html', array( $this, 'show_date_on_single_product' ), 20, 3 );
				add_filter( 'woocommerce_get_price', array( $this, 'edit_price' ), 10, 2 );
			} else {
				add_filter( 'woocommerce_get_stock_html', array( $this, 'show_date_on_single_product' ), 10, 3 );
				add_filter( 'woocommerce_product_get_price', array( $this, 'edit_price' ), 10, 2 );
				add_filter( 'woocommerce_product_variation_get_price', array( $this, 'edit_price' ), 10, 2 );
				add_filter( 'woocommerce_show_variation_price', array( $this, 'show_variation_price' ), 10, 2 );
				add_filter( 'woocommerce_product_get_sale_price', array( $this, 'empty_sale_price' ), 10, 2 );
				add_filter( 'woocommerce_product_variation_get_sale_price', array( $this, 'empty_sale_price' ), 10, 2 );
				add_filter( 'woocommerce_product_is_on_sale', array( $this, 'force_use_of_sale_price' ), 10, 2 );
			}
			add_action( 'ywpo_add_order_item_meta', array( $this, 'add_for_sale_date_order_item_meta' ), 10, 2 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'check_cart_mixing' ), 10, 4 );
			add_action( 'woocommerce_cart_item_restored', array(
				$this,
				'prevent_cart_mixing_on_restore_item'
			), 10, 2 );

			// YITH Badge Management integration
			add_filter( 'yith_wcbm_advanced_badge_info', array( $this, 'auto_badge_data' ), 10, 2 );

			// YITH WooCommerce Product Countdown integration
			add_filter( 'ywpc_timer_title', array( $this, 'product_countdown_label' ), 60, 3 );

			// Flatsome fix for showing availability date on Quick View
			add_action( 'wc_quick_view_before_single_product', array( $this, 'flatsome_fix' ), 5 );

			add_shortcode( 'yith_pre_order_products', array( $this, 'pre_order_products_loop' ) );
			add_action( 'yith_wcpo_pagination_nav', array( $this, 'pagination_nav' ) );
		}

		// Compatibility for themes which returns only 2 parameters of "woocommerce_stock_html" filter
		public function get_product_from_availability( $availability, $product ) {
			$this->_product_from_availability = $product;

			return $availability;
		}

		public function print_availability_date( $class, $timestamp, $style, $availability_label_product = '', $pre_order = '' ) {
			$default_no_date_msg = get_option( 'yith_wcpo_no_date_label' );
			// Checks if there is a date set for the product.
			if ( ! empty( $timestamp ) ) {
				$automatic_date_formatting = get_option( 'yith_wcpo_enable_automatic_date_formatting' );

				$availability_label = ! empty( $availability_label_product ) ? $availability_label_product : get_option( 'yith_wcpo_default_availability_date_label' );
				$availability_label = apply_filters( 'yith_ywpo_date_time', $availability_label );

				if ( empty( $availability_label ) ) {
					$availability_label = apply_filters( 'yith_ywpo_default_availability_date_label',
						sprintf( esc_html__( 'Available on: %s at %s', 'yith-pre-order-for-woocommerce' ),
							'{availability_date}', '{availability_time}' ) );
				}

				if ( 'yes' == $automatic_date_formatting ) {
					$availability_label = str_replace( '{availability_date}', '<span class="availability_date"></span>', $availability_label );
					$availability_label = str_replace( '{availability_time}', '<span class="availability_time"></span>', $availability_label );
					$availability_label = apply_filters( 'yith_ywpo_availability_date_auto', $availability_label );

					// Show the custom label set in the plugin options.
					return '<div class="' . $class
					       . '" style="' . apply_filters( 'ywpo_' . $class . '_style', $style )
					       . '" data-time="' . $timestamp . '">' . $availability_label . '</div>';
				} else {
					$date_format = get_option( 'date_format' );
					$date        = apply_filters( 'yith_ywpo_availability_date_no_auto_date', get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), $date_format ), $timestamp );
					$time        = apply_filters( 'yith_ywpo_availability_date_no_auto_time', get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), 'H:i' ), $timestamp );
					$gmt_offset  = get_option( 'gmt_offset' );

					if ( 0 <= $gmt_offset ) {
						$offset_name = '+' . $gmt_offset;
					} else {
						$offset_name = (string) $gmt_offset;
					}

					$offset_name = str_replace( array( '.25', '.5', '.75' ), array(
						':15',
						':30',
						':45'
					), $offset_name );
					$offset_name = '(UTC' . $offset_name . ')';
					$time        = apply_filters( 'yith_ywpo_no_auto_time', $time . ' ' . $offset_name, $time, $offset_name );

					$availability_label = str_replace( '{availability_date}', $date, $availability_label );
					$availability_label = str_replace( '{availability_time}', $time, $availability_label );
					$availability_label = apply_filters( 'yith_ywpo_availability_date_no_auto', $availability_label, $timestamp, $date, $time );

					return '<div class="' . $class . '-no-auto-format" style="' . $style . '">' . $availability_label . '</div>';
				}
			} else if ( ! empty( $default_no_date_msg ) ) {
				// If no date is set, it shows the No date label.
				$default_no_date_label = '<div class="' . $class . '" style="' . $style . '">' . $default_no_date_msg . '</div>';
				return apply_filters( 'yith_ywpo_no_date_label', $default_no_date_label, $pre_order, $default_no_date_msg, $class, $style );
			}

			return null;
		}

		public function show_date_on_loop() {
			global $product, $sitepress;
			$id = yit_get_product_id( $product );

			$product_id   = $sitepress ? yit_wpml_object_id( $id, 'product', true, $sitepress->get_default_language() ) : $id;
			$pre_order    = new YITH_Pre_Order_Product( $product_id );
			$is_pre_order = $pre_order->get_pre_order_status();

			// Checks if the product is Pre-Order.
			if ( 'yes' != $is_pre_order ) {
				return;
			}
			$timestamp = $pre_order->get_for_sale_date_timestamp();
			$color     = get_option( 'yith_wcpo_availability_date_color_loop' );
			$style     = $color ? 'color: ' . $color : 'color: #b20015';

			echo $this->print_availability_date( 'pre_order_loop', $timestamp, $style, $pre_order->get_pre_order_availability_date_label(), $pre_order );

		}

		public function availability_date_shortcode( $atts ) {
		    global $product;
			$is_preorder = null;
			$fields      = shortcode_atts(
				array(
					'product_id' => 0,
				), $atts );

			$product_id = false;
			if ( ! empty( $fields['product_id'] ) ) {
				$product_id = $fields['product_id'];
            } else {
				if ( ! empty( $product ) ) {
				    $product_id = $product->get_id();
				}
            }

			if ( $product_id ) {
				wp_enqueue_script( 'yith-wcpo-frontend-single-product' );
				ob_start();
				echo $this->availability_date( $product_id );
				return ob_get_clean();
			}
		}

		public function availability_date( $product_id ) {
			if ( empty( $product_id ) ) {
			    return false;
            }
			$pre_order = new YITH_Pre_Order_Product( $product_id );

			$is_pre_order = $pre_order->get_pre_order_status();

			if ( 'yes' != $is_pre_order ) {
				return false;
			}
			$timestamp = $pre_order->get_for_sale_date_timestamp();
			$color     = get_option( 'yith_wcpo_availability_date_color_single_product' );
			$style     = $color ? 'color: ' . $color : 'color: #a46497';

			return $this->print_availability_date( 'pre_order_single', $timestamp, $style, $pre_order->get_pre_order_availability_date_label(), $pre_order );


		}

		public function show_date_on_single_product( $availability_html, $availability, $product = false ) {
			global $sitepress;
			if ( ! $product ) {
				$product = $this->_product_from_availability;
			}

			$id          = $product->get_id();
			$id          = $sitepress ? yit_wpml_object_id( $id, 'product', true, $sitepress->get_default_language() ) : $id;
			$pre_order   = new YITH_Pre_Order_Product( $id );
			$is_preorder = $pre_order->get_pre_order_status();

			if ( 'yes' == $is_preorder ) {
				return $this->availability_date( $id );
			}

			return $availability_html;
		}

		public function show_date_on_cart( $cart_item ) {
			global $sitepress;

			if ( ! empty( $cart_item['variation_id'] ) ) {
				$id = $sitepress ? yit_wpml_object_id( $cart_item['variation_id'], 'product', true, $sitepress->get_default_language() ) : $cart_item['variation_id'];
			} else {
				$id = $sitepress ? yit_wpml_object_id( $cart_item['product_id'], 'product', true, $sitepress->get_default_language() ) : $cart_item['product_id'];
			}

			$pre_order    = new YITH_Pre_Order_Product( $id );
			$is_pre_order = $pre_order->get_pre_order_status();

			// Checks if the product is Pre-Order and if the page is correct.
			if ( $is_pre_order == 'yes' && apply_filters( 'yith_wcpo_allow_other_page', is_cart() ) ) {

				$timestamp = $pre_order->get_for_sale_date_timestamp();
				$color     = get_option( 'yith_wcpo_availability_date_color_cart' );
				$style     = $color ? 'color: ' . $color : 'color: #a46497';

				echo $this->print_availability_date( 'pre_order_on_cart', $timestamp, $style, $pre_order->get_pre_order_availability_date_label(), $pre_order );

			} else {
				return $cart_item;
			}

		}

		public function edit_price( $price, $product ) {
			global $sitepress;

			if ( ( 'simple' != $product->get_type() && 'variation' != $product->get_type() ) || apply_filters( 'yith_wcpo_return_original_price', false, $product ) ) {
				return $price;
			}

			$id        = $product->get_id();
			$id        = $sitepress ? yit_wpml_object_id( $id, 'product', true, $sitepress->get_default_language() ) : $id;
			$pre_order = new YITH_Pre_Order_Product( $id );

			$is_pre_order      = $pre_order->get_pre_order_status();
			$price_adjustment  = $pre_order->get_pre_order_price_adjustment();
			$manual_price      = $pre_order->get_pre_order_price();
			$adjustment_type   = $pre_order->get_pre_order_adjustment_type();
			$adjustment_amount = $pre_order->get_pre_order_adjustment_amount();

			if ( 'yes' == $is_pre_order ) {
				if ( ! get_current_user_id() ) {
					switch ( get_option( 'yith_wcpo_guest_users_price', 'show_pre_order_price' ) ) {
						case 'show_regular_price' :
							return $product->get_regular_price();
						case 'hidden_price' :
							return '';
					}
				}
				if ( 'yes' == get_option( 'yith_wcpo_show_regular_price' ) && 'manual' == $price_adjustment && $manual_price != '0' && ! empty( $manual_price ) ) {
					return $this->compute_price( $product->get_regular_price(), $price_adjustment, $manual_price, $adjustment_type, $adjustment_amount );
				} else {
					return $this->compute_price( $price, $price_adjustment, $manual_price, $adjustment_type, $adjustment_amount );
				}
			}

			return $price;
		}

		/**
		 * If all the variations have the same regular price, the price will be hidden despite the variations use the Pre-Order price. This function fixes this.
		 *
		 * @param $bool
		 * @param $product_variable
		 *
		 * @return bool
		 */
		public function show_variation_price( $bool, $product_variable ) {
			$product_variable           = wc_get_product( $product_variable );
			$has_any_preorder_variation = false;
			foreach ( $product_variable->get_children() as $child ) {
				$pre_order_child = new YITH_Pre_Order_Product( $child );
				if ( 'yes' === $pre_order_child->get_pre_order_status() ) {
					$has_any_preorder_variation = true;
				}
			}
			if ( $has_any_preorder_variation ) {
				return true;
			}

			return $bool;
		}

		/**
		 * @param $sale_price
		 * @param $product
		 *
		 * @return string
		 * @since 1.3.2
		 */
		public function empty_sale_price( $sale_price, $product ) {
			$pre_order    = new YITH_Pre_Order_Product( $product );
			$is_pre_order = $pre_order->get_pre_order_status();

			$price_adjustment = $pre_order->get_pre_order_price_adjustment();
			$manual_price     = $pre_order->get_pre_order_price();

			if ( 'manual' == $price_adjustment && empty( $manual_price ) ) {
				return $sale_price;
			}

			if ( 'yes' == $is_pre_order && 'yes' == get_option( 'yith_wcpo_show_regular_price' ) ) {
				return '0';
			}

			return $sale_price;
		}

		/**
		 * @param $on_sale
		 * @param $product
		 *
		 * @return bool
		 * @since 1.3.2
		 */
		public function force_use_of_sale_price( $on_sale, $product ) {
			$pre_order    = new YITH_Pre_Order_Product( $product );
			$is_pre_order = $pre_order->get_pre_order_status();

			$price_adjustment = $pre_order->get_pre_order_price_adjustment();
			$manual_price     = $pre_order->get_pre_order_price();

			// If the option guest_users_price is set to show_regular_price, disable the use of Sale price for only see the Regular price without a strikethrough price
			if ( 'yes' == $is_pre_order && ! get_current_user_id() && 'show_regular_price' == get_option( 'yith_wcpo_guest_users_price', 'show_pre_order_price' ) ) {
				return false;
			}

			if ( 'manual' == $price_adjustment && empty( $manual_price ) ) {
				return $on_sale;
			}

			if ( 'yes' == $is_pre_order && 'yes' == get_option( 'yith_wcpo_show_regular_price' ) ) {
				$on_sale = true;
			}

			return $on_sale;
		}

		public function variable_price_range( $price, $variation, $product_variable ) {
			global $sitepress;

			$id                = $variation->get_id();
			$variation_id      = $sitepress ? yit_wpml_object_id( $id, 'product', true, $sitepress->get_default_language() ) : $id;
			$pre_order         = new YITH_Pre_Order_Product( $variation_id );
			$is_pre_order      = $pre_order->get_pre_order_status();
			$price_adjustment  = $pre_order->get_pre_order_price_adjustment();
			$manual_price      = $pre_order->get_pre_order_price();
			$adjustment_type   = $pre_order->get_pre_order_adjustment_type();
			$adjustment_amount = $pre_order->get_pre_order_adjustment_amount();

			if ( 'yes' == $is_pre_order ) {
				if ( ! get_current_user_id() ) {
					switch ( get_option( 'yith_wcpo_guest_users_price', 'show_pre_order_price' ) ) {
						case 'show_regular_price' :
							return $variation->get_regular_price();
						case 'hidden_price' :
							return '';
					}
				}

				return $this->compute_price( $price, $price_adjustment, $manual_price, $adjustment_type, $adjustment_amount );
			}

			return $price;
		}

		public function variable_product_label() {
			global $product, $sitepress;

			if ( 'yes' != get_option( 'yith_wcpo_variable_product_label_enabled', 'no' ) ) {
				return;
			}

			if ( 'variable' != $product->get_type() ) {
				return;
			}

			$children                   = $product->get_children();
			$all_children_are_pre_order = true;

			foreach ( $children as $child ) {
				$variation_id = $sitepress ? yit_wpml_object_id( $child, 'product', true, $sitepress->get_default_language() ) : $child;
				$pre_order    = new YITH_Pre_Order_Product( $variation_id );
				if ( 'yes' != $pre_order->get_pre_order_status() ) {
					$all_children_are_pre_order = false;
					break;
				}
			}

			if ( $all_children_are_pre_order ) {
				$label   = apply_filters( 'yith_wcpo_variable_product_label_content_value', get_option( 'yith_wcpo_variable_product_label_content' ) );
				$color   = apply_filters( 'yith_wcpo_variable_product_label_color_value', get_option( 'yith_wcpo_variable_product_label_color' ) );
				$style   = apply_filters( 'yith_wcpo_variable_product_label_style', 'color: ' . $color . ';', $color );
				$message = '<div class="ywpo_variable_product_label" style="' . $style . '">' . $label . '</div>';
				$message = apply_filters( 'yith_ywpo_variable_product_label', $message );
				echo $message;
			}
		}

		public function variable_product_label_on_loop( $text ) {
			$this->variable_product_label();

			return $text;
		}

		public function compute_price( $price, $price_adjustment, $manual_price, $adjustment_type, $adjustment_amount ) {
			if ( 'manual' == $price_adjustment ) {
				if ( ! empty( $manual_price ) ) {
					return (string) $manual_price;
				}
			} else if ( isset( $adjustment_amount ) ) {
				if ( 'fixed' == $adjustment_type ) {
					if ( 'discount' == $price_adjustment ) {
						$price = (float) $price - (float) $adjustment_amount;
						if ( 0 > $price ) {
							$price = (string) '0';
						}
					}
					if ( 'mark-up' == $price_adjustment ) {
						$price = (float) $price + (float) $adjustment_amount;
					}

					return (string) $price;
				}
				if ( 'percentage' == $adjustment_type ) {
					if ( 'discount' == $price_adjustment ) {
						$price = (float) $price - ( ( (float) $price * (float) $adjustment_amount ) / 100 );
					}
					if ( 'mark-up' == $price_adjustment ) {
						$price = (float) $price + ( ( (float) $price * (float) $adjustment_amount ) / 100 );
					}

					return (string) $price;
				}
			}

			return $price;
		}

		public function add_for_sale_date_order_item_meta( $item_id, $pre_order ) {
			wc_add_order_item_meta( $item_id, '_ywpo_item_for_sale_date', $pre_order->get_for_sale_date_timestamp() );
		}

		public function check_cart_mixing( $validation, $product_id, $quantity, $variation = 0 ) {
			global $sitepress;

			if ( 'yes' == get_option( 'yith_wcpo_mixing' ) || 'yes' == get_option( 'yith_wcpo_one_pre_order_in_cart' ) ) {
				if ( $variation ) {
					$id = $sitepress ? yit_wpml_object_id( $variation, 'product', true, $sitepress->get_default_language() ) : $variation;
				} else {
					$id = $sitepress ? yit_wpml_object_id( $product_id, 'product', true, $sitepress->get_default_language() ) : $product_id;
				}

				if ( $this->check_cart_is_mixed( $id ) ) {
					$message = $this->get_cart_mixing_error_message();
					wc_add_notice( $message, 'error' );

					return false;
				}
			}

			return $validation;
		}

		public function prevent_cart_mixing_on_restore_item( $cart_item_key, $cart ) {
			if ( 'yes' == get_option( 'yith_wcpo_one_pre_order_in_cart' ) ) {
				$pre_order = new YITH_Pre_Order_Product( $cart->get_cart_item( $cart_item_key )['data'] );
				if ( $this->cart_has_pre_order() && 'yes' == $pre_order->get_pre_order_status()) {
					add_filter( 'ywpo_cart_mixing_message', array( $this, 'get_one_pre_order_in_cart_error_message' ) );
					$message = $this->get_cart_mixing_error_message();
					wc_add_notice( $message, 'error' );
					$cart->remove_cart_item( $cart_item_key );
					exit;
				}
			}
			if ( 'yes' == get_option( 'yith_wcpo_mixing' ) ) {
				$pre_order = new YITH_Pre_Order_Product( $cart->get_cart_item( $cart_item_key )['data'] );
				$message = $this->get_cart_mixing_error_message();
				if ( 'yes' == $pre_order->get_pre_order_status() ) {
					foreach ( $cart->cart_contents as $key => $cart_item ) {
						if ( $cart_item_key === $key ) {
							continue;
						}
						$check_pre_order = new YITH_Pre_Order_Product( $cart_item['data'] );
						if ( 'yes' == $check_pre_order->get_pre_order_status() ) {
							continue;
						} else {
							wc_add_notice( $message, 'error' );
							$cart->remove_cart_item( $cart_item_key );
							exit;
						}
					}
				} else {
					foreach ( $cart->cart_contents as $key => $cart_item ) {
						if ( $cart_item_key === $key ) {
							continue;
						}
						$check_no_pre_order = new YITH_Pre_Order_Product( $cart_item['data'] );
						if ( 'yes' == $check_no_pre_order->get_pre_order_status() ) {
							wc_add_notice( $message, 'error' );
							$cart->remove_cart_item( $cart_item_key );
							exit;
						} else {
							continue;
						}
					}
				}
			}
		}

		public function check_cart_is_mixed( $id ) {
			global $sitepress;

			$product = new YITH_Pre_Order_Product( $id );


			if ( 'yes' == get_option( 'yith_wcpo_one_pre_order_in_cart' ) ) {
			    if ( $this->cart_has_pre_order() && 'yes' == $product->get_pre_order_status()) {
			        add_filter( 'ywpo_cart_mixing_message', array( $this, 'get_one_pre_order_in_cart_error_message' ) );
			        return true;
                }
            }
            if ( 'yes' == get_option( 'yith_wcpo_mixing' ) ) {
	            if ( 'yes' == $product->get_pre_order_status() ) {
		            $check_for = 'pre-order';
	            } else {
		            $check_for = 'regular';
	            }

	            foreach ( WC()->cart->cart_contents as $cart_item ) {
		            if ( $cart_item['variation_id'] ) {
			            $id = $sitepress ? yit_wpml_object_id( $cart_item['variation_id'], 'product', true, $sitepress->get_default_language() ) : $cart_item['variation_id'];
		            } else {
			            $id = $sitepress ? yit_wpml_object_id( $cart_item['product_id'], 'product', true, $sitepress->get_default_language() ) : $cart_item['product_id'];
		            }
		            $pre_order = new YITH_Pre_Order_Product( $id );

		            if ( 'pre-order' == $check_for ) {
			            if ( 'yes' != $pre_order->get_pre_order_status() ) {
				            return true;
			            }
		            } else if ( 'regular' == $check_for ) {
			            if ( 'yes' == $pre_order->get_pre_order_status() ) {
				            return true;
			            }
		            }
	            }
            }

			return false;
		}

		public function cart_has_pre_order() {
			global $sitepress;
		    if ( ! empty( WC()->cart ) ) {
			    foreach ( WC()->cart->cart_contents as $cart_item ) {
				    if ( $cart_item['variation_id'] ) {
					    $id = $sitepress ? yit_wpml_object_id( $cart_item['variation_id'], 'product', true, $sitepress->get_default_language() ) : $cart_item['variation_id'];
				    } else {
					    $id = $sitepress ? yit_wpml_object_id( $cart_item['product_id'], 'product', true, $sitepress->get_default_language() ) : $cart_item['product_id'];
				    }
				    $pre_order = new YITH_Pre_Order_Product( $id );
				    if ( 'yes' == $pre_order->get_pre_order_status() ) {
				        return true;
				    }
			    }
            }
            return false;
        }

		public function auto_badge_data( $data, $product ) {
			if ( ! $product ) {
				return $data;
			}
			$pre_order = new YITH_Pre_Order_Product( $product );
			if ( 'yes' == $pre_order->get_pre_order_status() && 'discount' == $pre_order->get_pre_order_price_adjustment() ) {
				$amount            = $pre_order->get_pre_order_adjustment_amount();
				$args              = array( 'decimals' => 0 );
				$price             = $product->get_price();
				$regular_price     = $product->get_regular_price();
				$saved_money_float = $regular_price - $price;
				$saved_money       = absint( $saved_money_float );
				$saved             = strip_tags( wc_price( $saved_money, $args ) );

				if ( 'fixed' == $pre_order->get_pre_order_adjustment_type() && $amount > 0 ) {
					$data['saved_money']       = $saved_money;
					$data['saved_money_float'] = $saved_money_float;
					$data['saved']             = $saved;
				}
				if ( 'percentage' == $pre_order->get_pre_order_adjustment_type() && $amount > 0 ) {
					$data['percentual_sale']   = $amount;
					$data['sale_percentage']   = $amount;
					$data['saved_money']       = $saved_money;
					$data['saved_money_float'] = $saved_money_float;
					$data['saved']             = $saved;
				}
			}

			return $data;
		}

		public function product_countdown_label( $label, $a, $product_id ) {

			$product     = wc_get_product( $product_id );
			$is_preorder = yit_get_prop( $product, '_ywpo_preorder' );

			if ( $is_preorder == 'yes' ) {
				$option = get_option( 'yith_wcpo_countdown_label' );
				if ( $option ) {
					$label = $option;
				}
			}

			return $label;
		}

		/*
		 * Flatsome fix for showing availability date in Quick View
		 *
		 * @since 1.3.2
		 */
		public function flatsome_fix() {
			?>
            <script type="text/javascript">
                jQuery('div.pre_order_single').each(function () {
                    var unix_time = parseInt(jQuery(this).data('time'));
                    var date = new Date(0);
                    date.setUTCSeconds(unix_time);
                    var time = date.toLocaleTimeString();
                    time = time.slice(0, -3);
                    jQuery(this).find('.availability_date').text(date.toLocaleDateString());
                    jQuery(this).find('.availability_time').text(time);
                });
            </script>
			<?php
		}

		/**
		 * Shortcode for displaying Pre-Order products
		 *
		 * @param $atts
		 *
		 * @return string
		 */
		public function pre_order_products_loop( $atts ) {
			$atts = shortcode_atts( array(
				'columns'        => '4',
				'orderby'        => 'title',
				'order'          => 'asc',
				'posts_per_page' => 8,
                'show_variable'  => false
			), $atts, 'products' );

			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

			$query_args = array(
				'post_type'           => array( 'product', 'product_variation' ),
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'columns'             => $atts['columns'],
				'orderby'             => $atts['orderby'],
				'order'               => $atts['order'],
				'posts_per_page'      => $atts['posts_per_page'],
				'paged'               => $paged,
				'meta_query'          => array(
					array(
						'key'     => '_ywpo_preorder',
						'value'   => 'yes',
						'compare' => '='
					)
				)
			);

			wp_register_script( 'yith-wcpo-frontend-shop-loop', YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'frontend-shop-loop.js' ), array( 'jquery' ), YITH_WCPO_VERSION, 'true' );
			wp_enqueue_script( 'yith-wcpo-frontend-shop-loop' );

			return self::product_loop( $query_args, $atts, 'yith_pre_order_products' );
		}

		/**
		 * Loop over found products.
		 *
		 * @param array $query_args
		 * @param array $atts
		 * @param string $loop_name
		 *
		 * @return string
		 */
		private static function product_loop( $query_args, $atts, $loop_name ) {
			global $woocommerce_loop;


			if ( isset( $atts['show_variable'] ) && $atts['show_variable'] ) {
				$query_args['post_type'] = 'product';
				$query_args_2 = $query_args;
				$query_args_2['post_type'] = 'product_variation';
				$query_args_2['posts_per_page'] = -1;


				$only_variations = get_posts( $query_args_2 );
				$pre_order_variable_ids = array();
				foreach ( $only_variations as $variation ) {
					$pre_order_variable_ids[$variation->post_parent] = '1';
                }
				$pre_order_variable_ids = array_keys( $pre_order_variable_ids );


                $only_simple = get_posts( $query_args );
				$pre_order_simple_ids = array();
                foreach ( $only_simple as $_post ) {
	                $pre_order_simple_ids[] = $_post->ID;
                }


				$all_pre_order_ids = array_merge( $pre_order_simple_ids, $pre_order_variable_ids );
				unset( $query_args['meta_query'] );

				$query_args['post__in'] = $all_pre_order_ids;
            }

			$products                    = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $query_args, $atts, $loop_name ) );
			$columns                     = absint( $atts['columns'] );
			$woocommerce_loop['columns'] = $columns;
			$woocommerce_loop['name']    = $loop_name;

			ob_start();
			if ( is_singular( 'product' ) ) {
				while ( have_posts() ) {
					the_post();
					wc_get_template_part( 'content', 'single-product' );
				}
			} else {
				if ( $products->have_posts() ) {
					do_action( "woocommerce_shortcode_before_{$loop_name}_loop" );
					woocommerce_product_loop_start();


					while ( $products->have_posts() ) {
						$products->the_post();
						wc_get_template_part( 'content', 'product' );
					} // end of the loop.

					woocommerce_product_loop_end();

					do_action( "woocommerce_shortcode_after_{$loop_name}_loop" );
					do_action( 'yith_wcpo_pagination_nav', $products->max_num_pages );

				} elseif ( ! woocommerce_product_subcategories( array(
					'before' => woocommerce_product_loop_start( false ),
					'after'  => woocommerce_product_loop_end( false )
				) ) ) {
					do_action( 'woocommerce_no_products_found' );
				}
			}

			woocommerce_reset_loop();
			wp_reset_postdata();

			return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
		}

		/**
		 * Prints template for displaying navigation panel for pagination
		 *
		 * @param $max_num_pages
		 */
		public function pagination_nav( $max_num_pages ) {
			ob_start();
			wc_get_template( 'frontend/yith-pre-order-pagination-nav.php', array( 'max_num_pages' => $max_num_pages ), '', YITH_WCPO_WC_TEMPLATE_PATH );
			echo ob_get_clean();
		}

		public function enqueue_scripts() {
			parent::enqueue_scripts();

			if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() || apply_filters( 'yith_ywpo_enqueue_script', false ) ) {
				wp_register_script( 'yith-wcpo-frontend-shop-loop', YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'frontend-shop-loop.js' ), array( 'jquery' ), YITH_WCPO_VERSION, 'true' );
				wp_enqueue_script( 'yith-wcpo-frontend-shop-loop' );
			}
			if ( apply_filters( 'yith_wcpo_allow_other_page', is_cart() ) ) {
				wp_register_script( 'yith-wcpo-frontend-cart', YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'frontend-cart.js' ), array( 'jquery' ), YITH_WCPO_VERSION, 'true' );
				wp_enqueue_script( 'yith-wcpo-frontend-cart' );
			}
			if ( is_account_page() || is_checkout() ) {
				wp_register_script( 'yith-wcpo-frontend-my-account', YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'frontend-my-account.js' ), array( 'jquery' ), YITH_WCPO_VERSION, 'true' );
				wp_enqueue_script( 'yith-wcpo-frontend-my-account' );
			}

			// YITH WooCommerce Subscription compatibility //
			if ( defined( 'YITH_YWSBS_VERSION' ) ) {
				$params = array(
					'add_to_cart_label' => get_option( 'ywsbs_add_to_cart_label' )
				);
			} else {
				$params = array(
					'add_to_cart_label'  => get_option( 'ywsbs_add_to_cart_label' ),
					'default_cart_label' => apply_filters( 'ywsbs_add_to_cart_default_label', esc_html__( 'Add to cart', 'woocommerce' ) )
				);
			}
			wp_localize_script( 'yith_ywsbs_frontend', 'yith_ywsbs_frontend', $params );
			/////////////////////////////////////////////////
		}

		public function get_cart_mixing_error_message() {
			$message = esc_html__( "Sorry, it's not possible to mix Regular Products and Pre-Order Products in the same cart", 'yith-pre-order-for-woocommerce' );

			return apply_filters( 'ywpo_cart_mixing_message', $message );
		}

		public function get_one_pre_order_in_cart_error_message() {
		    return esc_html__( "Sorry, it's not possible to add more than one Pre-Order product to the cart", 'yith-pre-order-for-woocommerce' );
        }

	}
}