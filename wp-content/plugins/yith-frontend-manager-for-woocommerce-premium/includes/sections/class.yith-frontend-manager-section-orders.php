<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Frontend_Manager_Section_Orders' ) ) {

	class YITH_Frontend_Manager_Section_Orders extends YITH_WCFM_Section {

        /**
         * @var arrayPagination argument
         */
	    public $pagination_args = array();

		/**
		 * Constructor method
		 *
		 * @return \YITH_Frontend_Manager_Section
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id = 'product_orders';
			$this->_default_section_name = _x( 'Orders', '[Frontend]: Orders menu item', 'yith-frontend-manager-for-woocommerce' );

			$this->_subsections = apply_filters( 'yith_wcfm_orders_subsections', array(
                    'product_orders' => array(
                        'slug' => $this->get_option( 'slug', $this->id . '_product_orders', 'product_orders' ),
                        'name' => __( 'All Orders', 'yith-frontend-manager-for-woocommerce' )
                    ),

                    'product_order' => array(
                        'slug' => $this->get_option( 'slug', $this->id . '_product_order', 'product_order' ),
                        'name' => __( 'Add Order', 'yith-frontend-manager-for-woocommerce' )
                    ),
                )
			);

			add_action( 'yith_wcfm_order_cols', array( $this, 'orders_list_columns' ), 10, 2 );

			//Get product url from line item_id
			add_action( 'wp_ajax_yith_wcfm_get_product_url_from_line_item', 'YITH_Frontend_Manager_Section_Orders::get_product_uri_from_line_item_id', 10, 2 );

            if ( isset( $_GET['trashed'] ) && $_GET['trashed'] == 1 ) {
                $redirect_url = add_query_arg( array( 'product_orders' => 'product_orders', 'trashed' => 'ok', ), get_permalink() );
                wp_redirect( $redirect_url ); exit;
            }

			add_filter( 'wc_order_statuses', 'yith_wcfm_add_auto_draft_status_to_allowed_post_status' );

			/*
			 *  Construct
			 */
			parent::__construct();
		}

		/* === SECTION METHODS === */

        /**
         * Get product uri from line item id
         *
         * @author YITH <plugins@yithemes.com>
         * @sicne 1.0
         * @return void
         */
		public static function get_product_uri_from_line_item_id(){
            $url = false;

            if( ! empty( $_POST['order_id'] ) && ! empty( $_POST['line_item_id'] ) ){
                $order_id       = $_POST['order_id'];
                $line_item_id   = $_POST['line_item_id'];

                $order = wc_get_order( $order_id );

                if( $order instanceof WC_Order ){
                    /**
                     * @var $item WC_Order_Item_Product
                     */
                    $item       = $order->get_item( $line_item_id );
                    $product    = $item->get_product();

                    if( $product instanceof WC_Product){
                        $url = YITH_Frontend_Manager_Section_Products::get_edit_product_link( $product->get_id() );
                    }
                }
            }

            wp_send_json( $url, 200 );
        }

		/**
		 * Print shortcode function
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function print_shortcode( $atts = array(), $content = '', $tag = '' ) {
			$section = $this->id;
			$subsection_prefix = $this->get_shortcodes_prefix() . $section;
			$subsection = $tag != $subsection_prefix ? str_replace( $subsection_prefix . '_', '', $tag ) : $section;

			$default_per_page = 20;
			$post_type = 'shop_order';
			$option = 'edit_' . $post_type . '_per_page';

            $per_page = (int) get_user_option( $option );
            if ( empty( $per_page ) || $per_page < 1 ){
                $per_page = $default_per_page;
            }


            /**
             * Filters the number of items to be displayed on each page of the list table.
             *
             * The dynamic hook name, $option, refers to the `per_page` option depending
             * on the type of list table in use. Possible values include: 'edit_comments_per_page',
             * 'sites_network_per_page', 'site_themes_network_per_page', 'themes_network_per_page',
             * 'users_network_per_page', 'edit_post_per_page', 'edit_page_per_page',
             * 'edit_{$post_type}_per_page', etc.
             *
             * @since WordPress 2.9.0
             *
             * @param int $per_page Number of items to be displayed. Default 20.
             */
            $per_page = (int) apply_filters( $option, $per_page );

            $page = (int) ! empty( $_GET['pages'] ) ? $_GET['pages'] : 1;

            $query_args = array(
                'posts_per_page'    => $per_page,
                'offset'            => ( $page - 1 ) * $per_page,
                'post_type'         => 'shop_order',
            );

            $count_shop_orders = wp_count_posts( 'shop_order' );
            $shop_order_stati = wc_get_order_statuses();

            $this->pagination_args['total_items'] = $this->pagination_args['total_pages'] = 0;

            foreach( $shop_order_stati as $status_id => $status_label ){
                if( isset( $count_shop_orders->$status_id ) ){
                    $this->pagination_args['total_items'] += (int) $count_shop_orders->$status_id;
                }
            }

            $this->pagination_args['total_pages'] = ceil( $this->pagination_args['total_items'] / $per_page );

			$atts = apply_filters( 'yith_wcfm_print_shortcode_template_args', array(
			    'columns' => array(
                    'status' => __('Status', 'yith-frontend-manager-for-woocommerce'),
                    'order' => __('Order', 'yith-frontend-manager-for-woocommerce'),
                    'purchased' => __('Purchased', 'yith-frontend-manager-for-woocommerce'),
                    'ship_to' => __('Ship to', 'yith-frontend-manager-for-woocommerce'),
                    'date' => __('Date', 'yith-frontend-manager-for-woocommerce'),
                    'total' => __('Total', 'yith-frontend-manager-for-woocommerce'),
                    'actions' => __('Actions', 'yith-frontend-manager-for-woocommerce'),
                ),

                'query_args' => $query_args,

                'cols_class' => array(
                    'actions' => 'order_actions'
                ),

                'pagination_args' => $this->pagination_args,

                'section_obj'     => $this,

            ), $subsection, $section );

			if( ! empty( $_GET['order_status'] ) ){
				$atts['query_args']['post_status'] = $_GET['order_status'];
			}

			$atts['orders'] = array();

			if( ! empty( $_GET['search'] ) ){
				$atts['orders'] = wc_order_search( sanitize_text_field( $_GET['search'] ) );
			}

			$atts['orders']                 = apply_filters( 'yith_wcmv_print_orders_list_shortcode', $atts['orders'], $atts['query_args'] );
			$atts['query_args']['post__in'] = ! empty( $atts['query_args']['post__in'] ) ? $atts['query_args']['post__in'] : $atts['orders'];
			$atts['orders']                 = wc_get_orders( $atts['query_args'] );
            if( apply_filters( 'yith_wcfm_print_orders_section', true, $subsection, $section, $atts ) ){
                $this->print_section( $subsection, $section, $atts );
            }

            else {
                do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
            }
		}

		/**
		 * WP Enqueue Scripts
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_section_scripts() {

			// CSS
			wp_enqueue_style( 'yith-wcfm-product_orders', YITH_WCFM_URL . 'assets/css/orders.css', array(), YITH_WCFM_VERSION );

			// CSS WooCommerce
			$screen     = get_current_screen();
			$screen_id  = $screen ? $screen->id : '';
            $suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ! empty( $_GET['yith_debug'] ) ? '' : '.min';

			// Sitewide menu CSS
			wp_enqueue_style( 'woocommerce_admin_menu_styles' );
            wp_enqueue_style( 'woocommerce_admin_styles' );
            wp_enqueue_style( 'jquery-ui-style' );
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'wp-admin' );


			if ( in_array( $screen_id, array( 'dashboard' ) ) ) {
				wp_enqueue_style( 'woocommerce_admin_dashboard_styles' );
			}

			if ( in_array( $screen_id, array( 'woocommerce_page_wc-reports', 'toplevel_page_wc-reports' ) ) ) {
				wp_enqueue_style( 'woocommerce_admin_print_reports_styles' );
			}

			if( function_exists( 'TM_EPO_ADMIN' ) ){
				$deps = array(
					'yith-wcfm-product_orders',
					'woocommerce_admin_menu_styles',
					'woocommerce_admin_styles',
				);
				wp_enqueue_style( 'yith-wcfm-tm-extra-product-options', YITH_WCFM_URL . 'assets/third-party/woocommerce-tx-extra-product-options/tm_extra_product_options.css', $deps, YITH_WCFM_VERSION );
			}

			/**
			 * @deprecated 2.3
			 */
			if ( has_action( 'woocommerce_admin_css' ) ) {
				do_action( 'woocommerce_admin_css' );
				_deprecated_function( 'The woocommerce_admin_css action', '2.3', 'admin_enqueue_scripts' );
			}

			// JS
			wp_enqueue_script( 'wp-post' );
			wp_enqueue_script( 'wp-postbox' );
			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_script( 'wc-admin-order-meta-boxes' );

            wp_enqueue_script( 'yith-frontend-manager-order-js', YITH_WCFM_URL . "assets/js/yith-frontend-manager-order{$suffix}.js", array( 'jquery' ), YITH_WCFM_VERSION, true );

            wp_localize_script( 'yith-frontend-manager-order-js', 'yith_wcfm_orders', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

			do_action( 'yith_wcfm_orders_enqueue_scripts' );

		}

        /**
         * Print columns
         *
         * @param $column
         * @param $order
         */
		public function orders_list_columns( $column, $order ){
		    /** @var WC_Order $order */
		    $order_id = $order->get_id();
            $order_edit_url = YITH_Frontend_Manager_Section_Orders::get_edit_order_permalink( $order_id );
            switch( $column ){
                case 'status':
                    $order_status_name = wc_get_order_status_name ( $order->get_status () );
                    printf('<mark class="order-status tips %s" data-tip="%s">%s</mark>', sanitize_title( $order->get_status() ), $order_status_name, $order_status_name );
                    break;

                case 'order':
                    printf( '<a href="%s">#%s</a>', $order_edit_url, $order->get_order_number() );
                    break;

                case 'purchased':
                    $items_qty = 0;
                    $items_list = '';
                    foreach ( $order->get_items() as $line ) {
                        $items_qty += $line['qty'];
                        $items_list .= $line['qty'] . ' x <a href="#">' . $line['name'] . '</a><br />';
                    }
                    echo $items_qty . ' ' . ( $items_qty == 1 ? __('item', 'yith-frontend-manager-for-woocommerce') : __('items', 'yith-frontend-manager-for-woocommerce') );
                    echo '<div class="items_list" style="margin-top: 10px; display: none;">' . $items_list . '</div>';
                    break;

                case 'ship_to':
	                $address = ! empty( $order->get_formatted_shipping_address() ) ? $order->get_formatted_shipping_address() : $order->get_formatted_billing_address();

                    if ( ! empty( $address ) ) {
	                    echo '<a target="_blank" href="' . esc_url( $order->get_shipping_address_map_url() ) . '">' . esc_html( preg_replace( '#<br\s*/?>#i', ', ', $address ) ) . '</a>';
                    }
                    else {
                        echo '&ndash;';
                    }
                    break;

                case 'date':
                    echo get_the_date( apply_filters( 'yith_wcfm_order_date_format', '' ), $order_id );
                    break;

                case 'total':
                    echo $order->get_formatted_order_total();
                    break;

                case 'actions':
                    global $post;
                    do_action( 'woocommerce_admin_order_actions_start', $order );

                    $actions = array();

                    if ( $order->has_status( array( 'pending', 'on-hold' ) ) ) {
                        $actions['processing'] = array(
                            'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=processing&fired_via=yith_wcfm&order_id=' . $order_id . '&referer=' . $this->get_url() ), 'woocommerce-mark-order-status' ),
                            'name'      => __( 'Processing', 'woocommerce' ),
                            'action'    => "processing"
                        );
                    }

                    if ( $order->has_status( array( 'pending', 'on-hold', 'processing' ) ) ) {
                        $actions['complete'] = array(
                            'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&fired_via=yith_wcfm&order_id=' . $order_id . '&referer=' . $this->get_url() ), 'woocommerce-mark-order-status' ),
                            'name'      => __( 'Complete', 'woocommerce' ),
                            'action'    => "complete"
                        );
                    }

                    $actions['view'] = array(
                        'url'       => $order_edit_url,
                        'name'      => __( 'View', 'woocommerce' ),
                        'action'    => "view"
                    );

                    $actions = apply_filters( 'woocommerce_admin_order_actions', $actions, $order );

                    foreach ( $actions as $action ) {
                        printf( '<a class="button tips %s" href="%s" data-tip="%s">%s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
                    }

                    do_action( 'woocommerce_admin_order_actions_end', $order );
                    break;

                default:
                    $current_action = current_action();
                    do_action( "{$current_action}_{$column}", $column, $order );
            }
        }

        /**
         * Get frontend edit order link
         */
        public static function get_edit_order_permalink( $order_id ){
            return add_query_arg( array( 'id' => $order_id, ), yith_wcfm_get_section_url( 'product_orders', 'product_order' ) );
        }

        /**
         * Get the current page number
         *
         * @since 3.1.0
         * @access public
         *
         * @return int
         */
        public function get_pagenum() {
            $pagenum = isset( $_REQUEST['pages'] ) ? absint( $_REQUEST['pages'] ) : 0;

            if ( isset( $this->pagination_args['total_pages'] ) && $pagenum > $this->pagination_args['total_pages'] ) {
                $pagenum = $this->pagination_args['total_pages'];
            }

            return max( 1, $pagenum );
        }

        /**
         * Print pagination
         *
         * @param $which
         */
        public function pagination( $which ) {
            if ( empty( $this->pagination_args ) ) {
                return;
            }

            $total_items = $this->pagination_args['total_items'];
            $total_pages = $this->pagination_args['total_pages'];
            $infinite_scroll = false;

            if ( isset( $this->pagination_args['infinite_scroll'] ) ) {
                $infinite_scroll = $this->pagination_args['infinite_scroll'];
            }

            if ( ! empty( $this->screen ) && 'top' === $which && $total_pages > 1 ) {
                $this->screen->render_screen_reader_content( 'heading_pagination' );
            }

            $output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

            $current = $this->get_pagenum();
            $removable_query_args = wp_removable_query_args();

            $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

            $current_url = remove_query_arg( $removable_query_args, $current_url );

            $page_links = array();

            $total_pages_before = '<span class="paging-input">';
            $total_pages_after  = '</span></span>';

            $disable_first = $disable_last = $disable_prev = $disable_next = false;

            if ( $current == 1 ) {
                $disable_first = true;
                $disable_prev = true;
            }
            if ( $current == 2 ) {
                $disable_first = true;
            }
            if ( $current == $total_pages ) {
                $disable_last = true;
                $disable_next = true;
            }
            if ( $current == $total_pages - 1 ) {
                $disable_last = true;
            }

            if ( $disable_first ) {
                $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
            } else {
                $page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                    esc_url( remove_query_arg( 'pages', $current_url ) ),
                    __( 'First page' ),
                    '&laquo;'
                );
            }

            if ( $disable_prev ) {
                $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
            } else {
                $page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                    esc_url( add_query_arg( 'pages', max( 1, $current-1 ), $current_url ) ),
                    __( 'Previous page' ),
                    '&lsaquo;'
                );
            }

            if ( 'bottom' === $which ) {
                $html_current_page  = $current;
                $total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
            } else {
                $html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='pages' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                    '<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
                    $current,
                    strlen( $total_pages )
                );
            }
            $html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
            $page_links[] = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

            if ( $disable_next ) {
                $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
            } else {
                $page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                    esc_url( add_query_arg( 'pages', min( $total_pages, $current+1 ), $current_url ) ),
                    __( 'Next page' ),
                    '&rsaquo;'
                );
            }

            if ( $disable_last ) {
                $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
            } else {
                $page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                    esc_url( add_query_arg( 'pages', $total_pages, $current_url ) ),
                    __( 'Last page' ),
                    '&raquo;'
                );
            }

            $pagination_links_class = 'pagination-links';
            if ( ! empty( $infinite_scroll ) ) {
                $pagination_links_class = ' hide-if-js';
            }
            $output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

            if ( $total_pages ) {
                $page_class = $total_pages < 2 ? ' one-page' : '';
            } else {
                $page_class = ' no-pages';
            }

            $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

            echo $this->_pagination;
        }
	}
}
