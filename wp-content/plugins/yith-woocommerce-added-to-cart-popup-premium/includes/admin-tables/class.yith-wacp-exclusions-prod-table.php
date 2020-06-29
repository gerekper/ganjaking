<?php
/**
 * Exclusion products table class
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Shows a custom table
 *
 * @class   YITH_WACP_Custom_Table
 * @since   1.0.0
 * @author  Your Inspiration Themes
 * @extends WP_List_Table
 *
 * @package Yithemes
 */
class YITH_WACP_Exclusions_Prod_Table extends WP_List_Table {

	/**
	 * Class constructor method
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => 'excluded_product',
				'plural'   => 'excluded_products',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Print default column content
	 *
	 * @since 1.0.0
	 * @param array  $item        Item of the row.
	 * @param string $column_name Column name.
	 * @return string Column content.
	 */
	public function column_default( $item, $column_name ) {
		if ( isset( $item[ $column_name ] ) ) {
			return esc_html( $item[ $column_name ] );
		} else {
			return print_r( $item, true ); // phpcs:ignore
		}
	}

	/**
	 * Print product column content
	 *
	 * @since 1.0.0
	 * @param array $item Item of the row.
	 * @return string Column content.
	 */
	public function column_product( $item ) {
		if ( ! isset( $item['name'] ) || empty( $item['name'] ) ) {
			return '';
		}

		$column = sprintf( '<strong><a href="%s">%s</a></strong>', get_edit_post_link( $item['ID'] ), $item['name'] );

		return $column;
	}

	/**
	 * Print price column content
	 *
	 * @since 1.0.0
	 * @param array $item Item of the row.
	 * @return string Column content.
	 */
	public function column_price( $item ) {
		if ( ! isset( $item['price'] ) || empty( $item['price'] ) ) {
			return '';
		}

		$column = wc_price( $item['price'] );

		return $column;
	}

	/**
	 * Print thumb column content
	 *
	 * @since 1.0.0
	 * @param array $item Item of the row.
	 * @return string Column content.
	 */
	public function column_image( $item ) {
		if ( ! isset( $item['image'] ) ) {
			return '';
		}

		$column = $item['image'];

		return $column;
	}

	/**
	 * Print stock column content
	 *
	 * @since 1.0.0
	 * @param array $item Item of the row.
	 * @return string Column content.
	 */
	public function column_stock( $item ) {
		if ( ! isset( $item['stock'] ) ) {
			return '';
		}

		$status = $item['stock'];

		$availability = ( isset( $status['availability'] ) && '' !== $status['availability'] ) ? $status['availability'] : __( 'In Stock', 'yith-woocommerce-added-to-cart-popup' );
		$class        = ( isset( $status['class'] ) && '' !== $status['class'] ) ? $status['class'] : 'in-stock';

		$column = '<span class="' . $class . '">' . esc_html( $availability ) . '</span>';

		return $column;
	}

	/**
	 * Print actions column content
	 *
	 * @since 1.0.0
	 * @param array $item Item of the row.
	 * @return string Column content.
	 */
	public function column_actions( $item ) {

		$args = array(
			'remove_prod_exclusion' => $item['ID'],
			'remove_nonce'          => wp_create_nonce( 'yith_wacp_remove_exclusions_prod' ),
		);

		$column = sprintf( '<a href="%s" class="button button-secondary yith-wacp-remove-exclusion">%s</a>', esc_url( add_query_arg( $args ) ), __( 'Delete', 'yith-woocommerce-added-to-cart-popup' ) );

		return $column;
	}

	/**
	 * Returns columns available in table
	 *
	 * @since 1.0.0
	 * @return array Array of columns of the table.
	 */
	public function get_columns() {
		$columns = array(
			'image'   => __( 'Image', 'yith-woocommerce-added-to-cart-popup' ),
			'product' => __( 'Product', 'yith-woocommerce-added-to-cart-popup' ),
			'price'   => __( 'Price', 'yith-woocommerce-added-to-cart-popup' ),
			'stock'   => __( 'Stock Status', 'yith-woocommerce-added-to-cart-popup' ),
			'actions' => __( 'Actions', 'yith-woocommerce-added-to-cart-popup' ),
		);

		return $columns;
	}

	/**
	 * Returns column to be sortable in table
	 *
	 * @since 1.0.0
	 * @return array Array of sortable columns.
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'product' => array( 'products_name', false ),
			'price'   => array( 'products_price', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Prepare items for table
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended

		$exclusions      = array_filter( explode( ',', get_option( 'yith-wacp-exclusions-prod-list' ) ) );
		$exclusion_items = array();

		// Sets pagination arguments.
		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_items  = count( $exclusions );

		// Sets columns headers.
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		if ( ! empty( $exclusions ) ) {

			foreach ( $exclusions as $product_id ) {
				$product = wc_get_product( $product_id );

				if ( $product && $product instanceof WC_Product ) {
					$new_item = array(
						'ID'    => $product_id,
						'name'  => $product->get_formatted_name(),
						'price' => $product->get_price(),
						'image' => $product->get_image( 'shop_thumbnail' ),
						'stock' => $product->get_availability(),
					);

					$exclusion_items[] = $new_item;
				}
			}

			$products_name  = get_array_column( $exclusion_items, 'name' );
			$products_price = get_array_column( $exclusion_items, 'price' );

			$column_order = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : '';
			$column_order = in_array( $column_order, array( 'products_name', 'products_price' ) ) ? $column_order : 'products_name';

			$order = isset( $_REQUEST['order'] ) ? 'SORT_' . strtoupper( sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) ) : 'SORT_ASC';

			array_multisort( ${$column_order}, constant( $order ), $exclusion_items );
		}

		// Retrieve data for table.
		$this->items = $exclusion_items;

		// Sets pagination args.
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
