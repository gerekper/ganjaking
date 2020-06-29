<?php
/**
 * WooCommerce Print Invoices/Packing Lists
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * PIP Packing List class
 *
 * Packing List document object
 *
 * @since 3.0.0
 */
class WC_PIP_Document_Packing_List extends WC_PIP_Document {


	/**
	 * PIP Packing List document constructor
	 *
	 * @since 3.0.0
	 * @param array $args
	 */
	public function __construct( array $args ) {

		parent::__construct( $args );

		$this->type        = 'packing-list';
		$this->name        = __( 'Packing List', 'woocommerce-pip' );
		$this->name_plural = __( 'Packing Lists', 'woocommerce-pip' );

		$this->optional_fields = [
			'sku',
			'weight',
		];

		$this->table_headers = [
			'sku'      => __( 'SKU' , 'woocommerce-pip' ),
			'product'  => __( 'Product' , 'woocommerce-pip' ),
			'quantity' => __( 'Quantity' , 'woocommerce-pip' ),
			'weight'   => __( 'Total Weight' , 'woocommerce-pip' ),
			'id'       => '', // leave this blank
		];

		$this->column_widths = [
			'sku'      => 25,
			'product'  => 50,
			'quantity' => 10,
			'weight'   => 15,
		];

		$this->show_billing_address      = false;
		$this->show_shipping_address     = true;
		$this->show_shipping_method      = true;
		$this->show_header               = false;
		$this->show_footer               = 'yes' === get_option( 'wc_pip_packing_list_show_footer', 'no' );
		$this->show_terms_and_conditions = 'yes' === get_option( 'wc_pip_packing_list_show_terms_and_conditions', 'no' );
		$this->show_customer_details     = 'yes' === get_option( 'wc_pip_packing_list_show_customer_details', 'no' );
		$this->show_customer_note        = 'yes' === get_option( 'wc_pip_packing_list_show_customer_note', 'yes' );
		$this->hide_virtual_items        = 'yes' === get_option( 'wc_pip_packing_list_exclude_virtual_items', 'no' );

		// Maybe subtract hidden items from order items count
		add_filter( 'wc_pip_order_items_count', [ $this, 'filter_order_items_count' ], 100, 2 );

		// Customize document header output
		add_action( 'wc_pip_header', [ $this, 'document_header' ], 1, 4 );

		// Do not output virtual items in template tables
		add_filter( 'wc_pip_document_table_row_item_data', [ $this, 'exclude_order_items' ], 1, 3 );

		// Filter the output of items in table rows
		add_filter( 'wc_pip_document_table_rows', [ $this, 'add_table_rows_headings' ], 40, 5 );
	}


	/**
	 * Filter the order items count
	 * if excluding virtual or downloadable items
	 *
	 * @since 3.0.0
	 * @param int $count
	 * @param array $items
	 * @return int
	 */
	public function filter_order_items_count( $count, $items ) {

		// filter only if we are hiding virtual products in list
		if ( $items && true === $this->hide_virtual_items ) {

			$count = 0;

			foreach ( $items as $item_id => $item_data ) {

				$product = isset( $item_data['product_id'] ) ? wc_get_product( $item_data['product_id'] ) : null;

				// add refunded quantities as they're negative integers
				$refund_qty = absint( $this->order->get_qty_refunded_for_item( $item_id ) );
				$item_qty   = isset( $item_data['qty'] ) ? max( 0, (int) $item_data['qty'] ) : 1;
				$qty        = max( 0, $item_qty - $refund_qty );

				// add to count only if not a virtual item or not hiding deleted products
				if ( ! $this->maybe_hide_virtual_item( $item_data ) || ! $this->maybe_hide_deleted_product( $product ) ) {
					$count += ( 1 * $qty );
				}
			}
		}

		return $count;
	}


