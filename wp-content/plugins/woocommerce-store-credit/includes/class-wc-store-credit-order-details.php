<?php
/**
 * Class to handle the order details.
 *
 * @package WC_Store_Credit/Classes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Order_Details.
 */
class WC_Store_Credit_Order_Details {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		// Order Item metas.
		add_action( 'woocommerce_before_save_order_item', array( $this, 'before_save_order_item' ) );
		add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'display_item_meta_key' ), 10, 2 );
		add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'display_item_meta_value' ), 10, 3 );
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_metas' ) );
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'get_formatted_item_meta_data' ), 10, 2 );

		// Order totals.
		add_action( 'woocommerce_admin_order_totals_after_shipping', array( $this, 'admin_order_store_credit' ) );
		add_filter( 'woocommerce_get_order_item_totals', array( $this, 'add_order_store_credit_row' ), 10, 3 );
		add_filter( 'woocommerce_order_get_discount_total', array( $this, 'get_discount_total' ), 10, 2 );
		add_filter( 'woocommerce_order_get_discount_tax', array( $this, 'get_discount_tax' ), 10, 2 );
		add_filter( 'woocommerce_order_get_shipping_total', array( $this, 'get_shipping_total' ), 10, 2 );
		add_filter( 'woocommerce_order_get_shipping_tax', array( $this, 'get_shipping_tax' ), 10, 2 );
		add_filter( 'woocommerce_order_shipping_method', array( $this, 'shipping_method' ), 10, 2 );
	}

	/**
	 * Processes the Order Item before saving it.
	 *
	 * @since 3.2.0
	 *
	 * @param WC_Order_Item $order_item Order item object.
	 */
	public function before_save_order_item( $order_item ) {
		if ( ! $order_item->meta_exists( '_store_credit_coupons' ) ) {
			return;
		}

		// Converts the metadata value into an array.
		$coupons = $this->parse_coupons_meta_value( $order_item->get_meta( '_store_credit_coupons' ) );

		$order_item->update_meta_data( '_store_credit_coupons', $coupons );
	}

	/**
	 * Filters the displayed order item meta key.
	 *
	 * @since 3.2.0
	 *
	 * @param string       $key  Order item meta key.
	 * @param WC_Meta_Data $meta Order item meta.
	 * @return string
	 */
	public function display_item_meta_key( $key, $meta ) {
		if ( '_store_credit_coupons' === $meta->key ) {
			$coupons = $this->parse_coupons_meta_value( $meta->value );
			$key     = _n( 'Coupon', 'Coupons', count( $coupons ), 'woocommerce-store-credit' );
		}

		return $key;
	}

	/**
	 * Filters the displayed order item meta value.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed         $value      Order item meta value.
	 * @param WC_Meta_Data  $meta       Order item meta.
	 * @param WC_Order_Item $order_item Order item object.
	 * @return string
	 */
	public function display_item_meta_value( $value, $meta, $order_item ) {
		if ( '_store_credit_coupons' === $meta->key ) {
			$coupons = $this->parse_coupons_meta_value( $value );

			foreach ( $coupons as $index => $coupon_code ) {
				$coupons[ $index ] = sprintf( '<span class="coupon-code">%1$s</span>', esc_attr( $coupon_code ) );
			}

			$value = join( '<span class="divider">, </span>', $coupons );
		} elseif ( $this->is_shipping_discount( $order_item ) && is_numeric( $value ) ) {
			$value = wc_price( - $value );
		}

		return $value;
	}

	/**
	 * Hides custom order item metadata.
	 *
	 * @since 4.0.0
	 *
	 * @param array $metas An array with the meta keys.
	 * @return array
	 */
	public function hide_order_item_metas( $metas ) {
		$metas[] = '_store_credit_custom_amount';

		return $metas;
	}

	/**
	 * Gets the formatted order item metadata.
	 *
	 * @since 3.2.0
	 *
	 * @param array         $metadata   Order item metadata.
	 * @param WC_Order_Item $order_item Order item object.
	 * @return array
	 */
	public function get_formatted_item_meta_data( $metadata, $order_item ) {
		if ( ! $order_item instanceof WC_Order_Item_Product || ! $order_item->meta_exists( '_store_credit_coupons' ) ) {
			return $metadata;
		}

		$meta = wc_store_credit_get_order_item_meta( $order_item, '_store_credit_coupons' );

		if ( false === $meta ) {
			return $metadata;
		}

		$coupons = $this->parse_coupons_meta_value( $meta->value );

		$metadata[ $meta->id ] = (object) array(
			'key'           => $meta->key,
			'value'         => join( ', ', $coupons ), // Use an editable format.
			'display_key'   => $this->display_item_meta_key( $meta->key, $meta ),
			'display_value' => $this->display_item_meta_value( $coupons, $meta, $order_item ),
		);

		return $metadata;
	}

	/**
	 * Gets if the order item is a shipping discount.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order_Item $order_item Order item object.
	 * @return bool
	 */
	protected function is_shipping_discount( $order_item ) {
		return ( $order_item instanceof WC_Order_Item_Shipping && 'store_credit_discount' === $order_item->get_method_id() );
	}

	/**
	 * Parses the value of the 'coupon IDs' Order Item meta.
	 *
	 * @since 3.2.0
	 *
	 * @param mixed $value The meta value.
	 * @return array
	 */
	protected function parse_coupons_meta_value( $value ) {
		if ( ! is_array( $value ) ) {
			$value = array_values( array_filter( explode( ',', $value ) ) );
		}

		return $value;
	}

	/**
	 * Outputs the store credit used by the order in the edit shop-order admin screen.
	 *
	 * @since 3.0.0
	 *
	 * @param int $order_id The order ID.
	 */
	public function admin_order_store_credit( $order_id ) {
		$order  = wc_store_credit_get_order( $order_id );
		$credit = wc_get_store_credit_for_order( $order, false );

		if ( 0 >= $credit ) {
			return;
		}

		$total = wc_price( $credit, array( 'currency' => $order->get_currency() ) );

		if ( version_compare( WC_VERSION, '4.0', '>=' ) ) {
			$total = '- ' . $total;
		}
		?>
		<tr>
			<td class="label"><?php echo esc_html_x( 'Store Credit used:', 'order totals row', 'woocommerce-store-credit' ); ?></td>
			<td width="1%"></td>
			<td class="total"><?php echo $total; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
		</tr>
		<?php
	}

	/**
	 * Adds total row for store credit.
	 *
	 * @since 3.0.0
	 *
	 * @param array    $total_rows  An array with to total rows.
	 * @param WC_Order $order       The order instance.
	 * @param string   $tax_display Excl or incl tax display mode.
	 * @return array
	 */
	public function add_order_store_credit_row( $total_rows, $order, $tax_display ) {
		$credit = wc_get_store_credit_to_display_for_order( $order, $tax_display );

		if ( $credit ) {
			$offset = false;

			if ( wc_store_credit_apply_before_tax( $order ) ) {
				// After the first existing key.
				$offset_keys = array( 'shipping', 'discount', 'cart_subtotal' );

				foreach ( $offset_keys as $offset_key ) {
					$index = array_search( $offset_key, array_keys( $total_rows ), true );

					if ( false !== $index ) {
						$offset = $index + 1;
						break;
					}
				}
			} else {
				$offset = array_search( 'order_total', array_keys( $total_rows ), true );
			}

			// Append the row at the end of the list as a fallback.
			if ( false === $offset ) {
				$offset = count( $total_rows );
			}

			$total_rows = array_merge(
				array_slice( $total_rows, 0, $offset ),
				array(
					'store_credit' => array(
						'label' => _x( 'Store Credit used:', 'order totals row', 'woocommerce-store-credit' ),
						'value' => "-{$credit}",
					),
				),
				array_slice( $total_rows, $offset )
			);
		}

		return $total_rows;
	}

	/**
	 * Filters the order 'discount_total' value.
	 *
	 * This filter is only called for 'view' context.
	 *
	 * @since 3.0.0
	 *
	 * @param float    $discount_total The discounted amount.
	 * @param WC_Order $order          The order instance.
	 * @return float
	 */
	public function get_discount_total( $discount_total, $order ) {
		if ( wc_store_credit_apply_before_tax( $order ) ) {
			/*
			 * Remove store credit from the total discount when displaying it.
			 * The store credit discount is displayed in a different row.
			 */
			$credit = wc_get_store_credit_for_order( $order, false, 'cart' );

			if ( 0 < $credit ) {
				$discount_total -= $credit;
			}
		}

		return $discount_total;
	}

	/**
	 * Filters the order 'discount_tax' value.
	 *
	 * This filter is only called for 'view' context.
	 *
	 * @since 3.5.2
	 *
	 * @param float    $discount_tax The discounted amount.
	 * @param WC_Order $order        The order instance.
	 * @return float
	 */
	public function get_discount_tax( $discount_tax, $order ) {
		if ( wc_store_credit_apply_before_tax( $order ) ) {
			/*
			 * Remove store credit from the tax discount when displaying it.
			 * The store credit discount is displayed in a different row.
			 */
			$credit = array_sum( wc_get_store_credit_discounts_for_order( $order, 'total', array( 'cart_tax' ) ) );

			if ( 0 < $credit ) {
				$discount_tax -= $credit;
			}
		}

		return $discount_tax;
	}

	/**
	 * Filters the order 'shipping_total' value.
	 *
	 * Restores the shipping discounts when displaying the total shipping row.
	 *
	 * @since 3.0.0
	 *
	 * @param float    $shipping_total The shipping amount.
	 * @param WC_Order $order          The order instance.
	 * @return float
	 */
	public function get_shipping_total( $shipping_total, $order ) {
		if ( ! $this->is_total_shipping_row() ) {
			return $shipping_total;
		}

		$discounts = wc_get_store_credit_discounts_for_order( $order, 'total', array( 'shipping' ) );

		return ( $shipping_total + array_sum( $discounts ) );
	}

	/**
	 * Filters the order 'shipping_tax' value.
	 *
	 * Restores the shipping tax discounts when displaying the total shipping row.
	 *
	 * @since 3.0.0
	 *
	 * @param float    $shipping_tax The shipping tax amount.
	 * @param WC_Order $order        The order instance.
	 * @return float
	 */
	public function get_shipping_tax( $shipping_tax, $order ) {
		if ( ! $this->is_total_shipping_row() ) {
			return $shipping_tax;
		}

		$discounts = wc_get_store_credit_discounts_for_order( $order, 'total', array( 'shipping_tax' ) );

		return ( $shipping_tax + array_sum( $discounts ) );
	}

	/**
	 * Gets if we are in the method `WC_Order->add_order_item_totals_shipping_row`.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	protected function is_total_shipping_row() {
		$backtrace  = wp_debug_backtrace_summary( 'WP_Hook', 0, false ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
		$save_index = array_search( 'WC_Abstract_Order->get_shipping_to_display', $backtrace, true );

		return ( false !== $save_index && 'WC_Abstract_Order->add_order_item_totals_shipping_row' === $backtrace[ $save_index + 1 ] );
	}

	/**
	 * Filters the formatted shipping method title.
	 *
	 * Excludes the shipping discount items from the title.
	 *
	 * @since 3.0.0
	 *
	 * @param string   $shipping_method Formatted shipping method.
	 * @param WC_Order $order           Order object.
	 * @return string
	 */
	public function shipping_method( $shipping_method, $order ) {
		$shipping_methods = $order->get_shipping_methods();

		$names = array();

		foreach ( $shipping_methods as $shipping_method ) {
			if ( ! $this->is_shipping_discount( $shipping_method ) ) {
				$names[] = $shipping_method->get_name();
			}
		}

		return implode( ', ', $names );
	}
}

return new WC_Store_Credit_Order_Details();
