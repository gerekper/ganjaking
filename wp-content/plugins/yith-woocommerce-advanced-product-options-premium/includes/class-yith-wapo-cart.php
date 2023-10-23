<?php
/**
 * WAPO Cart Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Cart' ) ) {

	/**
	 *  YITH_WAPO Cart Class
	 */
	class YITH_WAPO_Cart {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WAPO_Instance
		 */
		public static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WAPO_Instance
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			// Loop add to cart button.
			if ( 'select' === get_option( 'yith_wapo_button_in_shop', 'select' ) ) {
				add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 50, 1 );
				add_action( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 10, 1 );
				add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 50, 2 );
			}

			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_addons_validation' ), 50, 3 );

			// Add options to cart item.
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 25, 2 );
			add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'add_cart_item_data_order_again' ), 25, 3 );
			// Display custom product thumbnail in cart.
			if ( 'yes' === get_option( 'yith_wapo_show_image_in_cart', 'no' ) ) {
				add_filter( 'woocommerce_order_item_thumbnail', array( $this, 'order_item_thumbnail' ), 10, 2 );
				add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'cart_item_thumbnail' ), 10, 3 );
			}

			// Add to cart the total price of the item with the addons.
			add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 20, 1 );

			// Display options in cart and checkout page.
			add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 25, 2 );

			// Load cart data per page load.
			add_filter(
				'woocommerce_get_cart_item_from_session',
				array(
					$this,
					'get_cart_item_from_session',
				),
				100,
				2
			);
			// Update cart total
			// add_filter( 'woocommerce_calculated_total', array( $this, 'custom_calculated_total' ), 10, 2 );
			// Add order item meta.
			add_action( 'woocommerce_new_order_item', array( $this, 'add_order_item_meta' ), 10, 3 );

			// Product Bundles.
			add_filter( 'yith_wcpb_woocommerce_cart_item_price', array( $this, 'ywcpb_woocommerce_cart_item_price' ), 10, 3 );

			add_filter(
				'yith_wcpb_bundle_pip_bundled_items_subtotal',
				array(
					$this,
					'ywcpb_bundle_pip_bundled_items_subtotal',
				),
				10,
				3
			);

			add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'order_item_subtotal' ), 10, 3 );

		}

		/**
		 * Add to cart validation
		 *
		 * @param bool $passed Passed.
		 * @param int  $product_id Product ID.
		 *
		 * @return false|mixed
		 */
		public function add_to_cart_validation( $passed, $product_id ) {
            // Disable add_to_cart_button class on shop and product archive pages.
			$is_product_archive = is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy();

			if ( ( $is_product_archive || wp_doing_ajax() ) && ! isset( $_REQUEST['yith_wapo_is_single'] ) && yith_wapo_product_has_blocks( $product_id ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return false;
			}

			return $passed;
		}
		/**
		 * Add to cart validation for addons
		 *
		 * @param bool $passed Passed.
		 * @param int  $product_id Product ID.
		 *
		 * @return false|mixed
		 */
		public function add_to_cart_addons_validation( $passed, $product_id, $quantity ) {
			if ( $passed ) {
				try {
					$post_data = $_POST;
					$addons    = ! empty( $post_data['yith_wapo'] ) ? $post_data['yith_wapo'] : array();

					if ( is_array( $addons ) && ! empty( $addons ) ) {
						foreach ( $addons as $index => $option ) {
							foreach ( $option as $addon_option => $value ) {

								$values = YITH_WAPO::get_instance()->split_addon_and_option_ids( $addon_option, $value );

								$addon_id  = $values['addon_id'];
								$option_id = $values['option_id'];

								$info = yith_wapo_get_option_info( $addon_id, $option_id, false );

								if ( 'product' === $info['addon_type'] ) {
									$product_id = $info['product_id'];
									$product    = wc_get_product( $product_id );
                                    $quantity   = $post_data['yith_wapo_product_qty'][ $addon_option ] ?? $quantity;

									if ( $product ) {
										if ( ! $product->is_in_stock() ) {
											/* translators: %s: product name */
											$message = sprintf( _x( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', '[FRONT] Error message when an add-on type Product is out of stock', 'yith-woocommerce-product-add-ons' ), $product->get_name() );

											$message = apply_filters( 'yith_wapo_cart_addon_product_out_of_stock_message', $message, $product );
											throw new Exception( $message );
										}
										if ( ! $product->has_enough_stock( $quantity ) ) {
											$stock_quantity = $product->get_stock_quantity();

											/* translators: 1: product name 2: quantity in stock */
											$message = sprintf( _x( 'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining).', '[FRONT] When add-on type Product is added to cart with more amount than allowed', 'yith-woocommerce-product-add-ons' ), $product->get_name(), wc_format_stock_quantity_for_display( $stock_quantity, $product ) );

											$message = apply_filters( 'yith_wapo_cart_addon_product_not_enough_stock_message', $message, $product, $stock_quantity );

											throw new Exception( $message );
										}
										if ( $product->managing_stock() ) {

											$products_qty_in_cart = $this->get_cart_item_quantities();

											if ( isset( $products_qty_in_cart[ $addon_option ] ) && ! $product->has_enough_stock( $products_qty_in_cart[ $addon_option ] + $quantity ) ) {
												$stock_quantity         = $product->get_stock_quantity();
												$stock_quantity_in_cart = $products_qty_in_cart[ $addon_option ];

												$message = sprintf(
													'<a href="%s" class="button wc-forward">%s</a> %s',
													wc_get_cart_url(),
													_x( 'View cart', '[FRONT] Redirect to cart page', 'yith-woocommerce-product-add-ons' ),
													/* translators: 1: quantity in stock 2: current quantity */
													sprintf( _x( 'You cannot add that amount to the cart &mdash; we have %1$s in stock and you already have %2$s in your cart.', '[FRONT] If sum of already add-ons type Products added to cart and the current stock selected are higher than expected', 'yith-woocommerce-product-add-ons' ), wc_format_stock_quantity_for_display( $stock_quantity, $product ), wc_format_stock_quantity_for_display( $stock_quantity_in_cart, $product ) )
												);

												$message = apply_filters( 'yith_wapo_cart_addon_product_not_enough_stock_already_in_cart_message', $message, $product, $stock_quantity, $stock_quantity_in_cart );

												throw new Exception( $message );
											}
										}
									}
								}
							}
						}
					}
				} catch ( Exception $e ) {
					if ( $e->getMessage() ) {
						wc_add_notice( $e->getMessage(), 'error' );
					}
					$passed = false;
				}
			}

			return $passed;

		}

		/**
		 * Filter cart item from session.
		 *
		 * @param array $cart_item Cart item.
		 * @param array $values Add-ons options.
		 *
		 * @return mixed
		 */
		public function get_cart_item_from_session( $cart_item, $values ) {

			if ( ! empty( $values['yith_wapo_options'] ) ) {

				$cart_item['yith_wapo_options'] = $values['yith_wapo_options'];
				$cart_item                      = $this->add_cart_item( $cart_item );

				if ( isset( $cart_item['ywsbs-subscription-info'] ) ) {
					$cart_item['ywsbs-subscription-info']['recurring_price'] = $cart_item['data']->get_price();
				}
			}

			return $cart_item;
		}

		/**
		 * Set the data for the cart item in cart object.
		 *
		 * @param array $cart_item_data Cart item data.
		 * @param int   $product_id Product ID.
		 * @param array $post_data Post data.
		 * @param bool  $sold_individually Sold individually.
		 *
		 * @return mixed
		 */
		public function add_cart_item_data( $cart_item_data, $product_id, $post_data = null, $sold_individually = false ) {

			if ( is_null( $post_data ) ) {
				$post_data = $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			}

            // Check if the item data should be added based on filters
            if ( apply_filters('yith_wapo_add_item_data_check', true ) && ( isset( $cart_item_data['bundled_by'] ) || ( isset( $post_data['yith_wapo_product_id'] ) && intval( $product_id ) !== intval( $post_data['yith_wapo_product_id'] ) ) ) ) {
				return $cart_item_data;
			}
			$data = array();

            if ( isset( $post_data['yith_wapo'] ) && is_array( $post_data['yith_wapo'] ) ) {

                $product_image = 0;

                if ( isset( $post_data['yith_wapo_product_img'] ) && ! empty( $post_data['yith_wapo_product_img'] ) ) {
                    $image_url = $post_data['yith_wapo_product_img'];
                    if ( ! preg_match("~^(?:f|ht)tps?://~i", $post_data['yith_wapo_product_img'] ) ) {
                        $image_url = "http:" . $post_data['yith_wapo_product_img'];
                    }
                    $product_image = attachment_url_to_postid( $image_url );
                }

                $cart_item_data['yith_wapo_product_img'] = $product_image;

				foreach ( $post_data['yith_wapo'] as $index => $option ) {
					foreach ( $option as $key => $value ) {
						if ( isset( $post_data['yith_wapo_sell_individually'][ $key ] ) ) { // Check if need to bypass the addons if it will be sell as individual addons.
							if ( ! empty( $value ) ) {
								$cart_item_data['yith_wapo_product_has_individual_addons'] = true;
							}
							continue;
						}
						$cart_item_data['yith_wapo_options'][ $index ][ $key ] = $value;
						$data[ $key ] = $value;
					}
				}
			}

			if ( isset( $post_data['yith_wapo_product_qty'] ) && is_array( $post_data['yith_wapo_product_qty'] ) ) {
				foreach ( $post_data['yith_wapo_product_qty'] as $key => $value ) {
					if ( isset( $data[ $key ] ) ) {
						$cart_item_data['yith_wapo_qty_options'][ $key ] = $value;
					}
				}
			}

			return $cart_item_data;
		}

		/**
		 * Set the data for the cart item in cart object (Order again).
		 *
		 * @param array $cart_item_data Cart item data.
		 * @param WC_Order_Item $item The item object.
		 * @param WC_Order $order The Order Object.
		 *
		 * @return mixed
		 */
		public function add_cart_item_data_order_again( $cart_item_data, $item, $order ) {

			$item_id       = $item->get_id();
			$meta_data     = wc_get_order_item_meta( $item_id, '_ywapo_meta_data', true );
			$product_image = wc_get_order_item_meta( $item_id, '_ywapo_product_img', true );

			if ( ! empty( $meta_data ) ) {
				$cart_item_data['yith_wapo_options'] = $meta_data;
			}
			if ( ! empty( $product_image ) ) {
				$cart_item_data['yith_wapo_product_img'] = $product_image;
			}

			return $cart_item_data;
		}

		/**
		 * Filter Item before add to cart.
		 *
		 * @param array $cart_item Cart item.
		 *
		 * @return mixed
		 */
		public function add_cart_item( $cart_item ) {
			// Avoid sum addons price of child products of YITH Composite Products.
			if ( isset( $cart_item['yith_wcp_child_component_data'] ) ) {
				return $cart_item;
			}

			// Avoid sum addons price of child products of YITH Product Bundles.
			if ( isset( $cart_item['bundled_by'] ) ) {
				return $cart_item;
			}

			if ( isset( $cart_item['yith_wapo_sold_individually'] ) ) {
				return $cart_item;
			}

			$wapo_price = yit_get_prop( $cart_item['data'], 'yith_wapo_price' );

			if ( ! empty( $cart_item['yith_wapo_options'] ) && ! $wapo_price ) {
				$total_options_price      = 0;
				$first_free_options_count = 0;
				$sell_individually_product_id = false;
				$product_id               = isset( $cart_item['variation_id'] ) && ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

				if( isset( $cart_item['yith_wapo_individual_addons'] ) && isset( $cart_item['yith_wapo_product_id'] ) && ! empty( $cart_item['yith_wapo_product_id'] ) ) { // Individually product
					$sell_individually_product_id = $product_id;
					$product_id                   = $cart_item['yith_wapo_product_id'];
				}

				$_product                 = wc_get_product( $product_id );
				$_individually_product    = wc_get_product( $sell_individually_product_id );

				// WooCommerce Measurement Price Calculator (compatibility).
				if ( isset( $cart_item['pricing_item_meta_data']['_price'] ) ) {
					$product_price = $cart_item['pricing_item_meta_data']['_price'];
				} else {
                    if ( apply_filters( 'yith_wapo_get_product_price_excluding_tax', true ) && ! wc_prices_include_tax() && 'incl' === get_option( 'woocommerce_tax_display_cart' ) ) {
                        $product_price = wc_get_price_excluding_tax( $_product ); // Calculate the add-ons taxes on cart depending on real product price (without taxes).
                    } else {
                        $product_price = yit_get_display_price( $_product );
                    }
				}
				$addon_id_check = '';

				foreach ( $cart_item['yith_wapo_options'] as $index => $option ) {
					foreach ( $option as $key => $value ) {
						if ( $key && '' !== $value ) {

							$values = YITH_WAPO::get_instance()->split_addon_and_option_ids( $key, $value );

							$addon_id  = $values['addon_id'];
							$option_id = $values['option_id'];

							if ( $addon_id !== $addon_id_check ) {
								$first_free_options_count = 0;
								$addon_id_check           = $addon_id;
							}

                            $info                   = yith_wapo_get_option_info( $addon_id, $option_id );
							$addon_type              = $info['addon_type'] ?? '';
							$first_options_selected = $info['addon_first_options_selected'] ?? '';
							$first_options_qty      = intval( $info['addon_first_free_options'] ) ?? 0;
							$price_method           = $info['price_method'] ?? '';
							$sell_individually      = $info['sell_individually'] ?? '';

							$is_empty_select = 'select' === $addon_type && 'default' === $option_id;

							if ( $is_empty_select ) {
								continue;
							}

                            $calculate_taxes = false;

							if ( wc_string_to_bool( $sell_individually ) && ( $_individually_product instanceof WC_Product && 'zero-rate' === $_individually_product->get_tax_class() ) ) {
                                $calculate_taxes = true;
                            }

							$addon_prices = $this->calculate_addon_prices_on_cart( $addon_id, $option_id, $key, $value, $cart_item, $product_price, $calculate_taxes );

							$option_price     = 0;
							$addon_price      = abs( floatval( $addon_prices['price'] ) );
							$addon_sale_price = abs( floatval( $addon_prices['price_sale'] ) );

							// First X free options check.
							if ( 'yes' === $first_options_selected && 0 < $first_options_qty && $first_free_options_count < $first_options_qty ) {
								$first_free_options_count ++;
							} else {
								if ( $addon_price !== 0 || $addon_sale_price !== 0 ) {
									if ( $addon_sale_price ) {
										$option_price = $addon_sale_price;
									} else {
										$option_price = $addon_price;
									}
								}
							}
							if ( 'decrease' === $price_method ) {
									$total_options_price -= floatval( $option_price );
							} else {
									$total_options_price += floatval( $option_price );
							}

						}
					}
				}

				$cart_item_price  = is_numeric( $cart_item['data']->get_price() ) ? ( $cart_item['data']->get_price() ) : 0;
				$total_item_price = apply_filters( 'yith_wapo_total_item_price', $cart_item_price + $total_options_price );

				$cart_item['data']->set_price( $total_item_price );
				$cart_item['yith_wapo_item_price']          = $cart_item_price;
				$cart_item['yith_wapo_total_options_price'] = $total_options_price;
				yit_set_prop( $cart_item['data'], 'yith_wapo_price', true );

			}

			return $cart_item;
		}

		/**
		 * Change the product image with the addon one (if selected).
		 *
		 * @param string $_product_img Product image.
		 * @param array  $cart_item Cart item.
		 * @param string $cart_item_key Cart item key.
		 *
		 * @return mixed|string
		 */
		public function cart_item_thumbnail( $_product_img, $cart_item, $cart_item_key ) {
			if ( ! empty( $cart_item['yith_wapo_product_img'] ) ) {
				$_product_img = wp_get_attachment_image( $cart_item['yith_wapo_product_img'] );
			}

            return $_product_img;
		}

		/**
		 * Change product image in order if replaced by add-ons
		 *
		 * @param string                $image The image.
		 * @param WC_Order_Item_Product $item The item object.
		 * @return string
		 */
		public function order_item_thumbnail( $image, $item ) {
			$wapo_image = $item->get_meta( '_ywapo_product_img' );

			if ( ! empty( $wapo_image ) ) {
				$image = wp_get_attachment_image( $wapo_image );
			}

			return $image;
		}

		/**
		 * Update cart items info.
		 *
		 * @param array $cart_data Cart data.
		 * @param array $cart_item Cart item.
		 *
		 * @return mixed
		 */
		public function get_item_data( $cart_data, $cart_item ) {

			// Avoid show addons of child products of YITH Composite Products.
			if ( isset( $cart_item['yith_wcp_child_component_data'] ) ) {
				return $cart_data;
			}

			$grouped_in_cart = ! apply_filters( 'yith_wapo_show_options_grouped_in_cart', true );

			$product_parent_id = yit_get_base_product_id( $cart_item['data'] );

			if ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] > 0 ) {
				$base_product = new WC_Product_Variation( $cart_item['variation_id'] );
			} else {
				$base_product = wc_get_product( $product_parent_id );
			}

			if ( is_object( $base_product ) && ! empty( $cart_item['yith_wapo_options'] ) &&
				isset( $cart_item['yith_wapo_product_has_individual_addons'] ) && 1 === intval( $cart_item['yith_wapo_product_has_individual_addons'] ) &&
				! isset( $cart_item['deposit'] )
			) {

				if ( 'yith_bundle' === $base_product->get_type() && true === $base_product->per_items_pricing && function_exists( 'YITH_WCPB_Frontend_Premium' ) && method_exists( yith_wcpb_frontend(), 'format_product_subtotal' ) ) {
					$price = yith_wcpb_frontend()->calculate_bundled_items_price_by_cart( $cart_item );
				} else {
					$price = yit_get_display_price( $base_product );
				}

				$price_html = wc_price( $price );

				$cart_data[] = array(
					'name'  => _x( 'Base price', '[FRONT] Label shown on YITH Bundle products when is added to cart.', 'yith-woocommerce-product-add-ons' ),
					'value' => $price_html,
				);
			}

			if ( ! empty( $cart_item['yith_wapo_options'] ) ) {
				// $total_options_price = 0; phpcs:ignore Squiz.PHP.CommentedOutCode.Found
				$cart_data_array          = array();
				$first_free_options_count = 0;
				$product_id               = isset( $cart_item['variation_id'] ) && ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
				$product_id               = isset( $cart_item['yith_wapo_individual_addons'] ) && isset( $cart_item['yith_wapo_product_id'] ) && ! empty( $cart_item['yith_wapo_product_id'] ) ? $cart_item['yith_wapo_product_id'] : $product_id;
				$_product                 = wc_get_product( $product_id );

				// WooCommerce Measurement Price Calculator (compatibility).
				if ( isset( $cart_item['pricing_item_meta_data']['_price'] ) ) {
					$product_price = $cart_item['pricing_item_meta_data']['_price'];
				} else {
					$product_price = yit_get_display_price( $_product );
				}
				$addon_id_check = '';

				foreach ( $cart_item['yith_wapo_options'] as $index => $option ) {
					foreach ( $option as $key => $value ) {
						if ( $key && '' !== $value ) {

                            if ( apply_filters( 'yith_wapo_option_on_cart', false, $option ) ) {
                                continue;
                            }

							$values = YITH_WAPO::get_instance()->split_addon_and_option_ids( $key, $value );

							$addon_id  = $values['addon_id'];
							$option_id = $values['option_id'];

							if ( $addon_id !== $addon_id_check ) {
								$first_free_options_count = 0;
								$addon_id_check           = $addon_id;
							}

							$addon_data_name = $this->get_addon_data_name( $addon_id, $option_id, $grouped_in_cart );
							$addon_value     = $this->get_addon_value_on_cart( $addon_id, $option_id, $key, $value, $cart_item, $grouped_in_cart );
							$addon_prices    = $this->calculate_addon_prices_on_cart( $addon_id, $option_id, $key, $value, $cart_item, $product_price );

                            $option_price     = 0;
                            $addon_price      = abs( floatval( $addon_prices['price'] ) );
                            $addon_sale_price = abs( floatval( $addon_prices['price_sale'] ) );
							$sign             = $addon_prices['sign'];

							$info                         = yith_wapo_get_option_info( $addon_id, $option_id );
							$addon_type                   = isset( $info['addon_type'] ) ? $info['addon_type'] : '';
							$addon_first_options_selected = isset( $info['addon_first_options_selected'] ) ? $info['addon_first_options_selected'] : '';

							$is_empty_select = 'select' === $addon_type && 'default' === $option_id;

							// First X free options check.
							if ( 'yes' === $addon_first_options_selected && $first_free_options_count < $addon_first_options_selected ) {
								$first_free_options_count ++;
							} else {
								if ( $addon_price !== 0 || $addon_sale_price !== 0 ) {
									if ( $addon_sale_price ) {
										$option_price = $addon_sale_price;
									} else {
										$option_price = $addon_price;
									}
								}
							}

							$option_price = '' !== $option_price ? $option_price : 0;
							$option_price = apply_filters( 'yith_wapo_addon_prices_on_cart', $option_price );

							if ( empty( $addon_value ) ) {
								$addon_value = '<span>' . $addon_value . '</span>';
							}

							$addon_value = apply_filters( 'yith_wapo_addon_value_on_cart', $addon_value, $key );

							if ( 'yes' === get_option( 'yith_wapo_show_options_in_cart', 'yes' ) ) {
								if ( ! $is_empty_select ) {
									if ( ! isset( $cart_data_array[ $addon_data_name ] ) ) {
										$cart_data_array[ $addon_data_name ] = '';
									}

									$cart_data_array[ $addon_data_name ] .= '<div>' . $this->get_addon_display_on_cart( $addon_value, $sign, $option_price, $addon_price, $addon_sale_price ) . '</div>';
								}
							}

							if ( $grouped_in_cart ) {
								if ( ! $is_empty_select ) {
									$cart_data[] = array(
										'name'    => $addon_data_name,
										'display' => empty( $option_price ) ? $addon_value : '<div>' . $this->get_addon_display_on_cart( $addon_value, $sign, $option_price, $addon_price, $addon_sale_price ) . '</div>',
									);
								}
							}
						}
					}
				}
				if ( ! $grouped_in_cart ) {
                    foreach ( $cart_data_array as $key => $value ) {
						$key = rtrim( $key, ':' );
						if ( '' === $key ) {
							$key = _x( 'Option', '[FRONT] Show it in the cart page if the add-on has not a label set', 'yith-woocommerce-product-add-ons' );
						}
						$cart_data[] = array(
							'name'    => $key,
							'display' => stripslashes( $value ),
						);
					}
				}
			}

            return apply_filters( 'yith_wapo_cart_data', $cart_data, $cart_item );
		}

		/**
		 * Format add-on data to display on the cart and checkout
		 * @param mixed $value Add-on value
		 * @param string $sign Add-on price sign
		 * @param mixed $price Add-on final price
		 * @param mixed $regular_price Add-on regular price
		 * @param mixed $sale_price Add-on sale price
		 * @return string Formatted add-on data
		 */
		public function get_addon_display_on_cart( $value, $sign, $price, $regular_price, $sale_price ) {
			$display = $value . ( '' !== $price && floatval( 0 ) !== floatval( $price ) ? ' (' . $sign . wc_price( $price ) . ')' : '' );
			return apply_filters( 'yith_wapo_addon_display_on_cart', $display, $value, $sign, $price, $regular_price, $sale_price );
		}

		/**
		 * Add order item meta
		 *
		 * @param int    $item_id Item ID.
		 * @param array  $cart_item Cart item.
		 * @param string $cart_item_key Cart item key.
		 */
		public function add_order_item_meta( $item_id, $cart_item, $cart_item_key ) {

			if ( is_object( $cart_item ) && property_exists( $cart_item, 'legacy_values' ) ) {
				$cart_item = $cart_item->legacy_values;
			}

			$addon_id_check = '';

            $quantity = $cart_item['quantity'] ?? 1;

			if ( isset( $cart_item['yith_wapo_options'] ) && ! isset( $cart_item['yith_wcp_child_component_data'] ) ) {

				$grouped_in_cart = ! apply_filters( 'yith_wapo_show_options_grouped_in_cart', true );

				foreach ( $cart_item['yith_wapo_options'] as $index => $option ) {
					foreach ( $option as $key => $value ) {
						if ( $key && '' !== $value ) {

                            if ( apply_filters( 'yith_wapo_option_on_order', false, $option ) ) {
                                continue;
                            }

							$values = YITH_WAPO::get_instance()->split_addon_and_option_ids( $key, $value );

							$addon_id  = $values['addon_id'];
							$option_id = $values['option_id'];

							if ( $addon_id !== $addon_id_check ) {
								$first_free_options_count = 0;
								$addon_id_check           = $addon_id;
							}

							// Check Product price
							$_product = wc_get_product( $cart_item['product_id'] );
							// WooCommerce Measurement Price Calculator (compatibility).
							if ( isset( $cart_item['pricing_item_meta_data']['_price'] ) ) {
								$product_price = $cart_item['pricing_item_meta_data']['_price'];
							} else {
								$product_price = floatval( $_product->get_price() );
							}

							$addon_name   = $this->get_addon_data_name( $addon_id, $option_id, $grouped_in_cart );
							$addon_value  = $this->get_addon_value_on_cart( $addon_id, $option_id, $key, $value, $cart_item, $grouped_in_cart );
							$addon_prices = $this->calculate_addon_prices_on_cart( $addon_id, $option_id, $key, $value, $cart_item, $product_price );

							$option_price     = 0;
                            $addon_price      = abs( floatval( $addon_prices['price'] ) );
                            $addon_sale_price = abs( floatval( $addon_prices['price_sale'] ) );
							$sign             = $addon_prices['sign'];

							$info                         = yith_wapo_get_option_info( $addon_id, $option_id );
							$addon_type                   = isset( $info['addon_type'] ) ? $info['addon_type'] : '';
							$addon_first_options_selected = isset( $info['addon_first_options_selected'] ) ? $info['addon_first_options_selected'] : '';

							$is_empty_select = 'select' === $addon_type && 'default' === $option_id;

							if ( $is_empty_select ) {
								continue;
							}

							if ( 'product' === $addon_type ) {

                                if ( ! isset( $cart_item['yith_wapo_qty_options'][ $key ] ) ) {
                                    $cart_item['yith_wapo_qty_options'][ $key ] = $quantity;
                                }

								$option_product_info = explode( '-', $value );
								$option_product_id   = $option_product_info[1];
								$option_product_qty  = isset( $cart_item['yith_wapo_qty_options'][ $key ] ) ? $cart_item['yith_wapo_qty_options'][ $key ] : 1;
								$option_product      = wc_get_product( $option_product_id );
								if ( $option_product && $option_product instanceof WC_Product ) {
									// Stock.
									if ( $option_product->get_manage_stock() ) {
										$stock_qty = $option_product->get_stock_quantity() - $option_product_qty;
										wc_update_product_stock( $option_product, $stock_qty, 'set' );
										wc_delete_product_transients( $option_product );
									}

									if ( isset( $cart_item['yith_wapo_qty_options'] ) ) {
										wc_add_order_item_meta( $item_id, '_ywapo_product_addon_qty', $cart_item['yith_wapo_qty_options'] );
									}
								}
							}

							// First X free options check.
							if ( 'yes' === $addon_first_options_selected && $first_free_options_count < $addon_first_options_selected ) {
								$first_free_options_count ++;
							} else {
								if ( $addon_price !== 0 || $addon_sale_price !== 0 ) {
									if ( $addon_sale_price ) {
										$option_price = $addon_sale_price;
									} else {
										$option_price = $addon_price;
									}
								}
							}

							if ( '' === $addon_name ) {
								$addon_name = apply_filters( 'yith_wapo_order_item_meta_name_default', _x( 'Option', '[FRONT] Show it in the cart page if the add-on has not a label set', 'yith-woocommerce-product-add-ons' ), $index, $item_id, $cart_item );
							}

							$addon_value  = apply_filters( 'yith_wapo_addon_value_as_order_item', $addon_value, $key );
							$option_price = apply_filters( 'yith_wapo_addon_prices_as_order_item', $option_price );

							$display_value = $this->get_addon_display_on_cart( $addon_value, $sign, $option_price, $addon_price, $addon_sale_price );
                            $display_value = html_entity_decode( stripslashes( $display_value ) );

							wc_add_order_item_meta( $item_id, $addon_name, $display_value );
						}
					}
				}
				wc_add_order_item_meta( $item_id, '_ywapo_meta_data', $cart_item['yith_wapo_options'] );
				if ( ! empty( $cart_item['yith_wapo_product_img'] ) ) {
					wc_add_order_item_meta( $item_id, '_ywapo_product_img', $cart_item['yith_wapo_product_img'] );
				}
			}
		}

		/**
		 * Add to cart URL
		 *
		 * @param string $url URL.
		 *
		 * @return false|string|WP_Error
		 */
		public function add_to_cart_url( string $url = '' ) {

            if ( is_product() ) {
                return $url;
            }

			global $product;
			$product_id = yit_get_base_product_id( $product );

            if ( yith_wapo_product_has_blocks( $product_id ) ) {
				return get_permalink( $product_id );
			}

			return $url;
		}

		/**
		 * Add to cart text
		 *
		 * @param string $text Text.
		 *
		 * @return false|mixed|string|void
		 */
		public function add_to_cart_text( string $text = '' ) {
			global $product, $post;

			if ( is_object( $product ) && ! is_single( $post ) && ! is_product() ) {
				$_product_id = apply_filters( 'yith_wapo_get_original_product_id', $product->get_id() );
                if ( yith_wapo_product_has_blocks( $_product_id ) ) {
					return get_option( 'yith_wapo_select_options_label', esc_html_x( 'Select options', '[FRONT] Default label on archive pages if the product has add-ons', 'yith-woocommerce-product-add-ons' ) );
				}
			}

			return $text;
		}

		/**
		 * Filter price in cart for items included in a bundle (support for YITH WooCommerce Product Bundle).
		 *
		 * @param string $price Cart item price.
		 * @param float  $bundled_items_price Bundle items price.
		 * @param array  $cart_item Cart item.
		 *
		 * @return string
		 */
		public function ywcpb_woocommerce_cart_item_price( $price, $bundled_items_price, $cart_item ) {

			if ( isset( $cart_item['yith_wapo_options'] ) ) {

				$types_total_price = $this->get_total_add_ons_price( $cart_item );

				if ( isset( $cart_item['yith_wapo_sold_individually'] ) && $cart_item['yith_wapo_sold_individually'] ) {
					$bundled_items_price = 0;
				}

				$price = wc_price( $bundled_items_price + $types_total_price );

			}

			return $price;
		}

		/**
		 * Filter bundles item subtotal (support for YITH WooCommerce Product Bundles)
		 *
		 * @param string $subtotal Bundle item subtotal.
		 * @param array  $cart_item Cart item.
		 * @param string $bundle_price Bundle price.
		 *
		 * @return mixed
		 */
		public function ywcpb_bundle_pip_bundled_items_subtotal( $subtotal, $cart_item, $bundle_price ) {

			if ( isset( $cart_item['yith_wapo_options'] ) ) {

				if ( method_exists( yith_wcpb_frontend(), 'format_product_subtotal' ) ) {
					$types_total_price = $this->get_total_add_ons_price( $cart_item );

					if ( isset( $cart_item['yith_wapo_sold_individually'] ) && $cart_item['yith_wapo_sold_individually'] ) {
						$bundle_price = 0;
					}

					$subtotal = yith_wcpb_frontend()->format_product_subtotal( $cart_item['data'], $bundle_price + $types_total_price );
				}
			}

			return $subtotal;
		}

		/**
		 * Get total price for add-ons
		 *
		 * @param array $cart_item Cart item.
		 *
		 * @return int
		 */
		public function get_total_add_ons_price( $cart_item ) {

			$product_id = isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : null;

			$type_list         = $this->get_cart_wapo_options( $cart_item, 'all' );
			$types_total_price = $this->get_total_by_add_ons_list( $type_list, $cart_item );

			return $types_total_price;

		}

		/**
		 * Filter cart item and add add-ons options
		 *
		 * @param array  $cart_item Cart item.
		 * @param string $type Option type.
		 *
		 * @return array
		 */
		public function get_cart_wapo_options( $cart_item, $type = 'all' ) {

			$cart_item_filtered = array();

			if ( isset( $cart_item['yith_wapo_options'] ) ) {

				if ( isset( $cart_item['yith_wapo_sold_individually'] ) ) {
					if ( $cart_item['yith_wapo_sold_individually'] ) {
						$type = 'sold_individually';
					} else {
						$type = 'simple';
					}
				}
				foreach ( $cart_item['yith_wapo_options'] as $key => $single_type_option ) {

					if ( 'all' === $type ) {
						$cart_item_filtered [ $key ] = $single_type_option;
					} elseif ( 'sold_individually' === $type && isset( $single_type_option['sold_individually'] ) && $single_type_option['sold_individually'] ) {
						$cart_item_filtered [ $key ] = $single_type_option;
					} elseif ( 'simple' === $type && ( ! isset( $single_type_option['sold_individually'] ) || ( isset( $single_type_option['sold_individually'] ) && ! $single_type_option['sold_individually'] ) ) ) {
						$cart_item_filtered[ $key ] = $single_type_option;
					}
				}
			}

			return $cart_item_filtered;
		}

		/**
		 * Get total price for add-ons list
		 *
		 * @param array $type_list Type list.
		 * @param array $cart_item The cart item.
		 *
		 * @return int
		 */
		private function get_total_by_add_ons_list( $type_list, $cart_item ) {

			$option_price = 0;
			$total_price  = 0;

			$product_id = isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : $cart_item['item_meta']['_product_id'][0];
			$_product   = wc_get_product( $product_id );
			// WooCommerce Measurement Price Calculator (compatibility).
			if ( isset( $cart_item['pricing_item_meta_data']['_price'] ) ) {
				$product_price = $cart_item['pricing_item_meta_data']['_price'];
			} else {
				$product_price = yit_get_display_price( $_product );
			}
			foreach ( $type_list as $list ) {
				foreach ( $list as $key => $value ) {
					if ( $key && '' !== $value ) {
						$values = YITH_WAPO::get_instance()->split_addon_and_option_ids( $key, $value );

						$addon_id  = $values['addon_id'];
						$option_id = $values['option_id'];

						$info = yith_wapo_get_option_info( $addon_id, $option_id );

						//TODO: use the price calculation method > calculate_addon_prices_on_cart
						if ( 'percentage' === $info['price_type'] ) {

							$option_percentage      = floatval( $info['price'] );
							$option_percentage_sale = floatval( $info['price_sale'] );
							$option_price           = ( $product_price / 100 ) * $option_percentage;
							$option_price_sale      = ( $product_price / 100 ) * $option_percentage_sale;
						} elseif ( 'multiplied' === $info['price_type'] ) {
							$option_price      = $info['price'] * $value;
							$option_price_sale = $info['price_sale'] * $value;
						} elseif ( 'characters' === $info['price_type'] ) {
							$remove_spaces     = apply_filters( 'yith_wapo_remove_spaces', false );
							$value             = $remove_spaces ? str_replace( ' ', '', $value ) : $value;
							$value_length      = function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );
							$option_price      = floatval( $info['price'] ) * $value_length;
							$option_price_sale = floatval( $info['price_sale'] ) * $value_length;
						} else {
							$option_price      = $info['price'];
							$option_price_sale = $info['price_sale'];
						}

						if ( 'number' === $info['addon_type'] ) {
							if ( 'value_x_product' === $info['price_method'] ) {
								$option_price = $value * $product_price;
							} else {
								if ( 'multiplied' === $info['price_type'] ) {
									$option_price = $value * $info['price'];
								}
							}
						}

						$option_price = $option_price_sale > 0 ? $option_price_sale : $option_price;

						if ( in_array( $info['addon_type'], array( 'product' ), true ) ) {
							$option_product_info = explode( '-', $value );
							$option_product_id   = $option_product_info[1];
							$option_product      = wc_get_product( $option_product_id );

							// Product prices.
							$product_price = $option_product instanceof WC_Product ? $option_product->get_price() : 0;
							if ( 'product' === $info['price_method'] ) {
								$option_price = $product_price;
							} elseif ( 'discount' === $info['price_method'] ) {
								$option_discount_value = floatval( $info['price'] );
								if ( 'percentage' === $info['price_type'] ) {
									$option_price = $product_price - ( ( $product_price / 100 ) * $option_discount_value );
								} else {
									$option_price = $product_price - $option_discount_value;
								}
							}
						}
						$option_price = apply_filters( 'yith_wapo_addon_prices_on_bundle_cart_item', $option_price );
					}
				}
				$total_price += (float) $option_price;
			}

			return apply_filters( 'yith_wapo_get_total_by_add_ons_list', $total_price, $type_list, $cart_item );
		}

		/**
		 * Return Order item subtotal
		 *
		 * @param string $product_sub_total Product subtotal.
		 * @param array  $item Order Item data.
		 * @param object $order WC Order object.
		 *
		 * @return string
		 */
		public function order_item_subtotal( $product_sub_total, $item, $order ) {

			if ( isset( $item['item_meta']['_ywapo_meta_data'] ) && isset( $item['item_meta']['_bundled_items'][0] ) ) {

				$type_list         = maybe_unserialize( $item['item_meta']['_ywapo_meta_data'] );
				$types_total_price = $this->get_total_by_add_ons_list( $type_list, $item );

				$tax_display = $order->tax_display_cart;

				if ( 'excl' === $tax_display ) {
					$ex_tax_label      = $order->prices_include_tax ? 1 : 0;
					$product_sub_total = wc_price(
						$order->get_line_subtotal( $item ) + $types_total_price,
						array(
							'ex_tax_label' => $ex_tax_label,
							'currency'     => $order->get_order_currency(),
						)
					);
				} else {
					$product_sub_total = wc_price( $order->get_line_subtotal( $item, true ) + $types_total_price, array( 'currency' => $order->get_order_currency() ) );
				}
			}

			return $product_sub_total;
		}

		/**
		 * Get addon cart items quantities - merged so we can do accurate stock checks on items across multiple lines.
		 *
		 * @return array
		 */
		public function get_cart_item_quantities() {
			$quantities = array();

			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

				if ( isset( $values['yith_wapo_qty_options'] ) ) {
					foreach ( $values['yith_wapo_qty_options'] as $key => $quantity ) {
						$quantities[ $key ] = isset( $quantities[ $key ] ) ? $quantities[ $key ] + $quantity : $quantity;
					}
				}
			}
			return $quantities;
		}

        /**
         * Calculate the add-on price individually on cart
         *
         * @param int    $addon_id The add-on id.
         * @param int    $option_id The option id.
         * @param int    $key The key.
         * @param string $value The value
         * @param array  $cart_item The cart item.
         * @param float  $product_price The product price.
         * @return array
         */
		public function calculate_addon_prices_on_cart( $addon_id, $option_id, $key, $value, $cart_item, $product_price, $calculate_taxes = true ) {

			$info = yith_wapo_get_option_info( $addon_id, $option_id, $calculate_taxes );

			$price_method = isset( $info['price_method'] ) ? $info['price_method'] : ''; // free, increase, decrease, product(product), discount(product), value_x_product(number)
			$price_type   = isset( $info['price_type'] ) ? $info['price_type'] : '';   // fixed, percentage, multiplied(number), characters(text, textarea)
			$price        = isset( $info['price'] ) ? $info['price'] : '';
			$price_sale   = isset( $info['price_sale'] ) ? $info['price_sale'] : '';

			$option_product_qty  = isset( $cart_item['yith_wapo_qty_options'][ $key ] ) ? $cart_item['yith_wapo_qty_options'][ $key ] : 1;

			$addon_price      = 0;
			$addon_price_sale = 0;

			switch ( $price_method ) {
				case 'increase':
				case 'decrease':
					$addon_price      = $this->get_addon_price( $price, $price_method, $price_type, $product_price, $value );
					$addon_price_sale = $this->get_addon_price( $price_sale, $price_method, $price_type, $product_price, $value );
					break;
				case 'product':
				case 'discount':
					$option_product_info = explode( '-', $value );
					$option_product_id   = isset( $option_product_info[1] ) ? $option_product_info[1] : '';
					$option_product      = wc_get_product( $option_product_id );
					if ( $option_product instanceof WC_Product ) {

						$product_price_addon = $calculate_taxes ? wc_get_price_to_display( $option_product ) : $option_product->get_price();
						if ( 'product' === $price_method ) { // Use price of linked product.
							$addon_price = $product_price_addon;
						}
						if ( 'discount' === $price_method ) { // Discount price of linked product.
							$option_discount_value = floatval( $price );
							if ( 'percentage' === $price_type ) {
								$addon_price = $product_price_addon - ( ( $product_price_addon / 100 ) * $option_discount_value );
							} else {
								$addon_price = $product_price_addon - $option_discount_value;
							}
						}
						break;
					}
				case 'value_x_product': // Value multiplied by product price.
                    if ( is_numeric( $value ) ) {
                        $addon_price = $value * $product_price;
                    }
					break;
				default:
					break;
			}

			$addon_price      = ! empty( (float) $addon_price ) ? (float) $addon_price * $option_product_qty : 0;
			$addon_price_sale = ! empty( (float) $addon_price_sale ) ? (float) $addon_price_sale * $option_product_qty : 0;

			return array(
	            'price'      => $addon_price,
	            'price_sale' => $addon_price_sale,
	            'sign'       => apply_filters( 'yith_wapo_price_sign', 'decrease' === $price_method ? '-' : '+' ),
			);

		}

        /**
         * Get the add-on price for 'increase' and 'decrease' price methods.
         *
         * @param float  $price The price.
         * @param string $price_method The price method.
         * @param string $price_type The price type.
         * @param float  $product_price The product price.
         * @param string $value The value.
         * @return float
         */
		public function get_addon_price( &$price, $price_method, $price_type, $product_price, $value ) {

            if ( ! is_numeric( $price ) || ! is_numeric( $product_price ) ) {
                return $price;
            }

		    if ( $price > 0 ) {
                if ( 'fixed' === $price_type ) {
                    if ( 'decrease' === $price_method ) {
                        $price = - $price;
                    }
                } elseif ( 'percentage' === $price_type ) {
                    $price = ( $product_price * $price ) / 100;
                    if ( 'decrease' === $price_method ) {
                        $price = - $price;
                    }
                } elseif ( 'characters' === $price_type ) {
	                $remove_spaces        = apply_filters( 'yith_wapo_remove_spaces', false );
	                $value                = $remove_spaces ? str_replace( ' ', '', $value ) : $value;
					$number_of_characters = function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );

					$price = $price * $number_of_characters;
                } elseif ( 'multiplied' === $price_type ) {
                    $price = $price * $value;
                }
            }

            return $price;

		}

		/**
		 * Get the add-on value on cart.
		 *
		 * @param int    $addon_id The add-on id.
		 * @param int    $option_id The option id.
		 * @param int    $key The key.
		 * @param string $value The value
		 * @param array  $cart_item The cart item.
		 *
		 * @return mixed|string
		 */
		public function get_addon_value_on_cart( $addon_id, $option_id, $key, $original_value, $cart_item, $grouped_in_cart = false ) {

            $info              = yith_wapo_get_option_info( $addon_id, $option_id );
            $addon_title       = $info['addon_label'] ?? '';
            $title_in_cart     = $info['title_in_cart'] ?? '';
            $title_in_cart_opt = $info['title_in_cart_opt'] ?? '';
            $addon_type        = $info['addon_type'] ?? '';   // checkbox, radio, select, product, color, colorpicker, file, number, date, label, text, textarea
            $label             = $info['label'] ?? '';
            $label_in_cart     = $info['label_in_cart'] ?? '';
            $label_in_cart_opt = $info['label_in_cart_opt'] ?? '';
			$color             = $info['color'] ?? '';
			$color_b           = $info['color_b'] ?? '';

            if ( ! empty( $color_b ) ) {
                $color = $color . ' - ' . $color_b;
            }

            $is_empty_title = false;

            if ( ! wc_string_to_bool( $label_in_cart ) && ! empty( $label_in_cart_opt ) ) {
                $label = $label_in_cart_opt;
            }

			$value = '';

            if ( ( empty( $addon_title ) && wc_string_to_bool( $title_in_cart ) ) || ( empty( $addon_title ) && ! wc_string_to_bool( $title_in_cart ) && empty( $title_in_cart_opt ) ) ) {
                $is_empty_title = true;
            }

            if ( 'product' === $addon_type ) {

				$option_product_info = explode( '-', $original_value );
				$option_product_id   = isset( $option_product_info[1] ) ? $option_product_info[1] : '';
				$option_product_qty  = isset( $cart_item['yith_wapo_qty_options'][ $key ] ) ? $cart_item['yith_wapo_qty_options'][ $key ] : 1;

				$option_product      = wc_get_product( $option_product_id );
				if ( $option_product instanceof WC_Product ) {
                    $value = apply_filters( 'yith_wapo_product_name_in_cart',
                        $option_product_qty . ' x ' . $option_product->get_name(), $option_product, $option_product_qty );
				}
            } elseif ( in_array( $addon_type, array( 'text', 'textarea', 'number', 'date', 'colorpicker' ) ) ) {
				if ( ! $grouped_in_cart && ! $is_empty_title ) {
					$label = ! empty( $label ) ? $label . ': ' : '';
					$value = $label . $original_value;
				} else {
					$value = $original_value;
				}
			} elseif ( 'file' === $addon_type ) {
				$files      = $original_value;
				$file_links = '';
                if ( is_array( $files ) ) {
                    foreach( $files as $file_id => $file_url ) {
                        $file_url   = urldecode( $file_url );
                        $file_split = explode( '/', $file_url );
                        // translators: [FRONT] Label shown on cart for add-on type Upload
                        $file_name  = apply_filters( 'yith_wapo_show_attached_file_name', true ) ? end( $file_split ) : __( 'Attached file', 'yith-woocommerce-product-add-ons' );

                        $file_links      .= '<br><a href="' . $file_url . '" target="_blank">' . $file_name . '</a>';
                    }
                    $value = $label . ': ' . $file_links;
                    if ( empty( $label ) ) {
                        $value = $file_links;
                    }
                }

			} elseif ( in_array( $addon_type, array( 'select', 'radio', 'label', 'color', 'checkbox' ) ) ) {
				$value = $label;
			}


			return apply_filters( 'yith_wapo_get_addon_value_on_cart', $value, $addon_id, $option_id, $key, $original_value, $cart_item );

        }

		/**
		 * Get the add-on data name.
		 *
		 * @param int $addon_id The add-on id.
		 * @param int $option_id The option id.
		 *
		 * @return mixed
		 *
		 */
		public function get_addon_data_name( $addon_id, $option_id, $grouped_in_cart = false ) {
			$info = yith_wapo_get_option_info( $addon_id, $option_id );

			$addon_title             = $info['addon_label'] ?? '';
			$addon_title_in_cart     = $info['title_in_cart'] ?? '';
			$addon_title_in_cart_opt = $info['title_in_cart_opt'] ?? '';

			$addon_label             = $info['label'] ?? '';
            $addon_label_in_cart     = $info['label_in_cart'] ?? '';
            $addon_label_in_cart_opt = $info['label_in_cart_opt'] ?? '';

            if ( ! wc_string_to_bool( $addon_title_in_cart ) && ! empty( $addon_title_in_cart_opt ) ) {
                $addon_title = $addon_title_in_cart_opt;
            }
            if ( ! wc_string_to_bool( $addon_label_in_cart ) && ! empty( $addon_label_in_cart_opt ) ) {
                $addon_label = $addon_label_in_cart_opt;
            }

            if ( $grouped_in_cart || empty( $addon_title ) ) {
                $addon_title = $addon_label;
            }

			return $addon_title;
		}
	}
}

/**
 * Unique access to instance of YITH_WAPO_Cart class
 *
 * @return YITH_WAPO_Cart
 */
function YITH_WAPO_Cart() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WAPO_Cart::get_instance();
}