	/**
	 * Outputs the document header.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type Document type
	 * @param string $action Document action
	 * @param \WC_PIP_Document $document Document object
	 * @param \WC_Order $order Order object
	 */
	public function document_header( $type, $action, $document, $order ) {

		$order_id = $order instanceof \WC_Order ? $order->get_id() : null;

		// prevent duplicating this content in bulk actions
		if ( ! $order_id || 'packing-list' !== $type || ( ( (int) $order_id !== (int) $this->order_id ) && has_action( 'wc_pip_header', array( $this, 'document_header' ) ) ) ) {
			return;
		}

		$view_order_url      = wc_get_endpoint_url( 'view-order', $order_id, get_permalink( wc_get_page_id( 'myaccount' ) ) );
		$invoice_number      = $document->get_invoice_number();
		$invoice_number_html = '<span class="invoice-number">' . $invoice_number . '</span>';
		$order_number        = $order->get_order_number();

		if ( 'send_email' !== $action ) {
			$order_number_html = '<a class="order-number hidden-print" href="' . $view_order_url . '" target="_blank">' . $order_number . '</a>' . '<span class="order-number visible-print-inline">' . $order_number . '</span>';
		} else {
			$order_number_html = '<span class="order-number">' . $order_number . '</span>';
		}

		// note: this is deliberately loose, do not use !== to compare invoice number and order number
		if ( 'yes' !== get_option( 'wc_pip_use_order_number', 'yes' ) || $invoice_number != $order_number ) {
			/* translators: Placeholders: %1$s - invoice number, %2$s - order number */
			$heading = sprintf( '<h3 class="order-info">' . esc_html__( 'Packing List for invoice %1$s (order %2$s)', 'woocommerce-pip') . '</h3>', $invoice_number_html, $order_number_html );
		} else {
			/* translators: Placeholder: %s order number */
			$heading = sprintf( '<h3 class="order-info">' . esc_html__( 'Packing List for order %s', 'woocommerce-pip' ) . '</h3>', $order_number_html );
		}

		/** This filter is documented in includes/class-wc-pip-document-invoice.php */
		echo wc_pip_parse_merge_tags( apply_filters( 'wc_pip_document_heading', $heading, $type, $action, $order, $invoice_number ), $type, $order );
	}


	/**
	 * Determines whether to exclude virtual/downloadable items from packing lists.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @param array $item_data order item data
	 * @param array|\WC_Order_Item $item order item object
	 * @param \WC_Product $product product object
	 * @return array
	 */
	public function exclude_order_items( $item_data, $item, $product ) {

		/**
		 * Filters whether an order item should be excluded from the packing list.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $exclude whether to exclude this product to be listed in packing list, default false (show)
		 * @param \WC_Product $product product object
		 * @param array $item order item meta
		 * @param array $item_data order item data
		 */
		$exclude = (bool) apply_filters( 'wc_pip_packing_list_exclude_item', false, $product, $item, $item_data );

		if ( in_array( true, [ $exclude, $this->maybe_hide_virtual_item( $item ), $this->maybe_hide_deleted_product( $product ) ], true ) ) {
			$item_data = [];
		}

		return $item_data;
	}


