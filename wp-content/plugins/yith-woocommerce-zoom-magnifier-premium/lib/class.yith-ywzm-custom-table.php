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

if ( ! class_exists('YITH_YWZM_Custom_Table') ) {
	/**
	 * Shows a custom table
	 *
	 * @class   YITH_YWZM_Custom_Table
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @extends WP_List_Table
	 *
	 */
	class YITH_YWZM_Custom_Table extends WP_List_Table {

		/**
		 * @var array $options array of options for table showing
		 */
		var $options;

		/**
		 * Constructor
		 *
		 * @param   $args array|string array or string of arguments
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @see     WP_List_Table
		 */
		function __construct( $args ) {
			global $status, $page;

			parent::__construct( $args );
		}

		function get_columns() {

			$columns = array(
				'cb'            => '<input type="checkbox" />',
				'product'       => esc_html__( 'Product', 'yith-woocommerce-zoom-magnifier' )
			);
			return $columns;
		}


		/**
		 * Default column renderer
		 *
		 * @param   $item array the row
		 * @param   $column_name string the column name
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @return  string
		 */
		function column_default( $item, $column_name ) {
			return $item[ $column_name ];
		}

		/**
		 * Checkbox column renderer
		 *
		 * @param   $item array the row
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @return  string
		 */
		function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="id[]" value="%s" />',
				$item[ $this->options['key_column'] ]
			);
		}

		/**
		 * Return array of bulk options
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @return  array
		 */
		function get_bulk_actions() {
			return $this->options['bulk_actions']['actions'];
		}

		/**
		 * Processes bulk actions
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @return  void
		 */
		function process_bulk_action() {

			$action = 'function_' . $this->current_action();

			if ( array_key_exists( $action, $this->options['bulk_actions']['functions'] ) ) {

				call_user_func( $this->options['bulk_actions']['functions'][ $action ] );

			}

		}

		/**
		 * It will get rows from database and prepare them to be showed in table
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @return  void
		 */
		function prepare_items() {
			global $wpdb;

			$select_table   = $this->options['select_table'];
			$select_columns = implode( ',', $this->options['select_columns'] );
			$select_where   = $this->options['select_where'] != '' ? 'WHERE ' . $this->options['select_where'] : '';
			$select_group   = $this->options['select_group'] != '' ? 'GROUP BY ' . $this->options['select_group'] : '';
			$select_limit   = $this->options['select_limit'];

			$count_table = $this->options['count_table'];
			$count_where = $this->options['count_where'] != '' ? 'WHERE ' . $this->options['count_where'] : '';

			$view_columns     = $this->options['view_columns'];
			$hidden_columns   = $this->options['hidden_columns'];
			$sortable_columns = $this->options['sortable_columns'];

			// Here we configure table headers, defined in our methods
			$this->_column_headers = array( $view_columns, $hidden_columns, $sortable_columns );

			// Process bulk action if any
			$this->process_bulk_action();

			// Will be used in pagination settings
			$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $count_table $count_where" );

			// Prepare query params, as usual current page, order by and order direction
			$paged   = isset( $_GET['paged'] ) ? $select_limit * ( intval( $_GET['paged'] ) - 1 ) : 0;
			$orderby = ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_GET['orderby'] : $this->options['select_order'];
			$order   = ( isset( $_GET['order'] ) && in_array( $_GET['order'], array(
						'asc',
						'desc'
					) ) ) ? $_GET['order'] : 'asc';

			$this->items = $wpdb->get_results( $wpdb->prepare( "
                        SELECT $select_columns
                        FROM $select_table
                        $select_where
                        $select_group
                        ORDER BY $orderby $order
                        LIMIT %d OFFSET %d
                        ", $select_limit, $paged ), ARRAY_A );

			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $select_limit,
				'total_pages' => ceil( $total_items / $select_limit )
			) );
		}

		/**
		 * Generates the columns for a single row of the table; overrides original class function
		 *
		 * @param   $item array the row
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @return  string
		 * @see     WP_List_Table
		 */
		protected function single_row_columns( $item ) {
			list( $columns, $hidden ) = $this->get_column_info();

			foreach ( $columns as $column_name => $column_display_name ) {
				$class = "class='$column_name column-$column_name'";

				$style = '';
				if ( in_array( $column_name, $hidden ) ) {
					$style = ' style="display:none;"';
				}

				$attributes = "$class$style";

				if ( 'cb' == $column_name ) {
					echo '<th scope="row" class="check-column">';
					echo $this->column_cb( $item );
					echo '</th>';
				} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
					echo "<td $attributes>";
					echo call_user_func( array( $this, 'column_' . $column_name ), $item );
					echo "</td>";
				} elseif ( isset( $this->options['custom_columns'][ 'column_' . $column_name ] ) ) {
					echo "<td $attributes>";
					echo call_user_func_array( $this->options['custom_columns'][ 'column_' . $column_name ], array(
							$item,
							$this
						) );
					echo "</td>";
				} else {
					echo "<td $attributes>";
					echo $this->column_default( $item, $column_name );
					echo "</td>";
				}
			}
		}

	}
}