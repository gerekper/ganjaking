<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Delivery_Date_Admin' ) ) {

	class YITH_Delivery_Date_Admin {

		protected static $_instance;


		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 20 );

			// manage time slot, priority 15 after woocommerce init option
			add_action( 'admin_init', array( $this, 'add_time_slot' ), 15 );

			add_action( 'wp_ajax_update_time_slot', array( $this, 'update_time_slot' ) );
			add_action( 'wp_ajax_delete_time_slot', array( $this, 'delete_time_slot' ) );

			// manage calendar ( custom holidays )
			add_action( 'wp_ajax_show_current_month', array( $this, 'show_current_month' ) );
			add_action( 'wp_ajax_enable_disable_holidays', array( $this, 'enable_disable_holidays' ) );
			add_action( 'wp_ajax_add_holidays', array( $this, 'add_holidays' ) );
			add_action( 'wp_ajax_update_holidays', array( $this, 'update_holidays' ) );
			add_action( 'wp_ajax_delete_calendar_holidays', array( $this, 'delete_calendar_holidays' ) );
			add_action( 'wp_ajax_delete_holidays', array( $this, 'delete_holidays' ) );

			// add custom tab in plugin panel
			add_action( 'yith_wcdd_timeslot_panel', array( $this, 'add_timeslot_table_field' ) );
			add_action( 'yith_wcdd_shippingday_panel', array( $this, 'show_shippingday_panel' ) );
			add_action( 'yith_wcdd_general_calendar_tab', array( $this, 'show_calendar_panel' ) );

			// add admin notices
			add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );

			add_action( 'ywcdd_show_processing_method_tab', array( $this, 'add_processing_method_tab' ) );
			add_action( 'ywcdd_show_carrier_tab', array( $this, 'add_carrier_tab' ) );

			add_filter( 'yith_plugin_fw_metabox_class', array( $this, 'add_custom_metabox_class' ), 10, 2 );
			add_filter( 'yith_plugin_fw_panel_wc_extra_row_classes', array( $this, 'add_extra_classes' ), 10, 2 );

			add_filter(
				'yith_plugin_fw_toggle_element_title_ywcdd_holidays_option',
				array(
					$this,
					'show_holiday_toggle_element_title',
				),
				10,
				3
			);
			add_filter(
				'yith_plugin_fw_toggle_element_subtitle_ywcdd_holidays_option',
				array(
					$this,
					'show_holiday_toggle_element_subtitle',
				),
				10,
				3
			);

			add_filter(
				'yith_plugin_fw_toggle_element_title_yith_new_shipping_day_prod',
				array(
					$this,
					'show_processing_product_toggle_element_title',
				),
				10,
				3
			);

			add_filter(
				'yith_plugin_fw_toggle_element_title_yith_new_shipping_day_cat',
				array(
					$this,
					'show_processing_product_category_toggle_element_title',
				),
				10,
				3
			);

			add_action( 'wp_ajax_update_custom_processing_title', array( $this, 'update_custom_processing_title' ) );

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
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function show_calendar_panel() {

			wc_get_template( 'calendar.php', array(), '', YITH_DELIVERY_DATE_TEMPLATE_PATH . 'admin/' );
		}


		/**
		 * add style and script in admin
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function enqueue_admin_scripts() {

			global $post;
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$is_delivery_panel_page         = ( isset( $_GET['page'] ) && 'yith_delivery_date_panel' === $_GET['page'] );
			$is_carrier_post_type           = ( isset( $post ) && 'yith_carrier' === get_post_type( $post->ID ) ) || ( isset( $_GET['post_type'] ) && 'yith_carrier' === $_GET['post_type'] );
			$is_processing_method_post_type = ( isset( $post ) && 'yith_proc_method' === get_post_type( $post->ID ) ) || ( isset( $_GET['post_type'] ) && 'yith_proc_method' === $_GET['post_type'] );
			$is_delivery_table_post_type    = ( isset( $post ) && 'yith_product_table' === get_post_type( $post->ID ) ) || ( isset( $_GET['post_type'] ) && 'yith_product_table' === $_GET['post_type'] );
			if ( $is_delivery_panel_page || $is_carrier_post_type || $is_processing_method_post_type ) {

				wp_register_script( 'ywcdd_timepicker', YITH_DELIVERY_DATE_ASSETS_URL . 'js/timepicker/jquery.timepicker' . $suffix . '.js', array( 'jquery' ), YITH_DELIVERY_DATE_VERSION, true );
				wp_register_style( 'ywcdd_timepicker', YITH_DELIVERY_DATE_ASSETS_URL . 'css/timepicker/jquery.timepicker.css', array(), YITH_DELIVERY_DATE_VERSION );

				// Calendar ASSETS
				wp_register_script( 'moment', YITH_DELIVERY_DATE_ASSETS_URL . 'js/fullcalendar/moment.min.js', array( 'jquery' ), '3.0.0', true );
				wp_register_script(
					'ywcdd_fullcalendar',
					YITH_DELIVERY_DATE_ASSETS_URL . 'js/fullcalendar/fullcalendar.min.js',
					array(
						'jquery',
						'moment',
						'jquery-ui-datepicker',
					),
					'3.0.0',
					true
				);
				wp_register_script(
					'ywcdd_fullcalendar_language',
					YITH_DELIVERY_DATE_ASSETS_URL . 'js/fullcalendar/locale-all.js',
					array(
						'jquery',
						'moment',
						'ywcdd_fullcalendar',
					),
					'3.0.0',
					true
				);
				wp_register_style( 'ywcdd_fullcalendar_style', YITH_DELIVERY_DATE_ASSETS_URL . 'css/fullcalendar/fullcalendar.min.css', array(), '3.0.0' );

			}

			if ( $is_delivery_panel_page ) {

				wp_enqueue_script( 'ywcdd_timepicker' );
				wp_enqueue_script( 'ywcdd_fullcalendar' );
				wp_enqueue_script( 'ywcdd_fullcalendar_language' );

				wp_enqueue_style( 'ywcdd_timepicker' );
				wp_enqueue_style( 'ywcdd_fullcalendar_style' );

				wp_register_script( 'yith_delivery_date_panel', YITH_DELIVERY_DATE_ASSETS_URL . 'js/yith_deliverydate_admin' . $suffix . '.js', array( 'jquery' ), YITH_DELIVERY_DATE_VERSION, true );

				$params = array(
					'ajax_url'     => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'actions'      => array(
						'update_time_slot'               => 'update_time_slot',
						'delete_time_slot'               => 'delete_time_slot',
						'update_custom_processing_title' => 'update_custom_processing_title',
					),
					'empty_row'    => sprintf( '<tr class="no-items"><td class="colspanchange" colspan="6">%s</td></tr>', __( 'No item found.', 'yith-woocommerce-delivery-date' ) ),
					'timeformat'   => 'H:i',
					'timestep'     => get_option( 'ywcdd_timeslot_step', 30 ),
					'dateformat'   => get_option( 'date_format' ),
					'plugin_nonce' => YITH_DELIVERY_DATE_SLUG,
				);
				wp_enqueue_script( 'yith_delivery_date_panel' );
				wp_localize_script( 'yith_delivery_date_panel', 'yith_delivery_parmas', $params );

				wp_enqueue_style( 'yith_delivery_date_panel_css', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_delivery_date_admin.css', array(), YITH_DELIVERY_DATE_VERSION );

				wp_enqueue_script( 'yith_wcdd_calendar', YITH_DELIVERY_DATE_ASSETS_URL . 'js/yith_deliverydate_calendar' . $suffix . '.js', array( 'jquery' ), YITH_DELIVERY_DATE_VERSION, true );

				$locale = substr( get_locale(), 0, 2 );

				$timezone_format = 'Y-m-d H:i:s';

				$now    = strtotime( date_i18n( $timezone_format ) );
				$now    = strtotime( 'midnight', $now );
				$params = array(
					'starday'           => date( 'Y-m-d', $now ),
					'dateformat'        => 'yy-mm-dd',
					'calendar_language' => $locale,
					'ajax_url'          => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'actions'           => array(
						'add_holidays'             => 'add_holidays',
						'delete_calendar_holidays' => 'delete_calendar_holidays',
						'delete_holidays'          => 'delete_holidays',
						'update_holidays'          => 'update_holidays',
						'show_current_month'       => 'show_current_month',
					),
				);

				wp_localize_script( 'yith_wcdd_calendar', 'ywcdd_calendar_params', $params );

			}
			if ( $is_carrier_post_type ) {
				wp_enqueue_style( 'yith_delivery_date_panel_css', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_delivery_date_admin.css', array(), YITH_DELIVERY_DATE_VERSION );

				wp_register_script(
					'yith_wcdd_carrier',
					YITH_DELIVERY_DATE_ASSETS_URL . 'js/yith_deliverydate_carrier' . $suffix . '.js',
					array(
						'jquery',
						'jquery-blockui',
						'wc-enhanced-select',
					),
					YITH_DELIVERY_DATE_VERSION
				);
				wp_register_style( 'ywcdd_carrier_metaboxes', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_carrier_metaboxes.css', array(), YITH_DELIVERY_DATE_VERSION );
				wp_enqueue_style( 'ywcdd_timepicker' );
			}

			if ( $is_processing_method_post_type ) {

				wp_register_script(
					'yith_wcdd_processing_method',
					YITH_DELIVERY_DATE_ASSETS_URL . 'js/yith_deliverydate_processing_method' . $suffix . '.js',
					array(
						'jquery',
						'jquery-blockui',
					),
					YITH_DELIVERY_DATE_VERSION
				);
				wp_register_style( 'ywcdd_processing_method_metaboxes', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_processing_method_metaboxes.css', array(), YITH_DELIVERY_DATE_VERSION );
				wp_enqueue_style( 'ywcdd_timepicker' );
			}

			if ( $is_delivery_table_post_type ) {

				wp_register_script( 'yith_delivery_table_metaboxes', YITH_DELIVERY_DATE_ASSETS_URL . 'js/' . yit_load_js_file( 'yith_delivery_date_admin_qty_table.js' ), array( 'jquery' ), YITH_DELIVERY_DATE_VERSION, true );
				wp_register_style( 'yith_delivery_table_metaboxes', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_delivery_date_admin_qty_table.css', array(), YITH_DELIVERY_DATE_VERSION );

				wp_enqueue_script( 'yith_delivery_table_metaboxes' );
				wp_enqueue_style( 'yith_delivery_table_metaboxes' );
			}
		}

		/**
		 * add time slot
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_time_slot() {

			if ( isset( $_POST['yith_new_timeslot'] ) ) {

				$timefrom  = $_POST['yith_new_timeslot']['timefrom'];
				$timeto    = $_POST['yith_new_timeslot']['timeto'];
				$max_order = $_POST['yith_new_timeslot']['max_order'];
				$fee       = $_POST['yith_new_timeslot']['fee'];
				$override  = 'no';
				$days      = array();

				if ( $timefrom !== '' && $timeto !== '' ) {

					$timeslots = get_option( 'yith_delivery_date_time_slot', array() );

					$id      = uniqid( 'ywcdd_gen_timeslot_' );
					$newslot = array(
						'timefrom'      => $timefrom,
						'timeto'        => $timeto,
						'max_order'     => $max_order,
						'fee'           => $fee,
						'override_days' => $override,
						'day_selected'  => $days,
					);

					$timeslots[ $id ] = $newslot;

					update_option( 'yith_delivery_date_time_slot', $timeslots );
				}
			}
		}

		/**
		 * update time slot via ajax
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function update_time_slot() {

			if ( isset( $_POST['plugin_nonce'] ) && YITH_DELIVERY_DATE_SLUG === $_POST['plugin_nonce'] && isset( $_POST['slot_action'] ) && 'update_slot' === $_POST['slot_action'] ) {

				$time_from     = $_POST['ywcdd_time_from'];
				$time_to       = $_POST['ywcdd_time_to'];
				$max_order     = $_POST['ywcdd_max_order'];
				$fee           = $_POST['ywcdd_fee'];
				$item_id       = $_POST['item_id'];
				$override_days = $_POST['override_days'];
				$days          = isset( $_POST['ywcdd_day'] ) ? $_POST['ywcdd_day'] : array();

				$time_slots = get_option( 'yith_delivery_date_time_slot' );

				if ( ! empty( $time_slots ) && isset( $time_slots[ $item_id ] ) ) {

					$single_slot                  = $time_slots[ $item_id ];
					$single_slot['timefrom']      = $time_from;
					$single_slot['timeto']        = $time_to;
					$single_slot['max_order']     = $max_order;
					$single_slot['fee']           = $fee;
					$single_slot['override_days'] = $override_days;
					$single_slot['day_selected']  = $days;
					$time_slots[ $item_id ]       = $single_slot;

					update_option( 'yith_delivery_date_time_slot', $time_slots );
				}

				wp_send_json( array( 'result' => 'ok' ) );
			}
		}

		/**
		 * delete time slot via ajax
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function delete_time_slot() {

			if ( isset( $_POST['plugin_nonce'] ) && YITH_DELIVERY_DATE_SLUG === $_POST['plugin_nonce'] && isset( $_POST['slot_action'] ) && 'delete_slot' === $_POST['slot_action'] ) {

				$item_id    = $_POST['item_id'];
				$time_slots = get_option( 'yith_delivery_date_time_slot' );

				if ( ! empty( $time_slots ) && isset( $time_slots[ $item_id ] ) ) {

					$new_time_slots = array();
					foreach ( $time_slots as $key => $slot ) {
						if ( $key != $item_id ) {

							$new_time_slots[ $key ] = $slot;
						}
					}
					update_option( 'yith_delivery_date_time_slot', $new_time_slots );
				}
			}
			wp_send_json( array( 'result' => 'ok' ) );

		}

		/**
		 * enable or disable the single custom processing days for categories
		 *
		 * @author YITH
		 * @since 2.0.0
		 */
		public function enable_disable_category_day() {

			if ( isset( $_POST['ywcdd_category_id'] ) ) {

				$category_id   = $_POST['ywcdd_category_id'];
				$enable        = $_POST['ywcdd_category_enable'];
				$category_days = get_option( 'yith_new_shipping_day_cat', array() );

				if ( isset( $category_days[ $category_id ] ) ) {

					$category_days[ $category_id ]['enabled'] = $enable;

					update_option( 'yith_new_shipping_day_cat', $category_days );
				}

				wp_send_json( array( 'result' => true ) );
			}
		}

		/**
		 * update process category day
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function update_category_day() {
			if ( ! empty( $_POST['ywcdd_category_id'] ) ) {

				$category_id = $_POST['ywcdd_category_id'];
				$args        = array();
				$arg         = isset( $_POST['ywcdd_args'] ) ? $_POST['ywcdd_args'] : '';
				parse_str( $arg, $args );

				$quantity_days = isset( $args['yith_new_shipping_day_cat']['need_process_day'] ) ? $args['yith_new_shipping_day_cat']['need_process_day'] : array();

				$category_day = get_option( 'yith_new_shipping_day_cat', array() );

				if ( isset( $category_day[ $category_id ] ) ) {
					$category_day[ $category_id ]['need_process_day'] = $quantity_days;
					$category_day[ $category_id ]['enabled']          = isset( $args['yith_new_shipping_day_cat']['enabled'] );

				} else {
					$category_day[ $category_id ] = array(
						'category'         => $category_id,
						'need_process_day' => $quantity_days,
						'enabled'          => 'yes',
					);

				}

				update_option( 'yith_new_shipping_day_cat', $category_day );

				if ( isset( $_POST['ywcdd_action'] ) && 'add' === $_POST['ywcdd_action'] ) {
					$type = 'category';
					ob_start();
					include YITH_DELIVERY_DATE_TEMPLATE_PATH . '/admin/custom-processing-day-view.php';
					$template = ob_get_contents();
					ob_end_clean();

					wp_send_json( array( 'template' => $template ) );
				}
			}
		}

		/**
		 * delete process category day
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function delete_category_day() {

			if ( isset( $_POST['ywcdd_category_id'] ) ) {

				$category_id  = $_POST['ywcdd_category_id'];
				$category_day = get_option( 'yith_new_shipping_day_cat', array() );

				if ( isset( $category_day[ $category_id ] ) ) {
					unset( $category_day[ $category_id ] );
					update_option( 'yith_new_shipping_day_cat', $category_day );
				}
			}
		}

		/**
		 * enable or disable single custom processing product day
		 *
		 * @since 2.0.0
		 * @author YITH
		 */
		public function enable_disable_product_day() {
			if ( isset( $_POST['ywcdd_product_id'] ) ) {

				$product_id   = $_POST['ywcdd_product_id'];
				$enable       = $_POST['ywcdd_product_enable'];
				$product_days = get_option( 'yith_new_shipping_day_prod', array() );

				if ( isset( $product_days[ $product_id ] ) ) {

					$product_days[ $product_id ]['enabled'] = $enable;

					update_option( 'yith_new_shipping_day_prod', $product_days );
				}

				wp_send_json( array( 'result' => true ) );
			}
		}

		/**
		 * update process product day
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function update_product_day() {

			if ( ! empty( $_POST['ywcdd_product_id'] ) ) {

				$product_id = $_POST['ywcdd_product_id'];
				$args       = array();
				$arg        = isset( $_POST['ywcdd_args'] ) ? $_POST['ywcdd_args'] : '';
				parse_str( $arg, $args );
				$quantity_days = isset( $args['yith_new_shipping_day_prod']['need_process_day'] ) ? $args['yith_new_shipping_day_prod']['need_process_day'] : array();

				$product_day = get_option( 'yith_new_shipping_day_prod', array() );

				if ( isset( $product_day[ $product_id ] ) ) {
					$product_day[ $product_id ]['need_process_day'] = $quantity_days;
					$product_day[ $product_id ]['enabled']          = isset( $args['yith_new_shipping_day_prod']['enabled'] );

				} else {
					$product_day[ $product_id ] = array(
						'product'          => $product_id,
						'need_process_day' => $quantity_days,
						'enabled'          => 'yes',
					);
				}

				update_option( 'yith_new_shipping_day_prod', $product_day );

				if ( isset( $_POST['ywcdd_action'] ) && 'add' === $_POST['ywcdd_action'] ) {
					$type = 'product';
					ob_start();
					include YITH_DELIVERY_DATE_TEMPLATE_PATH . '/admin/custom-processing-day-view.php';
					$template = ob_get_contents();
					ob_end_clean();
					wp_send_json( array( 'template' => $template ) );
				}
			}
		}

		/**
		 * delete process product day
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function delete_product_day() {

			if ( isset( $_POST['ywcdd_product_id'] ) ) {

				$product_id  = $_POST['ywcdd_product_id'];
				$product_day = get_option( 'yith_new_shipping_day_prod', array() );

				if ( isset( $product_day[ $product_id ] ) ) {
					unset( $product_day[ $product_id ] );
					update_option( 'yith_new_shipping_day_prod', $product_day );
				}

				wp_send_json( array( 'removed' => true ) );
			}

		}

		// CALENDAR

		public function show_current_month() {

			if ( isset( $_REQUEST['action'] ) && 'show_current_month' == $_REQUEST['action'] ) {

				$start = isset( $_REQUEST['start_date'] ) ? $_REQUEST['start_date'] : false;
				$end   = isset( $_REQUEST['end_date'] ) ? $_REQUEST['end_date'] : false;

				if ( $start && $end ) {
					$end_timestamp = strtotime( $end );
					$end           = date( 'Y-m-d', strtotime( '-1 days', $end_timestamp ) );

					$events = YITH_Delivery_Date_Calendar()->get_calendar_events( false, $start, $end );

					wp_send_json( $events );

				}
			}
		}

		/**
		 * add new holidays to calendar
		 */
		public function add_holidays() {

			if ( isset( $_REQUEST['action'] ) && 'add_holidays' === $_REQUEST['action'] && isset( $_REQUEST['ywcdd_holidays_option'] ) ) {

				$all_holidays_opts = get_option( 'ywcdd_holidays_option', array() );
				$new_holidays      = $_REQUEST['ywcdd_holidays_option'];

				foreach ( $new_holidays as $new_holiday_id => $new_holiday ) {

					$all_holidays_opts[ $new_holiday_id ] = $new_holiday;

					YITH_Delivery_Date_Calendar()->add_all_holiday_calendar_event( $new_holiday['how_add_holiday'], $new_holiday['event_name'], $new_holiday['start_event'], $new_holiday['end_event'] );
				}

				update_option( 'ywcdd_holidays_option', $all_holidays_opts );

				$title    = $this->show_holiday_toggle_element_title( '', array(), $all_holidays_opts[ $new_holiday_id ] );
				$subtitle = $this->show_holiday_toggle_element_subtitle( '', array(), $all_holidays_opts[ $new_holiday_id ] );

				wp_send_json(
					array(
						'title'    => $title,
						'subtitle' => $subtitle,
					)
				);
			}

		}

		public function delete_holidays() {

			if ( isset( $_REQUEST['action'] ) && 'delete_holidays' == $_REQUEST['action'] ) {

				$item_key          = isset( $_REQUEST['item_key'] ) ? $_REQUEST['item_key'] : false;
				$all_holidays_opts = get_option( 'ywcdd_holidays_option', array() );

				if ( $item_key && isset( $all_holidays_opts[ $item_key ] ) ) {

					$holiday_to_remove = $all_holidays_opts[ $item_key ];
					$old_from          = $holiday_to_remove['start_event'];
					$old_to            = $holiday_to_remove['end_event'];
					$old_holiday_for   = ! empty( $holiday_to_remove['how_add_holiday'] ) ? $holiday_to_remove['how_add_holiday'] : array();

					unset( $all_holidays_opts[ $item_key ] );

					update_option( 'ywcdd_holidays_option', $all_holidays_opts );

					YITH_Delivery_Date_Calendar()->delete_event_by_date( $old_from, $old_to, $old_holiday_for );

					wp_send_json( 'deleted' );
				}
			}
		}

		/**
		 * update holiday
		 * /**
		 *
		 * @author YITH
		 * @since 2.0.0
		 */
		public function update_holidays() {

			if ( isset( $_REQUEST['action'] ) && 'update_holidays' === $_REQUEST['action'] && isset( $_REQUEST['ywcdd_holidays_option'] ) ) {

				$all_holidays_opts = get_option( 'ywcdd_holidays_option', array() );
				$update_holidays   = $_REQUEST['ywcdd_holidays_option'];

				foreach ( $update_holidays as $holiday_id => $holiday ) {

					$old_holiday = isset( $all_holidays_opts[ $holiday_id ] ) ? $all_holidays_opts[ $holiday_id ] : false;

					if ( $old_holiday ) {
						$old_from        = $old_holiday['start_event'];
						$old_to          = $old_holiday['end_event'];
						$old_holiday_for = $old_holiday['how_add_holiday'];

						YITH_Delivery_Date_Calendar()->delete_event_by_date( $old_from, $old_to, $old_holiday_for );
					}

					if ( isset( $holiday['enabled'] ) && 'yes' === $holiday['enabled'] ) {

						$event_name  = $holiday['event_name'];
						$start_event = $holiday['start_event'];
						$end_event   = $holiday['end_event'];
						$holiday_for = $holiday['how_add_holiday'];
						YITH_Delivery_Date_Calendar()->add_all_holiday_calendar_event( $holiday_for, $event_name, $start_event, $end_event );
					} else {
						$holiday['enabled'] = 'no';
					}

					$all_holidays_opts[ $holiday_id ] = $holiday;
				}

				update_option( 'ywcdd_holidays_option', $all_holidays_opts );

				wp_send_json( 'updated' );

			}

		}


		/**
		 * @author YITH
		 * @since 2.0.0
		 * delete a holiday
		 */
		public function delete_calendar_holidays() {

			if ( isset( $_POST['ywcdd_event_id'] ) ) {

				$event_id = $_POST['ywcdd_event_id'];
				$res      = YITH_Delivery_Date_Calendar()->delete_event_by_id( $event_id );

				$result = $res ? 'deleted' : 'error';

				wp_send_json( array( 'result' => $result ) );
			}
		}



		public function show_admin_notices() {

			$messages = array();
			if ( isset( $_GET['page'] ) && 'yith_delivery_date_panel' == $_GET['page'] ) {

				$tot_post = wp_count_posts( 'yith_proc_method' );
				$tot_post = $tot_post->publish;

				$tot_carrier_post = wp_count_posts( 'yith_carrier' );
				$tot_carrier_post = $tot_carrier_post->publish;

				if ( $tot_carrier_post == 0 ) {
					$post_url     = admin_url( 'post-new.php' );
					$params       = array( 'post_type' => 'yith_carrier' );
					$new_post_url = esc_url( add_query_arg( $params, $post_url ) );
					$message      = sprintf(
						'%s <a href="%s" class="page-title-action" style="top:0;font-size:11px;">%s</a>',
						__( 'In order to use the plugin, it is essential to create at least a Carrier', 'yith-woocommerce-delivery-date' ),
						$new_post_url,
						__( 'Add new Carrier', 'yith-woocommerce-delivery-date' )
					);

					$message = array(
						'type'    => 'warning',
						'message' => $message,
						'url'     => '',
					);

					$messages[] = $message;
				}

				if ( $tot_post == 0 ) {
					$post_url     = admin_url( 'post-new.php' );
					$params       = array( 'post_type' => 'yith_proc_method' );
					$new_post_url = esc_url( add_query_arg( $params, $post_url ) );
					$message      = sprintf(
						'%s <a href="%s" class="page-title-action" style="top:0;font-size:11px;">%s</a>',
						__( 'In order to use the plugin, it is essential to create at least a Processing Method', 'yith-woocommerce-delivery-date' ),
						$new_post_url,
						__( 'Add new Processing Method', 'yith-woocommerce-delivery-date' )
					);

					$message = array(
						'type'    => 'warning',
						'message' => $message,
						'url'     => '',
					);

					$messages[] = $message;
				}
			}

			if ( count( $messages ) > 0 ) {

				foreach ( $messages as $message ) {

					wc_get_template(
						'/admin/notices/admin-notice-' . $message['type'] . '.php',
						array(
							'message' => $message['message'],
							'url'     => $message['url'],
						),
						YITH_DELIVERY_DATE_TEMPLATE_PATH,
						YITH_DELIVERY_DATE_TEMPLATE_PATH
					);
				}
			}
		}

		/**
		 * Show the Processing method tab
		 *
		 * @author YITH
		 * @since 2.0.0
		 */
		public function add_processing_method_tab() {

			include_once YITH_DELIVERY_DATE_TEMPLATE_PATH . 'admin/tabs/processing-options-tab.php';
		}

		/**
		 * show the Carrier tab
		 *
		 * @author YITH
		 * @since 2.0.0
		 */
		public function add_carrier_tab() {
			include_once YITH_DELIVERY_DATE_TEMPLATE_PATH . 'admin/tabs/carrier-tab.php';
		}



		/**
		 * @param string  $class
		 * @param WP_Post $post
		 *
		 * @return string
		 */
		public function add_custom_metabox_class( $class, $post ) {

			$allow_post_types = array(
				'yith_carrier',
				'yith_proc_method',
				'yith_product_table',
			);

			if ( $post && in_array( $post->post_type, $allow_post_types ) ) {
				$class .= ' ' . yith_set_wrapper_class();
			}

			return $class;
		}

		/**
		 * @param array $classes
		 * @param array $field
		 *
		 * @return array
		 */
		public function add_extra_classes( $classes, $field ) {

			if ( 'ywcdd_ddm_where_show_delivery_message' == $field['id'] ) {

				$show_shipping_date = get_option( 'ywcdd_ddm_enable_shipping_message', 'no' );
				$show_delivery_date = get_option( 'ywcdd_ddm_enable_delivery_message', 'no' );

				if ( 'no' === $show_delivery_date && 'no' === $show_shipping_date ) {
					$classes[] = 'yith-disabled';
				}
			}

			if ( 'yith_new_shipping_day_prod' == $field['id'] || 'yith_new_shipping_day_cat' == $field['id'] ) {
				$option = get_option( 'ywcdd_processing_type', 'checkout' );

				if ( 'product' == $option ) {
					$classes[] = 'yith-disabled';
				}
			}

			return $classes;
		}


		/**
		 *
		 * return the formatted toggle title for the  holidays
		 *
		 * @param string $formatted_title
		 * @param array  $elements
		 * @param array  $value
		 *
		 * @return string
		 */
		public function show_holiday_toggle_element_title( $formatted_title, $elements, $value ) {

			$holiday_name = $value['event_name'] . ' ' . _x( 'for', 'Part of: My holiday for Processing Method, DHL', 'yith-woocommerce-delivery-date' );
			$holiday_for  = array();
			if ( isset( $value['how_add_holiday'] ) && is_array( $value['how_add_holiday'] ) ) {
				foreach ( $value['how_add_holiday'] as $single_holiday_id ) {
					$holiday_for[] = get_the_title( $single_holiday_id );
				}
			}
			$holiday_for_label = implode( ', ', $holiday_for );
			$holiday_name     .= ' ' . $holiday_for_label;

			$original_title = $holiday_name;

			return $original_title;
		}

		/**
		 * return the formatted toggle subtitle for the holidays
		 *
		 * @param string $formatted_subtitle
		 * @param array  $elements
		 * @param array  $value
		 *
		 * @return string
		 */
		public function show_holiday_toggle_element_subtitle( $formatted_subtitle, $elements, $value ) {

			$sub_holiday_name  = _x( 'From', 'Part of: From 2019/08/15 - To 2019/08/20', 'yith-woocommerce-delivery-date' );
			$sub_holiday_name .= ' <span class="ywcdd_datefrom">' . $value['start_event'] . '</span> ';
			$sub_holiday_name .= _x( 'To', 'Part of: From 2019/08/15 - To 2019/08/20', 'yith-woocommerce-delivery-date' );
			$sub_holiday_name .= ' <span class=ywcdd_dateto>' . $value['end_event'] . '</span>';

			return $sub_holiday_name;
		}

		/**
		 * return the formatted title for the custom processing product day
		 *
		 * @param $formatted_title
		 * @param $elements
		 * @param $value
		 *
		 * @return string
		 */
		public function show_processing_product_toggle_element_title( $formatted_title, $elements, $value ) {
			$product = wc_get_product( $value['product'] );
			$title   = '';

			if ( $product ) {
				$title = $product->get_formatted_name();
			}

			return $title;
		}

		/**return the formatted title for the custom processing product category day
		 *
		 * @param $formatted_title
		 * @param $elements
		 * @param $value
		 *
		 * @return string
		 */
		public function show_processing_product_category_toggle_element_title( $formatted_title, $elements, $value ) {

			$category = get_term( $value['category'], 'product_cat' );

			if ( $category instanceof WP_Term ) {
				$formatted_title = $category->name;
			}
			return $formatted_title;
		}

		public function update_custom_processing_title() {

			if ( isset( $_REQUEST['field_selected'] ) ) {

				$type    = $_REQUEST['custom_type_title'];
				$title   = '';
				$post_id = $_REQUEST['field_selected'];
				if ( 'category' == $type ) {

					$title = $this->show_processing_product_category_toggle_element_title( $title, array(), array( 'category' => $post_id ) );
				} else {
					$title = $this->show_processing_product_toggle_element_title( $title, array(), array( 'product' => $post_id ) );
				}

				wp_send_json( array( 'title' => $title ) );
			}
		}


	}
}
/**
 * @return YITH_Delivery_Date_Admin
 */
function YITH_Delivery_Date_Admin() {
	return YITH_Delivery_Date_Admin::get_instance();
}

YITH_Delivery_Date_Admin();
