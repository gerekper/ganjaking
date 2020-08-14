<?php
/**
 * Commissions Table class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Commissions_Table' ) ) {
	/**
	 * WooCommerce Commissions Table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Commissions_Table extends WP_List_Table {

		/**
		 * Class constructor method
		 *
		 * @return \YITH_WCAF_Commissions_Table
		 * @since 1.0.0
		 */
		public function __construct() {
			// Set parent defaults
			parent::__construct( array(
				'singular' => 'commission',     //singular name of the listed records
				'plural'   => 'commissions',    //plural name of the listed records
				'ajax'     => false             //does this table support ajax?
			) );
		}

		/* === COLUMNS METHODS === */

		/**
		 * Print default column content
		 *
		 * @param $item        mixed Item of the row
		 * @param $column_name string Column name
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_default( $item, $column_name ) {
			if ( isset( $item[ $column_name ] ) ) {
				return esc_html( $item[ $column_name ] );
			} else {
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
			}
		}

		/**
		 * Print a column with checkbox for bulk actions
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				'commissions',  //Let's simply repurpose the table's singular label
				$item['ID']                //The value of the checkbox should be the record's id
			);
		}

		/**
		 * Print column with commission ID
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_id( $item ) {
			$column = sprintf( '<strong>#%d</strong>', $item['ID'] );

			return $column;
		}

		/**
		 * Print column with commission status (enabled/disabled)
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_status( $item ) {
			$human_friendly_status = YITH_WCAF_Commission_Handler()->get_readable_status( $item['status'] );
			$column                = sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $item['status'], $human_friendly_status, $human_friendly_status );

			return $column;
		}

		/**
		 * Print column with order commission
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_order( $item ) {
			$column   = '';
			$order_id = $item['order_id'];
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				return '';
			}

			$customer_tip = array();

			if ( $address = $order->get_formatted_billing_address() ) {
				$customer_tip[] = __( 'Billing:', 'yith-woocommerce-affiliates' ) . ' ' . $address . '<br/><br/>';
			}

			if ( $billing_phone = yit_get_prop( $order, 'billing_phone' ) ) {
				$customer_tip[] = __( 'Tel:', 'yith-woocommerce-affiliates' ) . ' ' . $billing_phone;
			}

			$column .= '<div class="tips" data-tip="' . wc_sanitize_tooltip( implode( "<br/>", $customer_tip ) ) . '">';

			if ( $order->get_user_id() ) {
				$user_info = get_userdata( $order->get_user_id() );
			}

			if ( ! empty( $user_info ) ) {

				$username = '';

				if ( $user_info->first_name || $user_info->last_name ) {
					$username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
				} else {
					$username .= esc_html( ucfirst( $user_info->display_name ) );
				}

			} else {

				$billing_first_name = yit_get_prop( $order, 'billing_first_name' );
				$billing_last_name  = yit_get_prop( $order, 'billing_last_name' );

				if ( $billing_first_name || $billing_last_name ) {
					$username = trim( $billing_first_name . ' ' . $billing_last_name );
				} else {
					$username = __( 'Guest', 'yith-woocommerce-affiliates' );
				}
			}

			$column .= sprintf( _x( '%s by %s', 'Order number by X', 'yith-wcaf' ), '<strong>#' . esc_attr( $order->get_order_number() ) . '</strong>', $username );

			$column .= '</div>';

			return $column;
		}

		/**
		 * Print column with affiliate user details
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_user( $item ) {
			$column    = '';
			$user_id   = $item['user_id'];
			$user_data = get_userdata( $user_id );

			if ( ! $user_data ) {
				return '';
			}

			$user_email = $user_data->user_email;

			$username = '';
			if ( $user_data->first_name || $user_data->last_name ) {
				$username .= esc_html( ucfirst( $user_data->first_name ) . ' ' . ucfirst( $user_data->last_name ) );
			} else {
				$username .= esc_html( ucfirst( $user_data->display_name ) );
			}

			$column .= sprintf( '%s%s<small class="meta email">%s</small>', get_avatar( $user_id, 32 ), $username, $user_email );

			return $column;
		}

		/**
		 * Print column with commission product details
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_product( $item ) {
			$product_id = $item['product_id'];
			$product    = wc_get_product( $product_id );

			if ( ! $product ) {
				return '';
			}

			$column = sprintf( '%s%s', $product->get_image( array( 32, 32 ) ), $product->get_title() );

			return $column;
		}

		/**
		 * Print column with commission category details
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_category( $item ) {
			$product_id = $item['product_id'];
			$categories = wp_get_post_terms( $product_id, 'product_cat' );

			if ( empty( $categories ) ) {
				$column = __( 'N/A', 'yith-woocommerce-affiliates' );
			} else {
				$column_items = array();

				foreach ( $categories as $category ) {
					$column_items[] = $category->name;
				}

				$column = implode( ' | ', $column_items );
			}

			return apply_filters( 'yith_wcaf_category_column', $column, $product_id, 'commissions' );
		}

		/**
		 * Print column with commission rate
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_rate( $item ) {
			$column = '';
			$column .= sprintf( '%.2f%%', number_format( round( $item['rate'], 2 ), 2 ) );

			return $column;
		}

		/**
		 * Print column with commission amount
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_amount( $item ) {
			$column = '';
			$column .= '<strong>' . wc_price( $item['amount'] ) . '</strong>';

			return $column;
		}

		/**
		 * Print column with commission date
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_date( $item ) {
			$column = '';
			$column .= date_i18n( wc_date_format(), strtotime( $item['created_at'] ) );

			return $column;
		}

		/**
		 * Print column with line item total
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_line_item_total( $item ) {
			$exclude_tax = YITH_WCAF_Commission_Handler()->get_option( 'exclude_tax' );
			$order       = wc_get_order( $item['order_id'] );

			if ( ! $order ) {
				return '';
			}

			$line_items = $order->get_items( 'line_item' );

			if ( empty( $line_items ) ) {
				return '';
			}

			$line_item = isset( $line_items[ $item['line_item_id'] ] ) ? $line_items[ $item['line_item_id'] ] : '';

			if ( empty( $line_item ) ) {
				return '';
			}

			$column = '';
			$column .= wc_price( $order->get_item_subtotal( $line_item, 'yes' != $exclude_tax ) * $line_item['qty'], array(
				'currency' => method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency()
			) );

			return $column;
		}

		/**
		 * Print column with commission active payemtns (should be one single element)
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_payments( $item ) {
			$column          = '';
			$active_payments = YITH_WCAF_Payment_Handler()->get_commission_payments( $item['ID'], 'active' );

			if ( empty( $active_payments ) ) {
				$column .= __( 'N/A', 'yith-woocommerce-affiliates' );
			} else {
				$first = true;
				foreach ( $active_payments as $payment ) {
					if ( ! $first ) {
						$column .= ' | ';
					}

					$column .= sprintf( '#%d', $payment['ID'] );

					$first = false;
				}
			}

			return $column;
		}

		/**
		 * Returns columns available in table
		 *
		 * @return array Array of columns of the table
		 * @since 1.0.0
		 */
		public function get_columns() {
			$columns = array(
				'cb'              => '<input type="checkbox" />',
				'id'              => __( 'ID', 'yith-woocommerce-affiliates' ),
				'status'          => sprintf( '<span class="status_head tips" data-tip="%s">%s</span>', __( 'Status', 'yith-woocommerce-affiliates' ), __( 'Status', 'yith-woocommerce-affiliates' ) ),
				'order'           => __( 'Order', 'yith-woocommerce-affiliates' ),
				'user'            => __( 'User', 'yith-woocommerce-affiliates' ),
				'product'         => __( 'Product', 'yith-woocommerce-affiliates' ),
				'category'        => __( 'Category', 'yith-woocommerce-affiliates' ),
				'line_item_total' => __( 'Total', 'yith-woocommerce-affiliates' ),
				'rate'            => __( 'Rate', 'yith-woocommerce-affiliates' ),
				'amount'          => __( 'Commission', 'yith-woocommerce-affiliates' ),
				'payments'        => __( 'Payment', 'yith-woocommerce-affiliates' ),
				'date'            => __( 'Date', 'yith-woocommerce-affiliates' )
			);

			return $columns;
		}

		/**
		 * Returns column to be sortable in table
		 *
		 * @return array Array of sortable columns
		 * @since 1.0.0
		 */
		public function get_sortable_columns() {
			$sortable_columns = array(
				'id'      => array( 'ID', false ),
				'status'  => array( 'status', false ),
				'order'   => array( 'order_id', false ),
				'user'    => array( 'user_id', false ),
				'product' => array( 'product_id', false ),
				'rate'    => array( 'rate', false ),
				'amount'  => array( 'amount', false ),
				'date'    => array( 'created_at', true )
			);

			return $sortable_columns;
		}

		/**
		 * Returns hidden columns for current table
		 *
		 * @return mixed Array of hidden columns
		 * @since 1.0.0
		 */
		public function get_hidden_columns() {
			return get_hidden_columns( get_current_screen() );
		}

		/**
		 * Print table views
		 *
		 * @return array Array with available views
		 * @since 1.0.0
		 */
		public function get_views() {
			$current = isset( $_GET['status'] ) ? $_GET['status'] : 'all';

			$query_arg = array();

			if ( ! empty( $_REQUEST['_product_id'] ) ) {
				$query_arg['product_id'] = $_REQUEST['_product_id'];
			}

			if ( ! empty( $_REQUEST['_user_id'] ) ) {
				$query_arg['user_id'] = $_REQUEST['_user_id'];
			}

			if ( ! empty( $_REQUEST['_from'] ) ) {
				$query_arg['interval']['start_date'] = date( 'Y-m-d 00:00:00', strtotime( $_REQUEST['_from'] ) );
			}

			if ( ! empty( $_REQUEST['_to'] ) ) {
				$query_arg['interval']['end_date'] = date( 'Y-m-d 23:59:59', strtotime( $_REQUEST['_to'] ) );
			}

			$views = array(
				'all' => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( 'status', 'all' ), $current == 'all' ? 'current' : '', __( 'All', 'yith-woocommerce-affiliates' ), YITH_WCAF_Commission_Handler()->per_status_count( 'all', $query_arg ) )
			);

			if ( $pending_count = YITH_WCAF_Commission_Handler()->per_status_count( 'pending', $query_arg ) ) {
				$views['pending'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( 'status', 'pending' ), $current == 'pending' ? 'current' : '', __( 'Pending', 'yith-woocommerce-affiliates' ), $pending_count );
			}

			if ( $pending_payment_count = YITH_WCAF_Commission_Handler()->per_status_count( 'pending-payment', $query_arg ) ) {
				$views['pending-payment'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( 'status', 'pending-payment' ), $current == 'pending-payment' ? 'current' : '', __( 'Pending Payment', 'yith-woocommerce-affiliates' ), $pending_payment_count );
			}

			if ( $paid_count = YITH_WCAF_Commission_Handler()->per_status_count( 'paid', $query_arg ) ) {
				$views['paid'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( 'status', 'paid' ), $current == 'paid' ? 'current' : '', __( 'Paid', 'yith-woocommerce-affiliates' ), $paid_count );
			}

			if ( $not_confirmed_count = YITH_WCAF_Commission_Handler()->per_status_count( 'not-confirmed', $query_arg ) ) {
				$views['not-confirmed'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( 'status', 'not-confirmed' ), $current == 'not-confirmed' ? 'current' : '', __( 'Not Confirmed', 'yith-woocommerce-affiliates' ), $not_confirmed_count );
			}

			if ( $cancelled_count = YITH_WCAF_Commission_Handler()->per_status_count( 'cancelled', $query_arg ) ) {
				$views['cancelled'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( 'status', 'cancelled' ), $current == 'cancelled' ? 'current' : '', __( 'Cancelled', 'yith-woocommerce-affiliates' ), $cancelled_count );
			}

			if ( $refunded_count = YITH_WCAF_Commission_Handler()->per_status_count( 'refunded', $query_arg ) ) {
				$views['refunded'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( 'status', 'refunded' ), $current == 'refunded' ? 'current' : '', __( 'Refunded', 'yith-woocommerce-affiliates' ), $refunded_count );
			}

			if ( $trash_count = YITH_WCAF_Commission_Handler()->per_status_count( 'trash', $query_arg ) ) {
				$views['trash'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( 'status', 'trash' ), $current == 'trash' ? 'current' : '', __( 'Trash', 'yith-woocommerce-affiliates' ), $trash_count );
			}

			return $views;
		}

		/**
		 * Return list of available bulk actions
		 *
		 * @return array Available bulk action
		 * @since 1.0.0
		 */
		public function get_bulk_actions() {
			$actions = array(
				'pay'                     => __( 'Create a payment manually', 'yith-woocommerce-affiliates' ),
				'switch-to-pending'       => __( 'Change status to pending', 'yith-woocommerce-affiliates' ),
				'switch-to-not-confirmed' => __( 'Change status to not confirmed', 'yith-woocommerce-affiliates' ),
				'switch-to-cancelled'     => __( 'Change status to cancelled', 'yith-woocommerce-affiliates' ),
				'switch-to-refunded'      => __( 'Change status to refunded', 'yith-woocommerce-affiliates' ),
			);

			if ( isset( $_GET['status'] ) && $_GET['status'] == 'trash' ) {
				$actions['restore'] = __( 'Restore', 'yith-woocommerce-affiliates' );
				$actions['delete']  = __( 'Delete Permanently', 'yith-woocommerce-affiliates' );
			} else {
				$actions['move-to-trash'] = __( 'Move to Trash', 'yith-woocommerce-affiliates' );
			}

			return $actions;
		}

		/**
		 * Print filters for current table
		 *
		 * @param $which string Top / Bottom
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function extra_tablenav( $which ) {
			if ( $which != 'top' ) {
				return;
			}

			$need_reset = false;

			// retrieve selected product
			$product_id       = isset( $_REQUEST['_product_id'] ) ? $_REQUEST['_product_id'] : false;
			$selected_product = '';

			if ( ! empty( $product_id ) ) {
				$product = wc_get_product( $product_id );

				if ( $product ) {
					$selected_product = '#' . $product_id . ' &ndash; ' . $product->get_title();
				}
			}

			// retrieve selected user
			$user_id       = isset( $_REQUEST['_user_id'] ) ? $_REQUEST['_user_id'] : false;
			$selected_user = '';

			if ( ! empty( $user_id ) ) {
				$user = get_userdata( $user_id );

				if ( $user instanceof WP_User ) {
					$selected_user = $user->user_login . ' (#' . $user_id . ' &ndash; ' . $user->user_email . ')';
				}
			}

			// retrieve other query args
			$from        = isset( $_REQUEST['_from'] ) ? $_REQUEST['_from'] : false;
			$to          = isset( $_REQUEST['_to'] ) ? $_REQUEST['_to'] : false;
			$post_status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : false;

			// set need reset if "Reset" button must be shown
			if ( $product_id || $user_id || $from || $to ) {
				$need_reset = true;
			}

			yit_add_select2_fields( array(
				'class'            => 'wc-product-search',
				'name'             => '_product_id',
				'data-placeholder' => __( 'Select a product', 'yith-woocommerce-affiliates' ),
				'data-selected'    => array( $product_id => $selected_product ),
				'style'            => 'min-width: 200px; vertical-align:middle;',
				'value'            => $product_id
			) );

			yit_add_select2_fields( array(
				'class'            => 'wc-customer-search',
				'name'             => '_user_id',
				'data-placeholder' => __( 'Select a user', 'yith-woocommerce-affiliates' ),
				'data-selected'    => array( $user_id => $selected_user ),
				'style'            => 'min-width: 200px; vertical-align:middle;',
				'value'            => $user_id
			) );
			?>
			<input style="min-width: 200px; vertical-align:middle;" class="date-picker" type="text" name="_from" value="<?php echo esc_attr( $from ) ?>" placeholder="<?php _e( 'From:', 'yith-woocommerce-affiliates' ) ?>"/>
			<input style="min-width: 200px; vertical-align:middle;" class="date-picker" type="text" name="_to" value="<?php echo esc_attr( $to ) ?>" placeholder="<?php _e( 'To:', 'yith-woocommerce-affiliates' ) ?>"/>
			<input type="hidden" name="status" class="post_status_page" value="<?php echo ! empty( $post_status ) ? esc_attr( $post_status ) : 'all'; ?>" />
			<?php
			submit_button( __( 'Filter', 'yith-woocommerce-affiliates' ), 'button', 'filter_action', false, array( 'id' => 'post-query-submit' ) );

			if ( $need_reset ) {
				echo sprintf( '<a href="%s" class="button button-secondary reset-button">%s</a>', esc_url( add_query_arg( array(
					'page' => 'yith_wcaf_panel',
					'tab'  => 'commissions'
				), admin_url( 'admin.php' ) ) ), __( 'Reset', 'yith-woocommerce-affiliates' ) );
			}
		}

		/**
		 * Prepare items for table
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function prepare_items() {
			global $wpdb;

			$query_arg = array();

			if ( ! empty( $_REQUEST['status'] ) && $_REQUEST['status'] != 'all' ) {
				$query_arg['status'] = $_REQUEST['status'];
			} else {
				$query_arg['status__not_in'] = 'trash';
			}

			if ( ! empty( $_REQUEST['_product_id'] ) ) {
				$query_arg['product_id'] = $_REQUEST['_product_id'];
			}

			if ( ! empty( $_REQUEST['_user_id'] ) ) {
				$query_arg['user_id'] = $_REQUEST['_user_id'];
			}

			if ( ! empty( $_REQUEST['_from'] ) ) {
				$query_arg['interval']['start_date'] = date( 'Y-m-d 00:00:00', strtotime( $_REQUEST['_from'] ) );
			}

			if ( ! empty( $_REQUEST['_to'] ) ) {
				$query_arg['interval']['end_date'] = date( 'Y-m-d 23:59:59', strtotime( $_REQUEST['_to'] ) );
			}

			// sets pagination arguments
			$per_page     = $this->get_items_per_page( 'edit_commissions_per_page' );
			$current_page = $this->get_pagenum();
			$total_items  = YITH_WCAF_Commission_Handler()->count_commission( $query_arg );
			$commissions  = YITH_WCAF_Commission_Handler()->get_commissions(
				array_merge(
					apply_filters( 'yith_wcaf_prepare_items_commissions', array(
						'limit'   => $per_page,
						'offset'  => ( ( $current_page - 1 ) * $per_page ),
						'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'created_at',
						'order'   => isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'DESC',
					) ),
					$query_arg
				)
			);

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = $this->get_hidden_columns();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			// retrieve data for table
			$this->items = $commissions;

			// sets pagination args
			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			) );
		}
	}
}
