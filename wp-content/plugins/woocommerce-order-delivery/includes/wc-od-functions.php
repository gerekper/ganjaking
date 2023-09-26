<?php
/**
 * Useful functions for the plugin
 *
 * @package WC_OD/Functions
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Include core functions.
require 'wc-od-compatibility-functions.php';
require 'wc-od-datetime-functions.php';
require 'wc-od-delivery-days-functions.php';
require 'wc-od-time-frames-functions.php';
require 'wc-od-shipping-methods-functions.php';
require 'wc-od-shipping-delivery-functions.php';
require 'wc-od-order-functions.php';
require 'wc-od-deprecated-functions.php';

/**
 * Gets the value of the query string argument.
 *
 * @since 1.0.0
 * @param string $arg The query string argument.
 * @return mixed      The argument value.
 */
function wc_od_get_query_arg( $arg ) {
	$value = '';
	$arg   = sanitize_key( $arg );
	if ( ! empty( $_POST ) && isset( $_POST['_wp_http_referer'] ) ) {
		$query_string = parse_url( $_POST['_wp_http_referer'], PHP_URL_QUERY );
		if ( $query_string ) {
			$query_args = array();
			parse_str( $query_string, $query_args );
			if ( isset( $query_args[ $arg ] ) ) {
				$value = $query_args[ $arg ];
			}
		}
	} elseif ( isset( $_GET[ $arg ] ) ) {
		$value = $_GET[ $arg ];
	}

	return urldecode( $value );
}

/**
 * Gets the specified admin url.
 *
 * @since 1.0.0
 *
 * @param string $section      Optional. The section name parameter.
 * @param array  $extra_params Optional. Additional parameters in pairs key => value.
 * @return string The admin page url.
 */
function wc_od_get_settings_url( $section = 'options', $extra_params = array() ) {
	$url = 'admin.php?page=wc-settings&tab=shipping';

	if ( $section ) {
		$url .= '&section=' . urlencode( $section );
	}

	if ( ! empty( $extra_params ) ) {
		foreach ( $extra_params as $param => $value ) {
			$url .= '&' . esc_attr( $param ) . '=' . urlencode( $value );
		}
	}

	return admin_url( $url );
}

/**
 * Gets the value from the posted data.
 *
 * @since 1.5.0
 *
 * @param string $key     The data key.
 * @param mixed  $default Optional. The default value.
 * @return mixed
 */
