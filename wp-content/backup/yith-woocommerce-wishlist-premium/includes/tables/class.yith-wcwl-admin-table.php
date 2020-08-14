<?php
/**
 * Admin table class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Admin_Table' ) ) {
	/**
	 * Admin view class. Create and populate "user with wishlists" table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Admin_Table extends WP_List_Table {

		/**
		 * Class constructor method
		 *
		 * @return \YITH_WCWL_Admin_Table
		 * @since 2.0.0
		 */
		public function __construct() {
			global $status, $page;

			// Set parent defaults.
			parent::__construct(
				array(
					'singular'  => __( 'wishlist', 'yith-woocommerce-wishlist' ),     // singular name of the listed records.
					'plural'    => __( 'wishlists', 'yith-woocommerce-wishlist' ),    // plural name of the listed records.
					'ajax'      => false,                                             // does this table support ajax?
				)
			);
		}

		/**
		 * Default columns print method
		 *
		 * @param array  $item       Associative array of element to print.
		 * @param string $column_name Name of the column to print.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function column_default( $item, $column_name ) {
			if ( isset( $item[ $column_name ] ) ) {
				return apply_filters( 'yith_wcwl_column_default', esc_html( $item[ $column_name ] ), $item, $column_name );
			} else {
				return apply_filters( 'yith_wcwl_column_default', print_r( $item, true ), $item, $column_name ); // Show the whole array for troubleshooting purposes.
			}
		}

		/**
		 * Prints column for wishlist user
		 *
		 * @param array $item Item to use to print record.
		 * @return string
		 * @since 2.0.0
		 */
		public function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				$this->_args['singular'],  // Let's simply repurpose the table's singular label.
				$item['ID']                // The value of the checkbox should be the record's id.
			);
		}

		/**
		 * Return username column for an item
		 *
		 * @param array $item Item to use to print record.
		 * @return string
		 * @since 2.0.0
		 */
		public function column_username( $item ) {
			$row = '';

			if ( isset( $item['user_id'] ) ) {
				$user = get_user_by( 'id', $item['user_id'] );

				if ( ! empty( $user ) ) {
					$row = sprintf(
						"%s<div class='customer-details'><strong><a href='%s'>%s</a></strong></div>",
						get_avatar( $item['user_id'], 40 ),
						get_edit_user_link( $item['user_id'] ),
						$user->user_login
					);
				} else {
					$row = sprintf( '- %s -', __( 'guest', 'yith-woocommerce-wishlist' ) );
				}
			}

			return $row;
		}

		/**
		 * Prints column for wishlist name
		 *
		 * @param array $item Item to use to print record.
		 * @return string
		 * @since 2.0.0
		 */
		public function column_name( $item ) {
			$row = '';

			$delete_wishlist_url = add_query_arg(
				array(
					'action' => 'delete_wishlist',
					'wishlist_id' => $item['ID'],
				),
				wp_nonce_url( admin_url( 'admin.php' ), 'delete_wishlist', 'delete_wishlist' )
			);

			$actions = array(
				'view' => sprintf( '<a href="%s">%s</a>', YITH_WCWL()->get_wishlist_url( 'view/' . $item['wishlist_token'] ), __( 'View', 'yith-woocommerce-wishlist' ) ),
				'delete' => sprintf( '<a href="%s">%s</a>', esc_url( $delete_wishlist_url ), __( 'Delete', 'yith-woocommerce-wishlist' ) ),
			);

			if ( isset( $item['wishlist_name'] ) ) {
				$row = sprintf(
					"<a href='%s'>%s</a>%s",
					YITH_WCWL()->get_wishlist_url( 'view/' . $item['wishlist_token'] ),
					( ! empty( $item['wishlist_name'] ) ) ? $item['wishlist_name'] : get_option( 'yith_wcwl_wishlist_title' ),
					$this->row_actions( $actions )
				);
			}

			return $row;
		}

		/**
		 * Prints column for wishlist privacy
		 *
		 * @param array $item Item to use to print record.
		 * @return string
		 * @since 2.0.0
		 */
		public function column_privacy( $item ) {
			$row = '';

			if ( isset( $item['wishlist_privacy'] ) ) {
				switch ( $item['wishlist_privacy'] ) {
					case 0:
						$row = __( 'Public', 'yith-woocommerce-wishlist' );
						break;
					case 1:
						$row = __( 'Shared', 'yith-woocommerce-wishlist' );
						break;
					case 2:
						$row = __( 'Private', 'yith-woocommerce-wishlist' );
						break;
					default:
						$row = __( 'N/D', 'yith-woocommerce-wishlist' );
						break;
				}
			}

			return $row;
		}

		/**
		 * Prints column for wishlist number of items
		 *
		 * @param array $item Item to use to print record.
		 * @return string
		 * @since 2.0.0
		 */
		public function column_items( $item ) {
			$row = '';

			if ( isset( $item['wishlist_token'] ) ) {
				$row = YITH_WCWL()->count_products( $item['wishlist_token'] );
			}

			return $row;
		}

		/**
		 * Prints column for wishlist creation date
		 *
		 * @param array $item Item to use to print record.
		 * @return string
		 * @since 2.0.0
		 */
		public function column_date( $item ) {
			$row = '';

			if ( isset( $item['dateadded'] ) ) {
				$dateadded_time = strtotime( $item['dateadded'] );
				$time_diff = time() - $dateadded_time;

				if ( $time_diff < DAY_IN_SECONDS ) {
					// translators: 1. Date diff since wishlist creation (EG: 1 hour, 2 seconds, etc...).
					$row = sprintf( __( '%s ago', 'yith-woocommerce-wishlist' ), human_time_diff( $dateadded_time ) );
				} else {
					$row = date_i18n( wc_date_format(), $dateadded_time );
				}
			}

			return $row;
		}


		/**
		 * Returns columns available in table
		 *
		 * @return array Array of columns of the table
		 * @since 2.0.0
		 */
		public function get_columns() {
			$columns = array(
				'cb'        => '<input type="checkbox" />',
				'name'      => __( 'Name', 'yith-woocommerce-wishlist' ),
				'username'  => __( 'Username', 'yith-woocommerce-wishlist' ),
				'privacy'   => __( 'Privacy', 'yith-woocommerce-wishlist' ),
				'items'     => __( 'Items in wishlist', 'yith-woocommerce-wishlist' ),
				'date'      => __( 'Date', 'yith-woocommerce-wishlist' ),
			);
			return apply_filters( 'yith_wcwl_wishlist_column', $columns );
		}

		/**
		 * Returns column to be sortable in table
		 *
		 * @return array Array of sortable columns
		 * @since 2.0.0
		 */
		public function get_sortable_columns() {
			$sortable_columns = array(
				'name'      => array( 'wishlist_name', false ), // true means it's already sorted.
				'username'  => array( 'user_login', false ),
				'privacy'   => array( 'wishlist_privacy', false ),
				'date'      => array( 'dateadded', false ),
			);
			return $sortable_columns;
		}

		/**
		 * Sets bulk actions for table
		 *
		 * @return array Array of available actions
		 * @since 2.0.0
		 */
		public function get_bulk_actions() {
			$actions = array(
				'delete' => __( 'Delete', 'yith-woocommerce-wishlist' ),
			);
			return $actions;
		}

		/**
		 * Returns views for wishlist page
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function get_views() {
			$views = array(
				'all' => sprintf(
					"<a href='%s' class='%s'>%s <span class='count'>(%d)</span></a>",
					esc_url( add_query_arg( 'wishlist_privacy', 'all' ) ),
					( empty( $_GET['wishlist_privacy'] ) || isset( $_GET['wishlist_privacy'] ) && 'all' === $_GET['wishlist_privacy'] ) ? 'current' : '',
					_x( 'All', 'Admin: "all wishlists" table views', 'yith-woocommerce-wishlist' ),
					count( YITH_WCWL()->get_wishlists( array( 'user_id' => false, 'wishlist_visibility' => 'all', 'show_empty' => false ) ) ) // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
				),
				'public'  => sprintf(
					"<a href='%s' class='%s'>%s <span class='count'>(%d)</span></a>",
					esc_url( add_query_arg( 'wishlist_privacy', 'public' ) ),
					( isset( $_GET['wishlist_privacy'] ) && 'public' === $_GET['wishlist_privacy'] ) ? 'current' : '',
					_x( 'Public', 'Admin: "all wishlists" table views', 'yith-woocommerce-wishlist' ),
					count( YITH_WCWL()->get_wishlists( array( 'user_id' => false, 'wishlist_visibility' => 'public', 'show_empty' => false ) ) ) // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
				),
				'shared'  => sprintf(
					"<a href='%s' class='%s'>%s <span class='count'>(%d)</span></a>",
					esc_url( add_query_arg( 'wishlist_privacy', 'shared' ) ),
					( isset( $_GET['wishlist_privacy'] ) && 'shared' === $_GET['wishlist_privacy'] ) ? 'current' : '',
					_x( 'Shared', 'Admin: "all wishlists" table views', 'yith-woocommerce-wishlist' ),
					count( YITH_WCWL()->get_wishlists( array( 'user_id' => false, 'wishlist_visibility' => 'shared', 'show_empty' => false ) ) ) // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
				),
				'private' => sprintf(
					"<a href='%s' class='%s'>%s <span class='count'>(%d)</span></a>",
					esc_url( add_query_arg( 'wishlist_privacy', 'private' ) ),
					( isset( $_GET['wishlist_privacy'] ) && 'private' === $_GET['wishlist_privacy'] ) ? 'current' : '',
					_x( 'Private', 'Admin: "all wishlists" table views', 'yith-woocommerce-wishlist' ),
					count( YITH_WCWL()->get_wishlists( array( 'user_id' => false, 'wishlist_visibility' => 'private', 'show_empty' => false ) ) ) // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
				),
			);

			return $views;
		}

		/**
		 * Delete wishlist on bulk action
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function process_bulk_action() {
			// Detect when a bulk action is being triggered...
			if ( 'delete' === $this->current_action() && ! empty( $_REQUEST[ $this->_args['singular'] ] ) ) {
				$wishlist_ids = array_map( 'intval', $_REQUEST[ $this->_args['singular'] ] );

				foreach ( $wishlist_ids as $wishlist_id ) {
					try {
						YITH_WCWL_Premium()->remove_wishlist( $wishlist_id );
					} catch ( Exception $e ) {
						continue;
					}
				}
			}
		}

		/**
		 * Prepare items for table
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function prepare_items() {
			// sets pagination arguments.
			$per_page = 20;
			$current_page = $this->get_pagenum();
			$total_items = count(
				YITH_WCWL()->get_wishlists(
					array(
						'user_id' => false,
						'user_search' => isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : false,
						'wishlist_visibility' => isset( $_REQUEST['wishlist_privacy'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wishlist_privacy'] ) ) : 'all',
						'show_empty' => apply_filters( 'yith_wcwl_admin_table_show_empty_list', false ),
					)
				)
			);

			// sets columns headers.
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			// process bulk actions.
			$this->process_bulk_action();

			// retrieve data for table.
			$this->items = YITH_WCWL()->get_wishlists(
				array(
					'user_id' => false,
					'orderby' => ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'wishlist_name',
					'order' => ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'asc',
					'limit' => $per_page,
					'offset' => ( ( $current_page - 1 ) * $per_page ),
					'user_search' => isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : false,
					'wishlist_visibility' => isset( $_REQUEST['wishlist_privacy'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wishlist_privacy'] ) ) : 'all',
					'show_empty' => apply_filters( 'yith_wcwl_admin_table_show_empty_list', false ),
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
	}
}
