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
 * Implements the YWRAQ_YITH_Composite_Products class.
 *
 * @class   YWRAQ_YITH_Composite_Products
 * @package YITH
 * @since   1.5.5
 * @author  YITH
 */
if ( ! class_exists( 'YWRAQ_YITH_Composite_Products' ) ) {

	/**
	 * Class YWRAQ_YITH_Composite_Products
	 */
	class YWRAQ_YITH_Composite_Products {

		/**
		 * Single instance of the class
		 *
		 * @var \YWRAQ_WooCommerce_Composite_Products
		 */

		protected static $instance;


		protected $_yith_wcp_cart;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWRAQ_WooCommerce_Composite_Products
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
		 * @author Andrea Frascaspata
		 */
		public function __construct() {

			$yith_wcp = YITH_WCP();

			if ( ! isset( $yith_wcp->frontend ) ) {
				$yith_wcp->frontend = new YITH_WCP_Frontend( $yith_wcp );
			}

			$this->_yith_wcp_cart = $yith_wcp->frontend->getCartObject();

			// add to quote button

			add_filter( 'ywraq_ajax_add_item_prepare', array( $this, 'ajax_add_item' ), 10, 2 );
			add_filter( 'ywraq_add_item', array( $this, 'add_item' ), 10, 2 );

			// table

			add_filter( 'yith_ywraq_item_class', array( $this, 'add_class_to_composite_parent' ), 10, 3 );
			add_action( 'ywraq_after_request_quote_view_item', array( $this, 'show_composite_data' ), 10, 2 );
            add_action( 'ywraq_mini_widget_view_item', array( $this, 'show_composite_data_in_widget' ), 10, 2 );
            add_action( 'ywraq_list_widget_view_item', array( $this, 'show_composite_data_in_widget' ), 10, 2 );
			add_action( 'ywraq_quote_adjust_price', array( $this, 'adjust_price' ), 10, 2 );

			// add_filter( 'yith_ywraq_product_price_html' , array( $this, 'product_price_html' ) , 10 , 3 );
			// add_filter( 'yith_ywraq_product_price' , array( $this, 'product_price' ) , 10 , 3 );
			add_action( 'ywraq_after_request_quote_view_item_on_email', array(
				$this,
				'show_composit_data_on_email'
			), 10, 2 );

			// order

			add_action( 'ywraq_from_cart_to_order_item', array( $this, 'add_order_item_meta' ), 10, 3 );

			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta' ), 10, 1 );

			add_filter( 'ywraq_formatted_line_total', array( $this->_yith_wcp_cart, 'order_item_subtotal' ), 10, 3 );

		}

		/**
		 * @param $postdata
		 * @param $product_id
		 *
		 * @return array
		 */
		public function ajax_add_item( $postdata, $product_id ) {

			if ( empty( $postdata ) ) {
				$postdata = array();
			}

			$postdata['add-to-cart'] = $product_id;

			$ywcp_composite_data = $this->_yith_wcp_cart->add_cart_item_data( null, $product_id, $postdata );
			if ( ! empty( $ywcp_composite_data ) ) {
				$postdata = array_merge( $ywcp_composite_data, $postdata );
			}

			return $postdata;
		}

		/**
		 * @param $product_raq
		 * @param $raq
		 *
		 * @return mixed
		 */
		public function add_item( $product_raq, $raq ) {

			if ( isset( $product_raq['yith_wcp_component_data'] ) ) {

				$raq['yith_wcp_component_data'] = $product_raq['yith_wcp_component_data'];

			}

			return $raq;
		}

		/**
		 * @param $class
		 * @param $raq
		 * @param $key
		 *
		 * @return string
		 */
		public function add_class_to_composite_parent( $class, $raq, $key ) {

			if ( array_key_exists( 'yith_wcp_component_data', $raq[ $key ] ) ) {
				$class .= ' ywcp_component_item';
			}

			return $class;
		}

		/**
		 * @param $raq
		 * @param $key
		 */
		public function show_composite_data( $raq, $key ) {

			if ( array_key_exists( 'yith_wcp_component_data', $raq[ $key ] ) ) {

				$product        = wc_get_product( $raq[ $key ]['yith-add-to-cart'] );
				$component_data = $raq[ $key ]['yith_wcp_component_data'];


				if ( $product->is_type( 'yith-composite' ) && isset( $component_data['selection_variation_data'] ) ) {

					$composite_quantity = $raq[ $key ]['quantity'];

					$composite_stored_data = $product->getComponentsData();

					foreach ( $composite_stored_data as $wcp_key => $component_item ) {

						if ( isset( $component_data['selection_data'][ $wcp_key ] ) ) {

							if ( $component_data['selection_data'][ $wcp_key ] > 0 ) {

								//variation selected
								if ( isset( $component_data['selection_variation_data'][ $wcp_key ] ) && $component_data['selection_variation_data'][ $wcp_key ] > 0 ) {
									$child_product = wc_get_product( $component_data['selection_variation_data'][ $wcp_key ] );

								} else {

									$child_product = wc_get_product( $component_data['selection_data'][ $wcp_key ] );

								}


								if ( ! $child_product ) {
									continue;
								}

								YITH_WCP_Frontend::markProductAsCompositeProcessed( $child_product, $product->get_id(), $wcp_key );

								$child_quantity = $component_data['selection_quantity'][ $wcp_key ];

								?>
                                <tr class="cart_item ywcp_component_child_item" data-wcpkey="<?php echo $key ?>">
                                    <td class="product-remove">
                                    </td>
                                    <td class="product-thumbnail">
										<?php $thumbnail = $child_product->get_image();

										if ( ! $child_product->is_visible() ) {
											echo $thumbnail;
										} else {
											printf( '<a href="%s">%s</a>', $child_product->get_permalink(), $thumbnail );
										}
										?>
                                    </td>

                                    <td class="product-name">
										<?php

										$title = $child_product->get_title();

										if ( $child_product->get_sku() != '' && get_option( 'ywraq_show_sku' ) == 'yes' ) {
											$sku = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $child_product->get_sku();
											$title .= apply_filters( 'ywraq_sku_label_html', $sku, $child_product );
										}

										echo sprintf( '<strong>%s</strong><br>', $component_item['name'] );
										?>
                                        <a href="<?php echo $child_product->get_permalink() ?>"><?php echo $title ?></a>
										<?php
										// Meta data

										$item_data = array();

										if ( $child_product->is_type( 'variation' ) ) {
											$variation_data = $child_product->get_data()['attributes'];

											if ( ! empty( $variation_data ) ) {
												foreach ( $variation_data as $attribute_name => $option ) {

													$item_data[] = array(
														'key'   => wc_attribute_label( str_replace( 'attribute_', '', $attribute_name ) ),
														'value' => $option
													);

												}
											}
										}

										$item_data = apply_filters( 'ywraq_request_quote_view_item_data', $item_data, $raq, $child_product );

										// Output flat or in list format
										if ( sizeof( $item_data ) > 0 ) {
											foreach ( $item_data as $data ) {
												echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
											}
										}


										?>
                                    </td>


                                    <td class="product-quantity">
										<?php
										echo $child_quantity * $composite_quantity;
										?>
                                    </td>

                                    <td class="product-subtotal">
										<?php
										//wc 2.7
										$child_product_price = wc_get_price_to_display( $child_product );
										echo ( $child_product->get_price() ) ? __( 'Option subtotal: ', 'yith-woocommerce-request-a-quote' ) . wc_price( $child_product_price * $child_quantity * $composite_quantity ) : '';
										?>
                                    </td>
                                </tr>
								<?php

							}

						}

					}

				}

			}
		}

        /**
         * @param $raq
         * @param $key
         */
        public function show_composite_data_in_widget( $raq, $key ) {

            if ( array_key_exists( 'yith_wcp_component_data', $raq[ $key ] ) ) {

                $product        = wc_get_product( $raq[ $key ]['yith-add-to-cart'] );
                $component_data = $raq[ $key ]['yith_wcp_component_data'];


                if ( $product->is_type( 'yith-composite' ) && isset( $component_data['selection_variation_data'] ) ) {

                    $composite_quantity = $raq[ $key ]['quantity'];

                    $composite_stored_data = $product->getComponentsData();

                    foreach ( $composite_stored_data as $wcp_key => $component_item ) {

                        if ( isset( $component_data['selection_data'][ $wcp_key ] ) ) {

                            if ( $component_data['selection_data'][ $wcp_key ] > 0 ) {

                                //variation selected
                                if ( isset( $component_data['selection_variation_data'][ $wcp_key ] ) && $component_data['selection_variation_data'][ $wcp_key ] > 0 ) {
                                    $child_product = wc_get_product( $component_data['selection_variation_data'][ $wcp_key ] );

                                } else {

                                    $child_product = wc_get_product( $component_data['selection_data'][ $wcp_key ] );

                                }


                                if ( ! $child_product ) {
                                    continue;
                                }

                                YITH_WCP_Frontend::markProductAsCompositeProcessed( $child_product, $product->get_id(), $wcp_key );

                                $child_quantity = $component_data['selection_quantity'][ $wcp_key ];

                                ?>
                                <div class="cart_item ywcp_component_child_item" data-wcpkey="<?php echo $key ?>">


                                        <?php

                                        $title = $child_product->get_title();

                                        if ( $child_product->get_sku() != '' && get_option( 'ywraq_show_sku' ) == 'yes' ) {
                                            $sku = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $child_product->get_sku();
                                            $title .= apply_filters( 'ywraq_sku_label_html', $sku, $child_product );
                                        }

                                        echo sprintf( '<strong>%s</strong>', $component_item['name'] );
                                        ?>
                                        <a href="<?php echo $child_product->get_permalink() ?>"><?php echo $title ?></a>
                                        <?php
                                        // Meta data
                                        $item_data = array();
                                        if ( $child_product->is_type( 'variation' ) ) {
                                            $variation_data = $child_product->get_data()['attributes'];

                                            if ( ! empty( $variation_data ) ) {
                                                foreach ( $variation_data as $attribute_name => $option ) {

                                                    $item_data[] = array(
                                                        'key'   => wc_attribute_label( str_replace( 'attribute_', '', $attribute_name ) ),
                                                        'value' => $option
                                                    );

                                                }
                                            }
                                        }
                                        $item_data = apply_filters( 'ywraq_request_quote_view_item_data', $item_data, $raq, $child_product );
                                        // Output flat or in list format
                                        if ( sizeof( $item_data ) > 0 ) {
                                            foreach ( $item_data as $data ) {
                                                echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
                                            }
                                        }
                                        ?>

                                    <span class="product-quantity"><?php echo $child_quantity * $composite_quantity; ?></span>
                                </div>
                                <?php

                            }

                        }

                    }

                }

            }
        }

		/**
		 * @param $raq
		 * @param $key
		 */
		public function show_composit_data_on_email( $raq, $key ) {

			if ( array_key_exists( 'yith_wcp_component_data', $raq[ $key ] ) ) {

				$product        = wc_get_product( $raq[ $key ]['yith-add-to-cart'] );
				$component_data = $raq[ $key ]['yith_wcp_component_data'];

				if ( $product->is_type( 'yith-composite' ) && isset( $component_data['selection_variation_data'] ) ) {

					$composite_quantity = $raq[ $key ]['quantity'];

					$composite_stored_data = $product->getComponentsData();

					foreach ( $composite_stored_data as $wcp_key => $component_item ) {

						if ( isset( $component_data['selection_data'][ $wcp_key ] ) ) {

							if ( $component_data['selection_data'][ $wcp_key ] > 0 ) {

								// variation selected
								if ( isset( $component_data['selection_variation_data'][ $wcp_key ] ) && $component_data['selection_variation_data'][ $wcp_key ] > 0 ) {

									$child_product = wc_get_product( $component_data['selection_variation_data'][ $wcp_key ] );

								} else {

									$child_product = wc_get_product( $component_data['selection_data'][ $wcp_key ] );

								}

								if ( ! $child_product ) {
									continue;
								}

								YITH_WCP_Frontend::markProductAsCompositeProcessed( $child_product, $product->get_id(), $wcp_key );

								$child_quantity = $component_data['selection_quantity'][ $wcp_key ];

								$title = $child_product->get_title();

								if ( $child_product->get_sku() != '' && get_option( 'ywraq_show_sku' ) == 'yes' ) {
									$sku = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $child_product->get_sku();
									$title .= apply_filters( 'ywraq_sku_label_html', $sku, $child_product );
								}

								?>
                                <tr class="yith-composite-child">
									<?php if ( get_option( 'ywraq_show_preview' ) == 'yes' ): ?>
                                        <td scope="col" class="td product-preview" style="text-align:center;">
											<?php

											$thumbnail = $child_product->get_image();
											if ( ! empty( $thumbnail ) ) {
												if ( $child_product->is_visible() ) {
													printf( '<a href="%s">%s</a>', $child_product->get_permalink(), $thumbnail );
												} else {
													echo $thumbnail;
												}
											}

											?>
                                        </td>
									<?php endif ?>

                                    <td scope="col" class="td product-name"
                                        style="text-align:left;"><?php echo sprintf( '<strong>%s</strong><br>', $component_item['name'] ); ?>
                                        <a href="<?php echo $child_product->get_permalink() ?>"><?php echo $title ?></a>
										<?php
										$item_data = array();

										// Variation data
										if ( $child_product->is_type( 'variation' ) ) {
											$variation_data = yit_get_prop( $child_product, 'variation_data', true );
											if ( is_array( $variation_data ) ) {
												foreach ( $variation_data as $attribute_name => $option ) {
													$item_data[] = array(
														'key'   => wc_attribute_label( str_replace( 'attribute_', '', $attribute_name ) ),
														'value' => $option
													);
												}
											}

										}

										$item_data = apply_filters( 'ywraq_request_quote_view_item_data', $item_data, $raq, $child_product );

										// Output flat or in list format
										if ( sizeof( $item_data ) > 0 ) {
											foreach ( $item_data as $data ) {
												echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
											}
										}
										?>
                                    </td>
                                    <td scope="col" class="td quantity"
                                        style="text-align:left;"><?php echo $child_quantity * $composite_quantity ?></td>
									<?php if ( get_option( 'ywraq_hide_total_column', 'yes' ) == 'no' && get_option( 'ywraq_show_total_in_list', 'no' ) == 'yes' && get_option( 'ywraq_hide_price' ) != 'yes' ): ?>
                                        <td scope="col" class="td subtotal" style="text-align:left;">
											<?php

											$child_product_price = wc_get_price_to_display( $child_product );
											echo ( $child_product->get_price() ) ? __( 'Option subtotal: ', 'yith-woocommerce-request-a-quote' ) . wc_price( $child_product_price * $child_quantity * $composite_quantity ) : '';
											?>
                                        </td>
									<?php endif; ?>
                                </tr>
								<?php

							}

						}

					}

				}

			}

		}

		/**
		 * @param $product_sub_total
		 * @param $product
		 * @param $raq_item
		 *
		 * @return string
		 */
		public function product_price_html( $product_sub_total, $product, $raq_item ) {

			if ( $product->is_type( 'yith-composite' ) && ! empty( $raq_item['yith_wcp_component_data'] ) ) {

				if ( $product->isPerItemPricing() ) {

					$component_data = $raq_item['yith_wcp_component_data'];

					$composite_base_price = $component_data['product_base_price'];

					$composite_total = $this->get_childs_totals( $raq_item );

					$new_subtotal = $composite_base_price + $composite_total;

					$total_price = $new_subtotal * absint( $raq_item['quantity'] );

					$price_html = wc_price( $total_price );

					if ( $composite_base_price > 0 ) {
						$price_html .= '<div>(' . __( 'Base Price:', 'cart price advice', 'yith-wooommerce-composite-products' ) . ' ' . wc_price( $composite_base_price * $raq_item['quantity'] ) . ' + ' . __( 'Total Price:', 'cart price advice', 'yith-wooommerce-composite-products' ) . ' ' . wc_price( $composite_total * $raq_item['quantity'] ) . ')</div>';
					}

					return $price_html;

				}

			} else if ( isset( $raq_item['yith_wcp_child_component_data'] ) ) {

				$child_item_meta = $raq_item['yith_wcp_child_component_data'];

				if ( $child_item_meta['yith_wcp_component_parent_object']->isPerItemPricing() ) {

					return __( 'Options subtotal', 'yith-composite-products-for-woocommerce' ) . ': ' . $product_sub_total;

				} else {

					return '';

				}

			}

			return $product_sub_total;

		}


		/**
		 * Return the price of the product.
		 *
		 * @param array $values
		 * @param WC_Product $_product
		 * @param  string $taxes
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function adjust_price( $values, $_product, $taxes = 'inc' ) {

			if ( isset( $values['yith_wcp_component_data'] ) && 'yith-composite' == $_product->get_type() ) {
				$child_price = $this->get_childs_totals( $values );
				$qty       = $values['quantity'];
				$price     = floatval( $_product->get_price() );
				$new_price = $price + $child_price;

				$_product->set_price( $new_price );
			}
		}

		/**
		 * @param $raq_item
		 *
		 * @return int|string
		 */
		private function get_childs_totals( $raq_item ) {

			$new_subtotal = 0;

			if ( isset( $raq_item['yith_wcp_component_data'] ) ) {

				$product = wc_get_product( $raq_item['yith-add-to-cart'] );

				$component_data = $raq_item['yith_wcp_component_data'];

				if ( $product->is_type( 'yith-composite' ) && isset( $component_data['selection_variation_data'] ) ) {

					$composite_stored_data = $product->getComponentsData();

					foreach ( $composite_stored_data as $wcp_key => $component_item ) {

						if ( isset( $component_data['selection_data'][ $wcp_key ] ) ) {

							if ( $component_data['selection_data'][ $wcp_key ] > 0 ) {

								//variation selected
								if ( isset( $component_data['selection_variation_data'][ $wcp_key ] ) && $component_data['selection_variation_data'][ $wcp_key ] > 0 ) {

									$child_product = wc_get_product( $component_data['selection_variation_data'][ $wcp_key ] );

								} else {

									$child_product = wc_get_product( $component_data['selection_data'][ $wcp_key ] );

								}

								if ( ! $child_product ) {
									continue;
								}

								YITH_WCP_Frontend::markProductAsCompositeProcessed( $child_product, $product->get_id(), $wcp_key );

								$child_quantity      = $component_data['selection_quantity'][ $wcp_key ];
								$child_product_price = wc_get_price_to_display( $child_product );
								$new_subtotal        += ( $child_product_price * $child_quantity );

							}

						}

					}

				}

			}

			return $new_subtotal;
		}

		/**
		 * @param $values
		 * @param $cart_item_key
		 * @param $item_id
		 */
		public function add_order_item_meta( $values, $cart_item_key, $item_id ) {

			if ( ! empty( $values['yith_wcp_component_data'] ) ) {

				wc_add_order_item_meta( $item_id, '_yith_wcp_component_data', $values['yith_wcp_component_data'] );

			} else if ( ! empty( $values['yith_wcp_child_component_data'] ) ) {

				wc_add_order_item_meta( $item_id, '_yith_wcp_child_component_data', $values['yith_wcp_child_component_data'] );

				wc_add_order_item_meta( $item_id, '_yith_wcp_child_component_data_no_reorder', 1 );

			}

		}

		/**
		 * @param $array
		 *
		 * @return array
		 */
		public function hidden_order_itemmeta( $array ) {

			$array = array_merge( $array, array( '_yith_wcp_child_component_data_no_reorder' ) );

			return $array;

		}

	}

	/**
	 * Unique access to instance of YWRAQ_WooCommerce_Product_Addon class
	 *
	 * @return YWRAQ_WooCommerce_Composite_Products
	 */
	function YWRAQ_YITH_Composite_Products() {
		return YWRAQ_YITH_Composite_Products::get_instance();
	}

	if ( class_exists( 'YITH_WCP' ) ) {
		YWRAQ_YITH_Composite_Products();
	}

}