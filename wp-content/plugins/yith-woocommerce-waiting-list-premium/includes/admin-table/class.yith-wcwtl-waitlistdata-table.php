<?php
/**
 * Admin waiting list data table class
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Displays the exclusions table in YITH_WCWTL plugin admin tab
 *
 * @class   YITH_WCWTL_WaitlistData_Table
 * @since   1.0.0
 * @author  Yithemes
 *
 * @package YITH Woocommerce Waiting List
 */
if ( ! class_exists( 'YITH_WCWTL_WaitlistData_Table' ) ) {

	class YITH_WCWTL_WaitlistData_Table extends WP_List_Table {
		/**
		 * Construct
		 */
		public function __construct() {

			//Set parent defaults
			parent::__construct( array(
					'singular' => 'waitlist', //singular name of the listed records
					'plural'   => 'waitlists', //plural name of the listed records
					'ajax'     => false //does this table support ajax?
				)
			);
		}

		/**
		 * Returns columns available in table
		 *
		 * @since 1.1.3
		 * @return array Array of columns of the table
		 */
		public function get_columns() {
			$columns = array(
				'cb'        => '<input type="checkbox" />',
				'product'   => __( 'Product', 'yith-woocommerce-waiting-list' ),
				'variation' => __( 'Variation', 'yith-woocommerce-waiting-list' ),
				'thumb'     => __( 'Thumbnail', 'yith-woocommerce-waiting-list' ),
				'status'    => __( 'Stock Status', 'yith-woocommerce-waiting-list' ),
				'users'     => __( 'Users in the Waiting list', 'yith-woocommerce-waiting-list' ),
				'actions'   => __( 'Actions', 'yith-woocommerce-waiting-list' ),
			);

			return apply_filters( 'yith_wcwtl_waitlistdata_table_columns', $columns );
		}

		/**
		 * Print the columns information
		 *
		 * @since 1.1.3
		 * @param $column_name
		 *
		 * @param $rec
		 * @return string
		 */
		public function column_default( $rec, $column_name ) {

			/** @var WC_Product $order */
			$product_id = intval( $rec['product_id'] );
			$product    = wc_get_product( $product_id );

			if ( empty( $product ) ) {
				return null;
			}

			switch ( $column_name ) {

				case 'product':
					/**
					 * @type $product WC_Product
					 */
					$product_query_args = array(
						'post'   => yit_get_base_product_id( $product ),
						'action' => 'edit',
					);
					$product_url        = add_query_arg( $product_query_args, admin_url( 'post.php' ) );

					return sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">%s</a></strong>', esc_url( $product_url ), esc_html__( 'Edit product', 'yith-woocommerce-waiting-list' ), $product->get_title() );
					break;

				case 'variation':
					if ( $product->is_type( 'variation' ) ) {

						$variations = $product->get_variation_attributes();

						$html = '<ul>';

						foreach ( $variations as $key => $value ) {
							if ( ! $value ) {
								continue;
							}
							$key  = ucfirst( str_replace( 'attribute_pa_', '', $key ) );
							$html .= '<li>' . $key . ': ' . $value . '</li>';
						}

						$html .= '</ul>';

						return $html;
					} else {
						return '-';
					}
					break;

				case 'thumb':
					return $product->get_image();
					break;

				case 'status':
					$status = $product->get_availability();
					return ! empty( $status ) ? '<span class="' . $status['class'] . '">' . $status['availability'] . '</span>' : '-';
					break;

				case 'users':
					$view_query_args = array(
						'page' => $_GET['page'],
						'tab'  => $_GET['tab'],
						'view' => 'users',
						'id'   => $product_id,
					);
					$view_url        = add_query_arg( $view_query_args, admin_url( 'admin.php' ) );

					return '<a href="' . esc_url( $view_url ) . '">' . yith_count_users_on_waitlist( $product_id ) . '</a>';
					break;

				case 'actions':

					$delete_query_args = array(
						'page'   => $_GET['page'],
						'tab'    => $_GET['tab'],
						'action' => 'delete',
						'id'     => $product_id,
					);
					$delete_url        = add_query_arg( $delete_query_args, admin_url( 'admin.php' ) );
					$actions_button    = '<a href="' . esc_url( $delete_url ) . '" class="button">' . esc_html__( 'Delete Waiting list', 'yith-woocommerce-waiting-list' ) . '</a>';

					$view_query_args = array(
						'page' => $_GET['page'],
						'tab'  => $_GET['tab'],
						'view' => 'users',
						'id'   => $product_id,
					);
					$view_url        = add_query_arg( $view_query_args, admin_url( 'admin.php' ) );
					$actions_button  .= '<a href="' . esc_url( $view_url ) . '" class="button">' . esc_html__( 'View Users', 'yith-woocommerce-waiting-list' ) . '</a>';

					$mail_query_args = array(
						'page'   => $_GET['page'],
						'tab'    => $_GET['tab'],
						'action' => 'send_mail',
						'id'     => $product_id,
					);
					$mail_url        = add_query_arg( $mail_query_args, admin_url( 'admin.php' ) );
					$actions_button  .= '<a href="' . esc_url( $mail_url ) . '" class="send_mail button">' . esc_html__( 'Send email', 'yith-woocommerce-waiting-list' ) . '</a>';

					return $actions_button;
					break;

				case 'sku' :
					return $product->get_sku();
					break;

			}

			return null;
		}

		/**
		 * Prints column cb
		 *
		 * @since 1.1.3
		 * @param $rec Object Item to use to print CB record
		 *
		 * @return string
		 */
		public function column_cb( $rec ) {
			return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $rec['product_id'] );
		}

		/**
		 * Sets bulk actions for table
		 *
		 * @since 1.1.3
		 * @return array Array of available actions
		 */
		public function get_bulk_actions() {
			$actions = array(
				'delete' => __( 'Delete waiting list', 'yith-woocommerce-waiting-list' ),
			);

			return apply_filters( 'yith_wcwtl_waitlistdata_table_bulk_actions', $actions );
		}

		/**
		 * Prepare items for table
		 *
		 * @since 1.1.3
		 * @param array $args
		 *
		 */
		public function prepare_items( $args = array() ) {

			// blacklist args
			$q = wp_parse_args( $args, array(
				'paged'  => absint( $this->get_pagenum() ),
				'number' => apply_filters( 'yith_wcwtl_waitlistdata_table_page_size', 20 ),
				's'      => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
				'status' => isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '',
			) );

			global $wpdb;

			// query parts initializating
			$pieces  = array( 'join', 'where', 'orderby', 'limits' );
			$join    = "INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id";
			$where   = $wpdb->prepare( "pm.meta_key = '%s' AND pm.meta_value NOT LIKE 'a:0:{}'", '_yith_wcwtl_users_list' );
			$orderby = "";

			// Search
			if ( $q['s'] ) {
				// added slashes screw with quote grouping when done early, so done later
				$q['s'] = stripslashes( $q['s'] );
				// there are no line breaks in <input /> fields
				$q['s'] = str_replace( array( "\r", "\n" ), '', $q['s'] );

				$s     = $wpdb->prepare( "p.post_title LIKE %s", "%{$q['s']}%" );
				$where .= " AND {$s}";
			}

			// Filter by status
			if ( $q['status'] ) {
				$join  .= " INNER JOIN {$wpdb->postmeta} AS pmm ON pm.post_id = pmm.post_id";
				$s     = $wpdb->prepare( "pmm.meta_key = '_stock_status' AND pmm.meta_value = '%s'", $q['status'] );
				$where .= " AND {$s}";
			}

			// Paging
			if ( ! empty( $q['paged'] ) && ! empty( $q['number'] ) ) {
				$page = absint( $q['paged'] );
				! $page && $page = 1;

				if ( empty( $q['offset'] ) ) {
					$pgstrt = absint( ( $page - 1 ) * $q['number'] ) . ', ';
				} else { // we're ignoring $page and using 'offset'
					$q['offset'] = absint( $q['offset'] );
					$pgstrt      = $q['offset'] . ', ';
				}
				$limits = 'LIMIT ' . $pgstrt . $q['number'];
			}

			$clauses = compact( $pieces );

			$join    = isset( $clauses['join'] ) ? $clauses['join'] : '';
			$where   = isset( $clauses['where'] ) ? $clauses['where'] : '';
			$orderby = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
			$limits  = isset( $clauses['limits'] ) ? $clauses['limits'] : '';

			$items       = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS p.ID AS product_id FROM {$wpdb->posts} AS p $join WHERE $where $orderby $limits", ARRAY_A );
			$total_items = $wpdb->get_var( "SELECT FOUND_ROWS()" );

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			// retrieve data for table
			$this->items = $items;

			// sets pagination args
			if ( ! empty( $q['number'] ) ) {
				$this->set_pagination_args(
					array(
						'total_items' => $total_items,
						'per_page'    => $q['number'],
						'total_pages' => ceil( $total_items / $q['number'] ),
					)
				);
			}
		}

		/**
		 * Display the search box.
		 *
		 * @since  3.1.0
		 * @access public
		 *
		 * @param string $text     The search button text
		 * @param string $input_id The search input id
		 */
		public function add_search_box( $text, $input_id ) {
			parent::search_box( $text, $input_id );
		}

		/**
		 * Message to be displayed when there are no items
		 *
		 * @since  3.1.0
		 * @access public
		 */
		public function no_items() {
			esc_html_e( 'No items found.', 'yith-woocommerce-waiting-list' );
		}

		/**
		 * Extra controls to be displayed between bulk actions and pagination
		 *
		 * @since 1.1.3
		 *
		 * @param string $which
		 */
		protected function extra_tablenav( $which ) {
			if ( 'top' == $which ) {
				$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';
				?>
				<div class="alignleft actions">
				<select name="status">
					<option value=""><?php esc_html_e( 'Select status..', 'yith-woocommerce-waiting-list' ); ?></option>
					<option value="instock" <?php selected( 'instock', $status ) ?>><?php esc_html_e( 'In Stock', 'yith-woocommerce-waiting-list' ); ?></option>
					<option value="outofstock" <?php selected( 'outofstock', $status ) ?>><?php esc_html_e( 'Out of Stock', 'yith-woocommerce-waiting-list' ); ?></option>
				</select>
				<?php
				submit_button( __( 'Filter' ), 'button', 'filter_action', false, array( 'id' => 'filter-action-submit' ) );
				?></div><?php
			}

			// let add other actions
			do_action( 'yith_wcwtl_table_extra_tablenav', $which );
		}
	}
}