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
 * Shows a custom table
 *
 * @class   YITH_WCMAS_Table_Template
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 * @extends WP_List_Table
 *
 */
class YITH_WCMAS_Table_Template extends WP_List_Table {

    /**
     * @var array $options array of options for table showing
     */
    var $options;

    /**
     * Constructor
     *
     * @param   $args array|string array or string of arguments
     * @since   1.0.0
     * @author  Francesco Licandro <francesco.licandro@yithemes.com>
     * @see     WP_List_Table
     */
    function __construct( $args ) {
        parent::__construct( $args );
    }

    /**
     * Default column renderer
     *
     * @param   $item array the row
     * @param   $column_name string the column name
     * @since   1.0.0
     * @author  Francesco Licandro <francesco.licandro@yithems.com>
     * @return  string
     */
    function column_default( $item, $column_name ) {
        return $item[ $column_name ];
    }

    /**
     * Checkbox column renderer
     *
     * @param   $item array the row
     * @since   1.0.0
     * @author  Francesco Licandro <francesco.licandro@yithemes.com>
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
     * @author  Francesco Licandro <francesco.licandro@yithemes.com>
     * @return  array
     */
    function get_bulk_actions() {
        return $this->options['bulk_actions']['actions'] ;
    }

    /**
     * It will get rows from database and prepare them to be showed in table
     *
     * @since   1.0.0
     * @author  Francesco Licandro <francesco.licandro@yithemes.com>
     * @return  void
     */
    function prepare_items() {
        global $wpdb;

        $select_table   = $this->options['select_table'];
        $select_columns = implode( ',', $this->options['select_columns'] );
        $select_where   = isset( $this->options['select_where'] ) ? 'WHERE ' . $this->options['select_where'] : '' ;
        $select_group   = isset( $this->options['select_group'] ) ? 'GROUP BY ' . $this->options['select_group'] : '' ;
        $select_limit   = $this->options['select_limit'];

        $count_table    = ( isset( $this->options['count_table'] ) ) ? $this->options['count_table'] : false;
        $count_where    = ( isset( $this->options['count_where'] ) ) ? 'WHERE ' . $this->options['count_where'] : '' ;

        $view_columns       = $this->get_columns();
        $hidden_columns     = $this->options['hidden_columns'];
        $sortable_columns   = $this->options['sortable_columns'];

	    $unserialize = isset( $this->options['unserialized'] ) ? $this->options['unserialized'] : false;

        // Here we configure table headers, defined in our methods
        $this->_column_headers = array( $view_columns, $hidden_columns, $sortable_columns );

        // Will be used in pagination settings
	    $total_items = false;
        if( $count_table ) {
	        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $count_table $count_where" );
        }

        // Prepare query params, as usual current page, order by and order direction
	    $select_order = isset( $this->options['select_order'] ) ? $this->options['select_order'] : false;
        $paged      = isset( $_GET['paged'] ) ? $select_limit * ( intval( $_GET['paged'] ) - 1 ) : 0;
        $orderby    = ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_GET['orderby'] : $select_order;
        $order      = ( isset( $_GET['order'] ) && in_array( $_GET['order'], array( 'asc', 'desc' ) ) ) ? $_GET['order'] : 'asc';

	    $query = 'SELECT ' . $select_columns . ' FROM ' . $select_table . ' ' . $select_where . ' ' . $select_group;

	    if( $orderby ) {
		    $query .= ' ORDER BY ' . $orderby . ' ' . $order;
	    }
	    // add param if array is unserialized
	    if( ! $unserialize ) {
		    $query .= ' LIMIT ' . $select_limit . ' OFFSET ' . $paged;
	    }

        $this->items = $wpdb->get_results( $query, ARRAY_A );

	    if( $unserialize ) {

		    $unserialized = array();

		    foreach( $this->items as $value ) {
			    foreach( $value as $key => $s ) {
				    $s = maybe_unserialize( $s );
				    // recalculate total items
				    $total_items = count( $s );
				    // slice array for pagination
				    $s = array_slice( $s, $paged, $select_limit );
				    foreach( $s as $email ) {
					    $unserialized[] = array( $key => $email );
				    }
			    }
		    }

		    $this->items = ! empty( $unserialized ) ? $unserialized : false;

	    }

	    // set pagination
        if( $total_items ) {
	        $this->set_pagination_args( array(
		        'total_items'   => $total_items,
		        'per_page'      => $select_limit,
		        'total_pages'   => ceil( $total_items / $select_limit )
	        ));
        }

    }

    /**
     * Generates the columns for a single row of the table; overrides original class function
     *
     * @author  Francesco Licandro <francesco.licandro@yithemes.com>
     * @since   1.0.0
     * @param   $item array the row
     * @see     WP_List_Table
     */
    protected function single_row_columns( $item ) {
	    $product = ( isset( $item['post_id'] ) ) ? wc_get_product( $item['post_id'] ) : false;
	    $category = ( isset( $item['term_id'] ) ) ? get_term_by( 'id', $item['term_id'], 'product_cat' ) : false;

	    if ( $product || $category ) {
		    list( $columns, $hidden ) = $this->get_column_info();

		    foreach ( $columns as $column_name => $column_display_name ) {
			    $class = "class='$column_name column-$column_name'";

			    $style = '';
			    if ( in_array( $column_name, $hidden ) )
				    $style = ' style="display:none;"';

			    $attributes = "$class$style";

			    // get column
			    if ( 'cb' == $column_name ) {
				    echo '<th scope="row" class="check-column">';
				    echo $this->column_cb( $item );
				    echo '</th>';
			    } elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				    echo "<td $attributes>";
				    echo call_user_func( array( $this, 'column_' . $column_name ), $item );
				    echo "</td>";
			    } elseif ( isset( $this->options['custom_columns']['column_' . $column_name] ) ) {
				    echo "<td $attributes>";
				    if ( $product )
				    	echo call_user_func_array( $this->options['custom_columns']['column_' . $column_name] , array( $item, $this, $product ) );
				    if ( $category )
					    echo call_user_func_array( $this->options['custom_columns']['column_' . $column_name] , array( $item, $this, $category ) );
				    echo "</td>";
			    } else {
				    echo "<td $attributes>";
				    echo $this->column_default( $item, $column_name );
				    echo "</td>";
			    }
		    }
	    }
    }

    /**
     * Get a list of columns. The format is:
     * 'internal-name' => 'Title'
     *
     * @since 3.1.0
     * @access public
     * @abstract
     *
     * @return array
     */
    public function get_columns() {
	    return $this->options['view_columns'];
    }

}