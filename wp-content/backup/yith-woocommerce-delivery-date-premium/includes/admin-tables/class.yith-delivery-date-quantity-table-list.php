<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Quantity_Table_List_Table' ) ) {

	class YITH_Quantity_Table_List_Table extends WP_List_Table {

		public function __construct( $args = array() ) {
			parent::__construct( array(
				'singular' => __( 'Quantity Table', 'yith-woocommerce-delivery-date' ),
				//singular name of the listed records
				'plural'   => __( 'Quantity Tables', 'yith-woocommerce-delivery-date' ),
				//plural name of the listed records
				'ajax'     => false
			) );
		}

		/**
		 * return the columns for the table
		 * @return array
		 * @since 2.1.0
		 * @author YITH
		 */
		public function get_columns() {

			$columns = array(
				'cb'          => '<input type="checkbox"/>',
				'post_title'  => __( 'Title', 'yith-woocommerce-delivery-date' ),
				'status'      => __( 'Status', 'yith-woocommerce-delivery-date' ),
				'assign_at'   => __( 'Assign at', 'yith-woocommerce-delivery-date' ),
				'carrier'   => __( 'Carrier', 'yith-woocommerce-delivery-date' ),
				'post_author' => __( 'Post Author', 'yith-woocommerce-delivery-date' ),
				'post_date'   => __( 'Date', 'yith-woocommerce-delivery-date' )
			);

			return $columns;
		}

		/**
		 * @param object $item
		 *
		 * @return string
		 */
		public function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="ywcdd_quantity_table_ids[]" value="%s" />', $item
			);
		}

		/**
		 * @param object|int $item
		 * @param string $column_name
		 */
		public function column_default( $item, $column_name ) {

			$output = '';
			switch ( $column_name ) {

				case 'post_title' :
					$action_edit_query_args = array(
						'action' => 'edit',
						'post'   => $item
					);
					$action_edit_url        = esc_url( add_query_arg( $action_edit_query_args, admin_url( 'post.php' ) ) );

					$delete = ( isset( $_GET['status'] ) && 'trash' === $_GET['status'] );

					$actions = array();

					if ( $delete ) {

						$post_type        = get_post_type( $item );
						$post_type_object = get_post_type_object( $post_type );

						$actions['untrash'] = "<a title='" . esc_attr__( 'Restore this item from Trash', 'yith-woocommerce-delivery-date' ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $item ) ), 'untrash-post_' . $item ) . "'>" . __( 'Restore', 'yith-woocommerce-delivery-date' ) . "</a>";

						$actions['delete'] = '<a href="' . esc_url( get_delete_post_link( $item, '',
								true ) ) . '" class="submitdelete">' . __( 'Delete permanently', 'yith-woocommerce-delivery-date' ) . '</a>';
					} else {
						$actions['edit']  = '<a href="' . $action_edit_url . '">' . __( 'Edit', 'yith-woocommerce-delivery-date' ) . '</a>';
						$actions['trash'] = '<a href="' . esc_url( get_delete_post_link( $item, '', false ) ) . '" class="submitdelete">' . __( 'Trash', 'yith-woocommerce-delivery-date' ) . '</a>';
					}

					$post_title = get_the_title( $item );
					$output     = sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">#%d %s </a></strong> %s', $action_edit_url, __( 'Edit', 'yith-woocommerce-delivery-date' ), $item, $post_title, $this->row_actions( $actions ) );
					break;

				case 'post_author':

					$post = get_post( $item );

					$author_query_args = array(
						'user_id' => $post->post_author,
					);

					$user_link = esc_url( add_query_arg( $author_query_args, admin_url( 'user-edit.php' ) ) );
					$user      = get_user_by( 'id', $post->post_author );

					echo sprintf( '<a href="%s" target="_blank">%s</a>', $user_link, $user->user_nicename );
					break;

				case 'post_date':
					$post = get_post( $item );
					echo '<p>' . $post->post_modified . '</p>';
					break;

				case 'status' :
					$status = get_post_meta( $item, 'ywcdd_enable_quantity_rule_table', true );

					$status = 'yes' === $status ? __('Activated', 'yith-woocommerce-delivery-date' ) : __( 'Deactivated', 'yith-woocommerce-delivery-date' );
					echo $status;
					break;

				case 'assign_at':
					$how_apply_table = get_post_meta( $item, 'ywcdd_table_how_set_table', true );
					$list_name = array();
					if( 'product' === $how_apply_table ){
						$products = get_post_meta( $item, 'ywcdd_table_select_product', true );

						if( $products ){
							foreach( $products as $product_id ){
								$product = wc_get_product( $product_id );
								$list_name[] = $product->get_formatted_name();
							}
						}

					}else{
						$categories = get_post_meta( $item, 'ywcdd_table_select_product_cat', true );

						if( $categories ){
							foreach ( $categories as $category_id ){

								$category = get_term( $category_id, 'product_cat');
								$list_name[] = $category->name;
							}
						}
					}

					echo implode(', ', $list_name);
					break;

				case 'carrier' :
					$carrier_id = get_post_meta( $item, 'ywcdd_table_select_carrier', true );

					if( $carrier_id ){
						echo get_the_title( $carrier_id );
					}
					break;
			}

			echo $output;
		}

		public function prepare_items() {
			$per_page              = 15;
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			$current_page = $this->get_pagenum();

			$query_args = array(
				'posts_per_page' => $per_page,
				'paged'          => $current_page,
				'fields'         => 'ids'
			);


			if ( isset( $_GET['status'] ) && 'all' !== $_GET['status'] ) {

				$query_args['post_status'] = $_GET['status'];
			}

			if ( isset( $_GET['author'] ) && 'mine' === $_GET['author'] ) {
				$query_args['author'] = $_GET['author'];
			}

			$items = YITH_Delivery_Product_Quantity_Table()->get_quantity_tables( $query_args );

			@usort( $items, array( $this, 'sort_by' ) );

			$this->items = $items;
			$count_posts = wp_count_posts( 'yith_product_table' );
			$total_items = $count_posts->publish;
			/**
			 * REQUIRED. We also have to register our pagination options & calculations.
			 */
			$this->set_pagination_args( array(
				'total_items' => $total_items,                  //WE have to calculate the total number of items
				'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
			) );
		}

		public function display_rows_or_placeholder() {
			if ( $this->has_items() ) {
				$this->display_rows();
			} else {
				echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
				$this->no_items();
				echo '</td></tr>';
			}
		}

		/**
		 * return bulk actions
		 * @return array|false|string
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public function get_bulk_actions() {

			$actions = $this->current_action();

			if ( isset( $_REQUEST['ywcdd_quantity_table_ids'] ) ) {

				$rules = $_REQUEST['ywcdd_quantity_table_ids'];

				if ( $actions == 'delete' || $actions == 'trash' ) {

					$delete = $actions == 'delete';


					foreach ( $rules as $rule_id ) {

						if ( $delete ) {
							wp_delete_post( $rule_id, true );
						} else {
							wp_trash_post( $rule_id );
						}
					}

					$args = array(
						'page' => 'yith_delivery_date_panel',
						'tab'  => 'delivery-table'
					);

					$admin_url = admin_url( 'admin.php' );
					$url       = esc_url_raw( add_query_arg( $args, $admin_url ) );

					wp_redirect( $url );
					exit;
				}


			}

			$actions = array(
				'delete' => __( 'Delete', 'yith-woocommerce-delivery-date' ),
				'trash'  => __( 'Move to Trash', 'yith-woocommerce-delivery-date' )
			);

			return $actions;
		}

		/**
		 * get views for the table
		 * @return array
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		protected function get_views() {
			$views = array(
				'all'     => __( 'All', 'yith-woocommerce-delivery-date' ),
				'publish' => __( 'Published', 'yith-woocommerce-delivery-date' ),
				'mine'    => __( 'Mine', 'yith-woocommerce-delivery-date' ),
				'trash'   => __( 'Trash', 'yith-woocommerce-delivery-date' ),
				'draft'   => __( 'Draft', 'yith-woocommerce-delivery-date' )
			);

			$current_view = $this->get_current_view();

			foreach ( $views as $view_id => $view ) {

				$query_args = array(
					'posts_per_page'  => - 1,
					'post_type'       => 'yith_product_table',
					'post_status'     => 'publish',
					'suppress_filter' => false
				);


				$status                   = 'status';
				$id                       = $view_id;

				if ( 'mine' === $view_id ) {
					$query_args['author'] = get_current_user_id();
					$status               = 'author';
					$id                   = get_current_user_id();

				} elseif ( 'all' !== $view_id ) {
					$query_args['post_status'] = $view_id;
				}

				$href              = esc_url( add_query_arg( $status, $id ) );
				$total_items       = count( get_posts( $query_args ) );
				$class             = $view_id == $current_view ? 'current' : '';
				$views[ $view_id ] = sprintf( "<a href='%s' class='%s'>%s <span class='count'>(%d)</span></a>", $href, $class, $view, $total_items );
			}


			return $views;
		}

		/**
		 * return current view
		 * @return string
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public function get_current_view() {

			return empty( $_GET['status'] ) ? 'all' : $_GET['status'];
		}
	}
}

