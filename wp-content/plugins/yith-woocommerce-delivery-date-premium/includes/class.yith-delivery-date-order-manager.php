<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Delivery_Date_Order_Manager' ) ) {

	class YITH_Delivery_Date_Order_Manager {

		protected static $_instance;

		public function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'add_order_delivery_date_meta_boxes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'include_scripts' ) );
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'edit_columns' ) );
			add_filter( 'manage_edit-shop_order_sortable_columns', array( $this, 'edit_sortable_columns' ) );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'custom_columns' ) );
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_order_meta' ), 99 );

			add_filter( 'ywcdd_send_email', array( $this, 'can_send_email' ), 10, 2 );

			if ( is_admin() ) {
				add_filter( 'request', array( $this, 'request_query' ), 25 );
				add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ), 15 );
			}

			add_action( 'wp_ajax_update_order_details', array( $this, 'update_order_details' ) );

		}

		/**
		 * @return YITH_Delivery_Date_Admin
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * @author YITH
		 * @since 1.0.0
		 */
		public function add_order_delivery_date_meta_boxes() {

			add_meta_box(
				'yith-wc-order-delivery-date-metabox',
				__( 'Delivery Details', 'yith-woocommerce-delivery-date' ),
				array(
					$this,
					'order_delivery_date_meta_box_content',
				),
				'shop_order',
				'side',
				'core'
			);

		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function order_delivery_date_meta_box_content() {

			wc_get_template( 'meta-boxes/order-delivery-details-meta-box.php', array(), YITH_DELIVERY_DATE_TEMPLATE_PATH, YITH_DELIVERY_DATE_TEMPLATE_PATH );
		}

		/**
		 * @param $post_id
		 *
		 * @since 1.0.0
		 *
		 * @author YITHEMES
		 */
		public function save_order_meta( $post_id ) {

			$post_type = get_post_type( $post_id );

			if ( 'shop_order' === $post_type ) {

				$order = wc_get_order( $post_id );

				if ( isset( $_POST['ywcdd_order_shipped'] ) ) {
					$shipping_date = $order->get_meta( 'ywcdd_order_shipping_date' );

					if ( ! empty( $shipping_date ) ) {
						$shipped = isset( $_POST['ywcdd_order_shipped'] ) ? 'yes' : 'no';

						$order->update_meta_data( 'ywcdd_order_shipped', $shipped );
						$order->save();

						$email_is_sent = yit_get_prop( $order, '_ywcdd_email_sent' );

						if ( 'yes' === $shipped && empty( $email_is_sent ) && apply_filters( 'ywcdd_send_email', true, $order ) ) {

							WC()->mailer();
							do_action( 'yith_advise_user_delivery_email_notification', $order );
						}

						do_action( 'yith_delivery_date_suborders_shipped', $post_id, $shipped );
					}
				}

				if ( isset( $_POST['ywcdd_edit_processing_method'] ) ) {

					$processing_id  = $_POST['ywcdd_edit_processing_method'];
					$carrier_id     = $_POST['ywcdd_edit_carrier'];
					$shipping_date  = $_POST['ywcdd_edit_processing_date'];
					$delivery_date  = $_POST['ywcdd_edit_delivery_date'];
					$time_from      = $_POST['ywcdd_edit_time_from'];
					$time_to        = $_POST['ywcdd_edit_time_to'];
					$meta_to_update = array(
						'ywcdd_order_processing_method' => $processing_id,
						'ywcdd_order_carrier_id'        => $carrier_id,
						'ywcdd_order_shipping_date'     => $shipping_date,
						'ywcdd_order_delivery_date'     => $delivery_date,
						'ywcdd_order_slot_from'         => $time_from,
						'ywcdd_order_slot_to'           => $time_to,
						'ywcdd_order_carrier'           => get_the_title( $carrier_id ),
					);

					foreach ( $meta_to_update as $key => $value ) {
						$order->update_meta_data( $key, $value );
					}
					$order->save();
				}
			}
		}

		public function edit_columns( $columns ) {

			$columns['shipping_date'] = __( 'Shipping date', 'yith-woocommerce-delivery-date' );
			$columns['delivery_date'] = __( 'Delivery date', 'yith-woocommerce-delivery-date' );

			return $columns;
		}

		public function edit_sortable_columns( $sortable_columns ) {

			$sortable_columns['shipping_date'] = 'ywcdd_order_shipping_date';
			$sortable_columns['delivery_date'] = 'ywcdd_order_delivery_date';

			return $sortable_columns;
		}

		public function custom_columns( $column_name ) {
			global $post, $the_order;

			if ( empty( $the_order ) ) {
				$the_order = $post;
			}
			$order_id = yit_get_prop( $the_order, 'id' );
			if ( empty( $the_order ) || $order_id !== $post->ID ) {
				$the_order = wc_get_order( $post->ID );
			}

			if ( 'shipping_date' == $column_name ) {

				$ship_date = $this->get_more_near_date_details( $the_order );

				$value = __( 'No shipping date', 'yith-woocommerce-delivery-date' );

				if ( ! empty( $ship_date ) ) {

					$date_format = apply_filters( 'ywcdd_custom_order_column_date_format', 'Y/m/d' );
					$value       = date( $date_format, strtotime( $ship_date ) );

				}

				echo $value;
			}

			if ( 'delivery_date' == $column_name ) {
				$ship_date = $this->get_more_near_date_details( $the_order, 'delivery' );

				$value = __( 'No delivery date', 'yith-woocommerce-delivery-date' );

				if ( ! empty( $ship_date ) ) {

					$date_format = apply_filters( 'ywcdd_custom_order_column_date_format', 'Y/m/d' );
					$value       = date( $date_format, strtotime( $ship_date ) );

					$time_from = $the_order->get_meta( 'ywcdd_order_slot_from' );
					$time_to   = $the_order->get_meta( 'ywcdd_order_slot_to' );
					if ( ! empty( $time_from ) && ! empty( $time_to ) ) {

						$value = sprintf( '%s<br/><small>%s - %s</small>', $value, $time_from, $time_to );
					}
				}

				echo $value;
			}

		}

		/**
		 * @param WC_Order $order
		 *
		 * @throws
		 */
		public function get_more_near_date_details( $order, $return = 'shipping' ) {

			$shipping_date = $order->get_meta( 'ywcdd_order_shipping_date' );
			$delivery_date = $order->get_meta( 'ywcdd_order_delivery_date' );

			if ( empty( $shipping_date ) ) {

				$today = current_time( 'Y-m-d' );
				$today = new WC_DateTime( $today, new DateTimeZone( 'UTC' ) );
				$min   = false;
				foreach ( $order->get_items() as $order_item ) {

					$processing_date = $order_item->get_meta( '_ywcdd_last_shipping_date' );

					if ( ! empty( $processing_date ) ) {
						$item_date = new WC_DateTime( $processing_date, new DateTimeZone( 'UTC' ) );

						if ( $item_date >= $today ) {

							$current_diff = $item_date->diff( $today )->days;

							if ( ! $min || $min > $current_diff ) {
								$min           = $current_diff;
								$shipping_date = $item_date->date( 'Y/m/d' );
								$delivery_date = $order_item->get_meta( 'ywcdd_product_delivery_date' );
							}
						}
					}
				}
			}

			return 'shipping' == $return ? $shipping_date : $delivery_date;
		}

		public function include_scripts() {

			$current_screen = get_current_screen();
			if ( $current_screen->id == 'shop_order' ) {

				wp_enqueue_script( 'ywcdd_timepicker', YITH_DELIVERY_DATE_ASSETS_URL . 'js/timepicker/' . yit_load_js_file( 'jquery.timepicker.js' ), array( 'jquery' ), YITH_DELIVERY_DATE_VERSION, true );
				wp_enqueue_style( 'ywcdd_timepicker', YITH_DELIVERY_DATE_ASSETS_URL . 'css/timepicker/jquery.timepicker.css', array(), YITH_DELIVERY_DATE_VERSION );

				wp_enqueue_style( 'delivery_date_order_metabox', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_order_metaboxes.css', array(), YITH_DELIVERY_DATE_VERSION );

				$args = array(
					'ajax_url'                   => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'actions'                    => array(
						'update_order_details' => 'update_order_details',
					),
					'update_order_details_nonce' => wp_create_nonce( 'update-order-details' ),
					'timeformat'                 => 'H:i',
					'timestep'                   => get_option( 'ywcdd_timeslot_step', 30 ),
				);
				wp_register_script(
					'delivery_date_order_metabox',
					YITH_DELIVERY_DATE_ASSETS_URL . 'js/' . yit_load_js_file( 'yith_deliverydate_order_metaboxes.js' ),
					array(
						'jquery',
						'jquery-ui-datepicker',
					),
					YITH_DELIVERY_DATE_VERSION,
					true
				);

				wp_localize_script( 'delivery_date_order_metabox', 'ywcdd_order_args', $args );
				wp_enqueue_script( 'delivery_date_order_metabox' );
			}
		}

		/**
		 * @param bool     $send_email
		 * @param WC_Order $order
		 *
		 * @return bool
		 */
		public function can_send_email( $send_email, $order ) {

			if ( 'yes' == get_option( 'ywcdd_user_privacy', 'no' ) ) {

				$not_send_email = yit_get_prop( $order, '_ywcdd_not_send', true );

				if ( $not_send_email === 'yes' ) {

					$send_email = false;
				}
			}

			return $send_email;
		}


		/**
		 * add query vars to sort the order by shipping or delivery date
		 *
		 * @param array $query_vars
		 *
		 * @return array
		 * @author Salvatore Strano
		 */
		public function request_query( $query_vars ) {
			global $typenow;
			if ( 'shop_order' == $typenow ) {

				if ( isset( $query_vars['orderby'] ) ) {
					$orderby        = $query_vars['orderby'];
					$custom_orderby = array( 'ywcdd_order_shipping_date', 'ywcdd_order_delivery_date' );

					if ( in_array( $orderby, $custom_orderby ) ) {
						$query_vars = array_merge(
							$query_vars,
							array(
								'meta_key' => $orderby,
							)
						);
					}
				}

				$meta_key = false;
				if ( ! empty( $_GET['ywcdd_order_shipping_date'] ) ) {
					$meta_key = 'ywcdd_order_shipping_date';

				} elseif ( ! empty( $_GET['ywcdd_order_delivery_date'] ) ) {

					$meta_key = 'ywcdd_order_delivery_date';
				}

				if ( $meta_key ) {
					$meta_value = $_GET[ $meta_key ];
					$month      = substr( $meta_value, 0, 4 );
					$year       = substr( $meta_value, 4 );

					$first_date = $month . '-' . $year . '-01';
					$last_date  = date( 'Y-m-t', strtotime( $first_date ) );

					if ( ! isset( $query_vars['meta_query'] ) ) {
						$query_vars['meta_query'] = array();
					}

					$query_vars['meta_query'][] = array(
						'key'     => $meta_key,
						'value'   => array( $first_date, $last_date ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE',
					);

				}
			}

			return $query_vars;
		}

		public function restrict_manage_posts() {
			global $typenow;

			if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ), true ) ) {

				$this->render_filters( $typenow, 'ywcdd_order_shipping_date' );
				$this->render_filters( $typenow, 'ywcdd_order_delivery_date' );

			}
		}

		public function render_filters( $post_type, $type ) {
			global $wpdb, $wp_locale;
			$extra_checks = "AND post_status != 'auto-draft'";
			if ( ! isset( $_GET['post_status'] ) || 'trash' !== $_GET['post_status'] ) {
				$extra_checks .= " AND post_status != 'trash'";
			} elseif ( isset( $_GET['post_status'] ) ) {
				$extra_checks = $wpdb->prepare( ' AND post_status = %s', $_GET['post_status'] );
			}

			$months = $wpdb->get_results(
				$wpdb->prepare(
					"
			SELECT DISTINCT YEAR( meta_value ) AS year, MONTH( meta_value ) AS month
			FROM $wpdb->postmeta JOIN $wpdb->posts ON $wpdb->postmeta.post_id = $wpdb->posts.ID
			WHERE post_type = %s AND meta_key = %s AND meta_value !=''
			$extra_checks
			ORDER BY meta_value DESC
		",
					$post_type,
					$type
				)
			);

			$month_count = count( $months );

			if ( ! $month_count || ( 1 == $month_count && 0 == $months[0]->month ) ) {
				return;
			}

			$m              = isset( $_GET[ $type ] ) ? (int) $_GET[ $type ] : 0;
			$general_option = 'ywcdd_order_shipping_date' == $type ? __( 'All Shipping date', 'yith-woocommerce-delivery-date' ) : __( 'All Delivery date', 'yith-woocommerce-delivery-date' );
			?>
			<label for="filter-by-<?php echo $type; ?>"
				   class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
			<select name="<?php echo $type; ?>" id="filter-by-<?php echo $type; ?>">
				<option<?php selected( $m, 0 ); ?> value="0"><?php echo $general_option; ?></option>
				<?php
				foreach ( $months as $arc_row ) {
					if ( 0 == $arc_row->year ) {
						continue;
					}

					$month = zeroise( $arc_row->month, 2 );
					$year  = $arc_row->year;

					printf(
						"<option %s value='%s'>%s</option>\n",
						selected( $m, $year . $month, false ),
						esc_attr( $arc_row->year . $month ),
						/* translators: 1: Month name, 2: 4-digit year. */
						sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
					);
				}
				?>
			</select>
			<?php

		}

		public function update_order_details() {

			check_ajax_referer( 'update-order-details', 'security' );

			$order_id = ! empty( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : false;
			if ( $order_id ) {

				$processing_method_id = ! empty( $_REQUEST['processing_method_id'] ) ? $_REQUEST['processing_method_id'] : false;
				$carrier_id           = ! empty( $_REQUEST['carrier_id'] ) ? $_REQUEST['carrier_id'] : false;
				$processing_date      = ! empty( $_REQUEST['processing_date'] ) ? $_REQUEST['processing_date'] : false;
				$delivery_date        = ! empty( $_REQUEST['delivery_date'] ) ? $_REQUEST['delivery_date'] : false;
				$timefrom             = ! empty( $_REQUEST['time_from'] ) ? $_REQUEST['time_from'] : false;
				$timeto               = ! empty( $_REQUEST['time_to'] ) ? $_REQUEST['time_to'] : false;

				if ( $processing_method_id && $carrier_id && $processing_date && $delivery_date && $timefrom && $timeto ) {

					$order                  = wc_get_order( $order_id );
					$add_event_order_status = get_option( 'ywcdd_add_event_into_calendar' );
					YITH_Delivery_Date_Calendar()->delete_event_by_order_id( $order_id );

					if ( in_array( $order->get_status(), $add_event_order_status ) ) {
						/**add new shipping event and add new delivery event into calendar*/
						YITH_Delivery_Date_Calendar()->add_calendar_event( $processing_method_id, '', 'shipping_to_carrier', $processing_date, '', $order_id );

						YITH_Delivery_Date_Calendar()->add_calendar_event( $carrier_id, '', 'delivery_day', $delivery_date, $delivery_date, $order_id );
					}

					$order_meta = array(
						'ywcdd_order_delivery_date'     => $delivery_date,
						'ywcdd_order_shipping_date'     => $processing_date,
						'ywcdd_order_slot_from'         => $timefrom,
						'ywcdd_order_slot_to'           => $timeto,
						'ywcdd_order_carrier_id'        => $carrier_id,
						'ywcdd_order_processing_method' => $processing_method_id,
						'ywcdd_order_carrier'           => get_the_title( $carrier_id ),

					);

					foreach ( $order_meta as $key => $meta ) {

						$order->update_meta_data( $key, $meta );
					}
					$order->save();
					$add_event_order_status = get_option( 'ywcdd_add_event_into_calendar' );
					YITH_Delivery_Date_Calendar()->delete_event_by_order_id( $order_id );

					if ( in_array( $order->get_status(), $add_event_order_status ) ) {
						// add new shipping event and add new delivery event into calendar.
						YITH_Delivery_Date_Calendar()->add_calendar_event( $processing_method_id, '', 'shipping_to_carrier', $processing_date, '', $order_id );
						YITH_Delivery_Date_Calendar()->add_calendar_event( $carrier_id, '', 'delivery_day', $delivery_date, $delivery_date, $order_id );
					}

				}
			}
		}
	}
}

if ( ! function_exists( 'YITH_Delivery_Date_Order_Manager' ) ) {

	function YITH_Delivery_Date_Order_Manager() {
		YITH_Delivery_Date_Order_Manager::get_instance();
	}
}

YITH_Delivery_Date_Order_Manager();
