<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}


if ( ! class_exists( 'YITH_Stripe_Blacklist_Table' ) ) {
	/**
	 *
	 *
	 * @class      class.yith-stripe-blacklist-table
	 * @package    Yithemes
	 * @since      Version 1.1.2
	 * @author     Your Inspiration Themes
	 *
	 */
	class YITH_Stripe_Blacklist_Table extends WP_List_Table {

		/**
		 * Months Dropdown value
		 *
		 * @var array
		 * @since 1.0
		 */
		protected $_months_dropdown = array();

		/**
		 * Construct
		 */
		public function __construct() {

			//Set parent defaults
			parent::__construct( array(
					'singular' => 'ban', //singular name of the listed records
					'plural'   => 'bans', //plural name of the listed records
					'ajax'     => false //does this table support ajax?
				)
			);

			// Months dropdown
			$this->_months_dropdown = $this->months_dropdown_results();
			add_filter( 'months_dropdown_results', array( $this, 'get_months_dropdown' ) );
		}

		/**
		 * Returns columns available in table
		 *
		 * @return array Array of columns of the table
		 * @since 1.1.3
		 */
		public function get_columns() {
			$columns = array(
				'cb'           => '<input type="checkbox" />',
				'ban_status'   => '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'yith-woocommerce-stripe' ) . '">' . esc_attr__( 'Status', 'yith-woocommerce-stripe' ) . '</span>',
				'user'         => __( 'User', 'yith-woocommerce-stripe' ),
				'ip'           => __( 'IP Address', 'yith-woocommerce-stripe' ),
				'order'        => __( 'Order', 'yith-woocommerce-stripe' ),
				'date'         => __( 'Date', 'yith-woocommerce-stripe' ),
				'user_actions' => __( 'Actions', 'yith-woocommerce-stripe' )
			);

			return $columns;
		}

		/**
		 * Sets bulk actions for table
		 *
		 * @return array Array of available actions
		 * @since 1.1.3
		 */
		public function get_bulk_actions() {
			$actions = array(
				'ban'   => __( 'Ban', 'yith-woocommerce-stripe' ),
				'unban' => __( 'Unban', 'yith-woocommerce-stripe' )
			);

			return $actions;
		}

		/**
		 * Extra controls to be displayed between bulk actions and pagination
		 *
		 * @since  1.1.3
		 * @access protected
		 */
		protected function get_views() {
			global $wpdb;

			$views        = array(
				'all'      => __( 'All', 'yith-woocommerce-stripe' ),
				'banned'   => __( 'Banned', 'yith-woocommerce-stripe' ),
				'unbanned' => __( 'Unbanned', 'yith-woocommerce-stripe' ),
			);
			$current_view = $this->get_current_view();

			foreach ( $views as $id => $view ) {
				$href   = esc_url( add_query_arg( 'status', $id ) );
				$class  = $id == $current_view ? 'current' : '';
				$filter = "0', '1";
				if ( 'banned' == $id ) {
					$filter = '0';
				} elseif ( 'unbanned' == $id ) {
					$filter = '1';
				}
				$count        = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->yith_wc_stripe_blacklist WHERE unbanned IN ( %s )", $filter ) );
				$views[ $id ] = sprintf( "<a href='%s' class='%s'>%s <span class='count'>(%d)</span></a>", $href, $class, $view, $count );
			}

			return $views;
		}

		/**
		 * Extra controls to be displayed between bulk actions and pagination
		 *
		 * @return string The view name
		 * @since  1.1.2
		 *
		 */
		public function get_current_view() {
			return ! empty( $_GET['status'] ) ? $_GET['status'] : 'all';
		}

		/**
		 * Prepare items for table
		 *
		 * @return void
		 * @since 1.1.3
		 */
		public function prepare_items() {

			// sets pagination arguments
			$per_page     = $this->get_items_per_page( 'edit_bans_per_page' );
			$current_page = absint( $this->get_pagenum() );

			// blacklist args
			$q = array(
				'status'  => $this->get_current_view(),
				'paged'   => $current_page,
				'number'  => $per_page,
				'm'       => isset( $_REQUEST['m'] ) ? $_REQUEST['m'] : false,
				's'       => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
				'orderby' => 'ban_date',
				'order'   => 'DESC'
			);

			global $wpdb;

			// First let's clear some variables
			$where   = '';
			$join    = '';
			$limits  = '';
			$groupby = '';
			$orderby = '';

			// query parts initializating
			$pieces = array( 'where', 'groupby', 'join', 'orderby', 'limits' );

			// The "m" parameter is meant for months but accepts datetimes of varying specificity
			if ( $q['m'] ) {
				$q['m'] = absint( preg_replace( '|[^0-9]|', '', $q['m'] ) );

				$where .= " AND YEAR(b.ban_date)=" . substr( $q['m'], 0, 4 );
				if ( strlen( $q['m'] ) > 5 ) {
					$where .= " AND MONTH(b.ban_date)=" . substr( $q['m'], 4, 2 );
				}
				if ( strlen( $q['m'] ) > 7 ) {
					$where .= " AND DAYOFMONTH(b.ban_date)=" . substr( $q['m'], 6, 2 );
				}
				if ( strlen( $q['m'] ) > 9 ) {
					$where .= " AND HOUR(b.ban_date)=" . substr( $q['m'], 8, 2 );
				}
				if ( strlen( $q['m'] ) > 11 ) {
					$where .= " AND MINUTE(b.ban_date)=" . substr( $q['m'], 10, 2 );
				}
				if ( strlen( $q['m'] ) > 13 ) {
					$where .= " AND SECOND(b.ban_date)=" . substr( $q['m'], 12, 2 );
				}
			}

			// View
			if ( 'banned' == $q['status'] ) {
				$where .= ' AND unbanned = 0';
			} elseif ( 'unbanned' == $q['status'] ) {
				$where .= ' AND unbanned = 1';
			}

			// Search
			if ( $q['s'] ) {
				// added slashes screw with quote grouping when done early, so done later
				$q['s'] = stripslashes( $q['s'] );
				// there are no line breaks in <input /> fields
				$q['s'] = str_replace( array( "\r", "\n" ), '', $q['s'] );

				// user
				$join  .= " JOIN $wpdb->users u ON u.ID = b.user_id";
				$join  .= " JOIN $wpdb->usermeta um ON um.user_id = b.user_id";
				$join  .= " JOIN $wpdb->usermeta um2 ON um2.user_id = b.user_id";
				$join  .= " JOIN $wpdb->usermeta um3 ON um3.user_id = b.user_id";
				$where .= " AND um.meta_key = 'first_name'";
				$where .= " AND um2.meta_key = 'last_name'";

				// order
				$join .= strpos( $join, "$wpdb->posts o" ) === false ? " JOIN $wpdb->posts o ON o.ID = b.order_id" : '';

				$s = array(
					// search by username
					$wpdb->prepare( "u.user_login LIKE %s", "%{$q['s']}%" ),
					$wpdb->prepare( "u.user_nicename LIKE %s", "%{$q['s']}%" ),
					$wpdb->prepare( "u.user_email LIKE %s", "%{$q['s']}%" ),
					$wpdb->prepare( "um.meta_value LIKE %s", "%{$q['s']}%" ),
					$wpdb->prepare( "um2.meta_value LIKE %s", "%{$q['s']}%" ),
					// search by ip address
					$wpdb->prepare( "b.ip = %s", $q['s'] ),
					// search by order
					$wpdb->prepare( "o.ID = %s", $q['s'] ),
				);

				$where .= ' AND ( ' . implode( ' OR ', $s ) . ' )';
			}

			// Paging
			if ( ! empty( $q['paged'] ) && ! empty( $q['number'] ) ) {
				$page = absint( $q['paged'] );
				if ( ! $page ) {
					$page = 1;
				}

				if ( empty( $q['offset'] ) ) {
					$pgstrt = absint( ( $page - 1 ) * $q['number'] ) . ', ';
				} else { // we're ignoring $page and using 'offset'
					$q['offset'] = absint( $q['offset'] );
					$pgstrt      = $q['offset'] . ', ';
				}
				$limits = 'LIMIT ' . $pgstrt . $q['number'];
			}

			// Order
			if ( ! empty( $q['paged'] ) && ! empty( $q['number'] ) ) {
				$orderby = "ORDER BY {$q['orderby']} {$q['order']}";
			}

			$clauses = compact( $pieces );

			$where   = isset( $clauses['where'] ) ? $clauses['where'] : '';
			$join    = isset( $clauses['join'] ) ? $clauses['join'] : '';
			$groupby = isset( $clauses['groupby'] ) ? $clauses['groupby'] : '';
			$orderby = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
			$limits  = isset( $clauses['limits'] ) ? $clauses['limits'] : '';

			$bans        = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->yith_wc_stripe_blacklist b $join WHERE 1=1 $where $groupby $orderby $limits" );
			$total_items = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

			// sets columns headers
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			$items = array();

			foreach ( $bans as $ban ) {
				$items[ $ban->ID ] = $ban;
			}

			// retrieve data for table
			$this->items = $items;

			// sets pagination args
			$this->set_pagination_args(
				array(
					'total_items' => $total_items,
					'per_page'    => $per_page,
					'total_pages' => ceil( $total_items / $per_page )
				)
			);
		}

		/**
		 * Print the columns information
		 *
		 * @param $rec
		 * @param $column_name
		 *
		 * @return string
		 * @since 1.1.3
		 */
		public function column_default( $rec, $column_name ) {
			switch ( $column_name ) {

				case 'ban_status':
					$display = $rec->unbanned == 1 ? __( 'Unbanned', 'yith-woocommerce-stripe' ) : __( 'Banned', 'yith-woocommerce-stripe' );
					$icon    = $rec->unbanned == 1 ? 'unbanned' : 'cancelled';

					return "<mark data-tip='{$display}' class='$icon tips'>{$display}</mark>";
					break;

				case 'user' :
					if ( empty( $rec->user_id ) ) {
						return __( 'Unknown', 'yith-woocommerce-stripe' );
					}

					$user_info = get_user_by( 'id', $rec->user_id );

					if ( ! empty( $user_info ) ) {

						$current_user_can = current_user_can( 'edit_users' ) || get_current_user_id() == $user_info->ID;

						$username = $current_user_can ? '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">' : '';

						if ( $user_info->first_name || $user_info->last_name ) {
							$username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
						} else {
							$username .= esc_html( ucfirst( $user_info->display_name ) );
						}

						if ( $current_user_can ) {
							$username .= '</a>';
						}

						$user = sprintf( '<a href="user-edit.php?user_id=%d">%s</a> - <a href="mailto:%3$s">%3$s</a>', $user_info->ID, $username, $user_info->user_email );
					} else {
						$username = __( 'Guest', 'woocommerce' );

						$user = sprintf( '<i>%s</i>', $username );
					}

					return sprintf( '<div class="tips" data-tip="%s">%s</div>', $rec->ua, $user );

					break;

				case 'order':
					/** @var WC_Order $order */
					$order = wc_get_order( $rec->order_id );

					if ( empty( $order ) ) {
						return null;
					}

					$order_number = '<strong>#' . esc_attr( $order->get_order_number() ) . '</strong>';
					$order_uri    = '<a href="' . admin_url( 'post.php?post=' . yit_get_order_id( $order ) . '&action=edit' ) . '">' . $order_number . '</a>';

					return $order_uri;

					break;

				case 'ip':
					printf( '<a href="http://whois.domaintools.com/%1$s" target="_blank">%1$s</a>', $rec->ip );
					break;

				case 'date':
					$date   = $rec->ban_date;
					$t_time = date_i18n( __( 'Y/m/d g:i:s A' ), mysql2date( 'U', $date ) );
					$m_time = $date;
					$time   = mysql2date( 'G', $date );

					$time_diff = time() - $time;

					if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
						$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
					} else {
						$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
					}

					echo '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
					break;

				case 'user_actions':
					$unban_url = wp_nonce_url( add_query_arg( array( 'page'   => $_GET['page'],
																	 'tab'    => $_GET['tab'],
																	 'action' => 'unban',
																	 'id'     => $rec->ID
					), admin_url( 'admin.php' ) ), 'stripe_blacklist_action' );
					$ban_url   = wp_nonce_url( add_query_arg( array( 'page'   => $_GET['page'],
																	 'tab'    => $_GET['tab'],
																	 'action' => 'ban',
																	 'id'     => $rec->ID
					), admin_url( 'admin.php' ) ), 'stripe_blacklist_action' );

					if ( $rec->unbanned == 0 ) {
						printf( '<a class="button tips complete" href="%1$s" data-tip="%2$s">%2$s</a>', $unban_url, __( 'Unban', 'yith-woocommerce-stripe' ) );
					} elseif ( $rec->unbanned == 1 ) {
						printf( '<a class="button tips" href="%1$s" data-tip="%2$s">%2$s</a>', $ban_url, __( 'Ban', 'yith-woocommerce-stripe' ) );
					}

					break;
			}

			return null;
		}

		/**
		 * Prints column cb
		 *
		 * @param $rec Object Item to use to print CB record
		 *
		 * @return string
		 * @since 1.1.3
		 */
		public function column_cb( $rec ) {
			return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				$this->_args['plural'], //Let's simply repurpose the table's plural label
				$rec->ID //The value of the checkbox should be the record's id
			);
		}

		/**
		 * Display the search box.
		 *
		 * @param string $text     The search button text
		 * @param string $input_id The search input id
		 *
		 * @since  3.1.0
		 * @access public
		 *
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
			_e( 'No bans found.', 'yith-woocommerce-stripe' );
		}

		/**
		 * Extra controls to be displayed between bulk actions and pagination
		 *
		 * @param string $which
		 *
		 * @since 1.1.3
		 *
		 */
		protected function extra_tablenav( $which ) {
			if ( 'top' == $which ) {
				?>
				<div class="alignleft actions"><?php

				$this->months_dropdown( 'bans' );
				submit_button( __( 'Filter' ), 'button', 'filter_action', false, array( 'id' => 'ban-query-submit' ) );

				?></div><?php
			}
		}

		/**
		 * Month Dropdown filter
		 *
		 * @return array
		 * @since 1.1.3
		 *
		 */
		public function months_dropdown_results() {
			global $wpdb;

			$current_view = $this->get_current_view();
			$where        = 'WHERE 1=1 ';

			$months = $wpdb->get_results( "
                SELECT DISTINCT YEAR( ban_date ) AS year, MONTH( ban_date ) AS month
                FROM $wpdb->yith_wc_stripe_blacklist
                ORDER BY ban_date DESC
            " );

			if ( empty( $months ) ) {
				$months           = array();
				$months[0]        = new stdClass();
				$months[0]->year  = date( 'Y' );
				$months[0]->month = date( 'n' );
			}

			return $months;
		}

		/**
		 * Get month dropdown protected var
		 *
		 * @return array
		 * @since  1.1.3
		 * @access protected
		 *
		 */
		public function get_months_dropdown() {
			return $this->_months_dropdown;
		}
	}
}

