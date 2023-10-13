<?php
/**
 * Popular users products table class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes\Tables
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Users_Popular_Table' ) ) {
	/**
	 * Admin view class. Create and populate "users that added product to wishlist" table
	 *
	 * @since 2.0.6
	 */
	class YITH_WCWL_Users_Popular_Table extends WP_List_Table {

		/**
		 * Current product id
		 *
		 * @var int current product id
		 */
		public $product_id;

		/**
		 * Class constructor method
		 *
		 * @since 2.0.6
		 */
		public function __construct() {
			global $status, $page;

			$product_id = isset( $_GET['product_id'] ) ? intval( $_GET['product_id'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $product_id ) {
				$this->product_id = $product_id;
				$product          = wc_get_product( $this->product_id );

				$product_name = $product instanceof WC_Product ? $product->get_title() : __( 'product', 'yith-woocommerce-wishlist' );
			} else {
				$product_name = __( 'product', 'yith-woocommerce-wishlist' );
			}

			// Set parent defaults.
			parent::__construct(
				array(
					'singular' => sprintf( 'user for %s', $product_name ),
					'plural'   => sprintf( 'users for %s', $product_name ),
					'ajax'     => false,
				)
			);
		}

		/**
		 * Print column for user thumbnail
		 *
		 * @param array $item Item for the current record.
		 * @return string Column content
		 * @since 2.0.6
		 */
		public function column_thumb( $item ) {
			$avatar   = get_avatar( $item['id'], 40 );
			$edit_url = get_edit_user_link( $item['id'] );

			$column_content = sprintf( '<a href="%s">%s</a>', $edit_url, $avatar );
			return $column_content;
		}

		/**
		 * Print column for user name
		 *
		 * @param array $item Item for the current record.
		 * @return string Column content
		 * @since 2.0.5
		 */
		public function column_name( $item ) {
			if ( ! $item['id'] ) {
				return sprintf( '- %s -', __( 'guest', 'yith-woocommerce-wishlist' ) );
			}
			$user_edit_url = get_edit_user_link( $item['id'] );
			$user_name     = $item['user_name'];
			$user_email    = $item['user_email'];

			$actions     = array(
				'ID'      => $item['id'],
				'edit'    => sprintf( '<a href="%s" title="%s">%s</a>', $user_edit_url, __( 'Edit this user', 'yith-woocommerce-wishlist' ), __( 'Edit', 'yith-woocommerce-wishlist' ) ),
				'mail_to' => sprintf( '<a href="mailto:%s" title="%s">%s</a>', $user_email, __( 'Email this user', 'yith-woocommerce-wishlist' ), __( 'Email user', 'yith-woocommerce-wishlist' ) ),
			);
			$row_actions = $this->row_actions( $actions );

			$column_content = sprintf( '<strong><a class="row-title" href="%s">%s</a></strong>%s', $user_edit_url, $user_name, $row_actions );
			return $column_content;
		}

		/**
		 * Print column for user name
		 *
		 * @param array $item Item for the current record.
		 * @return string Column content
		 * @since 2.0.5
		 */
		public function column_date_added( $item ) {
			$date_added = $item['date_added'];

			return date_i18n( 'd F Y', strtotime( $date_added ) );
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
				'thumb'      => sprintf( '<span class="wc-image tips" data-tip="%s">%s</span>', __( 'Image', 'yith-woocommerce-wishlist' ), __( 'Image', 'yith-woocommerce-wishlist' ) ),
				'name'       => __( 'Name', 'yith-woocommerce-wishlist' ),
				'date_added' => __( 'Added on', 'yith-woocommerce-wishlist' ),
				'actions'    => '',
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
				'date_added' => array( 'dateadded', true ),
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
			// sets pagination arguments.
			$per_page     = 20;
			$current_page = $this->get_pagenum();

			// sets order by arguments.
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'dateadded';
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc';
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			// sets columns headers.
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			$args        = array(
				'product_id'  => $this->product_id,
				'user_id'     => false,
				'session_id'  => false,
				'wishlist_id' => 'all',
				'order'       => $order,
				'orderby'     => $orderby,
			);
			$total_items = YITH_WCWL_Wishlist_Factory::get_wishlist_items_count( $args );

			$args  = array_merge(
				$args,
				array(
					'limit'  => $per_page,
					'offset' => $per_page * ( $current_page - 1 ),
				)
			);
			$items = YITH_WCWL_Wishlist_Factory::get_wishlist_items( $args );

			if ( ! empty( $items ) ) {
				$user_ids = array();

				foreach ( $items as $item ) {
					$user = $item->get_user();

					if ( ! $user ) {
						$this->items[] = array(
							'id'         => false,
							'date_added' => $item->get_date_added(),
						);
					} elseif ( ! in_array( $item->get_user_id(), $user_ids, true ) ) {
						$user_ids[] = $item->get_user_id();

						$this->items[] = array(
							'id'         => $item->get_user_id(),
							'user_name'  => $user->user_login,
							'user_email' => $user->user_email,
							'date_added' => $item->get_date_added(),
						);
					}
				}
			}

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
			return array();
		}
	}
}
