<?php
/**
 * List Table API: YITH_Products_List_Table class
 *
 * @author YITH <plugins@yithemes.com>
 */

if ( ! class_exists( 'YITH_Products_List_Table' ) ) {

	/**
	 * Core class used to implement displaying posts in a list table.
	 *
	 * @since  3.1.0
	 * @access private
	 *
	 * @see    WP_List_Table
	 * @see    WP_Posts_List_Table
	 */
	class YITH_Products_List_Table extends WP_Posts_List_Table {
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

			if( ! empty( $_GET['search'] ) ){
				$query_args['s'] = sanitize_text_field( $_GET['search'] );
			}

			if( ! empty( $_GET['pages'] ) ){
				$query_args['offset'] = ( $_GET['pages'] -1 ) * $per_page;
			}

			if( ! empty( $_GET['orderby'] ) ){
				$query_args['orderby'] = $_GET['orderby'];
			}

			if( ! empty( $_GET['order'] ) ){
				$query_args['order'] = $_GET['order'];
			}

			if( ! empty( $_GET['product_cat'] ) || ! empty( $_GET['product_tag'] ) ){
				$taxonomy = '';

				if( isset( $_GET['product_cat'] ) ){
					$taxonomy = 'product_cat';
				}

				if( isset( $_GET['product_tag'] ) ){
					$taxonomy = 'product_tag';
				}

				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $_GET[ $taxonomy ],
					'operator' => 'IN'
				);
			}

			if( ! empty( $tax_query ) ){
				$query_args['tax_query'] = $tax_query;
			}



			/**
			 * Allow 3rd party plugin to change WP_Query args
			 */
			$query_args = apply_filters( 'yith_wcfm_products_list_query_args', $query_args );

			$query_results     = new WP_Query( $query_args );
			$this->items       = $query_results->posts;
			$this->total_items = $query_results->found_posts;

			$this->is_trash = isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] === 'trash';

			wp_reset_query();

			if( apply_filters( 'yith_wcfm_search_products_by_sku', false ) ){
				/**
				 * Search by Product SKU
				 */
				if( ! empty( $query_args['s'] ) ){
					$query_args['meta_query'] = array(
						'relation' => 'OR',
						array(
							'key'     => '_sku',
							'value'   => $query_args['s'],
							'compare' => 'LIKE'
						)
					);
					unset( $query_args['s'] );
					$query_args['post__not_in'] = wp_list_pluck( $this->items, 'ID' );
				}

				$query_results_sku = new WP_Query( $query_args );


				$items_sku = $query_results_sku->posts;

				$this->items = array_merge( $this->items, $items_sku );
				$this->total_items += $query_results_sku->found_posts;
			}

			$this->set_pagination_args( array(
				'total_items' => $this->total_items,
				'per_page'    => $per_page
			) );
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
		 * Column name
		 *
		 * @param $item
		 * @since 1.0
		 * @return void
		 */
		public function column_name( $item ){
			global $the_product;

			$the_product_id = is_object( $the_product ) ? $the_product->get_id() : null;

			if ( empty( $the_product ) || $the_product_id != $item->ID ) {
				$the_product = wc_get_product( $item );
				$the_product_id = $the_product()->get_id();
			}

			$edit_link = YITH_Frontend_Manager_Section_Products::get_edit_product_link( $item->ID );
			$title     = _draft_or_post_title();

			echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

			_post_states( $item );

			echo '</strong>';

			if ( $item->post_parent > 0 ) {
				echo '&nbsp;&nbsp;&larr; <a href="'. get_edit_post_link( $item->post_parent ) .'">'. get_the_title( $item->post_parent ) .'</a>';
			}

			// Excerpt view
			if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
				echo apply_filters( 'the_excerpt', $item->post_excerpt );
			}

			get_inline_data( $item );

			$product_inline_data = array(
				'menu_order',
				'sku',
				'regular_price',
				'sale_price',
				'weight',
				'lenght',
				'width',
				'height',
				'visibility',
				'stock_status',
				'stock',
				'manage_stock',
				'featured',
				'product_type',
				'virtual',
				'tax_status',
				'tax_class',
				'backorders'
			);

			$product_inline_data_to_print = '';

			foreach( $product_inline_data as $data ){
				$getter = "get_{$data}";
				$value = is_callable( $the_product, $getter ) ? $the_product->$getter() : '';
				$product_inline_data_to_print .= "<div class='{$data}'>{$value}</div>";
			}

			/* Custom inline data for woocommerce. */
			echo '<div class="hidden" id="woocommerce_inline_' . $item->ID . '">
						<div class="menu_order">' . $item->menu_order . '</div>'
			     . $product_inline_data_to_print .
			     '<div class="shipping_class">' . $the_product->get_shipping_class() . '</div>
					</div>
				';
			$current_section_uri = yith_wcfm_get_section_url( 'current' );
			$actions_uri = apply_filters( 'yith_wcfm_products_action_uri', array(
					'edit_uri'   => add_query_arg( array( 'product_id' => $the_product_id ), yith_wcfm_get_section_url( 'current', 'product' ) ),
					'delete_uri' => add_query_arg( array(
						'act'        => 'delete',
						'product_id' => $the_product_id
					), $current_section_uri ),
					'view_uri'   => $the_product->get_permalink()
				), $the_product
			);

			$actions_uri['duplicate_uri'] = wp_nonce_url( add_query_arg( array( 'action' => 'duplicate_product', 'post' => $the_product_id ), yith_wcfm_get_section_url( 'current', 'product' ) ), 'yith-wcfm-duplicate-product_' . $the_product_id ) ;
			yith_wcfm_add_inline_action( $actions_uri );
		}

		/**
		 * Column thumb
		 *
		 * @param $item
		 * @since 1.0
		 * @return void
		 */
		public function column_thumb( $item ){
			global $the_product;
			$the_product_id = is_object( $the_product ) ? $the_product->get_id() : null;

			if ( empty( $the_product ) || $the_product_id != $item->ID ) {
				$the_product = wc_get_product( $item );
			}

			$edit_link = YITH_Frontend_Manager_Section_Products::get_edit_product_link( $item->ID, $this->section_obj );
			echo '<a href="' . $edit_link . '">' . $the_product->get_image( 'thumbnail' ) . '</a>';
		}

		/**
		 * Column Product Categories
		 *
		 * @param $item
		 * @since 1.0
		 * @return void
		 */
		public function column_product_cat( $item ){
			$this->column_product_taxonomy( $item, 'product_cat' );
		}

		/**
		 * Column Product Tags
		 *
		 * @param $item
		 * @since 1.0
		 * @return void
		 */
		public function column_product_tag( $item ){
			$this->column_product_taxonomy( $item, 'product_tag' );
		}

		/**
		 *  Product Taxonomy content
		 *
		 * @param $item
		 * @since 1.0
		 * @return void
		 */
		public function column_product_taxonomy( $item, $taxonomy ){
			if ( ! $terms = get_the_terms( $item->ID, $taxonomy ) ) {
				echo '<span class="na">&ndash;</span>';
			}

			else {
				$termlist = array();
				foreach ( $terms as $term ) {
					$filter_link = add_query_arg( array( $taxonomy => $term->slug ), $this->section_uri );
					$termlist[] = '<a href="' . $filter_link . ' ">' . $term->name . '</a>';
				}

				echo implode( ', ', $termlist );
			}
		}

		/**
		 * Get a list of CSS classes for the WP_List_Table table tag.
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @return array List of CSS classes for the table tag.
		 */
		protected function get_table_classes() {
			return apply_filters( 'yith_wcfm_product_table_classes', array( 'widefat', 'striped', $this->_args['plural'], 'yith-wcfm-products' ) );
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
			return apply_filters( 'yith_wcfm_products_list_cols', $posts_columns );
		}

		/**
		 * Displays the table.
		 *
		 * @since 3.1.0
		 */
		public function display() {
			$singular = $this->_args['singular'];

			$this->screen->render_screen_reader_content( 'heading_list' );
			?>
			<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
				<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
				</thead>

				<tbody id="the-list"
					<?php
					if ( $singular ) {
						echo " data-wp-lists='list:$singular'";
					}
					?>
				>
				<?php $this->display_rows_or_placeholder(); ?>
				</tbody>

				<tfoot>
				<tr>
					<?php $this->print_column_headers( false ); ?>
				</tr>
				</tfoot>

			</table>
			<?php
			$this->display_tablenav( 'bottom' );
		}

	}
}