	/**
	 * Gets table group breadcrumb.
	 *
	 * Formats the product category to get a link to product category page and the parent category product category page.
	 *
	 * @since 3.0.0
	 *
	 * @param int|string $product_cat WP_Term id, object, name or slug
	 * @return string HTML
	 */
	public function get_table_order_items_group_breadcrumb( $product_cat ) {

		$term = null;

		if ( is_numeric( $product_cat ) && ! in_array( (string) $product_cat, [ '0', '-1' ], false ) ) {

			$term = get_term( $product_cat, 'product_cat' );

		} elseif ( is_string( $product_cat ) ) {

			$maybe_remove_parent    = explode( ' |pip| ', $product_cat );
			$product_cat_child_name = isset( $maybe_remove_parent[1] ) ? trim( $maybe_remove_parent[1] ) : trim( $maybe_remove_parent[0] );

			// try fetching by slug
			$term = get_term_by( 'slug', $product_cat_child_name, 'product_cat' );

			// if term was not found, try by name
			if ( ! $term || is_wp_error( $term ) ) {
				$term = get_term_by( 'name', $product_cat_child_name, 'product_cat' );
			}
		}

		if ( ! $term || is_wp_error( $term ) ) {

			if ( is_numeric( $product_cat ) && '-1' === (string) $product_cat && $this->is_type( 'pick-list' ) ) {
				// deleted products handling
				$urls = [ '#deleted'       => _x( 'Removed', 'Products deleted from the shop', 'woocommerce-pip' ) ];
			} else {
				// uncategorized products handling
				$urls = [ '#uncategorized' => __( 'Uncategorized', 'woocommerce-pip' ) ];
			}

		} else {

			// start off the breadcrumb tree with the current term link => label couple
			$urls = [ get_term_link( $term, 'product_cat' ) => $term->name ];

			// climb the term hierarchy to build the term breadcrumb tree
			while ( isset( $term->parent ) && $term->parent > 0 ) {

				// try to get the term parent link
				$term      = get_term( $term->parent, 'product_cat' );
				$term_link = get_term_link( $term, 'product_cat' );

				// if sanity check passes, add the link/name pair to the array
				if ( $term_link && ! is_wp_error( $term_link ) ) {
					/** @type string $term_link */
					$urls[ $term_link ] = $term->name;
				}
			}
		}

		$crumbs = [];

		foreach ( $urls as $url => $term_name ) {

			$url = trim( $url );

			switch ( $url ) {

				case '' :
				case '#deleted' :
				case '#uncategorized' :

					$crumb = '<a href="#">' . esc_html( $term_name ) . '</a>';

					if ( '#deleted' === $url ) {
						$crumb .= '<br><small><em>' . esc_html__ ( 'These items have been removed from the shop catalog', 'woocommerce-pip' ) . '</em></small>';
					}

					$crumbs[] = $crumb;

				break;

				default :
					$crumbs[] = '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $term_name ) . '</a>';
				break;
			}
		}

		return implode( '&nbsp; &gt; &nbsp;', array_reverse( $crumbs ) );
	}


