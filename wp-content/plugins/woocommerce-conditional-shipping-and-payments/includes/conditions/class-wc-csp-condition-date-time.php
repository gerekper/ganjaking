<?php
/**
 * WC_CSP_Condition_Date_Time class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.9.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Date Time Condition.
 *
 * @class    WC_CSP_Condition_Date_Time
 * @version  1.15.0
 */
class WC_CSP_Condition_Date_Time extends WC_CSP_Package_Condition {

	protected $available_modifiers = array();
	protected $date_ranges         = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'date_time';
		$this->title                          = __( 'Date/Time', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions  = array( 'shipping_methods', 'payment_gateways', 'shipping_countries' );
		$this->supported_product_restrictions = array( 'shipping_methods', 'payment_gateways', 'shipping_countries' );

		$this->date_ranges = array(
			'years'         => range( 2000, 2100 ),
			'months'        => range( 1, 12 ), // Multiselect
			'days_in_month' => range( 1, 31 ),
			'days_in_week'  => range( 0, 6 ), // Multiselect
			'hours'         => range( 0, 23 ),
		);

		/**
		 * Configuration settings for the available modifiers.
		 *
		 * array['dt_format']    string Date Format as in https://www.php.net/manual/en/datetime.format.php .
		 * array['label']        string Gets displayed in the modifier dropdown.
		 * array['range']        array All the possible values for the modifier.
		 * array['sample']       string Sample text of possible values.
		 *
		 * @var array|array[]
		 **/
		$this->available_modifiers = array(
			'year-is'             => array(
				'dt_format' => 'Y',
				'label'     => __( 'year is', 'woocommerce-conditional-shipping-and-payments' ),
				'range'     => $this->date_ranges[ 'years' ],
				'sample'    => '2021, 2022, 2023-2025'
			),
			'year-not-is'         => array(
				'dt_format' => 'Y',
				'label'     => __( 'year is not', 'woocommerce-conditional-shipping-and-payments' ),
				'range'     => $this->date_ranges[ 'years' ],
				'sample'    => '2021, 2022, 2023-2025'
			),
			'month-is'            => array(
				'dt_format' => 'n',
				'label'     => __( 'month is', 'woocommerce-conditional-shipping-and-payments' ),
				'range'     => $this->date_ranges[ 'months' ],
			),
			'month-not-is'        => array(
				'dt_format' => 'n',
				'label'     => __( 'month is not', 'woocommerce-conditional-shipping-and-payments' ),
				'range'     => $this->date_ranges[ 'months' ],
			),
			'day-in-month-is'     => array(
				'dt_format' => 'j',
				'label'     => __( 'day in month is', 'woocommerce-conditional-shipping-and-payments' ),
				'range'     => $this->date_ranges[ 'days_in_month' ],
				'sample'    => '1, 2, 4, 10-15'
			),
			'day-in-month-not-is' => array(
				'dt_format' => 'j',
				'label'     => __( 'day in month is not', 'woocommerce-conditional-shipping-and-payments' ),
				'range'     => $this->date_ranges[ 'days_in_month' ],
				'sample'    => '1, 2, 4, 10-15'
			),
			'day-in-week-is'      => array(
				'dt_format' => 'w',
				'label'     => __( 'day in week is', 'woocommerce-conditional-shipping-and-payments' ),
				'range'     => $this->date_ranges[ 'days_in_week' ],
			),
			'day-in-week-not-is'  => array(
				'dt_format' => 'w',
				'label'     => __( 'day in week is not', 'woocommerce-conditional-shipping-and-payments' ),
				'range'     => $this->date_ranges[ 'days_in_week' ],
			),
			'hour-is'             => array(
				'dt_format' => 'G',
				'label'     => __( 'hour is', 'woocommerce-conditional-shipping-and-payments' ),
				'range'     => $this->date_ranges[ 'hours' ],
				'sample'    => '0, 2, 4, 10-15, 20-23'
			),
			'hour-not-is'         => array(
				'dt_format' => 'G',
				'label'     => __( 'hour is not', 'woocommerce-conditional-shipping-and-payments' ),
				'range'     => $this->date_ranges[ 'hours' ],
				'sample'    => '0, 2, 4, 10-15, 20-23'
			),
		);
	}

