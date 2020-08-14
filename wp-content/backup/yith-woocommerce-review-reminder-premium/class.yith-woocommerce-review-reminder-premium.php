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

if ( ! class_exists( 'YWRR_Review_Reminder_Premium' ) ) {

	/**
	 * Implements features of YWRR plugin
	 *
	 * @class   YWRR_Review_Reminder_Premium
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @package Yithemes
	 */
	class YWRR_Review_Reminder_Premium extends YWRR_Review_Reminder {

		/**
		 * Returns single instance of the class
		 *
		 * @return YWRR_Review_Reminder
		 * @since 1.1.5
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			parent::__construct();

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_action( 'init', array( $this, 'set_ywrr_image_sizes' ) );

			// Include required files
			add_action( 'init', array( $this, 'includes_premium' ), 15 );

			add_action( 'template_redirect', array( $this, 'redirect_to_login' ), 10 );
			add_filter( 'woocommerce_login_redirect', array( $this, 'login_redirect' ) );

			add_filter( 'yith_wcet_email_template_types', array( $this, 'add_yith_wcet_template' ) );
			add_action( 'yith_wcet_after_email_styles', array( $this, 'add_yith_wcet_styles' ), 10, 3 );
			add_filter( 'woocommerce_email_styles', array( $this, 'add_ywrr_styles' ), 10, 2 );
			add_filter( 'ywrr_product_permalink', array( $this, 'set_product_permalink' ), 10, 3 );
			add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'add_custom_fields_premium' ), 10, 2 );

			if ( get_option( 'ywrr_schedule_order_column' ) == 'yes' ) {

				add_filter( 'manage_shop_order_posts_columns', array( $this, 'add_ywrr_column' ), 11 );
				add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_ywrr_column' ), 3, 2 );
				add_filter( 'manage_yith_booking_posts_columns', array( $this, 'add_ywrr_column' ), 11 );
				add_action( 'manage_yith_booking_posts_custom_column', array( $this, 'render_ywrr_column_bookings' ), 3, 2 );
				add_action( 'admin_footer', array( $this, 'order_schedule_template' ) );

			}

			add_filter( 'woocommerce_mail_callback', array( $this, 'mail_use_mandrill' ) );
			add_action( 'load-edit.php', array( $this, 'process_bulk_actions' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_premium' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		}

		/**
		 * Files inclusion
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function includes_premium() {

			include_once( 'includes/ywrr-functions-premium.php' );

			if ( is_admin() ) {
				include_once( 'includes/admin/class-ywrr-ajax-premium.php' );
				include_once( 'includes/admin/meta-boxes/class-ywrr-meta-box.php' );
				include_once( 'templates/admin/ywrr-schedule-table.php' );
			}

		}

		/**
		 * Initialize custom fields
		 *
		 * @param   $path  string
		 * @param   $field array
		 *
		 * @return  string
		 * @since   1.6.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_custom_fields_premium( $path, $field ) {

			if ( $field['type'] == 'ywrr-custom-checklist' ) {
				$path = YWRR_TEMPLATE_PATH . '/admin/ywrr-custom-checklist.php';
			}

			return $path;

		}

		/**
		 * Sets Mandrill as mailer if enabled
		 *
		 * @param   $mailer_func string
		 *
		 * @return  string
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function mail_use_mandrill( $mailer_func ) {
			return get_option( 'ywrr_mandrill_enable' ) == 'yes' ? 'ywrr_mandrill_send' : $mailer_func;
		}

		/**
		 * Set image sizes for email
		 *
		 * @return  void
		 * @since   1.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_ywrr_image_sizes() {

			add_image_size( 'ywrr_picture', 135, 135, true );

		}

		/**
		 * If is active YITH WooCommerce Email Templates, add YWRR to list
		 *
		 * @param   $templates array
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_yith_wcet_template( $templates ) {

			$templates[] = array(
				'id'   => 'yith-review-reminder',
				'name' => 'YITH WooCommerce Review Reminder',
			);

			return $templates;

		}

		/**
		 * If is active YITH WooCommerce Email Templates, add YWRR styles
		 *
		 * @param   $premium_style integer
		 * @param   $meta          array
		 * @param   $current_email WC_Email
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_yith_wcet_styles( $premium_style, $meta, $current_email ) {

			if ( isset( $current_email ) && $current_email->id == 'yith-review-reminder' ) {
				ywrr_email_styles();

				?>
                .ywrr-table td.title-column a{
                color:<?php echo $meta['base_color'] ?>;
                }
				<?php

			}

		}

		/**
		 * Add YWRR styles to WC Emails
		 *
		 * @param   $css   string
		 * @param   $email WC_Email|boolean
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_ywrr_styles( $css, $email = false ) {

			if ( $email && $email->id == 'yith-review-reminder' ) {
				ob_start();
				ywrr_email_styles();
				$css .= ob_get_clean();
			}

			return $css;

		}

		/**
		 * Set the link to the product
		 *
		 * @param   $permalink    string
		 * @param   $customer_id  integer
		 * @param   $no_login     boolean
		 *
		 * @return  string
		 * @since   1.0.4
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_product_permalink( $permalink, $customer_id, $no_login = false ) {

			$link_type = get_option( 'ywrr_mail_item_link' );

			switch ( $link_type ) {
				case 'custom':
					$permalink .= ywrr_check_hash( get_option( 'ywrr_mail_item_link_hash' ) );
					break;
				case 'review':
					$permalink .= '#tab-reviews';
					break;
			}

			$query_args = array();

			if ( get_option( 'ywrr_enable_analytics' ) == 'yes' ) {

				$campaign_source  = str_replace( ' ', '%20', get_option( 'ywrr_campaign_source' ) );
				$campaign_medium  = str_replace( ' ', '%20', get_option( 'ywrr_campaign_medium' ) );
				$campaign_term    = str_replace( ',', '+', get_option( 'ywrr_campaign_term' ) );
				$campaign_content = str_replace( ' ', '%20', get_option( 'ywrr_campaign_content' ) );
				$campaign_name    = str_replace( ' ', '%20', get_option( 'ywrr_campaign_name' ) );

				$query_args['utm_source'] = $campaign_source;
				$query_args['utm_medium'] = $campaign_medium;

				if ( $campaign_term != '' ) {
					$query_args['utm_term'] = $campaign_term;
				}

				if ( $campaign_content != '' ) {
					$query_args['utm_content'] = $campaign_content;
				}

				$query_args['utm_name'] = $campaign_name;

			}

			if ( get_option( 'ywrr_login_from_link' ) == 'yes' && ! $no_login && $customer_id !== 0 ) {
				$query_args['ywrr_login'] = 1;
			}

			if ( ! empty( $query_args ) ) {
				$permalink = add_query_arg( $query_args, $permalink );
			}

			return $permalink;

		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Add the schedule column
		 *
		 * @param   $columns array
		 *
		 * @return  array
		 * @since   1.2.2
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_ywrr_column( $columns ) {

			if ( ! ywrr_vendor_check() ) {
				$columns['ywrr_status'] = esc_html__( 'Review Reminder', 'yith-woocommerce-review-reminder' );
			}

			return $columns;

		}

		/**
		 * Render the schedule column in orders page
		 *
		 * @param   $column  string
		 * @param   $post_id integer
		 *
		 * @return  void
		 * @since   1.2.2
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function render_ywrr_column( $column, $post_id ) {

			if ( ! ywrr_vendor_check() && 'ywrr_status' == $column ) {

				$order = wc_get_order( $post_id );

				if ( ! $order ) {
					return;
				}

				$customer_id    = $order->get_user_id();
				$customer_email = $order->get_billing_email();

				if ( ywrr_check_blocklist( $customer_id, $customer_email ) == true ) {

					$is_funds    = $order->get_meta( '_order_has_deposit' ) == 'yes';
					$is_deposits = $order->get_created_via() == 'yith_wcdp_balance_order';
					//APPLY_FILTER: ywrr_skip_renewal_orders: check if plugin should skip subscription renewal orders
					$is_renew = $order->get_meta( 'is_a_renew' ) == 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );
					//APPLY_FILTER: ywrr_can_ask_for_review: check if plugin can ask for a review
					$can_ask_review = apply_filters( 'ywrr_can_ask_for_review', true, $order );

					if ( ywrr_check_reviewable_items( $post_id ) == 0 || $is_funds || $is_deposits || $is_renew || ! $can_ask_review ) {

						ywrr_get_noreview_message( 'no-items' );

						if ( defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM ) {

							$suborders = YITH_Orders::get_suborder( $post_id );

							if ( ! empty( $suborders ) ) {
								?><br /><?php

								foreach ( $suborders as $suborder_id ) {

									if ( ywrr_check_reviewable_items( $suborder_id ) == 0 ) {
										printf( esc_html__( 'Suborder #%s has no reviewable items', 'yith-woocommerce-review-reminder' ), $suborder_id );

									} else {

										//APPLY_FILTER: yith_wcmv_edit_order_uri: get edit vendor order uri
										$order_uri = apply_filters( 'yith_wcmv_edit_order_uri', esc_url( 'post.php?post=' . absint( $suborder_id ) . '&action=edit' ), absint( $suborder_id ) );
										$link_text = sprintf( esc_html__( 'Suborder %s has reviewable items', 'yith-woocommerce-review-reminder' ), '<strong>#' . $suborder_id . '</strong>' );

										printf( '<a href="%s">%s</a><br />',
										        $order_uri,
										        $link_text
										);

									}

								}

							}

						}

					} else {
						ywrr_get_send_box( $post_id, $order );
					}

				} else {
					ywrr_get_noreview_message();
				}

			}

		}

		/**
		 * Render the schedule column in bookings page
		 *
		 * @param   $column  string
		 * @param   $post_id integer
		 *
		 * @return  void
		 * @since   1.6.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function render_ywrr_column_bookings( $column, $post_id ) {

			if ( ! ywrr_vendor_check() && 'ywrr_status' == $column ) {

				$booking = yith_get_booking( $post_id );

				if ( ! $booking ) {
					return;
				}

				$order = $booking->get_order();
				if ( ! $order ) {
					ywrr_get_noreview_message( 'no-booking' );

					return;
				}
				$customer_id    = $order->get_user_id();
				$customer_email = $order->get_billing_email();

				if ( ywrr_check_blocklist( $customer_id, $customer_email ) == true ) {

					$is_funds    = $order->get_meta( '_order_has_deposit' ) == 'yes';
					$is_deposits = $order->get_created_via() == 'yith_wcdp_balance_order';
					//APPLY_FILTER: ywrr_skip_renewal_orders: check if plugin should skip subscription renewal orders
					$is_renew = $order->get_meta( 'is_a_renew' ) == 'yes' && apply_filters( 'ywrr_skip_renewal_orders', true );
					//APPLY_FILTER: ywrr_can_ask_for_review: check if plugin can ask for a review
					$can_ask_review = apply_filters( 'ywrr_can_ask_for_review', true, $order );

					if ( ! ywrr_items_has_comments_opened( $booking->get_product_id() ) || ywrr_user_has_commented( $booking->get_product_id(), $customer_email ) || $is_funds || $is_deposits || $is_renew || ! $can_ask_review ) {
						ywrr_get_noreview_message( 'no-booking' );
					} else {
						ywrr_get_send_box( $post_id, $order, $booking->get_id(), $booking->order_item_id );
					}

				} else {
					ywrr_get_noreview_message();
				}

			}

		}

		/**
		 * Set up backbone modal for schedule actions
		 *
		 * @return  void
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function order_schedule_template() {

			global $post_type;

			if ( ywrr_vendor_check() || ( 'shop_order' != $post_type && 'yith_booking' != $post_type ) ) {
				return;
			}

			$action_type    = array(
				'id'      => 'action_type',
				'name'    => 'action_type',
				'type'    => 'radio',
				'options' => array(
					'now'      => esc_html__( 'Now', 'yith-woocommerce-review-reminder' ),
					'schedule' => esc_html__( 'Choose a date', 'yith-woocommerce-review-reminder' ) . '{{{data.additional_label}}}',
				),
				'value'   => 'now',
			);
			$schedule_date  = array(
				'id'    => 'schedule_date',
				'name'  => 'schedule_date',
				'type'  => 'datepicker',
				'data'  => array(
					'date-format' => 'yy-mm-dd',
					'min-date'    => 1
				),
				'value' => '{{{data.scheduled_date}}}',
			);
			$buttons        = array(
				'type'    => 'buttons',
				'buttons' => array(
					array(
						'name'  => esc_html__( 'Send', 'yith-woocommerce-review-reminder' ),
						'class' => 'button-primary ywrr-email-action',
					),
					array(
						'name'  => esc_html__( 'Cancel', 'yith-woocommerce-review-reminder' ),
						'class' => 'modal-close',
					),
				)
			);
			$delete_buttons = array(
				'type'    => 'buttons',
				'buttons' => array(
					array(
						'name'  => esc_html__( 'Delete', 'yith-woocommerce-review-reminder' ),
						'class' => 'button-primary ywrr-delete-action',
					),
					array(
						'name'  => esc_html__( 'Cancel', 'yith-woocommerce-review-reminder' ),
						'class' => 'modal-close',
					),
				)
			);

			?>
            <script type="text/template" id="tmpl-ywrr-actions">
                <div class="wc-backbone-modal">
                    <div class="wc-backbone-modal-content yith-plugin-fw yith-plugin-ui ywrr-actions-modal">
                        <section class="wc-backbone-modal-main" role="main">
                            <header class="wc-backbone-modal-header">
                                <h1><?php esc_html_e( 'Schedule a review reminder email', 'yith-woocommerce-review-reminder' ); ?>:</h1>
                                <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                                    <span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'yith-woocommerce-review-reminder' ); ?></span>
                                </button>
                            </header>
                            <article>
								<?php esc_html_e( 'Send this reminder email on', 'yith-woocommerce-review-reminder' ); ?>:
                                <br />
                                <br />
								<?php
								yith_plugin_fw_get_field( $action_type, true );
								?>
                                <div class="ywrr-modal-datepicker">
									<?php
									yith_plugin_fw_get_field( $schedule_date, true );
									?>
                                </div>
                                <div class="error-message"></div>
                                <input class="ywrr-order-id" type="hidden" value="<?php echo '{{{data.order_id}}}' ?>">
                                <input class="ywrr-order-item-id" type="hidden" value="<?php echo '{{{data.order_item_id}}}' ?>">
                                <input class="ywrr-booking-id" type="hidden" value="<?php echo '{{{data.booking_id}}}' ?>">
                                <input class="ywrr-order-date" type="hidden" value="<?php echo '{{{data.order_date}}}' ?>">
                                <input class="ywrr-row-id" type="hidden" value="<?php echo '{{{data.row_id}}}' ?>">
                            </article>
                            <footer>
								<?php yith_plugin_fw_get_field( $buttons, true ); ?>
                            </footer>
                        </section>
                    </div>
                </div>
                <div class="wc-backbone-modal-backdrop modal-close"></div>
            </script>
            <script type="text/template" id="tmpl-ywrr-delete">
                <div class="wc-backbone-modal">
                    <div class="wc-backbone-modal-content yith-plugin-fw yith-plugin-ui ywrr-delete-modal">
                        <section class="wc-backbone-modal-main" role="main">
                            <header class="wc-backbone-modal-header">
                                <h1><?php esc_html_e( 'Cancel a review reminder email', 'yith-woocommerce-review-reminder' ); ?>:</h1>
                                <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                                    <span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'yith-woocommerce-review-reminder' ); ?></span>
                                </button>
                            </header>
                            <article>
								<?php esc_html_e( 'Do you want to cancel the reminder email?', 'yith-woocommerce-review-reminder' ) ?>
                                <br />
                                <div class="error-message"></div>
                                <input class="ywrr-order-id" type="hidden" value="<?php echo '{{{data.order_id}}}' ?>">
                                <input class="ywrr-order-item-id" type="hidden" value="<?php echo '{{{data.order_item_id}}}' ?>">
                                <input class="ywrr-booking-id" type="hidden" value="<?php echo '{{{data.booking_id}}}' ?>">
                                <input class="ywrr-row-id" type="hidden" value="<?php echo '{{{data.row_id}}}' ?>">
                            </article>
                            <footer>
								<?php yith_plugin_fw_get_field( $delete_buttons, true ); ?>
                            </footer>
                        </section>
                    </div>
                </div>
                <div class="wc-backbone-modal-backdrop modal-close"></div>
            </script>
			<?php
		}

		/**
		 * Trigger bulk actions to orders
		 *
		 * @return  void
		 * @throws  Exception
		 * @since   1.2.2
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function process_bulk_actions() {

			if ( ywrr_vendor_check() || ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'shop_order' && $_GET['post_type'] == 'yith_booking' ) ) {
				return;
			}

			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action        = $wp_list_table->current_action();

			// Bail out if this is not a status-changing action
			if ( strpos( $action, 'ywrr_' ) === false ) {
				return;
			}

			$processed = 0;
			$post_ids  = array_map( 'absint', (array) $_REQUEST['post'] );

			if ( $_GET['post_type'] == 'yith_booking' ) {
				foreach ( $post_ids as $post_id ) {

					$booking = yith_get_booking( $post_id );
					$order   = $booking->get_order();
					$ok      = false;

					if ( ! $order ) {
						continue;
					}

					$customer_id    = $order->get_user_id();
					$customer_email = $order->get_billing_email();

					if ( ywrr_check_blocklist( $customer_id, $customer_email ) == true ) {

						if ( ! ywrr_items_has_comments_opened( $booking->get_product_id() ) || ywrr_user_has_commented( $booking->get_product_id(), $customer_email ) ) {
							continue;
						}

						switch ( substr( $action, 5 ) ) {

							case 'send':

								$today      = new DateTime( current_time( 'mysql' ) );
								$order_date = $order->get_date_modified();

								if ( ! $order_date ) {
									$order_date = $order->get_date_created();
								}

								$pay_date        = new DateTime( date( 'Y-m-d H:i:s', yit_datetime_to_timestamp( $order_date ) ) );
								$days            = $pay_date->diff( $today );
								$items_to_review = array( $booking->order_item_id );
								$email_result    = ywrr_send_email( $order->get_id(), $days->days, $items_to_review, array(), 'booking' );

								if ( $email_result === true ) {

									if ( ywrr_check_exists_schedule( $order->get_id(), $booking->get_id() ) != 0 ) {
										ywrr_change_schedule_status( $order->get_id(), 'sent', $booking->get_id() );
									} else {
										ywrr_log_unscheduled_email( $order, $booking->get_id(), ywrr_get_review_list_forced( $items_to_review, $order->get_id() ) );
									}

									$ok = true;

								}

								break;

							case 'reschedule':

								$scheduled_date = date( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + ' . get_option( 'ywrr_mail_schedule_day' ) . ' days' ) );

								if ( ywrr_check_exists_schedule( $order->get_id(), $booking->get_id() ) != 0 ) {
									$items_to_review = array( $booking->order_item_id );
									$list            = ywrr_get_review_list_forced( $items_to_review, $order->get_id() );
									ywrr_reschedule( $order->get_id(), $scheduled_date, $list );

								} else {

									ywrr_schedule_booking_mail( $booking->get_id() );

								}

								$ok = true;

								break;

							case 'cancel':

								if ( ywrr_check_exists_schedule( $order->get_id(), $booking->get_id() ) != 0 ) {

									ywrr_change_schedule_status( $order->get_id(), 'cancelled', $booking->get_id() );
									$ok = true;
								}

								break;

						}

						if ( $ok ) {
							$processed ++;
						}

					}

				}
			} else {
				foreach ( $post_ids as $post_id ) {

					$order = wc_get_order( $post_id );
					$ok    = false;

					if ( ! $order ) {
						continue;
					}

					$customer_id    = $order->get_user_id();
					$customer_email = $order->get_billing_email();

					if ( ywrr_check_blocklist( $customer_id, $customer_email ) == true ) {

						if ( ywrr_check_reviewable_items( $order->get_id() ) == 0 ) {
							continue;
						}

						switch ( substr( $action, 5 ) ) {

							case 'send':

								$today      = new DateTime( current_time( 'mysql' ) );
								$order_date = $order->get_date_modified();

								if ( ! $order_date ) {
									$order_date = $order->get_date_created();
								}

								$pay_date     = new DateTime( date( 'Y-m-d H:i:s', yit_datetime_to_timestamp( $order_date ) ) );
								$days         = $pay_date->diff( $today );
								$email_result = ywrr_send_email( $order->get_id(), $days->days );

								if ( $email_result === true ) {

									if ( ywrr_check_exists_schedule( $order->get_id() ) != 0 ) {
										ywrr_change_schedule_status( $order->get_id(), 'sent' );
									} else {
										ywrr_log_unscheduled_email( $order );
									}

									$ok = true;

								}
								break;

							case 'reschedule':

								$scheduled_date = date( 'Y-m-d', strtotime( current_time( 'mysql' ) . ' + ' . get_option( 'ywrr_mail_schedule_day' ) . ' days' ) );

								if ( ywrr_check_exists_schedule( $order->get_id() ) != 0 ) {

									ywrr_reschedule( $order->get_id(), $scheduled_date );

								} else {

									ywrr_schedule_mail( $order->get_id() );

								}

								$ok = true;

								break;

							case 'cancel':

								if ( ywrr_check_exists_schedule( $order->get_id() ) != 0 ) {

									ywrr_change_schedule_status( $order->get_id() );
									$ok = true;

								}

								break;

						}

						if ( $ok ) {
							$processed ++;
						}

					}

				}
			}

			$sendback = add_query_arg( array( 'post_type' => $_GET['post_type'], 'ywrr_action' => substr( $action, 5 ), 'processed' => $processed, 'ids' => join( ',', $post_ids ) ), '' );

			wp_redirect( esc_url_raw( $sendback ) );
			exit();
		}

		/**
		 * Show admin notices
		 *
		 * @return  void
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_notices() {

			if ( ywrr_vendor_check() ) {
				return;
			}

			global $post_type, $pagenow;
			$message = $classes = '';

			// Bail out if not on shop order list page
			if ( 'edit.php' == $pagenow && ( 'shop_order' == $post_type || 'yith_booking' == $post_type ) ) {
				if ( isset( $_REQUEST['ywrr_action'] ) ) {

					$number = isset( $_REQUEST['processed'] ) ? absint( $_REQUEST['processed'] ) : 0;

					switch ( $_REQUEST['ywrr_action'] ) {

						case'send':
							$message = sprintf( _n( 'Review Reminder: Email sent.', 'Review Reminder: %s emails sent', $number, 'yith-woocommerce-review-reminder' ), number_format_i18n( $number ) );
							break;

						case'reschedule':
							$message = sprintf( _n( 'Review Reminder: Email rescheduled.', 'Review Reminder: %s emails rescheduled.', $number, 'yith-woocommerce-review-reminder' ), number_format_i18n( $number ) );
							break;

						case'cancel':
							$message = sprintf( _n( 'Review Reminder: Email cancelled.', 'Review Reminder: %s emails cancelled.', $number, 'yith-woocommerce-review-reminder' ), number_format_i18n( $number ) );
							break;

						default:
							$message = '';
					}

					$classes = 'notice-success is-dismissible';

				}
			}

			if ( $message ) {
				echo '<div class="notice ' . $classes . '"><p>' . $message . '</p></div>';
			}

		}

		/**
		 * Initializes Javascript with localization
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_scripts_premium() {

			global $post_type;


			if ( ywrr_vendor_check() ) {
				return;
			}

			if ( 'shop_order' == $post_type || 'yith_booking' == $post_type ) {

				wp_enqueue_style( 'yith-plugin-fw-fields' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
				wp_enqueue_style( 'ywrr-actions-premium', yit_load_css_file( YWRR_ASSETS_URL . 'css/ywrr-actions-premium.css' ), array( 'yith-plugin-fw-fields' ), YWRR_VERSION );
				wp_enqueue_script( 'ywrr-actions-premium', yit_load_js_file( YWRR_ASSETS_URL . 'js/ywrr-actions-premium.js' ), array( 'jquery', 'wc-backbone-modal', 'yith-plugin-fw-fields' ), YWRR_VERSION );

				$params = array(
					'ajax_url'              => admin_url( 'admin-ajax.php' ),
					'send_button_label'     => esc_html__( 'Send', 'yith-woocommerce-review-reminder' ),
					'schedule_button_label' => esc_html__( 'Schedule', 'yith-woocommerce-review-reminder' ),
					'missing_date_error'    => esc_html__( 'Please, select a date.', 'yith-woocommerce-review-reminder' ),
					'send_label'            => esc_html__( 'Review Reminder: Send email', 'yith-woocommerce-review-reminder' ),
					'reschedule_label'      => esc_html__( 'Review Reminder: Reschedule email', 'yith-woocommerce-review-reminder' ),
					'cancel_label'          => esc_html__( 'Review Reminder: Cancel email', 'yith-woocommerce-review-reminder' ),
				);

				wp_localize_script( 'ywrr-actions-premium', 'ywrr_actions', $params );

			} elseif ( ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_ywrr_panel' ) ) {

				wp_enqueue_style( 'ywrr-admin-premium', yit_load_css_file( YWRR_ASSETS_URL . 'css/ywrr-admin-premium.css' ), array(), YWRR_VERSION );
				wp_enqueue_script( 'ywrr-admin-premium', yit_load_js_file( YWRR_ASSETS_URL . 'js/ywrr-admin-premium.js' ), array( 'jquery' ), YWRR_VERSION );

			}

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Initializes Javascript
		 *
		 * @return  void
		 * @since   1.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function frontend_scripts() {

			if ( get_option( 'ywrr_mail_item_link' ) == 'product' ) {
				return;
			}

			wp_enqueue_script( 'ywrr-frontend', yit_load_js_file( YWRR_ASSETS_URL . 'js/ywrr-frontend.js' ), array( 'jquery' ), YWRR_VERSION, true );

			$params = array(
				'reviews_tab'  => get_option( 'ywrr_mail_item_link' ) == 'review' ? '#tab-reviews' : ywrr_check_hash( get_option( 'ywrr_mail_item_link_hash' ) ),
				'reviews_form' => ywrr_check_hash( get_option( 'ywrr_comment_form_id' ) ),
				'offset'       => get_option( 'ywrr_comment_form_offset' )
			);

			wp_localize_script( 'ywrr-frontend', 'ywrr', $params );

		}

		/**
		 * Redirects to login page if querystring is set
		 *
		 * @return  void
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function redirect_to_login() {

			if ( ! is_user_logged_in() && isset( $_GET['ywrr_login'] ) ) {

				global $post;

				$product_link = $this->set_product_permalink( get_permalink( $post->ID ), 0, true );
				$redirect     = add_query_arg( 'redirect_to', urlencode( $product_link ), wc_get_page_permalink( 'myaccount' ) );

				wp_redirect( $redirect );
				exit();

			}

		}

		/**
		 * Redirects to product page after login
		 *
		 * @param   $redirect_to string
		 *
		 * @return  string
		 * @since   1.6.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function login_redirect( $redirect_to ) {
			if ( isset( $_REQUEST['redirect_to'] ) ) {
				return urldecode( $_REQUEST['redirect_to'] );
			} else {
				return $redirect_to;
			}
		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Register plugins for activation tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWRR_INIT, YWRR_SECRET_KEY, YWRR_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWRR_SLUG, YWRR_INIT );
		}

		/**
		 * Plugin row meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $new_row_meta_args array
		 * @param   $plugin_meta       mixed
		 * @param   $plugin_file       string
		 * @param   $plugin_data       mixed
		 * @param   $status            mixed
		 * @param   $init_file         string
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWRR_INIT' ) {
			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $links array
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;
		}

	}

}