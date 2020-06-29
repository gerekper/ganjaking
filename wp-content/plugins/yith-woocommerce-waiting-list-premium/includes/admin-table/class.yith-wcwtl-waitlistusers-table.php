<?php
/**
 * Admin waiting list users data table class
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
 * @class   YITH_WCWTL_WaitlistUsers_Table
 * @since   1.0.0
 * @author  Yithemes
 *
 * @package YITH Woocommerce Waiting List
 */
if ( ! class_exists( 'YITH_WCWTL_WaitlistUsers_Table' ) ) {

	class YITH_WCWTL_WaitlistUsers_Table extends WP_List_Table {
		/**
		 * Construct
		 */
		public function __construct() {

			//Set parent defaults
			parent::__construct( array(
					'singular' => 'waitlist-user', //singular name of the listed records
					'plural'   => 'waitlist-users', //plural name of the listed records
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
				'cb'       => '<input type="checkbox" />',
				'user'     => __( 'email', 'yith-woocommerce-waiting-list' ),
				'customer' => __( 'Is Customer', 'yith-woocommerce-waiting-list' ),
				'actions'  => __( 'Actions', 'yith-woocommerce-waiting-list' ),
			);

			return $columns;
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
			switch ( $column_name ) {

				case 'user':
					return $rec['email'];
					break;

				case 'customer':
					$user = get_user_by( 'email', $rec['email'] );

					if ( $user ) {
						$class = 'enabled';
						$tip   = __( 'Yes', 'yith-woocommerce-waiting-list' );
					} else {
						$class = 'disabled';
						$tip   = __( 'No', 'yith-woocommerce-waiting-list' );
					}

					return sprintf( '<span class="status-%s tips" data-tip="%s">%s</span>', $class, $tip, $tip );
					break;

				case 'actions':
					$delete_query_args = array(
						'page'       => $_GET['page'],
						'tab'        => $_GET['tab'],
						'view'       => 'users',
						'action'     => 'remove_user',
						'id'         => $_GET['id'],
						'user_email' => $rec['email'],
					);
					$delete_url        = add_query_arg( $delete_query_args, admin_url( 'admin.php' ) );
					$actions_button    = '<a href="' . esc_url( $delete_url ) . '" class="button">' . esc_html__( 'Remove User', 'yith-woocommerce-waiting-list' ) . '</a>';

					$mail_query_args = array(
						'page'   => $_GET['page'],
						'tab'    => $_GET['tab'],
						'view'   => 'users',
						'action' => 'send_mail',
						'id'     => $_GET['id'],
						'user'   => $rec['email'],
					);
					$mail_url        = add_query_arg( $mail_query_args, admin_url( 'admin.php' ) );
					$actions_button  .= '<a href="' . esc_url( $mail_url ) . '" class="send_mail button">' . esc_html__( 'Send Email', 'yith-woocommerce-waiting-list' ) . '</a>';

					return $actions_button;
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
			return sprintf( '<input type="checkbox" name="user_email[]" value="%s" />', $rec['email'] );
		}

		/**
		 * Sets bulk actions for table
		 *
		 * @since 1.1.3
		 * @return array Array of available actions
		 */
		public function get_bulk_actions() {
			$actions = array(
				'remove_user' => __( 'Remove User', 'yith-woocommerce-waiting-list' ),
			);

			return apply_filters( 'yith_wcwtl_waitlistusers_table_bulk_actions', $actions );
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
				'number' => 20,
				'list'   => isset( $_GET['id'] ) ? $_GET['id'] : '',
				's'      => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
			) );

			global $wpdb;

			// query parts initializating
			$where = $wpdb->prepare( "meta_key = '%s' AND post_id = '%s'", array( '_yith_wcwtl_users_list', $q['list'] ) );
			$paged = isset( $_GET['paged'] ) ? $q['number'] * ( intval( $_GET['paged'] ) - 1 ) : 0;

			$items = $wpdb->get_results( "SELECT meta_value AS email FROM {$wpdb->postmeta} WHERE $where", ARRAY_A );
			// unserialize it
			$unserialized_items = array();
			$total_items        = 0;
			foreach ( $items as $item ) {
				foreach ( $item as $key => $s ) {
					$s = maybe_unserialize( $s );
					if ( $q['s'] ) {
						$q['s'] = stripslashes( $q['s'] );
						$search = str_replace( array( "\r", "\n" ), '', $q['s'] );
						$s      = array_filter( $s, function ( $var ) use ( $search ) {
							return FALSE !== strpos( $var, $search );
						} );
					}
					$total_items = count( $s );
					// slice array for pagination
					$s = array_slice( $s, $paged, $q['number'] );
					foreach ( $s as $email ) {
						$unserialized_items[] = array( $key => $email );
					}
				}
			}

			// sets columns headers
			$columns               = $this->get_columns();
			$this->_column_headers = array( $columns, array(), array() );

			// retrieve data for table
			$this->items = $unserialized_items;

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
			esc_html_e( 'No user found for this list.', 'yith-woocommerce-waiting-list' );
		}

		/**
		 * Extra controls to be displayed between bulk actions and pagination
		 *
		 * @since 1.5.3
		 * @param string $which
		 */
		protected function extra_tablenav( $which ) {

			if ( 'top' == $which ) {
				?>
				<div class="alignleft actions"><?php
				submit_button( __( 'Export Users' ), 'button', 'export_action', false, array( 'id' => 'export-action-submit' ) );
				?></div><?php
			}
		}
	}
}