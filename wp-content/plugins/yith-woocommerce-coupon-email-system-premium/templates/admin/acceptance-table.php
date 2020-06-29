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


if ( ! class_exists( 'YWCES_Acceptance_Table' ) ) {

	/**
	 * Displays the acceptance table in YWCES plugin admin tab
	 *
	 * @class   YWCES_Acceptance_Table
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWCES_Acceptance_Table {

		/**
		 * Single instance of the class
		 *
		 * @var \YWCES_Acceptance_Table
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWCES_Acceptance_Table
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since   1.1.5
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
			add_action( 'current_screen', array( $this, 'add_options' ) );

		}

		/**
		 * Outputs the acceptance template with insert form in plugin options panel
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function output() {

			global $wpdb;

			$table = new YITH_Custom_Table( array(
				                                'singular' => esc_html__( 'customer', 'yith-woocommerce-coupon-email-system' ),
				                                'plural'   => esc_html__( 'customers', 'yith-woocommerce-coupon-email-system' )
			                                ) );

			$table->options = array(
				'select_table'     => $wpdb->prefix . 'users a INNER JOIN ' . $wpdb->base_prefix . 'usermeta b ON a.ID = b.user_id',
				'select_columns'   => array(
					'a.ID',
				),
				'select_where'     => 'b.meta_key = "ywces_receive_coupons" AND b.meta_value = "yes"',
				'select_group'     => 'a.ID',
				'select_order'     => 'a.ID',
				'select_order_dir' => 'ASC',
				'search_where'     => array(
					'a.user_email',
					'a.display_name',
				),
				'per_page_option'  => 'user_per_page',
				'count_table'      => $wpdb->prefix . 'users a INNER JOIN ' . $wpdb->base_prefix . 'usermeta b ON a.ID = b.user_id',
				'count_where'      => 'b.meta_key = "ywces_receive_coupons" AND b.meta_value = "yes"',
				'key_column'       => 'ID',
				'view_columns'     => array(
					'cb'    => '<input type="checkbox" />',
					'name'  => esc_html__( 'Customer', 'yith-woocommerce-coupon-email-system' ),
					'email' => esc_html__( 'E-mail', 'yith-woocommerce-coupon-email-system' ),
				),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'name' => array( 'name', true ),
				),
				'custom_columns'   => array(
					'column_name'  => function ( $item, $me ) {

						$user          = get_user_by( 'id', $item['ID'] );
						$customer_name = $user->get( 'billing_first_name' ) . ' ' . $user->get( 'billing_last_name' );
						$customer_name = ( $customer_name == ' ' ? $user->get( 'nickname' ) : $customer_name );

						$query_args = array(
							'page'   => $_GET['page'],
							'tab'    => $_GET['tab'],
							'action' => 'delete',
							'id'     => $item['ID']
						);
						$delete_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );

						$actions = array(
							'delete' => '<a href="' . $delete_url . '">' . esc_html__( 'Delete', 'yith-woocommerce-coupon-email-system' ) . '</a>',
						);

						return sprintf( '%s %s', '<strong>' . $customer_name . '</strong>', $me->row_actions( $actions ) );
					},
					'column_email' => function ( $item, $me ) {

						$user           = get_user_by( 'id', $item['ID'] );
						$customer_email = $user->get( 'billing_email' );
						$customer_email = ( $customer_email == '' ? $user->get( 'user_email' ) : $customer_email );

						return '<strong>' . $customer_email . '</strong>';
					}
				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => esc_html__( 'Delete', 'yith-woocommerce-coupon-email-system' ),
					),
					'functions' => array(
						'function_delete' => function () {
							$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
							$ids = is_array( $ids ) ? $ids : array( $ids );

							if ( ! empty( $ids ) ) {

								foreach ( $ids as $id ) {

									delete_user_meta( $id, 'ywces_receive_coupons' );

								}

							}
						},
					)
				),
			);

			$message = $notice = '';

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], basename( __FILE__ ) ) ) {

				if ( empty( $_POST['_customer_user'] ) ) {

					$notice = esc_html__( 'Select a customer', 'yith-woocommerce-coupon-email-system' );

				} else {

					update_user_meta( $_POST['_customer_user'], 'ywces_receive_coupons', 'yes' );

					$message = esc_html__( '1 customer added successfully', 'yith-woocommerce-coupon-email-system' );

				}

			}

			$table->prepare_items();

			$query_args    = array(
				'page' => $_GET['page'],
				'tab'  => $_GET['tab']
			);
			$blocklist_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );

			if ( 'delete' === $table->current_action() ) {
				$ids     = ( ! is_array( $_GET['id'] ) ) ? explode( ',', $_GET['id'] ) : $_GET['id'];
				$message = sprintf( esc_html__( 'Items deleted: %d', 'yith-woocommerce-coupon-email-system' ), count( $ids ) );
			}

			?>
            <div class="wrap">
                <h1>
					<?php esc_html_e( 'Coupons Acceptance', 'yith-woocommerce-coupon-email-system' ); ?>
                </h1>
				<?php

				if ( ! empty( $notice ) ) : ?>
                    <div id="notice" class="error below-h2"><p><?php echo $notice; ?></p></div>
				<?php endif;

				if ( ! empty( $message ) ) : ?>
                    <div id="message" class="updated below-h2"><p><?php echo $message; ?></p></div>
				<?php endif; ?>
                <form id="form" method="POST" action="<?php echo $blocklist_url; ?>">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
                    <table class="form-table" style="width: auto">
                        <tbody>
                        <tr valign="top" class="titledesc">
                            <th scope="row">
                                <label for="email"><?php esc_html_e( 'Add Customer', 'yith-woocommerce-coupon-email-system' ); ?></label>
                            </th>
                            <td class="forminp forminp-email">
								<?php yit_add_select2_fields(
									array(
										'type'              => 'hidden',
										'class'             => 'wc-customer-search',
										'id'                => 'customer_user',
										'name'              => '_customer_user',
										'data-placeholder'  => esc_html__( 'Search Customer', 'yith-woocommerce-coupon-email-system' ),
										'data-allow_clear'  => false,
										'data-selected'     => '',
										'data-multiple'     => false,
										'data-action'       => '',
										'value'             => '',
										'style'             => 'width:200px',
										'custom-attributes' => array()
									)
								);
								?>
                            </td>
                            <td>
                                <input type="submit" value="<?php esc_html_e( 'Add E-mail', 'yith-woocommerce-coupon-email-system' ) ?>" id="submit" class="button-primary" name="submit">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
                <form id="custom-table" method="GET" action="<?php echo $blocklist_url; ?>">
					<?php $table->search_box( esc_html__( 'Search Email' ), 'email' ); ?>

                    <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
                    <input type="hidden" name="tab" value="<?php echo $_GET['tab'] ?>" />

					<?php $table->display(); ?>
                </form>
            </div>
			<?php
		}

		/**
		 * Add screen options for acceptance table template
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_options() {
			if ( 'yith-plugins_page_yith-wc-coupon-email-system' == get_current_screen()->id && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'acceptance' ) ) {

				$option = 'per_page';

				$args = array(
					'label'   => esc_html__( 'Customers', 'yith-woocommerce-coupon-email-system' ),
					'default' => 10,
					'option'  => 'user_per_page'
				);

				add_screen_option( $option, $args );

			}
		}

	}

	/**
	 * Unique access to instance of YWCES_Acceptance_Table class
	 *
	 * @return \YWCES_Acceptance_Table
	 */
	function YWCES_Acceptance_Table() {

		return YWCES_Acceptance_Table::get_instance();

	}

	new YWCES_Acceptance_Table();

}
