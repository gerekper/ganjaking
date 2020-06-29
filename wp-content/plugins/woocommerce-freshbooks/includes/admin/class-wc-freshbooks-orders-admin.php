<?php
/**
 * WooCommerce FreshBooks
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce FreshBooks to newer
 * versions in the future. If you wish to customize WooCommerce FreshBooks for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-freshbooks/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * FreshBooks Admin Order class
 *
 * Handles customizations to the Orders/Edit Order screens
 *
 * @since 3.0
 */
class WC_FreshBooks_Orders_Admin {


	/**
	 * Add various admin hooks/filters
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// add 'Invoice Status' column on Orders screen
		add_filter( 'manage_edit-shop_order_columns',        array( $this, 'add_order_status_column_header' ), 20 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_order_status_column' ) );

		// Add 'Create & Send Invoice' action on Orders screen
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_order_actions' ), 10, 2 );

		// add bulk order filter for invoice status
		add_action( 'restrict_manage_posts', array( $this, 'filter_orders_by_invoice_status' ) , 20 );
		add_filter( 'request',               array( $this, 'filter_orders_by_invoice_status_query' ) );

		// add bulk action for invoice actions
		add_action( 'admin_footer-edit.php', array( $this, 'add_order_bulk_actions' ) );
		add_action( 'load-edit.php',         array( $this, 'process_order_bulk_actions' ) );

		// add invoice/payment order meta box order actions
		add_filter( 'woocommerce_order_actions', array( $this, 'add_order_meta_box_actions' ) );

		// process order meta box order actions
		add_action( 'woocommerce_order_action_wc_freshbooks_create_and_send_invoice', array( $this, 'process_order_meta_box_actions' ) );
		add_action( 'woocommerce_order_action_wc_freshbooks_create_draft_invoice',    array( $this, 'process_order_meta_box_actions' ) );
		add_action( 'woocommerce_order_action_wc_freshbooks_send_invoice',            array( $this, 'process_order_meta_box_actions' ) );
		add_action( 'woocommerce_order_action_wc_freshbooks_apply_invoice_payment',   array( $this, 'process_order_meta_box_actions' ) );
		add_action( 'woocommerce_order_action_wc_freshbooks_update_invoice',          array( $this, 'process_order_meta_box_actions' ) );

		// add 'FreshBooks Invoice' order meta box
		add_action( 'add_meta_boxes', array( $this, 'add_order_meta_box' ) );

		// add Freshbooks Invoice Status to order preview modal
		add_action( 'woocommerce_admin_order_preview_end',               array( $this, 'render_order_preview_modal_status' ) );
		add_filter( 'woocommerce_admin_order_preview_get_order_details', array( $this, 'get_order_preview_status_markup'), 10, 2 );
	}


	/**
	 * Add 'FreshBooks Invoice' meta-box to 'Edit Order' page
	 *
	 * @since 3.0
	 */
	public function add_order_meta_box() {

		add_meta_box(
			'wc_freshbooks_invoice_meta_box',
			__( 'FreshBooks Invoice Info', 'woocommerce-freshbooks' ),
			array( $this, 'render_order_meta_box' ),
			'shop_order',
			'side',
			'high'
		);
	}


	/**
	 * Adds the invoice status to the bottom of the order preview modal template.
	 *
	 * @since 3.11.0
	 */
	public function render_order_preview_modal_status() {

		printf( '<div class="freshbooks_preview_modal_status"><strong>%s:</strong> <div class="freshbooks_status">{{{ data.freshbooks_status_mark }}}</div></div>', esc_html__( 'Freshbooks Invoice Status', 'woocommerce-freshbooks' ) );
	}


	/**
	 * Gets the freshbooks status markup for the order preview modal.
	 *
	 * @since 3.11.0
	 *
	 * @param array $data
	 * @param \WC_Order $order
	 * @return array $data
	 */
	public function get_order_preview_status_markup( $data, $order ) {

		if ( $order ) {
			$freshbooks_order = new \WC_FreshBooks_Order( $order->get_id() );

			$data['freshbooks_status_mark'] = $this->get_freshbooks_status_mark( $freshbooks_order );
		}

		return $data;
	}