function wc_od_get_posted_data( $key, $default = null ) {
	$value = $default;

	// phpcs:disable WordPress.Security.NonceVerification
	if ( isset( $_POST[ $key ] ) ) {
		$value = wc_clean( wp_unslash( $_POST[ $key ] ) );
	} elseif ( isset( $_POST['post_data'] ) ) { // Posted by AJAX on refresh the content.
		parse_str( $_POST['post_data'], $post_data ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( isset( $post_data[ $key ] ) ) {
			$value = wc_clean( wp_unslash( $post_data[ $key ] ) );
		}
	}
	// phpcs:enable WordPress.Security.NonceVerification

	return $value;
}

/**
 * Removes the plugin prefix from the beginning of the string.
 *
 * @since 1.0.0
 * @since 1.7.0 Added `$prefix` parameter.
 *
 * @param string $string The string to parse.
 * @param string $prefix Optional. The prefix to remove from. Default 'wc_od_'.
 * @return string The parsed string.
 */
function wc_od_no_prefix( $string, $prefix = 'wc_od_' ) {
	if ( ! empty( $prefix ) && substr( $string, 0, strlen( $prefix ) ) === $prefix ) {
		$string = substr( $string, strlen( $prefix ) );
	}

	return $string;
}

/**
 * Maybe adds the plugin prefix to the beginning of the string.
 *
 * @since 1.0.0
 * @since 1.7.0 Added `$prefix` parameter.
 *
 * @param string $string The string to parse.
 * @param string $prefix Optional. The prefix to remove from. Default 'wc_od_'.
 * @return string The parsed string.
 */
function wc_od_maybe_prefix( $string, $prefix = 'wc_od_' ) {
	$string = wc_od_no_prefix( $string );

	return $prefix . $string;
}

/**
 * Gets the suffix for the script filenames.
 *
 * @since 1.7.0
 *
 * @return string The scripts suffix.
 */
function wc_od_get_scripts_suffix() {
	return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
}

/**
 * Gets templates passing attributes and including the file.
 *
 * @since 1.0.0
 *
 * @param string $template_name The template name.
 * @param array  $args          Optional. The template arguments.
 */
function wc_od_get_template( $template_name, $args = array() ) {
	wc_get_template( $template_name, $args, WC()->template_path(), WC_OD_PATH . 'templates/' );
}

/**
 * Logs a message.
 *
 * @since 1.4.0
 *
 * @param string         $message The message to log.
 * @param string         $level   The level.
 * @param string         $handle  Optional. The log handlers.
 * @param WC_Logger|null $logger  Optional. The logger instance.
 */
function wc_od_log( $message, $level = 'notice', $handle = 'wc_od', $logger = null ) {
	if ( ! $logger ) {
		$logger = wc_get_logger();
	}

	if ( method_exists( $logger, $level ) ) {
		call_user_func( array( $logger, $level ), $message, array( 'source' => $handle ) );
	} else {
		$logger->add( $handle, $message );
	}
}

/**
 * Gets the HTML markup for the specified attributes.
 *
 * @since 1.7.0
 *
 * @param array $attributes An array with pairs [key => value].
 * @return string
 */
function wc_od_get_attrs_html( $attributes ) {
	$attribute_strings = array();

	foreach ( $attributes as $key => $value ) {
		if ( 'class' === $key && is_array( $value ) ) {
			$value = join( ' ', $value );
		}

		$attribute_strings[] = esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
	}

	return implode( ' ', $attribute_strings );
}

/**
 * Adds a note (comment) to the order.
 *
 * This function is a wrapper for the WC_Order->add_order_note() method.
 *
 * @since 1.4.0
 *
 * @param WC_Order $order             The order object.
 * @param string   $note              Note to add.
 * @param int      $is_customer_note  Is this a note for the customer?.
 * @param bool     $added_by_user     Was the note added by a user?.
 * @return int Comment ID.
 */
function wc_od_add_order_note( $order, $note, $is_customer_note = 0, $added_by_user = false ) {
	$note_id = $order->add_order_note( $note, $is_customer_note );

	if ( $note_id ) {
		$type = $order->get_type();

		/**
		 * Fired after to add a note to the order.
		 *
		 * The dynamic portion of the hook name, $type, refers to the post_type value of the $order.
		 *
		 * @since 1.4.0
		 */
		do_action( "wc_od_added_{$type}_note", $note, $order, $is_customer_note, $added_by_user );
	}

	return $note_id;
}


/**
 * Gets the weekdays in a pair index => label.
 *
 * @since 1.0.0
 * @since 1.5.0 Added `$format` parameter.
 *
 * @global WP_Locale $wp_locale The WP_Locale instance.
 *
 * @param string $format Optional. The weekdays format. Allowed: abbrev, initial, empty. Default: empty.
 * @return array The week days.
 */
function wc_od_get_week_days( $format = '' ) {
	global $wp_locale;

	$weekdays = $wp_locale->weekday;

	if ( in_array( $format, array( 'abbrev', 'initial' ), true ) ) {
		$property = "weekday_{$format}";

		return array_combine( array_flip( $weekdays ), $wp_locale->$property );
	}

	return $weekdays;
}

/**
 * Gets the weekday name.
 *
 * @since 1.7.0
 *
 * @param int    $number The weekday number.
 * @param string $format Optional. The weekday format. Allowed: abbrev, initial, empty. Default: empty.
 * @return string
 */
function wc_od_get_weekday( $number, $format = '' ) {
	$weekdays = wc_od_get_week_days( $format );

	return ( isset( $weekdays[ $number ] ) ? $weekdays[ $number ] : '' );
}

/**
 * Formats a delivery range.
 *
 * @since 1.1.0
 *
 * @param array $range An associative array with the 'min' and 'max' values.
 * @param bool  $echo  Optional. Whether to echo or just return the string.
 * @return string The formatted delivery range.
 */
function wc_od_format_delivery_range( $range, $echo = false ) {
	if ( $range['min'] === $range['max'] ) {
		$output = $range['max'];
	} else {
		$output = "{$range['min']}-{$range['max']}";
	}

	/**
	 * Filter the formatted delivery range.
	 *
	 * @since 1.1.0
	 *
	 * @param string $output The output string.
	 * @param array  $range  An associative array with the 'min' and 'max' values.
	 */
	$output = apply_filters( 'wc_od_format_delivery_range', $output, $range );

	if ( $echo ) {
		echo $output;
	}

	return $output;
}

/**
 * Prints the order delivery details.
 *
 * @since 1.1.0
 *
 * @param array $args The arguments.
 */
function wc_od_order_delivery_details( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'order'           => '',
			'details_type'    => 'specific', // Accepts: 'specific' or 'estimated'.
			'is_local_pickup' => false,
		)
	);

	if ( $args['order'] instanceof WC_Order ) {
		$args['is_local_pickup'] = wc_od_order_is_local_pickup( $args['order'] );
	}

	if ( ! isset( $args['title'] ) ) {
		if ( wc_string_to_bool( $args['is_local_pickup'] ) ) {
			$args['title'] = __( 'Pickup details', 'woocommerce-order-delivery' );
		} else {
			$args['title'] = __( 'Delivery details', 'woocommerce-order-delivery' );
		}
	}

	$args['details_template'] = sprintf(
		'order-delivery/%1$s-%2$s-details.php',
		sanitize_text_field( $args['details_type'] ),
		( $args['is_local_pickup'] ? 'pickup' : 'delivery' )
	);

	/**
	 * Filter the arguments used by the order/delivery-date.php template.
	 *
	 * @since 1.1.0
	 * @since 2.6.0 Added `order`, `details_type`, and `is_local_pickup` parameters.
	 * @since 2.6.0 Deprecated `delivery_date` parameter. Use `date` instead.
	 * @since 2.6.0 Deprecated `delivery_time_frame` parameter. Use `time_frame` instead.
	 *
	 * @param array $args The template arguments.
	 */
	$args = apply_filters( 'wc_od_order_delivery_details_args', $args );

	wc_od_get_template( 'order/delivery-date.php', $args );
}


