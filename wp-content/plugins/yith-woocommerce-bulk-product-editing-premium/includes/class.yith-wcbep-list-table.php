<?php
if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( !class_exists( 'YITH_WCBEP_List_Table' ) ) {
    /**
     * List table class
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBEP_List_Table extends WP_List_Table {

        public $columns;
        public $hidden;
        public $sortable;

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct( $columns = array(), $hidden = array(), $sortable = array() ) {
            global $status, $page;

            $this->columns  = $columns;
            $this->hidden   = $hidden;
            $this->sortable = $sortable;

            //Set parent defaults
            parent::__construct(
                array(
                    'singular' => 'yith_wcbep_product',
                    'plural'   => 'yith_wcbep_products',
                    'ajax'     => true,
                    'screen'   => 'yith-wcbep-product-list'
                )
            );
        }

        public function get_columns() {
            $default = array(
                'cb'            => '<input type="checkbox">',
                'show'          => '<span class="dashicons dashicons-admin-generic"></span>',
                'ID'            => __( 'ID', 'yith-woocommerce-bulk-product-editing' ),
                'title'         => __( 'Title', 'yith-woocommerce-bulk-product-editing' ),
                'regular_price' => __( 'Regular Price', 'yith-woocommerce-bulk-product-editing' ),
                'sale_price'    => __( 'Sale Price', 'yith-woocommerce-bulk-product-editing' ),
                'categories'    => __( 'Categories', 'yith-woocommerce-bulk-product-editing' ),
                'date'          => __( 'Date', 'yith-woocommerce-bulk-product-editing' ),
            );

            return !empty( $this->columns ) ? $this->columns : $default;
        }

        public function get_sortable() {
            $default = array(
                'title'         => array( 'title', false ),
                'regular_price' => array( 'regular_price', false ),
                'sale_price'    => array( 'sale_price', false ),
                'date'          => array( 'date', false ),
            );

            return !empty( $this->sortable ) ? $this->sortable : $default;
        }

        public function get_hidden() {
            $default = array( 'ID' );

            return !empty( $this->hidden ) ? $this->hidden : $default;
        }

        public function prepare_items( $items = array() ) {
            $current_page = $this->get_pagenum();
            $per_page     = !empty( $_REQUEST[ 'f_per_page' ] ) && intval( $_REQUEST[ 'f_per_page' ] ) > 0 ? intval( $_REQUEST[ 'f_per_page' ] ) : 10;

            $columns  = $this->get_columns();
            $hidden   = $this->get_hidden();
            $sortable = $this->get_sortable();

            $this->_column_headers = array( $columns, $hidden, $sortable );

            /* ========================================= F I L T E R S ================================================ */
            $filtered_categories = !empty( $_REQUEST[ 'f_categories' ] ) ? $_REQUEST[ 'f_categories' ] : array();
            $f_regular_price_sel = !empty( $_REQUEST[ 'f_reg_price_select' ] ) ? $_REQUEST[ 'f_reg_price_select' ] : 'mag';
            $f_regular_price_val = isset( $_REQUEST[ 'f_reg_price_value' ] ) ? $_REQUEST[ 'f_reg_price_value' ] : null;
            $f_sale_price_sel    = !empty( $_REQUEST[ 'f_sale_price_select' ] ) ? $_REQUEST[ 'f_sale_price_select' ] : 'mag';
            $f_sale_price_val    = isset( $_REQUEST[ 'f_sale_price_value' ] ) ? $_REQUEST[ 'f_sale_price_value' ] : null;
            /* =================================== E N D   F I L T E R S ============================================== */

            $posts_not_in = array();
            if ( $variable_term = get_term_by( 'slug', 'variable', 'product_type' ) )
                $posts_not_in = array_unique( (array)get_objects_in_term( $variable_term->term_id, 'product_type' ) );

            $post_types = 'product';
            $order_by   = !empty( $_REQUEST[ 'orderby' ] ) ? $_REQUEST[ 'orderby' ] : 'ID';

            $query_args = array(
                'post_type'           => $post_types,
                'post_status'         => 'any',
                'posts_per_page'      => $per_page,
                'ignore_sticky_posts' => true,
                'paged'               => $current_page,
                'orderby'             => $order_by,
                'order'               => !empty( $_REQUEST[ 'order' ] ) ? $_REQUEST[ 'order' ] : 'DESC',
                'post__not_in'        => $posts_not_in
            );

            switch ( $order_by ) {
                case 'regular_price':
                    $query_args[ 'orderby' ]  = 'meta_value_num';
                    $query_args[ 'meta_key' ] = '_regular_price';
                    break;
                case 'sale_price':
                    $query_args[ 'orderby' ]  = 'meta_value_num';
                    $query_args[ 'meta_key' ] = '_sale_price';
                    break;
            }
            $meta_query = array();


            // Filter Regular Price
            if ( isset( $f_regular_price_val ) && is_numeric( $f_regular_price_val ) ) {
                $compare = '>';
                $value   = $f_regular_price_val;
                switch ( $f_regular_price_sel ) {
                    case 'mag':
                        $compare = '>';
                        break;
                    case 'min':
                        $compare = '<';
                        break;
                    case 'ug':
                        $compare = '=';
                        break;
                    case 'magug':
                        $compare = '>=';
                        break;
                    case 'minug':
                        $compare = '<=';
                        break;
                }
                $meta_query[] = array(
                    'key'     => '_regular_price',
                    'type'    => 'NUMERIC',
                    'value'   => $value,
                    'compare' => $compare,
                );
            }

            // Filter Sale Price
            if ( isset( $f_sale_price_val ) && is_numeric( $f_sale_price_val ) ) {
                $compare = '>';
                $value   = $f_sale_price_val;
                switch ( $f_sale_price_sel ) {
                    case 'mag':
                        $compare = '>';
                        break;
                    case 'min':
                        $compare = '<';
                        break;
                    case 'ug':
                        $compare = '=';
                        break;
                    case 'magug':
                        $compare = '>=';
                        break;
                    case 'minug':
                        $compare = '<=';
                        break;
                }
                $meta_query[] = array(
                    'key'     => '_sale_price',
                    'type'    => 'NUMERIC',
                    'value'   => $value,
                    'compare' => $compare,
                );
            }

            // Filter Categories
            if ( !empty( $filtered_categories ) ) {
                $query_args[ 'tax_query' ][ 'relation' ] = 'AND';
                $query_args[ 'tax_query' ][]             = array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $filtered_categories,
                    'operator' => 'IN',
                );
            }

            if ( !empty( $meta_query ) ) {
                $query_args[ 'meta_query' ]               = $meta_query;
                $query_args[ 'meta_query' ][ 'relation' ] = 'AND';
            }

            $query_args = apply_filters( 'yith_wcbep_product_list_query_args', $query_args );

            $p_query = new WP_Query( $query_args );

            $this->items = $p_query->posts;

            $this->set_pagination_args(
                array(
                    'total_items' => $p_query->found_posts,
                    'per_page'    => $per_page,
                    'total_pages' => $p_query->max_num_pages,
                    'orderby'     => !empty( $_REQUEST[ 'orderby' ] ) && '' != $_REQUEST[ 'orderby' ] ? $_REQUEST[ 'orderby' ] : 'ID',
                    'order'       => !empty( $_REQUEST[ 'order' ] ) && '' != $_REQUEST[ 'order' ] ? $_REQUEST[ 'order' ] : 'DESC'
                )
            );
        }

        function column_default( $item, $column_name ) {
            $r          = '';
            $product_id = $item->ID;
            $product    = wc_get_product( $product_id );
            $edit_link  = get_edit_post_link( yit_get_base_product_id( $product ) );

            switch ( $column_name ) {
                case 'ID':
                    $r = $product_id;
                    break;
                case 'show':
                    $r = '<a href="' . $edit_link . '" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>';
                    break;
                case 'title':
                    $r = $item->post_title;
                    break;
                case 'regular_price':
                    $r = yit_get_prop($product, '_regular_price', true, 'edit' );
                    break;
                case 'sale_price':
                    $r = yit_get_prop($product, '_sale_price', true, 'edit' );
                    break;
                case 'categories':
                    // CATEGORIES
                    $cats      = get_the_terms( $product_id, 'product_cat' );
                    $cats      = !empty( $cats ) ? $cats : array();
                    $cats_html = '';
                    $loop      = 0;
                    foreach ( $cats as $c ) {
                        $loop++;
                        $cats_html .= $c->name;
                        if ( $loop < count( $cats ) ) {
                            $cats_html .= ', ';
                        }
                    }

                    $r = $cats_html;
                    break;
                case 'date':
                    $r = date_i18n( 'Y/m/d', strtotime( $item->post_date ) );
                    break;
            }

            $r = apply_filters( 'yith_wcbep_manage_custom_columns', $r, $column_name, $item );

            return $r;

        }

        function column_cb( $item ) {
            return sprintf( '<input type="checkbox" value="%s" />', $item->ID );
        }

        public function print_column_headers( $with_id = true ) {
            list( $columns, $hidden, $sortable ) = $this->get_column_info();

            $current_url = set_url_scheme( admin_url() . '?page=yith_wcbep_panel' );
            $current_url = remove_query_arg( 'paged', $current_url );

            if ( isset( $_GET[ 'orderby' ] ) )
                $current_orderby = $_GET[ 'orderby' ];
            else
                $current_orderby = '';

            if ( isset( $_GET[ 'order' ] ) && 'desc' == $_GET[ 'order' ] )
                $current_order = 'desc';
            else
                $current_order = 'asc';

            if ( !empty( $columns[ 'cb' ] ) ) {
                static $cb_counter = 1;
                $columns[ 'cb' ] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
                                   . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
                $cb_counter++;
            }

            foreach ( $columns as $column_key => $column_display_name ) {
                $class = array( 'manage-column', "column-$column_key" );

                $style = '';
                if ( in_array( $column_key, $hidden ) )
                    $style = 'display:none;';

                $style = ' style="' . $style . '"';

                if ( 'cb' == $column_key )
                    $class[] = 'check-column';
                elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
                    $class[] = 'num';

                if ( isset( $sortable[ $column_key ] ) ) {
                    list( $orderby, $desc_first ) = $sortable[ $column_key ];

                    if ( $current_orderby == $orderby ) {
                        $order   = 'asc' == $current_order ? 'desc' : 'asc';
                        $class[] = 'sorted';
                        $class[] = $current_order;
                    } else {
                        $order   = $desc_first ? 'desc' : 'asc';
                        $class[] = 'sortable';
                        $class[] = $desc_first ? 'asc' : 'desc';
                    }

                    $column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
                }

                $id = $with_id ? "id='$column_key'" : '';

                if ( !empty( $class ) )
                    $class = "class='" . join( ' ', $class ) . "'";

                echo "<th scope='col' $id $class $style>$column_display_name</th>";
            }
        }

        public function display() {

            wp_nonce_field( 'ajax-yith-wcbep-list-nonce', '_ajax_yith_wcbep_list_nonce' );

            echo '<input id="order" type="hidden" name="order" value="' . $this->_pagination_args[ 'order' ] . '" />';
            echo '<input id="orderby" type="hidden" name="orderby" value="' . $this->_pagination_args[ 'orderby' ] . '" />';

            parent::display();
        }

        function ajax_response() {

            check_ajax_referer( 'ajax-yith-wcbep-list-nonce', '_ajax_yith_wcbep_list_nonce' );

            $this->prepare_items();

            extract( $this->_args );
            extract( $this->_pagination_args, EXTR_SKIP );

            ob_start();
            if ( !empty( $_REQUEST[ 'no_placeholder' ] ) )
                $this->display_rows();
            else
                $this->display_rows_or_placeholder();
            $rows = ob_get_clean();

            ob_start();
            $this->print_column_headers();
            $headers = ob_get_clean();

            ob_start();
            $this->pagination( 'top' );
            $pagination_top = ob_get_clean();

            ob_start();
            $this->pagination( 'bottom' );
            $pagination_bottom = ob_get_clean();

            $response                             = array( 'rows' => $rows );
            $response[ 'pagination' ][ 'top' ]    = $pagination_top;
            $response[ 'pagination' ][ 'bottom' ] = $pagination_bottom;
            $response[ 'column_headers' ]         = $headers;

            if ( isset( $total_items ) )
                $response[ 'total_items_i18n' ] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

            if ( isset( $total_pages ) ) {
                $response[ 'total_pages' ]      = $total_pages;
                $response[ 'total_pages_i18n' ] = number_format_i18n( $total_pages );
            }

            die( json_encode( $response ) );
        }
    }
}
?>