	/**
	 * Adds headings to table rows items.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @param array $table_rows original table rows
	 * @param array $items order items
	 * @param int $order_id order ID
	 * @param string $document_type the document type
	 * @param \WC_PIP_Document the document object
	 * @return array new table rows
	 */
	public function add_table_rows_headings( $table_rows, $items, $order_id, $document_type, $document ) {

		$new_table_rows = [];

		if ( ! empty( $order_id ) ) {
			$this->order = wc_get_order( $order_id );
		}

		// Return if there is no items available in the order, i.e. exclude virtual items completely if setting is on.
		// This way we avoid printing empty documents and save trees. ;)
		if ( 0 === $this->get_items_count() ) {
			return $new_table_rows;
		}

		// for Shop Manager pick list, add information on the current order at the top header
		if ( 'pick-list' === $this->type && method_exists( $this, 'group_items_by' ) && 'order' === $this->group_items_by() && method_exists( $this, 'add_table_order_heading' ) ) {
			$new_table_rows[0] = $this->add_table_order_heading();
		}

		/**
		 * Filters whether to group items by category in packing list.
		 *
		 * @since 3.1.1
		 *
		 * @param bool $group_items_by_category default true (group items)
		 * @param int $order_id order ID
		 * @param string $type either 'packing-list' or 'pick-list'
		 */
		$group_items_by_category = (bool) apply_filters( 'wc_pip_packing_list_group_items_by_category', true, $this->order_id, $this->type );

		// output packing list similar to invoice if we don't group by category
		if ( ! is_object( $this->order ) || false === $group_items_by_category ) {

			$i = count( $new_table_rows ) + 1;

			if ( 0 === $this->get_items_count() ) {

				$new_table_rows[ $i ] = $this->add_no_shippable_items_row();

			} else {

				$sorted_table_rows = array_values( $table_rows );

				if ( ! empty( $sorted_table_rows ) ) {
					$new_table_rows[ $i ] = $sorted_table_rows[0];
				}
			}

			return $new_table_rows;
		}

		// group items by category
		if ( $this->get_items_count() > 0 ) {

			$items_grouped_by_category = array();

			// group first order items by product category id
			foreach ( $items as $item_id => $item ) {

				if ( isset( $item['variation_id'] ) && (int) $item['variation_id'] > 0 ) {
					$product_id = (int) $item['variation_id'];
				} elseif ( isset( $item['product_id'] ) ) {
					$product_id = (int) $item['product_id'];
				} else {
					$product_id = 0;
				}

				$product = $product_id > 0 ? wc_get_product( $product_id ) : null;

				// maybe skip deleted or virtual products
				if ( $this->maybe_hide_virtual_item( $item ) || $this->maybe_hide_deleted_product( $product ) ) {
					continue;
				}

				if ( ! $product instanceof \WC_Product ) {
					$product_categories     = [];
					$group_as_uncategorized = true; // will be placed into a 'deleted' group
				} else {
					$product_id             = $product->is_type( 'variation' ) ? $product->get_parent_id( 'edit' ) : $product->get_id();
					$product_categories     = wc_get_product_terms( $product_id, 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) );
					$group_as_uncategorized = false;
				}

				/**
				 * Filters whether to force a product to be grouped as uncategorized.
				 *
				 * @since 3.1.7
				 *
				 * @param bool $group_as_uncategorized whether to force group a product as uncategorized (default false)
				 * @param array $item the order item being grouped
				 * @param \WC_Order $order the order the item belongs to
				 */
				$group_as_uncategorized = (bool) apply_filters( 'wc_pip_packing_list_group_item_as_uncategorized', $group_as_uncategorized, $item, $this->order );

				if ( $group_as_uncategorized || is_wp_error( $product_categories ) || empty( $product_categories[0] ) ) {

					if ( ! $product instanceof \WC_Product && $this->is_type( 'pick-list' ) ) {
						// deleted items
						$items_grouped_by_category['-1'][] = (array) $this->get_table_row_order_item_data( $item_id, $item );
					} else {
						// uncategorized items
						$items_grouped_by_category['0'][]  = (array) $this->get_table_row_order_item_data( $item_id, $item );
					}

				} else {

					// we necessarily have to pick one individual category to build breadcrumbs later
					$child_category  = $product_categories[0];
					// get the top most parent as it will appear first in breadcrumbs later (left to right hierarchy)
					$parent_category = $this->get_parent_category( $child_category );
					// parent is used for indexing, child for pretty breadcrumbs later
					$items_grouped_by_category[ $parent_category->slug . ' |pip| ' . $child_category->slug ][] = (array) $this->get_table_row_order_item_data( $item_id, $item );
				}
			}

			$sort_alphabetically = $this->sort_order_items_alphabetically();

			// maybe sort category groups alphabetically
			if ( $sort_alphabetically ) {
				ksort( $items_grouped_by_category );
			}

			// loop groups and insert table headings
			$i = 1;
			foreach ( $items_grouped_by_category as $term_slug => $grouped_items ) {

				// maybe sort items within categories as well
				if ( $sort_alphabetically ) {
					usort( $grouped_items, array( $this, 'sort_order_items_by_column_key' ) );
				}

				$new_table_rows[ $i ] = [
					'headings' => [
						'breadcrumbs' => [
							'content' => $this->get_table_order_items_group_breadcrumb( $term_slug ),
							'colspan' => count( $this->get_column_widths() ),
						],
					],
					'items'    => $grouped_items,
				];

				$i ++;
			}

		} else {

			$new_table_rows[1] = $this->add_no_shippable_items_row();
		}

