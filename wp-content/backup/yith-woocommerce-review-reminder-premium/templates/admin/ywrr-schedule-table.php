<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YWRR_Schedule_Table' ) ) {

	/**
	 * Displays the schedule table in YWRR plugin admin tab
	 *
	 * @class   YWRR_Schedule_Table
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWRR_Schedule_Table {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ), 20 );
		}

		/**
		 * Schedule list initialization
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init() {
			add_action( 'ywrr_schedulelist', array( $this, 'output' ) );
			add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
			add_action( 'current_screen', array( $this, 'add_options' ) );
		}

		/**
		 * Outputs the schedule list template
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function output() {

			global $wpdb;

			$table = new YITH_Custom_Table( array(
				                                'singular' => esc_html__( 'reminder', 'yith-woocommerce-review-reminder' ),
				                                'plural'   => esc_html__( 'reminders', 'yith-woocommerce-review-reminder' )
			                                ) );

			$table->options = array(
				'select_table'     => $wpdb->prefix . 'ywrr_email_schedule',
				'select_columns'   => array(
					'id',
					'order_id',
					'order_date',
					'scheduled_date',
					'request_items',
					'mail_status',
				),
				'select_where'     => ( isset( $_REQUEST['mail_status'] ) && $_REQUEST['mail_status'] != 'all' ? 'mail_status="' . $_REQUEST['mail_status'] . '"' : '' ),
				'select_group'     => '',
				'select_order'     => 'scheduled_date',
				'select_order_dir' => 'DESC',
				'search_where'     => array(
					'order_id'
				),
				'per_page_option'  => 'mails_per_page',
				'count_table'      => $wpdb->prefix . 'ywrr_email_schedule',
				'count_where'      => ( isset( $_REQUEST['mail_status'] ) && $_REQUEST['mail_status'] != 'all' ? 'mail_status="' . $_REQUEST['mail_status'] . '"' : '' ),
				'key_column'       => 'id',
				'view_columns'     => array(
					'cb'             => '<input type="checkbox" />',
					'order_id'       => esc_html__( 'Order', 'yith-woocommerce-review-reminder' ),
					'request_items'  => esc_html__( 'Items to review', 'yith-woocommerce-review-reminder' ),
					'order_date'     => esc_html__( 'Completed Date', 'yith-woocommerce-review-reminder' ),
					'scheduled_date' => esc_html__( 'E-mail Scheduled Date', 'yith-woocommerce-review-reminder' ),
					'mail_status'    => esc_html__( 'Status', 'yith-woocommerce-review-reminder' )
				),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'order_id'       => array( 'order_id', false ),
					'order_date'     => array( 'order_date', false ),
					'scheduled_date' => array( 'scheduled_date', false ),
				),
				'custom_columns'   => array(
					'column_mail_status'   => function ( $item ) {

						switch ( $item['mail_status'] ) {

							case 'sent':
								$class = 'sent';
								$tip   = esc_html__( 'Sent', 'yith-woocommerce-review-reminder' );
								break;
							case 'cancelled':
								$class = 'cancelled';
								$tip   = esc_html__( 'Cancelled', 'yith-woocommerce-review-reminder' );
								break;
							default;
								$class = 'on-hold';
								$tip   = esc_html__( 'On Hold', 'yith-woocommerce-review-reminder' );

						}

						return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $class, $tip, $tip );

					},
					'column_order_id'      => function ( $item, $me ) {

						$the_order = wc_get_order( $item['order_id'] );

						if ( ! $the_order ) {
							return '';
						}

						$customer_tip = '';
						$address      = $the_order->get_formatted_billing_address();
						$phone        = $the_order->get_billing_phone();
						$first_name   = $the_order->get_billing_first_name();
						$last_name    = $the_order->get_billing_last_name();

						if ( $address ) {
							$customer_tip .= esc_html__( 'Billing:', 'yith-woocommerce-review-reminder' ) . ' ' . $address . '<br/><br/>';
						}

						if ( $phone ) {
							$customer_tip .= esc_html__( 'Phone:', 'yith-woocommerce-review-reminder' ) . ' ' . $phone;
						}

						if ( $first_name || $last_name ) {
							$username = trim( $first_name . ' ' . $last_name );
						} else {
							$username = esc_html__( 'Guest', 'yith-woocommerce-review-reminder' );
						}

						$order_query_args = array(
							'post'   => absint( $item['order_id'] ),
							'action' => 'edit'
						);
						$order_url        = esc_url( add_query_arg( $order_query_args, admin_url( 'post.php' ) ) );
						$order_number     = '<a href="' . $order_url . '"><strong>#' . esc_attr( $the_order->get_order_number() ) . '</strong></a>';
						$billing_email    = $the_order->get_billing_email();
						$customer_email   = '<a href="' . esc_url( 'mailto:' . $billing_email ) . '">' . esc_html( $billing_email ) . '</a>';

						$query_args = array(
							'page'   => $_GET['page'],
							'tab'    => $_GET['tab'],
							'action' => 'delete',
							'id'     => $item['id']
						);
						$delete_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						$actions    = array(
							'delete' => '<a href="' . $delete_url . '">' . esc_html__( 'Cancel Schedule', 'yith-woocommerce-review-reminder' ) . '</a>',
						);

						return '<div class="tips" data-tip="' . wc_sanitize_tooltip( $customer_tip ) . '">' . sprintf( _x( '%s by %s', 'Order number by X', 'yith-woocommerce-review-reminder' ), $order_number, $username ) . ' - ' . $customer_email . '</div>' . $me->row_actions( $actions );

					},
					'column_request_items' => function ( $item ) {

						if ( $item['request_items'] == '' ) {

							return esc_html__( 'As general settings', 'yith-woocommerce-review-reminder' );

						} else {
							$review_items = maybe_unserialize( $item['request_items'] );
							ob_start();
							ywrr_compact_list( $review_items );

							return ob_get_clean();
						}

					}
				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => esc_html__( 'Cancel Schedule', 'yith-woocommerce-review-reminder' ),
					),
					'functions' => array(
						'function_delete' => function () {
							global $wpdb;

							$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
							if ( is_array( $ids ) ) {
								$ids = implode( ',', $ids );
							}

							if ( ! empty( $ids ) ) {
								$wpdb->query( "UPDATE {$wpdb->prefix}ywrr_email_schedule SET mail_status = 'cancelled' WHERE id IN ( $ids )" );

							}
						},
					)
				),
			);

			$table->prepare_items();

			$message              = '';
			$query_args           = array(
				'page' => $_GET['page'],
				'tab'  => $_GET['tab']
			);
			$schedulelist_url     = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
			$mail_status          = array(
				'all'       => esc_html__( 'All', 'yith-woocommerce-review-reminder' ),
				'sent'      => esc_html__( 'Sent', 'yith-woocommerce-review-reminder' ),
				'pending'   => esc_html__( 'On Hold', 'yith-woocommerce-review-reminder' ),
				'cancelled' => esc_html__( 'Cancelled', 'yith-woocommerce-review-reminder' )
			);
			$keys                 = array_keys( $mail_status );
			$last_key             = array_pop( $keys );
			$selected_mail_status = isset( $_GET['mail_status'] ) ? $_GET['mail_status'] : 'all';

			if ( 'delete' === $table->current_action() ) {
				$deleted = is_array( $_GET['id'] ) ? $_GET['id'] : explode( ',', $_GET['id'] );
				$message = sprintf( esc_html__( 'Email unscheduled: %d', 'yith-woocommerce-review-reminder' ), count( $deleted ) );
			}

			?>
            <div class="yith-plugin-fw yit-admin-panel-container">
                <h2>
					<?php esc_html_e( 'Scheduled Reminders', 'yith-woocommerce-review-reminder' ); ?>
                </h2>
				<?php if ( $message ) : ?>
                    <div class="notice notice-success is-dismissible"><p><?php echo $message ?></p></div>
				<?php endif; ?>
                <div class="yith-plugin-fw-panel-custom-tab-container">

                    <ul class="subsubsub">
						<?php foreach ( $mail_status as $key => $status ): ?>
                            <li>
                                <a href="<?php echo $schedulelist_url ?>&mail_status=<?php echo $key ?>" <?php echo( isset( $selected_mail_status ) && $selected_mail_status == $key ? 'class="current"' : '' ) ?> ><?php echo $status ?> <span class="count">(<?php echo $this->count_items( $key ); ?>)</span></a>
								<?php echo( $key != $last_key ? ' | ' : '' ) ?>
                            </li>
						<?php endforeach; ?>
                    </ul>
                    <form id="custom-table" method="GET" action="<?php echo $schedulelist_url; ?>">
						<?php $table->search_box( esc_html__( 'Search Order' ), 'email' ); ?>
                        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
                        <input type="hidden" name="tab" value="<?php echo $_GET['tab'] ?>" />
						<?php $table->display(); ?>
                    </form>
                </div>
            </div>
			<?php

		}

		/**
		 * Add screen options for schedule table template
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_options() {
			if ( 'yith-plugins_page_yith_ywrr_panel' == get_current_screen()->id && isset( $_GET['tab'] ) && $_GET['tab'] == 'schedule' ) {

				$option = 'per_page';
				$args   = array(
					'label'   => esc_html__( 'Reminders', 'yith-woocommerce-review-reminder' ),
					'default' => 10,
					'option'  => 'mails_per_page'
				);

				add_screen_option( $option, $args );

			}
		}

		/**
		 * Set screen options for schedule table template
		 *
		 * @param   $status string
		 * @param   $option string
		 * @param   $value  string
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_options( $status, $option, $value ) {

			return ( 'mails_per_page' == $option ) ? $value : $status;

		}

		/**
		 * Count items for each status
		 *
		 * @param   $status string
		 *
		 * @return  integer
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function count_items( $status ) {

			global $wpdb;

			if ( $status == 'all' ) {
				$query = "SELECT COUNT(*) FROM  {$wpdb->prefix}ywrr_email_schedule";
			} else {
				$query = "SELECT COUNT(*) FROM  {$wpdb->prefix}ywrr_email_schedule WHERE mail_status = '$status'";
			}

			return $wpdb->get_var( $query );

		}

	}

	new YWRR_Schedule_Table();

}
