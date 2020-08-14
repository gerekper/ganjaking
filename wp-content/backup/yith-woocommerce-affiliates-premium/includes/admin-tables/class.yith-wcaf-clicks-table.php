<?php
/**
 * Clicks Table class
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

if ( ! class_exists( 'YITH_WCAF_Clicks_Table' ) ) {
	/**
	 * WooCommerce Clicks Table
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Clicks_Table extends WP_List_Table {
		/**
		 * Class constructor method
		 *
		 * @return \YITH_WCAF_Clicks_Table
		 * @since 1.0.0
		 */
		public function __construct() {
			// Set parent defaults
			parent::__construct( array(
				'singular' => 'click',         //singular name of the listed records
				'plural'   => 'clicks',        //plural name of the listed records
				'ajax'     => false            //does this table support ajax?
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
		 * Print column with affiliate user details
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_referrer( $item ) {
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

			$column .= sprintf( '%s<a href="%s">%s</a><small class="meta email"><a href="mailto:%s">%s</a></small>', get_avatar( $user_id, 32 ), add_query_arg( '_user_id', $user_id ), $username, $user_email, $user_email );

			return $column;
		}

		/**
		 * Print column with click status (converted/not converted)
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_status( $item ) {
			$status                = ! empty( $item['order_id'] ) ? 'converted' : 'not-converted';
			$human_friendly_status = ! empty( $item['order_id'] ) ? __( 'Converted', 'yith-woocommerce-affiliates' ) : __( 'Not Converted', 'yith-woocommerce-affiliates' );

			$column = sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $status, $human_friendly_status, $human_friendly_status );

			return $column;
		}

		/**
		 * Print column with visited link
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_link( $item ) {
			$column = sprintf( '%s<small class="meta">%s %s</small>', $item['link'], __( 'Guest IP:', 'yith-woocommerce-affiliates' ), $item['IP'] );

			return $column;
		}

		/**
		 * Print column with user origin
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_origin( $item ) {
			$column = '';

			if ( ! empty( $item['origin'] ) && ! empty( $item['origin_base'] ) ) {
				$column .= sprintf( '%s<small class="meta">%s</small>', $item['origin_base'], $item['origin'] );
			} else {
				$column .= __( 'N/A', 'yith-woocommerce-affiliates' );
			}

			return $column;
		}

		/**
		 * Print column with click date
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_date( $item ) {
			$column = '';
			$column .= date_i18n( wc_date_format(), strtotime( $item['click_date'] ) );

			return $column;
		}

		/**
		 * Print column with order details (only for converted clicks)
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
				return __( 'N/A', 'yith-woocommerce-affiliates' );
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

				$username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

				if ( $user_info->first_name || $user_info->last_name ) {
					$username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
				} else {
					$username .= esc_html( ucfirst( $user_info->display_name ) );
				}

				$username .= '</a>';

			} else {
				$billing_first_name = yit_get_prop( $order, 'billing_first_name' );
				$billing_last_name  = yit_get_prop( $order, 'billing_last_name' );

				if ( $billing_first_name || $billing_last_name ) {
					$username = trim( $billing_first_name . ' ' . $billing_last_name );
				} else {
					$username = __( 'Guest', 'yith-woocommerce-affiliates' );
				}
			}

			$column .= sprintf( _x( '%s by %s', 'Order number by X', 'yith-wcaf' ), '<a href="' . admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) . '"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>', $username );

			if ( $billing_email = yit_get_prop( $order, 'billing_email' ) ) {
				$column .= '<small class="meta email"><a href="' . esc_url( 'mailto:' . $billing_email ) . '">' . esc_html( $billing_email ) . '</a></small>';
			}

			$column .= '</div>';

			return $column;
		}

		/**
		 * Print column with time passed between click and sale
		 *
		 * @param $item mixed Current item row
		 *
		 * @return string Column content
		 * @since 1.0.0
		 */
		public function column_conv_time( $item ) {
			$column = '';

			if ( ! empty( $item['conv_date'] ) ) {
				$column = human_time_diff( strtotime( $item['click_date'] ), strtotime( $item['conv_date'] ) );
			} else {
				$column = __( 'N/A', 'yith-woocommerce-affiliates' );
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
				'status'    => sprintf( '<span class="status_head tips" data-tip="%s">%s</span>', __( 'Converted', 'yith-woocommerce-affiliates' ), __( 'Converted', 'yith-woocommerce-affiliates' ) ),
				'referrer'  => __( 'Referrer', 'yith-woocommerce-affiliates' ),
				'order'     => __( 'Order', 'yith-woocommerce-affiliates' ),
				'link'      => __( 'Followed URL', 'yith-woocommerce-affiliates' ),
				'origin'    => __( 'Origin URL', 'yith-woocommerce-affiliates' ),
				'date'      => __( 'Date', 'yith-woocommerce-affiliates' ),
				'conv_time' => __( 'Conversion time', 'yith-woocommerce-affiliates' )
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
				'referrer'  => array( 'referrer', false ),
				'status'    => array( 'order_id', false ),
				'link'      => array( 'link', false ),
				'origin'    => array( 'origin', false ),
				'date'      => array( 'click_date', true ),
				'order'     => array( 'order_id', false ),
				'conv_time' => array( 'conv_time', false )
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
			$tab_url = add_query_arg( array(
				'page' => 'yith_wcaf_panel',
				'tab'  => 'clicks'
			), admin_url( 'admin.php' ) );

			return array(
				'all'       => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'all', $tab_url ) ), $current == 'all' ? 'current' : '', __( 'All', 'yith-woocommerce-affiliates' ), YITH_WCAF_Click_Handler()->count_hits() ),
				'converted' => sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_url( add_query_arg( 'status', 'converted', $tab_url ) ), $current == 'converted' ? 'current' : '', __( 'Converted', 'yith-woocommerce-affiliates' ), YITH_WCAF_Click_Handler()->count_hits( array( 'converted' => 'yes' ) ) )
			);
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

			// retrieve selected user
			$user_id       = isset( $_REQUEST['_user_id'] ) ? $_REQUEST['_user_id'] : false;
			$selected_user = '';

			if ( ! empty( $user_id ) ) {
				$user = get_userdata( $user_id );

				if ( ! is_wp_error( $user ) ) {
					$selected_user = $user->user_login . ' (#' . $user_id . ' &ndash; ' . $user->user_email . ')';
				}
			}

			// retrieve other query args
			$from        = isset( $_REQUEST['_from'] ) ? $_REQUEST['_from'] : false;
			$to          = isset( $_REQUEST['_to'] ) ? $_REQUEST['_to'] : false;
			$post_status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : false;

			// set need reset if "Reset" button must be shown
			if ( $user_id || $from || $to ) {
				$need_reset = true;
			}

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
					'tab'  => 'clicks'
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

			if ( ! empty( $_REQUEST['status'] ) && $_REQUEST['status'] == 'converted' ) {
				$query_arg['converted'] = 'yes';
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
			$per_page     = $this->get_items_per_page( 'edit_clicks_per_page' );
			$current_page = $this->get_pagenum();
			$total_items  = YITH_WCAF_Click_Handler()->count_hits( $query_arg );
			$hits         = YITH_WCAF_Click_Handler()->get_hits(
				array_merge(
					array(
						'limit'   => $per_page,
						'offset'  => ( ( $current_page - 1 ) * $per_page ),
						'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'click_date',
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