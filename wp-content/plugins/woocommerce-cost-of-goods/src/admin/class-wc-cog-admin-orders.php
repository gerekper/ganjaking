<?php
/**
 * WooCommerce Cost of Goods
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cost of Goods to newer
 * versions in the future. If you wish to customize WooCommerce Cost of Goods for your
 * needs please refer to http://docs.woocommerce.com/document/cost-of-goods/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Cost of Goods Admin Orders Class
 *
 * Handles various modifications to the orders list table and edit order screen
 *
 * @since 2.0.0
 */
class WC_COG_Admin_Orders {


	/**
	 * Bootstrap class
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->init_hooks();
	}


	/**
	 * Initialize hooks
	 *
	 * @since 2.0.0
	 */
	protected function init_hooks() {

		// add column headers to the order items
		add_action( 'woocommerce_admin_order_item_headers', array( $this, 'add_order_item_cost_column_headers' ) );

		// add cost of goods value and input field to order items
		add_action( 'woocommerce_admin_order_item_values', array( $this, 'add_order_item_cost' ), 10, 3 );

		// save cost of goods value when edited
		add_action( 'woocommerce_saved_order_items', array( $this, 'maybe_save_order_item_cost' ), 10, 2 );

		// update line item cost totals and order cost total when editing an order in the admin
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'process_order_cost_meta' ), 15 );

		// update the order total cost when line items are removed in the admin via AJAX for WC >= 3.2
		add_action( 'woocommerce_delete_order_item', [ $this, 'delete_order_item_order_cost' ] );

		// hide the _wc_cog_item_cost and _wc_cog_item_total_cost item meta on the Order Items table
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_cost_meta' ) );

		// display the order total cost on the order admin page
		add_action( 'woocommerce_admin_order_totals_after_total', array( $this, 'show_order_total_cost' ) );
	}


	/**
	 * Add order item column headers
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order Order object
	 */
	public function add_order_item_cost_column_headers( $order ) {
		global $pagenow;

		// Do not add for orders being created manually and not saved yet
		if ( 'post-new.php' === $pagenow ) {
			return;
		}

		?>
		<th class="item_cost_of_goods sortable" data-sort="float">
			<?php esc_html_e( 'Cost of Goods', 'woocommerce-cost-of-goods' ); ?>
		</th>
		<?php
	}


	/**
	 * Add order item cost of goods
	 *
	 * @since 2.0.0
	 * @param null|\WC_Product $product
	 * @param array|\WC_Order_Refund $item
	 * @param int $item_id
	 */
	public function add_order_item_cost( $product, $item, $item_id ) {
		global $pagenow;

		// do not add for orders being created manually and not saved yet
		if ( 'post-new.php' === $pagenow ) {
			return;
		}

		// empty cell for refunds or where product is null
		if ( ! $item || ! $product instanceof \WC_Product ) {

			echo '<td width="1%">&nbsp;</td>';

		} else {

			if ( is_array( $item ) ) {
				$item_qty = isset( $item['qty'] ) ? max( 1, (int) $item['qty'] ) : 1;
			} elseif ( $item instanceof \WC_Order_Item ) {
				$item_qty = $item->get_quantity();
			} else {
				return;
			}

			$item_cost = wc_get_order_item_meta( $item_id, '_wc_cog_item_total_cost', true );

			// set default cost if item cost doesn't exist
			if ( false === $item_cost ) {
				$item_cost = (float) \WC_COG_Product::get_cost( $product ) * $item_qty;
			}

			$decimals = wc_get_price_decimals();

			// number input stepper value
			$steps = $decimals > 0 ? '0.' . str_repeat( '0', $decimals - 1 ) . '1' : 1;

			$formatted_item_cost = wc_format_decimal( $item_cost, $decimals );

			?>
			<td class="item_cost_of_goods" width="1%">

				<div class="view">
					<?php echo wc_price( $formatted_item_cost ); ?>
					<?php if ( $refunded_item_total_cost = $this->get_total_cost_refunded_for_item( $item, $item_id ) ) : ?>
						<small class="refunded"><?php echo wc_price( $refunded_item_total_cost ); ?></small>
					<?php endif; ?>
				</div>

				<div class="edit edit-cog" style="display: none;">
					<div class="split-input">
						<div class="input">
							<label></label>
							<input type="number"
								   name="item_cost_of_goods[<?php esc_attr_e( $item_id ); ?>]"
								   class="cog-total"
								   min="0"
								   step="<?php echo esc_attr( $steps ); ?>"
								   placeholder="0"
								   data-cog-total="<?php echo esc_attr( $formatted_item_cost ); ?>"
								   value="<?php echo esc_attr( $formatted_item_cost ); ?>" />
						</div>
						<div class="input">
							<label><?php esc_html_e( 'Should be:', 'woocommerce-cost-of-goods' ); ?></label>
							<input type="text"
								   value="<?php esc_attr_e( wc_format_localized_price( $formatted_item_cost ) ); ?>"
								   class="cog-suggestion"
								   disabled="disabled" />
						</div>
					</div>
				</div>

			</td>
			<?php

		}
	}


	/**
	 * Gets the total item cost refunded for a given item ID.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order_Item|array
	 * @param int|null item ID
	 * @return null|string
	 */
	protected function get_total_cost_refunded_for_item( $item, $item_id = null ) {
		global $wpdb;

		$total = null;

		if ( $item instanceof \WC_Order_Item ) {

			$total = 0;
			$order = $item->get_order();

			foreach ( $order->get_refunds() as $refund ) {

				foreach ( $refund->get_items() as $refunded_item ) {

					if ( absint( $refunded_item->get_meta( '_refunded_item_id' ) ) === $item->get_id() ) {
						$total += $refunded_item->get_meta( '_wc_cog_item_total_cost' );
					}
				}
			}

		} elseif ( $item_id ) {

			$total = $wpdb->get_var( $wpdb->prepare( "
				SELECT SUM( order_itemmeta.meta_value )
				FROM {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
				WHERE order_itemmeta.meta_key = '_wc_cog_item_total_cost' AND
				order_itemmeta.order_item_id  IN (
					SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta2
					WHERE order_itemmeta2.meta_value = %d AND order_itemmeta2.meta_key = '_refunded_item_id'
				)
			", $item_id ) );
		}

		$total = str_replace( '-', '', (string) $total );

		return $total;
	}


	/**
	 * Maybe save order item cost data over editing order items over Ajax
	 *
	 * @since 2.0.0
	 * @param int $order_id order ID
	 * @param array $items line item data
	 */
	public function maybe_save_order_item_cost( $order_id, $items ) {

		if ( ! empty( $items['item_cost_of_goods'] ) && is_array( $items['item_cost_of_goods'] ) ) {

			$this->update_order_cost_totals( $order_id, $items['item_cost_of_goods'] );

			// clear transients, WC core does not clear shop order transients when line items are updated
			wc_cog()->get_admin_reports_instance()->clear_report_transients();
		}
	}


	/**
	 * Update the order line item cost totals and the order cost total when editing
	 * an order in the admin.
	 *
	 * This relies on the historical item cost set when the original was first
	 * processed, so any changes in quantities will be reflected with the correct
	 * cost basis.
	 *
	 * @since 2.0.0
	 * @param int $post_id the post ID for the order
	 */
	public function process_order_cost_meta( $post_id ) {

		// nonce check
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) {
			return;
		}

		if ( ! empty( $items['item_cost_of_goods'] ) && is_array( $items['item_cost_of_goods'] ) ) {

			$this->update_order_cost_totals( $post_id, $_POST['item_cost_of_goods'] );
		}
	}


	/**
	 * Update the total cost for each line item. This is done via AJAX when
	 * editing line items *and* when the overall order is saved.
	 *
	 * @since 1.0
	 * @param int $order_id
	 * @param array $order_item_costs
	 */
	public function update_order_cost_totals( $order_id, $order_item_costs ) {

		$order_cost_total = 0;

		// update total cost for each line item
		foreach ( $order_item_costs as $order_item_id => $item_total_cost ) {

			$order_cost_total += (float) $item_total_cost;

			wc_update_order_item_meta( $order_item_id, '_wc_cog_item_total_cost', wc_format_decimal( $item_total_cost, 4 ) );

			// prevents stale data from appearing when the order screen is reloaded
			wp_cache_delete( $order_item_id, 'order_item_meta' );
		}


		/**
		 * Update Order Cost Meta Filter.
		 *
		 * Allow actors to change the order total cost before it's set when
		 * saving an order in the admin.
		 *
		 * @since 2.0.0
		 * @param float|string $total_cost order total cost to update.
		 * @param WC_Order $order order object
		 */
		$order_cost_total = apply_filters( 'wc_cost_of_goods_update_order_cost_meta', $order_cost_total, wc_get_order( $order_id ) );

		// update the total order cost
		if ( $order = wc_get_order( $order_id ) ) {

			$order->update_meta_data( '_wc_cog_order_total_cost', wc_format_decimal( $order_cost_total, wc_get_price_decimals() ) );
			$order->save_meta_data();
		}
	}


	/**
	 * Updates the order total cost when items are removed in the admin edit
	 * order page via AJAX for WC >= 3.2.
	 *
	 * WC <= 3.1 does not automatically update the order total when an item is
	 * removed so the total cost should not be updated either.
	 *
	 * @internal
	 *
	 * @since 2.8.4
	 *
	 * @param int $item_id the order item id
	 * @throws \Exception
	 */
	public function delete_order_item_order_cost( $item_id ) {

		// WC >= 3.2
		if ( ! empty( $_POST['order_id'] ) ) {

			$order_id         = $_POST['order_id'];
			$order            = wc_get_order( $order_id );
			$order_item_costs = [];

			foreach ( $order->get_items() as $item ) {

				$item_cost = wc_get_order_item_meta( $item->get_id(), '_wc_cog_item_total_cost', true );

				$order_item_costs[ $item->get_id() ] = $item_cost;
			}

			$this->update_order_cost_totals( $order_id, $order_item_costs );
		}
	}


	/**
	 * Update the line item cost and cost total when items are added in the admin edit order page via AJAX
	 *
	 * Note: the action that this is hooked into was added in WC 2.0.8, so versions prior will show a blank cost for items
	 * added via the admin
	 *
	 * TODO: remove this method by version 2.11.0 or later {WV 2019-09-04}
	 *
	 * @since 1.0
	 * @deprecated 2.9.1
	 *
	 * @param int $item_id the order item ID
	 * @param array $item the order item meta already added
	 */
	public function ajax_add_order_line_cost( $item_id, $item ) {

		wc_deprecated_function( __METHOD__, '2.9.1', 'wc_cog()->add_new_order_item_cost()' );

		if ( $item instanceof \WC_Order_Item_Product ) {
			wc_cog()->add_new_order_item_cost( $item_id, $item, $item->get_order_id() );
		}
	}


	/**
	 * Hide cost of goods meta data fields from the order admin
	 *
	 * @since 1.0
	 * @param array $hidden_fields array of item meta data field names to hide from
	 *        the order admin
	 * @return array of item meta data field names to hide from the order admin
	 */
	public function hide_order_item_cost_meta( $hidden_fields ) {
		return array_merge( $hidden_fields, array( '_wc_cog_item_cost', '_wc_cog_item_total_cost' ) );
	}


	/**
	 * Render a read-only input box with the order total cost of goods
	 *
	 * @since 1.0
	 * @param int $post_id post (order) ID
	 */
	public function show_order_total_cost( $post_id ) {
		?>
			<tr>

				<td class="label"><?php esc_html_e( 'Cost of Goods', 'woocommerce-cost-of-goods' ); ?>:</td>
				<td width="1%"></td>
				<td class="total cost-total"><?php echo $this->get_formatted_order_total_cost( $post_id ); ?></td>

			</tr>
		<?php
	}


	/**
	 * Return the formatted order total cost, which includes the refunded order
	 * total cost if refunds have been processed
	 *
	 * @since 2.0.0
	 * @param int $order_id
	 * @return string formatted total
	 */
	protected function get_formatted_order_total_cost( $order_id ) {

		$order            = wc_get_order( $order_id );
		$order_total_cost = (float) $order->get_meta( '_wc_cog_order_total_cost', true, 'edit' );
		$formatted_total  = wc_price( $order_total_cost );

		$refunded_order_total_cost = 0;

		foreach ( $order->get_refunds() as $refund ) {

			$refunded_order_total_cost += (float) $refund->get_meta( '_wc_cog_order_total_cost', true, 'edit' );
		}

		if ( $refunded_order_total_cost < 0 ) {
			return sprintf( '<del>%1$s</del> <ins>%2$s</ins>', strip_tags( $formatted_total ), wc_price( $refunded_order_total_cost + $order_total_cost ) );
		}

		return $formatted_total;
	}


}