	/**
	 * Display the 'FreshBooks Invoice' meta-box on the Edit Order screen
	 *
	 * @since 3.0
	 */
	public function render_order_meta_box() {
		global $post;

		$order = new \WC_FreshBooks_Order( $post->ID );

		if ( $order->invoice_was_created() ) {

			?>
				<div class="wc-freshbooks-invoice-meta-box">
					<div class="wc-freshbooks-meta-box-title">
						<strong><?php esc_html_e( 'Status', 'woocommerce-freshbooks' ) ?>: </strong>
						<?php echo $this->get_freshbooks_status_mark( $order ); ?>
					</div>
					<div class="wc-freshbooks-meta-box-title">
						<strong><?php esc_html_e( 'Number', 'woocommerce-freshbooks' ) ?>: </strong>
						<a href="<?php echo esc_attr( $order->invoice->view_url ); ?>" title="<?php esc_attr_e( 'View in FreshBooks', 'woocommerce-freshbooks' ); ?>"><?php echo esc_html( $order->invoice->number ); ?></a>
					</div>
				</div>
			<?php

		} else {

			?><p><?php esc_html_e( 'Create an invoice for this order to see invoice information.', 'woocommerce-freshbooks' ); ?></p><?php
		}
	}


	/**
	 * Gets the status mark for an order to display in the admin.
	 *
	 * @internal
	 *
	 * @since 3.11.0
	 *
	 * @param \WC_Freshbooks_Order $order
	 * @return string
	 */
	public function get_freshbooks_status_mark( $order ) {

		if ( $order ) {
			return sprintf( '<mark class="%s">%s</mark>', esc_attr( $order->get_invoice_status() ), esc_html( $order->get_invoice_status_for_display() ) );
		}

		return '';
	}


	/**
	 * Add 'Invoice Status' column header to the Orders screen
	 *
	 * @since 3.0
	 * @param array $column_headers
	 * @return array new columns
	 */
	public function add_order_status_column_header( $column_headers ) {

		$new_column_headers = array();

		foreach ( $column_headers as $column_id => $column_info ) {

			$new_column_headers[ $column_id ] = $column_info;

			if ( 'order_status' === $column_id ) {
				$new_column_headers['freshbooks_invoice_status'] = __( 'Invoice Status', 'woocommerce-freshbooks' );
			}
		}

		return $new_column_headers;
	}


	/**
	 * Add 'FreshBooks Invoice Status' column content to the 'Orders' page
	 *
	 * @since 3.0
	 * @param array $column
	 */
	public function add_order_status_column( $column ) {
		global $post;

		if ( 'freshbooks_invoice_status' === $column ) {

			$order = new \WC_FreshBooks_Order( $post->ID );

			printf( '<mark class="%1$s">%2$s</mark>', esc_attr( $order->get_invoice_status() ), esc_html( $order->get_invoice_status_for_display() ) );
		}
	}


	/**
	 * Adds 'Create & Send Invoice' order action icon to Orders screen
	 *
	 * Processed via Ajax
	 *
	 * @since 3.0
	 * @param array $actions order actions
	 * @param \WC_Order $the_order order object
	 * @return array
	 */
	public function add_order_actions( $actions, $the_order ) {

		$order = new \WC_FreshBooks_Order( $the_order );

		if ( ! $order->invoice_was_created() ) {

			$order_id = $order->get_id();

			$actions[] = [
				'action' => 'wc_freshbooks_create_and_send_invoice',
				'url'    => wp_nonce_url( admin_url( "admin-ajax.php?action=wc_freshbooks_create_and_send_invoice&order_id={$order_id}" ), 'wc_freshbooks_create_and_send_invoice' ),
				'name'   => __( 'Create & Send Invoice', 'woocommerce-freshbooks' ),
			];
		}

		return $actions;
	}


