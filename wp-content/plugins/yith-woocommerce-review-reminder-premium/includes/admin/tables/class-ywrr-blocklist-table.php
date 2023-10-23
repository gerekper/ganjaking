<?php
/**
 * Blocklist table class
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YWRR_Blocklist_Table' ) ) {

	/**
	 * Displays the blocklist table in YWRR plugin admin tab
	 *
	 * @class   YWRR_Blocklist_Table
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 *
	 * @package YITH
	 */
	class YWRR_Blocklist_Table {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.1.5
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ), 20 );
		}

		/**
		 * Blocklist initialization
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function init() {
			add_action( 'ywrr_blocklist', array( $this, 'output' ) );
			add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
			add_action( 'current_screen', array( $this, 'add_options' ) );
		}

		/**
		 * Outputs the blocklist template with insert form in plugin options panel
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function output() {

			global $wpdb;

			$table = new YITH_Custom_Table(
				array(
					'singular' => esc_html__( 'customer', 'yith-woocommerce-review-reminder' ),
					'plural'   => esc_html__( 'customers', 'yith-woocommerce-review-reminder' ),
				)
			);

			$table->options = array(
				'select_table'     => $wpdb->prefix . 'ywrr_email_blocklist a LEFT JOIN ' . $wpdb->base_prefix . 'usermeta b ON a.customer_id = b.user_id',
				'select_columns'   => array(
					'a.id',
					'a.customer_id',
					'a.customer_email',
					'MAX(CASE WHEN b.meta_key = "first_name" THEN b.meta_value ELSE NULL END) AS first_name',
					'MAX(CASE WHEN b.meta_key = "last_name" THEN b.meta_value ELSE NULL END) AS last_name',
					'MAX(CASE WHEN b.meta_key = "nickname" THEN b.meta_value ELSE NULL END) AS nickname',
				),
				'select_where'     => '',
				'select_group'     => 'a.customer_email',
				'select_order'     => 'a.customer_id',
				'select_order_dir' => 'ASC',
				'search_where'     => array(
					'a.customer_email',
				),
				'per_page_option'  => 'user_per_page',
				'count_table'      => $wpdb->prefix . 'ywrr_email_blocklist a',
				'count_where'      => '',
				'key_column'       => 'id',
				'view_columns'     => array(
					'cb'             => '<input type="checkbox" />',
					'name'           => esc_html__( 'Customer', 'yith-woocommerce-review-reminder' ),
					'customer_email' => esc_html__( 'Email', 'yith-woocommerce-review-reminder' ),
				),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'name'           => array( 'name', true ),
					'customer_email' => array( 'customer_email', false ),
				),
				'custom_columns'   => array(
					'column_name' => function ( $item, $me ) {
						switch ( $item['customer_id'] ) {
							case 0:
								$customer_name = esc_html__( 'Unregistered User', 'yith-woocommerce-review-reminder' );
								break;
							default:
								$edit_url = esc_url(
									add_query_arg(
										array(
											'user_id' => $item['customer_id'],
										),
										admin_url( 'user-edit.php' )
									)
								);

								$customer_name = '<a target="_blank" href="' . $edit_url . '">' . ( ( ' ' === $item['first_name'] . ' ' . $item['last_name'] ) ? $item['nickname'] : $item['first_name'] . ' ' . $item['last_name'] ) . '</a>';
						}
						$getted = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

						$delete_url = esc_url(
							add_query_arg(
								array(
									'page'   => $getted['page'],
									'tab'    => $getted['tab'],
									'action' => 'delete',
									'id'     => $item['id'],
								),
								admin_url( 'admin.php' )
							)
						);
						$actions    = array(
							'delete' => '<a href="' . $delete_url . '">' . esc_html__( 'Delete', 'yith-woocommerce-review-reminder' ) . '</a>',
						);

						return sprintf( '%s %s', '<strong>' . $customer_name . '</strong>', $me->row_actions( $actions ) );
					},
				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => esc_html__( 'Delete', 'yith-woocommerce-review-reminder' ),
					),
					'functions' => array(
						'function_delete' => function () {
							global $wpdb;
							$getted = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$ids    = isset( $getted['id'] ) ? $getted['id'] : array();
							if ( is_array( $ids ) ) {
								$ids = implode( ',', $ids );
							}

							if ( ! empty( $ids ) ) {
								$wpdb->query( "DELETE FROM {$wpdb->prefix}ywrr_email_blocklist WHERE id IN ( $ids )" ); //phpcs:ignore
							}
						},
					),
				),
				'wp_cache_option'  => 'ywrr_email_blocklist',
			);

			$table->prepare_items();
			$getted        = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$message       = '';
			$query_args    = array(
				'page' => $getted['page'],
				'tab'  => $getted['tab'],
			);
			$blocklist_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );

			if ( 'delete' === $table->current_action() ) {
				$deleted = is_array( $getted['id'] ) ? $getted['id'] : explode( ',', $getted['id'] );
				/* translators: %s item number */
				$message = sprintf( esc_html__( 'Items deleted: %d', 'yith-woocommerce-review-reminder' ), count( $deleted ) );
			}

			?>
			<div class="yith-plugin-fw yit-admin-panel-container">
				<h2>
					<?php esc_html_e( 'Blocklist', 'yith-woocommerce-review-reminder' ); ?>
				</h2>
				<?php if ( $message ) : ?>
					<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
				<?php endif; ?>
				<div class="yith-plugin-fw-panel-custom-tab-container">
					<table class="form-table" style="width: auto">
						<tbody>
						<tr valign="top" class="titledesc">
							<th scope="row">
								<label for="email"><?php esc_html_e( 'Add email to the blocklist', 'yith-woocommerce-review-reminder' ); ?></label>
							</th>
							<td class="forminp">
								<?php

								$args = array(
									'id'      => 'add_to_blocklist',
									'name'    => 'add_to_blacklist',
									'type'    => 'text-button',
									'buttons' => array(
										array(
											'name'  => esc_html__( 'Add Email', 'yith-woocommerce-review-reminder' ),
											'class' => 'ywrr-add-blocklist',
										),
									),
								);

								yith_plugin_fw_get_field( $args, true );

								?>
							</td>
						</tr>
						</tbody>
					</table>
					<form id="custom-table" method="GET" action="<?php echo esc_url( $blocklist_url ); ?>">
						<?php $table->search_box( esc_html__( 'Search Email' ), 'email' ); ?>
						<input type="hidden" name="page" value="<?php echo esc_attr( $getted['page'] ); ?>" />
						<input type="hidden" name="tab" value="<?php echo esc_attr( $getted['tab'] ); ?>" />
						<?php $table->display(); ?>
					</form>
				</div>
			</div>
			<?php
		}

		/**
		 * Add screen options for blocklist table template
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function add_options() {
			$getted = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'yith-plugins_page_yith_ywrr_panel' === get_current_screen()->id && ( isset( $getted['tab'] ) && 'blocklist' === $getted['tab'] ) && ( ! isset( $getted['action'] ) || 'addnew' !== $getted['action'] ) ) {

				$option = 'per_page';

				$args = array(
					'label'   => esc_html__( 'Customers', 'yith-woocommerce-review-reminder' ),
					'default' => 10,
					'option'  => 'user_per_page',
				);

				add_screen_option( $option, $args );

			}
		}

		/**
		 * Set screen options for blocklist table template
		 *
		 * @param string $status Screen status.
		 * @param string $option Option name.
		 * @param string $value  Option value.
		 *
		 * @return  string
		 * @since   1.0.0
		 */
		public function set_options( $status, $option, $value ) {
			return ( 'user_per_page' === $option ) ? $value : $status;
		}

	}

	new YWRR_Blocklist_Table();

}
