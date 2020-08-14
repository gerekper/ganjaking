<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( !class_exists( 'YITH_Custom_Table' ) ) {

    /**
     * Shows a custom table
     *
     * @class   YITH_Custom_Table
     * @package Yithemes
     * @since   1.0.0
     * @author  Your Inspiration Themes
     * @extends WP_List_Table
     *
     */
    class YITH_Custom_Table extends WP_List_Table {

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
        public function __construct( $args ) {
            global $status, $page;

            parent::__construct( $args );
        }

        /**
         * Get a list of columns.
         *
         * @since   1.0.0
         * @return  array
         * @author  Alberto Ruggiero
         */
        public function get_columns() {

            return $this->options['view_columns'];

        }

        /**
         * Default column renderer
         *
         * @since   1.0.0
         *
         * @param   $item        array the row
         * @param   $column_name string the column name
         *
         * @return  string
         * @author  Alberto Ruggiero
         */
        protected function column_default( $item, $column_name ) {
            return $item[$column_name];
        }

        /**
         * Checkbox column renderer
         *
         * @since   1.0.0
         *
         * @param   $item array the row
         *
         * @return  string
         * @author  Alberto Ruggiero
         *
         */
        protected function column_cb( $item ) {

            return sprintf(
                '<input type="checkbox" name="id[]" value="%s" />',
                $item[$this->options['key_column']]
            );

        }

        /**
         * Return array of bulk options
         *
         * @since   1.0.0
         * @return  array
         * @author  Alberto Ruggiero
         */
        protected function get_bulk_actions() {
            return $this->options['bulk_actions']['actions'];
        }

        /**
         * Return array of sortable columns
         *
         * @since   1.0.0
         * @return  array
         * @author  Alberto Ruggiero
         */
        protected function get_sortable_columns() {

            return $this->options['sortable_columns'];

        }

        /**
         * Processes bulk actions
         *
         * @since   1.0.0
         * @return  void
         * @author  Alberto Ruggiero
         */
        public function process_bulk_action() {

            $action = 'function_' . $this->current_action();

            if ( array_key_exists( $action, $this->options['bulk_actions']['functions'] ) ) {

                call_user_func( $this->options['bulk_actions']['functions'][$action] );

            }

        }

        /**
         * It will get rows from database and prepare them to be showed in table
         *
         * @since   1.0.0
         * @return  void
         * @author  Alberto Ruggiero
         */
        public function prepare_items() {
            global $wpdb;

            $select_table   = $this->options['select_table'];
            $select_columns = implode( ',', $this->options['select_columns'] );
            $select_where   = $this->options['select_where'] != '' ? 'WHERE ' . $this->options['select_where'] : '';
            $select_group   = $this->options['select_group'] != '' ? 'GROUP BY ' . $this->options['select_group'] : '';
            $select_limit   = $this->get_items_per_page( $this->options['per_page_option'], 10 );

            $count_table = $this->options['count_table'];
            $count_where = $this->options['count_where'] != '' ? 'WHERE ' . $this->options['count_where'] : '';

            if ( !empty( $this->options['search_where'] ) && isset( $_REQUEST['s'] ) ) {

                $search_where = array();

                foreach ( $this->options['search_where'] as $search_param ) {

                    $search_where[] = $search_param . " LIKE '%{$wpdb->esc_like( $_REQUEST['s'] )}%'";

                }

                $select_where .= ( $this->options['select_where'] != '' ? ' AND (' : 'WHERE (' ) . implode( ' OR ', $search_where ) . ') ';
                $count_where .= ( $this->options['count_where'] != '' ? ' AND (' : 'WHERE (' ) . implode( ' OR ', $search_where ) . ') ';

            }


            $view_columns     = $this->get_columns();
            $hidden_columns   = $this->options['hidden_columns'];
            $sortable_columns = $this->get_sortable_columns();

            // Here we configure table headers, defined in our methods
            $this->_column_headers = array( $view_columns, $hidden_columns, $sortable_columns );

            // Process bulk action if any
            $this->process_bulk_action();

            // Will be used in pagination settings
            $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $count_table $count_where" );

            // Prepare query params, as usual current page, order by and order direction
            $paged   = isset( $_GET['paged'] ) ? $select_limit * ( intval( $_GET['paged'] ) - 1 ) : 0;
            $orderby = ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_GET['orderby'] : $this->options['select_order'];

            $order_dir = ( $this->options['select_order_dir'] != '' ) ? $this->options['select_order_dir'] : 'asc';
            $order     = ( isset( $_GET['order'] ) && in_array( $_GET['order'], array( 'asc', 'desc' ) ) ) ? $_GET['order'] : $order_dir;

            $sql = "
                    SELECT $select_columns
                    FROM $select_table
                    $select_where
                    $select_group
                    ORDER BY $orderby $order
                    LIMIT $select_limit
                    OFFSET $paged
                    ";

            $this->items = $wpdb->get_results( $sql, ARRAY_A );

            $this->set_pagination_args( array(
                                            'total_items' => $total_items,
                                            'per_page'    => $select_limit,
                                            'total_pages' => ceil( $total_items / $select_limit )
                                        ) );
        }

        /**
         * Generates the columns for a single row of the table; overrides original class function
         *
         * @since   1.0.0
         *
         * @param   $item array the row
         *
         * @return  string
         * @author  Alberto Ruggiero
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
                }
                elseif ( method_exists( $this, 'column_' . $column_name ) ) {
                    echo "<td $attributes>";
                    echo call_user_func( array( $this, 'column_' . $column_name ), $item );
                    echo "</td>";
                }
                elseif ( isset( $this->options['custom_columns']['column_' . $column_name] ) ) {
                    echo "<td $attributes>";
                    echo call_user_func_array( $this->options['custom_columns']['column_' . $column_name], array( $item, $this ) );
                    echo "</td>";
                }
                else {
                    echo "<td $attributes>";
                    echo $this->column_default( $item, $column_name );
                    echo "</td>";
                }
            }
        }

    }
}