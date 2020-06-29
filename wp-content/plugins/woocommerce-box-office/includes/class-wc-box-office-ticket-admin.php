<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Ticket_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add extension pages to WooCommerce screens.
		add_filter( 'woocommerce_screen_ids', array( $this, 'screen_ids' ), 10, 1 );

		// Filterings.
		add_action( 'restrict_manage_posts', array( $this, 'filter_ticket_product_id' ) );
		add_action( 'parse_query', array( $this, 'filter_ticket_product_id_query' ) );

		// Add metabox to ticket and ticket's email edit screens.
		add_action( 'add_meta_boxes_event_ticket', array( $this, 'event_ticket_meta_box' ), 10, 1 );
		add_action( 'add_meta_boxes_event_ticket_email', array( $this, 'event_ticket_email_meta_box' ), 10, 1 );
		add_action( 'save_post', array( $this, 'event_ticket_meta_box_save' ), 10, 1 );

		// Update order item meta for this ticket when ticket is updated.
		add_action( 'save_post', array( $this, 'update_order_item_meta' ), 9, 3 );

		// Manage admin columns for tickets.
		add_filter( 'manage_event_ticket_posts_columns', array( $this, 'manage_ticket_columns' ), 11, 1 );
		add_action( 'manage_event_ticket_posts_custom_column', array( $this, 'display_ticket_columns' ), 10, 2 );

		// Set row actions.
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );

		// Bulk actions.
		add_filter( 'bulk_actions-edit-event_ticket', array( $this, 'remove_edit_from_bulk_actions' ) );
		add_action( 'admin_footer-edit.php', array( $this, 'custom_bulk_actions' ) );
		add_action( 'load-edit.php', array( $this, 'bulk_action' ) );

		// Manage admin columns for ticket emails.
		add_filter( 'manage_event_ticket_email_posts_columns', array( $this, 'manage_ticket_email_columns' ), 11, 1 );

		// Prevent add ticket page & ticket email list table from being accessible.
		add_action( 'admin_menu', array( $this, 'hide_ticket_add' ) );
		add_action( 'init', array( $this, 'block_admin_pages' ) );

		// Add create ticket page.
		add_action( 'admin_menu', array( $this, 'add_create_ticket_page' ) );
	}

	/**
	 * Add Box Office pages to WooCommerce screen IDs.
	 *
	 * @param  array $screen_ids Existing IDs
	 * @return array             Modified IDs
	 */
	public function screen_ids( $screen_ids = array() ) {
		$screen_ids[] = 'edit-event_ticket';
		$screen_ids[] = 'event_ticket';
		$screen_ids[] = 'event_ticket_email';
		$screen_ids[] = 'edit-event_ticket_email';
		$screen_ids[] = 'event_ticket_page_ticket_tools';
		$screen_ids[] = 'event_ticket_page_create_ticket';

		return $screen_ids;
	}

	/**
	 * Add ticket product_id filter.
	 *
	 * @return void
	 */
	public function filter_ticket_product_id() {
		global $typenow, $wp_query;

		if ( 'event_ticket' !== $typenow ) {
			return;
		}

		$output  = '';
		$tickets = wc_box_office_get_all_ticket_products();
		if ( $tickets ) {
			$current = ! empty( $_GET['filter_ticket_product_id'] ) ? absint( $_GET['filter_ticket_product_id'] ) : '';
			$output .= '<select name="filter_ticket_product_id">';
			$output .= '<option value="">' . __( 'All Ticket Products', 'woocommerce-box-office' ) . '</option>';

			foreach ( $tickets as $ticket ) {
				$output .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $ticket->ID ), selected( $ticket->ID, $current, false ), esc_html( $ticket->post_title ) );
			}

			$output .= '</select>';
		}

		echo $output;
	}

	/**
	 * Filter ticket products query.
	 *
	 * @param mixed $query
	 *
	 * @return void
	 */
	public function filter_ticket_product_id_query( $query ) {
		global $typenow;

		if ( 'event_ticket' !== $typenow ) {
			return;
		}

		if ( empty( $_GET['filter_ticket_product_id'] ) ) {
			return;
		}

		if ( ! empty( $query->query_vars['suppress_filters'] ) ) {
			return;
		}

		$query->query_vars['meta_query'] = array(
			array(
				'key'   => '_product_id',
				'value' => absint( $_GET['filter_ticket_product_id'] ),
			)
		);
	}

	/**
	 * Create meta box on ticket edit screen.
	 *
	 * @param  object $post Post object
	 * @return void
	 */
	public function event_ticket_meta_box( $post ) {
		add_meta_box( 'ticket-info', __( 'Ticket Information', 'woocommerce-box-office' ), array( $this, 'event_ticket_meta_box_content' ), 'event_ticket', 'normal', 'high' );

		if ( function_exists( 'WC_Order_Barcodes' ) ) {
			add_meta_box( 'ticket-barcode', __( 'Ticket Barcode', 'woocommerce-box-office' ), array( $this, 'display_ticket_barcode_meta_box' ), 'event_ticket', 'side', 'default' );
		}
	}

	/**
	 * Ticket barcode meta box.
	 *
	 * @param  object $post Post object
	 * @return void
	 */
	public function display_ticket_barcode_meta_box( $post ) {
		WCBO()->components->ticket_barcode->display_ticket_barcode( $post );
	}

	/**
	 * Load content for ticket meta box.
	 *
	 * @param  object $post Post object
	 * @return void
	 */
	public function event_ticket_meta_box_content ( $post ) {
		wp_nonce_field( 'woocommerce_box_office_ticket_info', 'event_ticket_meta_box_nonce' );

		$ticket      = wc_box_office_get_ticket( $post );
		$ticket_form = new WC_Box_Office_Ticket_Form( $ticket->product, wp_list_pluck( $ticket->fields, 'value' ) );

		add_filter( 'wocommerce_box_office_input_field_template_vars', array( $this, 'custom_field_wrapper' ) );
		add_filter( 'wocommerce_box_office_option_field_template_vars', array( $this, 'custom_field_wrapper' ) );
		require_once( WCBO()->dir . 'includes/views/admin/ticket-meta-box.php' );
		remove_filter( 'wocommerce_box_office_input_field_template_vars', array( $this, 'custom_field_wrapper' ) );
		remove_filter( 'wocommerce_box_office_option_field_template_vars', array( $this, 'custom_field_wrapper' ) );
	}

	/**
	 * Custom field wrapper in ticket meta box.
	 *
	 * @param array $tpl_vars Template vars for field
	 *
	 * @return array Template vars
	 */
	public function custom_field_wrapper( $tpl_vars ) {
		$tpl_vars['before_field'] = '<p class="form-field">';
		$tpl_vars['after_field']  = '</p>';

		return $tpl_vars;
	}

	/**
	 * Save fields from event ticket meta box.
	 *
	 * @param  integer $post_id Ticket ID
	 * @return void
	 */
	public function event_ticket_meta_box_save( $post_id = 0 ) {
		if ( ! $post_id || ! is_admin() ) {
			return;
		}

		$post_type = get_post_type( $post_id );
		if ( 'event_ticket' !== $post_type ) {
			return;
		}

		if ( empty( $_POST['event_ticket_meta_box_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['event_ticket_meta_box_nonce'], 'woocommerce_box_office_ticket_info' ) ) {
			return;
		}

		if ( ! isset( $_POST['ticket_fields'] ) || ! is_array( $_POST['ticket_fields'] ) ) {
			return;
		}

		try {
			$ticket = wc_box_office_get_ticket( $post_id );
			$ticket_form = new WC_Box_Office_Ticket_Form( $ticket->product );
			$ticket_form->validate( $_POST );

			// TODO(gedex) should we send email if email field is changed -- like
			// in front-end?
			remove_action( 'save_post', array( $this, 'event_ticket_meta_box_save' ), 10 );
			$ticket->update( $ticket_form->get_clean_data() );
			add_action( 'save_post', array( $this, 'event_ticket_meta_box_save' ), 10, 1 );

			if ( isset( $_POST['_attended'] ) ) {
				update_post_meta( $post_id, '_attended', $_POST['_attended'] );
			} else {
				delete_post_meta( $post_id, '_attended' );
			}
		} catch ( Exception $e ) {
			WC_Admin_Meta_Boxes::add_error( $e->getMessage() );
		}
	}

	/**
	 * Update order item meta when ticket is updated.
	 *
	 * @since 1.1.4
	 * @version 1.1.4
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	public function update_order_item_meta( $post_id, $post, $update ) {
		if ( ! $post_id || ! is_admin() ) {
			return;
		}

		// Ignore if this is created initially since it's generated already by
		// order handler when an order is created.
		if ( ! $update ) {
			return;
		}

		WCBO()->components->order->update_item_meta_from_ticket( $post_id );
	}

	/**
	 * Create meta box on ticket edit screen.
	 *
	 * @param  object $post Post object
	 * @return void
	 */
	public function event_ticket_email_meta_box ( $post ) {
		add_meta_box( 'email-info', __( 'Email Information', 'woocommerce-box-office' ), array( $this, 'event_ticket_email_meta_box_content' ), 'event_ticket_email', 'normal', 'high' );
		add_meta_box( 'email-content', __( 'Email Content', 'woocommerce-box-office' ), array( $this, 'event_ticket_email_content_meta_box_content' ), 'event_ticket_email', 'normal', 'high' );
		add_meta_box( 'email-log', __( 'Email Log', 'woocommerce-box-office' ), array( $this, 'event_ticket_email_log_meta_box_content' ), 'event_ticket_email', 'normal', 'high' );
		remove_meta_box( 'submitdiv', 'event_ticket_email', 'side' );
	}

	/**
	 * Load content for ticket email meta box.
	 *
	 * @param  object $post Post object
	 * @return void
	 */
	public function event_ticket_email_meta_box_content( $post ) {
		require_once( WCBO()->dir . 'includes/views/admin/ticket-email-meta-box.php' );
	}

	/**
	 * Load content for ticket email meta box.
	 *
	 * @param  object $post Post object
	 * @return void
	 */
	public function event_ticket_email_content_meta_box_content( $post ) {
		echo wpautop( $post->post_content );
	}

	public function event_ticket_email_log_meta_box_content( $post ) {
		require_once( WCBO()->dir . 'includes/views/admin/ticket-email-log-meta-box.php' );;
	}

	/**
	 * Modify admin columns for tickets list table.
	 *
	 * @param  array  $columns Default columns
	 * @return array           Modified columns
	 */
	public function manage_ticket_columns( $columns = array() ) {
		// Remove WordPress SEO columns.
		unset( $columns['wpseo-score'] );
		unset( $columns['wpseo-title'] );
		unset( $columns['wpseo-metadesc'] );
		unset( $columns['wpseo-focuskw'] );

		// Remove WP columns.
		unset( $columns['title'] );
		unset( $columns['date'] );

		// Add custom columns.
		$columns['ticket']     = __( 'Ticket', 'woocommerce-box-office' );
		$columns['order']      = __( 'Order', 'woocommerce-box-office' );
		$columns['checked-in'] = __( 'Checked-in yet?', 'woocommerce-box-office' );
		$columns['date']       = __( 'Date', 'woocommerce-box-office' );

		return $columns;
	}

	/**
	 * Display data in ticket list table columns.
	 *
	 * @param  string  $column  Column name
	 * @param  integer $post_id Ticket ID
	 * @return void
	 */
	public function display_ticket_columns( $column = '', $post_id = 0 ) {
		if ( ! $column || ! $post_id ) {
			return;
		}

		switch ( $column ) {
			case 'ticket':
				printf(
					'
					<strong>
						<a class="row-title" href="%1$s">%2$s</a>%3$s
					</strong>
					<div class="ticket-fields">%4$s</div>
					',
					esc_url( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) ),
					esc_html( get_the_title( $post_id ) ),
					( 'pending' === get_post_status( $post_id ) ) ? sprintf( ' - <span class="post-state">%s</span>', __( 'Pending', 'woocommerce-box-office' ) ) : '',
					wc_box_office_get_ticket_description( $post_id )
				);
				break;
			case 'order':
				$order_id     = wp_get_post_parent_id( $post_id );
				$order_status = get_post_status( $order_id );
				if ( $order_id ) {
					printf(
						'<strong><a href="%1$s">%2$s</a> - %3$s</strong>',
						esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ),
						esc_html( '#' . $order_id ),
						wc_get_order_status_name( $order_status )
					);
				} else {
					echo '-';
				}
				break;
			case 'checked-in':
				if ( get_post_meta( $post_id, '_attended', true ) ) {
					printf( '<strong>%s</strong>', __( 'Yes', 'woocommerce-box-office' ) );
				}
		}
	}

	/**
	 * Set row actions for event_ticket post type.
	 *
	 * @param array   $actions List of actions
	 * @param WP_Post $post    Post object
	 *
	 * @return array
	 */
	public function row_actions( $actions, $post ) {
		if ( 'event_ticket' === $post->post_type && isset( $actions['inline hide-if-no-js'] ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}

	/**
	 * Remove edit in bulk actions for event_ticket post type.
	 *
	 * @param array $actions Bulk actions
	 *
	 * @return array $actions Bulk actions
	 */
	public function remove_edit_from_bulk_actions( $actions ) {
		unset( $actions['edit'] );

		return $actions;
	}

	/**
	 * Add custom bulk action options.
	 *
	 * Using Javascript until WordPress core fixes: http://core.trac.wordpress.org/ticket/16031.
	 */
	public function custom_bulk_actions() {
		global $post_type;

		if ( 'event_ticket' !== $post_type ) {
			return;
		}

		?>
		<script>
		jQuery(function() {
			jQuery( '<option>' ).val( 'mark_attended' ).text( '<?php _e( 'Mark as attended', 'woocommerce-box-office' )?>').appendTo('select[name="action"]' );
			jQuery( '<option>' ).val( 'mark_attended' ).text( '<?php _e( 'Mark as attended', 'woocommerce-box-office' )?>').appendTo('select[name="action2"]' );

			jQuery( '<option>' ).val( 'mark_not_checked_in' ).text( '<?php _e( 'Mark as not checked-in yet', 'woocommerce-box-office' )?>').appendTo('select[name="action"]' );
			jQuery( '<option>' ).val( 'mark_not_checked_in' ).text( '<?php _e( 'Mark as not checked-in yet', 'woocommerce-box-office' )?>').appendTo('select[name="action2"]' );
		});
		</script>
		<?php
	}

	/**
	 * Process the custom bulk actions.
	 */
	public function bulk_action() {
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action        = $wp_list_table->current_action();

		if ( ! in_array( $action, array( 'mark_attended', 'mark_not_checked_in' ) ) ) {
			return;
		}

		check_admin_referer( 'bulk-posts' );

		$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );
		$updated  = 0;
		foreach ( $post_ids as $post_id ) {
			$succeed = false;
			switch ( $action ) {
				case 'mark_attended':
					$succeed = update_post_meta( $post_id, '_attended', 'yes' );
					break;
				case 'mark_not_checked_in':
					$succeed = delete_post_meta( $post_id, '_attended' );
					break;
			}

			if ( $succeed ) {
				$updated++;
			}
		}

		$sendback = add_query_arg( array( 'post_type' => 'event_ticket', $action => true, 'updated' => $updated, 'ids' => join( ',', $post_ids ) ), '' );

		if ( isset( $_GET['post_status'] ) ) {
			$sendback = add_query_arg( 'post_status', sanitize_text_field( $_GET['post_status'] ), $sendback );
		}

		wp_redirect( esc_url_raw( $sendback ) );
		exit();
	}

	/**
	 * Modify admin columns for ticket emails list table.
	 *
	 * @param  array  $columns Default columns
	 * @return array           Modified columns
	 */
	public function manage_ticket_email_columns( $columns = array() ) {
		// Remove WordPress SEO columns.
		unset( $columns['wpseo-score'] );
		unset( $columns['wpseo-title'] );
		unset( $columns['wpseo-metadesc'] );
		unset( $columns['wpseo-focuskw'] );

		return $columns;
	}

	/**
	 * Remove 'Add New' menu item from Tickets
	 * @return void
	 */
	public function hide_ticket_add () {
		global $submenu;
		unset( $submenu['edit.php?post_type=event_ticket'][10] );
	}

	/**
	 * Prevent access to specific admin pages
	 * @return void
	 */
	public function block_admin_pages () {
		if ( ! is_admin() ) {
			return;
		}

		global $pagenow;

		$type = '';
		if ( isset( $_GET['post_type'] ) ) {
			$type = esc_attr( $_GET['post_type'] );
		}

		if ( ! $type ) {
			return;
		}

		$url = '';

		if ( 'post-new.php' === $pagenow && 'event_ticket' === $type ) {
			$url = admin_url( 'edit.php?post_type=event_ticket&page=create_ticket' );
		} elseif ( 'post-new.php' === $pagenow && 'event_ticket_email' === $type ) {
			$url = admin_url( 'edit.php?post_type=event_ticket&page=ticket_tools&tab=email' );
		} elseif ( 'edit.php' === $pagenow && 'event_ticket_email' === $type ) {
			$url = admin_url( 'edit.php?post_type=event_ticket&page=ticket_tools&tab=email' );
		}

		if ( $url ) {
			wp_safe_redirect( $url );
			exit;
		}
	}

	/**
	 * Add create ticket page.
	 *
	 * @return void
	 */
	public function add_create_ticket_page() {
		$create_ticket_page = add_submenu_page( 'edit.php?post_type=event_ticket', __( 'Create Ticket', 'woocommerce-box-office' ), __( 'Create Ticket', 'woocommerce-box-office' ), 'manage_woocommerce', 'create_ticket', array( $this, 'create_ticket_page' ) );
	}

	/**
	 * Render create ticket page on admin.
	 *
	 * @return void
	 */
	public function create_ticket_page() {
		$create_page = new WC_Box_Office_Ticket_Create_Admin();
		$create_page->render( $_POST );
	}
}
