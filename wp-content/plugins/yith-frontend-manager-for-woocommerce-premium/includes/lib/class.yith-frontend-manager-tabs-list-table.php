<?php
/**
 * List Table API: YITH_Products_List_Table class
 *
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */

if ( ! class_exists( 'YITH_Tabs_List_Table' ) ) {

	/**
	 * Core class used to implement displaying posts in a list table.
	 *
	 * @since  3.1.0
	 * @access private
	 *
	 * @see    WP_List_Table
	 * @see    WP_Posts_List_Table
	 */
	class YITH_Tabs_List_Table extends WP_Posts_List_Table {
		/**
		 * @var int Total items
		 */
		public $total_items = 0;

		/**
		 * @var YITH_Frontend_Manager_Section Product section object
		 */
		public $section_obj = null;

		/**
		 * @var string section uri
		 */
		public $section_uri = null;

		/**
		 * Constructor.
		 *
		 * @since 3.1.0
		 * @access public
		 *
		 * @see WP_List_Table::__construct() for more information on default arguments.
		 *
		 * @global WP_Post_Type $post_type_object
		 * @global wpdb         $wpdb
		 *
		 * @param array $args An associative array of arguments.
		 */
		public function __construct( $args = array() ) {
			$this->section_obj = $args['section_obj'];
			$this->section_uri = $this->section_obj->get_url();


			/* === Remove CB col === */
			add_filter( "manage_product_posts_columns", array( $this, 'manage_cols_on_front' ) );

			/* === Parent Construct === */
			parent::__construct( $args );
		}

		/**
		 * Prepare items
		 *
		 * @global int      $per_page
		 */
		public function prepare_items() {
			global $per_page;

			$tax_query = array();

			$this->set_hierarchical_display( is_post_type_hierarchical( $this->screen->post_type ) );

			$post_type = $this->screen->post_type;
			$per_page  = $this->get_items_per_page( 'edit_' . $post_type . '_per_page' );

			/** This filter is documented in wp-admin/includes/post.php */
			$per_page = apply_filters( 'edit_posts_per_page', $per_page, $post_type );

			$query_args = array(
				'post_type' => $post_type,
				'posts_per_page' => $per_page
			);

			if( ! empty( $_GET['pages'] ) ){
				$query_args['offset'] = ( $_GET['pages'] -1 ) * $per_page;
			}

			if( ! empty( $_GET['orderby'] ) ){
				$query_args['orderby'] = $_GET['orderby'];
			}

			if( ! empty( $_GET['order'] ) ){
				$query_args['order'] = $_GET['order'];
			}



			/**
			 * Allow 3rd party plugin to change WP_Query args
			 */
			$query_args = apply_filters( 'yith_wcfm_tabs_list_query_args', $query_args );
			
			$query_results = new WP_Query( $query_args );
			$this->items = $query_results->posts;
			$this->total_items = $query_results->found_posts;
			
			$this->is_trash = isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] === 'trash';

			$this->set_pagination_args( array(
				'total_items' => $this->total_items,
				'per_page'    => $per_page
			) );

			wp_reset_query();
		}

		/**
		 * check if there are post for current post_type
		 * 
		 * @return bool
		 */
		public function has_items() {
			return $this->total_items > 0 ? true : false;
		}

		/**
		 * Generate the table rows
		 *
		 * @since 3.1.0
		 * @access public
		 */
		public function display_rows( $posts = array(), $level = 0 ) {
			foreach ( $this->items as $item ){
				$this->single_row( $item );
			}
		}

		/* === Manage Columns === */



		/**
		 * Get a list of CSS classes for the WP_List_Table table tag.
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @return array List of CSS classes for the table tag.
		 */
		protected function get_table_classes() {
			return apply_filters( 'yith_wcfm_tab_table_classes', array( 'widefat', 'striped', $this->_args['plural'], 'yith-wcfm-products' ) );
		}

		/**
		 * Display the pagination.
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @param string $which
		 */
		protected function pagination( $which ) {
			if ( empty( $this->_pagination_args ) ) {
				return;
			}

			$total_items = $this->_pagination_args['total_items'];
			$total_pages = $this->_pagination_args['total_pages'];
			$infinite_scroll = false;
			if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
				$infinite_scroll = $this->_pagination_args['infinite_scroll'];
			}

			if ( 'top' === $which && $total_pages > 1 ) {
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

			if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] )
				$pagenum = $this->_pagination_args['total_pages'];

			return max( 1, $pagenum );
		}

		/**
		 *
		 * @return array
		 */
		protected function get_bulk_actions() {
			return array();
		}

		/**
		 * Manage cols on front
		 *
		 * @since 3.1.0
		 * @access public
		 *
		 * @param array $posts_columns
		 * @return array allowed columns
		 */
		public function manage_cols_on_front( $posts_columns ){
			if( isset( $posts_columns['cb'] ) ){
				unset( $posts_columns['cb'] );
			}
			return apply_filters( 'yith_wcfm_tabs_list_cols', $posts_columns );
		}

		/**
		 * get columns
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array
		 */
		public function get_columns() {
			$columns = array(
				'title' => __( 'Title', 'yith-woocommerce-tab-manager' ),
				'description' => __( 'Description', 'yith-woocommerce-tab-manager' ),
				'is_show' => __( 'Is Visible', 'yith-woocommerce-tab-manager' ),
				'tab_position' => __( 'Tab Position', 'yith-woocommerce-tab-manager' ),
				'tab_type' => __( 'Tab type', 'yith-woocommerce-tab-manager' ),
				'date' => __( 'Date', 'yith-woocommerce-tab-manager' ),
			);

			return $columns;
		}

		/**
		 * @param WP_Post $tab
		 */
		public function  column_title( $tab ) {

			$tab_title = $tab->post_title;
			$current_section_uri = yith_wcfm_get_section_url( 'current' );
			$tab_edit_link = YITH_Frontend_Manager_Section_Tab_Manager::get_edit_tab_link( $tab->ID );
			$tab_edit_html = sprintf( '<a href="%s" class="yith-wcfm-edit edit">%s</a> |', $tab_edit_link, __('Edit', 'yith-frontend-manager-for-woocommerce' ) );
			$tab_delete_link = add_query_arg( array('act' => 'delete', 'tab_id' => $tab->ID ), $current_section_uri );
			$tab_delete_html = sprintf( '<a href="%s" class="yith-wcfm-delete delete">%s</a>', $tab_delete_link, __('Delete', 'yith-frontend-manager-for-woocommerce' ) );
			$action_row = sprintf('<small class="act">%s %s</small>', $tab_edit_html, $tab_delete_html );

			printf('<strong><a href="%s" class="row-title">%s</a></strong>%s',$tab_edit_link, $tab_title, $action_row );
		}


		public function column_description( $tab ){

			$description = $tab->post_excerpt;

			if( empty( $description ) ) {
				$description = __( 'No description', 'yith-woocommerce-tab-manager' );
			}


			printf( '<div class="ywtm_description show_more" data-max_char="%s" data-more_text="%s" data-less_text="%s">%s</div>', 80, __( 'Show more', 'yith-woocommerce-tab-manager' ), __( 'Show less', 'yith-woocommerce-tab-manager' ), $description );

		}

		public function  column_is_show( $tab ){

			$show = get_post_meta( $tab->ID, '_ywtm_show_tab', true );

			if( $show ) {
				echo '<mark class="show tips" data-tip="yes">yes</mark>';
			}
			else {
				echo '<mark class="hide tips" data-tip="no">no</mark>';
			}
		}

		public function column_tab_position( $tab ){

			$tab_position = get_post_meta( $tab->ID, '_ywtm_order_tab', true );
			echo $tab_position;
		}

		public function column_tab_type( $tab ){

			$type = get_post_meta( $tab->ID, '_ywtm_tab_type', true );

			if( empty( $type ) || $type == 'global' ) {
				echo 'global';
			}

			else {
				echo $type;
			}

		}


		public function  handle_row_actions( $post, $column_name, $primary ){
			return '';
		}


	}
}