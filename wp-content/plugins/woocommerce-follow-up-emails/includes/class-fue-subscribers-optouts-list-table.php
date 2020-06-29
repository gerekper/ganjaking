<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FUE_Subscribers_Optouts_List_Table extends WP_List_Table {
	private $date_format;

	/**
	 * Create and instance of this list table.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular'  => 'email',
			'plural'    => 'emails',
			'ajax'      => false
		) );

		$this->date_format = get_option('date_format') .' '. get_option('time_format');
	}

	/**
	 * List of columns
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'                => '<input type="checkbox" />',
			'email'             => __('Email', 'follow_up_emails'),
			'date_added'        => __('Date', 'follow_up_emails')
		);
	}

	/**
	 * List of sortable columns
	 * @return array
	 */
	public function get_sortable_columns() {
		return array();
	}

	/**
	 * Get bulk actions
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'restore'   => __( 'Remove from Opt-Out List', 'follow_up_emails' )
		);
	}

	public function prepare_items() {
		global $wpdb;

		$per_page   = 20;
		$columns    = $this->get_columns();
		$hidden     = array();

		$sortable   = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$result     = Follow_Up_Emails::instance()->newsletter->get_excludes( array(
			'search'    => ! empty( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'page'      => $this->get_pagenum(),
			'per_page'  => $per_page
		) );

		$total_rows = $wpdb->get_var("SELECT FOUND_ROWS()");
		$total_pages = ceil( $total_rows / $per_page );

		$this->items = $result;

		// Set the pagination
		$this->set_pagination_args( array(
			'total_items' => $total_rows,
			'per_page'    => $per_page,
			'total_pages' => $total_pages
		) );
	}

	/**
	 * @param  object $item
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="email[]" value="%1$s" />', $item->id );
	}

	public function column_email( $item ) {
		$out = '<strong>'. $item->email .'</strong>';

		// Get actions
		$delete_url = wp_nonce_url( admin_url( 'admin-post.php?action=fue_optout_remove&email='. urlencode( $item->email ) ), 'optout_remove' );
		$actions = array(
			'trash' => '<a class="submitdelete" title="' . esc_attr( __( 'Delete', 'follow_up_emails' ) ) . '" href="'. $delete_url .'">' . __( 'Delete', 'follow_up_emails' ) . '</a>'
		);

		$row_actions = array();

		foreach ( $actions as $action => $link ) {
			$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
		}

		$out .= '<div class="row-actions">' . implode(  ' | ', $row_actions ) . '</div>';

		return $out;
	}

	public function column_date_added( $item ) {
		return date( $this->date_format, strtotime($item->date_added) );
	}

	/**
	 * Display the table
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display() {
		$singular = $this->_args['singular'];
		?>
		<form method="get" id="subscribers_optout_form">
			<input type="hidden" name="page" value="followup-emails-subscribers" />
			<input type="hidden" name="view" value="opt-outs" />
			<?php
			$this->search_box( __('Search', 'follow_up_emails'), 'optout' );
			$this->display_tablenav( 'top' );
			?>
			<table class="wp-list-table <?php echo esc_attr( implode( ' ', $this->get_table_classes() ) ); ?>">
				<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
				</thead>

				<tbody id="the-list"<?php
				if ( $singular ) {
					echo ' data-wp-lists="list:' . esc_attr( $singular ) . '"';
				} ?>>
				<?php $this->display_rows_or_placeholder(); ?>
				</tbody>

				<tfoot>
				<tr>
					<?php $this->print_column_headers( false ); ?>
				</tr>
				</tfoot>

			</table>
			<?php
			$this->display_tablenav( 'bottom' );
			?>
		</form>
		<?php
	}

}
