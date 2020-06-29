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

if ( ! class_exists( 'YLC_Offline_Messages' ) ) {

	/**
	 * Displays the offline messages table in YITH Live Chat tab
	 *
	 * @class   YLC_Offline_Messages
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YLC_Offline_Messages {

		/**
		 * Single instance of the class
		 *
		 * @var \YLC_Offline_Messages
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YLC_Offline_Messages
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
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
			add_action( 'current_screen', array( $this, 'add_options' ) );

		}

		/**
		 * Outputs the offline messages table template
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @return  void
		 */
		public function output() {

			global $wpdb;

			$table        = new YITH_Custom_Table( array(
				                                       'singular' => esc_html__( 'message', 'yith-live-chat' ),
				                                       'plural'   => esc_html__( 'messages', 'yith-live-chat' )
			                                       ) );
			$view_columns = array(
				'cb'           => '<input type="checkbox" />',
				'mail_read'    => esc_html__( 'Read?', 'yith-live-chat' ),
				'mail_date'    => esc_html__( 'Date', 'yith-live-chat' ),
				'user_name'    => esc_html__( 'User', 'yith-live-chat' ),
				'user_email'   => esc_html__( 'E-mail', 'yith-live-chat' ),
				'user_message' => esc_html__( 'Message', 'yith-live-chat' ),
			);

			$bulk_actions = array(
				'actions'   => array(
					'delete' => esc_html__( 'Delete', 'yith-live-chat' ),
					'read'   => esc_html__( 'Mark as "read"', 'yith-live-chat' ),
					'unread' => esc_html__( 'Mark as "unread"', 'yith-live-chat' ),
				),
				'functions' => array(
					'function_delete' => function () {
						global $wpdb;

						$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
						if ( is_array( $ids ) ) {
							$ids = implode( ',', $ids );
						}

						if ( ! empty( $ids ) ) {
							$wpdb->query( "DELETE FROM {$wpdb->prefix}ylc_offline_messages WHERE id IN ( $ids )" );
						}
					},
					'function_unread' => function () {
						global $wpdb;

						$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
						if ( is_array( $ids ) ) {
							$ids = implode( ',', $ids );
						}

						if ( ! empty( $ids ) ) {
							$wpdb->query( "UPDATE {$wpdb->prefix}ylc_offline_messages SET mail_read = 0 WHERE id IN ( $ids )" );
						}
					},
					'function_read'   => function () {
						global $wpdb;

						$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();
						if ( is_array( $ids ) ) {
							$ids = implode( ',', $ids );
						}

						if ( ! empty( $ids ) ) {
							$wpdb->query( "UPDATE {$wpdb->prefix}ylc_offline_messages SET mail_read = 1 WHERE id IN ( $ids )" );
						}
					}
				)
			);

			if ( apply_filters( 'yith_wcfm_offline_messages_hide', false ) ) {
				unset( $view_columns['cb'] );
				/*$bulk_actions = array(
					'actions'   => array(),
					'functions' => array()
				);*/
			}

			$table->options = array(
				'select_table'     => $wpdb->prefix . 'ylc_offline_messages',
				'select_columns'   => array(
					'id',
					'user_name',
					'user_email',
					'user_message',
					'user_info',
					'mail_date',
					'mail_read',
				),
				'select_where'     => '',
				'select_group'     => '',
				'select_order'     => 'mail_date',
				'select_order_dir' => 'DESC',
				'search_where'     => array(
					'user_name',
					'user_email',
					'user_message'
				),
				'per_page_option'  => 'msg_per_page',
				'count_table'      => $wpdb->prefix . 'ylc_offline_messages',
				'count_where'      => '',
				'key_column'       => 'id',
				'view_columns'     => $view_columns,
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'mail_read'  => array( 'mail_read', false ),
					'mail_date'  => array( 'mail_date', false ),
					'user_name'  => array( 'user_name', false ),
					'user_email' => array( 'user_email', false ),
				),
				'custom_columns'   => array(
					'column_mail_read'    => function ( $item ) {

						if ( $item['mail_read'] != true ) {

							return sprintf( '<span class="ylc-icons ylc-icons-unread ylc-tips" data-tip="%s"></span>', esc_html__( 'Unread', 'yith-live-chat' ) );

						} else {

							return sprintf( '<span class="ylc-icons ylc-icons-read ylc-tips" data-tip="%s"></span>', esc_html__( 'Read', 'yith-live-chat' ) );

						}

					},
					'column_mail_date'    => function ( $item ) {

						if ( $item['mail_read'] != true ) {

							return '<b>' . $item['mail_date'] . '</b>';

						} else {

							return $item['mail_date'];

						}

					},
					'column_user_name'    => function ( $item, $me ) {

						if ( $item['mail_read'] != true ) {

							$user_name = '<b>' . $item['user_name'] . '</b>';

						} else {

							$user_name = $item['user_name'];

						}

						$view_query_args = array(
							'action' => 'view',
							'id'     => $item['id']
						);

						if ( isset( $_GET['page'] ) ) {

							$view_query_args['page'] = $_GET['page'];

						}

						$view_url = esc_url( add_query_arg( $view_query_args, apply_filters( 'yith_wcfm_offline_messages_url', admin_url( 'admin.php' ), 'offline-messages' ) ) );

						$read_query_args = array(
							'action' => 'read',
							'id'     => $item['id']
						);

						if ( isset( $_GET['page'] ) ) {

							$read_query_args['page'] = $_GET['page'];

						}

						$read_url = esc_url( add_query_arg( $read_query_args, apply_filters( 'yith_wcfm_offline_messages_url', admin_url( 'admin.php' ), 'offline-messages' ) ) );

						$unread_query_args = array(
							'action' => 'unread',
							'id'     => $item['id']
						);

						if ( isset( $_GET['page'] ) ) {

							$unread_query_args['page'] = $_GET['page'];

						}

						$unread_url = esc_url( add_query_arg( $unread_query_args, apply_filters( 'yith_wcfm_offline_messages_url', admin_url( 'admin.php' ), 'offline-messages' ) ) );

						$delete_query_args = array(
							'action' => 'delete',
							'id'     => $item['id']
						);

						if ( isset( $_GET['page'] ) ) {

							$delete_query_args['page'] = $_GET['page'];

						}

						$delete_url = esc_url( add_query_arg( $delete_query_args, apply_filters( 'yith_wcfm_offline_messages_url', admin_url( 'admin.php' ), 'offline-messages' ) ) );

						$actions = array(
							'view'   => '<a href="' . $view_url . '">' . esc_html__( 'View message', 'yith-live-chat' ) . '</a>',
							'read'   => '<a href="' . $read_url . '">' . esc_html__( 'Mark as "read"', 'yith-live-chat' ) . '</a>',
							'unread' => '<a href="' . $unread_url . '">' . esc_html__( 'Mark as "unread"', 'yith-live-chat' ) . '</a>',
							'delete' => '<a href="' . $delete_url . '">' . esc_html__( 'Delete', 'yith-live-chat' ) . '</a>',
						);

						return '<a href="' . $view_url . '">' . $user_name . '</a>' . $me->row_actions( $actions );

					},
					'column_user_email'   => function ( $item ) {

						if ( $item['mail_read'] != true ) {

							return '<b>' . $item['user_email'] . '</b>';

						} else {

							return $item['user_email'];

						}
					},
					'column_user_message' => function ( $item ) {

						$message = ( strlen( $item['user_message'] ) > 100 ) ? substr( $item['user_message'], 0, 97 ) . '...' : $item['user_message'];

						if ( $item['mail_read'] != true ) {

							return '<b>' . $message . '</b>';

						} else {

							return $message;

						}
					},
				),
				'bulk_actions'     => $bulk_actions,
			);

			if ( defined( 'YITH_WPV_PREMIUM' ) ) {

				$vendor = yith_get_vendor( 'current', 'user' );

				$table->options['select_columns'][] = 'vendor_id';

				if ( $vendor->id == 0 ) {

					//If current user is a global admin show the column with vendors name
					$table->options['view_columns']['vendor']          = esc_html__( 'Vendor', 'yith-live-chat' );
					$table->options['custom_columns']['column_vendor'] = function ( $item ) {

						if ( $item['vendor_id'] != 0 ) {

							$vendor = yith_get_vendor( $item['vendor_id'], 'vendor' );

							return '<b>' . $vendor->term->name . '</b>';

						} else {

							return '-';

						}

					};

				} else {

					//If current user is a vendor admin show only the emails to that vendor
					$table->options['select_where'] = 'vendor_id = ' . $vendor->id;
					$table->options['count_where']  = 'vendor_id = ' . $vendor->id;

				}

			}

			$table->prepare_items();

			$message         = '';
			$notice          = '';
			$list_query_args = array();

			if ( isset( $_GET['page'] ) ) {

				$list_query_args = array(
					'page' => $_GET['page'],
				);

			}

			$list_url = esc_url( add_query_arg( $list_query_args, apply_filters( 'yith_wcfm_offline_messages_url', admin_url( 'admin.php' ), 'offline-messages' ) ) );

			if ( 'delete' === $table->current_action() ) {

				$ids     = isset( $_GET['id'] ) ? ( ( ! is_array( $_GET['id'] ) ) ? explode( ',', $_GET['id'] ) : $_GET['id'] ) : '';
				$items   = $ids != '' ? count( $ids ) : 0;
				$message = sprintf( _n( '%s message deleted successfully', '%s messages deleted successfully', $items, 'yith-live-chat' ), $items );

			} elseif ( 'read' === $table->current_action() || 'unread' === $table->current_action() ) {

				$ids     = isset( $_GET['id'] ) ? ( ( ! is_array( $_GET['id'] ) ) ? explode( ',', $_GET['id'] ) : $_GET['id'] ) : '';
				$items   = $ids != '' ? count( $ids ) : 0;
				$message = sprintf( _n( '%s message updated successfully', '%s messages updated successfully', $items, 'yith-live-chat' ), $items );

			}

			?>
            <div class="wrap">
                <h1>
					<?php esc_html_e( 'Offline messages', 'yith-live-chat' ); ?>
                </h1>
				<?php

				if ( ! empty( $notice ) ) : ?>
                    <div id="notice" class="error below-h2"><p><?php echo $notice; ?></p></div>
				<?php endif;

				if ( ! empty( $message ) ) : ?>
                    <div id="message" class="updated below-h2"><p><?php echo $message; ?></p></div>
				<?php endif;

				if ( isset( $_GET['id'] ) && ! empty( $_GET['action'] ) && 'view' == $_GET['action'] ) : ?>
					<?php

					$wpdb->update(
						$wpdb->prefix . 'ylc_offline_messages',
						array(
							'mail_read' => 1,
						),
						array( 'id' => $_GET['id'] ),
						array(
							'%d'
						),
						array( '%d' )
					);

					$select_table   = $table->options['select_table'];
					$select_columns = implode( ',', $table->options['select_columns'] );
					$item           = $wpdb->get_row( $wpdb->prepare( "SELECT $select_columns FROM $select_table WHERE id = %d", $_GET['id'] ), ARRAY_A );
					$user_info      = maybe_unserialize( $item['user_info'] );

					?>
                    <div class="message_box">

                        <div class="mail_head">
                            <b><?php esc_html_e( 'Message Sender' ) ?>:</b>
                            <a href="mailto:<?php echo esc_attr( $item['user_email'] ); ?>">
								<?php echo esc_attr( $item['user_name'] ) ?> (<?php echo esc_attr( $item['user_email'] ); ?>)
                            </a>

                        </div>
                        <div class="mail_body">
							<?php echo esc_attr( $item['user_message'] ); ?>
                        </div>
                        <div class="user_info">
                            <h3><?php esc_html_e( 'User Info', 'yith-live-chat' ) ?></h3>

                            <div class="info">
                                <span><b><?php esc_html_e( 'IP Address', 'yith-live-chat' ) ?>:</b> <?php echo $user_info['ip'] ?></span>
                                <span><b><?php esc_html_e( 'OS', 'yith-live-chat' ) ?>:</b> <?php echo $user_info['os'] ?></span>
                                <span><b><?php esc_html_e( 'Browser', 'yith-live-chat' ) ?>:</b> <?php echo $user_info['browser'] . ' ' . $user_info['version'] ?></span>
                                <span><b><?php esc_html_e( 'Page', 'yith-live-chat' ) ?>:</b> <?php echo $user_info['page'] ?></span>

								<?php if ( ylc_get_option( 'offline-gdpr-compliance', ylc_get_default( 'offline-gdpr-compliance' ) ) == 'yes' ): ?>
                                    <span><b><?php esc_html_e( 'GDPR Acceptance', 'yith-live-chat' ) ?>:</b> <?php echo( ( isset( $user_info['gdpr_acceptance'] ) ? esc_html__( 'Yes', 'yith-live-chat' ) : esc_html__( 'No', 'yith-live-chat' ) ) ) ?></span>
								<?php endif; ?>

                            </div>
                            <div class="btn">
                                <a class="button button-secondary" href="<?php echo $list_url; ?>"><?php esc_html_e( 'Return to message list', 'yith-live-chat' ); ?></a>
                            </div>
                        </div>
                    </div>
                    <br class="clear">
				<?php else : ?>
                    <form id="custom-table" method="GET" class="ylc-offline-table" action="<?php echo $list_url; ?>">
						<?php
						if ( ! apply_filters( 'yith_wcfm_offline_messages_hide', false ) ) {
							$table->search_box( esc_html__( 'Search Message' ), 'message' );
						}
						?>
                        <input type="hidden" name="page" value="<?php echo isset( $_GET['page'] ) ? $_GET['page'] : ''; ?>" />
						<?php $table->display(); ?>
                    </form>
				<?php endif; ?>
            </div>
			<?php
		}

		/**
		 * Add screen options for list table template
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_options() {

			if ( 'yith-live-chat_page_ylc_offline_messages' == get_current_screen()->id && ( ! isset( $_GET['action'] ) || ( $_GET['action'] != 'view' ) ) ) {

				$option = 'per_page';
				$args   = array(
					'label'   => esc_html__( 'Messages', 'yith-live-chat' ),
					'default' => 10,
					'option'  => 'msg_per_page'
				);
				add_screen_option( $option, $args );

			}

		}

		/**
		 * Set screen options for list table template
		 *
		 * @since   1.0.0
		 *
		 * @param   $status string
		 * @param   $option string
		 * @param   $value  string
		 *
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function set_options( $status, $option, $value ) {

			return ( 'msg_per_page' == $option ) ? $value : $status;

		}

	}

	/**
	 * Unique access to instance of YLC_Offline_Messages class
	 *
	 * @return \YLC_Offline_Messages
	 */
	function YLC_Offline_Messages() {

		return YLC_Offline_Messages::get_instance();

	}

}