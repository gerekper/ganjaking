<?php
/**
 * Payments Table class
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

if ( ! class_exists( 'YITH_WCAF_Payments_Table' ) ) {
	/**
	 * WooCommerce Payments Table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Payments_Table extends WP_List_Table {
		/**
		 * Class constructor method
		 *
		 * @return \YITH_WCAF_Payments_Table
		 * @since 1.0.0
		 */
		public function __construct() {
			// Set parent defaults
			parent::__construct( array(
				'singular' => 'payment',         //singular name of the listed records
				'plural'   => 'payments',        //plural name of the listed records
				'ajax'     => false              //does this table support ajax?
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
				return apply_filters( 'yith_wcaf_payment_table_column_default', print_r( $item, true ), $item, $column_name ); //Show the whole array for troubleshooting purposes
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
				'payments',  //Let's simply repurpose the table's singular label
				$item['ID']                //The value of the checkbox should be the record's id
			);
		}

		/**
		 * Print a column with payment ID
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
		 * Print a column with payment status
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_status( $item ) {
			$human_friendly_status = ucfirst( str_replace( '-', ' ', $item['status'] ) );
			$column                = sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $item['status'], $human_friendly_status, $human_friendly_status );

			return $column;
		}

		/**
		 * Print a column with payment related Affiliate
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_affiliate( $item ) {
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

			$column .= sprintf( '%s<a href="%s">%s</a><small class="meta email"><a href="mailto:%s">%s</a></small>', get_avatar( $user_id, 32 ), add_query_arg( '_affiliate_id', $item['affiliate_id'] ), $username, $user_email, $user_email );

			return $column;
		}

		/**
		 * Print a column with payment email
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_payment_email( $item ) {
			$column = '';

			if ( ! empty( $item['payment_email'] ) ) {
				$column = sprintf( '<a href="mailto:%s">%s</a>', $item['payment_email'], $item['payment_email'] );
			} else {
				$column = __( 'N/A', 'yith-woocommerce-affiliates' );
			}

			return $column;
		}

		/**
		 * Print a column with payment amount
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
		 * Print a column with payment related commissions
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_commissions( $item ) {
			$column      = '';
			$commissions = YITH_WCAF_Payment_Handler()->get_payment_commissions( $item['ID'] );

			if ( empty( $commissions ) ) {
				$column .= __( 'N/A', 'yith-woocommerce-affiliates' );
			} else {
				$first = true;

				foreach ( $commissions as $commission ) {
					$column .= $first ? '' : ' | ';
					$column .= sprintf( '<a href="%s">%d</a>', esc_url( add_query_arg( array(
						'page'          => 'yith_wcaf_panel',
						'tab'           => 'commissions',
						'commission_id' => $commission['ID']
					), admin_url( 'admin.php' ) ) ), $commission['ID'] );

					$first = false;
				}
			}

			return $column;
		}

		/**
		 * Print a column with payment creation date
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_created_at( $item ) {
			$column = '';
			$column .= date_i18n( wc_date_format(), strtotime( $item['created_at'] ) );

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
				'cb'            => '<input type="checkbox" />',
				'id'            => __( 'ID', 'yith-woocommerce-affiliates' ),
				'status'        => sprintf( '<span class="status_head tips" data-tip="%s">%s</span>', __( 'Status', 'yith-woocommerce-affiliates' ), __( 'Status', 'yith-woocommerce-affiliates' ) ),
				'affiliate'     => __( 'Affiliate ', 'yith-woocommerce-affiliates' ),
				'payment_email' => __( 'Payment email', 'yith-woocommerce-affiliates' ),
				'amount'        => __( 'Amount', 'yith-woocommerce-affiliates' ),
				'created_at'    => __( 'Created at', 'yith-woocommerce-affiliates' )
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
				'status'        => array( 'status', false ),
				'id'            => array( 'id', false ),
				'affiliate'     => array( 'user_login', false ),
				'payment_email' => array( 'payment_email', false ),
				'amount'        => array( 'amount', false ),
				'created_at'    => array( 'created_at', true ),
				'completed_at'  => array( 'completed_at', true )
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
		 * Return list of available bulk actions
		 *
		 * @return array Available bulk action
		 * @since 1.0.0
		 */
		public function get_bulk_actions() {
			$actions = array(
				'switch-to-completed' => __( 'Change status to completed', 'yith-woocommerce-affiliates' ),
				'switch-to-on-hold'   => __( 'Change status to on hold', 'yith-woocommerce-affiliates' ),
				'switch-to-cancelled' => __( 'Change status to cancelled', 'yith-woocommerce-affiliates' ),
				'delete'              => __( 'Delete', 'yith-woocommerce-affiliates' )
			);

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

			$affiliate_id  = isset( $_REQUEST['_affiliate_id'] ) ? $_REQUEST['_affiliate_id'] : false;
			$selected_user = '';
			$user_id       = false;

			if ( ! empty( $affiliate_id ) ) {
				$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id );

				if ( ! empty( $affiliate ) ) {
					$user_id = $affiliate['user_id'];
					$user    = get_userdata( $user_id );

					if ( ! is_wp_error( $user ) ) {
						$selected_user = $user->user_login . ' (#' . $user_id . ' &ndash; ' . $user->user_email . ')';
					}
				}
			}

			$from = isset( $_REQUEST['_from'] ) ? $_REQUEST['_from'] : false;
			$to   = isset( $_REQUEST['_to'] ) ? $_REQUEST['_to'] : false;
			$post_status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : false;

			if ( $user_id || $from || $to ) {
				$need_reset = true;
			}

			yit_add_select2_fields( array(
				'class'            => 'yith-users-select wc-product-search',
				'name'             => '_affiliate_id',
				'data-action'      => 'json_search_affiliates',
				'data-placeholder' => __( 'Select an affiliate', 'yith-woocommerce-affiliates' ),
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
					'tab'  => 'payments'
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
			}

			if ( ! empty( $_REQUEST['_affiliate_id'] ) ) {
				$query_arg['affiliate_id'] = $_REQUEST['_affiliate_id'];
			}

			if ( ! empty( $_REQUEST['_from'] ) ) {
				$query_arg['interval']['start_date'] = date( 'Y-m-d 00:00:00', strtotime( $_REQUEST['_from'] ) );
			}

			if ( ! empty( $_REQUEST['_to'] ) ) {
				$query_arg['interval']['end_date'] = date( 'Y-m-d 23:59:59', strtotime( $_REQUEST['_to'] ) );
			}

			// sets pagination arguments
			$per_page     = $this->get_items_per_page( 'edit_payments_per_page' );
			$current_page = $this->get_pagenum();
			$total_items  = YITH_WCAF_Payment_Handler()->count_payments( $query_arg );
			$hits         = YITH_WCAF_Payment_Handler()->get_payments(
				array_merge(
					array(
						'limit'   => $per_page,
						'offset'  => ( ( $current_page - 1 ) * $per_page ),
						'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'created_at',
						'order'   => isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'DESC',
					),
					$query_arg
				)
			);

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = $this->get_hidden_columns();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			// retrieve data for table
			$this->items = $hits;

			// sets pagination args
			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			) );
		}
	}
}