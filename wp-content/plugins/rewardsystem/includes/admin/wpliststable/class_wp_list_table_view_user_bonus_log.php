<?php

/**
 * View User Bonus Log List Table.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'SRP_View_User_Bonus_Log_List_Table' ) ) {

	/**
	 * SRP_View_User_Bonus_Log_List_Table Class.
	 * */
	class SRP_View_User_Bonus_Log_List_Table extends WP_List_Table {

		/**
		 * Total count of table.
		 * */
		private $total_items;

		/**
		 * Per page count.
		 * */
		private $perpage;

		/**
		 * Database.
		 * */
		private $database;

		/**
		 * Offset.
		 * */
		private $offset;

		/**
		 * User bonus log.
		 * */
		private $user_bonus_log;

		/**
		 * Base URL.
		 * */
		private $base_url;

		/**
		 * Current URL.
		 * */
		private $current_url;

		/**
		 * Prepare the table data to display table based on pagination.
		 * */
		public function prepare_items() {

			global $wpdb;
			$this->database = &$wpdb;
			$this->base_url = get_permalink();

			$this->prepare_user_ids();
			$this->prepare_current_url();
			$this->process_bulk_action();
			$this->get_perpage_count();
			$this->get_current_pagenum();
			$this->get_current_page_items();
			$this->prepare_pagination_args();
			$this->prepare_column_headers();
		}

		/**
		 * Prepare bonus log.
		 * */
		private function prepare_user_ids() {

			global $wpdb;
			$db              = &$wpdb;
			$user_id         = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;
			$user_bonus_data = array();
			if ( $user_id ) {
				$user_bonus_data = $db->get_results( $db->prepare( "SELECT * FROM {$db->prefix}rsrecordpoints WHERE earnedpoints NOT IN(0) AND checkpoints IN('OBP') AND userid='%d' ORDER BY ID DESC", $user_id ), ARRAY_A );
			}

			$this->user_bonus_log = $user_bonus_data;
		}

		/**
		 * Get per page count.
		 * */
		private function get_perpage_count() {
			$this->perpage = 10;
		}

		/**
		 * Prepare pagination.
		 * */
		private function prepare_pagination_args() {
			$this->set_pagination_args(
				array(
					'total_items' => $this->total_items,
					'per_page'    => $this->perpage,
				)
			);
		}

		/**
		 * Get current page number.
		 * */
		private function get_current_pagenum() {
			$this->offset = 10 * ( $this->get_pagenum() - 1 );
		}

		/**
		 * Prepare header columns.
		 * */
		private function prepare_column_headers() {

			$columns  = $this->get_columns();
			$hidden   = $this->get_hidden_columns();
			$sortable = $this->get_sortable_columns();

			$this->_column_headers = array( $columns, $hidden, $sortable );
		}

		/**
		 * Initialize the columns.
		 * */
		public function get_columns() {

			return array(
				'earned_points' => __( 'Earned Points', 'rewardsystem' ),
				'earned_date'   => __( 'Earned Date', 'rewardsystem' ),
				'order_ids'     => __( 'Order IDs', 'rewardsystem' ),
			);
		}

		/**
		 * Initialize the hidden columns.
		 * */
		protected function get_hidden_columns() {
			return array();
		}

		/**
		 * Initialize the sortable columns.
		 * */
		protected function get_sortable_columns() {
			return array();
		}

		/**
		 * Get current URL.
		 * */
		private function prepare_current_url() {

			$pagenum       = $this->get_pagenum();
			$args['paged'] = $pagenum;
			$url           = add_query_arg( $args, $this->base_url );

			$this->current_url = $url;
		}

		/**
		 * Initialize the bulk actions.
		 * */
		protected function get_bulk_actions() {
		}

		/**
		 * Bulk action functionality.
		 * */
		public function process_bulk_action() {
		}

		/**
		 * Display the list of views available on this table.
		 * */
		protected function get_views() {
		}

		/**
		 * Display the extra table nav.
		 * */
		protected function extra_tablenav( $which ) {

			if ( 'top' == $which ) {
				$user_id   = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : 0;
				$user      = get_user_by( 'ID', $user_id );
				$user_name = is_object( $user ) ? $user->user_login : '';
				printf( '<span style="margin-left:7px;">%s</span>', wp_kses_post( __( 'User Name: <b>"' . $user_name . '"</b>', 'rewardsystem' ) ) );
				return;
			}

			$url = remove_query_arg( array( 'srp_action', 'user_id' ), $this->current_url );
			echo wp_kses_post( sprintf( '<a href="%s" class="button">%s</a>', esc_url( $url ), __( 'Click Back', 'rewardsystem' ) ) );
		}

		/**
		 * Get the edit link for status.
		 *
		 * @return string
		 * */
		private function get_edit_link( $args, $label, $class = '' ) {

			$url        = add_query_arg( $args, $this->base_url );
			$class_html = '';
			if ( ! empty( $class ) ) {
				$class_html = sprintf(
					' class="%s"',
					esc_attr( $class )
				);
			}

			return sprintf(
				'<a href="%s"%s>%s</a>',
				esc_url( $url ),
				$class_html,
				$label
			);
		}

		/**
		 * Prepare cb column data.
		 * */
		protected function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="id[]" value="%s" />',
				$item->get_id()
			);
		}

		/**
		 * Prepare each column data.
		 * */
		protected function column_default( $item, $column_name ) {

			switch ( $column_name ) {

				case 'earned_points':
					$earned_points = isset( $item['earnedpoints'] ) ? $item['earnedpoints'] : 0;
					return 0 != $earned_points ? $earned_points : 0;
					break;

				case 'earned_date':
					return ! empty( $item['earneddate'] ) ? SRP_Date_Time::get_wp_format_datetime_from_gmt( gmdate( 'Y-m-d H:i:s', $item['earneddate'] ) ) : '-';
					break;

				case 'order_ids':
					$user_id = ! empty( $item['userid'] ) ? $item['userid'] : '';
					if ( ! $user_id ) {
						return '-';
					}

					$order_id = ! empty( $item['orderid'] ) ? $item['orderid'] : '';
					if ( ! $order_id ) {
						return '-';
					}

					return sprintf( '<a href="#" data-user_id="%d" class="rs-bonus-point-placed-order-ids-view" data-order_id="%d">%s</a>', $user_id, $order_id, __( 'View Order IDs', 'rewardsystem' ) );
					break;
			}
		}

		/**
		 * Initialize the columns.
		 * */
		private function get_current_page_items() {

			if ( empty( $this->user_bonus_log ) ) {
				return;
			}

			$this->total_items = count( $this->user_bonus_log );

			$this->items = array_slice( $this->user_bonus_log, $this->offset, $this->perpage );
		}
	}

}