	/**
	 * Parses the a string of numeric values, separated by commas and checks if they
	 * are in range with the default date ranges.
	 *
	 * If a string has - , then it gets replaced with the equivalent values.
	 *
	 * @param  string   $value  Value entered in the admin.
	 * @param  string   $modifier  The date modifier.
	 * @return array|false
	 */
	protected function date_value_parser( $value, $modifier ) {

		if ( ! array_key_exists( $modifier, $this->available_modifiers ) ) {
			return false;
		}

		// If you find anything else except numbers, spaces, dashes and commas, bail out early.
		$value               = str_replace( ' ', '', $value );
		$regex_allowed_chars = '/^[0-9,-]+$/';
		if ( 0 === preg_match( $regex_allowed_chars, $value ) ) {
			return false;
		}

		// Replace dashes, with their comma separated values (digit-digit)
		if ( false !== strpos( $value, '-' ) ) {
			$value = preg_replace_callback( '/(\d+)-(\d+)/', function ( $match ) {

				// Return an out of range number (-1) if the first number of the range is higher than the second one.
				if ( $match[ 1 ] >= $match[ 2 ] ) {
					return -1;
				}

				return implode( ',', range( $match[ 1 ], $match[ 2 ] ) );
			}, $value );

			// If the value still contains a dash, return false
			if ( false !== strpos( $value, '-' ) ) {
				return false;
			}
		}

		$clean_values        = array_unique( ( array_map( 'intval', explode( ',', $value ) ) ) );
		$out_of_range_values = array_diff( $clean_values, $this->available_modifiers[ $modifier ][ 'range' ] );

		// If the array is empty, it means that all of the values entered are in range and we're good to go.
		if ( empty( $out_of_range_values ) ) {
			return $clean_values;
		}

		return false;
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always return false (not evaluated).
		if ( empty( $data[ 'value' ] ) && '0' !== $data[ 'value' ] ) {
			return false;
		}

		return __( 'complete your order at a different time/date', 'woocommerce-conditional-shipping-and-payments' );
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  array $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) && '0' !== $data[ 'value' ] ) {
			return true;
		}

		$found_part = false;
		$date_time  = apply_filters( 'woocommerce_csp_date_time_current',
			new DateTime( 'now',  WC_CSP_Core_Compatibility::wp_timezone() ),
			$data,
			$args
		);

		$current_value = (int) $date_time->format( $this->available_modifiers[ $data[ 'modifier' ] ][ 'dt_format' ] );
		$is_modifier   = self::modifier_is(
			$data[ 'modifier' ],
			array( 'year-is', 'month-is', 'day-in-month-is', 'day-in-week-is', 'hour-is' )
		);

		// If the modifier value matches the current date/time value...
		// and we're evaluating an `is` modifier, we should immediately apply the condition.
		$values = $this->date_value_parser( $data[ 'value' ], $data[ 'modifier' ] );
		if ( is_array( $values ) ) {
			foreach ( $values as $modifier_value ) {
				if ( $current_value === $modifier_value ) {
					$found_part = true;
					if ( $is_modifier ) {
						return true;
					}
				}
			}

			// If the modifier value doesn't matches the current date/time value...
			// and we're evaluating an `not-is` modifier, we should apply the condition.
			if ( ! $found_part && ! $is_modifier ) {
				return true;
			}
		}

		// In any other case, do not apply the condition.
		return false;
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return boolean|array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( isset( $posted_condition_data[ 'value' ] ) ) {
			$posted_condition_data[ 'value' ] = implode( ',', $posted_condition_data[ 'value' ] );
		}

		if ( isset( $posted_condition_data[ 'value' ] )
		     && isset( $posted_condition_data[ 'modifier' ] )
		     && $this->date_value_parser(
				$posted_condition_data[ 'value' ],
				$posted_condition_data[ 'modifier' ] )
		) {
			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'modifier' ]     = $posted_condition_data[ 'modifier' ];
			// Cleanup multiple spaces
			$processed_condition_data[ 'value' ] = preg_replace( '!\s+!', ' ', $posted_condition_data[ 'value' ] );

			return $processed_condition_data;
		}

		$modifier  = $this->available_modifiers[ $posted_condition_data[ 'modifier' ] ];
		$range_str = min( $modifier[ 'range' ] ) . '-' . max( $modifier[ 'range' ] );
		$position  = intval( $posted_condition_data[ 'restriction_position' ] ) + 1;

		WC_Admin_Meta_Boxes::add_error( sprintf( __( 'Rule <strong>#%s</strong>: <strong>%s</strong> modifier with condition <strong>%s: %s</strong> was invalid and removed. Please choose a value between %s.', 'woocommerce-conditional-shipping-and-payments' ),
			$position,
			$this->title,
			$modifier[ 'label' ],
			$posted_condition_data[ 'value' ],
			$range_str
		) );

		return false;
	}

	/**
	 * Get date/time condition content for restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier      = 'year-is'; // Default modifier
		$values       = isset( $condition_data[ 'value' ] ) ? $condition_data[ 'value' ] : '';
		$values_array = ( empty( $values ) && '0' !== $values )
			? array()
			: explode( ',', $values );

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		$year_modifiers         = array( 'year-is', 'year-not-is' );
		$day_in_month_modifiers = array( 'day-in-month-is', 'day-in-month-not-is' );
		$hour_modifiers         = array( 'hour-is', 'hour-not-is' );
		$month_modifiers        = array( 'month-is', 'month-not-is' );
		$day_in_week_modifiers  = array( 'day-in-week-is', 'day-in-week-not-is' );
		$value_input_name       = 'restriction[' . $index . '][conditions][' . $condition_index . '][value][]';

		?>
		<input type="hidden" name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][condition_id]" value="<?php echo esc_attr( $this->id ); ?>"/>
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo esc_attr( $index ); ?>][conditions][<?php echo esc_attr( $condition_index ); ?>][modifier]"
							class="has_conditional_values" data-value_input_name="<?php echo esc_attr( $value_input_name ); ?>"
					>
						<?php foreach ( $this->available_modifiers as $modifier_key => $modifier_content ) { ?>
							<option value="<?php echo esc_attr( $modifier_key ); ?>" <?php selected( $modifier, $modifier_key, true ); ?>>
								<?php echo esc_html( $modifier_content[ 'label' ] ); ?>
							</option>
						<?php } ?>
					</select>
				</div>
			</div>

			<?php
			echo $this->get_admin_fields_freetext_html( $modifier, $year_modifiers, $values, $value_input_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->get_admin_fields_freetext_html( $modifier, $day_in_month_modifiers, $values, $value_input_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->get_admin_fields_freetext_html( $modifier, $hour_modifiers, $values, $value_input_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			echo $this->get_admin_fields_select_html( $modifier, $month_modifiers, $values_array, $value_input_name, 'month' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->get_admin_fields_select_html( $modifier, $day_in_week_modifiers, $values_array, $value_input_name, 'day_in_week' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
		<?php
	}

	/**
	 * Prepare and return the freetext html fields
	 *
	 * @param string $current_modifier
	 * @param array  $modifiers
	 * @param string $values
	 * @param string $value_input_name
	 *
	 * @return false|string
	 */
	protected function get_admin_fields_freetext_html( $current_modifier, $modifiers, $values, $value_input_name ) {

		ob_start();
		?>

		<div class="condition_value select-field"
			 data-modifiers="<?php echo esc_attr( implode( ',', $modifiers ) ); ?>"
			<?php echo in_array( $current_modifier, $modifiers ) ? '' : ' style="display:none;"'; ?>
		>
			<input type="text"
				   class="csp_conditional_values_input"
				   name="<?php echo in_array( $current_modifier, $modifiers ) ? esc_attr( $value_input_name ) : ''; ?>"
				   value="<?php echo in_array( $current_modifier, $modifiers ) ? esc_attr( $values ) : ''; ?>"
				   placeholder="<?php echo esc_attr( sprintf( __( 'Enter values from %1$s to %2$s, separated by comma&hellip;', 'woocommerce-conditional-shipping-and-payments' ), min( $this->available_modifiers[ $modifiers[ 0 ] ][ 'range' ] ), max( $this->available_modifiers[ $modifiers[ 0 ] ][ 'range' ] ) ) ); ?>"
			/>
			<span class="description">
				<?php echo wp_kses_post( sprintf( __( 'Enter values from <code>%1$s</code> to <code>%2$s</code>, separated by comma. Supports dashes for ranges. Example: <code>%3$s</code>.', 'woocommerce-conditional-shipping-and-payments' ),
					min( $this->available_modifiers[ $modifiers[ 0 ] ][ 'range' ] ),
					max( $this->available_modifiers[ $modifiers[ 0 ] ][ 'range' ] ),
					$this->available_modifiers[ $modifiers[ 0 ] ][ 'sample' ]
				) );
				?>
			</span>
		</div>

		<?php

		return ob_get_clean();
	}

	/**
	 * Prepare and return the select2 html fields
	 *
	 * @param string  $current_modifier
	 * @param array   $modifiers
	 * @param array   $values_array
	 * @param string  $value_input_name
	 * @param string  $type
	 *
	 * @return false|string
	 */
	protected function get_admin_fields_select_html( $current_modifier, $modifiers, $values_array, $value_input_name, $type ) {

		global $wp_locale;

		// If the current modifer is not in the list of modifiers, then the selected values should be overriden to an empty array
		$values_array = in_array( $current_modifier, $modifiers ) ? $values_array : array();
		$placeholder  = 'month' === $type ? __( 'Select months&hellip;', 'woocommerce-conditional-shipping-and-payments' ) : __( 'Select days&hellip;', 'woocommerce-conditional-shipping-and-payments' );

		ob_start();
		?>

		<div class="condition_value select-field"
			 data-modifiers="<?php echo esc_attr( implode( ',', $modifiers ) ); ?>"
			<?php echo in_array( $current_modifier, $modifiers ) ? '' : ' style="display:none;"'; ?>
		>
			<select class="csp_conditional_values_input multiselect sw-select2"
					name="<?php echo in_array( $current_modifier, $modifiers ) ? esc_attr( $value_input_name ) : ''; ?>"
					multiple="multiple" data-placeholder="<?php echo esc_attr( $placeholder ); ?>">
				<?php
				foreach ( $this->available_modifiers[ $modifiers[ 0 ] ][ 'range' ] as $key ) {
					$option = 'month' === $type ? $wp_locale->get_month( $key ) : $wp_locale->get_weekday( $key );
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $values_array ), true, false ) . '>' . esc_html( $option ) . '</option>';
				}
				?>
			</select>
			<div class="condition_form_row">
				<a class="wccsp_select_all button" href="#"><?php esc_html_e( 'All', 'woocommerce' ); ?></a>
				<a class="wccsp_select_none button" href="#"><?php esc_html_e( 'None', 'woocommerce' ); ?></a>
			</div>
		</div>

		<?php

		return ob_get_clean();
	}
}