	/**
	 * Add bulk filter for order invoice status
	 *
	 * @since 3.0
	 */
	public function filter_orders_by_invoice_status() {
		global $typenow, $wp_query;

		if ( 'shop_order' !== $typenow ) {
			return;
		}

		?>
		<select
			name="wc_freshbooks_invoice_status"
			id="wc_freshbooks_invoice_status"
			class="wc-enhanced-select"
			style="width:200px;">
			<option value=""><?php esc_html_e( 'Show all Invoice Statuses', 'woocommerce-freshbooks' ); ?></option>
			<?php foreach ( \WC_FreshBooks_Order::get_invoice_statuses() as $status ) : ?>
				<option value="<?php echo esc_attr( $status ); ?>" <?php selected( $status, ( isset( $wp_query->query['freshbooks_invoice_status'] ) ? $wp_query->query['freshbooks_invoice_status'] : '' ), true ); ?>>
					<?php echo esc_html( ucwords( $status ) ); ?>
				</option>
			<?php endforeach; ?>
			<option value="-1" <?php selected( '-1', ( isset( $wp_query->query['freshbooks_invoice_status'] ) ? $wp_query->query['freshbooks_invoice_status'] : '' ), true ); ?>>
				<?php esc_html_e( 'Not Exported', 'woocommerce-freshbooks' ); ?>
			</option>
		</select>
		<?php
	}


	/**
	 * Process bulk filter action for order invoice status
	 *
	 * @since 3.0
	 * @param array $vars query vars without filtering
	 * @return array $vars query vars with (maybe) filtering
	 */
	public function filter_orders_by_invoice_status_query( $vars ) {
		global $typenow;

		if ( 'shop_order' === $typenow && isset( $_GET['wc_freshbooks_invoice_status'] ) && ! empty( $_GET['wc_freshbooks_invoice_status'] ) ) {

			if ( '-1' === $_GET['wc_freshbooks_invoice_status'] ) {

				$vars['meta_query'] = isset( $vars['meta_query'] ) && is_array( $vars['meta_query'] ) ? $vars['meta_query'] : array();
				$vars['meta_query'][] = array(
					'key' => '_wc_freshbooks_invoice_status',
					'compare' => 'NOT EXISTS',
				);
			} else {

				$vars['meta_key']   = '_wc_freshbooks_invoice_status';
				$vars['meta_value'] = $_GET['wc_freshbooks_invoice_status'];
			}
		}

		return $vars;
	}


	/**
	 * Adds custom bulk action to the Orders screen bulk action drop-down
	 *
	 * + Create & send invoice
	 * + Create draft invoice
	 * + Apply invoice payment
	 *
	 * @since 3.0
	 */
	public function add_order_bulk_actions() {
		global $post_type, $post_status;

		if ( 'shop_order' === $post_type && 'trash' !== $post_status ) {
			?>
			<script type="text/javascript">
				jQuery( document ).ready( function ( $ ) {
					$( 'select[name^=action]' ).append(
						$( '<option>' ).val( 'wc_freshbooks_create_and_send_invoice' ).html( '<?php esc_html_e( 'Create & Send Invoice', 'woocommerce-freshbooks' ); ?>' ),
						$( '<option>' ).val( 'wc_freshbooks_create_draft_invoice' ).html( '<?php esc_html_e( 'Create Draft Invoice', 'woocommerce-freshbooks' ); ?>' ),
						$( '<option>' ).val( 'wc_freshbooks_apply_invoice_payment' ).html( '<?php esc_html_e( 'Apply Invoice Payment', 'woocommerce-freshbooks' ); ?>' ),
						$( '<option>' ).val( 'wc_freshbooks_update_invoice_status' ).html( '<?php esc_html_e( 'Update Invoice Status', 'woocommerce-freshbooks' ); ?>' ),
						$( '<option>' ).val( 'wc_freshbooks_update_invoice_from_order' ).html( '<?php esc_html_e( 'Update Invoice from Order', 'woocommerce-freshbooks' ); ?>' )
					);
				});
			</script>
		<?php
		}
	}


