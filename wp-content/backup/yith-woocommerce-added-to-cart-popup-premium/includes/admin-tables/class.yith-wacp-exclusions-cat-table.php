<?php
/**
 * Exclusion categories table class
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup Premium
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

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
class YITH_WACP_Exclusions_Cat_Table extends WP_List_Table {

	/**
	 * Class constructor method
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'excluded_category',
				'plural'   => 'excluded_categories',
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
	 * @return string Column content
	 */
	public function column_default( $item, $column_name ) {
		if ( isset( $item[ $column_name ] ) ) {
			return esc_html( $item[ $column_name ] );
		} else {
			return print_r( $item, true ); // phpcs:ignore
		}
	}

	/**
	 * Print category image column content
	 *
	 * @since 1.0.0
	 * @param array $item Item of the row.
	 * @return string Column content.
	 */
	public function column_image( $item ) {
		if ( ! isset( $item['image'] ) ) {
			return '';
		}

		$column = '<img src="' . esc_url( $item['image'] ) . '" alt="' . esc_attr__( 'Thumbnail', 'yith-woocommerce-added-to-cart-popup' ) . '" class="wp-post-image" height="48" width="48" />';

		return $column;
	}

	/**
	 * Print name column content
	 *
	 * @since 1.0.0
	 * @param array $item Item of the row.
	 * @return string Column content.
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
	 * @since 1.0.0
	 * @param array $item Item of the row.
	 * @return string Column content.
	 */
	public function column_slug( $item ) {
		if ( ! isset( $item['slug'] ) ) {
			return '';
		}

		return $item['slug'];
	}

	/**
	 * Print stock column content
	 *
	 * @since 1.0.0
	 * @param array $item Item of the row.
	 * @return string Column content.
	 */
	public function column_description( $item ) {
		if ( ! isset( $item['description'] ) ) {
			return '';
		}

		return $item['description'];
	}

	/**
	 * Print stock column content
	 *
	 * @since 1.0.0
	 * @param array $item Item of the row.
	 * @return string Column content.
	 */
	public function column_count( $item ) {
		if ( ! isset( $item['count'] ) ) {
			return '';
		}
		return $item['count'];
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
			'remove_cat_exclusion' => $item['ID'],
			'remove_nonce'         => wp_create_nonce( 'yith_wacp_remove_exclusions_cat' ),
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
			'image'       => __( 'Image', 'yith-woocommerce-added-to-cart-popup' ),
			'name'        => __( 'Name', 'yith-woocommerce-added-to-cart-popup' ),
			'description' => __( 'Description', 'yith-woocommerce-added-to-cart-popup' ),
			'slug'        => __( 'Slug', 'yith-woocommerce-added-to-cart-popup' ),
			'count'       => __( 'Count', 'yith-woocommerce-added-to-cart-popup' ),
			'actions'     => __( 'Actions', 'yith-woocommerce-added-to-cart-popup' ),
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
			'name'  => array( 'categories_name', false ),
			'count' => array( 'categories_count', true ),
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

		$exclusions      = array_filter( explode( ',', get_option( 'yith-wacp-exclusions-cat-list' ) ) );
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
					'image'       => str_replace( ' ', '%20', $image ),
				);

				$exclusion_items[] = $new_item;
			}

			$categories_name  = get_array_column( $exclusion_items, 'name' );
			$categories_count = get_array_column( $exclusion_items, 'count' );

			$column_order = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : '';
			$column_order = in_array( $column_order, array( 'categories_name', 'categories_count' ) ) ? $column_order : 'categories_name';

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