/** Countries & states functions **********************************************/


/**
 * Gets the countries you ship to.
 *
 * @since 1.0.0
 *
 * @return array
 */
function wc_od_get_countries() {
	return WC()->countries->get_shipping_countries();
}

/**
 * Gets the country states you ship to.
 *
 * @since 1.0.0
 *
 * @return array
 */
function wc_od_get_country_states() {
	return WC()->countries->get_shipping_country_states();
}

/**
 * Gets the country states you ship to.
 *
 * The state's information is formatted for the select2 library.
 *
 * @since 1.0.0
 *
 * @return array
 */
function wc_od_get_country_states_for_select2() {
	$formatted_country_states = array();
	$country_states           = wc_od_get_country_states();
	foreach ( $country_states as $country => $states ) {
		$formatted_country_states[ $country ] = array();
		foreach ( $states as $key => $state ) {
			$formatted_country_states[ $country ][] = array(
				'id'   => $key,
				'text' => $state,
			);
		}
	}

	return $formatted_country_states;
}


/** Template functions *********************************************************/


/**
 * Gets the delivery date field arguments.
 *
 * @since 1.1.0
 * @since 1.5.0 Updated default values for the `return` and `label` parameters.
 *
 * @param array  $args    Optional. The arguments to overwrite.
 * @param string $context Optional. The context in which the form field is used.
 *
 * @return array An array with the delivery date field arguments.
 */
