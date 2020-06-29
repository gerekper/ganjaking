<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FUE_Subscribers_List_Table extends WP_List_Table {

	public $newsletter;
	public $date_format;

	/**
	 * Create and instance of this list table.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular'  => 'subscriber',
			'plural'    => 'subscribers',
			'ajax'      => false
		) );

		$this->newsletter  = Follow_Up_Emails::instance()->newsletter;
		$this->date_format = get_option( 'date_format') . ' '. get_option( 'time_format' );
	}

	/**
	 * List of columns
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'         => '<input type="checkbox" />',
			'email'      => __( 'Email Address', 'follow_up_emails' ),
			'lists'      => __( 'Subscriptions', 'follow_up_emails' ),
			'name'       => __( 'Full name', 'follow_up_emails' ),
			'date_added' => __( 'Signup Date/Time', 'follow_up_emails' ),
		);
	}

	/**
	 * List of sortable columns
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'email'     => array('email', true),
			'date_added'=> array('date_added', false)
		);
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		global $wp_query;

		if ( 'top' == $which ) {
			?>
			<!--
			<div class="alignleft actions">
				<button type="button" class="button button-primary btn-new-list">Create New List</button>
			</div>-->
			<div class="alignleft actions">
				<select id="filter_list">
					<?php $selected_filter = isset($_GET['list']) ? sanitize_text_field( wp_unslash( $_GET['list'] ) ): '-1'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
					<option value="-1" <?php selected( $selected_filter, -1 ); ?>><?php esc_html_e('Filter by list', 'follow_up_emails'); ?></option>
					<option value="" <?php selected( $selected_filter, '' ); ?>><?php esc_html_e('Uncategorized', 'follow_up_emails'); ?></option>
					<?php foreach ( $this->newsletter->get_lists() as $list ): ?>
						<option value="<?php echo esc_attr( $list['id'] ); ?>" <?php selected( $selected_filter, $list['id'] ); ?>><?php echo esc_html( $list['list_name'] ); ?></option>
					<?php endforeach; ?>
				</select>
				<button type="button" class="button run-filter"><?php esc_html_e('Filter', 'follow_up_emails'); ?></button>
			</div>
		<?php
		}
	}

	/**
	 * Get bulk actions
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'move'      => __( 'Move subscriber to existing list', 'follow_up_emails' ),
			'new'       => __( 'Create new list and move subscriber', 'follow_up_emails' ),
			'delete'    => __( 'Remove subscriber and email address', 'follow_up_emails' ),
			'rename'    => __( 'Rename subscriber', 'follow_up_emails' ),
		);
	}

	public function prepare_items() {
		$per_page   = 20;
		$columns    = $this->get_columns();
		$hidden     = array();

		$sortable   = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$current_page = $this->get_pagenum();

		if ( !isset( $_GET['list'] ) || $_GET['list'] == -1 ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$list_filter = false;
		} else {
			if ( empty( $_GET['list'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$list_filter = '';
			} else {
				$list_filter = absint( $_GET['list'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
		}

		$subscribers = $this->newsletter->get_subscribers( array(
			'search'    => !empty( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'list'      => $list_filter,
			'orderby'   => isset($_GET['orderby']) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ): 'date_added', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'order'     => isset($_GET['order']) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ): 'DESC', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'length'    => $per_page,
			'page'      => $current_page
		) );

		$total_pages    = ceil( $this->newsletter->found_subscribers / $per_page );
		$this->items    = $subscribers;

		// Set the pagination
		$this->set_pagination_args( array(
			'total_items' => $this->newsletter->found_subscribers,
			'per_page'    => $per_page,
			'total_pages' => $total_pages
		) );
	}

	/**
	 * @param  array $subscriber
	 * @return string
	 */
	public function column_cb( $subscriber ) {
		return sprintf( '<input type="checkbox" name="email[]" value="%1$s" />', $subscriber['id'] );
	}

	/**
	 * @param array $subscriber
	 * @return string
	 */
	public function column_email( $subscriber ) {
		return $subscriber['email'];
	}

	/**
	 * @param  array $subscribes
	 * @return string
	 */
	public function column_name( $subscriber ) {
		return $subscriber['first_name'] . ' ' . $subscriber['last_name'];
	}

	/**
	 * @param array $subscriber
	 * @return string
	 */
	public function column_lists( $subscriber ) {
		$subscriber_lists = '';

		foreach ( $subscriber['lists'] as $list ) {
			$subscriber_lists .= '<div class="list"><a href="admin.php?page=followup-emails-subscribers&list='. $list['id'] .'" class="">'. esc_html( $list['name'] ) .'</a> <a href="#" data-list="'. $list['id'] .'" data-subscriber="'. $subscriber['id'] .'" class="remove-from-list dashicons dashicons-dismiss">&nbsp;</a></div>';
		}
		return rtrim( $subscriber_lists, ', ' );
	}

	public function column_date_added( $subscriber ) {
		return date( $this->date_format, strtotime($subscriber['date_added']) );
	}

	public function display() {
		$singular = $this->_args['singular'];
		?>
		<form method="get" id="subscribers_list_form">
			<input type="hidden" name="page" value="followup-emails-subscribers" />

			<?php
			$this->search_box( __('Search', 'follow_up_emails'), 'subscribers' );
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
					echo esc_attr( " data-wp-lists='list:$singular'" );
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
			<div id="div_lists" style="display:none;">
				<select name="list[]" id="select_lists" class="select2" multiple style="width: 300px; clear: both;" data-placeholder="<?php esc_attr_e('Select lists', 'follow_up_emails'); ?>">
					<option></option>
					<?php foreach ( $this->newsletter->get_lists() as $list ): ?>
						<option value="<?php echo esc_attr( $list['id'] ); ?>"><?php echo esc_html( $list['list_name'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div id="new_list" style="display: none; clear: both;">
				<input type="text" name="new_list_name" id="new_list_name" placeholder="<?php esc_attr_e( 'List name', 'follow_up_emails' ); ?>" style="float: left;" />
			</div>

			<div id="rename_subscriber" style="display: none; clear: both;">
				<input type="text" name="new_first_name" id="new_first_name" placeholder="<?php esc_attr_e( 'First name', 'follow_up_emails' ); ?>" style="float: left;" />
				<input type="text" name="new_last_name" id="new_last_name" placeholder="<?php esc_attr_e( 'Last name', 'follow_up_emails' ); ?>" style="float: left;" />
			</div>
		</form>
	<?php
	}

}
