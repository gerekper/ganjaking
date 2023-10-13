<?php
/**
 * Popular products table class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes\Tables
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Popular_Table' ) ) {
	/**
	 * Admin view class. Create and populate "user with wishlists" table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Popular_Table extends WP_List_Table {

		/**
		 * Class constructor method
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			global $status, $page;

			// Set parent defaults.
			parent::__construct(
				array(
					'singular' => 'product',     // singular name of the listed records.
					'plural'   => 'products',    // plural name of the listed records.
					'ajax'     => false,        // does this table support ajax?
				)
			);
		}

		/**
		 * Print column for product name
		 *
		 * @param array $item Item for the current record.
		 * @return string Column content
		 * @since 2.0.5
		 */
		public function column_name( $item ) {
			$product = wc_get_product( $item['id'] );

			if ( ! $product ) {
				return '';
			}

			$product_url      = $product->get_permalink();
			$product_edit_url = get_edit_post_link( $item['id'] );
			$product_name     = $product->get_name();

			$actions     = array(
				'ID'           => $item['id'],
				'edit'         => sprintf( '<a href="%s" title="%s">%s</a>', $product_edit_url, __( 'Edit this item', 'yith-woocommerce-wishlist' ), __( 'Edit', 'yith-woocommerce-wishlist' ) ),
				'view_users'   => sprintf(
					'<a href="%s" title="%s">%s</a>',
					esc_url(
						add_query_arg(
							array(
								'page'       => 'yith_wcwl_panel',
								'tab'        => 'dashboard-popular',
								'action'     => 'show_users',
								'product_id' => $item['id'],
							),
							admin_url( 'admin.php' )
						)
					),
					__( 'View a list with users that have added this product to their wishlist', 'yith-woocommerce-wishlist' ),
					__( 'View users', 'yith-woocommerce-wishlist' )
				),
				'view_product' => sprintf( '<a href="%s" title="%s" rel="permalink">%s</a>', $product_url, __( 'View Product', 'yith-woocommerce-wishlist' ), __( 'View Product', 'yith-woocommerce-wishlist' ) ),
			);
			$row_actions = $this->row_actions( $actions );

			$column_content = sprintf(
				'%s<div class="product-details"><strong><a class="row-title" href="%s">%s</a></strong>%s</div>',
				$product->get_image( 'thumbnail' ),
				esc_url(
					add_query_arg(
						array(
							'page'       => 'yith_wcwl_panel',
							'tab'        => 'dashboard-popular',
							'action'     => 'show_users',
							'product_id' => $item['id'],
						),
						admin_url( 'admin.php' )
					)
				),
				$product_name,
				$row_actions
			);
			return $column_content;
		}

		/**
		 * Print column for product category
		 *
		 * @param array $item Item for the current record.
		 * @return string Column content
		 * @since 2.0.5
		 */
		public function column_category( $item ) {
			$product_categories = wp_get_post_terms( $item['id'], 'product_cat' );

			if ( ! $product_categories || is_wp_error( $product_categories ) ) {
				return '-';
			}

			$product_categories_names = wp_list_pluck( $product_categories, 'name' );

			$column_content = implode( ', ', $product_categories_names );
			return $column_content;
		}

		/**
		 * Print column for wishlist count
		 *
		 * @param array $item Item for the current record.
		 * @return string Column content
		 * @since 2.0.5
		 */
		public function column_count( $item ) {
			$column_content = $item['wishlist_count'];
			return sprintf(
				'<a href="%s">%d</a>',
				esc_url(
					add_query_arg(
						array(
							'page'       => 'yith_wcwl_panel',
							'tab'        => 'dashboard-popular',
							'action'     => 'show_users',
							'product_id' => $item['id'],
						),
						admin_url( 'admin.php' )
					)
				),
				$column_content
			);
		}

		/**
		 * Print column for product actions
		 *
		 * @param array $item Item for the current record.
		 * @return string Column content
		 * @since 2.0.5
		 */
		public function column_actions( $item ) {
			$column_content = '';
			$actions        = $this->get_item_actions( $item );

			if ( empty( $actions ) ) {
				return $column_content;
			}

			foreach ( $actions as $action ) {
				$column_content .= yith_plugin_fw_get_component(
					array_merge(
						array(
							'type' => 'action-button',
						),
						$action
					),
					false
				);
			}

			return $column_content;
		}

		/**
		 * Default columns print method
		 *
		 * @param array  $item Associative array of element to print.
		 * @param string $column_name Name of the column to print.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function column_default( $item, $column_name ) {
			if ( isset( $item[ $column_name ] ) ) {
				return esc_html( $item[ $column_name ] );
			} else {
				// Show the whole array for troubleshooting purposes.
				return print_r( $item, true ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}
		}

		/**
		 * Returns columns available in table
		 *
		 * @return array Array of columns of the table
		 * @since 2.0.0
		 */
		public function get_columns() {
			$columns = array(
				'name'     => __( 'Name', 'yith-woocommerce-wishlist' ),
				'category' => __( 'Category', 'yith-woocommerce-wishlist' ),
				'count'    => __( 'Wishlist count', 'yith-woocommerce-wishlist' ),
				'actions'  => '',
			);
			return $columns;
		}

		/**
		 * Returns column to be sortable in table
		 *
		 * @return array Array of sortable columns
		 * @since 2.0.0
		 */
		public function get_sortable_columns() {
			$sortable_columns = array(
				'name'  => array( 'post_title', false ),     // true means it's already sorted.
				'count' => array( 'wishlist_count', true ),
			);
			return $sortable_columns;
		}

		/**
		 * Display the table
		 *
		 * @since 3.0.0
		 */
		public function display() {
			// prints table.
			parent::display();

			// add content after table.
			/**
			 * DO_ACTION: yith_wcwl_after_popular_table
			 *
			 * Allows to render some content or fire some action after the Popular items table.
			 */
			do_action( 'yith_wcwl_after_popular_table' );
		}

		/**
		 * Prepare items for table
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function prepare_items() {
			try {
				/**
				 * Load data store.
				 *
				 * @var $items_data_store \YITH_WCWL_Wishlist_Item_Data_Store.
				 */
				$items_data_store = WC_Data_Store::load( 'wishlist-item' );
			} catch ( Exception $e ) {
				return;
			}

			// phpcs:disable WordPress.Security.NonceVerification.Recommended

			$query_arg = array();

			if ( ! empty( $_REQUEST['status'] ) && 'all' !== $_REQUEST['status'] ) {
				$query_arg['status'] = sanitize_text_field( wp_unslash( $_REQUEST['status'] ) );
			} else {
				$query_arg['status__not_in'] = 'trash';
			}

			if ( ! empty( $_REQUEST['_product_id'] ) ) {
				$query_arg['product_id'] = intval( $_REQUEST['_product_id'] );
			}

			if ( ! empty( $_REQUEST['_user_id'] ) ) {
				$query_arg['user_id'] = intval( $_REQUEST['_user_id'] );
			}

			if ( ! empty( $_REQUEST['_from'] ) ) {
				$query_arg['interval']['start_date'] = gmdate( 'Y-m-d 00:00:00', strtotime( sanitize_text_field( wp_unslash( $_REQUEST['_from'] ) ) ) );
			}

			if ( ! empty( $_REQUEST['_to'] ) ) {
				$query_arg['interval']['end_date'] = gmdate( 'Y-m-d 23:59:59', strtotime( sanitize_text_field( wp_unslash( $_REQUEST['_to'] ) ) ) );
			}

			// sets order by arguments.
			$orderby = ! empty( $_REQUEST['orderby'] ) ? sanitize_file_name( wp_unslash( $_REQUEST['orderby'] ) ) : 'wishlist_count';
			$order   = ! empty( $_REQUEST['order'] ) ? sanitize_file_name( wp_unslash( $_REQUEST['order'] ) ) : 'desc';

			// sets search params.
			$search_string = ! empty( $_REQUEST['s'] ) ? sanitize_file_name( wp_unslash( $_REQUEST['s'] ) ) : false;

			// sets pagination arguments.
			$per_page     = 20;
			$current_page = $this->get_pagenum();
			$total_items  = $items_data_store->count_products( $query_arg );

			// sets columns headers.
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			$this->items = $items_data_store->query_products(
				array_merge(
					array(
						'search'  => $search_string,
						'orderby' => $orderby,
						'order'   => $order,
						'offset'  => ( $current_page - 1 ) * $per_page,
						'limit'   => $per_page,
					),
					$query_arg
				)
			);

			// sets pagination args.
			$this->set_pagination_args(
				array(
					'total_items' => $total_items,
					'per_page'    => $per_page,
					'total_pages' => ceil( $total_items / $per_page ),
				)
			);
		}

		/**
		 * Returns a set of valid actions for current item
		 *
		 * @param array $item Single record.
		 * @return array Array of available actions
		 */
		protected function get_item_actions( $item ) {
			$view_users_url = esc_url(
				add_query_arg(
					array(
						'page'       => 'yith_wcwl_panel',
						'tab'        => 'dashboard-popular',
						'action'     => 'show_users',
						'product_id' => $item['id'],
					),
					admin_url( 'admin.php' )
				)
			);

			// retrieve draft for current item.
			$actions = array(
				'view-users' => array(
					'action' => 'view-users',
					'title'  => __( 'View users that have added this product to their wishlist', 'yith-woocommerce-wishlist' ),
					'url'    => $view_users_url,
					'icon'   => 'people',
				),
			);

			return $actions;
		}

		/**
		 * Print filters for current table
		 *
		 * @param string $which Top / Bottom.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function extra_tablenav( $which ) {
			if ( 'top' !== $which ) {
				return;
			}

			$need_reset = false;

			// phpcs:disable WordPress.Security.NonceVerification.Recommended

			// retrieve selected product.
			$product_id       = isset( $_REQUEST['_product_id'] ) ? intval( $_REQUEST['_product_id'] ) : false;
			$selected_product = '';

			if ( ! empty( $product_id ) ) {
				$product = wc_get_product( $product_id );

				if ( $product ) {
					$selected_product = '#' . $product_id . ' &ndash; ' . $product->get_title();
				}
			}

			// retrieve other query args.
			$from        = isset( $_REQUEST['_from'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_from'] ) ) : false;
			$to          = isset( $_REQUEST['_to'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_to'] ) ) : false;
			$post_status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : false;

			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			// set need reset if "Reset" button must be shown.
			if ( $product_id || $from || $to ) {
				$need_reset = true;
			}

			yit_add_select2_fields(
				array(
					'class'            => 'wc-product-search',
					'name'             => '_product_id',
					'data-placeholder' => __( 'Select a product', 'yith-woocommerce-wishlist' ),
					'data-selected'    => array( $product_id => $selected_product ),
					'style'            => 'min-width: 200px; vertical-align:middle;',
					'value'            => $product_id,
				)
			);

			?>
			<input style="width: 200px; vertical-align:middle;" class="date-picker" type="text" name="_from" value="<?php echo esc_attr( $from ); ?>" placeholder="<?php esc_html_e( 'From:', 'yith-woocommerce-wishlist' ); ?>"/>
			<input style="width: 200px; vertical-align:middle;" class="date-picker" type="text" name="_to" value="<?php echo esc_attr( $to ); ?>" placeholder="<?php esc_html_e( 'To:', 'yith-woocommerce-wishlist' ); ?>"/>
			<input type="hidden" name="status" class="post_status_page" value="<?php echo ! empty( $post_status ) ? esc_attr( $post_status ) : 'all'; ?>" />
			<?php
			submit_button( __( 'Filter', 'yith-woocommerce-wishlist' ), 'button', 'filter_action', false, array( 'id' => 'post-query-submit' ) );

			if ( $need_reset ) {
				printf(
					'<a href="%s" class="button button-secondary reset-button filter-button">%s</a>',
					esc_url(
						add_query_arg(
							array(
								'page' => 'yith_wcwl_panel',
								'tab'  => 'dashboard-popular',
							),
							admin_url( 'admin.php' )
						)
					),
					esc_html( __( 'Reset', 'yith-woocommerce-wishlist' ) )
				);
			}
		}
	}
}