function wc_od_get_delivery_date_field_args( $args = array(), $context = '' ) {
	$defaults = array(
		'type'              => 'delivery_date',
		'label'             => __( 'Delivery date', 'woocommerce-order-delivery' ),
		'placeholder'       => '',
		'class'             => array( 'form-row-wide' ),
		'required'          => ( 'required' === WC_OD()->settings()->get_setting( 'delivery_fields_option' ) ),
		'return'            => false,
		'value'             => '',
		'custom_attributes' => array(
			'readonly' => 'true',
		),
	);

	// Add priority to allow sorting the field.
	if ( 'checkout' === $context ) {
		$defaults['priority'] = 10;
	}

	/**
	 * Filters the arguments for the delivery date field.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added `$context` parameter.
	 *
	 * @param array  $args    The arguments for the delivery date field.
	 * @param string $context The context in which the form field is used.
	 */
	return apply_filters( 'wc_od_delivery_date_field_args', wp_parse_args( $args, $defaults ), $context );
}

/**
 * Gets the HTML content for a delivery_date form field.
 *
 * @since 1.2.0
 *
 * @param string $field The field content.
 * @param string $key   The field key.
 * @param array  $args  The field arguments.
 * @param mixed  $value The field value.
 *
 * @return string The field HTML content.
 */
function wc_od_delivery_date_field( $field, $key, $args, $value ) {
	$args['type']   = 'text';
	$args['return'] = true;

	// Create a hidden field with the 'name' attribute.
	$name_attr    = 'name="' . esc_attr( $key ) . '"';
	$hidden_field = sprintf( '<input type="hidden" %1$s value="%2$s" />', $name_attr, $value );

	// Create a text field with the date localized for the datepicker.
	$field = woocommerce_form_field( $key, $args, ( $value ? wc_od_localize_date( $value ) : null ) );

	// Remove the 'name' attribute from the input text.
	$field = str_replace( $name_attr, '', $field );

	// Prepend the hidden field to the text field.
	$field = str_replace( '<input', $hidden_field . '<input', $field );

	return $field;
}
add_filter( 'woocommerce_form_field_delivery_date', 'wc_od_delivery_date_field', 10, 4 );

/**
 * Gets the calendar settings that will be used to configure the datepicker.
 *
 * Note: Dates must have the same format as the 'format' parameter.
 *
 * @since 1.1.0
 *
 * @param array  $args     Optional. The parameters to overwrite the defaults.
 * @param string $context  Optional. The context.
 * @return array An array with the calendar settings.
 */
function wc_od_get_calendar_settings( $args = array(), $context = '' ) {
	$defaults = array(
		'language'           => get_bloginfo( 'language' ),
		'format'             => wc_od_get_date_format( 'js' ),
		'weekStart'          => get_option( 'start_of_week', 0 ),
		'startDate'          => '',
		'endDate'            => '',
		'daysOfWeekDisabled' => array(),
		'datesDisabled'      => array(),
		'clearBtn'           => true, // Enabled by default to avoid locks between delivery days and shipping methods.
	);

	/**
	 * Filter the calendar settings.
	 *
	 * @since 1.1.0
	 *
	 * @param array  $settings The calendar settings.
	 * @param string $context  The context.
	 */
	return apply_filters( 'wc_od_get_calendar_settings', wp_parse_args( $args, $defaults ), $context );
}

/**
 * Enqueue the necessary scripts and styles to load the datepicker.
 *
 * @since 1.1.0
 *
 * @param string $context The context.
 */
