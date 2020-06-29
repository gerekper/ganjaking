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

if ( ! class_exists( 'YLC_Chat_Logs' ) ) {

	/**
	 * Displays the chat logs table in YITH Live Chat tab
	 *
	 * @class   YLC_Chat_Logs
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YLC_Chat_Logs {

		/**
		 * Single instance of the class
		 *
		 * @var \YLC_Chat_Logs
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YLC_Chat_Logs
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
		 * Outputs the chat logs table template
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function output() {

			global $wpdb;

			$table = new YITH_Custom_Table(
				array(
					'singular' => esc_html__( 'log', 'yith-live-chat' ),
					'plural'   => esc_html__( 'logs', 'yith-live-chat' )
				)
			);

			$view_columns = array(
				'cb'           => '<input type="checkbox" />',
				'user_name'    => esc_html__( 'User name', 'yith-live-chat' ),
				'email'        => esc_html__( 'E-mail', 'yith-live-chat' ),
				'ip'           => esc_html__( 'IP Address', 'yith-live-chat' ),
				'created_at'   => esc_html__( 'Date', 'yith-live-chat' ),
				'total_msgs'   => esc_html__( 'Total Messages', 'yith-live-chat' ),
				'duration'     => esc_html__( 'Chat duration', 'yith-live-chat' ),
				'evaluation'   => esc_html__( 'Evaluation', 'yith-live-chat' ),
				'receive_copy' => esc_html__( 'Request Copy', 'yith-live-chat' )
			);

			$bulk_actions = array(
				'actions'   => array(
					'delete' => esc_html__( 'Delete', 'yith-live-chat' ),
				),
				'functions' => array(
					'function_delete' => function () {
						global $wpdb;

						$ids = isset( $_GET['id'] ) ? $_GET['id'] : array();

						if ( ! empty( $ids ) ) {

							if ( is_array( $ids ) ) {

								foreach ( $ids as $id ) {

									$wpdb->query(
										$wpdb->prepare(
											"DELETE FROM {$wpdb->prefix}ylc_chat_sessions WHERE conversation_id = %s LIMIT 1",
											$id
										)
									);

									$wpdb->query(
										$wpdb->prepare(
											"DELETE FROM {$wpdb->prefix}ylc_chat_rows WHERE conversation_id = %s",
											$id
										)
									);

								}

							} else {

								$wpdb->query(
									$wpdb->prepare(
										"DELETE FROM {$wpdb->prefix}ylc_chat_sessions WHERE conversation_id = %s LIMIT 1",
										$ids
									)
								);

								$wpdb->query(
									$wpdb->prepare(
										"DELETE FROM {$wpdb->prefix}ylc_chat_rows WHERE conversation_id = %s",
										$ids
									)
								);

							}
						}


					},
				)
			);

			if ( apply_filters( 'yith_wcfm_chat_log_hide', false ) ) {
				unset( $view_columns['cb'] );
				/*$bulk_actions = array(
					'actions'   => array(),
					'functions' => array()
				);*/
			}

			$table->options = array(
				'select_table'     => $wpdb->prefix . 'ylc_chat_sessions a LEFT JOIN ' . $wpdb->prefix . 'ylc_chat_visitors b ON a.user_id = b.user_id',
				'select_columns'   => array(
					'a.conversation_id',
					'a.user_id',
					'a.created_at',
					'a.evaluation',
					'a.receive_copy',
					'a.duration',
					'b.user_name',
					'b.user_type',
					'b.user_ip',
					'b.user_email'
				),
				'select_where'     => '',
				'select_group'     => 'a.conversation_id',
				'select_order'     => 'a.created_at',
				'select_order_dir' => 'DESC',
				'search_where'     => array(
					'b.user_name',
					'b.user_email'
				),
				'per_page_option'  => 'logs_per_page',
				'count_table'      => $wpdb->prefix . 'ylc_chat_sessions a LEFT JOIN ' . $wpdb->prefix . 'ylc_chat_visitors b ON a.user_id = b.user_id',
				'count_where'      => '',
				'key_column'       => 'conversation_id',
				'view_columns'     => $view_columns,
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'user_name'  => array( 'user_name', false ),
					'created_at' => array( 'created_at', false ),
				),
				'custom_columns'   => array(
					'column_created_at'   => function ( $item ) {

						return ylc_convert_timestamp( $item['created_at'] );

					},
					'column_total_msgs'   => function ( $item ) {

						global $wpdb;

						return $wpdb->get_var(
							$wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ylc_chat_rows WHERE conversation_id = %s", $item['conversation_id'] ) );

					},
					'column_user_name'    => function ( $item, $me ) {

						$view_query_args = array(
							'action' => 'view',
							'id'     => $item['conversation_id']
						);

						if ( isset( $_GET['page'] ) ) {

							$view_query_args['page'] = $_GET['page'];

						}

						$view_url = esc_url( add_query_arg( $view_query_args, apply_filters( 'yith_wcfm_chat_log_url', admin_url( 'admin.php' ), 'chat-logs' ) ) );

						$delete_query_args = array(
							'action' => 'delete',
							'id'     => $item['conversation_id']
						);

						if ( isset( $_GET['page'] ) ) {

							$delete_query_args['page'] = $_GET['page'];

						}

						$delete_url = esc_url( add_query_arg( $delete_query_args, apply_filters( 'yith_wcfm_chat_log_url', admin_url( 'admin.php' ), 'chat-logs' ) ) );

						$actions = array(
							'view'   => '<a href="' . $view_url . '">' . esc_html__( 'View conversation', 'yith-live-chat' ) . '</a>',
							'delete' => '<a href="' . $delete_url . '">' . esc_html__( 'Delete', 'yith-live-chat' ) . '</a>',
						);

						return '<b>' . $item['user_name'] . '</b> <span style="color:silver">' . $item['user_type'] . '</span>' . $me->row_actions( $actions );

					},
					'column_email'        => function ( $item ) {

						if ( ! empty( $item['user_email'] ) ) {
							return '<a href="mailto:' . $item['user_email'] . '">' . $item['user_email'] . '</a>';
						} else {
							return '<span style="color:silver">' . esc_html__( 'N/A', 'yith-live-chat' ) . '</span>';
						}
					},
					'column_ip'           => function ( $item ) {

						return long2ip( $item['user_ip'] );

					},
					'column_evaluation'   => function ( $item ) {

						switch ( $item['evaluation'] ) {
							case 'good':
								return sprintf( '<span class="ylc-icons ylc-icons-good ylc-tips" data-tip="%s"></span>', esc_html__( 'Good', 'yith-live-chat' ) );
								break;

							case 'bad':
								return sprintf( '<span class="ylc-icons ylc-icons-bad ylc-tips" data-tip="%s"></span>', esc_html__( 'Bad', 'yith-live-chat' ) );
								break;

							default:
								return '';
						}

					},
					'column_duration'     => function ( $item ) {

						if ( $item['duration'] == '00:00:00' ) {
							return $item['duration'] . '<br />' . esc_html__( 'Not Started', 'yith-live-chat' );
						} else {
							return $item['duration'];
						}

					},
					'column_receive_copy' => function ( $item ) {

						if ( $item['receive_copy'] == true ) {
							return sprintf( '<span class="ylc-icons ylc-icons-yes ylc-tips" data-tip="%s"></span>', esc_html__( 'Sent', 'yith-live-chat' ) );
						} else {
							return sprintf( '<span class="ylc-icons ylc-icons-no ylc-tips" data-tip="%s"></span>', esc_html__( 'Not sent', 'yith-live-chat' ) );
						}

					},
				),
				'bulk_actions'     => $bulk_actions,
			);

			if ( defined( 'YITH_WPV_PREMIUM' ) ) {

				$vendor = yith_get_vendor( 'current', 'user' );

				$table->options['select_columns'][] = 'b.vendor_id';

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
					$table->options['select_where'] = 'b.vendor_id = ' . $vendor->id;
					$table->options['count_where']  = 'b.vendor_id = ' . $vendor->id;

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

			$list_url = esc_url( add_query_arg( $list_query_args, apply_filters( 'yith_wcfm_chat_log_url', admin_url( 'admin.php' ), 'chat-logs' ) ) );

			if ( 'delete' === $table->current_action() ) {

				$ids     = isset( $_GET['id'] ) ? ( ( ! is_array( $_GET['id'] ) ) ? explode( ',', $_GET['id'] ) : $_GET['id'] ) : '';
				$items   = $ids != '' ? count( $ids ) : 0;
				$message = sprintf( _n( '%s conversation deleted successfully', '%s conversations deleted successfully', $items, 'yith-live-chat' ), $items );

			} ?>

            <div class="wrap">
				<?php if ( isset( $_GET['id'] ) && ! empty( $_GET['action'] ) && 'view' == $_GET['action'] ) : ?>
					<?php

					$item      = ylc_get_chat_info( $_GET['id'] );
					$chat_logs = ylc_get_chat_conversation( $_GET['id'] );

					?>

                    <h1><?php echo $item['user_name']; ?></h1>
                    <div class="chat_info">
                                <span>
                                    <b><?php esc_html_e( 'User type', 'yith-live-chat' ) ?>:</b>
	                                <?php echo ucfirst( $item['user_type'] ); ?>
                                </span>
                        <span>
                                    <b><?php esc_html_e( 'IP Address', 'yith-live-chat' ) ?>:</b>
							<?php echo long2ip( $item['user_ip'] ); ?>
                                </span>
                        <span>
                                    <b><?php esc_html_e( 'User e-mail', 'yith-live-chat' ) ?>:</b>
							<?php echo $item['user_email']; ?>
                                </span>
                        <span>
                                    <b><?php esc_html_e( 'Chat Evaluation', 'yith-live-chat' ) ?>:</b>
							<?php

							switch ( $item['evaluation'] ) {
								case 'good':
									echo sprintf( '<span class="ylc-icons ylc-icons-good ylc-tips" data-tip="%s"></span>', esc_html__( 'Good', 'yith-live-chat' ) );
									break;

								case 'bad':
									echo sprintf( '<span class="ylc-icons ylc-icons-bad ylc-tips" data-tip="%s"></span>', esc_html__( 'Bad', 'yith-live-chat' ) );
									break;

								default:
									echo '--';
							}

							?>
                                </span>
                        <a class="button button-secondary" href="<?php echo $list_url; ?>"><?php esc_html_e( 'Return to chat logs', 'yith-live-chat' ); ?></a>
                    </div>
                    <hr>
                    <div class="chat_log">
						<?php foreach ( $chat_logs as $log ): ?>
                            <p class="chat_row">
                                <span class="date"><?php echo ylc_convert_timestamp( $log['msg_time'] ); ?></span>
                                <span class="message <?php echo $log['user_type']; ?>">
                                            <b><?php echo $log['user_name']; ?>: </b>
									<?php echo stripslashes( $log['msg'] ); ?>
                                        </span>
                            </p>
						<?php endforeach; ?>
                    </div>
				<?php else : ?>
                    <h1>
						<?php esc_html_e( 'Chat Logs', 'yith-live-chat' ); ?>
                    </h1>
					<?php if ( ! empty( $notice ) ) : ?>
                        <div id="notice" class="error below-h2"><p><?php echo $notice; ?></p></div>
					<?php endif;

					if ( ! empty( $message ) ) : ?>
                        <div id="message" class="updated below-h2"><p><?php echo $message; ?></p></div>
					<?php endif; ?>
                    <form id="custom-table" method="GET" class="ylc-log-table" action="<?php echo $list_url; ?>">
						<?php
						if ( ! apply_filters( 'yith_wcfm_chat_log_hide', false ) ) {
							$table->search_box( esc_html__( 'Search Log' ), 'message' );
						}
						?>
                        <input type="hidden" name="page" value="<?php echo isset( $_GET['page'] ) ? $_GET['page'] : '' ?>" />
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

			if ( 'yith-live-chat_page_ylc_chat_logs' == get_current_screen()->id && ( ! isset( $_GET['action'] ) || ( $_GET['action'] != 'view' ) ) ) {

				$option = 'per_page';
				$args   = array(
					'label'   => esc_html__( 'Conversations', 'yith-live-chat' ),
					'default' => 10,
					'option'  => 'logs_per_page'
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

			return ( 'logs_per_page' == $option ) ? $value : $status;

		}

	}

	/**
	 * Unique access to instance of YLC_Chat_Logs class
	 *
	 * @return \YLC_Chat_Logs
	 */
	function YLC_Chat_Logs() {

		return YLC_Chat_Logs::get_instance();

	}

}