<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FUE_Sending_Queue_List_Table class
 * Output a List Table that mirrors the followup_email_orders table
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FUE_Sending_Queue_List_Table extends WP_List_Table {

	/**
	 * Create and instance of this list table.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular'  => 'item',
			'plural'    => 'items',
			'ajax'      => false
		) );
		add_thickbox();
	}

	/**
	 * Prepare items to be displayed and setup pagination data
	 */
	public function prepare_items() {
		$wpdb       = Follow_Up_Emails::instance()->wpdb;

		$columns    = $this->get_columns();
		$hidden     = array();

		$sortable   = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$per_page   = get_option( 'fue_sending_queue_list_table_per_page', 20 );

		if ( ! empty( $_REQUEST['_items_per_page'] ) && check_admin_referer( 'bulk-items' ) ) {
			$per_page = absint( $_REQUEST['_items_per_page'] );
			update_option( 'fue_sending_queue_list_table_per_page', $per_page );
		}

		$page       = empty( $_GET['paged'] ) ? 1 : absint( $_GET['paged'] );
		$start      = ( $per_page * $page ) - $per_page;

		$sql = "SELECT SQL_CALC_FOUND_ROWS *
				FROM {$wpdb->prefix}followup_email_orders eo, {$wpdb->posts} p
				WHERE 1=1
				AND eo.is_sent = 0
				AND p.ID = eo.email_id
				AND eo.status IN (". FUE_Sending_Queue_Item::STATUS_SUSPENDED .",". FUE_Sending_Queue_Item::STATUS_ACTIVE .")";

		if ( ! empty( $_GET['_customer_user'] ) ) {
			// filter by user id/user email
			$user = new WP_User( absint( $_GET['_customer_user'] ) );
			$user_email = $user->billing_email;

			if ( empty( $user_email ) ) {
				$user_email = $user->user_email;
			}

			$sql .= " AND (
				user_id = ". esc_sql( absint( $user->ID ) ) ." OR
				user_email = '". esc_sql( sanitize_email( $user_email ) ) ."'
			)";
		}

		$order = 'desc';
		$order_column = 'send_on';

		if ( !empty( $_GET['orderby'] ) ) {
			$order_column = sanitize_sql_orderby( wp_unslash( $_GET['orderby'] ) );
			$order = strtolower(  isset( $_GET['order']) && $_GET['order'] === 'asc' ) ? 'asc' : 'desc';

			if ( !isset( $sortable[ $order_column ] ) ) {
				$order_column = 'send_on';
			}

			if ( $order_column == 'user_id' ) {
				$order_column = "user_id {$order}, user_email";
			}
		}

		$sql .= " ORDER BY {$order_column} {$order} LIMIT {$start},{$per_page}";

		$this->items = $wpdb->get_results( $sql, ARRAY_A );

		$total_items = $wpdb->get_var("SELECT FOUND_ROWS()");

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );

	}

	/**
	 * Return the value for the columns
	 * @param array     $item
	 * @param string    $column
	 *
	 * @return string
	 */
	public function column_default( $item, $column ) {
		$value = '';

		switch ( $column ) {

			case 'user_id':
				$value = $this->get_user_value( $item );
				break;

			case 'order_id':
				$value = $this->get_order_value( $item );
				break;

			case 'email_id':
				$value = $this->get_email_value( $item );
				break;

			case 'status':
				$value = $this->get_status_value( $item );
				break;

			case 'date':
				$value = $this->get_date_value( $item );
				break;

		}

		return $value;
	}

	/**
	 * Checkbox column value
	 *
	 * @param array $item
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" class="" name="queue[]" value="%1$s" />', $item['id'] );
	}

	/**
	 * Value of the user_id column. Return the email address if the item has no user id.
	 * Otherwise, return the customer's name and email address
	 *
	 * @param array $item
	 * @return string
	 */
	public function get_user_value( $item ) {
		$first_name = '';
		$last_name  = '';

		if ( empty( $item['user_id'] ) ) {
			// attempt to get the user id from the order
			if ( !empty( $item['order_id'] ) ) {
				$item['user_id'] = get_post_meta( $item['order_id'], '_customer_user', true );
			}

			// if the user_id is still empty, use the order's billing email
			if ( empty( $item['user_id'] ) && !empty( $item['order_id'] ) ) {
				$first_name = get_post_meta( $item['order_id'], '_billing_first_name', true );
				$last_name  = get_post_meta( $item['order_id'], '_billing_last_name', true );
				$email      = get_post_meta( $item['order_id'], '_billing_email', true );
			} else {
				$email = $item['user_email'];
			}

		} else {
			// look for the billing name
			$first_name = get_user_meta( $item['user_id'], 'billing_first_name', true );
			$last_name  = get_user_meta( $item['user_id'], 'billing_last_name', true );
			$email      = $item['user_email'];
		}

		if ( $first_name && $last_name ) {
			$name = $first_name .' '. $last_name;

			if ( empty( $email ) && $item['user_id'] ) {
				$email = get_user_meta( $item['user_id'], 'billing_email', true );
			}
		} else {
			// fallback to using the display name
			$user   = new WP_User( $item['user_id'] );
			$name   = $user->display_name;

			if ( empty( $email ) ) {
				$email  = $user->user_email;
			}

		}

		if ( $item['user_id'] ) {
			return sprintf(
				__('<a href="%s">#%d - %s &lt;%s&gt;', 'follow_up_emails'),
				get_edit_user_link( $item['user_id'] ),
				$item['user_id'],
				$name,
				$email
			);
		} else {
			return sprintf(
				__('%s &lt;%s&gt;', 'follow_up_emails'),
				$name,
				$email
			);
		}

	}

	/**
	 * Return a link to the order screen
	 * @param array $item
	 * @return string
	 */
	public function get_order_value( $item ) {
		if ( empty( $item['order_id'] ) ) {
			return '-';
		}

		$order = WC_FUE_Compatibility::wc_get_order( $item['order_id'] );

		if ( $order instanceof WC_Order ) {
			return sprintf(
				__('<a href="%s">%s</a>', 'follow_up_emails'),
				'post.php?post='. $item['order_id'] .'&action=edit',
				$order->get_order_number()
			);
		}

		return '-';

	}

	/**
	 * Get the name and trigger of the follow-up email
	 * @param array $item
	 * @return string
	 */
	public function get_email_value( $item ) {
		$email = new FUE_Email( $item['email_id'] );

		if ( !$email->exists() ) {
			return '<em>deleted</em>';
		}

		if ( $email->status != FUE_Email::STATUS_ACTIVE ) {
			return sprintf(
				__('<a href="%s">#%d %s</a> - Inactive<br/><small>(%s)</small>', 'follow_up_emails'),
				admin_url('post.php?post='. $item['email_id'] .'&action=edit'),
				$item['email_id'],
				$email->name,
				$email->get_trigger_string()
			);
		} else {
			return sprintf(
				__('<a href="%s">#%d %s</a><br/><small>(%s)</small>', 'follow_up_emails'),
				admin_url('post.php?post='. $item['email_id'] .'&action=edit'),
				$item['email_id'],
				$email->name,
				$email->get_trigger_string()
			);
		}

		return sprintf(
			__('<a href="%s">#%d %s</a><br/><small>(%s)</small>', 'follow_up_emails'),
			admin_url( 'admin.php?page=followup-emails-form&id='. $item['email_id'] ),
			$item['email_id'],
			$email->name,
			$email->get_trigger_string()
		);
	}

	/**
	 * Get the status of the item
	 * @param array $item
	 * @return string
	 */
	public function get_status_value( $item ) {
		$actions        = '';
		$delete_link    = wp_nonce_url( 'admin-post.php?action=fue_delete_queue&id='. $item['id'], 'delete_queue_item' );
		$delete_text    = __('Delete', 'follow_up_emails');

		if ( $item['is_sent'] == 1 ) {
			$status     = __('Sent', 'follow_up_emails');

			return sprintf(
				__( '%s<br/><small><a href="%s">%s</a></small>', 'follow_up_emails'),
				$status,
				$delete_link,
				$delete_text
			);
		} else {
			if ( $item['status'] == 1 ) {
				$status         = __('Queued', 'follow_up_emails');
				$action_link    = wp_nonce_url( 'admin-post.php?action=fue_update_queue_status&status=0&id='. $item['id'], 'update_queue_status' );
				$actions        = '<a href="'. $action_link .'" onclick="return confirm(\''. __('Really suspend this item?', 'follow_up_emails') .'\');">'. __('Suspend', 'follow_up_emails') .'</a>';

				//if ( defined('FUE_DEBUG') ) {
					$url = wp_nonce_url( 'admin-post.php?action=fue_send_queue_item&id='. $item['id'], 'send_queue_item' );
					$actions .= ' | <a href="'. $url .'" onclick="return confirm(\''. __('Really send this email now?', 'follow_up_emails') .'\');">'. __('Send Now', 'follow_up_emails') .'</a>';
				//}


			} elseif ( $item['status'] == 0 ) {
				$status = __('Suspended', 'follow_up_emails');

				$action = __('Activate', 'follow_up_emails');
				$action_link = wp_nonce_url( 'admin-post.php?action=fue_update_queue_status&status=1&id='. $item['id'], 'update_queue_status' );

				$actions .= '<a href="'. $action_link .'" onclick="return confirm(\''. __('Really activate this item?', 'follow_up_emails') .'\');">'. $action .'</a>';
			}

			$actions .= ' | <span class="trash"><a href="'. $delete_link .'" onclick="return confirm(\''. __('Really delete this item?', 'follow_up_emails') .'\');">'. $delete_text .'</a></span>';

			return sprintf(
				__( '%s<br/><small>%s</small>', 'follow_up_emails'),
				$status,
				$actions
			);

		}

	}

	/**
	 * Return the scheduled date/time
	 * @param array $item
	 * @return string
	 */
	public function get_date_value( $item ) {
		$scheduler  = Follow_Up_Emails::instance()->scheduler;
		$param      = $scheduler->get_scheduler_parameters( $item['id'] );
		$send_on    = as_next_scheduled_action( 'sfn_followup_emails', $param, 'fue' );

		if ( false === $send_on ) {
			// attempt to schedule the email again
			$send_on = strtotime( get_gmt_from_date( date( 'Y-m-d H:i:s', $item['send_on'] ) ) );
			as_schedule_single_action( $send_on, 'sfn_followup_emails', $param, 'fue' );
		}

		return get_date_from_gmt(
			date( 'Y-m-d H:i:s', $send_on ),
			get_option('date_format') .' '. get_option('time_format')
		);

	}

	/**
	 * Add all the columns
	 *
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 */
	public function get_columns(){

		$columns = array(
			'cb'                    => '<input type="checkbox" />',
			'user_id'               => __( 'Recipient', 'follow_up_emails' ),
			'order_id'              => __( 'Order', 'follow_up_emails' ),
			'email_id'              => __( 'Email', 'follow_up_emails' ),
			'status'                => __( 'Status', 'follow_up_emails' ),
			'date'                  => __( 'Scheduled', 'follow_up_emails' )
		);

		return $columns;
	}

	/**
	 * Make the table sortable by all columns and set the default sort field to be 'send_on'.
	 *
	 * @return array An associative array containing all the columns that should be sortable: 'slugs' => array( 'data_values', bool )
	 */
	public function get_sortable_columns() {

		$sortable_columns = array(
			'order_id'            => array( 'order_id', false ),
			'user_id'             => array( 'user_id', false ),
			'date'                => array( 'send_on', true )
		);

		return $sortable_columns;
	}

	/**
	 * @return array An associative array containing all the bulk actions: 'slugs' => 'Visible Titles'
	 */
	public function get_bulk_actions() {

		$actions = array(
			'send'      => __( 'Send', 'follow_up_emails' ),
			'activate'  => __( 'Activate', 'follow_up_emails' ),
			'suspend'   => __( 'Suspend', 'follow_up_emails' ),
			'delete'    => __( 'Delete', 'follow_up_emails' ),
		);

		return $actions;
	}

	/**
	 * Get the current action selected from the bulk actions dropdown.
	 *
	 * @return string|bool The action name or False if no action was selected
	 */
	function current_action() {

		$current_action = false;

		if ( isset( $_REQUEST['new_status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$current_action = sanitize_text_field( wp_unslash( $_REQUEST['new_status'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$current_action = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		return $current_action;
	}

	/**
	 * Generate the table navigation above or below the table
	 */
	function display_tablenav( $which ) {
		if ( 'top' == $which ) { ?>
			<input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>" />
			<?php if ( isset( $_REQUEST['status'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<input type="hidden" name="status" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>" />
			<?php endif;
		}
		parent::display_tablenav( $which );
	}

	/**
	 * Display extra filter controls between bulk actions and pagination.
	 *
	 * @since 1.3.1
	 */
	function extra_tablenav( $which ) {
		$per_page = get_option( 'fue_sending_queue_list_table_per_page', 20 );
		$user_string = '';
		$user_id     = '';

		if ( ! empty( $_GET['_customer_user'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$user_id     = absint( $_GET['_customer_user'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$user        = get_user_by( 'id', $user_id );
			$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
		}

		if ( 'top' == $which ) {
	?>
			<div class="alignleft actions">
				<select id="dropdown_per_page" name="_items_per_page">
					<option value="20" <?php selected( 20, $per_page ); ?>><?php esc_html_e( 'Show 20 per page', 'follow_up_emails' ); ?></option>
					<option value="50" <?php selected( 50, $per_page ); ?>><?php esc_html_e( 'Show 50 per page', 'follow_up_emails' ); ?></option>
					<option value="100" <?php selected( 100, $per_page ); ?>><?php esc_html_e( 'Show 100 per page', 'follow_up_emails' ); ?></option>
					<option value="200" <?php selected( 200, $per_page ); ?>><?php esc_html_e( 'Show 200 per page', 'follow_up_emails' ); ?></option>
				</select>

				<select class="fue-customer-search" name="_customer_user" data-placeholder="<?php esc_attr_e( 'Search for a customer&hellip;', 'follow_up_emails' ); ?>" data-allow_clear="true" data-nonce="<?php echo esc_attr( wp_create_nonce( 'customer_search' ) ); ?>">
				<?php if ( ! empty( $user_id ) ) : ?>
					<option value="<?php echo esc_attr( $user_id ); ?>"><?php echo esc_attr( $user_string ); ?></option>
				<?php endif; ?>
				</select>

			<?php
				submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
				submit_button( __( 'Delete ALL', 'follow_up_emails' ), 'button', 'fue_delete_all', false, array( 'id' => 'delete-all-submit' ) );

				do_action( 'fue_scheduled_events_extra_tablenav' );
			?>
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
					echo '<div id="moderated" class="updated"><p>' . wp_kses_post( implode( "<br/>\n", $all_messages['messages'] ) ). '</p></div>';
				}

				if ( ! empty( $all_messages['error_messages'] ) ) {
					echo '<div id="moderated" class="error"><p>' . wp_kses_post( implode( "<br/>\n", $all_messages['error_messages'] ) ) . '</p></div>';
				}
			}

		} elseif ( isset( $_REQUEST['s'] ) ) {

			echo '<div id="moderated" class="updated"><p>';
			echo '<a href="' . esc_url( admin_url( 'admin.php?page=followup-emails-queue' ) ). '" class="close-fue-search">&times;</a>';
			printf( esc_html__( 'Showing only emails containing "%s"', 'follow_up_emails' ), esc_html( sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) ) );
			echo '</p></div>';

		}

		if ( !empty( $_GET['_customer_user'] ) || !empty( $_GET['_product_id'] ) ) {

			echo '<div id="moderated" class="updated"><p>';
			echo '<a href="' . esc_url( admin_url( 'admin.php?page=followup-emails-queue' ) ). '" class="close-fue-search">&times;</a>';

			if ( ! empty( $_GET['_customer_user'] ) ) {

				$user_id = intval( $_GET['_customer_user'] );
				$user    = get_user_by( 'id', absint( $_GET['_customer_user'] ) );

				if ( false === $user ) {
					esc_html_e( 'Invalid user. ', 'follow_up_emails' );
				} else {
					printf( esc_html__( "Showing %s's emails", 'follow_up_emails' ), esc_html( $user->display_name ) );
				}
			}

			if ( ! empty( $_GET['_product_id'] ) ) {

				$product_id = intval( $_GET['_product_id'] );
				$product    = FUE_WC_Compatibility::wc_get_product( $product_id );

				if ( false === $product ) {
					printf( esc_html__( 'Invalid product.', 'follow_up_emails' ), esc_html( $user->display_name ) );
				} elseif ( ! empty( $_GET['_customer_user'] ) ) {
					printf( esc_html__( ' for product #%s &ndash; %s%s%s', 'follow_up_emails' ), esc_html( $product_id ), '<em>', esc_html( $product->get_title() ), '</em>' );
				} else {
					printf( esc_html__( 'Showing emails for product #%s &ndash; %s%s%s', 'follow_up_emails' ), esc_html( $product_id ), '<em>', esc_html( $product->get_title() ), '</em>' );
				}
			}

			echo '</p></div>';

		}
	}

}