function wc_od_enqueue_datepicker( $context = '' ) {
	wp_enqueue_style( 'bootstrap-datepicker', WC_OD_URL . 'assets/css/lib/bootstrap-datepicker.css', array(), '1.9.0' );
	wp_add_inline_style( 'bootstrap-datepicker', wc_od_get_datepicker_custom_styles( $context ) );

	wp_enqueue_script( 'bootstrap-datepicker', WC_OD_URL . 'assets/js/lib/bootstrap-datepicker/bootstrap-datepicker.min.js', array( 'jquery' ), '1.9.0-475ecbf', true );

	$datepicker_locale = wc_od_get_datepicker_locale_url();
	if ( $datepicker_locale ) {
		wp_enqueue_script( 'bootstrap-datepicker-l10n', $datepicker_locale, array( 'jquery', 'bootstrap-datepicker' ), '1.9.0', true );
	}

	$suffix = wc_od_get_scripts_suffix();

	wp_enqueue_script( 'wc-od-datepicker', WC_OD_URL . "assets/js/wc-od-datepicker{$suffix}.js", array( 'jquery', 'bootstrap-datepicker' ), WC_OD_VERSION, true );

	/**
	 * Enqueue scripts after the datepicker scripts.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context The context.
	 */
	do_action( 'wc_od_enqueue_datepicker', $context );
}

/**
 * Gets the datepicker locale URL.
 *
 * @since 1.2.0
 *
 * @return string|null The locale URL. Null on failure.
 */
function wc_od_get_datepicker_locale_url() {
	$locale       = str_replace( '_', '-', get_locale() );
	$locales_path = 'assets/js/lib/bootstrap-datepicker/locales/';

	$paths = array(
		"bootstrap-datepicker.{$locale}.min.js",
		sprintf( 'bootstrap-datepicker.%s.min.js', substr( $locale, 0, 2 ) ),
	);

	$url = null;

	foreach ( $paths as $path ) {
		if ( file_exists( WC_OD_PATH . $locales_path . $path ) ) {
			$url = WC_OD_URL . $locales_path . $path;
			break;
		}
	}

	/**
	 * Filter the datepicker locale URL.
	 *
	 * @since 1.2.0
	 *
	 * @param string $url    The locale URL.
	 * @param string $locale The current locale.
	 */
	return apply_filters( 'wc_od_get_datepicker_locale_url', $url, $locale );
}

/**
 * Gets the datepicker custom styles.
 *
 * @since 1.1.0
 *
 * @param string $context Optional. The context. [checkout, settings]
 * @return string The datepicker styles.
 */
function wc_od_get_datepicker_custom_styles( $context = '' ) {
	$styles = '

/**
 * WC Order Delivery: Datepicker custom styles.
 */

.datepicker-rtl.dropdown-menu {
  left: auto;
}

.datepicker table {
	width: auto;
	border: 0;
}

.datepicker table tr {
	border: 0;
	background-color: transparent;
}

.datepicker table tr td,
.datepicker table tr th {
	width: 24px;
	height: 24px;
	border: 0;
}

.datepicker.dropdown-menu th,
.datepicker.datepicker-inline th,
.datepicker.dropdown-menu td,
.datepicker.datepicker-inline td {
	padding: 3px;
	background-color: #fff;
	box-sizing: content-box;
	vertical-align: middle;
}

.datepicker .datepicker-days tr:nth-child(2n) td {
	background-color: #fff;
}

.datepicker table tr td.disabled,
.datepicker table tr td span.disabled {
	opacity: 0.4;
}';

	/**
	 * Filter the datepicker custom styles.
	 *
	 * @since 1.1.0
	 *
	 * @param string $styles  The styles.
	 * @param string $context The context.
	 */
	return apply_filters( 'wc_od_datepicker_custom_styles', $styles, $context );
}

/**
 * Gets if the plugin should work for local pickup too.
 *
 * @since 2.6.0
 *
 * @return bool
 */
function wc_od_is_local_pickup_enabled() {
	return wc_string_to_bool( WC_OD()->settings()->get_setting( 'enable_local_pickup' ) );
}
