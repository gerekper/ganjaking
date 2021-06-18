<?php
/**
 * Admin Settings
 *
 * @package WC_OD/Admin
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Admin_Settings' ) ) {
	/**
	 * WC_OD_Admin_Settings Class
	 */
	class WC_OD_Admin_Settings {

		/**
		 * The settings API instance.
		 *
		 * @var WC_OD_Settings_API
		 */
		protected $settings_api;

		/**
		 * The setting errors registered during validation.
		 *
		 * @since 1.0.0
		 *
		 * @var array The setting errors.
		 */
		protected $errors = array();


		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 *
		 * @global string $current_tab The current tab.
		 * @global string $current_section The current section.
		 */
		public function __construct() {
			global $current_tab, $current_section;

			// The $current_tab and $current_section global variables are initialized after.
			if ( null === $current_tab ) {
				$current_tab = wc_od_get_query_arg( 'tab' );
				if ( ! $current_tab ) {
					$current_tab = 'general';
				}
			}

			if ( null === $current_section ) {
				$current_section = wc_od_get_query_arg( 'section' );
			}

			$this->includes();
			$this->init_settings_api();

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );

			// WooCommerce settings menu.
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'rename_shipping_tab' ), 25 );
			add_filter( 'woocommerce_get_sections_shipping', array( $this, 'add_shipping_sections' ) );

			// WooCommerce settings hooks.
			add_filter( 'woocommerce_get_settings_shipping', array( $this, 'add_shipping_settings' ), 10, 2 );
			add_action( 'woocommerce_settings_shipping', array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_shipping', array( $this, 'save' ) );

			// Custom fields display.
			add_action( 'woocommerce_admin_field_wc_od_table', 'wc_od_field_wrapper' );
			add_action( 'woocommerce_admin_field_wc_od_shipping_days', 'wc_od_field_wrapper' );
			add_action( 'woocommerce_admin_field_wc_od_delivery_days', 'wc_od_field_wrapper' );
			add_action( 'woocommerce_admin_field_wc_od_day_range', 'wc_od_field_wrapper' );
			add_action( 'woocommerce_admin_field_wc_od_calendar', 'wc_od_calendar_field' );

			// Custom fields sanitize.
			add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'sanitize_field' ), 10, 2 );
		}

		/**
		 * Gets if we are in the shipping tab of the settings page.
		 *
		 * @since 1.0.0
		 *
		 * @global string $current_tab The current tab.
		 *
		 * @return boolean True if the current setting tab is 'shipping'. False otherwise.
		 */
		public function is_shipping_tab() {
			global $current_tab;

			return ( 'shipping' === $current_tab );
		}

		/**
		 * Gets if the current settings section has a calendar.
		 *
		 * @since 1.0.0
		 *
		 * @global string $current_section The current section.
		 *
		 * @return boolean True if the current section has a calendar. False otherwise.
		 */
		public function is_calendar_section() {
			global $current_section;

			return ( 'shipping_calendar' === $current_section || 'delivery_calendar' === $current_section );
		}

		/**
		 * Includes the necessary files.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			if ( $this->is_shipping_tab() ) {
				if ( $this->is_calendar_section() ) {
					include_once 'wc-od-admin-calendars-settings.php';
				} else {
					include_once 'wc-od-admin-general-settings.php';
				}

				/**
				 * Includes the necessary files.
				 *
				 * @since 1.0.0
				 */
				do_action( 'wc_od_settings_includes' );
			}
		}

		/**
		 * Initialize the settings API.
		 *
		 * @since 1.5.0
		 *
		 * @global string $current_section The current section.
		 */
		public function init_settings_api() {
			global $current_section;

			if ( ! $this->is_shipping_tab() ) {
				return;
			}

			// phpcs:disable WordPress.Security.NonceVerification
			if ( 'delivery_range' === $current_section ) {
				$range_id = ( isset( $_GET['range_id'] ) ? wc_clean( wp_unslash( $_GET['range_id'] ) ) : 'new' );
				$range_id = ( 'new' === $range_id ? null : (int) $range_id );

				$delivery_range     = WC_OD_Delivery_Ranges::get_range( $range_id );
				$this->settings_api = new WC_OD_Settings_Delivery_Range( $delivery_range );
				return;
			}

			$day_id = false;

			if ( isset( $_GET['day_id'] ) ) {
				$day_id = (int) wc_clean( wp_unslash( $_GET['day_id'] ) );
				$day_id = ( $day_id >= 0 && $day_id <= 6 ? $day_id : false );
			}

			if ( 'delivery_day' === $current_section && false !== $day_id ) {
				$this->settings_api = new WC_OD_Settings_Delivery_Day( $day_id );
			} elseif ( 'time_frame' === $current_section ) {
				if ( isset( $_GET['frame_id'] ) && false !== $day_id ) {
					$frame_id = (string) wc_clean( wp_unslash( $_GET['frame_id'] ) );

					$this->settings_api = new WC_OD_Settings_Delivery_Day_Time_Frame( $day_id, $frame_id );
				} else {
					$this->settings_api = new WC_OD_Settings_Delivery_Days_Time_Frame();
				}
			}
			// phpcs:enable WordPress.Security.NonceVerification
		}

		/**
		 * Enqueues the settings scripts.
		 *
		 * @since 1.0.0
		 *
		 * @global string $current_section The current section.
		 */
		public function enqueue_scripts() {
			global $current_section;

			if ( ! $this->is_shipping_tab() ) {
				return;
			}

			$suffix = wc_od_get_scripts_suffix();

			if ( $this->is_calendar_section() ) {
				wp_enqueue_style( 'fullcalendar', WC_OD_URL . 'assets/css/lib/fullcalendar.css', array(), '2.9.3' );
				wp_enqueue_style( 'tooltipster', WC_OD_URL . 'assets/css/lib/tooltipster.css', array(), '3.3.0' );

				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script( 'tooltipster', WC_OD_URL . 'assets/js/lib/jquery.tooltipster.min.js', array( 'jquery' ), '3.3.0', true );
				wp_enqueue_script( 'moment', WC_OD_URL . 'assets/js/lib/moment.min.js', array(), '2.13.0', true );
				wp_enqueue_script( 'fullcalendar', WC_OD_URL . 'assets/js/lib/fullcalendar.min.js', array( 'jquery', 'moment' ), '2.9.3', true );

				wc_od_enqueue_datepicker( 'settings' );
				wp_enqueue_script( 'wc-od-calendar', WC_OD_URL . "assets/js/wc-od-calendar{$suffix}.js", array( 'jquery', 'wc-od-datepicker' ), WC_OD_VERSION, true );
			} elseif ( in_array( $current_section, array( 'options', 'delivery_day' ), true ) ) {
				wp_enqueue_script( 'wc-od-table-fields', WC_OD_URL . "assets/js/admin/table-fields{$suffix}.js", array( 'jquery' ), WC_OD_VERSION, true );
			}

			wp_enqueue_style( 'jquery-timepicker', WC_OD_URL . 'assets/css/lib/jquery.timepicker.css', array(), '1.13.18' );
			wp_enqueue_script( 'jquery-timepicker', WC_OD_URL . 'assets/js/lib/jquery.timepicker.min.js', array( 'jquery' ), '1.13.18', true );

			wp_enqueue_style( 'wc-od-settings', WC_OD_URL . 'assets/css/wc-od-settings.css', array(), WC_OD_VERSION );
			wp_enqueue_script( 'wc-od-settings', WC_OD_URL . "assets/js/wc-od-settings{$suffix}.js", array( 'jquery' ), WC_OD_VERSION, true );
			wp_localize_script( 'wc-od-settings', 'wc_od_settings_l10n', $this->localize_settings_script() );
		}

		/**
		 * Adds localization to the wc-od-settings.js script.
		 *
		 * @since 1.0.0
		 *
		 * @global string $current_section The current section.
		 *
		 * @return array The localized parameters.
		 */
		public function localize_settings_script() {
			global $current_section;

			$wc_od_settings_l10n = array();
			if ( $this->is_calendar_section() ) {
				// Gets the events type.
				$event_type = str_replace( '_calendar', '', $current_section );
				if ( ! $event_type ) {
					$event_type = 'shipping';
				}

				// Shipping events use the defaults callables.
				$callable_type = ( 'shipping' === $event_type ? 'event' : $event_type );

				// Defines the localization parameters.
				$wc_od_settings_l10n = array(
					'language'            => get_bloginfo( 'language' ),
					'weekStart'           => get_option( 'start_of_week', 0 ),
					'eventsType'          => $event_type,
					'modalContent'        => call_user_func( "wc_od_{$callable_type}_modal_content" ),
					'eventTooltipContent' => call_user_func( "wc_od_{$callable_type}_tooltip_content" ),
					'modalTexts'          => array(
						'add'    => __( 'Add event', 'woocommerce-order-delivery' ),
						'edit'   => __( 'Edit event', 'woocommerce-order-delivery' ),
						'delete' => __( 'Are you sure you want to delete this event?', 'woocommerce-order-delivery' ),
					),
				);

				if ( 'delivery' === $event_type ) {
					$wc_od_settings_l10n['countryStates'] = wp_json_encode( wc_od_get_country_states_for_select2() );
				}
			}

			/**
			 * Filters the localized parameters for the wc-od-settings.js script.
			 *
			 * @since 1.0.0
			 *
			 * @param array $wc_od_settings_l10n The default parameters to filter.
			 */
			return apply_filters( 'wc_od_settings_l10n', $wc_od_settings_l10n );
		}

		/**
		 * Renames the shipping tab.
		 *
		 * @since 1.0.0
		 *
		 * @param array $tabs The WooCommerce settings tabs.
		 * @return array The filtered WooCommerce settings tabs.
		 */
		public function rename_shipping_tab( $tabs ) {
			$tabs['shipping'] = __( 'Shipping & Delivery', 'woocommerce-order-delivery' );

			return $tabs;
		}

		/**
		 * Adds sections to the shipping tab.
		 *
		 * @since 1.0.0
		 *
		 * @param array $sections The shipping sections.
		 * @return array The filtered shipping sections.
		 */
		public function add_shipping_sections( $sections ) {
			return array_merge(
				array_slice( $sections, 0, 3 ),
				array(
					'shipping_calendar' => __( 'Shipping Calendar', 'woocommerce-order-delivery' ),
					'delivery_calendar' => __( 'Delivery Calendar', 'woocommerce-order-delivery' ),
				),
				array_slice( $sections, 3 )
			);
		}

		/**
		 * Adds the shipping settings.
		 *
		 * @since 1.0.0
		 * @since 1.5.6 Added `$section` parameter.
		 *
		 * @global string $current_section The current section.
		 *
		 * @param array  $settings The shipping settings.
		 * @param string $section  Optional. The settings section.
		 * @return array The shipping settings.
		 */
		public function add_shipping_settings( $settings, $section = null ) {
			global $current_section;

			// Clear the default shipping settings.
			if ( $this->settings_api instanceof WC_OD_Settings_API ) {
				return array();
			}

			/*
			 * The parameter `$section` is not available in WC 2.6.
			 * In addition, if it's an empty string, it refers to the 'options' section.
			 */
			if ( ! $section ) {
				$section = $current_section;
			}

			// WC 3.6 adds the 'options' settings by default for the sections that aren't shipping methods.
			if ( in_array( $section, array( 'shipping_calendar', 'delivery_calendar' ), true ) ) {
				$settings = $this->get_settings( $section );
			} else {
				$settings = array_merge( $settings, $this->get_settings( $section ) );
			}

			return $settings;
		}

		/**
		 * Gets settings array.
		 *
		 * @since 1.0.0
		 *
		 * @param string $current_section The current section.
		 * @return array An array with the settings.
		 */
		public function get_settings( $current_section = '' ) {
			$settings = array();
			$section  = ( 'options' === $current_section ? 'shipping' : $current_section );

			if ( 'shipping' === $section ) {

				$settings = array(

					array(
						'id'   => 'shipping_options_extended',
						'type' => 'title',
					),

					array(
						'id'                => wc_od_maybe_prefix( 'min_working_days' ),
						'title'             => __( 'Minimum working days', 'woocommerce-order-delivery' ),
						'desc'              => __( 'The minimum number of days it takes you to process an order.', 'woocommerce-order-delivery' ),
						'type'              => 'number',
						'default'           => WC_OD()->settings()->get_default( 'min_working_days' ),
						'css'               => 'width:50px;',
						'desc_tip'          => true,
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
						),
					),

					array(
						'id'       => wc_od_maybe_prefix( 'shipping_days' ),
						'title'    => __( 'Shipping days', 'woocommerce-order-delivery' ),
						'desc'     => __( 'Choose the shipping days and their time limit to ship orders. You can set the time limit to process an order on the same day.', 'woocommerce-order-delivery' ),
						'type'     => 'wc_od_shipping_days',
						'value'    => WC_OD()->settings()->get_setting( 'shipping_days' ),
						'desc_tip' => true,
					),

					array(
						'id'   => 'shipping_options_extended',
						'type' => 'sectionend',
					),

					array(
						'id'    => 'delivery_options',
						'title' => __( 'Delivery Options', 'woocommerce-order-delivery' ),
						'type'  => 'title',
					),

					array(
						'id'       => wc_od_maybe_prefix( 'delivery_ranges' ),
						'title'    => __( 'Delivery ranges', 'woocommerce-order-delivery' ),
						'desc'     => __( 'Define different delivery ranges.', 'woocommerce-order-delivery' ),
						'type'     => 'wc_od_table',
						'value'    => WC_OD()->settings()->get_setting( 'delivery_ranges' ),
						'desc_tip' => true,
					),

					array(
						'id'       => wc_od_maybe_prefix( 'checkout_location' ),
						'title'    => __( 'Checkout location', 'woocommerce-order-delivery' ),
						'desc'     => __( 'Choose the location in the checkout form where to display the delivery details.', 'woocommerce-order-delivery' ),
						'type'     => 'select',
						'desc_tip' => true,
						'default'  => WC_OD()->settings()->get_default( 'checkout_location' ),
						'options'  => array(
							'before_customer_details' => __( 'Before customer details', 'woocommerce-order-delivery' ),
							'before_billing'          => __( 'Before billing details', 'woocommerce-order-delivery' ),
							'after_billing'           => __( 'After billing details', 'woocommerce-order-delivery' ),
							'before_order_notes'      => __( 'Before order notes', 'woocommerce-order-delivery' ),
							'after_order_notes'       => __( 'After order notes', 'woocommerce-order-delivery' ),
							'after_additional_fields' => __( 'After additional fields', 'woocommerce-order-delivery' ),
							'after_order_review'      => __( 'Between order review and payments', 'woocommerce-order-delivery' ),
							'after_customer_details'  => __( 'After customer details', 'woocommerce-order-delivery' ),
						),
					),

					array(
						'id'       => wc_od_maybe_prefix( 'checkout_delivery_option' ),
						'title'    => __( 'Checkout options', 'woocommerce-order-delivery' ),
						'desc'     => __( 'Choose what kind of content to display in the checkout form.', 'woocommerce-order-delivery' ),
						'type'     => 'radio',
						'desc_tip' => true,
						'default'  => WC_OD()->settings()->get_default( 'checkout_delivery_option' ),
						'options'  => array(
							'text'     => __( 'A text block with information about shipping and delivery', 'woocommerce-order-delivery' ),
							'calendar' => __( 'A calendar to let the customer choose a delivery date', 'woocommerce-order-delivery' ),
						),
					),

					array(
						'id'       => wc_od_maybe_prefix( 'delivery_days' ),
						'title'    => __( 'Delivery days', 'woocommerce-order-delivery' ),
						'desc'     => __( 'Choose the available days to deliver orders.', 'woocommerce-order-delivery' ),
						'type'     => 'wc_od_delivery_days',
						'value'    => WC_OD()->settings()->get_setting( 'delivery_days' ),
						'desc_tip' => true,
					),

					array(
						'id'                => wc_od_maybe_prefix( 'max_delivery_days' ),
						'title'             => __( 'Maximum delivery range', 'woocommerce-order-delivery' ),
						'desc'              => __( 'Maximum days that the customer can choose from, starting on the current date, to receive the order.', 'woocommerce-order-delivery' ),
						'type'              => 'number',
						'default'           => WC_OD()->settings()->get_default( 'max_delivery_days' ),
						'css'               => 'width:50px;',
						'desc_tip'          => true,
						'custom_attributes' => array(
							'min'  => 0,
							'step' => 1,
						),
					),

					array(
						'id'       => wc_od_maybe_prefix( 'delivery_fields_option' ),
						'title'    => __( 'Delivery fields', 'woocommerce-order-delivery' ),
						'desc'     => __( 'Choose the option to determine the behavior of the delivery fields in the checkout form.', 'woocommerce-order-delivery' ),
						'type'     => 'radio',
						'desc_tip' => true,
						'default'  => WC_OD()->settings()->get_default( 'delivery_fields_option' ),
						'options'  => array(
							'optional' => __( 'The fields are optional', 'woocommerce-order-delivery' ),
							'auto'     => __( 'The fields values will be assigned automatically if the customer leave them empty', 'woocommerce-order-delivery' ),
							'required' => __( 'The fields are required', 'woocommerce-order-delivery' ),
						),
					),

					array(
						'id'       => wc_od_maybe_prefix( 'enable_local_pickup' ),
						'title'    => __( 'Enable for Local Pickup', 'woocommerce-order-delivery' ),
						'desc'     => __( "Display the 'Shipping & Delivery' section on the checkout page when the shipping method is 'Local Pickup'", 'woocommerce-order-delivery' ),
						'desc_tip' => __( 'Recommended if you only provide local pickup and you want to use the delivery calendar for the pickup date.', 'woocommerce-order-delivery' ),
						'type'     => 'checkbox',
						'default'  => WC_OD()->settings()->get_default( 'enable_local_pickup' ),
					),

					array(
						'id'   => 'delivery_options',
						'type' => 'sectionend',
					),

				);

			} elseif ( 'shipping_calendar' === $current_section ) {

				$settings = array(

					array(
						'id'    => 'shipping_calendar',
						'title' => __( 'Shipping Calendar', 'woocommerce-order-delivery' ),
						'type'  => 'title',
						'desc'  => __( 'This calendar is used to overwrite the default <em>Shipping days</em> setting. Use it for example to define your non working days or holidays periods.', 'woocommerce-order-delivery' ),
					),

					array(
						'id'    => wc_od_maybe_prefix( 'shipping_events' ),
						'type'  => 'wc_od_calendar',
						'value' => WC_OD()->settings()->get_setting( 'shipping_events' ),
					),

					array(
						'id'   => 'shipping_calendar',
						'type' => 'sectionend',
					),

				);

			} elseif ( 'delivery_calendar' === $current_section ) {

				$settings = array(

					array(
						'id'    => 'delivery_calendar',
						'title' => __( 'Delivery Calendar', 'woocommerce-order-delivery' ),
						'type'  => 'title',
						'desc'  => __( 'This calendar is used to overwrite the default <em>Delivery days</em> setting. Use it to disable specific delivery days.', 'woocommerce-order-delivery' ),
					),

					array(
						'id'    => wc_od_maybe_prefix( 'delivery_events' ),
						'type'  => 'wc_od_calendar',
						'value' => WC_OD()->settings()->get_setting( 'delivery_events' ),
					),

					array(
						'id'   => 'delivery_calendar',
						'type' => 'sectionend',
					),

				);

			}

			/**
			 * Filters the settings.
			 *
			 * The dynamic portion of the hook name, $section, refers to the $section name.
			 *
			 * @since 1.0.0
			 *
			 * @param array $settings The settings.
			 */
			return apply_filters( "wc_od_{$section}_settings", $settings );
		}

		/**
		 * Outputs the settings.
		 *
		 * @since 1.0.0
		 *
		 * @global string $current_section  The current section.
		 * @global bool   $hide_save_button Hide the save button or not.
		 */
		public function output() {
			global $current_section, $hide_save_button;

			if ( $this->settings_api instanceof WC_OD_Settings_API ) {
				$this->settings_api->admin_options();
			} elseif ( $this->is_calendar_section() ) {
				// Hide the save button for the calendar sections.
				$hide_save_button = true;

				if ( version_compare( WC()->version, '3.6', '<' ) ) {
					woocommerce_admin_fields( $this->get_settings( $current_section ) );
				}
			}
		}

		/**
		 * Sanitize the setting.
		 *
		 * @since 1.0.5
		 *
		 * @param mixed $value   The setting value.
		 * @param array $setting The setting data.
		 * @return mixed The sanitized value.
		 */
		public function sanitize_field( $value, $setting ) {
			$setting_type = wc_od_no_prefix( $setting['type'] );

			switch ( $setting_type ) {
				case 'shipping_days':
				case 'delivery_days':
					$setting_id      = wc_od_no_prefix( $setting['id'] );
					$previous_value  = WC_OD()->settings()->get_setting( $setting_id );
					$days_data       = is_array( $value ) ? $value : array();
					$clean_days_data = array();

					foreach ( $previous_value as $key => $data ) {
						$day_data = ( isset( $days_data[ $key ] ) ? $days_data[ $key ] : array() );

						$clean_day_data = array(
							'enabled' => wc_bool_to_string( ( isset( $day_data['enabled'] ) && $day_data['enabled'] ) ),
						);

						if ( 'shipping_days' === $setting_id ) {
							$time = ( ( isset( $day_data['time'] ) && $day_data['time'] ) ? $day_data['time'] : '' );

							$clean_day_data['time'] = wc_od_sanitize_time( $time );
						}

						$clean_days_data[ $key ] = array_merge( $data, $clean_day_data );
					}

					$enabled_days = wc_od_get_days_by( $clean_days_data, 'enabled', 'yes' );

					if ( empty( $enabled_days ) ) {
						$error_key = ( 'shipping_days' === $setting_id ? 'shipping_days_empty' : 'delivery_days_empty' );
						$this->add_setting_error( $error_key );
					} else {
						$value = $clean_days_data;
					}
					break;
				case 'day_range':
					if ( null === $value ) {
						$value = array(
							'min' => 0,
							'max' => 0,
						);
					} else {
						$value = array(
							'min' => ( isset( $value['min'] ) ? absint( $value['min'] ) : 0 ),
							'max' => ( isset( $value['max'] ) ? absint( $value['max'] ) : 0 ),
						);
					}
					break;
				case 'table':
					$instance = wc_od_get_table_field( $setting );

					if ( $instance ) {
						$value = $instance->sanitize_field( $value );
					}
					break;
			}

			// The field contains errors. Return null to bypass the save.
			if ( ! empty( $this->errors ) ) {
				return null;
			}

			return $value;
		}

		/**
		 * Save the settings.
		 *
		 * @since 1.5.0
		 */
		public function save() {
			if ( ! $this->settings_api instanceof WC_OD_Settings_API ) {
				return;
			}

			$saved = $this->settings_api->process_admin_options();

			if ( $saved ) {
				$this->settings_api->maybe_redirect();
			}
		}

		/**
		 * Validate and save the setting.
		 *
		 * Backward compatibility with WC 2.3 and older.
		 *
		 * @since 1.0.0
		 * @deprecated 1.1.0 We sanitize the field in the 'sanitize_field' method instead of save it directly.
		 *
		 * @param array $setting The setting data.
		 */
		public function save_field( $setting ) {
			wc_deprecated_function( __METHOD__, '1.1.0' );
		}

		/**
		 * Gets a setting error message by key.
		 *
		 * @since 1.0.0
		 *
		 * @param string $error_key Optional. The error key.
		 * @return string The error message, or an empty string if the key doesn't exists.
		 */
		public function get_setting_error_message( $error_key = '' ) {
			/**
			 * Filters the error messages.
			 *
			 * @since 1.0.0
			 *
			 * @param array $error_messages The error messages.
			 */
			$error_messages = apply_filters( 'wc_od_settings_error_messages', array(
				'shipping_days_empty' => __( 'You must check at least one shipping day.', 'woocommerce-order-delivery' ),
				'delivery_days_empty' => __( 'You must check at least one delivery day.', 'woocommerce-order-delivery' ),
			) );

			if ( $error_key ) {
				return ( isset( $error_messages[ $error_key ] ) ? $error_messages[ $error_key ] : '' );
			}

			return $error_messages;
		}

		/**
		 * Adds a setting error.
		 *
		 * @since 1.0.0
		 *
		 * @param string $error_key The error key.
		 */
		public function add_setting_error( $error_key ) {
			$error_message = $this->get_setting_error_message( $error_key );
			if ( $error_message ) {
				// Store the error key.
				$this->errors[] = $error_key;

				WC_Admin_Settings::add_error( $error_message );
			}
		}

	}
}

new WC_OD_Admin_Settings();
