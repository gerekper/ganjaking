<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH Invoice Details class.
 *
 * Handles the invoice details.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_Invoice_Details' ) ) {
	/**
	 * Calculate all the details of an invoice
	 *
	 * @class   YITH_Invoice_Details
	 * @package YITH\PDFInvoice\Classes
	 * @since   1.0.0
	 */
	class YITH_Invoice_Details {

		/**
		 * The document object.
		 *
		 * @var YITH_Document the document
		 */
		public $document = null;

		/**
		 * The Order object.
		 *
		 * @var WC_Order */
		private $order = null;

		/**
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param YITH_Document $document the order to be invoiced.
		 *
		 * @since  1.0
		 * @access public
		 */
		public function __construct( $document ) {
			$this->document = $document;

			if ( $document instanceof YITH_Credit_Note ) {
				$current_order_id = $document->order->get_id();
				$parent_order_id  = get_post_field( 'post_parent', $current_order_id );

				$this->order = wc_get_order( $parent_order_id );
			} else {
				$this->order = $document->order;
			}
		}

		/**
		 * Get the item product.
		 *
		 * @param  mixed $item The item object.
		 * @return void/object
		 */
		public function get_item_product( $item ) {
			if ( ! is_object( $item ) ) {
				return;
			}

			$_product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

			return $_product;
		}

		/**
		 * Retrieve the path of the product image
		 *
		 * @param mixed $item The item object.
		 *
		 * @return mixed
		 * @since  1.0.0
		 */
		public function get_product_image( $item ) {
			$_product = $this->get_item_product( $item );

			if ( is_object( $_product ) ) {
				$upload_dir = wp_upload_dir();

				$product_image = $_product->get_image_id() && is_array( wp_get_attachment_image_src( $_product->get_image_id() ) ) ? current( wp_get_attachment_image_src( $_product->get_image_id() ) ) : wc_placeholder_img_src();

				/**
				 * APPLY_FILTERS: yith_ywpi_invoice_details_get_product_image
				 *
				 * Filter the URL of the product image to display in the invoice.
				 *
				 * @param string the URL of the product image.
				 * @param array $item the product item.
				 *
				 * @return string
				 */
				$product_image = apply_filters( 'yith_ywpi_invoice_details_get_product_image', str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $product_image ), $item );
			} else {
				/**
				 * APPLY_FILTERS: yith_ywpi_invoice_details_get_product_image_null
				 *
				 * Filter the default placeholder of the product image to display in the invoice.
				 *
				 * @param string the URL of the product image default palceholder.
				 * @param array $item the product item.
				 *
				 * @return string
				 */
				$product_image = apply_filters( 'yith_ywpi_invoice_details_get_product_image_null', wc_placeholder_img_src(), $item );
			}

			return $product_image;
		}

		/**
		 * Retrieve the text to be shown when asked for the product SKU
		 *
		 * @param array $item The item object.
		 * @param int   $item_id The item id.
		 *
		 * @return string
		 *
		 * @since  1.0.0
		 */
		public function get_sku_text( $item, $item_id ) {
			$_product = $this->get_item_product( $item );

			if ( is_object( $_product ) && $_product->get_sku() ) {
				/**
				 * APPLY_FILTERS: yith_ywpi_invoice_details_get_product_sku
				 *
				 * Filter the "SKU:" label in the product information on invoice.
				 *
				 * @param string the label.
				 * @param array $item the product item.
				 *
				 * @return string
				 */
				$result = apply_filters( 'yith_ywpi_invoice_details_get_product_sku', esc_html__( 'SKU: ', 'yith-woocommerce-pdf-invoice' ) . esc_html( $_product->get_sku() ), $item );
			} else {
				$product_sku_post_meta = wc_get_order_item_meta( $item_id, '_ywpi_product_sku', true );

				$result = apply_filters( 'yith_ywpi_invoice_details_get_product_sku_null', esc_html__( 'SKU: ', 'yith-woocommerce-pdf-invoice' ) . $product_sku_post_meta, $item );
			}

			return $result;
		}

		/**
		 * Get the meta field.
		 *
		 * @param  array $meta The meta array.
		 * @return array
		 */
		public function get_meta_field( $meta ) {
			if ( is_object( $meta ) ) {
				$meta = array(
					'meta_id'    => $meta->id,
					'meta_key'   => $meta->key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value' => $meta->value, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				);
			}

			return $meta;
		}

		/**
		 * Retrieve the text to be shown when asked for the product variation text
		 *
		 * @param int    $item_id The item id.
		 * @param object $_product The product object.
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function get_variation_text( $item_id, $_product ) {
			$variation_text = '';

			if ( ywpi_is_enabled_column_variation( $this->document ) ) {
				/**
				 * APPLY_FILTERS: yith_ywpi_product_metadata
				 *
				 * Filters the order item metadata to be displayed in the invoice.
				 *
				 * @param array                $metadata        Order item metadata
				 * @param int                  $item_id         Order item ID
				 * @param WC_Product           $_product        Product object
				 * @param YITH_Invoice_Details $invoice_details Invoice details object
				 *
				 * @return array
				 */
				$metadata = apply_filters( 'yith_ywpi_product_metadata', $this->order->get_item( $item_id )->get_meta_data(), $item_id, $_product, $this );

				if ( $metadata ) {
					foreach ( $metadata as $meta ) {
						// Prevent empty titles to appear under the product data.
						if ( empty( $meta->value ) ) {
							continue;
						}

						$meta        = $this->get_meta_field( $meta );
						$hidden_meta = apply_filters( 'woocommerce_hidden_order_itemmeta', array() );

						if ( in_array( $meta['meta_key'], $hidden_meta, true ) ) {
							continue;
						}

						do_action( 'yith_ywpi_before_process_meta_invoice', $meta, $_product );

						$pos = strpos( $meta['meta_key'], '_' );

						if ( ( false !== $pos ) && ( 0 === $pos ) ) {
							continue;
						}

						// Skip serialised meta.
						if ( is_serialized( $meta['meta_value'] ) ) {
							continue;
						}

						if ( is_array( $meta['meta_value'] ) || is_object( $meta['meta_value'] ) ) {
							continue;
						}

						// Get attribute data.
						if ( taxonomy_exists( wc_sanitize_taxonomy_name( $meta['meta_key'] ) ) ) {
							$term             = get_term_by( 'slug', $meta['meta_value'], wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
							$meta['meta_key'] = wc_attribute_label( wc_sanitize_taxonomy_name( $meta['meta_key'] ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key

							$meta['meta_value'] = isset( $term->name ) ? $term->name : $meta['meta_value']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
						}

						/**
						 * APPLY_FILTERS: yith_ywpi_template_product_variation_string
						 *
						 * Filter the product variation string in the product details.
						 *
						 * @param string the variation data.
						 * @param array $meta the product meta data.
						 * @param object $_product the product object.
						 *
						 * @return string
						 */
						$variation_text .= apply_filters( 'yith_ywpi_template_product_variation_string', sprintf( '%s: %s %s ', $meta['meta_key'], $meta['meta_value'], '<br>' ), $meta, $_product );
					}
				} else {
					$variation_text = '';
				}
			}

			return $variation_text;
		}

		/** Get shipping items from the order*/
		public function get_order_shipping() {
			$order_shipping = apply_filters(
				'yith_ywpi_get_order_shipping_for_invoice',
				$this->order->get_items( 'shipping' ),
				$this->order
			);

			return $order_shipping;
		}

		/** Get fees items from the order*/
		public function get_order_fees() {
			$order_fee = apply_filters(
				'yith_ywpi_get_order_fee_for_invoice',
				$this->order->get_items( 'fee' ),
				$this->order
			);

			return $order_fee;
		}

		/** Get items from the order*/
		public function get_order_items() {
			$order_items = apply_filters(
				'yith_ywpi_get_order_items_for_invoice',
				$this->order->get_items(),
				$this->order
			);

			return $order_items;
		}

		/**
		 * Get item regular price (product price)
		 *
		 * @param mixed $item The item object.
		 * @param mixed $item_id The item id.
		 * @return float
		 */
		public function get_item_product_regular_price( $item, $item_id ) {
			$product_regular_price = 0.00;
			$_product              = $this->get_item_product( $item );

			if ( is_object( $_product ) ) {
				/*  Fix for gift cards products that hasn't a regular price */
				if ( $_product instanceof WC_Product_Gift_Card ) {
					$product_regular_price = $this->get_item_price_per_unit( $item );
				} else {
					$product_regular_price = $_product->get_regular_price();
				}
			} else {
				$product_regular_price_post_meta = wc_get_order_item_meta( $item_id, '_ywpi_product_regular_price', true );

				/**
				 * APPLY_FILTERS: yith_ywpi_invoice_details_get_product_regular_price_null
				 *
				 * Filter the product regular price when null in the invoice details.
				 *
				 * @param string $product_regular_price_post_meta the product regular price.
				 * @param array $item the product item.
				 *
				 * @return string
				 */
				$product_regular_price = apply_filters( 'yith_ywpi_invoice_details_get_product_regular_price_null', $product_regular_price_post_meta, $item );
			}

			if ( class_exists( 'YITH_Role_Based_Prices' ) ) {
				$yrbpp                 = new YITH_Role_Based_Prices_Product();
				$product_regular_price = $yrbpp->return_role_based_price_for_pdf( $product_regular_price, $item );
			}

			return $product_regular_price;
		}

		/**
		 * Get item sale price
		 *
		 * @param array $item The item array.
		 * @return float
		 */
		public function get_item_price_per_unit( $item ) {
			$price = 0.00;

			$tax_aux = wc_prices_include_tax() ? $item['subtotal_tax'] : 0;

			if ( isset( $item['quantity'] ) ) {
				$price = ( $item['subtotal'] + $tax_aux ) / $item['quantity'];
			}

			/**
			 * APPLY_FILTERS: yith_ywpi_get_item_price_per_unit_sale
			 *
			 * Filter the product price per unit sale.
			 *
			 * @param string $price the product price.
			 * @param array $item the product item.
			 * @param string $tax_aux the product price tax.
			 *
			 * @return string
			 */
			return apply_filters( 'yith_ywpi_get_item_price_per_unit_sale', $price, $item, $tax_aux );
		}

		/**
		 * Get item final price in the order.
		 *
		 * @param array $item The item array.
		 * @return float
		 */
		public function get_item_price_per_unit_sale( $item ) {
			$price   = 0.00;
			$tax_aux = wc_prices_include_tax() ? $item['total_tax'] : 0;

			if ( isset( $item['quantity'] ) ) {
				$price = ( $item['total'] + $tax_aux ) / $item['quantity'];
			}

			/**
			 * APPLY_FILTERS: yith_ywpi_get_item_price_per_unit
			 *
			 * Filter the product price per unit.
			 *
			 * @param string $price the product price.
			 * @param array $item the product item.
			 * @param string $tax_aux the product price tax.
			 *
			 * @return string
			 */
			return apply_filters( 'yith_ywpi_get_item_price_per_unit', $price, $item, $tax_aux );
		}

		/** Get item discount */
		public function get_products_total_discount() {
			$product_discount = 0.00;

			foreach ( $this->get_order_items() as $item_id => $item ) {
				$item_rsale_price = $this->get_item_price_per_unit_sale( $item );

				if ( ! is_numeric( $item_rsale_price ) ) {
					$item_regular_price = 0;
				}

				$item_price = $this->get_item_price_per_unit( $item );

				/**
				 * APPLY_FILTERS: yith_ywpi_line_discount
				 *
				 * Filter the discount per item.
				 *
				 * @param string the discount.
				 * @param array $item the product item.
				 *
				 * @return string
				 */
				$diff = apply_filters( 'yith_ywpi_line_discount', $item_rsale_price - $item_price, $item );

				if ( $diff > 0.01 ) {
					$product_discount += $item['qty'] * $diff;
				}
			}

			return $product_discount;
		}

		/**
		 * Get total percentage of the item discount
		 *
		 * @param array $item The item array.
		 * @return string
		 * */
		public function get_item_percentage_discount( $item ) {
			$sale_price    = $this->get_item_price_per_unit_sale( $item );
			$product_price = $this->get_item_price_per_unit( $item );
			$discount      = 0;

			if ( ( $sale_price > 0 ) && ( $product_price > 0 ) ) {
				$discount = 100 - floatval( $sale_price / $product_price * 100 );
			}

			return number_format( $discount, 0 ) . '%';
		}

		/**
		 * Get order subtotal
		 *
		 * @param boolean $incl_order_discount True or false if order include discount.
		 * @return float
		 */
		public function get_order_subtotal( $incl_order_discount = true ) {
			$order_fee_amount       = 0.00;
			$order_fee_taxes_amount = 0.00;

			foreach ( $this->get_order_fees() as $item_id => $item ) {
				$order_fee_amount       += $item['line_total'];
				$order_fee_taxes_amount += $item['line_tax'];
			}

			/**
			 * APPLY_FILTERS: yith_ywpi_order_subtotal
			 *
			 * Filter the order subtotal in the invoice.
			 *
			 * @param string the order subtotal.
			 * @param string $order_fee_amount the order fee amount.
			 * @param object the order object.
			 *
			 * @return string
			 */
			$_order_subtotal = apply_filters(
				'yith_ywpi_order_subtotal',
				(float) $this->order->get_subtotal() + (float) $this->order->get_shipping_total() + $order_fee_amount,
				$this->order
			);

			if ( $incl_order_discount ) {
				$_order_subtotal -= $this->get_order_discount();
			}

			/**
			 * APPLY_FILTERS: yith_ywpi_invoice_subtotal
			 *
			 * Filter the invoice subtotal.
			 *
			 * @param string $_order_subtotal the order subtotal.
			 * @param object the order object.
			 * @param string the product total discount.
			 * @param string $order_fee_amount the order fee amount.
			 *
			 * @return string
			 */
			$_order_subtotal = apply_filters(
				'yith_ywpi_invoice_subtotal',
				$_order_subtotal,
				$this->order,
				$this->get_products_total_discount(),
				$order_fee_amount
			);

			return $_order_subtotal;
		}

		/** Get order total taxes */
		public function get_order_taxes() {
			$order_items = apply_filters( 'yith_ywpi_get_order_items_for_invoice', $this->order->get_items(), $this->order );

			$fee = apply_filters( 'yith_ywpi_get_order_fee_for_invoice', $this->order->get_items( 'fee' ), $this->order );

			$shipping = apply_filters( 'yith_ywpi_get_order_shipping_for_invoice', $this->order->get_items( 'shipping' ), $this->order );

			$order_items = array_merge( $order_items, $fee, $shipping );

			$taxes = array();

			foreach ( $order_items as $item_id => $item ) :
				if ( $item instanceof WC_Order_Item_Product ) {
					if ( $item->get_product() instanceof WC_Product_Bundle ) {
						$bundle_exists = true;
					}
				}

				$tax = $item->get_taxes();

				$tax_rate_amount = $item->get_total_tax();

				$tax_percentage = '0.00';

				if ( abs( $tax_rate_amount ) > 0 || $item instanceof WC_Order_Item_Product ) {
					$tax_class = $item->get_tax_class() === 'inherit' ? '' : $item->get_tax_class();

					$tax_rates = WC_Tax::find_rates(
						array(
							'country'   => $this->order->get_billing_country(),
							'state'     => $this->order->get_billing_state(),
							'city'      => $this->order->get_billing_city(),
							'postcode'  => $this->order->get_billing_postcode(),
							'tax_class' => $tax_class,
						)
					);

					foreach ( $tax_rates as $tax_rate ) {
						$tax_percentage = number_format( $tax_rate['rate'], 2, '.', '' );
						$tax_label      = $tax_rate['label'];
					}
				}

				$new_total = isset( $taxes[ $tax_percentage ]['total'] ) ? $taxes[ $tax_percentage ]['total'] + $item['total'] : $item['total'];

				$new_tax_total = isset( $taxes[ $tax_percentage ]['total_tax'] ) ? $taxes[ $tax_percentage ]['total_tax'] + $tax_rate_amount : $tax_rate_amount;

				$taxes[ $tax_percentage ] = array(
					'total'     => $new_total,
					'total_tax' => $new_tax_total,
					'label'     => isset( $tax_label ) ? $tax_label : esc_html__( 'Tax', 'yith-woocommerce-pdf-invoice' ),
				);

			endforeach;

			return $taxes;
		}

		/**
		 * Get order shipping taxes.
		 *
		 * @param array $item The array of the item.
		 */
		public function get_item_shipping_taxes( $item ) {
			$taxes = 0.00;

			if ( isset( $item['taxes'] ) ) {
				$taxes_list = maybe_unserialize( $item['taxes'] );

				$taxes_list = isset( $taxes_list['total'] ) ? $taxes_list['total'] : $taxes_list;

				if ( $taxes_list ) {
					foreach ( $taxes_list as $tax_id => $amount ) {
						if ( 'total' !== strval( $tax_id ) ) {
							$taxes += (float) $amount;
						}
					}
				}
			}

			return $taxes;
		}

		/** Get order total */
		public function get_order_total() {
			$_order_taxes       = $this->get_order_taxes();
			$_order_taxes_total = 0.00;

			foreach ( $_order_taxes as $code => $tax ) {
				if ( isset( $tax->amount ) ) {
					$_order_taxes_total += $tax->amount;
				}
			}

			$_order_total = apply_filters(
				'yith_ywpi_invoice_total',
				$this->order->get_total(),
				$this->order->get_id()
			);

			return $_order_total;
		}

		/** Get order total discount */
		public function get_order_discount() {
			/**
			 * APPLY_FILTERS: yith_ywpi_invoice_total_discount
			 *
			 * Filter the invoice total discount.
			 *
			 * @param string the total discount.
			 * @param object the order object.
			 * @param string the product total discount.
			 *
			 * @return string
			 */
			$_order_discount = apply_filters(
				'yith_ywpi_invoice_total_discount',
				$this->order->get_total_discount() === floatval( 0 ) ? $this->get_products_total_discount() : $this->order->get_total_discount(),
				$this->order,
				$this->get_products_total_discount()
			);

			return $_order_discount;
		}

		/**
		 * Get the order currency
		 *
		 * @param float $amount The amount.
		 */
		public function get_order_currency_new( $amount ) {
			$order_currency = $this->order->get_currency();
			$currency       = array( 'currency' => $order_currency );

			/**
			 * APPLY_FILTERS: yith_ywpi_order_currency
			 *
			 * Filter the order currency in the invoice.
			 *
			 * @param string $currencythe the invoice currency.
			 * @param string $order_currency the order currency.
			 * @param object the order object.
			 *
			 * @return string
			 */
			$currency = apply_filters( 'yith_ywpi_order_currency', $currency, $order_currency, $this->order );

			return wc_price( $amount, $currency );
		}

		/**
		 * Get short description of an item.
		 *
		 * @param  mixed $item The item object.
		 * @param  mixed $item_id The item id.
		 * @return string
		 */
		public function get_short_description( $item, $item_id ) {
			$_product = $this->get_item_product( $item );

			if ( is_object( $_product ) ) {
				if ( $_product->is_type( 'variation' ) ) {
					if ( version_compare( WC()->version, '3.0', '<' ) ) {
						$post_excerpt = $_product->get_variation_description();
					} else {
						$post_excerpt = $_product->get_description();
					}
				} else {
					$post_excerpt = get_post_field( 'post_excerpt', $_product->get_id() );
				}
			} else {
				$product_short_description_post_meta = wc_get_order_item_meta( $item_id, '_ywpi_product_short_description', true );

				/**
				 * APPLY_FILTERS: yith_ywpi_invoice_details_get_short_description_null
				 *
				 * Filter the product short description when null, using the post meta.
				 *
				 * @param string $product_short_description_post_meta the prpduct short desciption.
				 * @param array the product item.
				 *
				 * @return string
				 */
				$post_excerpt = apply_filters( 'yith_ywpi_invoice_details_get_short_description_null', $product_short_description_post_meta, $item );
			}

			return $post_excerpt;
		}
	}
}
