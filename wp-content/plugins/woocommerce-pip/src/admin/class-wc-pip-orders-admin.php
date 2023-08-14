<?php
/**
 * WooCommerce Print Invoices/Packing Lists
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

/**
 * PIP Admin Order class
 *
 * Handles customizations to the Orders/Edit Order screens
 *
 * @since 3.0.0
 */
class WC_PIP_Orders_Admin {


	/**
	 * Add various admin hooks/filters
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		$hpos_enabled = Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled();

		// add 'Print Count' orders page column header
		if ( $hpos_enabled ) {
			add_filter( 'woocommerce_shop_order_list_table_columns', [ $this, 'add_order_status_column_header' ], 20 );
		} else {
			add_filter( 'manage_edit-shop_order_columns', [ $this, 'add_order_status_column_header' ], 20 );
		}

		// add information to the columns in the orders edit screen
		if ( $hpos_enabled ) {
			add_action( 'manage_woocommerce_page_wc-orders_custom_column', [ $this, 'add_order_status_column_content' ], 20, 2 );
		} else {
			add_action( 'manage_shop_order_posts_custom_column', [ $this, 'add_order_status_column_content' ], 20, 2 );
		}

		// add invoice information to the order preview
		add_filter( 'woocommerce_admin_order_preview_get_order_details', array( $this, 'add_order_preview_invoice_number' ), 10, 2 );
		add_action( 'woocommerce_admin_order_preview_start',             array( $this, 'display_order_preview_invoice_number' ) );

		if ( $hpos_enabled ) {
			// add bulk order filter for printed / non-printed orders
			add_action( 'woocommerce_order_list_table_restrict_manage_orders', [ $this, 'filter_orders_by_print_status' ] , 20 );
		} else {
			add_action( 'restrict_manage_posts', [ $this, 'filter_orders_by_print_status' ] , 20 );
		}

		// filter orders by document status
		if ( $hpos_enabled ) {
			add_filter( 'woocommerce_shop_order_list_table_prepare_items_query_args', [ $this, 'filter_orders_by_print_status_query' ] );
		} else {
			add_filter( 'request', [ $this, 'filter_orders_by_print_status_query' ] );
		}

		// add invoice numbers to shop orders search fields in admin
		add_filter( 'woocommerce_shop_order_search_fields', [ $this, 'make_invoice_numbers_searchable' ] );
		add_filter( 'woocommerce_order_table_search_query_meta_keys', [ $this, 'make_invoice_numbers_searchable' ] );

		// generate invoice number upon order save
		add_action( 'woocommerce_after_order_object_save', [ $this, 'generate_invoice_number_on_order_save' ], 20, 2 );

		// display invoice number on order screen
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'display_order_invoice_number' ), 42, 1 );

		// add buttons for PIP actions for individual orders in Orders screen table
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_order_actions' ), 10, 2 );

		// add bulk actions to the Orders screen table bulk action drop-downs according to HPOS support
		if ( $hpos_enabled ) {
			add_filter( 'bulk_actions-woocommerce_page_wc-orders', [ $this, 'add_order_bulk_actions' ] );
		} else {
			add_filter( 'bulk_actions-edit-shop_order', [ $this, 'add_order_bulk_actions' ] );
		}

		// add actions to individual Order edit screen
		add_filter( 'woocommerce_order_actions', array( $this, 'add_order_meta_box_actions' ), 10, 2 );

		// add a modal for displaying results of bulk actions and order actions
		add_action( 'admin_footer', [ $this, 'render_order_actions_modal_template' ] );
	}


	/**
	 * Renders the order actions modal markup.
	 *
	 * @internal
	 *
	 * @since 3.7.1
	 */
	public function render_order_actions_modal_template() {

		if ( Framework\SV_WC_Order_Compatibility::is_orders_screen() || Framework\SV_WC_Order_Compatibility::is_order_edit_screen() ) :

			?>
			<script type="text/template" id="tmpl-wc-pip-action-modal">
				<div class="wc-backbone-modal wc-pip-action-modal">
					<div class="wc-backbone-modal-content">
						<section class="wc-backbone-modal-main" role="main">
							<header class="wc-backbone-modal-header">
								<h1>{{{data.heading}}}</h1>
								<button class="modal-close modal-close-link dashicons dashicons-no-alt">
									<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce-pip' ); ?></span>
								</button>
							</header>
							<article>{{{data.message}}}</article>
							<footer>
								<div class="inner">
									<# if ( data.done ) { #>
										<button id="wc-pip-dismiss-done-order-action" class="button button-large button-primary"><?php esc_html_e( 'Done', 'woocommerce-pip' ); ?></button>
									<# } else { #>
										<button id="wc-pip-cancel-order-action" class="button button-large modal-close"><?php esc_html_e( 'Cancel', 'woocommerce-pip' ); ?></button>
										<a
											id="wc-pip-confirm-order-action"
											class="button button-large button-primary"
											data-type="{{{data.type}}}"
											data-action="{{{data.action}}}"
											data-orders="{{{data.orders}}}"
											target="_blank"
											href="{{{data.url}}}">{{{data.label}}}</a>
									<# } #>
								</div>
							</footer>
						</section>
					</div>
				</div>
				<div class="wc-backbone-modal-backdrop modal-close"></div>
			</script>
			<?php

		endif;
	}