	/**
	 * Processes the  custom bulk actions on the Orders screen bulk action drop-down
	 *
	 * + Create & send invoice
	 * + Create draft invoice
	 * + Apply invoice payment
	 *
	 * @since 3.0
	 */
	public function process_order_bulk_actions() {
		global $typenow;

		if ( 'shop_order' === $typenow ) {

			// get the action
			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action        = $wp_list_table->current_action();

			$actions = array(
				'wc_freshbooks_create_and_send_invoice',
				'wc_freshbooks_create_draft_invoice',
				'wc_freshbooks_apply_invoice_payment',
				'wc_freshbooks_update_invoice_status',
				'wc_freshbooks_update_invoice_from_order',
			);

			// return if not processing our actions
			if ( ! in_array( $action, $actions ) ) {
				return;
			}

			// security check
			check_admin_referer( 'bulk-posts' );

			// make sure order IDs are submitted
			if ( isset( $_REQUEST['post'] ) ) {
				$order_ids = array_map( 'absint', $_REQUEST['post'] );
			}

			// return if there are no orders to export
			if ( empty( $order_ids ) ) {
				return;
			}

			// give ourselves an unlimited timeout if possible
			@set_time_limit( 0 );

			foreach ( $order_ids as $order_id ) {

				$order = new \WC_FreshBooks_Order( $order_id );

				switch ( $action ) {

					case 'wc_freshbooks_create_and_send_invoice':
						$order->create_invoice();
						break;

					case 'wc_freshbooks_create_draft_invoice':
						$order->create_invoice( false );
						break;

					case 'wc_freshbooks_apply_invoice_payment':
						$order->apply_invoice_payment();
						break;

					case 'wc_freshbooks_update_invoice_status':
						$order->refresh_invoice();
						break;

					case 'wc_freshbooks_update_invoice_from_order':
						$order->update_invoice_from_order();
						break;

					default:
						break;
				}
			}
		}
	}


	/**
	 * Add order actions to the Edit Order screen
	 *
	 * + Create & send invoice
	 * + Create draft invoice
	 * + Send invoice
	 * + Apply invoice payment
	 *
	 * @since 3.0
	 * @param array $actions
	 * @return array
	 */
	public function add_order_meta_box_actions( $actions ) {
		global $theorder;

		$order = new \WC_FreshBooks_Order( $theorder );

		// create invoice
		if ( ! $order->invoice_was_created() ) {

			$actions['wc_freshbooks_create_and_send_invoice'] = __( 'Create & Send Invoice', 'woocommerce-freshbooks' );
			$actions['wc_freshbooks_create_draft_invoice']    = __( 'Create Draft Invoice', 'woocommerce-freshbooks' );

		} else {

			$actions['wc_freshbooks_update_invoice'] = __( 'Update Invoice from Order', 'woocommerce-freshbooks' );
		}

		// send invoice
		if ( $order->invoice_was_created() && ! $order->invoice_was_sent() ) {
			$actions['wc_freshbooks_send_invoice'] = __( 'Send Invoice', 'woocommerce-freshbooks' );
		}

		// apply invoice
		if ( $order->invoice_needs_payment() ) {
			$actions['wc_freshbooks_apply_invoice_payment'] = __( 'Apply Invoice Payment', 'woocommerce-freshbooks' );
		}

		return $actions;
	}


	/**
	 * Handle actions from the Edit Order order actions select box
	 *
	 * @since 3.0
	 * @param \WC_Order $order Order object
	 */
	public function process_order_meta_box_actions( $order ) {

		$order = new \WC_FreshBooks_Order( $order );

		switch ( current_action() ) {

			case 'woocommerce_order_action_wc_freshbooks_create_and_send_invoice':
				$order->create_invoice();
			break;

			case 'woocommerce_order_action_wc_freshbooks_create_draft_invoice':
				$order->create_invoice( false );
			break;

			case 'woocommerce_order_action_wc_freshbooks_send_invoice':
				$order->send_invoice();
			break;

			case 'woocommerce_order_action_wc_freshbooks_apply_invoice_payment':
				$order->apply_invoice_payment();
			break;

			case 'woocommerce_order_action_wc_freshbooks_update_invoice':
				$order->update_invoice_from_order();
			break;

			default:
				return;
		}
	}


}
