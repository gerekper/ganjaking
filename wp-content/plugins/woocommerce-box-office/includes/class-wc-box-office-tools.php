<?php
/**
 * Box Office tools.
 *
 * @package WCBO\Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Box Office tools.
 *
 * Handle tools that can be found in Ticket > Tools page.
 */
class WC_Box_Office_Tools {

	/**
	 * Error messages.
	 *
	 * @var array
	 */
	private $_errors = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add settings page to menu.
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Process requested action.
		add_action( 'load-event_ticket_page_ticket_tools', array( $this, 'dispatch_tools_action' ) );

		// Get ticket fields via ajax.
		add_action( 'wp_ajax_get_ticket_field_options', array( $this, 'get_ticket_field_options_ajax' ) );

		// Get previewed email via ajax.
		add_action( 'wp_ajax_show_test_email', array( $this, 'show_test_email' ) );

		// Add settings link to plugins page.
		add_filter( 'plugin_action_links_' . plugin_basename( WCBO()->file ) , array( $this, 'add_tools_link' ) );
	}

	/**
	 * Add settings page to admin menu.
	 *
	 * @return void
	 */
	public function add_menu_item() {
		add_submenu_page( 'edit.php?post_type=event_ticket', __( 'Ticket Tools', 'woocommerce-box-office' ), __( 'Tools', 'woocommerce-box-office' ), 'manage_woocommerce', 'ticket_tools', array( $this, 'tools_page' ) );
	}

	/**
	 * Add settings link to plugin list table.
	 *
	 * @param  array $links Existing links.
	 * @return array        Modified links
	 */
	public function add_tools_link( $links ) {
		$settings_link = '<a href="edit.php?post_type=event_ticket&page=ticket_tools">' . __( 'Tools', 'woocommerce-box-office' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Dispatch action in tools page.
	 *
	 * @return void
	 */
	public function dispatch_tools_action() {
		$tab = ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'export';
		switch ( $tab ) {
			case 'export':
				$this->export_tickets();
				break;
			case 'email':
				$this->email_tickets();
		}
	}

	/**
	 * Send test email via ajax request.
	 */
	public function show_test_email() {
		if ( empty( $_POST['wc_box_office_admin_test_email_nonce'] ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( $_POST['wc_box_office_admin_test_email_nonce'], 'test-email' ) ) {
			return false;
		}

		// Get email details.
		$product_id = ! empty( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : '';
		$content    = isset( $_POST['content'] ) ? trim( $_POST['content'] ) : '';
		$subject    = isset( $_POST['subject'] ) ? trim( $_POST['subject'] ) : '';

		if ( empty( $product_id ) || empty( $content ) ) {
			exit;
		}

		$ticket_ids = get_posts( array(
			'post_type'      => 'event_ticket',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'nopaging'       => true,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'cache_results'  => false,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_product',
					'value'   => $product_id,
				),
			),

		) );

		$response = '';

		if ( empty( $ticket_ids ) ) {
			$response = '<p><strong><em>' . __( 'No purchased tickets found for the selected products.', 'woocommerce-box-office' ) . '</em></strong></p>';
		} else {
			$ticket_id = array_shift( $ticket_ids );

			if ( ! empty( $subject ) ) {
				$subject = wc_box_office_get_parsed_ticket_content( $ticket_id, $subject );
			} else {
				$subject = '<em>' . __( '(no subject)', 'woocommerce-box-office' ) . '</em>';
			}

			$response  = '<p><strong>' . $subject . '</strong></p>' . "\n";
			$response .= '<hr/>' . "\n";
			$response .= wc_box_office_get_parsed_ticket_content( $ticket_id, $content ) . "\n";
		}

		echo $response;

		exit;
	}

	/**
	 * AJAX handler to get ticket field options.
	 */
	public function get_ticket_field_options_ajax() {
		if ( empty( $_POST['wc_box_office_admin_ticket_fields_nonce'] ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( $_POST['wc_box_office_admin_ticket_fields_nonce'], 'get-ticket-field-options' ) ) {
			return false;
		}

		$product_id = ! empty( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : '';
		if ( ! $product_id ) {
			return false;
		}

		// Get HTML for product options.
		echo wc_box_office_get_product_ticket_fields_options( $product_id );

		// Exit function to prevent 0 being printed out at end of ajax request.
		exit;
	}

	/**
	 * Export selected tickets.
	 *
	 * @since 1.0.0
	 * @version 1.1.5
	 */
	public function export_tickets() {
		if ( ! is_admin() || ! isset( $_GET['action'] ) || 'export_tickets' !== $_GET['action'] || ! isset( $_GET['tickets'] ) || ! is_array( $_GET['tickets'] ) ) {
			return false;
		}

		$products    = $_GET['tickets'];
		$post_status = isset( $_GET['only_published_tickets'] ) ? 'publish' : 'any';

		$filename = sprintf( 'ticket-export-%1$s.csv', date( 'Y-m-d' ) );

		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Cache-control: private' );
		header( 'Pragma: private' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );

		$columns = array(
			'ticket_id'     => __( 'Ticket ID', 'woocommerce-box-office' ),
			'ticket_status' => __( 'Ticket status', 'woocommerce-box-office' ),
			'sku'           => __( 'SKU', 'woocommerce-box-office' ),
			'ticket_name'   => __( 'Ticket', 'woocommerce-box-office' ),
			'ticket_url'    => __( 'Ticket URL', 'woocommerce-box-office' ),
			'purchase_date' => __( 'Purchase date', 'woocommerce-box-office' ),
			'order_id'      => __( 'Order ID', 'woocommerce-box-office' ),
			'order_status'  => __( 'Order status', 'woocommerce-box-office' ),
			'user_id'       => __( 'User ID', 'woocommerce-box-office' ),
		);

		foreach ( $products as $product_id ) {

			// Get available ticket fields.
			$ticket_fields = get_post_meta( $product_id, '_ticket_fields', true );

			foreach ( $ticket_fields as $field_key => $field ) {
				$columns[ $field_key ] = $field['label'];
			}
		}

		ob_start();
		$export = fopen( 'php://output', 'w' );
		fputcsv( $export, $columns );

		$paged = 1;
		while ( $tickets = get_posts( array(
			'post_type'      => 'event_ticket',
			'post_status'    => $post_status,
			'posts_per_page' => 200,
			'paged'          => $paged++,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'cache_results'  => false,
			'meta_query' => array(
				array(
					'key' => '_product',
					'value' => $products,
					'compare' => 'IN',
				),
			),
		) ) ) {

			foreach ( $tickets as $ticket ) {

				// Get ticket ID.
				$ticket_id = $ticket->ID;

				// Get product ID.
				$product_id = get_post_meta( $ticket_id, '_product', true );

				$product = wc_get_product( $product_id );

				// Get customer user ID.
				$user_id = get_post_meta( $ticket_id, '_user', true );

				// Get order info.
				$order_id = get_post_meta( $ticket_id, '_order', true );
				$order = get_post( $order_id );

				$ticket_url = wcbo_get_my_ticket_url( $ticket_id );

				// Get order date.
				if ( ! $order || ( $order && '0000-00-00 00:00:00' === $order->post_date ) ) {
					$purchase_time = __( 'N/A', 'woocommerce-box-office' );
				} else {
					$purchase_time = get_the_time( __( 'Y/m/d g:i:s A', 'woocommerce-box-office' ), $order_id );
				}

				// Add basic ticket data to export.
				$data = array(
					'ticket_id'     => $ticket_id,
					'ticket_status' => get_post_status( $ticket_id ),
					'sku'           => $product->get_sku(),
					'ticket_name'   => $ticket->post_title,
					'ticket_url'    => $ticket_url,
					'purchase_date' => $purchase_time,
					'order_id'      => $order_id,
					'order_status'  => wc_get_order_status_name( get_post_status( $order_id ) ),
					'user_id'       => $user_id,
				);

				// Get available ticket fields.
				$ticket_fields = get_post_meta( $product_id, '_ticket_fields', true );
				if ( is_array( $ticket_fields ) ) {
					foreach ( $ticket_fields as $field_key => $field ) {
						$ticket_meta        = get_post_meta( $ticket_id, $field_key, true );
						if ( is_array( $ticket_meta ) ) {
							$ticket_meta = implode( ',', $ticket_meta );
						}
						$data[ $field_key ] = $ticket_meta;
					}
				}

				// Make sure every column is printed.
				$clean_data = array();
				foreach ( $columns as $key => $label ) {
					$clean_data[ $key ] = isset( $data[ $key ] ) ? wcbo_esc_csv( $data[ $key ] ) : '';
				}

				fputcsv( $export, $clean_data );

			}
		}

		fclose( $export );
		$export = ob_get_clean();

		echo $export;
		die();
	}

	/**
	 * Send submitted email to specified recipients.
	 *
	 * @return void
	 */
	public function email_tickets() {
		if ( empty( $_POST['action'] ) ) {
			return;
		}

		if ( 'email_tickets' !== $_POST['action'] ) {
			return;
		}

		try {
			$required_fields = array(
				'product_id'              => __( 'Ticket product is required.', 'woocommerce-box-office' ),
				'email_subject'           => __( 'Empty email subject.', 'woocommerce-box-office' ),
				'email_body'              => __( 'Empty email body.', 'woocommerce-box-office' ),
				'tools_send_emails_nonce' => __( 'Missing security nonce for this request. Please try again.', 'woocommerce-box-office' ),
			);
			foreach ( $required_fields as $field => $error_message ) {
				if ( empty( $_POST[ $field ] ) ) {
					throw new Exception( $error_message );
				}
			}

			if ( ! wp_verify_nonce( $_POST['tools_send_emails_nonce'], 'woocommerce_box_office_tools_send_emails' ) ) {
				throw new Exception( __( 'Invalid security nonce for this request. Please try again.', 'woocommerce-box-office' ) );
			}

			// Create send-batch-emails job.
			$job_id = wp_insert_post( array(
				'post_type'    => 'event_ticket_email',
				'post_status'  => 'pending',
				'post_title'   => sanitize_text_field( $_POST['email_subject'] ),
				'post_content' => wp_kses_post( $_POST['email_body'] ),
			) );

			if ( $job_id ) {
				add_post_meta( $job_id, '_product_id', $_POST['product_id'] );

				$ticket_ids = get_posts( array(
					'post_type'      => 'event_ticket',
					'posts_per_page' => -1,
					'post_status'    => array( 'publish' ),
					'fields'         => 'ids',
					'orderby'        => 'ID',
					'order'          => 'ASC',
					'cache_results'  => false,
					'meta_query'     => array(
						array(
							'key'   => '_product_id',
							'value' => absint( $_POST['product_id'] ),
						),
					),
				) );

				foreach ( $ticket_ids as $ticket_id ) {
					add_post_meta( $job_id, '_ticket_id', $ticket_id );
				}

				add_post_meta( $job_id, '_ticket_ids', $ticket_ids );

				WCBO()->components->logger->log( sprintf( 'Created e-mail job for %s tickets', count( $ticket_ids ) ), $job_id );

				add_action( 'admin_notices', array( $this, 'notice_email_job_queued' ) );

			} else {
				throw new Except( 'Failed to create email job. Please try again.', 'woocommerce-box-office' );
			}
		} catch ( Exception $e ) {
			$this->_errors[] = $e->getMessage();
		}
	}

	/**
	 * Notice message when email job is queued.
	 */
	public function notice_email_job_queued() {
		?>
		<div class="updated">
			<p><?php _e( 'Sending email job is successfully queued.', 'woocommerce-box-office' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Load tools page content.
	 *
	 * @return void
	 */
	public function tools_page() {
		$errors = $this->_get_formatted_errors();
		require_once( WCBO()->dir . 'includes/views/admin/tools.php' );
	}

	/**
	 * Get formatted errors.
	 *
	 * @return string
	 */
	protected function _get_formatted_errors() {
		$ret = '';
		foreach ( $this->_errors as $error ) {
			$ret .= sprintf( '<p>%s</p>', esc_html( $error ) );
		}

		if ( ! empty( $ret ) ) {
			$ret = sprintf( '<div class="notice error">%s</div>', $ret );
		}

		return $ret;
	}
}