	/**
	 * Generates the invoice number upon order save.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 * @deprecated since 3.13.0
	 *
	 * @param int|mixed $post_id Post id
	 * @param WP_Post|mixed $post Post object
	 */
	public function generate_invoice_number_order_save( $post_id, $post ) {

		wc_deprecated_function( __METHOD__, '3.13.0', __CLASS__ . '::generate_invoice_number_on_order_save()' );
	}


	/**
	 * Generates the invoice number for an order upon saving.
	 *
	 * @internal
	 *
	 * @since 3.13.0
	 *
	 * @param WC_Order|WC_Data $order
	 */
	public function generate_invoice_number_on_order_save( $order ) {

		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		/* This filter is documented in /src/class-wc-pip-handler.php */
		if ( false === apply_filters( 'wc_pip_generate_invoice_number_on_order_paid', true ) ) {
			return;
		}

		// generate the invoice number, will trigger post meta update
		if ( $order->is_paid() ) {

			$document = wc_pip()->get_document( 'invoice', [ 'order' => $order ] );

			if ( $document ) {
				$document->get_invoice_number();
			}
		}
	}


	/**
	 * Display the invoice number in the order screen meta box
	 *
	 * @since 3.0.0
	 * @param \WC_Order|int $wc_order Order object or id
	 */
	public function display_order_invoice_number( $wc_order ) {

		if ( is_numeric( $wc_order ) ) {
			$wc_order = wc_get_order( $wc_order );
		}

		$order_id = $wc_order instanceof \WC_Order ? $wc_order->get_id() : null;

		// only display if the invoice number was generated before
		if ( is_numeric( $order_id ) && $order_id > 0 ) :

			$document = wc_pip()->get_document( 'invoice', array( 'order' => $wc_order ) );

			if ( $document && $document->has_invoice_number() ) :

				?>
				<p class="form-field form-field-wide wc-pip-invoice-number">
					<label for="pip-invoice-number"><?php esc_html_e( 'Invoice number:', 'woocommerce-pip' ); ?></label>
					<strong><?php echo esc_html( $document->get_invoice_number() ); ?></strong>
				</p>
				<?php

			endif;

		endif;
	}


	/**
	 * Get individual order actions
	 *
	 * @since 3.0.0
	 * @return array Associative array of actions with their labels
	 */
	public function get_actions() {

		$actions = array();

		if ( wc_pip()->get_handler_instance()->current_admin_user_can_manage_documents() ) {

			/**
			 * Filters the admin order actions.
			 *
			 * @since 3.0.0
			 * @param array $actions
			 */
			$actions = apply_filters( 'wc_pip_admin_order_actions', array(
				'wc_pip_print_invoice'           => __( 'Print Invoice', 'woocommerce-pip' ),
				'wc_pip_send_email_invoice'      => __( 'Email Invoice', 'woocommerce-pip' ),
				'wc_pip_print_packing_list'      => __( 'Print Packing List', 'woocommerce-pip' ),
				'wc_pip_send_email_packing_list' => __( 'Email Packing List', 'woocommerce-pip' ),
			) );
		}

		return $actions;
	}


	/**
	 * Get orders bulk actions.
	 *
	 * @since 3.0.0
	 *
	 * @return array associative array of actions with their labels
	 */
	public function get_bulk_actions() : array {

		$shop_manager_actions = array();

		if ( wc_pip()->get_handler_instance()->current_admin_user_can_manage_documents() ) {

			/**
			 * Filters the bulk order actions.
			 *
			 * @since 3.0.0
			 *
			 * @param array $actions
			 */
			$shop_manager_actions = apply_filters( 'wc_pip_admin_order_bulk_actions', array_merge( $this->get_actions(), array(
				'wc_pip_print_pick_list'      => __( 'Print Pick List', 'woocommerce-pip' ),
				'wc_pip_send_email_pick_list' => __( 'Email Pick List', 'woocommerce-pip' ),
			) ) );
		}

		return $shop_manager_actions;
	}


