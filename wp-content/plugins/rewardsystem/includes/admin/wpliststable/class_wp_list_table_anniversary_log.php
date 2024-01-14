<?php

/**
 * Anniversary Log List Table.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ;
}

if ( ! class_exists( 'SRP_Anniversary_Log_List_Table' ) ) {

	/**
	 * SRP_Anniversary_Log_List_Table Class.
	 * */
	class SRP_Anniversary_Log_List_Table extends WP_List_Table {

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
		 * User ids.
		 * */
		private $user_ids;

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
			$this->user_ids = get_users( array( 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'DESC' ) );
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
				'username_and_email'     => __( 'Username & Email', 'rewardsystem' ),
				'total_earned_points'    => __( 'Total Earned Points', 'rewardsystem' ),
				'account_anniv_details'  => __( 'Account Anniversary Details', 'rewardsystem' ),
				'single_anniv_details'   => __( 'Single Anniversary Details[Custom]', 'rewardsystem' ),
				'multiple_anniv_details' => __( 'Multiple Anniversary Details[Custom]', 'rewardsystem' ),
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

			$pagenum         = $this->get_pagenum();
			$args[ 'paged' ] = $pagenum;
			$url             = add_query_arg( $args, $this->base_url );

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
		 * Get the edit link for status.
		 * 
		 * @return string
		 * */
		private function get_edit_link( $args, $label, $class = '' ) {

			$url        = add_query_arg( $args, $this->base_url );
			$class_html = '';
			if ( ! empty( $class ) ) {
				$class_html = sprintf(
						' class="%s"', esc_attr( $class )
				);
			}

			return sprintf(
					'<a href="%s"%s>%s</a>', esc_url( $url ), $class_html, $label
			);
		}

		/**
		 * Prepare cb column data.
		 * */
		protected function column_cb( $item ) {

			return sprintf(
					'<input type="checkbox" name="id[]" value="%s" />', $item->get_id()
			);
		}

		/**
		 * Prepare each column data.
		 * */
		protected function column_default( $item, $column_name ) {

			$user = get_user_by( 'ID', $item );

			switch ( $column_name ) {

				case 'username_and_email':
					if ( ! is_object( $user ) ) {
						return '-';
					}

					return $user->user_login . ' <br/>[' . $user->user_email . ']';
					break;

				case 'total_earned_points':
					if ( ! is_object( $user ) || ! $user->ID ) {
						return '-';
					}

					global $wpdb;
					$db                  = &$wpdb;
					$total_earned_points = $db->get_var( $db->prepare( "SELECT SUM(earnedpoints) FROM {$db->prefix}rsrecordpoints WHERE earnedpoints NOT IN(0) AND checkpoints IN('AAP','CSAP','CMAP') AND userid='%d'", $user->ID ) );
					return ! empty( $total_earned_points ) ? $total_earned_points : '-';
					break;

				case 'account_anniv_details':
					if ( ! is_object( $user ) || ! $user->ID ) {
						return '-';
					}

					return sprintf( '<a href="#" class="rs-account-anniv-details-popup" data-user_id="%d">%s</a>', $user->ID, __( 'View Details', 'rewardsystem' ) );
					break;

				case 'single_anniv_details':
					if ( ! is_object( $user ) || ! $user->ID ) {
						return '-';
					}

					$single_anniv_date = get_user_meta( $user->ID, 'rs_single_anniversary_date', true );
					if ( ! $single_anniv_date ) {
						return '-';
					}

					return sprintf( '<a href="#" class="rs-single-anniv-details-popup" data-user_id="%d">%s</a>', $user->ID, __( 'View Details', 'rewardsystem' ) );
					break;

				case 'multiple_anniv_details':
					if ( ! is_object( $user ) || ! $user->ID ) {
						return '-';
					}

					$multiple_anniv_dates            = get_user_meta( $user->ID, 'rs_multiple_anniversary_dates', true );
					if ( ! $multiple_anniv_dates ) {
						return '-';
					}

					return sprintf( '<a href="#" class="rs-multiple-anniv-details-popup" data-user_id="%d">%s</a>', $user->ID, __( 'View Details', 'rewardsystem' ) );
					break;
			}
		}

		/**
		 * Initialize the columns.
		 * */
		private function get_current_page_items() {

			if ( empty( $this->user_ids ) ) {
				return;
			}

			// Custom Search.
			$this->user_ids = $this->get_custom_search();

			$this->total_items = count( $this->user_ids );

			$this->items = array_slice( $this->user_ids, $this->offset, $this->perpage );
		}

		/**
		 * Get custom search.
		 * */
		public function get_custom_search() {

			if ( empty( $_REQUEST[ 's' ] ) ) {
				return $this->user_ids;
			}

			$data_store = WC_Data_Store::load( 'customer' );

			$customer_ids = $data_store->search_customers( wc_clean( wp_unslash( $_REQUEST[ 's' ] ) ), '', true, true );

			return array_intersect( $customer_ids, $this->user_ids );
		}
	}

}
