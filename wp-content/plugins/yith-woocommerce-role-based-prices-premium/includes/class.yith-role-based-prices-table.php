<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'YITH_Role_Based_Prices_Table' ) ) {

	class YITH_Role_Based_Prices_Table extends WP_List_Table {

		/**
		 * YITH_Role_Based_Prices_Table constructor.
		 *
		 * @param array|string $args
		 */
		public function __construct( $args ) {
			parent::__construct( array(
				'singular' => _x( 'price rule', 'yith-woocommerce-role-based-prices' ),
				//singular name of the listed records
				'plural'   => _x( 'price rules', 'yith-woocommerce-role-based-prices' ),
				//plural name of the listed records
				'ajax'     => false
				//does this table support ajax?
			) );
		}

		/**
		 * get columns
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array
		 */
		public function get_columns() {
			$columns = array(
				'cb'             => '<input type="checkbox"/>',
				'post_title'     => __( 'Rule name', 'yith-woocommerce-role-based-prices' ),
				'rule_type'      => __( 'Rule type', 'yith-woocommerce-role-based-prices' ),
				'role_to_apply'  => __( 'User role', 'yith-woocommerce-role-based-prices' ),
				'type_price'     => __( 'Discount or markup', 'yith-woocommerce-role-based-prices' ),
				'priority_rule'  => __( 'Priority', 'yith-woocommerce-role-based-prices' ),
				'rule_is_active' => __( 'Active rule', 'yith-woocommerce-role-based-prices' ),
				'post_author'    => __( 'Author', 'yith-woocommerce-role-based-prices' ),
				'post_date'      => __( 'Date', 'yith-woocommerce-role-based-prices' )
			);

			return $columns;
		}

		/**
		 * get views for the table
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array
		 */
		protected function get_views() {
			$views = array(
				'all'     => __( 'All', 'yith-woocommerce-role-based-prices' ),
				'publish' => __( 'Published', 'yith-woocommerce-role-based-prices' ),
				'mine'    => __( 'Mine', 'yith-woocommerce-role-based-prices' ),
				'trash'   => __( 'Trash', 'yith-woocommerce-role-based-prices' ),
				'draft'   => __( 'Draft', 'yith-woocommerce-role-based-prices')
			);

			$current_view = $this->get_current_view();

			foreach ( $views as $view_id => $view ) {

				$query_args = array(
					'posts_per_page'  => - 1,
					'post_type'       => 'yith_price_rule',
					'post_status'     => 'publish',
					'suppress_filter' => false
				);
				$status     = 'status';
				$id         = $view_id;

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
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return string
		 */
		public function get_current_view() {

			return empty( $_GET['status'] ) ? 'all' : $_GET['status'];
		}

		/**
		 * show content for each column
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param object $item
		 * @param string $column_name
		 */
		public function column_default( $item, $column_name ) {

			switch ( $column_name ) {

				case 'post_title':
					$action_edit_query_args = array(
						'action' => 'edit',
						'post'   => $item
					);

					$action_edit_url = esc_url( add_query_arg( $action_edit_query_args, admin_url( 'post.php' ) ) );

					$delete = ( isset( $_GET['status'] ) && 'trash' === $_GET['status'] );

					$actions = array();

					if ( $delete ) {

						$post_type        = get_post_type( $item );
						$post_type_object = get_post_type_object( $post_type );

						$actions['untrash'] = "<a title='" . esc_attr__( 'Restore this item from Trash', 'yith-woocommerce-role-based-prices' ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $item ) ), 'untrash-post_' . $item ) . "'>" . __( 'Restore', 'yith-woocommerce-role-based-prices' ) . "</a>";

						$actions['delete'] = '<a href="' . esc_url( get_delete_post_link( $item, '',
								true ) ) . '" class="submitdelete">' . __( 'Delete permanently', 'yith-woocommerce-role-based-prices' ) . '</a>';
					} else {
						$actions['edit']  = '<a href="' . $action_edit_url . '">' . __( 'Edit', 'yith-woocommerce-role-based-prices' ) . '</a>';
						$actions['trash'] = '<a href="' . esc_url( get_delete_post_link( $item, '', false ) ) . '" class="submitdelete">' . __( 'Trash', 'yith-woocommerce-role-based-prices' ) . '</a>';
					}

					$post_title = get_the_title( $item );
					echo sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">#%d %s </a></strong> %s', $action_edit_url, __( 'Edit', 'yith-woocommerce-role-based-prices' ), $item, $post_title, $this->row_actions( $actions ) );

					break;

				case 'rule_type':

					$rule_type = get_post_meta( $item, '_ywcrbp_type_rule', true );
					$rule_name = '';
					if ( 'global' === $rule_type ) {
						$rule_name = __( 'All Products', 'yith-woocommerce-role-based-prices' );
					} elseif ( 'category' === $rule_type ) {
						$rule_name  = __( 'Product Category', 'yith-woocommerce-role-based-prices' );
						$categories = get_post_meta( $item, '_ywcrbp_category_product', true );
						$cat_list   = $this->get_category_list( $categories );
						$cat_list = empty( $cat_list ) ? '': $cat_list;
						$rule_name  = sprintf( '%s<br/>%s', $rule_name, $cat_list );

					} elseif( 'tag' === $rule_type) {
						$rule_name = __( 'Product Tag', 'yith-woocommerce-role-based-prices' );
						$tags      = get_post_meta( $item, '_ywcrbp_tag_product', true );
						$tag_list  = $this->get_tag_list( $tags );
						$rule_name = sprintf( '%s<br/>%s', $rule_name, $tag_list );

					}elseif( 'exc_category' === $rule_type ){
						$rule_name  = __( 'Excluded Product Category', 'yith-woocommerce-role-based-prices' );
						$categories = get_post_meta( $item, '_ywcrbp_exc_category_product', true );
						$cat_list   = $this->get_category_list( $categories );
						$cat_list = empty( $cat_list ) ? '': $cat_list;
						$rule_name  = sprintf( '%s<br/>%s', $rule_name, $cat_list );
					}else{
						$rule_name = __( 'Excluded Product Tag', 'yith-woocommerce-role-based-prices' );
						$tags      = get_post_meta( $item, '_ywcrbp_exc_tag_product', true );
						$tag_list  = $this->get_tag_list( $tags );
						$rule_name = sprintf( '%s<br/>%s', $rule_name, $tag_list );
					}

					echo '<p>' . $rule_name . '</p>';

					break;

				case 'role_to_apply':
					$role_to_apply = get_post_meta( $item, '_ywcrbp_role', true );

					if ( ! empty( $role_to_apply ) ) {
						echo '<p>' . get_user_role_label_by_slug( $role_to_apply ) . '</p>';
					}
					break;

				case 'type_price':
					$price_type = get_post_meta( $item, '_ywcrbp_type_price', true );
					//$price_value = get_post_meta( $item->ID, '_ywcrbp_value', true );

					if ( 'discount_val' === $price_type || 'markup_val' === $price_type ) {
						$price_value = get_post_meta( $item, '_ywcrbp_price_value', true );

					} else {

						$price_value = get_post_meta( $item, '_ywcrbp_decimal_value', true );
						$price_value = wc_format_localized_decimal( $price_value );
					}

					if ( 'discount_val' === $price_type ) {
						$price_html = sprintf( '%s %s', wc_price( $price_value ), __( 'discount', 'yith-woocommerce-role-based-prices' ) );
					} elseif ( 'discount_perc' === $price_type ) {
						$price_html = sprintf( '%s%s %s', $price_value, '%', __( 'discount', 'yith-woocommerce-role-based-prices' ) );
					} elseif ( 'markup_val' === $price_type ) {
						$price_html = sprintf( '%s %s', wc_price( $price_value ), __( 'markup', 'yith-woocommerce-role-based-prices' ) );
					} else {
						$price_html = sprintf( '%s%s %s', $price_value, '%', __( 'markup', 'yith-woocommerce-role-based-prices' ) );
					}

					echo '<p>' . $price_html . '</p>';
					break;

				case 'priority_rule':
					$priority = get_post_meta( $item, '_ywcrbp_priority_rule', true );

					echo '<p>' . $priority . '</p>';
					break;

				case 'rule_is_active':

					$is_enabled = get_post_meta( $item, '_ywcrbp_active_rule', true );
					if ( $is_enabled ) {
						$class = 'show';
						$tip   = __( 'Enabled', 'yith-woocommerce-role-based-prices' );
					} else {
						$class = 'hide';
						$tip   = __( 'Disabled', 'yith-woocommerce-role-based-prices' );
					}

					echo sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $class, $tip, $tip );
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
			}
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param object $item
		 *
		 * @return string
		 */
		public function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="ywcrbs_ids[]" value="%s" />', $item
			);
		}

		/**
		 * return bulk actions
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array|false|string
		 */
		public function get_bulk_actions() {

			$actions = $this->current_action();

			if ( isset( $_REQUEST['ywcrbs_ids'] ) ) {

				$rules = $_REQUEST['ywcrbs_ids'];

				if ( $actions == 'delete' || $actions == 'trash' ) {

					$delete = $actions == 'delete';


					foreach ( $rules as $rule_id ) {

						if( $delete ) {
							wp_delete_post( $rule_id, true );
						}else {
							wp_trash_post( $rule_id );
						}
					}

					$args = array(
						'page' => 'yith_wcrbp_panel',
						'tab' =>'price-rules'
					);

					$admin_url = admin_url( 'admin.php') ;
					$url = esc_url_raw( add_query_arg( $args, $admin_url ) );

					wp_redirect( $url );
					exit;
				}


			}

			$actions = array(
				'delete' => __( 'Delete', 'yith-woocommerce-role-based-prices' ),
				'trash'  => __( 'Move to Trash', 'yith-woocommerce-role-based-prices')
			);

			return $actions;
		}

		/** get sortable columns
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array
		 */
		public function get_sortable_columns() {
			$sortable_columns = array(
				'post_title'    => array( 'post_title', true ),
				'post_date'     => array( 'post_date', false ),
				'priority_rule' => array( 'priority_rule', false ),
				'role_to_apply' => array( 'role_to_apply', false )
			);

			return $sortable_columns;
		}

		/**
		 * prepare items to display
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function prepare_items() {
			$per_page              = 15;
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			$current_page = $this->get_pagenum();

			$query_args = array(
				'posts_per_page'  => $per_page,
				'paged'           => $current_page,
				'suppress_filter' => false
			);

			if ( isset( $_GET['status'] ) && 'all' !== $_GET['status'] ) {

				$query_args['post_status'] = $_GET['status'];
			}

			if ( isset( $_GET['author'] ) && 'mine' === $_GET['author'] ) {
				$query_args['author'] = $_GET['author'];
			}

			$items = YITH_Role_Based_Type()->get_price_rule( $query_args, false );

			@usort( $items, array( $this, 'sort_by' ) );

			$this->items = $items;
			$count_posts = wp_count_posts( 'yith_price_rule' );
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

		/**
		 * sort items
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return int
		 */
		public function sort_by( $a, $b ) {

			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'priority_rule'; //If no sort, default to priority rule
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; //If no order, default to desc

			if ( $orderby == 'priority_rule' ) {

				$p1 = get_post_meta( $a, '_ywcrbp_priority_rule', true );
				$p2 = get_post_meta( $b, '_ywcrbp_priority_rule', true );

				$p1 = intval( $p1 );
				$p2 = intval( $p2 );

				if ( $p1 < $p2 ) {
					$result = - 1;
				} elseif ( $p1 > $p2 ) {
					$result = 1;
				} else {
					$result = 0;
				}
			} elseif ( $orderby === 'post_date' ) {

				$pa    = get_post( $a );
				$pb    = get_post( $b );
				$date1 = strtotime( $pa->post_modified );
				$date2 = strtotime( $pb->post_modified );

				if ( $date1 < $date2 ) {
					$result = - 1;
				} else if ( $date1 > $date2 ) {
					$result = 1;
				} else {
					$result = 0;
				}

			} elseif ( $orderby == 'role_to_apply' ) {
				$p1 = get_post_meta( $a, '_ywcrbp_role', true );
				$p2 = get_post_meta( $b, '_ywcrbp_role', true );

				$result = strcmp( $p1, $p2 );
			} else {
				$pa     = get_post( $a );
				$pb     = get_post( $b );
				$result = strcmp( $pa->post_title, $pb->post_title );
			}

			return $order === 'asc' ? $result : - $result;
		}

		private function get_category_list( $categories_id ) {

			$categories_name = '';

			if ( ! empty( $categories_id ) ) {

				if ( ! is_array( $categories_id ) ) {
					$categories_id = explode( ',', $categories_id );
				}
				$categories = array();
				foreach ( $categories_id as $cat_id ) {

					$category          = get_term_by( 'id', $cat_id, 'product_cat', 'ARRAY_A' );
					$categories[] = $category['name'];
				}

				$categories_name = sprintf( '<small>%s</small>', implode( ',', $categories ) );
			}

			return $categories_name;
		}

		private function get_tag_list( $tags_id ) {

			$tag_name = '';

			if ( ! empty( $tags_id ) ) {

				if ( ! is_array( $tags_id ) ) {
					$tags_id = explode( ',', $tags_id );
				}
				$tags = array();
				foreach ( $tags_id as $tag_id ) {

					$tag        = get_term_by( 'id', $tag_id, 'product_tag', 'ARRAY_A' );
					$tags[] = $tag['name'];
				}

				$tag_name = sprintf( '<small>%s</small>', implode( ',', $tags ) );
			}

			return $tag_name;
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
	}

}