		return $new_table_rows;
	}


	/**
	 * Output a row to inform that order has no shippable items
	 *
	 * @since 3.1.1
	 * @return array
	 */
	protected function add_no_shippable_items_row() {

		$row = array(
			'headings' => array(
				'no-items'    => array(
					'content' => '<em>' . esc_html__( 'This order does not contain shippable items.', 'woocommerce-pip' ) . '</em>',
					'colspan' => count( $this->get_column_widths() ),
				)
			),
			'items' => array(),
		);

		return $row;
	}


	/**
	 * Get top most parent category recursively
	 *
	 * @since 3.1.1
	 * @param int|\WP_Term $term Product category object or id
	 * @return \WP_Term
	 */
	protected function get_parent_category( $term ) {

		if ( ! empty( $term->parent ) ) {

			$parent = get_term( $term->parent, 'product_cat' );

			if ( $parent instanceof \WP_Term ) {

				return $this->get_parent_category( $parent );
			}
		}

		return $term;
	}


	/**
	 * Gets item data.
	 *
	 * @since 3.0.0
	 *
	 * @param string $item_id Item id
	 * @param array $item Item data
	 * @param \WC_Product $product Product object
	 * @return array
	 */
	protected function get_order_item_data( $item_id, $item, $product ) {

		$item_meta = $this->get_order_item_meta_html( $item_id, $item, $product );
		$item_data = [
			'sku'      => $this->get_order_item_sku_html( $product, $item ),
			'product'  => $this->get_order_item_name_html( $product, $item ) . ( $item_meta ? '<br>' . $item_meta : '' ),
			'quantity' => $this->get_order_item_quantity_html( $item_id, $item ),
			'weight'   => $this->get_order_item_weight_html( $item_id, $item, $product ),
			'id'       => $this->get_order_item_id_html( $item_id ),
		];

		// remove any field that has no matching column
		foreach ( $item_data as $item_key => $data ) {
			if ( ! array_key_exists( $item_key, $this->get_table_headers() ) ) {
				unset( $item_data[ $item_key ] );
			}
		}

		/** This filter is documented in includes/class-wc-pip-document-invoice.php */
		return apply_filters( 'wc_pip_document_table_row_cells', $item_data, $this->type, $item_id, $item, $product, $this->order );
	}


	/**
	 * Gets the total weight of items in the document order.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_items_total_weight() {

		if ( ! is_object( $this->order ) ) {
			return '';
		}

		/** @var \WC_Order_Item_Product[] $items */
		$items        = $this->order->get_items();
		$total_weight = 0;

		// Loop through items in order to add to total weight
		foreach ( $items as $item_id => $item ) {

			if ( isset( $item['qty'], $item['product_id'] ) ) {

				$item_qty     = max( (int) $item['qty'], 0 );
				$refunded_qty = absint( $this->order->get_qty_refunded_for_item( $item_id ) );
				$total_qty    = max( 0, $item_qty - $refunded_qty );
				$product      = $item->get_product();

				if ( ! $product instanceof \WC_Product || $total_qty < 1 || $this->maybe_hide_virtual_item( $item ) ) {
					continue;
				}

				$item_weight  = (float) $product->get_weight();
				$items_weight = (float) ( max( $item_weight, 0 ) * $total_qty );

				/** This filter is documented in includes/abstract-wc-pip-document.php */
				$total_weight += apply_filters( 'wc_pip_order_item_weight', (float) $items_weight, $item, wc_get_product( (int) $item['product_id'] ), $this->order );
			}
		}

		$weight_unit = get_option( 'woocommerce_weight_unit' );

		/**
		 * Filters the total weight of items in the order.
		 *
		 * @since 3.0.0
		 * @param string $formatted_weight Total weight with weight unit text
		 * @param float $total_weight Total weight of items in order
		 * @param string $weight_unit Weight unit as per store option
		 * @param \WC_Order $order The order object
		 */
		return apply_filters( 'wc_pip_order_items_total_weight', $total_weight . ' ' . $weight_unit, $total_weight, $weight_unit, $this->order );
	}


	/**
	 * Get table footer
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_table_footer() {

		$rows = array();

		if ( ! is_object( $this->order ) || $this->get_items_count() === 0 ) {
			return $rows;
		}

		$rows['totals'] = array(
			'colspan'        => '<strong>' . __( 'Totals:', 'woocommerce-pip' ) . '</strong>',
			/* translators: Placeholder: %d - total amount of items in packing list */
			'total-quantity' => '<strong>' . sprintf( _n( '%d pc.', '%d pcs.', $this->get_items_count(), 'woocommerce-pip' ), $this->get_items_count() ) . '</strong>',
			'total-weight'   => '<strong>' . $this->get_items_total_weight() . '</strong>',
		);

		if ( in_array( 'weight', $this->optional_fields, true ) && ! in_array( 'weight', $this->get_chosen_fields(), true ) ) {
			unset( $rows['totals']['total-weight'] );
		}

		/** This filter is documented in includes/class-wc-pip-document-invoice.php */
		return apply_filters( 'wc_pip_document_table_footer', $rows, $this->type, $this->order_id );
	}


}
