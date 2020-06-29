<?php
/**
 * Admin exclusion table class
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_WCWTL_Exclusions_Table' ) ) {
	/**
	 * Exclusion table
	 *
	 * @class   YITH_WCWTL_Exclusions_Table
	 * @since   1.0.0
	 * @author  Yithemes
	 *
	 * @package YITH Woocommerce Waiting List
	 */
	class YITH_WCWTL_Exclusions_Table extends WP_List_Table {
		/**
		 * Construct
		 */
		public function __construct() {

			//Set parent defaults
			parent::__construct( array(
					'singular' => 'product', //singular name of the listed records
					'plural'   => 'products', //plural name of the listed records
					'ajax'     => false //does this table support ajax?
				)
			);
		}

		/**
		 * Returns columns available in table
		 *
		 * @since 1.1.3
		 * @return array Array of columns of the table
		 */
		public function get_columns() {
			$columns = array(
				'cb'        => '<input type="checkbox" />',
				'product'   => __( 'Product', 'yith-woocommerce-waiting-list' ),
				'variation' => __( 'Variation', 'yith-woocommerce-waiting-list' ),
				'thumb'     => __( 'Thumbnail', 'yith-woocommerce-waiting-list' ),
				'actions'   => __( 'Action', 'yith-woocommerce-waiting-list' ),
			);

			return $columns;
		}

		/**
		 * Print the columns information
		 *
		 * @since 1.1.3
		 * @param $column_name
		 *
		 * @param $rec
		 * @return string
		 */
		public function column_default( $rec, $column_name ) {

			$product = wc_get_product( intval( $rec['product_id'] ) );
			if ( ! $product ) {
				return null;
			}

			/** @var WC_Product $product */
			switch ( $column_name ) {

				case 'product':
					$product_query_args = array(
						'post'   => yit_get_base_product_id( $product ),
						'action' => 'edit',
					);
					$product_url        = add_query_arg( $product_query_args, admin_url( 'post.php' ) );

					return sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">%s</a></strong>', esc_url( $product_url ), esc_html__( 'Edit product', 'yith-woocommerce-waiting-list' ), $product->get_title() );
					break;

				case 'variation':

					if ( $product->is_type( 'variation' ) ) {

						$variations = $product->get_variation_attributes();
						$html       = '<ul>';
						foreach ( $variations as $key => $value ) {
							$key  = ucfirst( str_replace( 'attribute_pa_', '', $key ) );
							$html .= '<li>' . $key . ': ' . $value . '</li>';
						}
						$html .= '</ul>';

						return $html;
					} else {
						return '-';
					}
					break;

				case 'thumb' :
					return $product->get_image();
					break;

				case 'actions':
					$delete_query_args = array(
						'page'   => $_GET['page'],
						'tab'    => $_GET['tab'],
						'action' => 'delete',
						'id'     => $rec['product_id'],
					);
					$delete_url        = add_query_arg( $delete_query_args, admin_url( 'admin.php' ) );

					return '<a href="' . esc_url( $delete_url ) . '" class="button">' . esc_html__( 'Remove', 'yith-woocommerce-waiting-list' ) . '</a>';
					break;
			}

			return null;
		}

		/**
		 * Prints column cb
		 *
		 * @since 1.1.3
		 * @param $rec Object Item to use to print CB record
		 *
		 * @return string
		 */
		public function column_cb( $rec ) {
			return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $rec['product_id'] );
		}

		/**
		 * Sets bulk actions for table
		 *
		 * @since 1.1.3
		 * @return array Array of available actions
		 */
		public function get_bulk_actions() {
			$actions = array(
				'delete' => __( 'Remove from list', 'yith-woocommerce-waiting-list' ),
			);

			return apply_filters( 'yith_wcwtl_waitlistusers_table_bulk_actions', $actions );
		}

		/**
		 * Prepare items for table
		 *
		 * @since 1.1.3
		 * @param array $args
		 *
		 */
		public function prepare_items( $args = array() ) {

			// blacklist args
			$q = wp_parse_args( $args, array(
				'paged'  => absint( $this->get_pagenum() ),
				'number' => 20,
				's'      => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
			) );

			global $wpdb;

			// query parts initializating
			$pieces  = array( 'where', 'orderby', 'limits' );
			$where   = $wpdb->prepare( "pm.meta_key = '%s' AND pm.meta_value = '1'", '_yith_wcwtl_exclude_list' );
			$orderby = "";

			// Search
			if ( $q['s'] ) {
				// added slashes screw with quote grouping when done early, so done later
				$q['s'] = stripslashes( $q['s'] );
				// there are no line breaks in <input /> fields
				$q['s'] = str_replace( array( "\r", "\n" ), '', $q['s'] );

				$s     = $wpdb->prepare( "p.post_title LIKE %s", "%{$q['s']}%" );
				$where .= " AND {$s}";
			}

			// Paging
			if ( ! empty( $q['paged'] ) && ! empty( $q['number'] ) ) {
				$page = absint( $q['paged'] );
				! $page && $page = 1;

				if ( empty( $q['offset'] ) ) {
					$pgstrt = absint( ( $page - 1 ) * $q['number'] ) . ', ';
				} else { // we're ignoring $page and using 'offset'
					$q['offset'] = absint( $q['offset'] );
					$pgstrt      = $q['offset'] . ', ';
				}
				$limits = 'LIMIT ' . $pgstrt . $q['number'];
			}

			$clauses = compact( $pieces );

			$where   = isset( $clauses['where'] ) ? $clauses['where'] : '';
			$orderby = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
			$limits  = isset( $clauses['limits'] ) ? $clauses['limits'] : '';

			$items       = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS p.ID AS product_id FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id WHERE $where $orderby $limits", ARRAY_A );
			$total_items = $wpdb->get_var( "SELECT FOUND_ROWS()" );

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			// retrieve data for table
			$this->items = $items;

			// sets pagination args
			if ( ! empty( $q['number'] ) ) {
				$this->set_pagination_args(
					array(
						'total_items' => $total_items,
						'per_page'    => $q['number'],
						'total_pages' => ceil( $total_items / $q['number'] ),
					)
				);
			}
		}

		/**
		 * Display the search box.
		 *
		 * @since  3.1.0
		 * @access public
		 *
		 * @param string $text     The search button text
		 * @param string $input_id The search input id
		 */
		public function add_search_box( $text, $input_id ) {
			parent::search_box( $text, $input_id );
		}

		/**
		 * Message to be displayed when there are no items
		 *
		 * @since  3.1.0
		 * @access public
		 */
		public function no_items() {
			esc_html_e( 'No items found.', 'yith-woocommerce-waiting-list' );
		}
	}
}