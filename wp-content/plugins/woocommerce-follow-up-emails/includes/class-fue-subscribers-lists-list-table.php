<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FUE_Subscribers_Lists_List_Table extends WP_List_Table {
	/**
	 * Create and instance of this list table.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular'  => 'list',
			'plural'    => 'lists',
			'ajax'      => false
		) );
	}

	public function get_columns() {
		return array(
			'cb'                => '<input type="checkbox" />',
			'list_name'         => __('Name', 'follow_up_emails'),
			'subscriber_count'  => __('Subscribers in List', 'follow_up_emails'),
			'access'            => __('Access', 'follow_up_emails')
		);
	}

	public function prepare_items() {
		global $wpdb;

		$per_page   = get_option( 'fue_subscribers_lists_list_table_per_page', 20 );
		$columns    = $this->get_columns();
		$sortable   = array(
			'list_name'         => array('list_name', true),
			'subscriber_count'  => array('subscriber_count', false)
		);
		$hidden     = array();

		if ( ! empty( $_REQUEST['_items_per_page'] ) && check_admin_referer( 'bulk-lists' ) ) {
			$per_page = absint( $_REQUEST['_items_per_page'] );
			update_option( 'fue_subscribers_lists_list_table_per_page', $per_page );
		}

		$this->_column_headers = array($columns, $hidden, $sortable);

		$sql = "SELECT SQL_CALC_FOUND_ROWS l.*, COUNT(s2l.list_id) AS subscriber_count
				FROM {$wpdb->prefix}followup_subscriber_lists l
				LEFT JOIN {$wpdb->prefix}followup_subscribers_to_lists s2l ON l.id = s2l.list_id
				WHERE 1=1
				GROUP BY l.id";

		$orderby    = ! empty( $_GET['orderby'] ) ? esc_sql( sanitize_sql_orderby( wp_unslash( $_GET['orderby'] ) ) ) : 'list_name';
		$order      = ! empty( $_GET['order'] ) ? esc_sql( sanitize_text_field( wp_unslash( $_GET['order'] ) ) ) : 'ASC';
		$page       = $this->get_pagenum();
		$start      = ( $page * $per_page ) - $per_page;

		$sql .= " ORDER BY {$orderby} {$order} LIMIT {$start}, {$per_page}";
		$result = $wpdb->get_results( $sql );
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
	 * Generates content for a single row of the table
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param object $item The current item
	 */
	public function single_row( $item ) {
		echo '<tr class="row" id="row_'. esc_attr( $item->id ) .'" data-id="'. esc_attr( $item->id ) .'">';
		$this->single_row_columns( $item );
		echo '</tr>';
		echo '<tr class="spacer-row" id="spacer_row_'. esc_attr( $item->id ) .'" style="display: none;"></tr>';
		echo '<tr class="edit-row" id="edit_row_'. esc_attr( $item->id ) .'" data-id="'. esc_attr( $item->id ) .'" style="display: none;">';
		echo '<td>&nbsp;</td>';
		echo '<td><input type="text" class="list-name" value="'. esc_attr( $item->list_name ) .'" /></td>';
		echo '<td>&nbsp;</td>';
		echo '<td>
				<select class="list-access">
					<option value="0" '. selected( $item->access, 0, false ) .'>'. esc_html__('Private', 'follow_up_emails') .'</option>
					<option value="1" '. selected( $item->access, 1, false ) .'>'. esc_html__('Public', 'follow_up_emails') .'</option>
				</select>
				<input type="button" class="button button-primary btn-save" value="'. esc_attr__('Save', 'follow_up_emails') .'" />
				<input type="button" class="button button-secondary btn-cancel" value="'. esc_attr__('Cancel', 'follow_up_emails') .'" />
			</td>';
		echo '</tr>';
	}

	/**
	 * @param  object $list
	 * @return string
	 */
	public function column_cb( $list ) {
		return sprintf( '<input type="checkbox" name="list[]" value="%1$s" />', $list->id );
	}

	public function column_list_name( $list ) {
		$out = '<strong>'. $list->list_name .'</strong>';

		// Get actions
		$actions = array(
			'id' => sprintf( __( 'ID: %d', 'follow_up_emails' ), $list->id ),
			'edit' => '<a href="#" class="inline-edit" data-id="'. $list->id .'" data-name="'. esc_attr( $list->list_name ) .'" data-access="'. esc_attr( $list->access ) .'">' . __( 'Edit', 'follow_up_emails' ) . '</a>',
			'trash' => '<a class="submitdelete" title="' . esc_attr( __( 'Delete', 'follow_up_emails' ) ) . '" href="#">' . __( 'Delete', 'follow_up_emails' ) . '</a>'
		);

		$row_actions = array();

		foreach ( $actions as $action => $link ) {
			$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
		}

		$out .= '<div class="row-actions">' . implode(  ' | ', $row_actions ) . '</div>';

		return $out;
	}

	public function column_subscriber_count( $list ) {
		return $list->subscriber_count;
	}

	public function column_access( $list ) {
		return $list->access == 0 ? __('Private', 'follow_up_emails') : __('Public', 'follow_up_emails');
	}

	/**
	 * @return array An associative array containing all the bulk actions: 'slugs' => 'Visible Titles'
	 */
	public function get_bulk_actions() {

		$actions = array(
			'delete_list'    => __( 'Delete', 'follow_up_emails' ),
		);

		return $actions;
	}

	/**
	 * Generate the table navigation above or below the table
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' == $which ) { ?>
			<input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>" />
			<input type="hidden" name="view" value="lists" />
		<?php
		}
		parent::display_tablenav( $which );
	}

	/**
	 * Display extra filter controls between bulk actions and pagination.
	 *
	 * @since 1.3.1
	 */
	protected function extra_tablenav( $which ) {
		$per_page = get_option( 'fue_subscribers_lists_list_table_per_page', 20 );
		if ( 'top' == $which ) {
	?>
			<div class="alignleft actions">
			<select id="dropdown_per_page" name="_items_per_page">
				<option value="20" <?php selected( 20, $per_page ); ?>><?php esc_html_e( 'Show 20 per page', 'follow_up_emails' ); ?></option>
				<option value="50" <?php selected( 50, $per_page ); ?>><?php esc_html_e( 'Show 50 per page', 'follow_up_emails' ); ?></option>
				<option value="100" <?php selected( 100, $per_page ); ?>><?php esc_html_e( 'Show 100 per page', 'follow_up_emails' ); ?></option>
				<option value="200" <?php selected( 200, $per_page ); ?>><?php esc_html_e( 'Show 200 per page', 'follow_up_emails' ); ?></option>
			</select>
			<?php submit_button( esc_html__( 'Show' ), 'button', false, false, array( 'id' => 'post-query-submit' ) ); ?>

			<?php submit_button( esc_html__( 'Delete ALL', 'follow_up_emails' ), 'button', 'fue_delete_all_lists', false, array( 'id' => 'delete-all-submit' ) ); ?>
			</div>
	<?php
		}
	}

	/**
	 * Output any messages set on the class
	 */
	public function messages() {

		if ( isset( $_GET['message'] ) && check_admin_referer( 'fue-messages', 'message') ) {

			$message_key = sanitize_text_field( wp_unslash( $_GET['message'] ) );

			$all_messages = get_transient( '_fue_messages_' . $message_key );

			if ( ! empty( $all_messages ) ) {

				delete_transient( '_fue_messages_' . $message_key );

				if ( ! empty( $all_messages['messages'] ) ) {
					echo '<div id="moderated" class="updated"><p>' . wp_kses_post( implode( "<br/>\n", $all_messages['messages'] ) ) . '</p></div>';
				}

				if ( ! empty( $all_messages['error_messages'] ) ) {
					echo '<div id="moderated" class="error"><p>' . wp_kses_post( implode( "<br/>\n", $all_messages['error_messages'] ) ) . '</p></div>';
				}
			}

		}

	}

}