	/**
	 * Adds 'Invoice' and 'Packing List' column headers to 'Orders' page immediately before the 'Actions' column.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @param array|mixed $columns
	 * @return array|mixed $new_columns
	 */
	public function add_order_status_column_header( $columns ) {

		if ( ! is_array( $columns ) ) {
			return $columns;
		}

		$new_columns = [];

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'order_total' === $column_name ) {

				$new_columns['pip_print_invoice']      = __( 'Invoice', 'woocommerce-pip' );
				$new_columns['pip_print_packing-list'] = __( 'Packing List', 'woocommerce-pip' );
			}
		}

		return $new_columns;
	}


	/**
	 * Adds content to the order columns.
	 *
	 * - The invoice number (if already generated) under the order ID and customer info
	 * - The invoice print status
	 * - The packing list print status
	 * - Hidden HTML content in the order actions that will be used to output a document print button in JS
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @param string|mixed $column name of column being displayed
	 * @param int|\WC_Order $order_id the post (order) ID or order object if HPOS is available
	 */
	public function add_order_status_column_content( $column, $order_id = null ) {
		global $theorder;

		if ( $order_id instanceof \WC_Order ) {
			$order = $order_id;
		} elseif ( $theorder instanceof \WC_Order ) {
			$order = $theorder;
		} else {
			$order = wc_get_order( $order_id );
		}

		if ( ! $order || ! is_string( $column ) ) {
			return;
		}

		// Invoice No. ('order_number' is for WC 3.3+)
		if ( 'order_title' === $column || 'order_number' === $column ) {

			$invoice_number = $order->get_meta( '_pip_invoice_number' );

			if ( ! empty( $invoice_number ) && is_string( $invoice_number ) ) {
				/* translators: Placeholder: %s - invoice number */
				echo '<span class="wc-pip-invoice-number">' . sprintf( __( 'Invoice: %s', 'woocommerce-pip' ), $invoice_number ) . '</span>';
			}

		// Invoice print status
		} elseif ( 'pip_print_invoice' === $column ) {

			echo $this->get_print_status( $order, 'invoice' );

		// Packing List print status
		} elseif ( 'pip_print_packing-list' === $column ) {

			echo $this->get_print_status( $order, 'packing_list' );

		// hidden content that will be injected into a WP Pointer via JS ('wc_actions' here is for WC 3.3+)
		} elseif ( 'order_actions' === $column || 'wc_actions' === $column ) {

			?>
			<div id="wc-pip-pointer-order-actions-<?php echo esc_attr( $order->get_id() ); ?>" style="display:none;">

				<input type="hidden" value="<?php echo esc_attr( $order->get_id() ); ?>" />

				<h3 class="wp-pointer-header"><?php

					/* translators: Placeholder: %s - order number */
					printf( esc_html__( 'Invoice/Packing List (Order #%s)', 'woocommerce-pip' ), $order->get_order_number() );

				?></h3>

				<div class="wp-pointer-inner-content">
					<?php

					$print = $email = $other = [];

					foreach ( $this->get_actions() as $action => $name ) :

						ob_start();

						?>
						<button
								class="button button-small <?php echo sanitize_html_class( $action ); ?> wc-pip-document-tooltip-order-action"
								data-order-id="<?php echo esc_attr( $order->get_id() ); ?>"
								data-action="<?php echo esc_attr( $action ); ?>">
							<?php echo esc_html( $name ); ?>
						</button>
						<?php

						if ( false !== strpos( $action, 'print' ) ) {
							$print[] = ob_get_clean();
						} elseif ( false !== strpos( $action, 'email' ) ) {
							$email[] = ob_get_clean();
						} else {
							$other[] = ob_get_clean();
						}

					endforeach;

					$max_rows = max( count( $print ), count( $email ), count( $other ) );

					?>
					<table>
						<tbody>
							<tr>
								<?php if ( ! empty( $print ) ) : ?>
									<th><?php esc_html_e( 'Print', 'woocommerce-pip' ); ?></th>
								<?php endif; ?>
								<?php if ( ! empty( $email ) ) : ?>
									<th><?php esc_html_e( 'Email', 'woocommerce-pip' ); ?></th>
								<?php endif; ?>
								<?php if ( ! empty( $other ) ) : ?>
									<th>&nbsp;</th>
								<?php endif; ?>
							</tr>
							<?php

							$row = 0;

							while ( $row <= $max_rows - 1 ) :

								echo '<tr>';

								if ( isset( $print[ $row ] ) ) {
									echo '<td>' . $print[ $row ] . '</td>';
								} elseif ( ! empty( $print ) ) {
									echo '<td></td>';
								}

								if ( isset( $email[ $row ] ) ) {
									echo '<td>' . $email[ $row ] . '</td>';
								} elseif ( ! empty( $email ) ) {
									echo '<td></td>';
								}

								if ( isset( $other[ $row ] ) ) {
									echo '<td>' . $other[ $row ] . '</td>';
								} elseif ( ! empty( $other ) ) {
									echo '<td></td>';
								}

								echo '</tr>';

								$row++;

							endwhile;

							?>
						</tbody>
					</table>
				</div>
			</div>
			<?php
		}
	}


	/**
	 * Adds the invoice number to the order data meant for order preview.
	 *
	 * @since 3.4.0
	 *
	 * @internal
	 *
	 * @param array $data associative array with order data
	 * @param \WC_Order $order the order object
	 * @return array
	 */
	public function add_order_preview_invoice_number( $data, $order ) {

		if ( $order ) {

			$invoice_number = $order->get_meta( '_pip_invoice_number' );

			$data['invoice_number'] = ! empty( $invoice_number ) && is_string( $invoice_number ) ? $invoice_number : '&mdash;';
		}

		return $data;
	}


	/**
	 * Displays the invoice number information in order preview modals.
	 *
	 * @internal
	 *
	 * @since 3.4.0
	 */
	public function display_order_preview_invoice_number() {

		?>
		<div class="wc-pip-order-preview">
			<h2><?php esc_html_e( 'Invoice Number', 'woocommerce-pip' ); ?></h2>
			<span class="wc-pip-invoice-number">{{{ data.invoice_number }}}</span>
		</div>
		<?php
	}


	/**
	 * Returns the order documents print status (whether a document had a print window open).
	 *
	 * @since 3.0.0
	 *
	 * @param int|WC_Order $order  order ID or object
	 * @param string $document_type PIP Document type
	 * @return string HTML
	 */
	private function get_print_status( $order, string $document_type ) : string {

		$count = Framework\SV_WC_Order_Compatibility::get_order_meta( $order, "_wc_pip_{$document_type}_print_count" );

		return is_numeric( $count ) && (int) $count > 0 ? '&#10004' : '<strong>&ndash;</strong>';
	}


	/**
	 * Adds order action icons to the Orders screen table for printing the invoice and packing list.
	 *
	 * Processed via Ajax.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @param array $actions Order actions
	 * @param int|\WC_Order $order Order object or order ID
	 * @return array
	 */
	public function add_order_actions( $actions, $order ) {

		if ( ! $order instanceof \WC_Order && is_numeric( $order ) ) {
			$wc_order = wc_get_order( $order );
		} else {
			$wc_order = $order;
		}

		if ( $wc_order instanceof \WC_Order && wc_pip()->get_handler_instance()->current_admin_user_can_manage_documents() ) {

			$actions = array_merge( $actions, [ [
				'name'   => __( 'Print Invoices / Packing Lists', 'woocommerce-pip' ),
				'action' => 'wc_pip_document',
				'url'    => sprintf( '#%s', $wc_order->get_id() ),
			] ] );
		}

		return $actions;
	}


	/**
	 * Adds custom bulk actions to the Orders screen table bulk action drop-down.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @param array<string, string>|mixed $bulk_actions associative array of bulk actions and their labels
	 * @return array<string, string>|mixed
	 */
	public function add_order_bulk_actions( $bulk_actions ) {

		return is_array( $bulk_actions ) ? array_merge( $bulk_actions, $this->get_bulk_actions() ) : $bulk_actions;
	}


	/**
	 * Add order actions to the Edit Order screen
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @param array<string, string> $actions
	 * @param \WC_Order|null $order
	 * @return array
	 */
	public function add_order_meta_box_actions( $actions, $order = null ) {
		global $post;

		// bail out if the order hasn't been saved yet according to HPOS availability
		if ( Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled() ) {
			if ( ! $order instanceof \WC_Order || ! $order->get_id() ) {
				return $actions;
			}
		} elseif ( $post instanceof \WP_Post && 'auto-draft' === $post->post_status ) {
			return $actions;
		}

		return array_merge( $actions, $this->get_actions() );
	}


	/**
	 * Display a dropdown to filter orders by print status
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 */
	public function filter_orders_by_print_status() {

		if ( Framework\SV_WC_Order_Compatibility::is_orders_screen() ) :

			$options  = array(
				'invoice_not_printed'      => __( 'Invoice not printed', 'woocommerce-pip' ),
				'invoice_printed'          => __( 'Invoice printed', 'woocommerce-pip' ),
				'packing_list_not_printed' => __( 'Packing List not printed', 'woocommerce-pip' ),
				'packing_list_printed'     => __( 'Packing List printed', 'woocommerce-pip' ),
				'pick_list_not_printed'    => __( 'Pick List not printed', 'woocommerce-pip' ),
				'pick_list_printed'        => __( 'Pick List printed', 'woocommerce-pip' ),
			);

			$selected = isset( $_GET['_shop_order_pip_print_status'] ) ? $_GET['_shop_order_pip_print_status'] : '';

			?>
			<select name="_shop_order_pip_print_status" id="dropdown_shop_order_pip_print_status">
				<option value=""><?php esc_html_e( 'Show all print statuses', 'woocommerce-pip' ); ?></option>
				<?php foreach ( $options as $option_value => $option_name ) : ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $selected, $option_value ); ?>><?php echo esc_html( $option_name ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php

		endif;
	}


	/**
	 * Filter orders by print status.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @param array<string, mixed>|mixed $args WP_Query vars or WC_Query args according to filter
	 * @return array|mixed
	 */
	public function filter_orders_by_print_status_query( $args ) {

		if ( ! is_array( $args ) ) {
			return $args;
		}

		switch ( current_filter() ) {

			case 'request' :
				return array_merge( $args, $this->get_query_vars_for_filtering_orders_by_print_status() );

			case 'woocommerce_shop_order_list_table_prepare_items_query_args' :

				$meta_query = $this->get_query_vars_for_filtering_orders_by_print_status();

				if ( empty( $meta_query ) ) {
					return $args;
				}

				if ( ! isset( $args['meta_query'] ) ) {
					$args['meta_query'] = [];
				} else {
					$args['meta_query']['relation'] = 'AND';
				}

				$args['meta_query'] = array_merge( $args['meta_query'], [ $meta_query ] );

				return $args;

			default :
				return $args;
		}
	}


	/**
	 * Gets the order meta query arguments for filtering orders by document status.
	 *
	 * @since 3.13.0
	 *
	 * @return array<string, mixed>
	 */
	private function get_query_vars_for_filtering_orders_by_print_status() : array
	{
		$meta_query = [];

		if ( isset( $_GET['_shop_order_pip_print_status'] ) && Framework\SV_WC_Order_Compatibility::is_orders_screen() ) {

			$meta = $compare = $value = '';

			switch ( $_GET['_shop_order_pip_print_status'] ) {

				case 'invoice_not_printed' :

					$meta    = '_wc_pip_invoice_print_count';
					$compare = 'NOT EXISTS';

					break;

				case 'invoice_printed' :

					$meta    = '_wc_pip_invoice_print_count';
					$compare = '>';
					$value   = '0';

					break;

				case 'packing_list_not_printed' :

					$meta  = '_wc_pip_packing_list_print_count';
					$compare = 'NOT EXISTS';

					break;

				case 'packing_list_printed' :

					$meta    = '_wc_pip_packing_list_print_count';
					$compare = '>';
					$value   = '0';

					break;

				case 'pick_list_not_printed' :

					$meta    = '_wc_pip_pick_list_print_count';
					$compare = 'NOT EXISTS';

					break;

				case 'pick_list_printed' :

					$meta    = '_wc_pip_pick_list_print_count';
					$compare = '>';
					$value   = '0';

					break;

			}

			if ( $meta && $compare ) {

				if ( ! Framework\SV_WC_Plugin_Compatibility::is_hpos_enabled() ) {
					$meta_query['meta_key']     = $meta;
					$meta_query['meta_value']   = $value;
					$meta_query['meta_compare'] = $compare;
				} else {
					$meta_query['key']     = $meta;
					$meta_query['value']   = $value;
					$meta_query['compare'] = $compare;
				}
			}
		}

		return $meta_query;
	}


	/**
	 * Make invoice numbers searchable
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @param string[]|mixed $search_fields Existing search fields
	 * @return string[]
	 */
	public function make_invoice_numbers_searchable( $search_fields ) {

		return array_merge( (array) $search_fields, [ '_pip_invoice_number' ] );
	}


}
