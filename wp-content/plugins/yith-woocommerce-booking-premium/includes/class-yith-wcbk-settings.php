<?php
/**
 * Class YITH_WCBK_Settings
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Settings' ) ) {
	/**
	 * Class YITH_WCBK_Settings
	 */
	class YITH_WCBK_Settings {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Settings constructor.
		 */
		protected function __construct() {
			if ( is_admin() ) {
				add_action( 'init', array( $this, 'save_settings' ) );
			}
		}

		/**
		 * Save Settings
		 */
		public function save_settings() {
			if ( isset( $_POST['yith-wcbk-cache-check-for-transient-creation'] ) ) {
				if ( isset( $_POST['yith-wcbk-cache-enabled'] ) && 'yes' === $_POST['yith-wcbk-cache-enabled'] ) {
					delete_transient( 'yith-wcbk-cache-disabled' );
				} else {
					set_transient( 'yith-wcbk-cache-disabled', '1', DAY_IN_SECONDS );
				}
			}

			if ( empty( $_POST['yith_wcbk_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yith_wcbk_nonce'] ) ), 'yith_wcbk_settings_fields' ) ) {
				return;
			}

			$page = sanitize_title( wp_unslash( $_REQUEST['yith-wcbk-settings-page'] ?? '' ) );

			if ( 'global-price-rules' === $page ) {
				$ranges = isset( $_POST['yith_booking_global_cost_ranges'] ) ? wc_clean( wp_unslash( $_POST['yith_booking_global_cost_ranges'] ) ) : array();
				$ranges = ! ! $ranges ? $ranges : array();
				update_option( 'yith_wcbk_booking_global_cost_ranges', $ranges );
				yith_wcbk_sync_booking_product_prices();
			}
		}

		/**
		 * Retrieve the global availability rules.
		 *
		 * @return YITH_WCBK_Availability_Rule[]
		 * @deprecated since 2.1 | use YITH_WCBK_Settings::get_global_availability_rules() instead
		 */
		public function get_global_availability_ranges() {
			return $this->get_global_availability_rules();
		}

		/**
		 * Retrieves the global availability range array
		 *
		 * @param array $args Arguments.
		 *
		 * @return YITH_WCBK_Availability_Rule[]
		 * @since 2.1
		 */
		public function get_global_availability_rules( $args = array() ) {
			$deprecated_availability_rules = get_option( 'yith_wcbk_booking_global_availability_ranges', null );
			if ( version_compare( YITH_WCBK_Install::get_db_version(), '5.0.0', '<' ) && ! is_null( $deprecated_availability_rules ) ) {
				$deprecated_availability_rules = ! ! $deprecated_availability_rules && is_array( $deprecated_availability_rules ) ? $deprecated_availability_rules : array();

				return array_map( 'yith_wcbk_availability_rule', $deprecated_availability_rules );
			}

			$defaults = array(
				'product_id' => false,
			);
			$args     = wp_parse_args( $args, $defaults );

			$rules_args = array(
				'items_per_page' => - 1,
				'order'          => 'ASC',
				'order_by'       => 'priority',
				'return'         => 'objects',
				'paginate'       => false,
				'enabled'        => true,
				'product_id'     => $args['product_id'],
			);

			$rules = yith_wcbk_get_global_availability_rules( $rules_args );

			$rules = array_map(
				function ( $rule ) {
					return $rule->get_availability_rule();
				},
				$rules
			);

			return ! ! $rules && is_array( $rules ) ? $rules : array();
		}


		/**
		 * Retrieves the global cost range array
		 *
		 * @return array the array of ranges as StdClass
		 * @deprecated since 2.1 | use YITH_WCBK_Settings::get_global_availability_rules() instead
		 */
		public function get_global_cost_ranges() {
			return $this->get_global_price_rules();
		}

		/**
		 * Retrieves the global price rules
		 *
		 * @param array $args Arguments.
		 *
		 * @return YITH_WCBK_Price_Rule[]
		 * @since 2.1
		 */
		public function get_global_price_rules( $args = array() ) {
			$deprecated_availability_rules = get_option( 'yith_wcbk_booking_global_cost_ranges', null );
			if ( version_compare( YITH_WCBK_Install::get_db_version(), '5.0.0', '<' ) && ! is_null( $deprecated_availability_rules ) ) {
				$deprecated_availability_rules = ! ! $deprecated_availability_rules && is_array( $deprecated_availability_rules ) ? $deprecated_availability_rules : array();

				return array_map( 'yith_wcbk_price_rule', $deprecated_availability_rules );
			}

			$defaults = array(
				'product_id' => false,
			);
			$args     = wp_parse_args( $args, $defaults );

			$rules_args = array(
				'items_per_page' => - 1,
				'order'          => 'ASC',
				'order_by'       => 'priority',
				'return'         => 'objects',
				'paginate'       => false,
				'enabled'        => true,
				'product_id'     => $args['product_id'],
			);

			$rules = yith_wcbk_get_global_price_rules( $rules_args );

			$rules = array_map(
				function ( $rule ) {
					return $rule->get_price_rule();
				},
				$rules
			);

			return ! ! $rules && is_array( $rules ) ? $rules : array();
		}

		/**
		 * Get settings related to Booking plugin
		 *
		 * @param string $key     The key.
		 * @param mixed  $default The default value.
		 *
		 * @return mixed
		 */
		public function get( $key, $default = false ) {
			return get_option( 'yith-wcbk-' . $key, $default );
		}

		/**
		 * Check if an option exists.
		 *
		 * @param string $key The key.
		 *
		 * @return bool
		 * @since 3.0.0
		 */
		public function has_option( $key ) {
			return ! is_null( $this->get( $key, null ) );
		}

		/**
		 * Check if the option need a backward compatibility.
		 *
		 * @param string $old_option Old option name.
		 * @param string $new_option New option name.
		 * @param string $db_version DB version of the new option.
		 *
		 * @return bool
		 * @since 3.0.0
		 */
		private function need_backward_compatibility( $old_option, $new_option, $db_version ) {
			return version_compare( YITH_WCBK_Install::get_db_version(), $db_version, '<' ) && ! $this->has_option( $new_option ) && $this->has_option( $old_option );
		}

		/**
		 * Return true if showing booking form requires login
		 *
		 * @return bool
		 * @since      1.0.5
		 */
		public function show_booking_form_to_logged_users_only() {
			return 'logged-in' === $this->get_show_booking_form_to();
		}

		/**
		 * Return true if check min max duration in calendar is enabled
		 *
		 * @return bool
		 * @since 2.0.3
		 */
		public function check_min_max_duration_in_calendar() {
			return $this->get( 'check-min-max-duration-in-calendar', 'yes' ) === 'yes';
		}

		/**
		 * Return true if people selector is enabled
		 *
		 * @return bool
		 */
		public function is_people_selector_enabled() {
			return $this->get( 'people-selector-enabled', 'yes' ) === 'yes';
		}

		/**
		 * Return true if unique calendar range picker is enabled
		 *
		 * @return bool
		 */
		public function is_unique_calendar_range_picker_enabled() {
			return 'unique' === $this->get_date_range_picker_layout();
		}

		/**
		 * Return true if date-picker is displayed inline
		 *
		 * @return bool
		 */
		public function display_date_picker_inline() {
			return 'inline' === $this->get_calendar_style();
		}

		/**
		 * Return true if show included services is enabled
		 *
		 * @return bool
		 */
		public function show_included_services() {
			return $this->get( 'show-included-services', 'yes' ) === 'yes';
		}

		/**
		 * Return true if show totals is enabled
		 *
		 * @return bool
		 */
		public function show_totals() {
			return $this->get( 'show-totals', 'no' ) === 'yes';
		}

		/**
		 * Return the number of months to show in calendar
		 *
		 * @return int
		 */
		public function get_months_loaded_in_calendar() {
			$months = absint( $this->get( 'months-loaded-in-calendar', 3 ) );
			$months = min( 12, max( 1, $months ) );

			return $months;
		}

		/**
		 * Return true if cache is enabled
		 *
		 * @return bool
		 * @since 2.0.5
		 */
		public function is_cache_enabled() {
			return apply_filters( 'yith_wcbk_is_cache_enabled', ! get_transient( 'yith-wcbk-cache-disabled' ) );
		}

		/**
		 * Return date picker format
		 *
		 * @return bool
		 * @since 2.1.4
		 */
		public function get_date_picker_format() {
			return $this->get( 'date-picker-format', 'yy-mm-dd' );
		}

		/**
		 * Return booking categories to show.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_booking_categories_to_show() {
			if ( $this->need_backward_compatibility( 'booking-categories', 'booking-categories-to-show', '3.0.0' ) ) {
				return ! ! $this->get_booking_categories() ? 'specific' : 'all';
			}

			return $this->get( 'booking-categories-to-show', 'all' );
		}

		/**
		 * Return booking categories.
		 *
		 * @return int[]
		 * @since 3.0.0
		 */
		public function get_booking_categories() {
			return $this->get( 'booking-categories', array() );
		}

		/**
		 * Return booking categories to show.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_reject_pending_confirmation_bookings_enabled() {
			if ( $this->need_backward_compatibility( 'reject-pending-confirmation-bookings-after', 'reject-pending-confirmation-booking-enabled', '3.0.0' ) ) {
				return ! ! $this->get( 'reject-pending-confirmation-bookings-after', 0 );
			}

			return 'yes' === $this->get( 'reject-pending-confirmation-booking-enabled', 'no' );
		}

		/**
		 * Return booking categories.
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_reject_pending_confirmation_bookings_after() {
			return absint( $this->get( 'reject-pending-confirmation-bookings-after', 1 ) );
		}

		/**
		 * Return booking categories to show.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_complete_paid_bookings_enabled() {
			if ( $this->need_backward_compatibility( 'complete-paid-bookings-after', 'complete-paid-bookings-enabled', '3.0.0' ) ) {
				return '' !== $this->get( 'complete-paid-bookings-after', '' ) ? 'yes' : 'no';
			}

			return 'yes' === $this->get( 'complete-paid-bookings-enabled', 'no' );
		}

		/**
		 * Return booking categories.
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_complete_paid_bookings_after() {
			return absint( $this->get( 'complete-paid-bookings-after', 1 ) );
		}

		/**
		 * Returns the kind of users to whom the booking form is shown.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_show_booking_form_to() {
			if ( $this->need_backward_compatibility( 'show-booking-form-to-logged-users-only', 'show-booking-form-to', '3.0.0' ) ) {
				return $this->get( 'show-booking-form-to-logged-users-only', 'no' ) === 'yes' ? 'logged-in' : 'all';
			}

			$allowed_values = array( 'all', 'logged-in' );
			$value          = $this->get( 'show-booking-form-to', 'all' );

			return in_array( $value, $allowed_values, true ) ? $value : 'all';
		}

		/**
		 * Return the layout of the date range picker.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_date_range_picker_layout() {
			if ( $this->need_backward_compatibility( 'unique-calendar-range-picker-enabled', 'date-range-picker-layout', '3.0.0' ) ) {
				return $this->get( 'unique-calendar-range-picker-enabled', 'yes' ) === 'yes' ? 'unique' : 'different';
			}

			$allowed_values = array( 'unique', 'different' );
			$value          = $this->get( 'date-range-picker-layout', 'unique' );

			return in_array( $value, $allowed_values, true ) ? $value : 'unique';
		}

		/**
		 * Return the default layout for resources.
		 *
		 * @return string
		 * @since feature/resources-layout
		 */
		public function get_resources_default_layout() {
			$allowed_values = array( 'dropdown', 'list' );
			$value          = $this->get( 'resources-default-layout', 'dropdown' );

			return in_array( $value, $allowed_values, true ) ? $value : 'dropdown';
		}

		/**
		 * Return the layout for the time selector.
		 *
		 * @return string
		 * @since feature/resources-layout
		 */
		public function get_time_selector_layout() {
			$allowed_values = array( 'dropdown', 'list' );
			$value          = $this->get( 'time-selector-layout', 'dropdown' );

			return in_array( $value, $allowed_values, true ) ? $value : 'dropdown';
		}

		/**
		 * Return true if unique calendar range picker is enabled
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_calendar_style() {
			if ( $this->need_backward_compatibility( 'display-date-picker-inline', 'calendar-style', '3.0.0' ) ) {
				return $this->get( 'display-date-picker-inline', 'no' ) === 'yes' ? 'inline' : 'dropdown';
			}

			$allowed_values = array( 'inline', 'dropdown' );
			$value          = $this->get( 'calendar-style', 'dropdown' );

			return in_array( $value, $allowed_values, true ) ? $value : 'dropdown';
		}

		/**
		 * Return the time-picker format.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_time_picker_format() {
			return $this->get( 'time-picker-format', wc_time_format() );
		}

		/**
		 * Return true if showing duration unit in price
		 *
		 * @return bool
		 * @since 3.0.0
		 */
		public function show_duration_unit_in_price() {
			$costs_included = $this->get_costs_included_in_shown_price();
			$allowed        = in_array( 'base-price', $costs_included, true ) && ! in_array( 'extra-costs', $costs_included, true ) && ! in_array( 'services', $costs_included, true );

			return $allowed && 'yes' === $this->get( 'show-duration-unit-in-price', 'no' );
		}

		/**
		 * Return true if replacing days with weeks in the duration shown in prices.
		 *
		 * @return bool
		 * @since 3.0.0
		 */
		public function replace_days_with_weeks_in_price() {
			return 'yes' === $this->get( 'replace-days-with-weeks-in-price', 'no' );
		}

		/**
		 * Return the Form Error Handling.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_form_error_handling() {
			$allowed_values = array( 'on-form-update', 'on-button-click' );
			$value          = $this->get( 'form-error-handling', 'on-button-click' );

			return in_array( $value, $allowed_values, true ) ? $value : 'on-button-click';
		}

		/**
		 * Return the costs included in shown price.
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_costs_included_in_shown_price() {
			$cost_parts_options = yith_wcbk_get_costs_included_in_shown_price_options();
			$default            = array_keys( $cost_parts_options );
			$value              = $this->get( 'costs-included-in-shown-price', $default );

			$value = array_intersect( $value, array_keys( $cost_parts_options ) );

			// Empty array is not allowed.
			return ! ! $value ? $value : $default;
		}

		/**
		 * Return true if showing externals in calendar.
		 *
		 * @return bool
		 * @since 4.0.0
		 */
		public function show_externals_in_calendar(): bool {
			return 'yes' === $this->get( 'external-calendars-show-externals-in-calendar', 'no' ) && yith_wcbk_is_external_sync_module_active();
		}

		/**
		 * Return true if showing externals in calendar.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_fields_font_size(): string {
			$default = array(
				'size' => 15,
				'unit' => 'px',
			);
			$value   = $this->get( 'fields-font-size', $default );
			$value   = is_array( $value ) ? $value : array();
			$value   = wp_parse_args( $value, $default );

			if ( 'px' === $value['unit'] ) {
				$value['size'] = absint( $value['size'] );
			}

			return $value['size'] . $value['unit'];
		}

		/**
		 * Returns true if the dates should be blocked for pending confirmation bookings.
		 *
		 * @return bool
		 * @since 5.0.0
		 */
		public function should_block_dates_for_pending_confirmation_bookings() {
			$value = $this->get( 'block-dates-for-pending-confirmation-bookings', 'no' );

			return 'yes' === $value;
		}
	}
}
