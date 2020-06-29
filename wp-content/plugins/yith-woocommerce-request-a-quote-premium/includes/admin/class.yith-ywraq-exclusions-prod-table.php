<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Implements the YITH_YWRAQ_Exclusions_Prod_Table class.
 *
 * @class   YITH_YWRAQ_Exclusions_Prod_Table
 * @package YITH
 * @since   2.0.0
 * @author  YITH
 * @extends WP_List_Table
 *
 */
class YITH_YWRAQ_Exclusions_Prod_Table extends WP_List_Table {

	/**
	 * Class constructor method.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		// Set parent defaults
		parent::__construct( array(
			'singular' => 'excluded_product',     //singular name of the listed records
			'plural'   => 'excluded_products',    //plural name of the listed records
			'ajax'     => false          //does this table support ajax?
		) );
	}

	/* === COLUMNS METHODS === */

	/**
	 * Print default column content
	 *
	 * @param $item mixed Item of the row
	 * @param $column_name string Column name
	 *
	 * @return string Column content
	 * @since 2.0.0
	 */
	public function column_default( $item, $column_name ) {
		if ( isset( $item[ $column_name ] ) ) {
			return esc_html( $item[ $column_name ] );
		} else {
			return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Print product column content
	 *
	 * @param $item mixed Item of the row
	 *
	 * @return string Column content
	 * @since 2.0.0
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
	 * @param $item mixed Item of the row
	 *
	 * @return string Column content
	 * @since 2.0.0
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
	 * @param $item mixed Item of the row
	 *
	 * @return string Column content
	 * @since 2.0.0
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
	 * @param $item mixed Item of the row
	 *
	 * @return string Column content
	 * @since 2.0.0
	 */
	public function column_stock( $item ) {
		if ( ! isset( $item['stock'] ) ) {
			return '';
		}

		$status = $item['stock'];

		$availability = ( isset( $status['availability'] ) && $status['availability'] != '' ) ? $status['availability'] : __( 'In Stock', 'yith-woocommerce-request-a-quote' );
		$class        = ( isset( $status['class'] ) && $status['class'] != '' ) ? $status['class'] : 'in-stock';

		$column = '<span class="' . $class . '">' . $availability . '</span>';

		return $column;
	}

	/**
	 * Print actions column content
	 *
	 * @param $item mixed Item of the row
	 *
	 * @return string Column content
	 * @since 2.0.0
	 */
	public function column_actions( $item ) {

		$args = array(
			'remove_prod_exclusion' => $item['ID'],
			'remove_nonce'          => wp_create_nonce( 'yith_ywraq_remove_exclusions_prod' )
		);

		$column = sprintf( '<a href="%s" class="button button-secondary yith-ywraq-remove-exclusion">%s</a>', esc_url( add_query_arg( $args ) ), __( 'Delete', 'yith-woocommerce-request-a-quote' ) );

		return $column;
	}

	/**
	 * Returns columns available in table
	 *
	 * @return array Array of columns of the table
	 * @since 2.0.0
	 */
	public function get_columns() {
		$columns = array(
			'image'   => __( 'Image', 'yith-woocommerce-request-a-quote' ),
			'product' => __( 'Product', 'yith-woocommerce-request-a-quote' ),
			'price'   => __( 'Price', 'yith-woocommerce-request-a-quote' ),
			'stock'   => __( 'Stock Status', 'yith-woocommerce-request-a-quote' ),
			'actions' => __( 'Actions', 'yith-woocommerce-request-a-quote' )
		);

		return $columns;
	}

	/**
	 * Returns column to be sortable in table
	 *
	 * @return array Array of sortable columns
	 * @since 2.0.0
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'product' => array( 'products_name', false ),
			'price'   => array( 'products_price', true )
		);

		return $sortable_columns;
	}

	/**
	 * Prepare items for table
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function prepare_items() {

		$exclusions      = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-prod-list' ) ) );
		$exclusion_items = array();

		// sets pagination arguments
		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$total_items  = count( $exclusions );

		// sets columns headers
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		if ( ! empty( $exclusions ) ) {

			foreach ( $exclusions as $product_id ) {
				$product = wc_get_product( $product_id );

				if ( $product ) {
				
				
				$new_item = array(
					'ID'    => $product_id,
					'name'  => $product->get_formatted_name(),
					'price' => $product->get_price(),
					'image' => $product->get_image( 'shop_thumbnail' ),
					'stock' => $product->get_availability()
				);

				$exclusion_items[] = $new_item;
				}
			}

			$products_name  = get_array_column( $exclusion_items, 'name' );
			$products_price = get_array_column( $exclusion_items, 'price' );

			$column_order = isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array(
				'products_name',
				'products_price'
			) ) ? $_REQUEST['orderby'] : 'products_name';
			$order        = isset( $_REQUEST['order'] ) ? 'SORT_' . strtoupper( $_REQUEST['order'] ) : 'SORT_ASC';

			array_multisort( ${$column_order}, constant( $order ), $exclusion_items );
		}

		// retrieve data for table
		$this->items = $exclusion_items;

		// sets pagination args
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}

}