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
 * Implements the YITH_YWRAQ_Exclusions_Cat_Table class.
 *
 * @class   YITH_YWRAQ_Exclusions_Cat_Table
 * @package YITH
 * @since   2.0.0
 * @author  YITH
 * @extends WP_List_Table
 *
 */
class YITH_YWRAQ_Exclusions_Cat_Table extends WP_List_Table {

	/**
	 * Class constructor method
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		// Set parent defaults
		parent::__construct( array(
			'singular' => 'excluded_category',     //singular name of the listed records
			'plural'   => 'excluded_categories',    //plural name of the listed records
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
	 * Print category image column content
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

		$column = '<img src="' . esc_url( $item['image'] ) . '" alt="' . __( 'Thumbnail', 'yith-woocommerce-request-a-quote' ) . '" class="wp-post-image" height="48" width="48" />';

		return $column;
	}

	/**
	 * Print name column content
	 *
	 * @param $item mixed Item of the row
	 *
	 * @return string Column content
	 * @since 2.0.0
	 */
	public function column_name( $item ) {
		if ( ! isset( $item['name'] ) || ! isset( $item['ID'] ) ) {
			return '';
		}

		$column = '<strong><a href="' . esc_url( get_edit_term_link( $item['ID'], 'product_cat' ) ) . '">' . $item['name'] . '</a></strong>';

		return $column;
	}

	/**
	 * Print slug column content
	 *
	 * @param $item mixed Item of the row
	 *
	 * @return string Column content
	 * @since 2.0.0
	 */
	public function column_slug( $item ) {
		if ( ! isset( $item['slug'] ) ) {
			return '';
		}

		$column = $item['slug'];

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
	public function column_description( $item ) {
		if ( ! isset( $item['description'] ) ) {
			return '';
		}

		$column = $item['description'];

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
	public function column_count( $item ) {
		if ( ! isset( $item['count'] ) ) {
			return '';
		}

		$column = $item['count'];

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
			'remove_cat_exclusion' => $item['ID'],
			'remove_nonce'         => wp_create_nonce( 'yith_ywraq_remove_exclusions_cat' )
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
			'image'       => __( 'Image', 'yith-woocommerce-request-a-quote' ),
			'name'        => __( 'Name', 'yith-woocommerce-request-a-quote' ),
			'description' => __( 'Description', 'yith-woocommerce-request-a-quote' ),
			'slug'        => __( 'Slug', 'yith-woocommerce-request-a-quote' ),
			'count'       => __( 'Count', 'yith-woocommerce-request-a-quote' ),
			'actions'     => __( 'Actions', 'yith-woocommerce-request-a-quote' )
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
			'name'  => array( 'categories_name', false ),
			'count' => array( 'categories_count', true )
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

		$exclusions      = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-cat-list' ) ) );
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

			foreach ( $exclusions as $cat_id ) {
				$category = get_term_by( 'id', $cat_id, 'product_cat' );
				$thumb    = function_exists( 'get_term_meta' ) ? get_term_meta( $cat_id, 'thumbnail_id', true ) : get_metadata( 'woocommerce_term', $cat_id, 'thumbnail_id', true );

				if ( $thumb ) {
					$image = wp_get_attachment_thumb_url( $thumb );
				} else {
					$image = wc_placeholder_img_src();
				}

				$new_item = array(
					'ID'          => $cat_id,
					'name'        => $category->name,
					'slug'        => $category->slug,
					'description' => $category->description,
					'count'       => $category->count,
					'image'       => str_replace( ' ', '%20', $image )
				);

				$exclusion_items[] = $new_item;
			}

			$categories_name  = get_array_column( $exclusion_items, 'name' );
			$categories_count = get_array_column( $exclusion_items, 'count' );

			$column_order = isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array(
				'categories_name',
				'categories_count'
			) ) ? $_REQUEST['orderby'] : 'categories_name';
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